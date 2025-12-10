📌 Proyecto FinanzasApp – API en Laravel

Este repositorio contiene la API desarrollada en Laravel, utilizada por la aplicación móvil FinanzasApp.
La API gestiona usuarios, transacciones, categorías, dashboards, proyecciones y consejos financieros basados en reglas.

🚀 Características principales

Autenticación con tokens (Laravel Sanctum).

CRUD de transacciones (ingresos y egresos).

CRUD de categorías.

Dashboard con:

Totales por mes

Gastos por categoría

Proyección de saldo futuro

Consejos de ahorro basados en IA de reglas.

Arquitectura limpia con Services.

Soporte para Docker (MySQL incluido).

📦 Requisitos

Asegúrate de tener instalado:

PHP 8.1+

Composer 2+

MySQL / MariaDB

Laravel 10+

⚙️ Configuración del entorno

Instala dependecias

composer install

Archivo de entorno

cp .env.example .env

Genera la clave de aplicacion

php artisan key:generate

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=finanzasdb
DB_USERNAME=root
DB_PASSWORD=

🗄 Migraciones

php artisan migrate

▶️ Iniciar el servidor de desarrollo

php artisan serve
