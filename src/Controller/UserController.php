<?php

namespace App\Controller;

use App\Entity\Narqd;
use App\Entity\User;
use App\Form\Type\UserType;
use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class UserController extends AbstractController
{


    /**
     * @required
     */
    public EntityManagerInterface $entityManager;

    /**
     * @required
     */
    public NoteRepository $noteRepository;
    /**
     * @required
     */
    public UserRepository $userRepository;

    public function register(Request $request): RedirectResponse|Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($this->userRepository->findOneBy(['username' => $user->getUsername()])) {
                $this->addFlash("error", "Username " . $user->getUsername() . " Taken");
                return $this->render('users/register.html.twig', ['form' => $form->createView()]);
            }
            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = password_hash($form->getData()->getPassword(), PASSWORD_BCRYPT);
            $user->setPassword($password);
            // 4) save the User!
            $this->entityManager->persist($user);

            $this->entityManager->flush();

            $this->addFlash("success", "Username " . $user->getUsername() . " Created");
            return $this->redirectToRoute('login');
        }
        return $this->render('users/register.html.twig', ['form' => $form->createView()]);
    }

    public function reset(Request $request): RedirectResponse|Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $foundUser = $this->userRepository->findOneBy(['pin' => $user->getPin(), 'username' => $user->getUsername()]);
            if ($foundUser) {
                $password = password_hash($form->getData()->getPassword(), PASSWORD_BCRYPT);
                $foundUser->setPassword($password);
                $this->entityManager->persist($foundUser);
                $this->entityManager->flush();
                $this->addFlash("success", "password updated");
                return $this->redirectToRoute("login");
            }
            $this->addFlash("error", "wrong pin");
        }

        return $this->render('users/reset.html.twig', ['form' => $form->createView()]);
    }

    public function login(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var User $user
             */
            $user = $this->userRepository->findOneBy(['username' => $form->getData()->getUsername()]);
            if ($user) {
                if (password_verify($form->getData()->getPassword(), $user->getPassword())) {
                    $request->getSession()->set('userId', $user->getId());
                    return $this->redirectToRoute('profile');
                }
                $this->addFlash('error', 'wrong password');
                return $this->render('users/login.html.twig', ['form' => $form->createView()]);
            }
            $this->addFlash('error', 'user not found');
        }

        return $this->render('users/login.html.twig', ['form' => $form->createView()]);

    }

    public function profile(Request $request): Response
    {
        $user = $this->userRepository->find($request->getSession()->get('userId'));
        $sortedMonths = $this->sortMonths($user);
        return $this->render('users/profile.html.twig', ['user' => $user, 'sorted_months' => $sortedMonths]);

    }

    public function logout()
    {
        unset($_SESSION);
        return $this->redirectToRoute('login');
    }

    private function sortMonths(User $user): array
    {
        $monthlySum = [];
        foreach ($user->getNarqds() as $note) {
            /**
             * @var Narqd $note
             */
            // Get the month key in the format 'Y-m' from the createdAt property of the note
            $monthKey = $note->getCreatedAt()->format("Y-m");

            // Get the compensation hours from the note
            $compensationHours = (float)$note->getCompensationHours();

            // Check if the month key already exists in the $monthlySum array
            if (!isset($monthlySum[$monthKey])) {
                // If the month key doesn't exist, add it to the $monthlySum array with the compensation hours
                $monthlySum[$monthKey] = $compensationHours;
            } else {
                // If the month key already exists, add the compensation hours to the existing value
                $monthlySum[$monthKey] += $compensationHours;
            }
        }

        ksort($monthlySum);
        return $monthlySum;
    }
}