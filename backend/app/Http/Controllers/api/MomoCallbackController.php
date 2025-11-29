<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\MomoPaymentService;
use Illuminate\Support\Facades\Log;

use function Laravel\Prompts\alert;

class MomoCallbackController extends Controller
{
    protected $momoService;

    public function __construct(MomoPaymentService $momoService)
    {
        $this->momoService = $momoService;
    }

    /**
     * Xử lý IPN (Instant Payment Notification) từ Momo.
     * Cập nhật trạng thái đơn hàng và Transaction.
     */
    public function handleIpn(Request $request): JsonResponse
    {
        Log::info('Momo IPN Received', $request->all());

        // 1. [SECURITY] XÁC THỰC CHỮ KÝ
        if (!$this->momoService->checkSignature($request->all())) {
            Log::warning('Momo IPN: Invalid signature detected.', $request->all());
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
        }

        $momoOrderId = $request->orderId;
        $resultCode = $request->resultCode;

        // 2. Lấy thông tin Transaction và Order IDs
        $transaction = Transaction::where('transaction_id', $momoOrderId)->first();
        if (!$transaction) {
            Log::error("Momo IPN: Transaction not found for OrderID: {$momoOrderId}");
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $orderIds = $transaction->transaction_details['order_ids'] ?? [];

        // 3. Xử lý trạng thái thanh toán
        if ($resultCode == 0) {
            if ($transaction->status !== 'success') {
                Order::whereIn('id', $orderIds)->update(['status' => 'paid']);
                $transaction->update(['status' => 'success']);
            }

            Log::info("Momo IPN: Payment successful for {$momoOrderId}. Orders updated to 'paid'.");
            return response()->json(['message' => 'Successful payment processing'], 200);
        } else {
            if ($transaction->status === 'pending') {
                Order::whereIn('id', $orderIds)->update(['status' => 'cancelled']);
                $transaction->update(['status' => 'failed']);
            }

            Log::info("Momo IPN: Payment failed for {$momoOrderId}. ResultCode: {$resultCode}");
            return response()->json(['message' => 'Payment processed, status is not successful'], 200);
        }
    }

    /**
     * Xử lý Return URL (Người dùng trở lại website) - Chỉ để chuyển hướng UX
     */
    public function handleReturn(Request $request)
    {
        // Validate chữ ký MoMo
        if (!$this->momoService->checkSignature($request->all())) {
            return redirect()->away(
                config('app.frontend_url') . '/checkout/fail?msg=invalid_signature'
            );
        }

        $resultCode = $request->resultCode;
        $orderIds = $this->momoService->getOrderIdsByMomoOrderId($request->orderId);
        $orderIdsParam = collect($orderIds)->join(',');

        $frontendDevUrl = env('FRONTEND_DEV_URL', 'http://localhost:5173');

        // Thanh toán thành công
        if ($resultCode == 0) {
            return redirect()->away($frontendDevUrl . '/checkout/success?' . http_build_query([
                'orderIds' => $orderIdsParam,
                'resultCode' => $resultCode,
                'orderId' => $request->orderId
            ]));
        }

        // Thanh toán thất bại
        return redirect()->away($frontendDevUrl . '/checkout/fail?' . http_build_query([
            'msg' => 'payment_failed',
            'resultCode' => $resultCode
        ]));
    }
}
