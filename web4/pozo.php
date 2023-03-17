<!DOCTYPE html>
<html>

<head>
    <title>Registro de válvulas PSI</title>
</head>

<body>
    <h1>Registro de válvulas PSI</h1>
    <form method="post" action="index.php">
        <label for="pozo">Pozo:</label>
        <input type="text" name="pozo" required><br><br>
        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" required><br><br>
        <label for="hora">Hora:</label>
        <input type="time" name="hora" required><br><br>
        <label for="psi">PSI:</label>
        <input type="number" name="psi" required><br><br>
        <input type="submit" value="Guardar">
    </form>
    <?php
    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "valvulas psi";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Procesar el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $pozo = $_POST["pozo"];
        $fecha = $_POST["fecha"];
        $hora = $_POST["hora"];
        $psi = $_POST["psi"];
        $sql = "INSERT INTO valvulas (pozo, fecha, hora, psi) VALUES ('$pozo', '$fecha', '$hora', '$psi')";
        if ($conn->query($sql) === TRUE) {
            echo "Registro guardado exitosamente";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
    ?>
</body>

<h2>Historial de registros</h2>
<table>
    <thead>
        <tr>
            <th>Pozo</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>PSI</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Conexión a la base de datos
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "valvulas psi";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Obtener los registros de la base de datos
        $sql = "SELECT * FROM valvulas";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["pozo"] . "</td>";
                echo "<td>" . $row["fecha"] . "</td>";
                echo "<td>" . $row["hora"] . "</td>";
                echo "<td>" . $row["psi"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No hay registros</td></tr>";
        }

        // Cerrar la conexión a la base de datos
        $conn->close();
        ?>
    </tbody>
</table>
<h2>Gráfica de PSI por pozo</h2>
<div>
    <canvas id="myChart"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Obtener los datos de la base de datos
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "valvulas psi";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    $sql = "SELECT pozo, GROUP_CONCAT(psi ORDER BY fecha ASC, hora ASC SEPARATOR ',') AS psi FROM valvulas GROUP BY pozo";
    $result = $conn->query($sql);
    $datos = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $datos[] = array(
                "pozo" => $row["pozo"],
                "psi" => explode(",", $row["psi"])
            );
        }
    }
    $conn->close();
    ?>

    // Preparar los datos para la gráfica
    var datosGrafica = {
        labels: [],
        datasets: []
    };

    <?php
    foreach ($datos as $d) {
        $datosGrafica["labels"] = array_map(function ($x) {
            return date("d/m H:i", strtotime($x));
        }, array_keys($d["psi"]));
        $datosGrafica["datasets"][] = array(
            "label" => $d["pozo"],
            "data" => $d["psi"],
            "borderColor" => "rgba(<?php echo rand(0, 255); ?>, <?php echo rand(0, 255); ?>, <?php echo rand(0, 255); ?>, 1)",
            "fill" => false
        );
    }
    ?>

    // Crear la gráfica
    var ctx = document.getElementById("myChart").getContext("2d");
    var myChart = new Chart(ctx, {
        type: "line",
        data: datosGrafica,
        options: {
            responsive: true,
            title: {
                display: true,
                text: "PSI por pozo"
            },
            tooltips: {
                mode: "index",
                intersect: false
            },
            hover: {
                mode: "nearest",
                intersect: true
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: "Fecha y hora"
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: "PSI"
                    }
                }]
            }
        }
    });
</script>

</html>