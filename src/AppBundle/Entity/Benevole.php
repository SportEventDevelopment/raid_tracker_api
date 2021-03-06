<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Benevole
 *
 * @ORM\Table(name="benevole")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BenevoleRepository")
 */
class Benevole
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $idUser;

    /**
     * @var Raid
     *
     * @ORM\ManyToOne(targetEntity="Raid")
     * @ORM\JoinColumn(name="raid_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $idRaid;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return Benevole
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return int
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set idRaid
     *
     * @param integer $idRaid
     *
     * @return Benevole
     */
    public function setIdRaid($idRaid)
    {
        $this->idRaid = $idRaid;

        return $this;
    }

    /**
     * Get idRaid
     *
     * @return int
     */
    public function getIdRaid()
    {
        return $this->idRaid;
    }
}

