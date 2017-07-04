<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="wall_post")
 */
class WallPost
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
    private $vkId;
    /**
     * @ORM\Column(type="integer")
     */
    private $fromId;
    /**
     * @ORM\Column(type="integer")
     */
    private $toId;
    /**
     * @ORM\Column(type="integer")
     */
    private $date;
    /**
     * @ORM\Column(type="string", length=20)
     */
    private $postType;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;
    /**
     * @ORM\OneToMany(targetEntity="Attachment", mappedBy="post", cascade={"persist"})
     */
    private $attachments;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPosted = 0;

    public function __construct()
    {
        $this->attachments = new ArrayCollection();
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
     * Set vkId
     *
     * @param integer $vkId
     *
     * @return WallPost
     */
    public function setVkId($vkId)
    {
        $this->vkId = $vkId;

        return $this;
    }

    /**
     * Get vkId
     *
     * @return integer
     */
    public function getVkId()
    {
        return $this->vkId;
    }

    /**
     * Set fromId
     *
     * @param integer $fromId
     *
     * @return WallPost
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
     * @return WallPost
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
     * Set date
     *
     * @param integer $date
     *
     * @return WallPost
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set postType
     *
     * @param integer $postType
     *
     * @return WallPost
     */
    public function setPostType($postType)
    {
        $this->postType = $postType;

        return $this;
    }

    /**
     * Get postType
     *
     * @return integer
     */
    public function getPostType()
    {
        return $this->postType;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return WallPost
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set attachments
     *
     * @param ArrayCollection $attachments
     *
     * @return WallPost
     */
    public function setAttachments(ArrayCollection $attachments)
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * Get attachments
     *
     * @return integer
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    public function setIsPosted(bool $isPosted) {
        $this->isPosted = $isPosted;
    }
}
