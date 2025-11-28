<?php

namespace App\Services;

use App\Repositories\ComplaintRepository;
use App\Repositories\OperationRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComplaintService
{

  protected $complaintRepository;
  protected $userRepository;
  protected $operationRepository;

  public function __construct(ComplaintRepository $complaintRepository, UserRepository $userRepository,
    OperationRepository $operationRepository)
  {
      $this->complaintRepository = $complaintRepository;
      $this->userRepository = $userRepository;
      $this->operationRepository = $operationRepository;
  }

  public function get_all_government_sectors()
  {
    return $this->complaintRepository->get_governement_sectors();
  }


  public function createComplaint(array $data, array $files = [])
  {
      $uploadedDocuments = [];
      $errors = [];
      $user = Auth::user();
      
      $complaint_number = time() . rand(100, 999);
      $complaint = $this->complaintRepository->createComplaint([
          'government_sector_id' => $data['government_sector_id'],
          'user_id' => $user->id,
          'location' => $data['location'],
          'description' => $data['description'],
          'complaint_type' => $data['complaint_type'],
          'complaint_number' => $complaint_number,
      ]);
      foreach ($files as $file) {
          try {
              // MIME real
              $finfo = finfo_open(FILEINFO_MIME_TYPE);
              $realMime = finfo_file($finfo, $file->getPathname());
              finfo_close($finfo);
              $allowedMime = [
                  'image/jpeg',
                  'image/png',
                  'image/gif',
                  'application/pdf',
                  'video/mp4',
                  'video/quicktime',
                  'application/zip',
                  'image/heic',
                  'image/heif',
                  'image/heic-sequence',
                  'image/heif-sequence'
              ];
              if (!in_array($realMime, $allowedMime)) {
                  $errors[] = [
                      'name' => $file->getClientOriginalName(),
                      'message' => 'نوع الملف الحقيقي غير مسموح: '.$realMime
                  ];
                  continue;
              }
              // UUID
              $extension = strtolower($file->getClientOriginalExtension());
              $uuid = Str::uuid()->toString();
              $newName = "{$uuid}.{$extension}";
              $path = "complaints/{$complaint->id}/{$newName}";

              Storage::disk('local')->put($path, file_get_contents($file));
              
              $doc = $this->complaintRepository->createDocument([
                  'complaint_id' => $complaint->id,
                  'document_path' => $path,
                  'mime_type' => $realMime,
              ]);
              $uploadedDocuments[] = $doc;

          } catch (\Exception $e) {
              $errors[] = [
                  'name' => $file->getClientOriginalName(),
                  'message' => $e->getMessage()
              ];
          }
      }
      return [
          'complaint' => $complaint,
          'documents' => $uploadedDocuments,
          'errors' => $errors
      ];
  }


  public function get_for_government_sector()
  {
    $user = Auth::user();
    return $this->complaintRepository->get_complaints_governement_sectors($user->id);
  }


  public function get_complaint_details($id)
  {
    $complaint = $this->complaintRepository->get_complaint($id);
    $documents = $this->complaintRepository->get_complaint_documents($complaint->id);
    $notices = $this->complaintRepository->get_complaint_notices($complaint->id);
    $complainant_user = $this->userRepository->findByID($complaint->user_id);

    foreach ($documents as $doc) {
        $doc->download_url = url("/api/documents/download/{$doc->id}");
    }
    return [
          'complaint' => $complaint,
          'documents' => $documents,
          'notices' => $notices,
          'complainant_user' => $complainant_user
      ];
  }

  public function download_document($id)
  {
    $document = $this->complaintRepository->find_document_by_id($id);

    if (!$document) {
        abort(404, "Document not found");
    }

    if (!Storage::disk('local')->exists($document->document_path)) {
        abort(404, "File does not exist");
    }

    return Storage::disk('local')->download(
        $document->document_path,
        basename($document->document_path)
    );
  }


  public function update_complaint_status(array $data)
  {

    $complaint = $this->complaintRepository->updateComplaint([
      'status' => $data['status'],
    ], $data['complaint_id']);

    // $user = Auth::user();
    // $employee = $this->complaintRepository->get_employee($user->id);
    // $complaint = $this->operationRepository->createOperation([
    //   'complaint_id' => $data['complaint_id'],
    //   'employee_id' => $employee->id,
    //   'notice_id' => null,
    //   'details' => 'تم التعديل على حالة الشكوى',
    //   'operation_date' => now()->addHours(3),
    // ]);

  }

  public function add_notice(array $data)
  {
    $notice = $this->complaintRepository->createNotice([
      'complaint_id' => $data['complaint_id'],
      'description' => $data['description'],
    ]);

    // $user = Auth::user();
    // $employee = $this->complaintRepository->get_employee($user->id);
    // $complaint = $this->operationRepository->createOperation([
    //   'complaint_id' => $data['complaint_id'],
    //   'employee_id' => $employee->id,
    //   'notice_id' => $notice->id,
    //   'details' => 'تمت إضافة ملاحظة خاصة بالشكوى',
    //   'operation_date' => now()->addHours(3),
    // ]);
  }


  public function get_for_citizen()
  {
    $user = Auth::user();
    return $this->complaintRepository->get_for_citizen($user->id);
  }

  public function search_complaint_number(array $data)
  {
    return $this->complaintRepository->search_complaint_number($data['complaint_number']);
  }

  public function update_complaint_by_citizen(array $data, array $files = [])
  {
      $uploadedDocuments = [];
      $errors = [];

      $complaint = $this->complaintRepository->get_complaint($data['complaint_id']);

      $user = Auth::user();
      if ($complaint->user_id !== $user->id) {
          return [
              'success' => false,
              'message' => 'غير مصرح لك بتعديل هذه الشكوى',
          ];
      }

      if ($complaint->status !== 'جديدة') {
          return [
              'success' => false,
              'message' => 'لا يمكن تعديل الشكوى إلا إذا كانت حالتها (جديدة)',
          ];
      }

      $updateData = [];
      if (isset($data['location'])) {
          $updateData['location'] = $data['location'];
      }
      if (isset($data['description'])) {
          $updateData['description'] = $data['description'];
      }
      if (isset($data['complaint_type'])) {
          $updateData['complaint_type'] = $data['complaint_type'];
      }

      if (!empty($updateData)) {
          $this->complaintRepository->updateComplaint($updateData, $complaint->id);
          $complaint = $this->complaintRepository->get_complaint($data['complaint_id']);
      }

      foreach ($files as $file) {
          try {
              $finfo = finfo_open(FILEINFO_MIME_TYPE);
              $realMime = finfo_file($finfo, $file->getPathname());
              finfo_close($finfo);
              $allowedMime = [
                  'image/jpeg',
                  'image/png',
                  'image/gif',
                  'application/pdf',
                  'video/mp4',
                  'video/quicktime',
                  'application/zip',
                  'image/heic',
                  'image/heif',
                  'image/heic-sequence',
                  'image/heif-sequence',
              ];
              if (!in_array($realMime, $allowedMime)) {
                  $errors[] = [
                      'name' => $file->getClientOriginalName(),
                      'message' => 'نوع الملف الحقيقي غير مسموح: '.$realMime
                  ];
                  continue;
              }

              $extension = strtolower($file->getClientOriginalExtension());
              $uuid = Str::uuid()->toString();
              $newName = "{$uuid}.{$extension}";
              $path = "complaints/{$complaint->id}/{$newName}";

              Storage::disk('local')->put($path, file_get_contents($file));

              $doc = $this->complaintRepository->createDocument([
                  'complaint_id' => $complaint->id,
                  'document_path' => $path,
                  'mime_type' => $realMime,
              ]);
              $uploadedDocuments[] = $doc;

          } catch (\Exception $e) {
              $errors[] = [
                  'name' => $file->getClientOriginalName(),
                  'message' => $e->getMessage()
              ];
          }
      }

      return [
          'success' => true,
          'message' => 'تم تحديث الشكوى بنجاح',
          'complaint' => $complaint,
          'documents' => $uploadedDocuments,
          'errors' => $errors
      ];
  }


  public function delete_complaint_by_citizen($id)
  {
    $user = Auth::user();
    $complaint = $this->complaintRepository->get_complaint($id);

    if ($complaint->user_id !== $user->id) {
        return [
            'success' => false,
            'message' => 'غير مصرح لك بحذف هذه الشكوى',
        ];
    }
    if ($complaint->status !== 'جديدة') {
        return [
            'success' => false,
            'message' => 'لا يمكن حذف الشكوى إلا إذا كانت حالتها (جديدة)',
        ];
    }

    try {
        $folderPath = "complaints/{$complaint->id}";
        if (Storage::disk('local')->exists($folderPath)) {
            Storage::disk('local')->deleteDirectory($folderPath);
        }
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'حدث خطأ أثناء حذف الملفات: ' . $e->getMessage(),
        ];
    }

    $delete_complaint = $this->complaintRepository->delete_complaint($id);
    return [
        'success' => true,
        'message' => 'تم حذف الشكوى بنجاح',
    ];
  }

}