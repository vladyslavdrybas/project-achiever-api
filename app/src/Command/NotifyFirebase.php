<?php

declare(strict_types=1);

namespace App\Command;

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
use function bin2hex;
use function random_bytes;
use function time;
use const PHP_EOL;

#[AsCommand(
    name: 'api:notifier:firebase',
    description: 'create user with key for api',
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
    // TODO Do Not Repeat Yourself -> do not repeat already sent achievements
    // TODO add query for sending messages
    // TODO send a bulk (batch) of messages in one request to firebase
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var FcmToken[] $tokens */
        $tokens = $this->messagingRepository->findBy(['deviceType' => 'web']);

        $tokenUserHash = [];
        foreach ($tokens as $token) {
            if ( !isset($tokenUserHash[$token->getToken()])) {
                $tokenUserHash[$token->getToken()] = $token->getUser()->getRawId();
            }
        }

        $messages = [];
        foreach ($tokenUserHash as $token => $userId)
        {
            $achievements = $this->achievementRepository->findBy(
                [
                    'user' => $userId
                ]
            );

            $len = count($achievements);
            if (!$len) {
                continue;
            }

            $index = rand(0, $len - 1);

            $achievement = $achievements[$index];

            $message = [
                'title' => $achievement->getTitle(),
                'body' => $achievement->getDescription(),
                'doneAt' => (string) $achievement->getDoneAt()?->getTimestamp(),
            ];

            $messages[$token] = $message;
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

        foreach ($messages as $token => $msg)
        {
            $message = [
                'message' => [
                    'token' => $token,
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
