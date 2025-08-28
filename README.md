Documentación del Proyecto: Sistema de Gestión de Préstamos "PrestaSys"
1. Visión General del Proyecto
PrestaSys es una aplicación web de página única (SPA) diseñada para la administración integral del ciclo de vida de préstamos. El objetivo principal es proporcionar una herramienta intuitiva, eficiente y centralizada para que pequeñas y medianas empresas financieras puedan gestionar sus operaciones diarias, desde el registro de clientes hasta la liquidación de deudas.

El sistema se enfoca en la claridad y el control, presentando la información de manera visual a través de un dashboard y separando las funcionalidades en módulos lógicos para facilitar su uso por parte de personal con distintos roles.

2. Módulos Principales
La aplicación se estructura en los siguientes módulos clave, accesibles a través de una barra de navegación lateral:

Dashboard: Es la pantalla de inicio y ofrece una vista panorámica del estado del negocio. Muestra indicadores clave de rendimiento (KPIs) como el capital total prestado, el monto cobrado, el número de préstamos activos y los clientes en mora. Incluye gráficos para visualizar el rendimiento mensual y la distribución de los estados de los préstamos.

Clientes: Permite la creación, búsqueda y gestión de la información de los clientes. Cada cliente tiene un perfil único asociado a su cédula de identidad.

Préstamos: Es el corazón del sistema. Desde aquí, los usuarios autorizados pueden:

Solicitar un nuevo préstamo para un cliente existente.

Calcular tablas de amortización en tiempo real, especificando frecuencia de pago (diaria, semanal, mensual) y plazo.

Gestionar el flujo de aprobación de un préstamo, cambiando su estado de "Pendiente" a "Aprobado", "Rechazado" o "Desembolsado".

Registrar Pago: Un módulo simplificado para que los cajeros puedan buscar un préstamo activo por su ID y registrar los abonos de los clientes.

Empleados: Módulo informativo que define los roles y permisos dentro del sistema, asegurando que cada usuario solo tenga acceso a las funcionalidades que le corresponden.

3. Roles y Permisos de Usuario
Para garantizar la seguridad y la correcta delegación de funciones, el sistema define cuatro roles principales:

Admin: Tiene control total sobre todos los módulos. Es el único que puede gestionar usuarios y configuraciones del sistema.

Gerente: Puede realizar todas las tareas de un agente de servicio al cliente, pero además tiene la autoridad para aprobar, rechazar y desembolsar préstamos.

Servicio al Cliente: Su función principal es interactuar con los clientes. Puede registrar nuevos clientes y crear solicitudes de préstamo, que quedarán en estado "Pendiente" para su aprobación.

Cajero: Se encarga de las transacciones financieras. Su acceso está limitado a registrar los pagos de los clientes y, potencialmente, a marcar los préstamos como "Desembolsados".

4. Arquitectura Técnica
El proyecto está construido bajo un modelo de cliente-servidor desacoplado:

Frontend (Cliente):

Un único archivo index.html que funciona como una SPA (Single-Page Application).

Tailwind CSS para un diseño moderno, profesional y totalmente responsivo.

JavaScript (Vanilla) para manejar toda la lógica de la interfaz, la interactividad, la navegación entre vistas y las llamadas a la API.

Chart.js para la visualización de datos en el dashboard.

Backend (Servidor):

Escrito en PHP.

Una estructura de archivos organizada en carpetas private y public_html para mayor seguridad.

Un único punto de entrada público (public_html/api.php) que actúa como enrutador.

Lógica de negocio separada en Controladores y Modelos para interactuar con la base de datos.

Utiliza PDO para una conexión segura y estandarizada a la base de datos.

Base de Datos:

MySQL / MariaDB.

Un esquema relacional con tablas para clientes, prestamos, amortizaciones, pagos, empleados y roles, garantizando la integridad de los datos.

5. Flujo de Trabajo Típico (Solicitud de Préstamo)
Un agente de Servicio al Cliente inicia sesión.

Navega al módulo de Préstamos y hace clic en "Solicitar Préstamo".

Busca a un cliente existente por nombre o cédula.

Completa el formulario del préstamo (monto, interés, frecuencia, plazo). La fecha de solicitud se establece automáticamente.

El sistema calcula una tabla de amortización preliminar.

El agente envía la solicitud. El préstamo se crea en la base de datos con estado "Pendiente".

Un Gerente inicia sesión, ve el préstamo pendiente en la tabla.

El Gerente revisa los detalles y utiliza los botones de acción para "Aprobar" o "Rechazar" el préstamo. El estado se actualiza en la base de datos.

Si es aprobado, el Gerente (o un Cajero) puede proceder a "Desembolsar" el dinero, cambiando el estado una vez más.

A partir de este momento, el préstamo está activo y el cliente puede comenzar a realizar pagos.