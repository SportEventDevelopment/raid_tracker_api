<?php

namespace AppBundle\Repository;

/**
 * TraceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TraceRepository extends \Doctrine\ORM\EntityRepository
{
    function findParcoursByIdTrace($id_trace){
        $query = $this->getEntityManager()->createQuery(
            'SELECT parcours FROM AppBundle:Trace t
            INNER JOIN AppBundle:Parcours parcours
            WHERE parcours.id = t.idParcours  AND t.id = :idTrace'
        )->setParameter('idTrace', $id_trace);
        
        return $query->getOneOrNullResult();
    }
}