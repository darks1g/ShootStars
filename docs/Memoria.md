# Proyecto: ShootStars - Memoria del Proyecto

## 1. Introducción y Descripción General

### 1.1 Motivación
ShootStars nace de la necesidad de crear un espacio digital donde los usuarios puedan compartir pensamientos, reflexiones o mensajes de manera efímera y aleatoria, fomentando la interacción genuina a través de reacciones simples y seguras. La aplicación busca ofrecer una experiencia de usuario fluida y protegida por medidas de seguridad modernas.

### 1.2 Objetivos
- **Visualización Aleatoria**: Implementar un sistema de entrega de mensajes que garantice el anonimato y la sorpresa.
- **Interacción Social**: Permitir que los usuarios reaccionen con emoticonos y reporten contenido inapropiado.
- **Seguridad Robusta**: Establecer un sistema de autenticación de dos factores (2FA) para proteger las cuentas de usuario.
- **Gestión de Contenido**: Proporcionar paneles de control tanto para usuarios (gestión de mensajes propios) como para administradores (moderación de reportes).

### 1.3 Descripción General
ShootStars es una aplicación web que permite a los usuarios leer y escribir mensajes. Al acceder, el usuario ve un mensaje al azar y puede reaccionar a él. Los usuarios registrados tienen acceso a un panel personal, y los administradores cuentan con herramientas para mantener la integridad de la comunidad.

---

## 2. Estado del Arte y Tecnologías

### 2.1 Stack Tecnológico
Para el desarrollo de ShootStars se han seleccionado tecnologías estables y ampliamente documentadas:

- **Backend**: 
    - **PHP 8.x**: Como lenguaje principal del servidor por su versatilidad y compatibilidad con bases de datos SQL.
    - **PHPMailer**: Utilizado para la gestión de envíos de correo electrónico críticos (códigos 2FA y notificaciones de administración).
- **Frontend**: 
    - **HTML5 y CSS3**: Para una estructura semántica y un diseño visual atractivo y responsive.
    - **JavaScript (Vanilla)**: Manejo de la interactividad del lado del cliente y peticiones asíncronas (AJAX) al backend para una experiencia dinámica sin recargas de página.
- **Base de Datos**: 
    - **MySQL**: Motor de base de datos relacional para el almacenamiento persistente de usuarios, mensajes, reacciones y logs de seguridad.

### 2.2 Entorno de Servidor
- **Sistema Operativo**: Debian (servidor dedicado local).
- **Servidor Web**: Apache HTTP Server.

---

## 3. Documentación Técnica

### 3.1 Análisis del Sistema
#### Casos de Uso Principales
1. **Ver e interactuar con mensajes**: Acceso a contenido aleatorio y reacciones (emoticonos).
2. **Registro e Inicio de Sesión con 2FA**: Proceso seguro de entrada a la plataforma.
3. **Gestión de Mensajes Propios**: Panel de usuario para editar o eliminar sus publicaciones.
4. **Moderación de Administrador**: Revisión y gestión de reportes de contenido.

#### Requisitos Funcionales
- El sistema debe validar cada inicio de sesión mediante un código único enviado por correo.
- Los mensajes deben mostrarse de forma aleatoria y no repetitiva en una misma sesión rápida.
- Las reacciones deben contabilizarse y mostrarse al autor del mensaje en tiempo real.

---

## 4. Diseño y Estructura del Código

### 4.1 Base de Datos
El modelo relacional se compone de las siguientes tablas principales:
- `users`: Información de perfil y credenciales cifradas.
- `messages`: Contenido de las publicaciones y su estado (activo/reportado).
- `reactions`: Vínculo entre usuarios, tipos de emoticono y mensajes.
- `two_factor_codes`: Almacenamiento temporal de tokens de acceso para 2FA.

### 4.2 Arquitectura del Código
El proyecto sigue una separación clara entre el frontend y el backend:
- **/frontend**: Contiene las vistas (PHP/HTML), estilos (CSS) y scripts de cliente (JS).
- **/backend**: Contiene los endpoints de la API que procesan la lógica del negocio, conexión a BD (`db.php`) y utilidades de correo (`email_helper.php`).
- **Seguridad**: Uso de archivos `.env` para la gestión de variables de entorno sensibles y protección mediante `.htaccess`.

---

## 5. Manuales

### 5.1 Manual de Usuario
1. **Acceso**: El usuario accede a la URL principal y hace clic para ver un mensaje aleatorio.
2. **Interacción**: Bajo el mensaje, aparecen iconos de reacciones (corazón, risa, etc.) y un botón de reporte.
3. **Registro/Login**: El usuario puede crear una cuenta. Tras introducir sus datos, recibirá un código de 6 dígitos en su correo.
4. **Dashboard**: El usuario registrado puede ver sus mensajes publicados, las reacciones recibidas y tiene la opción de eliminarlos.
5. **Administración**: El administrador accede a un panel donde visualiza mensajes reportados y decide si eliminarlos o mantenerlos.

### 5.2 Manual de Instalación
1. **Prerrequisitos**: Servidor con Apache, PHP 8.x y MySQL.
2. **Base de Datos**: Importar el esquema disponible en `/database/schema.sql`.
3. **Configuración**: 
    - Copiar `.env.example` a `.env` y configurar las credenciales de BD y SMTP para PHPMailer.
    - Asegurar que la carpeta de avatares tenga permisos de escritura.
4. **Despliegue**: Mover los archivos al directorio raíz del servidor web (ej. `/var/www/html`).

---

## 6. Conclusiones y Futuras Mejoras

### 6.1 Conclusiones
El proyecto ShootStars ha cumplido con los objetivos planteados inicialmente. Se ha logrado integrar una lógica de backend sólida con PHP y una interfaz dinámica con JavaScript, priorizando la seguridad del usuario mediante el cifrado de contraseñas y la implementación exitosa de 2FA. El sistema de moderación permite un control efectivo sobre el contenido generado por la comunidad.

### 6.2 Futuras Mejoras
- **Notificaciones Real-Time**: Implementar WebSockets para ver reacciones en tiempo real sin recargar.
- **Categorización**: Permitir que los mensajes se agrupen por temas o etiquetas.
- **App Móvil**: Convertir la web en una PWA (Progressive Web App) para mejorar la experiencia en dispositivos móviles.

---

## 7. Bibliografía y Webgrafía
- Documentación oficial de PHP: [php.net](https://www.php.net/)
- Repositorio oficial PHPMailer: [GitHub PHPMailer](https://github.com/PHPMailer/PHPMailer)
- Documentación de MDN Web Docs para JS/CSS.
