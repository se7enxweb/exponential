# SQLite für Exponential Platform: kein Datenbankserver nötig

Diese Seite ist für Entwickler, die eine Exponential Platform (die Symfony-basierte Plattform) in Minuten zum Laufen
bringen wollen, ohne Datenbankserver. Es geht **nicht** um den eigenen SQLite-Treiber des Legacy-Kernels: dafür siehe
[Datenbank SQLite für Exponential 6](sqlite-datenbank.md). Referenz:
[Spezifikation des SQLite-Installers der Plattform](../../spezifikationen/6.0/platform-sqlite-installer.md).

**Repositorys:** `core` (Kernel von Platform v5), `ezplatform-kernel` (Linie 3.x / 4.6), `ezpublish-kernel` (Linie 2.5),
`exponential-platform-legacy`, `exponential-platform-nexus` / `cjw-exponential-platform-nexus`, `legacyBridge`.

## Was es ist

Zwischen Januar und April 2026 lernten die Installer der Plattform, in eine einzelne SQLite-Datei zu installieren. Sie brauchen die PHP-Erweiterungen `pdo_sqlite` und `sqlite3` und sonst nichts: keinen Server für MySQL, MariaDB oder PostgreSQL. Das Ergebnis ist eine vollständige Plattform-Datenbank (Inhaltstypen, Inhalte, Benutzer, Rollen, Suchtabellen) in `var/data_dev.db`.

## Warum es hilft

- Ein neuer Entwickler hat Minuten nach `composer install` eine laufende Administrationsoberfläche.
- Automatisierte Tests und Demos laufen, ohne einen Server bereitzustellen.
- Sie können eine ganze Website als eine Datei mitnehmen und sie später nach MySQL oder PostgreSQL konvertieren ([Anleitung zum DXP-Skeleton](plattform-dxp-skeleton.md), Konvertierung der Datenbank).

SQLite ist für Entwicklung, Tests, Demos und Deployments ohne Netzanbindung. Verwenden Sie MySQL, MariaDB oder PostgreSQL für Produktionswebsites mit gleichzeitig arbeitenden Redakteuren.

## Verwenden (Platform v5)

Prüfen Sie die Erweiterungen:

```bash
php -m | grep -i sqlite        # must list pdo_sqlite and SQLite3
```

Tragen Sie dies in `.env.local` ein:

```bash
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
MESSENGER_TRANSPORT_DSN=sync://
```

`sync://` wird gebraucht, weil der Standard-Transport von Doctrine Messenger eine zweite Datenbankverbindung will. Entfernen Sie die Zeilen `DATABASE_DRIVER`, `DATABASE_HOST`, `DATABASE_PORT`, `DATABASE_NAME`, `DATABASE_USER` und `DATABASE_PASSWORD`; sie werden ignoriert, wenn ein vollständiger DSN gesetzt ist.

Installieren:

```bash
php bin/console exponential:install exponential-oss
```

Sie sehen `Using SQLite database <path> (file created automatically on first connection).` Der Installer legt das Verzeichnis bei Bedarf an, legt die Tabellen an, importiert die Startdaten und indiziert die Inhalte. Machen Sie die Datei dann für den Webserver beschreibbar (SQLite schreibt bei jeder Bearbeitung in die Datei):

```bash
chmod 664 var/data_dev.db
chown $USER:www-data var/data_dev.db     # use your web server group
php bin/console cache:clear
```

Auch das Verzeichnis, das die Datei enthält, muss beschreibbar sein, weil SQLite daneben Journal-Dateien anlegt.

## Verwenden (Projekte im Stil von Platform 2.5 / Nexus 1.x)

Setzen Sie Treiber und Pfad in `app/config/parameters.yml`:

```yaml
parameters:
    database_driver: pdo_sqlite
    database_path: '%kernel.project_dir%/var/data_%kernel.environment%.db'
```

(Der Standard von `database_path` ist `%kernel.project_dir%/var/data_%kernel.environment%.db`, zum Beispiel `var/data_dev.db`; die Umgebungsvariable `DATABASE_PATH` überschreibt ihn in `app/config/env/generic.php`.) Dann:

```bash
php bin/console ezplatform:install exponential-oss        # platform data
php bin/console ezplatform:install exponential-cjw        # Nexus: the CJW starter content (Nexus 1.0.0.4 and later)
```

Auf der Linie 2.5 heißt der Befehl noch `ezplatform:install`. Der Kernel der Linie 3.x / 4.6 registriert ihn als `exponential:install` mit dem alten Namen als Alias und installiert den Typ `ibexa-oss` (`php bin/console exponential:install ibexa-oss`); siehe [Namen der Befehle](../../spezifikationen/6.0/platform-konsolenbefehle.md) und die [Spezifikation des Installers](../../spezifikationen/6.0/platform-sqlite-installer.md) für jeden Installationstyp.

## Mit der Legacy Bridge

Eine Plattform-Installation auf SQLite kann seit `v4.0.0.1` auch den Legacy-Kernel von Exponential über die [Legacy Bridge](legacy-bridge.md) ausführen: Die Bridge bildet `pdo_sqlite` auf die Legacy-Implementierung `sqlite3` ab und übergibt den Dateipfad an die Legacy-`site.ini`. Der SQLite-Treiber des Legacy-Kernels selbst wird in [der Spezifikation des Datenbanktreibers SQLite 3](../../spezifikationen/6.0/sqlite3-datenbanktreiber.md) beschrieben.

## Was nebenbei behoben wurde (und warum Sie ihm trauen können)

| Problem auf SQLite | Behebung | Wo / wann |
|---|---|---|
| `doctrine:database:create` scheitert mit „getListDatabasesSQL is not supported by platform“ | der Installationsbefehl erkennt SQLite, sorgt für das Verzeichnis und überspringt den Aufruf | `core` 29.04.2026, `ezplatform-kernel` 11.04.2026 |
| Der Installer wählte das Verzeichnis der SQL-Dateien nach dem Plattformnamen von Doctrine, der das SQLite-Verzeichnis nicht zuverlässig nennt | das DBMS wird mit `instanceof` erkannt (MySQL, PostgreSQL, SQLite) | `core` 29.04.2026 |
| Tabellen mit zusammengesetztem Primärschlüssel (`id`, `version`) wie die Inhaltsattribute erhielten einen einspaltigen Autoincrement-Schlüssel, sodass das Speichern eines neuen Entwurfs eine Verletzung einer Eindeutigkeitsbedingung auslöste | der Installer setzt die SQLite-Plattformklasse von Ibexa ein, sodass Tabellen mit zusammengesetztem Schlüssel kein `AUTOINCREMENT` erhalten; die Startdatei von 3.x löscht die drei betroffenen Tabellen und legt sie mit dem richtigen Schlüssel neu an | `core` 29.04.2026, `ezplatform-kernel` 07.04. / 11.04.2026 |
| Escape-Folgen von MySQL in den Startdaten (`\'`, `\n`) zerstörten SQLite | Startdaten konvertiert: `''` für Anführungszeichen, echter Leerraum für `\n \r \t` | `ezpublish-kernel` 09.04.2026, `ezplatform-kernel` 07.04.2026 |
| Legacy-Tabellen fehlten bei Installationen von Plattform plus Legacy | 73 Tabellen des Legacy-Schemas an die Startdaten angehängt | `ezplatform-kernel` 29.03.2026 |
| Eine harmlose Warnung bei der Neuindizierung brach eine frische Installation ab | der Indizierungsschritt ist umschlossen, sodass eine Warnung bei einer leeren Installation den Befehl nicht anhält | `ezpublish-kernel` 09.04.2026 |

## Grenzen

- Ein Schreiber zur Zeit. Verwenden Sie SQLite nicht für ein stark beschäftigtes Redaktionsteam.
- Dateiberechtigungen sind das übliche Installationsproblem: Der Benutzer des Webservers muss die `.db`-Datei und ihr Verzeichnis besitzen oder beschreiben können.
- Die Startdateien tragen das Inhaltsmodell des Installationstyps, den Sie wählen; ein anderer Installationstyp braucht eigene Startdaten.

## Verwandte Seiten

- [Datenbank SQLite für Exponential 6](sqlite-datenbank.md), [SQLite: Transaktionen stellen sich für die Schreibsperre an](../../bc/6.0/sqlite-transaktionen.md)
- Plattformfunktionen: [Nexus-Starter](plattform-nexus-starter.md), [DXP-Skeleton](plattform-dxp-skeleton.md), [Administrationsoberfläche der Plattform](plattform-admin-ui-fork.md), [Layouts auf der Plattform](plattform-layouts-core-fork.md), [Framework-Forks für PHP 8.5](plattform-php85-framework-forks.md), [Site-Bundles](plattform-site-bundles.md), [Legacy Bridge](legacy-bridge.md), [Datenbankverwaltung AdminNeo](adminneo-datenbankverwaltung.md)
- Spezifikationen: [SQLite-Installer der Plattform](../../spezifikationen/6.0/platform-sqlite-installer.md), [Namen der Konsolenbefehle der Plattform](../../spezifikationen/6.0/platform-konsolenbefehle.md), [Paketkarte der Plattform](../../spezifikationen/6.0/platform-paketzuordnung.md), [Bundle der Legacy Bridge](../../spezifikationen/6.0/legacy-bridge-bundle.md)
- Hinweise zur Aktualisierung: [Paket-Forks und Umbenennungen von Befehlen](../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md)
- Changelog: [Changelog der Plattform](../../../changelogs/extensions/exponential-platform.md)
- Chronik: [core](../../geschichte/oekosystem/core.md), [ezplatform-kernel](../../geschichte/oekosystem/ezplatform-kernel.md), [ezpublish-kernel](../../geschichte/oekosystem/ezpublish-kernel.md), [exponential-platform-legacy](../../geschichte/oekosystem/exponential-platform-legacy.md), [Übersicht über das Ökosystem](../../geschichte/oekosystem.md), [Monate des Ökosystems](../../geschichte/oekosystem/monate/2026-04.md), [Änderungsverzeichnis](../../geschichte/aenderungsverzeichnis/README.md), [Januar 2024, erste Hälfte](../../geschichte/2024/2024-01a.md), [Januar 2024, zweite Hälfte](../../geschichte/2024/2024-01b.md), [Juni 2026, zweite Hälfte](../../geschichte/2026/2026-06b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
