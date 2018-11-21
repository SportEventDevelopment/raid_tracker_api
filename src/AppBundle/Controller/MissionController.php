<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Form\MissionType;
use AppBundle\Entity\Mission;
use Nelmio\ApiDocBundle\Annotation as Doc;

class MissionController extends Controller
{
    /**
     * @Doc\ApiDoc(
     *     section="MISSION",
     *     description="Get all missions",
     *     statusCodes={
     *         200="Returned when missions are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no mission is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/missions", name="get_all_missions")
     */
    public function getMissions(Request $request)
    {
        $missions = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Mission')
                ->findAll();
        
        if(empty($missions)){
            return new JsonResponse(["message" => "Aucune mission présente dans la BDD !"], Response::HTTP_NOT_FOUND);
        }
        
        return $missions;
    }

    /**
     * @Doc\ApiDoc(
     *     section="MISSION",
     *     input="AppBundle\Form\MissionType",
     *     output="AppBundle\Form\Mission",
     *     description="Create new mission",
     *     statusCodes={
     *         202="Missions created successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/missions", name="post_all_missions")
     */
    public function postMissions(Request $request)
    {
        $idPoste = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Poste')
                    ->find($request->get('idPoste'));

        if(empty($idPoste)) {
            return new JsonResponse(['message' => 'La mission ne peut pas être affectée au poste car le poste est inexistant'], Response::HTTP_NOT_FOUND);
        }

        $objectif = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Mission')
                    ->find($request->get('objectif'));

        if(empty($objectif)) {
            return new JsonResponse(['message' => 'La mission ne peut pas être affectée au poste car l\'objectif est inexistant'], Response::HTTP_NOT_FOUND);
        }

        $mission = new Mission();
        
        $form = $this->createForm(MissionType::class, $mission);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($mission);
            $em->flush();
            
            return $mission;

        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="MISSION",
     *     description="Delete all missions",
     *     statusCodes={
     *         202="All missions have been removed",
     *         401="Unauthorized, you need to use auth-token",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/missions", name="delete_all_missions")
     */
    public function deleteMissions(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $missions = $em->getRepository('AppBundle:Mission')
                ->findAll();

        if($missions) {
            foreach ($missions as $mission) {
                $em->remove($mission);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="MISSION",
     *     description="Get one mission",
     *     statusCodes={
     *         200="Returned when mission is found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no mission is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/missions/{id_mission}", name="get_mission_one")
     */
    public function getOneMission(Request $request)
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $mission = $em->getRepository('AppBundle:Mission')
                    ->find($request->get('id_mission'));
        /* @var $point Point */

        if(empty($mission)){
            return new JsonResponse(["message" => "Mission non trouvée !"], Response::HTTP_NOT_FOUND);
        }
        
        return $mission;
    }


    /**
     * @Doc\ApiDoc(
     *     section="MISSION",
     *     input="AppBundle\Form\MissionType",
     *     output="AppBundle\Form\Mission",
     *     description="Update one mission",
     *     statusCodes={
     *         200="Returned when point have been modified",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no mission is present in the database"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/missions/{id_mission}", name="post_mission_one")
     */
    public function updateOneMission(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $mission = $this->getDoctrine()->getRepository('AppBundle:Mission')
                ->find($request->get('id_mission'));

        if(empty($mission)){
            return new JsonResponse(["message" => "La mission à modifier n'a pas été trouvée !"], Response::HTTP_NOT_FOUND);
        }

        $idPoste = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Poste')
            ->find($request->request->get('idPoste'));
            
        if(empty($idPoste)) {
            return new JsonResponse(['message' => 'La mission ne peut pas être affecté au poste car le poste est inexistant'], Response::HTTP_NOT_FOUND);
        }

        $objectif = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Mission')
                ->find($request->request->get('objectif'));

        if(empty($objectif)) {
            return new JsonResponse(['message' => 'La mission ne peut pas être affectée au poste car l\'objectif est inexistant'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(MissionType::class, $mission);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->flush();
            return $mission;
        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="MISSION",
     *     description="Delete one mission",
     *     statusCodes={
     *         202="Returned when mission is found",
     *         401="Unauthorized, you need to use auth-token"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/missions/{id_mission}", name="delete_mission_one")
     */
    public function deleteOneMission(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $mission = $em->getRepository('AppBundle:Mission')
                ->find($request->get('id_mission'));

        if($mission){
            $em->remove($mission);
            $em->flush();
        }
    }

        /**
     * @Doc\ApiDoc(
     *     section="MISSION",
     *     description="Get missions of one raid",
     *     statusCodes={
     *         200="Returned when missions are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no mission is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/missions/raids/{id_raid}", name="get_all_missions_raid")
     */
    public function getMissionsByIdRaid(Request $request)
    {
        $missions = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Mission')
                ->findBy(array("idRaid" => $request->get('id_raid')));
        /* @var $benevole Benevole */

        if (empty($missions)) {
            return new JsonResponse(['message' => "Le raid ne contient pas encore de missions !"], Response::HTTP_NOT_FOUND);
        }

        return $missions;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="MISSION",
     *     input="AppBundle\Form\MissionType",
     *     output="AppBundle\Form\Mission",
     *     description="Delete all missions of a specific RAID",
     *     statusCodes={
     *         202="Remove all missions successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/missions/raids/{id_raid}", name="delete_all_missions_raid")
     */
    public function deleteMissionsByIdRaid(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $missions = $em->getRepository('AppBundle:Mission')
                    ->findBy(array("idRaid" => $request->get('id_raid')));

        if ($missions) {
            foreach ($missions as $mission) {
                $em->remove($mission);
            }
            $em->flush();
        }
    }

        /**
     * @Doc\ApiDoc(
     *     section="MISSION",
     *     description="Get misisons of one parcours",
     *     statusCodes={
     *         200="Returned when missions are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no mission is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/missions/parcours/{id_parcours}", name="get_all_missions_parcours")
     */
    public function getMissionsByIdParcours(Request $request)
    {
        $missions = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Mission')
                ->findBy(array("idParcours" => $request->get('id_parcours')));
        /* @var $benevole Benevole */

        if (empty($missions)) {
            return new JsonResponse(['message' => "Le parcours ne contient pas encore de missions !"], Response::HTTP_NOT_FOUND);
        }

        return $missions;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="MISSION",
     *     input="AppBundle\Form\MissionType",
     *     output="AppBundle\Form\Mission",
     *     description="Delete all missions of a specific PARCOURS",
     *     statusCodes={
     *         202="Remove all missions successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/missions/parcours/{id_parcours}", name="delete_all_missions_parcours")
     */
    public function deleteMissionsByIdParcours(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $missions = $em->getRepository('AppBundle:Mission')
                    ->findBy(array("idParcours" => $request->get('id_parcours')));

        if ($missions) {
            foreach ($missions as $mission) {
                $em->remove($mission);
            }
            $em->flush();
        }
    }

        /**
     * @Doc\ApiDoc(
     *     section="MISSION",
     *     description="Get missions of one poste",
     *     statusCodes={
     *         200="Returned when repartitions are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no mission is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/missions/postes/{id_poste}", name="get_all_missions_poste")
     */
    public function getMissionsByIdPoste(Request $request)
    {
        $missions = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Mission')
                ->findBy(array("idPoste" => $request->get('id_poste')));
        /* @var $benevole Benevole */

        if (empty($missions)) {
            return new JsonResponse(['message' => "Le poste n'a pas encore de missions !"], Response::HTTP_NOT_FOUND);
        }

        return $missions;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="MISSION",
     *     input="AppBundle\Form\MissionType",
     *     output="AppBundle\Form\Mission",
     *     description="Delete all missions of a specific POSTE",
     *     statusCodes={
     *         202="Remove all missions successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/missions/postes/{id_poste}", name="delete_all_missions_poste")
     */
    public function deleteMissionsByIdPoste(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $missions = $em->getRepository('AppBundle:Mission')
                    ->findBy(array("idPoste" => $request->get('id_poste')));

        if ($missions) {
            foreach ($missions as $mission) {
                $em->remove($mission);
            }
            $em->flush();
        }
    }
}
