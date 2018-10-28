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

class OrganisateurController extends Controller
{

    /**
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
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("api/organisateurs/raids/{id_raid}/users/{id_user}", name="post_organisateur_one_raid")
     */
    public function postOrganisateurByIdRaidAndByIdUser(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $organisateur = $this->getDoctrine()->getRepository('AppBundle:Organisateur')
                ->findOneBy(array(  
                    'idRaid' => $request->get('id_raid'), 
                    'idUser' => $request->get('id_user'))
                );

        if(empty($organisateur)){
            return new JsonResponse(["message" => "L'utilisateur recherché n'est pas organisateur de ce raid !"], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(OrganisateurType::class, $organisateur);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->merge($organisateur);
            $em->flush();
            return $organisateur;
        } else {
            return $form;
        }
    }

    /**
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
