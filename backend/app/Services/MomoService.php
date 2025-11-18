<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Transaction;

class MomoService
{
    protected $partnerCode;
    protected $accessKey;
    protected $secretKey;
    protected $endpoint;
    protected $ipnUrl;

    public function __construct()
    {
        $this->partnerCode = config('momo.partner_code');
        $this->accessKey = config('momo.access_key');
        $this->secretKey = config('momo.secret_key');
        $this->endpoint = config('momo.endpoint');
        $this->ipnUrl = config('momo.ipn_url');
    }

    // 1. Gửi yêu cầu thanh toán đến MoMo
    public function createPaymentRequest(int $orderId, float $amount): array
    {
        $requestId = time() . "-" . $orderId;
        $orderInfo = "Thanh toán đơn hàng #" . $orderId;
        $amount = (int) $amount; 
        
        $rawHash = "partnerCode={$this->partnerCode}&accessKey={$this->accessKey}&requestId={$requestId}&amount={$amount}&orderId={$orderId}&orderInfo={$orderInfo}&returnUrl=...&ipnUrl={$this->ipnUrl}&requestType=captureWallet";
        
        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $data = [
            'partnerCode' => $this->partnerCode,
            'accessKey' => $this->accessKey,
            'requestId' => $requestId,
            'orderId' => (string) $orderId,
            'orderInfo' => $orderInfo,
            'amount' => $amount,
            'returnUrl' => route('momo.return'), // URL Frontend của bạn
            'ipnUrl' => $this->ipnUrl, // URL Callback API
            'requestType' => 'captureWallet',
            'signature' => $signature,
        ];

        $response = Http::post($this->endpoint, $data);
        
        return $response->json(); 
    }

    public function verifySignature(array $data): bool
    {
        $rawHash = "partnerCode={$data['partnerCode']}&accessKey={$this->accessKey}&requestId={$data['requestId']}&amount={$data['amount']}&orderId={$data['orderId']}&orderInfo={$data['orderInfo']}&orderType={$data['orderType']}&transId={$data['transId']}&message={$data['message']}&localMessage={$data['localMessage']}&responseTime={$data['responseTime']}&errorCode={$data['errorCode']}&payType={$data['payType']}";
        
        $expectedSignature = hash_hmac('sha256', $rawHash, $this->secretKey);
        
        return $expectedSignature === $data['signature'];
    }
}