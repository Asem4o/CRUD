<?php

namespace App\Controller;

use App\Entity\Narqd;
use App\Entity\Note;
use App\Entity\User;
use App\Form\Type\NoteType;
use App\Repository\NarqdRepository;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class NoteController extends AbstractController
{
    /**
     * @required
     */
    public NoteRepository $noteRepository;

    /**
     * @required
     */
    public EntityManagerInterface $entityManager;
    /**
     * @required
     */
    public UserRepository $userRepository;

    public function create(Request $request)
    {

        $form = $this->createForm(NoteType::class, new Note());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $note = $form->getData();
            $note->setUser($this->userRepository->find($request->getSession()->get('userId')));
            $this->entityManager->persist($note);
            $this->entityManager->flush();
            return $this->redirectToRoute('profile');
        }
        return $this->render('notes/create.html.twig',
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
            $this->addFlash("error", "You don't have permission to delete this Note");
        }

        return $this->redirectToRoute('profile');
    }

    public function update(Request $request, $id): RedirectResponse|Response
    {
        $note = $this->noteRepository->find($id);

        if (!$note) {
            return $this->redirectToRoute('profile');
        }

        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        $loggedInUserId = $request->getSession()->get('userId');

        // Assuming $note->getUser() returns the user who created the note
        if ($note->getUser()->getId() !== $loggedInUserId) {

            return $this->redirectToRoute('profile');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $note = $form->getData();
            $user = $this->userRepository->find($loggedInUserId); // Fetch the user from the session
            $note->setIsCompleted(false);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->redirectToRoute('profile');
        }

        return $this->render('notes/update.html.twig',
            ['form' => $form->createView()]
        );
    }

    public function updateStatus(int $id)
    {
        $note = $this->noteRepository->find($id);
        $note->setIsCompleted(!$note->isCompleted());
        $this->entityManager->persist($note);
        $this->entityManager->flush();
        return $this->redirectToRoute('profile');
    }
    private function handleNarqds($form, $userId)
    {
        /**
         * @var Note $note
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
    public function getNoteRepository(): NoteRepository
    {
        return $this->noteRepository;
    }
}