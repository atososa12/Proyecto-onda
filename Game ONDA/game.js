const canvas = document.getElementById("gameCanvas");
const ctx = canvas.getContext("2d");

// Ajustar canvas al tamaño de la pantalla
function resizeCanvas() {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
}
window.addEventListener("resize", resizeCanvas);
resizeCanvas();

// Jugador (bus)
const bus = { x: canvas.width/2 - 25, y: canvas.height - 120, w:50, h:90, speed:10 };
const keys = {};

// Obstáculos y puntuación
let obstacles = [], score = 0, gameOver = false, gameStarted = false, victory = false, timer = null;

// Cargar sonidos
const crashSound = new Audio("sounds/crash.mp3");
const pointSound = new Audio("sounds/point.mp3");
const moveSound = new Audio("sounds/move.mp3");
crashSound.volume = 0.5;
pointSound.volume = 0.3;
moveSound.volume = 0.4;

// Controles de teclado
window.addEventListener("keydown", e => {
  keys[e.key] = true;

  // Sonido de movimiento
  if(["ArrowLeft","ArrowRight","ArrowUp","ArrowDown"].includes(e.key)){
    const s = moveSound.cloneNode();
    s.play();
  }

  // Iniciar juego con Enter si todas las imágenes están cargadas
  if(!gameStarted && e.key === "Enter" && imagesLoaded === images.length){
    startGame();
  }

  // Reiniciar juego si terminó
  if(gameOver && e.key === "Enter") resetGame();
});
window.addEventListener("keyup", e => keys[e.key] = false);

// Iniciar juego con click
canvas.addEventListener("click", () => {
  if(!gameStarted && imagesLoaded === images.length){
    startGame();
  } else if(gameOver){
    resetGame();
  }
});

// Cargar imágenes
const busImg = new Image(); busImg.src = "img/bus.png";
const autoRed = new Image(); autoRed.src = "img/auto_rojo.png";
const autoBlue = new Image(); autoBlue.src = "img/auto_azul.png";
const pistaImg = new Image(); pistaImg.src = "img/Pista.png";

let imagesLoaded = 0;
const images = [busImg, autoRed, autoBlue, pistaImg];

images.forEach(img => {
  img.onload = () => { imagesLoaded++; };
  img.onerror = () => console.error("No se pudo cargar la imagen: " + img.src);
});

// Variables pista
let pistaY = 0;
const pistaSpeed = 3;

// Funciones de juego
function getLanes() {
  const pistaWidth = canvas.width * 0.6;
  const pistaX = (canvas.width - pistaWidth)/2;
  const numLanes = 3;
  const laneWidth = pistaWidth/numLanes;
  return Array.from({length:numLanes}, (_, i) => pistaX + i*laneWidth + laneWidth/2 - 25);
}

function drawPista() {
  ctx.fillStyle = "#0a0"; 
  ctx.fillRect(0,0,canvas.width,canvas.height);

  const pistaWidth = canvas.width*0.6;
  const pistaX = (canvas.width-pistaWidth)/2;
  ctx.drawImage(pistaImg, pistaX, pistaY, pistaWidth, canvas.height);
  ctx.drawImage(pistaImg, pistaX, pistaY - canvas.height, pistaWidth, canvas.height);

  pistaY += pistaSpeed;
  if(pistaY >= canvas.height) pistaY = 0;
}

function drawBus() { ctx.drawImage(busImg, bus.x, bus.y, bus.w, bus.h); }

function drawObstacle(o) { ctx.drawImage(o.img, o.x, o.y, o.w, o.h); }

function moveBus() {
  if(keys.ArrowLeft && bus.x>0) bus.x -= bus.speed;
  if(keys.ArrowRight && bus.x+bus.w<canvas.width) bus.x += bus.speed;
  if(keys.ArrowUp && bus.y>0) bus.y -= bus.speed;
  if(keys.ArrowDown && bus.y+bus.h<canvas.height) bus.y += bus.speed;
}

function updateObstacles() {
  const lanes = getLanes();
  const colors = [autoRed, autoBlue];

  if(Math.random() < 0.02){
    const freeLanes = lanes.filter(l => !obstacles.some(o => Math.abs(o.x-l)<50 && o.y<150));
    if(freeLanes.length>0){
      const x = freeLanes[Math.floor(Math.random()*freeLanes.length)];
      const img = colors[Math.floor(Math.random()*colors.length)];
      obstacles.push({x, y:-90, w:50, h:90, img});
    }
  }

  const speed = 3 + Math.floor(score/10);
  const padding = 5;

  obstacles.forEach(o => {
    o.y += speed;
    drawObstacle(o);
    if(bus.x+padding < o.x+o.w-padding &&
       bus.x+bus.w-padding > o.x+padding &&
       bus.y+padding < o.y+o.h-padding &&
       bus.y+bus.h-padding > o.y+padding){
      if(!gameOver) crashSound.play();
      gameOver = true;
      victory = false;
      clearTimeout(timer);
    }
  });

  obstacles = obstacles.filter(o=>{
    if(o.y>canvas.height){
      score++;
      pointSound.play();
      return false;
    }
    return true;
  });
}

// Iniciar juego
function startGame() {
  gameStarted = true;
  resetGame();
  startTimer();
}

// Reiniciar juego
function resetGame(){
  bus.x = canvas.width/2 - bus.w/2;
  bus.y = canvas.height - bus.h - 20;
  obstacles = [];
  score = 0;
  gameOver = false;
  victory = false;
  pistaY = 0;
  if(timer) clearTimeout(timer);
  startTimer();
  gameLoop();
}

// Temporizador de 1:30 por partida
function startTimer() {
  if(timer) clearTimeout(timer);
  timer = setTimeout(() => {
    gameOver = true;
    victory = true;
  }, 90000); // 1 min 30 seg
}

// Bucle principal
function gameLoop(){
  if(gameOver) return endGame();
  ctx.clearRect(0,0,canvas.width,canvas.height);
  drawPista();
  moveBus();
  drawBus();
  updateObstacles();

  ctx.fillStyle="white";
  ctx.font = Math.floor(canvas.height*0.03) + "px Arial";
  ctx.textAlign = "left";
  ctx.textBaseline = "top";
  ctx.fillText("Puntos: "+score, canvas.width*0.02, canvas.height*0.02);

  requestAnimationFrame(gameLoop);
}

// Game Over / Victoria
function endGame(){
  ctx.fillStyle="rgba(0,0,0,0.7)";
  ctx.fillRect(0,0,canvas.width,canvas.height);
  ctx.fillStyle="white";
  ctx.textAlign="center";

  ctx.font = Math.floor(canvas.height*0.08) + "px Arial";
  ctx.fillText(victory ? "¡FELICITACIONES!" : "GAME OVER", canvas.width/2, canvas.height*0.4);

  ctx.font = Math.floor(canvas.height*0.04) + "px Arial";
  ctx.fillText(victory ? "Has sobrevivido 1:30 evitando los obstáculos" : "Puntaje final: "+score, canvas.width/2, canvas.height*0.5);
  ctx.fillText("Presiona ENTER o haz click para reiniciar", canvas.width/2, canvas.height*0.6);
}

// Pantalla de homenaje antes de iniciar
function showLoadingScreen() {
  ctx.clearRect(0,0,canvas.width,canvas.height);
  ctx.fillStyle = "white";
  ctx.textAlign = "center";

  if(imagesLoaded < images.length){
    ctx.font = Math.floor(canvas.height*0.04) + "px Arial";
    ctx.fillText("Cargando imágenes...", canvas.width/2, canvas.height/2);
  } else {
    const lines = [
      "Para rendir homenaje a los pilotos de ONDA,",
      "intenta evitar todos los obstáculos en este juego.",
      "Presiona ENTER o haz click para iniciar"
    ];
    ctx.font = Math.floor(canvas.height*0.035) + "px Arial";
    const lineHeight = canvas.height * 0.05;
    lines.forEach((line, i) => {
      ctx.fillText(line, canvas.width/2, canvas.height/2 - lineHeight + i*lineHeight);
    });
  }

  if(!gameStarted){
    requestAnimationFrame(showLoadingScreen);
  }
}

// Iniciar pantalla de carga
showLoadingScreen();
