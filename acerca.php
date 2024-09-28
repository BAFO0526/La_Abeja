<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="css/acerc.css">
    <link rel="stylesheet" href="css/maximo.css">
    <title>Acerca de Nosotros</title>
</head>
<body>
    <?php include 'vista/menu.php'; ?>

    <section class="about-container">
        <!-- Misión -->
        <div class="about-grid animate__animated animate__fadeInLeft" style="width: 90%; margin: auto; margin-top: 150px; margin-bottom: 30px;">
            <div class="about-content mission">
                <h2>Nuestra Misión</h2>
                <p>Producir y comercializar miel, polen, propóleo y productos relacionados de manera ética y sostenible, promoviendo la apicultura responsable y contribuyendo al bienestar de las comunidades locales y al cuidado del medio ambiente.</p>
            </div>
            <div class="about-image">
                <img src="img/fondo3.jpg" alt="Misión de la Empresa">
            </div>
        </div>

        <!-- Visión -->
        <div class="about-grid animate__animated animate__fadeInRight" style="width: 90%; margin: auto; margin-top: 150px; margin-bottom: 30px;">
            <div class="about-image">
                <img src="img/fondo3.jpg" alt="Visión de la Empresa">
            </div>
            <div class="about-content vision">
                <h2>Nuestra Visión</h2>
                <p>Ser líderes en la producción de miel y productos apícolas de la más alta calidad, comprometidos con la sostenibilidad del medio ambiente y la salud de nuestros consumidores, llevando la riqueza natural de nuestras colmenas a cada hogar.</p>
            </div>
        </div>

        <!-- Sección de Nuestra Historia -->
    <section class="our-history animate__animated animate__fadeInUp">
        <h2>Nuestra Historia</h2>
        <div class="history-gallery">
            <div class="history-card">
                <img src="img/fondo3.jpg" alt="Imagen 1">
                <div class="history-content">
                    <h3>2005 - Inicios Humildes</h3>
                    <p>Fundamos nuestra empresa con la visión de ofrecer productos apícolas de alta calidad, comenzando con una pequeña granja familiar.</p>
                </div>
            </div>
            <div class="history-card">
                <img src="img/fondo3.jpg" alt="Imagen 2">
                <div class="history-content">
                    <h3>2010 - Crecimiento Sostenido</h3>
                    <p>A lo largo de los años, ampliamos nuestras operaciones, invirtiendo en tecnología y aumentando nuestra capacidad de producción.</p>
                </div>
            </div>
            <div class="history-card">
                <img src="img/fondo3.jpg" alt="Imagen 3">
                <div class="history-content">
                    <h3>2020 - Innovación y Futuro</h3>
                    <p>Hoy, estamos enfocados en la innovación continua y en ser líderes en el mercado, con un compromiso firme con la sostenibilidad.</p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'vista/footer.php'; ?>
</body>
</html>
