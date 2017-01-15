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
     *@Assert\Choice({"ROLE_ADMIN", "ROLE_USER"}, message="Invalid value! Only ROLE_USER or ROLE_ADMIN!")
     */
    private $role;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="user_create_date", type="datetime")
     * @Assert\DateTime()
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
     * @Assert\Choice({1, 0}, message="Invalid value! Only 1 or 0!")
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

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="user_comment", cascade={"persist", "remove"})
     *
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserGallery", mappedBy="owner_images", cascade={"persist", "remove"})
     */
    private $images;

    public function __construct($username, $email, $role, $active, $password)
    {
        $this->username = $username;
        $this->email = $email;
        $this->role = $role;
        $this->apiKey = bin2hex(random_bytes(32));
        $this->userCreateDate = new \DateTime('now');
        $this->active = $active;
        $this->password = $password;
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
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

    //logic in entity

    public function activationUser()
    {
        $this->active = 1;

    }

    public function logoutUser()
    {
        $this->apiKey = null;
    }

    public function createNewPost($name_post, $author_post, $text_post)
    {
        $post = new Post($name_post, $author_post, $text_post);

        $post->setUserPost($this);

        $this->addPost($post);

        return $post;
    }

    public function createNewComment($text_comment, $post_comment)
    {
        $comment = new Comment($text_comment, $this->getUsername());

        $comment->setPostComment($post_comment);

        $comment->setUserComment($this);

        $post_comment->addPostComment($comment);

        $this->addComment($comment);

        return $comment;
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

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return User
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add image
     *
     * @param \AppBundle\Entity\UserGallery $image
     *
     * @return User
     */
    public function addImage(\AppBundle\Entity\UserGallery $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \AppBundle\Entity\UserGallery $image
     */
    public function removeImage(\AppBundle\Entity\UserGallery $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }
}
