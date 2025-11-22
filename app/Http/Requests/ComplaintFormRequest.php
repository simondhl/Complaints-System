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
            'documents.*' => 'file|max:51200|mimes:jpg,jpeg,png,gif,pdf,mp4,mov,zip',
        ],
        'update_complaint_status' => [
            'status' => ['required', 'string', new NoHtml],
        ],
        'add_notice' => [
            'description' => ['required', 'string', new NoHtml],
        ],

        default => [],
      };
    }

    public function messages()
    {
        return [
            'government_sector_id.required' => 'يجب تحديد الجهة الحكومية',
            'government_sector_id.exists' => 'الجهة الحكومية غير موجودة',
            'location.required' => 'الموقع مطلوب',
            'description.required' => 'الوصف مطلوب',
            'complaint_type.required' => 'نوع الشكوى مطلوب',
            'documents.array' => 'يجب أن تكون الملفات مصفوفة',
            'documents.*.file' => 'كل ملف يجب أن يكون ملفاً صحيحاً',
            'documents.*.max' => 'حجم الملف كبير جداً',
            'documents.*.mimes' => 'امتداد الملف غير مسموح',
        ];
    }

}
