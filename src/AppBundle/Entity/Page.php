<?php

namespace AppBundle\Entity;

use AppBundle\DBAL\Types\PageStatusType;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Page Entity
 *
 * @ORM\Table(name="pages")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 * @ORM\EntityListeners({"AppBundle\EntityListener\PageListener"})
 *
 * @UniqueEntity("url")
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Log")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @JMS\ExclusionPolicy("all")
 */
class Page
{
    use TimestampableEntity, SoftDeleteableEntity;

    /**
     * @var int $id ID
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     * @JMS\Groups({"page"})
     */
    protected $id;

    /**
     * @var string $title Title
     *
     * @ORM\Column(type="text", nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     *
     * @JMS\Expose
     * @JMS\Groups({"page"})
     */
    protected $title;

    /**
     * @var string $content Content
     *
     * @ORM\Column(type="text", nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     *
     * @Gedmo\Versioned
     *
     * @JMS\Expose
     * @JMS\Groups({"page"})
     */
    protected $content;

    /**
     * @var string $url Url
     *
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="255")
     * @Assert\Type(type="string")
     *
     * @JMS\Expose
     * @JMS\Groups({"page"})
     */
    protected $url;

    /**
     * Останній час сканування публікації
     *
     * @var \DateTime $scannedAt Scanned At
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Assert\Type(type="datetime")
     *
     * @JMS\Expose
     * @JMS\Groups({"page"})
     */
    protected $scannedAt;

    /**
     * @var PageStatusType $status Page status type
     *
     * @ORM\Column(name="status", type="PageStatusType", nullable=false)
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\PageStatusType")
     *
     * @JMS\Expose
     * @JMS\Groups({"page"})
     */
    protected $status = PageStatusType::NEW_PAGE;

    /**
     * Повертає кількість оновлень публікації
     *
     * @var int $version Version
     *
     * @ORM\Column(type="integer")
     * @ORM\Version
     *
     * @JMS\Expose
     * @JMS\Groups({"page"})
     */
    protected $version;

    /**
     * @var string $hash Hash
     *
     * @ORM\Column(type="string", length=50, nullable=false)
     *
     * @Assert\NotBlank()
     */
    protected $hash;

    /**
     * Get ID
     *
     * @return int ID
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title Title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set content
     *
     * @param string $content Content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set url
     *
     * @param string $url URL
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get scanned at
     *
     * @return \DateTime
     */
    public function getScannedAt()
    {
        return $this->scannedAt;
    }

    /**
     * Set scanned at
     *
     * @param \DateTime $scannedAt Scanned At
     *
     * @return $this
     */
    public function setScannedAt($scannedAt)
    {
        $this->scannedAt = $scannedAt;

        return $this;
    }

    /**
     * Get status
     *
     * @return PageStatusType
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param string $status Page status type
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get version
     *
     * @return int Version
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set version
     *
     * @param int $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string Hash
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return $this
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }
}
