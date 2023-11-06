<?php

$branchcode = "int";
$key = "GRN-2023-11-06-001";

// Data yang akan diupdate
$request_data = [
    'branchcode' => 'int',
    'received_date' => '2023-11-03',
    'id_purchase' => 15,
    'received_by' => 'demang',
    'description' => 'lengkap lur',
    'grand_total' => 88392,
    'items' => [
        [
            "id_product" => "7" ,
            "id_unit" => "rim" ,
            "qty" => 200 ,   
            "bonusqty" => 3 ,   
            "unitbonusqty" => "rim" ,   
            "price" => 10000 ,
            "total"=> 2000000,
            "discount" => 10000,
            "sub_total" =>1990000,
            
        ],
        [
            "id_product" => "11" ,
            "id_unit" => "rim" ,
            "qty" => 400 ,   
            "bonusqty" => 3 ,   
            "unitbonusqty" => "rim" ,   
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
