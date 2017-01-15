<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

class CommentManager
{
    private $repo_comment;
    private $repo_post;
    private $em;
    private $any_services;

    public function __construct(EntityManager $em, AnyServices $anyServices)
    {
        $this->repo_comment = $em->getRepository('AppBundle:Comment');
        $this->repo_post = $em->getRepository('AppBundle:Post');
        $this->em = $em;
        $this->any_services = $anyServices;
    }

    public function getAllComments()
    {
        return $this->repo_comment->findAll();
    }

    public function getOneComment($id_comment)
    {
        return $this->repo_comment->find($id_comment);
    }

    public function getLimitOffsetComments($limit, $offset)
    {
        return $this->repo_comment->getLimitOffsetComments($limit, $offset);
    }

    public function deleteComment($id_comment)
    {
        $comment = $this->getOneComment($id_comment);

        if(!$comment){

            return 'Comment not found';
        }

        $this->repo_comment->removeObject($comment);

        return 'Comment deleted';
    }

    public function createComment($text_comment, $actual_user, $post_id)
    {
        $post = $this->repo_post->find($post_id);

        if(!$post){
            return 'Invalid post ID';
        }
        $comment = $actual_user->createNewComment(
            $text_comment,
            $post
        );

        $this->any_services->validator($comment);

        $this->em->flush();

        return 'Post create';
    }

    public function editComment($id_comment, $text_comment)
    {
        $actual_comment = $this->getOneComment($id_comment);

        if(!$actual_comment){
            return 'Comment not found';
        }
        $actual_comment->setTextComment($text_comment);

        $this->repo_comment->saverObject($actual_comment);

        return 'Comment edit';
    }
}