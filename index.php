<?php
include 'admin/conexion.php';

// Consultar portadas desde la base de datos
$query = "SELECT * FROM carrusel ORDER BY id DESC";
$result = $conn->query($query);
$portadas = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $portadas[] = $row;
    }
}
// Consultar servicios desde la base de datos
$query = "SELECT * FROM servicios ORDER BY id DESC";
$result = $conn->query($query);
$servicios = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $servicios[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apicultura y Miel</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/menu12.css">
    <link rel="stylesheet" href="css/portada1.css">
    <link rel="stylesheet" href="css/servicios.css">
    <link rel="stylesheet" href="css/proceso1.css">
    <link rel="stylesheet" href="css/beneficio.css">
    <link rel="stylesheet" href="css/maximo.css">
</head>

<body>
    <?php include 'vista/menu.php'; ?>
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php foreach ($portadas as $index => $portada): ?>
            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                <div class="hero" style="background-image: url('img/<?php echo htmlspecialchars(basename($portada['imagen_fondo'])); ?>');">
                    <div class="hero-content">
                        <h1><?php echo htmlspecialchars($portada['texto_encabezado']); ?></h1>
                        <p><?php echo htmlspecialchars($portada['texto_descripcion']); ?></p>
                        <a href="<?php echo htmlspecialchars($portada['enlace_boton']); ?>" class="btn btn-primary"><?php echo htmlspecialchars($portada['texto_boton']); ?></a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>
</div>


<div class="container" id="servicios">
        <h1 class="servicios__title">Servicios</h1>
        <div class="row">
            <?php foreach ($servicios as $servicio): ?>
                <div class="col-md-4">
                    <div class="tilt-card">
                        <div class="tilt-card__bg" style="background-image: url('img/<?php echo htmlspecialchars(basename($servicio['fondo_imagen'])); ?>');"></div>
                        <div class="tilt-card__inner">
                            <i class="tilt-card__icon material-icons">
                                <img src="img/<?php echo htmlspecialchars(basename($servicio['icono'])); ?>" width="50px" alt="">
                            </i>
                            <h3 class="tilt-card__title"><?php echo htmlspecialchars($servicio['subtitulo']); ?></h3>
                            <p class="tilt-card__text"><?php echo htmlspecialchars($servicio['texto']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="container" id="beneficios">
    <h1 class="beneficios__title">Beneficios de Consumir Miel</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="beneficio-card">
                <i class="fas fa-heartbeat beneficio-card__icon"></i>
                <h3 class="beneficio-card__title">Rica en Antioxidantes</h3>
                <p class="beneficio-card__text">La miel contiene antioxidantes que pueden ayudar a reducir el riesgo de enfermedades cardíacas.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="beneficio-card">
                <i class="fas fa-shield-virus beneficio-card__icon"></i>
                <h3 class="beneficio-card__title">Propiedades Antibacterianas</h3>
                <p class="beneficio-card__text">La miel puede ser usada como un remedio natural para heridas y quemaduras debido a sus propiedades antibacterianas.</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="beneficio-card">
                <i class="fas fa-utensils beneficio-card__icon"></i>
                <h3 class="beneficio-card__title">Mejora la Digestión</h3>
                <p class="beneficio-card__text">Consumir miel ayuda a mejorar la digestión al actuar como un prebiótico natural.</p>
            </div>
        </div>
    </div>
</div>


<div class="container" id="proceso">
    <h1 class="proceso__title">Proceso de Producción</h1>
    <div class="timeline">
        <div class="timeline__item">
            <div class="timeline__content">
                <h3 class="timeline__title">Recolección de Néctar</h3>
                <p class="timeline__text">Las abejas recolectan el néctar de las flores y lo transportan a la colmena.</p>
            </div>
        </div>
        <div class="timeline__item">
            <div class="timeline__content modificar">
                <h3 class="timeline__title">Transformación en Miel</h3>
                <p class="timeline__text">Dentro de la colmena, el néctar se convierte en miel a través de procesos enzimáticos.</p>
            </div>
        </div>
        <div class="timeline__item">
            <div class="timeline__content">
                <h3 class="timeline__title">Almacenamiento en Panales</h3>
                <p class="timeline__text">La miel es almacenada en los panales para su maduración y posterior recolección.</p>
            </div>
        </div>
        <div class="timeline__item modificar">
            <div class="timeline__content ">
                <h3 class="timeline__title">Extracción de la Miel</h3>
                <p class="timeline__text">La miel madura es extraída de los panales y procesada para su consumo.</p>
            </div>
        </div>
        
    </div>
</div>
    <?php include 'vista/footer.php'; ?>
</body>

</html>