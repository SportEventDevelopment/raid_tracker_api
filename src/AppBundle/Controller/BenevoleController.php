<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Form\BenevoleType;
use AppBundle\Entity\Benevole;
use Nelmio\ApiDocBundle\Annotation as Doc;

class BenevoleController extends Controller
{
    /**
     * @Doc\ApiDoc(
     *     section="BENEVOLE",
     *     description="Get all benevoles",
     *     statusCodes={
     *         200="Returned when benevoles are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no benevole are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/benevoles", name="benevoles")
     */
    public function getBenevoles(Request $request)
    {
        $benevoles = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Benevole')
                ->findAll();
        /* @var $benevoles benevoles[] */

        if (empty($benevoles)) {
            return new JsonResponse(['message' => "Aucun benevoles présents dans la BDD !"], Response::HTTP_NOT_FOUND);
        }

        return $benevoles;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="BENEVOLE",
     *     input="AppBundle\Form\BenevoleType",
     *     output="AppBundle\Form\Benevole",
     *     description="Delete all benevoles",
     *     statusCodes={
     *         202="Remove all benevoles successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/benevoles", name="delete_all_benevoles")
     */
    public function deleteBenevoles(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $benevoles = $em->getRepository('AppBundle:Benevole')->findAll();

        if($benevoles) {
            foreach ($benevoles as $benevole) {
                $em->remove($benevole);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="BENEVOLE",
     *     description="Get one benevole",
     *     statusCodes={
     *         200="Returned when benevoles are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no benevoles are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/benevoles/{id_benevole}", name="benevoles_one")
     */
    public function getBenevole(Request $request)
    {
        $benevole = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Benevole')
                ->find($request->get('id_benevole'));
        /* @var $benevole Benevole */

        if (empty($benevole)) {
            return new JsonResponse(['message' => "Le bénévole recherché n'a pas été trouvé !"], Response::HTTP_NOT_FOUND);
        }

        return $benevole;
    }


    /**
     *  @Doc\ApiDoc(
     *     section="BENEVOLE",
     *     description="Delete one benevole",
     *     statusCodes={
     *         202="Returned when benevole is found",
     *         401="Unauthorized, you need to use auth-token"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/benevoles/{id_benevole}", name="delete_benevoles_one")
     */
    public function deleteBenevole(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $benevole = $em->getRepository('AppBundle:Benevole')->find($request->get('id_benevole'));
       
        if ($benevole) {
            $em->remove($benevole);
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="BENEVOLE",
     *     description="Get benevoles of one raid",
     *     statusCodes={
     *         200="Returned when benevoles are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no benevoles are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/benevoles/raids/{id_raid}", name="get_all_benevoles_raid")
     */
    public function getBenevolesByIdRaid(Request $request)
    {
        $benevoles = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Benevole')
                ->findBy(array("idRaid" => $request->get('id_raid')));
        /* @var $benevole Benevole */

        if (empty($benevoles)) {
            return new JsonResponse(['message' => "Le raid ne contient pas encore de bénévoles !"], Response::HTTP_NOT_FOUND);
        }

        return $benevoles;
    }

    /**
     *  @Doc\ApiDoc(
     *     section="BENEVOLE",
     *     input="AppBundle\Form\BenevoleType",
     *     output="AppBundle\Form\Benevole",
     *     description="Delete all benevoles of a specific RAID",
     *     statusCodes={
     *         202="Remove all benevoles successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/benevoles/raids/{id_raid}", name="delete_all_benevoles_raid")
     */
    public function deleteBenevolesByIdRaid(Request $request)
    {   
        $em = $this->get('doctrine.orm.entity_manager');
        $benevoles = $em->getRepository('AppBundle:Benevole')
                    ->findBy(array("idRaid" => $request->get('id_raid')));

        if ($benevoles) {
            foreach ($benevoles as $benevole) {
                $em->remove($benevole);
            }
            $em->flush();
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="BENEVOLE",
     *     description="Get benevole if present in a specific RAID",
     *     statusCodes={
     *         200="Returned when benevole is found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no benevoles are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/benevoles/raids/{id_raid}/users/{id_user}", name="get_raid_if_user_is_benevole")
     */
    public function getBenevolesByIdRaidAndByIdUser(Request $request)
    {
        $benevole = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Benevole')
                ->findOneBy(array(  
                    'idRaid' => $request->get('id_raid'), 
                    'idUser' => $request->get('id_user'))
                );

        if(empty($benevole)){
            return new JsonResponse(["message" => "L'utilisateur n'est pas bénévole du raid"], Response::HTTP_NOT_FOUND);
        }

        return $benevole;
    }


    /**
     * @Doc\ApiDoc(
     *     section="BENEVOLE",
     *     input="AppBundle\Form\BenevoleType",
     *     output="AppBundle\Form\Benevole",
     *     description="Add benevole in a RAID",
     *     statusCodes={
     *         200="Returned when benevole has been added successfully",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no benevoles are presents in the database"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/benevoles/raids/{id_raid}/users/{id_user}", name="post_benevole_one_raid")
     */
    public function postBenevoleByIdRaidAndByIdUser(Request $request)
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

        $benevole = new Benevole();

        $form = $this->createForm(BenevoleType::class, $benevole);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($benevole);
            $em->flush();
            return $benevole;
        } else {
            return $form;
        }
    }

    /**
     *  @Doc\ApiDoc(
     *     section="BENEVOLE",
     *     input="AppBundle\Form\BenevoleType",
     *     output="AppBundle\Form\Benevole",
     *     description="Delete benevole of a specific RAID",
     *     statusCodes={
     *         202="Remove benevole if present successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/benevoles/raids/{id_raid}/users/{id_user}", name="delete_benevole_one_raid")
     */
    public function deleteBenevoleByIdRaidAndByIdUser(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $benevole = $em->getRepository('AppBundle:Benevole')
                    ->findOneBy(array(
                        "idRaid" => $request->get('id_raid'),
                        "idUser" => $request->get('id_user')
                    ));
       
        if ($benevole) {
            $em->remove($benevole);
            $em->flush();
        }
    }
}