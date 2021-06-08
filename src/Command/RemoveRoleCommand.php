<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * A console command that creates users and stores them in the database.
 *
 * To use this command, open a terminal window, enter into your project
 * directory and execute the following:
 *
 *     $ php bin/console app:add-user
 *
 * To output detailed information, increase the command verbosity:
 *
 *     $ php bin/console app:add-user -vv
 *
 * See https://symfony.com/doc/current/cookbook/console/console_command.html
 * For more advanced uses, commands can be defined as services too. See
 * https://symfony.com/doc/current/console/commands_as_services.html
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class RemoveRoleCommand extends Command
{
    const MAX_ATTEMPTS = 5;

    private $io;
    private $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $encoder)
    {
        parent::__construct();

        $this->entityManager = $em;
        $this->passwordEncoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        // a good practice is to use the 'app:' prefix to group all your custom application commands
            ->setName('app:remove-role')
            ->setDescription('Remove a role a user has')
            ->setHelp($this->getCommandHelp())
        // commands can optionally define arguments and/or options (mandatory and optional)
        // see https://symfony.com/doc/current/components/console/console_arguments.html
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the new user')
            ->addArgument('role', InputArgument::OPTIONAL, 'Role to remove user')
        ;
    }

    /**
     * This optional method is the first one executed for a command after configure()
     * and is useful to initialize properties based on the input arguments and options.
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * This method is executed after initialize() and before execute(). Its purpose
     * is to check if some of the options/arguments are missing and interactively
     * ask the user for those values.
     *
     * This method is completely optional. If you are developing an internal console
     * command, you probably should not implement this method because it requires
     * quite a lot of work. However, if the command is meant to be used by external
     * users, this method is a nice way to fall back and prevent errors.
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('email') && null !== $input->getArgument('role')) {
            return;
        }

        $this->io->title('Remove Role Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:remove-role email ROLE_ROLENAME',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        // Ask for the email if it's not defined
        $email = $input->getArgument('email');
        if (null !== $email) {
            $this->io->text(' > <info>Email</info>: '.$email);
        } else {
            $email = $this->io->ask('Email', null);
            $input->setArgument('email', $email);
        }

        // Ask for the role if it's not defined
        $role = $input->getArgument('role');
        if (null !== $role) {
            $this->io->text(' > <info>Role</info>: '.$role);
        } else {
            $role = $this->io->ask('Role', null);
            $input->setArgument('role', $role);
        }
    }

    /**
     * This method is executed after interact() and initialize(). It usually
     * contains the logic to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');

        $user = $this->getUser($email);

        $roles = $user->getRoles();
        if (false !== ($key = array_search($role, $roles, true))) {
            unset($roles[$key]);
        }
        $user->setRoles($roles);

        //dump($user->getRoles());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->io->success(sprintf('%s was successfully removed from: (%s)', $role, $user->getEmail()));

        return 0;
    }

    private function getUser($email)
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        // first check if a user with the same email already exists.
        $existingUser = $userRepository->findOneBy(['email' => $email]);

        if (null === $existingUser) {
            throw new \RuntimeException(sprintf('No user registered with the "%s" email.', $email));
        }

        return $existingUser;
    }

    /**
     * The command help is usually included in the configure() method, but when
     * it's too long, it's better to define a separate method to maintain the
     * code readability.
     */
    private function getCommandHelp()
    {
        return <<<'HELP'
The <info>%command.name%</info> command removes a  role from user:

  <info>php %command.full_name%</info> <comment>email role</comment>


If you omit any of the three required arguments, the command will ask you to
provide the missing values:

HELP;
    }
}
