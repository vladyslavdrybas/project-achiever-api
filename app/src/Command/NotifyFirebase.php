<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Achievement;
use App\Entity\FcmTokenDeviceType;
use App\Entity\FirebaseCloudMessaging as FcmToken;
use App\Repository\AchievementRepository;
use App\Repository\FirebaseCloudMessagingRepository;
use Google\Service\FirebaseCloudMessaging;
use Google_Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function array_filter;
use function array_map;
use function bin2hex;
use function rand;
use function random_bytes;
use function time;
use function var_dump;
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
        protected readonly AchievementRepository $achievementRepository
    ) {
        parent::__construct();
    }

    // TODO store sent messages
    // TODO shorten title -> make it same for all. move achievement title to body
    // TODO add link on achievement into message
    // TODO shorten message body
    // TODO add query for sending messages
    // TODO send a bulk (batch) of messages in one request to firebase
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var FcmToken[] $tokens */
        $tokens = $this->messagingRepository->findBy(['deviceType' => FcmTokenDeviceType::WEB]);

        $tokenUserHash = [];
        foreach ($tokens as $token) {
            $hash = $token->getToken() . ':' . $token->getUser()->getRawId();
            if ( !isset($tokenUserHash[$hash])) {
                $tokenUserHash[$hash] = [
                    'token' => $token->getToken(),
                    'userId' => $token->getUser()->getRawId(),
                ];
            }
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

                    return !$a->isNotified();
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

                $achievement = $achievementsToNotify[$index];

                $achievement->setIsNotified(true);
                $this->achievementRepository->add($achievement);

                $message = [
                    'token' => $data['token'],
                    'title' => $achievement->getTitle(),
                    'body' => $achievement->getDescription(),
                    'doneAt' => (string) $achievement->getDoneAt()?->getTimestamp(),
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
                        'requireInteraction' => 'true',
                        'title' => $msg['title'],
                        'body' => $msg['body'],
                        'doneAt' => $msg['doneAt'],
                        'messageId' => time() . ':' . bin2hex(random_bytes(10)),
                        'duration' => (string)(30 * 1000), // 30sec
                    ],
                ],
            ];

            $response = $http_client->post(
                "https://fcm.googleapis.com/v1/projects/{$project}/messages:send",
                [
                    'json' => $message
                ]
            );

            $io->info($response->getStatusCode() . PHP_EOL);
            $io->info($response->getBody() . PHP_EOL);
        }

        $io->success('Message sent.');

        return Command::SUCCESS;
    }
}
