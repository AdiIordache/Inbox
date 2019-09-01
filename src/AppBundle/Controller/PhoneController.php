<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Phone;
use AppBundle\Form\PhoneType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PhoneController extends Controller
{
    /**
     * @Route("{id}/addPhone", name="addPhone")
     */
    public function addNewPhone(Request $request, $id){

        $phone = new Phone();
        $form = $this->createForm(PhoneType::class, $phone);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AppBundle:Person')->find($id);

        if($form->isSubmitted() && $form->isValid()){
            $phone = $form->getData();
            $phone->setPerson($person);
            $em->persist($phone);
            $em->flush();
//            return new Response('Email is successfully added');
            return $this->redirect('/'.$id);
        }
        return $this->render('@App/Person/add_phone.html.twig', [
            'form_phone' => $form->createView(),
            'person' => $person
        ]);
    }

    /**
     * @Route("{personId}/{phoneId}/deletePhone", name="deletePhone")
     */
    public function deletePhone($personId, $phoneId){

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Person');
        $person = $repo->find($personId);
        $phones = $em->getRepository('AppBundle:Phone')->findBy(array('person'=>$person, 'id' =>$phoneId ));
        foreach($phones as $phone){
            $phoneId = $phone->getId();
        }
        $em->remove($phone);
        $em->flush();
//        return new Response('Phone number is now deleted '.$phoneId);
        return $this->redirect('/' . $personId);
    }
}
