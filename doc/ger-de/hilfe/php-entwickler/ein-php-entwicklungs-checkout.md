---
title: Einen PHP-Entwicklungs-Checkout einrichten
audience: php-developer, developer
category: php-development
order: 40
summary: Ein funktionierender Checkout für PHP-Arbeit an Exponential - klonen, Composer, eine SQLite-Installation, der Name der Entwicklungsumgebung, die Entwicklungswerkzeuge in .devtools/, die Git-Hooks, Debug-Einstellungen und die Befehle, die Sie jeden Tag ausführen.
keywords: Entwicklungs-Checkout, klonen, Entwicklungswerkzeuge, Git-Hooks, Debug-Einstellungen, tägliche Befehle, development checkout, clone, composer install, exp:install, EXP_ENV, config.env.php, make devtools, make hooks, .devtools, PHP_CodeSniffer, PHPStan, PHP 8.0 binary, debug settings, daily commands
type: help
---
# Einen PHP-Entwicklungs-Checkout einrichten

Diese Seite richtet sich an PHP-Entwickler, die einen Checkout von Exponential möchten, um darin PHP-Code zu schreiben und
zu testen: Kernel-Korrekturen, Erweiterungen, Tests. Sie geht einen Schritt weiter als die Einrichtungsseite für
Anwendungsentwickler ([Eine Entwicklungsumgebung einrichten](../ersteller/anwendungsentwickler/entwicklungsumgebung.md)): die
Entwicklungswerkzeuge, die Git-Hooks und eine PHP-8.0-Binärdatei für Kompatibilitätsprüfungen.

## Inhalt

1. [Was Sie brauchen](#was-sie-brauchen)
2. [Klonen und installieren](#klonen-und-installieren)
3. [Die Umgebung benennen](#die-umgebung-benennen)
4. [Die Entwicklungswerkzeuge](#die-entwicklungswerkzeuge)
5. [Die Git-Hooks](#die-git-hooks)
6. [Eine PHP-8.0-Binärdatei](#eine-php-80-binärdatei)
7. [Debug-Einstellungen für die Entwicklung](#debug-einstellungen-für-die-entwicklung)
8. [Tägliche Befehle](#tägliche-befehle)

## Was Sie brauchen

| Werkzeug | Warum |
|---|---|
| PHP 8.0 oder neuer auf der Kommandozeile | der Kernel unterstützt 8.0 bis 8.5; PHPUnit 13 im installierten `vendor/` braucht 8.4 |
| Composer 2 | die Abhängigkeiten und die Entwicklungswerkzeuge |
| Git | die Hooks und die änderungsbasierten Prüfungen vergleichen mit `origin/main` |
| Node.js (optional) | `node --check` für geändertes JavaScript im pre-commit-Hook |
| Ein zweites PHP, 8.0 (optional) | `make lint80`, die älteste unterstützte Syntax |

## Klonen und installieren

```bash
git clone https://github.com/se7enxweb/exponential.git exp-dev
cd exp-dev
composer install
php bin/php/console exp:install --apply --package=sevenx_multisite_clean --url=http://127.0.0.1:8088
```

`composer install` führt danach `bin/php/ezpgenerateautoloads.php` aus (`"post-install-cmd"` in `composer.json`).
Die Installation verwendet SQLite, sofern Sie nicht `--db=` angeben ([Die Konsoleninstallation](../../installation/07-installation-ueber-die-konsole.md)).
Installieren Sie mit `--force` neu, wenn Sie einen sauberen Zustand brauchen.

## Die Umgebung benennen

Kopieren Sie `config.env.php.example` nach `config.env.php` und setzen Sie den Umgebungsnamen dieser Maschine:

```php
<?php
define( 'EXP_ENV', 'dev' );
```

Mit `dev` (oder `test`) zeigt `index.php` PHP-Fehler im Browser, und `eZINI` liest zusätzlich eine Variante jeder
Einstellungsdatei mit dem Namen nach `.ini`, zum Beispiel `settings/override/site.ini.dev.append.php`, nach der
Standarddatei derselben Schicht. Jede Umgebung hat ihren eigenen INI-Cache. `config.env.php` steht in `.gitignore`;
committen Sie sie niemals.

## Die Entwicklungswerkzeuge

```bash
make devtools   # PHP_CodeSniffer, PHPCompatibility and PHPStan into .devtools/ (composer.dev.json), never into vendor/
```

Die Werkzeuge werden aus `composer.dev.json` für PHP 8.0.30 aufgelöst, sodass sie unter jedem unterstützten PHP laufen.
Danach existieren `.devtools/vendor/bin/phpcs` und `.devtools/vendor/bin/phpstan`, und die make-Ziele verwenden sie
([Git-Hooks und make-Ziele](git-hooks-und-make-ziele.md)).

## Die Git-Hooks

```bash
make hooks      # git config core.hooksPath .githooks
```

| Hook | Prüft |
|---|---|
| pre-commit | `php -l` der vorgemerkten PHP-Dateien, PHP-8.0-Syntax, wenn eine PHP-8.0-Binärdatei gefunden wird, `node --check` des vorgemerkten JavaScript, das Datei-Manifest, den Code-Stil der vorgemerkten PHP-Dateien |
| commit-msg | der Betreff beginnt mit `Added: `, `Updated: `, `Fixed: `, `Removed: ` oder `Renamed: ` |
| pre-push | PHPStan gegen die Baseline |

Ohne `make devtools` prüfen die Hooks weiterhin Syntax, Manifest und Nachricht und überspringen Code-Stil und PHPStan
mit einem Hinweis.

## Eine PHP-8.0-Binärdatei

PHP 7.4 ist die älteste unterstützte Version (`make lint74` prüft sie), PHP 8.0 das mitgelieferte PHP von RHEL 9. Der pre-commit-Hook und `make lint80` finden PHP 8.0 als `EXP_PHP80`, `php8.0`
oder `php80` im `PATH` oder als `/opt/plesk/php/8.0/bin/php`:

```bash
make lint80                                   # PHP 8.0 syntax of the files changed since origin/main
make lint80-all PHP80=/path/to/php8.0         # every PHP file in git, as CI runs it
```

Ohne eine solche Binärdatei ist der CI-Job `php80` Ihre einzige PHP-8.0-Prüfung ([Code schreiben, der unter PHP 8.0 läuft](code-der-unter-php-8-0-laeuft.md)).

## Debug-Einstellungen für die Entwicklung

Nur in `settings/override/site.ini.append.php` der Entwicklungsmaschine:

```ini
[DebugSettings]
DebugOutput=enabled

[TemplateSettings]
DevelopmentMode=enabled
ShowTemplatePathComments=enabled
```

Die Debug-Leiste zeigt dann für jede Seite Abfragen, Templates, Zeitmessungen und Einstellungen
([Die Debug-Leiste für PHP-Entwickler](die-debug-leiste-fuer-php-entwickler.md)). Schreiben Sie diese Zeilen stattdessen in
eine `site.ini.dev.append.php`, dann können sie niemals eine Maschine erreichen, deren Umgebung nicht `dev` ist.

## Tägliche Befehle

| Befehl | Wann |
|---|---|
| `php -l <file.php>` | nach jeder Bearbeitung |
| `php bin/php/ezpgenerateautoloads.php -e` | nach dem Hinzufügen, Umbenennen oder Verschieben einer Erweiterungsklasse |
| `php bin/php/ezcache.php --clear-tag=ini` | nach einer Änderung der Einstellungen |
| `php bin/php/ezcache.php --clear-all` | nach einer Änderung einer Klasse oder eines Template-Pfads, wenn die Ausgabe veraltet aussieht |
| `vendor/bin/phpunit <test file>` | die Tests dessen, was Sie geändert haben |
| `make check` | Syntax, Manifest, Code-Stil und PHPStan, bevor Sie pushen |
| `php bin/php/console exp:velocity deploy` | unter Velocity: Autoloads, Caches, Worker, in der richtigen Reihenfolge |

Fügen Sie `--allow-root-user` den Cache- und Konsolenbefehlen nur hinzu, wenn Sie als root arbeiten.

## Verwandte Seiten

- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Eine Entwicklungsinstallation einrichten](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/eine-entwicklungsinstallation-einrichten.md)
- [Umgebungseinstellungen](../../funktionen/6.0/umgebungseinstellungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
