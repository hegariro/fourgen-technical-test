# Sistema de Gestión de Mascotas API

## Descripción del Proyecto

Este proyecto implementa una API RESTful para un sistema de gestión de mascotas, desarrollado con Laravel como prueba técnica. El sistema permite a los usuarios registrarse, iniciar sesión, administrar su perfil y gestionar sus mascotas. Además, integra una API externa de gatos para obtener información sobre razas y datos aleatorios.

## Características Principales

- **Autenticación de Usuarios**: Registro, login y gestión de perfil de usuario con Laravel Sanctum para la autenticación API.
- **Gestión de Mascotas**: CRUD completo para que los usuarios gestionen sus mascotas.
- **Integración API Externa**: Conectividad con una API externa de gatos para obtener información de razas y datos aleatorios.
- **Arquitectura Limpia**: Implementación siguiendo principios SOLID y buenas prácticas de Laravel.
- **Documentación API**: Endpoints documentados con PHPDoc y ejemplos de respuestas.
- **Contenedorización**: Docker y Docker Compose para facilitar el despliegue y desarrollo.
- **Pruebas unitarias**: PHPUnit para documentar cada uno de los features de la aplicación

## Requisitos Previos

- Docker y Docker Compose
- Git
- Postman o similar (para probar la API)

## Instalación y Configuración

### 1. Clonar el Repositorio

```bash
git clone https://github.com/tuusuario/nombre-repositorio.git
cd nombre-repositorio
```

### 2. Configurar Variables de Entorno

Copia el archivo `.env.example` que se encuentra en la raíz de la aplicación a `.env` y configura las variables de entorno:

```bash
cp .env.example .env
```

Edita el archivo `.env` con tus configuraciones preferidas. Los valores mínimos necesarios son:

```
# App settings
APP_ENV=local
APP_DEBUG=true

# Database settings
DB_DRIVER=mysql
DB_HOST=database
DB_PORT=3306
DB_NAME=nombre_db
DB_USER=usuario_db
DB_PASSWORD=password_db
DB_CHARSET=utf8mb4
DB_ROOT_PASSWORD=root_password
```

Adicionalmente, copia el archivo `.env.example` del directorio `backend` y crea un nuevo `.env` en el mismo directorio 

```bash
cp backend/.env.example backend/.env
```

Edita el archivo `backend/.env` con tu configuración

```
# App settings
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

# Database settings -- utiliza la misma configuración del `.env` anterior
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=backend
DB_USERNAME=root
DB_PASSWORD=cambia_esto_por_un_password_seguro_de_un_usuario_NO_root

# JWT Configuration
JWT_SECRET=cambia_esto_por_un_password_seguro
JWT_ALGO=HS256

# API Key
THE_CAT_API_KEY=cambia_esto_por_el_token_de_tu_api
```

### 3. Iniciar Contenedores con Docker Compose

Para iniciar el contenedor y ejecutar comandos iniciales de configuración utiliza el siguiente comando en la raíz del proyecto:

```bash
bash ./init-services.sh
```
Si ya has ejecutado el script inicial y solo oquieres levantar los servicios utiliza el siguiente comando:

```bash
docker-compose -f ./container-compose.yaml -env-file .env up -d
```

Cualquiera de los dos comandos anteriores iniciará tres contenedores:
- `backend`: Microservicio PHP para Laravel
- `proxy`: Microservicio para Nginx como proxy inverso
- `database`: Microservicio para MySQL

Para bajar los servicios utiliza el siguiente comando:

```bash
docker-compose -f ./container-compose.yaml down
```

### 4. Configuración del Archivo Hosts

Para acceder a la API a través de un dominio personalizado, es necesario modificar el archivo `hosts` de tu sistema:

de la hostea los microservicios, si el sistema operativo es basado en linux encontrarás el archivo en `/etc/hosts`, abrelo y agrega la siguiente línea al final del archivo

```bash
# Para sistemas Linux/Mac
sudo nano /etc/hosts

# Para Windows
# Abre el Bloc de notas como administrador y abre C:\Windows\System32\drivers\etc\hosts
```
Añade la siguiente línea al final del archivo:
```bash
127.0.0.1 fourgen.dev
```
Esta configuración te permitirá:
- Realizar peticiones REST a través del dominio seguro: `https://fourgen.dev`
- Acceder a la documentación interactiva de la API desde el navegador: [`https://fourgen.dev/docs`](https://fourgen.dev/docs)

### 5. Verificar la Instalación

Visita [https://fourgen.dev/docs](https://fourgen.dev/docs) en tu navegador para confirmar que el servidor está funcionando correctamente.

## Estructura del Proyecto

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controladores API y Web
│   │   ├── Requests/        # Form Requests para validación
│   │   └── ...
│   ├── Models/              # Modelos Eloquent
│   ├── Policies/            # Politicas de autorización de recursos
│   ├── Providers/           # Proveedores de servicios
│   ├── Repositories/        # Capa de repositorio
│   ├── Services/            # Servicios, incluido el de API externa
│   └── ...
├── database/
│   ├── factories/           # Clases para generar datos de prueba
│   ├── migrations/          # Archivos de migración de la BD
│   └── seeders/             # Datos iniciales para BD
├── routes/
│   ├── api.php              # Definición de rutas API
│   └── ...
├── test/
│   ├── Feature/             # Guarda los Feature Test
│   └── ...
└── ...
```

## Endpoints API

### Autenticación

- `POST /api/register` - Registrar un nuevo usuario
- `POST /api/login` - Iniciar sesión y obtener token
- `POST /api/logout` - Cerrar sesión (requiere autenticación)

### Gestión de Usuario

- `GET /api/user` - Obtener información del usuario autenticado
- `PUT /api/user` - Actualizar información del usuario
- `PUT /api/user/password` - Actualizar contraseña
- `DELETE /api/user` - Eliminar cuenta

### Gestión de Mascotas

- `GET /api/pets` - Listar mascotas del usuario
- `GET /api/pets/{id}` - Ver detalle de una mascota
- `POST /api/pets` - Crear una nueva mascota
- `PUT /api/pets/{id}` - Actualizar una mascota
- `DELETE /api/pets/{id}` - Eliminar una mascota
- `GET /api/pets/all` - Ver todas las mascotas (paginado)

### API Externa de Gatos

- `GET /api/cats/breeds` - Listar razas de gatos
- `GET /api/cats/random` - Obtener información aleatoria de un gato

## Uso de la API

Para usar los endpoints protegidos, debes incluir el token Bearer en las cabeceras:

```
Authorization: Bearer {tu_token}
```

Ejemplo de registro de usuario con Curl:

```bash
curl -X POST https://fourgen.dev/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Usuario Prueba",
    "email": "usuario.unico@example.com",
    "password": "s3Cur3P@ssw0rd",
    "password_confirmation": "s3Cur3P@ssw0rd",
    "birthdate": "2000-11-21"
  }'
```

## Testing

Para ejecutar las pruebas:

```bash
docker exec -it backend php artisan test
```

## Consideraciones de Seguridad

- La API usa Laravel Sanctum para autenticación mediante tokens
- Todas las peticiones de entrada son validadas con Form Requests
- Las contraseñas se almacenan usando hashing seguro
- Se implementan políticas de autorización para operaciones CRUD

## Comentarios sobre la Implementación

- Se utiliza el patrón Repositorio para separar la lógica de acceso a datos
- Se implementaron servicios para interactuar con APIs externas
- Las respuestas JSON siguen una estructura consistente
- Documentación del API usando OpenAPI/Scribe
- Tests de características implementadas para ver el informe de covertura ingresa al coverage [https://fourgen.dev/coverage](https://fourgen.dev/coverage/)

## Mejoras Potenciales

- Implementar cola de trabajos para tareas asíncronas
- Agregar caché para mejorar el rendimiento
- Implementar tests de integración

## Licencia

[MIT](LICENSE)

## Contacto

[Heiner Ríos Rodríguez] - [heinerr.rodriguez@gmail.com]
