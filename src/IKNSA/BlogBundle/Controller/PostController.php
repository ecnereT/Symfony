<?php

namespace IKNSA\BlogBundle\Controller;

use IKNSA\BlogBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use IKNSA\BlogBundle\Entity\Comment;
use IKNSA\BlogBundle\Form\PostType;

/**
 * Post controller.
 *
 */
class PostController extends Controller
{
    /**
     * Lists all post entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('IKNSABlogBundle:Post')->getLastInserted('IKNSABlogBundle:Post', 3);

        return $this->render('IKNSABlogBundle:Post:index.html.twig', array(
            'posts' => $posts,
        ));
    }
    

    /**
     * Creates a new post entity.
     *
     */
    public function newAction(Request $request)
    {
        if(!$this->getUser())
        {
            $this->addFlash('notice', 'Connecte toi avant!');
            return $this->redirectToRoute('post_index');
        }
    
        $post = new Post();
        $form = $this->createForm('IKNSA\BlogBundle\Form\PostType', $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $post->setUser($this->getUser());
            $em->persist($post);
            $em->flush($post);

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('IKNSABlogBundle:Post:new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a post entity.
     *
     */
    public function showAction(Post $post, Request $request)
    {
         $em = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $comment->setPost($post);
        $form = $this->createForm('IKNSA\BlogBundle\Form\CommentType', $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $em->persist($comment);
            $em->flush();
            return $this->redirect($request->headers->get('referer'));
        }
        $comments = $em->getRepository('IKNSABlogBundle:Comment')->findByPost($post);
        $deleteForm = $this->createDeleteForm($post);
        return $this->render('IKNSABlogBundle:post:show.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
            'comments' => $comments,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing post entity.
     *
     */
    public function editAction(Request $request, Post $post)
    {
        if(!$this->getUser())
        {
            $this->addFlash('notice', 'Connecte toi avant!');
            return $this->redirectToRoute('post_index');
        }
        
        $deleteForm = $this->createDeleteForm($post);
        $editForm = $this->createForm('IKNSA\BlogBundle\Form\PostType', $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_edit', array('id' => $post->getId()));
        }

        return $this->render('IKNSABlogBundle:Post:edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a post entity.
     *
     */
    public function deleteAction(Request $request, Post $post)
    {
        if(!$this->getUser())
        {
            $this->addFlash('notice', 'Connecte toi avant!');
            return $this->redirectToRoute('post_index');
        }
        
        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush($post);
        }

        return $this->redirectToRoute('post_index');
    }

    /**
     * Creates a form to delete a post entity.
     *
     * @param Post $post The post entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
