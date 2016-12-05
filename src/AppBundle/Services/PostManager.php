<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class PostManager
{
    private $repo_post;

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->repo_post = $em->getRepository('AppBundle:Post');

        $this->em = $em;

    }

    public function getAllPosts()
    {
        return $this->repo_post->findAll();
    }

    public function getOnePost($id_post)
    {
        return $this->repo_post->find($id_post);
    }

    public function getPostsLimitOffset($limit, $offset)
    {
        return $this->repo_post->getLimitOffsetPost($limit, $offset);
    }

    public function deletePost($post)
    {
        $this->repo_post->removeObject($post);
    }

    public function srvcFlush()
    {
        $this->em->flush();
    }

    public function savePostInDatabase($post)
    {
        $this->repo_post->saverObject($post);
    }
}