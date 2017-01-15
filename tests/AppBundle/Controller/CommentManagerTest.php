<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17.12.16
 * Time: 13:38
 */

namespace tests\AppBundle\Controller;

use AppBundle\Services\CommentManager;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;

class CommentManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAllComments()
    {
        $mocker = $this->mocker();

        $mocker['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mocker['repo']));

        $mocker['repo']->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue([$comments = new Comment('text_comment', 'author_comment')]));

        $comment_manager = new CommentManager($mocker['em'], $mocker['any_services']);

        $this->assertEquals([$comments], $comment_manager->getAllComments());
    }

    public function testGetOneComment()
    {
        $mocker = $this->mocker();

        $mocker['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mocker['repo']));

        $mocker['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($comment = new Comment('text_comment', 'author_comment')));

        $comment_manager = new CommentManager($mocker['em'], $mocker['any_services']);

        $this->assertEquals($comment, $comment_manager->getOneComment('id_comment'));

    }

    public function testGetLimitOffsetComments()
    {
        $mocker = $this->mocker();

        $mocker['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mocker['repo']));

        $mocker['repo']->expects($this->once())
            ->method('getLimitOffsetComments')
            ->will($this->returnValue([$comments = new Comment('text_comment', 'author_comment')]));

        $comment_manager = new CommentManager($mocker['em'], $mocker['any_services']);

        $this->assertEquals([$comments], $comment_manager->getLimitOffsetComments('limit', 'offset'));
    }

    public function testDeleteCommentCommentNotFound()
    {
        $mocker = $this->mocker();

        $mocker['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mocker['repo']));

        $comment_manager = new CommentManager($mocker['em'], $mocker['any_services']);

        $this->assertEquals('Comment not found', $comment_manager->deleteComment('id_comment'));
    }

    public function testDeleteCommentSuccess()
    {
        $mocker = $this->mocker();

        $mocker['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mocker['repo']));

        $mocker['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($comment = new Comment('text_comment', 'author_comment')));

        $mocker['repo']->expects($this->once())
            ->method('removeObject')
            ->with($comment);

        $comment_manager = new CommentManager($mocker['em'], $mocker['any_services']);

        $this->assertEquals('Comment deleted', $comment_manager->deleteComment('id_comment'));
    }

    public function testCreateCommentInvalidIdPost()
    {
        $mocker = $this->mocker();

        $mocker['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mocker['repo']));

        $comment_manager = new CommentManager($mocker['em'], $mocker['any_services']);

        $this->assertEquals('Invalid post ID', $comment_manager->createComment('text_comment', 'actual_user', 'post_id'));
    }

    public function testCreateCommentSuccess()
    {
        $mocker = $this->mocker();

        $mocker['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mocker['repo']));

        $mocker['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($post = new Post('name', 'author', 'bla-bla')));

        $comment_manager = new CommentManager($mocker['em'], $mocker['any_services']);

        $actual_user = new User('username', 'email', 'role', 'active', 'password');

        $mocker['any_services']->expects($this->once())
            ->method('validator');

        $mocker['em']->expects($this->once())
            ->method('flush');

        $this->assertEquals('Post create', $comment_manager->createComment('text_comment', $actual_user, 'post_id'));
    }

    public function testEditCommentCommentNotFound()
    {
        $mocker = $this->mocker();

        $mocker['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mocker['repo']));

        $comment_manager = new CommentManager($mocker['em'], $mocker['any_services']);

        $this->assertEquals('Comment not found', $comment_manager->editComment('id_comment', 'text_comment'));
    }

    public function testEditCommentSuccess()
    {
        $mocker = $this->mocker();

        $mocker['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mocker['repo']));

        $mocker['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($comment = new Comment('text_comment', 'author_comment')));

        $mocker['repo']->expects($this->once())
            ->method('saverObject')
            ->with($comment);

        $comment_manager = new CommentManager($mocker['em'], $mocker['any_services']);

        $this->assertEquals('Comment edit', $comment_manager->editComment('id_comment', 'text_comment'));
    }

    public function mocker()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $any_services = $this->getMockBuilder('AppBundle\Services\AnyServices')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\CommentRepository')
            ->disableOriginalConstructor()->getMock();

        return $mockers = [
            'em' => $em,
            'any_services' => $any_services,
            'repo' => $repo
        ];
    }
}
