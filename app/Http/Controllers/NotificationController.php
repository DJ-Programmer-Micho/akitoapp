<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        // Ensure the user is authenticated for all actions in this controller
        // $this->middleware('auth:admin');
    }

    public function markAsRead(Request $request)
    {
        // $user = auth('admin')->user();
        // $notification = $user->notifications()->find($request->notification_id);

        // if ($notification) {
        //     $notification->markAsRead();
        // }

        // return response()->json(['message' => 'Notification marked as read']);
    }
}
