<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Repartition
 *
 * @ORM\Table(name="repartition")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RepartitionRepository")
 */
class Repartition
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
     * @var Poste
     *
     * @ORM\ManyToOne(targetEntity="Poste")
     * @ORM\JoinColumn(name="poste_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $idPoste;

    /**
     * @var Benevole
     *
     * @ORM\ManyToOne(targetEntity="Benevole")
     * @ORM\JoinColumn(name="benevole_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $idBenevole;

    /**
     * 
     * @ORM\Column(name="estConfirme", type="boolean")
     */
    private $estConfirme;


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
     * Set idPoste
     *
     * @param integer $idPoste
     *
     * @return Repartition
     */
    public function setIdPoste($idPoste)
    {
        $this->idPoste = $idPoste;

        return $this;
    }

    /**
     * Get idPoste
     *
     * @return int
     */
    public function getIdPoste()
    {
        return $this->idPoste;
    }

    /**
     * Set idBenevole
     *
     * @param integer $idBenevole
     *
     * @return Repartition
     */
    public function setIdBenevole($idBenevole)
    {
        $this->idBenevole = $idBenevole;

        return $this;
    }

    /**
     * Get idBenevole
     *
     * @return int
     */
    public function getIdBenevole()
    {
        return $this->idBenevole;
    }

    /**
     * Set estConfirme
     *
     * @param boolean $estConfirme
     *
     * @return Repartition
     */
    public function setEstConfirme($estConfirme)
    {
        $this->estConfirme = $estConfirme;

        return $this;
    }

    /**
     * Get estConfirme
     *
     * @return boolean
     */
    public function getEstConfirme()
    {
        return $this->estConfirme;
    }
}

