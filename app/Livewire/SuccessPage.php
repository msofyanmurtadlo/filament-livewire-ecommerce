<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class SuccessPage extends Component
{
    #[Title('Success - Ecommerce')]
    #[Url()]

    public $session_id;

    public function render()
    {
        $latest_orders = Order::with('address')->where('user_id', auth()->user()->id)->latest()->first();

        if ($this->session_id) {
            Stripe::setApiKey('STRIPE_SECRET');
            $session_info = Session::retrieve($this->session_id);
            if ($session_info->payment_status != 'paid') {
                $latest_orders->payment_status = 'failed';
                $latest_orders->save();
                return redirect()->route('cancel');
            } elseif ($session_info->payment_status === 'paid') {
                $latest_orders->payment_status = 'paid';
                $latest_orders->save();
            }
        }

        return view('livewire.success-page', [
            'order' => $latest_orders
        ]);
    }
}
