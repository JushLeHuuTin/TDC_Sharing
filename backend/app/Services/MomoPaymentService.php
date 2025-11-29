<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Transaction;

class MomoPaymentService
{
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $endpoint;

    public function __construct()
    {
        // TẢI KHÓA TỪ FILE CẤU HÌNH (.env)
        // 
        $this->partnerCode = env('MOMO_PARTNER_CODE', 'MOMOBKUN20180529');
        $this->accessKey   = env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j');
        $this->secretKey   = env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');
        $this->endpoint    = env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create');
    }

    /**
     * Tạo yêu cầu thanh toán mới và lấy URL chuyển hướng.
     *
     * @param float $amount Tổng số tiền cần thanh toán.
     * @param string $orderInfo Thông tin đơn hàng (ví dụ: danh sách Order ID).
     * @param string $returnUrl URL để Momo chuyển hướng người dùng về.
     * @param string $notifyUrl URL để Momo gọi thông báo kết quả (IPN).
     * @return array Trả về PayUrl và các dữ liệu liên quan.
     */
    public function createPaymentUrl(float $amount, string $orderInfo, string $returnUrl, string $notifyUrl): array
    {
        $orderId = time() . "_" . uniqid(); // Mã giao dịch duy nhất
        $requestId = time() . "_" . uniqid();
        $requestType = "captureWallet";
        $extraData = "";
        $amountInt = round($amount);
        // 1. Tạo Raw Hash và Chữ ký
       $rawHash = "accessKey={$this->accessKey}&amount={$amountInt}&extraData=&ipnUrl={$notifyUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$this->partnerCode}&redirectUrl={$returnUrl}&requestId={$requestId}&requestType={$requestType}";
        
        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        // 2. Tạo Payload
        $data = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => "YourShopName",
            'storeId'     => "YourStoreId", // Nên có Store ID riêng
            'requestId'   => $requestId,
            'amount'      => $amountInt,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $returnUrl,
            'ipnUrl'      => $notifyUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature
        ];

        // 3. Gọi API Momo (Sử dụng Laravel Http Client thay vì cURL thủ công)
        $response = Http::timeout(10)->post($this->endpoint, $data)->json();

        if (isset($response['payUrl'])) {
            return [
                'payUrl'    => $response['payUrl'],
                'orderId'   => $orderId,
                'requestId' => $requestId,
                // Lưu trạng thái tạm thời vào DB ở đây nếu cần
            ];
        }

        Log::error("Momo API Error:", ['response' => $response, 'data' => $data]);
        throw new \Exception($response['message'] ?? 'Lỗi kết nối Momo');
    }

    public function checkSignature(array $response): bool
    {
        // 1. Khai báo các biến an toàn (sử dụng ?? '' để tránh lỗi khi key không tồn tại)
        $accessKey      = $this->accessKey;
        $amount         = $response['amount'] ?? '';
        $extraData      = $response['extraData'] ?? '';
        $message        = $response['message'] ?? '';
        $orderId        = $response['orderId'] ?? '';
        $orderInfo      = $response['orderInfo'] ?? '';
        $orderType      = $response['orderType'] ?? '';
        $partnerCode    = $this->partnerCode;
        $payType        = $response['payType'] ?? '';
        $requestId      = $response['requestId'] ?? '';
        $responseTime   = $response['responseTime'] ?? '';
        $resultCode     = $response['resultCode'] ?? '';
        $transId        = $response['transId'] ?? '';

        // 2. Tái tạo RAW HASH (PHẢI DÙNG CHUỖI CỦA MỌI TRƯỜNG NHẬN ĐƯỢC)
        // Đảm bảo thứ tự A->Z và không bỏ sót trường nào
      $rawHash = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}&message={$message}&orderId={$orderId}&orderInfo={$orderInfo}&orderType={$orderType}&partnerCode={$partnerCode}&payType={$payType}&requestId={$requestId}&responseTime={$responseTime}&resultCode={$resultCode}&transId={$transId}";
      
        $expectedSignature = hash_hmac("sha256", $rawHash, $this->secretKey);
        
        // 3. Log để debug nếu chữ ký không khớp
        if ($expectedSignature !== $response['signature']) {
             Log::error('Signature Mismatch FINAL DEBUG:', [
                 'RawHash_Generated' => $rawHash,
                 'Expected_Sig' => $expectedSignature,
                 'Received_Sig' => $response['signature'],
                 'Momo_Data' => $response
             ]);
        }

        return $expectedSignature === $response['signature'];
    }

    public function storeTransactionData(string $momoOrderId, string $momoRequestId, array $orderIds, float $totalAmount, string $paymentMethod = 'e_wallet'): Transaction
    {
        return Transaction::create([
            'transaction_id' => $momoOrderId, // Lưu Momo Order ID vào transaction_id
            'order_id' => null, // NULL vì đây là giao dịch Multi-Order
            'payment_method' => $paymentMethod,
            'amount' => $totalAmount,
            'status' => 'pending',
            // Lưu Momo Request ID và danh sách Order IDs vào trường JSON
            'transaction_details' => [
                'momo_request_id' => $momoRequestId,
                'order_ids' => $orderIds,
            ],
        ]);
    }

    /**
     * Lấy danh sách Order IDs của ứng dụng từ Momo Order ID.
     */
    public function getOrderIdsByMomoOrderId(string $momoOrderId): array
    {
        $transaction = Transaction::where('transaction_id', $momoOrderId)->first();

        if ($transaction && $transaction->transaction_details && isset($transaction->transaction_details['order_ids'])) {
            // Lấy array order_ids từ trường JSON transaction_details
            return $transaction->transaction_details['order_ids'];
        }

        return [];
    }
}
