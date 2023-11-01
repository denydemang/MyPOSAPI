<?php
$apiUrl = 'http://127.0.0.1:8000/api/purchases';

// Data yang akan dikirim dalam permintaan POST
$postData = [
    
    "branchcode" => "int",
    "trans_date" => "2024-10-28",
    "id_user" => "6",
    "id_supplier" => "1",
    "discount" => 2000,
    "other_fee" => 1500,
    "payment_term" => "sdads",
    "ppn" => 2000,
    "total" => 15000,
    "is_credit" => 1,
];

// Menginisialisasi curl session
$ch = curl_init($apiUrl);

// Mengatur opsi curl untuk POST
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Menjalankan curl session dan mendapatkan respons
$response = curl_exec($ch);

// Menutup curl session
curl_close($ch);

// Mengonversi respons JSON menjadi array
$data = json_decode($response, true);

// // Memeriksa apakah permintaan berhasil
// if ($data === null) {
//     echo "Gagal menguraikan respons JSON.";
// } else {
//     // Proses data yang diperoleh dari API
//     print_r($data);
// }
print_r($data);

?>