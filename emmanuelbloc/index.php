<!DOCTYPE html>
<html>
<head>
    <title>Bloc de Notas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            text-align: center;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form p {
            color: green;
            font-weight: bold;
            margin: 10px 0;
        }

        form label {
            display: block;
            margin-top: 10px;
        }

        form input[type="text"],
        form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form input[type="submit"] {
            display: block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #6699CC;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #6699CC;
        }

        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <h1>Bloc de Notas</h1>

    <?php
    // Directorio donde se guardarán los archivos
    $directory = 'documentos/';

    $archivoGuardado = false; // Variable para controlar el mensaje
    $mostrarFormulario = false; // Variable de bandera para mostrar el formulario

    if (isset($_POST['save'])) {
        // Obtener el nombre del archivo ingresado por el usuario
        $fileName = $_POST['file-name'];

        // Crear la carpeta si no existe
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $content = $_POST['content'];

        // Verificar si el nombre de archivo no está vacío
        if ($fileName !== '') {
            // Agregar la extensión ".txt" al nombre de archivo
            $fileName = $fileName . '.txt';

            $filePath = $directory . $fileName;
            $file = fopen($filePath, 'w');
            fwrite($file, $content);
            fclose($file);

            $archivoGuardado = true; // Cambiar el valor de la variable de bandera

            // Mostrar el formulario de edición si se ha guardado un archivo
            $mostrarFormulario = true;
        } else {
            echo '<p>Por favor, ingresa un nombre de archivo.</p>';
        }
        
    }

    // Leer archivos existentes
    $archivosExistentes = scandir($directory);
    $archivosExistentes = array_diff($archivosExistentes, array('.', '..')); // Eliminar las entradas "." y ".."

    if (isset($_POST['load'])) {
        $selectedFile = $_POST['existing-file'];
        
        if ($selectedFile !== '') {
            $filePath = $directory . $selectedFile;
            $fileContent = file_get_contents($filePath);
            echo '<h2>Contenido del archivo "' . $selectedFile . '"</h2>';
            echo '<pre>' . htmlspecialchars($fileContent) . '</pre>';

            // Mostrar formulario para editar el contenido
            echo '<h2>Editar archivo</h2>';
            echo '<form action="index.php" method="post">';
            echo '<textarea id="edit-content" name="edit-content" style="width: 100%; height: 400px;">' . htmlspecialchars($fileContent) . '</textarea>';
            echo '<br>';
            echo '<input type="hidden" name="edit-file" value="' . $selectedFile . '">';
            echo '<input type="submit" name="update" value="Actualizar">';
            echo '</form>';
            $mostrarFormulario = true; // Variable de bandera para mostrar el formulario
        } else {
            echo '<p>Por favor, selecciona un archivo existente.</p>';
        }
    }
    ?>

    <form action="index.php" method="post">
        <?php if (isset($archivoGuardado) && $archivoGuardado) : ?>
            <script>
                window.onload = function() {
                    alert("Archivo guardado correctamente.");
                };
            </script>
        <?php endif; ?>

        <label for="file-name">Nombre del archivo:</label>
        <input type="text" name="file-name" id="file-name">
        
        <label for="folder-select">Carpeta de destino:</label>
        <select name="folder-select" id="folder-select">
            <?php
            $folders = scandir($directory);
            foreach ($folders as $folder) {
                if ($folder !== '.' && $folder !== '..' && is_dir($directory . $folder)) {
                    echo '<option value="' . $folder . '">' . $folder . '</option>';
                }
            }
            ?>
        </select>
        <br>
        <textarea id="content" name="content" style="width: 100%; height: 400px;"></textarea>
        <br>
        <input type="submit" name="save" value="Guardar">

        <label for="existing-file">Leer y Editar Archivo Existente:</label>
        <select name="existing-file" id="existing-file">
            <option value="">Seleccionar archivo...</option>
            <?php foreach ($archivosExistentes as $archivo) : ?>
                <?php if (is_file($directory . $archivo)) : ?>
                    <option value="<?php echo $archivo ?>"><?php echo $archivo ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <input type="submit" name="load" value="Cargar">

        <label for="folder-name">Nombre de la carpeta:</label>
        <input type="text" name="folder-name" id="folder-name">
        <input type="submit" name="create-folder" value="Crear carpeta">
    </form>


    <?php
    if (isset($_POST['update'])) {
        $updatedFile = $_POST['edit-file'];
        $updatedContent = $_POST['edit-content'];
        $filePath = $directory . $updatedFile;
        file_put_contents($filePath, $updatedContent);
        echo '<script>
                window.onload = function() {
                    alert("El archivo se ha actualizado correctamente.");
                };
            </script>';
    }
    ?>

    <?php
    if (isset($_POST['create-folder'])) {
        $folderName = $_POST['folder-name'];

        // Verificar si el nombre de la carpeta no está vacío
        if ($folderName !== '') {
            // Crear la carpeta en el directorio especificado
            $folderPath = $directory . $folderName;
            if (!is_dir($folderPath)) {
                mkdir($folderPath, 0777, true);
                echo '<script>
                    window.onload = function() {
                        alert("Carpeta creada correctamente.");
                    };
                </script>';
            } else {
                echo '<script>
                    window.onload = function() {
                        alert("La carpeta ya existe.");
                    };
                </script>';
            }
        } else {
            echo '<script>
                window.onload = function() {
                    alert("Por favor, ingresa un nombre de carpeta válido.");
                };
            </script>';
        }
    }
    ?>

    <?php
    if (isset($_POST['save'])) {
        // Obtener el nombre del archivo ingresado por el usuario
        $fileName = $_POST['file-name'];

        // Obtener la carpeta de destino seleccionada por el usuario
        $selectedFolder = $_POST['folder-select'];

        // Construir la ruta completa del archivo
        $filePath = $directory . $selectedFolder . '/' . $fileName;

        // Resto del código para guardar el archivo...
    }
    ?>

</body>
</html>