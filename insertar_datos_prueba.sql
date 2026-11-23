-- Insertar especies si no existen
INSERT INTO especie (nombre, created_at, updated_at)
SELECT 'perro', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM especie WHERE nombre = 'perro');

INSERT INTO especie (nombre, created_at, updated_at)
SELECT 'gato', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM especie WHERE nombre = 'gato');

-- Verificar si hay mascotas, si no, insertar algunas de prueba
-- Primero necesitamos un cliente y un sexo
INSERT INTO sexo (nombre, created_at, updated_at)
SELECT 'Macho', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM sexo WHERE nombre = 'Macho');

INSERT INTO sexo (nombre, created_at, updated_at)
SELECT 'Hembra', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM sexo WHERE nombre = 'Hembra');

-- Insertar mascotas de prueba si no hay ninguna
-- Nota: Asegúrate de tener al menos un cliente en la tabla clientes
INSERT INTO mascotas (nombre_mascota, edad, especie_id, sexo_id, cliente_id, created_at, updated_at)
SELECT 'Firulais', 3, (SELECT id FROM especie WHERE nombre = 'perro' LIMIT 1), 
       (SELECT id FROM sexo WHERE nombre = 'Macho' LIMIT 1),
       (SELECT id FROM clientes LIMIT 1), NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM mascotas LIMIT 1);

INSERT INTO mascotas (nombre_mascota, edad, especie_id, sexo_id, cliente_id, created_at, updated_at)
SELECT 'Michi', 2, (SELECT id FROM especie WHERE nombre = 'gato' LIMIT 1), 
       (SELECT id FROM sexo WHERE nombre = 'Hembra' LIMIT 1),
       (SELECT id FROM clientes LIMIT 1), NOW(), NOW()
WHERE EXISTS (SELECT id FROM clientes LIMIT 1) AND EXISTS (SELECT 1 FROM mascotas LIMIT 1);