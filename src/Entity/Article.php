<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\String\Slugger\AsciiSlugger;


#[UniqueEntity(fields: ['slug'], message: 'Ce slug est déjà utilisé')]
#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[Vich\Uploadable]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: Console::class, inversedBy: 'articles')]
    private Collection $console;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Licence $licence = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Comments::class, orphanRemoval: true,)]
    #[ORM\OrderBy(["updatedAt" => "DESC"])]
    private Collection $comments;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $img = null;

    /**
     * @var null|File
     */
    #[Vich\UploadableField(mapping: "article_img", fileNameProperty: "img")]
    private ?File $articleImgFile = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?bool $state = null;

    #[ORM\Column(nullable: true)]
    private ?bool $moderated = null;

    public function __construct()
    {
        $this->console = new ArrayCollection();
        $this->comments = new ArrayCollection();
        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTimeImmutable());
        }
        $this->setImg("article_default.png");
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, console>
     */
    public function getConsole(): Collection
    {
        return $this->console;
    }

    public function addConsole(Console $console): self
    {
        if (!$this->console->contains($console)) {
            $this->console->add($console);
        }

        return $this;
    }

    public function removeConsole(Console $console): self
    {
        $this->console->removeElement($console);

        return $this;
    }

    public function getLicence(): ?licence
    {
        return $this->licence;
    }

    public function setLicence(?licence $licence): self
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
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

    public function getArticleImgFile()
    {
        return $this->articleImgFile;
    }

    public function setArticleImgFile(File $img = null)
    {
        $this->articleImgFile = $img;

        if ($img) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
    public function __toString(): string
    {
        return $this->getName();
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
            $this->slug = $tempSlug . '-' . $this->getId();
        }

        return $this;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function removeSlug(): void
    {
        $this->slug = null;
    }
    
    public function isState(): ?bool
    {
        return $this->state;
    }

    public function setState(?bool $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function isModerated(): ?bool
    {
        return $this->moderated;
    }

    public function setModerated(?bool $moderated): self
    {
        $this->moderated = $moderated;

        return $this;
    }
}
