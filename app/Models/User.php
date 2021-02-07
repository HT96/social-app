<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get users.
     *
     * @param int $userId
     * @param array $keywords
     * @return Builder
     */
    public static function getWithRelationship(int $userId, array $keywords = [])
    {
        $query = User::query()
            ->select(['users.id', 'users.name', 'users.surname', 'send_rel.status as send_status', 'receive_rel.status as receive_status'])
            ->leftJoin('users_relationship as send_rel', function(JoinClause $join) use($userId) {
                $join->on('send_rel.user_sender_id', '=', 'users.id')
                    ->where('send_rel.user_receiver_id', '=', $userId);
            })
            ->leftJoin('users_relationship as receive_rel', function(JoinClause $join) use($userId) {
                $join->on('receive_rel.user_receiver_id', '=', 'users.id')
                    ->where('receive_rel.user_sender_id', '=', $userId);
            })
            ->where('users.id', '!=', $userId);

        if ( !empty($keywords)) {
            $query->where(function(Builder $subQuery) use($keywords) {
                foreach ($keywords as $keyword) {
                    $subQuery->orWhere('users.name', 'like', "%$keyword%")
                        ->orWhere('users.surname', 'like', "%$keyword%");
                }
            });
        }

        return $query;
    }
}
