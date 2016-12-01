<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\HttpException;
use AppBundle\Entity\User;

class UserController extends Controller
{

    /**
     * @Route("/api/get_all_users", name="get_all_users")
     * @Method("GET")
     */
    public function getAllUsersAction()
    {
        $all_users = $this->get('user_manager')->getAllUser();

        $result = [];

        foreach($all_users as $value) {
            $result[] = [
                'id' => $value->getId(),
                'user_name' => $value->getUsername(),
                'user_mail' => $value->getEmail(),
                'user_role' => $value->getRoles(),
                'user_active' => $value->getActive(),
                'user_create_date' => $value->getUserCreateDate(),
                'user_api' => $value->getApiKey(),
                'user_photo' => $value->getPhoto() ?  '/' . $value->getPhoto() : null,

            ];

        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/api/get_user/{id_user}", name="get_user")
     * @Method("GET")
     */
    public function getOneUserAction($id_user)
    {
        $user = $this->get('user_manager')->getOneUser($id_user);

        if(!$user){

            return new JsonResponse('User not found');
        }

        $result[] = [
            'id' => $user->getId(),
            'user_name' => $user->getUsername(),
            'user_mail' => $user->getEmail(),
            'user_role' => $user->getRoles(),
            'user_active' => $user->getActive(),
            'user_create_date' => $user->getUserCreateDate(),
            'user_api' => $user->getApiKey(),
            'user_photo' => $user->getPhoto() ?  '/' . $user->getPhoto() : null,

        ];

        return new JsonResponse($result);
    }

    /**
     * @Route("/api/get_users_limit_offset/{limit}/{offset}", name="get_users_limit_offset")
     * @Method("GET")
     */
    public function getUsersLimitOffsetAction($limit, $offset)
    {
        $users = $this->get('user_manager')->getUsersLimitOffset($limit, $offset);

        $result = [];

        foreach($users as $value) {
            $result[] = [
                'id' => $value->getId(),
                'user_name' => $value->getUsername(),
                'user_mail' => $value->getEmail(),
                'user_role' => $value->getRoles(),
                'user_active' => $value->getActive(),
                'user_create_date' => $value->getUserCreateDate(),
                'user_api' => $value->getApiKey(),
                'user_photo' => $value->getPhoto() ?  '/' . $value->getPhoto() : null,

            ];

        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/api/user_delete/{id_user}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteUserAction($id_user)
    {
        $user = $this->get('user_manager')->getOneUser($id_user);

        if(!$user){

            return new JsonResponse('User not found');
        }

        return new JsonResponse($this->get('user_manager')->deleteUser($user));
    }

    /**
     * @Route("/api/create_superuser", name="create_superuser")
     * @Method("POST")
     */
    /*
    Ожидает JSON-данные в таком виде:
    {

    "username": "Shurik",
    "email": "shurik@gmail.com",
    "active": 1,
    "role": "ROLE_ADMIN",
    "api_key": "ekll@#0)llrfdvll232323245fffd",
    "password": "qwerty"

}
    В результате будет создан уже активный юзер с ролью ADMIN. Пароль шифруется при помощи bcrypt
    */
    public function createNewSuperUser(Request $request)
    {
        $content = $request->getContent();

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }

        $user_data = json_decode($content, true);

        $user = new User(
            $user_data['username'],
            $user_data['email'],
            $user_data['role'],
            $apiKey = bin2hex(random_bytes(32)),
            $createDate = new \DateTime('now'),
            $active = 1,
            $password = $user_data['password']
        );

        $this->validator($user);

        $user->setPassword($this->hashPassword($user, $user->getPassword()));

        $this->get('user_manager')->userSaveInDatabase($user);

        return new JsonResponse('User create');
    }

    /**
     * @Route("/api/create_user", name="create_user")
     * @Method("POST")
     */

    /*
    Ожидает JSON-данные в таком виде:
    {

    "username": "Shurik",
    "email": "shurik@gmail.com",
    "password": "qwerty"

}
    В результате будет создан неактивный юзер с ролью USER. Пароль шифруется при помощи bcrypt, а на
    указанный email будет отправлено письмо для активации юзера.
    */
    public function createNewStandardUser(Request $request)
    {
        $content = $request->getContent();

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }

        $user_data = json_decode($content, true);

        $user = new User(
            $user_data['username'],
            $user_data['email'],
            $role = 'ROLE_USER',
            $apiKey = bin2hex(random_bytes(32)),
            $createDate = new \DateTime('now'),
            $active = 0,
            $password = $user_data['password']
        );

        $this->validator($user);

        $user->setPassword($this->hashPassword($user, $user->getPassword()));

        $this->get('user_manager')->userSaveInDatabase($user);

        $heading = 'Hi, friend!';
        $from = 'grandShushpanchik@gmail.com';
        $setTo = $user_data['email'];
        $text_message = 'Welcome! Link for activation your account:  ' .'http://'. $request->getHost(). '/activation?apikey=' . $apiKey;

        $this->sendEmail($heading, $from, $setTo, $text_message);

        return new JsonResponse('New user create! Sent a letter to the email to activate. Email:  ' . $user_data['email']);
    }

    /**
     * @Route("/activation", name="user_activate")
     * @Method("GET")
     */
    public function activationUserAction(Request $request)
    {
        $apikey = $request->query->get('apikey');

        $user = $this->get('user_manager')->findOneUserAccordingApiKey($apikey);

        $user->activationUser();

        $this->get('user_manager')->userSaveInDatabase($user);

        return new JsonResponse('User activate! Congratulation!');
    }

    /**
     * @Route("/login", name="login_user")
     * @Method("POST")
     */

    /*
     * Ожидает данные в таком виде:
     *
     * {

    "username": "Alexis",
    "email": "luceatlux@gmail.com",
    "password": "qwerty"

}

     *
     * В случае успешной авторизации возвращает НОВЫЙ Api Key для юзера
     */
    public function loginUser(Request $request)
    {
        $content = $request->getContent();

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }

        $user_data = json_decode($content, true);

        $actual_user = $this->get('user_manager')->findOneUserAccordingLoginAndEmail($user_data['username'], $user_data['email']);

        if(!$actual_user){

            return new JsonResponse('User not found');
        }

        if($this->get('security.password_encoder')->isPasswordValid($actual_user, $user_data['password'])){

            $new_apiKey = bin2hex(random_bytes(32));

            $actual_user->setApiKey($new_apiKey);

            $this->get('user_manager')->userSaveInDatabase($actual_user);

            return new JsonResponse($new_apiKey);

        }else{

            return new JsonResponse('Password not valid!');
        }
    }

    /**
     * @Route("/api/logout", name="logout_user")
     * @Method("GET")
     */

    /*
     * Сбрасывает ApiKey юзера в NULL
     */
    public function userLogout()
    {
        $actual_user = $this->getUser()->getUsername();

        $actual_user->logoutUser();

        $this->get('user_manager')->userSaveInDatabase($actual_user);

        return new JsonResponse('User with ID ' . $actual_user->getId() . ' logout!');
    }

    /**
     * @Route("/api/get_user_posts/{id_user}", name="get_user_post")
     * @Method("GET")
     */
    public function getUserPosts($id_user)
    {
        $user = $this->get('user_manager')->getOneUser($id_user);

        if(!$user){

            return new JsonResponse('User not found');
        }

        $posts = $user->getPosts();

            $result = [];

            foreach ($posts as $value) {

                $result[] = [
                    'id' => $value->getId(),
                    'author_post' => $value->getAuthorPost(),
                    'name_post' => $value->getNamePost(),
                    'picture_post' => $value->getPicturePost() ? '/' . $value->getPicturePost() : null,
                    'date_create_post' => $value->getDateCreatePost(),
                    'text_post' => $value->getTextPost(),

                ];
            }

            return new JsonResponse($result);
    }

    //service methods

    public function hashPassword($user, $user_password)
    {
        $hash = $this->get('security.password_encoder')->encodePassword($user, $user_password);

        return $hash;
    }

    public function validator($object_validate)
    {
        $validator = $this->get('validator');
        $errors = $validator->validate($object_validate);

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

        $this->get('mailer')->send($message);
    }
}