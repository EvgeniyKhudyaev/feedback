<?php

namespace App\Controller;

use App\Entity\Feedback\Feedback;
use App\Entity\Feedback\FeedbackField;
use App\Repository\FeedbackFieldAnswerRepository;
use App\Repository\FeedbackFieldOptionRepository;
use App\Repository\FeedbackFieldRepository;
use App\Repository\FeedbackRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reports')]
class ReportController extends AbstractController
{
    public function __construct(
        private readonly FeedbackRepository $feedbackRepository,
        private readonly FeedbackFieldRepository $feedbackFieldRepository,
        private readonly FeedbackFieldAnswerRepository $feedbackFieldAnswerRepository,
        private readonly FeedbackFieldOptionRepository $feedbackFieldOptionRepository
    ) {
    }

    #[Route('/index', name: 'report_index')]
    public function index(): Response
    {
        $feedbackForms = $this->feedbackRepository->findAll();

        return $this->render('report/index.html.twig', [
            'feedbackForms' => $feedbackForms,
        ]);
    }

    #[Route('/{feedback}/view', name: 'report_view')]
    public function view(Feedback $feedback, Request $request): Response
    {
        $fields = $this->feedbackFieldRepository->findBy(['feedback' => $feedback]);
        $answers = $this->feedbackFieldAnswerRepository->findAnswersByFeedback($feedback->getId());

        // Формируем таблицу: строки — ответы пользователей, колонки — поля формы

        // Можно передать всё в Twig, чтобы вывести таблицу

        // Если есть запрос на Excel
        if ($request->query->get('export') === 'excel') {
            return $this->exportExcel($feedback, $fields, $answers);
        }

        return $this->render('report/view.html.twig', [
            'feedback' => $feedback,
            'fields' => $fields,
            'answers' => $answers,
        ]);
    }

    private function exportExcel($feedback, $fields, $answers): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Заголовки
        $sheet->setCellValue('A1', 'User ID / Submission ID');
        $col = 'B';

        /** @var FeedbackField $field */
        foreach ($fields as $field) {
            $sheet->setCellValue($col.'1', $field->getLabel());
            $col++;
        }

        // Данные
        $row = 2;
        foreach ($answers as $key => $answerRow) {
            // $answerRow — массив ответов одного пользователя/отправки
            $sheet->setCellValue('A'.$row, $key);
            $col = 'B';
            foreach ($fields as $field) {
                $fieldId = $field->getId();
                $sheet->setCellValue($col.$row, $answerRow[$field->getLabel()][0] ?? '');
                $col++;
            }
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'report_'.$feedback->getId().'.xlsx';

        // Отдаем файл пользователю
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return $this->file($temp_file, $filename, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}