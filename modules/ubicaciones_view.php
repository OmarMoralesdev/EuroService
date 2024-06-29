<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubicaciones</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
}

.accordion {
    display: flex;
    flex-direction: row;
}

.accordion-item {
    width: 100%;
    max-width: 300px;
    overflow: hidden;
    transition: max-width 0.3s ease;
    border: 1px solid #ccc;
    margin-right: 10px;
}

.accordion-title {
    background-color: #f1f1f1;
    padding: 10px;
    cursor: pointer;
    text-align: center;
    border-bottom: 1px solid #ccc;
}

.accordion-content {
    padding: 10px;
}

.show {
    max-width: 600px; 
}
.table-wrapper {
    display: flex;
    justify-content: center;
}

table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #ccc;
}

th, td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: center;
}
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../includes/vabr.html'; ?>
        <div class="main p-3">
            <div class="accordion">
                <div class="accordion-item" id="item1">
                    <div class="accordion-title">Terreno 1</div>
                    <div class="accordion-content">
                        <table class="table-wrapper">
                            <tr>
                                <th>Vehículo</th>
                                <th>Propietario</th>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="accordion-item" id="item2">
                    <div class="accordion-title">Terreno 2</div>
                    <div class="accordion-content">
                    <table class="table-wrapper">
                            <tr>
                                <th>Vehículo</th>
                                <th>Propietario</th>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="accordion-item" id="item3">
                    <div class="accordion-title">Terreno 3</div>
                    <div class="accordion-content">
                    <table class="table-wrapper">
                            <tr>
                                <th>Vehículo</th>
                                <th>Propietario</th>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="accordion-item" id="item4">
                    <div class="accordion-title">Terreno 4</div>
                    <div class="accordion-content">
                    <table class="table-wrapper">
                            <tr>
                                <th>Vehículo</th>
                                <th>Propietario</th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

