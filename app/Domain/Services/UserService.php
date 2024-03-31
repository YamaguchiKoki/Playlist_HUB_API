<?php

declare(strict_types=1);

namespace App\Domain\Services;
use App\Domain\Repositories\UserRepository;
use App\Exceptions\UniqueConstraintException;
use App\Jobs\SendVerificationEmail;
use Illuminate\Auth\Events\Registered;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService
{
    public function __construct(private UserRepository $repository) {}

    public function fetchUserByValidAuthCode(int $authCode)
    {
        return $this->repository->fetchUserByValidAuthCode($authCode);
    }

    public function fetchUserByEmail(string $email)
    {
        return $this->repository->searchUserByEmail($email);
    }

    public function activateUserAndGenerateToken(object $user)
    {
        $token = JWTAuth::fromUser($user);
        $user->update(['status' => 1]);

        return $token;
    }

    public function sendAuthcode(object $user=null, array $attributes)
    {
        $attributes['onetime_token'] = mt_rand(100000, 999999);
        $attributes['onetime_expiration'] = now()->addMinute(3);

        if (!$user) {
            $user = $this->repository->storeTokenAndEmail($attributes);

            event(new Registered($user));
            dispatch(new SendVerificationEmail($user));

        } elseif($user && $user->status == 0) {
            $this->repository->storeToken($attributes);

            event(new Registered($user));
            dispatch(new SendVerificationEmail($user));

        } else {
            throw new UniqueConstraintException('このメールアドレスはすでに存在しています');
        }
    }


}
