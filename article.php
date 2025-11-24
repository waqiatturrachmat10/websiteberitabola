<?php
require_once 'db.php';

// Cek Slug
if (!isset($_GET['slug'])) { header("Location: index.php"); exit; }
$slug = $_GET['slug'];

// Update Views
$pdo->prepare("UPDATE articles SET views = views + 1 WHERE slug = ?")->execute([$slug]);

// Ambil Artikel Utama
$stmt = $pdo->prepare("SELECT a.*, c.name as cat, c.slug as cat_slug, u.username FROM articles a JOIN categories c ON a.category_id=c.id JOIN users u ON a.user_id=u.id WHERE a.slug = ?");
$stmt->execute([$slug]); 
$article = $stmt->fetch();

if(!$article) die("Artikel tidak ditemukan.");

// Ambil Berita Terkait
$related = $pdo->query("SELECT * FROM articles WHERE status='published' AND id != {$article['id']} ORDER BY created_at DESC LIMIT 4")->fetchAll();

// LOGIKA IKLAN (Fetch & Grouping)
$ads_raw = $pdo->query("SELECT * FROM ads WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll();
$ads = []; 
$sidebar_ads = []; 

foreach ($ads_raw as $ad) {
    if ($ad['type'] == 'sidebar_square') $sidebar_ads[] = $ad;
    else $ads[$ad['type']] = $ad;
}

// --- FUNGSI SISIPKAN IKLAN KE TENGAH KONTEN ---
/**
 * Menyisipkan HTML iklan (Native Feed) tepat di tengah paragraf artikel
 * @param string $content Konten artikel dari DB
 * @param array $adData Data iklan (Native Feed)
 * @return string Konten yang sudah disisipi iklan
 */
function insertAdInContent($content, $adData) {
    // Jika data iklan tidak ada, kembalikan konten asli
    if (!$adData) return $content;

    // Desain Iklan Tengah
    $adHTML = '
    <div class="my-8 p-1 rounded-2xl bg-gradient-to-r from-barca-blue to-barca-red shadow-lg transform hover:scale-[1.01] transition">
        <div class="bg-white rounded-xl p-4 flex flex-col sm:flex-row gap-4 items-center">
            <div class="w-24 h-24 shrink-0 rounded-lg overflow-hidden bg-gray-200 border border-gray-100">
                <img src="'.getImageUrl($adData['image_url']).'" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 text-center sm:text-left">
                <span class="text-[10px] font-bold text-barca-gold uppercase tracking-widest">Rekomendasi</span>
                <h4 class="font-bold text-barca-dark leading-tight mb-2">'.$adData['title'].'</h4>
                <a href="'.$adData['target_url'].'" target="_blank" class="text-xs font-bold text-white bg-barca-blue px-4 py-2 rounded-lg hover:bg-barca-red transition inline-block">Buka Sekarang</a>
            </div>
        </div>
    </div>';

    // Pecah konten berdasarkan penutup paragraf </p>
    $paragraphs = explode('</p>', $content);
    $total = count($paragraphs);
    
    // Sisipkan iklan di tengah (setelah paragraf kedua jika ada lebih dari 4 total)
    if ($total > 4) {
        $insertion_point = floor($total / 2);
        array_splice($paragraphs, $insertion_point, 0, $adHTML);
    }

    return implode('</p>', $paragraphs);
}

// Data Navbar (sudah statis)
$cats = ['La Liga' => 'la-liga', 'Premier League' => 'premier-league', 'Champions League' => 'champions-league'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($article['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Merriweather:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'], serif: ['Merriweather'] },
                    colors: { barca: { blue: '#004D98', red: '#A50044', gold: '#EDBB00', dark: '#0e1e30', light: '#F2F4F6' } }
                }
            }
        }
    </script>
    <style>body{background-color:#F2F4F6}</style>
</head>
<body class="bg-barca-light text-barca-dark">
    
    <?php if(isset($ads['banner_top'])): ?>
    <div id="top-banner" class="bg-barca-dark text-white text-center py-2 text-xs relative z-50 border-b border-white/10 hidden transition-all duration-300">
        <div class="container mx-auto px-4 relative">
            <span class="opacity-75 font-light">SPONSORED:</span> 
            <a href="<?php echo $ads['banner_top']['target_url']; ?>" target="_blank" class="font-bold text-barca-gold ml-2 hover:underline"><?php echo $ads['banner_top']['title']; ?></a>
            <button onclick="closeTopAd()" class="absolute right-0 top-1/2 -translate-y-1/2 text-white/50 hover:text-white transition p-2"><i class="fas fa-times"></i></button>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const banner = document.getElementById('top-banner');
            const storageKey = 'skygoal_top_ad_closed';
            const duration = 3 * 60 * 1000; 
            const closedTime = localStorage.getItem(storageKey);
            const now = new Date().getTime();
            if (!closedTime || (now - closedTime > duration)) { if(banner) banner.classList.remove('hidden'); }
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
        <a href="<?php echo $ads['horizontal_banner']['target_url']; ?>" target="_blank" class="block w-full max-w-4xl mx-auto mb-10 rounded-2xl overflow-hidden shadow-lg border-2 border-white hover:shadow-2xl transition">
            <img src="<?php echo getImageUrl($ads['horizontal_banner']['image_url']); ?>" class="w-full object-cover max-h-40">
        </a>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <div class="lg:col-span-8">
                <div class="flex items-center gap-3 text-xs font-bold text-gray-400 mb-6 uppercase tracking-wider">
                    <a href="index.php" class="hover:text-barca-blue">Home</a> <i class="fas fa-chevron-right text-[10px]"></i>
                    <a href="category.php?slug=<?php echo $article['cat_slug']; ?>" class="text-barca-blue bg-barca-blue/10 px-2 py-1 rounded hover:bg-barca-blue hover:text-white transition"><?php echo $article['cat']; ?></a>
                </div>

                <h1 class="text-3xl md:text-5xl font-black mb-6 mt-2 leading-tight text-barca-dark"><?php echo $article['title']; ?></h1>
                
                <div class="flex items-center gap-4 text-sm font-bold text-gray-500 border-b border-gray-200 pb-8 mb-8">
                    <span class="text-barca-blue"><?php echo $article['username']; ?></span> &bull; 
                    <span><?php echo date('d M Y', strtotime($article['created_at'])); ?></span> &bull; 
                    <span><i class="far fa-eye"></i> <?php echo $article['views']; ?></span>
                </div>

                <img src="<?php echo getImageUrl($article['image_url']); ?>" class="w-full rounded-3xl mb-8 shadow-2xl border-4 border-white">
                
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mb-8">
                    
                    <?php if(isset($ads['article_top'])): ?>
                    <div class="mb-8 border-b-2 border-barca-gold/30 pb-4">
                        <p class="text-[10px] text-barca-red font-bold text-left mb-2 uppercase tracking-widest">Sponsor</p>
                        <a href="<?php echo $ads['article_top']['target_url']; ?>" target="_blank" class="block w-full rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition border-2 border-barca-blue group">
                            <img src="<?php echo getImageUrl($ads['article_top']['image_url']); ?>" class="w-full object-cover max-h-48">
                        </a>
                    </div>
                    <?php endif; ?>

                    <article class="prose prose-lg prose-slate max-w-none font-serif text-gray-700 leading-loose">
                        <?php 
                        // IKLAN TENGAH (NATIVE FEED) DISISIPKAN DI SINI
                        echo insertAdInContent($article['content'], isset($ads['native_feed']) ? $ads['native_feed'] : null); 
                        ?>
                    </article>

                    <?php if(isset($ads['article_end'])): ?>
                    <div class="mt-10 pt-8 border-t-2 border-dashed border-gray-200">
                        <p class="text-xs text-gray-400 font-bold text-center mb-3 uppercase tracking-widest">Sponsor</p>
                        <a href="<?php echo $ads['article_end']['target_url']; ?>" target="_blank" class="block w-full rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition border-2 border-barca-gold group relative">
                            <img src="<?php echo getImageUrl($ads['article_end']['image_url']); ?>" class="w-full object-cover">
                            <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur px-4 py-2 rounded-lg">
                                <p class="text-barca-blue font-bold text-sm"><?php echo $ads['article_end']['title']; ?> <i class="fas fa-external-link-alt ml-1"></i></p>
                            </div>
                        </a>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

            <aside class="lg:col-span-4 space-y-10">
                <div class="bg-white p-8 rounded-3xl border-t-8 border-barca-blue shadow-lg">
                    <h3 class="font-black text-xl text-barca-blue mb-8">Berita Terkait</h3>
                    <div class="space-y-6">
                        <?php foreach($related as $r): ?>
                        <a href="article.php?slug=<?php echo $r['slug']; ?>" class="flex gap-4 group items-center">
                            <div class="w-20 h-20 rounded-xl bg-gray-100 overflow-hidden shrink-0 border border-gray-200">
                                <img src="<?php echo getImageUrl($r['image_url']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            </div>
                            <div><h4 class="font-bold text-sm leading-snug text-gray-700 group-hover:text-barca-blue transition line-clamp-2"><?php echo $r['title']; ?></h4></div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <?php if(!empty($sidebar_ads)): ?>
                    <div class="space-y-6 sticky top-24">
                        <?php foreach($sidebar_ads as $sb_ad): ?>
                        <div class="relative group hover:scale-105 transition duration-300">
                            <span class="absolute top-3 right-3 bg-white/90 text-[9px] font-bold px-2 py-0.5 rounded z-10 shadow-sm text-gray-500">AD</span>
                            <a href="<?php echo $sb_ad['target_url']; ?>" target="_blank" class="block">
                                <img src="<?php echo getImageUrl($sb_ad['image_url']); ?>" class="rounded-3xl w-full shadow-xl border-4 border-white object-cover">
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>

    <?php include 'template/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>