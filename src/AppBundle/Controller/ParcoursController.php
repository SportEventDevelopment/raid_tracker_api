<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Form\ParcoursType;
use AppBundle\Entity\Parcours;
use Nelmio\ApiDocBundle\Annotation as Doc;

class ParcoursController extends Controller
{
    /**
     * @Doc\ApiDoc(
     *     section="PARCOURS",
     *     description="Get all parcours",
     *     statusCodes={
     *         200="Returned when parcours are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no parcours are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/parcours", name="get_all_parcours")
     */
    public function getParcours(Request $request)
    {
        $parcours = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Parcours')
                ->findAll();
        
        if(empty($parcours)){
            return new JsonResponse(["message" => "Aucun parcours présent dans la BDD !"], Response::HTTP_NOT_FOUND);
        }
        
        return $parcours;
    }

    /**
     * @Doc\ApiDoc(
     *     section="PARCOURS",
     *     input="AppBundle\Form\ParcoursType",
     *     output="AppBundle\Form\Parcours",
     *     description="Create new parcours",
     *     statusCodes={
     *         202="Parcours created successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/parcours", name="post_all_parcours")
     */
    public function postParcours(Request $request)
    {
        $raid = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Raid')
                ->find($request->get('idRaid'));

        if(empty($raid)) {
            return new JsonResponse(['message' => 'Le RAID est inexistant, parcours non créé'], Response::HTTP_NOT_FOUND);
        }

        $parcours = new Parcours();
        
        $form = $this->createForm(ParcoursType::class, $parcours);
        
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($parcours);
            $em->flush();
            
            return $parcours;

        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="PARCOURS",
     *     description="Delete all parcourss",
     *     statusCodes={
     *         202="All parcourss have been removed",
     *         401="Unauthorized, you need to use auth-token",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/parcours", name="delete_all_parcours")
     */
    public function deleteParcours(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $parcours = $em->getRepository('AppBundle:Parcours')->findAll();

        if($parcours) {
            foreach ($parcours as $parcours) {
                $em->remove($parcours);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="PARCOURS",
     *     description="Get one parcours",
     *     statusCodes={
     *         200="Returned when parcourss are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no parcourss are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/parcours/{id_parcours}", name="get_parcours_one")
     */
    public function getOneParcours(Request $request)
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $parcours = $em->getRepository('AppBundle:Parcours')
                    ->find($request->get('id_parcours'));
        /* @var $parcours Parcours */

        if(empty($parcours)){
            return new JsonResponse(["message" => "Parcours non trouvé !"], Response::HTTP_NOT_FOUND);
        }
        
        return $parcours;
    }


    /**
     * @Doc\ApiDoc(
     *     section="PARCOURS",
     *     input="AppBundle\Form\UserType",
     *     output="AppBundle\Form\User",
     *     description="Update one parcours",
     *     statusCodes={
     *         200="Returned when parcourss are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no parcourss are presents in the database"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/parcours/{id_parcours}", name="post_parcours_one")
     */
    public function updateOneParcours(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $parcours = $this->getDoctrine()->getRepository('AppBundle:Parcours')
                ->find($request->get('id_parcours'));

        if(empty($parcours)){
            return new JsonResponse(["message" => "Le parcours à modifier n'a pas été trouvé !"], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ParcoursType::class, $parcours);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->flush();
            return $parcours;
        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="PARCOURS",
     *     description="Delete one parcours",
     *     statusCodes={
     *         202="Returned when parcours is found",
     *         401="Unauthorized, you need to use auth-token"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/parcours/{id_parcours}", name="delete_parcours_one")
     */
    public function deleteOneParcours(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $parcours = $em->getRepository('AppBundle:Parcours')
                ->find($request->get('id_parcours'));

        if($parcours){
            $em->remove($parcours);
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="PARCOURS",
     *     description="Get all parcours of a specific raid",
     *     statusCodes={
     *         200="Returned when parcours are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no parcours are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/parcours/raids/{id_raid}", name="get_parcours_one_raid")
     */
    public function getParcoursByIdRaid(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $parcours = $em->getRepository('AppBundle:Parcours')
                    ->findBy(array('idRaid' => $request->get('id_raid')));
        /* @var $parcours Parcours */

        if(empty($parcours)){
            return new JsonResponse(["message" => "Ce raid n'existe pas ou il n'y a pas de parcours encore associé !"], Response::HTTP_NOT_FOUND);
        }
        
        return $parcours;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="PARCOURS",
     *     description="Delete all parcours of a specific RAID",
     *     statusCodes={
     *         202="Remove all parcours successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/parcours/raids/{id_raid}", name="delete_parcours_one_raid")
     */
    public function deleteParcoursByIdRaid(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $parcours = $em->getRepository('AppBundle:Parcours')
                    ->findBy(array('idRaid' => $request->get('id_raid')));

        if($parcours){
            foreach($parcours as $parcour){
                $em->remove($parcour);
            }
            $em->flush();
        }
    }
}
