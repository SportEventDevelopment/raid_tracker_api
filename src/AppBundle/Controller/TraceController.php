<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Form\TraceType;
use AppBundle\Entity\Trace;
use Nelmio\ApiDocBundle\Annotation as Doc;

class TraceController extends Controller
{
    /**
     * @Doc\ApiDoc(
     *     section="TRACE",
     *     description="Get all traces",
     *     statusCodes={
     *         200="Returned when traces are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no traces are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/traces", name="get_all_trace")
     */
    public function getTraces(Request $request)
    {
        $traces = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Trace')
                ->findAll();
        
        if(empty($traces)){
            return new JsonResponse(["message" => "Aucun tracé présent dans la BDD !"], Response::HTTP_NOT_FOUND);
        }
        
        return $traces;
    }

    /**
     * @Doc\ApiDoc(
     *     section="TRACE",
     *     input="AppBundle\Form\TraceType",
     *     output="AppBundle\Form\Trace",
     *     description="Create new trace",
     *     statusCodes={
     *         202="Trace created successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/traces", name="post_all_trace")
     */
    public function postTraces(Request $request)
    {
        $parcours = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Parcours')
                    ->find($request->get('idParcours'));

        if(empty($parcours)) {
            return new JsonResponse(['message' => 'Parcours '. $request->get('idParcours').' inexistant '], Response::HTTP_NOT_FOUND);
        }

        $trace = new Trace();
        
        $form = $this->createForm(TraceType::class, $trace);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($trace);
            $em->flush();
            
            return $trace;

        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="TRACE",
     *     description="Delete all traces",
     *     statusCodes={
     *         202="All traces have been removed",
     *         401="Unauthorized, you need to use auth-token",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/traces", name="delete_all_trace")
     */
    public function deleteTraces(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $traces = $em->getRepository('AppBundle:Trace')
                ->findAll();

        if($traces) {
            foreach ($traces as $trace) {
                $em->remove($trace);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="TRACE",
     *     description="Get one trace",
     *     statusCodes={
     *         200="Returned when trace is found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no traces are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/traces/{id_trace}", name="get_trace_one")
     */
    public function getOneTrace(Request $request)
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $trace = $em->getRepository('AppBundle:Trace')
                    ->find($request->get('id_trace'));
        /* @var $trace Trace */

        if(empty($trace)){
            return new JsonResponse(["message" => "Tracé ". $request->get('id_trace') ." non trouvé !"], Response::HTTP_NOT_FOUND);
        }
        
        return $trace;
    }


    /**
     * @Doc\ApiDoc(
     *     section="TRACE",
     *     input="AppBundle\Form\TraceType",
     *     output="AppBundle\Form\Trace",
     *     description="Update one trace",
     *     statusCodes={
     *         200="Returned when trace have been modified",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no traces are presents in the database"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/traces/{id_trace}", name="post_trace_one")
     */
    public function updateOneTrace(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $trace = $this->getDoctrine()->getRepository('AppBundle:Trace')
                ->find($request->get('id_trace'));

        if(empty($trace)){
            return new JsonResponse(["message" => "Tracé ". $request->get('id_trace') ." inexistant !"], Response::HTTP_NOT_FOUND);
        }

        $parcours = $this->getDoctrine()->getRepository('AppBundle:Parcours')
                ->find($request->get('idParcours'));

        if(empty($parcours)){
            return new JsonResponse(["message" => "Le parcours renseigné n'est pas dans la bdd"], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(TraceType::class, $trace);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->flush();
            return $trace;
        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="TRACE",
     *     description="Delete one trace",
     *     statusCodes={
     *         202="Returned when trace is found",
     *         401="Unauthorized, you need to use auth-token"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/traces/{id_trace}", name="delete_trace_one")
     */
    public function deleteOneTrace(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $trace = $em->getRepository('AppBundle:Trace')
                ->find($request->get('id_trace'));

        if($trace){
            $em->remove($trace);
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="TRACE",
     *     description="Get traces by id parcours",
     *     statusCodes={
     *         200="Returned when traces are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no traces are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/traces/parcours/{id_parcours}", name="get_traces_by_idparcours")
     */
    public function getTracesByIdParcours(Request $request)
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $parcours = $em->getRepository('AppBundle:Parcours')
                    ->find($request->get('id_parcours'));
        /* @var $parcours Parcours */

        if(empty($parcours)){
            return new JsonResponse(["message" => "Parcours ". $request->get('id_parcours') ." non trouvé !"], Response::HTTP_NOT_FOUND);
        }
        
        $trace = $em->getRepository('AppBundle:Trace')
                ->findTracesByIdParcours($request->get('id_parcours'));
        /* @var $trace Trace */
        
        if(empty($trace)){
            return new JsonResponse(["message" => "Aucun tracé pour le parcours ". $request->get('id_parcours')], Response::HTTP_NOT_FOUND);
        }

        return $trace;
    }

    /**
     * @Doc\ApiDoc(
     *     section="TRACE",
     *     description="Delete all traces by idparcours",
     *     statusCodes={
     *         202="All traces have been removed",
     *         401="Unauthorized, you need to use auth-token",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/traces/parcours/{id_parcours}", name="delete_all_trace_by_idparcours")
     */
    public function deleteTracesByIdParcours(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $traces = $em->getRepository('AppBundle:Trace')
                ->findTracesByIdParcours($request->get('id_parcours'));

        if($traces) {
            foreach ($traces as $trace) {
                $em->remove($trace);
            }
            $em->flush();
        }
    }
}
