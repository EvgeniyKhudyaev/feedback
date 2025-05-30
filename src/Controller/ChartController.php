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

        // Найдём все поля с типами: RATING, SELECT, RADIO
        $fields = $fieldRepo->createQueryBuilder('f')
            ->where('f.type IN (:types)')
            ->setParameter('types', [
                FeedbackFieldTypeEnum::RATING,
                FeedbackFieldTypeEnum::SELECT,
                FeedbackFieldTypeEnum::RADIO
            ])
            ->getQuery()
            ->getResult();

        $charts = [];

        /** @var FeedbackField $field */
        foreach ($fields as $field) {
            $answers = $answerRepo->findBy(['field' => $field]);

            $optionLabels = [];
            $counts = [];

            // Если у поля есть опции, считаем по ним
//            if ($field->getOptions()->count() > 0) {
//                foreach ($field->getOptions() as $option) {
//                    $label = $option->getLabel();
//                    $optionLabels[$option->getId()] = $label;
//                    $counts[$label] = 0;
//                }
//
//                foreach ($answers as $answer) {
//                    $selectedOptionId = $answer->getValue();
//                    $label = $optionLabels[$selectedOptionId] ?? 'Неизвестно';
//                    $counts[$label] = ($counts[$label] ?? 0) + 1;
//                }
//
//                $charts[] = [
//                    'question' => $field->getLabel(),
//                    'labels' => array_keys($counts),
//                    'data' => array_values($counts),
//                ];
//            }

            // Если это поле RATING — считаем по числам от 1 до 5
            if ($field->getType() === FeedbackFieldTypeEnum::RATING) {
                $ratingCounts = array_fill(1, 5, 0);
                foreach ($answers as $answer) {
                    $value = (int)$answer->getValue();
                    if (isset($ratingCounts[$value])) {
                        $ratingCounts[$value]++;
                    }
                }

                $charts[] = [
                    'question' => $field->getLabel(),
                    'labels' => ['1 ⭐️', '2 ⭐️⭐️', '3 ⭐️⭐️⭐️', '4 ⭐️⭐️⭐️⭐️', '5 ⭐️⭐️⭐️⭐️⭐️'],
                    'data' => array_values($ratingCounts),
                ];
            }
        }

        $charts[] = [
            'question' => 'Была ли консультация решением вашей проблемы?',
            'labels' => ['Да', 'Частично', 'Нет'],
            'data' => [1, 2, 0], // например, 10 "Да", 3 "Частично", 3 "Нет"
        ];

        // Вопрос 2: "Насколько удобно работать с приложением?"
        $charts[] = [
            'question' => 'Как вы оцениваете удобство использования онлайн-консультации?',
            'labels' => ['Очень неудобно', 'Неудобно', 'Нормально', 'Удобно', 'Очень удобно'],
            'data' => [1, 0, 1, 1, 0], // примеры подсчётов ответов
        ];

        return $this->render('chart/charts.html.twig', [
            'charts' => $charts,
            'feedback' => $feedback,
            'questionText' => 'test'
        ]);
    }


//    #[Route('/feedback/{id}/charts', name: 'feedback_charts')]
//    public function charts(Feedback $feedback): Response
//    {
//        $chartData = [];
//
//        /** @var FeedbackField $field */
//        foreach ($feedback->getFields() as $field) {
//            if (!in_array($field->getType(), [
//                FeedbackFieldTypeEnum::SELECT,
//                FeedbackFieldTypeEnum::RADIO,
//                FeedbackFieldTypeEnum::MULTISELECT,
//                FeedbackFieldTypeEnum::RATING,
//            ])) {
//                continue;
//            }
//
//            $answers = $this->em->getRepository(FeedbackFieldAnswer::class)
//                ->findBy(['field' => $field]);
//
//            // Собираем статистику
//            $counts = [];
//
//            foreach ($answers as $answer) {
//                $value = $answer->getValue();
//
//                if ($field->getType() === FeedbackFieldTypeEnum::MULTISELECT) {
//                    $values = explode(',', $value);
//                } else {
//                    $values = [$value];
//                }
//
//                foreach ($values as $v) {
//                    $label = $this->getOptionLabelById($field, $v);
//                    if (!$label) {
//                        $label = 'N/A';
//                    }
//
//                    if (!isset($counts[$label])) {
//                        $counts[$label] = 0;
//                    }
//                    $counts[$label]++;
//                }
//            }
//
//            $chartData[] = [
//                'label' => $field->getLabel(),
//                'labels' => array_keys($counts),
//                'data' => array_values($counts),
//            ];
//        }
//
//        return $this->render('chart/charts.html.twig', [
//            'feedback' => $feedback,
//            'chartData' => $chartData,
//        ]);
//    }
//
//// Возвращает label по ID опции
//    private function getOptionLabelById(FeedbackField $field, $id): ?string
//    {
//        foreach ($field->getOptions() as $option) {
//            if ((string) $option->getId() === (string) $id) {
//                return $option->getLabel();
//            }
//        }
//        return null;
//    }


    //    #[Route('/example', name: 'chart_example')]
//    public function index(): Response
//    {
//        return $this->render('chart/chart.html.twig');
//    }

//    #[Route('/index', name: 'chart_index')]
//    public function index(): Response
//    {
//        $feedbackForms = $this->feedbackRepository->findAll();
//
//        return $this->render('chart/index.html.twig', [
//            'feedbackForms' => $feedbackForms,
//        ]);
//    }
}