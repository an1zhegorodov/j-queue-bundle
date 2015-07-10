<?php

namespace An1zhegorodov\JQueueBundle\Command;

use An1zhegorodov\JQueueBundle\Entity\BaseJob;
use An1zhegorodov\JQueueBundle\Entity\Job;
use An1zhegorodov\JQueueBundle\Entity\JobRepositoryInterface;
use An1zhegorodov\JQueueBundle\Entity\JobStatuses;
use An1zhegorodov\JQueueBundle\Event\Events;
use An1zhegorodov\JQueueBundle\Event\JobReceivedEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;

class JQueueWorkerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('jqueue:worker:run')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'Unique worker id')
            ->addOption('job_type', null, InputOption::VALUE_REQUIRED, 'Job type for this worker to select')
            ->addOption('repository', null, InputOption::VALUE_OPTIONAL, 'Job repository service')
            ->addOption('delay', null, InputOption::VALUE_OPTIONAL, 'Delay in seconds between queue polls', 1)
            ->addOption('expires', null, InputOption::VALUE_REQUIRED, 'Seconds before the worker dies')
            ->addOption('no-keep', null, InputOption::VALUE_NONE, 'Do not keep processed items in queue');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $noKeep = $input->getOption('no-keep');
        $expires = $input->getOption('expires');
        $delay = $input->getOption('delay');
        $repositoryString = $input->getOption('repository');
        $jobTypeTitle = strtolower($input->getOption('job_type'));
        $workerId = $input->getOption('id');

        if (!is_numeric($expires) || !is_numeric($delay) || !is_numeric($workerId)) {
            $output->writeln(sprintf('<error>%s</error>', $this->getSynopsis()));
            return;
        }

        $container = $this->getContainer();
        $eventDispatcher = $container->get('event_dispatcher');
        $jobRepository = $container->get($repositoryString, Container::NULL_ON_INVALID_REFERENCE);

        $jobTypeId = $container->getParameter(sprintf('jqueue.job_types.%s', $jobTypeTitle));
        $endTime = strtotime(sprintf('+%s seconds', $expires));

        if (!$jobTypeId) {
            $output->writeln(sprintf('<error>%s</error>', $this->getSynopsis()));
            $output->writeln(sprintf('<error>%s</error>', 'Invalid job_type'));
            return;
        }

        if (is_null($jobRepository) && !($jobRepository instanceof JobRepositoryInterface)) {
            $output->writeln(sprintf('<error>%s</error>', $this->getSynopsis()));
            $output->writeln(sprintf('<error>%s</error>', 'Repository service does not exist'));
            return;
        }

        while (time() < $endTime) {
            /** @var BaseJob $job */
            $job = $jobRepository->pop($workerId, $jobTypeId);
            if ($job instanceof BaseJob) {
                $jobReceivedEvent = new JobReceivedEvent($job, $input, $output);
                $eventDispatcher->dispatch(Events::JOB_RECEIVED, $jobReceivedEvent);
                $job = $jobReceivedEvent->getJob();
                if ($noKeep) {
                    $jobRepository->remove($job);
                } else {
                    $job->setStatusId(JobStatuses::FINISHED);
                }
                $jobRepository->flush();
            }
            sleep($delay);
        }
    }
}