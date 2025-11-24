<?php
require_once 'db.php';
if (!isset($_GET['slug'])) { header("Location: index.php"); exit; }
$slug = $_GET['slug'];
$cat_stmt = $pdo->prepare("SELECT * FROM categories WHERE slug=?"); $cat_stmt->execute([$slug]); $category = $cat_stmt->fetch();
if(!$category) die("Not found");
$articles = $pdo->prepare("SELECT a.*, c.name as cat FROM articles a JOIN categories c ON a.category_id=c.id WHERE a.category_id=? AND status='published' ORDER BY created_at DESC");
$articles->execute([$category['id']]); $articles = $articles->fetchAll();
$cats = $pdo->query("SELECT * FROM categories LIMIT 5")->fetchAll();

// LOGIKA IKLAN (PERBAIKAN MULTI-SLOT)
$ads_raw = $pdo->query("SELECT * FROM ads WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll();
$ads = []; 
$sidebar_ads = [];

foreach ($ads_raw as $ad) {
    if ($ad['type'] == 'sidebar_square') {
        $sidebar_ads[] = $ad;
    } else {
        $ads[$ad['type']] = $ad;
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['"Plus Jakarta Sans"']},colors:{barca:{blue:'#004D98',red:'#A50044',gold:'#EDBB00',dark:'#0e1e30',light:'#F2F4F6'}}}}}</script>
    <style>body{background-color:#F2F4F6}</style>
</head>
<body class="bg-barca-light text-barca-dark font-sans antialiased">
    
    <?php include 'template/navbar.php'; ?>

    <div class="container mx-auto px-4 py-10">
        
        <div class="text-center mb-12">
            <span class="bg-barca-blue text-white px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg border border-barca-red">Arsip Berita</span>
            <h1 class="text-4xl md:text-5xl font-black mt-4 text-barca-blue"><?php echo htmlspecialchars($category['name']); ?></h1>
        </div>

        <?php if(isset($ads['horizontal_banner'])): ?>
        <a href="<?php echo $ads['horizontal_banner']['target_url']; ?>" target="_blank" class="block w-full max-w-5xl mx-auto mb-12 rounded-2xl overflow-hidden shadow-lg border-2 border-white hover:shadow-2xl transition transform hover:-translate-y-1">
            <img src="<?php echo getImageUrl($ads['horizontal_banner']['image_url']); ?>" class="w-full object-cover max-h-32 md:max-h-44">
        </a>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php if(count($articles) > 0): ?>
                    <?php foreach($articles as $news): ?>
                    <a href="article.php?slug=<?php echo $news['slug']; ?>" class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group border border-gray-100 flex flex-col h-full">
                        <div class="h-52 overflow-hidden relative border-b-4 border-barca-red">
                            <img src="<?php echo getImageUrl($news['image_url']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-barca-blue/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-2 h-2 rounded-full bg-barca-gold"></span>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide"><?php echo date('d M Y', strtotime($news['created_at'])); ?></span>
                            </div>
                            <h3 class="text-lg font-bold text-barca-dark leading-snug group-hover:text-barca-blue transition mb-3 line-clamp-2"><?php echo $news['title']; ?></h3>
                            <div class="mt-auto pt-4 border-t border-gray-100 flex justify-between items-center">
                                <span class="text-xs font-bold text-barca-red uppercase tracking-wider">Baca Berita</span>
                                <i class="fas fa-arrow-right text-barca-red transform group-hover:translate-x-1 transition"></i>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-2 text-center py-10 bg-white rounded-xl border-dashed border-2"><p class="text-gray-400">Belum ada berita.</p></div>
                <?php endif; ?>
            </div>

            <aside class="lg:col-span-4 space-y-8">
                <?php if(!empty($sidebar_ads)): ?>
                    <div class="space-y-6">
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

</body>
</html>