<?php
require_once '../db.php';
startSecureSession();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($action === 'register') {
        // Validasi Input
        $answer = trim($_POST['security_answer']);
        if(empty($answer)) {
            $error = "Jawaban keamanan wajib diisi.";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Email sudah terdaftar.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                // Simpan jawaban dalam huruf kecil agar tidak sensitif huruf besar/kecil
                $ans_clean = strtolower($answer); 
                $username = explode('@', $email)[0];
                
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, security_answer) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$username, $email, $hash, $ans_clean])) {
                    $success = "Akun dibuat! Silakan login.";
                }
            }
        }
    } elseif ($action === 'login') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Email atau password salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - SkyGoal</title>
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
        
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-barca-blue rounded-2xl flex items-center justify-center text-barca-gold text-3xl mx-auto mb-4 shadow-lg transform rotate-3 hover:rotate-0 transition-all duration-500">
                <i class="fas fa-futbol"></i>
            </div>
            <h1 class="text-3xl font-black text-barca-blue tracking-tight">Sky<span class="text-barca-gold">Goal</span>.</h1>
            <p class="text-gray-400 text-sm font-bold mt-1 uppercase tracking-widest">Admin Access</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-xl mb-4 text-sm text-center font-bold border border-red-100 flex items-center justify-center gap-2 animate-bounce">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="bg-green-50 text-green-600 p-3 rounded-xl mb-4 text-sm text-center font-bold border border-green-100 flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="authForm" class="space-y-4">
            <input type="hidden" name="action" id="formAction" value="login">
            
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Email Address</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-4 top-3.5 text-gray-400"></i>
                    <input type="email" name="email" required class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-100 focus:border-barca-blue focus:ring-0 outline-none font-bold text-barca-dark transition-all bg-gray-50 focus:bg-white" placeholder="admin@skygoal.com">
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1 ml-1">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-3.5 text-gray-400"></i>
                    <input type="password" name="password" required class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-100 focus:border-barca-blue focus:ring-0 outline-none font-bold text-barca-dark transition-all bg-gray-50 focus:bg-white" placeholder="••••••••">
                </div>
            </div>

            <div id="securityField" class="hidden transition-all duration-300">
                <label class="block text-xs font-bold text-barca-red uppercase mb-1 ml-1">Pertanyaan Keamanan</label>
                <div class="relative">
                    <i class="fas fa-shield-alt absolute left-4 top-3.5 text-barca-red"></i>
                    <input type="text" name="security_answer" id="secInput" class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-barca-red/20 focus:border-barca-red focus:ring-0 outline-none font-bold text-barca-dark transition-all bg-red-50/50 focus:bg-white" placeholder="Apa klub bola favorit Anda?">
                </div>
                <p class="text-[10px] text-gray-400 mt-1 ml-1">*Digunakan untuk reset password.</p>
            </div>

            <div class="flex flex-col gap-3 mt-6">
                <button type="submit" class="w-full bg-barca-blue text-white font-bold py-3.5 rounded-xl hover:bg-barca-red transition-all shadow-lg shadow-barca-blue/30 transform hover:-translate-y-1 text-sm uppercase tracking-wider">
                    <span id="btnText">Masuk</span>
                </button>
                <a href="forgot_password.php" id="forgotLink" class="text-xs text-center text-barca-blue font-bold hover:underline">Lupa Password?</a>
            </div>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-100 text-center">
            <button onclick="toggleMode()" class="text-gray-400 hover:text-barca-blue text-xs font-bold transition flex items-center justify-center gap-2 mx-auto group">
                <i class="fas fa-user-plus group-hover:scale-110 transition"></i>
                <span id="toggleText">Belum punya akun? Daftar</span>
            </button>
        </div>
    </div>

    <script>
        let isLogin = true;
        function toggleMode() {
            isLogin = !isLogin;
            const secField = document.getElementById('securityField');
            const secInput = document.getElementById('secInput');
            const forgotLink = document.getElementById('forgotLink');

            document.getElementById('formAction').value = isLogin ? 'login' : 'register';
            document.getElementById('btnText').innerText = isLogin ? 'Masuk Dashboard' : 'Buat Akun Baru';
            document.getElementById('toggleText').innerText = isLogin ? 'Belum punya akun? Daftar' : 'Sudah punya akun? Login';
            
            if(!isLogin) {
                secField.classList.remove('hidden');
                secInput.setAttribute('required', 'true');
                forgotLink.classList.add('hidden');
            } else {
                secField.classList.add('hidden');
                secInput.removeAttribute('required');
                forgotLink.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>