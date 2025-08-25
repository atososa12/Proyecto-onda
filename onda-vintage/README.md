🚌 ONDA Vintage - Viajes Históricos (1980-1990)
Revive la experiencia de viajar en los ómnibus clásicos de Uruguay.

📌 Proyecto GitHUB
https://github.com/orgs/BUCEO/projects/96

📌 Descripción
Proyecto web interactivo que recrea los viajes en ómnibus de las décadas de 1980-1990, con:

Mapa de rutas vintage.
Catálogo de ómnibus históricos.
Información de agencias y trayectos.
Tecnologías: PHP (OOP), MySQL, HTML/CSS vintage, JavaScript.

🗂 Estructura de Archivos
/onda-vintage
│
├── index.php                # Página principal (mapa interactivo)
├── trayecto.php             # Detalle de trayectos
├── reserva.php              # Sistema de reservas (si aplica)
├── admin/                   # Panel de gestión (opcional)
│   ├── agencias.php
│   └── omnibus.php
├── assets/
│   ├── css/                 # Estilos vintage
│   ├── js/                  # Scripts interactivos
│   ├── img/                 # Imágenes (ómnibus, mapas)
│   └── fonts/               # Tipografías retro
├── classes/                 # Modelos PHP
│   ├── Omnibus.php
│   ├── Trayecto.php
│   └── Agencia.php
├── database/                # Scripts SQL
│   ├── create_db.sql        # Estructura inicial
│   └── datos_ejemplo.sql    # Datos de prueba
└── README.md                # Este archivo