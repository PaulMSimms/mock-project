<?php
namespace App\Controller;

use App\Entity\Blogs;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{

    /**
     * @Route("/blogs", name="blog_home")
     */
    public function blog()
    {
        $blogs = ['blog 1', 'blog 2'];
        return $this->render("blog/index.html.twig", ['blogs' => $blogs]);
    }

    /**
     * @Route("/blogs/save", name="blog_save")
     */
    public function save()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $blog = new Blogs();
        $blog->setTitle("blog 1");
        $blog->setBody("Body for blog 1");

        $entityManager->persist($blog);
        $entityManager->flush();

        return new Response('blog saved with ID of ' . $blog->getId());
    }
}
