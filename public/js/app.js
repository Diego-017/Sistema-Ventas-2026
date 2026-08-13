// DigitalsPos — app.js
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
}

// ── Acordeón del menú lateral ──────────────────────────────
const NAV_STATE_KEY = 'digitalspos_nav_open_groups';

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
  // Reabrir grupos que el usuario dejó abiertos en su última visita
  // (los grupos con la ruta activa ya vienen abiertos desde el servidor)
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

  // Cerrar sidebar en mobile al hacer click afuera
  document.addEventListener('click', e => {
    const sidebar = document.getElementById('sidebar');
    const toggle  = document.getElementById('sidebarToggle');
    if (sidebar && sidebar.classList.contains('open') &&
        !sidebar.contains(e.target) && e.target !== toggle) {
      sidebar.classList.remove('open');
    }
  });
});
