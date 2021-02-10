<?php

namespace App\Entity;

use App\Repository\CapsuleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CapsuleRepository::class)
 */
class Capsule
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
    private $kind;

    /**
     * @ORM\Column(type="string", length=10000)
     */
    private $attached_files;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiration_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $owner_email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archived;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $recipients;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(string $kind): self
    {
        $this->kind = $kind;

        return $this;
    }

    public function getAttachedFiles(): ?array
    {
        if ($this->attached_files)
            return unserialize($this->attached_files);
        return null;
    }

    public function setAttachedFiles(array $attached_files): self
    {
        $this->attached_files = serialize($attached_files);

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expiration_date;
    }

    public function setExpirationDate(\DateTimeInterface $expiration_date): self
    {
        $this->expiration_date = $expiration_date;

        return $this;
    }

    public function getOwnerEmail(): ?string
    {
        return $this->owner_email;
    }

    public function setOwnerEmail(string $owner_email): self
    {
        $this->owner_email = $owner_email;

        return $this;
    }

    public function getArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    public function getRecipients(): array
    {
        if (!$this->recipients)
            return [];
        return unserialize($this->recipients);
    }

    public function setRecipients(?array $recipients): self
    {
        if (!$recipients)
            $this->recipients = null;
        else
            $this->recipients = serialize($recipients);

        return $this;
    }

    public function hasExpired(): bool {
        return $this->getExpirationDate() < new \DateTime();
    }

    public function generateCode(): ?string
    {
        if ($this->code)
            return $this->code;

        $this->code = "";
        $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for ($i = 0; $i < 6; $i++) {
            $this->code .= $alphabet[rand(0, strlen($alphabet) - 1)];
        }

        return $this->code;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }
}
