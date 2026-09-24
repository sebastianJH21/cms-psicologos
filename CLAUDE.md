# Proyecto: PsicoCMS

## Rol del agente:
Desarrollador web con 12 años de experiencia.

---

## Objetivo general:

Crear una aplicación web (CMS) para psicologas, donde puedan:

- Tener una web administrable.
- Elegir entre varios temas visuales.
- Tener blog.
- Gestionar reservas de citas.
- Tener un panel de administración.
- Gestionar pacientes.
- Gestionar historias clinicas.
- Gestionar disponibilidad y calendario.
- Administrar toda la información publica de la web.

El objetivo es cubrir el flujo de trabajo completo de una psicologa independiente.

Todo se podrá administrar desde un panel privado.

---

## Consideraciones generales:

Estas reglas aplican SIEMPRE a todas las fases y funcionalidades:

- Protección de rutas.
- Validación de solapamientos.
- Priorizar secillez, que todo sea intuitivo y facil de entender.
- Priozar buenas practicas y seguridad.
- Mostrar mensajes de confirmación.
- Si no existen datos en una seccion, mostrar un "empty state" agradable.
- Usar Font Awesome (tenemos la fuente en concreto para usar en la carpeta "tema-visual-base/assets/fonts") 
- Todas las acciones del dashboard requieren autenticación.
- Todas las urls del panel deben comenzar por: /panel-psicologa
- Todas las funcionalidades deben ser totalmente funcionales.
- No romper funcionalidades anteriores.
- Mantener la consistencia visual en todo el dashboard.
- Mantener coherencia responsive.
- Priorizar UX.
- Priorizar reutilizacion de componentes.

---

## Arquitectura general:

### Parte publica:

La parte pública incluirá:

- Homepage
- Sobre mí
- Servicios
- Especialidades
- Blog
- Preguntas frecuentes
- Sistema de reserva de citas
- Contacto

### Dashboard privado:

El dashboard permitirá:

- Gestionar citas.
- Gestionar pacientes.
- Gestionar historias.
- Gestionar blog.
- Gestionar preguntas frecuentes.
- Gestionar servicios.
- Gestionar especialidades.
- Gestionar temas visuales.
- Gestionar imagenes.
- Gestionar configuraciones de la web.
- Gestionar disponibilidad.
- Gestionar frases publicas.
- Gestionar notificaciones por email.
- Gestionar redes sociales.
- Gestionar el perfil privado.


---

## Funcionalidades de la aplicación:

### FASE 1:
- Asistente de instalación donde se rellenan en un inicio los datos más importantes de la psicologa:
    - Crear la base de datos automaticamente con los datos de nuestro servidor y nuestra conexión.
    - Nombre y apellidos del / la psicologa.
    - Email, numero de telefono y contraseña (para hacer el login con esos 3 datos, unico y privado para la psicologa, no habrá multiples usuarios, ni registro, más allá de la instalación inicial)
    - Rellenar la información básica de la psicologa, para la web (nombre y apellidos, frase gancho o eslogan, numero de colegiado, numero de telefono para citas, email para citas, servicios principales, sobre mi, tipos de especialidades que sabe o hace, planes y precios (online y presencial), horarios y disponibilidad, direccion y lugar de consulta)
    - Subir foto de la psicologa, preferiblemente sin fondo (indicarlo)
    - Todo estos datos luego serán modificables y ampliables en el dashboard.
    - Seleccion de tema o plantilla visual (habrá 5 para elegir, basate en el que ya tenemos en la carpeta "tema-visual-base", ese será practicamente identico, pero basate en el para crear más).
    - Cuando el asistente termine, llegaremos al dashboard de administración.

- Añadido tras la auditoría del código (ya implementado):
    - Botón "Probar conexión" a la base de datos (AJAX) antes de continuar.
    - Marca de instalación (`storage/app/installed.lock`) y middlewares que bloquean el asistente una vez instalado.
    - Creación robusta de `storage:link` y login automático al terminar.

### FASE 2:
- Panel de aministración privado:
    - Login seguro, con Email, numero de telefono y contraseña.

    - Será obligatorio introducir los 3 datos y debe existir la opción de persistir el login.

    - Usa el metodo más adecuado para el login y la autenticacion segura pero que no se pase de complejo. Y que la contraseña esté bien cifrada.

    - Login de la psicologa en la url: /acceso-psicologa


- Añadido tras la auditoría del código (ya implementado):
    - Límite de intentos de login por usuario e IP y registro de intentos fallidos.
    - Recuperación de contraseña solo en entorno local (`/recuperar-pwd`).

### FASE 3:

- Dashboard en la url: /panel-psicologa (todas las urls de dentro del dashboard irán a partir de esta y todas requieren autenticación de la psicologa, al igual que cualquier acción que hagamos en el backend relacionado con el panel de administración)

- Layout estructura y menú del dashboard.

- El dashboard debe quedar muy simple, y debe tener un menú lateral izquierdo donde se agrupen las cosas de la gestion de la web publica y las configuraciones para no tener mucho lio. Ciertos elementos del menú serán desplegables (igual que en wordpress para agrupar cosas que tienen sentido que estén agrupadas) y las opciones más importantes para la gestíon de la psicologa deben tener su elemento del menú para un acceso mas rápido.



- Añadido tras la auditoría del código (ya implementado):
    - Componentes comunes del panel: mensajes flash, modal de confirmación propio, breadcrumbs, paginación y sidebar responsive.

# FASE 4:
- Dentro del panel se podrá:
    - Pagina de inicio con un resumen de todo y estadisticas básicas (inicialmente con datos de prueba, cuando se completen el resto de fases ya aparecerán datos reales).


    - Configuración de disponibilidad de la profesional (online y presencial):
        - Debemos tener un campo de duracion de las sesiones (en minutos) (un campo para la duracion de la sesiones tanto online como presenciona, uno para cada una)
    
        Teniendo eso en cuenta:
            - Debemos tener un selector de hora de entrada y hora de salida (maxima)
            - Debemos tener una semana de 7 dias con la posibidad de marcar la disponibilidad de horas marcandolas dando click.
            - Y un boton de guardar, para dejar asignada la disponibilidad que luego se usará en la parte publica para que los pacientes puedan reservar cita.
            - En esta sección tambien habrá una palomita o un checkbox deslizante para marcar el "modo vaciones" y asi poder parar el sistema de citas.

        - Funcionalidad descanso entre sesiones tanto presencial como online y que ese tiempo se tenga en cuenta para los huecos de disponibilidad. 

            El descanso entre sesiones debe ser configurable en el dashboard y se puede configurar, activar y desactivar en la sección de disponibilidad del dashboard.
            Cuando hay tiempo de descanso entre sesiones configurado y activado, ese tiempo se "suma" al tiempo disponible de cada uno de los "huecos" disponibles que hay para reservar por parte de los pacientes (y se debe tener en cuenta en el Formulario público de reservas y en las diferentes zonas del dashboard donde se usa la disponibilidad para añadir o modificar citas)
            Si por ejemplo mis citas online duran 50 minutos y configuro 10 minutos de descanso entre sesiones. Ahora mis huecos de disponibilidad para configurar son de 60 minutos. Por tanto los pacientes por ejemplo pueden reservara las 9:00h, a las 10:00h, y así consecutivamente.

            Si mis citas presenciales duran 50 minutos y no tengo descanso entre sesiones. Ahora mis pacientes a nivel presencial pueden reservar a las 9:00h, a las 9:50h y así consecutivamente.
            Y además esto se debe tener en cuenta cuando añado manualmente una cita, o la modifico manualmente.
            Además cuando hago un cambio en mi duranacion de las sesiones o el tiempo de descanso entre sesiones, se debe avisar a la psicóloga de que debe volver a configurar y marcar sus huecos de disponibilidad semanales tanto online como presenciales.

        - Funcionalidad para añadir periodos de vacaciones:
            - Se podrán añadir varios periodos de vacaciones (fecha de inicio y fecha de fin)
            - Los campos de fecha e inicio serán un selector de fecha de html5.
            - Los periodos de vacaciones se podran borrar y añadir (tantos como queramos).
            - En el calendario de reservas publico (en el resto de sitios donde se use la disponibilidad de la psicologa) los dias que coincidan con esos periodos de vacaciones estarán "bloquedos" para que no se pueda reservar cita en esos rangos de fechas. 
            - Todos estos cambios se debe tener en cuenta en el Formulario público de reservas y en las diferentes zonas del dashboard donde se usa la disponibilidad para añadir o modificar citas.
            - Esta funcionalidad irá aparte del "Modo vacaciones" general (que se puede activar o desactivar y ya está indicado)
            - El panel de "Modo vacaciones" y el .panel de "Periodos de vacaciones" estarán uno al lado del otro. 

    - Gestionar todas las citas, podiendo añadir nuevas, editar y eliminarlas. Se podrán visualizar en una seccion con un listado paginado con diferentes filtros y tambien en el calendario.


- Añadido tras la auditoría del código (ya implementado):
    - Lista de "primeros pasos" en el inicio con porcentaje de avance.
    - Horario de disponibilidad en dos franjas (mañana y tarde).
    - Mensaje personalizado del modo vacaciones y días de antelación reservables.
    - Filtros de citas por AJAX (próximas/pasadas, modalidad, estado, fechas y texto), cambio rápido de estado y botón para confirmar la cita por WhatsApp.
    - Selector de huecos disponibles en el formulario de cita.

### FASE 5:
- Dentro del panel de administración:
    - Calendario con las citas del sistema de reservas de citas y posibilidad de añadir manualmente (tipo Google Calendar). Si hay algun plugin o libreria de javascript famosa y recomendable para esto, https://calendarjs.com/ puede ser una buena opción. Adaptalo a nuestro sistema.

- Añadido tras la auditoría del código (ya implementado):
    - Eventos extra en el calendario (no citas) con color, que se pueden crear, editar y borrar.
    - La vista elegida del calendario (mes/semana/día) se recuerda y hay leyenda de colores.

# FASE 6:
- Dentro del panel de administración:
    - Gestion del Blog. Con su crud y a cada articulo se le puede subir una imagen y se le puede vincular una categoria (que se pueden administrar, aparte de las creadas por defecto (crea las mas adecuadas))

    - Todos los campos de texto grandes en el dashboard deben tener incluido algun editor de texto wysiwyg para hacer agradable la edición (por ejemplo: https://xdsoft.net/jodit/)

    - Copia la libreria dentro del proyecto, descargala del cdn. Esa info de la instalación y el uso general de la herramienta, la puedes conseguir desde aqui: https://xdsoft.net/jodit/docs/docs/getting-started.html

    y en concreto el cdn para descargar la libreria de este editor wysiwig es este:
    https://cdnjs.cloudflare.com/ajax/libs/jodit/4.7.6/es2021/jodit.min.css 
    https://cdnjs.cloudflare.com/ajax/libs/jodit/4.7.6/es2021/jodit.min.js

- Añadido tras la auditoría del código (ya implementado):
    - Estados del artículo (borrador/publicado/archivado), fecha de publicación y campos SEO (meta title y meta description).
    - Subida de imágenes dentro del editor Jodit y saneado del HTML con HTMLPurifier.

# FASE 7:
- Dentro del panel de administración:
    - Gestión de pacientes con los datos que pusieron al pedir cita (consultar la funcionalidad a desarrollar en la parte publica), con la posibilidad de añadir manualmente nuevos pacientes, editar los que hay para ampliar datos e información (mete los necesarios). El objetivo es que los pacientes al pedir cita mediante el sistema de reservas automaticamente se creen en este sistema de gestion, con los datos que ellos introdujeron, lo mas importante e obligatorio es el numero de telefono, ya que se usará como identificador de los mismos (clave primaria), aparte del id que tenga ese paciente en la bbdd. Además, si por ejemplo la psicologa a agendado una cita con el paciente por telefono, email, whatsapp o en persona, podrá añadir este paciente y su cita de forma manual.

    - Al crear una cita manualmente, implementar un buscador de pacientes en el campo del nombre del paciente y al darle click que se rellenen los datos de la cita. Si el paciente al cual le estamos dando cita no existe en la tabla de pacientes, crearlo a la vez que creamos la cita y vincular esa cita con ese paciente.

- Añadido tras la auditoría del código (ya implementado):
    - Campo DNI del paciente, papelera con restauración e historial de citas de cada paciente.

 ### FASE 8:
 - Dentro del panel de administracion:
    - Gestión de historias (seguimiento de sesiones del paciente con posibilidad de escribir y subir fotos o pdfs). El objetivo de esto es que cada paciente, tenga su historia o su ficha, y la psicologa pueda (despues de cada sensión de terapia que tenga con el paciente), escribir en su historia acerca de la sesión de hoy, subir fotos o documentos pdf escaneados con las anotaciones de la terapia de ese dia.

- Añadido tras la auditoría del código (ya implementado):
    - Listado general de historias con búsqueda.
    - Adjuntos de las historias guardados en disco privado y servidos solo con sesión iniciada.

### FASE 9:
- Dentro del panel de administracion:
    - Documentos de política de protección de datos (configuración de plantilla y botón de generar pdf con los datos del paciente):
        - Pagina de configuracion de la plantilla con editor wysywig (boton para descargar plantilla vacia en pdf)
        - En la pagina del detalle del paciente, a parte de poder meter mas info de el, tendremos un botón para descargar el pdf de la proteccion de datos ya relleno con sus datos.

- Añadido tras la auditoría del código (ya implementado):
    - Variable `{{psicologa_colegiado}}` en la plantilla de protección de datos.

### FASE 10:
- Dentro del panel de administracion:
    - Gestión de preguntas frecuentes para los pacientes

- Añadido tras la auditoría del código (ya implementado):
    - Reordenar las preguntas frecuentes arrastrando.

### FASE 11:
- Dentro del panel de administracion:
    - Configuración de toda la información que se muestra publicamente y que anteriormente se rellenó "provisionalmente" en el asistente.


- Añadido tras la auditoría del código (ya implementado):
    - Latitud y longitud de la consulta para el mapa.
    - Selector visual de iconos en servicios y especialidades.

### FASE 12:
- Dentro del panel de administracion:
    - Gestión de temas con 5 plantillas diferentes, simplemente se podrán seleccionar y automaticamente se activará una nueva apariencia en la web. Debe existir un boton que ponga ¿Quieres un diseño personalizado? Pidemelo aquí. Y que lleve aqui: https://victorroblesweb.es/contacto

    - Los temas se podrán seleccionar en formato landing (pagina larga) o multipaginas (diferentes secciones navegables).

    - Al darle click al tema se podrá ver una pequeña previsualización con los datos que ha rellenado la psicologa en el asistente (abrir una pestaña nueva con la previsualización del tema)

    - Los las plantillas de los temas se guardarán en una carpeta de "themes" para facilmente poder modificarlos o añadir nuevos y que sea todo muy plug and play.

        

- Añadido tras la auditoría del código (ya implementado):
    - 11 temas disponibles en lugar de 5.
    - Assets de los temas servidos con una ruta segura (`/theme-assets/{slug}/{path}`).

### FASE 13:
- Dentro del panel de administracion:
    - Gestión de imagenes, se podrán gestionar todas las imagenes estaticas que aparecen en la parte publica de la web. (habrá huecos en los temas visuales que apareceran imagenes de prueba que aparecen por ejemplo en el "tema-visual-base", pero se podrán subir imagenes que a nivel programatico tendrian prioridad para renderizarse en la web)

- Añadido tras la auditoría del código (ya implementado):
    - Sección "Logo" para elegir imagen, icono o ninguno; se usa también como favicon.

### FASE 14: 
- Dentro del panel de administracion:
    - Opción de tema claro y tema oscuro en el panel (al marcar uno de los dos, se quedará persistente, aunque me desloguee, cierre el navegador etc):

        - Cuando le demos click al boton de tema claro/oscuro aparecerá una ventana modal, cono selector de los colores mas relacionados con la psicologia, que haya 8 colores muy aestetic para que el usuario pueda seleccionar (uno será el azul que tenemos en el dashboard), para que actue como color principal del dashboard (que se adapte toda la paleta de colores a ese tema).

        - En la ventana modal de seleccion de tema claro / oscuro del dashboard, se podrá seleccionar si queremos un tema claro o oscuro y el color primario para el dashboard y se guardará y persistirá (se quedará persistente, aunque me desloguee, cierre el navegador etc).

    - Casi todas las opciones de la web serán activables y desactivables (blog, sistema de reservas, faq, etc, porque habrá psicologas que quizas no las quieran)

    
        
- Añadido tras la auditoría del código (ya implementado):
    - 11 colores primarios en lugar de 8.
    - Activar y desactivar funcionalidades al instante (AJAX).

### FASE 15:
- Dentro del panel de administracion:

    - Crear una seccion de configuración de "Frases publicas" donde se puedan configurar todos los strings o frases que aparecen por defecto en los temas visuales para que la psicologa los pueda personalizar.
    
    - Hacer las secciones de configuración de redes sociales donde la psicologa podrá poner todos los enlaces a las diferentes redes sociales para que aparezcan en la parte publica de la web.

    - Hacer la seccion de "Email y notificaciones" (revisa siguientes fases donde explico la necesidad que tenemos, basicamente indicar un email de gmail para enviar un email a ese mismo email con gmail con las nuevas reservas).

    - En el header del dashboard poder darle click al nombre / avatar de la psicologa, para tener una sección donde podemos cambiar los datos privados de la psicologa (nombre de la psicologa, email interno/privado, telefono interno/privado, poder modificar la contraseña y poder subir un avatar).

    - Hacer el buscador general que tenemos en el header del dashboard y que al buscar nos lleve a una página especial donde nos filtre y nos encuentre las conicidencias en citas, pacientes, historias, blog, etc (se creativo y haz algo muy util para el usuario)

    - En el botón Ayuda del header del dasboard, que tenga un hover y que lleve a una sección donde expliques con un tutorial general en texto y de forma sencilla de entender como funciona el dashboard de PsicoCMS completo.

    - Añadir un boton para ir a ver la parte publica de la web en la barra lateral.

- Añadido tras la auditoría del código (ya implementado):
    - Campana de notificaciones en el header con el contador de nuevas reservas y página de notificaciones.
    - Avatar de la psicóloga guardado en privado.

### FASE 16: 
- En la parte publica:

    - Tendremos la información de la psicologa
    - Botones para pedir cita, llamar, contactar por whatsapp o mandar email.
    - Basate en la estructura de "tema-visual-base" (que tiene todas las opciones que una web de este tipo podria llegar a a tener), quedate con las secciones necesarias para nuestro caso.

    - Si la psicologa elije el tema en modo landing, será toda la web en una sola página con scroll suavizado, si no, la web tendrá secciones bien diferenciadas:
        - Inicio con una landing page agradable y con los datos más estrictamente nesarios (basate en el tipo de web de la carpeta "tema-visual-base") - en una url
        - Seccion sobre mi - en otra url
        - Seccion servicios - en otra url
        - Seccion blog - en otra url
        - Seccion preguntas frecuentes - en otra url y quizas incluida 
        - Seccion Pide cita - en otra url (dentro, en un lado tendremos toda la parte de hacer reservas, y en otro lado, con algo menos de importancia, tendremos un ¿Donde estamos?, con la direccion, y un mapa de google maps con ella, además del numero de telefono y datos de contacto)

- Añadido tras la auditoría del código (ya implementado):
    - SEO con Open Graph y JSON-LD en el layout de los temas.
    - Página pública de política de privacidad.
    - Ruta alternativa para `/storage` cuando el hosting no admite symlinks.

### FASE 17:
- En la parte publica:
    - Seccion de reservas de citas:
        - Selector para elegir si la cita es presencial u online (en función de eso, y segun la disponibilidad que la psicologa tenga configurada para sus citas presenciales y su disponiblidad online, el calendario siguiente aparecerá adaptado. Las disponibilidad que la psicologa va a tener online y presenciales son diferentes y podrá configurarlas por separado)
        - Calendario para seleccionar el dia (segun las disponibilidad que tenga la psicologa configurada, ella podrá seleccionar los dias de disponiblidad y horarios semanales)
        - El paciente podrá rellenar su nombre (obligatorio), numero de telefono (obligatorio y validar que solo se puedan poner numeros) y motivo de la consulta (opcional) (en base a esto se le creará su "ficha de paciente" cuya clave primaria e identificador principal será el numero de telefono, sin los espacios por delante y por detrás y espacios entre numeros, es decir el numero todo junto, si el paciente no lo rellená asi, se limpiará programaticamente. Aparte el paciente tendrá su id unico en la bbdd).
        - Ventana modal de consulta agendada correctamente con boton de agendar en su calendario de google calendar por ejemplo (hazlo si hay una forma simple de hacerlo sin necesidad de usar apis).
        - Enviar un email a la psicologa avisando de que tiene una nueva cita (si tiene configuradas las notificaciones por email con un correo de gmail, en la seccion de configuracion tener un formulario con los datos que necesitas y un mini tutorial para explicar a la psicologa como conseguirlos y poder hacer un envio de email sencillo con phpmailer / mailer de laravel a su propio correo)

- Añadido tras la auditoría del código (ya implementado):
    - Anti-spam en la reserva pública: campo trampa, pregunta de seguridad y límite de reservas por IP; aceptación obligatoria de la política de privacidad.
    - Máximo una cita por paciente y día.
    - El email de nueva cita se envía al propio correo con el alias `+notificaciones`.

    ### FASE 18:
    - En la parte publica:
        - Seccion de blog con los articulos paginados y su correspondiente filtrado por categorias (si la psicologa tiene activada esta seccion de blog, se podrá activar o desactivar desde el dashboard, al igual que tendrá un crud de articulos)

        - Botones de redes sociales en el footer (configurables las diferentes redes en el panel de administración)

        - Añadido tras la auditoría del código (ya implementado):
            - Paginación y filtro por categoría del blog por AJAX.
            - Artículos relacionados en el detalle y feed RSS (`/blog/rss.xml`).

### FASE 19:
- Calidad, limpieza y puesta en producción (fase añadida tras la auditoría del código):
    - Eliminar el uso de `innerHTML` en los JavaScript de los temas.
    - Eliminar código muerto y ficheros de andamiaje sin uso (rutas y vistas antiguas de temas, Vite/Tailwind, scripts de un solo uso).
    - Tests automáticos de las funcionalidades críticas (login, protección de rutas, solapamientos, disponibilidad, reservas, PDF).
    - SEO técnico: `robots.txt` que bloquee el panel, `sitemap.xml` y URL canónica.
    - `.env.example` en español y zona horaria `Europe/Madrid`.
    - README propio del proyecto (instalación, despliegue y cómo crear un tema).
    - Limitar la previsualización de temas (`?preview_theme`) a la psicóloga con sesión iniciada.


---


## Stack de tecnologia:

- HTML5
- CSS3 (nativo, basate en el codigo de la carpeta "tema-visual-base")
- JavaScript (nativo, sin frameworks)
- Lenguaje de programación backend: PHP
- Framework para PHP: Laravel
- Base de datos MySQL / MariaDB
- Aplicación web monolitica con la arquitectura de Laravel

---

## Preferencias generales:
- Todos los textos visibles en la web deben estar en español y el agente tambien debe comunicarse conmigo en Español.
- Usa todoas las imagenes de stock de "tema-visual-base" para tener algo de imagenes en las plantillas. Luego estas imagenes podran editarse o cambiarse en una sección del dashboard.

---

## Preferencias de diseño para la parte publica:

- Basate en el diseño de la carpeta "tema-visual-base"

---

## Preferencias de diseño para la parte privada:

- Crea un diseño de un dashboard visualmente agradable, sencillo de entender y muy intuitivo para una psicologa, que no son usuarios avanzados de informatica (ten en cuenta todas las funcionalidades e inspirate mucho en la carpeta "dashboard-design" que tienes en la raiz del proyecto, dentro de esa carpeta tienes un prototipo de diseño, puede tener fallos, pero la idea y el concepto es muy similar a lo que necesitamos, hay diferentes pantallas diseñadas para inspirarte en ellas e intentar imitarlas y mejorarlas. En el diseño hay ciertos datos que están mal, como el nombre del cms, algunos textos en ingles, etc, ten criterio y ten muy en cuenta las funcionalidades descritas en este documento)

---


## Preferencias de estilos:

- Colores (cada tema visual puede tener diferentes, el "tema-visual-base" ya tiene los suyos y los del panel de administración los tienes en la carpeta "dashboard-design")
- Uso de medidas en rem, usando un font-size base de 10px
- Uso de HTML5 y CSS3 nativo.
- Uso de buenas practicas de maquetación css y si es necesario usa flexbox y css grid layout.
- Que la webapp sea responsive, tanto en la parte publica, como en el dashboard.

---


## Preferencias de código:
- No mezcles el código css entre los diferentes componentes, tengo bien separado para cada tema visual y para el dashboard. Si puedes tenerlo en una carpeta de css, como hago en la carpeta "tema-visual-base", mucho mejor.
- HTML debe ser semantico.
- La parte publica debe estar completamente optimizada para SEO (a nivel codigo y buenas practicas)
- Usa siempre let o const, y no uses nunca var.
- No uses alert, confirm o prompt, todo el feedback debe ser visual en el dom.
- Toda alerta o ventana modal que aparezca debe tener el mismo estilo que la web.
- No uses innerHTML, todo el contenido debe ser insertado con appendChild o previamente creando un elemento con document.createElement
- Cuidado con olvidar prevener el default en los eventos submit o click.
- Prioriza el codigo legible y mantenible.
- Prioriza que el codigo sea sencillo de entender.
- Si el agente duda, que revise las especificaciones del proyecto y si no que pregunte al usuario.


---

## Estructura de archivos:
- carpeta "tema-visual-base" (es una maquetación web de una de las plantillas o temas visuales de la web)
- carpeta "dashboard-design" (contiene unas imagenes con un diseño provisional para el dashboard)
- CLAUDE.md
- carpeta para el proyecto de laravel (usa la estructura de archivos mas adecuada para proyectos de php y laravel)

---

## Fases del desarrollo:

- Estudia las caracteristicas del proyecto
- Crea la base de datos
- Sigue las fases del desarrollo (especificadas en la seccion de funcionalidades de este fichero CLAUDE.md) y para de trabajar despues de cada fase para poder probar lo que has desarrollado, corregir y mejorar algo si es necesario y poder continuar.
- Si crees que se puede optimizar, indicamelo en el plan de implementación.

---

## Otras consideraciones:

- Guarda el plan de implementación en un fichero plan-implementación.md en la raiz del proyecto.
- Guarda las tareas y su estado en un fichero tareas.md en la raiz del proyecto y cada vez que se cumpla una, modificalo para actualizarlas.
- Guarda cada uno de los prompt nuevos que haga en un fichero prompts.md en la raiz del proyecto (todos ordenados uno detras de otro dentro del fichero), cada vez que yo haga un prompt aparte, guardalo ahí.

---

## Modo implementación:
- Solo código, minimos comentarios, el codigo ya debe ser autoexplicativo.
- No expliques que hace el código en el chat del agente.
- Responde en español si preguntas, pero en prompts usa Ingles / Español libremente.
- Si hay ambigüedad, asume la decisión más simple que no rompa algo.