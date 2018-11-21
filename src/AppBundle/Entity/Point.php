<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Point
 *
 * @ORM\Table(name="point")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PointRepository")
 */
class Point
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
     * @var Trace
     *
     * @ORM\ManyToOne(targetEntity="Trace")
     * @ORM\JoinColumn(name="trace_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $idTrace;

    /**
     * @var float
     *
     * @ORM\Column(name="lon", type="float")
     */
    private $lon;

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="float")
     */
    private $lat;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="ordre", type="integer")
     */
    private $ordre;


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
     * Set idTrace
     *
     * @param integer $idTrace
     *
     * @return Point
     */
    public function setIdTrace($idTrace)
    {
        $this->idTrace = $idTrace;

        return $this;
    }

    /**
     * Get idTrace
     *
     * @return int
     */
    public function getIdTrace()
    {
        return $this->idTrace;
    }

    /**
     * Set lon
     *
     * @param integer $lon
     *
     * @return Point
     */
    public function setLon($lon)
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * Get lon
     *
     * @return int
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * Set lat
     *
     * @param integer $lat
     *
     * @return Point
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return int
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Point
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     *
     * @return Point
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return int
     */
    public function getOrdre()
    {
        return $this->ordre;
    }
}

