<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Gestión de Préstamos</title>
    <!-- Chosen Palette: Paleta Corporativa Azul y Gris -->
    <!-- Application Structure Plan: Se ha diseñado una Single-Page Application (SPA) con una estructura de dashboard modular. La navegación lateral permite cambiar entre "vistas" (Dashboard, Clientes, Préstamos, Pagos, Empleados) que se muestran u ocultan dinámicamente con JavaScript. Esta arquitectura fue elegida porque es la más intuitiva para una aplicación de gestión, separando las funcionalidades por tareas y permitiendo al usuario enfocarse en una sola área a la vez (ej. crear un cliente, luego registrar un préstamo) sin recargar la página, lo que resulta en una experiencia de usuario fluida y eficiente. -->
    <!-- Visualization & Content Choices: 
        - Dashboard: KPI Cards (HTML/CSS) para informar métricas clave de un vistazo. Gráfico de Barras (Chart.js) para comparar préstamos vs. cobros a lo largo del tiempo. Gráfico de Anillo (Chart.js) para mostrar la proporción de estados de préstamos. Interacción: Tooltips al pasar el ratón. Justificación: Proporciona un resumen visual rápido del estado del negocio.
        - Clientes/Préstamos/Pagos: Tablas interactivas (HTML/CSS) para organizar y gestionar datos. Interacción: Búsqueda y filtrado dinámico con JS. Justificación: Es el método estándar y más eficiente para manejar listas de registros.
        - Creación de Préstamo: Formulario con cálculo de amortización en tiempo real (JS). Interacción: El usuario ingresa datos y la tabla de pagos se genera instantáneamente. Justificación: Es la funcionalidad central del sistema y una herramienta interactiva de alto valor que demuestra la lógica de negocio.
    -->
    <!-- CONFIRMATION: NO SVG graphics used. NO Mermaid JS used. -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-icon { width: 20px; text-align: center; }
        .kpi-card { transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; }
        .kpi-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .chart-container { position: relative; width: 100%; max-width: 800px; margin-left: auto; margin-right: auto; height: 350px; max-height: 40vh; }
        @media (max-width: 768px) { .chart-container { height: 300px; max-height: 50vh; } }
        .modal-backdrop {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.5); z-index: 40;
            display: none; align-items: center; justify-content: center;
        }
        .modal-content { max-height: 90vh; overflow-y: auto; }
        .nav-link.active { background-color: #2563EB; color: white; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="flex h-screen">
        <!-- Sidebar de Navegación -->
        <aside class="w-64 bg-gray-800 text-white flex flex-col fixed h-full md:relative md:translate-x-0 transform -translate-x-full transition-transform duration-200 ease-in-out z-30" id="sidebar">
            <div class="h-16 flex items-center justify-center border-b border-gray-700">
                <i class="fas fa-landmark mr-3 text-xl text-blue-400"></i>
                <h1 class="text-xl font-bold">PrestaSys</h1>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="#" class="nav-link flex items-center px-4 py-2 rounded-lg hover:bg-gray-700 active" data-view="dashboard">
                    <i class="fas fa-chart-pie sidebar-icon mr-3"></i> Dashboard
                </a>
                <a href="#" class="nav-link flex items-center px-4 py-2 rounded-lg hover:bg-gray-700" data-view="clientes">
                    <i class="fas fa-users sidebar-icon mr-3"></i> Clientes
                </a>
                <a href="#" class="nav-link flex items-center px-4 py-2 rounded-lg hover:bg-gray-700" data-view="prestamos">
                    <i class="fas fa-hand-holding-dollar sidebar-icon mr-3"></i> Préstamos
                </a>
                <a href="#" class="nav-link flex items-center px-4 py-2 rounded-lg hover:bg-gray-700" data-view="pagos">
                    <i class="fas fa-cash-register sidebar-icon mr-3"></i> Registrar Pago
                </a>
                <a href="#" class="nav-link flex items-center px-4 py-2 rounded-lg hover:bg-gray-700" data-view="empleados">
                    <i class="fas fa-user-tie sidebar-icon mr-3"></i> Empleados
                </a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center">
                    <div id="user-initials" class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center font-bold">
                        --
                    </div>
                    <div class="ml-3">
                        <p id="user-name" class="font-semibold">Cargando...</p>
                        <p id="user-role" class="text-xs text-gray-400">...</p>
                    </div>
                </div>
                 <button id="logout-btn" class="w-full mt-4 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Cerrar Sesión
                </button>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1 flex flex-col overflow-y-auto">
            <header class="bg-white shadow-md p-4 flex justify-between items-center">
                <button id="menu-toggle" class="text-gray-600 focus:outline-none md:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-2xl font-semibold text-gray-700" id="view-title">Dashboard</h2>
                <div class="relative">
                    <i class="fas fa-bell text-gray-600 text-xl"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">3</span>
                </div>
            </header>

            <div class="p-4 md:p-8 flex-1">
                <!-- VISTA: Dashboard -->
                <div id="dashboard-view" class="view-content">
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Resumen General</h3>
                        <p class="text-gray-500">Esta sección ofrece una vista panorámica de las métricas más importantes del negocio, permitiendo evaluar rápidamente la salud financiera y operativa de la cartera de préstamos.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="kpi-card bg-white p-6 rounded-xl shadow-lg flex items-center">
                            <div class="bg-blue-100 text-blue-600 rounded-full h-12 w-12 flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500">Capital Prestado</p>
                                <p class="text-2xl font-bold">$1,250,000</p>
                            </div>
                        </div>
                        <div class="kpi-card bg-white p-6 rounded-xl shadow-lg flex items-center">
                            <div class="bg-green-100 text-green-600 rounded-full h-12 w-12 flex items-center justify-center">
                                <i class="fas fa-wallet text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500">Total Cobrado</p>
                                <p class="text-2xl font-bold">$480,500</p>
                            </div>
                        </div>
                         <div class="kpi-card bg-white p-6 rounded-xl shadow-lg flex items-center">
                            <div class="bg-yellow-100 text-yellow-600 rounded-full h-12 w-12 flex items-center justify-center">
                                <i class="fas fa-file-invoice-dollar text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500">Préstamos Activos</p>
                                <p class="text-2xl font-bold">152</p>
                            </div>
                        </div>
                        <div class="kpi-card bg-white p-6 rounded-xl shadow-lg flex items-center">
                            <div class="bg-red-100 text-red-600 rounded-full h-12 w-12 flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500">Clientes en Mora</p>
                                <p class="text-2xl font-bold">18</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                        <div class="lg:col-span-3 bg-white p-6 rounded-xl shadow-lg">
                             <h4 class="text-lg font-semibold mb-4">Rendimiento Mensual</h4>
                             <div class="chart-container">
                                <canvas id="monthlyPerformanceChart"></canvas>
                            </div>
                        </div>
                        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg">
                            <h4 class="text-lg font-semibold mb-4">Distribución de Préstamos</h4>
                            <div class="chart-container">
                                <canvas id="loanStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VISTA: Clientes -->
                <div id="clientes-view" class="view-content hidden">
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Gestión de Clientes</h3>
                        <p class="text-gray-500">Aquí puede registrar nuevos clientes y consultar la información de los existentes. Utilice la barra de búsqueda para encontrar rápidamente a un cliente por su nombre o cédula.</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="flex justify-between items-center mb-4">
                             <div class="relative w-full max-w-sm">
                                <input type="text" id="cliente-search" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Buscar cliente...">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <button id="btn-new-client" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                                <i class="fas fa-plus mr-2"></i> Nuevo Cliente
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="p-3">Nombre Completo</th>
                                        <th class="p-3">Cédula</th>
                                        <th class="p-3">Teléfono</th>
                                        <th class="p-3">Fecha Registro</th>
                                        <th class="p-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="clientes-table-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- VISTA: Préstamos -->
                <div id="prestamos-view" class="view-content hidden">
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Gestión de Préstamos</h3>
                        <p class="text-gray-500">Cree nuevas solicitudes de préstamo, consulte el estado de las existentes y visualice las tablas de amortización detalladas para cada una.</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                         <div class="flex justify-between items-center mb-4">
                             <div class="relative w-full max-w-sm">
                                <input type="text" id="prestamo-search" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Buscar por cliente, cédula o ID...">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <button id="btn-new-loan" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                                <i class="fas fa-plus mr-2"></i> Solicitar Préstamo
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="p-3">ID Préstamo</th>
                                        <th class="p-3">Cliente</th>
                                        <th class="p-3">Monto</th>
                                        <th class="p-3">Fecha Solicitud</th>
                                        <th class="p-3">Estado</th>
                                        <th class="p-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="prestamos-table-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- VISTA: Pagos -->
                <div id="pagos-view" class="view-content hidden">
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Registrar Pago</h3>
                        <p class="text-gray-500">Busque un préstamo por su ID para ver los detalles de la próxima cuota y registrar el pago del cliente de manera rápida y segura.</p>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-1">
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <h4 class="font-semibold mb-4">Buscar Préstamo</h4>
                                <div class="relative">
                                    <input type="text" id="pago-search-prestamo" class="w-full pl-10 pr-4 py-2 border rounded-lg" placeholder="ID del Préstamo...">
                                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                </div>
                                <button id="btn-search-pago" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Buscar</button>
                            </div>
                            <div id="payment-details-card" class="bg-white p-6 rounded-xl shadow-lg mt-6 hidden">
                                <h4 class="font-semibold mb-4 text-lg">Detalles de Cuota</h4>
                                <div class="space-y-3 text-gray-600">
                                    <p><strong>Cliente:</strong> <span id="pago-cliente-nombre"></span></p>
                                    <p><strong>Próxima Cuota:</strong> <span id="pago-numero-cuota"></span></p>
                                    <p><strong>Fecha Límite:</strong> <span id="pago-fecha-limite"></span></p>
                                    <p class="text-2xl font-bold text-blue-600"><strong>Monto:</strong> $<span id="pago-monto-cuota"></span></p>
                                </div>
                                <div class="mt-6">
                                    <label for="monto_pagado" class="block font-medium mb-1">Monto a Pagar</label>
                                    <input type="number" id="monto_pagado" class="w-full p-2 border rounded-lg" placeholder="0.00">
                                    <button class="w-full mt-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">Confirmar Pago</button>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg">
                            <h4 class="font-semibold mb-4">Pagos Recientes</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="p-3">ID Pago</th>
                                            <th class="p-3">ID Préstamo</th>
                                            <th class="p-3">Monto</th>
                                            <th class="p-3">Fecha</th>
                                            <th class="p-3">Cajero</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pagos-recientes-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VISTA: Empleados -->
                <div id="empleados-view" class="view-content hidden">
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Gestión de Empleados y Roles</h3>
                        <p class="text-gray-500">Administre los usuarios del sistema, sus roles y permisos de acceso a las diferentes funcionalidades de la plataforma.</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-lg">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="p-3">Nombre Completo</th>
                                        <th class="p-3">Usuario</th>
                                        <th class="p-3">Rol</th>
                                        <th class="p-3">Descripción del Rol</th>
                                    </tr>
                                </thead>
                                <tbody id="empleados-table-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Modal para Nuevo Cliente -->
    <div id="modal-new-client" class="modal-backdrop">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg m-4 modal-content">
            <div class="p-6 border-b">
                <h3 class="text-xl font-semibold">Registrar Nuevo Cliente</h3>
            </div>
            <div class="p-6">
                <form class="space-y-4">
                    <div>
                        <label for="nombre_completo" class="block font-medium mb-1">Nombre Completo</label>
                        <input type="text" id="nombre_completo" class="w-full p-2 border rounded-lg" required>
                    </div>
                    <div>
                        <label for="cedula" class="block font-medium mb-1">Cédula / ID</label>
                        <input type="text" id="cedula" class="w-full p-2 border rounded-lg" required>
                    </div>
                    <div>
                        <label for="telefono" class="block font-medium mb-1">Teléfono</label>
                        <input type="tel" id="telefono" class="w-full p-2 border rounded-lg">
                    </div>
                     <div>
                        <label for="direccion" class="block font-medium mb-1">Dirección</label>
                        <textarea id="direccion" rows="3" class="w-full p-2 border rounded-lg"></textarea>
                    </div>
                </form>
            </div>
            <div class="p-4 bg-gray-50 flex justify-end space-x-3 rounded-b-xl">
                <button type="button" class="btn-close-modal px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Guardar Cliente</button>
            </div>
        </div>
    </div>

    <!-- Modal para Nuevo Préstamo -->
    <div id="modal-new-loan" class="modal-backdrop">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl m-4 modal-content">
            <div class="p-6 border-b">
                <h3 class="text-xl font-semibold">Solicitar Préstamo</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Columna de Formulario -->
                    <div>
                        <h4 class="font-semibold text-lg mb-4 border-b pb-2">Datos del Préstamo</h4>
                        <form id="form-new-loan" class="space-y-4">
                            <div class="relative">
                                <label for="loan_cliente_search" class="block font-medium mb-1">Buscar Cliente (Nombre o Cédula)</label>
                                <input type="text" id="loan_cliente_search" class="w-full p-2 border rounded-lg" placeholder="Escriba para buscar..." autocomplete="off">
                                <input type="hidden" id="loan_cliente_id" required>
                                <div id="search-results" class="absolute z-10 w-full bg-white border rounded-lg mt-1 max-h-48 overflow-y-auto hidden shadow-lg">
                                </div>
                            </div>
                            <div>
                                <label for="loan_amount" class="block font-medium mb-1">Monto a Solicitar ($)</label>
                                <input type="number" id="loan_amount" class="w-full p-2 border rounded-lg" placeholder="5000" required>
                            </div>
                            <div>
                                <label for="loan_interest_rate" class="block font-medium mb-1">Tasa de Interés Anual (%)</label>
                                <input type="number" id="loan_interest_rate" class="w-full p-2 border rounded-lg" placeholder="22" required>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="loan_frequency" class="block font-medium mb-1">Frecuencia de Pago</label>
                                    <select id="loan_frequency" class="w-full p-2 border rounded-lg bg-white">
                                        <option value="mensual" selected>Mensual</option>
                                        <option value="semanal">Semanal</option>
                                        <option value="diario">Diario</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="loan_term" id="loan_term_label" class="block font-medium mb-1">Plazo (Meses)</label>
                                    <input type="number" id="loan_term" class="w-full p-2 border rounded-lg" placeholder="12" required>
                                </div>
                            </div>
                            <div>
                                <label for="loan_date" class="block font-medium mb-1">Fecha de Solicitud</label>
                                <input type="text" id="loan_date" class="w-full p-2 border rounded-lg bg-gray-100" readonly>
                            </div>
                            <button type="button" id="btn-calculate-amortization" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Calcular Amortización</button>
                        </form>
                    </div>
                    <!-- Columna de Tabla de Amortización -->
                    <div>
                        <h4 class="font-semibold text-lg mb-4 border-b pb-2">Tabla de Amortización (Vista Previa)</h4>
                        <div id="amortization-preview" class="border rounded-lg h-96 overflow-y-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-100 sticky top-0">
                                    <tr>
                                        <th class="p-2">#</th>
                                        <th class="p-2">Fecha</th>
                                        <th class="p-2">Cuota</th>
                                        <th class="p-2">Capital</th>
                                        <th class="p-2">Interés</th>
                                        <th class="p-2">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody id="amortization-table-body">
                                    <tr><td colspan="6" class="text-center p-8 text-gray-500">Ingrese los datos y presione "Calcular"</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gray-50 flex justify-end space-x-3 rounded-b-xl">
                <button type="button" class="btn-close-modal px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancelar</button>
                <button type="submit" form="form-new-loan" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Enviar Solicitud</button>
            </div>
        </div>
    </div>
    
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentUser = null;
    
    // La URL base de tu API. Ajusta la ruta si es necesario.
    const API_URL = 'api.php';

    // Datos de ejemplo para módulos aún no conectados al backend
    const mockData = {
        clientes: [
            { id: 1, nombre: 'Juan Pérez', cedula: '001-1234567-8', telefono: '809-555-1234', fecha: '2024-01-15' },
            { id: 2, nombre: 'María Rodríguez', cedula: '001-7654321-9', telefono: '829-555-5678', fecha: '2024-02-20' },
            { id: 3, nombre: 'Carlos Gómez', cedula: '402-9876543-1', telefono: '849-555-9012', fecha: '2024-03-10' },
            { id: 4, nombre: 'Laura Fernández', cedula: '031-3456789-2', telefono: '809-555-3456', fecha: '2024-04-05' }
        ],
        pagos: [
            {id: 'T001', prestamoId: 'P001', monto: 4500, fecha: '2024-08-15', cajero: 'Luis Soto'},
            {id: 'T003', prestamoId: 'P001', monto: 4500, fecha: '2024-07-15', cajero: 'Ana Ríos'},
        ],
        empleados: [
            { id: 1, nombre: 'Ana Ríos', usuario: 'arios', rol: 'Admin', desc: 'Acceso total al sistema. Gestiona usuarios, configuraciones y todos los módulos.' },
            { id: 2, nombre: 'Pedro Castillo', usuario: 'pcastillo', rol: 'Gerente', desc: 'Puede crear solicitudes y administrar el ciclo de vida de los préstamos (aprobar, rechazar).' },
            { id: 3, nombre: 'Sofía Luna', usuario: 'sluna', rol: 'Servicio al Cliente', desc: 'Registra clientes y crea nuevas solicitudes de préstamo para aprobación.' },
            { id: 4, nombre: 'Luis Soto', usuario: 'lsoto', rol: 'Cajero', desc: 'Registra pagos y desembolsa el dinero de los préstamos aprobados.' }
        ]
    };

    const viewTitle = document.getElementById('view-title');
    const navLinks = document.querySelectorAll('.nav-link');
    const views = document.querySelectorAll('.view-content');

    // --- LÓGICA DE AUTENTICACIÓN Y ROLES ---

    async function checkAuth() {
        try {
            const response = await fetch(`${API_URL}?action=get_session`);
            if (!response.ok) {
                window.location.href = 'login.php';
                return; // Detiene la ejecución si no está autenticado
            }
            currentUser = await response.json();
            initializeDashboard();
        } catch (error) {
            console.error('Error de autenticación:', error);
            window.location.href = 'login.php';
        }
    }

    function updateUserInfoUI() {
        if (!currentUser) return;
        const initials = currentUser.nombre.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
        document.getElementById('user-initials').textContent = initials;
        document.getElementById('user-name').textContent = currentUser.nombre;
        document.getElementById('user-role').textContent = currentUser.rol;
    }

    function updateNavForRole() {
        if (!currentUser) return;
        const userRole = currentUser.rol;
        const permissions = {
            'Admin': ['dashboard', 'clientes', 'prestamos', 'pagos', 'empleados'],
            'Gerente': ['dashboard', 'clientes', 'prestamos', 'pagos'],
            'Servicio al Cliente': ['dashboard', 'clientes', 'prestamos'],
            'Cajero': ['dashboard', 'pagos']
        };

        const allowedViews = permissions[userRole] || [];

        navLinks.forEach(link => {
            const view = link.dataset.view;
            if (allowedViews.includes(view)) {
                link.style.display = 'flex';
            } else {
                link.style.display = 'none';
            }
        });
    }

    document.getElementById('logout-btn').addEventListener('click', async () => {
        try {
            await fetch(`${API_URL}?action=logout`, { method: 'POST' });
            window.location.href = 'login.php';
        } catch (error) {
            console.error('Error al cerrar sesión:', error);
            alert('No se pudo cerrar la sesión. Intente de nuevo.');
        }
    });

    // --- FIN DE LÓGICA DE AUTENTICACIÓN ---


    function showView(viewId) {
        views.forEach(view => view.classList.add('hidden'));
        document.getElementById(`${viewId}-view`).classList.remove('hidden');
        
        const activeLink = document.querySelector(`.nav-link[data-view="${viewId}"]`);
        navLinks.forEach(link => link.classList.remove('active'));
        activeLink.classList.add('active');
        
        viewTitle.textContent = activeLink.textContent.trim();
    }

    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const viewId = e.currentTarget.dataset.view;
            showView(viewId);
            if (window.innerWidth < 768) {
                document.getElementById('sidebar').classList.add('-translate-x-full');
            }
        });
    });
    
    document.getElementById('menu-toggle').addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
    });

    function renderClientesTable(filter = '') {
        const tableBody = document.getElementById('clientes-table-body');
        tableBody.innerHTML = '';
        const filteredData = mockData.clientes.filter(c => 
            c.nombre.toLowerCase().includes(filter.toLowerCase()) || 
            c.cedula.includes(filter)
        );
        filteredData.forEach(cliente => {
            const row = `
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">${cliente.nombre}</td>
                    <td class="p-3">${cliente.cedula}</td>
                    <td class="p-3">${cliente.telefono}</td>
                    <td class="p-3">${cliente.fecha}</td>
                    <td class="p-3">
                        <button class="text-blue-500 hover:text-blue-700 mr-2"><i class="fas fa-eye"></i></button>
                        <button class="text-yellow-500 hover:text-yellow-700"><i class="fas fa-edit"></i></button>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }
    
    function getStatusBadge(status) {
        switch(status) {
            case 'Desembolsado': return '<span class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">Desembolsado</span>';
            case 'Pendiente': return '<span class="bg-yellow-100 text-yellow-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">Pendiente</span>';
            case 'Aprobado': return '<span class="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">Aprobado</span>';
            case 'Rechazado': return '<span class="bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">Rechazado</span>';
            case 'Saldado': return '<span class="bg-gray-200 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">Saldado</span>';
            default: return `<span class="bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full">${status}</span>`;
        }
    }
    
    function getActionButtons(prestamo) {
        switch(prestamo.estado) {
            case 'Pendiente':
                return `
                    <button data-id="${prestamo.id}" data-action="Aprobado" class="action-btn text-xs bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-2 rounded">Aprobar</button>
                    <button data-id="${prestamo.id}" data-action="Rechazado" class="action-btn text-xs bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded ml-1">Rechazar</button>
                `;
            case 'Aprobado':
                return `
                    <button data-id="${prestamo.id}" data-action="Desembolsado" class="action-btn text-xs bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-2 rounded">Desembolsar</button>
                `;
            default:
                return `<button class="text-blue-500 hover:text-blue-700"><i class="fas fa-file-alt"></i> Ver</button>`;
        }
    }

    async function fetchAndRenderPrestamos(filter = '') {
    try {
        const response = await fetch(`${API_URL}?action=list_prestamos&filter=${encodeURIComponent(filter)}`);
        const text = await response.text();
        let prestamos = [];
        try {
            prestamos = JSON.parse(text);
        } catch (e) {
            prestamos = [];
        }
        const tableBody = document.getElementById('prestamos-table-body');
        tableBody.innerHTML = '';
        if (!Array.isArray(prestamos) || prestamos.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center p-8 text-gray-500">No se encontraron préstamos.</td></tr>';
            return;
        }
        prestamos.forEach(p => {
            const row = `
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-mono">${p.id}</td>
                    <td class="p-3">${p.clienteNombre}</td>
                    <td class="p-3">$${parseFloat(p.monto).toLocaleString()}</td>
                    <td class="p-3">${p.fecha}</td>
                    <td class="p-3">${getStatusBadge(p.estado)}</td>
                    <td class="p-3 text-center">${getActionButtons(p)}</td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    } catch (error) {
        console.error('Error al cargar los préstamos:', error);
        const tableBody = document.getElementById('prestamos-table-body');
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center p-8 text-red-500">Error al cargar los datos.</td></tr>';
    }
}
    
    document.getElementById('prestamos-table-body').addEventListener('click', async (e) => {
        if (e.target.classList.contains('action-btn')) {
            const id = e.target.dataset.id;
            const nuevoEstado = e.target.dataset.action;

            try {
                const response = await fetch(`${API_URL}?action=update_prestamo_status`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id, estado: nuevoEstado })
                });
                const result = await response.json();
                if (response.ok) {
                    fetchAndRenderPrestamos(document.getElementById('prestamo-search').value);
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error al actualizar estado:', error);
                alert('Error de conexión al actualizar el estado.');
            }
        }
    });
    
    function renderPagosRecientesTable() {
        const tableBody = document.getElementById('pagos-recientes-table-body');
        tableBody.innerHTML = '';
        mockData.pagos.forEach(p => {
            const row = `
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-mono">${p.id}</td>
                    <td class="p-3 font-mono">${p.prestamoId}</td>
                    <td class="p-3">$${p.monto.toLocaleString()}</td>
                    <td class="p-3">${new Date(p.fecha).toLocaleString()}</td>
                    <td class="p-3">${p.cajero}</td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }
    
    async function fetchAndRenderEmpleados() {
        try {
            const response = await fetch(`${API_URL}?action=list_empleados`);
            const empleados = await response.json();
            const tableBody = document.getElementById('empleados-table-body');
            tableBody.innerHTML = '';

            if (response.ok) {
                empleados.forEach(e => {
                    const row = `
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3 font-semibold">${e.nombre}</td>
                            <td class="p-3 font-mono">${e.usuario}</td>
                            <td class="p-3 font-medium">${e.rol}</td>
                            <td class="p-3 text-sm text-gray-600">${e.descripcion || 'N/A'}</td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            }
        } catch (error) {
            console.error('Error al cargar empleados:', error);
        }
    }

    document.getElementById('cliente-search').addEventListener('input', (e) => renderClientesTable(e.target.value));
    document.getElementById('prestamo-search').addEventListener('input', (e) => fetchAndRenderPrestamos(e.target.value));

    const modals = document.querySelectorAll('.modal-backdrop');
    const closeButtons = document.querySelectorAll('.btn-close-modal');

    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
    }

    function closeModal() {
        modals.forEach(modal => modal.style.display = 'none');
    }

        // Manejo del formulario de nuevo cliente
        document.querySelector('#modal-new-client form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const nombre = document.getElementById('nombre_completo').value.trim();
            const cedula = document.getElementById('cedula').value.trim();
            const telefono = document.getElementById('telefono').value.trim();
            const direccion = document.getElementById('direccion').value.trim();

            if (!nombre || !cedula) {
                alert('Nombre y cédula son obligatorios.');
                return;
            }

            const clienteData = {
                nombre_completo: nombre,
                cedula: cedula,
                telefono: telefono,
                direccion: direccion
            };

            try {
                const response = await fetch(`${API_URL}?action=create_cliente`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(clienteData)
                });
                const result = await response.json();
                if (response.ok) {
                    closeModal();
                    renderClientesTable(); // Si tienes backend, actualiza para que use datos reales
                    showView('clientes');
                } else {
                    alert('Error al guardar cliente: ' + (result.message || 'Error desconocido'));
                }
            } catch (error) {
                console.error('Error al guardar cliente:', error);
                alert('Error de conexión al guardar el cliente.');
            }
        });

    document.getElementById('btn-new-client').addEventListener('click', () => openModal('modal-new-client'));
    document.getElementById('btn-new-loan').addEventListener('click', () => {
        const form = document.getElementById('form-new-loan');
        form.reset();
        document.getElementById('loan_cliente_id').value = '';
        document.getElementById('amortization-table-body').innerHTML = '<tr><td colspan="6" class="text-center p-8 text-gray-500">Ingrese los datos y presione "Calcular"</td></tr>';
        
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('loan_date').value = today;
        
        openModal('modal-new-loan');
    });

    closeButtons.forEach(btn => btn.addEventListener('click', closeModal));
    modals.forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    });

    const loanClienteSearch = document.getElementById('loan_cliente_search');
    const loanClienteId = document.getElementById('loan_cliente_id');
    const searchResultsContainer = document.getElementById('search-results');

    loanClienteSearch.addEventListener('input', async () => {
        const searchTerm = loanClienteSearch.value.trim();
        searchResultsContainer.innerHTML = '';
        loanClienteId.value = '';

        if (searchTerm.length < 2) {
            searchResultsContainer.classList.add('hidden');
            return;
        }
        
        try {
            const response = await fetch(`${API_URL}?action=search_clientes&term=${encodeURIComponent(searchTerm)}`);
            const results = await response.json();

            if (results.length > 0) {
                results.forEach(cliente => {
                    const resultItem = document.createElement('div');
                    resultItem.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                    resultItem.textContent = `${cliente.nombre_completo} - ${cliente.cedula}`;
                    resultItem.dataset.id = cliente.id;
                    resultItem.dataset.name = cliente.nombre_completo;
                    resultItem.addEventListener('click', (e) => {
                        loanClienteSearch.value = e.currentTarget.dataset.name;
                        loanClienteId.value = e.currentTarget.dataset.id;
                        searchResultsContainer.classList.add('hidden');
                    });
                    searchResultsContainer.appendChild(resultItem);
                });
                searchResultsContainer.classList.remove('hidden');
            } else {
                searchResultsContainer.classList.add('hidden');
            }
        } catch (error) {
            console.error('Error al buscar clientes:', error);
        }
    });
    
    document.addEventListener('click', function(event) {
        if (!loanClienteSearch.contains(event.target) && !searchResultsContainer.contains(event.target)) {
            searchResultsContainer.classList.add('hidden');
        }
    });

    document.getElementById('loan_frequency').addEventListener('change', (e) => {
        const termLabel = document.getElementById('loan_term_label');
        const termInput = document.getElementById('loan_term');
        switch(e.target.value) {
            case 'diario':
                termLabel.textContent = 'Plazo (Días)';
                termInput.placeholder = '30';
                break;
            case 'semanal':
                termLabel.textContent = 'Plazo (Semanas)';
                termInput.placeholder = '4';
                break;
            case 'mensual':
            default:
                termLabel.textContent = 'Plazo (Meses)';
                termInput.placeholder = '12';
                break;
        }
    });

    document.getElementById('btn-calculate-amortization').addEventListener('click', () => {
        const amount = parseFloat(document.getElementById('loan_amount').value);
        const annualRate = parseFloat(document.getElementById('loan_interest_rate').value);
        const termPeriods = parseInt(document.getElementById('loan_term').value);
        const frequency = document.getElementById('loan_frequency').value;
        const startDateString = document.getElementById('loan_date').value;

        if (isNaN(amount) || isNaN(annualRate) || isNaN(termPeriods) || !startDateString) {
            alert('Por favor, complete todos los campos del préstamo.');
            return;
        }
        
        const startDate = new Date(startDateString + 'T00:00:00');

        let periodicRate;
        let periodsPerYear;

        switch(frequency) {
            case 'diario':
                periodsPerYear = 365;
                break;
            case 'semanal':
                periodsPerYear = 52;
                break;
            case 'mensual':
            default:
                periodsPerYear = 12;
                break;
        }
        
        periodicRate = annualRate / 100 / periodsPerYear;

        const periodicPayment = amount * (periodicRate * Math.pow(1 + periodicRate, termPeriods)) / (Math.pow(1 + periodicRate, termPeriods) - 1);
        
        let balance = amount;
        const tableBody = document.getElementById('amortization-table-body');
        tableBody.innerHTML = '';

        for (let i = 1; i <= termPeriods; i++) {
            const interest = balance * periodicRate;
            const principal = periodicPayment - interest;
            balance -= principal;

            const paymentDate = new Date(startDate);
            switch(frequency) {
                case 'diario':
                    paymentDate.setDate(startDate.getDate() + i);
                    break;
                case 'semanal':
                    paymentDate.setDate(startDate.getDate() + (i * 7));
                    break;
                case 'mensual':
                default:
                    paymentDate.setMonth(startDate.getMonth() + i);
                    break;
            }

            const row = `
                <tr class="border-b">
                    <td class="p-2 text-center">${i}</td>
                    <td class="p-2">${paymentDate.toLocaleDateString()}</td>
                    <td class="p-2 text-right">$${periodicPayment.toFixed(2)}</td>
                    <td class="p-2 text-right">$${principal.toFixed(2)}</td>
                    <td class="p-2 text-right">$${interest.toFixed(2)}</td>
                    <td class="p-2 text-right font-medium">$${Math.abs(balance).toFixed(2)}</td>
                </tr>
            `;
            tableBody.innerHTML += row;
        }
    });
    
    document.getElementById('form-new-loan').addEventListener('submit', async (e) => {
        e.preventDefault();
        const clienteId = document.getElementById('loan_cliente_id').value;
        
        if (!clienteId) {
            alert('Por favor, seleccione un cliente válido de la búsqueda.');
            return;
        }

        const loanData = {
            id_cliente: clienteId,
            monto_aprobado: document.getElementById('loan_amount').value,
            tasa_interes_anual: document.getElementById('loan_interest_rate').value,
            plazo: document.getElementById('loan_term').value,
            frecuencia_pago: document.getElementById('loan_frequency').value,
            fecha_solicitud: document.getElementById('loan_date').value,
        };

        try {
            const response = await fetch(`${API_URL}?action=create_prestamo`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(loanData)
            });
            const result = await response.json();
            if (response.ok) {
                fetchAndRenderPrestamos();
                closeModal();
                showView('prestamos');
            } else {
                alert('Error al crear préstamo: ' + result.message);
            }
        } catch (error) {
            console.error('Error al crear préstamo:', error);
            alert('Error de conexión al crear el préstamo.');
        }
    });

    document.getElementById('btn-search-pago').addEventListener('click', () => {
        // Esta función sigue usando mockData, se puede conectar al backend de forma similar
        const prestamoId = document.getElementById('pago-search-prestamo').value;
        const prestamo = mockData.prestamos.find(p => p.id === prestamoId && p.estado === 'Desembolsado');
        const card = document.getElementById('payment-details-card');
        if (prestamo) {
            document.getElementById('pago-cliente-nombre').textContent = prestamo.clienteNombre;
            document.getElementById('pago-numero-cuota').textContent = '1 de 12'; // Simulación
            document.getElementById('pago-fecha-limite').textContent = '2024-09-20'; // Simulación
            document.getElementById('pago-monto-cuota').textContent = (prestamo.monto / 12 * 1.1).toFixed(2); // Simulación
            card.classList.remove('hidden');
        } else {
            alert('Préstamo no encontrado o no está desembolsado.');
            card.classList.add('hidden');
        }
    });

    function initCharts() {
        // Los gráficos siguen usando datos estáticos por ahora.
        // Se pueden conectar al backend creando nuevos endpoints en api.php
        const monthlyCtx = document.getElementById('monthlyPerformanceChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago'],
                datasets: [{
                    label: 'Prestado',
                    data: [150000, 200000, 180000, 220000, 250000, 190000, 210000, 230000],
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }, {
                    label: 'Cobrado',
                    data: [45000, 55000, 50000, 60000, 65000, 58000, 62000, 70000],
                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });

        const statusCtx = document.getElementById('loanStatusChart').getContext('2d');
        const statusLabels = ['Desembolsado', 'Pendiente', 'Aprobado', 'Rechazado', 'Saldado'];
        const statusData = [1, 1, 1, 1, 1]; // Datos de ejemplo para que el gráfico se muestre
        
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: ['#22C55E', '#F59E0B', '#3B82F6', '#EF4444', '#6B7280'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    }

    function initializeDashboard() {
        updateUserInfoUI();
        updateNavForRole();
        
        // Cargas iniciales de datos
        renderClientesTable(); // Aún con mockData
        fetchAndRenderPrestamos(); // Conectado al backend
        renderPagosRecientesTable(); // Aún con mockData
        fetchAndRenderEmpleados(); // Ahora conectado al backend
        initCharts(); // Aún con mockData
        showView('dashboard');
    }

    // ¡Punto de entrada principal de la aplicación!
    checkAuth(); 
});
</script>
</body>
</html>
