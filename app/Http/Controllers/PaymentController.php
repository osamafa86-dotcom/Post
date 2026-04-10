<?php

namespace App\Http\Controllers;

use App\Http\Requests\RazorpayWebhookRequest;
use App\Services\Payment\Contract\PaymentGatewayContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;
use Razorpay\Api\Api;
use Exception;

class PaymentController extends Controller
{
    /**
     * @param RazorpayWebhookRequest $request
     * @param PaymentGatewayContract $paymentGatewayContract
     * @return JsonResponse
     */
    // TODO: use App\Http\Requests\RazorpayWebhookRequest instead of Request for validation ( Abdelrahman Ibrahim )
    public function capture(Request $request, PaymentGatewayContract $paymentGatewayContract): JsonResponse
    {
        $data = $request->all();

        try {
            $paymentGatewayContract->storePayment($data);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()]);
        }

        return response()->json(['message' => 'Payment successful']);
    }

    /**
     * @param PaymentGatewayContract $paymentGatewayContract
     * @return JsonResponse
     */
    public function verify(PaymentGatewayContract $paymentGatewayContract): JsonResponse
    {
        try {
            $paymentGatewayContract->verifyPayment('pay_QkHDZlSx9QJ43r');
        }catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()]);
        }
        return response()->json(['message' => 'Payment successful']);

    }
}
