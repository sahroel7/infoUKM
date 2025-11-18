<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fungsi untuk memeriksa apakah pengguna adalah admin
function isAdmin() {
    return isset($_SESSION['sebagai']) && $_SESSION['sebagai'] === 'admin';
}



require_once 'koneksi.php';
class infoUKM {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    //<!-- tambahkan disini -->
    public function insert($judul, $kategori, $tanggal_mulai, $tanggal_selesai, $nama_kontak, $hp_kontak, $link_pendaftaran){ 
        $stmt = $this->conn->prepare("INSERT INTO postingan 
        (judul, kategori, tanggal_mulai, tanggal_selesai, nama_kontak, hp_kontak, link_pendaftaran) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $judul, $kategori, $tanggal_mulai, $tanggal_selesai, $nama_kontak, $hp_kontak, $link_pendaftaran); 
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getAll(){
        $result = $this->conn->query("SELECT * FROM postingan");
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getById($id){
        $stmt = $this->conn->prepare("SELECT * FROM postingan WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }

//<!-- tambahkan disini -->
    public function update($id, $judul, $kategori, $tanggal_mulai, $tanggal_selesai, $nama_kontak, $hp_kontak, $link_pendaftaran){
        $stmt = $this->conn->prepare("UPDATE postingan SET judul = ?, kategori = ?, tanggal_mulai = ?, tanggal_selesai = ?, nama_kontak = ?, hp_kontak = ?, link_pendaftaran = ? WHERE id=?");
        $stmt->bind_param("sssssssi", $judul, $kategori, $tanggal_mulai, $tanggal_selesai, $nama_kontak, $hp_kontak, $link_pendaftaran, $id); 
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }


    public function delete($id){
        $stmt = $this->conn->prepare("DELETE FROM postingan WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
} 

if (isset($_POST['submit'])) {
    if (!isAdmin()) {
        echo "Akses ditolak. Anda bukan admin. <a href='index.php'>Kembali</a>";
        exit();
    }

    //<!-- tambahkan disini -->
    $judul = $_POST['judul'];
    $kategori = $_POST['kategori'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $nama_kontak = $_POST['nama_kontak'];
    $hp_kontak = $_POST['hp_kontak'];
    $link_pendaftaran = $_POST['link_pendaftaran'];
    
    $db = new Database();
    $conn = $db->getConnection();
    $postingan = new infoUKM($conn);

    if (empty($judul) || 
        empty($kategori) || 
        empty($tanggal_mulai) || 
        empty($tanggal_selesai)
    ) {
        echo "Field wajib (Judul, Kategori, Tanggal Mulai, Tanggal Selesai) harus diisi! <a href='index.php'>Kembali</a>";
    } else {
        if ($postingan->insert($judul, $kategori, $tanggal_mulai, $tanggal_selesai, $nama_kontak, $hp_kontak, $link_pendaftaran)) { //<!-- tambahkan disini -->
            echo "Data berhasil disimpan. <a href='index.php'>Kembali</a>";
        } else {
            echo "Gagal menyimpan data. <a href='index.php'>Kembali</a>";
        }
    }
}

if (isset($_POST['update'])) {
    if (!isAdmin()) {
        echo "Akses ditolak. Anda bukan admin. <a href='index.php'>Kembali</a>";
        exit();
    }

    //<!-- tambahkan disini -->
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $kategori = $_POST['kategori'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $nama_kontak = $_POST['nama_kontak'];
    $hp_kontak = $_POST['hp_kontak'];
    $link_pendaftaran = $_POST['link_pendaftaran'];

    $db = new Database();
    $conn = $db->getConnection();
    $postingan = new infoUKM($conn);

    if ($postingan->update($id, $judul, $kategori, $tanggal_mulai, $tanggal_selesai, $nama_kontak, $hp_kontak, $link_pendaftaran)) { //<!-- tambahkan disini -->
        header("Location: index.php?status=updated");
        exit();
    } else {
        echo "Gagal mengupdate data. <a href='index.php'>Kembali</a>";
    }
    $db->close();
}

if (isset($_GET['delete'])) {
    if (!isAdmin()) {
        echo "Akses ditolak. Anda bukan admin. <a href='index.php'>Kembali</a>";
        exit();
    }

    $id = $_GET['delete'];
    $db = new Database();
    $conn = $db->getConnection();
    $postingan = new infoUKM($conn);

    if ($postingan->delete($id)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal menghapus data. <a href='index.php'>Kembali</a>";
    }

    $db->close();
}

?>