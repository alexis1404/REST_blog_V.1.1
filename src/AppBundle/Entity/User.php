<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(fields="username", message="User name already taken!")
 * @UniqueEntity(fields="email", message="Email already taken!")
 *
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=50, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Assert\Email()
     *
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="apiKey", type="string", length=100, nullable=true)
     *
     */
    private $apiKey;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=50)
     *
     */
    private $role;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="user_create_date", type="datetime")
     *
     */
    private $userCreateDate;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     *
     */
    private $photo;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     *
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     * @Assert\Length(
     * min="5",
     * max="30",
     * minMessage="Password too short!",
     * maxMessage="Password too long! Maximum - 30 symbols!"
     * )
     *
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="user_post", cascade={"persist", "remove"})
     *
     */
    private $posts;

    public function __construct($username, $email, $role, $apiKey, $createDate, $active, $password)
    {
        $this->username = $username;
        $this->email = $email;
        $this->role = $role;
        $this->apiKey = $apiKey;
        $this->userCreateDate = $createDate;
        $this->active = $active;
        $this->password = $password;
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     *
     * @return User
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return User
     */
    public function setRoles($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRoles()
    {
        return $this->role;
    }

    /**
     * Set userCreateDate
     *
     * @param \DateTime $userCreateDate
     *
     * @return User
     */
    public function setUserCreateDate($userCreateDate)
    {
        $this->userCreateDate = $userCreateDate;

        return $this;
    }

    /**
     * Get userCreateDate
     *
     * @return \DateTime
     */
    public function getUserCreateDate()
    {
        return $this->userCreateDate;
    }

    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return User
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * Constructor
     */

    public function eraseCredentials()
    {
    }

    public function getSalt()
    {

        return null;
    }


    public function userActivator()
    {
        $this->active = 1;
    }

    public function logout()
    {
        $this->apiKey = null;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    //logic in entity

    public function activationUser()
    {
        $this->active = 1;

    }

    public function logoutUser()
    {
        $this->apiKey = null;
    }

    public function createNewPost($name_post, $date_create_post, $author_post)
    {
        $post = new Post($name_post, $date_create_post, $author_post);

        $post->setUserPost($this);

        $this->addPost($post);

        return $post;
    }

    /**
     * Add post
     *
     * @param \AppBundle\Entity\Post $post
     *
     * @return User
     */
    public function addPost(\AppBundle\Entity\Post $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Remove post
     *
     * @param \AppBundle\Entity\Post $post
     */
    public function removePost(\AppBundle\Entity\Post $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }
}
