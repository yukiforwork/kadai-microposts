<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

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
    
     /**
     * このユーザが所有する投稿。（ Micropostモデルとの関係を定義）
     */
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
        
    }
    
    //フォロー関連//
    /**
     * このユーザがフォロー中のユーザ。（ Userモデルとの関係を定義）
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    /**
     * このユーザをフォロー中のユーザ。（ Userモデルとの関係を定義）
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
     /**
     * $userIdで指定されたユーザをフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
        public function follow($userId)
    {
        // すでにフォローしているか
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうか
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            // フォロー済み、または、自分自身の場合は何もしない
            return false;
        } else {
            // 上記以外はフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }

    /**
     * $userIdで指定されたユーザをアンフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function unfollow($userId)
    {
        // すでにフォローしているか
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうか
        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
            // フォロー済み、かつ、自分自身でない場合はフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 上記以外の場合は何もしない
            return false;
        }
    }

    /**
     * 指定された $userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
     *
     * @param  int  $userId
     * @return bool
     */
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    
    ///↓ここからお気に入り設定↓///
    
    
   /**
     * このユーザがお気に入り中のmicroposts。（ Userモデルとの関係を定義）
     */
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'user_favorite','user_id', 'favorite_id')->withTimestamps();
        
    }
    
    /**
     * $userIdで指定されたmicropostをお気に入りする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function makefavorite($micropostId)
    {
        // すでにフォローしているか
        $exist = $this->is_favorite($micropostId);

        if ($exist) {
            // お気に入り済みの場合は何もしない
            return false;
        } else {
            // 上記以外はお気に入りする
            $this->favorites()->attach($micropostId);
            return true;
        }
    }
    
    
     /**
     * $userIdで指定されたmicropostをお気に入りから外す。
     *
     * @param  int  $userId
     * @return bool
     */
    public function unfavorite($micropostId)
    {
        // すでにお気に入りしているか
        $exist = $this->is_favorite($micropostId);

        if ($exist) {
            // お気に入り済みの場合はお気に入りを外す
            $this->favorites()->detach($micropostId);
            return true;
        } else {
            // 上記以外の場合は何もしない
            return false;
        }
    }
    
    /**
     * 指定された $userIdのmicropostをこのユーザがお気に入り中であるか調べる。フォロー中ならtrueを返す。
     *
     * @param  int  $userId
     * @return bool
     */
    public function is_favorite($userId)
    {
        // お気に入り中タスクの中に $userIdのものが存在するか
        return $this->favorites()->where('favorite_id', $userId)->exists();
    }
   
    
    /**
     * このユーザに関係するモデルの件数をロードする。
   */
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers','favorites']);
    }  
    
    /**
     * このユーザとフォロー中ユーザの投稿に絞り込む。
     */
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
     /**
     * このユーザのお気に入りの投稿のみに絞り込む。
     */
    public function feed_favorites()
    {
        // このユーザがお気に入り中の投稿のidを取得して配列にする
        $userIds = $this->favorites()->pluck('user_favorite.id')->toArray();
        
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_favorite_id', $userFavoriteIds);
    }
    
}
