<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Form\PointType;
use AppBundle\Entity\Point;
use Nelmio\ApiDocBundle\Annotation as Doc;

class PointController extends Controller
{
    /**
     * @Doc\ApiDoc(
     *     section="POINT",
     *     description="Get all points",
     *     statusCodes={
     *         200="Returned when points are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no points are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/points", name="get_all_point")
     */
    public function getPoints(Request $request)
    {
        $points = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Point')
                ->findAll();
        
        if(empty($points)){
            return new JsonResponse(["message" => "Aucun points présent dans la BDD !"], Response::HTTP_NOT_FOUND);
        }
        
        return $points;
    }

    /**
     * @Doc\ApiDoc(
     *     section="POINT",
     *     input="AppBundle\Form\PointType",
     *     output="AppBundle\Form\Point",
     *     description="Create new point",
     *     statusCodes={
     *         202="Point created successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/points", name="post_all_point")
     */
    public function postPoints(Request $request)
    {
        $trace = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Trace')
                    ->find($request->get('idTrace'));
        
        if(empty($trace)) {
            return new JsonResponse(['message' => 'Le point ne peut pas être ajouté au tracé car inexistant'], Response::HTTP_NOT_FOUND);
        }

        $point = new Point();
        
        $form = $this->createForm(PointType::class, $point);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($point);
            $em->flush();
            
            return $point;

        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="POINT",
     *     description="Delete all points",
     *     statusCodes={
     *         202="All points have been removed",
     *         401="Unauthorized, you need to use auth-token",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/points", name="delete_all_point")
     */
    public function deletePoints(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $points = $em->getRepository('AppBundle:Point')
                ->findAll();

        if($points) {
            foreach ($points as $point) {
                $em->remove($point);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="POINT",
     *     description="Get one point",
     *     statusCodes={
     *         200="Returned when point is found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no points are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/points/{id_point}", name="get_point_one")
     */
    public function getOnePoint(Request $request)
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $point = $em->getRepository('AppBundle:Point')
                    ->find($request->get('id_point'));
        /* @var $point Point */

        if(empty($point)){
            return new JsonResponse(["message" => "Point non trouvé !"], Response::HTTP_NOT_FOUND);
        }
        
        return $point;
    }


    /**
     * @Doc\ApiDoc(
     *     section="POINT",
     *     input="AppBundle\Form\PointType",
     *     output="AppBundle\Form\Point",
     *     description="Update one point",
     *     statusCodes={
     *         200="Returned when point have been modified",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no points are presents in the database"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/points/{id_point}", name="post_point_one")
     */
    public function updateOnePoint(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $point = $this->getDoctrine()->getRepository('AppBundle:Point')
                ->find($request->get('id_point'));

        if(empty($point)){
            return new JsonResponse(["message" => "Le point à modifier n'a pas été trouvé !"], Response::HTTP_NOT_FOUND);
        }

        $trace = $this->getDoctrine()->getRepository('AppBundle:Trace')
                ->find($request->get('idTrace'));

        if(empty($trace)){
            return new JsonResponse(["message" => "Le tracé renseigné n'est pas dans la bdd"], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(PointType::class, $point);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->flush();
            return $point;
        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="POINT",
     *     description="Delete one point",
     *     statusCodes={
     *         202="Returned when point is found",
     *         401="Unauthorized, you need to use auth-token"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/points/{id_point}", name="delete_point_one")
     */
    public function deleteOnePoint(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $point = $em->getRepository('AppBundle:Point')
                ->find($request->get('id_point'));

        if($point){
            $em->remove($point);
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="POINT",
     *     description="Get all points of one parcours",
     *     statusCodes={
     *         200="Returned when points are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no points are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/points/parcours/{id_parcours}", name="get_point_one_parcours")
     */
    public function getPointsByIdParcours(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $parcours = $em->getRepository('AppBundle:Parcours')
                    ->find($request->get('id_parcours'));

        if(empty($parcours)){
            return new JsonResponse(["message" => "Ce parcours n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $points = $em->getRepository('AppBundle:Point')
                    ->findPointsByIdParcours($request->get('id_parcours'));

        if(empty($points)){
            return new JsonResponse(["message" => "Aucun points dans ce parcours"], Response::HTTP_NOT_FOUND);
        }
        
        return $points;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="POINT",
     *     description="Delete all points of a specific parcours",
     *     statusCodes={
     *         202="Remove all points successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/points/parcours/{id_parcours}", name="delete_point_one_parcours")
     */
    public function deletePointsByIdParcours(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $parcours = $em->getRepository('AppBundle:Parcours')
                    ->find($request->get('id_parcours'));

        if(empty($parcours)){
            return new JsonResponse(["message" => "Ce parcours n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $points = $em->getRepository('AppBundle:Point')
                    ->findPointsByIdParcours($request->get('id_parcours'));

        if($points){
            foreach($points as $point){
                $em->remove($point);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="POINT",
     *     description="Get all points of one trace",
     *     statusCodes={
     *         200="Returned when points are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no points are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/points/traces/{id_trace}", name="get_point_one_trace")
     */
    public function getPointsByIdTrace(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $trace = $em->getRepository('AppBundle:Trace')
                    ->find($request->get('id_trace'));

        if(empty($trace)){
            return new JsonResponse(["message" => "Ce tracé n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $points = $em->getRepository('AppBundle:Point')
                    ->findBy(
                        array('idTrace' => $request->get('id_trace')), 
                        array('ordre' => 'ASC')
                    );

        if(empty($points)){
            return new JsonResponse(["message" => "Aucun points dans ce tracé"], Response::HTTP_NOT_FOUND);
        }
        
        return $points;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="POINT",
     *     description="Delete all points of a specific trace",
     *     statusCodes={
     *         202="Remove all points successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/points/traces/{id_trace}", name="delete_point_one_trace")
     */
    public function deletePointsByIdTrace(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $trace = $em->getRepository('AppBundle:Trace')
                    ->find($request->get('id_trace'));

        if(empty($trace)){
            return new JsonResponse(["message" => "Ce tracé n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $points = $em->getRepository('AppBundle:Point')
                    ->findBy(
                        array('idTrace' => $request->get('id_trace')),
                        array('ordre' => 'ASC')
                    );

        if($points){
            foreach($points as $point){
                $em->remove($point);
            }
            $em->flush();
        }
    }

    /**
     *  @Doc\ApiDoc(
     *     section="POINT",
     *     description="Get departure point of one trace",
     *     statusCodes={
     *         200="Returned when departure point is found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no points are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/points/traces/{id_trace}/depart", name="get_point_one_trace_depart")
     */
    public function getPointDepartByIdTrace(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $trace = $em->getRepository('AppBundle:Trace')
                ->find($request->get('id_trace'));

        if(empty($trace)){
            return new JsonResponse(["message" => "Ce tracé n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $point = $em->getRepository('AppBundle:Point')
                    ->findOneBy(array(
                        'idTrace' => $request->get('id_trace'),
                        'type' => 1
                    ));
        /* @var $point Point */

        if(empty($point)){
            return new JsonResponse(["message" => "Pas de point de départ définit pour ce tracé"], Response::HTTP_NOT_FOUND);
        }
        
        return $point;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="POINT",
     *     description="Delete departure point of a specific trace",
     *     statusCodes={
     *         202="Remove departure point successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/points/traces/{id_trace}/depart", name="delete_point_one_trace_depart")
     */
    public function deletePointDepartByIdTrace(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $trace = $em->getRepository('AppBundle:Trace')
            ->find($request->get('id_trace'));

        if(empty($trace)){
            return new JsonResponse(["message" => "Ce tracé n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $point = $em->getRepository('AppBundle:Point')
                    ->findOneBy(array(
                        'idTrace' => $request->get('id_trace'),
                        'type' => 1
                    ));

        if($point){
            $em->remove($point);
            $em->flush();
        }
    }


    /**
     *  @Doc\ApiDoc(
     *     section="POINT",
     *     description="Get arrival point of one trace",
     *     statusCodes={
     *         200="Returned when arrival point is found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no points are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/points/traces/{id_trace}/arrivee", name="get_point_one_trace_arrivee")
     */
    public function getPointArriveByIdTrace(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $trace = $em->getRepository('AppBundle:Trace')
            ->find($request->get('id_trace'));

        if(empty($trace)){
            return new JsonResponse(["message" => "Ce tracé n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $point = $em->getRepository('AppBundle:Point')
                    ->findOneBy(array(
                        'idTrace' => $request->get('id_trace'),
                        'type' => 2
                    ));
        /* @var $point Point */

        if(empty($point)){
            return new JsonResponse(["message" => "Pas de point d'arrivée définit pour ce tracé"], Response::HTTP_NOT_FOUND);
        }
        
        return $point;
    }

    /**     
     *  @Doc\ApiDoc(
     *     section="POINT",
     *     description="Delete arrival point of a specific trace",
     *     statusCodes={
     *         202="Remove arrival point successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/points/traces/{id_trace}/arrivee", name="delete_point_one_trace_arrivee")
     */
    public function deletePointArriveByIdTrace(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $trace = $em->getRepository('AppBundle:Trace')
            ->find($request->get('id_trace'));

        if(empty($trace)){
            return new JsonResponse(["message" => "Ce tracé n'existe pas"], Response::HTTP_NOT_FOUND);
        }

        $point = $em->getRepository('AppBundle:Point')
                    ->findOneBy(array(
                        'idTrace' => $request->get('id_trace'),
                        'type' => 2
                    ));
        /* @var $point Point */

        if($point){
            $em->remove($point);
            $em->flush();
        }
    }


    /**
     *  @Doc\ApiDoc(
     *     section="POINT",
     *     description="Get poste point of one trace",
     *     statusCodes={
     *         200="Returned when poste point is found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no points are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/points/postes/{id_poste}", name="get_one_poste")
     */
    public function getPointByIdPoste(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $poste = $em->getRepository('AppBundle:Poste')
            ->find($request->get('id_poste'));
        /* @var $poste Poste */

        if(empty($poste)){
            return new JsonResponse(["message" => "Ce poste n'existe pas"], Response::HTTP_NOT_FOUND);
        }
        
        $points = $em->getRepository('AppBundle:Point')
                    ->findPointByIdPoste($request->get('id_poste'));
        /* @var $points Point */

        if(empty($points)){
            return new JsonResponse(["message" => "Aucun point trouvé pour le poste demandé"], Response::HTTP_NOT_FOUND);
        }
        
        return $points;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="POINT",
     *     description="Delete poste point",
     *     statusCodes={
     *         202="Remove poste point successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/points/postes/{id_poste}", name="delete_one_poste")
     */
    public function deletePointByIdPoste(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $poste = $em->getRepository('AppBundle:Poste')
            ->find($request->get('id_poste'));
        /* @var $poste Poste */

        if(empty($poste)){
            return new JsonResponse(["message" => "Ce poste n'existe pas"], Response::HTTP_NOT_FOUND);
        }
        
        $point = $em->getRepository('AppBundle:Point')
                    ->findPointByIdPoste($request->get('id_poste'));
        /* @var $point Point */

        if($point){
            $em->remove($point);
            $em->flush();
        }
    }
}
