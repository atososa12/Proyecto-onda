// map_render.js
let mapRef = null;
let showAgencyInfoRef = null;

let trayectoLine = null;
let trayectoAltLines = [];
let trayectoMarkers = [];
let globalMarkers = [];
const markersLayer = L.layerGroup();

export function createMap(el) {
  const map = L.map(el, { zoomControl: false }).setView([-34.9, -56.2], 6);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(map);

  L.control.zoom({ position: 'bottomright' }).addTo(map);
  map.attributionControl.setPosition('bottomright');
  return map;
}

export function setSharedHandlers({ map, showAgencyInfo }) {
  mapRef = map;
  showAgencyInfoRef = showAgencyInfo;
  markersLayer.addTo(mapRef);
}

export function invalidateOnSidebarTransition(map) {
  const sidebar = document.getElementById('sidebar');
  if (!sidebar) return;
  sidebar.addEventListener('transitionend', (e) => {
    if (e.propertyName === 'transform') map.invalidateSize();
  });
}

// --- helpers internos ---
function clearTrayectoLayers() {
  if (trayectoLine) {
    mapRef.removeLayer(trayectoLine);
    trayectoLine = null;
  }
  // borrar TODAS las líneas alternativas
  trayectoAltLines.forEach(l => mapRef.removeLayer(l));
  trayectoAltLines = [];

  // borrar marcadores del trayecto
  trayectoMarkers.forEach(m => mapRef.removeLayer(m));
  trayectoMarkers = [];
}

function clearGlobalMarkers() {
  globalMarkers.forEach(m => mapRef.removeLayer(m));
  globalMarkers = [];
}

// --- Render GLOBAL (marcadores sueltos) ---
export function renderGlobalMarkers(list = []) {
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

    m.on('click', () => showAgencyInfoRef?.(a));
    globalMarkers.push(m);
    bounds.push([a.lat, a.lng]);
  });

  if (bounds.length) mapRef.fitBounds(bounds, { padding: [50, 50] });
}

// --- Render TRAYECTO (línea + marcadores ordenados) ---
export function drawTrayectoPayload(data) {
  clearTrayectoLayers();
  markersLayer.clearLayers();

  if (!data || !Array.isArray(data.line) || data.line.length < 2) {
    console.warn('Payload de trayecto inválido o sin suficientes puntos.');
    return;
  }

  // Línea principal
  trayectoLine = L.polyline(data.line, { weight: 4 }).addTo(mapRef);

  // Ramales (mismo estilo sólido)
  const altLines = Array.isArray(data.linesAlt) ? data.linesAlt : [];
  const toCoords = alt => Array.isArray(alt?.coords) ? alt.coords : (Array.isArray(alt) ? alt : null);

  const allBounds = [...trayectoLine.getLatLngs().map(ll => [ll.lat, ll.lng])];
  altLines.forEach(alt => {
    const coords = toCoords(alt);
    if (!Array.isArray(coords) || coords.length < 2) return;
    const altLine = L.polyline(coords, { weight: 4 }).addTo(mapRef);
    trayectoAltLines.push(altLine); // guardamos para poder limpiar al cambiar de ruta
    altLine.getLatLngs().forEach(ll => allBounds.push([ll.lat, ll.lng]));
  });

  if (allBounds.length) mapRef.fitBounds(allBounds, { padding: [20, 20] });

  // Marcadores (principal + alternativas si las mandás en agenciasAlt)
  const rutaNombre = data?.trayecto?.nombre ?? 'N/D';
  const listMain = Array.isArray(data.agencias) ? data.agencias : [];
  const listAlt  = Array.isArray(data.agenciasAlt) ? data.agenciasAlt : [];
  const allAgs   = [...listMain, ...listAlt];

  const seen = new Set();
  allAgs.forEach((a, i) => {
    const id = a?.id ?? `idx:${i}`;

    // ⛔ no mostrar pins para nodos de forma
    if (a?.nombre && a.nombre.startsWith('[shape]')) return;

    if (seen.has(id)) return;
    seen.add(id);

    const lat = typeof a?.lat === 'number' ? a.lat : parseFloat(a?.lat);
    const lng = typeof a?.lng === 'number' ? a.lng : parseFloat(a?.lng);
    if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

    const marker = L.marker([lat, lng]).addTo(mapRef);
    marker.bindPopup(
      `<strong>${a.nombre ?? 'Sin nombre'}</strong><br>` +
      `Ruta: ${rutaNombre}<br>` +
      (a.km != null ? `Km ${a.km}` : 'Km s/d')
    );
    marker.on('click', () => showAgencyInfoRef?.(a));
    trayectoMarkers.push(marker);
  });
}





