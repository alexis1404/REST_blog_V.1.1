<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Services\PostManager;
use AppBundle\Entity\Post;

class PostManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAllPosts()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\PostRepository')
            ->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $repo->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue([$posts = new Post('name', 'author', 'bla-bla')]));

        $postManager = new PostManager($em);

        $this->assertEquals([$posts], $postManager->getAllPosts());
    }

    public function testGetOnePost()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\PostRepository')
            ->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $repo->expects($this->once())
            ->method('find')
            ->will($this->returnValue($post = new Post('name', 'author', 'bla-bla')));

        $postManager = new PostManager($em);

        $this->assertEquals($post, $postManager->getOnePost('7'));
    }

    public function testGetPostsLimitOffset()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\PostRepository')
            ->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $repo->expects($this->once())
            ->method('getLimitOffsetPost')
            ->will($this->returnValue([$posts = new Post('name', 'author', 'bla-bla')]));

        $postManager = new PostManager($em);

        $this->assertEquals([$posts], $postManager->getPostsLimitOffset('1', '0'));
    }

    public function testDeletePost()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\PostRepository')
            ->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $repo->expects($this->once())
            ->method('removeObject');

        $postManager = new PostManager($em);

        $post = new Post('name', 'author', 'bla-bla');

        $postManager->deletePost($post);
    }

    public function testSrvcFlush()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\PostRepository')
            ->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $em->expects($this->once())
            ->method('flush');

        $postManager = new PostManager($em);

        $postManager->srvcFlush();
    }

    public function testSavePostInDatabase()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\PostRepository')
            ->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $repo->expects($this->once())
            ->method('saverObject');

        $postManager = new PostManager($em);

        $postManager->savePostInDatabase(new Post('name', 'author', 'bla-bla'));
    }

}
