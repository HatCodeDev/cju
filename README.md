# Casa del jubilado universitario

## Tecnologías Utilizadas

Este proyecto fue construido utilizando las siguientes tecnologías:

* **Laravel**: Un framework de aplicación web PHP con sintaxis expresiva y elegante.
* **Filament**: Un conjunto de herramientas de desarrollo rápido para Laravel, ideal para construir interfaces de administración elegantes.
* **PHP**: El lenguaje de programación principal utilizado para el backend del proyecto.
* **MySQL**: El sistema de gestión de bases de datos relacionales utilizado para almacenar la información.
* **HTML**: El lenguaje de marcado estándar para la creación de páginas web.
* **CSS**: El lenguaje de hojas de estilo utilizado para el diseño y la presentación.


1.  **Clonar el repositorio:**
    Ejecuta el siguiente comando en tu terminal para clonar el proyecto:

    ```bash
    https://github.com/HatCodeDev/cju.git
    ```

2.  **Acceder al directorio del proyecto:**
    Navega al directorio del proyecto recién clonado:

    ```bash
    cd cju
    
3. **Instalar dependencias de Composer:**
    Una vez en el directorio del proyecto, instala las dependencias de PHP con Composer:

    ```bash
    composer install
    ```

4. **Instalar dependencias de NPM:**
    Para las dependencias de frontend, usa npm:

    ```bash
    npm install
    ```

5. **Copiar el archivo de entorno y configurarlo:**
    Copia el archivo de entorno de ejemplo:

    ```bash
    cp .env.example .env
    ```
    Luego, abre el archivo `.env` en tu editor de código y configura los detalles de tu base de datos (asegúrate de que los valores de `DB_USERNAME` y `DB_PASSWORD` coincidan con tu configuración local):

6. **Generar la clave de aplicación:**

    ```bash
    php artisan key:generate
    ```

7. **Ejecutar migraciones:**
    Crea las tablas de la base de datos:

    ```bash
    php artisan migrate
    ```
8. **Crear un usuario de Filament:**
    Para crear un usuario administrador para el panel de Filament, ejecuta:

    ```bash
    php artisan make:filament-user
    ```
    Sigue las indicaciones en la terminal para ingresar los detalles del usuario:
    * `Name:` admin
    * `Email address:` admin@gmail.com
    * `Password:` admin
9. **Generar permisos y políticas de Shield:**
    Para generar los permisos y políticas para el panel de administración de Filament, ejecuta:

    ```bash
    php artisan shield:generate --all
    ```
    Cuando te pregunte `Which panel do you want to generate permissions/policies for?`, selecciona `admin`.

10. **Asignar el rol de super-administrador:**
    Asigna el rol de super-administrador al usuario que acabas de crear:

    ```bash
    php artisan shield:super-admin
    ```

11. **Iniciar el servidor de desarrollo:**
    Finalmente, inicia el servidor de desarrollo de Laravel:

    ```bash
    php artisan serve
    ```
    Ahora deberías poder acceder al proyecto en tu navegador en `http://127.0.0.1:8000` (o la dirección que te indique la terminal).
