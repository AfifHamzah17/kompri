<?php
$conn = new mysqli('localhost', 'root', '', 'db_buku');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function format_harga($harga) {
    return number_format($harga, 0, ',', '.');
}

if (isset($_POST['tambah'])) {
    $kode_buku = $_POST['kode_buku'];
    $judul = $_POST['judul'];
    $penerbit = $_POST['penerbit'];
    $genre = $_POST['genre'];
    $harga = str_replace('.', '', $_POST['harga']);
    $harga = str_replace(',', '.', $harga);
    // Include kode_buku in the SQL query
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
        echo "success|Data berhasil dihapus!";
    } else {
        echo "error|Gagal menghapus data!";
    }
    exit;
}

$result = $conn->query("SELECT * FROM buku");
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        .notification {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background: #4caf50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            display: none;
        }
    </style>

    <script>
    $(document).ready(function() {
        function showNotification(message, status) {
            var notif = $('#notification');
            $('#notification-message').text(message);

            if (status == "success") {
                notif.removeClass("bg-red-500").addClass("bg-green-500");
            } else {
                notif.removeClass("bg-green-500").addClass("bg-red-500");
            }

            notif.removeClass("hidden").fadeIn().delay(3000).fadeOut(function() {
                $(this).addClass("hidden");
            });
        }

        <?php if (isset($_GET['msg'])): ?>
            var msg = "<?php echo $_GET['msg']; ?>";
            var result = msg.split("|");
            showNotification(result[1], result[0]);
        <?php endif; ?>

        $('.delete-btn').click(function() {
            var kode_buku = $(this).data('kode_buku');
            var confirmed = confirm('Apakah Anda yakin ingin menghapus data ini?');

            if (confirmed) {
                $.ajax({
                    url: 'index.php',
                    type: 'POST',
                    data: { delete: true, kode_buku: kode_buku },
                    success: function(response) {
                        var result = response.split("|");
                        showNotification(result[1], result[0]);
                        if (result[0] == "success") {
                            location.reload();
                        }
                    },
                    error: function() {
                        showNotification('Gagal menghapus data', 'error');
                    }
                });
            }
        });

        $('.edit-btn').click(function() {
            var kode_buku = $(this).data('kode_buku');
            var judul = $(this).data('judul');
            var penerbit = $(this).data('penerbit');
            var genre = $(this).data('genre');
            var harga = $(this).data('harga');

            $('#editModal input[name="kode_buku"]').val(kode_buku);
            $('#editModal input[name="judul"]').val(judul);
            $('#editModal input[name="penerbit"]').val(penerbit);
            $('#editModal input[name="genre"]').val(genre);
            $('#editModal input[name="harga"]').val(harga);
            $('#editModal').show();
        });

        $('.close-modal').click(function() {
            $('#editModal').hide();
        });

        $('#editForm').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: 'index.php',
                type: 'POST',
                data: $(this).serialize() + '&edit=true',
                success: function(response) {
                    var result = response.split("|");
                    showNotification(result[1], result[0]);
                    if (result[0] == "success") {
                        location.reload();
                    }
                },
                error: function() {
                    showNotification('Gagal mengubah data', 'error');
                }
            });
        });
    });
    </script>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto mt-10">
        <div class="flex">
            <div class="w-1/3 bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-4">Form Tambah Buku</h2>
                <form id="tambahForm" method="POST" action="index.php">
                    <label class="block mb-2">Kode Buku</label>
                    <input type="text" name="kode_buku" class="w-full p-2 mb-4 border border-gray-300 rounded" required>
                    
                    <label class="block mb-2">Judul Buku</label>
                    <input type="text" name="judul" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                    <label class="block mb-2">Penerbit</label>
                    <input type="text" name="penerbit" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                    <label class="block mb-2">Genre Buku</label>
                    <input type="text" name="genre" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                    <label class="block mb-2">Harga Buku (contoh: 100000)</label>
                    <input type="text" name="harga" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                    <button type="submit" name="tambah" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Tambah Data</button>
                </form>
            </div>
            <div class="w-2/3 ml-6 bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-4">Daftar Tabel Buku</h2>

            <div class="flex justify-between items-center mb-4">
                <div>
                    Show 
                    <input type="number" class="p-2 border border-gray-300 rounded">
                    entries
                </div>
                <div class="flex items-center">
                    <input type="text" placeholder="Search..." class="p-2 border border-gray-300 rounded mr-2">
                    <button class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cari</button>
                </div>
            </div>
    
                <table class="table-auto w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2">No</th>
                            <th class="px-4 py-2">Kode Buku</th>
                            <th class="px-4 py-2">Judul Buku</th>
                            <th class="px-4 py-2">Penerbit</th>
                            <th class="px-4 py-2">Genre</th>
                            <th class="px-4 py-2">Harga</th>
                            <th class="px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="border px-4 py-2"><?= $no++; ?></td>
                                <td class="border px-4 py-2"><?= $row['kode_buku']; ?></td>
                                <td class="border px-4 py-2"><?= $row['judul']; ?></td>
                                <td class="border px-4 py-2"><?= $row['penerbit']; ?></td>
                                <td class="border px-4 py-2"><?= $row['genre']; ?></td>
                                <td class="border px-4 py-2"><?= format_harga($row['harga']); ?></td>
                                <td class="border px-4 py-2">
                                    <button class="edit-btn bg-yellow-500 text-white px-2 py-1 rounded" 
                                            data-kode_buku="<?= $row['kode_buku']; ?>" 
                                            data-judul="<?= $row['judul']; ?>" 
                                            data-penerbit="<?= $row['penerbit']; ?>" 
                                            data-genre="<?= $row['genre']; ?>" 
                                            data-harga="<?= format_harga($row['harga']); ?>">Edit</button>
                                    <button class="delete-btn bg-red-500 text-white px-2 py-1 rounded" 
                                            data-kode_buku="<?= $row['kode_buku']; ?>">Hapus</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="notification" class="notification hidden">
        <span id="notification-message"></span>
    </div>

    <div id="editModal" style="display:none;">
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-lg">
                <h2 class="text-xl font-bold mb-4">Edit Buku</h2>
                <form id="editForm" method="POST" action="index.php">
                    <input type="hidden" name="kode_buku">
                    <label class="block mb-2">Judul Buku</label>
                    <input type="text" name="judul" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                    <label class="block mb-2">Penerbit</label>
                    <input type="text" name="penerbit" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                    <label class="block mb-2">Genre Buku</label>
                    <input type="text" name="genre" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                    <label class="block mb-2">Harga Buku</label>
                    <input type="text" name="harga" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                    <button type="submit" name="edit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan Perubahan</button>
                    <button type="button" class="close-modal bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Tutup</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
