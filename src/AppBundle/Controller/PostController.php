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
    public function getAllPostsAction()
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
    public function getOnePostAction($id_post)
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
    public function getPostsLimitOffsetAction($limit, $offset)
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

    /**
     * @Route("/api/delete_post/{id_post}", name="delete_post")
     * @Method("GET")
     */
    public function deletePostAction($id_post)
    {
        $post = $this->get('post_manager')->getOnePost($id_post);

        if(empty($post)){

            throw new HttpException(204, 'Post not found');
        }

        $this->get('post_manager')->deletePost($post);

        return new JsonResponse('Post with ID ' . $id_post . ' deleted');
    }

    /**
     * @Route("/api/create_post", name="create_post")
     * @Method("POST")
     */
    /*
     * Ожидает JSON-запрос в таком формате:
     *
     * {

    "name_post": "Edit Example",
    "text_post": "Lorem ipsum dolor! Is issum post!"
}
     */
    public function createPostAction(Request $request)
    {
        $content = $request->getContent();

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }

        $post_data = json_decode($content, true);

        $actual_user = $this->getUser()->getUsername();

        $post = $actual_user->createNewPost($post_data['name_post'], $actual_user->getUsername(), $post_data['text_post']);

        $this->validator($post);

        $this->get('post_manager')->srvcFlush();

        return new JsonResponse('Post created');
    }

    /**
     * @Route("/api/edit_post/{id_post}", name="edit_post")
     * @Method("POST")
     */
    /*
     * Ожидает JSON-запрос в таком формате:
     *
     * {

    "name_post": "Edit Example",
    "text_post": "Lorem ipsum dolor! Is issum post!"
}
    Количество полей может быть любым
     */
    public function editPostAction($id_post, Request $request)
    {
        $content = $request->getContent();

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }

        $post_data = json_decode($content, true);

        $post = $this->get('post_manager')->getOnePost($id_post);

        if(empty($post)){

            throw new HttpException(204, 'Post not found');
        }

        if(isset($post_data['name_post'])){

            $post->setNamePost($post_data['name_post']);
        }

        if(isset($post_data['text_post'])){

            $post->setTextPost($post_data['text_post']);
        }

        $this->validator($post);

        $this->get('post_manager')->savePostInDatabase($post);

        return new JsonResponse('Post edit');
    }

    //service methods

    public function validator($object_validate)
    {
        $validator = $this->get('validator');
        $errors = $validator->validate($object_validate);

        if (count($errors) > 0) {

            $errorsString = (string) $errors;

            throw new HttpException(422, $errorsString);
        }
    }
}