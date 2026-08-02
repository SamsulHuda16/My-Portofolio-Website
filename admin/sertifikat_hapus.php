<?php
/**
 * Admin Panel - Hapus Sertifikat (Delete)
 */
require_once 'auth_check.php';
require_once '../koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $stmtSelect = $pdo->prepare("SELECT gambar FROM certificates WHERE id = :id LIMIT 1");
        $stmtSelect->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtSelect->execute();
        $cert = $stmtSelect->fetch();

        if ($cert) {
            if (!empty($cert['gambar'])) {
                $filePath = '../assets/uploads/' . $cert['gambar'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $stmtDelete = $pdo->prepare("DELETE FROM certificates WHERE id = :id");
            $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDelete->execute();

            header("Location: sertifikat.php?msg=deleted");
            exit;
        }
    } catch (PDOException $e) {
        die("Gagal menghapus sertifikat: " . htmlspecialchars($e->getMessage()));
    }
}

header("Location: sertifikat.php");
exit;
