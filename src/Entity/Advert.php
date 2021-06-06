<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Entity;

use App\Repository\AdvertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

/**
 * @ORM\Entity(repositoryClass=AdvertRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(indexes={@ORM\Index(columns={"title", "description"}, flags={"fulltext"})})
 */
class Advert
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=20, nullable=false)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastUpdated;

    /**
     * @ORM\Column(type="integer")
     */
    private $likes = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $unlikes = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="adverts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity=Lga::class, inversedBy="adverts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lga;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active = true;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="adverts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="adverts")
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="adverts")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="advert")
     */
    private $comments;

    /**
     * @ORM\Column(type="integer")
     */
    private $commentCount = 0;

    /**
     * @ORM\Column(type="json")
     */
    private $userLikes = [];

    /**
     * @ORM\Column(type="json")
     */
    private $userDislikes = [];

    /**
     * @ORM\OneToMany(targetEntity=Upload::class, mappedBy="advert")
     */
    private $photos;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $uploadToken;

    public function __construct()
    {
        $now = new \DateTime();
        $this->createdAt = $now;
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->photos = new ArrayCollection();

        // init upload token
        $ulid = new Ulid();
        $this->uploadToken = $ulid->toBase58();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->lastUpdated;
    }

    /**
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function setUpdatedAt(): self
    {
        $now = new \DateTime();
        $this->lastUpdated = $now;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getUnlikes(): ?int
    {
        return $this->unlikes;
    }

    public function setUnlikes(int $unlikes): self
    {
        $this->unlikes = $unlikes;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getLga(): ?Lga
    {
        return $this->lga;
    }

    public function setLga(?Lga $lga): self
    {
        $this->lga = $lga;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAdvert($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAdvert() === $this) {
                $comment->setAdvert(null);
            }
        }

        return $this;
    }

    public function getCommentCount(): ?int
    {
        return $this->commentCount;
    }

    public function setCommentCount(int $commentCount): self
    {
        $this->commentCount = $commentCount;

        return $this;
    }

    /**
     * Returns the user likes.
     */
    public function getUserLikes(): array
    {
        $userLikes = null !== $this->userLikes ? array_values($this->userLikes) : [];

        return array_unique($userLikes);
    }

    public function setUserLikes(array $userLikes): self
    {
        $this->userLikes = array_unique($userLikes);

        return $this;
    }

    /**
     * @param mixed $role
     */
    public function userLiked(?User $user)
    {
        if (null === $user) {
            return false;
        }
        if (\in_array($user->getId(), $this->getUserLikes(), true)) {
            return true;
        }

        return false;
    }

    public function addUserLike(User $user): self
    {
        $userLikes = $this->getUserLikes();
        $userLikes[] = $user->getId();

        return $this->setUserLikes($userLikes);
    }

    public function removeUserLike(User $user): self
    {
        $users = $this->getUserLikes();
        $pos = array_search($user->getId(), $users, true);
        if (false !== $pos) {
            unset($users[$pos]);
        }

        return $this->setUserLikes($users);
    }

    /**
     * Returns the user dislikes.
     */
    public function getUserDislikes(): array
    {
        $userDislikes = null !== $this->userDislikes ? array_values($this->userDislikes) : [];

        return array_unique($userDislikes);
    }

    public function setUserDislikes(array $userDislikes): self
    {
        $this->userDislikes = array_unique($userDislikes);

        return $this;
    }

    /**
     * @param mixed $role
     */
    public function userDisliked(?User $user)
    {
        if (null === $user) {
            return false;
        }
        if (\in_array($user->getId(), $this->getUserDislikes(), true)) {
            return true;
        }

        return false;
    }

    public function addUserDislike(User $user): self
    {
        $userDislikes = $this->getUserDislikes();
        $userDislikes[] = $user->getId();

        return $this->setUserDislikes($userDislikes);
    }

    public function removeUserDislike(User $user): self
    {
        $users = $this->getUserDislikes();
        $pos = array_search($user->getId(), $users, true);
        if (false !== $pos) {
            unset($users[$pos]);
        }

        return $this->setUserDislikes($users);
    }

    /**
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function updateLikesAndDislikes(): self
    {
        $this->likes = \count($this->getUserLikes());
        $this->unlikes = \count($this->getUserDislikes());

        return $this;
    }

    /**
     * @return Collection|Upload[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Upload $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setAdvert($this);
        }

        return $this;
    }

    public function removePhoto(Upload $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getAdvert() === $this) {
                $photo->setAdvert(null);
            }
        }

        return $this;
    }

    public function getUploadToken(): ?string
    {
        return $this->uploadToken;
    }

    public function setUploadToken(?string $uploadToken): self
    {
        $this->uploadToken = $uploadToken;

        return $this;
    }

    public function getRandomPhoto(): ?Upload
    {
        $items = $this->photos->getValues();

        if (0 === \count($items)) {
            return null;
        }

        return $items[array_rand($items)];
    }
}
