<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Form\RepartitionType;
use AppBundle\Entity\Repartition;
use Nelmio\ApiDocBundle\Annotation as Doc;

class RepartitionController extends Controller
{
  /**
     * @Doc\ApiDoc(
     *     section="REPARTITION",
     *     description="Get all repartitions",
     *     statusCodes={
     *         200="Returned when repartitions are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no repartitions are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/repartitions", name="get_all_repartitions")
     */
    public function getRepartitions(Request $request)
    {
        $repartitions = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Repartition')
                ->findAll();
        
        if(empty($repartitions)){
            return new JsonResponse(["message" => "Aucune repartition présente dans la BDD !"], Response::HTTP_NOT_FOUND);
        }
        
        return $repartitions;
    }

    /**
     * @Doc\ApiDoc(
     *     section="REPARTITION",
     *     input="AppBundle\Form\RepartitionType",
     *     output="AppBundle\Form\Repartition",
     *     description="Create new repartition",
     *     statusCodes={
     *         202="Repartitions created successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/repartitions", name="post_all_repartitions")
     */
    public function postRepartitions(Request $request)
    {
        $idPoste = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Poste')
                    ->find($request->get('idPoste'));

        if(empty($idPoste)) {
            return new JsonResponse(['message' => 'Le bénévole ne peut pas être affecté au poste car le poste est inexistant'], Response::HTTP_NOT_FOUND);
        }

        $idBenevole = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Benevole')
                    ->find($request->get('idBenevole'));

        if(empty($idBenevole)) {
            return new JsonResponse(['message' => 'Le bénévole ne peut pas être affecté au poste car le bénévole est inexistant'], Response::HTTP_NOT_FOUND);
        }

        $valid =  $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Repartition')
                    ->isBenevoleInRaid($request->get('idPoste'), $request->get('idBenevole'));

        if(empty($valid)){
            return new JsonResponse(['message' => 'Le bénévole n\'est pas dans le même raid que le poste'], Response::HTTP_NOT_FOUND); 
        }

        $repartition = new Repartition();
        
        $form = $this->createForm(RepartitionType::class, $repartition);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($repartition);
            $em->flush();
            
            return $repartition;

        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="REPARTITION",
     *     description="Delete all repartitions",
     *     statusCodes={
     *         202="All repartitions have been removed",
     *         401="Unauthorized, you need to use auth-token",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/repartitions", name="delete_all_repartition")
     */
    public function deleteRepartitions(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $repartitions = $em->getRepository('AppBundle:Repartition')
                ->findAll();

        if($repartitions) {
            foreach ($repartitions as $repartition) {
                $em->remove($repartition);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="REPARTITION",
     *     description="Get one repartition",
     *     statusCodes={
     *         200="Returned when repartition is found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no repartitions are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/repartitions/{id_repartition}", name="get_repartition_one")
     */
    public function getOneRepartition(Request $request)
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $repartition = $em->getRepository('AppBundle:Repartition')
                    ->find($request->get('id_repartition'));
        /* @var $point Point */

        if(empty($repartition)){
            return new JsonResponse(["message" => "Répartition non trouvée !"], Response::HTTP_NOT_FOUND);
        }
        
        return $repartition;
    }


    /**
     * @Doc\ApiDoc(
     *     section="REPARTITION",
     *     input="AppBundle\Form\RepartitionType",
     *     output="AppBundle\Form\Repartition",
     *     description="Update one repartition",
     *     statusCodes={
     *         200="Returned when point have been modified",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no repartition is present in the database"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/repartitions/{id_repartition}", name="post_repartition_one")
     */
    public function updateOneRepartition(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $repartition = $this->getDoctrine()->getRepository('AppBundle:Repartition')
                ->find($request->get('id_repartition'));

        if(empty($repartition)){
            return new JsonResponse(["message" => "La répartition à modifier n'a pas été trouvé !"], Response::HTTP_NOT_FOUND);
        }

        $idPoste = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Poste')
            ->find($request->request->get('idPoste'));
            
        if(empty($idPoste)) {
            return new JsonResponse(['message' => 'Le bénévole ne peut pas être affecté au poste car le poste est inexistant'], Response::HTTP_NOT_FOUND);
        }

        $idBenevole = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Benevole')
                ->find($request->request->get('idBenevole'));

        if(empty($idBenevole)) {
            return new JsonResponse(['message' => 'Le bénévole ne peut pas être affecté au poste car le bénévole est inexistant'], Response::HTTP_NOT_FOUND);
        }

        $valid =  $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Repartition')
                ->isBenevoleInRaid($request->get('idPoste'), $request->get('idBenevole'));

        if(empty($valid)){
            return new JsonResponse(['message' => 'Le bénévole n\'est pas dans le même raid que le poste'], Response::HTTP_NOT_FOUND); 
        }

        $form = $this->createForm(RepartitionType::class, $repartition);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->flush();
            return $repartition;
        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="REPARTITION",
     *     description="Delete one repartition",
     *     statusCodes={
     *         202="Returned when repartition is found",
     *         401="Unauthorized, you need to use auth-token"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/repartitions/{id_repartition}", name="delete_repartition_one")
     */
    public function deleteOneRepartition(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $repartition = $em->getRepository('AppBundle:Repartition')
                ->find($request->get('id_repartition'));

        if($repartition){
            $em->remove($repartition);
            $em->flush();
        }
    }

        /**
     * @Doc\ApiDoc(
     *     section="REPARTITION",
     *     description="Get repartitions of one raid",
     *     statusCodes={
     *         200="Returned when repartitions are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no repartition is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/repartitions/raids/{id_raid}", name="get_all_repartitions_raid")
     */
    public function getRepartitionsByIdRaid(Request $request)
    {
        $repartitions = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Repartition')
                ->findRepartitionsByIdRaid($request->get('id_raid'));
        /* @var $repartition Repartition */

        if (empty($repartitions)) {
            return new JsonResponse(['message' => "Le raid ne contient pas encore de répartitions !"], Response::HTTP_NOT_FOUND);
        }

        return $repartitions;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="REPARTITION",
     *     input="AppBundle\Form\RepartitionType",
     *     output="AppBundle\Form\Repartition",
     *     description="Delete all repartitions of a specific RAID",
     *     statusCodes={
     *         202="Remove all repartitions successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/repartitions/raids/{id_raid}", name="delete_all_repartitions_raid")
     */
    public function deleteRepartitionsByIdRaid(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $repartitions = $em->getRepository('AppBundle:Repartition')
                        ->findRepartitionsByIdRaid($request->get('id_raid'));

        if ($repartitions) {
            foreach ($repartitions as $repartition) {
                $em->remove($repartition);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="REPARTITION",
     *     description="Get repartitions of one parcours",
     *     statusCodes={
     *         200="Returned when repartitions are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no repartition is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/repartitions/parcours/{id_parcours}", name="get_all_repartitions_parcours")
     */
    public function getRepartitionsByIdParcours(Request $request)
    {
        $repartitions = $this->get('doctrine.orm.entity_manager')
                        ->getRepository('AppBundle:Repartition')
                        ->findRepartitionsByIdParcours($request->get('id_parcours'));
        /* @var $benevole Benevole */

        if (empty($repartitions)) {
            return new JsonResponse(['message' => "Aucune répartition associée au parcours"], Response::HTTP_NOT_FOUND);
        }

        return $repartitions;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="REPARTITION",
     *     input="AppBundle\Form\RepartitionType",
     *     output="AppBundle\Form\Repartition",
     *     description="Delete all repartitions of a specific PARCOURS",
     *     statusCodes={
     *         202="Remove all repartitions successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/repartitions/parcours/{id_parcours}", name="delete_all_repartitions_parcours")
     */
    public function deleteRepartitionsByIdParcours(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $repartitions = $em->getRepository('AppBundle:Repartition')
                        ->findRepartitionsByIdParcours($request->get('id_parcours'));

        if ($repartitions) {
            foreach ($repartitions as $repartition) {
                $em->remove($repartition);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="REPARTITION",
     *     description="Get repartitions of one user",
     *     statusCodes={
     *         200="Returned when repartitions are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no repartition is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/repartitions/users/{id_user}", name="get_all_repartitions_user")
     */
    public function getRepartitionsByIdUsers(Request $request)
    {
        $repartitions = $this->get('doctrine.orm.entity_manager')
                        ->getRepository('AppBundle:Repartition')
                        ->findRepartitionsByIdUser($request->get('id_user'));
        /* @var $benevole Benevole */

        if (empty($repartitions)) {
            return new JsonResponse(['message' => "L'utilisateur n'a pas encore de répartitions !"], Response::HTTP_NOT_FOUND);
        }

        return $repartitions;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="REPARTITION",
     *     description="Delete all repartitions of a specific USER",
     *     statusCodes={
     *         202="Remove all repartitions successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/repartitions/users/{id_user}", name="delete_all_repartitions_users")
     */
    public function deleteRepartitionsByIdUsers(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $repartitions = $em->getRepository('AppBundle:Repartition')
                        ->findRepartitionsByIdUser($request->get('id_user'));

        if ($repartitions) {
            foreach ($repartitions as $repartition) {
                $em->remove($repartition);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="REPARTITION",
     *     description="Get repartition if the user has already been affected",
     *     statusCodes={
     *         200="Returned when repartitions are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no repartition is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/repartitions/users/{id_user}/postes/{id_poste}", name="get_repartition_if_user_affected")
     */
    public function getRepartitionIfUserHasAlreadyBeenAffected(Request $request)
    {
        $user = $this->get('doctrine.orm.entity_manager')
                        ->getRepository('AppBundle:Poste')
                        ->find($request->get('id_user'));

        if (empty($user)) {
            return new JsonResponse(['message' => "Cet utilisateur n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $poste = $this->get('doctrine.orm.entity_manager')
                        ->getRepository('AppBundle:Poste')
                        ->find($request->get('id_poste'));

        if (empty($poste)) {
            return new JsonResponse(['message' => "Ce poste n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $repartition = $this->get('doctrine.orm.entity_manager')
                        ->getRepository('AppBundle:Repartition')
                        ->findIfUserIsAffected(
                            $request->get('id_user'),
                            $request->get('id_poste')
                        );

        if (empty($repartition)) {
            return new JsonResponse(['message' => "L'utilisateur n'est pas affecté sur ce poste !"], Response::HTTP_NOT_FOUND);
        }

        return $repartition;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="REPARTITION",
     *     description="Delete repartition if the user has already been affected",
     *     statusCodes={
     *         202="Remove all repartitions successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/repartitions/users/{id_user}/postes/{id_poste}", name="delete_repartition_if_user_affected")
     */
    public function deleteRepartitionIfUserHasAlreadyBeenAffected(Request $request)
    {   
        $user = $this->get('doctrine.orm.entity_manager')
        ->getRepository('AppBundle:Poste')
        ->find($request->get('id_user'));

        if (empty($user)) {
            return new JsonResponse(['message' => "Cet utilisateur n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $poste = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Poste')
                ->find($request->get('id_poste'));

        if (empty($poste)) {
            return new JsonResponse(['message' => "Ce poste n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $repartition = $this->get('doctrine.orm.entity_manager')
                        ->getRepository('AppBundle:Repartition')
                        ->findIfUserIsAffected($request->get('id_user'),$request->get('id_poste'));

        if($repartition){
            $em->remove($repartition);
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="REPARTITION",
     *     description="Get repartition by id raid and user",
     *     statusCodes={
     *         200="Returned when repartitions are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no repartition is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/repartitions/raids/{id_raid}/users/{id_user}", name="get_repartition_idraid_iduser")
     */
    public function getRepartitionByIdUserByIdRaid(Request $request)
    {
        $user = $this->get('doctrine.orm.entity_manager')
                        ->getRepository('AppBundle:User')
                        ->find($request->get('id_user'));

        if (empty($user)) {
            return new JsonResponse(['message' => "Cet utilisateur n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $poste = $this->get('doctrine.orm.entity_manager')
                        ->getRepository('AppBundle:Poste')
                        ->find($request->get('id_poste'));

        if (empty($poste)) {
            return new JsonResponse(['message' => "Ce poste n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $repartition = $this->get('doctrine.orm.entity_manager')
                        ->getRepository('AppBundle:Repartition')
                        ->findByIdRaidAndIdUser(
                            $request->get('id_raid'),
                            $request->get('id_user')
                        );

        if (empty($repartition)) {
            return new JsonResponse(['message' => "L'utilisateur n'est pas affecté sur ce poste !"], Response::HTTP_NOT_FOUND);
        }

        return $repartition;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="REPARTITION",
     *     description="Delete repartition by idraid and iduser",
     *     statusCodes={
     *         202="Remove all repartitions successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/repartitions/raids/{id_raid}/users/{id_user}", name="delete_repartition_idraid_iduser")
     */
    public function deleteRepartitionByIdUserByIdRaid(Request $request)
    {   
        $user = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:User')
                    ->find($request->get('id_user'));

        if (empty($user)) {
            return new JsonResponse(['message' => "Cet utilisateur n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $poste = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Poste')
                ->find($request->get('id_poste'));

        if (empty($poste)) {
            return new JsonResponse(['message' => "Ce poste n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $repartition = $this->get('doctrine.orm.entity_manager')
                        ->getRepository('AppBundle:Repartition')
                        ->findByIdRaidAndIdUser(
                            $request->get('id_raid'),
                            $request->get('id_user')
                        );

        if($repartition){
            $em->remove($repartition);
            $em->flush();
        }
    }
}
