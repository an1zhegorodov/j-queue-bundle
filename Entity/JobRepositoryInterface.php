<?php

namespace An1zhegorodov\JQueueBundle\Entity;

interface JobRepositoryInterface
{
    public function push(BaseJob $job);
    public function pop($workerId, $jobTypeId);
    public function remove(BaseJob $job);
    public function flush();
}