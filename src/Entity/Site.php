<?php

namespace App\Entity;

use App\Utils\SiteUtils;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="name_idx", columns={"name"}),
 *     @ORM\Index(name="url_idx", columns={"url"}),
 *     @ORM\Index(name="slug_idx", columns={"slug"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 */
class Site
{
    /**
     * @var string
     *
     * @ORM\Column(type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Url(
     *    message = "The url '{{ value }}' is not a valid url",
     * )
     */
    private $url;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @Gedmo\Slug(fields={"name","author"})
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $authorUrl;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hit;

    /**
     * @ORM\ManyToOne(targetEntity="Site")
     */
    private $parent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default":false})
     */
    private $isValidated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $validatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default":false})
     */
    private $isHighlight;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $updatedBy;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SiteHit", mappedBy="site")
     */
    private $siteHits;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SiteRate", mappedBy="site")
     */
    private $siteRates;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Language", mappedBy="site")
     */
    private $languages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Source", mappedBy="site")
     */
    private $sources;

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Site
     */
    public function setId(string $id): Site
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return Site
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     *
     * @return Site
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     *
     * @return Site
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     *
     * @return Site
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     *
     * @return Site
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthorUrl()
    {
        return $this->authorUrl;
    }

    /**
     * @param mixed $authorUrl
     *
     * @return Site
     */
    public function setAuthorUrl($authorUrl)
    {
        $this->authorUrl = $authorUrl;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param mixed $rate
     *
     * @return Site
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHit()
    {
        return $this->hit;
    }

    /**
     * @param mixed $hit
     *
     * @return Site
     */
    public function setHit($hit)
    {
        $this->hit = $hit;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     *
     * @return Site
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     *
     * @return Site
     */
    public function setPublishedAt(\DateTime $publishedAt): Site
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getisValidated()
    {
        return $this->isValidated;
    }

    /**
     * @param mixed $isValidated
     *
     * @return Site
     */
    public function setIsValidated($isValidated)
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getValidatedAt(): ?\DateTime
    {
        return $this->validatedAt;
    }

    /**
     * @param \DateTime $validatedAt
     *
     * @return Site
     */
    public function setValidatedAt(\DateTime $validatedAt): Site
    {
        $this->validatedAt = $validatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     *
     * @return Site
     */
    public function setCreated(\DateTime $created): Site
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     *
     * @return Site
     */
    public function setUpdated(\DateTime $updated): Site
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getisHighlight()
    {
        return $this->isHighlight;
    }

    /**
     * @param mixed $isHighlight
     *
     * @return Site
     */
    public function setIsHighlight($isHighlight)
    {
        $this->isHighlight = $isHighlight;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param mixed $createdBy
     *
     * @return Site
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param mixed $updatedBy
     *
     * @return Site
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSiteHits()
    {
        return $this->siteHits;
    }

    /**
     * @param mixed $siteHits
     *
     * @return Site
     */
    public function setSiteHits($siteHits)
    {
        $this->siteHits = $siteHits;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSiteRates()
    {
        return $this->siteRates;
    }

    /**
     * @param mixed $siteRates
     *
     * @return Site
     */
    public function setSiteRates($siteRates)
    {
        $this->siteRates = $siteRates;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @param mixed $languages
     *
     * @return Site
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * @param mixed $sources
     *
     * @return Site
     */
    public function setSources($sources)
    {
        $this->sources = $sources;

        return $this;
    }

    /**
     * @return SiteUtils
     */
    public function getUtils(): SiteUtils
    {
        return new SiteUtils($this);
    }
}