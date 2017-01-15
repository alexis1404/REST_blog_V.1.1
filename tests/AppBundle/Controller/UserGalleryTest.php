<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.01.17
 * Time: 17:57
 */

namespace tests\AppBundle\Controller;

use AppBundle\Entity\UserGallery;
use AppBundle\Entity\User;

class UserGalleryTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $user_gallery = new UserGallery('image_name', 'image_path', $user);

        $this->assertEquals(null, $user_gallery->getId());
        $this->assertEquals('image_name', $user_gallery->getImageName());
        $this->assertEquals('image_path', $user_gallery->getImagePath());
        $this->assertEquals($user, $user_gallery->getOwnerImages());
    }

    public function testSettersAndGetters()
    {
        $user = new User('Alex', 'alex@mail.com', 'ROLE_USER', 0, 'qwerty');

        $user_gallery = new UserGallery('image_name', 'image_path', $user);

        $user_gallery->setImageName('name');
        $this->assertEquals('name', $user_gallery->getImageName());
        $user_gallery->setImagePath('path');
        $this->assertEquals('path', $user_gallery->getImagePath());
        $user_gallery->setOwnerImages($user);
        $this->assertEquals($user, $user_gallery->getOwnerImages());
    }
}
