<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Email;
use AppBundle\Form\EmailType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class EmailController extends Controller
{

    /**
     * @Route("{id}/addEmail", name="addEmail")
     */
    public function addNewEmail(Request $request, $id){

        $email = new Email();
        $form = $this->createForm(EmailType::class, $email);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AppBundle:Person')->find($id);

        if($form->isSubmitted() && $form->isValid()){
            $email = $form->getData();
            $email->setPerson($person);
            $em->persist($email);
            $em->flush();
//            return new Response('Email is successfully added');
            return $this->redirect('/'.$id);
        }
        return $this->render('@App/Person/add_email.html.twig', [
            'form_email' => $form->createView(),
            'person' => $person
        ]);
    }
    /**
     * @Route("{personId}/{emailId}/deleteEmail", name="deleteAddress")
     */
    public function deleteEmail($personId, $emailId){

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Person');
        $person = $repo->find($personId);
        $emails = $em->getRepository('AppBundle:Email')->findBy(array('person'=>$person, 'id' =>$emailId ));
        foreach($emails as $email){
            $emailId = $email->getId();
        }
        $em->remove($email);
        $em->flush();
        return $this->redirect('/' . $personId);
    }
}
