<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        return new JsonResponse($this->get('user_manager')->deleteUser($id_user));
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
    "password": "qwerty"

}
    В результате будет создан уже активный юзер с заданными параметрами. Пароль шифруется при помощи bcrypt
    Будет возвращен ApiKey созданного юзера.
    */
    public function createNewSuperUserAction(Request $request)
    {
        $content = $request->getContent();

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }
        $user_data = json_decode($content, true);

        return new JsonResponse($this->get('user_manager')->createSuperUser(
            $user_data['username'],
            $user_data['email'],
            $user_data['role'],
            $user_data['active'],
            $user_data['password']
        ));
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
    public function createNewStandardUserAction(Request $request)
    {
        $content = $request->getContent();

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }
        $user_data = json_decode($content, true);

        return new JsonResponse($this->get('user_manager')->createStandardUser(
            $user_data['username'],
            $user_data['email'],
            $user_data['password'],
            $hostname = $request->getHost()
        ));

    }

    /**
     * @Route("/activation", name="user_activate")
     * @Method("GET")
     */
    public function activationUserAction(Request $request)
    {
        return new JsonResponse($this->get('user_manager')->userActivation($request->query->get('apikey')));
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
    public function loginUserAction(Request $request)
    {
        $content = $request->getContent();

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }
        $user_data = json_decode($content, true);

        return new JsonResponse($this->get('user_manager')->loginUser($user_data['username'], $user_data['email'], $user_data['password']));
    }

    /**
     * @Route("/api/logout", name="logout_user")
     * @Method("GET")
     */

    /*
     * Сбрасывает ApiKey юзера в NULL
     */
    public function userLogoutAction()
    {
        return new JsonResponse($this->get('user_manager')->logoutUser($this->getUser()->getUsername()));
    }

    /**
     * @Route("/api/get_user_posts/{id_user}", name="get_user_post")
     * @Method("GET")
     */
    public function getUserPostsAction($id_user)
    {
        $posts = $this->get('user_manager')->getAllUserPosts($id_user);

        if(!$posts){
            return new JsonResponse('Posts not found');
        }
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

    /**
     * @Route("/api/get_user_comment/{id_user}", name="get_user_comments")
     * @Method("GET")
     */
    public function getUserCommentsAction($id_user)
    {
        $comments = $this->get('user_manager')->getAllUserComments($id_user);

        if(!$comments){
            return new JsonResponse('Comments not found');
        }

        $result = [];

        foreach($comments as $value){

            $result[] = [
                'id' => $value->getId(),
                'author_comment' => $value->getAuthorComment(),
                'text_comment' => $value->getTextComment(),
                'date_create_comment' => $value->getDateCreateComment()
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/api/edit_user/{id_user}", name="edit_user")
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
    Количество полей может быть любым
    */
    public function editUser($id_user, Request $request)
    {
        $content = $request->getContent();

        if(empty($content)){

            throw new HttpException(400, 'Bad request!');
        }
        $user_data = json_decode($content, true);

        return new JsonResponse($this->get('user_manager')->editUser(
            $id_user,
            isset($user_data['username']) ? $user_data['username'] : null,
            isset($user_data['email']) ? $user_data['email'] : null,
            isset($user_data['active']) ? $user_data['active'] : null,
            isset($user_data['role']) ? $user_data['role'] : null,
            isset($user_data['api_key']) ? $user_data['api_key'] : null,
            isset($user_data['password']) ? $user_data['password'] : null
        ));
    }

    /**
     * @Route("/api/upload_avatar/{id_user}", name="upload_avatar")
     * @Method("POST")
     */
    /*
     * Загружает аватарку для юзера. Если у юзера уже есть аватар
     * он будет заменен на новый, а старый файл будет удален.
     */
    public function uploadAvatarAction(Request $request, $id_user)
    {
        $file = $request->files->get('user_avatar');

        return new JsonResponse($this->get('user_manager')->uploadPhotoForUser($file, $id_user));
    }

    /**
     * @Route("/api/upload_gallery/{id_user}", name="upload_images")
     * @Method("POST")
     */
    /*
     * Загружает файлы в галлерею юзера (мультизагрузка поддерживается)
     */
    public function uploadToGalleryUserAction(Request $request, $id_user)
    {
        $file = $request->files->get('user_images');

        return new JsonResponse($this->get('user_manager')->uploadPicturesInUserGallery($file, $id_user));
    }

    /**
     * @Route("/api/get_all_users_images/{id_user}", name="all_user_images")
     * @Method("GET")
     */
    public function getAllUsersImages($id_user)
    {
        $gallery = $this->get('user_manager')->getUserGallery($id_user);

        if($gallery == null)
        {
            return new JsonResponse('User not found!');
        }

        $result = [];

        foreach ($gallery as $value ) {

            $result[] = [
                'name_picture' => $value->getImageName(),
                'link_picture' => $value->getImagePath()
            ];
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/api/delete_image/{id_image}", name="delete_image_in_gallery")
     * @Method("GET")
     */
    public function deleteImageFromGallery($id_image)
    {
        return new JsonResponse($this->get('user_manager')->deleteImageInUserGallery($id_image));
    }
}