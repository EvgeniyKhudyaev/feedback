<?php

namespace App\Controller;

use App\Entity\Feedback\Feedback;
use App\Entity\Feedback\FeedbackField;
use App\Entity\Feedback\FeedbackFieldAnswer;
use App\Entity\Sync\ClientUser;
use App\Enum\Feedback\FeedbackFieldTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/feedbacks')]
class FeedbackController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    #[Route('/{feedback}/answer/{clientUser}/{token}', name: 'feedback_answer', methods: ['GET', 'POST'])]
    public function answer(Feedback $feedback, ClientUser $clientUser, string $token, Request $request): RedirectResponse|Response
    {
        $expectedToken = substr(md5($clientUser->getUuid()), 0, 6);

        if ($token !== $expectedToken) {
            throw $this->createAccessDeniedException('Неверный токен доступа');
        }

//        $existingAnswer = $this->em->createQueryBuilder()
//            ->select('a')
//            ->from(FeedbackFieldAnswer::class, 'a')
//            ->join('a.field', 'f')
//            ->where('f.feedback = :feedback')
//            ->andWhere('a.responder = :responder')
//            ->setParameter('feedback', $feedback)
//            ->setParameter('responder', $clientUser)
//            ->setMaxResults(1)
//            ->getQuery()
//            ->getOneOrNullResult();
//
//        if ($existingAnswer !== null) {
//            throw $this->createAccessDeniedException('Вы уже проходили этот опрос');
//        }

        $formBuilder = $this->createFormBuilder();

        foreach ($feedback->getFields() as $field) {
            $fieldName = 'field_' . $field->getId();

            switch ($field->getType()) {
                case FeedbackFieldTypeEnum::INPUT:
                    $formBuilder->add($fieldName, TextType::class, [
                        'label' => $field->getLabel(),
                        'required' => true,
                    ]);
                    break;

                case FeedbackFieldTypeEnum::TEXTAREA:
                    $formBuilder->add($fieldName, TextareaType::class, [
                        'label' => $field->getLabel(),
                        'required' => true,
                    ]);
                    break;

                case FeedbackFieldTypeEnum::CHECKBOX:
                    $formBuilder->add($fieldName, CheckboxType::class, [
                        'label' => $field->getLabel(),
                        'required' => false,
                    ]);
                    break;

                case FeedbackFieldTypeEnum::SELECT:
                    $formBuilder->add($fieldName, ChoiceType::class, [
                        'label' => $field->getLabel(),
                        'required' => true,
                        'choices' => array_combine(
                            array_map(fn($opt) => $opt->getLabel(), $field->getOptions()->toArray()),
                            array_map(fn($opt) => $opt->getValue(), $field->getOptions()->toArray())
                        ),
                        'placeholder' => 'Выберите вариант',
                    ]);
                    break;

                case FeedbackFieldTypeEnum::RADIO:
                    $formBuilder->add($fieldName, ChoiceType::class, [
                        'label' => $field->getLabel(),
                        'required' => true,
                        'choices' => array_combine(
                            array_map(fn($opt) => $opt->getLabel(), $field->getOptions()->toArray()),
                            array_map(fn($opt) => $opt->getId(), $field->getOptions()->toArray())
                        ),
                        'expanded' => true,
                        'multiple' => false,
                    ]);
                    break;

                case FeedbackFieldTypeEnum::MULTISELECT:
                    $formBuilder->add($fieldName, ChoiceType::class, [
                        'label' => $field->getLabel(),
                        'required' => true,
                        'choices' => array_combine(
                            array_map(fn($opt) => $opt->getLabel(), $field->getOptions()->toArray()),
                            array_map(fn($opt) => $opt->getId(), $field->getOptions()->toArray())
                        ),
                        'expanded' => true,
                        'multiple' => true,
                    ]);
                    break;

                case FeedbackFieldTypeEnum::RATING:
                    $formBuilder->add($fieldName, ChoiceType::class, [
                        'label' => $field->getLabel(),
                        'required' => true,
                        'choices' => [
                            '1 ⭐️' => 1,
                            '2 ⭐️⭐️' => 2,
                            '3 ⭐️⭐️⭐️' => 3,
                            '4 ⭐️⭐️⭐️⭐️' => 4,
                            '5 ⭐️⭐️⭐️⭐️⭐️' => 5,
                        ],
                        'expanded' => true,
                        'multiple' => false,
                    ]);
                    break;
            }
        }
        
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

//            dd($data);
            foreach ($data as $fieldName => $answer) {
                $fieldId = (int) str_replace('field_', '', $fieldName);
                $field = $feedback->getFields()->filter(fn($f) => $f->getId() === $fieldId)->first();

                $answerEntity = new FeedbackFieldAnswer();
                $answerEntity->setField($field);
                $answerEntity->setResponder($clientUser);

                if (is_array($answer)) {
                    $answerEntity->setValue(implode(',', $answer));
                } else {
                    $answerEntity->setValue($answer);
                }

                $this->em->persist($answerEntity);
            }

            $this->em->flush();

            return $this->redirectToRoute('feedback_thanks');
        }

        return $this->render('feedback/answer.html.twig', [
            'form' => $form->createView(),
            'feedback' => $feedback,
        ]);
    }

    #[Route('/thanks', name: 'feedback_thanks')]
    public function thanks(): Response
    {
        return $this->render('feedback/thanks.html.twig');
    }
}