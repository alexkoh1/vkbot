<?php

declare(strict_types = 1);

namespace AppBundle\Repository;

use Doctrine\ORM\EntityManager;
use getjump\Vk\Core;

class UserRepository
{
    /**
     * @var Core
     */
    private $vk;

    /**
     * UserRepository constructor.
     *
     * @param Core $vk
     */
    public function __construct(Core $vk)
    {
        $this->vk = $vk;
    }

    public function getFio(int $id)
    {
        $this->vk->setToken('d4cb3360501d41a5b335bec5cf9890aee4e58de387478302c8301f4e10ffce11eb4c4e0c8cb33418be4b4');

        return $this->vk->request('users.get', ['user_ids' => $id])->getResponse();

    }
}