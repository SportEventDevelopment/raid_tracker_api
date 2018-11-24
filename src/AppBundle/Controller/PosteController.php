<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Form\PosteType;
use AppBundle\Entity\Poste;
use Nelmio\ApiDocBundle\Annotation as Doc;

class PosteController extends Controller
{
    /**
     * @Doc\ApiDoc(
     *     section="POSTE",
     *     description="Get all postes",
     *     statusCodes={
     *         200="Returned when postes are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no postes are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/postes", name="get_all_poste")
     */
    public function getPostes(Request $request)
    {
        $postes = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Poste')
                ->findAll();
        
        if(empty($postes)){
            return new JsonResponse(["message" => "Aucun poste présent dans la BDD !"], Response::HTTP_NOT_FOUND);
        }
        
        return $postes;
    }

    /**
     * @Doc\ApiDoc(
     *     section="POSTE",
     *     input="AppBundle\Form\PosteType",
     *     output="AppBundle\Form\Poste",
     *     description="Create new poste",
     *     statusCodes={
     *         202="Poste created successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/postes", name="post_all_poste")
     */
    public function postPostes(Request $request)
    {
        $point = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Point')
                    ->find($request->get('idPoint'));

        if(empty($point)) {
            return new JsonResponse(['message' => 'Point '. $request->get('idPoint').' inexistant '], Response::HTTP_NOT_FOUND);
        }

        $poste = new Poste();
        
        $form = $this->createForm(PosteType::class, $poste);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($poste);
            $em->flush();
            
            return $poste;

        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="POSTE",
     *     description="Delete all postes",
     *     statusCodes={
     *         202="All postes have been removed",
     *         401="Unauthorized, you need to use auth-token",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/postes", name="delete_all_poste")
     */
    public function deletePostes(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $postes = $em->getRepository('AppBundle:Poste')
                ->findAll();

        if($postes) {
            foreach ($postes as $poste) {
                $em->remove($poste);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="POSTE",
     *     description="Get one poste",
     *     statusCodes={
     *         200="Returned when poste is found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no postes are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/postes/{id_poste}", name="get_poste_one")
     */
    public function getOnePoste(Request $request)
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $poste = $em->getRepository('AppBundle:Poste')
                    ->find($request->get('id_poste'));
        /* @var $poste Poste */

        if(empty($poste)){
            return new JsonResponse(["message" => "Poste ". $request->get('id_poste') ." non trouvé !"], Response::HTTP_NOT_FOUND);
        }
        
        return $poste;
    }


    /**
     * @Doc\ApiDoc(
     *     section="POSTE",
     *     input="AppBundle\Form\PosteType",
     *     output="AppBundle\Form\Poste",
     *     description="Update one poste",
     *     statusCodes={
     *         200="Returned when poste have been modified",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no postes are presents in the database"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/postes/{id_poste}", name="post_poste_one")
     */
    public function updateOnePoste(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $poste = $this->getDoctrine()->getRepository('AppBundle:Poste')
                ->find($request->get('id_poste'));

        if(empty($poste)){
            return new JsonResponse(["message" => "Poste ". $request->get('id_poste') ." inexistant !"], Response::HTTP_NOT_FOUND);
        }

        $point = $this->getDoctrine()->getRepository('AppBundle:Point')
                ->find($request->request->get('idPoint'));

        if(empty($point)){
            return new JsonResponse(["message" => "Le point renseigné n'est pas dans la bdd"], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(PosteType::class, $poste);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->flush();
            return $poste;
        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="POSTE",
     *     description="Delete one poste",
     *     statusCodes={
     *         202="Returned when poste is found",
     *         401="Unauthorized, you need to use auth-token"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/postes/{id_poste}", name="delete_poste_one")
     */
    public function deleteOnePoste(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $poste = $em->getRepository('AppBundle:Poste')
                ->find($request->get('id_poste'));

        if($poste){
            $em->remove($poste);
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="POSTE",
     *     description="Get all postes by id benevole",
     *     statusCodes={
     *         200="Returned when postes are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no postes are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/postes/benevoles/{id_benevole}", name="get_all_postes_by_idbenevole")
     */
    public function getPostesByIdBenevole(Request $request)
    {
        $postes = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Poste')
                ->findPostesByIdBenevole($request->get('id_benevole'));
        
        if(empty($postes)){
            return new JsonResponse(["message" => "Aucun poste affecté pour ce bénévole ! (id=". $request->get('id_benevole').")"], Response::HTTP_NOT_FOUND);
        }
        
        return $postes;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="POSTE",
     *     description="Delete all postes of a specific Benevole",
     *     statusCodes={
     *         202="Remove all prefpostes successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/postes/benevoles/{id_benevole}", name="delete_all_postes_benevole")
     */
    public function deletePostesByIdBenevole(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $postes = $em->getRepository('AppBundle:Poste')
                    ->findPostesByIdBenevole($request->get('id_benevole'));

        if ($postes) {
            foreach ($postes as $poste) {
                $em->remove($poste);
            }
            $em->flush();
        }
    }
    
    /**
     * @Doc\ApiDoc(
     *     section="POSTE",
     *     description="Get all postes by id raid",
     *     statusCodes={
     *         200="Returned when postes are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no postes are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/postes/raids/{id_raid}", name="get_all_poste_by_idraid")
     */
    public function getPostesByIdRaid(Request $request)
    {
        $postes = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Poste')
                ->findPostesByIdRaid($request->get('id_raid'));
        
        if(empty($postes)){
            return new JsonResponse(["message" => "Aucun poste présent dans la BDD !"], Response::HTTP_NOT_FOUND);
        }
        
        return $postes;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="POSTE",
     *     description="Delete all postes of a specific RAID",
     *     statusCodes={
     *         202="Remove all postes successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/postes/raids/{id_raid}", name="delete_all_postes_raid")
     */
    public function deletePostesByIdRaid(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $postes = $em->getRepository('AppBundle:Poste')
                    ->findPostesByIdRaid($request->get('id_raid'));

        if ($postes) {
            foreach ($postes as $poste) {
                $em->remove($poste);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="POSTE",
     *     description="Get all postes available by id raid",
     *     statusCodes={
     *         200="Returned when postes are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no postes are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/postes/raids/{id_raid}/available", name="get_all_poste_available_by_idraid")
     */
    public function getPostesAvailableByIdRaid(Request $request)
    {
        $postes = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Poste')
                ->findAvailablePosteByIdRaid($request->get('id_raid'));
        
        if(empty($postes)){
            return new JsonResponse(["message" => "Aucun poste disponible dans ce raid (".$request->get('id_raid').")"], Response::HTTP_NOT_FOUND);
        }
        
        return $postes;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="POSTE",
     *     description="Delete all available postes of a specific RAID",
     *     statusCodes={
     *         202="Remove all postes successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/postes/raids/{id_raid}/available", name="delete_all_available_postes_raid")
     */
    public function deletePostesAvailableByIdRaid(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $postes = $em->getRepository('AppBundle:Poste')
                    ->findAvailablePosteByIdRaid($request->get('id_raid'));

        if ($postes) {
            foreach ($postes as $poste) {
                $em->remove($poste);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="POSTE",
     *     description="Get all postes by id parcours",
     *     statusCodes={
     *         200="Returned when postes are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no postes are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/postes/parcours/{id_parcours}", name="get_all_poste_by_idparcours")
     */
    public function getPostesByIdParcours(Request $request)
    {
        $postes = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Poste')
                ->findPostesByIdParcours($request->get('id_parcours'));
        
        if(empty($postes)){
            return new JsonResponse(["message" => "Aucun poste présent dans la BDD !"], Response::HTTP_NOT_FOUND);
        }
        
        return $postes;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="POSTE",
     *     description="Delete all postes of a specific PARCOURS",
     *     statusCodes={
     *         202="Remove all prefpostes successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/postes/parcours/{id_parcours}", name="delete_all_postes_parcours")
     */
    public function deletePostesByIdParcours(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $postes = $em->getRepository('AppBundle:Poste')
                    ->findPostesByIdParcours($request->get('id_parcours'));

        if ($postes) {
            foreach ($postes as $poste) {
                $em->remove($poste);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="POSTE",
     *     description="Get all postes by id parcours",
     *     statusCodes={
     *         200="Returned when postes are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no postes are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/postes/parcours/{id_parcours}/available", name="get_all_poste_available_by_idparcours")
     */
    public function getPostesAvailableByIdParcours(Request $request)
    {
        $postes = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Poste')
                ->findAvailablePosteByIdParcours($request->get('id_parcours'));
        
        if(empty($postes)){
            return new JsonResponse(["message" => "Aucun poste disponible dans le parcours (". $request->get('id_parcours') .")"], Response::HTTP_NOT_FOUND);
        }
        
        return $postes;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="POSTE",
     *     description="Delete all available postes of a specific RAID",
     *     statusCodes={
     *         202="Remove all postes successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/postes/parcours/{id_parcours}/available", name="delete_all_available_postes_by_idparcours")
     */
    public function deletePostesAvailableByIdParcours(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $postes = $em->getRepository('AppBundle:Poste')
                    ->findAvailablePosteByIdParcours($request->get('id_parcours'));

        if ($postes) {
            foreach ($postes as $poste) {
                $em->remove($poste);
            }
            $em->flush();
        }
    }
}
