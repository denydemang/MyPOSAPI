<?php

$branchcode = "int";
$key = "GRN-2023-11-03-003";

// Data yang akan diupdate
$request_data = [
    'branchcode' => 'int',
    'received_date' => '2023-11-03',
    'id_purchase' => 16,
    'received_by' => 'demang',
    'description' => 'lengkap lur',
    'grand_total' => 88392,
    'items' => [
        [
            "id_product" => "7",
            "id_unit" => "pcs",
            "qty" => 20,
            "bonusqty" => 2,
            "price" => 9999,
            "sub_total" => 9999,
            
        ],
        [
            "id_product" => "3",
            "id_unit" => "pcs",
            "qty" => 20,
            "bonusqty" => 22,
            "price" => 9999,
            "sub_total" => 9999,
            
        ],
        [
            "id_product" => "8",
            "id_unit" => "pcs",
            "qty" => 10,
            "price" => 9999,
            "bonusqty" => 11,
            "sub_total" => 9999,
            
        ]
    ],
];

// Mengirim permintaan PUT ke API
$api_url = "http://127.0.0.1:8000/api/grns/{$branchcode}/{$key}";
$ch = curl_init($api_url);

// Mengatur opsi permintaan
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
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
