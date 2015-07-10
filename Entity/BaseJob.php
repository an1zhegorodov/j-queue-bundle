<?php

namespace An1zhegorodov\JQueueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BaseJob
 *
 * @ORM\MappedSuperclass
 */
abstract class BaseJob
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="status_id", type="smallint")
     */
    protected $statusId;

    /**
     * @var integer
     *
     * @ORM\Column(name="worker_id", type="smallint")
     */
    protected $workerId;

    /**
     * @param int $statusId
     * @param int $workerId
     */
    public function __construct($statusId = JobStatuses::SNEW, $workerId = 0)
    {

        $this->statusId = $statusId;
        $this->workerId = $workerId;
    }
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set statusId
     *
     * @param integer $statusId
     * @return BaseJob
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;

        return $this;
    }

    /**
     * Get statusId
     *
     * @return integer 
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * Set workerId
     *
     * @param integer $workerId
     * @return BaseJob
     */
    public function setWorkerId($workerId)
    {
        $this->workerId = $workerId;

        return $this;
    }

    /**
     * Get workerId
     *
     * @return integer 
     */
    public function getWorkerId()
    {
        return $this->workerId;
    }
}
