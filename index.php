<?php
require_once 'db.php';

// --- TIDAK PERLU DEKLARASI ULANG getImageUrl DI SINI ---
// Karena sudah ada di db.php

// --- LOGIKA DATA ---
// 1. Ambil Iklan Aktif
$ads_raw = $pdo->query("SELECT * FROM ads WHERE is_active = 1")->fetchAll();
$ads = []; 
foreach ($ads_raw as $ad) {
    // Decode HTML entities untuk script agar tag <script> berfungsi
    if(($ad['ad_mode'] ?? 'image') === 'script') {
        $ad['script_code'] = html_entity_decode($ad['script_code']);
    }
    $ads[$ad['type']] = $ad;
}

// 2. Ambil Hero Article (1 Teratas)
$hero = $pdo->query("
    SELECT a.*, c.name as cat 
    FROM articles a 
    JOIN categories c ON a.category_id=c.id 
    WHERE a.status='published' 
    ORDER BY a.created_at DESC LIMIT 1
")->fetch();

// 3. Ambil Feed Article (6 Berikutnya)
$feed = $pdo->query("
    SELECT a.*, c.name as cat 
    FROM articles a 
    JOIN categories c ON a.category_id=c.id 
    WHERE a.status='published' 
    ORDER BY a.created_at DESC LIMIT 6 OFFSET 1
")->fetchAll();

// 4. Populer & Kategori
$popular = $pdo->query("SELECT * FROM articles WHERE status='published' ORDER BY views DESC LIMIT 5")->fetchAll();
$cats = $pdo->query("SELECT * FROM categories LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyGoal - Blaugrana Edition</title>
    
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
                            blue: '#004D98',  // Blau
                            red: '#A50044',   // Grana
                            gold: '#EDBB00',  // Kuning Emas
                            dark: '#0e1e30',  // Teks Gelap
                            light: '#F2F4F6'  // Background Abu
                        }
                    },
                    animation: {
                        'fade-up': 'fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'scale-in': 'scaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards'
                    },
                    keyframes: {
                        fadeUp: { '0%': { opacity: 0, transform: 'translateY(20px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                        scaleIn: { '0%': { opacity: 0, transform: 'scale(0.95)' }, '100%': { opacity: 1, transform: 'scale(1)' } }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #F2F4F6; }
        .line-clamp-2{-webkit-line-clamp:2;line-clamp:2;display:-webkit-box;-webkit-box-orient:vertical;overflow:hidden}
        /* Style khusus untuk container iklan script agar center */
        .ad-script-container { display: flex; justify-content: center; align-items: center; width: 100%; overflow: hidden; margin: 10px 0; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-thumb { background: #A50044; border-radius: 10px; }
        ::-webkit-scrollbar-track { background: #004D98; }
    </style>
</head>
<body class="bg-barca-light text-barca-dark font-sans antialiased selection:bg-barca-gold selection:text-barca-blue">

    <?php if(isset($ads['popup'])): ?>
    <div id="popup" class="fixed inset-0 bg-barca-blue/90 z-[100] flex items-center justify-center p-4 hidden backdrop-blur-sm transition-all duration-500">
        <div class="relative bg-white rounded-2xl overflow-hidden max-w-md w-full shadow-2xl border-4 border-barca-gold scale-90 opacity-0 transition-all duration-500" id="popup-content">
            <button onclick="closePopup()" class="absolute top-3 right-3 z-20 bg-barca-red text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-barca-blue transition shadow-lg cursor-pointer transform hover:rotate-90">
                <i class="fas fa-times"></i>
            </button>
            
            <?php if(($ads['popup']['ad_mode'] ?? 'image') === 'script'): ?>
                <div class="p-4 bg-white flex justify-center min-h-[300px] items-center">
                    <?php echo $ads['popup']['script_code']; ?>
                </div>
            <?php else: ?>
                <a href="<?php echo $ads['popup']['target_url']; ?>" target="_blank" class="block relative group">
                    <img src="<?php echo getImageUrl($ads['popup']['image_url']); ?>" class="w-full object-cover">
                </a>
            <?php endif; ?>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const p = document.getElementById('popup'); const c = document.getElementById('popup-content');
            if(p) { p.classList.remove('hidden'); setTimeout(() => { c.classList.remove('scale-90', 'opacity-0'); }, 50); }
        }, 2500);
        function closePopup() { document.getElementById('popup').classList.add('opacity-0', 'pointer-events-none'); }
    </script>
    <?php endif; ?>

    <?php if(isset($ads['banner_top'])): ?>
    <div id="top-banner" class="bg-barca-dark text-white text-center py-2 text-xs relative z-50 border-b border-white/10 hidden transition-all duration-300">
        <div class="container mx-auto px-4 relative flex justify-center items-center">
            
            <?php if(($ads['banner_top']['ad_mode'] ?? 'image') === 'script'): ?>
                <div class="ad-script-container max-h-[90px]">
                    <?php echo $ads['banner_top']['script_code']; ?>
                </div>
            <?php else: ?>
                <span class="opacity-75 font-light">SPONSORED:</span> 
                <a href="<?php echo $ads['banner_top']['target_url']; ?>" target="_blank" class="font-bold text-barca-gold ml-2 hover:underline">
                    <?php echo $ads['banner_top']['title']; ?>
                </a>
            <?php endif; ?>

            <button onclick="closeTopAd()" class="absolute right-0 top-1/2 -translate-y-1/2 text-white/50 hover:text-white transition p-2">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const banner = document.getElementById('top-banner');
            const storageKey = 'skygoal_top_ad_closed';
            const duration = 3 * 60 * 1000; 
            const closedTime = localStorage.getItem(storageKey);
            const now = new Date().getTime();
            if (!closedTime || (now - closedTime > duration)) {
                if(banner) banner.classList.remove('hidden');
            }
        });
        function closeTopAd() {
            const banner = document.getElementById('top-banner');
            banner.classList.add('hidden');
            localStorage.setItem('skygoal_top_ad_closed', new Date().getTime());
        }
    </script>
    <?php endif; ?>

    <?php include 'template/navbar.php'; ?>

    <div class="container mx-auto px-4 py-10">
        
        <?php if(isset($ads['horizontal_banner'])): ?>
            <div class="block w-full max-w-5xl mx-auto mb-10 animate-fade-up">
                <?php if(($ads['horizontal_banner']['ad_mode'] ?? 'image') === 'script'): ?>
                    <div class="ad-script-container bg-gray-100 rounded-xl border border-gray-200 p-2">
                        <?php echo $ads['horizontal_banner']['script_code']; ?>
                    </div>
                <?php else: ?>
                    <a href="<?php echo $ads['horizontal_banner']['target_url']; ?>" target="_blank" class="block rounded-2xl overflow-hidden shadow-lg border-2 border-white hover:shadow-2xl transition transform hover:-translate-y-1">
                        <img src="<?php echo getImageUrl($ads['horizontal_banner']['image_url']); ?>" class="w-full object-cover max-h-32 md:max-h-44">
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-8 space-y-12">
                
                <?php if ($hero): ?>
                <a href="article.php?slug=<?php echo $hero['slug']; ?>" class="block group relative rounded-3xl overflow-hidden shadow-2xl h-[450px] border border-white animate-scale-in">
                    <img src="<?php echo getImageUrl($hero['image_url']); ?>" class="w-full h-full object-cover transition duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-barca-blue/90 via-barca-blue/40 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-8 md:p-10 w-full md:w-3/4">
                        <span class="bg-barca-gold text-barca-blue text-xs font-extrabold px-3 py-1.5 rounded shadow-lg uppercase tracking-widest mb-4 inline-block transform group-hover:scale-105 transition">
                            <?php echo $hero['cat']; ?>
                        </span>
                        <h1 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight drop-shadow-md group-hover:text-barca-gold transition-colors">
                            <?php echo $hero['title']; ?>
                        </h1>
                        <div class="flex items-center text-white/80 text-sm font-bold gap-6">
                            <span class="flex items-center gap-2"><i class="far fa-clock text-barca-gold"></i> <?php echo date('d M Y', strtotime($hero['created_at'])); ?></span>
                            <span class="flex items-center gap-2"><i class="fas fa-eye text-barca-gold"></i> <?php echo $hero['views'] ?? 0; ?>x Dibaca</span>
                        </div>
                    </div>
                </a>
                <?php endif; ?>

                <div>
                    <div class="flex items-center gap-3 mb-6 border-l-8 border-barca-red pl-4">
                        <h2 class="text-2xl font-black text-barca-blue uppercase tracking-tight">Berita Terbaru</h2>
                    </div>
                    
                    <div id="news-grid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php $count=0; foreach($feed as $news): $count++; ?>
                        
                        <a href="article.php?slug=<?php echo $news['slug']; ?>" class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group border border-gray-100 flex flex-col h-full">
                            <div class="h-48 overflow-hidden relative">
                                <span class="absolute top-3 left-3 bg-barca-blue/90 backdrop-blur text-white text-[10px] font-bold px-3 py-1 rounded uppercase z-10 shadow-md border-b-2 border-barca-red">
                                    <?php echo $news['cat']; ?>
                                </span>
                                <img src="<?php echo getImageUrl($news['image_url']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="text-lg font-bold text-barca-dark mb-3 leading-snug group-hover:text-barca-red transition line-clamp-2">
                                    <?php echo $news['title']; ?>
                                </h3>
                                <div class="mt-auto pt-4 border-t border-gray-100 flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-400 uppercase"><?php echo date('d M Y', strtotime($news['created_at'])); ?></span>
                                    <span class="text-barca-red text-xs font-bold flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                        Baca <i class="fas fa-arrow-right"></i>
                                    </span>
                                </div>
                            </div>
                        </a>

                        <?php if($count == 2 && isset($ads['native_feed'])): ?>
                        <div class="md:col-span-2 my-2">
                            <?php if(($ads['native_feed']['ad_mode'] ?? 'image') === 'script'): ?>
                                <div class="w-full bg-gray-50 border border-gray-200 rounded-2xl p-4 text-center">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest block mb-2">SPONSORED</span>
                                    <div class="ad-script-container">
                                        <?php echo $ads['native_feed']['script_code']; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <a href="<?php echo $ads['native_feed']['target_url']; ?>" target="_blank" class="relative block bg-gradient-to-r from-barca-blue to-barca-dark rounded-2xl p-1 shadow-lg hover:scale-[1.01] transition-transform">
                                    <div class="bg-white rounded-xl p-4 flex flex-col md:flex-row items-center gap-6 h-full relative overflow-hidden">
                                        <div class="absolute top-0 right-0 bg-barca-gold text-barca-blue text-[10px] font-black px-3 py-1 rounded-bl-xl z-10 shadow-sm">PARTNER</div>
                                        <div class="w-full md:w-1/3 h-32 rounded-lg overflow-hidden shadow-inner">
                                            <img src="<?php echo getImageUrl($ads['native_feed']['image_url']); ?>" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1 text-center md:text-left">
                                            <h4 class="text-xl font-extrabold text-barca-blue mb-1"><?php echo $ads['native_feed']['title']; ?></h4>
                                            <p class="text-gray-500 text-sm mb-3 line-clamp-2">Penawaran eksklusif spesial untuk fans sepak bola.</p>
                                            <span class="inline-block bg-barca-red text-white px-6 py-2 rounded-lg font-bold text-xs hover:bg-barca-blue transition shadow-md">Lihat Sekarang</span>
                                        </div>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php endforeach; ?>
                    </div>
                    
                    <div class="text-center mt-12">
                        <button id="loadMore" onclick="loadNews()" class="bg-white border-2 border-barca-blue text-barca-blue px-10 py-3 rounded-full font-bold hover:bg-barca-blue hover:text-white transition shadow-lg flex items-center gap-2 mx-auto">
                            <span>Muat Lebih Banyak</span>
                            <i class="fas fa-spinner fa-spin hidden" id="loader"></i>
                        </button>
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
        let offset=7;
        async function loadNews(){
            const btn = document.getElementById('loadMore');
            const txt = btn.querySelector('span');
            const spin = document.getElementById('loader');
            
            txt.innerText='Sedang Memuat...';
            spin.classList.remove('hidden');
            
            try {
                const res = await fetch(`get_more_news.php?offset=${offset}`);
                const data = await res.json();
                
                if(data.length > 0){
                    data.forEach(n => {
                        const html = `
                        <a href="article.php?slug=${n.slug}" class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group border border-gray-100 flex flex-col h-full animate-fade-up">
                            <div class="h-48 overflow-hidden relative">
                                <span class="absolute top-3 left-3 bg-barca-blue/90 backdrop-blur text-white text-[10px] font-bold px-3 py-1 rounded uppercase z-10 shadow-md border-b-2 border-barca-red">${n.cat}</span>
                                <img src="${n.image_url}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                <h3 class="text-lg font-bold text-barca-dark mb-3 leading-snug group-hover:text-barca-red transition line-clamp-2">${n.title}</h3>
                                <div class="mt-auto pt-4 border-t border-gray-100 flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-400 uppercase">Baru Saja</span>
                                    <span class="text-barca-red text-xs font-bold flex items-center gap-1 group-hover:translate-x-1 transition-transform">Baca <i class="fas fa-arrow-right"></i></span>
                                </div>
                            </div>
                        </a>`;
                        document.getElementById('news-grid').insertAdjacentHTML('beforeend', html);
                    });
                    offset += data.length;
                } else {
                    btn.style.display='none';
                }
            } catch(e) { console.error(e); } 
            finally {
                txt.innerText='Muat Lebih Banyak';
                spin.classList.add('hidden');
            }
        }
    </script>

    <?php include 'template/footer.php'; ?>

</body>
</html>