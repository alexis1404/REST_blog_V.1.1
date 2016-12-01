<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 */
class Post
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name_post", type="string", length=255)
     */
    private $namePost;

    /**
     * @var string
     *
     * @ORM\Column(name="picture_post", type="string", length=255, nullable=true)
     */
    private $picturePost;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_create_post", type="datetime")
     */
    private $dateCreatePost;

    /**
     * @var string
     *
     * @ORM\Column(name="author_post", type="string", length=255)
     */
    private $authorPost;

    /**
     * @var string
     *
     * @ORM\Column(name="text_post", type="text")
     */
    private $textPost;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="posts", cascade={"persist"})
     * @ORM\JoinColumn(name="user_post", referencedColumnName="id")
     */
    private $user_post;

    public function __construct($name_post, $date_create_post, $author_post)
    {
        $this->namePost = $name_post;
        $this->dateCreatePost = $date_create_post;
        $this->authorPost = $author_post;
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
     * Set namePost
     *
     * @param string $namePost
     *
     * @return Post
     */
    public function setNamePost($namePost)
    {
        $this->namePost = $namePost;

        return $this;
    }

    /**
     * Get namePost
     *
     * @return string
     */
    public function getNamePost()
    {
        return $this->namePost;
    }

    /**
     * Set picturePost
     *
     * @param string $picturePost
     *
     * @return Post
     */
    public function setPicturePost($picturePost)
    {
        $this->picturePost = $picturePost;

        return $this;
    }

    /**
     * Get picturePost
     *
     * @return string
     */
    public function getPicturePost()
    {
        return $this->picturePost;
    }

    /**
     * Set dateCreatePost
     *
     * @param \DateTime $dateCreatePost
     *
     * @return Post
     */
    public function setDateCreatePost($dateCreatePost)
    {
        $this->dateCreatePost = $dateCreatePost;

        return $this;
    }

    /**
     * Get dateCreatePost
     *
     * @return \DateTime
     */
    public function getDateCreatePost()
    {
        return $this->dateCreatePost;
    }

    /**
     * Set authorPost
     *
     * @param string $authorPost
     *
     * @return Post
     */
    public function setAuthorPost($authorPost)
    {
        $this->authorPost = $authorPost;

        return $this;
    }

    /**
     * Get authorPost
     *
     * @return string
     */
    public function getAuthorPost()
    {
        return $this->authorPost;
    }

    /**
     * Set textPost
     *
     * @param string $textPost
     *
     * @return Post
     */
    public function setTextPost($textPost)
    {
        $this->textPost = $textPost;

        return $this;
    }

    /**
     * Get textPost
     *
     * @return string
     */
    public function getTextPost()
    {
        return $this->textPost;
    }

    /**
     * Set userPost
     *
     * @param \AppBundle\Entity\User $userPost
     *
     * @return Post
     */
    public function setUserPost(\AppBundle\Entity\User $userPost = null)
    {
        $this->user_post = $userPost;

        return $this;
    }

    /**
     * Get userPost
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserPost()
    {
        return $this->user_post;
    }
}
