<?php

use Illuminate\Support\Facades\DB;
use App\Models\EtamNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function getKabkota()
{
    // return DB::table('tbl_wilayah')
    // ->where('id_up', '=', null)
    // ->where('id', '=', 31)       // DKI JAKARTA
    // ->where('id', '=', 32)       // JABAR
    // ->where('id', '=', 33)       // JATENG
    // ->where('id', '=', 35)       // JATIM
    // ->where('id', '=', 34)       // DIY
    // ->where('id', '=', 14)       // RIAU
    // ->where('id', '=', 51)       // BALI
    // ->where('id', '=', 36)       // BANTEN
    // ->where('id', '=', 18)       // LAMPUNG
    // ->get();

    return DB::table('etam_kabkota')
        ->whereIn('province_id', [64])  // Daftar id
        ->get();
}


if (!function_exists('getFullCompanyType')) {
    function getFullCompanyType($abbreviation)
    {
        $types = [
            'bumd' => 'Badan Usaha Milik Daerah',
            'bumn' => 'Badan Usaha Milik Negara',
            'cv' => 'Comanditer Venotschaap',
            'firma' => 'Firma',
            'instansi' => 'Instansi',
            'kp' => 'Koperasi',
            'pt' => 'Perseroan Terbatas',
            'pp' => 'Perusahaan Perorangan',
            'po' => 'PO*',
            'yayasan' => 'Yayasan',
        ];

        return isset($types[$abbreviation]) ? $types[$abbreviation] : '';
    }
}

if (!function_exists('replacePenyediaKerja')) {
    function replacePenyediaKerja($inputString) {
        // Menggunakan str_replace untuk mengganti semua kemunculan
        return str_replace(
            ['Penyedia Kerja', 'penyedia kerja', 'penyedia_kerja'],
            ['Pemberi Kerja', 'pemberi kerja', 'pemberi_kerja'],
            $inputString
        );
    }
}

function getAgama(){
    return DB::table('etam_agama')
        ->get();
}

function getKategoriBkk(){
    return DB::table('etam_bkk_kategori')
        ->get();
}

function getRowPenyediaById($user_id){
    return DB::table('users_penyedia')
    ->where('user_id', $user_id) // Contoh filter jika diperlukan
    ->first();
}

function getRowBkkById($user_id){
    return DB::table('users_bkk')
    ->where('user_id', $user_id) // Contoh filter jika diperlukan
    ->first();
}

function getProvinsiKaltim(){
    return DB::table('etam_provinsi')
    ->where('id', 64) // KALTIM
    ->get();
}

function getProgresLamaran(){
    return DB::table('etam_progres')
    ->where('modul', 'lamaran') // Contoh filter jika diperlukan
    ->get();
}

function getRowPencariById($user_id){
    return DB::table('users_pencari')
    ->where('user_id', $user_id) // Contoh filter jika diperlukan
    ->first();
}

function getPendidikan(){
    return DB::table('etam_pendidikan')
    ->get();
}

function getMarital(){
    return DB::table('etam_marital')
    ->get();
}

function getSektor(){
    return DB::table('etam_sektor')
    ->get();
}

function getJabatan(){
    return DB::table('etam_jabatan')
    ->get();
}

function getProvinsi(){
    return DB::table('etam_provinsi')
    ->get();
}

function getStatusKerja(){
    return '';
}

function encode_url($url){
    $random1 = substr(sha1(rand()), 0, 40);
    $random2 = substr(md5(rand()), 0, 20);
    $ret = base64_encode($random1.$url.$random2);

    return strtr(
        $ret,
        array(
            '+' => '.',
            '=' => '-',
            '/' => '~'
        )
    );

}

function decode_url($url){

    $url = strtr($url, array(
        '.' => '+',
        '-' => '=',
        '~' => '/'
    ));
    
    $a = base64_decode($url);
    $hitung = strlen($a);
    $x = $hitung - 60;
    $y = $x + 20;
    $c = substr($a,-$y);
    return substr($c, 0, $x);
}

function short_encode_url($id){
    $strength = 2;
    // $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    // $input = 'ABC2';
    $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    // $input2 = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $input_length = strlen($input);
    // $input_length2 = strlen($input2);
    $random_string = '';
    $random_string2 = '';

    for($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }

    for($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string2 .= $random_character;
    }

    return  $random_string.$id.$random_string2;
}

function short_decode_url($id){
    $res = substr($id, 2, -2);

    return $res;
}

function generateOtp($length = 5)
{
    $characters = '123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    $otp = '';

    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $otp;
}

function sendWa($phone, $message)
{
    // $curl = curl_init();

    // $pesan = [
    //     "messageType" => "text",
    //     "to" => "081995241103",
    //     "body" => "Test",
    //     "delay" => 10,
    //     // "schedule" => 1665408510000
    // ];

    // curl_setopt_array($curl, array(
    //     CURLOPT_URL => 'https://api.starsender.online/api/send',
    //     CURLOPT_RETURNTRANSFER => true,
    //     CURLOPT_ENCODING => '',
    //     CURLOPT_MAXREDIRS => 10,
    //     CURLOPT_TIMEOUT => 0,
    //     CURLOPT_FOLLOWLOCATION => true,
    //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //     CURLOPT_CUSTOMREQUEST => 'POST',
    //     CURLOPT_POSTFIELDS => json_encode($pesan),
    //     CURLOPT_HTTPHEADER => array(
    //         'Content-Type: application/json',
    //         'Authorization: 39a4b748-fd7a-4e5a-a8ba-186e6e9f3562'
    //     ),
    // ));

    // $response = curl_exec($curl);

    // if (curl_errno($curl)) {
    //     $response = 'Curl error: ' . curl_error($curl);
    // }

    // curl_close($curl);
    // return $response;
    $token = env('WA_TOKEN');

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://nusagateway.com/api/send-message.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('token' => $token, 'phone' => $phone, 'message' => $message),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    // echo $response;
}

function getJenisDisabilitas(){
    return DB::table('etam_jenis_disabilitas')
    ->whereNull('deleted_at')
    ->get();
}

function getTipeLowongan(){
    return DB::table('etam_tipe_lowongan')
    ->get();
}


/**
 * =====================================================
 * ETAM NOTIFICATION HELPER
 * =====================================================
 * 
 * Helper functions untuk sistem notifikasi custom
 * - add_notif: Menambah notifikasi + kirim email/wa jika diaktifkan
 * - send_email: Kirim email via SMTP
 * - send_whatsapp: Kirim WhatsApp via Fonnte API
 */

if (!function_exists('add_notif')) {
    /**
     * Menambahkan notifikasi ke database
     * Otomatis kirim email/whatsapp jika is_email/is_whatsapp = 1
     *
     * @param int|null $from_user ID user pengirim (nullable untuk sistem)
     * @param int $to_user ID user penerima
     * @param string|null $table_target Nama tabel terkait
     * @param int|null $id_target ID record terkait
     * @param string|null $url_redirection URL redirect saat notif diklik
     * @param int $is_open Status sudah dibaca (default: 0)
     * @param int $is_email Kirim email? (1=ya, 0=tidak)
     * @param int $is_whatsapp Kirim WhatsApp? (1=ya, 0=tidak)
     * @param string|null $info Pesan/info notifikasi
     * @param string|null $created_at Waktu dibuat (default: now)
     * @param string|null $email Email penerima (wajib jika is_email=1)
     * @param string|null $no_wa No WA penerima (wajib jika is_whatsapp=1)
     * @param string|null $subject Subject untuk email/wa
     * @return EtamNotification|false
     */
    function add_notif(
        $from_user,
        $to_user,
        $table_target = null,
        $id_target = null,
        $url_redirection = null,
        $is_open = 0,
        $is_email = 0,
        $is_whatsapp = 0,
        $info = null,
        $created_at = null,
        $email = null,
        $no_wa = null,
        $subject = 'Notifikasi'
    ) {
        try {
            // Simpan ke database
            $notification = EtamNotification::create([
                'from_user' => $from_user,
                'to_user' => $to_user,
                'table_target' => $table_target,
                'id_target' => $id_target,
                'url_redirection' => $url_redirection,
                'is_open' => $is_open,
                'is_email' => $is_email,
                'is_whatsapp' => $is_whatsapp,
                'info' => $info,
                'created_at' => $created_at ?? now(),
            ]);

            // Kirim email jika is_email = 1
            if ($is_email == 1 && !empty($email)) {
                send_email($email, $subject, $info);
            }

            // Kirim WhatsApp jika is_whatsapp = 1
            if ($is_whatsapp == 1 && !empty($no_wa)) {
                send_whatsapp($no_wa, $subject, $info);
            }

            return $notification;
        } catch (\Exception $e) {
            Log::error('Add Notification Error: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('send_email')) {
    /**
     * Kirim email via SMTP
     *
     * @param string $email Email penerima
     * @param string $subject Subject email
     * @param string $text Isi email (bisa HTML)
     * @return array ['success' => bool, 'message' => string]
     */
    function send_email_bak($email, $subject, $text)
    {
        try {
            // Menggunakan PHPMailer untuk kontrol lebih baik
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host       = config('mail.mailers.smtp.host', 'mail.ezrapratama.co.id');
            $mail->SMTPAuth   = true;
            $mail->Username   = config('mail.mailers.smtp.username');
            $mail->Password   = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
            $mail->Port       = config('mail.mailers.smtp.port', 465);
            $mail->CharSet    = 'UTF-8';

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Recipients
            $mail->setFrom(
                config('mail.from.address', 'test-etamkerja@ezrapratama.co.id'),
                config('mail.from.name', 'Etam Kerja')
            );
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $text;
            $mail->AltBody = strip_tags($text);

            $mail->send();

            Log::info("Email sent to: {$email}");
            return ['success' => true, 'message' => 'Email berhasil dikirim'];
        } catch (Exception $e) {
            Log::error("Email Error: {$mail->ErrorInfo}");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    function send_email($email, $subject, $text)
    {
        try {
            // Menggunakan PHPMailer untuk kontrol lebih baik
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host       = config('mail.mailers.smtp.host', 'mail.ezrapratama.co.id');
            $mail->SMTPAuth   = true;
            $mail->Username   = config('mail.mailers.smtp.username', 'test-etamkerja@ezrapratama.co.id');
            $mail->Password   = config('mail.mailers.smtp.password', 'test-etamkerja');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
            $mail->Port       = config('mail.mailers.smtp.port', 465);
            $mail->CharSet    = 'UTF-8';

            // Recipients
            $mail->setFrom(
                config('mail.from.address', 'test-etamkerja@ezrapratama.co.id'),
                config('mail.from.name', 'Etam Kerja')
            );
            $mail->addAddress($email);

            // Email body dengan footer
            $htmlBody = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
            </head>
            <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
                <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0;">
                    ' . $text . '
                </div>
                
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; text-align: center; color: #666; font-size: 12px;">
                    <p style="margin: 0 0 10px 0;">Email ini dikirim secara otomatis oleh sistem.</p>
                    <p style="margin: 0 0 10px 0;">
                        <a href="https://etamkerja.kaltimprov.go.id/" style="color: #0066cc; text-decoration: none; font-weight: bold;">
                            https://etamkerja.kaltimprov.go.id/
                        </a>
                    </p>
                    <p style="margin: 0; color: #999;">
                        &copy; ' . date('Y') . ' Etam Kerja - Dinas Tenaga Kerja dan Transmigrasi Provinsi Kalimantan Timur
                    </p>
                </div>
            </body>
            </html>';

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags($text) . "\n\n---\nEtam Kerja\nhttps://etamkerja.kaltimprov.go.id/";

            $mail->send();

            Log::info("Email sent to: {$email}");
            return ['success' => true, 'message' => 'Email berhasil dikirim'];
        } catch (Exception $e) {
            Log::error("Email Error: {$mail->ErrorInfo}");
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

if (!function_exists('send_email_laravel')) {
    /**
     * Alternatif: Kirim email via Laravel Mail Facade
     * Gunakan ini jika sudah config .env dengan benar
     *
     * @param string $email Email penerima
     * @param string $subject Subject email
     * @param string $text Isi email
     * @return array ['success' => bool, 'message' => string]
     */
    function send_email_laravel($email, $subject, $text)
    {
        try {
            Mail::raw($text, function ($message) use ($email, $subject) {
                $message->to($email)
                    ->subject($subject);
            });

            Log::info("Email sent to: {$email}");
            return ['success' => true, 'message' => 'Email berhasil dikirim'];
        } catch (\Exception $e) {
            Log::error("Email Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

if (!function_exists('send_whatsapp')) {
    /**
     * Kirim WhatsApp via Fonnte API
     *
     * @param string $nowa Nomor WhatsApp penerima
     * @param string $subject Subject/judul pesan
     * @param string $text Isi pesan
     * @return array ['success' => bool, 'message' => string, 'response' => mixed]
     */
    function send_whatsapp($nowa, $subject, $text)
    {
        try {
            // Format pesan dengan subject
            $message = "*{$subject}*\n{$text}";

            // Bersihkan nomor WA (hapus karakter non-digit kecuali +)
            $nowa = preg_replace('/[^0-9+]/', '', $nowa);

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => [
                    'target' => $nowa,
                    'message' => $message,
                    'countryCode' => '62',
                ],
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . config('services.fonnte.token', '9atzsYp44wJostEbu9Wx')
                ],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                curl_close($curl);
                Log::error("WhatsApp Error: {$error_msg}");
                return ['success' => false, 'message' => $error_msg, 'response' => null];
            }

            curl_close($curl);

            $responseData = json_decode($response, true);

            Log::info("WhatsApp sent to: {$nowa}", ['response' => $responseData]);

            return [
                'success' => ($httpCode == 200 && isset($responseData['status']) && $responseData['status'] == true),
                'message' => $responseData['reason'] ?? 'WhatsApp terkirim',
                'response' => $responseData
            ];
        } catch (\Exception $e) {
            Log::error("WhatsApp Error: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage(), 'response' => null];
        }
    }
}

if (!function_exists('get_unread_notifications')) {
    /**
     * Ambil notifikasi yang belum dibaca untuk user tertentu
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function get_unread_notifications($userId, $limit = 10)
    {
        return EtamNotification::forUser($userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

if (!function_exists('get_notifications')) {
    /**
     * Ambil semua notifikasi untuk user tertentu
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function get_notifications($userId, $limit = 20)
    {
        return EtamNotification::forUser($userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

if (!function_exists('mark_notification_read')) {
    /**
     * Tandai notifikasi sebagai sudah dibaca
     *
     * @param int $notificationId
     * @return bool
     */
    function mark_notification_read($notificationId)
    {
        $notification = EtamNotification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        return false;
    }
}

if (!function_exists('mark_all_notifications_read')) {
    /**
     * Tandai semua notifikasi user sebagai sudah dibaca
     *
     * @param int $userId
     * @return int Jumlah notifikasi yang diupdate
     */
    function mark_all_notifications_read($userId)
    {
        return EtamNotification::forUser($userId)
            ->unread()
            ->update(['is_open' => true]);
    }
}

if (!function_exists('count_unread_notifications')) {
    /**
     * Hitung jumlah notifikasi yang belum dibaca
     *
     * @param int $userId
     * @return int
     */
    function count_unread_notifications($userId)
    {
        return EtamNotification::forUser($userId)
            ->unread()
            ->count();
    }
}
