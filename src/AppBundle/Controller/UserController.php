<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Nelmio\ApiDocBundle\Annotation as Doc;

class UserController extends Controller
{
    /**
     * @Doc\ApiDoc(
     *     section="USER",
     *     description="Get all users",
     *     statusCodes={
     *         200="Returned when users are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no users are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/users", name="get_all_users")
     */
    public function getUsers(Request $request)
    {
        $users = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->findAll();
        
        if(empty($users)){
            return new JsonResponse(["message" => "Aucun utilisateur présent dans la BDD !"], Response::HTTP_NOT_FOUND);
        }
        
        return $users;
    }

    /**
     * @Doc\ApiDoc(
     *     section="USER",
     *     input="AppBundle\Form\UserType",
     *     output="AppBundle\Form\User",
     *     description="Create new user",
     *     statusCodes={
     *         202="User created successfully",
     *         400="Bad request",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/users", name="post_all_users")
     */
    public function postUsers(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['validation_groups'=>['Default', 'New']]);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $encoder = $this->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoded);

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($user);
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="USER",
     *     description="Delete all users",
     *     statusCodes={
     *         202="All users have been removed",
     *         401="Unauthorized, you need to use auth-token",
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/users", name="delete_all_users")
     */
    public function deleteUsers(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $users = $em->getRepository('AppBundle:User')->findAll();

        if($users) {
            foreach ($users as $user) {
                $em->remove($user);
            }
    
            $em->flush();
        }
    }


    /**
     * @Doc\ApiDoc(
     *     section="USER",
     *     description="Get one user",
     *     statusCodes={
     *         200="Returned when users are found",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no users are presents in the database"
     *     }
     * )
     * @Rest\View()
     * @Rest\Get("/api/users/{id_user}", name="get_users_one")
     */
    public function getOneUser(Request $request)
    {
        $em =  $this->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')
                ->find($request->get('id_user'));
        /* @var $user User */

        if(empty($user)){
            return new JsonResponse(["message" => "Utilisateur non trouvé !"], Response::HTTP_NOT_FOUND);
        }
        
        return $user;
    }


    /**
     * @Doc\ApiDoc(
     *     section="USER",
     *     input="AppBundle\Form\UserType",
     *     output="AppBundle\Form\User",
     *     description="Update one user",
     *     statusCodes={
     *         200="Returned when user have been modified",
     *         401="Unauthorized, you need to use auth-token",
     *         404="Returned when no users are presents in the database"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/api/users/{id_user}", name="post_users_one")
     */
    public function updateOneUser(Request $request)
    {
        $user = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:User')
                ->find($request->get('id_user'));
        /* @var $user User */

        if (empty($user)) {
            return \FOS\RestBundle\View\View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(UserType::class, $user);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            // Si l'utilisateur veut changer son mot de passe
            if (!empty($user->getPlainPassword())) {
                $encoder = $this->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($encoded);
            }
            $em = $this->get('doctrine.orm.entity_manager');
            $em->merge($user);
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }

    /**
     * @Doc\ApiDoc(
     *     section="USER",
     *     description="Delete one user",
     *     statusCodes={
     *         202="Returned when user is found",
     *         401="Unauthorized, you need to use auth-token"
     *     }
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/api/users/{id_user}", name="delete_users_one")
     */
    public function deleteOneUser(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('AppBundle:User')
                ->find($request->get('id_user'));

        if($user){
            $em->remove($user);
            $em->flush();
        }
    }
}
