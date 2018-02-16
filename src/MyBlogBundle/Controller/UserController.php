<?php

namespace MyBlogBundle\Controller;

use MyBlogBundle\Entity\Role;
use MyBlogBundle\Entity\User;
use MyBlogBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        if($this->getUser() != null){
            return $this->redirectToRoute("users_profile");
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $roleRepository = $this->getDoctrine()->getRepository(Role::class);
            $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
            $user->setRoles([$userRole]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute("articles_all");
        }
        return $this->render('users/Register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/login", name="user_login")
     * @param AuthenticationUtils $utils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(AuthenticationUtils $utils)
    {
        if($this->getUser() != null){
            return $this->redirectToRoute("users_profile");
        }
        return $this->render("users/Login.html.twig", ['error' => $utils->getLastAuthenticationError()]);
    }

    /**
     * @Route("/logout", name="user_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/MyProfile", name="users_profile")
     */
    public function userProfileView()
    {
        if ($user = $this->getUser()){
            return $this->render("users/UsersProfile.html.twig", ['user' => $user]);
        }
        else{
            return $this->redirectToRoute("user_login");
        }
    }
}
