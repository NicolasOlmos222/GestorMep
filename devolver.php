<?php
// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'c2660463_1');
//$conn = new mysqli('localhost', 'c2660463_1', '44guwedeWI', 'c2660463_1');

if (isset($_POST['devolver'])) {
    $id_computadora = $_POST['devolver'];

    // Obtener la reserva activa
    $reserva = $conn->query("SELECT id_reserva FROM reservas WHERE id_computadora='$id_computadora' AND estado_reserva='activa'")->fetch_assoc();

    // Actualizar el estado de la computadora
    $sql = "UPDATE computadoras SET estado='disponible' WHERE id_computadora='$id_computadora'";
    $conn->query($sql);
    
    // Finalizar la reserva en lugar de eliminarla
    $sql = "UPDATE reservas SET estado_reserva='finalizada' WHERE id_reserva='{$reserva['id_reserva']}'";
    $conn->query($sql);

    // Registrar movimiento
    $sql = "INSERT INTO movimientos (id_computadora, id_reserva, fecha_movimiento, tipo_movimiento) 
            VALUES ('$id_computadora', '{$reserva['id_reserva']}', NOW(), 'devolucion')";
    $conn->query($sql);

    echo "<p>Computadora devuelta con éxito.</p>";
}

if (isset($_POST['devolver_todas'])) {
    // Obtener todas las reservas activas
    $reservas = $conn->query("SELECT id_reserva, id_computadora FROM reservas WHERE estado_reserva='activa'");
    
    // Procesar cada computadora reservada
    while ($reserva = $reservas->fetch_assoc()) {
        $id_computadora = $reserva['id_computadora'];

        // Actualizar el estado de la computadora
        $conn->query("UPDATE computadoras SET estado='disponible' WHERE id_computadora='$id_computadora'");

        // Finalizar la reserva
        $conn->query("UPDATE reservas SET estado_reserva='finalizada' WHERE id_reserva='{$reserva['id_reserva']}'");

        // Registrar movimiento
        $conn->query("INSERT INTO movimientos (id_computadora, id_reserva, fecha_movimiento, tipo_movimiento) 
                      VALUES ('$id_computadora', '{$reserva['id_reserva']}', NOW(), 'devolucion')");
    }

    echo "<p>Todas las computadoras han sido devueltas con éxito.</p>";
}

$computadoras = $conn->query("SELECT DISTINCT c.id_computadora, c.rack, c.numero, r.curso, d.nombre_docente 
                              FROM computadoras c
                              JOIN reservas r ON c.id_computadora = r.id_computadora
                              JOIN docentes d ON r.id_docente = d.id_docente
                              WHERE c.estado = 'en uso' AND r.estado_reserva = 'activa'
                              ORDER BY r.curso");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devolución de Computadoras</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #007BFF;
            color: #fff;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        table tr:hover {
            background-color: #ddd;
        }
        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 15px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #218838;
        }
        .navigation-buttons {
            margin-top: 20px;
        }
        .navigation-buttons a {
            text-decoration: none;
        }
        .navigation-buttons button {
            background-color: #6c757d;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            margin: 5px;
            transition: background-color 0.3s ease;
        }
        .navigation-buttons button:hover {
            background-color: #5a6268;
        }
        input{
            height: 30px;
        }
    </style>
    <script>
        function confirmarDevolucionTodas() {
            return confirm("¿Está seguro de que desea devolver todas las computadoras?");
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="navigation-buttons">
            <a href="escaner.php"><button>Ir a escaner</button></a>
            <a href="reservar.php"><button>Ir a carga manual</button></a>
            <a href="escanerDevolucion.php"><button>Ir a devolucion con escaner</button></a>
            <a href="historial.php"><button>Ir a historial</button></a>
        </div>
        <h2>Devolución de Computadoras</h2>

        <form method="post">
            <table>
                <tr>
                    <th>Rack</th>
                    <th>Número</th>
                    <th>Curso</th>
                    <th>Docente</th>
                    <th>Acción</th>
                </tr>
                <?php while ($row = $computadoras->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['rack'] ?></td>
                        <td><?= $row['numero'] ?></td>
                        <td><?= $row['curso'] ?></td>
                        <td><?= $row['nombre_docente'] ?></td>
                        <td>
                            <button type="submit" name="devolver" value="<?= $row['id_computadora'] ?>">Devolver</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
            <button type="submit" name="devolver_todas" onclick="return confirmarDevolucionTodas();">Devolver Todas</button>
        </form>
    </div>
    <p>v1.1</p>
</body>
</html>
