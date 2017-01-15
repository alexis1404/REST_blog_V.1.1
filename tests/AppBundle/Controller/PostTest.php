<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.12.16
 * Time: 21:54
 */

namespace tests\AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Entity\Comment;

class PostTest extends \PHPUnit_Framework_TestCase
{
    public function testPostConstructor()
    {
        $post = new Post('name_post', 'author_post', 'text_post');

        $this->assertEquals('name_post', $post->getNamePost());
        $this->assertEquals('author_post', $post->getAuthorPost());
        $this->assertEquals('text_post', $post->getTextPost());
        $this->assertEquals(new \DateTime('now'), $post->getDateCreatePost());
        $this->assertEquals(null, $post->getId());
    }

    public function testGettersSettersPost()
    {
        $post = new Post('name_post', 'author_post', 'text_post');

        $post->setDateCreatePost(new \DateTime('2016-10-08 17:53:28'));
        $this->assertEquals(new \DateTime('2016-10-08 17:53:28'), $post->getDateCreatePost());
        $post->setPicturePost('/path/your/picture');
        $this->assertEquals('/path/your/picture', $post->getPicturePost());
        $post->setAuthorPost('username');
        $this->assertEquals('username', $post->getAuthorPost());

    }

    public function testGetUserPostRelation()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $post = new Post('name_post', 'author_post', 'text_post');

        $this->assertEmpty($post->getUserPost());

        $post->setUserPost($user);

        $this->assertNotEmpty($post->getUserPost());

        $this->assertEquals($user, $post->getUserPost());
    }

    public function testAddCommentAndRemoveComment()
    {
        $comment = new Comment('text_comment', 'author_comment');

        $post = new Post('name_post', 'author_post', 'text_post');

        $this->assertEmpty($post->getPostComment());

        $post->addPostComment($comment);

        $this->assertNotEmpty($post->getPostComment());

        $this->assertEquals($comment, $post->getPostComment()[0]);

        $post->removePostComment($comment);

        $this->assertEmpty($post->getPostComment());
    }
}
