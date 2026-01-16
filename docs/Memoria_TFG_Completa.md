# Memoria de Trabajo de Fin de Grado: ShootStars

## 0. Resumen (Abstract)

### Castellano
El presente Trabajo de Fin de Grado describe el diseño, desarrollo e implementación de **ShootStars**, una plataforma web avanzada dedicada a la mensajería efímera y anónima con un enfoque primordial en la seguridad y la experiencia del usuario. El proyecto aborda la problemática de la sobreexposición digital actual, proponiendo un sistema de interacción minimalista sustentado por un backend robusto desarrollado en PHP 8.2 y una interfaz dinámica construida exclusivamente con tecnologías nativas (Vanilla JavaScript, HTML5 y CSS3). La seguridad del sistema se garantiza mediante la implementación de un flujo de autenticación multifactor (2FA) y una arquitectura de base de datos relacional optimizada en MySQL. El resultado es una aplicación escalable, eficiente y con un alto grado de usabilidad que cumple con los estándares más exigentes del desarrollo web moderno.

### English (Abstract)
This Final Degree Project details the design, development, and implementation of **ShootStars**, an advanced web platform focused on ephemeral and anonymous messaging with a primary emphasis on security and user experience. The project addresses the contemporary issue of digital overexposure by proposing a minimalist interaction system backed by a robust PHP 8.2 backend and a dynamic interface built solely with native technologies (Vanilla JavaScript, HTML5, and CSS3). System security is ensured through the implementation of a multi-factor authentication (2FA) flow and an optimized relational database architecture in MySQL. The outcome is a scalable, efficient, and highly usable application that meets the most rigorous standards of modern web development.

---

## 1. Introducción

### 1.1 Objeto del Proyecto
El objeto central de este Trabajo de Fin de Grado es la creación de una aplicación web técnica titulada "ShootStars". Este sistema permite la difusión y recepción de mensajes de texto de forma aleatoria y anónima. El propósito técnico es doble: por un lado, gestionar de forma segura el ciclo de vida de los datos volátiles (mensajes que no buscan la permanencia) y, por otro, blindar el acceso a las funciones administrativas y de gestión de usuario mediante técnicas de criptografía y autenticación bifásica. 

Desde un punto de vista funcional, el proyecto busca redefinir la interacción social digital, eliminando los sesgos de popularidad o identidad personal que dominan las redes sociales convencionales, centrándose exclusivamente en el valor intrínseco del mensaje compartido.
---

## 2. Estado del Arte y Marco Tecnológico

### 2.1 Contexto Evolutivo de las Tecnologías Web
Para comprender la arquitectura de ShootStars, es imperativo analizar la evolución de las tecnologías web. Desde el nacimiento de la Web 1.0, caracterizada por documentos estáticos enlazados, hasta la eclosión de la Web 2.0 y las actuales *Single Page Applications* (SPA), el desarrollo ha buscado siempre la interactividad y la velocidad.

Este proyecto se sitúa en un punto de equilibrio técnico: utiliza la fiabilidad del renderizado y el procesamiento en servidor (propio de la Web tradicional) con la agilidad en cliente de las aplicaciones modernas.

### 2.2 Análisis Comparativo de Tecnologías Candidatas
Durante la fase de diseño, se evaluaron diversas alternativas para cada capa de la aplicación. El análisis de estas candidatas es vital para justificar la solución final.

#### 2.2.1 El Backend: PHP vs. Otros Entornos
*   **Node.js:** Basado en el motor V8 de Google Chrome, Node.js ofrece un modelo de E/S no bloqueante muy eficiente para aplicaciones en tiempo real (como chats). Sin embargo, su curva de aprendizaje y la gestión de dependencias vía NPM a menudo introducen vulnerabilidades y una "inflación" de código innecesaria para un sistema de mensajería asíncrona como ShootStars.
*   **Python (Django/Flask):** Python es excelente para el procesamiento de datos y la legibilidad. No obstante, el despliegue de aplicaciones Django en servidores compartidos o infraestructuras locales pequeñas puede ser más complejo debido a la gestión de entornos virtuales y procesos WSGI/ASGI.
*   **PHP 8.2 (La elección):** PHP sigue alimentando más del 75% de la web activa. La versión 8.2 ha supuesto una revolución, introduciendo el compilador JIT (*Just-In-Time*), tipado estricto y una gestión de memoria optimizada. Su integración nativa con servidores Apache y su facilidad para interactuar con bases de datos MySQL lo convierten en la herramienta más lógica para un TFG que busca estabilidad y rendimiento predecible.

#### 2.2.2 El Almacenamiento: MySQL vs. NoSQL
*   **MongoDB (NoSQL):** Las bases de datos documentales son ideales para esquemas flexibles. No obstante, ShootStars requiere una integridad referencial estricta (por ejemplo, entre usuarios, mensajes y sus reacciones). El uso de NoSQL obligaría a gestionar estas relaciones en la capa de aplicación, aumentando las líneas de código y el riesgo de inconsistencia.
*   **MySQL (La elección):** Como motor relacional, su capacidad para gestionar transacciones y asegurar que cada reacción pertenezca a un mensaje válido es fundamental. Además, la madurez de sus herramientas de administración lo hace preferido para entornos académicos.

#### 2.2.3 El Cliente: Frameworks vs. Vanilla JavaScript
*   **React/Vue/Angular:** Estos frameworks son los "estándares de facto" en la industria. Sin embargo, para una aplicación enfocada en la ligereza y la entrega de contenido aleatorio, la carga de librerías que superan los cientos de kilobytes es contraproducente.
*   **Vanilla JS (La elección):** El uso de JavaScript "puro" garantiza que el navegador no tenga que interpretar abstracciones pesadas. Se ha optado por un enfoque orientado a eventos y el uso intensivo de `fetch()` para las comunicaciones con la API, demostrando que se puede lograr una UX moderna sin dependencias externas.

### 2.3 Marco Tecnológico del Proyecto
Finalmente, el ecosistema de ShootStars se compone de:
-   **Sistema Operativo:** Debian GNU/Linux, elegido por su robustez y filosofía de software libre.
-   **Servidor Web:** Apache HTTP Server, gestionando el enrutamiento mediante ficheros `.htaccess`.
-   **Seguridad y Comunicaciones:** PHPMailer para la integración SMTP con servidores de correo profesionales.
-   **Control de Versiones:** Git, permitiendo una gestión de ramas para el desarrollo de nuevas funcionalidades (como el 2FA) sin comprometer la rama principal.

---

## 3. Metodología de Desarrollo

### 3.1 Introducción a la Metodología
El desarrollo de un proyecto de la magnitud de un TFG requiere de una disciplina metodológica que asegure el cumplimiento de los plazos y la calidad del entregable final. En ingeniería de software, la elección del ciclo de vida determina la flexibilidad del proyecto ante cambios o errores.

### 3.2 El Ciclo de Vida en Cascada (Waterfall)
Aunque este modelo es tradicional y rígido, se ha tomado como base para la definición de hitos críticos:
1.  **Requisitos:** Definición de lo que la aplicación debe hacer.
2.  **Análisis:** Cómo se estructuran esos requisitos técnicamente.
3.  **Diseño:** Creación de los planos (BD e interfaz).
4.  **Codificación:** El grueso del trabajo de programación.
5.  **Pruebas:** Verificación antes del despliegue.

### 3.3 Adaptación a Metodologías Ágiles (Agile)
Dada la naturaleza iterativa de este proyecto (donde, por ejemplo, el sistema de reacciones se refinó tras ver el funcionamiento del 2FA), se ha aplicado una filosofía Agile simplificada.
*   **Sprints de 2 semanas:** Cada ciclo de desarrollo terminaba con una "demo" funcional (por ejemplo, el módulo de login completo).
*   **Gestión de Tareas:** Se han utilizado herramientas como Trello (o el gestor de tareas integrado en el IDE) para monitorizar el progreso y gestionar el "Backlog" de funcionalidades pendientes.

### 3.4 Herramientas de Gestión y Seguimiento
Para asegurar la trazabilidad del proyecto, se han documentado todos los commits en GitHub, permitiendo una visión histórica de cómo ha evolucionado el código desde el primer "Hola Mundo" hasta la implementación final del sistema de moderación avanzada.

---

## 4. Análisis del Sistema

### 4.1 Análisis de Requisitos
El análisis de requisitos es la piedra angular del proyecto. Se han categorizado en funcionales (qué hace el sistema) y no funcionales (cómo lo hace).

#### 4.1.1 Requisitos Funcionales (RF)
*   **RF01 - Gestión de Usuarios (Registro):** El sistema debe permitir que nuevos usuarios se den de alta proporcionando un nombre de usuario único, un correo electrónico válido y una contraseña. El sistema debe cifrar la contraseña antes de almacenarla.
*   **RF02 - Sistema de Autenticación con 2FA:** Tras validar las credenciales básicas, el sistema debe detener el acceso y generar un código OTP de 6 dígitos, enviándolo al correo del usuario. El acceso solo se concederá si el código introducido coincide con el generado.
*   **RF03 - Visualización de Mensajes:** Los usuarios (sin necesidad de loguearse) deben poder ver un mensaje aleatorio del total de mensajes activos en la base de datos al realizar un clic en la interfaz.
*   **RF04 - Publicación de Mensajes:** Los usuarios registrados podrán redactar y publicar mensajes de texto. Cada mensaje tendrá un límite de caracteres para asegurar la brevedad.
*   **RF05 - Catálogo de Reacciones:** El sistema ofrecerá 10 tipos de emoticonos (Corazón, Risa, Fuego, etc.) para que los usuarios interactúen con los mensajes. Se limitará a una reacción por usuario y mensaje.
*   **RF06 - Sistema de Reportes:** Cualquier usuario podrá marcar un mensaje como inapropiado. Si un mensaje supera un número crítico de reportes, se desactivará automáticamente su visibilidad hasta ser revisado por un administrador.
*   **RF07 - Panel de Control del Usuario:** Espacio privado donde el usuario puede ver sus mensajes publicados, el número total de reacciones recibidas por cada uno y opciones para editar o eliminar.
*   **RF08 - Administración de Contenido:** El administrador debe poder ver una lista de mensajes reportados, con detalles sobre el motivo y el autor, pudiendo eliminarlos definitivamente o rehabilitarlos.

#### 4.1.2 Requisitos No Funcionales (RNF)
*   **RNF01 - Seguridad:** Uso de `password_hash()` con algoritmo BCRYPT. Protección contra SQL Injection mediante sentencias preparadas.
*   **RNF02 - Usabilidad:** Interfaz intuitiva. El usuario no debe tardar más de 3 segundos en entender cómo ver un mensaje.
*   **RNF03 - Rendimiento:** El tiempo de carga de un mensaje aleatorio (petición AJAX + consulta SQL) no debe superar los 250ms en condiciones normales.
*   **RNF04 - Escalabilidad:** El esquema de base de datos debe permitir el crecimiento hasta decenas de miles de mensajes sin degradación notable del rendimiento.
*   **RNF05 - Portabilidad:** La aplicación debe ser compatible con los navegadores modernos más utilizados (Chrome, Firefox, Safari, Edge).

### 4.2 Especificación de Casos de Uso
Para cada interacción crítica, se ha definido un flujo de trabajo. 

**Caso de Uso: Visualización de Mensaje Aleatorio**
*   **Actor:** Usuario anónimo / registrado.
*   **Precondición:** Acceso a la URL principal.
*   **Flujo Principal:**
    1. El usuario hace clic en el área de visualización.
    2. El frontend lanza una petición GET a `get_random_msg.php`.
    3. El servidor selecciona un ID aleatorio de la tabla `mensajes` donde `visible = 1`.
    4. El servidor devuelve el contenido, autor y reacciones en formato JSON.
    5. El frontend actualiza el DOM con una animación de entrada.

---

## 5. Diseño del Sistema

### 5.1 Arquitectura del Software
ShootStars sigue un modelo de **Arquitectura de Capas Disociadas**:
1.  **Capa de Presentación (Frontend):** HTML5 Semántico, CSS3 (Variables, Flexbox, Grid) y Vanilla JS.
2.  **Capa de Servicio (API Backend):** Scripts PHP que actúan como controladores de peticiones.
3.  **Capa de Datos:** Base de Datos MySQL MariaDB.

### 5.2 Diseño de la Base de Datos (Diccionario de Datos)
El diseño se ha normalizado hasta la Tercera Forma Normal (3FN). A continuación se detallan las tablas principales:

#### 5.2.1 Tabla `usuarios`
| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id_usuario` | INT (PK, AI) | Identificador único del usuario. |
| `nombre_usuario` | VARCHAR(50) | Nombre público (único). |
| `email` | VARCHAR(100) | Correo para 2FA y contacto (único). |
| `contraseña` | VARCHAR(255) | Hash BCRYPT de la clave. |
| `es_admin` | BOOLEAN | Flag de permisos de moderador. |
| `estado` | ENUM | 'activo' o 'suspendido'. |

#### 5.2.2 Tabla `mensajes`
| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `id_mensaje` | INT (PK, AI) | Identificador único del mensaje. |
| `id_usuario` | INT (FK) | Relación con el autor. |
| `contenido` | TEXT | Cuerpo del mensaje. |
| `fecha_creacion` | TIMESTAMP | Fecha y hora de publicación. |
| `visible` | BOOLEAN | Estado de moderación. |

#### 5.2.3 Tabla `reacciones`
Esta tabla es clave para la optimización. Registra la relación M:N entre usuarios/cookies y mensajes.
- `id_reaccion` (PK)
- `id_mensaje` (FK)
- `id_usuario` (FK, nulo para anónimos)
- `cookie_id` (VARCHAR para seguimiento de anónimos)
- `tipo` (Enum de las 10 reacciones)

#### 5.2.4 Tabla `reportes` (Evolucionada)
Maneja las denuncias de contenido tanto para mensajes como para ecos:
- `id_reporte` (PK, AI)
- `id_usuario` (FK)
- `id_mensaje` (FK, NULLable)
- `id_eco` (FK, NULLable)
- `motivo` (TEXT)
- `fecha` (TIMESTAMP)
- **Constraint**: `UNIQUE (id_usuario, id_mensaje)` y `UNIQUE (id_usuario, id_eco)` para evitar reportes duplicados del mismo usuario sobre el mismo elemento.

#### 5.2.5 Tabla `ecos` (Novedad)
Registra las respuestas anónimas a mensajes específicos:
- `id_eco` (PK)
- `id_mensaje` (FK)
- `id_usuario` (FK)
- `contenido` (TEXT)
- `fecha_creacion` (TIMESTAMP)
- `visible` (BOOLEAN)

### 5.3 Diseño de la Interfaz (UX UI)
Se ha seguido una estética **Dark Mode / Cyberpunk Space**. 
*   **Paleta:** Fondos en `#050014`, acentos en neón `#4cc9f0` y `#7209b7`.
*   **Tipografía:** `Orbitron` para títulos (aspecto futurista) y `Outfit` para el cuerpo de texto (alta legibilidad).
*   **Interacciones:** Micro-animaciones en botones y transiciones de opacidad al cargar mensajes para dar sensación de fluidez y "magia".


---

## 6. Implementación Detallada

### 6.1 Lógica de Backend: El Motor de ShootStars
La implementación del backend se ha realizado siguiendo un enfoque modular. Aunque no se utiliza un framework completo, se han extraído funcionalidades comunes a librerías de utilidad.

#### 6.1.1 Conexión y Gestión de Base de Datos
Se utiliza `db.php` como punto central de conexión. Implementa una función `getDBConnection()` que carga las credenciales desde el archivo `.env`. Esto asegura que las claves de acceso nunca estén "harcodeadas" en el código fuente, facilitando el despliegue en diferentes entornos de forma segura.

#### 6.1.2 Algoritmo de Selección Aleatoria
El archivo `get_random_msg.php` contiene la lógica para la visualización de mensajes. La consulta SQL selecciona un mensaje visible mediante `ORDER BY RAND() LIMIT 1`. Para optimizar esta consulta en bases de datos extensas, se ha asegurado un índice sobre la columna `id_mensaje` y se ha pre-filtrado por el campo `visible`. El resultado se encapsula en un objeto JSON que incluye no solo el texto, sino los contadores de reacciones actuales.

#### 6.1.3 Implementación del 2FA (Double Authentication)
El flujo de 2FA es el componente de seguridad más complejo. Se divide en tres fases técnicas:
1.  **Fase de Identificación (`auth/login.php`):** Tras verificar `password_verify()`, se genera un entero aleatorio entre 100000 y 999999. Este código se guarda en `$_SESSION['2fa_code']`.
2.  **Fase de Notificación (`email_helper.php`):** Se invoca a PHPMailer para enviar un correo transaccional. La plantilla utiliza estilos inline para asegurar la compatibilidad con clientes de correo.
3.  **Fase de Verificación (`verify_2fa.php`):** El usuario introduce el código. El sistema compara el valor del POST con el valor de la sesión. Solo si coinciden se activa el flag `authenticated = true`.

#### 6.1.4 El Sistema de Ecos y Moderación
Para fomentar la interacción, se ha implementado la funcionalidad de "Ecos". Cuando un usuario visualiza un mensaje, el sistema realiza una petición secundaria a `get_ecos.php` para cargar todas las respuestas previas. Mediante `create_eco.php`, los usuarios registrados pueden responder al autor del mensaje de forma anónima.

Además, para garantizar un entorno seguro, el sistema de reportes se ha extendido a los ecos. Cada respuesta cuenta con un botón de alerta (⚠) que permite a los usuarios denunciar contenido inapropiado. Los reportes se gestionan de forma centralizada por `report.php`, que monitoriza tanto mensajes como ecos, notificando al administrador automáticamente si algún elemento supera un umbral de denuncias.

### 6.2 Desarrollo del Frontend Dinámico
El frontend se ha diseñado como una "cuasi-SPA". El archivo `index.php` actúa como el marco principal, y los scripts en `js/` gestionan la carga de mensajes sin recargar la página.

#### 6.2.1 El Ciclo de Vida del Mensaje en Cliente
1. El usuario interactúa con la "Estrella" principal.
2. Se ejecuta un `Fetch` asíncrono.
3. El JSON recibido se procesa y se inyecta en el DOM mediante manipulación de nodos.
4. Se disparan animaciones CSS de opacidad y escala para mejorar el feedback visual.

---

## 7. Seguridad y Protección de Datos

### 7.1 Mitigación de Vulnerabilidades (OWASP)
1.  **SQL Injection:** Uso sistemático de Sentencias Preparadas (`mysqli_prepare`).
2.  **Cross-Site Scripting (XSS):** Escapado de datos de salida mediante `htmlspecialchars()`.
3.  **Cross-Site Request Forgery (CSRF):** Validación de origen y tokens de sesión.

---

## 8. Pruebas y Control de Calidad

### 8.1 Estrategia de Pruebas
Se ha seguido una estrategia multinivel:
-   **Pruebas Unitarias:** Validación de lógica aislada (ej. validador de emails).
-   **Pruebas de Integración:** Flujos completos (Registro -> 2FA -> Login).
-   **Pruebas de Usuario (Manual):** Verificación de la UX en diferentes dispositivos.

---

## 9. Despliegue e Infraestructura

### 9.1 Entorno de Producción
Despliegue en **Debian 12** con Apache y MariaDB. Configuración de seguridad en el servidor y optimización de permisos de archivos (`www-data`).

---

## 10. Manuales de Referencia

### 10.1 Manual del Administrador (Backoffice)
El administrador de ShootStars dispone de una interfaz privilegiada para asegurar la convivencia y calidad del contenido.

#### 10.1.1 Acceso y Seguridad
El acceso al panel de administración requiere una cuenta con el flag `es_admin = true` en la base de datos. Se recomienda que esta cuenta tenga una contraseña de alta complejidad y el 2FA activado obligatoriamente.

#### 10.1.2 Gestión de Mensajes Reportados
En la sección "Moderación", el administrador encontrará una tabla con:
-   **ID del Mensaje:** Identificador único.
-   **Contenido:** El texto denunciado.
-   **Motivo del Reporte:** En caso de que el usuario lo haya especificado.
-   **Acciones:**
    -   *Mantener:* Si el reporte se considera falso o no infringe las normas. El contador de reportes se resetea.
    -   *Eliminar:* El mensaje se marca como `visible = 0`. No se borra físicamente para mantener la trazabilidad ante posibles delitos.

#### 10.1.3 Gestión de Usuarios
El administrador puede suspender cuentas. Un usuario suspendido no podrá hacer login ni publicar mensajes, aunque sus mensajes anteriores se mantengan (a menos que se borren manualmente).

### 10.2 Manual del Usuario Final
La aplicación ha sido diseñada para ser "Zero-Learning", es decir, que no requiera aprendizaje previo.

#### 10.2.1 El Flujo de la Estrella
1.  Al entrar, el usuario presiona el botón central (la "Estrella").
2.  Un mensaje aparece con una animación suave.
3.  El usuario puede reaccionar haciendo clic en uno de los 10 iconos inferiores.
4.  Si el mensaje es ofensivo, puede pulsar el botón de Reportar.

#### 10.2.2 Registro y Perfil
Para publicar mensajes, el usuario debe registrarse. 
-   **IMPORTANTE:** Tras el registro, el primer login requerirá la introducción del código enviado al email. 
-   En su perfil, podrá ver sus estadísticas de reacciones y editar sus mensajes si contienen errores.

---

## 11. Estudio Económico y Presupuesto

### 11.1 Análisis de Costes de Personal
Se estima que el desarrollo ha sido realizado por un desarrollador Full-Stack durante un periodo de 3 meses (aproximadamente 360 horas de trabajo efectivo).
-   **Coste por hora:** 35 € (incluyendo seguridad social y gastos de autónomo).
-   **Total Personal:** 12.600 €.

### 11.2 Costes de Infraestructura (Cloud/Hardware)
-   **Servidor Dedicado (Anual):** 360 € (Instancia de alto rendimiento).
-   **Dominio .com y Certificado SSL:** 45 €/año.
-   **Total Infraestructura:** 405 €.

### 11.3 Costes de Software y Herramientas
Gracias al uso de tecnologías Open Source, el coste en licencias es cero. 
-   **PHP, MySQL, Apache, Debian:** 0 €.
-   **Visual Studio Code:** 0 €.
-   **Git/GitHub:** 0 €.

### 11.4 Resumen del Presupuesto
El coste total teórico para la puesta en marcha de ShootStars como proyecto comercial sería de **13.005 €** (más IVA).

---

## 12. Conclusiones y Trabajo Futuro

### 12.1 Conclusiones Técnicas
Tras finalizar el desarrollo de ShootStars, se ha demostrado que es posible construir una plataforma de interacción social con estándares de seguridad elevados utilizando tecnologías nativas. El sistema de 2FA ha sido el mayor reto técnico, pero su implementación exitosa garantiza la robustez del proyecto.

### 12.2 Valoración Personal
Este TFG ha permitido consolidar conocimientos de todas las áreas del desarrollo DAW, desde la administración de sistemas Linux hasta el diseño UX avanzado. La satisfacción de ver una idea convertirse en un producto funcional y seguro es inmensa.

### 12.3 Lineas de Trabajo Futuro
-   **IA de Moderación:** Detección automática de lenguaje tóxico.
-   **Animaciones WebGL:** Para una experiencia visual aún más inmersiva.
-   **PWA:** Convertir la app en una aplicación web progresiva para dispositivos móviles.

---

## 13. Bibliografía y Webgrafía
-   Lockhart, J. (2015). *Modern PHP: New Features and Good Practices*. O'Reilly.
-   Nixon, R. (2021). *Learning PHP, MySQL & JavaScript*. O'Reilly.
-   Mozilla Developer Network (MDN). *Fetch API Documentation*.
-   The PHPMailer Project. *Official Documentation*.
