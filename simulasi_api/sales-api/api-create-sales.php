<?php

// Data yang akan dikirimkan ke API
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
            "qty" => 25000,
            "price" => 9999,
            "discount" => 0,
            "sub_total" => 900000,
            
        ],
        [
            "id_product" => "7",
            "id_unit" => "pcs",
            "qty" => 5000,
            "price" => 34141,
            "discount" => 0,
            "sub_total" => 9999,
            
        ],
        [
            "id_product" => "7",
            "id_unit" => "pcs",
            "qty" => 40000,
            "price" => 34141,
            "discount" => 0,
            "sub_total" => 9999,
            
        ],
    ],
];

// URL endpoint API
$apiUrl = 'http://127.0.0.1:8000/api/sales';

// Inisialisasi cURL
$ch = curl_init($apiUrl);

// Set opsi cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
]);

// Eksekusi cURL dan dapatkan respons
$response = curl_exec($ch);

// Periksa apakah ada kesalahan
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
}

// Tutup koneksi cURL
curl_close($ch);

// Tampilkan respons dari API
echo json_encode(json_decode($response), JSON_PRETTY_PRINT);
