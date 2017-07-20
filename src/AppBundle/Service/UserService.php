<?php

declare(strict_types = 1);

namespace AppBundle\Service;


use AppBundle\Repository\UserRepository;

class UserService
{
    /**
     * Репозиторий пользователей вк
     *
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserFio($id) {
        return $this->userRepository->getFio($id);
    }

}