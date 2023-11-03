<?php

$branchcode = "int";
$key = "GRN-2023-11-03-001";

// Data yang akan didelete
// $request_data = [
//     "trans_date" => "2023-10-29",
//     "id_user" => 6,
//     "id_supplier" => 1,
//     "discount" => 0,
//     "other_fee" => 5000,
//     "payment_term" => "NET 30",
//     "ppn" => 9000,
//     "total" => 60000,
//     "is_credit" => true,
//     "items" => [
//         [
//             "id_product" => 7,
//             "id_unit" => "kg",
//             "qty" => 2000,
//             "price" => 10000,
//             "sub_total" => 90000,
//         ],
//         // Tambahkan item lain jika diperlukan
//     ],
// ];

// Mengirim permintaan PUT ke API
$api_url = "http://127.0.0.1:8000/api/grns/{$branchcode}/{$key}";
$ch = curl_init($api_url);

// Mengatur opsi permintaan
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
// curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
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
