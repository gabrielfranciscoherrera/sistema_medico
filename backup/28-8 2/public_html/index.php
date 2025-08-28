<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Gestión de Préstamos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
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
                    <button id="notif-button" class="relative p-2 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell text-gray-600 text-xl"></i>
                        <span id="notif-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full h-4 min-w-4 px-1 flex items-center justify-center hidden">0</span>
                    </button>
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border z-40">
                        <div class="px-4 py-2 border-b font-semibold text-gray-700">Notificaciones</div>
                        <ul id="notif-list" class="max-h-72 overflow-y-auto divide-y">
                            <li class="px-4 py-3 text-gray-500 text-sm">No hay notificaciones</li>
                        </ul>
                        <div class="px-4 py-2 text-xs text-gray-400 border-t">Se actualiza cada minuto</div>
                    </div>
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
                                <p id="kpi-capital" class="text-2xl font-bold">$0</p>
                            </div>
                        </div>
                        <div class="kpi-card bg-white p-6 rounded-xl shadow-lg flex items-center">
                            <div class="bg-green-100 text-green-600 rounded-full h-12 w-12 flex items-center justify-center">
                                <i class="fas fa-wallet text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500">Total Cobrado</p>
                                <p id="kpi-cobrado" class="text-2xl font-bold">$0</p>
                            </div>
                        </div>
                         <div class="kpi-card bg-white p-6 rounded-xl shadow-lg flex items-center">
                            <div class="bg-yellow-100 text-yellow-600 rounded-full h-12 w-12 flex items-center justify-center">
                                <i class="fas fa-file-invoice-dollar text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500">Préstamos Activos</p>
                                <p id="kpi-activos" class="text-2xl font-bold">0</p>
                            </div>
                        </div>
                        <div class="kpi-card bg-white p-6 rounded-xl shadow-lg flex items-center">
                            <div class="bg-red-100 text-red-600 rounded-full h-12 w-12 flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-gray-500">Clientes en Mora</p>
                                <p id="kpi-mora" class="text-2xl font-bold">0</p>
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
                        <p class="text-gray-500">Busque un préstamo por nombre, cédula o ID para ver los detalles de la próxima cuota y registrar el pago del cliente de manera rápida y segura.</p>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-1">
                            <div class="bg-white p-6 rounded-xl shadow-lg">
                                <h4 class="font-semibold mb-4">Buscar Préstamo</h4>
                                <div class="relative">
                                    <input type="text" id="pago-search-prestamo" class="w-full pl-10 pr-4 py-2 border rounded-lg" placeholder="Buscar por cliente, cédula o ID...">
                                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <div id="pago-search-results" class="absolute bg-white border rounded-lg shadow w-full mt-1 max-h-60 overflow-y-auto hidden z-10"></div>
                                    <input type="hidden" id="pago-prestamo-id">
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
                                    <button id="btn-confirmar-pago" class="w-full mt-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">Confirmar Pago</button>
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
                        <div class="flex justify-end mb-4">
                            <button id="btn-new-employee" class="btn btn-primary text-sm"><i class="fas fa-user-plus mr-2"></i>Nuevo Empleado</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="p-3">Nombre Completo</th>
                                        <th class="p-3">Usuario</th>
                                        <th class="p-3">Rol</th>
                                        <th class="p-3">Descripción del Rol</th>
                                        <th class="p-3 text-center">Acciones</th>
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
                <form id="form-new-client" class="space-y-4">
                    <div>
                        <label for="nombre_completo" class="block font-medium mb-1">Nombre Completo</label>
                        <input type="text" id="nombre_completo" class="w-full p-2 border rounded-lg" required>
                    </div>
                    <div>
                        <label for="cedula" class="block font-medium mb-1">Cédula / ID</label>
                        <input type="text" id="cedula" class="w-full p-2 border rounded-lg" placeholder="000-0000000-0" inputmode="numeric" maxlength="13" autocomplete="off" required>
                    </div>
                    <div>
                        <label for="telefono" class="block font-medium mb-1">Teléfono</label>
                        <input type="tel" id="telefono" class="w-full p-2 border rounded-lg" inputmode="numeric" placeholder="(809) 555-1234">
                    </div>
                     <div>
                        <label for="direccion" class="block font-medium mb-1">Dirección</label>
                        <textarea id="direccion" rows="3" class="w-full p-2 border rounded-lg"></textarea>
                    </div>
                </form>
            </div>
            <div class="p-4 bg-gray-50 flex justify-end space-x-3 rounded-b-xl">
                <button type="button" class="btn-close-modal px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancelar</button>
                <button type="submit" form="form-new-client" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Guardar Cliente</button>
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

    <!-- Modal Editar Empleado -->
    <div id="modal-edit-employee" class="modal-backdrop">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg m-4 modal-content">
            <div class="p-6 border-b">
                <h3 class="text-xl font-semibold">Editar Empleado</h3>
            </div>
            <div class="p-6">
                <form id="form-edit-employee" class="space-y-4">
                    <input type="hidden" id="edit_emp_id">
                    <div>
                        <label class="block font-medium mb-1">Nombre</label>
                        <input type="text" id="edit_emp_nombre" class="w-full p-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Usuario</label>
                        <input type="text" id="edit_emp_usuario" class="w-full p-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Rol (ID)</label>
                        <select id="edit_emp_id_rol" class="w-full p-2 border rounded-lg">
                            <option value="1">Admin</option>
                            <option value="2">Gerente</option>
                            <option value="3">Servicio al Cliente</option>
                            <option value="4">Cajero</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Nueva Contraseña (opcional)</label>
                        <input type="password" id="edit_emp_password" class="w-full p-2 border rounded-lg" placeholder="Dejar en blanco para no cambiar">
                    </div>
                </form>
            </div>
            <div class="p-4 bg-gray-50 flex justify-end space-x-3 rounded-b-xl">
                <button type="button" class="btn-close-modal btn btn-secondary">Cancelar</button>
                <button type="submit" form="form-edit-employee" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Empleado -->
    <div id="modal-new-employee" class="modal-backdrop">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg m-4 modal-content">
            <div class="p-6 border-b">
                <h3 class="text-xl font-semibold">Nuevo Empleado</h3>
            </div>
            <div class="p-6">
                <form id="form-new-employee" class="space-y-4">
                    <div>
                        <label class="block font-medium mb-1">Nombre</label>
                        <input type="text" id="new_emp_nombre" class="w-full p-2 border rounded-lg" required>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Usuario</label>
                        <input type="text" id="new_emp_usuario" class="w-full p-2 border rounded-lg" required>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Rol (ID)</label>
                        <select id="new_emp_id_rol" class="w-full p-2 border rounded-lg" required>
                            <option value="1">Admin</option>
                            <option value="2">Gerente</option>
                            <option value="3">Servicio al Cliente</option>
                            <option value="4">Cajero</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Contraseña</label>
                        <input type="password" id="new_emp_password" class="w-full p-2 border rounded-lg" minlength="6" required>
                    </div>
                </form>
            </div>
            <div class="p-4 bg-gray-50 flex justify-end space-x-3 rounded-b-xl">
                <button type="button" class="btn-close-modal btn btn-secondary">Cancelar</button>
                <button type="submit" form="form-new-employee" class="btn btn-primary">Crear</button>
            </div>
        </div>
    </div>
    
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentUser = null;
    
    // La URL base de tu API. Ajusta la ruta si es necesario.
    const API_URL = 'api.php';
    const AUTH_URL = 'auth.php';

    // --- NOTIFICACIONES (campanita) ---
    const notifButton = document.getElementById('notif-button');
    const notifDropdown = document.getElementById('notif-dropdown');
    const notifBadge = document.getElementById('notif-badge');
    const notifList = document.getElementById('notif-list');

    function renderNotifications(items) {
        if (!notifList) return;
        notifList.innerHTML = '';
        if (!items || items.length === 0) {
            notifList.innerHTML = '<li class="px-4 py-3 text-gray-500 text-sm">No hay notificaciones</li>';
        } else {
            items.forEach(it => {
                const color = it.type === 'warning' ? 'text-yellow-600' : it.type === 'danger' ? 'text-red-600' : 'text-blue-600';
                const icon = it.type === 'warning' ? 'fa-triangle-exclamation' : it.type === 'danger' ? 'fa-circle-exclamation' : 'fa-info-circle';
                const li = document.createElement('li');
                li.className = 'px-4 py-3 hover:bg-gray-50 text-sm flex items-start gap-3';
                li.innerHTML = `<i class="fas ${icon} ${color} mt-0.5"></i><div class="text-gray-700">${it.text}</div>`;
                notifList.appendChild(li);
            });
        }
        const count = items ? items.length : 0;
        if (notifBadge) {
            if (count > 0) {
                notifBadge.textContent = String(count);
                notifBadge.classList.remove('hidden');
            } else {
                notifBadge.classList.add('hidden');
            }
        }
    }

    async function loadNotifications() {
        const items = [];
        // 1) Resumen del dashboard (clientes en mora, etc.)
        try {
            const res = await fetch(`${API_URL}?action=get_dashboard_summary`);
            if (res.ok) {
                const s = await res.json();
                if (s && typeof s.clientes_en_mora === 'number' && s.clientes_en_mora > 0) {
                    items.push({ type: 'warning', text: `Clientes en mora: ${s.clientes_en_mora}` });
                }
                if (s && typeof s.prestamos_activos === 'number' && s.prestamos_activos > 0) {
                    items.push({ type: 'info', text: `Préstamos activos: ${s.prestamos_activos}` });
                }
            }
        } catch (_) { /* ignorar */ }
        // 2) Préstamos pendientes
        try {
            const res2 = await fetch(`${API_URL}?action=list_prestamos&filter=`);
            if (res2.ok) {
                const all = await res2.json();
                if (Array.isArray(all)) {
                    const pendientes = all.filter(p => (p.estado || '').toLowerCase() === 'pendiente');
                    if (pendientes.length > 0) {
                        items.push({ type: 'info', text: `Préstamos pendientes de aprobación: ${pendientes.length}` });
                    }
                }
            }
        } catch (_) { /* ignorar */ }
        renderNotifications(items);
    }

    if (notifButton && notifDropdown) {
        notifButton.addEventListener('click', (e) => {
            e.stopPropagation();
            const expanded = notifButton.getAttribute('aria-expanded') === 'true';
            notifButton.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            notifDropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (!notifDropdown.classList.contains('hidden') && !notifDropdown.contains(e.target) && !notifButton.contains(e.target)) {
                notifDropdown.classList.add('hidden');
                notifButton.setAttribute('aria-expanded', 'false');
            }
        });
    }

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
            const response = await fetch(`${AUTH_URL}?action=get_session`);
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
            // Cajero ahora puede ver préstamos para desembolsar
            'Cajero': ['dashboard', 'prestamos', 'pagos']
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
            await fetch(`${AUTH_URL}?action=logout`, { method: 'POST' });
            window.location.href = 'login.php';
        } catch (error) {
            console.error('Error al cerrar sesión:', error);
            alert('No se pudo cerrar la sesión. Intente de nuevo.');
        }
    });

    // --- FIN DE LÓGICA DE AUTENTICACIÓN ---


    function showView(viewId) {
        // Validar que la vista existe y está permitida por el rol
        const viewEl = document.getElementById(`${viewId}-view`);
        const activeLink = document.querySelector(`.nav-link[data-view="${viewId}"]`);
        const isAllowed = !!activeLink && activeLink.style.display !== 'none';

        if (!viewEl || !isAllowed) {
            // Fallback seguro al dashboard si la vista no existe o no está permitida
            viewId = 'dashboard';
        }

        // Ocultar todas y mostrar la seleccionada
        views.forEach(view => view.classList.add('hidden'));
        document.getElementById(`${viewId}-view`).classList.remove('hidden');

        // Marcar link activo y actualizar título
        navLinks.forEach(link => link.classList.remove('active'));
        const resolvedLink = document.querySelector(`.nav-link[data-view="${viewId}"]`);
        if (resolvedLink) {
            resolvedLink.classList.add('active');
            viewTitle.textContent = resolvedLink.textContent.trim();
        }

        // Persistir vista actual y reflejar en la URL
        try { localStorage.setItem('activeView', viewId); } catch (_) {}
        if (location.hash !== `#${viewId}`) {
            location.hash = viewId;
        }
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

    // Navegación por hash (permite refrescar y usar atrás/adelante)
    window.addEventListener('hashchange', () => {
        const target = (location.hash || '').replace('#', '');
        if (target) {
            showView(target);
        }
    });
    
    document.getElementById('menu-toggle').addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
    });

    async function fetchAndRenderClientes(filter = '') {
        const tableBody = document.getElementById('clientes-table-body');
        tableBody.innerHTML = '';
        try {
            const resp = await fetch(`${API_URL}?action=search_clientes&term=${encodeURIComponent(filter)}`);
            const clientes = await resp.json();
            if (!Array.isArray(clientes) || clientes.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-8 text-gray-500">No se encontraron clientes.</td></tr>';
                return;
            }
            clientes.forEach(c => {
                const nombre = c.nombre_completo || c.nombre || '';
                const cedula = c.cedula || '';
                const telefonoRaw = c.telefono || '';
                const telefono = telefonoRaw ? formatPhoneRD(telefonoRaw) : '—';
                const fecha = c.fecha || c.fecha_registro || c.created_at || '—';
                const row = `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">${nombre}</td>
                        <td class="p-3">${cedula}</td>
                        <td class="p-3">${telefono}</td>
                        <td class="p-3">${fecha}</td>
                        <td class="p-3">
                            <button class="text-blue-500 hover:text-blue-700 mr-2"><i class="fas fa-eye"></i></button>
                            <button class="text-yellow-500 hover:text-yellow-700"><i class="fas fa-edit"></i></button>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        } catch (error) {
            console.error('Error al cargar clientes:', error);
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-8 text-red-500">Error al cargar los datos.</td></tr>';
        }
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
        const role = (currentUser && currentUser.rol) || '';
        // Para Cajero: solo permitir Desembolsar cuando esté Aprobado
        if (role === 'Cajero') {
            if (prestamo.estado === 'Aprobado') {
                return `
                    <button data-id="${prestamo.id}" data-action="Desembolsado" class="action-btn text-xs bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-2 rounded">Desembolsar</button>
                `;
            }
            return `<button class=\"text-blue-500 hover:text-blue-700\"><i class=\"fas fa-file-alt\"></i> Ver</button>`;
        }

        // Resto de roles: comportamiento actual
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
            const cliente = p.cliente_nombre || p.clienteNombre || '';
            const monto = parseFloat(p.monto_aprobado ?? p.monto ?? 0);
            const fecha = p.fecha_solicitud || p.fecha || '';
            const row = `
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-mono">${p.id}</td>
                    <td class="p-3">${cliente}</td>
                    <td class="p-3">$${monto.toLocaleString()}</td>
                    <td class="p-3">${fecha}</td>
                    <td class="p-3">${getStatusBadge(p.estado || '')}</td>
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
    
    async function renderPagosRecientesTable() {
        const tableBody = document.getElementById('pagos-recientes-table-body');
        tableBody.innerHTML = '';
        try {
            const response = await fetch(`${API_URL}?action=list_pagos_recientes&limit=10`);
            const pagos = await response.json();
            if (!response.ok) {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-8 text-red-500">Error al cargar pagos.</td></tr>';
                return;
            }
            if (!Array.isArray(pagos) || pagos.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-8 text-gray-500">No hay pagos recientes.</td></tr>';
                return;
            }
            pagos.forEach(p => {
                const row = `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-mono">${p.pago_id}</td>
                        <td class="p-3 font-mono">${p.prestamo_id}</td>
                        <td class="p-3">$${Number(p.monto_pagado).toLocaleString()}</td>
                        <td class="p-3">${new Date(p.fecha_pago).toLocaleString()}</td>
                        <td class="p-3">${p.cajero || '—'}</td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        } catch (error) {
            console.error('Error al cargar pagos:', error);
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-8 text-red-500">Error al cargar pagos.</td></tr>';
        }
    }
    
    async function fetchAndRenderEmpleados() {
        try {
            const response = await fetch(`${API_URL}?action=list_empleados`);
            const empleados = await response.json();
            const tableBody = document.getElementById('empleados-table-body');
            tableBody.innerHTML = '';

            if (!response.ok) {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-8 text-red-500">Error al cargar empleados.</td></tr>';
                return;
            }

            if (!Array.isArray(empleados) || empleados.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-8 text-gray-500">No se encontraron empleados.</td></tr>';
                return;
            }

            const roleMap = {1:'Admin', 2:'Gerente', 3:'Servicio al Cliente', 4:'Cajero'};
            empleados.forEach(e => {
                const rolNombre = e.rol || roleMap[e.id_rol] || 'Empleado';
                const actionBtns = `
                    <div class="flex items-center justify-center space-x-2">
                        <button class="btn btn-secondary text-xs edit-emp" data-id="${e.id}"><i class="fas fa-pen"></i> Editar</button>
                        <button class="btn btn-danger text-xs del-emp" data-id="${e.id}"><i class="fas fa-trash"></i> Eliminar</button>
                    </div>`;
                const row = `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-semibold">${e.nombre}</td>
                        <td class="p-3 font-mono">${e.usuario}</td>
                        <td class="p-3 font-medium">${rolNombre}</td>
                        <td class="p-3 text-sm text-gray-600">${e.descripcion || 'N/A'}</td>
                        <td class="p-3">${actionBtns}</td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        } catch (error) {
            console.error('Error al cargar empleados:', error);
            const tableBody = document.getElementById('empleados-table-body');
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center p-8 text-red-500">Error de conexión al cargar empleados.</td></tr>';
        }
    }

    // Delegación para editar/eliminar empleados
    document.getElementById('empleados-table-body').addEventListener('click', async (e) => {
        const editBtn = e.target.closest('.edit-emp');
        const delBtn = e.target.closest('.del-emp');
        if (editBtn) {
            const id = editBtn.dataset.id;
            openEditEmployeeModal(id);
            return;
        }
        if (delBtn) {
            const id = delBtn.dataset.id;
            if (!confirm('¿Está seguro de eliminar este empleado?')) return;
            try {
                const response = await fetch(`${API_URL}?action=delete_empleado&id=${id}`, { method: 'DELETE' });
                const res = await response.json();
                if (response.ok) {
                    fetchAndRenderEmpleados();
                } else {
                    alert(res.message || 'No se pudo eliminar.');
                }
            } catch (err) {
                alert('Error de conexión al eliminar.');
            }
        }
    });

    async function openEditEmployeeModal(id) {
        try {
            const response = await fetch(`${API_URL}?action=get_empleado&id=${id}`);
            const emp = await response.json();
            if (!response.ok) {
                if (response.status === 404) {
                    alert(emp.message || 'Empleado no encontrado.');
                    try { await fetchAndRenderEmpleados(); } catch (_) {}
                } else {
                    alert(emp.message || 'No se pudo cargar el empleado');
                }
                return;
            }
            // Prefill form
            document.getElementById('edit_emp_id').value = emp.id;
            document.getElementById('edit_emp_nombre').value = emp.nombre || '';
            document.getElementById('edit_emp_usuario').value = emp.usuario || '';
            document.getElementById('edit_emp_id_rol').value = emp.id_rol || '';
            openModal('modal-edit-employee');
        } catch (err) {
            alert('Error de conexión al cargar empleado');
        }
    }

    document.getElementById('cliente-search').addEventListener('input', (e) => fetchAndRenderClientes(e.target.value));
    document.getElementById('prestamo-search').addEventListener('input', (e) => fetchAndRenderPrestamos(e.target.value));

    const modals = document.querySelectorAll('.modal-backdrop');
    const closeButtons = document.querySelectorAll('.btn-close-modal');

    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
    }

    function closeModal() {
        modals.forEach(modal => modal.style.display = 'none');
    }

    // Formateo automático de cédula (RD: 000-0000000-0)
    const cedulaInput = document.getElementById('cedula');
    if (cedulaInput) {
        cedulaInput.addEventListener('input', function () {
            const digits = this.value.replace(/\D/g, '').slice(0, 11);
            let formatted = '';
            if (digits.length <= 3) {
                formatted = digits;
            } else if (digits.length <= 10) {
                formatted = `${digits.slice(0,3)}-${digits.slice(3)}`;
            } else {
                formatted = `${digits.slice(0,3)}-${digits.slice(3,10)}-${digits.slice(10)}`;
            }
            this.value = formatted;
        });
    }

    // Teléfono RD: utilidades de formato/validación y máscara
    function onlyDigits(v) { return (v || '').toString().replace(/\D/g, ''); }
    function formatPhoneRD(v) {
        let d = onlyDigits(v);
        if (d.startsWith('1') && d.length === 11) d = d.slice(1);
        d = d.slice(0, 10);
        if (!d) return '';
        if (d.length <= 3) return `(${d}`;
        if (d.length <= 6) return `(${d.slice(0,3)}) ${d.slice(3)}`;
        return `(${d.slice(0,3)}) ${d.slice(3,6)}-${d.slice(6)}`;
    }
    function isValidPhoneRD(v) {
        let d = onlyDigits(v);
        if (d.startsWith('1') && d.length === 11) d = d.slice(1);
        if (d.length !== 10) return false;
        const area = d.slice(0,3);
        return ['809','829','849'].includes(area);
    }
    function normalizePhoneRD(v) {
        let d = onlyDigits(v);
        if (d.startsWith('1') && d.length === 11) d = d.slice(1);
        d = d.slice(0,10);
        if (d.length !== 10) return d;
        return `${d.slice(0,3)}-${d.slice(3,6)}-${d.slice(6)}`;
    }
    document.querySelectorAll('input[type="tel"]').forEach(inp => {
        inp.addEventListener('input', function() {
            const caret = this.selectionStart;
            this.value = formatPhoneRD(this.value);
            try { this.setSelectionRange(caret, caret); } catch (_) {}
        });
        inp.addEventListener('blur', function() {
            const val = this.value.trim();
            if (val && !isValidPhoneRD(val)) {
                alert('Ingrese un teléfono válido de RD (Ej.: (809) 555-1234).');
                this.focus();
            }
        });
    });

        // Manejo del formulario de nuevo cliente (validación + envío)
        document.querySelector('#modal-new-client form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const nombre = document.getElementById('nombre_completo').value.trim();
            const cedula = document.getElementById('cedula').value.trim();
            const telefono = document.getElementById('telefono').value.trim();
            const direccion = document.getElementById('direccion').value.trim();

            if (!nombre || nombre.length < 3) { alert('Ingrese un nombre válido (mínimo 3 caracteres).'); return; }
            const cedulaDigits = cedula.replace(/\D/g, '');
            const cedulaOk = /^\d{3}-\d{7}-\d$/.test(cedula) || cedulaDigits.length === 11;
            if (!cedulaOk) { alert('Ingrese una cédula válida (formato 000-0000000-0).'); return; }
            if (telefono && !isValidPhoneRD(telefono)) { alert('Ingrese un teléfono válido de RD (áreas 809, 829 o 849).'); return; }

            const clienteData = {
                nombre_completo: nombre,
                cedula: cedula,
                telefono: telefono ? normalizePhoneRD(telefono) : '',
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
                    fetchAndRenderClientes();
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

    // Guardar cambios de empleado (validación + envío)
    document.getElementById('form-edit-employee').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('edit_emp_id').value;
        const payload = {
            nombre: document.getElementById('edit_emp_nombre').value.trim(),
            usuario: document.getElementById('edit_emp_usuario').value.trim(),
            id_rol: parseInt(document.getElementById('edit_emp_id_rol').value, 10),
        };
        const pwd = document.getElementById('edit_emp_password').value;
        // Validaciones
        if (!payload.nombre) { alert('El nombre es obligatorio.'); return; }
        if (!payload.usuario) { alert('El usuario es obligatorio.'); return; }
        if (!payload.id_rol || ![1,2,3,4].includes(payload.id_rol)) { alert('Seleccione un rol válido.'); return; }
        if (pwd && pwd.length < 6) { alert('La nueva contraseña debe tener al menos 6 caracteres.'); return; }
        if (pwd) payload.password = pwd;
        try {
            const response = await fetch(`${API_URL}?action=update_empleado&id=${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const res = await response.json();
            if (response.ok) {
                closeModal();
                fetchAndRenderEmpleados();
            } else {
                alert(res.message || 'No se pudo guardar.');
            }
        } catch (err) {
            alert('Error de conexión al guardar.');
        }
    });

    // Abrir modal de nuevo empleado
    document.getElementById('btn-new-employee').addEventListener('click', () => {
        const form = document.getElementById('form-new-employee');
        form.reset();
        openModal('modal-new-employee');
    });

    // Crear empleado (validación + envío)
    document.getElementById('form-new-employee').addEventListener('submit', async (e) => {
        e.preventDefault();
        const payload = {
            nombre: document.getElementById('new_emp_nombre').value.trim(),
            usuario: document.getElementById('new_emp_usuario').value.trim(),
            id_rol: parseInt(document.getElementById('new_emp_id_rol').value, 10),
            password: document.getElementById('new_emp_password').value
        };
        if (!payload.nombre || payload.nombre.length < 3) { alert('Nombre inválido (mín. 3 caracteres).'); return; }
        if (!payload.usuario || payload.usuario.length < 3) { alert('Usuario inválido (mín. 3 caracteres).'); return; }
        if (!payload.id_rol || ![1,2,3,4].includes(payload.id_rol)) { alert('Seleccione un rol válido.'); return; }
        if (!payload.password || payload.password.length < 6) { alert('La contraseña debe tener al menos 6 caracteres.'); return; }
        try {
            const response = await fetch(`${API_URL}?action=create_empleado`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const res = await response.json();
            if (response.ok) {
                closeModal();
                fetchAndRenderEmpleados();
            } else {
                alert(res.message || 'No se pudo crear el empleado.');
            }
        } catch (err) {
            alert('Error de conexión al crear el empleado.');
        }
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

    // Permitir seleccionar con Enter el resultado más similar (primer elemento)
    loanClienteSearch.addEventListener('keydown', async (e) => {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        e.stopPropagation();

        const term = loanClienteSearch.value.trim();
        if (term.length < 2) return;

        // Si ya hay resultados visibles, tomar el primero
        const firstItem = searchResultsContainer.querySelector('div');
        if (firstItem) {
            loanClienteSearch.value = firstItem.dataset.name || firstItem.textContent || '';
            loanClienteId.value = firstItem.dataset.id || '';
            searchResultsContainer.classList.add('hidden');
            return;
        }

        // Si aún no hay resultados renderizados (p. ej. se presionó Enter muy rápido), buscar y seleccionar el primero
        try {
            const response = await fetch(`${API_URL}?action=search_clientes&term=${encodeURIComponent(term)}`);
            const results = await response.json();
            if (Array.isArray(results) && results.length > 0) {
                const top = results[0];
                loanClienteSearch.value = (top.nombre_completo || '')
                    + (top.cedula ? '' : '');
                loanClienteId.value = top.id || '';
                searchResultsContainer.classList.add('hidden');
            }
        } catch (err) {
            console.error('Error al buscar clientes con Enter:', err);
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
        
        if (!clienteId) { alert('Por favor, seleccione un cliente válido de la búsqueda.'); return; }

        // Validaciones adicionales de préstamo
        const amount = parseFloat(document.getElementById('loan_amount').value);
        const annualRate = parseFloat(document.getElementById('loan_interest_rate').value);
        const term = parseInt(document.getElementById('loan_term').value, 10);
        const freq = document.getElementById('loan_frequency').value;
        if (!(amount > 0)) { alert('Ingrese un monto válido (> 0).'); return; }
        if (!(annualRate > 0)) { alert('Ingrese una tasa de interés válida (> 0).'); return; }
        if (!(term > 0)) { alert('Ingrese un plazo válido (> 0).'); return; }
        if (!['mensual','semanal','diario'].includes(freq)) { alert('Seleccione una frecuencia válida.'); return; }

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

    // --- Búsqueda de Préstamos por nombre, cédula o ID en la sección Pagos ---
    const pagoPrestamoInput = document.getElementById('pago-search-prestamo');
    const pagoSearchResults = document.getElementById('pago-search-results');
    const pagoPrestamoId = document.getElementById('pago-prestamo-id');
    let selectedPagoPrestamo = null;

    pagoPrestamoInput.addEventListener('input', async () => {
        const term = pagoPrestamoInput.value.trim();
        pagoSearchResults.innerHTML = '';
        selectedPagoPrestamo = null;
        pagoPrestamoId.value = '';

        if (term.length < 2) {
            pagoSearchResults.classList.add('hidden');
            return;
        }

        try {
            const resp = await fetch(`${API_URL}?action=list_prestamos&filter=${encodeURIComponent(term)}`);
            const dataText = await resp.text();
            let data = [];
            try { data = JSON.parse(dataText); } catch (_) { data = []; }

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(p => {
                    const item = document.createElement('div');
                    item.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                    const nombre = p.cliente_nombre || '';
                    const cedula = p.cliente_cedula || '';
                    item.textContent = `#${p.id} — ${nombre} — ${cedula}`;
                    item.addEventListener('click', () => {
                        selectedPagoPrestamo = p;
                        pagoPrestamoId.value = p.id;
                        pagoPrestamoInput.value = `#${p.id} - ${nombre} (${cedula})`;
                        pagoSearchResults.classList.add('hidden');
                        // Cargar información real de la próxima cuota
                        loadPagoInfo(p.id);
                    });
                    pagoSearchResults.appendChild(item);
                });
                pagoSearchResults.classList.remove('hidden');
            } else {
                pagoSearchResults.classList.add('hidden');
            }
        } catch (e) {
            console.error('Error buscando préstamos:', e);
            pagoSearchResults.classList.add('hidden');
        }
    });

    document.addEventListener('click', (ev) => {
        if (!pagoPrestamoInput.contains(ev.target) && !pagoSearchResults.contains(ev.target)) {
            pagoSearchResults.classList.add('hidden');
        }
    });

    document.getElementById('btn-search-pago').addEventListener('click', async () => {
        const card = document.getElementById('payment-details-card');
        if (selectedPagoPrestamo) {
            // Ya hay un préstamo seleccionado desde el autocomplete
            loadPagoInfo(selectedPagoPrestamo.id);
            return;
        }
        // Si no hay selección previa, intentar buscar por el texto ingresado
        const term = pagoPrestamoInput.value.trim();
        if (!term) { alert('Ingrese un término de búsqueda.'); return; }
        try {
            const resp = await fetch(`${API_URL}?action=list_prestamos&filter=${encodeURIComponent(term)}`);
            const data = await resp.json();
            if (Array.isArray(data) && data.length === 1) {
                const p = data[0];
                selectedPagoPrestamo = p;
                pagoPrestamoId.value = p.id;
                pagoPrestamoInput.value = `#${p.id} - ${p.cliente_nombre || ''} (${p.cliente_cedula || ''})`;
                loadPagoInfo(p.id);
            } else if (Array.isArray(data) && data.length > 1) {
                // Mostrar las opciones para que el usuario seleccione
                pagoSearchResults.innerHTML = '';
                data.forEach(p => {
                    const item = document.createElement('div');
                    item.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                    item.textContent = `#${p.id} — ${p.cliente_nombre || ''} — ${p.cliente_cedula || ''}`;
                    item.addEventListener('click', () => {
                        selectedPagoPrestamo = p;
                        pagoPrestamoId.value = p.id;
                        pagoPrestamoInput.value = `#${p.id} - ${p.cliente_nombre || ''} (${p.cliente_cedula || ''})`;
                        loadPagoInfo(p.id);
                        pagoSearchResults.classList.add('hidden');
                    });
                    pagoSearchResults.appendChild(item);
                });
                pagoSearchResults.classList.remove('hidden');
            } else {
                alert('No se encontraron préstamos.');
                card.classList.add('hidden');
            }
        } catch (e) {
            alert('Error de conexión en la búsqueda.');
        }
    });

    async function loadPagoInfo(prestamoId) {
        const card = document.getElementById('payment-details-card');
        try {
            const resp = await fetch(`${API_URL}?action=get_prestamo_pago_info&id=${encodeURIComponent(prestamoId)}`);
            const data = await resp.json();
            if (!resp.ok) {
                alert(data.message || 'No se pudo obtener la información de pago.');
                card.classList.add('hidden');
                return;
            }
            if (data.sin_cuotas_pendientes) {
                alert('El préstamo no tiene cuotas pendientes.');
                card.classList.add('hidden');
                return;
            }
            document.getElementById('pago-cliente-nombre').textContent = data.cliente_nombre || '';
            document.getElementById('pago-numero-cuota').textContent = data.proxima_cuota.numero_cuota;
            document.getElementById('pago-fecha-limite').textContent = data.proxima_cuota.fecha_pago;
            document.getElementById('pago-monto-cuota').textContent = Number(data.proxima_cuota.monto_cuota).toFixed(2);
            card.dataset.prestamoId = data.prestamo_id;
            card.dataset.amortizacionId = data.proxima_cuota.id_amortizacion;
            card.classList.remove('hidden');
        } catch (err) {
            console.error('Error al cargar info de pago:', err);
            alert('Error de conexión al cargar información de pago.');
            card.classList.add('hidden');
        }
    }

    // Confirmación de pago
    document.getElementById('btn-confirmar-pago').addEventListener('click', async () => {
        const card = document.getElementById('payment-details-card');
        const amortizacionId = card.dataset.amortizacionId;
        const monto = parseFloat(document.getElementById('monto_pagado').value || '0');
        if (!amortizacionId) { alert('Primero busque un préstamo válido.'); return; }
        if (isNaN(monto) || monto <= 0) { alert('Ingrese un monto válido.'); return; }
        try {
            const payload = { id_amortizacion: amortizacionId, monto_pagado: monto, metodo_pago: 'Efectivo' };
            const resp = await fetch(`${API_URL}?action=create_pago`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await resp.json();
            if (resp.ok) {
                alert('Pago registrado exitosamente.');
                document.getElementById('monto_pagado').value = '';
                card.classList.add('hidden');
                renderPagosRecientesTable();
            } else {
                alert(data.message || 'No se pudo registrar el pago.');
            }
        } catch (err) {
            console.error('Error al registrar pago:', err);
            alert('Error de conexión al registrar el pago.');
        }
    });

    async function fetchDashboardSummary() {
        try {
            const res = await fetch(`${API_URL}?action=get_dashboard_summary`);
            if (!res.ok) return;
            const data = await res.json();
            const fmt = (n) => new Intl.NumberFormat('es-DO', { maximumFractionDigits: 2 }).format(n || 0);
            document.getElementById('kpi-capital').textContent = `$${fmt(data.total_prestado)}`;
            document.getElementById('kpi-cobrado').textContent = `$${fmt(data.total_cobrado)}`;
            document.getElementById('kpi-activos').textContent = `${fmt(data.prestamos_activos)}`;
            document.getElementById('kpi-mora').textContent = `${fmt(data.clientes_en_mora)}`;
        } catch (e) { /* noop */ }
    }

    async function initCharts() {
        try {
            const resp = await fetch(`${API_URL}?action=get_dashboard_charts`);
            if (!resp.ok) throw new Error('No se pudo obtener datos de gráficas');
            const data = await resp.json();

            const monthly = data.monthly || {};
            const labels = monthly.labels_short || monthly.labels || [];
            const prestado = Array.isArray(monthly.prestado) ? monthly.prestado : [];
            const cobrado = Array.isArray(monthly.cobrado) ? monthly.cobrado : [];

            const monthlyCtx = document.getElementById('monthlyPerformanceChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Prestado',
                        data: prestado,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Cobrado',
                        data: cobrado,
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

            const dist = data.status_distribution || {};
            const statusLabels = Array.isArray(dist.labels) && dist.labels.length ? dist.labels : ['Desembolsado', 'Pendiente', 'Aprobado', 'Rechazado', 'Saldado'];
            const statusCounts = Array.isArray(dist.counts) && dist.counts.length ? dist.counts : new Array(statusLabels.length).fill(0);

            const statusCtx = document.getElementById('loanStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusCounts,
                        backgroundColor: ['#22C55E', '#F59E0B', '#3B82F6', '#EF4444', '#6B7280'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
        } catch (e) {
            // Fallback simple si hay error: no bloquear el resto del dashboard
            try {
                const monthlyCtx = document.getElementById('monthlyPerformanceChart').getContext('2d');
                new Chart(monthlyCtx, {
                    type: 'bar',
                    data: { labels: [], datasets: [] },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            } catch (_) {}
            try {
                const statusCtx = document.getElementById('loanStatusChart').getContext('2d');
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: { labels: [], datasets: [{ data: [] }] },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            } catch (_) {}
        }
    }

    function initializeDashboard() {
        updateUserInfoUI();
        updateNavForRole();
        // Notificaciones
        try { loadNotifications(); } catch (_) {}
        try { setInterval(loadNotifications, 60000); } catch (_) {}
        
        // Cargas iniciales de datos
        fetchAndRenderClientes(); // Conectado al backend
        fetchAndRenderPrestamos(); // Conectado al backend
        renderPagosRecientesTable(); // Aún con mockData
        fetchAndRenderEmpleados(); // Ahora conectado al backend
        fetchDashboardSummary();
        initCharts(); // Conectado al backend
        // Elegir la vista inicial respetando permisos y preferencia del usuario
        const allowed = Array.from(navLinks)
            .filter(l => l.style.display !== 'none')
            .map(l => l.dataset.view);
        const fromHash = (location.hash || '').replace('#', '');
        let preferred = fromHash || (typeof localStorage !== 'undefined' ? localStorage.getItem('activeView') : '') || 'dashboard';
        if (!allowed.includes(preferred)) preferred = 'dashboard';
        showView(preferred);
    }

    // ¡Punto de entrada principal de la aplicación!
    checkAuth(); 
});
</script>
</body>
</html>
