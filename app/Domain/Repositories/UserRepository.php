<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Models\User;
use App\Exceptions\UniqueConstraintException;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class UserRepository
{

    /**
     * emailからユーザー検索
     *
     * @param string $email
     * @return \App\Domain\Models\User
     */
    public function searchUserByEmail (string $email)
    {
        $user = User::where('email', $email)->first();
        return $user;
    }

    //emailがありかつステータス０
    public function storeToken(array $attributes)
    {
        $user = User::where('email', $attributes['email'])->update([
            'password' => Hash::make($attributes['password']),
            'onetime_token' => $attributes['onetime_token'],
            'onetime_expiration' => $attributes['onetime_expiration']
        ]);
    }

    //emailがなしかつステータス０
    public function storeTokenAndEmail(array $attributes)
    {
        $user = User::create([
            'email' => $attributes['email'],
            'password' => Hash::make($attributes['password']),
            'onetime_token' => $attributes['onetime_token'],
            'onetime_expiration' => $attributes['onetime_expiration']
        ]);
        return $user;
    }

    public function fetchUserByValidAuthCode(int $authCode)
    {
        $cond = [
            'onetime_token' => $authCode,
            'status' => 0
        ];
        $user = User::where($cond)->where('onetime_expiration', '>', CarbonImmutable::now(),)->firstOrFail();
        return $user;
    }
}
