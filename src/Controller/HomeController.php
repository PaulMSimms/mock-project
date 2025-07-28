<?php
// src/Controller/HomeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
/**
* @Route("/", name="home_redirect")
*/
public function index(): Response
{
return $this->redirectToRoute('blog_home');
}
    /**
     * @Route("/contact", name="contact")
     */
public function contact()
 {
        return $this->render('inc/contact.html.twig');
}
}
