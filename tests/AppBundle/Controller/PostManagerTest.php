<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Services\PostManager;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PostManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAllPosts()
    {
        $mockers = $this->mocker();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue([$posts = new Post('name', 'author', 'bla-bla')]));

        $post_manager = new PostManager($mockers['em'], $mockers['any_services'], $mockers['uploader']);

        $this->assertEquals([$posts], $post_manager->getAllPosts());
    }

    public function testGetOnePost()
    {
        $mockers = $this->mocker();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($post = new Post('name', 'author', 'bla-bla')));

        $post_manager = new PostManager($mockers['em'], $mockers['any_services'], $mockers['uploader']);

        $this->assertEquals($post, $post_manager->getOnePost('7'));
    }

    public function testGetPostsLimitOffset()
    {
        $mockers = $this->mocker();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('getLimitOffsetPost')
            ->will($this->returnValue([$posts = new Post('name', 'author', 'bla-bla')]));

        $post_manager = new PostManager($mockers['em'], $mockers['any_services'], $mockers['uploader']);

        $this->assertEquals([$posts], $post_manager->getPostsLimitOffset('1', '0'));
    }

    public function testDeletePost()
    {
        $mockers = $this->mocker();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($post = new Post('name', 'author', 'bla-bla')));

        $mockers['repo']->expects($this->once())
            ->method('removeObject')
            ->with($post);

        $post_manager = new PostManager($mockers['em'], $mockers['any_services'], $mockers['uploader']);

        $post = new Post('name', 'author', 'bla-bla');

        $post_manager->deletePost($post);
    }

    public function testDeletePostPostNotFound()
    {
        $mockers = $this->mocker();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $post_manager = new PostManager($mockers['em'], $mockers['any_services'], $mockers['uploader']);

        $this->assertEquals('Post not found', $post_manager->deletePost($post = new Post('name', 'author', 'bla-bla')));

        $post_manager->deletePost($post);
    }

    public function testCreateNewPost()
    {
        $mockers = $this->mocker();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['any_services']->expects($this->once())
            ->method('validator')
            ->will($this->returnValue(true));

        $mockers['em']->expects($this->any())
            ->method('flush');

        $post_manager = new PostManager($mockers['em'], $mockers['any_services'], $mockers['uploader']);

        $this->assertEquals('Post created', $post_manager->createNewPost(
            $user = new User('shurik', 'shurik@mail.com', 'ROLE_USER', '1111', '2014:10:11', 0, 'password'),
            'name post',
            'text_post'
            ));
    }

    public function testEditPostPostNotFound()
    {
        $mockers = $this->mocker();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $post_manager = new PostManager($mockers['em'], $mockers['any_services'], $mockers['uploader']);

        $this->assertEquals('Post not found', $post_manager->editPost('id post', 'post name', 'post_text'));
    }

    public function testEditPostSuccess()
    {
        $mockers = $this->mocker();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($post = new Post('name', 'author', 'bla-bla')));

        $post_manager = new PostManager($mockers['em'], $mockers['any_services'], $mockers['uploader']);

        $this->assertEquals('Post edit', $post_manager->editPost('id post', 'post name', 'post_text'));
    }

    public function testGetAllCommentForThisPostPostNotFound()
    {
        $mockers = $this->mocker();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $post_manager = new PostManager($mockers['em'], $mockers['any_services'], $mockers['uploader']);

        $this->assertEquals(null, $post_manager->getAllCommentForThisPost('id_post'));
    }

    public function testGetAllCommentForThisPostSuccess()
    {
        $mockers = $this->mocker();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($post = new Post('name', 'author', 'bla-bla')));

        $post_manager = new PostManager($mockers['em'], $mockers['any_services'], $mockers['uploader']);

        $this->assertEquals(new \Doctrine\Common\Collections\ArrayCollection(), $post_manager->getAllCommentForThisPost('id_post'));
    }

    public function testUploadPictureForPostPostNotFound()
    {
        $mockers = $this->mocker();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $post_manager = new PostManager($mockers['em'], $mockers['any_services'], $mockers['uploader']);

        $this->assertEquals('Post not found', $post_manager->uploadPictureForPost('file', 'id_post'));
    }

    public function testUploadPictureForPostSuccess()
    {
        $mockers = $this->mocker();

        $file = tempnam(sys_get_temp_dir(), 'upl');

        $image = new UploadedFile(
            $file,
            'new_image.png');

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($post = new Post('name', 'author', 'bla-bla')));

        $mockers['repo']->expects($this->any())
            ->method('saverObject')
            ->with($post);

        $post_manager = new PostManager($mockers['em'], $mockers['any_services'], $mockers['uploader']);

        $this->assertEquals('uploads/pictures_for_posts/', $post_manager->uploadPictureForPost($image, 'id_post'));
    }

    public function testUploadPictureForPostWithAlreadyExistsFile()
    {
        $mockers = $this->mocker();

        $file = tempnam(sys_get_temp_dir(), 'upl');

        $image = new UploadedFile(
            $file,
            'new_image.png');

        file_put_contents("web/uploads/pictures_for_posts/test_file.txt", "w");

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($post = new Post('name', 'author', 'bla-bla')));

        $mockers['repo']->expects($this->any())
            ->method('saverObject')
            ->with($post);

        $post->setPicturePost('web/uploads/pictures_for_posts/test_file.txt');

        $post_manager = new PostManager($mockers['em'], $mockers['any_services'], $mockers['uploader']);

        $this->assertEquals('uploads/pictures_for_posts/', $post_manager->uploadPictureForPost($image, 'id_post'));
    }

    public function mocker()
    {
        $any_services = $this->getMockBuilder('AppBundle\Services\AnyServices')
            ->disableOriginalConstructor()->getMock();
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\PostRepository')
            ->disableOriginalConstructor()->getMock();
        $uploader = $this->getMockBuilder('AppBundle\Uploader\Uploader')
            ->disableOriginalConstructor()->getMock();

        return $mockers = [
            'any_services' => $any_services,
            'em' => $em,
            'repo' => $repo,
            'uploader' => $uploader
        ];
    }
}
