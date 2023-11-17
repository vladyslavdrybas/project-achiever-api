<?php

declare(strict_types=1);

namespace App\Command;
use App\Entity\User;
use App\Repository\ShareObjectTokenRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use function array_shift;
use function array_unique;
use function file_put_contents;
use function str_replace;
use const JSON_UNESCAPED_SLASHES;

#[AsCommand(
    name: 'api:postman:environment:dev',
    description: 'Postman api dev environment generator.',
)]
class PostmanDevEnvironmentBuilder extends Command
{
    protected Generator $faker;

    public function __construct(
        protected readonly ParameterBagInterface $parameterBag,
        protected readonly UserRepository $userRepository,
        protected readonly SerializerInterface $serializer,
        protected readonly ShareObjectTokenRepository $shareObjectTokenRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->faker = Factory::create();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'userIdentifier'
                , InputArgument::OPTIONAL
                , 'User identifier for whom we will generate environment.'
                , 'test@localhost.com'
            )
            ->addArgument(
                'host'
                , InputArgument::OPTIONAL
                , 'Api host on my environment.'
                , 'https://dev.achievernotifier.localhost/api/v1'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = (new DateTimeImmutable());
        $userIdentifier = $input->getArgument('userIdentifier');
        $host = $input->getArgument('host');
        $jsonSerializerContext = [
            JsonDecode::ASSOCIATIVE => true,
            JsonEncode::OPTIONS => JSON_UNESCAPED_SLASHES,
        ];

        $valueBuilder = function (
            string $key,
            string $value = '',
            string $type = 'default',
            bool $enabled = true,
        ) {
            $obj = new class {
                public string $key;
                public string $value = '';
                public string $type = 'default';
                public bool $enabled = true;

                public function toArray(): array
                {
                    return [
                        'key' => $this->key,
                        'value' => $this->value,
                        'type' => $this->type,
                        'enabled' => $this->enabled,
                    ];
                }
            };

            $obj->key = $key;
            $obj->value = $value;
            $obj->type = $type;
            $obj->enabled = $enabled;

            return $obj;
        };

        $io = new SymfonyStyle($input, $output);
        $envFilePath = sprintf(
            '%s/var/postman.env.%s.%s.json'
            ,$this->parameterBag->get('kernel.project_dir')
            ,$this->parameterBag->get('kernel.environment')
            ,$now->format('Y-m-d-H-i-s')
        );

        $io->title('Generate postman user environment file.');
        $io->info('Project environment:' . $this->parameterBag->get('kernel.environment'));
        $io->info('Environment file: ' . $envFilePath);
        $io->info('Generating for user with identifier: ' . $userIdentifier);

        $user = $this->userRepository->loadUserByIdentifier($userIdentifier);
        if (!$user instanceof User) {
            $user = $this->userRepository->find($userIdentifier);
        }

        if (!$user instanceof User) {
            $io->error('User not found by identifier.');

            return Command::FAILURE;
        }

        $io->info('User id is: ' . $user->getRawId());

        $fakedEmails = [];
        for ($i = 0; $i < 50; $i++) {
            $fakedEmails[] = $this->faker->email();
        }

        /** @var \App\Entity\Achievement[] $achievementsCollection */
        $achievementsCollection = $user->getAchievements();
        $achievements = [];
        $lists = [];
        foreach ($achievementsCollection as $achievement) {
            foreach ($achievement->getLists() as $list) {
                $achievements[] = [
                    'a' => $achievement->getRawId(),
                    'l' => $list->getRawId(),
                ];
                $lists[] = $list->getRawId();
            }
        }

        $userGroups = $user->getUserGroupRelations();
        $members = [];
        $counter = 0;
        foreach ($userGroups as $group) {
            if ($counter >= 5) {
                break;
            }
            foreach ($group->getUserGroup()->getUserGroupRelations() as $groupRelation) {
                $counter++;
                $members[] = [
                    'm' => $groupRelation->getMember()->getRawId(),
                    'g' => $groupRelation->getUserGroup()->getRawId(),
                ];
            }
        }

        $ownedGroupsCollection = $user->getOwnedUserGroups();
        $ownedGroups = [];
        $counter = 0;
        foreach ($ownedGroupsCollection as $ownedGroup) {
            if ($counter >= 3) {
                break;
            }
            $ownedGroups[] = $ownedGroup->getRawId();
            $members[] = [
                'm' => $ownedGroup->getOwner()->getRawId(),
                'g' => $ownedGroup->getRawId(),
            ];
            $counter++;
        }
        array_shift($members);

        $shareObjectTokens = [];
        $counter = 0;
        foreach ($members as $member) {
            if ($counter >= 3) {
                break;
            }

            $token = $this->shareObjectTokenRepository->findOneBy(
                [
                    'target' => 'achievement',
                    'owner' => $member['m'],
                ],
            );

            if (null !== $token) {
                $shareObjectTokens[] = str_replace('http:', 'https:', $token->getLinkWithToken());
                $counter++;
            }
        }
        $shareObjectTokens = array_unique($shareObjectTokens);

        $shareObjectTokensContent = $this->serializer->serialize(
            $shareObjectTokens
            , JsonEncoder::FORMAT
            , $jsonSerializerContext
        );

        $achievementsContent = $this->serializer->serialize(
            $achievements
            , JsonEncoder::FORMAT
            , $jsonSerializerContext
        );

        $listsContent = $this->serializer->serialize(
            $lists
            , JsonEncoder::FORMAT
            , $jsonSerializerContext
        );

        $ownedGroupsContent = $this->serializer->serialize(
            $ownedGroups
            , JsonEncoder::FORMAT
            , $jsonSerializerContext
        );

        $membersContent = $this->serializer->serialize(
            $members
            , JsonEncoder::FORMAT
            , $jsonSerializerContext
        );

        $fakedEmailsContent = $this->serializer->serialize(
            $fakedEmails
            , JsonEncoder::FORMAT
            , $jsonSerializerContext
        );

        $data = [
            "_postman_variable_scope" => "environment",
            "_postman_exported_at" => $now->format(DateTimeInterface::W3C),
            "_postman_exported_using" => "Postman/10.19.14",
            "id" => $user->getRawId(),
            "name" => "Achiever-DEV/" . $now->format('Y-m-d-H-i-s'),
            "values" => [
                $valueBuilder('host', $host)->toArray(),
                $valueBuilder('userIdentifier', $user->getUserIdentifier())->toArray(),
                $valueBuilder('password', 'password')->toArray(),
                $valueBuilder('userId', $user->getRawId())->toArray(),
                $valueBuilder('username', $user->getUsername())->toArray(),
                $valueBuilder('locale', $user->getLocale())->toArray(),
                $valueBuilder('jwt_token', '')->toArray(),
                $valueBuilder('jwt_refresh_token', '')->toArray(),
                $valueBuilder('userOwnedAchievements', $achievementsContent)->toArray(),
                $valueBuilder('userOwnedLists', $listsContent)->toArray(),
                $valueBuilder('ownedGroups', $ownedGroupsContent)->toArray(),
                $valueBuilder('userGroupsMembers', $membersContent)->toArray(),
                $valueBuilder('achievementShareTokens', $shareObjectTokensContent)->toArray(),
                $valueBuilder('fakedEmails', $fakedEmailsContent)->toArray(),
                $valueBuilder('fakedText', $this->faker->realText(10000))->toArray(),
                $valueBuilder('offset', '0')->toArray(),
                $valueBuilder('limit', '5')->toArray(),
            ],
        ];

        $content = $this->serializer->serialize(
            $data
            , JsonEncoder::FORMAT
            , $jsonSerializerContext
        );
        file_put_contents($envFilePath, $content);

        $io->success('Success');

        return Command::SUCCESS;
    }
}
