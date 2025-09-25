let _sidebarSnapshotHTML = null; // snapshot del CONTENIDO (#sbContent)
let _onRestoreCb = null;
let _mapRef = null;

export function initUI({ map, onRestore }) {
  _mapRef = map;
  _onRestoreCb = typeof onRestore === 'function' ? onRestore : null;

  function setNavHeightVar() {
    const nav = document.querySelector('.nvbar');
    const h = nav ? Math.ceil(nav.getBoundingClientRect().height) : 60;
    document.documentElement.style.setProperty('--nav-h', `${h}px`);
  }
  setNavHeightVar();
  window.addEventListener('resize', setNavHeightVar);

  const app      = document.getElementById('app');       // 👈 referencia al contenedor
  const sidebar  = document.getElementById('sidebar');
  const sbContent = document.getElementById('sbContent');

  if (sbContent && _sidebarSnapshotHTML === null) {
    _sidebarSnapshotHTML = sbContent.innerHTML;
  }

  function openSidebar() {
    sidebar?.classList.remove('is-hidden');
    app?.classList.remove('sb-collapsed');               // 👈 quita bandera
    const btnToggle = document.getElementById('sbToggle');
    if (btnToggle) btnToggle.textContent = 'Ocultar ▸';
    queueMicrotask(() => _mapRef && _mapRef.invalidateSize());
  }

  function closeSidebar() {
    sidebar?.classList.add('is-hidden');
    app?.classList.add('sb-collapsed');                  // 👈 pone bandera
    const btnToggle = document.getElementById('sbToggle');
    if (btnToggle) btnToggle.textContent = 'Mostrar ◂';
    setTimeout(() => _mapRef && _mapRef.invalidateSize(), 300);
  }

  function wireStaticSidebarControls() {
    const btnClose = document.getElementById('sbClose');
    const btnToggle = document.getElementById('sbToggle');
    const edgeBtn  = document.getElementById('sbEdge');

    btnToggle?.addEventListener('click', () => {
      sidebar.classList.contains('is-hidden') ? openSidebar() : closeSidebar();
    });
    edgeBtn?.addEventListener('click', openSidebar);     // 👈 abre desde el borde
    btnClose?.addEventListener('click', closeSidebar);
  }

  wireStaticSidebarControls();
  openSidebar();

  populateStories();

  return { openSidebar, closeSidebar, renderRouteFullSidebarFromTemplate, restoreSidebar };
}


// Rellena el <template id="tplRouteSidebar"> con los datos y reemplaza SOLO #sbContent
export function renderRouteFullSidebarFromTemplate(ficha) {
  const sbContent = document.getElementById('sbContent');
  const tpl = document.getElementById('tplRouteSidebar');
  if (!sbContent || !tpl) return;

  const node = tpl.content.cloneNode(true);

  const title     = node.querySelector('.tpl-title');
  const heroWrap  = node.querySelector('.tpl-hero');
  const heroImg   = node.querySelector('.tpl-hero-img');
  const summaryEl = node.querySelector('.tpl-summary');
  const leadEl    = node.querySelector('.tpl-lead');
  const link      = node.querySelector('.tpl-link');

  const { nombre, slug, summary, hero_image_url, lead } = (ficha || {});

  // Título
  title.textContent = nombre || 'Ruta';

  // Hero
  if (hero_image_url) {
    heroImg.src = hero_image_url;
    heroImg.alt = nombre || 'Ruta';
    heroWrap.hidden = false;
  }

  // Summary
  if (summary) {
    summaryEl.textContent = summary;
    summaryEl.hidden = false;
  }

  // Primer párrafo
  const firstParagraph = (lead && lead.trim())
    ? lead.trim()
    : (summary ? summary.trim().split(/\n{2,}/)[0] : '');
  if (firstParagraph) {
    leadEl.textContent = firstParagraph;
    leadEl.hidden = false;
  }

  // Link público
  link.href = `?r=trayecto/ver&slug=${encodeURIComponent(slug || '')}`;

  // Reemplaza SOLO el cuerpo; el footer con "Ocultar/Mostrar" queda
  sbContent.innerHTML = '';
  sbContent.appendChild(node);

  // Back → restaurar snapshot
  sbContent.querySelector('.tpl-back')?.addEventListener('click', restoreSidebar);

  // Invalidate por si cambia alto
  queueMicrotask(() => _mapRef && _mapRef.invalidateSize());
}

export function restoreSidebar() {
  const sbContent = document.getElementById('sbContent');
  if (!sbContent || _sidebarSnapshotHTML === null) return;

  sbContent.innerHTML = _sidebarSnapshotHTML;

  // Rehidratar contenido dinámico
  populateStories();

  // Avisar a app.js para re-montar chips/handlers
  if (typeof _onRestoreCb === 'function') _onRestoreCb();

  queueMicrotask(() => _mapRef && _mapRef.invalidateSize());
}

// Chips de rutas
export function renderRouteChips(activeKey, onSelect) {
  const routeChipsEl = document.getElementById('routeChips');
  if (!routeChipsEl) return;

  const ROUTES = [
    { key: 'global', label: 'Global', color: '#0b66ff' },
    { key: 'ruta_1', label: 'Ruta 1', color: '#1e90ff' },
    { key: 'ruta_2', label: 'Ruta 2', color: '#22b8cf' },
    { key: 'ruta_3', label: 'Ruta 3', color: '#00a884' },
    { key: 'ruta_5', label: 'Ruta 5', color: '#ff8c00' },
    { key: 'ruta_8', label: 'Ruta 8', color: '#8a3ffc' },
    { key: 'ruta_9', label: 'Interbalnearia + 9', color: '#d93f0b' },
  ];

  const makeIcon = (hex) => `
    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
      <circle cx="12" cy="12" r="9" fill="none" stroke="${hex}" stroke-width="2"/>
      <path d="M7 13l3 3 7-7" fill="none" stroke="${hex}" stroke-width="2" stroke-linecap="round"/>
    </svg>`;

  routeChipsEl.innerHTML = '';
  ROUTES.forEach(r => {
    const chip = document.createElement('button');
    chip.className = 'chip' + (r.key === activeKey ? ' is-active' : '');
    chip.type = 'button';
    chip.dataset.route = r.key;
    chip.innerHTML = `${makeIcon(r.color)} <span>${r.label}</span>`;
    chip.addEventListener('click', async () => {
      document.querySelectorAll('.chip.is-active').forEach(el => el.classList.remove('is-active'));
      chip.classList.add('is-active');
      await onSelect(r.key);
    });
    routeChipsEl.appendChild(chip);
  });
}

export function onChipSelect(handler) {
  return async (routeKey) => handler(routeKey);
}

// Panel agencia
export function showAgencyInfo(ag) {
  const panelInfo = document.getElementById('panelInfo');
  if (!panelInfo) return;
  panelInfo.classList.remove('muted');
  panelInfo.innerHTML = `
    <div><strong>${ag.nombre ?? 'Sin nombre'}</strong></div>
    ${ag.foto ? `<img src="${ag.foto}" alt="${ag.nombre}" style="max-width:100%;border-radius:8px">` : ''}
    ${Number.isFinite(ag.lat) && Number.isFinite(ag.lng) ? `<div><small>Lat: ${ag.lat.toFixed(5)}, Lng: ${ag.lng.toFixed(5)}</small></div>` : ''}
    ${ag.km != null ? `<div><small>Km en ruta: ${ag.km}</small></div>` : ''}
    ${ag.rol ? `<div><small>Rol: ${ag.rol}</small></div>` : ''}
    <a class="badge" href="?r=agencia/index&id=${ag.id}">Ver ficha</a>
  `;
}

// Data / fetch
const ROUTE_TO_ID = { global: 0, ruta_1: 1, ruta_2: 6, ruta_3: 3, ruta_5: 2, ruta_8: 4, ruta_9: 5 };

async function fetchJSON(url) {
  const r = await fetch(url);
  if (!r.ok) throw new Error(`HTTP ${r.status}`);
  return r.json();
}

export async function fetchAgenciasByRouteKey(routeKey) {
  const trayectoId = ROUTE_TO_ID[routeKey] ?? 0;
  if (trayectoId > 0) return fetchJSON(`?r=api/agencias&trayecto_id=${trayectoId}`);
  return { trayecto: null, agencias: Array.isArray(window.AG) ? window.AG : [], line: [] };
}

export async function fetchTrayectoFichaById(id) {
  return fetchJSON(`?r=api/trayecto_ficha&id=${id}`);
}

export function populateStories() {
  const storiesEl = document.getElementById('stories');
  if (!storiesEl) return;
  const data = [
    { id: 101, titulo: 'La ONDA Marina', tag: 'Maldonado' },
    { id: 102, titulo: 'Cruce del Río Negro', tag: 'Rutas 3 y 5' },
    { id: 103, titulo: 'Terminal de Montevideo', tag: 'Montevideo' },
  ];
  storiesEl.innerHTML = data.map(d => `
    <li><a href="?r=historia/ver&id=${d.id}">
      <span class="badge">${d.tag}</span>
      <strong>${d.titulo}</strong>
    </a></li>
  `).join('');
}
