<?php

// Data yang akan dikirimkan ke API
$data = [
    'branchcode' => 'int',
    'received_date' => '2023-11-04',
    'id_purchase' => 15 ,
    'received_by' => 'demang',
    'description' => 'lengkap lur',
    'items' => [
        [
            "id_product" => "7" ,
            "id_unit" => "rim" ,
            "qty" => 200 ,   
            "bonusqty" => 100 ,   
            "unitbonusqty" => "pcs" ,   
            "price" => 10000 ,
            "total"=> 2000000,
            "discount" => 10000,
            "sub_total" =>1990000,
            
        ],
        [
            "id_product" => "11" ,
            "id_unit" => "rim" ,
            "qty" => 400 ,   
            "bonusqty" => 100 ,   
            "unitbonusqty" => "pcs" ,   
            "price" => 10000 ,
            "total"=> 4000000,
            "discount" => 10000,
            "sub_total" =>3990000,
            
        ],
        // [
        //     "id_product" => "11" ,
        //     "id_unit" => "pcs" ,
        //     "qty" => 200 ,   
        //     "bonusqty" => 100 ,   
        //     "unitbonusqty" => "pcs" ,   
        //     "price" => 1000 ,
        //     "total"=> 200000,
        //     "discount" => 1000 ,
        //     "sub_total" =>199000,
            
        // ],
        // [
        //     "id_product" => "3",
        //     "id_unit" => "pcs",
        //     "qty" => 99,
        //     "bonusqty" => 2000,
        //     "price" => 9999,
        //     "sub_total" => 9999,
            
        // ],
        // [
        //     "id_product" => "8",
        //     "id_unit" => "pcs",
        //     "qty" => 100,
        //     "price" => 9999,
        //     "bonusqty" => 2000,
        //     "sub_total" => 9999,
            
        // ]
    ],
];

// URL endpoint API
$apiUrl = 'http://127.0.0.1:8000/api/grns';

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
