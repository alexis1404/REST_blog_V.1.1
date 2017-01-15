<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use AppBundle\Uploader\Uploader;
use AppBundle\Entity\UserGallery;

class UserManager
{
    private $repo_user;
    private $any_services;
    private $uploader;
    private $uploader_gallery;
    private $repo_user_gallery;

    public function __construct(EntityManager $em, AnyServices $any_services, Uploader $uploader, Uploader $uploader_gallery)
    {
        $this->repo_user = $em->getRepository('AppBundle:User');
        $this->any_services = $any_services;
        $this->uploader = $uploader;
        $this->uploader_gallery = $uploader_gallery;
        $this->repo_user_gallery = $em->getRepository('AppBundle:UserGallery');
    }

    public function getAllUser()
    {
        return $this->repo_user->findAll();
    }

    public function getOneUser($id_user)
    {
        return $this->repo_user->find($id_user);
    }

    public function getUsersLimitOffset($limit, $offset)
    {
        return $this->repo_user->getLimitOffsetUser($limit, $offset);
    }

    public function deleteUser($id_user)
    {
        $user = $this->getOneUser($id_user);

        if(!$user){
            return 'User not found';
        }

        if($user->getPhoto()){
            unlink($user->getPhoto());
        }

        if($user->getImages()){
            $user_gallery = $user->getImages();

            foreach($user_gallery as $value){
                unlink($value->getImagePath());
            }
        }

        $this->repo_user->removeObject($user);

        return 'User deleted';
    }

    public function findOneUserAccordingApiKey($apiKey)
    {
        return $this->repo_user->findOneBy(['apiKey' => $apiKey]);
    }

    public function findOneUserAccordingLoginAndEmail($login, $email)
    {
        return $this->repo_user->findOneBy(['username' => $login, 'email' => $email]);
    }

    public function createSuperUser($username, $email, $role, $active, $password)
    {
        $user = new User(
            $username,
            $email,
            $role,
            $active,
            $password
        );

        $this->any_services->validator($user);

        $user->setPassword($this->any_services->hashPassword($user, $user->getPassword()));

        $this->repo_user->saverObject($user);

        return $user->getApiKey();
    }

    public function createStandardUser($username, $email, $password, $hostname)
    {
        $user = new User(
            $username,
            $email,
            'ROLE_USER',
            0,
            $password
        );

        $this->any_services->validator($user);

        $user->setPassword($this->any_services->hashPassword($user, $user->getPassword()));

        $this->repo_user->saverObject($user);

        $heading = 'Hi, friend!';
        $from = 'grandShushpanchik@gmail.com';
        $setTo = $email;
        $text_message = 'Welcome! Link for activation your account:  ' .'http://'. $hostname. '/activation?apikey=' . $user->getApiKey();

        $this->any_services->sendEmail($heading, $from, $setTo, $text_message);

        return 'New user create! Sent a letter to the email to activate. Email:  ' . $email;
    }

    public function userActivation($user_param)
    {
        $user = $this->findOneUserAccordingApiKey($user_param);

        if(!$user) {
            $user = $this->getOneUser($user_param);
            if(!$user){
                return 'User not found!';
            }
        }
        $user->activationUser();

        $this->repo_user->saverObject($user);

        return 'User activate! Congratulation!';
    }

    public function loginUser($username, $email, $password)
    {
        $actual_user = $this->findOneUserAccordingLoginAndEmail($username, $email);

        if(!$actual_user){

            return 'User not found';
        }

        if($this->any_services->passwordValidator($actual_user, $password)){

            $new_apiKey = bin2hex(random_bytes(32));

            $actual_user->setApiKey($new_apiKey);

            $this->repo_user->saverObject($actual_user);

            return $new_apiKey;

        }else{

            return 'Password not valid!';
        }
    }

    public function logoutUser($user)
    {
        $user->logoutUser();

        $this->repo_user->saverObject($user);

        return 'You logout';
    }

    public function getAllUserPosts($id_user)
    {
        $user = $this->getOneUser($id_user);

        if(!$user){

            return null;
        }

        return $posts = $user->getPosts();
    }

    public function editUser($user_id, $username, $email, $active, $role, $apikey, $password)
    {
        $actual_user = $this->getOneUser($user_id);

        if(!$actual_user){

            return 'User not found';

        }else{
            if($username != null)
            {
                $actual_user->setUsername($username);
            }
            if($email != null){

                $actual_user->setEmail($email);
            }
            if($active != null){

                $actual_user->setActive($active);
            }
            if($role != null){

                $actual_user->setRoles($role);
            }
            if($apikey != null){

                $actual_user->setApiKey($apikey);
            }
            if($password != null){

                $actual_user->setPassword($password);
            }

            $this->any_services->validator($actual_user);

            if($password != null){

                $actual_user->setPassword($this->any_services->hashPassword($actual_user, $password));
            }
            $this->repo_user->saverObject($actual_user);

            return 'User edit successfully';
        }
    }

    public function getAllUserComments($id_user)
    {
        $actual_user = $this->getOneUser($id_user);

        if(!$actual_user){

            return 'User not found';
        }

        return $actual_user->getComments();
    }

    public function uploadPhotoForUser($file, $id_user)
    {
        $actual_user = $this->getOneUser($id_user);

        if(empty($actual_user)){

            return 'User not found';
        }

        if($actual_user->getPhoto()){

            unlink($actual_user->getPhoto());

            $actual_user->setPhoto(NULL);

            $this->repo_user->saverObject($actual_user);
        }

        $file_name = $this->uploader->Upload($file);

        $picture_path = 'uploads/photos_for_user/' . $file_name;

        $actual_user->setPhoto($picture_path);

        $this->repo_user->saverObject($actual_user);

        return $picture_path;
    }

    public function uploadPicturesInUserGallery($file, $id_user)
    {
        $actual_user = $this->getOneUser($id_user);

        if(empty($actual_user)){

            return 'User not found';
        }

        foreach ($file as $value){

            $file_name = $this->uploader_gallery->Upload($value);

            $user_gallery = new UserGallery(
                $value->getClientOriginalName(),
                'uploads/users_gallery/' . $file_name,
                $actual_user
            );

            $this->repo_user_gallery->saverObject($user_gallery);
        }

        return 'Files uploaded!';
    }

    public function getUserGallery($id_user)
    {
        $actual_user = $this->getOneUser($id_user);

        if(empty($actual_user)){

            return null;
        }

        return $actual_user->getImages();
    }

    public function deleteImageInUserGallery($id_image)
    {
        $actual_image = $this->repo_user_gallery->find($id_image);

        if(empty($actual_image)){

            return 'Image not found';
        }

        unlink($actual_image->getImagePath());

        $this->repo_user_gallery->removeObject($actual_image);

        return 'Image deleted';
    }
}