-- Esquema principal del Sistema Menu.
-- Selecciona la base indicada en api/.env antes de ejecutar este archivo.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS CLIENTES (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ci VARCHAR(20) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL,
    direccion VARCHAR(250) NULL,
    telefono VARCHAR(15) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS EMPLEADOS (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ci VARCHAR(20) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS USUARIOS (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    estado BOOLEAN NOT NULL DEFAULT TRUE,
    cod_empleado INT NOT NULL,
    CONSTRAINT fk_usuarios_empleado FOREIGN KEY (cod_empleado) REFERENCES EMPLEADOS(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS PRODUCTOS (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    codBarras VARCHAR(100) NOT NULL,
    descripcion VARCHAR(100) NOT NULL,
    stock INT NOT NULL CHECK (stock >= 0),
    precio_unitario DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0),
    creado_por INT NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_productos_usuario FOREIGN KEY (creado_por) REFERENCES USUARIOS(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS PEDIDOS (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cod_cliente INT NOT NULL,
    fecha_compra DATETIME NOT NULL,
    cantidad INT NOT NULL CHECK (cantidad > 0),
    cod_empleado INT NOT NULL,
    creado_por INT NULL,
    CONSTRAINT fk_pedidos_cliente FOREIGN KEY (cod_cliente) REFERENCES CLIENTES(id),
    CONSTRAINT fk_pedidos_empleado FOREIGN KEY (cod_empleado) REFERENCES EMPLEADOS(id),
    CONSTRAINT fk_pedidos_usuario FOREIGN KEY (creado_por) REFERENCES USUARIOS(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS PEDIDO_PRODUCTOS (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cod_producto INT NOT NULL,
    cod_pedido INT NOT NULL,
    cantidad INT NOT NULL CHECK (cantidad > 0),
    precio_unitario DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0),
    descuento DECIMAL(10,2) NOT NULL DEFAULT 0.00 CHECK (descuento >= 0),
    CONSTRAINT fk_detalle_producto FOREIGN KEY (cod_producto) REFERENCES PRODUCTOS(id),
    CONSTRAINT fk_detalle_pedido FOREIGN KEY (cod_pedido) REFERENCES PEDIDOS(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS EMPLEADO_PEDIDOS (
    cod_pedido INT NOT NULL,
    cod_empleado INT NOT NULL,
    fecha DATE NOT NULL,
    PRIMARY KEY (cod_pedido, cod_empleado),
    CONSTRAINT fk_empleado_pedidos_pedido FOREIGN KEY (cod_pedido) REFERENCES PEDIDOS(id),
    CONSTRAINT fk_empleado_pedidos_empleado FOREIGN KEY (cod_empleado) REFERENCES EMPLEADOS(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
