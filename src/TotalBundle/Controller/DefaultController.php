<?php

namespace TotalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('TotalBundle:Default:index.html.twig');
    }

    /**
     * @Route("/albums")
     */
    public function albumsAction()
    {
        return $this->render('TotalBundle:Default:albums.html.twig');
    }

    /**
     * @Route("/contact")
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm('TotalBundle\Form\MailType');
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();


        if ($form->isSubmitted() && $form->isValid()) {
            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Le mail est envoyé !');

            $message = \Swift_Message::newInstance();
            $message->setSubject("Message provenant de votre site");
            $message->setFrom('monsite@yahoo.fr');
            $message->setTo('leclercjweb@gmail.com');
            $message->setBody(
                $this->renderView(
                    'TotalBundle:Default:reponse.html.twig'
                ),
                'text/html'
            );
            $this->get('mailer')->send($message);
        }
        return $this->render('TotalBundle:Default:contact.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/recherche", name="recherche")
     */
    public function rechercheAction(Request $request)
    {
        $form = $this->createForm('TotalBundle\Form\RechercheType');
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $albums = $em->getRepository('TotalBundle:Album')->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $albums = $em->getRepository('TotalBundle:Album')->findBy(["titre"=>$data['Recherche']]);
            //return $this->redirectToRoute('recherche');
        }
        return $this->render('TotalBundle:Default:recherche.html.twig', [
            'form'=>$form->createView(),
            'albums'=>$albums,
        ]);
    }
}
