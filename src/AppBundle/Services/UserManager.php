<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;

class UserManager
{
    private $repo_user;

    public function __construct(EntityManager $em)
    {
        $this->repo_user = $em->getRepository('AppBundle:User');
    }

    public function getAllUser()
    {
        return $this->repo_user->findAll();
    }

    public function getOneUser($id_user)
    {
        return $this->repo_user->find($id_user);
    }

    public function getUsersLimitOffset($limit, $offset)
    {
        return $this->repo_user->getLimitOffsetUser($limit, $offset);
    }

    public function deleteUser($user)
    {
        $this->repo_user->removeObject($user);
    }

    public function userSaveInDatabase($user)
    {
        $this->repo_user->saverObject($user);

        return true;
    }

    public function findOneUserAccordingApiKey($apiKey)
    {
        return $this->repo_user->findOneBy(['apiKey' => $apiKey]);
    }

    public function findOneUserAccordingLoginAndEmail($login, $email)
    {
        return $this->repo_user->findOneBy(['username' => $login, 'email' => $email]);
    }

}