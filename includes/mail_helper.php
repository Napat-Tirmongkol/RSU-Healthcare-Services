<?php
/**
 * includes/mail_helper.php
 * ระบบส่งอีเมลแจ้งเตือนการจอง (Confirmation, Reschedule, Cancellation)
 */

if (!function_exists('get_secrets')) {
    function get_secrets() {
        $path = __DIR__ . '/../config/secrets.php';
        return file_exists($path) ? require $path : [];
    }
}

/**
 * 📧 ฟังก์ชันหลักสำหรับส่งอีเมล
 * @param string $to อีเมลผู้รับ
 * @param string $subject หัวข้ออีเมล
 * @param string $body เนื้อหา HTML
 * @return bool
 */
function send_campaign_email(string $to, string $subject, string $body): bool {
    if (empty($to)) return false;

    $secrets = get_secrets();
    $host = $secrets['SMTP_HOST'] ?? '';
    
    // ถ้าไม่ได้ตั้งค่า SMTP ให้ใช้ php mail() เป็น fallback (แต่อาจจะลงถังขยะหรือส่งไม่ไปถ้า server ไม่ได้ config ไว้)
    if (empty($host)) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . ($secrets['SMTP_FROM_NAME'] ?? 'RSU Healthcare') . " <" . ($secrets['SMTP_FROM_EMAIL'] ?? 'no-reply@rsu.ac.th') . ">" . "\r\n";
        return mail($to, $subject, $body, $headers);
    }

    // ในกรณีที่มี PHPMailer (แนะนำให้ติดตั้งผ่าน Composer)
    // สามารถเพิ่ม code ตรงนี้เพื่อใช้การส่งผ่าน SMTP ที่มั่นคงกว่าได้
    // สำหรับเครื่อง User ผมจะเตรียมเป็น Skeleton ไว้ให้หากต้องการต่อยอด
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . $secrets['SMTP_FROM_NAME'] . " <" . $secrets['SMTP_FROM_EMAIL'] . ">" . "\r\n";
    
    return mail($to, $subject, $body, $headers);
}

/**
 * 🎨 สร้าง HTML Template สำหรับอีเมล
 */
function get_email_template(string $title, string $message, array $details = []): string {
    $detailRows = '';
    foreach ($details as $label => $value) {
        $detailRows .= "<tr>
            <td style='padding: 10px; border-bottom: 1px solid #eee; font-weight: bold; color: #555;'>{$label}</td>
            <td style='padding: 10px; border-bottom: 1px solid #eee; color: #333;'>{$value}</td>
        </tr>";
    }

    return "
    <!DOCTYPE html>
    <html lang='th'>
    <head>
        <meta charset='UTF-8'>
        <style>
            .container { max-width: 600px; margin: 0 auto; font-family: 'Prompt', sans-serif, Tahoma; line-height: 1.6; color: #333; }
            .header { background: #0052CC; color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { padding: 30px; border: 1px solid #eee; border-top: none; background: #fff; }
            .footer { padding: 20px; text-align: center; font-size: 12px; color: #999; }
            .btn { display: inline-block; padding: 12px 25px; background: #0052CC; color: white !important; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1 style='margin:0;'>RSU Healthcare Services</h1>
            </div>
            <div class='content'>
                <h2 style='color: #0052CC;'>{$title}</h2>
                <p>{$message}</p>
                <table>
                    {$detailRows}
                </table>
                <p style='margin-top:20px;'>ขอบคุณที่ใช้บริการของเรา</p>
            </div>
            <div class='footer'>
                <p>© 2026 มหาวิทยาลัยรังสิต | คลินิกเวชกรรม มหาวิทยาลัยรังสิต</p>
                <p>อีเมลฉบับนี้เป็นการแจ้งเตือนอัตโนมัติ กรุณาอย่าตอบกลับ</p>
            </div>
        </div>
    </body>
    </html>";
}

/**
 * 🚀 ฟังก์ชันส่งอีเมลตามประเภท (Notification Types)
 */
function notify_booking_status(string $to, string $type, array $data): bool {
    $subject = "";
    $title = "";
    $message = "";
    $details = [
        "กิจกรรม" => $data['campaign_title'] ?? '-',
        "วันที่" => $data['date'] ?? '-',
        "เวลา" => $data['time'] ?? '-'
    ];

    switch ($type) {
        case 'confirmation':
            $subject = "ยืนยันการเริ่มจองกิจกรรม: " . ($data['campaign_title'] ?? '');
            $title = "ยืนยันการจองสำเร็จ!";
            $message = "คุณได้ลงทะเบียนเข้าร่วมกิจกรรมเรียบร้อยแล้ว กรุณาตรวจสอบรายละเอียดด้านล่าง:";
            break;
            
        case 'cancelled_by_user':
            $subject = "ยกเลิกการจองกิจกรรม: " . ($data['campaign_title'] ?? '');
            $title = "ยกเลิกการจองแล้ว";
            $message = "คุณได้ทำการยกเลิกการจองกิจกรรมต่อไปนี้เมื่อสักครู่:";
            break;

        case 'cancelled_by_admin':
            $subject = "แจ้งเปลี่ยนรอบเวลาการจอง: " . ($data['campaign_title'] ?? '');
            $title = "ขออภัย! มีการเลื่อนรอบกิจกรรม";
            $message = "เนื่องจากมีผู้จองเต็มจำนวนหรือมีเหตุจำเป็น เจ้าหน้าที่จึงขอยกเลิกคิวเดิมของคุณ เพื่อให้คุณเลือกจองรอบเวลาใหม่ที่สะดวกอีกครั้ง";
            $details["สถานะปัจจุบัน"] = "คิวเดิมถูกยกเลิก (กรุณาจองใหม่)";
            break;
            
        case 'approved':
            $subject = "การจองของคุณได้รับการอนุมัติ: " . ($data['campaign_title'] ?? '');
            $title = "การจองได้รับการอนุมัติแล้ว!";
            $message = "เจ้าหน้าที่ได้อนุมัติคิวการจองของคุณเรียบร้อยแล้ว กรุณาเตรียมตัวเข้าร่วมตามวันและเวลาที่กำหนด";
            break;

        default:
            return false;
    }

    $body = get_email_template($title, $message, $details);
    return send_campaign_email($to, $subject, $body);
}
