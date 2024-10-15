<?php

namespace App\Http\Livewire\Partial;

use Livewire\Component;

class HeaderOneLivewire extends Component
{
    protected $listeners = [
        'echo:AdminChannel,EventOrderStatusUpdated' => 'reloadNotification',
        'echo:AdminChannel,EventCustomerOrderCheckout' => 'reloadNotification',
        'echo:AdminChannel,EventDriverUpdated' => 'reloadNotification',
        'echo:AdminChannel,EventOrderPaymentStatusUpdated' => 'reloadNotification',
    ];

    public function mount()
    {
        $user = auth('admin')->user();

        // Check if the user is authenticated
        if (!$user) {
            abort(403, 'Unauthorized action.'); // Consider using a more user-friendly message
        }
    }

    public function reloadNotification($e)
    {
        // Reload notifications when an event is received
        $this->render();  // Not necessary; just use state update and rendering logic
    }

    public function markAsRead($notificationId)
    {
        $user = auth('admin')->user();

        // Find the notification and mark it as read
        if ($notification = $user->notifications()->find($notificationId)) {
            $notification->markAsRead();
        }
        // Optionally, re-render the component
        $this->render();  // This is unnecessary; state changes will automatically trigger re-rendering
    }

    public function render()
    {
    // Get the unread notifications query for the authenticated user
    $notificationsQuery = auth('admin')->user()->unreadNotifications();

    // Conditionally add the 'notifiable_id' filter if the user has role 8
    if (hasRole([8])) {
        $notificationsQuery->where('notifiable_id', auth('admin')->id());
    }

    // Fetch the notifications after applying any conditions
    $notifications = $notificationsQuery->get();
        return view('super-admins.components.header-one', [
            'notifications' => $notifications,
            'notificationsCount' => $notifications->count(),
        ]);
    }
}
