<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Person;
use AppBundle\Form\PersonType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Groups;


class PersonController extends Controller
{
    /**
     * @Route("/newPerson", name="create_person")
     */
    public function newPersonAction(Request $request)
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $person = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();
//            return new Response('New person is add in DB!');
            return $this->redirect('/' . $person->getId());
        }
        return $this->render('@App/Person/new_person.html.twig', ['form_person'=>$form->createView()]);

    }

    /**
     * @Route("/{id}/modifyPerson", name="modify")
     */
    public function modifyPersonAction(Request $request, Person $person)
    {
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $person = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();
//            return new Response("Person is successfully modified");
            return $this->redirect('/');
        }

        return $this->render('@App/Person/modify_person.html.twig', [
            'form_person' => $form->createView(),
            'person' => $person
        ]);
    }

    /**
     * @Route("{id}/deletePerson")
     */
    public function deletePersonAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Person');
        $personToRemove = $repo->find($id);
        if(!$personToRemove){
            return new Response('Person with id: ' . $id . ' not exist in database');
        }
        $em->remove($personToRemove);
        $em->flush();

//         return new Response('Person with id: '. $id . ' is deleted');
            return $this->redirect('/');
    }

    /**
     * @Route("{id}")
     */
    public function showPersonById($id){
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Person');
        $person = $repo->find($id);

        //here i show address for person
        $addressRepo = $em->getRepository('AppBundle:Address');
        $addresses = $addressRepo->findBy(array('person' => $person));

        $addressToShow = '<ul>';
        foreach ($addresses as $address){
            $city = 'City: ' .$address->getCity() . '<br>';
            $street = 'Street: ' .$address->getStreet() . '<br>';
            $house = 'House: ' .$address->getHouse() .'<br>';
            $deleteAddress = '<a href=/'.$person->getId().'/'. $address->getId().'/deleteAddress>(Delete this address)</a>';
            $addressToShow .= '<li>'. $city .' - ' . $street .' - ' . $house .$deleteAddress.'</li>';
        }
        $addressToShow .= '</ul>';

        //here i show email for person
        $emailRepo = $em->getRepository('AppBundle:Email');
        $emails = $emailRepo->findBy(array('person' => $person));

        $emailToShow = '<ul>';
        foreach($emails as $email){
            $data = $email->getEmailAddress() . '<br>';
            $emailType = 'Type: ' .$email->getType() . '<br>';
            $deleteEmail = '<a href=/'.$person->getId().'/' . $email->getId().'/deleteEmail>(Delete this email)</a>';
            $emailToShow .='<li>' . $emailType. $data .$deleteEmail.'</li>';
        }
        $emailToShow .= '</ul>';

        //here i show phone number for person
        $phoneRepo = $em->getRepository('AppBundle:Phone');
        $phones = $phoneRepo->findBy(array('person'=> $person));
        $phoneToShow = '<ul>';
        foreach($phones as $phone){
            $number = $phone->getPhoneNumber() . '<br>';
            $phoneType = 'Type: '. $phone->getType() . '<br>';
            $deletePhone = '<a href=/'.$person->getId().'/' . $phone->getId().'/deletePhone>(Delete this phone)</a>';
            $phoneToShow .='<li>' . $phoneType . $number .$deletePhone .'</li>';
        }
        $phoneToShow .= '</ul>';

        $modifyPerson = '<a href=/'. $person->getId() .'/modifyPerson>Edit data</a>';
        return new Response('
       <ul>
            Be careful, if you click here you will delete this person !!<a href="/'.$person->getId().'/deletePerson">Delete</a>!!
           <br>Click here if you want to edit personal data-> '.$modifyPerson.'
            <li>Id: ' . $person->getId() .'</li>
            <li>First Name: '. $person->getFirstName() .'</li>
            <li>Last Name: '. $person->getLastName() .'</li>
            <li>Description: '. $person->getDescription() .'</li>
           <li>Address: '. $addressToShow . '</li><a href="/'.$person->getId().'/addAddress">Add new Address</a>
           <li>Email: ' .$emailToShow. '</li><a href="/'.$person->getId().'/addEmail">Add new Email</a>
           <li>Phone: ' . $phoneToShow.'</li><a href="/' .$person->getId() .'/addPhone">Add new Phone</a>
           <li><a href="/groups">Groups!</a></li>
       </ul>
      
       <a href="/">Click here if you want to see all persons</a>');
    }

    /**
     * @Route("/")
     */
    public function showAllPersons(){
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Person');
        $persoane = $repo->findAll();

        $html = '<ul>';
        foreach ($persoane as $person) {
            $html .= '<li><a href="/'.$person->getId().'">'.$person->getFirstName(). ' ' .$person->getLastName().'</a></li>';
        }
        $html .= '</ul>';
        $addNewPerson = '<br><a href="/newPerson">Create new Person</a>';
        return new Response($html . $addNewPerson);
    }
}
