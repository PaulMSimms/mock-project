<?php
// src/Controller/BlogController.php

namespace App\Controller;

use App\Entity\Blogs;
use App\Entity\Comment;
use App\Form\Type\BlogType;
use App\Form\Type\CommentType;
use App\Repository\BlogsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/blogs")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/", name="blog_home", methods={"GET"})
     */
    public function index(BlogsRepository $blogRepository): Response
    {
        // Show all blogs to everyone, but limit actions to authenticated users
        $blogs = $blogRepository->findBy([], ['id' => 'DESC']);

        return $this->render('blog/index.html.twig', [
            'blogs' => $blogs,
        ]);
    }

    /**
     * @Route("/new", name="blog_save", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request): Response
    {
        $blog = new Blogs();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the current user as author if no author specified
            if (!$blog->getAuthor()) {
                $blog->setAuthor($this->getUser()->getFullName());
            }

            // Set created date
            $blog->setCreatedAt(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($blog);
            $entityManager->flush();

            $this->addFlash('success', 'Blog post created successfully!');
            return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
        }

        return $this->render('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="blog_show", methods={"GET","POST"})
     */
    public function show(Request $request, Blogs $blog): Response
    {
        // Get comments for this blog
        $comments = $blog->getComments();

        $comment = new Comment();
        $form = null;

        // Only create comment form for authenticated users
        if ($this->isGranted('ROLE_USER')) {
            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Set the current user as comment author if no author specified
                $comment->setAuthor($this->getUser()->getFullName());
                $comment->setBlog($blog);
                $comment->setCreatedAt(new \DateTime());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $entityManager->flush();

                $this->addFlash('success', 'Comment added successfully!');
                return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
            }
        }

        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
            'comments' => $comments,
            'form' => $form ? $form->createView() : null,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="blog_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Blogs $blog): Response
    {
        // Optional: Only allow editing own posts
         if ($blog->getAuthor() !== $this->getUser()->getFullName()) {
             throw $this->createAccessDeniedException('You can only edit your own posts.');
         }

        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blog->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Blog post updated successfully!');
            return $this->redirectToRoute('blog_show', ['id' => $blog->getId()]);
        }

        return $this->render('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="blog_delete", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, Blogs $blog): Response
    {
        // Optional: Only allow deleting own posts
         if ($blog->getAuthor() !== $this->getUser()->getFullName()) {
            $this->addFlash('error', 'You can only delete your own blog posts.');
            return $this->redirectToRoute('blog_home');
        }

        if ($this->isCsrfTokenValid('form' . $blog->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($blog);
            $entityManager->flush();

            $this->addFlash('success', 'Blog post deleted successfully!');
        } else {
            $this->addFlash('error', 'Invalid security token. Please try again.');
        }

        return $this->redirectToRoute('blog_home');
    }

    /**
     * @Route("/user/my-posts", name="blog_my_posts", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function myPosts(BlogsRepository $blogRepository): Response
    {
        $userBlogs = $blogRepository->findBy([
            'author' => $this->getUser()->getFullName()
        ], ['id' => 'DESC']);

        return $this->render('blog/my_posts.html.twig', [
            'blogs' => $userBlogs,
        ]);
    }
}