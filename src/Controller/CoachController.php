<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Form\CoachType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CoachController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_coach')]
    public function index(): Response
    {
        return $this->render('coach/index.html.twig', [
            'controller_name' => 'Ismail',
        ]);
    }

    #[Route('/coach', name: 'add_coach')]
    public function addCoach(Request $request, SessionInterface $session): Response
    {
        $coach = new Coach();
        $form = $this->createForm(CoachType::class, $coach);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
            $existingCoach = $this->em->getRepository(Coach::class)->findOneBy(['cin' => $coach->getCin()]);

            if ($existingCoach) {
              
                $session->getFlashBag()->add('error', 'CIN already exists');
                return $this->render('coach/coach.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

          
            $this->em->persist($coach);
            $this->em->flush();

           
            $session->getFlashBag()->add('success', 'Saved successfully');

            return $this->redirectToRoute('app_coach');
        }

        return $this->render('coach/coach.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete-coach', name: 'delete_coach')]
    public function deleteCoach(Request $request, SessionInterface $session): Response
    {
        $cin = $request->get('cin'); 
        $coach = $this->em->getRepository(Coach::class)->findOneBy(['cin' => $cin]);

        if ($coach) {
           
            $this->em->remove($coach);
            $this->em->flush();

           
            $session->getFlashBag()->add('success', 'Successfully deleted');
        } else {
            
            $session->getFlashBag()->add('error', 'CIN doesn\'t exist');
        }

        return $this->redirectToRoute('app_coach');
    }
}
