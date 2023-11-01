<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Achievement;
use App\Entity\FcmTokenDeviceType;
use App\Repository\AchievementRepository;
use App\Repository\FirebaseCloudMessagingRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\Criteria;
use Google\Service\FirebaseCloudMessaging;
use Google_Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function array_filter;
use function array_map;
use function bin2hex;
use function rand;
use function random_bytes;
use function time;
use const PHP_EOL;

#[AsCommand(
    name: 'api:notifier:firebase',
    description: 'generate and send notifications to fcm',
)]
class NotifyFirebase extends Command
{
    public function __construct(
        protected readonly HttpClientInterface $client,
        protected readonly ParameterBagInterface $bag,
        protected readonly SerializerInterface $serializer,
        protected readonly FirebaseCloudMessagingRepository $messagingRepository,
        protected readonly AchievementRepository $achievementRepository,
        protected readonly UrlGeneratorInterface $urlGenerator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('deviceType', InputArgument::OPTIONAL, 'device type');
    }

    // TODO SECURITY ISSUE: multiple users on same device has same fcm token. (For transactional purposes)
    // solution:
    // * user identifier inside data object.
    // * detect user identifier on frontend.
    // * monitoring user activity.

    // TODO SECURITY ISSUE: deactivate token on logout. activate token on login

    // TODO ANNOYING ISSUE: user will receive a bunch of messages when he will return. can be fixed via EXPIRATION date.
    // TODO store sent messages
    // TODO shorten message body
    // TODO add link on achievement into message
    // TODO add query for sending messages
    // TODO send a bulk (batch) of messages in one request to firebase
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $deviceType = FcmTokenDeviceType::from($input->getArgument('deviceType')) ?? FcmTokenDeviceType::WEB;

        $tokensCriteria = new Criteria();
        $tokensCriteria
            ->andWhere(Criteria::expr()->gt('expireAt', new DateTimeImmutable()))
            ->andWhere(Criteria::expr()->eq('deviceType', $deviceType))
        ;

        $tokens = $this->messagingRepository->matching($tokensCriteria);

        $tokensHash = [];
        $tokenUserHash = [];
        foreach ($tokens as $token) {
            $hash = $token->getToken() . ':' . $token->getUser()->getRawId();
            if ( !isset($tokenUserHash[$hash]) && $token->getUser()->isActive()) {
                $tokenUserHash[$hash] = [
                    'token' => $token->getToken(),
                    'userId' => $token->getUser()->getRawId(),
                ];

                $tokensHash[$token->getToken()] = $token;
            }
        }

        if (!count($tokenUserHash)) {
            $io->success('No tokens to send.');

            return Command::SUCCESS;
        }

        $messages = [];
        foreach ($tokenUserHash as $hash => $data)
        {
            $userId = $data['userId'];
            $achievements = $this->achievementRepository->findBy(
                [
                    'user' => $userId,
                ]
            );

            $len = count($achievements);
            if (!$len) {
                continue;
            }

            $achievementsToNotify = array_values(array_filter(
                $achievements,
                function (Achievement $a) {
                    return false === $a->isNotified();
                }
            ));

            $notifyLen = count($achievementsToNotify);

            $io->info(sprintf(
                'User %s has %s achievements. in notification list are %s',
                $userId,
                $len,
                $notifyLen,
            ));

            if (0 !== $notifyLen) {
                $index = rand(0, $notifyLen - 1);

                /** @var Achievement $achievement */
                $achievement = $achievementsToNotify[$index];

                $achievement->setNotifiedAt(new DateTimeImmutable());
                $achievement->setIsNotified(true);
                $this->achievementRepository->add($achievement);

                $link = sprintf(
                    '%s/achievements/show/%s',
                    $this->bag->get('web_host') ?? '',
                    $achievement->getRawId()
                );

                $doneAt = '';
                if ($achievement->getDoneAt()) {
                    $doneAt = $achievement->getDoneAt()->format(DateTimeInterface::W3C);
                }

                $message = [
                    'token' => $data['token'],
                    'title' => $achievement->getDoneAt() ? 'Achieved' : 'Achievement in progress',
                    'body' => sprintf('[%s] %s', $achievement->getTitle(), $achievement->getDescription()),
                    'doneAt' => $doneAt,
                    'link' => $link,
                    'userId' => $userId,
                ];

                $messages[$hash] = $message;
            } else {
                array_map(
                    function (Achievement $a) {
                        $a->setIsNotified(false);
                        $this->achievementRepository->add($a);

                        return $a;
                    },
                    $achievements
                );
            }
            $this->achievementRepository->save();
        }

        if (!count($messages)) {
            $io->success('Nothing to send.');

            return Command::SUCCESS;
        }

        $project = 'motivator-dcb76';
        $client = new Google_Client();
        $client->setAuthConfig($this->bag->get('google_application_credentials'));
        $client->addScope(FirebaseCloudMessaging::FIREBASE_MESSAGING);
        $http_client = $client->authorize();

        foreach ($messages as $hash => $msg)
        {
            $message = [
                'message' => [
                    'token' => $msg['token'],
                    'data' => [
                        'title' => $msg['title'],
                        'body' => $msg['body'],
                        'requireInteraction' => 'true',
                        'doneAt' => $msg['doneAt'],
                        'duration' => (string)(30 * 1000), // 30sec
                        'messageId' => time() . ':' . bin2hex(random_bytes(10)),
                        'link' => $msg['link'],
                        'userId' => $msg['userId'],
                        'icon' => 'http://localhost:3000/logo.svg'
                    ],
                    'webpush' => [
                        'fcm_options' => [
                            'link' => $msg['link'],
                        ]
                    ]
                ],
            ];

            $response = $http_client->post(
                "https://fcm.googleapis.com/v1/projects/{$project}/messages:send",
                [
                    'json' => $message
                ]
            );

            if ($response->getStatusCode() === 404) {
                if (isset($tokensHash[$msg['token']])) {
                    $tokenToDeactivate = $tokensHash[$msg['token']];
                    $tokenToDeactivate->setExpireAt(null);
                    $this->messagingRepository->add($tokenToDeactivate);
                    $this->messagingRepository->save();
                }
            }

            $io->info($response->getStatusCode() . PHP_EOL);
            $io->info($response->getBody() . PHP_EOL);
        }

        $io->success('Message sent.');

        return Command::SUCCESS;
    }
}
