-- Crear la base de datos.
IF DB_ID('Kima') IS NULL
BEGIN
	CREATE DATABASE Kima;
END
GO

-- Utilizar la base de datos.
USE Kima;
GO

-- =============================================
-- TABLAS
-- =============================================

-- Tabla de Categorías.
BEGIN
    CREATE TABLE dbo.Categorias (
        ID     INT IDENTITY(1,1) PRIMARY KEY,
        Nombre VARCHAR(50)       NOT NULL
    );

    -- Inserción de datos en la tabla Categorías.
    INSERT INTO dbo.Categorias (Nombre) VALUES
    ('Alta (Urgente)'),
    ('Media (Internedio)'),
    ('Baja');
END
GO

-- Tabla de Categorías y Requisitos.
BEGIN
    CREATE TABLE dbo.CategoriasRequisitos (
        ID     INT IDENTITY(1,1) PRIMARY KEY,
        Nombre VARCHAR(100)      NOT NULL
    );

    -- Inserción de datos en la tabla Categorías y Requisitos.
    INSERT INTO dbo.CategoriasRequisitos (Nombre) VALUES
    ('Funcional'),
    ('No Funcional'),
    ('Requerimiento Legal'),
    ('Genereal');
END
GO

-- Tabla de Categoría.
BEGIN
    CREATE TABLE dbo.Categoria (
        Categoria_ID        INT IDENTITY(1,1) PRIMARY KEY,
        TipoCategoria       VARCHAR(50)       NOT NULL,
        NombrePersonalizado VARCHAR(50)       NULL,
        CHECK (TipoCategoria IN ('Otros', 'Cosméticos', 'Productos Médicos', 'Químicos'))
    );
END
GO

-- Tabla de Categoría y Servicio.
BEGIN
    CREATE TABLE dbo.Categoria_Serv (
        ID            INT IDENTITY(1,1) PRIMARY KEY,
        Nombre        NVARCHAR(255)     NOT NULL,
        FechaCreacion DATETIME          DEFAULT GETDATE()
    );

    -- Inserción de datos en la tabla Categoría y Servicio.
    INSERT INTO dbo.Categoria_Serv (Nombre, FechaCreacion) VALUES
    ('Software', '2025-04-01 07:37:19.733'),
    ('Hardware', '2025-04-01 07:37:19.733'),
    ('Servicios', '2025-04-01 07:37:19.733'),
    ('Mantenimiento', '2025-04-01 07:37:19.733'),
    ('Test Categoria', '2025-04-13 15:05:48.237');
END
GO

-- Tabla de Estados Tickets.
BEGIN
    CREATE TABLE dbo.EstadosTickets (
        ID     INT IDENTITY(1,1) PRIMARY KEY,
        Estado VARCHAR(50)       NOT NULL UNIQUE
    );

    -- Inserción de datos en la tabla Estados Tickets.
    INSERT INTO dbo.EstadosTickets (Estado) VALUES
    ('Closed'),
    ('In Progress'),
    ('Open'),
    ('Pending');
END
GO

-- Tabla de Estados de Clientes.
BEGIN
    CREATE TABLE dbo.estados_clientes (
        id            INT IDENTITY(1,1) PRIMARY KEY,
        nombre_estado VARCHAR(50)       NOT NULL UNIQUE
    );

    -- Inserción de datos en la tabla Estados Clientes.
    INSERT INTO dbo.estados_clientes (nombre_estado) VALUES
    ('Activo'),
    ('Inactivo');
END
GO

-- Tabla de Contactos.
BEGIN
    CREATE TABLE dbo.Contactos (
        ID_Contactos      INT          PRIMARY KEY,
        Nombre            VARCHAR(50)  NOT NULL,
        Telefono          INT          NOT NULL,
        Correo            VARCHAR(100) NOT NULL,
        idioma_Traduccion VARCHAR(100) NOT NULL
    );
END
GO

-- Tabla de Tarifario.
BEGIN
    CREATE TABLE dbo.Tarifario (
        NombreServicio VARCHAR(50) PRIMARY KEY,
        NumeroTicket   INT NOT NULL,
        FechaCreacion  DATETIME NOT NULL
    );
END
GO

-- Tabla de Clientes.
BEGIN
    CREATE TABLE dbo.clientes (
        id             INT IDENTITY(1,1) PRIMARY KEY,
        nombre         NVARCHAR(255)     NOT NULL,
        email          NVARCHAR(255)     NOT NULL,
        empresa        NVARCHAR(255)     NOT NULL,
        fecha_creacion DATE              NOT NULL,
        telefono       NVARCHAR(50)      NULL,
        direccion      NVARCHAR(MAX)     NULL,
        estado_id      INT               NULL,
        cedula         VARCHAR(50)       NULL,
        ImagenPerfil   VARCHAR(255)      NULL,
        CONSTRAINT FK_clientes_estados FOREIGN KEY (estado_id) REFERENCES dbo.estados_clientes(id)
    );

    -- Inserción de datos en la tabla Clientes.
    INSERT INTO dbo.clientes (nombre, email, empresa, fecha_creacion, telefono, direccion, estado_id, cedula, ImagenPerfil) VALUES
    ('Luis Pérez', 'luis.perez@email.com', 'Innovate Solutions', '2025-02-18', '11112222222', 'Boulevard Las Américas, Alajuela', 2, '9087108752', NULL),
    ('Ana González', 'ana.gonzalez@email.com', 'MarketingPro', '2025-02-18', '8888888', 'Residencial Los Lagos, Cartago', 1, '504250864', NULL),
    ('Kevin Manuel', 'kmanuel_120@hotmail.com', 'KMSOFT', '2025-03-09', '89565936', 'Cañas Guanacaste, test 1', 2, '908710876', '686b179316e87_300-15.jpg'),
    ('Juan Hernandez', 'juan.hernandez@hotmail.es', 'KMSOFT', '2025-07-06', '89565939', 'kevin', 1, '908710875', '686b0f7e7cb24_300-17.jpg');
END
GO

-- Tabla de Usuarios.
BEGIN
    CREATE TABLE dbo.Usuarios (
        ID           INT IDENTITY(1,1) PRIMARY KEY,
        Nombre       VARCHAR(50)       NOT NULL,
        Email        VARCHAR(100)      NOT NULL UNIQUE,
        Contraseña   VARCHAR(255)      NOT NULL,
        Rol          NVARCHAR(15)      NOT NULL DEFAULT 'Usr',
        estado_id    INT               NULL,
        ImagenPerfil VARCHAR(255)      NULL,
        darkmode     BIT               NOT NULL DEFAULT 0,
        CONSTRAINT FK_usuarios_estados FOREIGN KEY (estado_id) REFERENCES dbo.estados_clientes(id),
        CHECK (Rol IN ('Admin', 'Usr'))
    );

    -- Inserción de datos en la tabla Usuarios.
    INSERT dbo.Usuarios (Nombre, Email, Contraseña, Rol, estado_id, ImagenPerfil, darkmode) VALUES
    (N'Marc Gómez', N'marc.gomez@gmail.com', N'marc1234', N'Admin', 1, N'67f629de4f6cd_300-3.jpg', 0),
    (N'Yurán Reyes', N'yuran.reyes@gmail.com', N'yuran745', N'Admin', 1, NULL, 0),
    (N'Kevin Manuel', N'kevin.elizondo@kmsoftcr.com', N'km12', N'Admin', 1, N'685c29e4e08a2_drift-diver-1.webp', 0),
    (N'sin notas', N'sinnotas@gmail.com', N'sn1234', N'Usr', 1, N'686b448936e05_300-16.jpg', 0);
END
GO

-- Tabla de Lista de Contactos.
BEGIN
    CREATE TABLE dbo.lista_contactos (
        id             INT IDENTITY(1,1) PRIMARY KEY,
        nombre         NVARCHAR(100)     NOT NULL,
        email          NVARCHAR(100)     NOT NULL,
        empresa        NVARCHAR(100)     NULL,
        fecha_creacion DATETIME          DEFAULT GETDATE(),
        telefono       NVARCHAR(50)      NULL,
        direccion      NVARCHAR(255)     NULL,
        estado_id      INT               NOT NULL,
        cedula         NVARCHAR(50)      NULL,
        servicio       NVARCHAR(100)     NULL,
        especialidad   NVARCHAR(100)     NULL,
        CONSTRAINT FK_contactos_estados FOREIGN KEY (estado_id) REFERENCES dbo.estados_clientes(id)
    );

    -- Inserción de datos en la tabla Lista de Contactos.
    INSERT INTO dbo.lista_contactos (nombre, email, empresa, fecha_creacion, telefono, direccion, estado_id, cedula, servicio, especialidad) VALUES
    ('Kevin Manuel', 'kmanuel_124@hotmail.es', 'KMSOFT', '2025-04-09 03:19:19.440', '89565936', 'Tilaran, Guanacaste', 1, '504250864', 'Desarrollador Jr', 'Software'),
    ('Juan Hernandez', 'jhernandez12222@gmail.com', 'KMSOFT', '2025-04-18 04:46:18.837', '89542438', 'Cañas 201', 1, '908710875', 'test 1', 'test 2'),
    ('Ana González', 'ana.gonzalez@email.com', 'KMSOFT', '2025-04-18 05:06:37.860', '89565930', 'Guana', 1, '9087108777', 'test servicio', 'test especialidad'),
    ('Manuel Orozco', 'manuel@gmail.com', 'KMSOFT', '2025-04-18 21:05:26.647', '87869002', 'Cañas, Gte', 1, '603010020', 'Software', 'Frontend'),
    ('Eneida', 'eneida@gmail.com', 'KMSOFT', '2025-04-18 21:07:40.847', '89551235', 'Cañas, Gte', 1, '503010020', 'Coordinación', 'ProJect Manager'),
    ('Victor Orozco test', 'victor@gmail.com', 'KMSOFT', '2025-04-18 21:15:21.980', '87052030', 'Tilaran,, Gte', 1, '508340964', 'Desarrollo', 'Software');
END
GO

-- Tabla de Historial de Contactos.
BEGIN
    CREATE TABLE dbo.Historial_Contactos (
        ID                   INT IDENTITY(1,1) PRIMARY KEY,
        ContactoID           INT               NULL,
        Accion               NVARCHAR(255)     NULL,
        NombreAnterior       NVARCHAR(255)     NULL,
        FechaAccion          DATETIME          NULL,
        Usuario              NVARCHAR(100)     NULL,
        Mensaje              NVARCHAR(MAX)     NULL,
        notificaciones_check BIT               DEFAULT 0,
        CONSTRAINT FK_Historial_ContactoID FOREIGN KEY (ContactoID) REFERENCES dbo.lista_contactos(id)
    );
END
GO

-- Tabla de Tipos de Productos.
BEGIN
    CREATE TABLE dbo.TiposProductos (
        ID                INT IDENTITY(1,1) PRIMARY KEY,
        Nombre            VARCHAR(100)      NOT NULL UNIQUE,
        Costo             DECIMAL(10,2)     NOT NULL DEFAULT 0,
        FechaCreacion     DATETIME          NOT NULL DEFAULT GETDATE(),
        Descripcion       VARCHAR(255)      NULL,
        id_categoria_serv INT               NULL,
        CONSTRAINT FK_TiposProductos_Categoria FOREIGN KEY (id_categoria_serv) REFERENCES dbo.Categoria_Serv(ID)
    );

    -- Inserción de datos en la tabla Tipos de Productos.
    INSERT dbo.TiposProductos (Nombre, Costo, FechaCreacion, Descripcion, id_categoria_serv) VALUES
    (N'Desarrollo de Software', CAST(900.00 AS Decimal(10,2)), CAST(N'2025-02-18T11:17:31.350' AS DateTime), N'Desarrollo de Software', 1),
    (N'Hardware', CAST(730.00 AS Decimal(10,2)), CAST(N'2025-02-18T11:17:31.350' AS DateTime), N'Hardware', 2),
    (N'Servicios', CAST(210.00 AS Decimal(10,2)), CAST(N'2025-02-18T11:17:31.350' AS DateTime), N'Servicios', 3),
    (N'Mantenimiento', CAST(322.00 AS Decimal(10,2)), CAST(N'2025-02-18T11:17:31.350' AS DateTime), N'Mantenimiento', 4),
    (N'Mantenimiento de equipos', CAST(6000.00 AS Decimal(10,2)), CAST(N'2025-04-01T07:58:51.550' AS DateTime), N'Limpiexa y actualización de equipos', 4);
END
GO

-- Tabla de Carpetas.
BEGIN
    CREATE TABLE dbo.carpetas (
        id             INT IDENTITY(1,1) PRIMARY KEY,
        nombre         NVARCHAR(255)     NOT NULL,
        fecha_creacion DATETIME DEFAULT  GETDATE(),
        ruta           VARCHAR(255)
    );
END
GO

-- Tabla de Archivos.
BEGIN
    CREATE TABLE dbo.archivos (
        id                 INT IDENTITY(1,1) PRIMARY KEY,
        nombre             NVARCHAR(255)     NOT NULL,
        ruta               NVARCHAR(255)     NOT NULL,
        tamaño             INT               NOT NULL,
        fecha_modificacion DATETIME          DEFAULT GETDATE(),
        carpeta_id         INT               NULL,
        CONSTRAINT FK_Archivos_Carpetas FOREIGN KEY (carpeta_id) REFERENCES dbo.carpetas(id)
    );
END
GO

-- Tabla de Tickets.
BEGIN
    CREATE TABLE dbo.Tickets (
        ID INT IDENTITY(1,1)         PRIMARY KEY,
        TipoProductoID INT           NOT NULL,
        ResponsableID  INT           NOT NULL,
        ClienteID      INT           NOT NULL,
        EstadoID       INT           NOT NULL,
        FechaCreacion  DATE          NOT NULL,
        Descripcion    TEXT          NULL,
        Documento      NVARCHAR(255) NULL,
        CreadoEn       DATETIME      DEFAULT GETDATE(),
        ActualizadoEn  DATETIME      NULL,
        FechaFin       DATETIME      NULL,
        CategoriaID    INT           NULL,
        CONSTRAINT FK_TipoProducto FOREIGN KEY (TipoProductoID) REFERENCES dbo.TiposProductos(ID),
        CONSTRAINT FK_Responsable FOREIGN KEY (ResponsableID) REFERENCES dbo.Usuarios(ID),
        CONSTRAINT FK_Cliente FOREIGN KEY (ClienteID) REFERENCES dbo.clientes(id),
        CONSTRAINT FK_Estado FOREIGN KEY (EstadoID) REFERENCES dbo.EstadosTickets(ID),
        FOREIGN KEY (CategoriaID) REFERENCES dbo.Categorias(ID)
    );

    -- Inserción de datos en la tabla Tickets.
    INSERT INTO dbo.Tickets (TipoProductoID, ResponsableID, ClienteID, EstadoID, FechaCreacion, Descripcion, Documento, CreadoEn, ActualizadoEn, FechaFin, CategoriaID) VALUES
    (1, 1, 1, 1, '2025-03-04', 'tesst 4456', NULL, '2025-03-04 03:07:21.693', '2025-04-15 01:47:40.133', '2025-03-05 00:33:48.367', 2),
    (2, 2, 2, 2, '2025-04-05', 'Deseamos recibir una cotización, de un software con los requerimientos en el documento adjunto.', NULL, '2025-04-05 00:59:26.123', NULL, NULL, 1),
    (3, 3, 3, 3, '2025-04-05', 'Sistema de manejo google Ads, según estandares de Marketing.', NULL, '2025-04-05 01:11:40.420', NULL, NULL, 2),
    (4, 4, 4, 4, '2025-04-05', 'Por favor deseamos cotización de limpieza de equipos descritos en el documento adjunto.', NULL, '2025-04-05 01:34:32.820', NULL, NULL, 2);
END
GO

-- Tabla de Comentarios Tickets.
BEGIN
    CREATE TABLE dbo.comentarios_tickets (
        ID         INT IDENTITY(1,1) PRIMARY KEY,
        TicketID   INT               NOT NULL,
        UsuarioID  INT               NOT NULL,
        Comentario TEXT              NOT NULL,
        Fecha      DATETIME          DEFAULT GETDATE(),
        FOREIGN KEY (TicketID) REFERENCES dbo.Tickets(ID),
        FOREIGN KEY (UsuarioID) REFERENCES dbo.Usuarios(ID)
    );

    -- Inserción de datos en la tabla Comentarios Tickets.
    INSERT INTO dbo.comentarios_tickets (TicketID, UsuarioID, Comentario, Fecha) VALUES
    (1, 1, 'Comentario test 1', '2025-04-01 02:32:41.843'),
    (2, 2, 'Comentario test 2', '2025-04-01 02:32:52.540'),
    (3, 2, 'Test 3', '2025-04-01 02:34:51.537'),
    (4, 3, 'Ticket en proceso.', '2025-04-09 00:28:19.357'),
    (1, 4, 'Ticket cerrado.', '2025-04-09 00:34:26.933'),
    (2, 4, 'test de comentarios', '2025-06-24 15:18:34.747');
END
GO

-- Tabla de Comentarios Tickets.
BEGIN
    CREATE TABLE dbo.documentos_tickets (
        ID            INT IDENTITY(1,1) PRIMARY KEY,
        TicketID      INT               NOT NULL,
        NombreArchivo VARCHAR(255)      NOT NULL,
        RutaArchivo   VARCHAR(255)      NOT NULL,
        FechaSubida   DATETIME          DEFAULT GETDATE(),
        FOREIGN KEY (TicketID) REFERENCES dbo.Tickets(ID)
    );

    -- Inserción de datos en la tabla Comentarios Tickets.
    INSERT INTO dbo.documentos_tickets (TicketID, NombreArchivo, RutaArchivo, FechaSubida) VALUES
    (1, '1743841721_Comparison_FairHarbor_vs_WeTravel_EN.pdf', '/uploads/1743841721_Comparison_FairHarbor_vs_WeTravel_EN.pdf', '2025-04-05 02:28:41.820'),
    (1, '1743841721_Comparativa_FairHarbor_vs_WeTravel.pdf', '/uploads/1743841721_Comparativa_FairHarbor_vs_WeTravel.pdf', '2025-04-05 02:28:41.823'),
    (1, '1743841721_Website revisions.pdf', '/uploads/1743841721_Website revisions.pdf', '2025-04-05 02:28:41.823'),
    (2, 'ticket-1222_1743847530_Comparison_FairHarbor_vs_WeTravel_EN.pdf', '/uploads/ticket-1222_1743847530_Comparison_FairHarbor_vs_WeTravel_EN.pdf', '2025-04-05 04:05:30.380');
END
GO

-- Tabla de Notas.
BEGIN
    CREATE TABLE dbo.Notas (
        id          INT IDENTITY(1,1) PRIMARY KEY,
        titulo      VARCHAR(255)      NOT NULL,
        descripcion TEXT              NOT NULL,
        fecha       DATETIME          DEFAULT GETDATE(),
        usuario_id  INT               NULL,
        CONSTRAINT FK_Notas_Usuarios FOREIGN KEY (usuario_id) REFERENCES dbo.Usuarios(ID)
    );

    -- Inserción de datos en la tabla de Notas.
    INSERT INTO dbo.Notas (titulo, descripcion, fecha, usuario_id) VALUES
    ('Test de notas', 'Detalle de notas', '2025-04-14 20:04:20.920', NULL),
    ('Test de notas 2', 'Detalles notas 2', '2025-04-14 20:05:35.347', NULL),
    ('Test de notas', 'Descripción Notas ', '2025-04-14 20:59:38.443', 1),
    ('Test de notas editar', 'Descripción editar', '2025-04-14 21:00:06.073', 1);
END
GO

-- Tabla de Requisitos.
BEGIN
    CREATE TABLE dbo.Requisitos (
        ID          INT IDENTITY(1,1) PRIMARY KEY,
        Descripcion VARCHAR(255)      NOT NULL,
        CategoriaID INT               NOT NULL,
        PrioridadID INT               NOT NULL,
        Fecha       DATE              NOT NULL DEFAULT GETDATE(),
        FOREIGN KEY (CategoriaID) REFERENCES dbo.CategoriasRequisitos(ID),
        FOREIGN KEY (PrioridadID) REFERENCES dbo.Categorias(ID)
    );

    -- Inserción de datos en la tabla Requisitos.
    INSERT INTO dbo.Requisitos (Descripcion, CategoriaID, PrioridadID, Fecha) VALUES
    ('Requisito 1', 1, 1, '2024-12-08'),
    ('Requisito 2', 2, 1, '2024-12-07'),
    ('Requisito 3', 3, 1, '2024-12-06'),
    ('Requisito 4', 4, 1, '2025-03-31');
END
GO

-- Tabla de Cotizaciones.
BEGIN
    CREATE TABLE dbo.cotizaciones (
        id             INT IDENTITY(1,1) PRIMARY KEY,
        cliente_id     INT               NOT NULL,
        subtotal       DECIMAL(10,2)     NOT NULL,
        iva            DECIMAL(10,2)     NOT NULL,
        total          DECIMAL(10,2)     NOT NULL,
        fecha_creacion DATETIME          DEFAULT GETDATE(),
        FOREIGN KEY (cliente_id) REFERENCES dbo.clientes(id)
    );

    -- Inserción de datos en la tabla Cotizaciones.
    INSERT INTO dbo.cotizaciones (cliente_id, subtotal, iva, total, fecha_creacion) VALUES
    (1, 791299.00, 102868.87, 894167.87, '2025-03-11 03:00:24.607'),
    (2, 789999.00, 102699.87, 892698.87, '2025-03-26 20:49:42.893'),
    (3, 660000.00, 85800.00, 745800.00, '2025-03-26 20:50:27.510');
END
GO

-- Tabla de Detalle Cotización.
BEGIN
    CREATE TABLE dbo.detalle_cotizacion (
        id            INT IDENTITY(1,1) PRIMARY KEY,
        cotizacion_id INT               NOT NULL,
        producto_id   INT               NOT NULL,
        cantidad      INT               NOT NULL,
        precio        DECIMAL(10,2)     NOT NULL,
        subtotal      DECIMAL(10,2)     NOT NULL,
        FOREIGN KEY (cotizacion_id) REFERENCES dbo.cotizaciones(id),
        FOREIGN KEY (producto_id) REFERENCES dbo.TiposProductos(ID)
    );

    -- Inserción de datos en la tabla Detalle Cotización.
    INSERT INTO dbo.detalle_cotizacion (cotizacion_id, producto_id, cantidad, precio, subtotal) VALUES
    (1, 1, 1, 1200.00, 1200.00),
    (2, 2, 1, 100.00, 100.00),
    (3, 3, 1, 789999.00, 789999.00),
    (3, 4, 22, 30000.00, 660000.00),
    (2, 5, 1, 789999.00, 789999.00);
END
GO

-- Tabla de Cotización.
BEGIN
    CREATE TABLE dbo.Cotizacion (
        ID          INT PRIMARY KEY,
        Cliente_ID  INT NULL,
        Descripcion VARCHAR(500) NULL,
        Monto       DECIMAL(18,2) NOT NULL,
        Fecha       DATETIME NULL
    );
END
GO

-- Tabla de Lista.
BEGIN
    CREATE TABLE dbo.Lista (
        Cedula     INT PRIMARY KEY,
        Cliente_ID INT NULL
    );
END
GO

-- Tabla de Historial de Tipos de Productos.
BEGIN
    CREATE TABLE dbo.Historial_TiposProductos (
        ID                   INT IDENTITY(1,1) PRIMARY KEY,
        TipoProductoID       INT               NOT NULL,
        Accion               VARCHAR(20)       NOT NULL,
        NombreAnterior       NVARCHAR(255)     NULL,
        CostoAnterior        DECIMAL(18,2)     NULL,
        DescripcionAnterior  NVARCHAR(MAX)     NULL,
        CategoriaAnterior    INT               NULL,
        FechaAccion          DATETIME          DEFAULT GETDATE(),
        Usuario              NVARCHAR(100)     NULL,
        Mensaje              NVARCHAR(255)     NULL,
        notificaciones_check TINYINT           DEFAULT 0
    );
END
GO
