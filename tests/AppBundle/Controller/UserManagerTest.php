<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 26.11.16
 * Time: 14:59
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Services\UserManager;
use AppBundle\Entity\User;


class UserManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAllUsersAction()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\UserRepository')
            ->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $repo->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue([$user = new User('shurik', 'shurik@mail.com', 'ROLE_USER', '1111', '2014:10:11', 0, 'password')]));


        $userManager = new UserManager($em);

        $this->assertEquals([$user], $userManager->getAllUser());

    }

    public function testGetOneUser()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\UserRepository')
            ->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $repo->expects($this->once())
            ->method('find')
            ->will($this->returnValue($user = new User('shurik', 'shurik@mail.com', 'ROLE_USER', '1111', '2014:10:11', 0, 'password')));

        $userManager = new UserManager($em);

        $this->assertEquals($user, $userManager->getOneUser('4'));
    }

    public function testGetUsersLimitOffset()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\UserRepository')
            ->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $repo->expects($this->once())
            ->method('getLimitOffsetUser')
            ->will($this->returnValue([$user = new User('shurik', 'shurik@mail.com', 'ROLE_USER', '1111', '2014:10:11', 0, 'password')]));

        $userManager = new UserManager($em);

        $this->assertEquals([$user], $userManager->getUsersLimitOffset('1', '0'));
    }

    public function testDeleteUser()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();

        $repo = $this->getMockBuilder('AppBundle\Repository\UserRepository')
            ->disableOriginalConstructor()->getMock();

        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $repo->expects($this->once())
            ->method('removeObject');

        $userManager = new UserManager($em);

        $this->assertEquals('User with ID  deleted', $userManager->deleteUser($user = new User('shurik', 'shurik@mail.com', 'ROLE_USER', '1111', '2014:10:11', 0, 'password')));

    }

    public function testUserSaveInDatabase()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\UserRepository')
            ->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $repo->expects($this->once())
            ->method('saverObject');

        $userManager = new UserManager($em);

        $user = new User('shurik', 'shurik@mail.com', 'ROLE_USER', '1111', '2014:10:11', 0, 'password');

        $userManager->userSaveInDatabase($user);
    }

    public function testReturnFindOneUserAccordingApiKey()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\UserRepository')
            ->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $repo->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($user = new User('shurik', 'shurik@mail.com', 'ROLE_USER', '1111', '2014:10:11', 0, 'password')));

        $userManager = new UserManager($em);

        $this->assertEquals($user, $userManager->findOneUserAccordingApiKey('api'));
    }

    public function testReturnFindOneUserAccordingLoginAndEmail()
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\UserRepository')
            ->disableOriginalConstructor()->getMock();
        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));
        $repo->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($user = new User('shurik', 'shurik@mail.com', 'ROLE_USER', '1111', '2014:10:11', 0, 'password')));

        $userManager = new UserManager($em);

        $this->assertEquals($user, $userManager->findOneUserAccordingLoginAndEmail('login', 'email'));

    }
}
