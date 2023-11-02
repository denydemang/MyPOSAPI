<?php

$branchcode = "int";
$key = "SLS-2023-11-02-001";

// Data yang akan diupdate
$data = [
    'branchcode' => 'int',
    'trans_date' => '2023-10-31',
    'id_cust' => 3 ,
    'id_user' => 13,
    'total' => 1000,
    'discount' => 0,
    'ppn' => 0,
    'notes' => "sdadad",
    'grand_total' => 25000,
    'paid' => 12313,
    'change_amount' => 43131,
    'is_credit' => false,
    'items' => [
        [
            "id_product" => "11",
            "id_unit" => "pcs",
            "qty" => 2000,
            "price" => 9999,
            "discount" => 0,
            "sub_total" => 900000,
            
        ],
        [
            "id_product" => "7",
            "id_unit" => "pcs",
            "qty" => 2000,
            "price" => 34141,
            "discount" => 0,
            "sub_total" => 9999,
            
        ]
    ],
];

// Mengirim permintaan PUT ke API
$api_url = "http://127.0.0.1:8000/api/sales/{$branchcode}/{$key}";
$ch = curl_init($api_url);

// Mengatur opsi permintaan
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    // Tambahkan header lain jika diperlukan
]);

// Melakukan eksekusi permintaan
$response = curl_exec($ch);

echo $response;
// Menangani respon
// $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// if ($http_code == 200) {
//     echo "Data berhasil diperbarui.";
// } else {
//     echo "Gagal memperbarui data. Kode HTTP: {$http_code}\n";
//     echo "Respon: {$response}\n";
// }

// Menutup koneksi cURL
curl_close($ch);
