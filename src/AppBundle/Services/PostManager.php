<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class PostManager
{
    private $rero_post;

    public function __construct(EntityManager $em)
    {
        $this->rero_post = $em->getRepository('AppBundle:Post');
    }

    public function getAllPosts()
    {
        return $this->rero_post->findAll();
    }

    public function getOnePost($id_post)
    {
        return $this->rero_post->find($id_post);
    }

    public function getPostsLimitOffset($limit, $offset)
    {
        return $this->rero_post->getLimitOffsetPost($limit, $offset);
    }
}