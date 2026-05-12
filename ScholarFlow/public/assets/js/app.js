/**
 * ScholarFlow — Main JavaScript
 * Sidebar toggle (with overlay), auto-dismiss toasts, general UX enhancements
 */

document.addEventListener('DOMContentLoaded', () => {

  // ── Sidebar toggle (mobile + desktop safety) ──────────────
  const sidebar  = document.getElementById('sidebar');
  const openBtn  = document.getElementById('sidebarOpen');
  const closeBtn = document.getElementById('sidebarClose');

  // Create overlay element once and append to body
  let overlay = document.getElementById('sidebarOverlay');
  if (!overlay) {
    overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    overlay.id = 'sidebarOverlay';
    document.body.appendChild(overlay);
  }

  function openSidebar() {
    sidebar?.classList.add('open');
    overlay?.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar?.classList.remove('open');
    overlay?.classList.remove('active');
    document.body.style.overflow = '';
  }

  openBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    openSidebar();
  });

  closeBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    closeSidebar();
  });

  overlay?.addEventListener('click', () => closeSidebar());

  // Close on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sidebar?.classList.contains('open')) {
      closeSidebar();
    }
  });

  // Close sidebar when a nav link is clicked on small screens
  sidebar?.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
      if (window.innerWidth <= 1024) closeSidebar();
    });
  });

  // ── Auto-dismiss flash toasts ────────────────────────────
  document.querySelectorAll('.alert-toast').forEach(toast => {
    setTimeout(() => {
      toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(-8px)';
      setTimeout(() => toast.remove(), 400);
    }, 5000);
  });

  // ── Active nav link highlight ─────────────────────────────
  // Server-side handles .active class; JS adds it for JS-only cases
  const currentPath = window.location.pathname;
  document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (!href) return;
    try {
      const linkPath = new URL(href, window.location.origin).pathname;
      if (linkPath === currentPath) {
        link.classList.add('active');
      }
    } catch (_) {}
  });

  // ── Confirm delete forms ─────────────────────────────────
  document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', (e) => {
      if (!confirm(form.dataset.confirm || 'Are you sure?')) {
        e.preventDefault();
      }
    });
  });

  // ── Form validation UI ───────────────────────────────────
  document.querySelectorAll('form[novalidate]').forEach(form => {
    form.addEventListener('submit', (e) => {
      let valid = true;
      form.querySelectorAll('[required]').forEach(input => {
        if (!input.value.trim()) {
          input.style.borderColor = 'var(--danger)';
          valid = false;
        } else {
          input.style.borderColor = '';
        }
      });
      if (!valid) {
        e.preventDefault();
        const first = form.querySelector('[required]:invalid, [required][style*="danger"]');
        first?.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });
  });

  // ── Input error state reset ──────────────────────────────
  document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('input', () => {
      if (input.value.trim()) {
        input.style.borderColor = '';
      }
    });
  });

  // ── Number formatting in stat cards ──────────────────────
  document.querySelectorAll('.stat-num[data-count]').forEach(el => {
    const target = parseInt(el.dataset.count, 10);
    if (Number.isNaN(target)) return;

    let current = 0;
    const step = Math.ceil(target / 30);
    const timer = setInterval(() => {
      current = Math.min(current + step, target);
      el.textContent = current;
      if (current >= target) clearInterval(timer);
    }, 30);
  });

  // ── Clickable table rows ──────────────────────────────────
  document.querySelectorAll('.sf-table tbody tr[data-href]').forEach(row => {
    row.style.cursor = 'pointer';
    row.addEventListener('click', () => {
      window.location.href = row.dataset.href;
    });
  });

  // ── Applications filter (inline JS fallback) ─────────────
  const appSearch = document.getElementById('appSearch');
  const filterBtns = document.querySelectorAll('.filter-btn');
  let activeFilter = '';

  function filterRows() {
    const q = (appSearch?.value || '').toLowerCase();
    document.querySelectorAll('#appsTable tbody tr').forEach(row => {
      const matchS = !activeFilter || row.dataset.status === activeFilter;
      const matchQ = (row.dataset.search || '').includes(q);
      row.style.display = matchS && matchQ ? '' : 'none';
    });
  }

  appSearch?.addEventListener('input', filterRows);
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      activeFilter = btn.dataset.status;
      filterRows();
    });
  });

});

