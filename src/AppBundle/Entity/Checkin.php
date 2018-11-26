<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Checkin
 *
 * @ORM\Table(name="checkin")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CheckinRepository")
 */
class Checkin
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
     * @var Repartition
     *
     * @ORM\ManyToOne(targetEntity="Repartition")
     * @ORM\JoinColumn(name="repartition_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $idRepartition;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="confirmation", type="datetime")
     */
    private $confirmation;


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
     * Set idRepartition
     *
     * @param integer $idRepartition
     *
     * @return Checkin
     */
    public function setIdRepartition($idRepartition)
    {
        $this->idRepartition = $idRepartition;

        return $this;
    }

    /**
     * Get idRepartition
     *
     * @return int
     */
    public function getIdRepartition()
    {
        return $this->idRepartition;
    }

    /**
     * Set confirmation
     *
     * @param \DateTime $confirmation
     *
     * @return Checkin
     */
    public function setConfirmation($confirmation)
    {
        $this->confirmation = $confirmation;

        return $this;
    }

    /**
     * Get confirmation
     *
     * @return \DateTime
     */
    public function getConfirmation()
    {
        return $this->confirmation;
    }
}

