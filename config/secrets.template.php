<?php
// config/secrets.template.php
// ไฟล์แม่แบบสำหรับการตั้งค่า Secrets
// คัดลอกไฟล์นี้ไปเป็น config/secrets.php และเติมค่าจริงให้ครบถ้วน

return [
    'LINE_LOGIN_CHANNEL_ID'               => '',
    'LINE_LOGIN_CHANNEL_SECRET'           => '',
    'LINE_LIFF_ID'                       => '',
    'LINE_MESSAGING_CHANNEL_ACCESS_TOKEN' => '',
    'LINE_MESSAGING_CHANNEL_SECRET'       => '',

    // --- Admin Panel (Google OAuth2) ---
    'GOOGLE_CLIENT_ID'                    => '',
    'GOOGLE_CLIENT_SECRET'                => '',
    'GOOGLE_REDIRECT_URI'                  => '',

    // --- Email System (SMTP) ---
    'SMTP_HOST'                           => '', // e.g., smtp.gmail.com
    'SMTP_PORT'                           => 587,
    'SMTP_USER'                           => '',
    'SMTP_PASS'                           => '',
    'SMTP_FROM_EMAIL'                     => '',
    'SMTP_FROM_NAME'                      => 'RSU Healthcare Services',
];
