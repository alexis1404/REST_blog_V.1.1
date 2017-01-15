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
use AppBundle\Entity\UserGallery;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class UserManagerTest extends \PHPUnit_Framework_TestCase
{

    public function testGetAllUsersAction()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));
        $mockers['repo']->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue([$user = new User('username', 'email', 'role', 'active', 'password')]));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals([$user], $user_manager->getAllUser());

    }

    public function testGetOneUser()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));
        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($user = new User('username', 'email', 'role', 'active', 'password')));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals($user, $user_manager->getOneUser('4'));
    }

    public function testGetUsersLimitOffset()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));
        $mockers['repo']->expects($this->once())
            ->method('getLimitOffsetUser')
            ->will($this->returnValue([$user = new User('username', 'email', 'role', 'active', 'password')]));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals([$user], $user_manager->getUsersLimitOffset('1', '0'));
    }

    public function testDeleteUserUserNotFound()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('User not found', $user_manager->deleteUser('id_user'));

    }

    public function testDeleteUserSuccessNoPhotoNoGallery()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($user = new User('username', 'email', 'role', 'active', 'password')));

        $mockers['repo']->expects($this->once())
            ->method('removeObject')
            ->with($user);

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('User deleted', $user_manager->deleteUser('id_user'));
    }

    public function testDeleteUserWithPhotoAndGallery()
    {
        $mockers = $this->mockers();

        $user = new User('username', 'email', 'role', 'active', 'password');

        $user->setPhoto('web/uploads/photos_for_users/test_file.txt');

        $user_gallery = new UserGallery('test_file_1.txt', 'web/uploads/photos_for_users/test_file_1.txt', $user);

        $user->addImage($user_gallery);

        file_put_contents("web/uploads/photos_for_users/test_file.txt", "w");

        file_put_contents("web/uploads/photos_for_users/test_file_1.txt", "w");

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($user));

        $mockers['repo']->expects($this->once())
            ->method('removeObject')
            ->with($user);

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('User deleted', $user_manager->deleteUser('id_user'));
    }

    public function testReturnFindOneUserAccordingApiKey()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));
        $mockers['repo']->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($user = new User('username', 'email', 'role', 'active', 'password')));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals($user, $user_manager->findOneUserAccordingApiKey('api'));
    }

    public function testReturnFindOneUserAccordingLoginAndEmail()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));
        $mockers['repo']->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($user = new User('username', 'email', 'role', 'active', 'password')));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals($user, $user_manager->findOneUserAccordingLoginAndEmail('login', 'email'));

    }

    public function testCreateSuperUser()
    {
        $mockers = $this->mockers();


        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));
        $mockers['any_services']->expects($this->once())
            ->method('validator');
        $mockers['any_services']->expects($this->once())
            ->method('hashPassword')
            ->will($this->returnValue(true));
        $mockers['repo']->expects($this->once())
            ->method('saverObject');

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals(true, is_string($user_manager->createSuperUser('shurik', 'shurik@mail.com', 'ROLE_USER', 0, 'password')));

    }

    public function testCreateStandardUser()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));
        $mockers['any_services']->expects($this->once())
            ->method('validator')
            ->will($this->returnValue(null));
        $mockers['any_services']->expects($this->once())
            ->method('hashPassword')
            ->will($this->returnValue(true));
        $mockers['repo']->expects($this->once())
            ->method('saverObject');

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('New user create! Sent a letter to the email to activate. Email:  ' .
            'alex@mail.com', $user_manager->createStandardUser('username', 'alex@mail.com', 'password', 'hostname'));
    }

    public function testUserActivation()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($user = new User('username', 'email', 'role', 'active', 'password')));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('User activate! Congratulation!', $user_manager->userActivation('7'));
    }

    public function testUserActivationUserNotFound()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('User not found!', $user_manager->userActivation('user_param'));
    }

    public function testLoginUserUserNotFound()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('User not found', $user_manager->loginUser('username', 'email', 'password'));
    }

    public function testLoginUserPasswordNotValid()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($user = new User('username', 'email', 'role', 'active', 'password')));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('Password not valid!', $user_manager->loginUser('username', 'email', 'password'));
    }

    public function testLoginUserSuccessful()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($user = new User('username', 'email', 'role', 'active', 'password')));

        $mockers['any_services']->expects($this->once())
            ->method('passwordValidator')
            ->will($this->returnValue(true));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals(true, is_string($user_manager->loginUser('username', 'email', 'password')));
    }

    public function testLogoutUser()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('You logout', $user_manager->logoutUser(new User('username', 'email', 'role', 'active', 'password')));
    }

    public function testGetAllUserPostsUserNotFound()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals(null, $user_manager->getAllUserPosts('id_user'));
    }

    public function testGetAllUserPostsUserSuccessful()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($user = new User('username', 'email', 'role', 'active', 'password')));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals(new \Doctrine\Common\Collections\ArrayCollection(), $user_manager->getAllUserPosts('id_user'));

    }

    public function testEditUserUserNotFound()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('User not found', $user_manager->editUser('user_id', 'username', 'email', 'active', 'role', 'api_key', 'password'));
    }

    public function testEditUserUser()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));
        $mockers['any_services']->expects($this->once())
            ->method('validator')
            ->will($this->returnValue(null));
        $mockers['any_services']->expects($this->once())
            ->method('hashPassword')
            ->will($this->returnValue(true));
        $mockers['repo']->expects($this->once())
            ->method('saverObject');
        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($user = new User('username', 'email', 'role', 'active', 'password')));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('User edit successfully', $user_manager->editUser('user_id', 'username', 'email', 'active', 'role', 'api_key', 'password'));
    }

    public function testGetAllUserCommentsUserNotFound()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->with('id_user');

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('User not found', $user_manager->getAllUserComments('id_user'));
    }

    public function testGetAllUserCommentsSuccess()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue(new User('username', 'email', 'role', 'active', 'password')));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals(new \Doctrine\Common\Collections\ArrayCollection(), $user_manager->getAllUserComments('id_user'));
    }

    public function testUploadPhotoForUserUserNotFound()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('User not found', $user_manager->uploadPhotoForUser('file', 'id_user'));
    }

    public function testUploadPhotoForUserSuccess()
    {
        $mockers = $this->mockers();

        $file = tempnam(sys_get_temp_dir(), 'upl');

        $image = new UploadedFile(
            $file,
            'new_image.png');

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($user = new User('username', 'email', 'role', 'active', 'password')));

        $mockers['repo']->expects($this->any())
            ->method('saverObject')
            ->with($user);

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('uploads/photos_for_user/', $user_manager->uploadPhotoForUser($image, 'id_user'));
    }

    public function testUploadPhotoForUserPhotoAlreadyExist()
    {
        $mockers = $this->mockers();

        $file = tempnam(sys_get_temp_dir(), 'upl');

        file_put_contents("web/uploads/photos_for_users/test_file.txt", "w");

        $image = new UploadedFile(
            $file,
            'new_image.png');

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($user = new User('username', 'email', 'role', 'active', 'password')));

        $mockers['repo']->expects($this->any())
            ->method('saverObject')
            ->with($user);

        $user->setPhoto('web/uploads/photos_for_users/test_file.txt');

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('uploads/photos_for_user/', $user_manager->uploadPhotoForUser($image, 'id_user'));
    }

    public function testUploadPicturesInUserGalleryUserNotFound()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('User not found', $user_manager->uploadPicturesInUserGallery('file', 'id_user'));
    }

    public function testUploadPicturesInUserGallerySuccess()
    {
        $mockers = $this->mockers();

        $file = tempnam(sys_get_temp_dir(), 'upl');

        file_put_contents("web/uploads/photos_for_users/test_file.txt", "w");

        $image = new UploadedFile(
            $file,
            'new_image.png');

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($user = new User('username', 'email', 'role', 'active', 'password')));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('Files uploaded!', $user_manager->uploadPicturesInUserGallery([$image], 'id_user'));

        unlink('web/uploads/photos_for_users/test_file.txt');
    }

    public function testGetUserGalleryUserNotFound()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals(null, $user_manager->getUserGallery('id_user'));
    }

    public function testGetUserGallerySuccess()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($user = new User('username', 'email', 'role', 'active', 'password')));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals(new \Doctrine\Common\Collections\ArrayCollection(), $user_manager->getUserGallery('id_user'));
    }

    public function testDeleteImageInUserGalleryGalleryEmpty()
    {
        $mockers = $this->mockers();

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('Image not found', $user_manager->deleteImageInUserGallery('id_image'));
    }

    public function testDeleteImageInUserGallerySuccess()
    {
        $mockers = $this->mockers();

        file_put_contents("web/uploads/photos_for_users/test_file.txt", "w");

        $user = new User('username', 'email', 'role', 'active', 'password');

        $user_gallery = new UserGallery('image_name', 'web/uploads/photos_for_users/test_file.txt', $user);

        $user->addImage($user_gallery);

        $mockers['em']->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($mockers['repo']));

        $mockers['repo']->expects($this->once())
            ->method('find')
            ->will($this->returnValue($user_gallery));

        $user_manager = new UserManager($mockers['em'], $mockers['any_services'], $mockers['uploader'], $mockers['uploader']);

        $this->assertEquals('Image deleted', $user_manager->deleteImageInUserGallery('id_image'));
    }

    public function mockers()
    {
        $any_services = $this->getMockBuilder('AppBundle\Services\AnyServices')
            ->disableOriginalConstructor()->getMock();
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $repo = $this->getMockBuilder('AppBundle\Repository\UserRepository')
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
