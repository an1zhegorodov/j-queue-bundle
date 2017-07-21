<?php

namespace Mintobit\JQueueBundle\Command;

use Mintobit\JQueueBundle\Command\Exception\InvalidConsumerException;
use Mintobit\JQueueBundle\Command\Exception\InvalidJobTypeException;
use Mintobit\JQueueBundle\Services\JobConsumerInterface;
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
            ->addOption('consumer', null, InputOption::VALUE_REQUIRED, 'Job consumer service')
            ->addOption('delay', null, InputOption::VALUE_OPTIONAL, 'Delay in seconds between queue polls', 1)
            ->addOption('expires', null, InputOption::VALUE_REQUIRED, 'Seconds before the worker dies')
            ->addOption('no-keep', null, InputOption::VALUE_NONE, 'Do not keep processed items in queue');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $workerId = $input->getOption('id');
        $jobTypeTitle = strtolower($input->getOption('job_type'));
        $consumerId = $input->getOption('consumer');
        $noKeep = $input->getOption('no-keep');
        $expires = $input->getOption('expires');
        $delay = $input->getOption('delay');

        if (!is_numeric($expires) || !is_numeric($delay) || !is_numeric($workerId)) {
            throw new \InvalidArgumentException('Expires, delay and worker_id are expected to be numeric');
        }

        $container = $this->getContainer();
        $consumer = $container->get($consumerId, Container::NULL_ON_INVALID_REFERENCE);
        $jobRepository = $container->get('jqueue.job_repository', Container::EXCEPTION_ON_INVALID_REFERENCE);

        $jobTypeId = $container->getParameter(sprintf('jqueue.job_types.%s', $jobTypeTitle));
        $endTime = strtotime(sprintf('+%s seconds', $expires));

        if (!$jobTypeId) {
            throw new InvalidJobTypeException;
        }

        if (is_null($consumer) || !($consumer instanceof JobConsumerInterface)) {
            throw new InvalidConsumerException;
        }

        while (time() < $endTime) {
            $jobId = $jobRepository->pop($jobTypeId, $workerId);
            if (!$jobId) {
                sleep($delay);
                continue;
            }
            $consumer->consume($jobId);
            $jobRepository->done($jobId);
            if ($noKeep) {
                $jobRepository->delete($jobId);
            }
        }
    }
}