<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Share\Pushers\NotificationAdded;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return responseJson(null, 401, 'Chưa xác thực người dùng');
            }
    
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $filter = $request->input('filter', 'all');
    
            $query = Notification::where('owner_id', $user->id)
                ->with(['user' => function ($query) {
                    $query->select('id', 'first_name', 'last_name', 'avatar', 'gender');
                }])
                ->orderByDesc('created_at');
    
            if ($filter === 'read') {
                $query->where('read', true);
            } elseif ($filter === 'unread') {
                $query->where('read', false);
            }
    
            $notifications = $query->paginate($perPage, ['*'], 'page', $page);
    
            $response = [
                'notifications' => $notifications->items(),
                'page_info' => [
                    'total' => $notifications->total(),
                    'total_page' => (int) ceil($notifications->total() / $notifications->perPage()),
                    'current_page' => $notifications->currentPage(),
                    'next_page' => $notifications->currentPage() < $notifications->lastPage() ? $notifications->currentPage() + 1 : null,
                    'per_page' => $notifications->perPage(),
                ],
            ];
    
            return responseJson($response, 200, "Lấy thành công danh sách thông báo");
        } catch (\Exception $e) {
            return responseJson(null, 500, 'Đã xảy ra lỗi khi lấy danh sách thông báo: ' . $e->getMessage());
        }
    }
    

    public function markAsRead($id)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return responseJson(null, 401, 'Chưa xác thực người dùng');
            }

            $notification = Notification::findOrFail($id);

            if ($notification->owner_id !== $user->id) {
                return responseJson(null, 403, 'Bạn không có quyền đánh dấu thông báo này là đã đọc');
            }

            $notification->read = true;
            $notification->save();

            $notificationAdded = new NotificationAdded();
            $notificationAdded->pusherMakeReadNotification($notification->id, $user->id);


            return responseJson($notification, 200, "Thông báo được đánh dấu là đã đọc");
        } catch (\Exception $e) {
            return responseJson(null, 500, 'Đã xảy ra lỗi khi đánh dấu là đã đọc thông báo: ' . $e->getMessage());
        }
    }

    public function markAllAsRead()
{
    try {
        $user = auth()->user();
        if (!$user) {
            return responseJson(null, 401, 'Chưa xác thực người dùng');
        }

        $updatedCount = Notification::where('owner_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);

        if ($updatedCount === 0) {
            return responseJson(null, 204, "Không có thông báo nào để đánh dấu là đã đọc");
        }

        return responseJson(null, 200, "Đã đánh dấu tất cả thông báo là đã đọc");
    } catch (\Exception $e) {
        return responseJson(null, 500, 'Đã xảy ra lỗi khi đánh dấu tất cả thông báo là đã đọc: ' . $e->getMessage());
    }
}

    public function getSecretKey(Request $request)
    {
        $user = auth()->user();
        $secretKey = JWTAuth::fromUser($user);

        return responseJson($secretKey, 200, "Lấy thành công khóa bảo mật.");
    }

    public function getUnreadCountNotifications(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return responseJson(null, 401, 'Chưa xác thực người dùng');
            }

            $unreadCount = Notification::where('owner_id', $user->id)
                ->where('read', false)
                ->count();

            return responseJson(['unread_count' => $unreadCount], 200, "Lấy thành công số lượng thông báo chưa đọc");
        } catch (\Exception $e) {
            return responseJson(null, 500, 'Đã xảy ra lỗi khi lấy số lượng thông báo chưa đọc: ' . $e->getMessage());
        }
    }
}
