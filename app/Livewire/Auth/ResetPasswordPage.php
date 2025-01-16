<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

class ResetPasswordPage extends Component
{
    #[Title('Reset Password - Ecommerce')]
    public $token;
    #[Url]
    public $email;
    public $password;
    public $password_confirmation;
    public function amount($token)
    {
        $this->token = $token;
    }

    public function save()
    {
        $this->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token
            ]
        );

        function (\App\Models\User $user, string $password) {
            $password = $this->password;
            $user->forcefill([
                'password' => Hash::make($password)
            ])->setRememberToken(\Illuminate\Support\Str::random(60));
            $user->save();
            event(new PasswordReset($user));
        };

        return $status === Password::PASSWORD_RESET ? redirect('/login') : session()->flash(
            'error',
            'Something went wrong'
        );
    }
    public function render()
    {
        return view('livewire.auth.reset-password-page');
    }
}
