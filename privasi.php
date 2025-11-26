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
    <title>Kebijakan Privasi - SkyGoal</title>
    
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
                        <i class="fas fa-user-shield text-[150px] text-barca-blue"></i>
                    </div>

                    <h1 class="text-3xl md:text-4xl font-black text-barca-blue mb-6 relative z-10">Kebijakan Privasi</h1>
                    
                    <div class="prose prose-lg text-gray-600 relative z-10">
                        <p class="mb-6 leading-relaxed">
                            Kami di <strong>SkyGoal</strong> sangat menghargai privasi pengunjung kami. Dokumen Kebijakan Privasi ini menjelaskan jenis data apa yang dikumpulkan dan bagaimana data tersebut digunakan untuk keperluan operasional situs.
                        </p>
                        
                        <div class="bg-blue-50 border-l-4 border-barca-blue p-6 rounded-r-xl mb-8">
                            <p class="italic text-sm text-barca-dark font-semibold m-0">
                                <i class="fas fa-info-circle mr-2"></i> Data Anda tidak akan dibagikan ke pihak ketiga tanpa izin, kecuali diwajibkan secara hukum.
                            </p>
                        </div>

                        <h2 class="text-2xl font-bold text-barca-dark mb-4 border-b pb-2 inline-block">Data yang Dikumpulkan</h2>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-barca-gold mt-1"></i>
                                <span><strong>Informasi Akun Admin:</strong> Data login seperti username dan password terenkripsi disimpan secara aman di database kami.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-barca-gold mt-1"></i>
                                <span><strong>File Upload:</strong> Gambar artikel atau iklan yang diunggah akan disimpan di folder penyimpanan server (<code>uploads/</code>).</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-barca-gold mt-1"></i>
                                <span><strong>Log Aktivitas:</strong> Statistik dasar pengunjung (seperti jumlah views artikel) dicatat untuk keperluan analisis konten.</span>
                            </li>
                        </ul>

                        <h2 class="text-2xl font-bold text-barca-dark mb-4 border-b pb-2 inline-block">Keamanan</h2>
                        <p class="mb-4 leading-relaxed">
                            Kami berkomitmen untuk memastikan informasi Anda aman. Admin diwajibkan menggunakan password yang kuat. 
                        </p>
                        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-xl text-sm text-yellow-800 flex gap-3 items-start">
                            <i class="fas fa-exclamation-triangle mt-0.5"></i>
                            <div>
                                <strong>Catatan Teknis:</strong> Proyek ini merupakan contoh implementasi sistem berita sederhana. Disarankan melakukan audit keamanan server (SSL, Firewall, dll) sebelum digunakan untuk publikasi skala besar.
                            </div>
                        </div>
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

    <?php include 'template/footer.php'; ?>

</body>
</html>