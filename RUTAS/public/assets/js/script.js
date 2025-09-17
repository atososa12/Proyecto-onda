// public/assets/js/script.js
document.addEventListener('DOMContentLoaded', () => {
  const mapEl = document.getElementById('map');
  if (!mapEl) return console.error('No existe #map en el DOM');

  // ===== MAPA =====
  const map = L.map('map', { zoomControl: false }).setView([-34.9, -56.2], 6);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  L.control.zoom({ position: 'bottomright' }).addTo(map);
  map.attributionControl.setPosition('bottomright');

  // ===== NAVBAR: altura → CSS var =====
  function setNavHeightVar() {
    const nav = document.querySelector('.nvbar'); // tu navbar tiene clase .nvbar
    const h = nav ? Math.ceil(nav.getBoundingClientRect().height) : 60;
    document.documentElement.style.setProperty('--nav-h', `${h}px`);
  }
  setNavHeightVar();
  window.addEventListener('resize', setNavHeightVar);

  // ===== SIDEBAR (overlay con una sola clase: is-hidden) =====
  const sidebar   = document.getElementById('sidebar');
  const btnClose  = document.getElementById('sbClose');   // X del header
  const btnToggle = document.getElementById('sbToggle');  // "Ocultar ▸ / Mostrar ◂"
  const edgeBtn   = document.getElementById('sbEdge');    // flecha de borde
  const mqMobile  = window.matchMedia('(max-width: 900px)');

  // Mostrar/ocultar flecha según estado del panel
  function syncEdge() {
    if (!edgeBtn || !sidebar) return;
    edgeBtn.style.display = sidebar.classList.contains('is-hidden') ? 'block' : 'none';
  }

  function openSidebar() {
    if (!sidebar) return;
    sidebar.classList.remove('is-hidden');
    if (btnToggle) btnToggle.textContent = 'Ocultar ▸';
    syncEdge();
  }

  function closeSidebar() {
    if (!sidebar) return;
    sidebar.classList.add('is-hidden');
    if (btnToggle) btnToggle.textContent = 'Mostrar ◂';
    syncEdge();
  }

  // Recalcular tamaño del mapa tras la transición del panel
  if (sidebar) {
    sidebar.addEventListener('transitionend', (e) => {
      if (e.propertyName === 'transform') map.invalidateSize();
    });
  }

  // Eventos
  btnToggle?.addEventListener('click', () => {
    sidebar.classList.contains('is-hidden') ? openSidebar() : closeSidebar();
  });
  edgeBtn?.addEventListener('click', openSidebar);
  btnClose?.addEventListener('click', closeSidebar);
  mqMobile.addEventListener('change', syncEdge);

  // Estado inicial: abierto en ambas vistas
  openSidebar();
  setTimeout(() => map.invalidateSize(), 0);

  // ====== CAPAS / ESTADO ======
  const agenciasGlobal = Array.isArray(window.AG) ? window.AG : [];
  const markersLayer = L.layerGroup().addTo(map);

  // Trayecto
  let trayectoLine = null;
  let trayectoMarkers = [];
  function clearTrayectoLayers() {
    if (trayectoLine) { map.removeLayer(trayectoLine); trayectoLine = null; }
    trayectoMarkers.forEach(m => map.removeLayer(m));
    trayectoMarkers = [];
  }

  // Global
  let globalMarkers = [];
  function clearGlobalMarkers() {
    globalMarkers.forEach(m => map.removeLayer(m));
    globalMarkers = [];
  }

  // Fetch helper
  async function fetchJSON(url) {
    const r = await fetch(url);
    if (!r.ok) throw new Error(`HTTP ${r.status}`);
    return r.json();
  }

  // Dibujo: Global
  function renderGlobalMarkers(list = agenciasGlobal) {
    clearTrayectoLayers();
    clearGlobalMarkers();
    markersLayer.clearLayers();

    const bounds = [];
    (list || []).forEach(a => {
      if (!Number.isFinite(a?.lat) || !Number.isFinite(a?.lng)) return;

      const m = L.marker([a.lat, a.lng]).addTo(markersLayer);

      const rutaTxt    = a.ruta ?? a.rutas ?? 'N/D';
      const esMultiple = !!(a.rutas && !a.ruta);
      const fotoHtml   = a.foto
        ? `<div style="margin-top:.5rem"><img src="${a.foto}" alt="${a.nombre}" style="max-width:220px;border-radius:.25rem"></div>`
        : '';
      const kmHtml = (!esMultiple && a.km != null) ? `Km: ${a.km}<br>` : (esMultiple ? '' : `Km: N/D<br>`);

      m.bindPopup(`
        <b>${a.nombre ?? 'Sin nombre'}</b><br>
        Ruta: ${rutaTxt}<br>
        ${kmHtml}
        ${fotoHtml}
      `.trim());

      m.on('click', () => showAgencyInfo(a));
      globalMarkers.push(m);
      bounds.push([a.lat, a.lng]);
    });

    if (bounds.length) map.fitBounds(bounds, { padding: [50, 50] });
  }

  // Dibujo: Trayecto
  function drawTrayectoPayload(data) {
    clearTrayectoLayers();
    markersLayer.clearLayers();

    if (!data || !Array.isArray(data.line) || data.line.length < 2) {
      console.warn('Payload de trayecto inválido o sin suficientes puntos.');
      return;
    }

    trayectoLine = L.polyline(data.line, { weight: 4 }).addTo(map);
    map.fitBounds(trayectoLine.getBounds(), { padding: [20, 20] });

    const rutaNombre = (data?.trayecto?.nombre) ? data.trayecto.nombre : 'N/D';

    (data.agencias || []).forEach((a, i) => {
      if (typeof a?.lat !== 'number' || typeof a?.lng !== 'number') return;
      const marker = L.marker([a.lat, a.lng]).addTo(map);
      marker.bindPopup(
        `<strong>${i + 1}. ${a.nombre ?? 'Sin nombre'}</strong><br>` +
        `Ruta: ${rutaNombre}<br>` +
        (a.km !== null && a.km !== undefined ? `Km ${a.km}` : 'Km s/d')
      );
      marker.on('click', () => showAgencyInfo(a));
      trayectoMarkers.push(marker);
    });
  }

  // Panel de detalle
  const panelInfo = document.getElementById('panelInfo');
  function showAgencyInfo(ag) {
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

  // Chips de ruta
  const routeChipsEl = document.getElementById('routeChips');
  const ROUTES = [
    { key: 'global', label: 'Global', color: '#0b66ff' },
    { key: 'ruta_1', label: 'Ruta 1', color: '#1e90ff' },
    { key: 'ruta_3', label: 'Ruta 3', color: '#00a884' },
    { key: 'ruta_5', label: 'Ruta 5', color: '#ff8c00' },
    { key: 'ruta_8', label: 'Ruta 8', color: '#8a3ffc' },
    { key: 'ruta_9', label: 'Interbalnearia + 9', color: '#d93f0b' },
  ];
  const ROUTE_TO_ID = { global: 0, ruta_1: 1, ruta_3: 3, ruta_5: 2, ruta_8: 4, ruta_9: 5 };

  const makeChipIcon = (hex) => `
    <svg viewBox="0 0 24 24" aria-hidden="true" width="18" height="18">
      <circle cx="12" cy="12" r="9" fill="none" stroke="${hex}" stroke-width="2"/>
      <path d="M7 13l3 3 7-7" fill="none" stroke="${hex}" stroke-width="2" stroke-linecap="round"/>
    </svg>
  `;

  function renderRouteChips(activeKey = 'global') {
    if (!routeChipsEl) return;
    routeChipsEl.innerHTML = '';
    ROUTES.forEach(r => {
      const chip = document.createElement('button');
      chip.className = 'chip' + (r.key === activeKey ? ' is-active' : '');
      chip.type = 'button';
      chip.dataset.route = r.key;
      chip.innerHTML = `${makeChipIcon(r.color)} <span>${r.label}</span>`;
      chip.addEventListener('click', () => {
        document.querySelectorAll('.chip.is-active').forEach(el => el.classList.remove('is-active'));
        chip.classList.add('is-active');
        setRoute(r.key);
      });
      routeChipsEl.appendChild(chip);
    });
  }

  // Historias (placeholder)
  const storiesEl = document.getElementById('stories');
  async function loadStories() {
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

  // Data por ruta
  async function fetchAgenciasByRouteKey(routeKey) {
    const trayectoId = ROUTE_TO_ID[routeKey] ?? 0;
    if (trayectoId > 0) return fetchJSON(`?r=api/agencias&trayecto_id=${trayectoId}`);
    return { trayecto: null, agencias: agenciasGlobal, line: [] };
  }

  async function setRoute(routeKey) {
    try {
      const payload = await fetchAgenciasByRouteKey(routeKey);
      if (routeKey === 'global') renderGlobalMarkers(payload.agencias || []);
      else drawTrayectoPayload(payload);
    } catch (e) {
      console.error('No se pudo cargar la ruta:', e);
      alert('No se pudo cargar la ruta seleccionada.');
    }
  }

  // Arranque
  renderRouteChips('global');
  loadStories();
  renderGlobalMarkers();

  // ===== NAVBAR (menú responsive) =====
  (function () {
    function initNavToggle() {
      const btn  = document.getElementById('nvToggle');
      const menu = document.getElementById('nvMenu');
      if (!btn || !menu) return;

      btn.addEventListener('click', (e) => {
        e.preventDefault(); e.stopPropagation();
        const open = menu.classList.toggle('is-open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.body.classList.toggle('menu-open', open);
      });

      document.addEventListener('click', (e) => {
        if (!menu.classList.contains('is-open')) return;
        const inside = menu.contains(e.target);
        const onBtn  = btn.contains(e.target);
        if (!inside && !onBtn) {
          menu.classList.remove('is-open');
          btn.setAttribute('aria-expanded', 'false');
        }
      });
    }
    initNavToggle();
  }());
});
