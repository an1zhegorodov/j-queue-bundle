<?php

namespace An1zhegorodov\JQueueBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class JobEntityGenerateCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('jqueue:generate:entity')
            ->addOption('bundle', null, InputOption::VALUE_REQUIRED, 'Bundle name where entity will be generated')
            ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'Entity name')
            ->addOption('table', null, InputOption::VALUE_REQUIRED, 'Table name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $generator = $container->get('jqueue.generator.entity');
        $bundle = $container->get('kernel')->getBundle($input->getOption('bundle'));
        $generator->generate($bundle, $input->getOption('entity'), 'An1zhegorodov\JQueueBundle\Entity\JobRepository');
    }

//    protected function interact(InputInterface $input, OutputInterface $output)
//    {
//        $questionHelper = $this->getHelper('question');
//        $question = new Question('<info>Please, provide bundle name:</info> ', 'AppBundle');
//        $bundles = array_keys($this->getContainer()->get('kernel')->getBundles());
//        $question->setAutocompleterValues($bundles);
//        $question->setValidator(array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateBundleName'));
//        $answer = $questionHelper->ask($input, $output, $question);
//    }
}