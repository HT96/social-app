<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRelationship extends Model
{
    /**
     * Statuses of user relationships.
     *
     * @var array
     */
    const STATUSES = [
        'rejected' => 0,
        'pending' => 1,
        'approved' => 2,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_relationship';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_sender_id',
        'user_receiver_id',
        'status',
    ];

    /**
     * Register add to friend request in users relationship.
     *
     * @param int $senderId
     * @param int $receiverId
     * @return bool
     */
    public function addFriendRequest(int $senderId, int $receiverId)
    {
        $this->makeQuery($senderId, $receiverId)
            ->delete();

        return self::query()
            ->insert([
                'user_sender_id' => $senderId,
                'user_receiver_id' => $receiverId,
                'status' => self::STATUSES['pending']
            ]);
    }

    /**
     * Delete users relationship.
     *
     * @param int $senderId
     * @param int $receiverId
     * @return bool
     */
    public function deleteFriendRequest(int $senderId, int $receiverId)
    {
        return (bool) $this->makeQuery($senderId, $receiverId)
            ->where('status', '=', self::STATUSES['approved'])
            ->delete();
    }

    /**
     * Check if users active relationship already exists.
     *
     * @param int $senderId
     * @param int $receiverId
     * @return bool
     */
    public function activeRequestExists(int $senderId, int $receiverId)
    {
        return $this->makeQuery($senderId, $receiverId)
            ->where('status', '!=', self::STATUSES['rejected'])
            ->exists();
    }

    /**
     * Make users relationship query builder.
     *
     * @param int $senderId
     * @param int $receiverId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function makeQuery(int $senderId, int $receiverId)
    {
        return self::query()
            ->whereRaw('((`user_sender_id` = ? and `user_receiver_id` = ?) or (`user_sender_id` = ? and `user_receiver_id` = ?))', [
                $senderId,
                $receiverId,
                $receiverId,
                $senderId
            ]);
    }

    /**
     * Update users relationship status as approved.
     *
     * @param int $senderId
     * @param int $receiverId
     * @return bool
     */
    public function approveFriendRequest(int $senderId, int $receiverId)
    {
        return $this->updateStatus($senderId, $receiverId, self::STATUSES['pending'], self::STATUSES['approved']);
    }

    /**
     * Update users relationship status as rejected.
     *
     * @param int $senderId
     * @param int $receiverId
     * @return bool
     */
    public function rejectFriendRequest(int $senderId, int $receiverId)
    {
        return $this->updateStatus($senderId, $receiverId, self::STATUSES['pending'], self::STATUSES['rejected']);
    }

    /**
     * Update users relationship status.
     *
     * @param int $senderId
     * @param int $receiverId
     * @param int $statusFrom
     * @param int $statusTo
     * @return bool
     */
    protected function updateStatus(int $senderId, int $receiverId, int $statusFrom, int $statusTo)
    {
        return (bool) self::query()
            ->where('user_sender_id', '=', $senderId)
            ->where('user_receiver_id', '=', $receiverId)
            ->where('status', '=', $statusFrom)
            ->update(['status' => $statusTo]);
    }

    /**
     * Get incoming friend requests.
     *
     * @param int $receiverId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getIncomingFriendRequests(int $receiverId)
    {
        return self::query()
            ->where('user_receiver_id', '=', $receiverId)
            ->where('status', '=', self::STATUSES['pending']);
    }
}
