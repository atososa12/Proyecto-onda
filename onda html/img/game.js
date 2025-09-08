const canvas = document.getElementById("gameCanvas");
const ctx = canvas.getContext("2d");

// Imágenes
const busImg = new Image(); busImg.src = "img/omnibus.jpg";
const autoImg = new Image(); autoImg.src = "img/auto.jpg";

// Jugador y obstáculos
const bus = { x:180, y:500, w:50, h:90, speed:5 };
let obstacles = [], score = 0, gameOver = false;
const keys = {};

// Controles
window.addEventListener("keydown", e => keys[e.key] = true);
window.addEventListener("keyup", e => keys[e.key] = false);

// Esperar a que carguen imágenes
let loaded = 0;
[busImg, autoImg].forEach(img => img.onload = () => { 
  if (++loaded === 2) gameLoop(); 
});

function gameLoop() {
  if(gameOver) return endGame();
  ctx.clearRect(0,0,canvas.width,canvas.height);

  // Mover bus
  if(keys.ArrowLeft && bus.x>0) bus.x -= bus.speed;
  if(keys.ArrowRight && bus.x+bus.w<canvas.width) bus.x += bus.speed;
  if(keys.ArrowUp && bus.y>0) bus.y -= bus.speed;
  if(keys.ArrowDown && bus.y+bus.h<canvas.height) bus.y += bus.speed;

  // Dibujar bus
  ctx.drawImage(busImg, bus.x, bus.y, bus.w, bus.h);

  // Obstáculos
  if(Math.random()<0.02) obstacles.push({ x: Math.random()*(canvas.width-50), y:-90, w:50, h:90 });
  const padding = 5;
  obstacles.forEach(o => {
    o.y += 3; // velocidad equilibrada
    ctx.drawImage(autoImg, o.x, o.y, o.w, o.h);
    // Colisión justa
    if(bus.x+padding < o.x+o.w-padding && bus.x+bus.w-padding > o.x+padding &&
       bus.y+padding < o.y+o.h-padding && bus.y+bus.h-padding > o.y+padding) {
      gameOver = true;
    }
  });

  // Quitar obstáculos fuera de pantalla y sumar puntos
  obstacles = obstacles.filter(o => { if(o.y>canvas.height){ score++; return false } return true });

  // Puntuación
  ctx.fillStyle="white"; ctx.font="20px Arial";
  ctx.fillText("Puntos: "+score,10,30);

  requestAnimationFrame(gameLoop);
}

function endGame(){
  ctx.fillStyle="rgba(0,0,0,0.7)"; ctx.fillRect(0,0,canvas.width,canvas.height);
  ctx.fillStyle="white"; ctx.font="40px Arial"; ctx.textAlign="center";
  ctx.fillText("GAME OVER", canvas.width/2, canvas.height/2-20);
  ctx.font="20px Arial";
  ctx.fillText("Puntaje final: "+score, canvas.width/2, canvas.height/2+20);
}



