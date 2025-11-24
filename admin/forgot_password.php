<?php
require_once '../db.php';
startSecureSession();

$step = 1; // Default step: Input Email
$error = '';
$success = '';

// Proses Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // STEP 1: Cek Email
    if (isset($_POST['check_email'])) {
        $email = trim($_POST['email']);
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $_SESSION['reset_email'] = $email;
            $step = 2; // Pindah ke pertanyaan keamanan
        } else {
            $error = "Email tidak ditemukan.";
        }
    }
    
    // STEP 2: Verifikasi Jawaban
    elseif (isset($_POST['verify_answer'])) {
        $answer = strtolower(trim($_POST['security_answer']));
        $email = $_SESSION['reset_email'];
        
        $stmt = $pdo->prepare("SELECT security_answer FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $user['security_answer'] === $answer) {
            $_SESSION['reset_verified'] = true;
            $step = 3; // Pindah ke reset password
        } else {
            $error = "Jawaban salah. Coba lagi.";
            $step = 2; // Tetap di step 2
        }
    }

    // STEP 3: Update Password
    elseif (isset($_POST['reset_password'])) {
        if (isset($_SESSION['reset_verified']) && $_SESSION['reset_verified'] === true) {
            $new_pass = $_POST['new_password'];
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $email = $_SESSION['reset_email'];

            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
            if ($stmt->execute([$hash, $email])) {
                $success = "Password berhasil diubah!";
                // Bersihkan session reset
                unset($_SESSION['reset_email']);
                unset($_SESSION['reset_verified']);
                $step = 4; // Step sukses
            }
        } else {
            header("Location: forgot_password.php"); // Illegal access
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - SkyGoal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        barca: { blue: '#004D98', red: '#A50044', gold: '#EDBB00', dark: '#0e1e30', light: '#F2F4F6' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-barca-light h-screen flex items-center justify-center font-sans">

    <div class="bg-white p-10 rounded-[2rem] shadow-2xl w-full max-w-md border border-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-barca-red via-barca-gold to-barca-blue"></div>
        
        <div class="text-center mb-8">
            <h1 class="text-2xl font-black text-barca-blue tracking-tight">Reset Password</h1>
            <p class="text-gray-400 text-sm font-bold mt-1 uppercase tracking-widest">Pemulihan Akun</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-xl mb-4 text-sm text-center font-bold border border-red-100 flex items-center justify-center gap-2 animate-bounce">
                <i class="fas fa-times-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if($step === 1): ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Masukkan Email Anda</label>
                <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-barca-blue outline-none font-bold text-barca-dark" placeholder="admin@skygoal.com">
            </div>
            <button type="submit" name="check_email" class="w-full bg-barca-blue text-white font-bold py-3 rounded-xl hover:bg-barca-dark transition shadow-lg">Lanjut</button>
            <div class="text-center mt-4"><a href="login.php" class="text-xs text-barca-red font-bold hover:underline">Kembali ke Login</a></div>
        </form>
        <?php endif; ?>

        <?php if($step === 2): ?>
        <form method="POST" class="space-y-4">
            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-center mb-4">
                <p class="text-xs text-barca-blue font-bold uppercase">Pertanyaan Keamanan</p>
                <p class="text-lg font-black text-barca-dark mt-1">"Apa klub bola favorit Anda?"</p>
            </div>
            <div>
                <input type="text" name="security_answer" required class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-barca-gold outline-none font-bold text-barca-dark text-center" placeholder="Jawab di sini...">
            </div>
            <button type="submit" name="verify_answer" class="w-full bg-barca-gold text-barca-dark font-bold py-3 rounded-xl hover:bg-yellow-500 transition shadow-lg">Verifikasi</button>
            <div class="text-center mt-4"><a href="forgot_password.php" class="text-xs text-gray-400 font-bold hover:underline">Ganti Email</a></div>
        </form>
        <?php endif; ?>

        <?php if($step === 3): ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Password Baru</label>
                <input type="password" name="new_password" required class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-barca-red outline-none font-bold text-barca-dark" placeholder="••••••••">
            </div>
            <button type="submit" name="reset_password" class="w-full bg-barca-red text-white font-bold py-3 rounded-xl hover:bg-red-800 transition shadow-lg">Ubah Password</button>
        </form>
        <?php endif; ?>

        <?php if($step === 4): ?>
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 animate-pulse">
                <i class="fas fa-check"></i>
            </div>
            <h2 class="text-xl font-bold text-barca-dark mb-2">Berhasil!</h2>
            <p class="text-sm text-gray-500 mb-6">Password Anda telah diperbarui.</p>
            <a href="login.php" class="block w-full bg-barca-blue text-white font-bold py-3 rounded-xl hover:bg-barca-dark transition shadow-lg">Login Sekarang</a>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>