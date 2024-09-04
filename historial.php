<?php
// Conectar a la base de datos
$conn = new mysqli('localhost', 'c2660463_1', '44guwedeWI', 'c2660463_1');

$movimientos = $conn->query("SELECT m.fecha_movimiento, m.tipo_movimiento, c.rack, c.numero, r.curso, d.nombre_docente 
                             FROM movimientos m
                             JOIN computadoras c ON m.id_computadora = c.id_computadora
                             JOIN reservas r ON m.id_reserva = r.id_reserva
                             JOIN docentes d ON r.id_docente = d.id_docente
                             ORDER BY m.fecha_movimiento DESC");
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
            <a href="reservar.php"><button>Ir a Reservas</button></a>
            <a href="devolver.php"><button>Ir a Devoluciones</button></a>
        </div>
        <h2>Historial de Movimientos</h2>

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
