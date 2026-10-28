# Entorno de desarrollo y distribución

## Perfiles

### Web local con MySQL/MariaDB

Copiar `.env.example` a `.env`, generar `APP_KEY` y configurar `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`. Cada desarrollador mantiene sus credenciales solo en `.env`.

En esta computadora se detectó MariaDB 11.8 en el puerto `3307`; la cuenta administrativa requiere su contraseña local. No se modifica esa cuenta desde el proyecto.

### Pruebas

PHPUnit utiliza SQLite en memoria según `phpunit.xml`. Las pruebas no dependen del servidor MySQL y no alteran datos de desarrollo.

### Demo de escritorio

NativePHP empaqueta Laravel, PHP, Electron, los recursos compilados y una base SQLite. En el primer inicio migra la base ubicada en AppData y carga el usuario demo si todavía no existen usuarios.

## Comandos habituales

```powershell
# Dependencias
composer install
npm install

# Base web
php artisan migrate --seed

# Desarrollo web
composer dev

# Calidad
php artisan test
vendor\bin\pint --test
npm run build

# Desarrollo de escritorio
php artisan native:migrate
php artisan native:seed
composer native:dev

# Instalador Windows x64
npm run desktop:build
```

## Configuración para VS Code

Se recomienda instalar extensiones de PHP, Blade, EditorConfig y ESLint compatibles con la política del equipo. No se versiona `.vscode`, para no imponer preferencias personales.

## Estrategia de configuración

- `.env.example` documenta variables sin secretos.
- `.env` es local y nunca se versiona.
- `APP_DEBUG` debe quedar en `false` al distribuir una demo.
- `NATIVEPHP_APP_VERSION` debe incrementarse en cada instalador; NativePHP usa ese valor para decidir migraciones de actualización.
- El actualizador automático queda deshabilitado hasta disponer de una política de releases y firma.

## Construcción y entrega de la demo

1. Ejecutar pruebas, Pint y el build de Vite.
2. Establecer `APP_ENV=production` y `APP_DEBUG=false` en el entorno de compilación.
3. Incrementar `NATIVEPHP_APP_VERSION`.
4. Ejecutar `php artisan native:build win x64`.
5. Instalar el artefacto en una PC de prueba y validar login, sesión, rutas protegidas y logout.
6. Publicar el instalador como evidencia o activo de una release; no incorporarlo al repositorio Git.

Una compilación sin firma puede mostrar la advertencia de reputación de Windows. La firma de código se evaluará para una distribución real; no es necesaria para el TPI local.

## Automatización CI/CD en GitHub

El repositorio utiliza GitHub Actions para que las verificaciones sean repetibles y queden registradas como evidencia:

- `.github/workflows/ci.yml` se ejecuta en cada push y pull request hacia `main`. Ejecuta Pint, compila Vite y corre las pruebas automatizadas con SQLite en memoria.
- `.github/workflows/cd-demo-windows.yml` se ejecuta manualmente o al publicar una etiqueta con formato `vX.Y.Z`. Genera el instalador Windows x64 y lo conserva durante 14 días como artefacto descargable.

Para generar una entrega versionada desde Git:

```powershell
git tag v0.2.0
git push origin v0.2.0
```

Antes de crear la etiqueta, la versión debe encontrarse integrada y verificada en `main`. El instalador no se agrega al repositorio.

GitHub for Atlassian proporciona la trazabilidad con Jira. Toda rama, commit y título de pull request debe contener la clave Jira correspondiente.

### Límites conocidos de la demo

- El instalador académico no está firmado y el código PHP no usa el servicio comercial de ofuscación de NativePHP.
- La configuración Electron está publicada para poder generar NSIS en Windows sin requerir Modo desarrollador; `signAndEditExecutable` permanece desactivado hasta incorporar firma de código.
- La aplicación PHP, Composer y el bundle web no presentan avisos conocidos. El runtime NativePHP 2.4 fija Electron 40 y mantiene dos avisos `npm audit` de severidad alta sin corrección compatible. El riesgo queda acotado porque la demo solo carga la aplicación local, no usa `webview`, extensiones, USB/HID, iframes ni navegación externa. Antes de una distribución pública debe actualizarse NativePHP/Electron y repetirse la auditoría.
- No ejecutar `npm audit fix --force` sobre `nativephp/electron`: implicaría saltos mayores no validados. Las actualizaciones compatibles ya se aplican en el lockfile publicado.

## Credencial demo

- Usuario: `admin`
- Contraseña inicial: `AutoTech#2026`

Esta cuenta facilita la evaluación local. No debe utilizarse en producción y la contraseña debe reemplazarse para cualquier entorno compartido.

