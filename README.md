# 🛒 Sistema Ventas 2026

**Sistema Ventas 2026** es una plataforma integral de gestión comercial diseñada para optimizar los procesos de venta, compras e inventario en negocios modernos. Desarrollado con un enfoque en la velocidad, seguridad y facilidad de uso.

---

## 🚀 Características Principales

### 📦 Gestión de Inventario
- **Control de Stock**: Seguimiento en tiempo real de existencias.
- **Soporte multi-depósito**: Gestioná productos en diferentes almacenes.
- **Identificación Única**: Soporte para campos de IMEI (ideal para electrónica/celulares).
- **Ajustes y Transferencias**: Movimientos de stock rápidos y documentados.
- **Categorización**: Organización jerárquica de productos.

### 💰 Ventas y Compras
- **Facturación Rápida**: Interfaz optimizada para ventas en mostrador.
- **Gestión de Compras**: Control de entrada de mercadería y costos.
- **Anulaciones**: Gestión segura de documentos anulados.
- **Ticket/Recibo**: Generación de comprobantes para el cliente.

### 👥 Clientes y Proveedores
- **Cuentas Corrientes**: Seguimiento de deudas y saldos de clientes y proveedores.
- **Historial de Pagos**: Registro detallado de cobros y pagos realizados.

### 🏦 Tesorería
- **Cajas y Bancos**: Control de flujos de efectivo y cuentas bancarias.
- **Gastos**: Registro y reporte de gastos operativos.

### 📊 Reportes e Impresión
- **Reportes de Utilidad**: Cálculo de ganancias por venta y por producto.
- **Exportación PDF**: Generación de listados de stock, precios y reportes generales mediante Dompdf.
- **Tableros de Control**: Visualización rápida del estado del negocio.

### 🔐 Seguridad
- **Sistema de Roles (RBAC)**: Permisos granulares para administradores, vendedores y otros perfiles.
- **Auditoría**: Registro de quién realizó cada acción importante.

---

## 🛠️ Stack Tecnológico
- **Lenguaje**: PHP 8.1+
- **Framework**: CodeIgniter 4
- **Base de Datos**: MySQL / MariaDB
- **Frontend**: Bootstrap 5, DataTables, Select2
- **PDF**: Dompdf

---

## ⚙️ Instalación y Configuración

### Requisitos Previos
- Servidor Web (Apache/Nginx)
- PHP 8.1 o superior con extensiones `intl`, `mbstring`, `curl`.
- MySQL 5.7+ o MariaDB 10.3+
- Composer

### Pasos Rápidos (Windows)
1.  Cloná el repositorio en tu carpeta de servidor (ej: `www` o `htdocs`).
2.  Ejecutá el archivo **`setup.bat`** (esto instalará las dependencias y creará las carpetas necesarias).
3.  Renombrá el archivo `env` a `.env` y configurá tus credenciales de base de datos:
    ```ini
    database.default.hostname = localhost
    database.default.database = nombre_tu_bd
    database.default.username = usuario_db
    database.default.password = contraseña_db
    ```
4.  Importá el archivo de base de datos (`.sql`) incluido en la raíz o carpeta `database/`.

---

## 👨‍💻 Créditos
Desarrollado para la optimización de procesos comerciales.

---
© 2026 - Todos los derechos reservados.
