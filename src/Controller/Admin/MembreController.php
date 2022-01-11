<?php

namespace App\Controller\Admin;

use App\Entity\Membre;
use App\Form\MembreType;
use App\Repository\MembreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as Hasher;

#[Route('/admin/membre')]
class MembreController extends AbstractController
{
    #[Route('/{page?}', name: 'admin_membre_index', methods: ['GET'])]
    public function index(MembreRepository $membreRepository, PaginatorInterface $paginator, Request $rq): Response
    {
        $page = $page ?? 1;
        return $this->render('admin/membre/index.html.twig', [
            'membres' => $paginator->paginate($membreRepository->findAll(), $rq->query->get("page", 1), 10)
        ]);
    }

    #[Route('/ajouter', name: 'admin_membre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Hasher $hasher): Response
    {
        $membre = new Membre();
        $form = $this->createForm(MembreType::class, $membre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // -------------------------Gestion du mot de passe--------------------------
            $mdp = $form->get("password")->getData(); // Ou on peut utiliser : $mdp = $request->request->get("password")   // Résultats identiques
            $nouveauMdp = $hasher->hashPassword($membre, $mdp);
            $membre->setPassword($nouveauMdp);
            //---------------------------------------------------------------------------

            $entityManager->persist($membre);
            $entityManager->flush();

            return $this->redirectToRoute('admin_membre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/membre/new.html.twig', [
            'membre' => $membre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_membre_show', methods: ['GET'])]
    public function show(Membre $membre): Response
    {
        return $this->render('admin/membre/show.html.twig', [
            'membre' => $membre,
        ]);
    }

    #[Route('/{id}/modifier', name: 'admin_membre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Membre $membre, EntityManagerInterface $entityManager, Hasher $hasher): Response
    {
        $form = $this->createForm(MembreType::class, $membre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // -------------------------Gestion du mot de passe--------------------------
            $mdp = $form->get("password")->getData(); // Ou on peut utiliser : $mdp = $request->request->get("password")   // Résultats identiques
            if ($mdp) {
                $nouveauMdp = $hasher->hashPassword($membre, $mdp);
                $membre->setPassword($nouveauMdp);
            }
            //---------------------------------------------------------------------------
            $entityManager->flush();

            return $this->redirectToRoute('admin_membre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/membre/edit.html.twig', [
            'membre' => $membre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'admin_membre_delete', methods: ['POST'])]
    public function delete(Request $request, Membre $membre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $membre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($membre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_membre_index', [], Response::HTTP_SEE_OTHER);
    }
}
