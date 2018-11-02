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

class RaidController extends Controller
{
    /**
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
