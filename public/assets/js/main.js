/**
 * Main JavaScript: PR. KERETA KENCANA
 * Menangani Age Gate 18+, Mobile Navigation, dan Live Search & Filter Produk
 */

document.addEventListener('DOMContentLoaded', function () {
  // 1. Verifikasi Usia 18+ (Age Gate)
  const ageGateOverlay = document.getElementById('ageGateOverlay');
  const btnAcceptAge = document.getElementById('btnAcceptAge');
  const btnRejectAge = document.getElementById('btnRejectAge');

  if (ageGateOverlay) {
    const isVerified = localStorage.getItem('kk_age_verified');
    if (!isVerified) {
      ageGateOverlay.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    } else {
      ageGateOverlay.style.display = 'none';
    }

    if (btnAcceptAge) {
      btnAcceptAge.addEventListener('click', function () {
        localStorage.setItem('kk_age_verified', 'true');
        ageGateOverlay.style.display = 'none';
        document.body.style.overflow = 'auto';
      });
    }

    if (btnRejectAge) {
      btnRejectAge.addEventListener('click', function () {
        const modal = ageGateOverlay.querySelector('.age-gate-modal');
        if (modal) {
          modal.innerHTML = `
            <div class="age-badge" style="background: #ef4444; color: #fff;">Akses Dibatasi</div>
            <h2 class="age-gate-title" style="margin-top: 16px;">MOHON MAAF</h2>
            <p class="age-gate-text" style="color: #cbd5e1;">
              Sesuai dengan regulasi PP 28/2024, materi informasi produk hasil tembakau hanya diperuntukkan bagi mitra usaha yang telah berusia <strong>21 tahun ke atas</strong>.
            </p>
            <p style="font-size: 0.85rem; color: #94a3b8; margin-top: 12px;">
              Anda dapat menutup tab browser ini. Terima kasih atas pengertiannya.
            </p>
          `;
        }
      });
    }
  }

  // 2. Mobile Menu Toggle
  const mobileToggle = document.getElementById('mobileToggle');
  const mainNav = document.getElementById('mainNav');

  if (mobileToggle && mainNav) {
    mobileToggle.addEventListener('click', function () {
      mainNav.classList.toggle('active');
    });

    // Tutup saat link anchor diklik di mobile
    const navLinks = mainNav.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
      link.addEventListener('click', function () {
        mainNav.classList.remove('active');
      });
    });
  }

  // 3. Filter & Live Search di Katalog Produk
  const searchInput = document.getElementById('catalogSearchInput');
  const filterBtns = document.querySelectorAll('.filter-btn');
  const productCards = document.querySelectorAll('.product-card-item');

  let currentCategory = 'SEMUA';
  let currentSearchQuery = '';

  function applyProductFilters() {
    let visibleCount = 0;

    productCards.forEach(card => {
      const cardCategory = card.getAttribute('data-category') || '';
      const cardName = (card.getAttribute('data-name') || '').toLowerCase();
      const cardDesc = (card.getAttribute('data-desc') || '').toLowerCase();

      const matchesCategory = (currentCategory === 'SEMUA' || cardCategory === currentCategory);
      const matchesSearch = currentSearchQuery === '' || 
                            cardName.includes(currentSearchQuery) || 
                            cardDesc.includes(currentSearchQuery);

      if (matchesCategory && matchesSearch) {
        card.style.display = 'flex';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    const noResultEl = document.getElementById('noProductResult');
    if (noResultEl) {
      noResultEl.style.display = (visibleCount === 0) ? 'block' : 'none';
    }
  }

  if (filterBtns.length > 0) {
    filterBtns.forEach(btn => {
      btn.addEventListener('click', function () {
        filterBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentCategory = this.getAttribute('data-category') || 'SEMUA';
        applyProductFilters();
      });
    });
  }

  if (searchInput) {
    searchInput.addEventListener('input', function () {
      currentSearchQuery = this.value.trim().toLowerCase();
      applyProductFilters();
    });
  }

  // 4. Interactive Button Ripple Feedback
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn');
    if (!btn) return;

    const circle = document.createElement('span');
    const diameter = Math.max(btn.clientWidth, btn.clientHeight);
    const radius = diameter / 2;
    const rect = btn.getBoundingClientRect();

    circle.style.width = circle.style.height = `${diameter}px`;
    circle.style.left = `${e.clientX - rect.left - radius}px`;
    circle.style.top = `${e.clientY - rect.top - radius}px`;
    circle.classList.add('btn-ripple');

    const existingRipple = btn.querySelector('.btn-ripple');
    if (existingRipple) {
      existingRipple.remove();
    }

    btn.appendChild(circle);
  });

  // 5. Visual Feedback on Form Submission (Loading State & Anti-Double Click)
  const forms = document.querySelectorAll('form');
  forms.forEach(form => {
    form.addEventListener('submit', function (e) {
      if (!this.checkValidity()) return;

      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn && !submitBtn.classList.contains('no-loader')) {
        submitBtn.classList.add('is-loading');
        submitBtn.style.pointerEvents = 'none';
        submitBtn.style.opacity = '0.85';
        submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Memproses...';
      }
    });
  });
});
