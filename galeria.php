<?php
// Incluye la conexión a la base de datos
include 'admin/conexion.php';

// Consulta para obtener las imágenes
$query = "SELECT imagen FROM galeria";
$result = $conn->query($query);

// Verifica si hay imágenes en la base de datos
$imagenes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imagenes[] = $row['imagen'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/maximo.css">
    <title>Galería de Imágenes</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }

        .gallery-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            margin-top: 150px;
        }

        .gallery-item {
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease-in-out;
            width: 100%;
            height: 200px;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .lightbox {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .lightbox img {
            max-width: 90%;
            max-height: 80%;
            border-radius: 10px;
            transition: opacity 0.3s ease;
        }

        .lightbox .close, .lightbox .prev, .lightbox .next {
            position: absolute;
            top: 50%;
            font-size: 30px;
            color: #fff;
            cursor: pointer;
            z-index: 10000;
            transform: translateY(-50%);
        }

        .lightbox .close {
            right: 20px;
        }

        .lightbox .prev {
            left: 20px;
        }

        .lightbox .next {
            right: 60px;
        }
    </style>
</head>
<body>
    <?php include 'vista/menu.php'; ?>
    <div class="gallery-container">
        <h2>Galería de Imágenes</h2>
        <div class="row">
            <?php foreach ($imagenes as $imagen): ?>
                <div class="col-lg-4 col-md-6 col-sm-12 gallery-item">
                    <img src="img/<?php echo htmlspecialchars($imagen); ?>" alt="Imagen de la galería" class="gallery-image">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="lightbox" id="lightbox">
        <span class="close" onclick="closeLightbox()">&times;</span>
        <span class="prev" onclick="changeImage(-1)">&#10094;</span>
        <span class="next" onclick="changeImage(1)">&#10095;</span>
        <img id="lightbox-img" src="" alt="Imagen agrandada">
    </div>

    <script>
        const galleryItems = document.querySelectorAll('.gallery-item img');
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        let currentIndex = 0;

        galleryItems.forEach((item, index) => {
            item.addEventListener('click', () => {
                currentIndex = index;
                showLightbox();
            });
        });

        function showLightbox() {
            lightboxImg.src = galleryItems[currentIndex].src;
            lightbox.style.display = 'flex';
            lightboxImg.style.opacity = 0;
            setTimeout(() => {
                lightboxImg.style.opacity = 1;
            }, 10);
        }

        function closeLightbox() {
            lightbox.style.display = 'none';
        }

        function changeImage(direction) {
            currentIndex = (currentIndex + direction + galleryItems.length) % galleryItems.length;
            lightboxImg.style.opacity = 0;
            setTimeout(() => {
                lightboxImg.src = galleryItems[currentIndex].src;
                lightboxImg.style.opacity = 1;
            }, 300);
        }

        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });
    </script>
    <?php include 'vista/footer.php'; ?>
</body>
</html>
