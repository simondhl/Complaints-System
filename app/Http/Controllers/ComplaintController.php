<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComplaintFormRequest;
use App\Services\ComplaintService;
use Illuminate\Http\JsonResponse;
use TCPDF;

class ComplaintController extends Controller
{
    protected ComplaintService $complaintService;

    public function __construct(ComplaintService $complaintService)
    {
        $this->complaintService = $complaintService;
    }

    public function get_all_government_sectors()
    {
      $government_sectors = $this->complaintService->get_all_government_sectors();
      return response()->json([
        'government_sectors' => $government_sectors
      ]);
    }

    public function store(ComplaintFormRequest $request)
    {
      $files = $request->hasFile('documents') ? $request->file('documents') : [];
      $result = $this->complaintService->createComplaint(
          $request->validated(),
          $files
      );
      return response()->json([
          'message' => 'تم إنشاء الشكوى بنجاح',
          'complaint' => $result['complaint'],
          'documents' => $result['documents'],
          'errors' => $result['errors']
      ], 201);
    }

    public function get_for_government_sector()
    {
      $complaints = $this->complaintService->get_for_government_sector();
      return response()->json([
        'complaints' => $complaints
      ]);
    }

    public function get_complaint_details($id)
    {
      $complaint = $this->complaintService->get_complaint_details($id);
      return response()->json([
        'complaint' => $complaint['complaint'],
        'documents' => $complaint['documents'],
        'notices' => $complaint['notices'],
        'complainant_user' => $complaint['complainant_user']
      ]);
    }

    public function download_document($id)
    {
      return $this->complaintService->download_document($id);
    }

    public function update_complaint_status(ComplaintFormRequest $request)
    {
      $result = $this->complaintService->update_complaint_status($request->validated());
      if (!$result) {
          return response()->json([
              'message' => 'تم تعديل الشكوى من قبل موظف آخر',
          ], 409);
      }

      return response()->json([
          'message' => 'تم تعديل حالة الشكوى بنجاح',
      ]);
    }

    public function add_notice(ComplaintFormRequest $request)
    {
      $result = $this->complaintService->add_notice($request->validated());
      return response()->json([
          'message' => 'تم إضافة ملاحظة خاصة بالشكوى بنجاح',
      ]);
    }


    public function get_for_citizen()
    {
      $complaints = $this->complaintService->get_for_citizen();
      return response()->json([
        'complaints' => $complaints
      ]);
    }

    public function search_complaint_number(ComplaintFormRequest $request)
    {
      $complaint = $this->complaintService->search_complaint_number($request->validated());
      return response()->json([
        'complaint' => $complaint
      ]);
    }

    public function update_by_citizen(ComplaintFormRequest $request)
    {
        $files = $request->hasFile('documents') ? $request->file('documents') : [];
        $result = $this->complaintService->update_complaint_by_citizen(
            $request->validated(),
            $files
        );
      
        if (!$result['success']) {
            return response()->json([
                'message' => $result['message'],
            ], 400);
        }
      
        return response()->json([
            'message' => $result['message'],
            'complaint' => $result['complaint'],
            'documents' => $result['documents'],
            'errors' => $result['errors']
        ], 200);
    }

    public function delete_by_citizen($id)
    {
      $result = $this->complaintService->delete_complaint_by_citizen($id);
      
      return response()->json([
          'message' => $result['message'],
      ]);
    }

    public function get_by_government_sector($government_sector_id)
    {
      $complaints = $this->complaintService->get_by_government_sector($government_sector_id);
      return response()->json([
        'complaints' => $complaints
      ]);
    }

    public function get_records_by_date(ComplaintFormRequest $request)
    {
        $result = $this->complaintService->get_records_by_date($request->validated());
      
        return response()->json([
          'complaints' => $result['complaints'],
          'complaints_count' => $result['complaints_count'],
          'operations' => $result['operations'],
          'operations_count' => $result['operations_count'],
        ], 200);
    }

    public function get_report_by_date(ComplaintFormRequest $request)
    {
        $data = $this->complaintService->get_records_by_date($request->validated());

        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetTitle('تقرير الشكاوى والعمليات');
        $pdf->SetMargins(10, 15, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        $pdf->SetFont('dejavusans', '', 12);

        $pdf->AddPage();

        $html = view('reports.report', [
            'complaints' => $data['complaints'],
            'complaints_count' => $data['complaints_count'],
            'operations' => $data['operations'],
            'operations_count' => $data['operations_count'],
            'start_date' => $request['start_date'],
            'end_date' => $request['end_date'],
        ])->render();

        $pdf->writeHTML($html, true, false, true, false, '');

        $content = $pdf->Output('report.pdf', 'S');
        
        return response($content, 200)
          ->header('Content-Type', 'application/pdf')
          ->header('Content-Disposition', 'attachment; filename="report.pdf"');
    }

}
