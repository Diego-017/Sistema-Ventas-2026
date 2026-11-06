// DigitalsPos — Enterprise POS App JS

const SIDEBAR_COLLAPSED_KEY = 'digitalspos_sidebar_collapsed';
const MONOCHROME_KEY        = 'digitalspos_monochrome_mode';
const ACTIVE_GROUP_KEY      = 'digitalspos_active_nav_group';

// ── Toggle de Modo Blanco y Negro / Alto Contraste ────────────────
function toggleMonochromeMode() {
  const isMono = document.body.classList.toggle('monochrome-mode');
  localStorage.setItem(MONOCHROME_KEY, isMono ? '1' : '0');
  const btn = document.getElementById('monochromeToggleBtn');
  if (btn) {
    btn.classList.toggle('active-mono', isMono);
    btn.title = isMono ? 'Desactivar Modo Blanco y Negro' : 'Activar Modo Blanco y Negro';
  }
}

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

  // Restaurar Modo Blanco y Negro
  if (localStorage.getItem(MONOCHROME_KEY) === '1') {
    document.body.classList.add('monochrome-mode');
    const monoBtn = document.getElementById('monochromeToggleBtn');
    if (monoBtn) {
      monoBtn.classList.add('active-mono');
      monoBtn.title = 'Desactivar Modo Blanco y Negro';
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
    // Si estamos en inicio u otra página sin subitem activo, abrir el último seleccionado
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
