<?php

namespace DUP\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DUP\BlogBundle\Entity\Post;
use DUP\BlogBundle\Form\PostType;

/**
 * Post controller.
 *
 */
class PostController extends Controller {

    /**
     * Lists all Post entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('DUPBlogBundle:Post')->findAll();

        return $this->render('DUPBlogBundle:Post:index.html.twig', array(
                    'posts' => $posts,
        ));
    }

    /**
     * Creates a new Post entity.
     *
     */
    public function newAction(Request $request) {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $file = $post->getPoster()->getFile();

            $fileName = $this->get('dup_blog.file_upload')->upload($file);

            $post->getPoster()->setName($fileName);

            $em->persist($post);
            $em->persist($post->getPoster());
            $em->flush();

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('DUPBlogBundle:Post:new.html.twig', array(
                    'post' => $post,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Post entity.
     *
     */
    public function showAction(Post $post) {
        $deleteForm = $this->createDeleteForm($post);

        return $this->render('DUPBlogBundle:Post:show.html.twig', array(
                    'post' => $post,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     */
    public function editAction(Request $request, Post $post) {
        $deleteForm = $this->createDeleteForm($post);
        $editForm = $this->createForm(PostType::class, $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_edit', array('id' => $post->getId()));
        }

        return $this->render('DUPBlogBundle:Post:edit.html.twig', array(
                    'post' => $post,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Post entity.
     *
     */
    public function deleteAction(Request $request, Post $post) {
        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('post_index');
    }

    /**
     * Creates a form to delete a Post entity.
     *
     * @param Post $post The Post entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
