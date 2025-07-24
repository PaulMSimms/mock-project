<?php
namespace App\Controller;

use App\Entity\Blogs;
use App\Form\Type\BlogType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="blog_home")
     */
    public function blog(): Response
    {
        $blogs = $this->getDoctrine()->getRepository(Blogs::class)->findAll();
        return $this->render("blog/index.html.twig", ['blogs' => $blogs]);
    }

    /**
     * @Route("/blogs/delete/{id}", name="blog_delete", methods={"DELETE"})
     */
    public function delete(Request $request, $id)
    {
        $blogs = $this->getDoctrine()->getRepository(Blogs::class)->find($id);
        if ($blogs) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($blogs);
            $entityManager->flush();
        }

        return new Response(null, 204); // 204 No Content
    }

    /**
     * @Route("/new", name="blog_save", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $NewBlog = new Blogs();
        $form = $this->createForm(BlogType::class, $NewBlog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($NewBlog);
            $entityManager->flush();

            return $this->redirectToRoute('blog_home');
        }

        return $this->render('blog/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
