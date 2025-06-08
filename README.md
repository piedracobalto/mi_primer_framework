# mi_primer_framework
una practica para generar y entender como funciona un framework basico de laravel basado en php

Ciclo de vida de la app

En xampp, hay que levantar los servidores de Apache y MySql.
En Apache tenemos dos archivos importantes a la hora de levantar el servidor:

Httpd.conf:  es el principal archivo de configuración de servidor HTTP de apache. Contiene las directivas de configuración que dan las instrucciones al servidor. Establece el ip y el puerto a conectarse al servidor, nombre del servidor, el nombre de las carpetas de documentos, errores, logs y ejecución de archivos, manejo de carpetas y archivos, manejos de los protocolos HTTP. También está configurado el virtual host (conf/extra/httpd-vhosts.conf). El archivo httpd-vhosts.conf en Apache se usa para configurar Virtual Hosts, permitiendo alojar múltiples sitios web en un mismo servidor con diferentes dominios o puertos. (En Windows se debe agregar las ip y el dns para asociar la configurar los dominios en el archivo hosts). También sirve para ejecutar un router (como web.php), donde si no existe un archivo o carpeta se puede redirigir siempre a un archivo (por ejemplo un archivo que emula un mensaje de error 404 o directamente al index.php).

En httpd-vhost.conf se configura el servidor múltiples sitios web en un mismo servidor, permitiendo que cada uno tenga su propia configuración, dominio, directorio raíz, logs, etc.
Un archivo de Virtual Host le indica a Apache cómo manejar las solicitudes que llegan a un dominio o subdominio específico. Esto incluye:
•	Qué carpeta del servidor usar como raíz del sitio (DocumentRoot)
•	Qué dominio o subdominio está asociado a esa configuración (ServerName, ServerAlias)
•	Dónde guardar los archivos de log (ErrorLog, CustomLog)
•	Qué permisos o configuraciones aplicar al sitio
•	Habilitar HTTPS si se usa SSL
________________________________________
Ejemplo básico de Virtual Host
apache
CopiarEditar
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/mi_primer_framework/public"
    ServerName localtest

    ErrorLog ${APACHE_LOG_DIR}/ejemplo_error.log
    CustomLog ${APACHE_LOG_DIR}/ejemplo_access.log combined
</VirtualHost>


El archivo httpd.conf en Apache es el archivo de configuración principal del servidor web. Su ubicación y uso dependen del sistema operativo y la distribución de Apache que estés utilizando.
puedes configurar:
✅ Virtual Hosts (múltiples sitios en el mismo servidor)
✅ Módulos de Apache (LoadModule)
✅ DocumentRoot (directorio donde están los archivos del sitio)
✅ Configuración de logs (ErrorLog, CustomLog)
✅ Configuración de seguridad


El archivo .htaccess (Hypertext Access) es un archivo de configuración de Apache que permite modificar la configuración del servidor en directorios específicos sin necesidad de modificar httpd.conf.
¿Para qué se usa .htaccess?
✅ Redirecciones (Redirect, RewriteRule, RewriteCond)
✅ Protección de directorios (AuthType Basic, Require valid-user)
✅ Bloqueo de IPs (Deny from, Allow from)
✅ Reescritura de URLs (mod_rewrite)
✅ Compresión y cacheo (mod_expires, mod_deflate)
✅ Evitar listado de archivos (Options -Indexes)
En Laravel, .htaccess se usa comúnmente para redirigir todas las solicitudes al public/index.php, permitiendo que el framework maneje las rutas. 

El .htaccess permite configurar que el index.php sea el archivo principal o la ruta raíz que permitiendo que  cualquier solicitud que no corresponda a un archivo o directorio físico existente será redirigida a index.php. Si quiero acceder a otro archivo o recurso por fuera de index.php, apache 


Luego, en el archivo .hosts establecemos una ip y el nombre del dominio (que debe coincidir con el ServerName del archivo de apache en el virtual host (conf/extra/httpd-vhosts.conf).

Tanto php.ini, .htacess , Httpd.conf y los permisos de de archivos del sistema operativo permiten acceder o no a los archivos.

Al ir al localhost irá a index.php que tiene tres instrucciones: 
La importación de funciones globales (dd,redirectTo y e)
Importación del archivo bootstrap.php que  genera una instancia de la aplicación
Y la ejecución del de la aplicación. 

En el archivo bootstrap.php se instancia la app o el service container (una clase que permite hacer inyección de dependencias o clases, es decir, poder llamar a las clases concretas sin necesidad de instanciarlas) y se le pasa un parámetro string que es el path que contiene un vector o array con todas las definiciones que necesita la app (cómo instanciar los objetos en la inyección de dependencias o si necesita ser un singleton, etc)
El objeto App va a instanciar un objeto Router y Container. En el objeto container se guardan todas las definiciones que estaban en el vector que se le insertó al parámetro del constructor de la App.
Además instancia las variables de entorno y registra las rutas y middlewares de la app.
Finalmente se utiliza el método run() de la app que se ejecuta el método $this->router->dispatch()  que seria ir al recurso según la url que se estableció. Recorre la ruta para ver si coinciden la url con el recurso a apuntar. Cuando ocurre eso, verifica que la clase y la función que tiene el controlador sea validos. Una vez validados, el service container vera si la clase que trajo el controller (generalmente es un controller) está instanciada esa clase o requiere hacerla.  
Luego se agregan todos los middlewares que tiene route y se recorren para verificar si los middlewares están instanciados o los tiene que instanciar y luego ejecutarlos para ver si tienen alguna restricción. Si pasa todos los middlewares, se ejecuta el método del controlador con sus parámetros. 
En el caso de que no coincida la url con algún recurso, el método dispatchNotFound que instancia o ejecuta el objeto controller y la función notFound() que lo lleva a la vista not-found.php

Ahí termina el ciclo de vida de la app.
