<?php

namespace App\Http\Requests;

use App\Rules\NoHtml;
use Illuminate\Foundation\Http\FormRequest;

class ComplaintFormRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return match($this->route()->getActionMethod()) {
            
        'store' => [
            'government_sector_id' => 'required|exists:government_sectors,id',
            'location' => ['required', 'string', 'max:255', new NoHtml],
            'description' => ['required', 'string', new NoHtml],
            'complaint_type' => ['required', 'string', new NoHtml],
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:51200|mimes:jpg,jpeg,png,pdf,mp4,mov,zip,heic,heif',
        ],
        'update_complaint_status' => [
            'status' => ['required', 'string', new NoHtml],
            'complaint_id' => 'required|exists:complaints,id',
            'version' => 'required|integer',
        ],
        'add_notice' => [
            'description' => ['required', 'string', new NoHtml],
            'complaint_id' => 'required|exists:complaints,id',
        ],
        'search_complaint_number' => [
            'complaint_number' => ['required', 'string', new NoHtml],
        ],
        'update_by_citizen' => [
            'complaint_id' => 'required|exists:complaints,id',
            'location' => ['nullable', 'string', 'max:255', new NoHtml],
            'description' => ['nullable', 'string', new NoHtml],
            'complaint_type' => ['nullable', 'string', new NoHtml],
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:51200|mimes:jpg,jpeg,png,pdf,mp4,mov,zip,heic,heif',
        ],
        'get_records_by_date' => [
            'start_date' => 'required|date|before_or_equal:today',
            'end_date'   => 'required|date|before_or_equal:today|after_or_equal:start_date',
        ],
        'get_report_by_date' => [
            'start_date' => 'required|date|before_or_equal:today',
            'end_date'   => 'required|date|before_or_equal:today|after_or_equal:start_date',
        ],

        default => [],
      };
    }

    public function messages()
    {
       return match($this->route()->getActionMethod()) {

        'store' => [
            'government_sector_id.required' => 'يجب تحديد الجهة الحكومية',
            'government_sector_id.exists' => 'الجهة الحكومية غير موجودة',
            'location.required' => 'الموقع مطلوب',
            'description.required' => 'الوصف مطلوب',
            'complaint_type.required' => 'نوع الشكوى مطلوب',
            'documents.array' => 'يجب أن تكون الملفات مصفوفة',
            'documents.*.file' => 'كل ملف يجب أن يكون ملفاً صحيحاً',
            'documents.*.max' => 'حجم الملف كبير جداً',
            'documents.*.mimes' => 'امتداد الملف غير مسموح',
        ],

        'update_complaint_status' => [
            'status.required' => 'يجب إدخال حالة الشكوى',
            'complaint_id.required' => 'يجب تحديد الشكوى',
            'complaint_id.exists' => 'الشكوى غير موجودة',
            'version.required' => 'يجب إدخال حالة الشكوى',
        ],

        'add_notice' => [
            'description.required' => 'الوصف مطلوب (لإضافة ملاحظة)',
            'complaint_id.required' => 'يجب تحديد الشكوى',
            'complaint_id.exists' => 'الشكوى غير موجودة',
        ],
        'search_complaint_number' => [
            'complaint_number.required' => 'رقم الشكوى مطلوب',
        ],
        'update_by_citizen' => [
            'complaint_id.required' => 'يجب تحديد الشكوى',
            'complaint_id.exists' => 'الشكوى غير موجودة',
            'documents.array' => 'يجب أن تكون الملفات مصفوفة',
            'documents.*.file' => 'كل ملف يجب أن يكون ملفاً صحيحاً',
            'documents.*.max' => 'حجم الملف كبير جداً',
            'documents.*.mimes' => 'امتداد الملف غير مسموح',
        ],

        default => []
    };
}

}
