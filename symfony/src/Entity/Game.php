<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\DBAL\Types\Types;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: GameRepository::class)]
#[UniqueEntity('title', message: 'game with this name is already existed')]
class Game
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['main', 'genre'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['main', 'genre'])]
    #[Assert\NotBlank(
        message: 'You should specify game title'
    )]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Gedmo\Slug(fields: ['title'])]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['main', 'genre'])]
    #[Assert\NotNull(
        message: 'No developer id or invalid id specified'
    )]
    private ?Developer $developer = null;

    /**
     * @ORM\Column(type="string")
     */
    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'games')]
    #[Groups('main')]
    #[Assert\NotNull(
        message: 'No genres id or invalid id specified'
    )]
    #[Assert\Count(
        min: 1,
        minMessage: 'You must specify at least one valid genre',
    )]
    private Collection $genres;

    #[Groups(['main', 'genre'])]
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected $createdAt;

    /**
     * Returns createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }


    public function __construct()
    {
        $this->genres = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getDeveloper(): ?Developer
    {
        return $this->developer;
    }

    public function setDeveloper(?Developer $developer): self
    {
        $this->developer = $developer;

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */

    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        $this->genres->removeElement($genre);

        return $this;
    }

}
