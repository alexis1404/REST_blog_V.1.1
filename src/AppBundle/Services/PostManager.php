<?php

namespace AppBundle\Services;

use AppBundle\Uploader\Uploader;
use Doctrine\ORM\EntityManager;

class PostManager
{
    private $repo_post;
    private $any_services;
    private $em;
    private $uploader;

    public function __construct(EntityManager $em, AnyServices $anyServices, Uploader $uploader)
    {
        $this->repo_post = $em->getRepository('AppBundle:Post');
        $this->any_services = $anyServices;
        $this->em = $em;
        $this->uploader = $uploader;
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

    public function deletePost($id_post)
    {
        $post = $this->getOnePost($id_post);

        if(empty($post)){

            return 'Post not found';
        }

        $this->repo_post->removeObject($post);

        return 'Post deleted';
    }

    public function createNewPost($actual_user, $name_post, $text_post)
    {
        $post = $actual_user->createNewPost($name_post, $actual_user->getUsername(), $text_post);

        $this->any_services->validator($post);

        $this->em->flush();

        return 'Post created';
    }

    public function editPost($id_post, $post_name, $post_text)
    {
        $post = $this->getOnePost($id_post);

        if(empty($post)){

            return 'Post not found';
        }

        if($post_name != null){

            $post->setNamePost($post_name);
        }

        if($post_text != null){

            $post->setTextPost($post_text);
        }

        $this->any_services->validator($post);

        $this->repo_post->saverObject($post);

        return 'Post edit';
    }

    public function getAllCommentForThisPost($id_post)
    {
        $post = $this->getOnePost($id_post);

        if(!$post){

            return null;
        }

        return $post->getPostComment();
    }

    public function uploadPictureForPost($file, $id_post)
    {
        $actual_post = $this->getOnePost($id_post);

        if(empty($actual_post)){

            return 'Post not found';
        }

        if($actual_post->getPicturePost()){

            unlink($actual_post->getPicturePost());

            $actual_post->setPicturePost(NULL);

            $this->repo_post->saverObject($actual_post);
        }

        $file_name = $this->uploader->Upload($file);

        $picture_path = 'uploads/pictures_for_posts/' . $file_name;

        $actual_post->setPicturePost($picture_path);

        $this->repo_post->saverObject($actual_post);

        return $picture_path;
    }
}