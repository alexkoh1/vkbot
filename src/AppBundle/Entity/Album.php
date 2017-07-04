<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="album")
 */
class Album
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     *  идентификатор фотографии, которая является обложкой (0, если обложка отсутствует)
     *
     * @ORM\Column(type="integer")
     */
    private $thumb_id ;
    /**
     *  идентификатор владельца альбома
     *
     * @ORM\Column(type="integer")
     */
    private $owner_id ;
    /**
     *  название альбома
     *
     * @ORM\Column(type="string")
     */
    private $title ;
    /**
     *  описание альбома (не приходит для системных альбомов)
     *
     * @ORM\Column(type="string")
     */
    private $description ;
    /**
     *  дата создания альбома в формате unixtime (не приходит для системных альбомов)
     *
     * @ORM\Column(type="datetime")
     */
    private $created ;
    /**
     *  дата последнего обновления альбома в формате unixtime (не приходит для системных альбомов)
     *
     * @ORM\Column(type="datetime")
     */
    private $updated ;
    /**
     *  количество фотографий в альбоме
     *
     * @ORM\Column(type="integer")
     */
    private $size ;
    /**
     *  1, если текущий пользователь может загружать фотографии в альбом (при запросе информации об альбомах сообщества)
     *
     * @ORM\Column(type="boolean")
     */
    private $can_upload ;

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Album
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set thumbId
     *
     * @param integer $thumbId
     *
     * @return Album
     */
    public function setThumbId($thumbId)
    {
        $this->thumb_id = $thumbId;

        return $this;
    }

    /**
     * Get thumbId
     *
     * @return integer
     */
    public function getThumbId()
    {
        return $this->thumb_id;
    }

    /**
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return Album
     */
    public function setOwnerId($ownerId)
    {
        $this->owner_id = $ownerId;

        return $this;
    }

    /**
     * Get ownerId
     *
     * @return integer
     */
    public function getOwnerId()
    {
        return $this->owner_id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Album
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Album
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Album
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
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
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Album
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
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

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return Album
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set canUpload
     *
     * @param boolean $canUpload
     *
     * @return Album
     */
    public function setCanUpload($canUpload)
    {
        $this->can_upload = $canUpload;

        return $this;
    }

    /**
     * Get canUpload
     *
     * @return boolean
     */
    public function getCanUpload()
    {
        return $this->can_upload;
    }
}
