<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * A console command that lists all the existing users.
 *
 * To use this command, open a terminal window, enter into your project directory
 * and execute the following:
 *
 *     $ php bin/console app:list-users
 *
 * See https://symfony.com/doc/current/cookbook/console/console_command.html
 * For more advanced uses, commands can be defined as services too. See
 * https://symfony.com/doc/current/console/commands_as_services.html
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class ListUsersCommand extends Command {
	private $entityManager;

	public function __construct(EntityManagerInterface $em) {
		parent::__construct();

		$this->entityManager = $em;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configure() {
		$this
		// a good practice is to use the 'app:' prefix to group all your custom application commands
		->setName('app:list-users')
			->setDescription('Lists all the existing users / Can list by role')
			->setHelp(<<<'HELP'
The <info>%command.name%</info> command lists all the users registered in the application:

  <info>php %command.full_name%</info>

By default the command only displays users with role ROLE_USER. Set the role with the
<comment>--role</comment> option:

  <info>php %command.full_name%</info> <comment>--max-results=2000</comment>

By default the command only displays the 50 most recent users. Set the number of
results to display with the <comment>--max-results</comment> option:

  <info>php %command.full_name%</info> <comment>--role=ROLE_ADMIN</comment> <comment>--max-results=2000</comment>

HELP
			)
		// commands can optionally define arguments and/or options (mandatory and optional)
		// see https://symfony.com/doc/current/components/console/console_arguments.html
			->addOption('role',null,InputOption::VALUE_OPTIONAL,'Sets the role of user to display','ROLE_USER')
			->addOption('max-results',null,InputOption::VALUE_OPTIONAL,'Limits the number of users listed',50)
		;
	}

	/**
	 * This method is executed after initialize(). It usually contains the logic
	 * to execute to complete this command task.
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$role = $input->getOption('role');
		$maxResults = $input->getOption('max-results');
		// Use ->findBy() instead of ->findAll() to allow result sorting and limiting
		//dump($role);
		$users = [];
		if ($role != null) {
			$users = $this->entityManager->getRepository(User::class)->findByRole($role);

		} else {
			$users = $this->entityManager->getRepository(User::class)->findBy([], ['id' => 'DESC'], $maxResults);
		}

		// Doctrine query returns an array of objects and we need an array of plain arrays
		$usersAsPlainArrays = array_map(function (User $user) {
			return [
				$user->getId(),
				$user->getUsername(),
				$user->getEmail(),
				implode(', ', $user->getRoles()),
			];
		}, $users);

		// In your console commands you should always use the regular output type,
		// which outputs contents directly in the console window. However, this
		// command uses the BufferedOutput type instead, to be able to get the output
		// contents before displaying them. This is needed because the command allows
		// to send the list of users via email with the '--send-to' option
		$bufferedOutput = new BufferedOutput();
		$io = new SymfonyStyle($input, $bufferedOutput);
		$io->table(
			['ID', 'Username', 'Email', 'Roles'],
			$usersAsPlainArrays
		);

		// instead of just displaying the table of users, store its contents in a variable
		$usersAsATable = $bufferedOutput->fetch();
		$output->write($usersAsATable);

		return 0;
	}
}
