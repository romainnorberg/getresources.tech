<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="sessions")
 * @ORM\Entity(repositoryClass="App\Repository\SessionRepository")
 */
class Session
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=128)
     */
    private $sess_id;

    /**
     * @ORM\Column(type="blob")
     */
    private $sess_data;

    /**
     * @ORM\Column(type="integer", length=10)
     */
    private $sess_time;

    /**
     * @ORM\Column(type="integer", length=9)
     */
    private $sess_lifetime;
}
