<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="task")
 */
class Task
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $fromId;

    /**
     * @ORM\Column(type="integer")
     */
    private $toId;

    /**
     * @ORM\ManyToOne(targetEntity="TaskType")
     * @ORM\JoinColumn(name="task_type_id", referencedColumnName="id")
     */
    private $taskType;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $time_started;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $time_finished;

    /**
     * @ORM\Column(type="integer")
     */
    private $pause_from;

    /**
     * @ORM\Column(type="integer")
     */
    private $pause_to;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $working_time_from;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $working_time_to;

    /**
     * @ORM\Column(type="string", nullable=true)
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
     * Set fromId
     *
     * @param integer $fromId
     *
     * @return Task
     */
    public function setFromId($fromId)
    {
        $this->fromId = $fromId;

        return $this;
    }

    /**
     * Get fromId
     *
     * @return integer
     */
    public function getFromId()
    {
        return $this->fromId;
    }

    /**
     * Set toId
     *
     * @param integer $toId
     *
     * @return Task
     */
    public function setToId($toId)
    {
        $this->toId = $toId;

        return $this;
    }

    /**
     * Get toId
     *
     * @return integer
     */
    public function getToId()
    {
        return $this->toId;
    }

    /**
     * Set timeStarted
     *
     * @param \DateTime $timeStarted
     *
     * @return Task
     */
    public function setTimeStarted($timeStarted)
    {
        $this->time_started = $timeStarted;

        return $this;
    }

    /**
     * Get timeStarted
     *
     * @return \DateTime
     */
    public function getTimeStarted()
    {
        return $this->time_started;
    }

    /**
     * Set timeFinished
     *
     * @param \DateTime $timeFinished
     *
     * @return Task
     */
    public function setTimeFinished($timeFinished)
    {
        $this->time_finished = $timeFinished;

        return $this;
    }

    /**
     * Get timeFinished
     *
     * @return \DateTime
     */
    public function getTimeFinished()
    {
        return $this->time_finished;
    }

    /**
     * Set pauseFrom
     *
     * @param integer $pauseFrom
     *
     * @return Task
     */
    public function setPauseFrom($pauseFrom)
    {
        $this->pause_from = $pauseFrom;

        return $this;
    }

    /**
     * Get pauseFrom
     *
     * @return integer
     */
    public function getPauseFrom()
    {
        return $this->pause_from;
    }

    /**
     * Set pauseTo
     *
     * @param integer $pauseTo
     *
     * @return Task
     */
    public function setPauseTo($pauseTo)
    {
        $this->pause_to = $pauseTo;

        return $this;
    }

    /**
     * Get pauseTo
     *
     * @return integer
     */
    public function getPauseTo()
    {
        return $this->pause_to;
    }

    /**
     * Set workingTimeFrom
     *
     * @param string $workingTimeFrom
     *
     * @return Task
     */
    public function setWorkingTimeFrom($workingTimeFrom)
    {
        $this->working_time_from = $workingTimeFrom;

        return $this;
    }

    /**
     * Get workingTimeFrom
     *
     * @return string
     */
    public function getWorkingTimeFrom()
    {
        return $this->working_time_from;
    }

    /**
     * Set workingTimeTo
     *
     * @param string $workingTimeTo
     *
     * @return Task
     */
    public function setWorkingTimeTo($workingTimeTo)
    {
        $this->working_time_to = $workingTimeTo;

        return $this;
    }

    /**
     * Get workingTimeTo
     *
     * @return string
     */
    public function getWorkingTimeTo()
    {
        return $this->working_time_to;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set taskType
     *
     * @param \AppBundle\Entity\TaskType $taskType
     *
     * @return Task
     */
    public function setTaskType(TaskType $taskType = null)
    {
        $this->taskType = $taskType;

        return $this;
    }

    /**
     * Get taskType
     *
     * @return TaskType
     */
    public function getTaskType()
    {
        return $this->taskType;
    }
}
