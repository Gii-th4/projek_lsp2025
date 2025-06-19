<?php

use Dom\Mysql;

session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "kamera_pinjam";

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

    if (isset($_POST['addkamera'])) {
    $nama_kamera = $_POST['nama_kamera'];
    $merk = $_POST['merk'];
    $stock = $_POST['stock'];

    // Cek apakah file diupload
    if ($_FILES['foto']['error'] === 0) {
        $foto = $_FILES['foto']['name'];
        $tmp = $_FILES['foto']['tmp_name'];
        $folder = 'uploads/';
        $foto_baru = time() . '-' . $foto; // Biar nama file unik

        // Upload file ke folder
        if (move_uploaded_file($tmp, $folder . $foto_baru)) {
            // Simpan nama file ke database
            $addtotable = mysqli_query($koneksi, "INSERT INTO stock (nama_kamera, merk, stock, foto) VALUES('$nama_kamera', '$merk', '$stock', '$foto_baru')");
        } else {
            echo "Upload foto gagal";
            exit;
        }
    } else {
        // Jika tidak ada foto, simpan tanpa gambar
        $addtotable = mysqli_query($koneksi, "INSERT INTO stock (nama_kamera, merk, stock) VALUES('$nama_kamera', '$merk', '$stock')");
    }

    if ($addtotable) {
        header('location:index.php');
    } else {
        echo "Gagal";
        exit;
    }
}





    // $addtotable = mysqli_query($koneksi, "insert into stock (nama_kamera, merk, stock) values('$nama_kamera', '$merk', '$stock')");
    // if ($addtotable) {
    //     header('location:index.php');
    // } else {
    //     echo "Gagal";
    //     header('gagal');
    // }
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
        header('location:masuk.php');
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
    $barang_dipinjam = $ambildatanya['barang_dipinjam']; // ambil jumlah pinjaman sekarang
    // $tambahstockskrgdgquantity = $stocksekarang - $quantity;

    $stockbaru = $stocksekarang - $quantity;
    $dipinjam_baru = $barang_dipinjam + $quantity;  // tambah yang dipinjam,

    // Simpan ke tabel keluar (log)
    $addtokeluar = mysqli_query($koneksi, "INSERT INTO keluar (id_kamera, penerima, quantity) VALUES('$id_kamera', '$penerima', '$quantity')");

    // Update stock DAN barang_dipinjam sekaligus
    $update_stock = mysqli_query($koneksi, "UPDATE stock SET stock = '$stockbaru', barang_dipinjam = '$dipinjam_baru' WHERE id_kamera = '$id_kamera'");

    // $addtokeluar = mysqli_query($koneksi, "insert into keluar (id_kamera, penerima, quantity) values('$id_kamera', '$penerima' , '$quantity')");
    // $update_stock = mysqli_query($koneksi, "UPDATE stock SET stock = '$tambahstockskrgdgquantity' WHERE id_kamera = '$id_kamera'");
    if ($addtokeluar && $update_stock) {
        header('location:keluar.php');
    } else {
        echo "Gagal";
        header('location:keluar.php');
    }
};

//update info barang
//update info barang + update foto jika ada
if (isset($_POST['updatebarang'])) {
    $id_kamera  = $_POST['idb'];
    $nama_kamera = $_POST['nama_kamera'];
    $merk = $_POST['merk'];

    // Cek apakah ada upload foto baru
    $updateFotoSQL = '';
    if (!empty($_FILES['foto']['name'])) {
        $fotoBaru = time() . '-' . basename($_FILES["foto"]["name"]);
        $targetDir = "uploads/";
        $targetFile = $targetDir . $fotoBaru;

        // Ambil nama foto lama dari DB
        $ambilFotoLama = mysqli_query($koneksi, "SELECT foto FROM stock WHERE id_kamera='$id_kamera'");
        $fotoData = mysqli_fetch_array($ambilFotoLama);
        $fotoLama = $fotoData['foto'];

        // pndh file baru
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
            // Hps ft lama
            if (!empty($fotoLama) && file_exists("uploads/" . $fotoLama)) {
                unlink("uploads/" . $fotoLama);
            }
            $updateFotoSQL = ", foto = '$fotoBaru'";
        }
    }

    // Update data
    $update = mysqli_query($koneksi, "UPDATE stock SET nama_kamera='$nama_kamera', merk='$merk' $updateFotoSQL WHERE id_kamera ='$id_kamera'");

    if ($update) {
        header('location:index.php');
    } else {
        echo 'Gagal';
        header('location:index.php');
    };
}


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
        $kurangin = $stockskrg + $selisih;
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
        $kurangin = $stockskrg - $selisih;
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

if (isset($_POST['updatebarangkeluar'])) {
    $idb = $_POST['idb'];
    $idk = $_POST['idk'];
    $penerima = $_POST['penerima'];
    $quantity = $_POST['quantity'];

    // Ambil data stock & barang_dipinjam sekarang
    $lihatstock = mysqli_query($koneksi, "SELECT * FROM stock WHERE id_kamera='$idb'");
    $stocknya = mysqli_fetch_array($lihatstock);
    $stockskrg = $stocknya['stock'];
    $dipinjam_skrg = $stocknya['barang_dipinjam'];

    // Ambil quantity sebelumnya dari tabel keluar
    $quantityskrg = mysqli_query($koneksi, "SELECT * FROM keluar WHERE id_keluar='$idk'");
    $quantityny = mysqli_fetch_array($quantityskrg);
    $quantitylama = $quantityny['quantity'];

    // Hitung selisih
    if ($quantity > $quantitylama) {
        // Tambah pinjaman
        $selisih = $quantity - $quantitylama;
        $stok_baru = $stockskrg - $selisih;
        $dipinjam_baru = $dipinjam_skrg + $selisih;
    } else {
        // Kurangi pinjaman
        $selisih = $quantitylama - $quantity;
        $stok_baru = $stockskrg + $selisih;
        $dipinjam_baru = $dipinjam_skrg - $selisih;
    }

    // Update tabel stock dan keluar
    $updatestock = mysqli_query($koneksi, "UPDATE stock SET stock='$stok_baru', barang_dipinjam='$dipinjam_baru' WHERE id_kamera='$idb'");
    $updatekeluar = mysqli_query($koneksi, "UPDATE keluar SET quantity='$quantity', penerima='$penerima' WHERE id_keluar='$idk'");

    if ($updatestock && $updatekeluar) {
        header('location:keluar.php');
    } else {
        echo "Gagal";
        header('location:keluar.php');
    }
}


//hapus bbarang keluar
if(isset($_POST['hapusbarangkeluar'])){
    $idb = $_POST['idb'];
    $quantity = $_POST['quantity'];
    $idk = $_POST['idk'];

    $getdatastock = mysqli_query($koneksi, "SELECT * FROM stock where id_kamera='$idb'");
    $data = mysqli_fetch_array($getdatastock);
    $stock = $data['stock'];

    $selisih = $stock +  $quantity;


    $update = mysqli_query($koneksi, "UPDATE stock set stock=  stock + $quantity, barang_dipinjam = barang_dipinjam - $quantity WHERE id_kamera = '$idb'");
    $hapusdata = mysqli_query($koneksi, "DELETE from  keluar where id_keluar='$idk'");

    if($update&&$hapusdata){
        header('location:keluar.php');
    }else{
         header('location:keluar.php');
    }
}


?>  