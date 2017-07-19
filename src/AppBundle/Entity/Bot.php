<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bot")
 */
class Bot
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
    private $vkId;

    /**
     * @ORM\Column(type="string")
     */
    private $accessToken;

    /**
     * @ORM\Column(type="string", nullable=true )
     */
    private $phone;

    /**
     * @ORM\Column(type="string", nullable=true )
     */
    private $fio;

    /**
     * @ORM\Column(type="string", nullable=true )
     */
    private $password;

    /**
     * @ORM\Column(type="string", nullable=true )
     */
    private $appId;

    /**
     * @ORM\Column(type="string", nullable=true )
     */
    private $simId;

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
     * @param string $vkId
     *
     * @return Bot
     */
    public function setVkId($vkId)
    {
        $this->vkId = $vkId;

        return $this;
    }

    /**
     * Get vkId
     *
     * @return string
     */
    public function getVkId()
    {
        return $this->vkId;
    }

    /**
     * Set accessToken
     *
     * @param string $accessToken
     *
     * @return Bot
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get accessToken
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Bot
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set fio
     *
     * @param string $fio
     *
     * @return Bot
     */
    public function setFio($fio)
    {
        $this->fio = $fio;

        return $this;
    }

    /**
     * Get fio
     *
     * @return string
     */
    public function getFio()
    {
        return $this->fio;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Bot
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set appId
     *
     * @param string $appId
     *
     * @return Bot
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;

        return $this;
    }

    /**
     * Get appId
     *
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Set simId
     *
     * @param string $simId
     *
     * @return Bot
     */
    public function setSimId($simId)
    {
        $this->simId = $simId;

        return $this;
    }

    /**
     * Get simId
     *
     * @return string
     */
    public function getSimId()
    {
        return $this->simId;
    }
}
