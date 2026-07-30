INSERT INTO books (isbn, title, author, status) VALUES
    ('9783498000064', 'Der Vorleser', 'Bernhard Schlink', 'available'),
    ('9783150000014', 'Faust I', 'Johann Wolfgang von Goethe', 'available'),
    ('9780061120084', 'To Kill a Mockingbird', 'Harper Lee', 'available'),
    ('9780140449136', 'The Odyssey', 'Homer', 'available');

INSERT INTO telephone_extensions (employee_name, department, extension, active) VALUES
    ('Anna Berger', 'Ausleihe', '4101', TRUE),
    ('Max Hoffmann', 'IT-Service', '4205', TRUE),
    ('Sara Klein', 'Erwerbung', '4302', TRUE);

INSERT INTO support_tickets
    (title, description, priority, status, external_provider, created_by)
VALUES
    (
        'Schnittstelle zum Katalog liefert Zeitüberschreitung',
        'Seit 08:15 Uhr antwortet die externe Katalogschnittstelle erst nach mehr als 30 Sekunden. Drei Mitarbeitende sind betroffen. Der Dienstleister wurde informiert.',
        'high',
        'waiting_provider',
        TRUE,
        NULL
    );

