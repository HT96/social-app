<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'public',
        'text',
    ];

    /**
     * Get all posts by user ids.
     *
     * @param array $userIds
     * @return Builder
     */
    public static function getByUserIds(array $userIds)
    {
        return self::queryWithUsers()
            ->whereIn('users.id', $userIds)
            ->groupBy('posts.id')
            ->orderBy('posts.created_at', 'desc');
    }

    /**
     * Get all posts by user id.
     *
     * @param int $userId
     * @param bool $onlyPublic
     * @return Builder
     */
    public static function getByUserId(int $userId, bool $onlyPublic)
    {
        $query = self::queryWithUsers()
            ->where('users.id', '=', $userId);

        if ($onlyPublic) {
            $query->where('posts.public', '=', 1);
        }

        return $query->groupBy('posts.id')
            ->orderBy('posts.created_at', 'desc');
    }

    /**
     * Make query with users.
     *
     * @return Builder
     */
    protected static function queryWithUsers()
    {
        return self::query()
            ->select(['posts.id', 'posts.title', 'posts.text', 'posts.public', 'posts.created_at',
                'users.id as user_id', 'users.name', 'users.surname'])
            ->join('users', 'users.id', '=', 'posts.user_id');
    }
}
