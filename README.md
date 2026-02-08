# 📋 Guía de Configuración - Página Web Armado de Tableros

## 1️⃣ ESTRUCTURA DEL PROYECTO

```
page/
├── index.html                    # Página principal
├── database.sql                  # Script de base de datos
├── .env.example                  # Variables de entorno (ejemplo)
│
├── assets/
│   ├── css/
│   │   └── styles.css           # Estilos principales
│   ├── js/
│   │   └── main.js              # JavaScript (formulario, validación)
│   └── images/                  # Imágenes del sitio
│
└── backend/
    ├── config/
    │   ├── config.php           # Configuración global
    │   └── Database.php         # Clase PDO
    └── handlers/
        ├── form-handler.php     # Procesar formulario de contacto
        └── get-contacts.php     # API para obtener contactos (admin)
```

## 2️⃣ REQUISITOS PREVIOS

- **Servidor Web**: Apache con módulo PHP 7.4+
- **PHP**: 7.4 o superior
- **Base de Datos**: MySQL 5.7+ o MariaDB
- **Extensiones PHP**: PDO, PDO_MySQL

## 3️⃣ INSTALACIÓN PASO A PASO

### Paso 1: Crear Base de Datos

```bash
# Opción A: Usar phpMyAdmin
1. Abre phpMyAdmin en tu navegador (http://localhost/phpmyadmin)
2. Copia y pega el contenido de database.sql en la pestaña "SQL"
3. Ejecuta

# Opción B: Usar línea de comandos MySQL
mysql -u root -p < database.sql
```

### Paso 2: Configurar Variables de Entorno

```bash
# Copia .env.example a .env
cp .env.example .env

# Edita .env con tus datos:
DB_HOST=localhost
DB_USER=root
DB_PASS=tu_password
DB_NAME=armado_tableros
```

### Paso 3: Configurar Servidor Web

**Para Apache (xampp, wamp, lamp):**
- Copia la carpeta `page` a `htdocs` (xampp) o `www` (otros)
- Accede a `http://localhost/page`

**Para servidor local Python:**
```bash
# Python 3.7+
python -m http.server 8000

# Luego accede a http://localhost:8000
```

### Paso 4: Permisos de Carpetas

```bash
# Linux/Mac
chmod 755 backend/
chmod 755 backend/config/
chmod 755 backend/handlers/

# Crear carpeta de logs
mkdir logs
chmod 755 logs
```

## 4️⃣ CARACTERÍSTICAS IMPLEMENTADAS

### ✅ Frontend
- **Responsive Design**: Funciona en móvil, tablet y PC
- **One-page Scroll**: Navegación suave entre secciones
- **Secciones**: Hero, Servicios, Portfolio, Contacto, Footer
- **Formulario Funcional**: Validación en JS y envío AJAX

### ✅ Backend
- **PDO + Prepared Statements**: Protección contra SQL Injection
- **Validación de Datos**: En JS y PHP
- **Guardado en Base de Datos**: Tabla de contactos
- **Sanitización**: Usando `htmlspecialchars()`
- **Logs de Acciones**: Registro de intentos y errores

### ✅ Seguridad
- **Headers HTTP Seguros**: CSP, X-Frame-Options, etc.
- **Session Security**: HttpOnly, Secure, SameSite cookies
- **No mostrar errores PHP**: En producción
- **Validación doble**: Frontend y Backend

## 5️⃣ PERSONALIZACIÓN

### Cambiar nombre de empresa
Edita en [index.html](index.html):
```html
<h1>⚡ TuEmpresa</h1>
```

Edita en [backend/config/config.php](backend/config/config.php):
```php
define('EMPRESA_EMAIL', 'tu_email@tuempresa.com');
```

### Cambiar colores
Edita [assets/css/styles.css](assets/css/styles.css):
```css
--primary: #1e40af;    /* Azul principal */
--secondary: #f97316;  /* Naranja */
```

### Agregar imágenes
- Coloca imágenes en `assets/images/`
- Reemplaza los placeholders en HTML

### Cambiar contenido de servicios
Edita la sección `SERVICIOS` en [index.html](index.html)

### Cambiar proyectos de portfolio
Edita la sección `PORTFOLIO` en [index.html](index.html)

## 6️⃣ FUNCIONALIDADES ADICIONALES

### Habilitar Envío de Emails
En [backend/handlers/form-handler.php](backend/handlers/form-handler.php):

```php
// Descomenta esta línea:
sendContactEmail($nombre, $email, $asunto, $mensaje);
```

Requiere servidor SMTP configurado.

### Agregar reCAPTCHA v3 (Recomendado)

1. Obtén claves en: https://www.google.com/recaptcha/admin

2. Añade en [index.html](index.html):
```html
<script src="https://www.google.com/recaptcha/api.js"></script>
```

3. En el formulario:
```html
<div class="g-recaptcha" data-sitekey="TU_SITE_KEY"></div>
```

### Sistema de Login Admin (Opcional)
- Existe tabla `usuarios_admin` en database.sql
- Se puede extender para panel de administración

## 7️⃣ TESTING

### Probar Formulario
1. Llena todos los campos del formulario
2. Haz clic en "Enviar"
3. Verifica en phpMyAdmin → tabla `contactos`

### Validaciones Que Funcionan
- ✅ Nombre: mínimo 3 caracteres
- ✅ Email: formato válido
- ✅ Teléfono: mínimo 10 dígitos
- ✅ Asunto: mínimo 5 caracteres
- ✅ Mensaje: mínimo 10 caracteres
- ✅ Términos: debe aceptar

## 8️⃣ PROBLEMAS COMUNES

### Error: "No se puede conectar a la base de datos"
- Verifica que MySQL está corriendo
- Verifica datos en `backend/config/config.php`
- Revisa `logs/error.log`

### Formulario no envía datos
- Abre consola del navegador (F12)
- Revisa errores en "Console" y "Network"
- Verifica archivo `form-handler.php` existe

### Emails no se envían
- Requiere servidor SMTP configurado
- Alternativa: Usar servicio como SendGrid, Mailgun

### Problemas de CORS
- Para desarrollo local, no hay problemas
- En producción, configura CORS en backend

## 9️⃣ DEPLOY A PRODUCCIÓN

### Checklist de Seguridad
- [ ] Cambiar `APP_ENV` a `'production'` en [backend/config/config.php](backend/config/config.php)
- [ ] Cambiar contraseña de MySQL
- [ ] Habilitar HTTPS/SSL
- [ ] Configurar `.htaccess` para ocultar `backend/`
- [ ] Cambiar permisos: `chmod 644 *.php`
- [ ] Crear `.env` (sin `example`)
- [ ] Habilitar reCAPTCHA
- [ ] Configurar backups automáticos de BD

### Archivo `.htaccess` (Apache)
```apache
<FilesMatch "\.php$">
    Deny from all
</FilesMatch>

# Permitir solo archivos específicos
<Files "form-handler.php">
    Allow from all
</Files>

# Redirigir HTTP a HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## 🔟 SOPORTE Y CONTACTO

Para modificaciones o consultas:
- 📧 Email: contacto@tuempresa.com
- 📞 Teléfono: +54 9 ...

---

**Versión**: 1.0  
**Última actualización**: Enero 2026  
**Autor**: Asistente IA
