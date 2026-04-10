<?php

namespace App\Traits;


use App\Models\Notification;
use App\Models\Role;
use App\Models\User;

trait Notifiable
{
    protected static function audit($action, $model)
    {
        $roles = Role::where('model','=',$model)->where('action','=',$action)->get();
        foreach ($roles as $role) {

            $note = Notification::create([
                'alert_text' => $model.' record has been '.$action,
                'alert_link' => '#',
            ]);

            $users = User::whereHas(
                'roles', function($q) use ($role) {
                $q->where('name', $role->target->name);
            })->get();

            foreach ($users as $user) {
                $user->notifications()->sync($note->id,false);
            }
        }

    }
}
