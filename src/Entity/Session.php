<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Safe\Exceptions\SessionException;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
	operations: [
		new Get(),
		new GetCollection(
            order: ['createdAt' => 'ASC']
        )
	],
)]
#[ApiResource(
	uriTemplate : '/session/{id}/reactions',
	operations : [
		new GetCollection()
	],
	uriVariables : [
		'id'=> new Link(
			fromClass : Session::class
		)
	],
	mercure : true,
	paginationItemsPerPage : 0
)]
#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $summary = null;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Feedback::class)]
    #[Groups(['feedback:read'])]
    private Collection $feedback;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Reaction::class)]
    private Collection $reactions;

    public function __construct()
    {
        $this->feedback = new ArrayCollection();
        $this->reactions = new ArrayCollection();
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

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

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

    /**
     * @return Collection<int, Feedback>
     */
    public function getFeedback(): Collection
    {
        return $this->feedback;
    }

    public function addFeedback(Feedback $feedback): self
    {
        if (!$this->feedback->contains($feedback)) {
            $this->feedback->add($feedback);
            $feedback->setSession($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): self
    {
        if ($this->feedback->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getSession() === $this) {
                $feedback->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reaction>
     */
    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    public function addReaction(Reaction $reaction): self
    {
        if (!$this->reactions->contains($reaction)) {
            $this->reactions->add($reaction);
            $reaction->setSession($this);
        }

        return $this;
    }

    public function removeReaction(Reaction $reaction): self
    {
        if ($this->reactions->removeElement($reaction)) {
            // set the owning side to null (unless already changed)
            if ($reaction->getSession() === $this) {
                $reaction->setSession(null);
            }
        }

        return $this;
    }
}
