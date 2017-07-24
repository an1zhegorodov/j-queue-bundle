<?php

namespace Mintobit\JQueueBundle\Services;

use Mintobit\JobQueue\JobRepositoryInterface;

class JobProducer implements JobProducerInterface
{
    /**
     * @var JobRepositoryInterface
     */
    private $jobRepository;

    public function __construct(JobRepositoryInterface $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    /**
     * @param int   $typeId
     * @param array $data
     *
     * @return int Job identifier
     */
    public function produce($typeId, array $data)
    {
        return $this->jobRepository->push($typeId, $data);
    }
}