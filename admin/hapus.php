<?php
/**
 * Admin Panel - Hapus Proyek (Delete)
 * Menghapus record proyek dari MySQL sekaligus menghapus file gambar fisiknya di server
 */
require_once 'auth_check.php';
require_once '../koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        // Ambil nama file gambar sebelum menghapus record dari DB
        $stmtSelect = $pdo->prepare("SELECT gambar FROM projects WHERE id = :id LIMIT 1");
        $stmtSelect->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtSelect->execute();
        $project = $stmtSelect->fetch();

        if ($project) {
            // Hapus file fisik gambar jika ada
            if (!empty($project['gambar'])) {
                $filePath = '../assets/uploads/' . $project['gambar'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            // Hapus record dari database via Prepared Statement
            $stmtDelete = $pdo->prepare("DELETE FROM projects WHERE id = :id");
            $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDelete->execute();

            header("Location: index.php?msg=deleted");
            exit;
        }
    } catch (PDOException $e) {
        die("Gagal menghapus data: " . htmlspecialchars($e->getMessage()));
    }
}

// Redirect jika ID tidak valid
header("Location: index.php");
exit;
