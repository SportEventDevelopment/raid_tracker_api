<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Form\CheckinType;
use AppBundle\Entity\Checkin;
use Nelmio\ApiDocBundle\Annotation as Doc;

class CheckInController extends Controller
{
    /**
     * @Doc\ApiDoc(
     *     section="CHECKIN",
     *     description="Get all checkin",
     *     statusCodes={
     *         200="Returned when checkin are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no checkin is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/checkin", name="get_all_checkin")
     */
    public function getCheckin(Request $request)
    {
        $checkin = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Checkin')
                ->findAll();
        
        if(empty($checkin)){
            return new JsonResponse(["message" => "Aucun checkin présent dans la BDD !"], Response::HTTP_NOT_FOUND);
        }
        
        return $checkin;
    }

    /**
     * @Doc\ApiDoc(
     *     section="CHECKIN",
     *     input="AppBundle\Form\CheckinType",
     *     output="AppBundle\Form\Checkin",
     *     description="Create new checkin",
     *     statusCodes={
     *         202="Checkin created successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/checkin", name="post_all_checkin")
     */
    public function postCheckin(Request $request)
    {
        $idRepartition = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Repartition')
                    ->find($request->get('idRepartition'));

        if(empty($idRepartition)) {
            return new JsonResponse(['message' => 'Le checkin n\'a pas pu être créé car la répartition est inexistante'], Response::HTTP_NOT_FOUND);
        }

        $confirmation = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Checkin')
                    ->find($request->get('confirmation'));

        if(empty($confirmation)) {
            return new JsonResponse(['message' => 'Le checkin n\'a pas pu être créé car la confirmation est inexistante'], Response::HTTP_NOT_FOUND);
        }

        $checkin = new Checkin();
        
        $form = $this->createForm(CheckinType::class, $checkin);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($checkin);
            $em->flush();
            
            return $checkin;

        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="CHECKIN",
     *     description="Delete all checkin",
     *     statusCodes={
     *         202="All checkin have been removed",
     *         401="Unauthorized, you need to use auth-token",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/checkin", name="delete_all_checkin")
     */
    public function deleteCheckin(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $checkins = $em->getRepository('AppBundle:Checkin')
                ->findAll();

        if($checkins) {
            foreach ($checkins as $checkin) {
                $em->remove($checkin);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="CHECKIN",
     *     description="Get one checkin",
     *     statusCodes={
     *         200="Returned when checkin is found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no checkin is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/checkin/{id_checkin}", name="get_checkin_one")
     */
    public function getOneCheckin(Request $request)
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $checkin = $em->getRepository('AppBundle:Checkin')
                    ->find($request->get('id_checkin'));
        /* @var $point Point */

        if(empty($checkin)){
            return new JsonResponse(["message" => "Checkin non trouvée !"], Response::HTTP_NOT_FOUND);
        }
        
        return $checkin;
    }


    /**
     * @Doc\ApiDoc(
     *     section="CHECKIN",
     *     input="AppBundle\Form\CheckinType",
     *     output="AppBundle\Form\Checkin",
     *     description="Update one checkin",
     *     statusCodes={
     *         200="Returned when checkin have been modified",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no checkin is present in the database"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/checkin/{id_checkin}", name="post_checkin_one")
     */
    public function updateOneCheckin(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $checkin = $this->getDoctrine()->getRepository('AppBundle:Checkin')
                ->find($request->get('id_checkin'));

        if(empty($checkin)){
            return new JsonResponse(["message" => "Le checkin à modifier n'a pas été trouvé !"], Response::HTTP_NOT_FOUND);
        }

        $idRepartition = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Repartition')
            ->find($request->request->get('idRepartition'));
            
        if(empty($idRepartition)) {
            return new JsonResponse(['message' => 'Le checkin ne peut pas être modifié car la répartition est inexistante'], Response::HTTP_NOT_FOUND);
        }

        $confirmation = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Checkin')
                ->find($request->request->get('confirmation'));

        if(empty($confirmation)) {
            return new JsonResponse(['message' => 'Le checkin ne peut pas être modifié car la confirmation est inexistante'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(RepartitionType::class, $checkin);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->flush();
            return $checkin;
        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="CHECKIN",
     *     description="Delete one checkin",
     *     statusCodes={
     *         202="Returned when checkin is found",
     *         401="Unauthorized, you need to use auth-token"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/checkin/{id_checkin}", name="delete_checkin_one")
     */
    public function deleteOneCheckin(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $checkin = $em->getRepository('AppBundle:Checkin')
                ->find($request->get('id_checkin'));

        if($checkin){
            $em->remove($checkin);
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="CHECKIN",
     *     description="Get checkin of one RAID",
     *     statusCodes={
     *         200="Returned when checkin are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no checkin is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/checkin/raids/{id_raid}", name="get_all_checkin_raid")
     */
    public function getCheckinByIdRaid(Request $request)
    {
        $checkin = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Checkin')
                ->findBy(array("idRaid" => $request->get('id_raid')));
        /* @var $benevole Benevole */

        if (empty($checkin)) {
            return new JsonResponse(['message' => "Le raid n'a pas encore de checkin !"], Response::HTTP_NOT_FOUND);
        }

        return $checkin;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="CHECKIN",
     *     input="AppBundle\Form\CheckinType",
     *     output="AppBundle\Form\Checkin",
     *     description="Delete all checkin of a specific RAID",
     *     statusCodes={
     *         202="Remove all checkin successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/checkin/raids/{id_raid}", name="delete_all_checkin_raid")
     */
    public function deleteCheckinByIdRaid(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $checkin = $em->getRepository('AppBundle:Checkin')
                    ->findBy(array("idRaid" => $request->get('id_raid')));

        if ($checkins) {
            foreach ($checkins as $checkin) {
                $em->remove($checkin);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="CHECKIN",
     *     description="Get checkin of one USER",
     *     statusCodes={
     *         200="Returned when checkin are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no checkin is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/checkin/users/{id_user}", name="get_all_checkin_user")
     */
    public function getCheckinByIdUser(Request $request)
    {
        $checkin = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Checkin')
                ->findBy(array("idUser" => $request->get('id_user')));
        /* @var $benevole Benevole */

        if (empty($checkin)) {
            return new JsonResponse(['message' => "L'utilisateur n'a pas encore de checkin !"], Response::HTTP_NOT_FOUND);
        }

        return $checkin;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="CHECKIN",
     *     input="AppBundle\Form\CheckinType",
     *     output="AppBundle\Form\Checkin",
     *     description="Delete all checkin of a specific USER",
     *     statusCodes={
     *         202="Remove all checkin successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/checkin/users/{id_user}", name="delete_all_checkin_user")
     */
    public function deleteCheckinByIdUser(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $checkin = $em->getRepository('AppBundle:Checkin')
                    ->findBy(array("idUser" => $request->get('id_user')));

        if ($checkins) {
            foreach ($checkins as $checkin) {
                $em->remove($checkin);
            }
            $em->flush();
        }
    }

}
