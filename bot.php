<?php

// توکن ربات خود را وارد کنید
define('API_KEY', 'توکن_ربات_تلگرام_شما');

// تابع برای ارسال درخواست به تلگرام
function TelegramRequest(string $method, array $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . API_KEY . '/' . $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    return json_decode($response);
}

// دریافت به روزرسانی‌ها
$update = json_decode(file_get_contents('php://input'), true);

// بررسی اگر پیام جدید است
if (isset($update['message'])) {
    $chat_id = $update['message']['chat']['id'];

    // دکمه ساده برای پرداخت
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => 'پرداخت با Stars', 'callback_data' => 'pay']
            ]
        ]
    ];

    // ارسال پیام خوشامدگویی با دکمه پرداخت
    TelegramRequest('sendMessage', [
        'chat_id' => $chat_id,
        'text' => 'سلام! برای پرداخت روی دکمه زیر کلیک کنید.',
        'reply_markup' => json_encode($keyboard)
    ]);
}

// ارسال فاکتور هنگام درخواست پرداخت
if (isset($update['callback_query']) && $update['callback_query']['data'] == 'pay') {
    $chat_id = $update['callback_query']['from']['id'];

    // پارامترهای فاکتور
    $title = 'خرید محصول';
    $description = 'خرید محصول با استفاده از Stars';
    $payload = 'unique_payload_123';
    $currency = 'STARS'; // نوع ارز
    $amount = 1000; // مقدار Stars
    $provider_token = ''; // این پارامتر برای پرداخت با Stars باید خالی باشد

    // ارسال فاکتور
    TelegramRequest('sendInvoice', [
        'chat_id' => $chat_id,
        'title' => $title,
        'description' => $description,
        'payload' => $payload,
        'currency' => $currency,
        'provider_token' => $provider_token,
        'prices' => [
            ['label' => $title, 'amount' => $amount]
        ]
    ]);
}

// بررسی پرداخت موفق
if (isset($update['message']['successful_payment'])) {
    $payment = $update['message']['successful_payment'];
    $chat_id = $update['message']['chat']['id'];

    // اطلاعات پرداخت
    $currency = $payment['currency'];
    $total_amount = $payment['total_amount'];

    // ارسال پیام تایید به کاربر
    TelegramRequest('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "پرداخت با موفقیت انجام شد! مقدار: {$total_amount} {$currency}"
    ]);
}

?>
