<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="task_log")
 */
class TaskLog
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     */
    private $taskId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $time;

    /**
     * @ORM\ManyToOne(targetEntity="WallPost")
     * @ORM\JoinColumn(name="record_id", referencedColumnName="id")
     */
    private $recordId;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

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
     * Set time
     *
     * @param \DateTime $time
     *
     * @return TaskLog
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return TaskLog
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set taskId
     *
     * @param \AppBundle\Entity\Task $taskId
     *
     * @return TaskLog
     */
    public function setTaskId(\AppBundle\Entity\Task $taskId = null)
    {
        $this->taskId = $taskId;

        return $this;
    }

    /**
     * Get taskId
     *
     * @return \AppBundle\Entity\Task
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * Set recordId
     *
     * @param \AppBundle\Entity\WallPost $recordId
     *
     * @return TaskLog
     */
    public function setRecordId(\AppBundle\Entity\WallPost $recordId = null)
    {
        $this->recordId = $recordId;

        return $this;
    }

    /**
     * Get recordId
     *
     * @return \AppBundle\Entity\WallPost
     */
    public function getRecordId()
    {
        return $this->recordId;
    }
}
