<?php

namespace AppBundle\Repository;

/**
 * CheckinRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CheckinRepository extends \Doctrine\ORM\EntityRepository
{
    function findCheckinByIdRaid($id_raid){
        $query = $this->getEntityManager()->createQuery(
            'SELECT c FROM AppBundle:Checkin c
            INNER JOIN AppBundle:Repartition r WITH c.idRepartition = r.id
            INNER JOIN AppBundle:Benevole b WITH r.idBenevole = b.id
            WHERE b.idRaid = :idRaid'
        )->setParameter('idRaid', $id_raid);
        
        return $query->getResult();
    }
    
    function findCheckinByIdUser($id_user){
        $query = $this->getEntityManager()->createQuery(
            'SELECT c FROM AppBundle:Checkin c
            INNER JOIN AppBundle:Repartition r WITH r.id = c.idRepartition 
            INNER JOIN AppBundle:Benevole b WITH b.id = r.idBenevole
            WHERE b.idUser = :idUser'
        )->setParameter('idUser', $id_user);
        return $query->getResult();
    }
}
