<?php

namespace App\Controller;

use App\DTO\Feedback\FeedbackFilterDto;
use App\DTO\Feedback\FeedbackSortDto;
use App\DTO\Report\ReportFilterDto;
use App\DTO\Report\ReportSortDto;
use App\Entity\Feedback\Feedback;
use App\Entity\Feedback\FeedbackField;
use App\Repository\FeedbackFieldAnswerRepository;
use App\Repository\FeedbackFieldOptionRepository;
use App\Repository\FeedbackFieldRepository;
use App\Repository\FeedbackRepository;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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
        $allFields = $this->feedbackFieldRepository->findBy(['feedback' => $feedback]);
        $fieldIds = $request->query->all('fields');

        $fields = $fieldIds
            ? array_filter($allFields, fn($f) => in_array($f->getId(), $fieldIds))
            : $allFields;

        $answers = $this->feedbackFieldAnswerRepository->findAnswersByFeedback($feedback->getId());

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
        $sheet->setCellValue('A1', 'Клиент');
        $col = 'B';

        /** @var FeedbackField $field */
        foreach ($fields as $field) {
            $sheet->setCellValue($col.'1', $field->getLabel());
            $col++;
        }

        // Стили для шапки
        $headerRange = 'A1:' . chr(ord('A') + count($fields)) . '1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDCE6F1');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Данные
        $row = 2;
        foreach ($answers as $key => $answerRow) {
            // $answerRow — массив ответов одного пользователя/отправки
            $sheet->setCellValue('A'.$row, $key);
            $col = 'B';
            foreach ($fields as $field) {
                $fieldLabel = $field->getLabel();
                // Проверка на существование данных
                $value = $answerRow[$fieldLabel][0] ?? '';
                $sheet->setCellValue($col.$row, $value);
                $col++;
            }
            $row++;
        }

        // Выделяем рамки по всему диапазону с данными и заголовком
        $lastCol = chr(ord('A') + count($fields));
        $lastRow = $row - 1;
        $dataRange = 'A1:' . $lastCol . $lastRow;

        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF000000'));

        // Автоподгонка ширины колонок
        for ($c = 'A'; $c <= $lastCol; $c++) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'report_'.$feedback->getId().'.xlsx';

        // Отдаем файл пользователю
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return $this->file($temp_file, $filename, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}