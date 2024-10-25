<?php if (isset($_POST['tambah'])) {
    $kode_buku = $_POST['kode_buku'];
    $judul = $_POST['judul'];
    $penerbit = $_POST['penerbit'];
    $genre = $_POST['genre'];
    $harga = str_replace('.', '', $_POST['harga']);
    $harga = str_replace(',', '.', $harga);
    $sql = "INSERT INTO buku (kode_buku, judul, penerbit, genre, harga) VALUES ('$kode_buku', '$judul', '$penerbit', '$genre', '$harga')";
    if ($conn->query($sql)) {
        header("Location: index.php?msg=success|Data berhasil ditambahkan!");
        exit;
    } else {
        echo "error|Gagal menambahkan data!";
    }
    exit;
}

if (isset($_POST['edit'])) {
    $kode_buku = $_POST['kode_buku'];
    $judul = $_POST['judul'];
    $penerbit = $_POST['penerbit'];
    $genre = $_POST['genre'];
    $harga = str_replace('.', '', $_POST['harga']);
    $harga = str_replace(',', '.', $harga);
    $sql = "UPDATE buku SET judul='$judul', penerbit='$penerbit', genre='$genre', harga='$harga' WHERE kode_buku='$kode_buku'";
    if ($conn->query($sql)) {
        echo "success|Data berhasil diubah!";
    } else {
        echo "error|Gagal mengubah data!";
    }
    exit;
}

if (isset($_POST['delete'])) {
    $kode_buku = $_POST['kode_buku'];
    $sql = "DELETE FROM buku WHERE kode_buku = '$kode_buku'";
    
    if ($conn->query($sql)) {
        header("Location: index.php?msg=success|Data berhasil dihapus!");
        exit;
    } else {
        echo "error|Gagal menghapus data!";
    }
    exit;
}
?>