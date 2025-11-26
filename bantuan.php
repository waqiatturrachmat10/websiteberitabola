<?php
require_once 'db.php';

// --- HELPER FUNCTIONS (Jika belum ada di db.php, un-comment ini) ---
// function getImageUrl($url) {
//     if (empty($url)) return 'https://via.placeholder.com/800x400?text=No+Image';
//     if (filter_var($url, FILTER_VALIDATE_URL)) return $url;
//     return 'admin/' . $url;
// }

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
    <title>Pusat Bantuan - SkyGoal</title>
    
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
        
        /* Style untuk Accordion */
        .faq-content { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
        .faq-active .faq-content { max-height: 200px; }
        .faq-active .faq-icon { transform: rotate(180deg); }
    </style>
</head>
<body class="bg-barca-light text-barca-dark font-sans antialiased selection:bg-barca-gold selection:text-barca-blue flex flex-col min-h-screen">

    <?php include 'template/navbar.php'; ?>

    <div class="container mx-auto px-4 py-10 flex-grow">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-8 space-y-8 animate-fade-up">
                
                <div class="bg-white rounded-3xl p-8 md:p-12 shadow-xl border-t-8 border-barca-blue relative overflow-hidden">
                    <div class="absolute top-0 right-0 opacity-5 pointer-events-none transform translate-x-10 -translate-y-10">
                        <i class="fas fa-life-ring text-[150px] text-barca-blue"></i>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black text-barca-blue mb-4 relative z-10">Pusat Bantuan</h1>
                    <p class="text-gray-500 font-medium relative z-10">Butuh bantuan? Temukan jawaban atas pertanyaan umum tentang SkyGoal di bawah ini.</p>
                    
                    <div class="mt-6 relative z-10">
                        <input type="text" placeholder="Cari topik bantuan..." class="w-full border-2 border-gray-200 bg-gray-50 rounded-xl px-5 py-3 pl-12 font-bold text-barca-dark focus:border-barca-gold focus:outline-none transition">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <div class="flex items-center gap-3 border-l-8 border-barca-red pl-4">
                    <h2 class="text-2xl font-black text-barca-blue uppercase tracking-tight">FAQ (Tanya Jawab)</h2>
                </div>

                <div class="space-y-4">
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group cursor-pointer" onclick="toggleFaq(this)">
                        <div class="p-5 flex justify-between items-center hover:bg-gray-50 transition">
                            <h3 class="font-bold text-barca-dark group-hover:text-barca-blue transition">Bagaimana cara membuat akun?</h3>
                            <i class="fas fa-chevron-down text-gray-400 faq-icon transition-transform duration-300"></i>
                        </div>
                        <div class="faq-content bg-gray-50 px-5 text-sm text-gray-600 border-t border-gray-100">
                            <p class="py-4">Saat ini fitur pendaftaran akun hanya tersedia untuk Admin dan Penulis terpilih. Pengunjung umum dapat menikmati berita tanpa perlu mendaftar.</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group cursor-pointer" onclick="toggleFaq(this)">
                        <div class="p-5 flex justify-between items-center hover:bg-gray-50 transition">
                            <h3 class="font-bold text-barca-dark group-hover:text-barca-blue transition">Apakah berita di SkyGoal valid?</h3>
                            <i class="fas fa-chevron-down text-gray-400 faq-icon transition-transform duration-300"></i>
                        </div>
                        <div class="faq-content bg-gray-50 px-5 text-sm text-gray-600 border-t border-gray-100">
                            <p class="py-4">Ya, kami berkomitmen menyajikan berita sepak bola yang valid dan terverifikasi dari sumber terpercaya, khususnya fokus pada La Liga dan Premier League.</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group cursor-pointer" onclick="toggleFaq(this)">
                        <div class="p-5 flex justify-between items-center hover:bg-gray-50 transition">
                            <h3 class="font-bold text-barca-dark group-hover:text-barca-blue transition">Bagaimana cara melaporkan bug website?</h3>
                            <i class="fas fa-chevron-down text-gray-400 faq-icon transition-transform duration-300"></i>
                        </div>
                        <div class="faq-content bg-gray-50 px-5 text-sm text-gray-600 border-t border-gray-100">
                            <p class="py-4">Jika Anda menemukan kendala teknis, silakan hubungi tim IT kami melalui email di <strong>support@skygoal.com</strong> dengan menyertakan screenshot kendala.</p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group cursor-pointer" onclick="toggleFaq(this)">
                        <div class="p-5 flex justify-between items-center hover:bg-gray-50 transition">
                            <h3 class="font-bold text-barca-dark group-hover:text-barca-blue transition">Apakah saya bisa memasang iklan di sini?</h3>
                            <i class="fas fa-chevron-down text-gray-400 faq-icon transition-transform duration-300"></i>
                        </div>
                        <div class="faq-content bg-gray-50 px-5 text-sm text-gray-600 border-t border-gray-100">
                            <p class="py-4">Tentu! Kami menyediakan berbagai slot iklan menarik. Silakan hubungi bagian marketing kami untuk penawaran harga terbaik.</p>
                        </div>
                    </div>

                </div>

                <div class="bg-gradient-to-r from-barca-blue to-barca-dark rounded-3xl p-8 text-white shadow-xl mt-8">
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center text-2xl text-barca-gold">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h3 class="text-xl font-bold mb-1">Masih butuh bantuan?</h3>
                            <p class="text-white/70 text-sm">Tim support kami siap membantu Anda 24/7.</p>
                        </div>
                        <a href="mailto:admin@skygoal.com" class="bg-barca-gold text-barca-blue px-6 py-3 rounded-xl font-bold hover:bg-white hover:scale-105 transition shadow-lg">
                            Hubungi Kami
                        </a>
                    </div>
                </div>

            </div>

            <aside class="lg:col-span-4 space-y-8">
                
                <div class="bg-white p-6 rounded-2xl shadow-lg border-t-4 border-barca-gold sticky top-24">
                    <h3 class="font-black text-xl text-barca-blue mb-6 flex items-center gap-2">
                        <i class="fas fa-fire text-barca-red"></i> Paling Populer
                    </h3>
                    <div class="space-y-6">
                        <?php foreach($popular as $i=>$pop): ?>
                        <a href="article.php?slug=<?php echo $pop['slug']; ?>" class="flex gap-4 group items-center">
                            <span class="text-4xl font-black text-gray-200 group-hover:text-barca-gold transition leading-none w-8 text-center"><?php echo $i+1; ?></span>
                            <div>
                                <h4 class="font-bold text-sm text-barca-dark leading-snug group-hover:text-barca-red transition mb-1 line-clamp-2"><?php echo $pop['title']; ?></h4>
                                <span class="text-[10px] font-bold text-white bg-barca-blue px-2 py-0.5 rounded inline-block shadow-sm"><?php echo $pop['views']; ?> Views</span>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if(isset($ads['sidebar_square'])): ?>
                <div class="bg-white p-3 rounded-2xl shadow-lg border border-gray-100 relative group hover:scale-105 transition duration-500">
                    <span class="block text-[10px] text-gray-400 font-bold text-center mb-2">ADVERTISEMENT</span>
                    
                    <?php if(($ads['sidebar_square']['ad_mode'] ?? 'image') === 'script'): ?>
                        <div class="ad-script-container">
                            <?php echo $ads['sidebar_square']['script_code']; ?>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo $ads['sidebar_square']['target_url']; ?>" target="_blank">
                            <img src="<?php echo getImageUrl($ads['sidebar_square']['image_url']); ?>" class="rounded-xl w-full hover:opacity-90 transition shadow-inner">
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </aside>

        </div>
    </div>

    <script>
        // Script sederhana untuk Accordion
        function toggleFaq(element) {
            // Tutup semua yang lain (opsional, jika ingin satu saja yang terbuka)
            const all = document.querySelectorAll('.group');
            all.forEach(el => {
                if(el !== element) el.classList.remove('faq-active');
            });
            
            // Toggle yang diklik
            element.classList.toggle('faq-active');
        }
    </script>

    <?php include 'template/footer.php'; ?>

</body>
</html>