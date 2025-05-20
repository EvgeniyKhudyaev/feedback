<?php

namespace App\Controller;

use App\Entity\User\User;
use App\Form\User\UserEditType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profile')]
final class ProfileController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
    )
    {
    }

    #[Route('/', name: 'profile_edit')]
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->userRepository->save($user);

                $this->addFlash('success', 'Профиль успешно обновлён.');

                return $this->redirectToRoute('profile_edit');
            }

            $this->addFlash('danger', 'Форма содержит ошибки. Пожалуйста, проверьте введённые данные.');
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}