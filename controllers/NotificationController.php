<?php
/**
 * NotificationController – View & manage notifications
 */
class NotificationController extends Controller
{
    public function index(): void
    {
        $userId   = current_user_id();
        $schoolId = current_school_id();
        $notifications = Notification::allForUser($userId, $schoolId);
        $this->view('notifications.index', [
            'title'         => 'Notifications',
            'notifications' => $notifications,
        ]);
    }

    public function markRead($id): void
    {
        $id = (int) $id;
        $userId = current_user_id();
        Notification::markRead($id, $userId);
        flash('success', 'Notification marked as read.');
        redirect(tenant_slug() . '/notifications');
    }

    public function markAllRead(): void
    {
        $userId   = current_user_id();
        $schoolId = current_school_id();
        Notification::markAllRead($userId, $schoolId);
        flash('success', 'All notifications marked as read.');
        redirect(tenant_slug() . '/notifications');
    }
}
