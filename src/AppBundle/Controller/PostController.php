<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PostController extends Controller
{
    /**
     * @Route("/api/get_all_posts", name="get_all_posts")
     * @Method("GET")
     */
    public function getAllPosts()
    {
        $all_post = $this->get('post_manager')->getAllPosts();

        if(empty($all_post)){

            throw new HttpException(204, 'Posts not found');
        }

        $result = [];

        foreach($all_post as $value){

            $result[] = [
                'id' => $value->getId(),
                'author_post' => $value->getAuthorPost(),
                'name_post' => $value->getNamePost(),
                'picture_post' => $value->getPicturePost() ? '/' . $value->getPicturePost() : null,
                'date_create_post' => $value->getDateCreatePost(),
                'text_post' => $value->getTextPost(),
                'id_author_post' => $value->getUserPost()->getId()
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/api/get_one_post/{id_post}", name="get_one_post")
     * @Method("GET")
     */
    public function getOnePost($id_post)
    {
        $post = $this->get('post_manager')->getOnePost($id_post);

        if(empty($post)){

            throw new HttpException(204, 'Post not found');
        }

        $result[] = [
            'id' => $post->getId(),
            'author_post' => $post->getAuthorPost(),
            'name_post' => $post->getNamePost(),
            'picture_post' => $post->getPicturePost() ? '/' . $post->getPicturePost() : null,
            'date_create_post' => $post->getDateCreatePost(),
            'text_post' => $post->getTextPost(),
            'id_author_post' => $post->getUserPost()->getId()
        ];

        return new JsonResponse($result);
    }

    /**
     * @Route("/api/get_post_limit_offset/{limit}/{offset}", name="get_posts_limit_offset")
     * @Method("GET")
     */
    public function getPostsLimitOffset($limit, $offset)
    {
        $posts = $this->get('post_manager')->getPostsLimitOffset($limit, $offset);

        if(empty($posts)){

            throw new HttpException(204, 'Posts not found');
        }

        $result = [];

        foreach($posts as $value){

            $result[] = [
                'id' => $value->getId(),
                'author_post' => $value->getAuthorPost(),
                'name_post' => $value->getNamePost(),
                'picture_post' => $value->getPicturePost() ? '/' . $value->getPicturePost() : null,
                'date_create_post' => $value->getDateCreatePost(),
                'text_post' => $value->getTextPost(),
                'id_author_post' => $value->getUserPost()->getId()
            ];
        }

        return new JsonResponse($result);
    }
}