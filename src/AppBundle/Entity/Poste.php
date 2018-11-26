<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Poste
 *
 * @ORM\Table(name="poste")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PosteRepository")
 */
class Poste
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
     * @var Point
     *
     * @ORM\ManyToOne(targetEntity="Point")
     * @ORM\JoinColumn(name="point_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $idPoint;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="nombre", type="integer")
     */
    private $nombre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heureDebut", type="datetime")
     */
    private $heureDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="heureFin", type="datetime")
     */
    private $heureFin;


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
     * Set idPoint
     *
     * @param integer $idPoint
     *
     * @return Poste
     */
    public function setIdPoint($idPoint)
    {
        $this->idPoint = $idPoint;

        return $this;
    }

    /**
     * Get idPoint
     *
     * @return int
     */
    public function getIdPoint()
    {
        return $this->idPoint;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Poste
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set nombre
     *
     * @param integer $nombre
     *
     * @return Poste
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return int
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set heureDebut
     *
     * @param \Time $heureDebut
     *
     * @return Poste
     */
    public function setHeureDebut($heureDebut)
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    /**
     * Get heureDebut
     *
     * @return \Time
     */
    public function getHeureDebut()
    {
        return $this->heureDebut;
    }

    /**
     * Set heureFin
     *
     * @param \Time $heureFin
     *
     * @return Poste
     */
    public function setHeureFin($heureFin)
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    /**
     * Get heureFin
     *
     * @return \Time
     */
    public function getHeureFin()
    {
        return $this->heureFin;
    }
}

