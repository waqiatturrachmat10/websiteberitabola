/* script.js */

// --- DATA DUMMY (Untuk simulasi Load More) ---
const moreNewsData = [
    { category: 'Serie A', title: 'Juventus Menang Dramatis di Menit Akhir', time: '7 jam lalu', img: 'https://images.unsplash.com/photo-1518091043644-c1d4457512c6?auto=format&fit=crop&w=500' },
    { category: 'La Liga', title: 'Barcelona Mengumumkan Kapten Baru', time: '8 jam lalu', img: 'https://images.unsplash.com/photo-1560272564-c83b66b1ad12?auto=format&fit=crop&w=500' },
    { category: 'Transfers', title: 'Rumor: Madrid Incar Bek Inggris', time: '9 jam lalu', img: 'https://images.unsplash.com/photo-1504124639248-dc58a3a0e664?auto=format&fit=crop&w=500' }
];

// --- 1. POPUP ADS LOGIC ---
document.addEventListener('DOMContentLoaded', () => {
    // Tampilkan iklan setelah 2.5 detik
    setTimeout(() => {
        toggleAd('popup-ad', true);
    }, 2500);
});

function toggleAd(id, show) {
    const el = document.getElementById(id);
    const content = el.querySelector('div'); // Child div for animation
    
    if (show) {
        el.classList.remove('hidden');
        // Delay sedikit agar transisi opacity berjalan
        setTimeout(() => {
            el.classList.remove('opacity-0');
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 50);
    } else {
        el.classList.add('opacity-0');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => {
            el.classList.add('hidden');
        }, 300);
    }
}

// --- 2. MOBILE MENU ---
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
}

// --- 3. SEARCH FUNCTIONALITY ---
function handleSearch(event, isMobile = false) {
    if (event.key === 'Enter') {
        const id = isMobile ? 'mobileSearchInput' : 'searchInput';
        const query = document.getElementById(id).value;
        performSearch(query);
    }
}

function performSearch(queryInput = null) {
    const query = queryInput || document.getElementById('searchInput').value;
    if(!query) return;

    Swal.fire({
        title: 'Mencari...',
        text: `Menampilkan hasil untuk "${query}"`,
        icon: 'info',
        timer: 1500,
        showConfirmButton: false
    });
}

// --- 4. SUBSCRIBE FUNCTIONALITY ---
function handleSubscribe(e) {
    e.preventDefault();
    const email = document.getElementById('emailSubscribe').value;
    
    Swal.fire({
        title: 'Berlangganan!',
        text: `Terima kasih! Berita terbaru akan dikirim ke ${email}`,
        icon: 'success',
        confirmButtonColor: '#0ea5e9',
        confirmButtonText: 'Mantap!'
    });
    
    document.getElementById('emailSubscribe').value = ''; 
}

function scrollToFooter() {
    const footer = document.getElementById('newsletter-section');
    if(footer) footer.scrollIntoView({ behavior: 'smooth' });
}

// --- 5. LOAD MORE NEWS (AJAX SIMULATION) ---
function loadMoreNews() {
    const btn = document.getElementById('loadMoreBtn');
    const icon = document.getElementById('loadingIcon');
    const grid = document.getElementById('news-grid');

    // UI Loading state
    icon.classList.remove('hidden');
    btn.setAttribute('disabled', 'true');
    btn.querySelector('span').innerText = 'Memuat...';

    // Simulasi delay jaringan 1 detik
    setTimeout(() => {
        moreNewsData.forEach(news => {
            const articleHTML = `
            <article class="news-item bg-white rounded-xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden border border-slate-100 flex flex-col h-full cursor-pointer animate-fade-in" data-category="${news.category}" onclick="readArticle('${news.title}')">
                <div class="h-48 overflow-hidden relative">
                        <span class="absolute top-3 left-3 z-10 bg-white/90 backdrop-blur-sm text-slate-800 text-[10px] font-bold px-2 py-1 rounded shadow-sm uppercase">${news.category}</span>
                    <img src="${news.img}" class="w-full h-full object-cover">
                </div>
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center text-xs text-slate-400 mb-2"><i class="far fa-clock mr-1"></i> ${news.time}</div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2 leading-snug hover:text-sky-600">${news.title}</h3>
                    </div>
                    <span class="text-sky-600 font-semibold text-xs mt-4 flex items-center group">Baca Selengkapnya <i class="fas fa-arrow-right ml-1"></i></span>
                </div>
            </article>`;
            grid.insertAdjacentHTML('beforeend', articleHTML);
        });

        // Reset tombol & sembunyikan karena data dummy habis
        icon.classList.add('hidden');
        btn.classList.add('hidden'); 
        
    }, 1000);
}

// --- 6. CATEGORY FILTER ---
function filterNews(category) {
    const items = document.querySelectorAll('.news-item');
    const heading = document.getElementById('news-heading');

    heading.innerText = category === 'all' ? 'Berita Terbaru' : `Kategori: ${category}`;

    items.forEach(item => {
        if (category === 'all' || item.getAttribute('data-category') === category) {
            item.classList.remove('hidden');
            item.classList.add('flex'); 
        } else {
            item.classList.add('hidden');
            item.classList.remove('flex');
        }
    });
}

// --- 7. ARTICLE CLICK HANDLER ---
function readArticle(title) {
    console.log("Navigasi ke: " + title);
    // window.location.href = 'article.php?slug=' + encodeURIComponent(title);
}

// --- NOTIFIKASI SYSTEM (SWEETALERT) ---
        const urlParams = new URLSearchParams(window.location.search);
        const msg = urlParams.get('msg');

        if(msg){
            // Default Config
            let icon = 'success';
            let title = 'Berhasil!';
            let text = 'Operasi berhasil dilakukan.';

            // Custom Messages
            if(msg === 'cat_added') { 
                title = 'Kategori Ditambahkan'; 
            }
            else if(msg === 'cat_duplicate') { 
                icon = 'error';
                title = 'Gagal Menambah';
                text = 'Nama Kategori tersebut sudah ada!';
            }
            else if(msg === 'article_added') { title = 'Artikel Diterbitkan'; }
            else if(msg === 'article_updated') { title = 'Artikel Diperbarui'; }
            else if(msg === 'ad_added') { title = 'Iklan Dibuat'; }
            else if(msg === 'ad_updated') { title = 'Iklan Diperbarui'; }
            
            // Tampilkan Alert
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                showConfirmButton: (icon === 'error'), // Tampilkan tombol OK jika error
                timer: (icon === 'error' ? undefined : 1500), // Otomatis tutup jika sukses
                confirmButtonColor: '#A50044' // Warna Merah Barca
            }).then(() => {
                // Bersihkan URL agar tidak muncul lagi saat refresh
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }