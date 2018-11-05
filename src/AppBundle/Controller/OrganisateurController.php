<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Form\OrganisateurType;
use AppBundle\Entity\Organisateur;
use Nelmio\ApiDocBundle\Annotation as Doc;

class OrganisateurController extends Controller
{

    /**
     * @Doc\ApiDoc(
     *     section="ORGANISATEUR",
     *     description="Get all organisateurs",
     *     statusCodes={
     *         200="Returned when organisateurs are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no organisateurs are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/organisateurs", name="get_all_organisateurs")
     */
    public function getOrganisateurs(Request $request)
    {
        $organisateurs = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Organisateur')
                ->findAll();   
        /* @var $organisateurs Organisateurs[] */
        
        if (empty($organisateurs)) {
            return new JsonResponse(['message' => "Aucun organisateurs présents dans la BDD !"], Response::HTTP_NOT_FOUND);
        }

        return $organisateurs;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="ORGANISATEUR",
     *     input="AppBundle\Form\OrganisateurType",
     *     output="AppBundle\Form\Organisateur",
     *     description="Delete all organisateurs",
     *     statusCodes={
     *         202="Remove all organisateurs successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/organisateurs", name="delete_all_organisateurs")
     */
    public function deleteOrganisateurs(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $organisateurs = $em->getRepository('AppBundle:Organisateur')->findAll();

        if($organisateurs) {
            foreach ($organisateurs as $organisateur) {
                $em->remove($organisateur);
            }
    
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="ORGANISATEUR",
     *     description="Get one organisateur",
     *     statusCodes={
     *         200="Returned when organisateurs are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no organisateurs are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/organisateurs/{id_organisateur}", name="get_organisateurs_one")
     */
    public function getOneOrganisateur(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $organisateur = $em->getRepository('AppBundle:Organisateur')
                        ->find($request->get('id_organisateur'));
        /* @var $organisateur organisateur */

        if(empty($organisateur)){
            return new JsonResponse(["message" => "Organisateur non trouvé !"], Response::HTTP_NOT_FOUND);
        }

       return $organisateur;
    }


    /**
     *  @Doc\ApiDoc(
     *     section="ORGANISATEUR",
     *     description="Delete one organisateur",
     *     statusCodes={
     *         202="Returned when organisateur is found",
     *         401="Unauthorized, you need to use auth-token"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/organisateurs/{id_organisateur}", name="delete_organisateurs_one")
     */
    public function deleteOneOrganisateur(Request $request)
    {
        
        $em = $this->get('doctrine.orm.entity_manager');
        $organisateur = $em->getRepository('AppBundle:Organisateur')
                        ->find($request->get('id_organisateur'));
       
        if ($organisateur){
            $em->remove($organisateur);
            $em->flush();
        }
    }

   /**
     * @Doc\ApiDoc(
     *     section="ORGANISATEUR",
     *     description="Get organisateurs of one raid",
     *     statusCodes={
     *         200="Returned when organisateurs are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no organisateurs are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/organisateurs/raids/{id_raid}", name="get_all_organisateurs_one_raid")
     */
    public function getOrganisateursByIdRaid(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $organisateurs = $em->getRepository('AppBundle:Organisateur')
                        ->findBy(array("idRaid" => $request->get('id_raid')));   
        /* @var $organisateurs Organisateurs[] */
        
        if (empty($organisateurs)) {
            return new JsonResponse(["message" => "Aucun organisateur pour ce raid !"], Response::HTTP_NOT_FOUND);
        }

       return $organisateurs;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="ORGANISATEUR",
     *     input="AppBundle\Form\OrganisateurType",
     *     output="AppBundle\Form\Organisateur",
     *     description="Delete all organisateurs of a specific RAID",
     *     statusCodes={
     *         202="Remove all organisateurs successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/organisateurs/raids/{id_raid}", name="delete_all_organisateurs_one_raid")
     */
    public function deleteOrganisateursByIdRaid(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $organisateurs = $em->getRepository('AppBundle:Organisateur')
                        ->findBy(array("idRaid" => $request->get('id_raid')));
       
        if ($organisateurs) {
            foreach ($organisateurs as $organisateur) {
                $em->remove($organisateur);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="ORGANISATEUR",
     *     description="Get organisateur if present in a specific RAID",
     *     statusCodes={
     *         200="Returned when organisateur is found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no organisateurs are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/organisateurs/raids/{id_raid}/users/{id_user}", name="get_organisateur_one_raid")
     */
    public function getOrganisateurByRaidIdAndByUserId(Request $request)
    {
        $organisateur = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Organisateur')
                ->findOneBy(array(  
                    'idRaid' => $request->get('id_raid'), 
                    'idUser' => $request->get('id_user'))
                );

        if(empty($organisateur)) {
            return New JsonResponse(['message' => "L'utilisateur recherché n'est pas organisateur de ce raid"], Response::HTTP_NOT_FOUND);
        }

        return $organisateur;
    }

    /**
     * @Doc\ApiDoc(
     *     section="ORGANISATEUR",
     *     input="AppBundle\Form\OrganisateurType",
     *     output="AppBundle\Form\Organisateur",
     *     description="Add organisateur in a RAID",
     *     statusCodes={
     *         200="Returned when organisateur has been added successfully",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no organisateurs are presents in the database"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("api/organisateurs/raids/{id_raid}/users/{id_user}", name="post_organisateur_one_raid")
     */
    public function postOrganisateurByIdRaidAndByIdUser(Request $request)
    {

        $raid = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Raid')
                ->find($request->get('id_raid'));
        
        if(empty($raid)){
            return new JsonResponse(['message' => "Le raid selectionné n'existe pas !"], Response::HTTP_NOT_FOUND);
        }
 
        $user = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->find($request->get('id_user'));
        
        if(empty($user)){
            return new JsonResponse(['message' => "L'utilisateur selectionné n'existe pas !"], Response::HTTP_NOT_FOUND);
        }
        
        $organisateur = new Organisateur();

        $form = $this->createForm(OrganisateurType::class, $organisateur);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($organisateur);
            $em->flush();
            return $organisateur;
        } else {
            return $form;
        }
    }

    /**
     *  @Doc\ApiDoc(
     *     section="ORGANISATEUR",
     *     input="AppBundle\Form\OrganisateurType",
     *     output="AppBundle\Form\Organisateur",
     *     description="Delete organisateur of a specific RAID",
     *     statusCodes={
     *         202="Remove organisateur if present successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/organisateurs/raids/{id_raid}/users/{id_user}", name="delete_organisateur_one_raid")
     */
    public function deleteOrganisateurByIdRaidAndByIdUser(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $organisateur = $this->getDoctrine()->getRepository('AppBundle:Organisateur')
                        ->findOneBy(array(
                            "idRaid" => $request->get('id_raid'),
                            "idUser" => $request->get('id_user')
                        ));
       
        if ($organisateur) {
            $em->remove($organisateur);
            $em->flush();
        }
    }
}
