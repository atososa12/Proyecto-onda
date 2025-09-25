// orquesta mapa + sidebar + template
import {
  createMap,
  invalidateOnSidebarTransition,
  renderGlobalMarkers,
  drawTrayectoPayload,
  setSharedHandlers
} from './map_render.js';

import {
  initUI,
  renderRouteChips,
  onChipSelect,
  showAgencyInfo,
  fetchAgenciasByRouteKey,
  fetchTrayectoFichaById
} from './ui_data.js';

document.addEventListener('DOMContentLoaded', async () => {
  const mapEl = document.getElementById('map');
  if (!mapEl) { console.error('No existe #map en el DOM'); return; }

  // 1) Mapa base
  const map = createMap(mapEl);

  // 2) Handlers compartidos (popups/markers)
  setSharedHandlers({ map, showAgencyInfo });

  // 3) Montaje/remo de chips
  const mountChips = () => {
    renderRouteChips('global', onChipSelect(async (routeKey) => {
      try {
        const payload = await fetchAgenciasByRouteKey(routeKey);

        if (routeKey === 'global') {
          renderGlobalMarkers(payload.agencias || []);
          return;
        }

        // Dibuja la ruta en el mapa (línea + pines)
        drawTrayectoPayload(payload);

        // Ficha para TODAS las rutas (no solo ruta_1)
        const ROUTE_TO_ID = { global: 0, ruta_1: 1, ruta_2: 6, ruta_3: 3, ruta_5: 2, ruta_8: 4, ruta_9: 5 };
        const trayectoId = ROUTE_TO_ID[routeKey] ?? 0;

        if (trayectoId > 0) {
          const ficha = await fetchTrayectoFichaById(trayectoId);
          // Render dentro del cuerpo del sidebar (manteniendo footer/ocultar)
          window.ondaUI.renderRouteFullSidebarFromTemplate(ficha);
        }
      } catch (e) {
        console.error('No se pudo cargar la ruta:', e);
        alert('No se pudo cargar la ruta seleccionada.');
      }
    }));
  };

  // 4) Inicializa UI (y expone helpers para usar desde app.js)
  window.ondaUI = initUI({
      map,
      onRestore: () => {
        mountChips(); // vuelve a dejar "Global" activo en los chips
        // Fuerza el mapa a la vista global:
        renderGlobalMarkers(Array.isArray(window.AG) ? window.AG : []);
      }
    });
  

  // 5) Render chips por primera vez
  mountChips();

  // 6) Ajustes tras transición del sidebar
  invalidateOnSidebarTransition(map);

  // 7) Vista inicial del mapa (global)
  renderGlobalMarkers(Array.isArray(window.AG) ? window.AG : []);
});
