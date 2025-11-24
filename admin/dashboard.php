<?php
require_once '../db.php';
startSecureSession();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// 1. STATISTIK
$stats = [
    'articles' => $pdo->query("SELECT count(*) FROM articles")->fetchColumn(),
    'ads' => $pdo->query("SELECT count(*) FROM ads WHERE is_active=1")->fetchColumn(),
    'cats' => $pdo->query("SELECT count(*) FROM categories")->fetchColumn()
];

// 2. DATA ARTIKEL
$articles = $pdo->query("
    SELECT a.*, c.name as cat, u.username 
    FROM articles a 
    JOIN categories c ON a.category_id = c.id 
    LEFT JOIN users u ON a.user_id = u.id 
    ORDER BY a.created_at DESC
")->fetchAll();

// 3. DATA IKLAN
$ads_raw = $pdo->query("
    SELECT ads.*, u.username 
    FROM ads 
    LEFT JOIN users u ON ads.user_id = u.id 
    ORDER BY ads.created_at DESC
")->fetchAll();

$adSlots = [];
$sidebarAdsAdmin = []; 

foreach ($ads_raw as $ad) {
    if($ad['type'] == 'sidebar_square') {
        $sidebarAdsAdmin[] = $ad;
    } else {
        if (!isset($adSlots[$ad['type']])) {
            $adSlots[$ad['type']] = $ad;
        }
    }
}

function getAdminImageUrl($url) {
    if (empty($url)) return 'https://via.placeholder.com/800x400?text=No+Image';
    if (filter_var($url, FILTER_VALIDATE_URL)) return $url;
    return '../' . $url;
}

// HELPER 1: Render Slot Tunggal (Updated Visual)
function renderAdSlot($type, $label, $adData) {
    $ad = isset($adData[$type]) ? $adData[$type] : null;
    $isActive = $ad && $ad['is_active'];
    // Style border putus-putus warna kuning emas Barca jika kosong, Biru jika ada isi
    $statusColor = $isActive ? 'border-barca-blue bg-white shadow-md border-solid' : 'border-barca-gold bg-yellow-50/50 border-dashed opacity-80';
    $title = $ad ? htmlspecialchars($ad['title']) : 'Slot Kosong';
    
    echo "
    <div class='border-2 $statusColor rounded-xl p-3 my-4 relative group transition-all hover:shadow-lg'>
        <div class='flex justify-between items-start mb-1'>
            <span class='text-[9px] font-black uppercase tracking-widest text-gray-400'>$label</span>
            " . ($ad ? "
            <div class='flex gap-1'>
                <button onclick='toggleAd({$ad['id']}, {$ad['is_active']})' class='text-[9px] font-bold px-2 py-0.5 rounded ".($isActive ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500')." hover:scale-105 transition'>".($isActive ? 'ON' : 'OFF')."</button>
                <button onclick='editAd(".json_encode($ad).")' class='text-barca-blue hover:text-barca-gold transition text-xs'><i class='fas fa-pencil-alt'></i></button>
            </div>
            " : "<button onclick=\"openModal('adModal'); document.getElementById('add-ad-type').value='$type';\" class='text-[10px] text-barca-blue font-bold hover:underline'>+ Pasang</button>") . "
        </div>
        <h4 class='font-bold text-barca-dark text-xs line-clamp-1'>$title</h4>
        " . ($ad && $ad['image_url'] ? "<div class='mt-1 h-10 w-full bg-gray-100 rounded overflow-hidden'><img src='".getAdminImageUrl($ad['image_url'])."' class='w-full h-full object-cover opacity-80'></div>" : "") . "
    </div>";
}

function renderSingleAdCard($ad) {
    $isActive = $ad['is_active'];
    $style = $isActive ? 'border-barca-blue bg-white shadow-md' : 'border-gray-300 bg-gray-50 opacity-75 border-dashed';
    return "<div class='border-2 $style rounded-xl p-3 mb-2 relative group transition-all hover:shadow-lg hover:border-barca-gold'><div class='flex justify-between items-center mb-1'><span class='text-[9px] font-black uppercase tracking-widest text-gray-400'>Sidebar</span><div class='flex gap-1'><button onclick='toggleAd({$ad['id']}, {$ad['is_active']})' class='text-[9px] font-bold px-2 py-0.5 rounded ".($isActive ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500')."'>".($isActive ? 'ON' : 'OFF')."</button><button onclick='editAd(".json_encode($ad).")' class='text-barca-blue hover:text-barca-gold text-xs'><i class='fas fa-pencil-alt'></i></button><button onclick='delAd({$ad['id']})' class='text-red-400 hover:text-red-600 text-xs'><i class='fas fa-trash-alt'></i></button></div></div><h4 class='font-bold text-barca-dark text-xs line-clamp-1'>" . htmlspecialchars($ad['title']) . "</h4><div class='mt-1 h-8 w-full bg-gray-100 rounded overflow-hidden'><img src='".getAdminImageUrl($ad['image_url'])."' class='w-full h-full object-cover opacity-80'></div></div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"'] }, colors: { barca: { blue: '#004D98', red: '#A50044', gold: '#EDBB00', dark: '#0e1e30', light: '#F2F4F6' } } } } }
    </script>
    <style> ::-webkit-scrollbar { width: 6px; } ::-webkit-scrollbar-thumb { background: #A50044; border-radius: 10px; } </style>
</head>
<body class="bg-barca-light font-sans text-barca-dark h-screen flex overflow-hidden">

    <aside class="w-72 bg-barca-blue text-white flex-col hidden md:flex shadow-2xl relative z-30 overflow-y-auto">
        <div class="h-24 flex items-center px-8 border-b border-white/10 shrink-0"><div class="flex items-center gap-3"><div class="w-10 h-10 bg-barca-gold text-barca-blue rounded-xl flex items-center justify-center text-xl font-bold shadow-lg"><i class="fas fa-futbol"></i></div><span class="text-2xl font-black tracking-wide">Sky<span class="text-barca-gold">Goal</span>.</span></div></div>
        <nav class="flex-1 p-6 space-y-8">
            <div>
                <p class="text-xs font-bold text-barca-gold/80 uppercase tracking-widest mb-4 ml-2">Konten</p>
                <ul class="space-y-2">
                    <li><a href="#" onclick="document.getElementById('articles-section').scrollIntoView({behavior: 'smooth'})" class="flex items-center gap-3 px-4 py-3 bg-white/10 text-white rounded-xl font-bold shadow-inner border-l-4 border-barca-gold"><i class="fas fa-newspaper w-5 text-center"></i> Data Artikel</a></li>
                    <li><button onclick="openModal('articleModal')" class="w-full flex items-center gap-3 px-4 py-3 text-white/80 hover:bg-barca-red rounded-xl font-medium transition-all group text-left"><i class="fas fa-pen-nib w-5 text-center"></i> Tulis Artikel</button></li>
                    <li><button onclick="openModal('catModal')" class="w-full flex items-center gap-3 px-4 py-3 text-white/80 hover:bg-barca-red rounded-xl font-medium transition-all group text-left"><i class="fas fa-tags w-5 text-center"></i> Kategori</button></li>
                </ul>
            </div>
            <div>
                <p class="text-xs font-bold text-barca-gold/80 uppercase tracking-widest mb-4 ml-2">Iklan</p>
                <ul class="space-y-2">
                    <li><button onclick="document.getElementById('ads-layout').scrollIntoView({behavior: 'smooth'})" class="w-full flex items-center gap-3 px-4 py-3 text-white/80 hover:bg-barca-red rounded-xl font-medium transition-all group text-left"><i class="fas fa-layer-group w-5 text-center"></i> Tata Letak</button></li>
                </ul>
            </div>
            <div>
                <p class="text-xs font-bold text-barca-gold/80 uppercase tracking-widest mb-4 ml-2">Sistem</p>
                <ul class="space-y-2">
                    <li><a href="../index.php" target="_blank" class="flex items-center gap-3 px-4 py-3 text-white/80 hover:bg-white/20 rounded-xl font-medium transition-all group"><i class="fas fa-external-link-alt w-5 text-center"></i> Lihat Website</a></li>
                    <li><a href="logout.php" class="flex items-center gap-3 px-4 py-3 text-red-300 hover:bg-red-500/20 rounded-xl font-medium transition-all group"><i class="fas fa-sign-out-alt w-5 text-center"></i> Logout</a></li>
                </ul>
            </div>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col overflow-y-auto relative bg-barca-light">
        <header class="h-24 bg-white/80 backdrop-blur-md border-b border-gray-200 flex justify-between items-center px-10 sticky top-0 z-20">
            <div><h2 class="font-black text-2xl text-barca-blue">Dashboard</h2><p class="text-sm text-gray-500 font-bold">Selamat datang, <span class="text-barca-red"><?php echo htmlspecialchars($_SESSION['username']); ?></span>!</p></div>
            <div class="w-12 h-12 bg-barca-gold rounded-full flex items-center justify-center text-barca-blue font-black text-xl border-4 border-white shadow-md"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div>
        </header>

        <div class="p-10 pb-32 max-w-[1600px] mx-auto w-full space-y-12">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-[2rem] border-l-8 border-barca-blue shadow-md flex justify-between items-center"><div><p class="text-xs font-bold text-gray-400 uppercase">ARTIKEL</p><h3 class="text-3xl font-black text-barca-blue"><?php echo $stats['articles']; ?></h3></div><i class="far fa-newspaper text-4xl text-gray-200"></i></div>
                <div class="bg-white p-6 rounded-[2rem] border-l-8 border-barca-red shadow-md flex justify-between items-center"><div><p class="text-xs font-bold text-gray-400 uppercase">IKLAN AKTIF</p><h3 class="text-3xl font-black text-barca-red"><?php echo $stats['ads']; ?></h3></div><i class="fas fa-ad text-4xl text-gray-200"></i></div>
                <div class="bg-white p-6 rounded-[2rem] border-l-8 border-barca-gold shadow-md flex justify-between items-center"><div><p class="text-xs font-bold text-gray-400 uppercase">KATEGORI</p><h3 class="text-3xl font-black text-barca-gold"><?php echo $stats['cats']; ?></h3></div><i class="fas fa-list text-4xl text-gray-200"></i></div>
            </div>

            <div id="ads-layout" class="flex flex-col gap-6">
                <div class="bg-gray-200/60 p-4 rounded-2xl border-2 border-dashed border-gray-300">
                    <h3 class="text-xs font-bold text-gray-500 uppercase mb-3 flex items-center gap-2"><i class="fas fa-globe"></i> Global Element</h3>
                    <?php renderAdSlot('popup', 'Popup Modal', $adSlots); ?>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                    <div class="bg-barca-blue h-10 rounded-xl mb-4 flex items-center justify-center text-white font-bold text-xs shadow-inner tracking-widest">HEADER / NAVBAR</div>
                    <?php renderAdSlot('banner_top', 'Top Bar Banner (Teks)', $adSlots); ?>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 h-full">
                            <div class="border-b-2 border-gray-100 pb-3 mb-4 text-sm font-bold text-barca-blue uppercase tracking-wide flex items-center gap-2"><i class="fas fa-file-alt"></i> Area Konten Utama</div>
                            
                            <?php renderAdSlot('horizontal_banner', 'Horizontal Banner', $adSlots); ?>

                            <div class="space-y-4 p-6 bg-gray-50 rounded-xl border border-gray-200">
                                <div class="h-6 bg-gray-300 rounded w-3/4 mb-4"></div> <div class="h-32 bg-gray-200 rounded w-full flex items-center justify-center text-gray-400 text-xs font-bold mb-4">GAMBAR ARTIKEL</div>
                                
                                <div class="space-y-2">
                                    <div class="h-2 bg-gray-200 rounded w-full"></div>
                                    <div class="h-2 bg-gray-200 rounded w-5/6"></div>
                                    <div class="h-2 bg-gray-200 rounded w-full"></div>
                                </div>

                                <?php renderAdSlot('native_feed', 'Native Feed Ad (Sela Berita)', $adSlots); ?>

                                <div class="space-y-2">
                                    <div class="h-2 bg-gray-200 rounded w-full"></div>
                                    <div class="h-2 bg-gray-200 rounded w-4/5"></div>
                                </div>

                                <?php renderAdSlot('article_end', 'Article End (Akhir Berita)', $adSlots); ?>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 h-full">
                            <div class="border-b-2 border-gray-100 pb-3 mb-4 flex justify-between items-center">
                                <span class="text-sm font-bold text-barca-red uppercase tracking-wide"><i class="fas fa-columns"></i> Sidebar</span>
                                <button onclick="openModal('adModal'); document.getElementById('add-ad-type').value='sidebar_square';" class="text-[10px] bg-barca-blue text-white px-3 py-1 rounded-lg hover:bg-barca-red transition font-bold">+ Tambah</button>
                            </div>
                            <div class="bg-gray-100 p-3 rounded-lg mb-4 text-center text-[10px] font-bold text-gray-400 border border-gray-200">Widget Populer</div>
                            
                            <div class="space-y-3">
                                <?php 
                                if (!empty($sidebarAdsAdmin)) {
                                    foreach($sidebarAdsAdmin as $sb_ad) echo renderSingleAdCard($sb_ad);
                                } else {
                                    echo "<div class='text-center text-xs text-gray-400 py-6 border-2 border-dashed border-gray-300 rounded-xl'>Slot Sidebar Kosong</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="articles-section" class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden mt-12">
                <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-white">
                    <h3 class="font-black text-xl text-barca-blue flex items-center gap-2"><i class="fas fa-newspaper"></i> Semua Artikel</h3>
                    <button onclick="openModal('articleModal')" class="bg-barca-gold text-barca-blue text-xs font-bold px-5 py-2.5 rounded-xl hover:bg-barca-blue hover:text-white shadow-lg transition-all">+ Tulis Baru</button>
                </div>
                <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[10px] tracking-wider sticky top-0 z-10">
                            <tr><th class="px-8 py-4">Judul</th><th class="px-8 py-4">Kategori</th><th class="px-8 py-4 text-center">Penulis</th><th class="px-8 py-4 text-right">Aksi</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach($articles as $a): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-8 py-5 font-bold text-barca-dark w-1/3"><?php echo htmlspecialchars($a['title']); ?></td>
                                <td class="px-8 py-5"><span class="bg-barca-blue/10 text-barca-blue px-3 py-1 rounded-lg text-[10px] font-bold uppercase"><?php echo $a['cat']; ?></span></td>
                                <td class="px-8 py-5 text-xs font-bold text-gray-500 text-center"><span class="bg-gray-100 px-3 py-1 rounded-full border border-gray-200 flex items-center justify-center gap-1 w-fit mx-auto"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($a['username'] ?? 'Unknown'); ?></span></td>
                                <td class="px-8 py-5 text-right"><button onclick='editArticle(<?php echo json_encode($a); ?>)' class="text-barca-blue hover:text-barca-gold mr-2"><i class="fas fa-pencil-alt"></i></button><button onclick="delArticle(<?php echo $a['id']; ?>)" class="text-red-400 hover:text-red-600"><i class="fas fa-trash-alt"></i></button></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <div id="catModal" class="hidden fixed inset-0 bg-barca-dark/80 z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-all"><div class="bg-white rounded-3xl p-8 w-full max-w-sm shadow-2xl"><h3 class="font-black text-xl mb-6 text-barca-blue">Tambah Kategori</h3><form action="process.php" method="POST"><input type="hidden" name="action" value="add_category"><input type="text" name="name" required placeholder="Nama Kategori" class="border-2 border-gray-200 w-full rounded-xl px-4 py-3 mb-6 focus:border-barca-blue outline-none font-bold text-barca-dark"><div class="flex justify-end gap-3"><button type="button" onclick="closeModal('catModal')" class="text-gray-400 font-bold px-4">Batal</button><button class="bg-barca-blue text-white px-6 py-2 rounded-xl font-bold hover:bg-barca-red transition">Simpan</button></div></form></div></div>

    <div id="articleModal" class="hidden fixed inset-0 bg-barca-dark/80 z-50 flex items-center justify-center p-4 backdrop-blur-sm"><div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl overflow-hidden"><div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white"><h3 class="font-black text-lg text-barca-blue">Artikel Baru</h3><button onclick="closeModal('articleModal')"><i class="fas fa-times text-gray-400"></i></button></div><form action="process.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-4"><input type="hidden" name="action" value="add_article"><div class="grid grid-cols-2 gap-4"><input type="text" name="title" required placeholder="Judul" class="border-2 border-gray-200 rounded-xl px-4 py-2 w-full font-bold focus:border-barca-blue outline-none"><select name="category_id" class="border-2 border-gray-200 rounded-xl px-4 py-2 w-full font-bold focus:border-barca-blue outline-none bg-white"><?php $allCats = $pdo->query("SELECT * FROM categories")->fetchAll(); foreach($allCats as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option><?php endforeach; ?></select></div><div class="flex gap-2"><button type="button" onclick="swIn('art','file')" class="bg-barca-blue text-white text-xs px-3 py-1 rounded-lg font-bold">File</button><button type="button" onclick="swIn('art','url')" class="bg-gray-200 text-gray-600 text-xs px-3 py-1 rounded-lg font-bold">URL</button></div><input type="file" name="image_file" id="art-file" class="w-full text-sm text-gray-500"><input type="url" name="image_url" id="art-url" placeholder="https://..." class="w-full border-2 rounded-xl px-4 py-2 hidden"><textarea name="content" rows="5" required placeholder="Isi berita..." class="w-full border-2 border-gray-200 rounded-xl px-4 py-2 focus:border-barca-blue outline-none"></textarea><div class="flex justify-end gap-2"><button type="button" onclick="closeModal('articleModal')" class="text-gray-400 font-bold px-4">Batal</button><button class="bg-barca-gold text-barca-dark px-6 py-2 rounded-xl font-bold hover:bg-yellow-500">Terbitkan</button></div></form></div></div>

    <div id="editArticleModal" class="hidden fixed inset-0 bg-barca-dark/80 z-50 flex items-center justify-center p-4 backdrop-blur-sm"><div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl overflow-hidden"><div class="p-6 border-b border-gray-100 flex justify-between items-center"><h3 class="font-black text-lg text-barca-blue">Edit Artikel</h3><button onclick="closeModal('editArticleModal')"><i class="fas fa-times text-gray-400"></i></button></div><form action="process.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-4"><input type="hidden" name="action" value="edit_article"><input type="hidden" name="article_id" id="edit-id"><input type="hidden" name="old_image" id="edit-old-image"><div class="grid grid-cols-2 gap-4"><input type="text" name="title" id="edit-title" required class="border-2 border-gray-200 rounded-xl px-4 py-2 w-full font-bold focus:border-barca-blue outline-none"><select name="category_id" id="edit-category" class="border-2 border-gray-200 rounded-xl px-4 py-2 w-full font-bold focus:border-barca-blue outline-none bg-white"><?php foreach($allCats as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option><?php endforeach; ?></select></div><div class="flex gap-4 items-center bg-gray-50 p-3 rounded-xl"><img id="edit-preview" class="w-16 h-16 rounded-lg object-cover"><div class="flex-1"><div class="flex gap-2 mb-2"><button type="button" onclick="swIn('edit','file')" class="text-xs bg-barca-blue text-white px-2 py-1 rounded">File</button><button type="button" onclick="swIn('edit','url')" class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded">URL</button></div><input type="file" name="image_file" id="edit-file" class="w-full text-xs hidden"><input type="url" name="image_url" id="edit-url" class="w-full border rounded px-2 py-1 text-xs hidden"></div></div><textarea name="content" id="edit-content" rows="5" required class="w-full border-2 border-gray-200 rounded-xl px-4 py-2 focus:border-barca-blue outline-none"></textarea><div class="flex justify-end gap-2"><button type="button" onclick="closeModal('editArticleModal')" class="text-gray-400 font-bold px-4">Batal</button><button class="bg-barca-blue text-white px-6 py-2 rounded-xl font-bold">Update</button></div></form></div></div>

    <div id="adModal" class="hidden fixed inset-0 bg-barca-dark/80 z-50 flex items-center justify-center p-4 backdrop-blur-sm"><div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden"><div class="p-6 border-b border-gray-100"><h3 class="font-black text-lg text-barca-red">Iklan Baru</h3></div><form action="process.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-4"><input type="hidden" name="action" value="add_ad"><input type="text" name="title" required placeholder="Nama Kampanye" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2 font-bold focus:border-barca-red outline-none"><select name="type" id="add-ad-type" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2 font-bold focus:border-barca-red outline-none bg-white"><option value="banner_top">Top Banner</option><option value="horizontal_banner">Horizontal Banner</option><option value="sidebar_square">Sidebar Square</option><option value="popup">Popup Modal</option><option value="native_feed">Native Feed</option><option value="article_end">Bottom Article</option></select><div class="bg-gray-50 p-4 rounded-xl border-2 border-gray-100"><div class="flex gap-2 mb-2"><button type="button" onclick="swIn('ad','file')" class="text-xs bg-barca-red text-white px-2 py-1 rounded">File</button><button type="button" onclick="swIn('ad','url')" class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded">URL</button></div><input type="file" name="image_file" id="ad-file" class="w-full text-xs text-gray-500"><input type="url" name="image_url" id="ad-url" placeholder="https://..." class="w-full border rounded px-2 py-1 hidden"></div><input type="url" name="target_url" required placeholder="Link Tujuan" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2 font-bold focus:border-barca-red outline-none"><div class="flex justify-end gap-2"><button type="button" onclick="closeModal('adModal')" class="text-gray-400 font-bold px-4">Batal</button><button class="bg-barca-red text-white px-6 py-2 rounded-xl font-bold">Simpan</button></div></form></div></div>

    <div id="editAdModal" class="hidden fixed inset-0 bg-barca-dark/80 z-50 flex items-center justify-center p-4 backdrop-blur-sm"><div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden"><div class="p-6 border-b border-gray-100"><h3 class="font-black text-lg text-barca-blue">Edit Iklan</h3></div><form action="process.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-4"><input type="hidden" name="action" value="edit_ad"><input type="hidden" name="ad_id" id="edit-ad-id"><input type="hidden" name="old_image" id="edit-ad-old-image"><input type="text" name="title" id="edit-ad-title" required class="w-full border-2 border-gray-200 rounded-xl px-4 py-2 font-bold focus:border-barca-blue outline-none"><select name="type" id="edit-ad-type" class="w-full border-2 border-gray-200 rounded-xl px-4 py-2 font-bold focus:border-barca-blue outline-none bg-white"><option value="banner_top">Top Banner</option><option value="horizontal_banner">Horizontal Banner</option><option value="sidebar_square">Sidebar Square</option><option value="popup">Popup Modal</option><option value="native_feed">Native Feed</option><option value="article_end">Bottom Article</option></select><div class="bg-gray-50 p-4 rounded-xl border-2 border-gray-100 flex gap-4 items-center"><img id="edit-ad-preview" class="w-16 h-16 object-cover rounded-lg border"><div class="flex-1"><div class="flex gap-2 mb-2"><button type="button" onclick="swIn('edit-ad','file')" class="text-xs bg-barca-blue text-white px-2 py-1 rounded">File</button><button type="button" onclick="swIn('edit-ad','url')" class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded">URL</button></div><input type="file" name="image_file" id="edit-ad-file" class="w-full text-xs hidden"><input type="url" name="image_url" id="edit-ad-url" placeholder="URL Baru" class="w-full border rounded px-2 py-1 hidden"></div></div><input type="url" name="target_url" id="edit-ad-target" required class="w-full border-2 border-gray-200 rounded-xl px-4 py-2 font-bold focus:border-barca-blue outline-none"><div class="flex justify-end gap-2"><button type="button" onclick="closeModal('editAdModal')" class="text-gray-400 font-bold px-4">Batal</button><button class="bg-barca-blue text-white px-6 py-2 rounded-xl font-bold">Update</button></div></form></div></div>

    <script>
        function openModal(id){document.getElementById(id).classList.remove('hidden');}
        function closeModal(id){document.getElementById(id).classList.add('hidden');}
        function swIn(p,t){const f=document.getElementById(p+'-file'),u=document.getElementById(p+'-url');if(t==='file'){f.classList.remove('hidden');u.classList.add('hidden');}else{f.classList.add('hidden');u.classList.remove('hidden');}}
        function toggleAd(id,s){const fd=new FormData();fd.append('action','toggle_ad_status');fd.append('id',id);fd.append('current_status',s);fetch('process.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{if(d.status==='success')location.reload();});}
        function editArticle(a){document.getElementById('edit-id').value=a.id;document.getElementById('edit-title').value=a.title;document.getElementById('edit-category').value=a.category_id;document.getElementById('edit-content').value=a.content;document.getElementById('edit-old-image').value=a.image_url;document.getElementById('edit-preview').src=a.image_url.startsWith('http')?a.image_url:'../'+a.image_url;openModal('editArticleModal');}
        function delArticle(id){Swal.fire({title:'Hapus?',icon:'warning',showCancelButton:true,confirmButtonColor:'#ef4444'}).then((r)=>{if(r.isConfirmed){const fd=new FormData();fd.append('action','delete_article');fd.append('id',id);fetch('process.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{if(d.status==='success')location.reload();});}});}
        function editAd(ad){document.getElementById('edit-ad-id').value=ad.id;document.getElementById('edit-ad-title').value=ad.title;document.getElementById('edit-ad-type').value=ad.type;document.getElementById('edit-ad-target').value=ad.target_url;document.getElementById('edit-ad-old-image').value=ad.image_url;document.getElementById('edit-ad-preview').src=ad.image_url.startsWith('http')?ad.image_url:'../'+ad.image_url;openModal('editAdModal');}
        function delAd(id){Swal.fire({title:'Hapus Iklan?',icon:'warning',showCancelButton:true,confirmButtonColor:'#ef4444'}).then((r)=>{if(r.isConfirmed){const fd=new FormData();fd.append('action','delete_ad');fd.append('id',id);fetch('process.php',{method:'POST',body:fd}).then(r=>r.json()).then(d=>{if(d.status==='success')location.reload();});}});}
        if(new URLSearchParams(window.location.search).get('msg')) Swal.fire({icon:'success',title:'Berhasil',timer:1500,showConfirmButton:false});
    </script>
</body>
</html>