# Arquitectura y modelo de implementación

## Decisión

AutoTech adopta un **monolito modular web con renderizado del lado del servidor**. Laravel concentra reglas de negocio, autenticación, autorización, persistencia y vistas Blade. Bootstrap aporta una interfaz consistente y responsive sin introducir la complejidad de una SPA separada.

Esta arquitectura es adecuada para un equipo pequeño, un despliegue local y un dominio administrativo altamente transaccional. Permite entregar rápido las tareas actuales y crecer por módulos sin sumar dos aplicaciones, dos pipelines ni una API prematura.

## Capas

```text
Presentación        Blade + Bootstrap + JavaScript puntual
        ↓
Aplicación          Controllers + Form Requests + Middleware
        ↓
Dominio             Modelos Eloquent + servicios por módulo
        ↓
Persistencia        MySQL/MariaDB (web) | SQLite (demo NativePHP)
```

Los módulos funcionales previstos son Clientes, Proveedores, Repuestos, Compras, Reparaciones, Turnos y Facturación. Reparaciones actúa como núcleo del dominio y se vincula con cliente, vehículo, mecánico, servicio, repuestos, tiempo e incidencias.

## Modelo de diseño

- **MVC:** rutas y controladores coordinan casos de uso; modelos representan entidades persistentes; Blade implementa la presentación.
- **Form Request:** centraliza validación y autorización de cada entrada.
- **Middleware:** protege rutas autenticadas y aplica roles.
- **Service Layer gradual:** se incorporará cuando un caso de uso coordine múltiples agregados o transacciones, especialmente Reparaciones, Compras y Facturación.
- **Transacciones de base de datos:** las operaciones compuestas deben ejecutarse de manera atómica para satisfacer RNF-006.
- **Migraciones y seeders:** el esquema y los datos mínimos son reproducibles en todos los entornos.

No se implementa una arquitectura de microservicios: aumentaría el costo operativo sin aportar valor al alcance actual.

## Seguridad

- Contraseñas almacenadas mediante el hasher seguro de Laravel, nunca en texto plano.
- Regeneración del identificador de sesión al ingresar e invalidación al salir.
- Token CSRF en formularios mutables.
- Limitación de intentos de login: cinco intentos por combinación de usuario e IP.
- Rutas privadas protegidas por `auth`; autorización funcional preparada mediante roles.
- Consultas Eloquent parametrizadas y validación del lado del servidor.
- `.env`, bases locales y secretos excluidos de Git.

La credencial incluida es exclusivamente de demostración local.

## Persistencia por entorno

| Entorno | Motor | Motivo |
| --- | --- | --- |
| Desarrollo web local | SQLite persistente | Permite clonar y probar el proyecto sin instalar un servidor de base de datos. |
| Desarrollo compartido | MySQL 8 o MariaDB 11 | Alternativa para centralizar los datos de varios usuarios o equipos. |
| Pruebas | SQLite temporal | Aislamiento, velocidad y repetibilidad. |
| Demo instalable | SQLite persistente en AppData | Cero dependencias para el evaluador y datos locales por PC. |

Las migraciones son compatibles con ambos motores. NativePHP selecciona SQLite, crea el archivo de usuario y ejecuta migraciones al instalar o actualizar la aplicación.

## Diseño visual

La interfaz adopta el concepto **AutoTech Telemetría**, inspirado en la operación en vivo del circuito de Suzuka y aplicado de forma uniforme al login, panel general y módulos funcionales:

- rojo, negro, blanco y rosa sakura como paleta principal;
- fondos claros con flores de cerezo y referencias visuales japonesas de baja intensidad;
- navegación horizontal superior para los siete módulos;
- panel general con circuito de Suzuka, flujo de reparación, telemetría, estados de puestos, prioridades y alertas;
- tarjetas, tablas y formularios con jerarquía tipográfica ampliada, foco visible, etiquetas explícitas y diseño responsive;
- textos operativos en castellano y logo propio de AutoTech.

El login conserva la identidad visual y separa la propuesta de valor del formulario seguro. Tras autenticarse, el encabezado contextual y la navegación superior se mantienen en Turnos, Reparaciones y Repuestos para evitar cambios de lenguaje visual entre pantallas.

## Trazabilidad de los RNF

| RNF | Decisión técnica |
| --- | --- |
| Rendimiento | Renderizado servidor, índices futuros según consultas y presupuesto de 2 s/5 s. |
| Integración | Base relacional y entidades compartidas, sin duplicar información entre módulos. |
| Usabilidad | Bootstrap, validación server-side, mensajes en español y responsive. |
| Disponibilidad | Aplicación local autónoma; mantenimiento y copias de la base quedan documentados para despliegues posteriores. |
| Mantenibilidad | módulos Laravel, Git, migraciones, pruebas y formato automático. |
| Confiabilidad | claves foráneas, validaciones y transacciones para operaciones compuestas. |
| Seguridad | autenticación, roles, hash, CSRF, rate limiting y configuración por entorno. |

