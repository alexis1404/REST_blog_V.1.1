<?php

namespace AppBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AnyServices
{
    private $validator;
    private $encoder;
    private $mailer;

    public function __construct(UserPasswordEncoder $encoder, ValidatorInterface $validator, \Swift_Mailer $mailer)
    {
        $this->encoder = $encoder;
        $this->validator = $validator;
        $this->mailer = $mailer;
    }

    public function hashPassword($user, $user_password)
    {

        $hash = $this->encoder->encodePassword($user, $user_password);

        return $hash;
    }

    public function validator($object_validate)
    {

        $errors = $this->validator->validate($object_validate);

        if (count($errors) > 0) {

            $errorsString = (string) $errors;

            throw new HttpException(422, $errorsString);
        }
    }

    public function sendEmail($heading, $from, $to, $text_message)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($heading)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($text_message);

        $this->mailer->send($message);
    }

    public function passwordValidator($user, $password)
    {
        return $this->encoder->isPasswordValid($user, $password);
    }
}