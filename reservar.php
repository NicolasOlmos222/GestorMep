<?php
session_start(); // Inicia la sesión

// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'c2660463_1');
//$conn = new mysqli('localhost', 'c2660463_1', '44guwedeWI', 'c2660463_1');
//LECTURA ESCANER:
//LenovoV330'31
//CONIC'9
//Pix'10

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_docente = $_POST['id_docente'];
    $curso = $_POST['curso'];
    $id_computadora = $_POST['id_computadora'];

    // Guardar el docente y curso seleccionados en la sesión
    $_SESSION['docente_seleccionado'] = $id_docente;
    $_SESSION['curso_seleccionado'] = $curso;

    // Insertar la reserva
    $sql = "INSERT INTO reservas (id_docente, curso, id_computadora, fecha_reserva, estado_reserva) 
            VALUES ('$id_docente', '$curso', '$id_computadora', NOW(), 'activa')";
    $conn->query($sql);

    // Actualizar el estado de la computadora
    $sql = "UPDATE computadoras SET estado='en uso' WHERE id_computadora='$id_computadora'";
    $conn->query($sql);

    // Registrar movimiento
    $sql = "INSERT INTO movimientos (id_computadora, id_reserva, fecha_movimiento, tipo_movimiento) 
            VALUES ('$id_computadora', LAST_INSERT_ID(), NOW(), 'reserva')";
    $conn->query($sql);

    echo "Reserva realizada con éxito.";
}

// Obtener docentes y computadoras disponibles
$docentes = $conn->query("SELECT * FROM docentes");
$computadoras = $conn->query("SELECT * FROM computadoras WHERE estado='disponible'");

// Obtener el docente y curso seleccionados previamente
$docente_seleccionado = isset($_SESSION['docente_seleccionado']) ? $_SESSION['docente_seleccionado'] : '';
$curso_seleccionado = isset($_SESSION['curso_seleccionado']) ? $_SESSION['curso_seleccionado'] : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Computadora</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
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
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin: 10px 0 5px;
            font-weight: bold;
            color: #555;
        }
        select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            margin: 5px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .computadoras-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }
        .computadora-button {
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 15px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .computadora-button:hover {
            background-color: #218838;
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
            <a href="devolver.php"><button>Ir a devolucion manual</button></a>
            <a href="escanerDevolucion.php"><button>Ir a devolucion con escaner</button></a>
            <a href="historial.php"><button>Ir a historial</button></a>
        </div>
        <h2>Reservar Computadora</h2>

        <form method="post">
            <label for="docente">Docente:</label>
            <select name="id_docente" id="docente">
                <?php while ($row = $docentes->fetch_assoc()): ?>
                    <option value="<?= $row['id_docente'] ?>" <?= $row['id_docente'] == $docente_seleccionado ? 'selected' : '' ?>>
                        <?= $row['nombre_docente'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="curso">Curso:</label>
            <select name="curso" id="curso">
                <option value="1A" <?= $curso_seleccionado == '1A' ? 'selected' : '' ?>>1A</option>
                <option value="1B" <?= $curso_seleccionado == '1B' ? 'selected' : '' ?>>1B</option>
                <option value="2A" <?= $curso_seleccionado == '2A' ? 'selected' : '' ?>>2A</option>
                <option value="2B" <?= $curso_seleccionado == '2B' ? 'selected' : '' ?>>2B</option>
                <option value="3A" <?= $curso_seleccionado == '3A' ? 'selected' : '' ?>>3A</option>
                <option value="3B" <?= $curso_seleccionado == '3B' ? 'selected' : '' ?>>3B</option>
                <option value="4A" <?= $curso_seleccionado == '4A' ? 'selected' : '' ?>>4A</option>
                <option value="4B" <?= $curso_seleccionado == '4B' ? 'selected' : '' ?>>4B</option>
                <option value="5A" <?= $curso_seleccionado == '5A' ? 'selected' : '' ?>>5A</option>
                <option value="5B" <?= $curso_seleccionado == '5B' ? 'selected' : '' ?>>5B</option>
                <option value="6A" <?= $curso_seleccionado == '6A' ? 'selected' : '' ?>>6A</option>
                <option value="6B" <?= $curso_seleccionado == '6B' ? 'selected' : '' ?>>6B</option>
                <option value="7A" <?= $curso_seleccionado == '7A' ? 'selected' : '' ?>>7A</option>
                <option value="7B" <?= $curso_seleccionado == '7B' ? 'selected' : '' ?>>7B</option>
            </select>

            <p>Selecciona una computadora:</p>
            <div class="computadoras-container">
                <?php while ($row = $computadoras->fetch_assoc()): ?>
                    <button type="submit" name="id_computadora" value="<?= $row['id_computadora'] ?>" class="computadora-button">
                        <?= $row['rack'] ?>, N° <?= $row['numero'] ?>
                    </button>
                <?php endwhile; ?>
            </div>
        </form>
    </div>
    <p>v1.1</p>
</body>
</html>
