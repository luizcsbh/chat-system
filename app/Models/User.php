<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="Usuário",
 *     required={"name", "email", "password"},
 *     properties={
 *         @OA\Property(
 *             property="id",
 *             type="integer",
 *             description="ID do usuário"
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             description="Nome do usuário"
 *         ),
 *         @OA\Property(
 *             property="email",
 *             type="string",
 *             format="email",
 *             description="E-mail do usuário"
 *         ),
 *         @OA\Property(
 *             property="created_at",
 *             type="string",
 *             format="date-time",
 *             description="Data de criação"
 *         ),
 *         @OA\Property(
 *             property="updated_at",
 *             type="string",
 *             format="date-time",
 *             description="Data de atualização"
 *         )
 *     }
 * )
 */

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function rules()
    {
        return [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email,',
            'password'  => 'nullable|string|min:8', // Torna a senha opcional no update
        ];
    }

    public function rulesForUpdate()
    {
        return [
            'name'      => 'sometimes|string|max:255',
            'email'     => 'sometimes|string|email|max:255|unique:users,email,',
            'password'  => 'sometimes|string|min:8', // Torna a senha opcional no update
        ];
    }

    public function feedback()
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string'   => 'O nome deve ser um texto.',
            'name.max'      => 'O nome não pode ter mais de 255 caracteres.',

            'email.required' => 'O e-mail é obrigatório.',
            'email.string'   => 'O e-mail deve ser um texto.',
            'email.email'    => 'O e-mail deve ser um endereço de e-mail válido.',
            'email.max'      => 'O e-mail não pode ter mais de 255 caracteres.',
            'email.unique'   => 'Este e-mail já está cadastrado.',

            'password.required' => 'A senha é obrigatória.',
            'password.string'   => 'A senha deve ser um texto.',
            'password.min'      => 'A senha deve ter no mínimo 8 caracteres.',
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];
}
