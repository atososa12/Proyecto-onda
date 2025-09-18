// ====== Selectores UI ======
const canvas = document.getElementById("gameCanvas");
const ctx = canvas.getContext("2d");

const scoreText = document.getElementById("scoreText");
const timeText = document.getElementById("timeText");
const startBtn = document.getElementById("startBtn");
const pauseBtn = document.getElementById("pauseBtn");
const restartBtn = document.getElementById("restartBtn");
const muteBtn = document.getElementById("muteBtn");

const menu = document.getElementById("menu");
const menuStart = document.getElementById("menuStart");
const menuHow = document.getElementById("menuHow");
const menuCredits = document.getElementById("menuCredits");

const creditsPanel = document.getElementById("creditsPanel");
const closeCredits = document.getElementById("closeCredits");

const mobilePad = {
  left: document.getElementById("leftBtn"),
  right: document.getElementById("rightBtn"),
  up: document.getElementById("upBtn"),
  down: document.getElementById("downBtn"),
};

const toast = document.getElementById("toast");

// ====== Configuración Canvas ======
function resizeCanvas() {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
}
window.addEventListener("resize", () => {
  resizeCanvas();
  bus.x = clamp(bus.x, getTrackBounds().minX, getTrackBounds().maxX);
  bus.y = clamp(bus.y, 0, canvas.height - bus.h);
});
resizeCanvas();

// ====== Utilidades ======
function clamp(v, a, b) { return Math.max(a, Math.min(b, v)); }
function lerp(a, b, t) { return a + (b - a) * t; }
function showToast(msg, ms = 1800) {
  toast.textContent = msg;
  toast.classList.add("show");
  clearTimeout(showToast._t);
  showToast._t = setTimeout(() => toast.classList.remove("show"), ms);
}
function formatTime(ms) {
  const s = Math.max(0, Math.ceil(ms / 1000));
  const m = Math.floor(s / 60);
  const r = s % 60;
  return `${m}:${r.toString().padStart(2, "0")}`;
}

// ====== Estado del juego ======
const keys = {};
const bus = { x: canvas.width / 2 - 25, y: canvas.height - 120, w: 50, h: 90, speed: 10 };
let obstacles = [];
let score = 0;
let gameOver = false;
let gameStarted = false;
let paused = false;
let victory = false;

const GAME_DURATION_MS = 90_000;
let startedAt = 0;
let remainingMs = GAME_DURATION_MS;

// ====== Recursos ======
const assets = {
  images: {
    bus: new Image(),
    autoRed: new Image(),
    autoBlue: new Image(),
    pista: new Image(),
  },
  sounds: {
    crash: new Audio("sounds/choque.mp3"),
    point: new Audio("sounds/punto.mp3"),
    move: new Audio("sounds/mover.mp3"),
    bg: new Audio("sounds/sonidofondo.mp3"),
    win: new Audio("sounds/ganar.mp3"),
    lose: new Audio("sounds/perder.mp3"),
  },
  loaded: false,
  muted: false,
};

assets.images.bus.src = "img/bus.png";
assets.images.autoRed.src = "img/auto_rojo.png";
assets.images.autoBlue.src = "img/auto_azul.png";
assets.images.pista.src = "img/Pista.png";

assets.sounds.crash.volume = 0.5;
assets.sounds.point.volume = 0.3;
assets.sounds.move.volume = 0.35;
assets.sounds.bg.volume = 0.2;
assets.sounds.bg.loop = true;
assets.sounds.win.volume = 0.5;
assets.sounds.lose.volume = 0.5;

Promise.all(
  Object.values(assets.images).map(
    (img) =>
      new Promise((res) => {
        img.onload = res;
        img.onerror = res;
      })
  )
).then(() => {
  assets.loaded = true;
});

// ====== Pista ======
let pistaY = 0;
let pistaSpeed = 3;

function getTrackBounds() {
  const pistaWidth = canvas.width * 0.6;
  const pistaX = (canvas.width - pistaWidth) / 2;
  return { pistaX, pistaWidth, minX: pistaX, maxX: pistaX + pistaWidth - bus.w };
}

// ====== Entrada ======
window.addEventListener("keydown", (e) => {
  if (["ArrowLeft", "ArrowRight", "ArrowUp", "ArrowDown", " "].includes(e.key)) e.preventDefault();
  keys[e.key] = true;

  if (["ArrowLeft", "ArrowRight", "ArrowUp", "ArrowDown"].includes(e.key) && !assets.muted) {
    const s = assets.sounds.move.cloneNode();
    s.volume = assets.sounds.move.volume;
    s.play().catch(() => {});
  }

  if (!gameStarted && e.key === "Enter") startGame();
  if (gameOver && e.key === "Enter") resetGame();
  if (e.key.toLowerCase() === "p") togglePause();
});
window.addEventListener("keyup", (e) => (keys[e.key] = false));

function bindPad(btn, keyName) {
  const on = () => (keys[keyName] = true);
  const off = () => (keys[keyName] = false);
  btn.addEventListener("pointerdown", (e) => {
    e.preventDefault();
    btn.setPointerCapture(e.pointerId);
    on();
  });
  btn.addEventListener("pointerup", () => off());
  btn.addEventListener("pointercancel", () => off());
  btn.addEventListener("pointerleave", () => off());
}
bindPad(mobilePad.left, "ArrowLeft");
bindPad(mobilePad.right, "ArrowRight");
bindPad(mobilePad.up, "ArrowUp");
bindPad(mobilePad.down, "ArrowDown");

canvas.addEventListener("click", () => {
  if (!gameStarted) startGame();
  else if (gameOver) resetGame();
});

// ====== Botones HUD ======
startBtn.addEventListener("click", () => startGame());
pauseBtn.addEventListener("click", () => togglePause());
restartBtn.addEventListener("click", () => resetGame());
muteBtn.addEventListener("click", () => {
  assets.muted = !assets.muted;
  updateMuteUI();
});
menuStart.addEventListener("click", () => startGame());
menuHow.addEventListener("click", () => {
  const instrucciones = [
    "1️⃣ Usa las flechas del teclado o los botones táctiles para mover el bus.",
    "2️⃣ Evita los autos que aparecen en los carriles.",
    "3️⃣ Sobrevive durante 1 minuto y 30 segundos sin chocar.",
    "4️⃣ Puedes pausar el juego con el botón 'Pausa'.",
    "5️⃣ Presiona Enter o haz clic en el botón 'jugar' para comenzar o reiniciar con el botón 'reiniciar'.",
  ].join("\n");
  showToast(instrucciones, 5000);
});
menuCredits.addEventListener("click", () => {
  menu.style.display = "none";
  creditsPanel.style.display = "flex";
});
closeCredits.addEventListener("click", () => {
  creditsPanel.style.display = "none";
  menu.style.display = "flex";
});

// ====== Mute UI ======
function updateMuteUI() {
  muteBtn.textContent = assets.muted ? "🔇" : "🔊";
  muteBtn.setAttribute("aria-pressed", assets.muted ? "true" : "false");
  Object.values(assets.sounds).forEach((a) => (a.muted = assets.muted));
  if (assets.muted) assets.sounds.bg.pause();
  else if (gameStarted && !paused && !gameOver) {
    assets.sounds.bg.play().catch(() => {});
  }
}

// ====== Juego ======
function drawPista() {
  const sky = ctx.createLinearGradient(0, 0, 0, canvas.height);
  sky.addColorStop(0, "#66ccff");
  sky.addColorStop(1, "#3aa0d6");
  ctx.fillStyle = sky;
  ctx.fillRect(0, 0, canvas.width, canvas.height);

  const { pistaX, pistaWidth } = getTrackBounds();
  ctx.drawImage(assets.images.pista, pistaX, pistaY, pistaWidth, canvas.height);
  ctx.drawImage(assets.images.pista, pistaX, pistaY - canvas.height, pistaWidth, canvas.height);

  pistaY += pistaSpeed;
  if (pistaY >= canvas.height) pistaY = 0;
}

function drawBus() {
  ctx.drawImage(assets.images.bus, bus.x, bus.y, bus.w, bus.h);
}

function drawObstacle(o) {
  const img = o.img;
  if (img && img.complete && img.naturalWidth > 0) {
    ctx.drawImage(img, o.x, o.y, o.w, o.h);
  } else {
    ctx.fillStyle = o.color || "#ff6b6b";
    ctx.fillRect(o.x, o.y, o.w, o.h);
  }
}

function moveBus() {
  const { minX, maxX } = getTrackBounds();
  const s = bus.speed;

  if (keys.ArrowLeft) bus.x -= s;
  if (keys.ArrowRight) bus.x += s;
  if (keys.ArrowUp) bus.y -= s;
  if (keys.ArrowDown) bus.y += s;

  bus.x = clamp(bus.x, minX, maxX);
  bus.y = clamp(bus.y, 0, canvas.height - bus.h);
}

// Obstáculos con distribución aleatoria
function spawnObstacle() {
  const { minX, maxX } = getTrackBounds();
  const imgs = [assets.images.autoRed, assets.images.autoBlue];
  const minDistance = 60;
  const candidateX = Math.random() * (maxX - minX) + minX;

  const tooClose = obstacles.some(
    (o) => Math.abs(o.x - candidateX) < minDistance && o.y < 150
  );
  if (tooClose) return;

  const img = imgs[Math.floor(Math.random() * imgs.length)];
  obstacles.push({
    x: candidateX,
    y: -90,
    w: 50,
    h: 90,
    img,
    color: "#ff6b6b",
  });
}

function updateObstacles() {
  const spawnProb = canvas.width < 500 ? 0.02 : canvas.width < 800 ? 0.035 : 0.05;
  if (Math.random() < spawnProb) spawnObstacle();

  const speed = 3 + Math.floor(score / 10);
  const padding = 5;

  for (const o of obstacles) {
    o.y += speed;
    drawObstacle(o);
    if (
      bus.x + padding < o.x + o.w - padding &&
      bus.x + bus.w - padding > o.x + padding &&
      bus.y + padding < o.y + o.h - padding &&
      bus.y + bus.h - padding > o.y + padding
    ) {
      if (!gameOver && !assets.muted) assets.sounds.crash.play().catch(() => {});
      endRun(false);
      return;
    }
  }

  const before = obstacles.length;
  obstacles = obstacles.filter((o) => o.y <= canvas.height);
  const cleared = before - obstacles.length;
  if (cleared > 0) {
    score += cleared;
    if (!assets.muted) assets.sounds.point.play().catch(() => {});
  }
}

function updateHUD() {
  scoreText.textContent = `${score}`;
  timeText.textContent = formatTime(remainingMs);
}

function startTimer() {
  startedAt = performance.now();
}

function updateTimer(now) {
  const elapsed = now - startedAt;
  remainingMs = clamp(GAME_DURATION_MS - elapsed, 0, GAME_DURATION_MS);
  if (remainingMs <= 0 && !gameOver) {
    endRun(true);
  }
}

// Ciclo principal
let rafId = 0;
function loop(now) {
  if (!gameStarted || paused) {
    rafId = requestAnimationFrame(loop);
    return;
  }

  ctx.clearRect(0, 0, canvas.width, canvas.height);
  drawPista();
  moveBus();
  drawBus();
  updateObstacles();
  updateTimer(now);
  updateHUD();

  if (!gameOver) {
    rafId = requestAnimationFrame(loop);
  } else {
    drawEndOverlay();
  }
}

// 🚦 Flujo de juego
function startGame() {
  if (!assets.loaded) showToast("Cargando recursos…");
  gameStarted = true;
  paused = false;
  menu.style.display = "none";
  creditsPanel.style.display = "none";
  resetRunCore();
  if (!assets.muted) assets.sounds.bg.play().catch(() => {});
  cancelAnimationFrame(rafId);
  rafId = requestAnimationFrame(loop);
  showToast("¡Buena suerte!");
}

function resetGame() {
  gameStarted = true;
  paused = false;
  menu.style.display = "none";
  creditsPanel.style.display = "none";
  resetRunCore();
  if (!assets.muted) {
    assets.sounds.bg.currentTime = 0;
    assets.sounds.bg.play().catch(() => {});
  }
  cancelAnimationFrame(rafId);
  rafId = requestAnimationFrame(loop);
  showToast("Partida reiniciada");
}

function resetRunCore() {
  score = 0;
  gameOver = false;
  victory = false;
  obstacles = [];
  pistaY = 0;
  pistaSpeed = 3;
  remainingMs = GAME_DURATION_MS;
  bus.x = canvas.width / 2 - bus.w / 2;
  bus.y = canvas.height - bus.h - 20;

  startTimer();
  updateHUD();
}

function togglePause() {
  if (!gameStarted || gameOver) return;
  paused = !paused;
  pauseBtn.textContent = paused ? "Reanudar" : "Pausa";
  if (paused) {
    if (!assets.muted) assets.sounds.bg.pause();
    remainingMs = Math.max(0, remainingMs);
  } else {
    startedAt = performance.now() - (GAME_DURATION_MS - remainingMs);
    if (!assets.muted) assets.sounds.bg.play().catch(() => {});
  }
  showToast(paused ? "Pausa" : "Reanudado");
}

function endRun(isVictory) {
  gameOver = true;
  victory = !!isVictory;
  if (!assets.muted) {
    (victory ? assets.sounds.win : assets.sounds.lose).play().catch(() => {});
    assets.sounds.bg.pause();
  }
}

function drawEndOverlay() {
  ctx.fillStyle = "rgba(0,0,0,0.7)";
  ctx.fillRect(0, 0, canvas.width, canvas.height);

  ctx.fillStyle = "white";
  ctx.textAlign = "center";
  ctx.textBaseline = "middle";

  ctx.font = Math.floor(canvas.height * 0.08) + "px Poppins, Arial";
  ctx.fillText(victory ? "¡FELICITACIONES!" : "GAME OVER", canvas.width / 2, canvas.height * 0.38);

  ctx.font = Math.floor(canvas.height * 0.04) + "px Poppins, Arial";
  const line2 = victory ? "Sobreviviste 1:30 evitando obstáculos" : `Puntaje final: ${score}`;
  ctx.fillText(line2, canvas.width / 2, canvas.height * 0.48);
  ctx.fillText("Enter o click para reiniciar", canvas.width / 2, canvas.height * 0.56);
}

// Pantalla inicial animada
function showIdleScreen() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  drawPista();
  bus.x = canvas.width / 2 - bus.w / 2 + Math.sin(performance.now() / 600) * 5;
  drawBus();
  if (!gameStarted) requestAnimationFrame(showIdleScreen);
}
showIdleScreen();

// Accesibilidad
window.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    if (creditsPanel.style.display === "flex") {
      creditsPanel.style.display = "none";
      menu.style.display = "flex";
    } else if (!gameOver && gameStarted) {
      togglePause();
    }
  }
});

// Estado inicial UI
updateHUD();
updateMuteUI();