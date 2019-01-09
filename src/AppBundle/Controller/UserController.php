<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Users;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserController extends Controller
{
    /**
     * @Route("/users", name="users_list")
     */
    public function indexAction(Request $request)
    {

        $users = $this->getDoctrine()->getRepository('AppBundle:Users')->findAll();

        // Render Template
        return $this->render('user/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function createAction(Request $request)
    {
        $user = new Users;

        $form = $this->createFormBuilder($user)
        ->add('name', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('userGroup', EntityType::class, array('class' => 'AppBundle:Groups', 'choice_label' => 'name', 'attr' => array('class' => 'form-control')))
        ->add('age', IntegerType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:20px;')))
        ->add('save', SubmitType::class, array('label' => 'Add User', 'attr' => array('class' => 'btn btn-success')))
        ->getForm();

        // Handle Request
        $form->handleRequest($request);

        // Submitting
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getData();
            $age = $form['age']->getData();
            $group = $form['userGroup']->getData();
            // Current Date And Time
            $now = new \DateTime('now');
            // Setting Data
            $user->setName($name);
            $user->setUserGroup($group);
            $user->setAge($age);
            $user->setCreatedDate($now);
            // Inserting Data
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            // Flash Message
            $this->addFlash('notice', 'User Saved');
            // Redirect To Users Page
            return $this->redirectToRoute('users_list');

        }
        
        // Render Template
        return $this->render('user/create.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/users/edit/{id}", name="user_edit")
     */
    public function editAction($id, Request $request)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:Users')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('No user found for id ' . $id);
        }

        $user->setName($user->getName());
        $user->setUserGroup($user->getUserGroup());
        $user->setAge($user->getAge());

        $form = $this->createFormBuilder($user)
        ->add('name', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('userGroup', EntityType::class, array('class' => 'AppBundle:Groups','choice_label' => 'name', 'attr' => array('class' => 'form-control')))
        ->add('age', IntegerType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px;')))
        ->add('save', SubmitType::class, array('label' => 'Edit User', 'attr' => array('class' => 'btn btn-success')))
        ->getForm();

        // Handle Request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getData();
            $group = $form['userGroup']->getData();
            $age = $form['age']->getData();

            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository('AppBundle:Users')->find($id);
            $user->setName($name);
            $user->setUserGroup($group);
            $user->setAge($age);

            $em->flush();

            $this->addFlash('notice', 'User Edited');

            return $this->redirectToRoute('users_list');

        }


        return $this->render('user/edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/users/delete/{id}", name="user_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:Users')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('No user found for id ' . $id);
        }

        $em->remove($user);
        $em->flush();
        $this->addFlash('notice', 'User Deleted');

        return $this->redirectToRoute('users_list');
    }
}
