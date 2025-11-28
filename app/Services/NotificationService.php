<?php

namespace App\Services;

use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Auth;

class NotificationService
{

    protected $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function send_notification(int $user_ID, string $message, int $complaint_number)
    {
        $notification = $this->notificationRepository->create([
          'user_id' => $user_ID,
          'message' => $message,
          'status' => false,
          'complaint_number' => $complaint_number
        ]);  

    }

    public function get_all_notifications()
    {
        $user = Auth::user();

        $notificatios = $this->notificationRepository->get($user->id);

        $this->notificationRepository->update(['status' => true], $user->id);
        
        return ['notificatios' => $notificatios]; 
    }

    public function new_notifications_count()
    {
        $user = Auth::user();
        $notificatios = $this->notificationRepository->count($user->id);
        return $notificatios;
    }


}