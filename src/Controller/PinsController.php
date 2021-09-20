<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PinsController extends AbstractController
{
    /**
    * @Route("/pins", methods="GET", name="app_pins")
    */
    public function pins(PinRepository $repo): Response
    { 
        return $this->render('pins/pins.html.twig', ['pins' => $repo->findAll()] );                      
    }

    /**
    * @Route("/pins/create", methods={"GET", "PATCH", "POST"}, name="app_create-pin")    
    */
        public function create(Request $request, EntityManagerInterface $em): Response
        {                       
            $pin = new Pin;

            $form = $this->createFormBuilder($pin) 
            // on peut passer un objet à cette methode : dans ce cas, les noms 
            // des champs du formulaire (ici 'title' et 'description')
            // doivent correspondre au attributs de l'objet.
            // aussi dans ce cas, $form va contenir cette fois un Objet de type 'Pin' et non plus un Objet de type 'form'                                                      
                ->add
                ('title', null, 
                [
                    'required'=>false, //on ne peut pas mettre "required" dans le tableau des attributs ci-dessous 
                    'attr'=>['class'=>'cool','autofocus'=>true] 
                ]                
                ) // click droit : import class
                
                ->add('description',null, ['attr'=>['rows'=>10,'cols'=>30] ]) // click droit : import class
                // ->add('submit',SubmitType::class, ['label'=>'create Pin']) // y'a une methode meilleure pour créer un bouton submit   
                                                  //ici aussi, click droit : import class
                ->getForm()                
            ;

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid() ) 
            {                
                $em->persist($pin);
                $em->flush();   

                return $this->redirectToRoute('app_pins_show', ['id'=>$pin->getId()]);
            }

            return $this->render('pins/create.html.twig', ['monForm'=>$form->createView()]);                                  
        }


         /**
         * @Route("/pins/{id<\d+>}", name="app_pins_show")
         */
        public function show(Pin $pin):Response
        {
            return $this->render('pins/show.html.twig', compact('pin') );
        }

}    