<?php

namespace Mintobit\JQueueBundle\Services;

interface JobConsumerInterface
{
    /**
     * @param int $jobId
     *
     * @return void
     */
    public function consume($jobId);
}