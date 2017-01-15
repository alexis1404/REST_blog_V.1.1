<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22.12.16
 * Time: 11:23
 */

namespace tests\AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use AppBundle\Entity\Post;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorComment()
    {
        $comment = new Comment('text_comment', 'author_comment');

        $this->assertNull($comment->getId());
        $this->assertEquals('text_comment', $comment->getTextComment());
        $this->assertEquals('author_comment', $comment->getAuthorComment());
        $this->assertEquals(new \DateTime('now'), $comment->getDateCreateComment());
    }

    public function testGettersAndSettersComment()
    {
        $comment = new Comment('text_comment', 'author_comment');

        $comment->setAuthorComment('name');
        $this->assertEquals('name', $comment->getAuthorComment());
        $comment->setDateCreateComment(new \DateTime('2016-10-08 17:53:28'));
        $this->assertEquals(new \DateTime('2016-10-08 17:53:28'), $comment->getDateCreateComment());
    }

    public function testAddUserComment()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $comment = new Comment('text_comment', 'author_comment');

        $this->assertEmpty($comment->getUserComment());

        $comment->setUserComment($user);

        $this->assertNotEmpty($comment->getUserComment());

        $this->assertEquals($user, $comment->getUserComment());
    }

    public function testAddPostComment()
    {
        $post = new Post('name_post', 'author_post', 'text_post');

        $comment = new Comment('text_comment', 'author_comment');

        $this->assertEmpty($comment->getPostComment());

        $comment->setPostComment($post);

        $this->assertNotEmpty($comment->getPostComment());

        $this->assertEquals($post, $comment->getPostComment());
    }
}
