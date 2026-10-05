USE shopstack;

INSERT INTO categories (name, slug, description) VALUES
('Biuro', 'biuro', 'Praktyczne akcesoria do codziennej pracy.'),
('Dom', 'dom', 'Przedmioty, które porządkują przestrzeń.'),
('Technologia', 'technologia', 'Prosty sprzęt do pracy i nauki.');

INSERT INTO products (category_id, name, slug, description, price, stock) VALUES
(1, 'Notes projektowy', 'notes-projektowy', 'Gruby papier i twarda oprawa do planowania projektów.', 39.90, 25),
(1, 'Pióro wieczne', 'pioro-wieczne', 'Minimalistyczne pióro do codziennych notatek.', 79.00, 12),
(2, 'Kubek ceramiczny', 'kubek-ceramiczny', 'Solidny kubek do porannej kawy.', 45.50, 30),
(3, 'Lampka biurkowa USB', 'lampka-biurkowa-usb', 'Regulowane światło do pracy po zmroku.', 129.00, 8),
(3, 'Podstawka pod laptopa', 'podstawka-pod-laptopa', 'Aluminiowa podstawka poprawiająca ergonomię.', 149.90, 5);

-- Development-only accounts. Replace these hashes before any public deployment.
INSERT INTO users (name, email, password, role) VALUES
('Test User', 'user@example.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCwJpYQb1QzYk8ZQyY6a', 'user'),
('ShopStack Admin', 'admin@example.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llCwJpYQb1QzYk8ZQyY6a', 'admin');
