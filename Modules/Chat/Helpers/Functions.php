<?php


use Modules\Chat\Entities\Status;

if (!function_exists('userStatusChange')) {
    function userStatusChange($userId, $status)
    {
        $current = Status::firstOrNew(
            ['user_id' => $userId],
            ['user_id' => $userId,]
        );

        $current->status = $status;
        $current->save();
    }
}

if (!function_exists('createGroupPermission')) {
    function createGroupPermission()
    {
        $role_id = auth()->user()->role_id;
        if ($role_id == 1) return true;
        if ($role_id == 3) return false;

        if ($role_id == 2){
            if(app('general_settings')->get('chat_can_make_group')== 'yes'){
                return true;
            }
            return false;
        }

        if(app('general_settings')->get('chat_teacher_staff_can_make_group')== 'yes'){
            return true;
        }
        return false;

    }
}