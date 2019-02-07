<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrefPoste
 *
 * @ORM\Table(name="pref_poste")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrefPosteRepository")
 */
class PrefPoste
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
     * @var Benevole
     *
     * @ORM\ManyToOne(targetEntity="Benevole")
     * @ORM\JoinColumn(name="benevole_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $idBenevole;

    /**
     * @var Poste
     *
     * @ORM\ManyToOne(targetEntity="Poste")
     * @ORM\JoinColumn(name="poste_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $idPoste;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

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
     * Set idBenevole
     *
     * @param integer $idBenevole
     *
     * @return PrefPoste
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
     * Set idPoste
     *
     * @param integer $idPoste
     *
     * @return PrefPoste
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
     * Set priority
     *
     * @param integer $priority
     *
     * @return PrefPoste
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

}

