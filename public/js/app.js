// DigitalsPos — Enterprise POS App JS

const SIDEBAR_COLLAPSED_KEY = 'digitalspos_sidebar_collapsed';
const DARK_MODE_KEY         = 'digitalspos_dark_mode';
const ACTIVE_GROUP_KEY      = 'digitalspos_active_nav_group';

// ── Toggle de Modo Oscuro (Convierte paneles blancos a oscuro con letras blancas) ────
function toggleDarkMode() {
  const isDark = document.body.classList.toggle('dark-mode');
  // Mantener también monochrome-mode para compatibilidad total
  document.body.classList.toggle('monochrome-mode', isDark);
  localStorage.setItem(DARK_MODE_KEY, isDark ? '1' : '0');
  localStorage.setItem('digitalspos_monochrome_mode', isDark ? '1' : '0');

  const btn = document.getElementById('monochromeToggleBtn');
  if (btn) {
    btn.classList.toggle('active-dark', isDark);
    btn.innerHTML = isDark ? '☀️' : '🌙';
    btn.title = isDark ? 'Cambiar a Modo Claro' : 'Cambiar a Modo Oscuro';
  }
}

// Alias para compatibilidad con código existente
const toggleMonochromeMode = toggleDarkMode;

// ── Toggle de barra lateral (3 barras / Hamburguesa) ─────────────
function toggleSidebar() {
  const wrapper = document.querySelector('.app-wrapper');
  const sidebar = document.getElementById('sidebar');

  if (window.innerWidth <= 768) {
    if (sidebar) sidebar.classList.toggle('open');
  } else {
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

// ── Acordeón del menú lateral (Función de Módulos Desplegables) ────
function toggleNavGroup(headerEl) {
  const group = headerEl.closest('.nav-group');
  if (!group) return;
  const wasOpen = group.classList.contains('open');

  // En modo acordeón inteligente: cerramos los otros grupos para mantener el menú despejado
  document.querySelectorAll('.nav-group.open').forEach(g => {
    if (g !== group) {
      g.classList.remove('open');
    }
  });

  // Alternar el grupo clickeado
  const willOpen = !wasOpen;
  group.classList.toggle('open', willOpen);

  const name = group.dataset.group;
  if (willOpen) {
    localStorage.setItem(ACTIVE_GROUP_KEY, name);
  } else {
    localStorage.removeItem(ACTIVE_GROUP_KEY);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const wrapper = document.querySelector('.app-wrapper');

  // Restaurar estado colapsado en desktop si el usuario lo guardó
  if (window.innerWidth > 768 && localStorage.getItem(SIDEBAR_COLLAPSED_KEY) === '1') {
    if (wrapper) wrapper.classList.add('sidebar-collapsed');
  }

  // Restaurar Modo Oscuro si estaba activo
  const isDarkActive = localStorage.getItem(DARK_MODE_KEY) === '1' || localStorage.getItem('digitalspos_monochrome_mode') === '1';
  if (isDarkActive) {
    document.body.classList.add('dark-mode', 'monochrome-mode');
    const darkBtn = document.getElementById('monochromeToggleBtn');
    if (darkBtn) {
      darkBtn.classList.add('active-dark');
      darkBtn.innerHTML = '☀️';
      darkBtn.title = 'Cambiar a Modo Claro';
    }
  }

  // Acordeón: asegurar que el grupo activo de la página actual esté abierto
  const activeSubitem = document.querySelector('.nav-subitem.active');
  if (activeSubitem) {
    const parentGroup = activeSubitem.closest('.nav-group');
    if (parentGroup) {
      document.querySelectorAll('.nav-group.open').forEach(g => {
        if (g !== parentGroup) g.classList.remove('open');
      });
      parentGroup.classList.add('open');
    }
  } else {
    const lastOpen = localStorage.getItem(ACTIVE_GROUP_KEY);
    if (lastOpen) {
      const g = document.querySelector(`.nav-group[data-group="${lastOpen}"]`);
      if (g) g.classList.add('open');
    }
  }

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
