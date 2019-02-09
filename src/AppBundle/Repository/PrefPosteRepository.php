<?php

namespace AppBundle\Repository;

/**
 * PrefPosteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PrefPosteRepository extends \Doctrine\ORM\EntityRepository
{
    function findPrefPostesByIdRaid($id_raid){
        $query = $this->getEntityManager()->createQuery(
            'SELECT p FROM AppBundle:PrefPoste p
            INNER JOIN AppBundle:Benevole b WITH b.id = p.idBenevole
            WHERE b.idRaid = :idRaid
            ORDER BY b.id, p.priority ASC'
        )->setParameter('idRaid', $id_raid);
        return $query->getResult();
    }

    function findByIdRaidIdUser($id_raid, $id_user){
        $query = $this->getEntityManager()->createQuery(
            'SELECT p FROM AppBundle:PrefPoste p
            INNER JOIN AppBundle:Benevole b WITH b.id = p.idBenevole
            WHERE b.idRaid = :idRaid AND b.idUser = :idUser
            ORDER BY b.id, p.priority ASC'
        )->setParameter('idRaid', $id_raid)
        ->setParameter('idUser', $id_user);
        return $query->getResult();
    }

    function findByIdPosteIdUser($id_poste, $id_user){
        $query = $this->getEntityManager()->createQuery(
            'SELECT p FROM AppBundle:PrefPoste p
            INNER JOIN AppBundle:Benevole b WITH b.id = p.idBenevole
            WHERE p.idPoste = :idPoste AND b.idUser = :idUser
            ORDER BY b.id, p.priority ASC'
        )->setParameter('idPoste', $id_poste)
        ->setParameter('idUser', $id_user);
        return $query->getResult();
    }

    function findCountByIdRaidIdUser($id_raid, $id_user){
        $query = $this->getEntityManager()->createQuery(
            'SELECT COUNT(p.id) FROM AppBundle:PrefPoste p
            INNER JOIN AppBundle:Benevole b WITH b.id = p.idBenevole
            WHERE b.idRaid = :idRaid AND b.idUser = :idUser
            ORDER BY b.id, p.priority ASC'
        )->setParameter('idRaid', $id_raid)
        ->setParameter('idUser', $id_user);
        return $query->getResult();
    }
}
