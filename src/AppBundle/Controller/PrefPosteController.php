<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Form\PrefPosteType;
use AppBundle\Entity\PrefPoste;
use Nelmio\ApiDocBundle\Annotation as Doc;

class PrefPosteController extends Controller
{
    /**
     * @Doc\ApiDoc(
     *     section="PREFPOSTE",
     *     description="Get all prefpostes",
     *     statusCodes={
     *         200="Returned when prefpostes are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no prefposte is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/prefpostes", name="get_all_prefpostes")
     */
    public function getPrefPostes(Request $request)
    {
        $prefpostes = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:PrefPoste')
                ->findAll();
        
        if(empty($prefpostes)){
            return new JsonResponse(["message" => "Aucune préférence de poste présente dans la BDD !"], Response::HTTP_NOT_FOUND);
        }
        
        return $prefpostes;
    }

    /**
     * @Doc\ApiDoc(
     *     section="PREFPOSTE",
     *     input="AppBundle\Form\PrefPosteType",
     *     output="AppBundle\Form\PrefPoste",
     *     description="Create new prefposte",
     *     statusCodes={
     *         202="PrefPostes created successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/prefpostes", name="post_all_prefpostes")
     */
    public function postPrefPostes(Request $request)
    {
        $idPoste = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:PrefPoste')
                    ->find($request->get('idPoste'));

        if(empty($idPoste)) {
            return new JsonResponse(['message' => 'La préférence de poste ne peut pas être affectée au bénévole car le poste est inexistant'], Response::HTTP_NOT_FOUND);
        }

        $benevole = $this->get('doctrine.orm.entity_manager')
                    ->getRepository('AppBundle:Benevole')
                    ->find($request->get('idBenevole'));

        if(empty($benevole)) {
            return new JsonResponse(['message' => 'La préférence de poste ne peut pas être affectée au bénévole car le bénévole est inexistant'], Response::HTTP_NOT_FOUND);
        }

        $prefposte = new PrefPoste();
        
        $form = $this->createForm(PrefPosteType::class, $prefposte);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($prefposte);
            $em->flush();
            
            return $prefposte;

        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="PREFPOSTE",
     *     description="Delete all prefpostes",
     *     statusCodes={
     *         202="All prefpostes have been removed",
     *         401="Unauthorized, you need to use auth-token",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/prefpostes", name="delete_all_prefpostes")
     */
    public function deletePrefPostes(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $prefpostes = $em->getRepository('AppBundle:PrefPoste')
                ->findAll();

        if($prefpostes) {
            foreach ($prefpostes as $prefposte) {
                $em->remove($prefposte);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="PREFPOSTE",
     *     description="Get one prefposte",
     *     statusCodes={
     *         200="Returned when prefposte is found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no prefposte is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/prefpostes/{id_prefposte}", name="get_prefposte_one")
     */
    public function getOnePrefPoste(Request $request)
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $prefposte = $em->getRepository('AppBundle:PrefPoste')
                    ->find($request->get('id_prefposte'));
        /* @var $point Point */

        if(empty($prefposte)){
            return new JsonResponse(["message" => "PrefPoste non trouvée !"], Response::HTTP_NOT_FOUND);
        }
        
        return $prefposte;
    }


    /**
     * @Doc\ApiDoc(
     *     section="PREFPOSTE",
     *     input="AppBundle\Form\PrefPosteType",
     *     output="AppBundle\Form\PrefPoste",
     *     description="Update one prefposte",
     *     statusCodes={
     *         200="Returned when prefposte have been modified",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no prefposte is present in the database"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/prefpostes/{id_prefposte}", name="post_prefposte_one")
     */
    public function updateOnePrefPoste(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $prefposte = $this->getDoctrine()->getRepository('AppBundle:PrefPoste')
                ->find($request->get('id_prefposte'));

        if(empty($prefposte)){
            return new JsonResponse(["message" => "La prefposte à modifier n'a pas été trouvée !"], Response::HTTP_NOT_FOUND);
        }

        $idPoste = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Poste')
            ->find($request->request->get('idPoste'));
            
        if(empty($idPoste)) {
            return new JsonResponse(['message' => 'La prefposte ne peut pas être affectée au bénévole car le poste est inexistant'], Response::HTTP_NOT_FOUND);
        }

        $idBenevole = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Benevole')
                ->find($request->request->get('idBenevole'));

        if(empty($idBenevole)) {
            return new JsonResponse(['message' => 'La prefposte ne peut pas être affectée au bénévole car le bénévole est inexistant'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(PrefPosteType::class, $prefposte);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->flush();
            return $prefposte;
        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="PREFPOSTE",
     *     description="Delete one prefposte",
     *     statusCodes={
     *         202="Returned when prefposte is found",
     *         401="Unauthorized, you need to use auth-token"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/prefpostes/{id_prefposte}", name="delete_prefposte_one")
     */
    public function deleteOnePrefPoste(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $prefposte = $em->getRepository('AppBundle:PrefPoste')
                ->find($request->get('id_prefposte'));

        if($prefposte){
            $em->remove($prefposte);
            $em->flush();
        }
    }

        /**
     * @Doc\ApiDoc(
     *     section="PREFPOSTE",
     *     description="Get PREFPOSTE of one benevole",
     *     statusCodes={
     *         200="Returned when prefpostes are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no prefposte is present in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/prefpostes/benevoles/{id_benevole}", name="get_all_prefpostes_benevole")
     */
    public function getPrefPostesByIdBenevole(Request $request)
    {
        $prefpostes = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:PrefPoste')
                ->findBy(array("idBenevole" => $request->get('id_benevole')));
        /* @var $benevole Benevole */

        if (empty($prefpostes)) {
            return new JsonResponse(['message' => "Le bénévole n'a pas encore de préférences de poste !"], Response::HTTP_NOT_FOUND);
        }

        return $prefpostes;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="PREFPOSTE",
     *     input="AppBundle\Form\PrefPosteType",
     *     output="AppBundle\Form\PrefPoste",
     *     description="Delete all prefpostes of a specific BENEVOLE",
     *     statusCodes={
     *         202="Remove all prefpostes successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/prefpostes/benevoles/{id_benevole}", name="delete_all_prefpostes_benevole")
     */
    public function deletePrefPostesByIdBenevole(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $prefpostes = $em->getRepository('AppBundle:PrefPoste')
                    ->findBy(array("idBenevole" => $request->get('id_benevole')));

        if ($prefpostes) {
            foreach ($prefpostes as $prefposte) {
                $em->remove($prefposte);
            }
            $em->flush();
        }
    }

}
