<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 15.12.16
 * Time: 14:37
 */

namespace tests\AppBundle\Controller;

use AppBundle\Services\AnyServices;
use AppBundle\Entity\User;

class AnyServicesTest extends \PHPUnit_Framework_TestCase
{
    public function testHashPassword()
    {
        $encoder = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\UserPasswordEncoder')
            ->disableOriginalConstructor()->getMock();
        $validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->disableOriginalConstructor()->getMock();
        $mailer = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()->getMock();

        $encoder->expects($this->once())
            ->method('encodePassword')
            ->will($this->returnValue('hash_password'));

        $any_services = new AnyServices($encoder, $validator, $mailer);

        $this->assertEquals('hash_password',
            $any_services->hashPassword(new User('shurik', 'shurik@mail.com', 'ROLE_USER', '1111', '2014:10:11', 0, 'password'), 'password'));
    }

    public function testValidatorFailureObject()
    {
        $encoder = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\UserPasswordEncoder')
            ->disableOriginalConstructor()->getMock();
        $validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->disableOriginalConstructor()->getMock();
        $mailer = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()->getMock();

        $validator->expects($this->once())
            ->method('validate')
            ->will($this->returnValue('error'));

        $any_services = new AnyServices($encoder, $validator, $mailer);

        $this->expectException('Symfony\Component\HttpKernel\Exception\HttpException');

        $any_services->validator(new User('shurik', 'shurik@mail.com', 'ROLE_USER', '1111', '2014:10:11', 0, 'password'));
    }

    public function testValidatorSuccess()
    {
        $encoder = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\UserPasswordEncoder')
            ->disableOriginalConstructor()->getMock();
        $validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->disableOriginalConstructor()->getMock();
        $mailer = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()->getMock();

        $validator->expects($this->once())
            ->method('validate')
            ->with($user = new User('shurik', 'shurik@mail.com', 'ROLE_USER', '1111', '2014:10:11', 0, 'password'));

        $any_services = new AnyServices($encoder, $validator, $mailer);

        $any_services->validator($user);
    }

    public function testMail()
    {
        $encoder = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\UserPasswordEncoder')
            ->disableOriginalConstructor()->getMock();
        $validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->disableOriginalConstructor()->getMock();
        $mailer = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()->getMock();

        $any_services = new AnyServices($encoder, $validator, $mailer);

        $any_services->sendEmail('heading', 'anyexample@mailer.com', 'example@mailer.com', 'text mesage');
    }

    public function testPasswordValidatorInvalidPassword()
    {
        $encoder = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\UserPasswordEncoder')
            ->disableOriginalConstructor()->getMock();
        $validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->disableOriginalConstructor()->getMock();
        $mailer = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()->getMock();

        $encoder->expects($this->once())
            ->method('isPasswordValid')
            ->with(
                $user = new User('shurik', 'shurik@mail.com', 'ROLE_USER', '1111', '2014:10:11', 0, 'password'),
                'password'
            );

        $any_services = new AnyServices($encoder, $validator, $mailer);

        $this->assertEquals(false, $any_services->passwordValidator(
            $user,
            'password'
        ));
    }

    public function testPasswordValidatorPasswordValid()
    {
        $encoder = $this->getMockBuilder('Symfony\Component\Security\Core\Encoder\UserPasswordEncoder')
            ->disableOriginalConstructor()->getMock();
        $validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->disableOriginalConstructor()->getMock();
        $mailer = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()->getMock();

        $encoder->expects($this->once())
            ->method('isPasswordValid')
            ->will($this->returnValue(true));

        $any_services = new AnyServices($encoder, $validator, $mailer);

        $this->assertEquals(true, $any_services->passwordValidator(
            new User('shurik', 'shurik@mail.com', 'ROLE_USER', '1111', '2014:10:11', 0, 'password'),
            'password'
        ));
    }
}
