<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="visitor_ip_idx", columns={"visitor_ip"}),
 *     @ORM\Index(name="created_by_idx", columns={"created_by_id"}),
 *     @ORM\Index(name="site_id_idx", columns={"site_id"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\SiteHitRepository")
 */
class SiteHit
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
     * @ORM\Column(type="string", length=255)
     */
    private $visitorIp;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="siteHits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

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
     * @return SiteHit
     */
    public function setId(string $id): SiteHit
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisitorIp()
    {
        return $this->visitorIp;
    }

    /**
     * @param mixed $visitorIp
     *
     * @return SiteHit
     */
    public function setVisitorIp($visitorIp): SiteHit
    {
        $this->visitorIp = $visitorIp;

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
     * @return SiteHit
     */
    public function setCreated(\DateTime $created): SiteHit
    {
        $this->created = $created;

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
     * @param User $user
     *
     * @return SiteHit
     */
    public function setCreatedBy(\App\Entity\User $user): SiteHit
    {
        $this->createdBy = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     *
     * @return SiteHit
     */
    public function setSite($site): SiteHit
    {
        $this->site = $site;

        return $this;
    }
}
