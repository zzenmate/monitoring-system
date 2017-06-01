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

/**
 * Page Entity
 *
 * @ORM\Table(name="pages")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 *
 * @UniqueEntity("url")
 *
 * @Gedmo\Loggable(logEntryClass="AppBundle\Entity\Log")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
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
     */
    protected $id;

    /**
     * @var string $title Title
     *
     * @ORM\Column(type="text", nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
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
     */
    protected $url;

    /**
     * @var \DateTime $scannedAt Scanned At
     *
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Assert\Type(type="datetime")
     */
    protected $scannedAt;

    /**
     * @var PageStatusType $status Page status type
     *
     * @ORM\Column(name="status", type="PageStatusType", nullable=false)
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\PageStatusType")
     */
    protected $status = PageStatusType::NEW_PAGE;

    /**
     * @var int $version Version
     *
     * @ORM\Column(type="integer")
     * @ORM\Version
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
     * @param PageStatusType $status Page status type
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
