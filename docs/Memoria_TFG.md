# Proyecto de Fin de Grado: ShootStars - Memoria Técnica Detallada

## 1. Introducción y Justificación

### 1.1 Contexto del Proyecto
En el ecosistema actual de las redes sociales, la tendencia predominante es la creación de perfiles permanentes y la acumulación de un historial público que a menudo condiciona la libertad de expresión de los usuarios. **ShootStars** nace como un proyecto de investigación y desarrollo técnico para explorar interfaces de comunicación efímera y desvinculada de la identidad persistente en el momento de la visualización.

### 1.2 Motivación y Necesidad
La motivación técnica principal es demostrar que es posible construir una aplicación web robusta, segura y escalable utilizando un stack tecnológico ligero (Vanilla JS y PHP) sin depender de frameworks de terceros que aumenten la complejidad y el tamaño del proyecto innecesariamente. Desde una perspectiva social, existe la necesidad de espacios digitales donde el contenido sea el protagonista absoluto, permitiendo interacciones rápidas y seguras.

### 1.3 Objetivos del Trabajo
*   **Diseño de una Arquitectura Distribuidora:** Implementar un algoritmo de entrega de mensajes aleatorios que no requiera de una base de datos de grafos compleja, utilizando SQL eficiente.
*   **Seguridad de Nivel Académico:** Implementar un flujo de autenticación multifactor (2FA) que mitigue los ataques de fuerza bruta y suplantación de identidad.
*   **Optimización del Frontend:** Desarrollar una interfaz que cumpla con los estándares modernos de diseño responsivo y rendimiento (Core Web Vitals).
*   **Sistema de Moderación Proactivo:** Crear un ciclo de vida para el contenido reportado, permitiendo a los administradores una gestión eficiente mediante un panel dedicado.

---

## 2. Tecnologías y Estado del Arte

### 2.1 Justificación Técnica del Stack
La selección de tecnologías se ha realizado bajo criterios de eficiencia y control total sobre el código:
-   **PHP 8.2:** Se utiliza esta versión para aprovechar el motor JIT y las mejoras de seguridad en el manejo de tipos. Es el motor que gestiona la lógica de negocio y la comunicación con el sistema de archivos y la base de datos.
-   **MySQL 8.0:** Motor relacional que garantiza la integridad referencial. Se ha optimizado el uso de índices para las consultas `ORDER BY RAND()`.
-   **PHPMailer 6.x:** Estándar de la industria para el envío de correos vía SMTP, permitiendo adjuntos y plantillas HTML enriquecidas.
-   **JavaScript ES6+:** Se evita el uso de librerías como jQuery para reducir la latencia de carga. Se utilizan `fetch API` y `Async/Await` para la comunicación asíncrona.

### 2.2 Comparativa con el Estado del Arte
A diferencia de plataformas como Twitter o Mastodon, donde el flujo de información es lineal o por suscripción, ShootStars utiliza una técnica de "Inyección de Mensaje Aleatorio" que rompe el sesgo del algoritmo tradicional, ofreciendo una experiencia de descubrimiento pura.

---

## 3. Planificación y Presupuesto

### 3.1 Cronograma de Desarrollo
El proyecto se ha desarrollado en un periodo de 12 semanas, siguiendo una metodología ágil simplificada:
1.  **Semanas 1-2:** Análisis de requisitos y diseño de la base de datos.
2.  **Semanas 3-5:** Desarrollo del backend (API de autenticación y mensajes).
3.  **Semanas 6-8:** Desarrollo del frontend y diseño UI/UX.
4.  **Semanas 9-10:** Implementación de seguridad (2FA) y sistema de reportes.
5.  **Semanas 11-12:** Pruebas integrales, depuración y redacción de la memoria.

### 3.2 Estimación de Costes (Presupuesto)
Para este TFG, se ha estimado el coste basado en el mercado laboral actual (estimación teórica):
-   **Recursos Humanos:** 1 desarrollador junior (30€/hora x 240 horas) = 7.200€.
-   **Infraestructura:** Servidor dedicado Debian + Dominio + SSL = 150€/año.
-   **Software:** Herramientas de código abierto (VS Code, Git, MySQL) = 0€.
-   **Total Estimado:** 7.350€.

---

## 4. Análisis y Diseño Técnico

### 4.1 Especificación de Requisitos
#### Funcionales (RF)
-   **RF-01:** Entrega de mensaje aleatorio al interactuar con el botón principal.
-   **RF-02:** Registro de usuario con validación de email y contraseña cifrada.
-   **RF-03:** Flujo obligatorio de 2FA para el acceso a la cuenta.
-   **RF-04:** Sistema de reacciones (10 tipos de emoticonos) con validación por usuario/cookie.
-   **RF-05:** Panel de administración con capacidad de suspensión de usuarios y eliminación de mensajes.

#### No Funcionales (RNF)
-   **RNF-01 Seguridad:** Las contraseñas se almacenan mediante `PASSWORD_BCRYPT`.
-   **RNF-02 Rendimiento:** Menos de 1s para la carga inicial de la aplicación.
-   **RNF-03 Compatibilidad:** Soporte para Chrome, Firefox, Safari y dispositivos móviles.

### 4.2 Diseño de la Base de Datos
El esquema relacional consta de 5 tablas principales vinculadas mediante claves foráneas con política `ON DELETE CASCADE`. Destaca la tabla `reacciones` que utiliza una clave única compuesta (`id_mensaje`, `id_usuario`) para evitar la duplicidad de votos, y un campo `cookie_id` para usuarios no registrados.

---

## 5. Implementación de Detalle

### 5.1 Algoritmo de Mensaje Aleatorio
La consulta SQL utilizada en `get_random_msg.php` selecciona un mensaje visible mediante `ORDER BY RAND() LIMIT 1`. Para optimizar este proceso en volúmenes altos, se ha implementado un filtrado previo en la aplicación para asegurar que los mensajes reportados no entren en el "pool" de selección.

### 5.2 Seguridad y Autenticación 2FA
El sistema utiliza sesiones efímeras para el proceso de login. Al introducir credenciales correctas, se genera un token de 6 dígitos almacenado en `$_SESSION`. El envío se realiza mediante una conexión segura TLS a un servidor SMTP. El acceso al tablero (`dashboard.php`) está protegido por un middleware que verifica la variable de sesión `authenticated_2fa`.

---

## 6. Manuales de Referencia

### 6.1 Manual del Administrador
El administrador tiene acceso a una interfaz exclusiva (`admin.php`) donde se listan de forma cronológica los mensajes que han recibido denuncias. Puede optar por:
-   **Descartar:** El mensaje se mantiene y se limpian los reportes.
-   **Eliminar:** El mensaje desaparece de la vista pública.
-   **Suspender Usuario:** El autor pierde acceso a su cuenta permanentemente.

### 6.2 Manual de Despliegue Técnico
1.  Servidor Linux (Debian 11/12 recomendado).
2.  Instalación de stack LAMP (Apache, MariaDB, PHP 8.2).
3.  Habilitar `mod_rewrite` en Apache para soportar `.htaccess`.
4.  Cargar `schema.sql` en la base de datos de producción.
5.  Configurar el entorno en `.env`.

---

## 7. Análisis de Riesgos
-   **Riesgo de Inyección:** Mitigado mediante el uso sistemático de `Prepared Statements` en todas las consultas mysqli.
-   **Riesgo de Exceso de Reportes (Spam):** Se ha limitado la capacidad de reportar a una vez por usuario/mensaje.
-   **Riesgo de Disponibilidad:** El uso de PHP nativo reduce la carga de memoria, permitiendo que el servidor maneje más conexiones simultáneas que un framework node.js pesado.

---

## 8. Conclusiones y Trabajo Futuro
El proyecto ShootStars ha demostrado ser una plataforma viable y segura. El cumplimiento de los objetivos académicos ha sido satisfactorio, destacando especialmente la integración del sistema de 2FA y la gestión de bases de datos.

**Trabajo Futuro:**
-   Desarrollo de una versión nativa para iOS y Android.
-   Implementación de cifrado de extremo a extremo para los mensajes.
-   Uso de IA para la detección automática de contenido tóxico antes del reporte humano.

---

## 9. Bibliografía
-   "Modern PHP: New Features and Good Practices", Josh Lockhart.
-   "Learning PHP, MySQL & JavaScript", Robin Nixon (O'Reilly).
-   Documentación oficial de MDN Web Docs sobre la API Fetch.
