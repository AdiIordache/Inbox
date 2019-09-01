<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Person;
use AppBundle\Form\PersonType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;


class ControllerController extends Controller
{

    /**
     * @Route("/new", name="new_user")
     */
    public function newAction()
    {
        $user = new Person();
        $form = $this->createForm(PersonType::class, $user);

        return $this->redirectToRoute('create_user');
    }

    /**
     * @Route("/createUser", name="create_user")
     */
    public function createAction(Request $request)
    {
        $user = new Person();
        $form = $this->createForm(PersonType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $form->getData();
            $em->persist($user);
            $em->flush();
        }
        return $this->render('@App/Controller/new.html.twig', ['personform' => $form->createView()]);
//        return $this->redirectToRoute('show_all');
    }

    /**
     * @Route("/{id}/update/", name="update_person")
     */
    public function updateAction(Request $request, Person $person)
    {
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $person = $form->getData();
            $em->persist($person);
            $em->flush();
            return $this->redirectToRoute('show_all');
        }
        return $this->render('@App/Controller/update.html.twig', [
            'db' => $form->createView(),
            'tweet' => $person
        ]);
    }

    /**
     * @Route("/showAll", name="show_all")
     */
    public function showAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Person');
        $people = $repo->findAll();
        return $this->render('@App/Controller/show_all.html.twig', ['people' => $people]);
    }

    /**
     * @Route("{id}/deletePerson/", name="deletePerson")
     */
    public function deletePerson(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Person');
        $person = $repository->find($id);
        $em->remove($person);
        $em->flush();

        return new Response("Deleted person with id=" . $id . ".");
    }
    /**
     * @Route("/showPerson/{id}", name="show")
     */
//    public function showPostAction(int $id): Response
////    {
////        $em = $this->getDoctrine()->getManager();
////        $repository = $em->getRepository('AppBundle:Person');
////        $person=$repository->find($id);
////        var_dump($person);
////
////        return new Response("You are the one and only".$id.".");
////    }

    public function showPersonById($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Person');
        $person = $repo->find($id);

        //here i show address for person
        $addressRepo = $em->getRepository('AppBundle:Address');
        $addresses = $addressRepo->findBy(array('person' => $person));

        $addressToShow = '<ul>';
        foreach ($addresses as $address) {
            $city = 'City: ' . $address->getCity() . '<br>';
            $street = 'Street: ' . $address->getStreet() . '<br>';
            $house = 'House: ' . $address->getStreet() . '<br>';
            $deleteAddress = '<a href=/' . $person->getId() . '/' . $address->getId() . '/deleteAddress>(Delete this address)</a>';
            $addressToShow .= '<li>' . $city . ' - ' . $street . ' - ' . $house . $deleteAddress . '</li>';
        }
        $addressToShow .= '</ul>';

        //here i show email for person
        $emailRepo = $em->getRepository('AppBundle:Email');
        $emails = $emailRepo->findBy(array('person' => $person));

        $emailToShow = '<ul>';
        foreach ($emails as $email) {
            $data = $email->getEmailAddress() . '<br>';
            $emailType = 'Type: ' . $email->getEmailAddress() . '<br>';
            $deleteEmail = '<a href=/email/' . $email->getId() . '>(Delete this email)</a>';
            $emailToShow .= '<li>' . $emailType . $data . $deleteEmail . '</li>';
        }
        $emailToShow .= '</ul>';
//here i show phone number for person
        $phoneRepo = $em->getRepository('AppBundle:Phone');
        $phones = $phoneRepo->findBy(array('person' => $person));
        $phoneToShow = '<ul>';
        foreach ($phones as $phone) {
            $number = $phone->getPhoneNumber() . '<br>';
            $phoneType = 'Type: ' . $phone->getType() . '<br>';
            $deletePhone = '<a href=/phone/' . $phone->getId() . '>(Delete this phone)</a>';
            $phoneToShow .= '<li>' . $phoneType . $number . $deletePhone . '</li>';
        }
        $phoneToShow .= '</ul>';

        $modifyPerson = '<a href=/' . $person->getId() . '/update>Edit data</a>';
        return new Response('
       <ul>
            Be careful, if you click here you will delete this person !!<a href="/' . $person->getId() . '/deletePerson">Delete</a>!!
           <br>Click here if you want to edit personal data-> ' . $modifyPerson . '
            <li>Id: ' . $person->getId() . '</li>
            <li>First Name: ' . $person->getFirstName() . '</li>
            <li>Last Name: ' . $person->getLastName() . '</li>
            <li>Description: ' . $person->getDescription() . '</li>
           <li>Address: ' . $addressToShow . '</li><a href="/' . $person->getId() . '/addAddress">Add new Address</a>
           <li>Email: ' . $emailToShow . '</li><a href="/' . $person->getId() . '/addEmail">Add new Email</a>
           <li>Phone: ' . $phoneToShow . '</li><a href="/' . $person->getId() . '/addPhone">Add new Phone</a>
       </ul>

       <a href="/">Click here if you want to see all persons</a>');
    }
}

