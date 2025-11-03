<template>
  <div ref="containerRef" class="map-container" :style="{ height }">
    <div ref="mapRef" class="map"></div>

    <div
      v-if="selectedStall"
      class="stall-popup"
      :style="{ top: popupPosition.y + 'px', left: popupPosition.x + 'px' }"
    >
      <h3>Stall Information</h3>
      <p v-for="(value, key) in selectedStall" :key="key">
        <strong>{{ key }}:</strong> {{ value }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css"; // keep, but advise consumer to import globally too

// marker icon fix (prevent 404 after publish)
import markerIcon2x from "leaflet/dist/images/marker-icon-2x.png";
import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2x,
  iconUrl: markerIcon,
  shadowUrl: markerShadow,
});

const props = defineProps({
  stallsData: { type: Object, required: true },
  scopeData: { type: Object, default: null },
  center: { type: Array, default: () => [13.965601, 121.527501] },
  zoom: { type: Number, default: 20 },
  height: { type: String, default: "600px" },
  disabled: { type: Boolean, default: false },
});
const emit = defineEmits(["stall-click"]);

const containerRef = ref(null);
const mapRef = ref(null);
const selectedStall = ref(null);
const popupPosition = ref({ x: 0, y: 0 });

let map = null;
let tileLayer = null;
let resizeObserver = null;
let scheduledTimeouts = [];

function clearScheduled() {
  scheduledTimeouts.forEach(t => clearTimeout(t));
  scheduledTimeouts = [];
}

function waitForVisible(el, timeout = 4000) {
  return new Promise((resolve, reject) => {
    const start = performance.now();
    function check() {
      if (!el) return reject(new Error("no element"));
      const rect = el.getBoundingClientRect();
      if (rect.width > 0 && rect.height > 0) return resolve();
      if (performance.now() - start > timeout) return reject(new Error("timeout waiting for container visible"));
      requestAnimationFrame(check);
    }
    check();
  });
}

function robustInvalidateSize() {
  if (!map) return;
  // multiple attempts
  map.invalidateSize();
  scheduledTimeouts.push(setTimeout(()=>map.invalidateSize(), 100));
  scheduledTimeouts.push(setTimeout(()=>map.invalidateSize(), 300));
  scheduledTimeouts.push(setTimeout(()=>map.invalidateSize(), 700));
  // couple of RAF attempts
  let attempts = 0;
  const rafLoop = () => {
    if (!map) return;
    map.invalidateSize();
    attempts++;
    if (attempts < 6) requestAnimationFrame(rafLoop);
  };
  requestAnimationFrame(rafLoop);
}

function initMapOnceVisible() {
  if (!mapRef.value) return;
  // destroy any previous map
  if (map) {
    try { map.remove(); } catch (e) { console.warn("map.remove() error:", e); }
    map = null;
  }

  // init map using element reference (safer than id)
  map = L.map(mapRef.value, { zoomControl: true }).setView(props.center, props.zoom);

  tileLayer = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  // debug hooks: tile load / tile error
  tileLayer.on('load', () => console.info('[SariayaMap] tilelayer loaded'));
  tileLayer.on('tileerror', (err) => console.warn('[SariayaMap] tile error', err));

  if (props.scopeData) {
    L.geoJSON(props.scopeData).addTo(map);
  }

  L.geoJSON(props.stallsData, {
    pointToLayer: (feature, latlng) => L.marker(latlng),
    onEachFeature: (feature, layer) => {
      const propsData = feature.properties || {};
      if(!props?.disabled){
        layer.on("click", (e) => {
          selectedStall.value = propsData;
          const point = map.latLngToContainerPoint(e.latlng);
          popupPosition.value = { x: point.x + 10, y: point.y - 10 };
          emit("stall-click", propsData);
        });
      }
    }
  }).addTo(map);

  // ensure correct redrawing + tile loading
  robustInvalidateSize();

  // ResizeObserver to auto-invalidate on container changes
  const container = containerRef.value;
  if (container && window.ResizeObserver) {
    resizeObserver = new ResizeObserver(() => {
      if (map) {
        map.invalidateSize();
      }
    });
    resizeObserver.observe(container);
  }
}

onMounted(async () => {
  await nextTick();
  const container = containerRef.value;
  try {
    await waitForVisible(container, 5000);
  } catch (err) {
    // still try to init even if not visible — but warn
    console.warn("[SariayaMap] container not visible before timeout, proceeding anyway:", err);
  }

  try {
    initMapOnceVisible();
  } catch (err) {
    console.error("[SariayaMap] initMap error:", err);
  }
});

// cleanup
onBeforeUnmount(() => {
  clearScheduled();
  if (resizeObserver) {
    try { resizeObserver.disconnect(); } catch (e) {}
    resizeObserver = null;
  }
  if (map) {
    try { map.remove(); } catch (e) {}
    map = null;
  }
});
</script>

<style>
/* DO NOT scope this style block */
.map-container { position: relative; width: 100%; }
.map { width: 100%; height: 100%; min-height: 300px; }
/* popup etc */
.stall-popup { position: absolute; z-index: 1000; background: rgba(255,255,255,0.95); border: 1px solid #ccc; padding: 6px 10px; pointer-events:none; max-width:220px; box-shadow:0 2px 5px rgba(0,0,0,0.15); }
</style>
