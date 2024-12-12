# راهنمای استفاده از پرداخت با Stars در ربات تلگرام

## 🌟 مقدمه
در این راهنما یاد میگیریم که چگونه با استفاده از ارز **Telegram Stars** یک فاکتور توسط ربات خود برای کاربر ارسال کنیم و پس از پرداخت موفق، به کاربر پیام ارسال شود که موفق بودن پرداخت را تأیید می‌کند.

---

## 💳 ارسال فاکتور
با استفاده از فانکشن **sendInvoice** می‌توانیم یک فاکتور به کاربر ارسال کنیم.

```php
function sendInvoice($chat_id, $title, $description, $payload, $currency, $amount, $provider_token) {
    $data = [
        'chat_id' => $chat_id,
        'title' => $title,
        'description' => $description,
        'payload' => $payload,
        'provider_token' => $provider_token,
        'currency' => $currency,
        'prices' => [
            ['label' => $title, 'amount' => $amount]
        ]
    ];

    return telegramRequest('sendInvoice', $data);
}
```

### توضیح پارامترها:
- **chat_id:** آیدی عددی کاربری که فاکتور به آن ارسال می‌شود.
- **title:** عنوان فاکتور.
- **description:** توضیحات محصول یا خدمات.
- **payload:** یک شناسه دلخواه که بعدا بتوانید فاکتور را پیدا کنید
- **provider_token:** این پارامتر یک رشته خالی باید باشد
- **currency:** نوع ارز (برای **XTR** از `STARS` استفاده کنید).
- **amount:** تعداد stars که کاربر باید پرداخت کند

---

## 🔀 هندل آپدیت موفقیت پرداخت
پس از پرداخت موفق، تلگرام آپدیتی با فیلد **successful_payment** ارسال می‌کند. با استفاده از کد زیر می‌توانید آن را هندل کنید:

```php
$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message']['successful_payment'])) {
    $payment = $update['message']['successful_payment'];
    $chat_id = $update['message']['chat']['id'];

    // اطلاعات پرداخت
    $currency = $payment['currency'];
    $total_amount = $payment['total_amount'];
    $payload = $payment['invoice_payload'];

    // انجام اقدامات لازم پس از پرداخت موفق
    // مثلاً ارسال پیام به کاربر
    $bot->sendMessage($chat_id, "پرداخت با موفقیت انجام شد! 
مقدار: {$total_amount} {$currency}");

    // ذخیره اطلاعات در دیتابیس یا هر چیز دیگری ...
}
```

---

## ⭐ ستاره دادن
اگر این راهنما برای شما مفید بود، لطفاً به آن ستاره بدهید و تجربه خود را با دیگران به اشتراک بگذارید..


