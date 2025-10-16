USE proyecto;

-- Crear usuarios normales
INSERT INTO usuarios (nombre_usuario, email, contraseña)
VALUES
('estrella1', 'estrella1@example.com', 'hash_contraseña'),
('universo', 'universo@example.com', 'hash_contraseña'),
('galaxia', 'galaxia@example.com', 'hash_contraseña');

-- Insertar mensajes de prueba
INSERT INTO mensajes (id_usuario, contenido)
VALUES
(1, 'A veces los pensamientos más brillantes aparecen en la oscuridad.'),
(2, 'Cada palabra lanzada al vacío puede ser una estrella fugaz.'),
(1, 'El silencio también habla, si sabes escucharlo.'),
(2, 'Aunque no me leas, te dejo mi luz por un instante.'),
(1, 'No busques sentido en todo; a veces solo hay belleza.'),
(3, 'Cuando todo parece apagarse, una chispa puede cambiarlo todo.'),
(2, 'Los sueños no caducan, solo esperan ser recordados.'),
(1, 'Una estrella fugaz no desaparece, solo sigue su camino.'),
(2, 'La distancia no existe cuando los pensamientos vuelan.'),
(3, 'Cada mensaje que escribes deja un rastro en el universo.'),
(1, 'Somos fragmentos de historias que se cruzan en silencio.'),
(2, 'No hace falta ver el cielo para desear algo.'),
(1, 'A veces el mejor mensaje es el que nunca se envía.'),
(3, 'El universo guarda secretos que solo el tiempo revela.'),
(2, 'Hay palabras que viajan más rápido que la luz.'),
(1, 'Cuando pienses que nadie te escucha, el universo sí lo hace.'),
(3, 'Toda estrella que cae ilumina un deseo.'),
(1, 'Cada adiós deja un eco que el tiempo no borra.'),
(2, 'Los mensajes desaparecen, pero las emociones quedan.'),
(3, 'Las estrellas fugaces son como los mensajes sinceros: breves y luminosos.');
