<?php

declare(strict_types=1);

namespace App\Command;

use Google\Service\FirebaseCloudMessaging;
use Google_Client;
use Google_Service_FirebaseCloudMessaging;
use Google_Service_FirebaseCloudMessaging_FcmOptions;
use Google_Service_FirebaseCloudMessaging_Message;
use Google_Service_FirebaseCloudMessaging_Notification;
use Google_Service_FirebaseCloudMessaging_SendMessageRequest;
use Google_Service_FirebaseCloudMessaging_WebpushConfig;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function sprintf;
use function var_dump;
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
        protected readonly SerializerInterface $serializer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('deviceToken', InputArgument::OPTIONAL, 'device token to send message')
            ->addArgument('message', InputArgument::OPTIONAL, 'message');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $deviceToken = $input->getArgument('deviceToken') ?? 'eSO_y8iGwoVBgZ4A-sPSzf:APA91bFa4f3dprl4Qnr_XK4OikZV1wYxQCoUmgF_0F1EgY9NHThVmV9cSoc8pB_uOwbpunebr-rnYbUpEcaC2g5mDk0gxRFIlpcIlBtmKzqAXycvWLH8i3FsGmTXBS7hlg3GXy35H2Ep';
        $message = $input->getArgument('message') ?? 'test message for device: ' . $deviceToken;

        $io->info(sprintf('I will send "%s" to device "%s"', $message, $deviceToken));

        $client = new Google_Client();
        $client->setAuthConfig($this->bag->get('google_application_credentials'));
        $client->addScope(FirebaseCloudMessaging::FIREBASE_MESSAGING);
        $http_client = $client->authorize();

        $project = 'motivator-dcb76';
        $message = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'body' => $message,
                    'title' => 'FCM Message',
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

        $io->success('Message sent.');

        return Command::SUCCESS;
    }
}
