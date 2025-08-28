PrestaSys - sistema de gestion de prestamos

Que es
- SPA simple en PHP + MySQL para manejar clientes, prestamos y pagos.
- Pensado para pymes; interfaz limpia con dashboard y modulos.

Requisitos
- PHP 7.4+ con ext-pdo
- MySQL o MariaDB
- Servidor apuntando a public_html/

Instalacion
- Configura la DB en private/config/database.php.
- Crea la base y tablas (usa tu script SQL).
- Sube el proyecto y deja public_html/ como raiz web.

Estructura
- public_html/: frontend y endpoints (index.php, api.php, auth.php).
- private/: logica, modelos y config (fuera del alcance publico).

Uso rapido
- Abre /login.php e inicia sesion.
- Crea clientes, solicita prestamos, registra pagos.
- KPIs y graficas en el dashboard.

API corta
- auth.php?action=login POST JSON {usuario, password}.
- auth.php?action=logout GET.
- api.php expone acciones para clientes, prestamos y pagos.

Seguridad
- Mantener private/ fuera del webroot.
- APP_DEBUG en false en produccion.
- Usa HTTPS y permisos de archivos correctos.

Notas
- Composer es opcional; esta version no lo requiere.
- Si prefieres .env, agrega autoload y dotenv.

Licencia
- Uso privado.
