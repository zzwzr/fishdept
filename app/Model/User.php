<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;
use HyperfExtension\Jwt\Contracts\JwtSubjectInterface;

/**
 * @property int $id 
 * @property string $name 
 * @property string $mobile 
 * @property string $avatar 
 * @property string $email 
 * @property string $gender 
 * @property int $is_verified 
 * @property string $last_login_at 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class User extends Model implements JwtSubjectInterface
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['name', 'mobile', 'avatar', 'email', 'gender', 'is_verified', 'password', 'last_login_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'is_verified' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJwtIdentifier()
    {
        return $this->id;
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJwtCustomClaims(): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'mobile'        => $this->mobile,
            'email'         => $this->email,
            'gender'        => $this->gender
        ];
    }
}
