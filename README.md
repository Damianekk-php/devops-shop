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
- `tests/` - testy jednostkowe i integracyjne PHPUnit.

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

## Testy

Wymagania testów: PHP z rozszerzeniami `pdo_mysql` i `pdo_sqlite`, Composer oraz uruchomiony MySQL/MariaDB. Testy integracyjne używają wyłącznie bazy o nazwie kończącej się na `_test` (domyślnie `shopstack_test`) i usuwają/odtwarzają tylko tę bazę na początku uruchomienia. Baza `shopstack` nie jest używana przez testy.

```powershell
composer install
$env:TEST_DB_HOST = '127.0.0.1'
$env:TEST_DB_PORT = '3306'
$env:TEST_DB_DATABASE = 'shopstack_test'
$env:TEST_DB_USERNAME = 'root'
$env:TEST_DB_PASSWORD = ''
.\vendor\bin\phpunit.bat
composer test:coverage
```

Testy jednostkowe koszyka i helperów używają izolowanej bazy SQLite in-memory. Testy integracyjne używają MySQL/MariaDB, sprawdzają modele, hashowanie haseł, izolację zamówień, checkout, stock i rollback.

## Future DevOps roadmap

Późniejsze etapy mogą dodać Docker, Docker Compose, Nginx, Redis, Prometheus, Grafana, Node Exporter, cAdvisor, Loki, Alertmanager, GitHub Actions, CI/CD oraz deployment na VPS. Żaden z tych elementów nie jest obecnie implementowany.

## TODO

- dodać testy automatyczne i migracje,
- rozbudować paginację i obsługę płatności,
- przygotować kolejne etapy obserwowalności i deploymentu.
