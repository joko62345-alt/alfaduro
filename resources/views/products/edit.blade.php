<?php
$pdo = new PDO(
    'mysql:host=localhost;dbname=tokoku;charset=utf8mb4',
    'user', 'password',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);

$keyword  = 'Laptop';      // dari input pengguna
$kategori = 'Elektronik';  // dari pilihan kategori
$page     = 1;
$perPage  = 5;
$offset   = ($page - 1) * $perPage;

$sql = "SELECT id, nama_produk, kategori, harga, stok
        FROM produk
        WHERE nama_produk LIKE :keyword
          AND kategori = :kategori
        ORDER BY harga DESC, id ASC
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':keyword',  '%' . $keyword . '%', PDO::PARAM_STR);
$stmt->bindValue(':kategori', $kategori,            PDO::PARAM_STR);
$stmt->bindValue(':limit',    $perPage,             PDO::PARAM_INT);
$stmt->bindValue(':offset',   $offset,              PDO::PARAM_INT);
$stmt->execute();

$hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);