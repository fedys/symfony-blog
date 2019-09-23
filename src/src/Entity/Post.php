<?php

namespace App\Entity;

use App\Validator\Post as PostAssert;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity
 * @ORM\Table(name="post",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"url"})
 *     },
 *     indexes={
 *         @ORM\Index(columns={"date"}),
 *         @ORM\Index(columns={"enabled"})
 *     }
 * )
 * @PostAssert\Url
 */
class Post
{
    /**
     * @var string
     */
    const SERIALIZER_GROUP_LIST = 'list';

    /**
     * @var string
     */
    const SERIALIZER_GROUP_DETAIL = 'detail';

    /**
     * @var int|null
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Groups({"list", "detail"})
     * @SWG\Property(example=256)
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max="150")
     * @Groups({"list", "detail"})
     * @SWG\Property(example="Post title")
     */
    private $title;

    /**
     * @var string|null
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(max="15000")
     * @Groups({"detail"})
     * @SWG\Property(example="<p>Post text</p>")
     */
    private $text;

    /**
     * @var DateTime|null
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     * @Groups({"list", "detail"})
     */
    private $date;

    /**
     * @var array
     * @ORM\Column(type="simple_array", nullable=true)
     * @Groups({"detail"})
     * @SWG\Property(example={"first", "second"})
     */
    private $tags;

    /**
     * @var string|null
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     * @Groups({"list", "detail"})
     * @SWG\Property(example="/post-url")
     */
    private $url;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $views;

    public function __construct()
    {
        $this->tags = [];
        $this->date = new DateTime();
        $this->enabled = false;
        $this->views = 0;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     */
    public function setDate(?DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return int
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * @param int $views
     */
    public function setViews(int $views): void
    {
        $this->views = $views;
    }

    /**
     * @return bool
     */
    public function incViews(): bool
    {
        if ($this->enabled) {
            ++$this->views;

            return true;
        }

        return false;
    }
}
