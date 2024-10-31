<?php
// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'c2660463_1');
//$conn = new mysqli('localhost', 'c2660463_1', '44guwedeWI', 'c2660463_1');

// Definir variables para los filtros
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$nombre_docente = isset($_GET['nombre_docente']) ? $_GET['nombre_docente'] : '';
$curso = isset($_GET['curso']) ? $_GET['curso'] : '';

// Construir la consulta SQL con filtros
$sql = "SELECT m.fecha_movimiento, m.tipo_movimiento, c.rack, c.numero, r.curso, d.nombre_docente 
        FROM movimientos m
        JOIN computadoras c ON m.id_computadora = c.id_computadora
        JOIN reservas r ON m.id_reserva = r.id_reserva
        JOIN docentes d ON r.id_docente = d.id_docente
        WHERE 1=1";

// Agregar condiciones según los filtros
if (!empty($fecha)) {
    $sql .= " AND DATE(m.fecha_movimiento) = '$fecha'";
}

if (!empty($nombre_docente)) {
    $sql .= " AND d.nombre_docente LIKE '%$nombre_docente%'";
}
if (!empty($curso)) {
    $sql .= " AND r.curso LIKE '%$curso%'";
}

$sql .= " ORDER BY m.fecha_movimiento DESC";

// Ejecutar la consulta
$movimientos = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Movimientos</title>
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
</head>
<body>
    <div class="container">
        <div class="navigation-buttons">
            <a href="escaner.php"><button>Ir a escaner</button></a>
            <a href="reservar.php"><button>Ir a carga manual</button></a>
            <a href="devolver.php"><button>Ir a devoluciones</button></a>
            <a href="escanerDevolucion.php"><button>Ir a devolucion con escaner</button></a>
            <a href="historial.php"><button>Ir a historial</button></a>
        </div>
        <h2>Historial de Movimientos</h2>

        <!-- Formulario de Filtros -->
        <form method="GET">
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" value="<?= htmlspecialchars($fecha) ?>">

            <label for="nombre_docente">Docente:</label>
            <input type="text" name="nombre_docente" id="nombre_docente" value="<?= htmlspecialchars($nombre_docente) ?>">

            <label for="curso">Curso:</label>
            <input type="text" name="curso" id="curso" value="<?= htmlspecialchars($curso) ?>">

            <button type="submit">Filtrar</button>
        </form>
        <br>
        <table>
            <tr>
                <th>Fecha</th>
                <th>Movimiento</th>
                <th>Rack</th>
                <th>Número</th>
                <th>Curso</th>
                <th>Docente</th>
            </tr>
            <?php while ($row = $movimientos->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['fecha_movimiento'] ?></td>
                    <td><?= ucfirst($row['tipo_movimiento']) ?></td>
                    <td><?= $row['rack'] ?></td>
                    <td><?= $row['numero'] ?></td>
                    <td><?= $row['curso'] ?></td>
                    <td><?= $row['nombre_docente'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <p>v1.0</p>
</body>
</html>
