# ShopStack

ShopStack to mały, modularny sklep internetowy portfolio napisany w czystym PHP. Projekt jest przygotowany do lokalnego uruchomienia w XAMPP i późniejszego rozwoju o narzędzia DevOps.

## Funkcje

- katalog produktów z wyszukiwaniem, kategorią i sortowaniem,
- rejestracja, logowanie, sesje i role `user` / `admin`,
- koszyk sesyjny z walidacją stocku,
- transakcyjny checkout z historią zamówień,
- panel administratora z dashboardem, CRUD produktów i kategorii, statusami zamówień oraz rolami użytkowników,
- PDO, prepared statements, CSRF, escaping HTML, bezpieczne sesje i logowanie błędów.

## Stack

PHP 8.3+, MySQL 8+, PDO, HTML5, CSS3, vanilla JavaScript, Apache z XAMPP. Aplikacja nie używa frameworka PHP.

## Struktura

- `public/` - front controller i assets,
- `src/` - kontrolery, modele, serwisy, middleware i core,
- `config/` - konfiguracja środowiska,
- `templates/` - widoki,
- `database/` - schema i dane testowe,
- `storage/logs/` - logi aplikacji.

## Instalacja w XAMPP

1. Skopiuj projekt do `C:\xampp\htdocs\sklepik`.
2. Uruchom Apache i MySQL w panelu XAMPP.
3. Utwórz plik `.env` na podstawie `.env.example`.
4. W phpMyAdmin zaimportuj `database/schema.sql`, a potem `database/seed.sql`.
5. Otwórz `http://localhost/sklepik/public/`.

Alternatywnie można uruchomić katalog `public/` przez `C:\xampp\php\php.exe -S localhost:8000 -t public` po uruchomieniu MySQL.

## Dane testowe

- `user@example.test` / `password`
- `admin@example.test` / `password`

Są to wyłącznie dane lokalne. Zmień je przed użyciem projektu poza środowiskiem deweloperskim.

## Konfiguracja i bezpieczeństwo

Nie commituj `.env`. Ustaw w nim `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `APP_ENV`, `APP_DEBUG` i `APP_URL`. Hasła są przechowywane przez `password_hash()`, a ceny zamówień są ponownie pobierane z bazy w transakcji. Logi nie powinny zawierać haseł ani sekretów.

## Future DevOps roadmap

Późniejsze etapy mogą dodać Docker, Docker Compose, Nginx, Redis, Prometheus, Grafana, Node Exporter, cAdvisor, Loki, Alertmanager, GitHub Actions, CI/CD oraz deployment na VPS. Żaden z tych elementów nie jest obecnie implementowany.

## TODO

- dodać testy automatyczne i migracje,
- rozbudować paginację i obsługę płatności,
- przygotować kolejne etapy obserwowalności i deploymentu.
