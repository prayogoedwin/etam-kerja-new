<?php

use Illuminate\Support\Facades\DB;


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
