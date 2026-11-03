# AutoTech S.A.

[![CI](https://github.com/sysprosolutions-arg/AutoTech/actions/workflows/ci.yml/badge.svg)](https://github.com/sysprosolutions-arg/AutoTech/actions/workflows/ci.yml)

Sistema de información para la gestión integral de clientes, proveedores, repuestos, compras, reparaciones, turnos y facturación de un taller mecánico. La reparación y su orden de trabajo constituyen la transacción principal del dominio.

## Estado del MVP

El MVP incluye login y sesión, datos maestros, turnos, presupuestos, decisión del cliente, generación automática de órdenes de trabajo, asignación de empleados, reparaciones y gestión principal de repuestos. Compras y Facturación permanecen fuera del incremento funcional vigente.

## Tecnologías

- PHP 8.5 y Laravel 13.
- Blade, Bootstrap 5.3, Bootstrap Icons y Vite 8.
- SQLite para la puesta en marcha local; MySQL/MariaDB como alternativa compartida.
- SQLite embebida para la demo autónoma de escritorio.
- NativePHP Desktop 2 y Electron para el instalador de Windows.
- PHPUnit para pruebas automatizadas y Laravel Pint para formato.

La justificación y las decisiones completas están en [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md).

## Elegir cómo probar AutoTech

### Opción A: demo instalable

La forma más rápida de evaluar el sistema es descargar `AutoTech-0.4.3-setup.exe` desde la [Release del MVP](https://github.com/sysprosolutions-arg/AutoTech/releases/tag/v0.4.3). Solo requiere Windows 10/11 x64: no hace falta instalar PHP, Composer, Node.js, MySQL, Visual Studio Code ni clonar el repositorio.

La compilación académica no está firmada comercialmente, por lo que Windows puede mostrar una advertencia de reputación. La suma SHA-256 esperada es:

```text
838B29390F6F1C9EA157CCEDF5E1ABD5FF2F86CCD2B0693CA10241ECA0E5465C
```

### Opción B: ejecutar el código fuente

Requisitos para Windows 10/11 x64:

- PHP 8.5 x64 en el `PATH`, con las extensiones habituales de Laravel y PHPUnit: `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `fileinfo`, `dom` y `xmlwriter`.
- Composer 2.
- Node.js 22 o superior con npm.
- Git y Visual Studio Code, recomendados.
- No se necesita instalar MySQL para la prueba local recomendada.

Comprobar las herramientas antes de continuar:

```powershell
php -v
composer --version
node -v
npm -v
git --version
```

PHP debe informar la versión 8.5. XAMPP con PHP 8.1 no es compatible con las dependencias bloqueadas del proyecto.

Clonar y preparar el sistema:

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

Ejecutar el backend en una terminal de Visual Studio Code:

```powershell
php artisan serve
```

En una segunda terminal, ejecutar Vite:

```powershell
npm run dev
```

Abrir `http://127.0.0.1:8000`. Para detener cada proceso, usar `Ctrl+C`.

La guía completa, incluyendo la alternativa MySQL/MariaDB y solución de problemas, está en [docs/INSTALLATION.md](docs/INSTALLATION.md).

## Credencial local de demostración

- Usuario: `admin`
- Contraseña inicial: `AutoTech#2026`

Debe cambiarse o eliminarse antes de cualquier uso real o compartido.

## Verificación

```powershell
php artisan test
vendor\bin\pint --test
npm run build
```

## Construcción del instalador de Windows

Esta sección es para desarrolladores que necesitan generar un instalador nuevo. No es necesaria para probar la Release publicada.

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
