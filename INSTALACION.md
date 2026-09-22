# Sistema de Información para la Gestión Administrativa de un Centro de Salud
Electiva Profesional II — UAJS · Laravel 12 + Blade + Tailwind + MySQL

---

## 1. Qué debes instalar (equipo desde cero, Windows)

| Programa | Para qué sirve | Dónde se descarga |
|---|---|---|
| **Laragon (Full)** | Trae PHP 8.2+, MySQL, Apache y Composer en un solo instalador | laragon.org/download |
| **Visual Studio Code** | Editar el código | code.visualstudio.com |
| **Node.js LTS** *(opcional)* | Solo si quieres compilar los estilos con Vite | nodejs.org |

Con **Laragon** ya tienes todo lo necesario. Si prefieres instalar por separado:
PHP 8.2 o superior (con las extensiones `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `curl`, `xml`), MySQL 8 o MariaDB, y Composer.

Extensiones recomendadas de VS Code: *PHP Intelephense*, *Laravel Blade Snippets*, *Laravel Extra Intellisense*.

---

## 2. Poner el proyecto a funcionar

1. Descomprime el proyecto en `C:\laragon\www\electivaii`.
2. Abre **Laragon** y presiona **Start All** (arranca Apache y MySQL).
3. Entra a **Menú → MySQL → phpMyAdmin** (o Laragon → Database) y crea una base de datos llamada:

   ```
   electivaii
   ```
   (cotejamiento `utf8mb4_unicode_ci`)

4. Abre la terminal dentro de la carpeta del proyecto (en Laragon: clic derecho → *Terminal*) y ejecuta:

   ```bash
   composer install          # solo si borraste la carpeta vendor
   php artisan key:generate  # genera la APP_KEY
   php artisan migrate:fresh --seed
   php artisan serve
   ```

5. Abre en el navegador: **http://127.0.0.1:8000**

> Si tu MySQL tiene contraseña de root, edita el archivo `.env` y coloca `DB_PASSWORD=tu_clave`.

### Usuarios de prueba (contraseña `password123`)

| Correo | Rol | Qué puede hacer |
|---|---|---|
| admin@hospital.com | Administrador | Todo, incluida la eliminación definitiva |
| operador@hospital.com | Operador | Registrar y editar; no puede eliminar |
| juan.perez@hospital.com | Médico | Ver su horario, sustituciones, vacaciones y pacientes |
| roberto.diaz@hospital.com | Paciente | Ver su médico asignado y horario (solo lectura) |

---

## 3. Estilos (Tailwind)

El proyecto funciona **sin instalar Node**: si no hay compilación, las vistas cargan Tailwind desde CDN.
Si quieres los estilos compilados (recomendado para la entrega final):

```bash
npm install
npm run build      # o "npm run dev" mientras desarrollas
```

---

## 4. Estructura de lo implementado

```
app/Models/                Doctor, Employee, Patient, Schedule,
                           Substitution, DoctorVacation, EmployeeVacation, User
app/Http/Controllers/      un controlador por módulo + Auth\LoginController,
                           DashboardController y ReportController
app/Http/Middleware/       EnsureUserHasRole (permisos por rol)
routes/web.php             login, dashboard, CRUD de cada módulo y reportes
resources/views/           layout, login, dashboards por rol y vistas de cada módulo
database/migrations/       tablas del modelo relacional + vínculo usuario-médico/paciente
database/seeders/          datos de ejemplo (médicos, empleados, pacientes, horarios...)
lang/es/                   mensajes de validación en español
```

### Módulos disponibles
- Gestión de médicos (titular / interino / sustituto, alta y baja)
- Gestión de empleados no médicos (ATS, ATS de zona, auxiliar, celador, administrativo)
- Gestión de pacientes con médico asignado
- Horarios de consulta (con validación de franjas cruzadas)
- Sustituciones entre médicos (relación N:M autorreferenciada)
- Vacaciones de médicos y de empleados
- Reportes: sustituciones activas, horario y pacientes por médico, vacaciones planificadas
  (también disponibles en JSON: `/reports/active-substitutions`, `/reports/doctor/{id}/schedule`, etc.)

---

## 5. Comandos útiles

```bash
php artisan serve                 # levantar el servidor
php artisan migrate:fresh --seed  # recrear la base de datos con datos de ejemplo
php artisan route:list            # ver todas las rutas
php artisan optimize:clear        # limpiar cachés si algo no se refleja
```

## 6. Problemas frecuentes

| Error | Solución |
|---|---|
| `SQLSTATE[HY000] [1049] Unknown database` | No creaste la base `electivaii` en MySQL |
| `Access denied for user 'root'` | Ajusta `DB_USERNAME` / `DB_PASSWORD` en `.env` |
| `No application encryption key` | Ejecuta `php artisan key:generate` |
| Pantalla sin estilos | Revisa tu conexión (Tailwind CDN) o ejecuta `npm run build` |
| Cambios que no aparecen | `php artisan optimize:clear` |
