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
    function findTracesByIdParcours($id_parcours){

        $query = $this->getEntityManager()->createQuery(
            'SELECT trace FROM AppBundle:Trace trace
            INNER JOIN AppBundle:Parcours parcours
            WHERE parcours.id = trace.idParcours AND parcours.id = :idParcours'
        )->setParameter('idParcours', $id_parcours);
        
        return $query->getResult();
    }
}
