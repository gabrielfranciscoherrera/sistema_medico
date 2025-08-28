<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - PrestaSys</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="w-full max-w-md p-8 space-y-8 bg-white rounded-xl shadow-lg">
        <div class="text-center">
            <i class="fas fa-landmark text-4xl text-blue-600"></i>
            <h1 class="mt-4 text-3xl font-bold text-gray-900">PrestaSys</h1>
            <p class="mt-2 text-sm text-gray-600">Inicia sesión para administrar tus préstamos</p>
        </div>

        <form id="login-form" class="space-y-6">
            <div class="relative">
                <label for="usuario" class="sr-only">Usuario</label>
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-user text-gray-400"></i>
                </span>
                <input id="usuario" name="usuario" type="text" required class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nombre de usuario">
            </div>

            <div class="relative">
                <label for="password" class="sr-only">Contraseña</label>
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-lock text-gray-400"></i>
                </span>
                <input id="password" name="password" type="password" required class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contraseña">
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Iniciar Sesión
                </button>
            </div>
        </form>
        <div id="error-message" class="hidden text-center text-sm text-red-600">
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const usuario = document.getElementById('usuario').value;
            const password = document.getElementById('password').value;
            const errorMessage = document.getElementById('error-message');

            try {
                const response = await fetch('api.php?action=login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ usuario, password })
                });

                const result = await response.json();

                if (response.ok) {
                    window.location.href = 'index.php';
                } else {
                    errorMessage.textContent = result.message || 'Error al iniciar sesión.';
                    errorMessage.classList.remove('hidden');
                }
            } catch (error) {
                errorMessage.textContent = 'Error de conexión. Inténtalo de nuevo.';
                errorMessage.classList.remove('hidden');
            }
        });
    </script>

</body>
</html>
