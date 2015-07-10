<?php

namespace An1zhegorodov\JQueueBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Job
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="jqueue_job", indexes={
 *  @ORM\Index(name="type_status_worker_created_idx", columns={"type_id", "status_id", "worker_id", "created"})
 * })
 */
abstract class Job extends BaseJob
{
    /**
     * @var integer
     *
     * @ORM\Column(name="type_id", type="smallint")
     */
    private $typeId;

    /**
     * @var array
     *
     * @ORM\Column(name="data", type="array")
     */
    private $data;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    public function __construct($typeId, array $data, $statusId = JobStatuses::SNEW, $workerId = 0)
    {
        parent::__construct($statusId, $workerId);
        $this->typeId = $typeId;
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return Job
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}