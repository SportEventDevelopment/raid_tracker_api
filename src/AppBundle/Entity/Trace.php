<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trace
 *
 * @ORM\Table(name="trace")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TraceRepository")
 */
class Trace
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
     * @var Parcours
     *
     * @ORM\ManyToOne(targetEntity="Parcours")
     * @ORM\JoinColumn(name="parcours_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $idParcours;

    /**
     * @var bool
     *
     * @ORM\Column(name="isCalibre", type="boolean")
     */
    private $isCalibre;


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
     * Set idParcours
     *
     * @param integer $idParcours
     *
     * @return Trace
     */
    public function setIdParcours($idParcours)
    {
        $this->idParcours = $idParcours;

        return $this;
    }

    /**
     * Get idParcours
     *
     * @return int
     */
    public function getIdParcours()
    {
        return $this->idParcours;
    }

    /**
     * Set isCalibre
     *
     * @param boolean $isCalibre
     *
     * @return Trace
     */
    public function setIsCalibre($isCalibre)
    {
        $this->isCalibre = $isCalibre;

        return $this;
    }

    /**
     * Get isCalibre
     *
     * @return bool
     */
    public function getIsCalibre()
    {
        return $this->isCalibre;
    }
}

