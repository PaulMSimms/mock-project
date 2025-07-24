<?php

namespace App\Entity;

use App\Repository\BlogsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BlogsRepository::class)
 */
class Blogs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="text", length=100)
     */
    private $title;
    /**
     * @ORM\Column(type="text")
     */
    private $body;
    /**
     * @ORM\Column(type="text")
     * 
     */
    private $author;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setTitle(string $title): Blogs
    {
        $this->title = $title;
        return $this;
    }
    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function setBody(string $body): Blogs
    {
        $this->body = $body;
        return $this;
    }
    public function getBody(): ?string
    {
        return $this->body;
    }
    public function setAuthor(string $author): Blogs
    {
        $this->author = $author;
        return $this;
    }
    public function getAuthor(): ?string
    {
        return $this->author;
    }
}
