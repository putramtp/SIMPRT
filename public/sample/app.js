/* ============================================================
   SIPRT — Sistem Informasi Penugasan & Pelaporan Teknisi
   app.js
   ============================================================ */

/* ── Page metadata ── */
const PAGES = {
  'dashboard-sales':   { title: 'Dashboard Sales',     badge: '1 / 6' },
  'form-tugas':        { title: 'Form Buat Tugas',      badge: '2 / 6' },
  'dashboard-teknisi': { title: 'Dashboard Teknisi',    badge: '3 / 6' },
  'form-laporan':      { title: 'Form Laporan Teknisi', badge: '4 / 6' },
  'template-builder':  { title: 'Custom Template',      badge: '5 / 6' },
  'laporan-customer':  { title: 'Laporan Customer',     badge: '6 / 6' },
};

/* ── Current state ── */
let currentPage = 'dashboard-sales';

/* ── Navigate to a page ── */
function goTo(pageId) {
  if (!PAGES[pageId]) return;

  // Hide all pages
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));

  // Show target page
  const target = document.getElementById('page-' + pageId);
  if (target) {
    target.classList.add('active');
    // Scroll to top of page body
    const body = target.querySelector('.page-body');
    if (body) body.scrollTop = 0;
  }

  // Update sidebar active state
  document.querySelectorAll('.nav-item').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.page === pageId);
  });

  // Update topbar
  document.getElementById('topbarTitle').textContent = PAGES[pageId].title;
  document.getElementById('topbarBadge').textContent = PAGES[pageId].badge;

  currentPage = pageId;

  // Close sidebar on mobile after navigation
  document.getElementById('sidebar').classList.remove('open');
}

/* ── Sidebar toggle (mobile) ── */
document.getElementById('menuToggle').addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('open');
});

/* ── Sidebar nav click ── */
document.querySelectorAll('.nav-item').forEach(btn => {
  btn.addEventListener('click', () => goTo(btn.dataset.page));
});

/* ── Filter tabs ── */
document.querySelectorAll('.filter-tabs').forEach(container => {
  container.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      container.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
    });
  });
});

/* ── Priority / segment selectors ── */
document.querySelectorAll('.priority-selector').forEach(selector => {
  selector.querySelectorAll('.priority-opt').forEach(opt => {
    opt.addEventListener('click', () => {
      selector.querySelectorAll('.priority-opt').forEach(o => o.classList.remove('selected'));
      opt.classList.add('selected');
    });
  });
});

/* ── Tab bars (template builder) ── */
document.querySelectorAll('.tab-bar').forEach(bar => {
  bar.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
      bar.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
    });
  });
});

/* ── Toggle switches ── */
document.querySelectorAll('.toggle').forEach(toggle => {
  toggle.addEventListener('click', () => toggle.classList.toggle('on'));
});

/* ── Checklist items ── */
document.querySelectorAll('.check-item').forEach(item => {
  item.addEventListener('click', () => {
    const box = item.querySelector('.check-box');
    if (!box) return;
    const isChecked = box.classList.contains('checked') || box.classList.contains('green-check');
    if (isChecked) {
      box.classList.remove('checked', 'green-check');
      box.innerHTML = '';
    } else {
      box.classList.add('checked');
      box.innerHTML = '<i class="ti ti-check"></i>';
    }
  });
});

/* ── Star rating (customer page) ── */
document.querySelectorAll('.star-row').forEach(row => {
  const stars = row.querySelectorAll('.star');
  stars.forEach((star, idx) => {
    star.style.cursor = 'pointer';
    star.addEventListener('click', () => {
      stars.forEach((s, i) => {
        if (i <= idx) {
          s.classList.remove('empty');
          s.classList.add('gold');
          s.className = s.className.replace('ti-star ', 'ti-star-filled ');
          // Handle tabler icon class
          if (s.classList.contains('ti-star') && !s.classList.contains('ti-star-filled')) {
            s.classList.remove('ti-star');
            s.classList.add('ti-star-filled');
          }
        } else {
          s.classList.remove('gold');
          s.classList.add('empty');
          if (s.classList.contains('ti-star-filled')) {
            s.classList.remove('ti-star-filled');
            s.classList.add('ti-star');
          }
        }
      });
    });
    star.addEventListener('mouseenter', () => {
      stars.forEach((s, i) => {
        s.style.opacity = i <= idx ? '1' : '0.4';
      });
    });
    row.addEventListener('mouseleave', () => {
      stars.forEach(s => s.style.opacity = '1');
    });
  });
});

/* ── Tech select cards ── */
document.querySelectorAll('.tech-select-card:not(.disabled)').forEach(card => {
  card.addEventListener('click', () => {
    const parent = card.closest('.card');
    parent.querySelectorAll('.tech-select-card').forEach(c => {
      c.classList.remove('selected');
      const circle = c.querySelector('.check-circle');
      if (circle) { circle.classList.remove('filled'); circle.innerHTML = ''; }
    });
    card.classList.add('selected');
    const circle = card.querySelector('.check-circle');
    if (circle) { circle.classList.add('filled'); circle.innerHTML = '<i class="ti ti-check"></i>'; }
  });
});

/* ── Builder fields: click to activate ── */
document.querySelectorAll('.builder-field').forEach(field => {
  field.addEventListener('click', () => {
    document.querySelectorAll('.builder-field').forEach(f => {
      f.classList.remove('active');
      const bar = f.querySelector('.field-active-bar');
      if (bar) bar.remove();
    });
    field.classList.add('active');
    if (!field.querySelector('.field-active-bar')) {
      const bar = document.createElement('div');
      bar.className = 'field-active-bar';
      field.appendChild(bar);
    }
  });
});

/* ── Upload area click feedback ── */
document.querySelectorAll('.upload-area').forEach(area => {
  area.addEventListener('click', () => {
    area.style.borderColor = 'var(--blue)';
    area.style.background  = 'var(--blue-light)';
    setTimeout(() => {
      area.style.borderColor = '';
      area.style.background  = '';
    }, 600);
  });
});

/* ── Photo slot add button ── */
document.querySelectorAll('.photo-slot.add').forEach(slot => {
  slot.addEventListener('click', () => {
    slot.style.background = 'var(--blue-light)';
    setTimeout(() => slot.style.background = '', 400);
  });
});

/* ── "Add Section" button feedback ── */
document.querySelectorAll('.add-section-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.style.background = 'var(--blue-light)';
    btn.style.borderColor = 'var(--blue)';
    setTimeout(() => {
      btn.style.background = '';
      btn.style.borderColor = '';
    }, 500);
  });
});

/* ── Bottom nav items ── */
document.querySelectorAll('.bottom-nav').forEach(nav => {
  nav.querySelectorAll('.bn-item').forEach(item => {
    item.addEventListener('click', () => {
      nav.querySelectorAll('.bn-item').forEach(i => i.classList.remove('active'));
      item.classList.add('active');
    });
  });
});

/* ── Close sidebar when clicking outside (mobile) ── */
document.addEventListener('click', e => {
  const sidebar = document.getElementById('sidebar');
  const toggle  = document.getElementById('menuToggle');
  if (sidebar.classList.contains('open') &&
      !sidebar.contains(e.target) &&
      !toggle.contains(e.target)) {
    sidebar.classList.remove('open');
  }
});

/* ── Keyboard navigation (arrow keys / number keys) ── */
const PAGE_ORDER = Object.keys(PAGES);
document.addEventListener('keydown', e => {
  const idx = PAGE_ORDER.indexOf(currentPage);
  if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
    e.preventDefault();
    if (idx < PAGE_ORDER.length - 1) goTo(PAGE_ORDER[idx + 1]);
  } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
    e.preventDefault();
    if (idx > 0) goTo(PAGE_ORDER[idx - 1]);
  } else if (e.key >= '1' && e.key <= '6') {
    goTo(PAGE_ORDER[parseInt(e.key) - 1]);
  }
});

/* ── Swipe navigation (touch) ── */
(function initSwipe() {
  let startX = 0, startY = 0;
  const frame = document.querySelector('.phone-frame');
  frame.addEventListener('touchstart', e => {
    startX = e.touches[0].clientX;
    startY = e.touches[0].clientY;
  }, { passive: true });
  frame.addEventListener('touchend', e => {
    const dx = e.changedTouches[0].clientX - startX;
    const dy = e.changedTouches[0].clientY - startY;
    // Only horizontal swipes wider than 50px and not primarily vertical
    if (Math.abs(dx) < 50 || Math.abs(dy) > Math.abs(dx)) return;
    const idx = PAGE_ORDER.indexOf(currentPage);
    if (dx < 0 && idx < PAGE_ORDER.length - 1) goTo(PAGE_ORDER[idx + 1]); // swipe left → next
    if (dx > 0 && idx > 0)                      goTo(PAGE_ORDER[idx - 1]); // swipe right → prev
  }, { passive: true });
})();

/* ── Drag-and-drop reorder for builder fields ── */
(function initDragDrop() {
  let dragged = null;

  document.querySelectorAll('.builder-field').forEach(field => {
    field.setAttribute('draggable', 'true');

    field.addEventListener('dragstart', e => {
      dragged = field;
      setTimeout(() => field.classList.add('dragging'), 0);
      e.dataTransfer.effectAllowed = 'move';
    });

    field.addEventListener('dragend', () => {
      dragged = null;
      field.classList.remove('dragging');
      document.querySelectorAll('.builder-field').forEach(f => f.classList.remove('drag-over'));
    });

    field.addEventListener('dragover', e => {
      e.preventDefault();
      if (field === dragged) return;
      field.classList.add('drag-over');
      const canvas = field.closest('.builder-canvas');
      const fields = [...canvas.querySelectorAll('.builder-field')];
      const draggedIdx = fields.indexOf(dragged);
      const targetIdx  = fields.indexOf(field);
      if (draggedIdx < targetIdx) {
        field.after(dragged);
      } else {
        field.before(dragged);
      }
    });

    field.addEventListener('dragleave', () => field.classList.remove('drag-over'));
    field.addEventListener('drop', e => { e.preventDefault(); field.classList.remove('drag-over'); });
  });

  // Add CSS for drag states inline (keeps JS self-contained)
  const style = document.createElement('style');
  style.textContent = `
    .builder-field.dragging  { opacity: .4; border: 1.5px dashed var(--blue); }
    .builder-field.drag-over { border-color: var(--blue); background: var(--blue-light); }
    .builder-field[draggable] { user-select: none; }
  `;
  document.head.appendChild(style);
})();

/* ── Delete field buttons in builder ── */
document.querySelectorAll('.builder-field .field-actions .ti-trash').forEach(icon => {
  icon.addEventListener('click', e => {
    e.stopPropagation();
    const field = icon.closest('.builder-field');
    if (!field) return;
    field.style.transition = 'opacity .2s, transform .2s';
    field.style.opacity = '0';
    field.style.transform = 'translateX(20px)';
    setTimeout(() => field.remove(), 200);
  });
});

/* ── Inline form editing (contenteditable on click for mockup fields) ── */
document.querySelectorAll('.form-input:not(.disabled), .form-textarea').forEach(el => {
  if (el.classList.contains('readonly')) return;
  el.setAttribute('contenteditable', 'true');
  el.style.outline = 'none';
  el.addEventListener('focus', () => {
    el.style.borderColor  = 'var(--blue)';
    el.style.background   = '#fff';
    el.style.boxShadow    = '0 0 0 3px rgba(21,101,192,0.12)';
  });
  el.addEventListener('blur', () => {
    el.style.borderColor  = '';
    el.style.background   = '';
    el.style.boxShadow    = '';
  });
});

/* ── Palette items: animate tap feedback ── */
document.querySelectorAll('.palette-item').forEach(item => {
  item.addEventListener('click', () => {
    item.style.transition = 'transform .1s';
    item.style.transform  = 'scale(0.96)';
    item.style.borderColor = 'var(--blue)';
    item.style.background  = 'var(--blue-light)';
    setTimeout(() => {
      item.style.transform  = '';
      item.style.borderColor = '';
      item.style.background  = '';
    }, 300);
    showToast('Field ditambahkan ke template');
  });
});

/* ── Toast notification ── */
function showToast(msg, duration = 2200) {
  const existing = document.querySelector('.siprt-toast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.className = 'siprt-toast';
  toast.textContent = msg;
  toast.style.cssText = `
    position: fixed;
    bottom: 28px;
    left: 50%;
    transform: translateX(-50%) translateY(10px);
    background: #1a1d23;
    color: #fff;
    padding: 10px 20px;
    border-radius: 24px;
    font-size: 13px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    z-index: 9999;
    opacity: 0;
    transition: opacity .2s, transform .2s;
    white-space: nowrap;
    pointer-events: none;
  `;
  document.body.appendChild(toast);
  requestAnimationFrame(() => {
    toast.style.opacity   = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';
  });
  setTimeout(() => {
    toast.style.opacity   = '0';
    toast.style.transform = 'translateX(-50%) translateY(10px)';
    setTimeout(() => toast.remove(), 220);
  }, duration);
}

/* ── Button feedback: Simpan Draft / Kirim / etc. ── */
document.querySelectorAll('.btn-primary, .btn-secondary, .btn-yellow').forEach(btn => {
  btn.addEventListener('click', e => {
    const txt = btn.textContent.trim().toLowerCase();

    if (txt.includes('simpan draft')) {
      showToast('Draft berhasil disimpan');
    } else if (txt.includes('kirim laporan')) {
      showToast('Laporan berhasil dikirim ke Sales');
    } else if (txt.includes('kirim tugas')) {
      showToast('Tugas berhasil dikirim ke Teknisi');
    } else if (txt.includes('simpan template')) {
      showToast('Template berhasil disimpan');
    } else if (txt.includes('kirim penilaian')) {
      showToast('Terima kasih atas penilaian Anda!');
    } else if (txt.includes('unduh pdf')) {
      showToast('Mengunduh laporan PDF...');
    } else if (txt.includes('bagikan')) {
      // Simulate copy link
      navigator.clipboard?.writeText('https://siprt.app/report/LPR-2026-0412').catch(() => {});
      showToast('Link laporan disalin ke clipboard');
    } else if (txt.includes('duplikat')) {
      showToast('Template berhasil diduplikat');
    } else if (txt.includes('mulai tugas')) {
      showToast('Status tugas diperbarui: Berlangsung');
    } else if (txt.includes('navigasi')) {
      showToast('Membuka navigasi ke lokasi...');
    }
  });
});

/* ── Status strip "Ubah" button ── */
document.querySelectorAll('.status-change-btn').forEach(btn => {
  const statuses = [
    { label: 'Aktif & Tersedia', color: '#69F0AE' },
    { label: 'Sedang Bertugas',  color: '#F9A825'  },
    { label: 'Istirahat',        color: '#90CAF9'  },
    { label: 'Tidak Aktif',      color: '#bdbdbd'  },
  ];
  let idx = 0;
  btn.addEventListener('click', () => {
    idx = (idx + 1) % statuses.length;
    const strip = btn.closest('.status-strip');
    const dot   = strip.querySelector('.status-dot');
    const label = strip.querySelector('strong');
    if (dot)   dot.style.background   = statuses[idx].color;
    if (label) label.textContent       = statuses[idx].label;
    showToast('Status diperbarui: ' + statuses[idx].label);
  });
});

/* ── Task card "Isi Laporan" button highlight ── */
document.querySelectorAll('.task-card .btn-primary').forEach(btn => {
  btn.addEventListener('mouseenter', () => {
    btn.closest('.task-card')?.style.setProperty('box-shadow', '0 4px 16px rgba(21,101,192,0.15)');
  });
  btn.addEventListener('mouseleave', () => {
    btn.closest('.task-card')?.style.setProperty('box-shadow', '');
  });
});

/* ── Sign canvas: click to "sign" ── */
document.querySelectorAll('.sign-canvas.empty').forEach(canvas => {
  canvas.addEventListener('click', () => {
    canvas.classList.remove('empty');
    canvas.classList.add('signed');
    canvas.innerHTML = `
      <svg viewBox="0 0 80 36" xmlns="http://www.w3.org/2000/svg">
        <path d="M8 24 Q18 6 28 18 Q38 30 50 12 Q60 0 72 16"
              stroke="#2E7D32" stroke-width="2.5" fill="none" stroke-linecap="round"/>
      </svg>`;
    // Update status text below
    const box  = canvas.closest('.sign-box');
    const stat = box?.querySelector('.sign-status');
    if (stat) {
      stat.className = 'sign-status ok';
      const now = new Date();
      stat.innerHTML = `<i class="ti ti-check"></i> ${now.getDate()} Mei, ${now.getHours()}:${String(now.getMinutes()).padStart(2,'0')}`;
    }
    showToast('Tanda tangan berhasil disimpan');
  });
});

/* ── Init ── */
goTo('dashboard-sales');
console.log('SIPRT Mockup loaded. Pages:', Object.keys(PAGES).join(', '));
console.log('Tips: Gunakan ← → atau tombol angka 1-6 untuk navigasi antar halaman.');
