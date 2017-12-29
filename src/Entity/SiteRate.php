<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="site_id_idx", columns={"site_id"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\SiteRateRepository")
 */
class SiteRate
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
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxValue;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="siteRates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;
}
