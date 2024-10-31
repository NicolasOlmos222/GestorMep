<?php
// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'c2660463_1');

// Manejo de la devolución al escanear un código
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo_escaneado = $_POST['codigo_escaneado'];

    if (preg_match("/^(LenovoV330|CONIC|Pix)'(\d+)$/", $codigo_escaneado, $matches)) {
        $marca = $matches[1];
        $numero = $matches[2];

        if ($marca == 'LenovoV330') {
            $marca = 'LENOVO';
        } else if ($marca == 'CONIC' || $marca == 'Pix') {
            $marca = 'CONIG';
        }

        // Obtener la computadora en uso basada en el código escaneado
        $sql = "SELECT id_computadora FROM computadoras WHERE rack = '$marca' AND numero = '$numero' AND estado = 'en uso'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_computadora = $row['id_computadora'];

            // Obtener la reserva activa
            $reserva = $conn->query("SELECT id_reserva FROM reservas WHERE id_computadora='$id_computadora' AND estado_reserva='activa'")->fetch_assoc();

            // Actualizar el estado de la computadora a disponible y finalizar la reserva
            $sql = "UPDATE computadoras SET estado='disponible' WHERE id_computadora='$id_computadora'";
            $conn->query($sql);
            $sql = "UPDATE reservas SET estado_reserva='finalizada' WHERE id_reserva='{$reserva['id_reserva']}'";
            $conn->query($sql);

            // Registrar el movimiento de devolución
            $sql = "INSERT INTO movimientos (id_computadora, id_reserva, fecha_movimiento, tipo_movimiento) 
                    VALUES ('$id_computadora', '{$reserva['id_reserva']}', NOW(), 'devolucion')";
            $conn->query($sql);

            echo "<p>Computadora devuelta con éxito.</p>";
        } else {
            echo "<p>Computadora no encontrada o no está en uso.</p>";
        }
    } else {
        echo "<p>Código de escaneo no válido.</p>";
    }
}

// Obtener lista de computadoras reservadas y en uso
$computadoras_reservadas = $conn->query("SELECT c.rack, c.numero, r.curso, d.nombre_docente 
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
    </style>
    <script>
        window.onload = function() {
            document.getElementById('codigo_escaneado').focus();
        };
    </script>
</head>
<body>
    <div class="container">
        <div class="navigation-buttons">
            <a href="escaner.php"><button>Ir a escaner</button></a>
            <a href="reservar.php"><button>Ir a carga manual</button></a>
            <a href="devolver.php"><button>Ir a devolucion manual</button></a>
            <a href="historial.php"><button>Ir a historial</button></a>
        </div>
        <h2>Devolución de Computadoras</h2>
        <form method="post">
            <label for="codigo_escaneado">Escanea el código de la computadora:</label>
            <input type="text" name="codigo_escaneado" id="codigo_escaneado" required placeholder="Ej. LenovoV330'31">
            <button type="submit">Devolver Computadora</button>
        </form>

        <h2>Computadoras Reservadas</h2>
        <table border="1">
            <thead>
                <tr>
                    <th>Rack</th>
                    <th>Número</th>
                    <th>Curso</th>
                    <th>Docente</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $computadoras_reservadas->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['rack'] ?></td>
                        <td><?= $row['numero'] ?></td>
                        <td><?= $row['curso'] ?></td>
                        <td><?= $row['nombre_docente'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>


