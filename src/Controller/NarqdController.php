<?php

namespace App\Controller;

use App\Entity\Narqd;
use App\Entity\User;
use App\Form\Type\NarqdType;
use App\Repository\NarqdRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class NarqdController extends AbstractController
{
    /**
     * @required
     */
    public NarqdRepository $noteRepository;

    /**
     * @required
     */
    public EntityManagerInterface $entityManager;
    /**
     * @required
     */
    public UserRepository $userRepository;

    public function create(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(NarqdType::class, new Narqd());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleNarqds($form, $request->getSession()->get('userId'));
            $this->addFlash("success", "Narqd Added");
            return $this->redirectToRoute('profile');
        }
        return $this->render('narqd/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    public function delete($id): RedirectResponse
    {
        $note = $this->noteRepository->find($id);
        if($note->getUser()->getId()!== $note = $this->noteRepository->find($id)){
            return $this->redirectToRoute('profile');
        }
        $this->entityManager->remove($note);
        $this->entityManager->flush();
        return $this->redirectToRoute('profile');
    }

    public function update(Request $request, $id): RedirectResponse|Response
    {
        $note = $this->noteRepository->find($id);

        // Check if the note exists
        if (!$note) {
            return $this->redirectToRoute('profile');
        }
        $loggedInUserId = $request->getSession()->get('userId');

        if ($note->getUser()->getId() !== $loggedInUserId) {
            return $this->redirectToRoute('profile');
        }

        $form = $this->createForm(NarqdType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleNarqds($form, $loggedInUserId);

            $this->addFlash("success", "Narqd Updated");
            return $this->redirectToRoute('profile');
        }

        return $this->render('narqd/update.html.twig',
            ['form' => $form->createView()]
        );
    }



    private function handleNarqds($form, $userId)
    {
        /**
         * @var Narqd $note
         */
        $note = $form->getData();
        /**
         * @var User $user
         */
        $user = $this->userRepository->find($userId);
        $note->setUser($user);
        $note->setIsCompleted(false);

        $this->entityManager->persist($note);
        $this->entityManager->flush();

    }

    /**
     * @return NarqdRepository
     */
    public function getNoteRepository(): NarqdRepository
    {
        return $this->noteRepository;
    }
}