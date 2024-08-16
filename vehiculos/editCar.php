<?php
session_start();
include '../includes/db.php'; // Asegúrate de que este archivo contiene la configuración de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtén los datos del formulario
    $vehiculoID = $_POST['carro'] ?? null;
    $placasNuevas = $_POST['placasN'] ?? null;
    $kilometrajeNuevo = $_POST['kmN'] ?? null;
    $colorNuevo = $_POST['colorN'] ?? null;
    $nuevoPropietarioID = $_POST['cliente'] ?? null;

    // Validación básica
    if (empty($vehiculoID)) {
        $_SESSION['error'] = 'El vehículo es obligatorio.';
        header('Location: editCarView.php'); // Redirige de nuevo al formulario
        exit;
    }

    // Verificar que al menos uno de los campos adicionales esté presente si se ha seleccionado un vehículo
    if (empty($placasNuevas) && empty($kilometrajeNuevo) && empty($colorNuevo) && empty($nuevoPropietarioID)) {
        $_SESSION['error'] = 'Debe ingresar al menos un campo adicional (placas, kilometraje, color, o propietario).';
        header('Location: editCarView.php'); // Redirige de nuevo al formulario
        exit;
    }

    // Validar que las placas tengan exactamente 7 caracteres alfanuméricos, si se han proporcionado
    if (!empty($placasNuevas) && !preg_match('/^[A-Z0-9]{7}$/', $placasNuevas)) {
        $_SESSION['error'] = 'Las placas deben tener exactamente 7 caracteres alfanuméricos.';
        header('Location: editCarView.php'); // Redirige de nuevo al formulario
        exit;
    }

    try {
        // Conectar a la base de datos
        $conexion = new Database();
        $pdo = $conexion->conectar(); // Asumiendo que este método retorna un objeto PDO

        // Obtener el kilometraje actual y otros datos del vehículo
        $consulta_actual = "SELECT kilometraje, placas, color, clienteID FROM VEHICULOS WHERE vehiculoID = :vehiculoID";
        $stmt = $pdo->prepare($consulta_actual);
        $stmt->execute([':vehiculoID' => $vehiculoID]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resultado) {
            $_SESSION['error'] = 'Vehículo no encontrado.';
            header('Location: editCarView.php'); // Redirige de nuevo al formulario
            exit;
        }

        $kilometrajeActual = $resultado['kilometraje'];
        $placasActuales = $resultado['placas'];
        $colorActual = $resultado['color'];
        $clienteActual = $resultado['clienteID'];

        // Verificar si el nuevo kilometraje es mayor que el actual, si se ha proporcionado
        if (!empty($kilometrajeNuevo) && $kilometrajeNuevo <= $kilometrajeActual) {
            $_SESSION['error'] = 'El nuevo kilometraje debe ser mayor que el kilometraje actual.';
            header('Location: editCarView.php'); // Redirige de nuevo al formulario
            exit;
        }

        // Construir la consulta de actualización solo con los campos que han cambiado
        $updateFields = [];
        $params = [
            ':vehiculoID' => $vehiculoID
        ];

        if (!empty($placasNuevas) && $placasNuevas !== $placasActuales) {
            $updateFields[] = "placas = :placasNuevas";
            $params[':placasNuevas'] = $placasNuevas;
        }

        if (!empty($kilometrajeNuevo) && $kilometrajeNuevo !== $kilometrajeActual) {
            $updateFields[] = "kilometraje = :kilometrajeNuevo";
            $params[':kilometrajeNuevo'] = $kilometrajeNuevo;
        }

        if (!empty($colorNuevo) && $colorNuevo !== $colorActual) {
            $updateFields[] = "color = :colorNuevo";
            $params[':colorNuevo'] = $colorNuevo;
        }

        if (!empty($nuevoPropietarioID) && $nuevoPropietarioID !== $clienteActual) {
            $updateFields[] = "clienteID = :nuevoPropietarioID";
            $params[':nuevoPropietarioID'] = $nuevoPropietarioID;
        }

        // Si no hay campos para actualizar, no se hace ninguna actualización
        if (empty($updateFields)) {
            $_SESSION['error'] = 'No se han ingresado datos nuevos.';
            header('Location: editCarView.php'); // Redirige de nuevo al formulario
            exit;
        }

        // Construir la consulta de actualización
        $consulta_actualizacion = "UPDATE VEHICULOS SET " . implode(', ', $updateFields) . " WHERE vehiculoID = :vehiculoID";
        $stmt = $pdo->prepare($consulta_actualizacion);

        // Ejecutar la consulta de actualización
        $stmt->execute($params);

        // Establecer un mensaje de éxito y redirigir
        $_SESSION['bien'] = 'Vehículo actualizado con éxito.';
        header('Location: editCarView.php'); // Redirige al formulario para mostrar el mensaje
        exit;

    } catch (Exception $e) {
        // En caso de error, guarda el mensaje de error en la sesión
        $_SESSION['error'] = 'Error al actualizar el vehículo: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        header('Location: editCarView.php'); // Redirige de nuevo al formulario
        exit;
    }
} else {
    // Redirige si el formulario no se envía por POST
    header('Location: editCarView.php');
    exit;
}
?>
