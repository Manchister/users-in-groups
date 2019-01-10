
<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Groups;
use AppBundle\Entity\Users;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class GroupController extends Controller
{
    /**
     * @Route("/groups", name="groups_list")
     */
    public function indexAction(Request $request)
    {
        $groups = $this->getDoctrine()->getRepository('AppBundle:Groups')->findAll();

        // Render Template
        return $this->render('group/index.html.twig', array(
            'groups' => $groups,
        ));
    }

    /**
     * @Route("/groups/create", name="group_create")
     */
    public function createAction(Request $request)
    {
        $group = new Groups;

        $form = $this->createFormBuilder($group)
        ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px;')))
        ->add('save', SubmitType::class, array('label' => 'Create Group', 'attr' => array('class' => 'btn btn-success')))
        ->getForm();

        // Handle Request
        $form->handleRequest($request);

        // Check Submit
        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getData();
            // Get Current Date And Time
            $now = new \DateTime('now');
            // Set Variables
            $group->setName($name);
            $group->setCreateDate($now);
            // Inserting Data
            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();
            // Flash Message
            $this->addFlash('notice', 'Group Saved');
            // Redirect To Groups Page
            return $this->redirectToRoute('groups_list');
        }
        // replace this example code with whatever you need
        return $this->render('group/create.html.twig', array(
            'form' => $form->createView()));
    }

    /**
     * @Route("/groups/edit/{id}", name="group_edit")
     */
    public function editAction($id, Request $request)
    {

        $group = $this->getDoctrine()->getRepository('AppBundle:Groups')->find($id);

        if (!$group) {
            throw $this->createNotFoundException('No group found for id ' . $id);
        }

        $group->setName($group->getName());

        $form = $this->createFormBuilder($group)
        ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px;')))
        ->add('save', SubmitType::class, array('label' => 'Edit Group', 'attr' => array('class' => 'btn btn-success')))
        ->getForm();

        // Handle Request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form['name']->getData();

            $em = $this->getDoctrine()->getManager();

            $group = $em->getRepository('AppBundle:Groups')->find($id);
            $group->setName($name);

            $em->flush();

            $this->addFlash('notice', 'User Group');

            return $this->redirectToRoute('groups_list');

        }


        return $this->render('group/edit.html.twig', array(
            'form' => $form->createView()
        ));

    }



    /**
     * @Route("/groups/delete/{id}", name="group_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('AppBundle:Groups')->find($id);
        $users = $this->getDoctrine()->getRepository('AppBundle:Users')->findBy(
            ['userGroup' => $id]
        );

        if (!$group) {
            throw $this->createNotFoundException('No group found for id ' . $id);
        }
        // Check if group includes users
        if ($users) {
            $this->addFlash('error', 'Group includes users, can not delete');
            return $this->redirectToRoute('groups_list');
        } else {
            $em->remove($group);
            $em->flush();
            $this->addFlash('notice', 'Group Deleted');
            return $this->redirectToRoute('groups_list');
        }

        

        
    }



    /**
     * @Route("/groups/{id}/", name="group_users")
     */
    public function showUsersAction($id, Request $request)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:Users')->findBy(
            ['userGroup' => $id]
        );
        // $group = $this->getDoctrine()->getRepository('AppBundle:Groups')->find($id);

        // Render Template
        return $this->render('group/users.html.twig', array(
            'users' => $users,
        ));
    }
}


    /**
     * @Route("/groups/{id}/", name="group_users")
     */
    public function showUsersAction($id, Request $request)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:Users')->findBy(
            ['userGroup' => $id]
        );
        // $group = $this->getDoctrine()->getRepository('AppBundle:Groups')->find($id);

        // Render Template
        return $this->render('group/users.html.twig', array(
            'users' => $users,
        ));
    }
}
