<?php 

$cats = ['La Liga' => 'la-liga', 'Premier League' => 'premier-league', 'Champions League' => 'champions-league'];
?>

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

<nav class="bg-barca-blue border-b-4 border-barca-red sticky top-0 z-40 shadow-xl">
    <div class="container mx-auto px-4 h-20 flex justify-between items-center">
        <a href="index.php" class="flex items-center gap-3 group">
            <div class="w-10 h-10 bg-barca-gold rounded-lg flex items-center justify-center text-barca-blue text-xl shadow-lg group-hover:rotate-12 transition-transform"><i class="fas fa-futbol"></i></div>
            <span class="text-2xl font-black tracking-tight text-white">Sky<span class="text-barca-gold">Goal</span>.</span>
        </a>
        <div class="hidden md:flex items-center gap-2">
            <a href="index.php" class="px-4 py-2 rounded-lg text-sm font-bold text-white/90 hover:text-barca-gold hover:bg-white/10 transition-all">Beranda</a>
            <?php foreach($cats as $name => $slug): ?>
            <a href="category.php?slug=<?php echo $slug; ?>" class="px-4 py-2 rounded-lg text-sm font-bold text-white/90 hover:text-barca-gold hover:bg-white/10 transition-all">
                <?php echo $name; ?>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="Swal.fire('Berhasil!', 'Anda telah berlangganan newsletter kami.', 'success')" class="hidden md:block bg-barca-red text-white px-6 py-2.5 rounded-lg font-bold shadow-lg text-xs uppercase tracking-wider transform hover:-translate-y-0.5 transition">Subscribe</button>
            <button onclick="document.getElementById('mob-menu').classList.toggle('hidden')" class="md:hidden w-10 h-10 bg-barca-red text-white rounded-lg flex items-center justify-center hover:bg-barca-gold hover:text-barca-blue transition"><i class="fas fa-bars"></i></button>
        </div>
    </div>
    <div id="mob-menu" class="hidden md:hidden bg-barca-blue border-t border-white/10 p-4 space-y-2 absolute w-full shadow-xl z-50">
        <a href="index.php" class="block px-4 py-3 text-white font-bold hover:text-barca-gold border-b border-white/5 hover:bg-white/5 transition">Beranda</a>
        <?php foreach($cats as $name => $slug): ?>
        <a href="category.php?slug=<?php echo $slug; ?>" class="block px-4 py-3 text-white font-bold hover:text-barca-gold border-b border-white/5 hover:bg-white/5 transition"><?php echo $name; ?></a>
        <?php endforeach; ?>
    </div>
</nav>