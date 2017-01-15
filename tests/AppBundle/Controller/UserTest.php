<?php

namespace tests\AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Post;
use AppBundle\Entity\UserGallery;

class UserTest extends \PHPUnit_Framework_TestCase
{

    public function testUserConstructor()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $this->assertEquals(null, $user->getId());
        $this->assertEquals('Alex', $user->getUsername());
        $this->assertEquals('alex@mail.com', $user->getEmail());
        $this->assertEquals('ROLE_USER', $user->getRoles());
        $this->assertEquals(0, $user->getActive());
        $this->assertEquals('qwerty', $user->getPassword());
    }

    public function testUserCreateDateGetterAndSetter()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $user->setUserCreateDate(new \DateTime('now'));
        $this->assertEquals(new \DateTime('now'), $user->getUserCreateDate());
    }

    public function testUserPhotoPathSetterAndGetter()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $user->setPhoto('/path/your/photo');
        $this->assertEquals('/path/your/photo', $user->getPhoto());
    }

    public function testUserDummyMethods()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');
        $user->eraseCredentials();
        $this->assertEquals(null, $user->getSalt());
    }

    public function testActivationUser()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $user->activationUser();
        $this->assertEquals(1, $user->getActive());
    }

    public function testUserLogout()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $user->logoutUser();
        $this->assertEquals(null, $user->getApiKey());
    }

    public function testUserCreatePost()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $this->assertEmpty($user->getPosts()->toArray());
        $user->createNewPost('name_post', 'author_post', 'text_post');
        $this->assertNotEmpty($user->getPosts()->toArray());
    }

    public function testUserRemovePost()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $this->assertEmpty($user->getPosts()->toArray());
        $user->createNewPost('name_post', 'author_post', 'text_post');
        $this->assertNotEmpty($user->getPosts()->toArray());
        $user->removePost($user->getPosts()->toArray()[0]);
        $this->assertEmpty($user->getPosts()->toArray());
    }

    public function testUserCreateComment()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $this->assertEmpty($user->getComments()->toArray());
        $user->createNewComment('text_post', $post = new Post('name', 'author', 'bla-bla'));
        $this->assertNotEmpty($user->getComments()->toArray());
    }

    public function testUserRemoveComment()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $this->assertEmpty($user->getComments()->toArray());
        $user->createNewComment('text_post', $post = new Post('name', 'author', 'bla-bla'));
        $this->assertNotEmpty($user->getComments()->toArray());
        $user->removeComment($user->getComments()->toArray()[0]);
        $this->assertEmpty($user->getComments()->toArray());
    }

    public function testUserAddImageInGallery()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $user_gallery = new UserGallery('image_name', 'image_path', $user);

        $this->assertEmpty($user->getImages());
        $user->addImage($user_gallery);
        $this->assertNotEmpty($user->getImages());
    }

    public function testUserRemoveGallery()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $user_gallery = new UserGallery('image_name', 'image_path', $user);

        $this->assertEmpty($user->getImages());
        $user->addImage($user_gallery);
        $this->assertNotEmpty($user->getImages());
        $user->removeImage($user_gallery);
        $this->assertEmpty($user->getImages());
    }
}
