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

if (isset($_POST['addkamera'])) {
    $nama_kamera = $_POST['nama_kamera'];
    $merk = $_POST['merk'];
    $stock = $_POST['stock'];

    $addtotable = mysqli_query($koneksi, "insert into stock (nama_kamera, merk, stock) values('$nama_kamera', '$merk', '$stock')");
    if ($addtotable) {
        header('location:index.php');
    } else {
        echo "Gagal";
        header('gagal');
    }
};

//tambah kamera masuk

if (isset($_POST['kameramasuk'])) {
    $id_kamera = $_POST['kameranya'];
    $penerima = $_POST['penerima'];
    $quantity = $_POST['quantity'];

    $cekstocksekarang = mysqli_query($koneksi, "SELECT * FROM stock WHERE id_kamera = '$id_kamera'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahstockskrgdgquantity = $stocksekarang + $quantity;

    $addtomasuk = mysqli_query($koneksi, "insert into masuk (id_kamera, keterangan, quantity) values('$id_kamera', '$penerima' , '$quantity')");
    $update_stock = mysqli_query($koneksi, "UPDATE stock SET stock = '$tambahstockskrgdgquantity' WHERE id_kamera = '$id_kamera'");
    if ($addtomasuk && $update_stock) {
        header('location:index.php');
    } else {
        echo "Gagal";
        header('gagal');
    }
};

//tambah kamera keluar

if (isset($_POST['kamerakeluar'])) {
    $id_kamera = $_POST['kameranya'];
    $penerima = $_POST['penerima'];
    $quantity = $_POST['quantity'];

    $cekstocksekarang = mysqli_query($koneksi, "SELECT * FROM stock WHERE id_kamera = '$id_kamera'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);

    $stocksekarang = $ambildatanya['stock'];
    $tambahstockskrgdgquantity = $stocksekarang - $quantity;

    $addtokeluar = mysqli_query($koneksi, "insert into keluar (id_kamera, penerima, quantity) values('$id_kamera', '$penerima' , '$quantity')");
    $update_stock = mysqli_query($koneksi, "UPDATE stock SET stock = '$tambahstockskrgdgquantity' WHERE id_kamera = '$id_kamera'");
    if ($addtokeluar && $update_stock) {
        header('location:keluar.php');
    } else {
        echo "Gagal";
        header('location:keluar.php');
    }
};

//update info barang
if (isset($_POST['updatebarang'])) {
    $id_kamera  = $_POST['idb'];
    $nama_kamera = $_POST['nama_kamera'];
    $merk = $_POST['merk'];

    $update = mysqli_query($koneksi, "update stock set nama_kamera='$nama_kamera', merk='$merk' where id_kamera ='$id_kamera'");
    if ($update) {
        header('location:index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');
    };
};

//hapus barang dari stock
if (isset($_POST['hapusbarang'])) {
    $idb = $_POST['idb'];

    $hapus = mysqli_query($koneksi, "delete from stock where id_kamera='$idb'");
    if ($hapus) {
        header('location:index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');
    };
};

//ubah data barang masuk
if(isset($_POST['updatebarangmasuk'])){
    $idb = $_POST['idb'];
    $idm = $_POST['idm'];
    $keterangan = $_POST['keterangan'];
    $quantity = $_POST['quantity'];

    $lihatstock = mysqli_query($koneksi, "SELECT * FROM stock where id_kamera='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $quantityskrg = mysqli_query($koneksi, "SELECT * FROM  masuk where id_masuk='$idm'");
    $quantityny =  mysqli_fetch_array($quantityskrg);
    $quantityskrg = $quantityny['quantity'];

    if($quantity>$quantityskrg){
        $selisih = $quantity - $quantityskrg;
        $kurangin = $stockskrg - $selisih;
        $kurangistocknya = mysqli_query($koneksi, "UPDATE stock set stock='$kurangin' WHERE id_kamera='$idb'");
        $updatenya = mysqli_query($koneksi,"UPDATE masuk set quantity='$quantity', keterangan='$keterangan' WHERE id_masuk='$idm'");
        if($kurangistocknya&&$updatenya){
            header('location:masuk.php');
        }else{
            echo "Gagal";
            header('location:masuk.php');
        }
    }else{
        $selisih = $quantityskrg - $quantity;
        $kurangin = $stockskrg + $selisih;
        $kurangistocknya = mysqli_query($koneksi, "UPDATE stock set stock='$kurangin' WHERE id_kamera='$idb'");
        $updatenya = mysqli_query($koneksi,"UPDATE masuk set quantity='$quantity', keterangan='$keterangan' WHERE id_masuk='$idm'");
        if($kurangistocknya&&$updatenya){
            header('location:masuk.php');
        }else{
            echo "Gagal";
            header('location:masuk.php');
        }
    }
}

//hapus barang masuk
if(isset($_POST['hapusbarangmasuk'])){
    $idb = $_POST['idb'];
    $quantity = $_POST['quantity'];
    $idm = $_POST['idm'];

    $getdatastock = mysqli_query($koneksi, "SELECT * FROM stock where id_kamera='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock - $quantity;


    $update = mysqli_query($koneksi, "UPDATE stock set stock='$selisih' where id_kamera='$idb'");
    $hapusdata = mysqli_query($koneksi, "DELETE from  masuk where id_masuk='$idm'");

    if($update&&$hapusdata){
        header('location:masuk.php');
    }else{
         header('location:masuk.php');
    }
}

//ubah data  barang keluar

if(isset($_POST['updatebarangkeluar'])){
    $idb = $_POST['idb'];
    $idm = $_POST['idk'];
    $penerima = $_POST['penerima'];
    $quantity = $_POST['quantity'];

    $lihatstock = mysqli_query($koneksi, "SELECT * FROM stock where id_kamera='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];

    $quantityskrg = mysqli_query($koneksi, "SELECT * FROM  keluar where id_keluar='$idk'");
    $quantityny =  mysqli_fetch_array($quantityskrg);
    $quantityskrg = $quantityny['quantity'];

    if($quantity>$quantityskrg){
        $selisih = $quantity - $quantityskrg;
        $kurangin = $stockskrg - $selisih;
        $kurangistocknya = mysqli_query($koneksi, "UPDATE stock set stock='$kurangin' WHERE id_kamera='$idb'");
        $updatenya = mysqli_query($koneksi,"UPDATE keluar set quantity='$quantity', penerima='$penerima' WHERE id_keluar='$idk '");
        if($kurangistocknya&&$updatenya){
            header('location:keluar.php');
        }else{
            echo "Gagal";
            header('location:keluar.php');
        }
    }else{
        $selisih = $quantityskrg - $quantity;
        $kurangin = $stockskrg + $selisih;
        $kurangistocknya = mysqli_query($koneksi, "UPDATE stock set stock='$kurangin' WHERE id_kamera='$idb'");
        $updatenya = mysqli_query($koneksi,"UPDATE keluar set quantity='$quantity', penerima='$penerima' WHERE id_keluar='$idk'");
        if($kurangistocknya&&$updatenya){
            header('location:keluar.php');
        }else{
            echo "Gagal";
            header('location:keluar.php');
        }
    }
}

//hapus bbarang keluar
if(isset($_POST['hapusbarangkeluar'])){
    $idb = $_POST['idb'];
    $quantity = $_POST['quantity'];
    $idk = $_POST['idm'];

    $getdatastock = mysqli_query($koneksi, "SELECT * FROM stock where id_kamera='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock +  $quantity;


    $update = mysqli_query($koneksi, "UPDATE stock set stock='$selisih' where id_kamera='$idb'");
    $hapusdata = mysqli_query($koneksi, "DELETE from  keluar where id_keluar='$idk'");

    if($update&&$hapusdata){
        header('location:keluar.php');
    }else{
         header('location:keluar.php');
    }
}


?>