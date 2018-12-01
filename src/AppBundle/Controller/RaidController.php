<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Form\RaidType;
use AppBundle\Entity\Raid;
use Nelmio\ApiDocBundle\Annotation as Doc;

class RaidController extends Controller
{
    /**
     * @Doc\ApiDoc(
     *     section="RAID",
     *     description="Get all raids",
     *     statusCodes={
     *         200="Returned when raids are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no raids are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/raids", name="get_all_raids")
     */
    public function getAllRaids(Request $request)
    {
        $raids = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:Raid')
                ->findAll();
        /* @var $raids Raids[] */
        if(empty($raids)){
            return new JsonResponse(["message" => "Aucun RAID présent dans la BDD !"], Response::HTTP_NOT_FOUND);
        }

        return $raids;
    }

    /**
     * @Doc\ApiDoc(
     *     section="RAID",
     *     input="AppBundle\Form\RaidType",
     *     output="AppBundle\Form\Raid",
     *     description="Create new raid",
     *     statusCodes={
     *         201="Raid created successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/raids", name="post_all_raids")
     */
    public function createNewRaid(Request $request)
    {
        $raid = new Raid();
        
        $form = $this->createForm(RaidType::class, $raid);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($raid);
            $em->flush();

            return $raid;
        } else {
            return $form;
        }
    }


    /**
     * @Doc\ApiDoc(
     *     section="RAID",
     *     description="Delete all raids",
     *     statusCodes={
     *         202="All raids have been removed",
     *         401="Unauthorized, you need to use auth-token",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/raids", name="delete_all_raids")
     */
    public function deleteRaids(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $raids = $em->getRepository('AppBundle:Raid')->findAll();

        if($raids) {
            foreach ($raids as $raid) {
                $em->remove($raid);
            }
            $em->flush();
        }
    }


    /**
     * @Doc\ApiDoc(
     *     section="RAID",
     *     description="Get one raid",
     *     statusCodes={
     *         200="Returned when raid is found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no raids are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/raids/{id_raid}", name="get_raids_one")
     */
    public function getOneRaid(Request $request)
    {
        $raid = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:Raid')
                ->findOneBy(array("id" => $request->get('id_raid')));
        /* @var $raid Raid */

        if(empty($raid)){
            return new JsonResponse(["message" => "Le raid recherché n'est pas présent dans la BDD !"], Response::HTTP_NOT_FOUND);
        }

        return $raid;
    }

    /**
     * @Doc\ApiDoc(
     *     section="RAID",
     *     input="AppBundle\Form\RaidType",
     *     output="AppBundle\Form\Raid",
     *     description="Update one raid",
     *     statusCodes={
     *         200="Returned when raid have been modified",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no raids are presents in the database"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/raids/{id_raid}", name="post_raids_one")
     */
    public function updateOneRaid(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $raid = $this->getDoctrine()->getRepository('AppBundle:Raid')
                ->find($request->get('id_raid'));

        if(empty($raid)){
            return new JsonResponse(["message" => "Le raid désiré n'est pas enregistré dans la BDD !"], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(RaidType::class, $raid);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->merge($raid);
            $em->flush();
            return $raid;
        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="RAID",
     *     description="Delete one raid",
     *     statusCodes={
     *         202="Returned when raid is found",
     *         401="Unauthorized, you need to use auth-token"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/raids/{id_raid}", name="delete_raids_one")
     */
    public function deleteRaid(Request $request)
    {

        $em = $this->get('doctrine.orm.entity_manager');
        $raid = $this->getDoctrine()->getRepository('AppBundle:Raid')->find($request->get('id_raid'));

        if($raid){
            $em->remove($raid);
            $em->flush(); 
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="RAID",
     *     description="Get all raids visibles",
     *     statusCodes={
     *         200="Returned when raids are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no raids are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/raids/visible/all", name="get_all_raids_visibles")
     */
    public function getAllRaidsVisibles(Request $request)
    {
        $raids = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:Raid')
                ->findBy(array(
                    'visibility' => true
                ));

        /* @var $raids Raids[] */
        if(empty($raids)){
            return new JsonResponse(["message" => "Aucun RAID visible présent dans la BDD !"], Response::HTTP_NOT_FOUND);
        }

        return $raids;
    }
    

    /**
     * @Doc\ApiDoc(
     *     section="RAID",
     *     description="Delete all raids visibles",
     *     statusCodes={
     *         202="All raids have been removed",
     *         401="Unauthorized, you need to use auth-token",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/raids/visible/all", name="delete_all_raids_visibles")
     */
    public function deleteRaidsVisibles(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $raids = $em->getRepository('AppBundle:Raid')
                    ->findBy(array(
                        'visibility' => true
                    ));

        if($raids) {
            foreach ($raids as $raid) {
                $em->remove($raid);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="RAID",
     *     description="Get all raids visibles for a user without raids benevoles",
     *     statusCodes={
     *         200="Returned when raids are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no raids are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/raids/visible/users/{id_user}", name="get_raids_visibles_without_benevoles_raid")
     */
    public function getRaidsVisiblesByIdUser(Request $request)
    {
        $raids = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:Raid')
                ->findRaidsVisibleByIdUser($request->get('id_user'));

        if(empty($raids)){
            return new JsonResponse(["message" => "Aucun RAID visible pour cet utilisateur présent dans la BDD !"], Response::HTTP_NOT_FOUND);
        }

        return $raids;
    }
    

    /**
     * @Doc\ApiDoc(
     *     section="RAID",
     *     description="Delete all raids visibles for a user without raids benevoles",
     *     statusCodes={
     *         202="All raids have been removed",
     *         401="Unauthorized, you need to use auth-token",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/raids/visible/users/{id_user}", name="delete_raids_visibles_without_benevoles_raid")
     */
    public function deleteRaidsVisiblesByIdUser(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $raids = $em->getRepository('AppBundle:Raid')
            ->findRaidsVisibleByIdUser($request->get('id_user'));

        if($raids) {
            foreach ($raids as $raid) {
                $em->remove($raid);
            }
            $em->flush();
        }
    }
    
    /**
     * @Doc\ApiDoc(
     *     section="RAID",
     *     description="Get all raids benevoles of one user",
     *     statusCodes={
     *         200="Returned when raids are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no raids are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/raids/benevoles/users/{id_user}", name="get_all_raids_benevoles")
     */
    public function getRaidsWhereBenevoleByIdUser(Request $request)
    {
        $raids = $this->getDoctrine()->getManager()
                ->getRepository('AppBundle:Raid')
                ->findRaidsBenevolesByIdUser($request->get('id_user'));
        /* @var $raids Raids[] */

        if(empty($raids)){
            return new JsonResponse(["message" => "Cet utilisateur n'est bénévole d'aucun RAID !"], Response::HTTP_NOT_FOUND);
        }

        return $raids;
    }


    /**
     *  @Doc\ApiDoc(
     *     section="RAID",
     *     description="Delete all raids benevoles of a specific user",
     *     statusCodes={
     *         202="Remove all raids successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/raids/benevoles/users/{id_user}", name="delete_all_raids_benevoles")
     */
    public function deleteRaidsWhereBenevoleByIdUser(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $raids = $em->getRepository('AppBundle:Raid')
                    ->findRaidsBenevolesByIdUser($request->get('id_user'));

        if($raids) {
            foreach ($raids as $raid) {
                $em->remove($raid);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="RAID",
     *     description="Get all raids organisateurs of one user",
     *     statusCodes={
     *         200="Returned when raids are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no raids are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/raids/organisateurs/users/{id_user}", name="get_all_raids_organisateurs")
     */
    public function getRaidsWhereOrganisateurByIdUser(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $raids = $em->getRepository('AppBundle:Raid')
                    ->findRaidsOrganisateursByIdUser($request->get('id_user'));
        /* @var $raids Raids[] */

        if(empty($raids)){
            return new JsonResponse(["message" => "Cet utilisateur n'est organisateur d'aucun RAID !"], Response::HTTP_NOT_FOUND);
        }

        return $raids;
    }


    /**
     *  @Doc\ApiDoc(
     *     section="RAID",
     *     description="Delete all raids organisateurs of a specific user",
     *     statusCodes={
     *         202="Remove all raids successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/raids/organisateurs/users/{id_user}", name="delete_all_raids_organisateurs")
     */
    public function deleteRaidsWhereOrganisateurByIdUser(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $raids = $em->getRepository('AppBundle:Raid')
                    ->findRaidsOrganisateursByIdUser($request->get('id_user'));

        if($raids){
            foreach ($raids as $raid) {
                $em->remove($raid);
            }
            $em->flush();
        }
    }


    /**
     * @Doc\ApiDoc(
     *     section="RAID",
     *     description="Get all raids organisateurs of a parcours",
     *     statusCodes={
     *         200="Returned when raids are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no raids are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/raids/parcours/{id_parcours}", name="get_all_raids_parcours")
     */
    public function getRaidsByIdParcours(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $raids = $em->getRepository('AppBundle:Raid')
                    ->findRaidsByIdParcours($request->get('id_parcours'));
        /* @var $raids Raids[] */

        if(empty($raids)){
            return new JsonResponse(["message" => "Ce parcours n'a aucun RAID associé ou existant !"], Response::HTTP_NOT_FOUND);
        }

        return $raids;
    }


    /**
     *  @Doc\ApiDoc(
     *     section="RAID",
     *     description="Delete all raids of a specific parcours",
     *     statusCodes={
     *         202="Remove all raids successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/raids/parcours/{id_parcours}", name="delete_all_raids_parcours")
     */
    public function deleteRaidsByIdParcours(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $raids = $em->getRepository('AppBundle:Raid')
                    ->findRaidsByIdParcours($request->get('id_parcours'));

        if($raids){
            foreach ($raids as $raid) {
                $em->remove($raid);
            }
            $em->flush();
        }
    }
}
