<?php

$apiUrl = 'http://127.0.0.1:8000/api/purchases';

// Data yang akan dikirim sebagai POST request
$data = [
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
    'items' => [
        ['product_name' => 'Product 3', 'quantity' => 8],
    ]
];

// Mengatur opsi konteks stream untuk POST
$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data),
    ],
];

$context = stream_context_create($options);

// Mengirim permintaan POST dan mendapatkan respons
$response = file_get_contents($apiUrl, false, $context);

// Mengonversi respons JSON menjadi array
$result = json_decode($response);

print_r($result);
// echo $result;

// Memeriksa apakah permintaan berhasil
// if ($result && isset($result['status']) && $result['status'] === 'success') {
//     echo "Data berh  asil disimpan!";
// } else {
//     echo "Gagal menyimpan data.";
// }

?>
