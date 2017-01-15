<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CommentController extends Controller
{
    /**
     * @Route("/api/get_all_comments", name="get_all_comments")
     * @Method("GET")
     */
    public function getAllCommentAction()
    {
        $all_comments = $this->get('comment_manager')->getAllComments();

        if(empty($all_comments)){

            throw new HttpException(204, 'Comments not found');
        }
        $result = [];

        foreach($all_comments as $value){

            $result[] = [
                'id' => $value->getId(),
                'author_comment' => $value->getAuthorComment(),
                'text_comment' => $value->getTextComment(),
                'date_create_comment' => $value->getDateCreateComment()
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/api/get_comment/{id_comment}", name="get_comment")
     * @Method("GET")
     */
    public function getOneCommentAction($id_comment)
    {
        $comment = $this->get('comment_manager')->getOneComment($id_comment);

        if(empty($comment)){

            throw new HttpException(204, 'Comment not found');
        }
        $result[] = [
            'id' => $comment->getId(),
            'author_comment' => $comment->getAuthorComment(),
            'text_comment' => $comment->getTextComment(),
            'date_create_comment' => $comment->getDateCreateComment()
        ];

        return new JsonResponse($result);
    }

    /**
     * @Route("/api/get_limit_offset_comments/{limit}/{offset}", name="get_comments_limit_offset")
     * @Method("GET")
     */
    public function getCommentsLimitOffsetAction($limit, $offset)
    {
        $all_comments = $this->get('comment_manager')->getLimitOffsetComments($limit, $offset);

        if(empty($all_comments)){

            throw new HttpException(204, 'Comments not found');
        }
        $result = [];

        foreach($all_comments as $value){

            $result[] = [
                'id' => $value->getId(),
                'author_comment' => $value->getAuthorComment(),
                'text_comment' => $value->getTextComment(),
                'date_create_comment' => $value->getDateCreateComment()
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/api/comment_delete/{id_comment}", name="comment_delete")
     * @Method("DELETE")
     */
    public function commentDeleteAction($id_comment)
    {
        return new JsonResponse($this->get('comment_manager')->deleteComment($id_comment));
    }

    /**
     * @Route("/api/create_comment/{id_post}", name="create_comment")
     * @Method("POST")
     */

    /*
     * Ожидает JSON-данные в таком виде:
     * {

    "text_comment": "Lorem ipsum dolor! Is issum post!"
}
    Создает новый коммент для поста c id_post
     */
    public function createCommentAction(Request $request, $id_post)
    {
        $content = $request->getContent();

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }
        $comment_data = json_decode($content, true);

        $actual_user = $this->getUser()->getUsername();

        return new JsonResponse($this->get('comment_manager')->createComment($comment_data['text_comment'], $actual_user, $id_post));
    }

    /**
     * @Route("/api/edit_comment/{id_comment}", name="edit_comment")
     * @Method("POST")
     */

    /*
     * Ожидает JSON-данные в таком виде:
     *
     * {

    "text_comment": "Lorem ipsum dolor! Is issum post!"

    }
     */
    public function editCommentAvtion(Request $request, $id_comment)
    {
        $content = $request->getContent();

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }
        $comment_data = json_decode($content, true);

        return new JsonResponse($this->get('comment_manager')->editComment($id_comment, $comment_data['text_comment']));
    }
}