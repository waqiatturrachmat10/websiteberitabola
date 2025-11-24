<?php
require_once '../db.php';
startSecureSession();

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

// Helper: Buat Slug
function createSlug($string) {
    return strtolower(preg_replace('/[^a-z0-9-]/', '-', trim($string))) . '-' . time();
}

// Helper: Upload Gambar
function handleImageUpload($fileInput, $urlInput) {
    $uploadDir = '../uploads/';
    
    // 1. Cek File Upload
    if (isset($_FILES[$fileInput]) && $_FILES[$fileInput]['error'] === UPLOAD_ERR_OK) {
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true); 
        
        $fileName = time() . '_' . basename($_FILES[$fileInput]['name']);
        if (move_uploaded_file($_FILES[$fileInput]['tmp_name'], $uploadDir . $fileName)) {
            return 'uploads/' . $fileName; // Path relatif untuk DB
        }
    }
    // 2. Cek URL
    return !empty($_POST[$urlInput]) ? $_POST[$urlInput] : 'https://via.placeholder.com/800x400';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        // --- KATEGORI ---
        if ($action === 'add_category') {
            try {
                $name = trim($_POST['name']);
                // Cek input kosong
                if (empty($name)) {
                    header("Location: dashboard.php?msg=cat_empty");
                    exit;
                }

                $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)")
                    ->execute([$name, createSlug($name)]);
                
                header("Location: dashboard.php?msg=cat_added");
                exit;

            } catch (PDOException $e) {
                // Cek apakah errornya karena Duplikat (Kode 23000)
                if ($e->getCode() == '23000') {
                    header("Location: dashboard.php?msg=cat_duplicate");
                    exit;
                } else {
                    // Error lain tetap ditampilkan (untuk debugging)
                    die("System Error: " . $e->getMessage());
                }
            }
        }

        // --- ARTIKEL: TAMBAH ---
        elseif ($action === 'add_article') {
            $img = handleImageUpload('image_file', 'image_url');
            $excerpt = substr(strip_tags($_POST['content']), 0, 150) . '...';
            
            $stmt = $pdo->prepare("INSERT INTO articles (user_id, category_id, title, slug, excerpt, content, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $_POST['category_id'], $_POST['title'], createSlug($_POST['title']), $excerpt, $_POST['content'], $img]);
            header("Location: dashboard.php?msg=article_added");
        }

        // --- ARTIKEL: EDIT ---
        elseif ($action === 'edit_article') {
            $id = $_POST['article_id'];
            $img = $_POST['old_image'];
            
            // Cek jika ada gambar baru
            if ((isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) || !empty($_POST['image_url'])) {
                $img = handleImageUpload('image_file', 'image_url');
            }

            $excerpt = substr(strip_tags($_POST['content']), 0, 150) . '...';
            $pdo->prepare("UPDATE articles SET category_id=?, title=?, excerpt=?, content=?, image_url=? WHERE id=?")
                ->execute([$_POST['category_id'], $_POST['title'], $excerpt, $_POST['content'], $img, $id]);
            header("Location: dashboard.php?msg=article_updated");
        }

        // --- ARTIKEL: HAPUS ---
        elseif ($action === 'delete_article') {
            $pdo->prepare("DELETE FROM articles WHERE id = ?")->execute([$_POST['id']]);
            echo json_encode(['status' => 'success']);
            exit;
        }

        // --- IKLAN: TAMBAH ---
        elseif ($action === 'add_ad') {
            $img = handleImageUpload('image_file', 'image_url');
            $pdo->prepare("INSERT INTO ads (title, type, image_url, target_url, is_active) VALUES (?, ?, ?, ?, 1)")
                ->execute([$_POST['title'], $_POST['type'], $img, $_POST['target_url']]);
            header("Location: dashboard.php?msg=ad_added");
        }

        // --- IKLAN: EDIT (BARU) ---
        elseif ($action === 'edit_ad') {
            $id = $_POST['ad_id'];
            $img = $_POST['old_image'];

            // Cek gambar baru
            if ((isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) || !empty($_POST['image_url'])) {
                $img = handleImageUpload('image_file', 'image_url');
            }

            $pdo->prepare("UPDATE ads SET title=?, type=?, image_url=?, target_url=? WHERE id=?")
                ->execute([$_POST['title'], $_POST['type'], $img, $_POST['target_url'], $id]);
            header("Location: dashboard.php?msg=ad_updated");
        }

        // --- IKLAN: HAPUS (BARU) ---
        elseif ($action === 'delete_ad') {
            $pdo->prepare("DELETE FROM ads WHERE id = ?")->execute([$_POST['id']]);
            echo json_encode(['status' => 'success']);
            exit;
        }

        // --- IKLAN: TOGGLE STATUS ---
        elseif ($action === 'toggle_ad_status') {
            $status = $_POST['current_status'] == 1 ? 0 : 1;
            $pdo->prepare("UPDATE ads SET is_active = ? WHERE id = ?")->execute([$status, $_POST['id']]);
            echo json_encode(['status' => 'success']);
            exit;
        }

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>