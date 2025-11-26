<?php
require_once '../db.php';
startSecureSession();

if (!isset($_SESSION['user_id'])) { exit(json_encode(['status'=>'error','msg'=>'Unauthorized'])); }

$action = $_POST['action'] ?? '';

// HELPER: Upload Image
function uploadImage($fileInputName, $urlInputName, $oldImage = null) {
    // 1. Cek jika ada file yang diupload
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($_FILES[$fileInputName]['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $filename = uniqid() . '.' . $ext;
            // Pastikan folder uploads ada dan writable
            $uploadDir = '../uploads/'; 
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadDir . $filename)) {
                return 'uploads/' . $filename;
            }
        }
    }
    
    // 2. Jika tidak ada file, cek URL input
    if (!empty($_POST[$urlInputName])) {
        return $_POST[$urlInputName];
    }
    
    // 3. Jika tidak ada keduanya, gunakan gambar lama (jika edit)
    return $oldImage;
}

// 1. ADD CATEGORY
if ($action == 'add_category') {
    $name = $_POST['name'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
    $stmt->execute([$name, $slug]);
    header("Location: dashboard.php?msg=added");
    exit;
}

// 2. ADD ARTICLE (Updated with Tags & Status)
if ($action == 'add_article') {
    $title = $_POST['title'];
    $cat_id = $_POST['category_id'];
    $content = $_POST['content'];
    $tags = $_POST['tags'];
    $status = $_POST['status'];
    
    $finalImage = uploadImage('image_file', 'image_url');
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    $stmt = $pdo->prepare("INSERT INTO articles (user_id, category_id, title, slug, content, image_url, tags, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$_SESSION['user_id'], $cat_id, $title, $slug, $content, $finalImage, $tags, $status]);
    
    header("Location: dashboard.php?msg=added");
    exit;
}

// 3. EDIT ARTICLE (Updated with Tags & Status)
if ($action == 'edit_article') {
    $id = $_POST['article_id'];
    $title = $_POST['title'];
    $cat_id = $_POST['category_id'];
    $content = $_POST['content'];
    $tags = $_POST['tags'];
    $status = $_POST['status'];
    $oldImage = $_POST['old_image'];
    
    $finalImage = uploadImage('image_file', 'image_url', $oldImage);
    
    $stmt = $pdo->prepare("UPDATE articles SET title=?, category_id=?, content=?, image_url=?, tags=?, status=? WHERE id=?");
    $stmt->execute([$title, $cat_id, $content, $finalImage, $tags, $status, $id]);
    
    header("Location: dashboard.php?msg=updated");
    exit;
}

// 4. DELETE ARTICLE
if ($action == 'delete_article') {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id=?");
    $stmt->execute([$id]);
    echo json_encode(['status' => 'success']);
    exit;
}

// 5. ADD AD (Updated with Ad Mode & Script)
if ($action == 'add_ad') {
    $title = $_POST['title'];
    $type = $_POST['type'];
    $ad_mode = $_POST['ad_mode'];
    $target_url = $_POST['target_url'];
    $script_code = $_POST['script_code'];
    
    $finalImage = uploadImage('image_file', 'image_url');

    $stmt = $pdo->prepare("INSERT INTO ads (user_id, title, type, ad_mode, image_url, target_url, script_code, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())");
    $stmt->execute([$_SESSION['user_id'], $title, $type, $ad_mode, $finalImage, $target_url, $script_code]);
    
    header("Location: dashboard.php?msg=ad_added");
    exit;
}

// 6. EDIT AD (Updated with Ad Mode & Script)
if ($action == 'edit_ad') {
    $id = $_POST['ad_id'];
    $title = $_POST['title'];
    $type = $_POST['type'];
    $ad_mode = $_POST['ad_mode'];
    $target_url = $_POST['target_url'];
    $script_code = $_POST['script_code'];
    $oldImage = $_POST['old_image'];
    
    $finalImage = uploadImage('image_file', 'image_url', $oldImage);
    
    $stmt = $pdo->prepare("UPDATE ads SET title=?, type=?, ad_mode=?, image_url=?, target_url=?, script_code=? WHERE id=?");
    $stmt->execute([$title, $type, $ad_mode, $finalImage, $target_url, $script_code, $id]);
    
    header("Location: dashboard.php?msg=ad_updated");
    exit;
}

// 7. TOGGLE AD STATUS
if ($action == 'toggle_ad_status') {
    $id = $_POST['id'];
    $newStatus = $_POST['current_status'] ? 0 : 1;
    $pdo->prepare("UPDATE ads SET is_active=? WHERE id=?")->execute([$newStatus, $id]);
    echo json_encode(['status' => 'success']);
    exit;
}

// 8. DELETE AD
if ($action == 'delete_ad') {
    $id = $_POST['id'];
    $pdo->prepare("DELETE FROM ads WHERE id=?")->execute([$id]);
    echo json_encode(['status' => 'success']);
    exit;
}
?>