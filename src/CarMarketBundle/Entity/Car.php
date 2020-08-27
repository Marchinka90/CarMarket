<?php

namespace CarMarketBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Car
 *
 * @ORM\Table(name="cars")
 * @ORM\Entity(repositoryClass="CarMarketBundle\Repository\CarRepository")
 */
class Car
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
     * @Assert\NotNull(
     *      message="Make cannot be empty"
     * )
     * @Assert\Length(
     *      min = 4,
     *      minMessage = "Make must be at least 4 symbol",
     * )
     *
     * @var string
     *
     * @ORM\Column(name="make", type="string", length=255)
     */
    private $make;

    /**
     * @Assert\NotNull(
     *      message="Model cannot be empty"
     * )
     * @Assert\Length(
     *      min = 4,
     *      minMessage = "Model must be at least 4 symbol",
     * )
     * @var string
     *
     * @ORM\Column(name="model", type="string", length=255)
     */
    private $model;

    /**
     * @Assert\NotNull(
     *      message="Year cannot be empty"
     * )
     * @Assert\Length(
     *      min = 4,
     *      minMessage = "Year must be 4 symbol"
     * )
     *
     * @Assert\Regex(
     *     pattern="/^[0-9]+$/",
     *     match=true,
     *     message="Year can contain only digits"
     * )
     * @var int
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @Assert\NotNull(
     *      message="Power cannot be empty"
     * )
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Power must be at least 3 symbol"
     * )
     * @var string
     *
     * @ORM\Column(name="power", type="string", length=255)
     */
    private $power;

    /**
     * @Assert\NotNull(
     *      message="Travelled Distance cannot be empty"
     * )
     * @Assert\Length(
     *      min = 2,
     *      minMessage = "Year must be at least 2 symbol"
     * )
     *
     * @Assert\Regex(
     *     pattern="/^[0-9]+$/",
     *     match=true,
     *     message="Travelled Distance can contain only digits"
     * )
     * @var int
     *
     * @ORM\Column(name="travelledDistance", type="integer")
     */
    private $travelledDistance;

    /**
     * @Assert\NotNull(
     *      message="Color cannot be empty"
     * )
     * @Assert\Length(
     *      min = 2,
     *      minMessage = "Color must be at least 2 symbol"
     * )
     *
     * @Assert\Regex(
     *     pattern="/^[A-Za-z]+$/",
     *     match=true,
     *     message="Color cannot contain digit"
     * )
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=255)
     */
    private $color;

    /**
     * @Assert\NotNull(
     *      message="City cannot be empty"
     * )
     * @Assert\Length(
     *      min = 2,
     *      minMessage = "City must be at least 2 symbol"
     * )
     *
     * @Assert\Regex(
     *     pattern="/^[A-Za-z]+$/",
     *     match=true,
     *     message="City cannot contain digit"
     * )
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @Assert\NotNull(
     *      message="Image cannot be empty"
     * )
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255)
     */
    private $image;

    /**
     * @Assert\NotNull(
     *      message="Note cannot be empty"
     * )
     * @var string
     *
     * @ORM\Column(name="note", type="text")
     */
    private $note;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="authorId", type="integer")
     */
    private $authorId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="CarMarketBundle\Entity\User", inversedBy="cars")
     *
     * @ORM\JoinColumn(name="authorId", referencedColumnName="id")
     */
    private $author;

    /**
     * @Assert\NotNull(
     *      message="Price cannot be empty"
     * )
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;

    public function __construct()
    {
        $this->dateAdded = new \DateTime('now');
        $this->status = 1;
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
     * Set make
     *
     * @param string $make
     *
     * @return Car
     */
    public function setMake($make)
    {
        $this->make = $make;

        return $this;
    }

    /**
     * Get make
     *
     * @return string
     */
    public function getMake()
    {
        return $this->make;
    }

    /**
     * Set model
     *
     * @param string $model
     *
     * @return Car
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return Car
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set power
     *
     * @param string $power
     *
     * @return Car
     */
    public function setPower($power)
    {
        $this->power = $power;

        return $this;
    }

    /**
     * Get power
     *
     * @return string
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * Set travelledDistance
     *
     * @param integer $travelledDistance
     *
     * @return Car
     */
    public function setTravelledDistance($travelledDistance)
    {
        $this->travelledDistance = $travelledDistance;

        return $this;
    }

    /**
     * Get travelledDistance
     *
     * @return int
     */
    public function getTravelledDistance()
    {
        return $this->travelledDistance;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return Car
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Car
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Car
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Car
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     *
     * @return Car
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Car
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param User $author
     * @return Car
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {   
        return $this->author;
    }

    /**
     * @param integer $price
     * @return Car
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return integer
     */
    public function getPrice()
    {   
        return $this->price;
    }
}

