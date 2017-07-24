<?php

namespace Mintobit\JQueueBundle\Entity;

interface JobInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getTypeId();

    /**
     *
     * @param string|null $key
     *
     * @return mixed Returns all data by default OR data[$key] if $key is provided
     */
    public function getData($key = null);

    /**
     * This method name implies that implementation should provide the
     * same result as array_replace_recursive($this->getData(), $update)
     * @see http://php.net/manual/en/function.array-replace-recursive.php
     *
     * @param array $update
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function updateDataRecursive(array $update);

    /**
     * @return int
     */
    public function getStatusId();

    /**
     * @return int
     */
    public function getWorkerId();

    /**
     * @return \Datetime
     */
    public function getCreated();

    /**
     * @return \Datetime
     */
    public function getUpdated();
}