# AutoTech S.A.

[![CI](https://github.com/sysprosolutions-arg/AutoTech/actions/workflows/ci.yml/badge.svg)](https://github.com/sysprosolutions-arg/AutoTech/actions/workflows/ci.yml)

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

## CI/CD

- **Integración continua:** el workflow `CI` se ejecuta en cada push y pull request hacia `main`. Instala dependencias reproducibles, verifica el formato PHP, compila los recursos web y ejecuta las pruebas automatizadas.
- **Entrega continua:** el workflow `CD - Demo Windows` puede ejecutarse manualmente o mediante una etiqueta `vX.Y.Z`. Verifica el proyecto, construye el instalador x64 y lo publica durante 14 días como artefacto descargable de GitHub Actions.
- **Trazabilidad con Jira:** el repositorio se vincula mediante GitHub for Atlassian. La clave de la tarea debe incluirse en el nombre de la rama, los commits y el título del pull request; por ejemplo, `feature/ATSA-50-consultar-turno`.

Los workflows y sus resultados se consultan en [GitHub Actions](https://github.com/sysprosolutions-arg/AutoTech/actions).

## Estrategia Git

- `main`: versión estable y demostrable.
- `feature/ATSA-<n>-descripcion`: una rama corta por tarea.
- Pull request hacia `main`, revisión y pruebas antes de integrar.
- Commits breves en modo imperativo, incluyendo la clave Jira cuando corresponda; por ejemplo: `ATSA-7 implementar autenticación por sesión`.
- No se versionan `.env`, contraseñas, dependencias, bases locales ni instaladores.
- Cada tarea de cualquier Sprint comienza en una rama nueva creada desde `main` actualizado; nunca se desarrolla directamente en `main`.

## Equipo

| Rol | Integrante |
| --- | --- |
| Scrum Master | Matías |
| Product Owner | Sabrina |
| Analista Funcional | Diego |
| Desarrollador | Cristian |

Organización: [SysPro Solutions ARG](https://github.com/sysprosolutions-arg) · Proyecto Jira: `ATSA` · Espacio Confluence: `ATS`.
