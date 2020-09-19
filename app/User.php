<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'login'
    ];

    public function articles()
    {
        return $this->hasMany('App\Article');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Role', 'role_user');
    }

    public function canDo($permission, $require = FALSE)
    {
        if (is_array($permission)) {
            foreach ($permission as $permName) {
                $permName = $this->canDo($permName);
                if ($permName && !$require) {
                    return TRUE;
                } elseif (!$permName && $require) {
                    return FALSE;
                }
            }

            return $require;
        } else {
            foreach ($this->roles as $role) {
                foreach ($role->perms as $perm) {
                    // foo* foobar
                    if (Str::is($permission, $perm->name)) {
                        return TRUE;
                    }
                }
            }
        }
    }

    // string ['role1', 'role2']
    public function hasRole($name, $require = FALSE)
    {
        if (is_array($name)) {
            foreach ($name as $roleName) {
                $hasRole = $this->canDo($roleName);
                if ($hasRole && !$require) {
                    return TRUE;
                } elseif (!$hasRole && $require) {
                    return FALSE;
                }
            }

            return $require;
        } else {
            foreach ($this->roles as $role) {
                if ($role->name == $name) {
                        return TRUE;
                }
            }
        }

        return false;
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
