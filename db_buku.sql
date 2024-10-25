CREATE TABLE buku (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,  -- id sebagai primary key dan auto increment
    kode_buku VARCHAR(10) NOT NULL UNIQUE,  -- Kode buku sebagai kolom unik
    judul VARCHAR(255) NOT NULL,
    penerbit VARCHAR(255) NOT NULL,
    genre VARCHAR(100) NOT NULL,
    harga DECIMAL(10,2) NOT NULL
);
