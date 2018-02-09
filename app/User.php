<?php namespace App;

use App\Traits\FormatsPermissions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\User
 *
 * @property int $id
 * @property string|null $username
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $gender
 * @property array $permissions
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $stripe_active
 * @property string|null $stripe_id
 * @property string|null $stripe_subscription
 * @property string|null $stripe_plan
 * @property string|null $last_four
 * @property string|null $trial_ends_at
 * @property string|null $subscription_ends_at
 * @property int $confirmed
 * @property string|null $confirmation_code
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $followedUsers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $followers
 * @property-read string $avatar
 * @property-read string $display_name
 * @property-read mixed $followers_count
 * @property-read bool $has_password
 * @property-read bool $is_admin
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Group[] $groups
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Social[] $oauth
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Playlist[] $playlists
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Track[] $tracks
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereConfirmationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLastFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereStripeActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereStripePlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereStripeSubscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereSubscriptionEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable, FormatsPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'email', 'password', 'permissions'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token', 'pivot'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['followers_count', 'display_name', 'has_password'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [ 'id' => 'integer', 'confirmed' => 'integer'];

    public function followedUsers()
    {
        return $this->belongsToMany('App\User', 'follows', 'follower_id', 'followed_id');
    }

    public function followers()
    {
        return $this->belongsToMany('App\User', 'follows', 'followed_id', 'follower_id');
    }

    public function getFollowersCountAttribute()
    {
        return $this->followers()->count();
    }

    /**
     * Groups this user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany('App\Group', 'user_group');
    }

    /**
     * Social profiles this users account is connected to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function social_profiles()
    {
        return $this->hasMany('App\SocialProfile');
    }

    /**
     * Many to many relationship with track model.
     *
     * @return BelongsToMany
     */
    public function tracks()
    {
        return $this->belongsToMany('App\Track')->withTimestamps();
    }

    /**
     * Many to many relationship with user model.
     *
     * @return BelongsToMany
     */
    public function playlists()
    {
        return $this->belongsToMany('App\Playlist')->withPivot('owner');
    }

    /**
     * Get user avatar.
     *
     * @return string
     */
    public function getAvatarAttribute()
    {
        $value = $this->attributes['avatar'];

        //absolute link
        if ($value && str_contains($value, '//')) return $value;

        //relative link (for new and legacy urls)
        if ($value) {
            return str_contains($value, 'assets') ? url($value) : url("storage/$value");
        }

        //gravatar
        $hash = md5(trim(strtolower($this->email)));

        return "https://www.gravatar.com/avatar/$hash?s=65";
    }

    /**
     * Compile user display name from available attributes.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        if ($this->username) {
            return $this->username;
        } else if ($this->first_name && $this->last_name) {
            return $this->first_name.' '.$this->last_name;
        } else if ($this->first_name) {
            return $this->first_name;
        } else if ($this->last_name) {
            return $this->last_name;
        } else {
            return explode('@', $this->email)[0];
        }
    }

    /**
     * Check if user has a password set.
     *
     * @return bool
     */
    public function getHasPasswordAttribute()
    {
        return isset($this->attributes['password']) && $this->attributes['password'];
    }

    /**
     * Check if user has a specified permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        $permissions = $this->permissions;

        foreach($this->groups as $group) {
            $permissions = array_merge($group->permissions, $permissions);
        }

        if (array_key_exists('admin', $permissions) && $permissions['admin']) return true;

        return array_key_exists($permission, $permissions) && $permissions[$permission];
    }

    public function setPermissionsAttribute($value)
    {
        $this->attributes['permissions'] = json_encode($value);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeCompact(Builder $query)
    {
        return $query->select('users.id', 'users.avatar', 'users.email', 'users.first_name', 'users.last_name', 'users.username');
    }
}
