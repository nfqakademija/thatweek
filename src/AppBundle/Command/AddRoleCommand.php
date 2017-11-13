<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class AddRoleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:role:add')
            ->setDescription('Grants user role for specific user')
            ->setHelp('This command allows you to create a user...')
            ->addArgument('apiId', InputArgument::REQUIRED, 'ApiId of the user.')
            ->addArgument('role', InputArgument::REQUIRED, 'Role to grant.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $apiId = $input->getArgument('apiId');
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        /**
         * @var $user User
         */
        $user = $em->getRepository(User::class)->findOneBy(array('apiId' => $apiId));

        if($user != null) {
            $output->writeln('User id: ' . $user->getId());
            $output->writeln('Full name: ' . $user->getFirstName() . ' ' . $user->getLastName());
            $user->addRole($input->getArgument('role'));
            $em->flush();
            }
            else $output->writeln('No user was found');
    }
}
