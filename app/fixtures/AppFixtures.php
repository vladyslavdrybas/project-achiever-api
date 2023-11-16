<?php

namespace DataFixtures;

use App\Builder\UserBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use function sleep;
use function ucfirst;

class AppFixtures extends Fixture
{
    protected Generator $faker;
    protected BufferedOutput $output;
    protected ObjectManager $manager;

    public function __construct(
        protected readonly UserBuilder $userBuilder,
    ) {
        $this->faker = Factory::create();
        $this->output = new BufferedOutput(
            OutputInterface::VERBOSITY_NORMAL,
            true
        );
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->loadFixturesWithProgress([$this, 'user'], 7);
    }

    protected function loadFixturesWithProgress(callable $fixtureGeneratorFunction, int $entitiesAmount = 100): void
    {
        $progressBar = new ProgressBar($this->output, $entitiesAmount);
        $progressBar->setMessage('Loading... ' . ucfirst($fixtureGeneratorFunction[1]));
        $progressBar->setFormat('%message% %current%/%max% [%bar%] %percent:3s%% %memory:6s%');
        $progressBar->start();
        echo $this->output->fetch();

        $addCounter = 0;
        $lastOutputIndex = 0;
        for($i = 1; $i <= $entitiesAmount; $i++) {
            call_user_func($fixtureGeneratorFunction);

            $addCounter++;
            if ($i % 15 === 0) {
                $this->manager->flush();
                $addCounter = 0;
                sleep(1);
            }

            if ($i % 5 === 0) {
                $progressBar->advance(5);
                echo $this->output->fetch();
                $lastOutputIndex = $i;
            }
        }

        if ($addCounter > 0) {
            $this->manager->flush();
        }

        $progressBar->advance($entitiesAmount - $lastOutputIndex);
        echo $this->output->fetch();

        $progressBar->finish();

        echo "\n";
    }

    public function user(): void
    {
        $user = $this->userBuilder->baseUser($this->faker->email(), 'password');
        $this->manager->persist($user);
    }
}
