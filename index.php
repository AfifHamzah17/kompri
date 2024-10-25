<?php
$conn = new mysqli('localhost', 'root', '', 'db_buku');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function format_harga($harga) {
    return number_format($harga, 0, ',', '.');
}

include 'quicksort.php';


$entries_per_page = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;

include 'crud.php';

$search_query = '';
if (isset($_GET['search']) && strlen($_GET['search']) >= 3) {
    $search_query = $_GET['search'];
}

$result = $conn->query("SELECT * FROM buku WHERE kode_buku LIKE '%$search_query%' OR judul LIKE '%$search_query%' OR penerbit LIKE '%$search_query%' OR genre LIKE '%$search_query%'");
$data_buku = [];

while ($row = $result->fetch_assoc()) {
    $data_buku[] = $row;
}

$data_buku = quicksort($data_buku, 'kode_buku');
$data_buku = array_slice($data_buku, 0, $entries_per_page);
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

        $('#searchButton').click(function() {
            var query = $('#search').val();
            if (query.length >= 3) {
                location.href = '?entries=' + $('#entries').val() + '&search=' + query;
            } else if (query.length === 0) {
                location.href = '?entries=' + $('#entries').val();
            }
        });

        $('#entries').change(function() {
            var entries = $(this).val();
            location.href = '?entries=' + entries + '&search=' + $('#search').val();
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
                    <input type="text" placeholder="Masukkan Kode Buku" name="kode_buku" class="w-full p-2 mb-4 border border-gray-300 rounded" required>
                    
                    <label class="block mb-2">Judul Buku</label>
                    <input type="text" placeholder="Masukkan Judul Buku" name="judul" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                    <label class="block mb-2">Penerbit</label>
                    <input type="text" placeholder="Masukkan Penerbit Buku" name="penerbit" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                    <label class="block mb-2">Genre Buku</label>
                    <input type="text" placeholder="Masukkan Genre Buku" name="genre" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                    <label class="block mb-2">Harga Buku </label>
                    <input type="number" placeholder="Masukkan Harga Buku tanpa koma atau titik" name="harga" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                    <button type="submit" name="tambah" class="bg-blue-500 text-white px-4 py-2 rounded">Tambah Buku</button>
                </form>
            </div>
            
            <div class="w-2/3 ml-4">
                <h2 class="text-xl font-bold mb-4">Daftar Tabel Buku</h2>

                <div class="flex mb-4">
                    <div class="flex items-center">
                    Show&nbsp;&nbsp;&nbsp; 
                    <select id="entries" class="p-2 border border-gray-300 rounded">
                            <option value="5" <?php echo ($entries_per_page == 5) ? 'selected' : ''; ?>>5</option>
                            <option value="10" <?php echo ($entries_per_page == 10) ? 'selected' : ''; ?>>10</option>
                            <option value="20" <?php echo ($entries_per_page == 20) ? 'selected' : ''; ?>>20</option>
                    </select>
                    &nbsp;&nbsp;&nbsp;Entries
                    </div>
                    <div class="ml-auto">
                        <div class="flex">
                            <input id="search" type="text" placeholder="Cari Buku" value="<?php echo htmlspecialchars($search_query); ?>" class="p-2 border border-gray-300 rounded-l">
                            <button id="searchButton" class="bg-blue-500 text-white px-4 py-2 rounded-r">
                                🔍
                            </button>
                        </div>
                    </div>
                </div>

                <table class="table-auto w-full bg-white shadow-lg rounded">
                    <thead>
                        <tr>
                            <th class="border px-4 py-2">NO</th>    
                            <th class="border px-4 py-2">Kode Buku</th>
                            <th class="border px-4 py-2">Judul Buku</th>
                            <th class="border px-4 py-2">Penerbit</th>
                            <th class="border px-4 py-2">Genre Buku</th>
                            <th class="border px-4 py-2">Harga Buku</th>
                            <th class="border px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($data_buku as $buku): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo $no++; ?></td>    
                            <td class="border px-4 py-2"><?php echo $buku['kode_buku']; ?></td>
                            <td class="border px-4 py-2"><?php echo $buku['judul']; ?></td>
                            <td class="border px-4 py-2"><?php echo $buku['penerbit']; ?></td>
                            <td class="border px-4 py-2"><?php echo $buku['genre']; ?></td>
                            <td class="border px-4 py-2"><?php echo format_harga($buku['harga']); ?></td>
                            <td class="border px-4 py-2">
                                <button class="bg-yellow-500 text-white px-4 py-1 rounded edit-btn" data-kode_buku="<?php echo $buku['kode_buku']; ?>" data-judul="<?php echo $buku['judul']; ?>" data-penerbit="<?php echo $buku['penerbit']; ?>" data-genre="<?php echo $buku['genre']; ?>" data-harga="<?php echo $buku['harga']; ?>">Edit</button>
                                <button class="bg-red-500 text-white px-4 py-1 rounded delete-btn" data-kode_buku="<?php echo $buku['kode_buku']; ?>">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-white p-6 rounded shadow-lg">
            <h2 class="text-xl font-bold mb-4">Edit Buku</h2>
            <form id="editForm">
                <input type="hidden" name="kode_buku">

                <label class="block mb-2">Judul Buku</label>
                <input type="text" placeholder="Masukkan Judul Buku" name="judul" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                <label class="block mb-2">Penerbit</label>
                <input type="text" placeholder="Masukkan Penerbit Buku" name="penerbit" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                <label class="block mb-2">Genre Buku</label>
                <input type="text" placeholder="Masukkan Genre Buku" name="genre" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                <label class="block mb-2">Harga Buku</label>
                <input type="text" placeholder="Masukkan Harga Buku" name="harga" class="w-full p-2 mb-4 border border-gray-300 rounded" required>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan Perubahan</button>
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded close-modal">Batal</button>
            </form>
        </div>
    </div>

    <div id="notification" class="notification hidden">
        <span id="notification-message"></span>
    </div>
</body>
</html>