<?php

namespace App\Controller;

use App\Entity\Feedback\Feedback;
use App\Entity\Feedback\FeedbackField;
use App\Entity\Feedback\FeedbackFieldAnswer;
use App\Enum\Feedback\FeedbackFieldTypeEnum;
use App\Repository\FeedbackFieldAnswerRepository;
use App\Repository\FeedbackFieldOptionRepository;
use App\Repository\FeedbackFieldRepository;
use App\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/charts')]
class ChartController extends AbstractController
{
    public function __construct(
        private readonly FeedbackRepository $feedbackRepository,
        private readonly FeedbackFieldRepository $feedbackFieldRepository,
        private readonly FeedbackFieldAnswerRepository $feedbackFieldAnswerRepository,
        private readonly FeedbackFieldOptionRepository $feedbackFieldOptionRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    #[Route('/{feedback}/feedback', name: 'report_chart')]
    public function ratingChart(Feedback $feedback, EntityManagerInterface $em): Response
    {
        $fieldRepo = $em->getRepository(FeedbackField::class);
        $answerRepo = $em->getRepository(FeedbackFieldAnswer::class);

        $fields = $fieldRepo->createQueryBuilder('f')
            ->where('f.feedback = :feedback')
            ->andWhere('f.type IN (:types)')
            ->setParameter('feedback', $feedback)
            ->setParameter('types', [
                FeedbackFieldTypeEnum::RATING,
                FeedbackFieldTypeEnum::SELECT,
                FeedbackFieldTypeEnum::RADIO,
            ])
            ->getQuery()
            ->getResult();

        $chartsRating = [];
        $chartsOther = [];

        /** @var FeedbackField $field */
        foreach ($fields as $field) {
            $answers = $answerRepo->findBy(['field' => $field]);

            if ($field->getType() === FeedbackFieldTypeEnum::RATING) {
                $ratingCounts = array_fill(1, 5, 0);
                foreach ($answers as $answer) {
                    $val = (int)$answer->getValue();
                    if (isset($ratingCounts[$val])) {
                        $ratingCounts[$val]++;
                    }
                }

                $chartsRating[] = [
                    'question' => $field->getLabel(),
                    'labels' => ['1 ⭐️', '2 ⭐️', '3 ⭐️', '4 ⭐️', '5 ⭐️'],
                    'data' => array_values($ratingCounts),
                    'type' => 'pie',
                ];
            } elseif ($field->getType() === FeedbackFieldTypeEnum::SELECT || $field->getType() === FeedbackFieldTypeEnum::RADIO) {
                $optionLabels = [];
                $counts = [];

                foreach ($field->getOptions() as $option) {
                    $label = $option->getLabel();
                    $optionLabels[$option->getValue()] = $label;
                    $counts[$label] = 0;
                }

                foreach ($answers as $answer) {
                    $selected = $answer->getValue();
                    $label = $optionLabels[$selected] ?? 'Неизвестно';
                    $counts[$label] = ($counts[$label] ?? 0) + 1;
                }

                $chartsOther[] = [
                    'id' => $feedback->getId(),
                    'question' => $field->getLabel(),
                    'labels' => array_keys($counts),
                    'code' => $field->getCode(),
                    'data' => array_values($counts),
                    'type' => 'pie',
                ];
            }
        }

        return $this->render('chart/modern.html.twig', [
            'feedback' => $feedback,
            'chartsRating' => $chartsRating,
            'chartsOther' => $chartsOther,
            'lineChart' => $this->feedbackRepository->getCountByDayOfWeekChartData($feedback),
        ]);
    }
}