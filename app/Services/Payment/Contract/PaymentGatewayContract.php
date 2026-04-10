<?php

namespace App\Services\Payment\Contract;

interface PaymentGatewayContract
{

    public function fetchPlans();
    public function storePayment(array $data) : bool;
    public function verifyPayment(string $paymentId);
}
