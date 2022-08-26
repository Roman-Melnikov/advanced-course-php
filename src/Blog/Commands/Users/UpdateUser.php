<?php

namespace Melni\AdvancedCoursePhp\Blog\Commands\Users;

use Melni\AdvancedCoursePhp\Blog\User;
use Melni\AdvancedCoursePhp\Blog\UUID;
use Melni\AdvancedCoursePhp\Person\Name;
use Melni\AdvancedCoursePhp\Repositories\Interfaces\UsersRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateUser extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('users:update')
            ->setDescription('Updates a user')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a user to update'
            )
            ->addOption(
                'first-name',
                'f',
                InputOption::VALUE_OPTIONAL,
                'First name'
            )
            ->addOption(
                'last-name',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Last name'
            );
    }

    protected function execute(
        InputInterface $input, OutputInterface $output
    ): int
    {
        $firstName = $input->getOption('first-name');
        $lastName = $input->getOption('last-name');

        if (empty($firstName) && empty($lastName)) {
            $output->writeln(
                'Nothing to update'
            );
            return Command::SUCCESS;
        }

        $uuid = new UUID($input->getArgument('uuid'));

        $user = $this->usersRepository->get($uuid);

        $updatedName = new Name(
            firstName: !empty($firstName)
                ? $firstName : $user->getName()->getFirstName(),
            lastName: !empty($lastName)
                ? $lastName : $user->getName()->getLastName()
        );

        $updatedUser = new User(
            $uuid,
            $user->getUsername(),
            $user->getHashedPassword(),
            $updatedName
        );

        $this->usersRepository->save($updatedUser);

        $output->writeln("User updated: $uuid");

        return Command::SUCCESS;
    }
}