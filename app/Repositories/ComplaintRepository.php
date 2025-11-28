<?php

namespace App\Repositories;

use App\Models\Complaint;
use App\Models\Complaint_document;
use App\Models\Employee;
use App\Models\Government_sector;
use App\Models\Notice;

class ComplaintRepository
{
    protected $complaint;
    protected $document;
    protected $governmentSector;
    protected $employee;
    protected $notice;

    public function __construct(Complaint $complaint, Government_sector $governmentSector, 
                                 Complaint_document $document, Employee $employee, Notice $notice)
    {
        $this->complaint = $complaint;
        $this->document = $document;
        $this->governmentSector = $governmentSector;
        $this->employee = $employee;
        $this->notice = $notice;
    }

    public function get_governement_sectors()
    {
        return $this->governmentSector->select('id', 'name', 'created_at')->get();
    }

    public function createComplaint(array $data)
    {
        return $this->complaint->create($data);
    }

    public function createDocument(array $data)
    {
        return $this->document->create($data);
    }

    public function get_complaints_governement_sectors($user_id)
    {
        $employee0 = $this->employee->where('user_id', $user_id)->first();
        return $this->complaint->where('government_sector_id', $employee0->government_sector_id)->latest()->get();
    }

    public function get_complaint($id)
    {
        return $this->complaint->where('id', $id)->first();
    }

    public function get_complaint_documents($complaint_id)
    {
        return $this->document->where('complaint_id', $complaint_id)->get();
    }

    public function get_complaint_notices($complaint_id)
    {
        return $this->notice->where('complaint_id', $complaint_id)->get();
    }

    public function find_document_by_id($id)
    {
        return $this->document->where('id', $id)->first();
    }

    public function updateComplaint(array $data, $id)
    {
        return $this->complaint->where('id', $id)->update($data);
    }

    public function createNotice(array $data)
    {
        return $this->notice->create($data);
    }

    public function get_for_citizen($user_id)
    {
        return $this->complaint->where('user_id', $user_id)->latest()->get();
    }

    public function search_complaint_number($complaint_number)
    {
        return $this->complaint->where('complaint_number', $complaint_number)->first();
    }

    public function delete_complaint($id)
    {
        return $this->complaint->where('id', $id)->delete();
    }

}