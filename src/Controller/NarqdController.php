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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


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

    public function delete(Request $request, $id): RedirectResponse
    {
        $note = $this->noteRepository->find($id);

        if (!$note) {
            $this->addFlash("error", "Narqd not found");
            return $this->redirectToRoute('profile');
        }

        // Получаване на идентификатора на текущия потребител от сесията
        $loggedInUserId = $request->getSession()->get('userId');

        // Проверка дали записът съществува и дали принадлежи на текущия потребител
        if ($note->getUser() && $note->getUser()->getId() === $loggedInUserId) {
            // Изтриване и записване на промените в базата данни
            $this->entityManager->remove($note);
            $this->entityManager->flush();

            $this->addFlash("success", "Narqd Deleted");
        } else {
            $this->addFlash("error", "You don't have permission to delete this Narqd");
        }

        return $this->redirectToRoute('profile');
    }

    public function update(Request $request, $id): RedirectResponse|Response
    {
        $note = $this->noteRepository->find($id);

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