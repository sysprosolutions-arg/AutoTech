# Instalación y puesta en marcha de AutoTech

Esta guía ofrece dos recorridos independientes. Para evaluar el sistema sin preparar un entorno de desarrollo, usar el instalador. Para revisar o modificar el código, seguir la puesta en marcha desde el repositorio.

## 1. Demo instalable para Windows

### Requisitos

- Windows 10 u 11 de 64 bits.
- Aproximadamente 350 MB libres para la instalación y los datos locales.

No se necesita PHP, Composer, Node.js, npm, MySQL, Visual Studio Code ni una copia del repositorio.

### Instalación

1. Descargar `AutoTech-0.4.6-setup.exe` desde la [Release AutoTech 0.4.6](https://github.com/sysprosolutions-arg/AutoTech/releases/tag/v0.4.6).
2. Verificar, si se desea, que el SHA-256 sea `2C8F690CFE145367BB8B16E99DD6D15CB07FAE6280A3CA530D1B63E17D13EA2F`.
3. Ejecutar el instalador.
4. Si Windows muestra una advertencia de reputación, seleccionar **Más información** y **Ejecutar de todas formas**. El MVP académico no posee una firma comercial.
5. Abrir AutoTech desde el menú Inicio.

La aplicación instala su propio runtime y utiliza una base SQLite por usuario en `%APPDATA%\AutoTech\database\database.sqlite`.

La versión 0.4.6 corrige la paginación de los listados de repuestos, turnos y reparaciones: utiliza controles Bootstrap compactos, textos en castellano y elimina los íconos SVG sobredimensionados. Mantiene las funciones del Sprint 3, la carga idempotente de datos demostrativos y la corrección del bloqueo en **Preparando el sistema del taller**. Si existe una versión anterior, cerrar AutoTech e instalar 0.4.6 sobre ella. La aplicación de escritorio selecciona un puerto local disponible, por lo que puede ejecutarse al mismo tiempo que la versión web.

En una instalación nueva o al actualizar desde una versión anterior se generan, de forma idempotente, datos demostrativos editables para visualizar el panel, los turnos, presupuestos, órdenes de trabajo, reparaciones y reportes. La actualización conserva los datos que ya existan.

## 2. Código fuente en Windows

### Herramientas necesarias

Instalar y agregar al `PATH` de Windows:

- [Git para Windows](https://git-scm.com/download/win).
- PHP 8.5 x64 con las extensiones habituales de Laravel y PHPUnit y, en particular, `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `fileinfo`, `dom` y `xmlwriter`.
- [Composer 2](https://getcomposer.org/download/).
- [Node.js](https://nodejs.org/) 22 o superior, que incluye npm.
- [Visual Studio Code](https://code.visualstudio.com/), recomendado pero no obligatorio.

Cerrar y volver a abrir Visual Studio Code después de modificar el `PATH`. Comprobar todo desde PowerShell:

```powershell
php -v
composer --version
node -v
npm -v
git --version
```

PHP debe informar la versión 8.5. PHP 8.1 de XAMPP no satisface las dependencias bloqueadas en `composer.lock`.

### Descargar y preparar el proyecto

```powershell
git clone https://github.com/sysprosolutions-arg/AutoTech.git
Set-Location AutoTech
composer install
Copy-Item .env.example .env
php artisan key:generate
New-Item -ItemType File -Path database\database.sqlite -Force
php artisan migrate --seed
npm install
```

`.env.example` utiliza SQLite para que la primera ejecución no dependa de MySQL. El archivo de base de datos local no se versiona.

### Ejecutar el sistema

Abrir dos terminales dentro de la carpeta del proyecto.

Terminal 1 — Laravel:

```powershell
php artisan serve
```

Terminal 2 — recursos visuales con Vite:

```powershell
npm run dev
```

Abrir `http://127.0.0.1:8000`. Mantener ambas terminales abiertas. Para detener los procesos, presionar `Ctrl+C` en cada terminal.

### Credencial de demostración

- Usuario: `admin`
- Contraseña inicial: `AutoTech#2026`

Esta credencial se genera mediante seeders y es exclusivamente local. Debe reemplazarse en cualquier ambiente real o compartido.

## 3. Alternativa con MySQL o MariaDB

SQLite es suficiente para una prueba local. Para compartir una base entre varios usuarios, instalar MySQL 8 o MariaDB 11, crear una base y un usuario exclusivos y reemplazar en `.env` la configuración SQLite por:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=autotech
DB_USERNAME=autotech
DB_PASSWORD=una-clave-local-segura
```

Después ejecutar:

```powershell
php artisan config:clear
php artisan migrate --seed
```

## 4. Verificación técnica

```powershell
php artisan test
vendor\bin\pint --test
npm run build
```

## 5. Problemas frecuentes

### `php` no se reconoce como comando

PHP no está instalado o su carpeta no está incluida en el `PATH`. Instalar PHP 8.5, agregar la carpeta que contiene `php.exe` al `PATH` y reiniciar Visual Studio Code.

### Composer informa una versión de PHP incompatible

Comprobar `php -v` y `Get-Command php`. Si aparece PHP 8.1 de XAMPP, instalar PHP 8.5 y colocar su carpeta antes que XAMPP en el `PATH`.

### La base SQLite no existe

```powershell
New-Item -ItemType File -Path database\database.sqlite -Force
php artisan migrate --seed
```

### La interfaz aparece sin estilos o no refleja cambios

Mantener `npm run dev` ejecutándose o generar los recursos una vez con:

```powershell
npm run build
```

### El puerto 8000 está ocupado

```powershell
php artisan serve --port=8001
```

En ese caso, abrir `http://127.0.0.1:8001`.

### El demo queda detenido en “Preparando el sistema del taller”

Comprobar que se instaló AutoTech 0.4.3 o posterior. Las versiones anteriores podían fallar al iniciar el PHP embebido en algunas configuraciones de Windows. Cerrar cualquier instancia de AutoTech e instalar nuevamente la versión publicada más reciente.

## 6. Desarrollo del escritorio y compilación

Estos comandos son para el equipo desarrollador; no hacen falta para instalar el MVP publicado:

```powershell
php artisan native:migrate
php artisan native:seed
composer native:dev
npm run desktop:build
```

El instalador resultante queda en `nativephp/electron/dist/` y no se incorpora al historial Git.
