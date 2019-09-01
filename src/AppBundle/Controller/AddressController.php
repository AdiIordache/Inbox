<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Address;
use AppBundle\Form\AddressType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class AddressController extends Controller
{

    /**
     * @Route("{id}/addAddress", name="addAddress")
     */

    public function addNewAddress(Request $request, $id){

        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $person = $em->getRepository('AppBundle:Person')->find($id);

        if($form->isSubmitted() && $form->isValid()){
            $address = $form->getData();
            $address->setPerson($person);
            $em->persist($address);
            $em->flush();
//            return new Response("Address is successfully added");
            return $this->redirect('/'.$id);
        }
        return $this->render('@App/Person/add_address.html.twig', [
            'form_address' => $form->createView(),
            'person' => $person
        ]);
    }
    /**
     * @Route("{personId}/{addressId}/deleteAddress")
     */
    public function deleteAddress($personId, $addressId){

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Person');
        $person = $repo->find($personId);
        $addresses = $em->getRepository('AppBundle:Address')->findBy(array('person'=>$person, 'id' =>$addressId ));
        foreach($addresses as $address){
            $addressId = $address->getId();
        }
        $em->remove($address);
        $em->flush();
        return $this->redirect('/'. $personId);
    }

}
