<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 7/4/17
 * Time: 11:28 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="photo")
 */
class Photo
{
    /**
     * идентификатор фотографии.
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * идентификатор альбома, в котором находится фотография.
     * @ORM\ManyToOne(targetEntity="Album")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     */
    private $album_id;

    /**
     * идентификатор владельца фотографии.
     * @ORM\Column(type="integer")
     */
    private $owner_id;

    /**
     * идентификатор пользователя, загрузившего фото (если фотография размещена в сообществе). Для фотографий, размещенных от имени сообщества, user_id = 100.
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * текст описания фотографии.
     * @ORM\Column(type="string")
     */
    private $text;

    /**
     * дата добавления в формате Unixtime.
     * @ORM\Column(type="integer")
     */
    private $date;

    /**
     * URL копии фотографии с максимальным размером 75x75px.
     * @ORM\Column(type="string")
     */
    private $photo_75;

    /**
     * URL копии фотографии с максимальным размером 130x130px.
     * @ORM\Column(type="string")
     */
    private $photo_130;

    /**
     * URL копии фотографии с максимальным размером 604x604px.
     * @ORM\Column(type="string")
     */
    private $photo_604;

    /**
     * URL копии фотографии с максимальным размером 807x807px.
     * @ORM\Column(type="string")
     */
    private $photo_807;

    /**
     * URL копии фотографии с максимальным размером 1280x1024px.
     * @ORM\Column(type="string")
     */
    private $photo_1280;

    /**
     * URL копии фотографии с максимальным размером 2560x2048px.
     * @ORM\Column(type="string")
     */
    private $photo_2560;

    /**
     * ширина оригинала фотографии в пикселах.
     * @ORM\Column(type="integer")
     */
    private $width;

    /**
     * высота оригинала фотографии в пикселах.
     * @ORM\Column(type="integer")
     */
    private $height;


    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Photo
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
     * Set ownerId
     *
     * @param integer $ownerId
     *
     * @return Photo
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
     * Set userId
     *
     * @param integer $userId
     *
     * @return Photo
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Photo
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
     * Set date
     *
     * @param integer $date
     *
     * @return Photo
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
     * Set photo75
     *
     * @param string $photo75
     *
     * @return Photo
     */
    public function setPhoto75($photo75)
    {
        $this->photo_75 = $photo75;

        return $this;
    }

    /**
     * Get photo75
     *
     * @return string
     */
    public function getPhoto75()
    {
        return $this->photo_75;
    }

    /**
     * Set photo130
     *
     * @param string $photo130
     *
     * @return Photo
     */
    public function setPhoto130($photo130)
    {
        $this->photo_130 = $photo130;

        return $this;
    }

    /**
     * Get photo130
     *
     * @return string
     */
    public function getPhoto130()
    {
        return $this->photo_130;
    }

    /**
     * Set photo604
     *
     * @param string $photo604
     *
     * @return Photo
     */
    public function setPhoto604($photo604)
    {
        $this->photo_604 = $photo604;

        return $this;
    }

    /**
     * Get photo604
     *
     * @return string
     */
    public function getPhoto604()
    {
        return $this->photo_604;
    }

    /**
     * Set photo807
     *
     * @param string $photo807
     *
     * @return Photo
     */
    public function setPhoto807($photo807)
    {
        $this->photo_807 = $photo807;

        return $this;
    }

    /**
     * Get photo807
     *
     * @return string
     */
    public function getPhoto807()
    {
        return $this->photo_807;
    }

    /**
     * Set photo1280
     *
     * @param string $photo1280
     *
     * @return Photo
     */
    public function setPhoto1280($photo1280)
    {
        $this->photo_1280 = $photo1280;

        return $this;
    }

    /**
     * Get photo1280
     *
     * @return string
     */
    public function getPhoto1280()
    {
        return $this->photo_1280;
    }

    /**
     * Set photo2560
     *
     * @param string $photo2560
     *
     * @return Photo
     */
    public function setPhoto2560($photo2560)
    {
        $this->photo_2560 = $photo2560;

        return $this;
    }

    /**
     * Get photo2560
     *
     * @return string
     */
    public function getPhoto2560()
    {
        return $this->photo_2560;
    }

    /**
     * Set width
     *
     * @param integer $width
     *
     * @return Photo
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     *
     * @return Photo
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set albumId
     *
     * @param \AppBundle\Entity\Album $albumId
     *
     * @return Photo
     */
    public function setAlbumId(\AppBundle\Entity\Album $albumId = null)
    {
        $this->album_id = $albumId;

        return $this;
    }

    /**
     * Get albumId
     *
     * @return \AppBundle\Entity\Album
     */
    public function getAlbumId()
    {
        return $this->album_id;
    }
}
