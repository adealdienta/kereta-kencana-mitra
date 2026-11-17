/**
 * Main JavaScript: PR. KERETA KENCANA
 * Menangani Age Gate 21+, Mobile Navigation, Live Search & Coverflow Varian
 */

document.addEventListener('DOMContentLoaded', function () {
  // 1. Verifikasi Usia 21+ (Age Gate)
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

  // 6. Repeatable Smooth Scroll Reveal Observer (Aktif Berulang Saat Scroll Atas/Bawah)
  const reveals = document.querySelectorAll('.scroll-reveal');
  if (reveals.length > 0) {
    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-revealed');
          } else {
            entry.target.classList.remove('is-revealed');
          }
        });
      }, {
        root: null,
        rootMargin: '0px 0px -20px 0px',
        threshold: 0.05
      });

      reveals.forEach(el => observer.observe(el));
    } else {
      reveals.forEach(el => el.classList.add('is-revealed'));
    }
  }

  // 7. Djarum-Style 3D Coverflow Showcase & Variant Detail Overlay (#produk)
  const coverflowRoot = document.getElementById('kkCoverflowShowcase');
  if (coverflowRoot) {
    const cards = Array.from(coverflowRoot.querySelectorAll('.kk-spice-card'));
    const dots = Array.from(coverflowRoot.querySelectorAll('.kk-coverflow-dot'));
    const prevBtn = coverflowRoot.querySelector('.kk-coverflow-prev');
    const nextBtn = coverflowRoot.querySelector('.kk-coverflow-next');
    const track = coverflowRoot.querySelector('.kk-coverflow-track');
    const total = cards.length;

    let activeIndex = Math.min(1, Math.max(0, total - 1));

    const getCircularOffset = (index, center, length) => {
      let diff = (index - center + length) % length;
      if (diff > length / 2) diff -= length;
      return diff;
    };

    const updateCoverflow = () => {
      cards.forEach((card, idx) => {
        const offset = getCircularOffset(idx, activeIndex, total);
        card.setAttribute('data-pos', String(offset));
        card.setAttribute('aria-hidden', offset === 0 ? 'false' : 'true');
      });
      dots.forEach((dot, idx) => {
        dot.classList.toggle('is-active', idx === activeIndex);
      });
    };

    const stepCoverflow = (delta) => {
      activeIndex = (activeIndex + delta + total) % total;
      updateCoverflow();
    };

    if (prevBtn) prevBtn.addEventListener('click', () => stepCoverflow(-1));
    if (nextBtn) nextBtn.addEventListener('click', () => stepCoverflow(1));

    dots.forEach((dot, idx) => {
      dot.addEventListener('click', () => {
        activeIndex = idx;
        updateCoverflow();
      });
    });

    // Modal Elements
    const modal = document.getElementById('kkVariantModal');
    const modalImg = document.getElementById('kkModalImg');
    const modalTitle = document.getElementById('kkModalTitle');
    const modalSub = document.getElementById('kkModalSub');
    const modalTaste = document.getElementById('kkModalTaste');
    const modalDesc = document.getElementById('kkModalDesc');
    const modalDetailLink = document.getElementById('kkModalDetailLink');
    const modalOrderLink = document.getElementById('kkModalOrderLink');
    const modalClose = document.getElementById('kkModalClose');
    const modalPrev = document.getElementById('kkModalPrev');
    const modalNext = document.getElementById('kkModalNext');

    const syncModalContent = (idx) => {
      const card = cards[idx];
      if (!card || !modal) return;
      modal.style.backgroundColor = card.dataset.color || '#6b5536';
      modalImg.src = card.dataset.image || '';
      modalImg.alt = card.dataset.name || '';
      modalTitle.textContent = card.dataset.name || '';
      modalSub.textContent = card.dataset.sub || '';
      modalTaste.textContent = card.dataset.taste || '';
      modalDesc.textContent = card.dataset.desc || '';
      modalDetailLink.href = card.dataset.url || '#';
      if (modalOrderLink) {
        const baseOrderUrl = modalOrderLink.dataset.baseUrl || '/pesan';
        const slug = card.dataset.slug || '';
        modalOrderLink.href = slug ? `${baseOrderUrl}?produk=${encodeURIComponent(slug)}` : baseOrderUrl;
      }
    };

    const openModalAt = (idx) => {
      if (!modal) return;
      activeIndex = idx;
      updateCoverflow();
      syncModalContent(idx);
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    };

    const closeModal = () => {
      if (!modal) return;
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    };

    cards.forEach((card, idx) => {
      card.addEventListener('click', (e) => {
        const isCtaClick = e.target.closest('.kk-spice-cta');
        const offset = Number(card.getAttribute('data-pos'));
        if (offset !== 0 && !isCtaClick) {
          activeIndex = idx;
          updateCoverflow();
          return;
        }
        openModalAt(idx);
      });
    });

    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modalPrev) modalPrev.addEventListener('click', () => {
      stepCoverflow(-1);
      syncModalContent(activeIndex);
    });
    if (modalNext) modalNext.addEventListener('click', () => {
      stepCoverflow(1);
      syncModalContent(activeIndex);
    });

    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
      });
    }

    document.addEventListener('keydown', (e) => {
      if (modal && modal.classList.contains('is-open')) {
        if (e.key === 'Escape') closeModal();
        if (e.key === 'ArrowLeft') {
          stepCoverflow(-1);
          syncModalContent(activeIndex);
        }
        if (e.key === 'ArrowRight') {
          stepCoverflow(1);
          syncModalContent(activeIndex);
        }
      }
    });

    // Touch swipe support on carousel track
    if (track) {
      let touchStartX = 0;
      track.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].clientX;
      }, { passive: true });
      track.addEventListener('touchend', (e) => {
        const diff = e.changedTouches[0].clientX - touchStartX;
        if (Math.abs(diff) > 42) {
          stepCoverflow(diff < 0 ? 1 : -1);
        }
      }, { passive: true });
    }

    updateCoverflow();
  }
});
