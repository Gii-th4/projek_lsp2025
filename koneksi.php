<?php

use Dom\Mysql;

session_start();

$host = "localhost"; 
$user = "root";      
$pass = "root";          
$db   = "pinjamkamera";  

// Membuat koneksi
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

//tambah barang

if(isset($_POST['addkamera'])){
    $nama_kamera = $_POST['nama_kamera'];
    $merk = $_POST['merk'];
    $stock = $_POST['stock'];

    $addtotable = mysqli_query($koneksi, "insert into stock (nama_kamera, merk, stock) values('$nama_kamera', '$merk', '$stock')");
    if($addtotable){
        header('location:index.php');
    }else {
        echo "Gagal";
        header('gagal');
    }
};

//tambah kamera masuk

if(isset($_POST['kameramasuk'])){
    $id_kamera = $_POST['kameranya'];
    $penerima = $_POST['penerima'];
    $quantity = $_POST['quantity'];

    $cekstocksekarang = mysqli_query($koneksi, "SELECT * FROM stock WHERE id_kamera = '$id_kamera'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahstockskrgdgquantity = $stocksekarang + $quantity;

    $addtomasuk = mysqli_query($koneksi, "insert into masuk (id_kamera, keterangan, quantity) values('$id_kamera', '$penerima' , '$quantity')");
    $update_stock = mysqli_query($koneksi, "UPDATE stock SET stock = '$tambahstockskrgdgquantity' WHERE id_kamera = '$id_kamera'");
    if($addtomasuk&&$update_stock){
        header('location:index.php');
    }else {
        echo "Gagal";
        header('gagal');
    }
};

//tambah kamera keluar

if(isset($_POST['kamerakeluar'])){
    $id_kamera = $_POST['kameranya'];
    $penerima = $_POST['penerima'];
    $quantity = $_POST['quantity'];

    $cekstocksekarang = mysqli_query($koneksi, "SELECT * FROM stock WHERE id_kamera = '$id_kamera'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahstockskrgdgquantity = $stocksekarang - $quantity;

    $addtokeluar = mysqli_query($koneksi, "insert into keluar (id_kamera, penerima, quantity) values('$id_kamera', '$penerima' , '$quantity')");
    $update_stock = mysqli_query($koneksi, "UPDATE stock SET stock = '$tambahstockskrgdgquantity' WHERE id_kamera = '$id_kamera'");
    if($addtokeluar&&$update_stock){
        header('location:keluar.php');
    }else {
        echo "Gagal";
        header('location:keluar.php');
    }
};

?>