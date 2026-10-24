# AutoTech S.A.

Sistema de información para la gestión integral de clientes, proveedores, repuestos, compras, reparaciones, turnos y facturación de un taller mecánico. La reparación y su orden de trabajo constituyen la transacción principal del dominio.

## Estado del incremento

Este repositorio contiene la base técnica correspondiente a:

- **ATSA-5:** repositorio, estructura inicial, documentación y estrategia de versionado.
- **ATSA-6:** Laravel, Bootstrap, persistencia, configuración, sesiones y distribución de escritorio.
- **ATSA-7:** login seguro, sesión, rutas protegidas y cierre de sesión.

Los módulos funcionales aparecen en el panel como próximos incrementos y no forman parte de estas tres tareas.

## Tecnologías

- PHP 8.5 y Laravel 13.
- Blade, Bootstrap 5.3, Bootstrap Icons y Vite 8.
- MySQL/MariaDB para el entorno web de desarrollo.
- SQLite embebida para la demo autónoma de escritorio.
- NativePHP Desktop 2 y Electron para el instalador de Windows.
- PHPUnit para pruebas automatizadas y Laravel Pint para formato.

La justificación y las decisiones completas están en [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md).

## Puesta en marcha para desarrollo

Requisitos: Windows 10/11, PHP 8.3 o superior con extensiones habituales de Laravel, Composer 2, Node.js 22 o superior, npm y MySQL 8 o MariaDB 11.

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
npm install
```

Crear una base y un usuario exclusivos para la aplicación:

```sql
CREATE DATABASE autotech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'autotech'@'localhost' IDENTIFIED BY 'una-clave-local-segura';
GRANT ALL PRIVILEGES ON autotech.* TO 'autotech'@'localhost';
FLUSH PRIVILEGES;
```

Completar `DB_PORT` y `DB_PASSWORD` en `.env`; luego ejecutar:

```powershell
php artisan migrate --seed
npm run build
php artisan serve
```

Abrir `http://127.0.0.1:8000`. La cuenta local de demostración es `admin` y su contraseña inicial es `AutoTech#2026`. Debe cambiarse o eliminarse antes de cualquier uso real.

## Verificación

```powershell
php artisan test
vendor\bin\pint --test
npm run build
```

## Demo instalable de Windows

La demo usa SQLite automáticamente; el evaluador solo instala la aplicación y no necesita PHP, Composer, Node.js, MySQL ni el repositorio.

```powershell
php artisan native:build win x64
```

El instalador generado queda fuera del control de versiones. El procedimiento completo está en [docs/DEVELOPMENT.md](docs/DEVELOPMENT.md).

## Estrategia Git

- `main`: versión estable y demostrable.
- `feature/ATSA-<n>-descripcion`: una rama corta por tarea.
- Pull request hacia `main`, revisión y pruebas antes de integrar.
- Commits breves en modo imperativo, incluyendo la clave Jira cuando corresponda; por ejemplo: `ATSA-7 implementar autenticación por sesión`.
- No se versionan `.env`, contraseñas, dependencias, bases locales ni instaladores.

## Equipo

| Rol | Integrante |
| --- | --- |
| Scrum Master | Matías |
| Product Owner | Sabrina |
| Analista Funcional | Diego |
| Desarrollador | Cristian |

Organización: [SysPro Solutions ARG](https://github.com/sysprosolutionsarg) · Proyecto Jira: `ATSA` · Espacio Confluence: `ATS`.
