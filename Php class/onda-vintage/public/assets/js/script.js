// public/assets/js/script.js
document.addEventListener('DOMContentLoaded', () => {
  const mapEl = document.getElementById('map');
  if (!mapEl) {
    console.error('No existe #map en el DOM');
    return;
  }

  // ---------- MAPA ----------
  const map = L.map('map').setView([-34.9, -56.2], 6);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  // ---------- UI (un solo selector) ----------
  const bar = document.createElement('div');
  bar.style.cssText = 'margin:.5rem 0; display:flex; gap:.5rem; align-items:center; flex-wrap:wrap';

  const lblVista = document.createElement('label');
  lblVista.textContent = 'Vista:';
  lblVista.setAttribute('for', 'sel-vista');

  const selVista = document.createElement('select');
  selVista.id = 'sel-vista';
  selVista.style.minWidth = '260px';
  // opción Global por defecto
  selVista.appendChild(new Option('Global (todas las agencias)', ''));

  bar.append(lblVista, selVista);
  mapEl.parentNode.insertBefore(bar, mapEl);

  // ---------- CAPAS ----------
  const agencias = Array.isArray(window.AG) ? window.AG : [];

  // Global
  let globalMarkers = [];
  function clearGlobalMarkers() {
    globalMarkers.forEach(m => map.removeLayer(m));
    globalMarkers = [];
  }
  
function drawGlobalMarkers() {
  clearTrayectoLayers();
  clearGlobalMarkers();

  agencias.forEach(a => {
    if (!Number.isFinite(a?.lat) || !Number.isFinite(a?.lng)) return;

    const marker = L.marker([a.lat, a.lng]).addTo(map);

    // Si la agencia tiene UNA sola ruta, backend manda a.ruta.
    // Si tiene varias, manda a.rutas (lista separada por comas).
    const rutaTxt   = a.ruta ?? a.rutas ?? 'N/D';
    const esMultiple = !!(a.rutas && !a.ruta);

    const fotoHtml = a.foto
      ? `<div style="margin-top:.5rem"><img src="${a.foto}" alt="${a.nombre}" style="max-width:220px;border-radius:.25rem"></div>`
      : '';

    // Mostramos Km sólo cuando hay una única ruta (no tiene sentido con múltiples)
    const kmHtml = (!esMultiple && a.km != null)
      ? `Km: ${a.km}<br>`
      : (esMultiple ? '' : `Km: N/D<br>`);

    marker.bindPopup(`
      <b>${a.nombre ?? 'Sin nombre'}</b><br>
      Ruta: ${rutaTxt}<br>
      ${kmHtml}
      ${fotoHtml}
    `.trim());

    globalMarkers.push(marker);
  });

  if (globalMarkers.length) {
    const group = L.featureGroup(globalMarkers);
    map.fitBounds(group.getBounds().pad(0.15));
  }
}

  // Trayecto
  let trayectoLine = null;
  let trayectoMarkers = [];
  function clearTrayectoLayers() {
    if (trayectoLine) { map.removeLayer(trayectoLine); trayectoLine = null; }
    trayectoMarkers.forEach(m => map.removeLayer(m));
    trayectoMarkers = [];
  }
function drawTrayectoPayload(data) {
  clearTrayectoLayers();
  if (!data || !Array.isArray(data.line) || data.line.length < 2) {
    console.warn('Payload de trayecto inválido o sin suficientes puntos.');
    return;
  }

  // Dibujar línea
  trayectoLine = L.polyline(data.line, { weight: 4 }).addTo(map);
  map.fitBounds(trayectoLine.getBounds(), { padding: [20, 20] });

  // Nombre de la ruta/trayecto (p.ej. "RUTA 1")
  const rutaNombre = (data && data.trayecto && data.trayecto.nombre) ? data.trayecto.nombre : 'N/D';

  // Marcadores con popup: Nombre + Ruta + Km
  (data.agencias || []).forEach((a, i) => {
    if (typeof a?.lat !== 'number' || typeof a?.lng !== 'number') return;
    const marker = L.marker([a.lat, a.lng]).addTo(map);
    marker.bindPopup(
      `<strong>${i + 1}. ${a.nombre ?? 'Sin nombre'}</strong><br>` +
      `Ruta: ${rutaNombre}<br>` +
      (a.km !== null && a.km !== undefined ? `Km ${a.km}` : 'Km s/d')
    );
    trayectoMarkers.push(marker);
  });
}

  // ---------- FETCH HELPERS ----------
  function fetchJSON(url) {
    return fetch(url).then(r => {
      if (!r.ok) throw new Error(`HTTP ${r.status}`);
      return r.json();
    });
  }

  // ---------- CARGA DE DATOS ----------
  function loadTrayectos() {
    return fetchJSON('?r=api/trayectos').then(list => {
      // quitar opciones previas (dejar solo Global)
      for (let i = selVista.options.length - 1; i >= 1; i--) selVista.remove(i);

      if (Array.isArray(list) && list.length) {
        list.forEach(t => {
          selVista.appendChild(new Option(`${t.nombre} (id ${t.id})`, String(t.id)));
        });
      }
      return list || [];
    }).catch(err => {
      console.error('Error cargando trayectos:', err);
      return [];
    });
  }

  function loadTrayecto(id) {
    if (!id) return Promise.resolve();
    return fetchJSON(`?r=api/agencias&trayecto_id=${id}`) // ← antes era ?r=api/trayecto&id=
      .then(drawTrayectoPayload)
      .catch(err => {
        console.error('Error cargando trayecto:', err);
        alert('No se pudo cargar el trayecto.');
      });
  }

  // ---------- EVENTOS ----------
  selVista.addEventListener('change', () => {
    const val = selVista.value;
    if (!val) {
      // Global
      drawGlobalMarkers();
    } else {
      // Ver trayecto elegido
      clearGlobalMarkers();
      const id = +val;
      if (id) loadTrayecto(id);
    }
  });

  // ---------- ARRANQUE ----------
  drawGlobalMarkers();   // ver todas las agencias
  loadTrayectos();       // poblar el selector con trayectos
});

