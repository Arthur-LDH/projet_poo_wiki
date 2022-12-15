<?php

namespace App\Entity;

use App\Repository\ConsoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[UniqueEntity(fields: ['slug'], message: 'Ce slug est déjà utilisé')]
#[ORM\Entity(repositoryClass: ConsoleRepository::class)]
#[Vich\Uploadable]
class Console
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Article::class, mappedBy: 'console')]
    private Collection $articles;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $img = null;

    /**
     * @var null|File
     */
    #[Vich\UploadableField(mapping:"console_img", fileNameProperty:"img")]

    private ?File $consoleImgFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $release_date = null;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTimeImmutable());
        }
        $this->setImg("console_default.png");
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->addConsole($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            $article->removeConsole($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getConsoleImgFile(): ?File
    {
        return $this-> consoleImgFile;
    }

    public function setConsoleImgFile(File $img = null): void
    {
        $this->consoleImgFile = $img;

        if ($img){
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function generateSlug(EntityManager $em): self
    {
        $slugger = new AsciiSlugger();
        $tempSlug = $slugger->slug($this->getName());
        $exist = $em->getRepository(Article::class)->findOneBy(['slug' => $tempSlug]);
        if (!$exist) {
            $this->slug = $tempSlug;
        } else {
            $this->slug = $tempSlug . '-' . (string)$this->getId();
        }

        return $this;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->release_date;
    }

    public function setReleaseDate(?\DateTimeInterface $release_date): self
    {
        $this->release_date = $release_date;

        return $this;
    }
}
