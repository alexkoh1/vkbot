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
     * @ORM\Column(type="string")
     */
    private $fromId;

    /**
     * @ORM\Column(type="string")
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
     * @ORM\Column(type="datetime")
     */
    private $working_time_from;

    /**
     * @ORM\Column(type="datetime")
     */
    private $working_time_to;
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
     * @param string $fromId
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
     * @return string
     */
    public function getFromId()
    {
        return $this->fromId;
    }

    /**
     * Set toId
     *
     * @param string $toId
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
     * @return string
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
     * Set taskType
     *
     * @param \AppBundle\Entity\TaskType $taskType
     *
     * @return Task
     */
    public function setTaskType(\AppBundle\Entity\TaskType $taskType = null)
    {
        $this->taskType = $taskType;

        return $this;
    }

    /**
     * Get taskType
     *
     * @return \AppBundle\Entity\TaskType
     */
    public function getTaskType()
    {
        return $this->taskType;
    }

    /**
     * Set workingTimeFrom
     *
     * @param \DateTime $workingTimeFrom
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
     * @return \DateTime
     */
    public function getWorkingTimeFrom()
    {
        return $this->working_time_from;
    }

    /**
     * Set workingTimeTo
     *
     * @param \DateTime $workingTimeTo
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
     * @return \DateTime
     */
    public function getWorkingTimeTo()
    {
        return $this->working_time_to;
    }
}
