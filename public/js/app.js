// DigitalsPos — Enterprise POS App JS

const SIDEBAR_COLLAPSED_KEY = 'digitalspos_sidebar_collapsed';
const NAV_STATE_KEY         = 'digitalspos_nav_open_groups';

// ── Toggle de barra lateral (3 barras / Hamburguesa) ─────────────
function toggleSidebar() {
  const wrapper = document.querySelector('.app-wrapper');
  const sidebar = document.getElementById('sidebar');

  if (window.innerWidth <= 768) {
    // Modo Mobile / Tablet
    if (sidebar) sidebar.classList.toggle('open');
  } else {
    // Modo Desktop: Colapsar a modo compacto
    if (wrapper) {
      const isCollapsed = wrapper.classList.toggle('sidebar-collapsed');
      localStorage.setItem(SIDEBAR_COLLAPSED_KEY, isCollapsed ? '1' : '0');
    }
  }
}

// ── Pantalla Completa (Modo Quiosco / POS) ────────────────────────
function toggleFullScreen() {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen().catch(err => {
      console.warn("Error al activar pantalla completa: ", err);
    });
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    }
  }
}

// ── Dropdown de Notificaciones ────────────────────────────────────
function toggleNotifDropdown() {
  const menu = document.getElementById('notifDropdownMenu');
  if (menu) {
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
  }
}

// ── Acordeón del menú lateral ─────────────────────────────────────
function getStoredNavGroups() {
  try { return JSON.parse(localStorage.getItem(NAV_STATE_KEY)) || []; }
  catch { return []; }
}

function toggleNavGroup(headerEl) {
  const group = headerEl.closest('.nav-group');
  if (!group) return;
  const name   = group.dataset.group;
  const isOpen = group.classList.toggle('open');

  let stored = getStoredNavGroups();
  if (isOpen && !stored.includes(name)) stored.push(name);
  if (!isOpen) stored = stored.filter(g => g !== name);
  localStorage.setItem(NAV_STATE_KEY, JSON.stringify(stored));
}

document.addEventListener('DOMContentLoaded', () => {
  const wrapper = document.querySelector('.app-wrapper');

  // Restaurar estado colapsado en desktop si el usuario lo guardó
  if (window.innerWidth > 768 && localStorage.getItem(SIDEBAR_COLLAPSED_KEY) === '1') {
    if (wrapper) wrapper.classList.add('sidebar-collapsed');
  }

  // Reabrir grupos que el usuario dejó abiertos
  getStoredNavGroups().forEach(name => {
    const group = document.querySelector(`.nav-group[data-group="${name}"]`);
    if (group && !group.classList.contains('open')) group.classList.add('open');
  });

  // Auto-ocultar flash messages
  const flash = document.getElementById('flash-msg');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity .4s';
      flash.style.opacity    = '0';
      setTimeout(() => flash.remove(), 400);
    }, 4000);
  }

  // Cerrar elementos emergentes al hacer clic fuera
  document.addEventListener('click', e => {
    const sidebar = document.getElementById('sidebar');
    const toggle  = document.getElementById('sidebarToggle');
    const notifBtn  = document.querySelector('.notif-btn');
    const notifMenu = document.getElementById('notifDropdownMenu');

    // Cerrar sidebar en mobile al hacer click afuera
    if (sidebar && sidebar.classList.contains('open') &&
        !sidebar.contains(e.target) && e.target !== toggle && !toggle.contains(e.target)) {
      sidebar.classList.remove('open');
    }

    // Cerrar dropdown de notificaciones si hace clic afuera
    if (notifMenu && notifMenu.style.display !== 'none') {
      if (!notifMenu.contains(e.target) && (!notifBtn || !notifBtn.contains(e.target))) {
        notifMenu.style.display = 'none';
      }
    }
  });
});
