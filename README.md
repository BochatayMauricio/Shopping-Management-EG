# 🛍️ Shopping Rosario - Sistema de Gestión de Promociones

Sistema web de gestión de promociones y tiendas para centros comerciales. Permite a los administradores gestionar locales, promociones y noticias, mientras que los clientes pueden explorar ofertas y canjear promociones según su categoría.

## 📋 Tabla de Contenidos

- [Tecnologías](#-tecnologías)
- [Requisitos](#-requisitos)
- [Instalación](#-instalación)
- [Credenciales de Acceso](#-credenciales-de-acceso)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Funcionalidades](#-funcionalidades)
- [Base de Datos](#-base-de-datos)

---

## 🚀 Tecnologías

| Tecnología       | Versión | Uso                             |
| ---------------- | ------- | ------------------------------- |
| **PHP**          | 7.4+    | Backend y lógica de servidor    |
| **MySQL**        | 5.7+    | Base de datos relacional        |
| **HTML5/CSS3**   | -       | Estructura y estilos            |
| **Bootstrap**    | 5.x     | Framework CSS responsivo        |
| **XAMPP**        | 8.x     | Servidor local (Apache + MySQL) |
| **Font Awesome** | 6.x     | Iconografía                     |
| **Google Fonts** | Poppins | Tipografía                      |

---

## 📦 Requisitos

- **XAMPP** (o similar con Apache + MySQL + PHP)
- **PHP 7.4** o superior
- **MySQL 5.7** o superior
- Navegador web moderno (Chrome, Firefox, Edge)

---

## ⚙️ Instalación

1. **Clonar el repositorio** en la carpeta `htdocs` de XAMPP:

   ```bash
   cd C:\xampp\htdocs
   git clone <url-del-repositorio> Shopping-Management-EG
   ```

2. **Iniciar XAMPP** y activar los servicios:
   - Apache
   - MySQL

3. **Configurar la base de datos** en `app/Config/config.php`:

   ```php
   $hostname = "localhost";
   $username = "root";
   $password = "root";  // Cambiar según tu configuración
   $dbname = "shopping_management";
   $dbport = 3306;
   ```

4. **Acceder a la aplicación**:

   ```
   http://localhost/Shopping-Management-EG/
   ```

   > ⚡ Las tablas y datos iniciales se crean automáticamente en el primer acceso.

---

## 🔐 Credenciales de Acceso

### Administrador

| Campo          | Valor              |
| -------------- | ------------------ |
| **Usuario**    | `admin`            |
| **Contraseña** | `admin123`         |
| **Email**      | admin@shopping.com |
| **Tipo**       | admin              |

### Clientes

| Usuario    | Contraseña   |
| ---------- | ------------ |
| `cliente1` | `cliente123` |
| `cliente2` | `cliente123` |

### Dueños de Tiendas

| Usuario   | Contraseña  | Email                |
| --------- | ----------- | -------------------- |
| `tienda1` | `tienda123` | tienda1@shopping.com |
| `tienda2` | `tienda123` | tienda2@shopping.com |

---

## 📁 Estructura del Proyecto

```
Shopping-Management-EG/
├── Index.php                    # Punto de entrada (splash screen)
├── README.md                    # Documentación del proyecto
│
├── app/                         # Lógica de negocio (Backend)
│   ├── Config/
│   │   └── config.php           # Configuración y migraciones BD
│   ├── controllers/             # Controladores MVC
│   │   ├── contact.controller.php
│   │   ├── login.controller.php
│   │   ├── news.controller.php
│   │   ├── promotion.controller.php
│   │   ├── store.controller.php
│   │   └── user.controller.php
│   ├── models/
│   │   └── User.php             # Modelo de usuario
│   └── Services/                # Servicios de negocio
│       ├── alert.service.php
│       ├── contact.service.php
│       ├── login.services.php
│       ├── news.services.php
│       ├── promotions.services.php
│       ├── stores.services.php
│       └── user.services.php
│
├── assets/                      # Recursos estáticos
│   └── stores/                  # Imágenes de tiendas
│
└── public/                      # Frontend
    ├── Components/              # Componentes reutilizables
    │   ├── alert/
    │   ├── footer/
    │   └── navbar/
    ├── Pages/                   # Páginas de la aplicación
    │   ├── Contact/             # Formulario de contacto
    │   ├── Home/                # Página principal
    │   ├── Login/               # Inicio de sesión
    │   ├── News/                # Noticias y eventos
    │   ├── Promotions/          # Listado de promociones
    │   ├── Redeem Promo/        # Canje de promociones
    │   ├── Reports/             # Reportes (admin)
    │   ├── Requests/            # Solicitudes
    │   ├── Stores/              # Listado de tiendas
    │   ├── User Portal/         # Panel de usuario
    │   └── User Register/       # Registro de usuarios
    └── Shared/
        └── globalStyles.css     # Estilos globales
```

---

## ✨ Funcionalidades

### 👤 Usuarios (Clientes)

- Registro e inicio de sesión
- Explorar tiendas y promociones
- Filtrar promociones por categoría
- Canjear promociones según nivel de membresía (inicial/medium/premium)
- Ver noticias y eventos del shopping

### 🏪 Dueños de Tiendas

- Gestionar información de su local
- Crear y administrar promociones
- Ver estadísticas de sus promociones

### 🔧 Administradores

- Gestión completa de usuarios
- Administración de todas las tiendas
- Control de promociones globales
- Publicación de noticias y eventos
- Acceso a reportes y métricas
- Gestión de mensajes de contacto

---

## 🗄️ Base de Datos

### Esquema de Tablas

| Tabla              | Descripción                                    |
| ------------------ | ---------------------------------------------- |
| `users`            | Usuarios del sistema (admin, clientes, dueños) |
| `stores`           | Información de tiendas/locales                 |
| `promotions`       | Promociones activas e históricas               |
| `user_promotions`  | Relación de promociones canjeadas por usuarios |
| `news`             | Noticias y eventos del shopping                |
| `contact_messages` | Mensajes del formulario de contacto            |

### Tipos de Usuario

- **admin**: Acceso completo al sistema
- **client**: Usuario consumidor de promociones
- **owner**: Dueño/administrador de tienda

### Categorías de Cliente

- **silver**: Acceso básico a promociones
- **gold**: Acceso a promociones exclusivas
- **premium**: Acceso total + beneficios especiales

---

## 🔒 Seguridad

- Contraseñas hasheadas con `password_hash()` (bcrypt)
- Validación de sesiones PHP
- Escape de caracteres HTML para prevenir XSS
- Queries parametrizadas para prevenir SQL Injection

---

## 📝 Notas de Desarrollo

- **Zona Horaria**: America/Argentina/Buenos_Aires
- **Migraciones**: Se ejecutan automáticamente al cargar `config.php`
- **Seeders**: Los datos iniciales solo se insertan si la BD está vacía

---

## 📄 Licencia

Este proyecto es de uso educativo / interno.

---

**Desarrollado para Shopping Rosario** | v1.0.0
