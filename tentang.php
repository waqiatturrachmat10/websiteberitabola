<?php
require_once 'db.php';

// --- LOGIKA DATA (Hanya untuk Sidebar) ---
// 1. Ambil Iklan Aktif (Untuk Sidebar)
$ads_raw = $pdo->query("SELECT * FROM ads WHERE is_active = 1")->fetchAll();
$ads = []; 
foreach ($ads_raw as $ad) {
    if(($ad['ad_mode'] ?? 'image') === 'script') {
        $ad['script_code'] = html_entity_decode($ad['script_code']);
    }
    $ads[$ad['type']] = $ad;
}

// 2. Ambil Berita Populer (Untuk Sidebar)
$popular = $pdo->query("SELECT * FROM articles WHERE status='published' ORDER BY views DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - SkyGoal</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
                    colors: {
                        barca: {
                            blue: '#004D98',
                            red: '#A50044',
                            gold: '#EDBB00',
                            dark: '#0e1e30',
                            light: '#F2F4F6'
                        }
                    },
                    animation: {
                        'fade-up': 'fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    },
                    keyframes: {
                        fadeUp: { '0%': { opacity: 0, transform: 'translateY(20px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #F2F4F6; }
        .ad-script-container { display: flex; justify-content: center; align-items: center; width: 100%; overflow: hidden; margin: 10px 0; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-thumb { background: #A50044; border-radius: 10px; }
        ::-webkit-scrollbar-track { background: #004D98; }
    </style>
</head>
<body class="bg-barca-light text-barca-dark font-sans antialiased selection:bg-barca-gold selection:text-barca-blue flex flex-col min-h-screen">

    <?php include 'template/navbar.php'; ?>

    <div class="container mx-auto px-4 py-10 flex-grow">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-8 space-y-8 animate-fade-up">
                
                <div class="bg-white rounded-3xl p-8 md:p-12 shadow-xl border-t-8 border-barca-blue relative overflow-hidden">
                    
                    <div class="absolute top-0 right-0 opacity-5 pointer-events-none transform translate-x-10 -translate-y-10">
                        <i class="fas fa-users text-[150px] text-barca-blue"></i>
                    </div>

                    <div class="relative z-10 mb-8">
                        <span class="text-barca-gold font-bold tracking-widest uppercase text-xs mb-2 block">Profil Website</span>
                        <h1 class="text-3xl md:text-5xl font-black text-barca-blue mb-4">Tentang Kami</h1>
                        <p class="text-xl text-gray-500 font-medium leading-relaxed">
                            <span class="text-barca-red font-bold">SkyGoal</span> adalah portal berita sepak bola modern yang didedikasikan untuk memberikan informasi terkini, hasil pertandingan, dan ulasan mendalam dengan semangat olahraga.
                        </p>
                    </div>

                    <hr class="border-gray-100 mb-8">

                    <div class="flex gap-6 items-start mb-8 relative z-10">
                        <div class="w-12 h-12 bg-barca-red/10 rounded-xl flex items-center justify-center text-barca-red text-2xl shrink-0">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-barca-dark mb-2">Misi Kami</h2>
                            <p class="text-gray-600 leading-relaxed">
                                Menyediakan ekosistem informasi sepak bola yang ringkas, mudah diakses di berbagai perangkat, dan terus diperbarui secara real-time demi kepuasan para penggemar si kulit bundar.
                            </p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-barca-dark to-barca-blue rounded-2xl p-6 text-white relative z-10 overflow-hidden shadow-lg group">
                        <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-barca-gold blur-[50px] opacity-20 group-hover:opacity-40 transition duration-700"></div>
                        
                        <div class="flex flex-col md:flex-row items-center gap-6 relative z-10">
                            <div class="w-16 h-16 bg-white/10 backdrop-blur rounded-full flex items-center justify-center text-3xl text-barca-gold shrink-0 border border-white/20">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="text-center md:text-left">
                                <h3 class="text-lg font-bold mb-1 text-barca-gold uppercase tracking-wide">Metode Pengembangan</h3>
                                <h4 class="text-2xl font-black mb-2">Vibe Coding</h4>
                                <p class="text-white/80 text-sm leading-relaxed">
                                    Website ini dibangun dengan pendekatan unik "Vibe Coding", di mana proses pengembangan berkolaborasi secara intensif dengan kecerdasan buatan (AI) untuk mempercepat implementasi fitur, menghasilkan kode yang efisien, dan memunculkan ide-ide kreatif secara instan.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-2xl shadow-md border-b-4 border-barca-gold">
                        <div class="flex items-center gap-4">
                            <img src="https://ui-avatars.com/api/?name=Admin+Sky&background=004D98&color=fff" class="w-12 h-12 rounded-full shadow-sm">
                            <div>
                                <h4 class="font-bold text-barca-dark">Administrator</h4>
                                <p class="text-xs text-gray-500 uppercase font-bold">Chief Editor</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-md border-b-4 border-barca-gold">
                        <div class="flex items-center gap-4">
                            <img src="https://ui-avatars.com/api/?name=AI+Assistant&background=A50044&color=fff" class="w-12 h-12 rounded-full shadow-sm">
                            <div>
                                <h4 class="font-bold text-barca-dark">Gemini AI</h4>
                                <p class="text-xs text-gray-500