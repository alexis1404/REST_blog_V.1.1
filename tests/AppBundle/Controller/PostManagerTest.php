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
            ->will($this->returnValue([$posts = new Post('name', 'dateCreate', 'author')]));

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
            ->will($this->returnValue($posts = new Post('name', 'dateCreate', 'author')));

        $postManager = new PostManager($em);

        $this->assertEquals($posts, $postManager->getOnePost('7'));
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
            ->will($this->returnValue([$posts = new Post('name', 'dateCreate', 'author')]));

        $postManager = new PostManager($em);

        $this->assertEquals([$posts], $postManager->getPostsLimitOffset('1', '0'));
    }

}
