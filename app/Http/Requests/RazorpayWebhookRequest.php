<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // Add any payload validation rules here if needed
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->header('user-agent') !== 'Razorpay-Webhook/v1' || 
                !$this->hasHeader('x-razorpay-event-id') || 
                !$this->hasHeader('x-razorpay-signature')) {
                
                Log::error('Invalid Razorpay webhook request', [
                    'user-agent' => $this->header('user-agent'),
                    'has_event_id' => $this->hasHeader('x-razorpay-event-id'),
                    'has_signature' => $this->hasHeader('x-razorpay-signature')
                ]);
                
                throw new HttpResponseException(
                    response()->json(['message' => 'Invalid webhook request'], 400)
                );
            }
        });
    }
}
