<?php

namespace MyBlogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class AdminController
 * @package MyBlogBundle\Controller
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 */
class AdminController extends Controller
{
    /**
     * @Route("/admin")
     * @return null
     */
    public function adminAction()
    {
        $user = $this->getUser()->getUsername();
        return $this->render('admin/AdminPanel.html.twig', ['user' => $user]);
    }
}
