---
title: Eine Entwicklungsumgebung einrichten
audience: app-creator, developer
category: app-creation
order: 50
summary: Eine Entwicklungsumgebung für Anwendungen auf Exponential - PHP, Composer und Git, eine lokale Installation auf SQLite, Velocity oder der eingebaute Server, Entwicklungseinstellungen, die Debug-Leiste, Template-Prüfungen und Ihre Erweiterung in einem eigenen Repository.
keywords: Entwicklungsumgebung, lokale Installation, eingebauter Server, Entwicklungsmodus, Debug, Debug-Leiste, Anwendungsentwickler, development environment, local install, PHP, Composer, Git, SQLite, Velocity, built-in server, DevelopmentMode, debug, debug bar, eztemplatecheck, app creator
type: help
---
# Eine Entwicklungsumgebung einrichten

Diese Seite ist für Anwendungsentwickler, die einen Rechner einrichten, um auf Exponential zu bauen. Das Ziel: eine
lokale Installation, die Sie in Minuten neu installieren können, mit eingeschalteter Fehlersuche und Ihrer Erweiterung
in einem eigenen Git-Repository.

## Inhalt

1. [Werkzeuge](#werkzeuge)
2. [Eine lokale Installation](#eine-lokale-installation)
3. [Ausliefern](#ausliefern)
4. [Entwicklungseinstellungen](#entwicklungseinstellungen)
5. [Ihre Erweiterung in Git](#ihre-erweiterung-in-git)
6. [Tägliche Befehle](#tägliche-befehle)

## Werkzeuge

| Werkzeug | Version |
|---|---|
| PHP | 8.0 oder neuer, für den Kernel und für Velocity (ab dessen Release 0.0.4.54), mit den Erweiterungen aus [2.3](../../../installation/02-voraussetzungen.md#23-php-extensions) |
| Composer | 2.x |
| Git | jede aktuelle Version ([Mit Git entwickeln](../../mit-git-entwickeln.md)) |
| Eine Datenbank | SQLite braucht nichts; verwenden Sie für Integrationstests das System der Produktivumgebung |

## Eine lokale Installation

```bash
git clone https://github.com/se7enxweb/exponential.git exp-dev
cd exp-dev
composer install
php bin/php/console exp:install --package=sevenx_multisite_clean --url=http://127.0.0.1:8088
```

SQLite, ein erzeugtes Administratorpasswort wird einmal angezeigt ([Die Installation per Konsole](../../../installation/07-installation-ueber-die-konsole.md)).
Eine Neuinstallation mit demselben Befehl (`--force`) liefert in Minuten einen sauberen Zustand.

## Ausliefern

```bash
composer require se7enxweb/exponential-velocity
php bin/php/console exp:velocity config set ServerSettings Engine qbix
php bin/php/console exp:velocity start
```

Velocity hält Klassen im Speicher: Nachdem Sie eine PHP-Klasse hinzugefügt oder geändert haben, `exp:velocity restart`
(oder `exp:velocity deploy`). Für schnelle Arbeit an Templates führt `--engine=php` (der eingebaute Server von PHP) jede
Anfrage frisch aus ([8.3 Velocity](../../../installation/08-die-website-ausliefern.md#83-exponential-velocity-der-empfohlene-weg)).

## Entwicklungseinstellungen

Nur in `settings/override/site.ini.append.php` des Entwicklungsrechners:

```ini
[TemplateSettings]
DevelopmentMode=enabled
ShowTemplatePathComments=enabled

[DebugSettings]
DebugOutput=enabled
```

`DevelopmentMode` sorgt dafür, dass kompilierte Templates ihren Quellen folgen, und schaltet den Packer ab. Die
Debug-Leiste zeigt Abfragen, Templates, Einstellungen und Laufzeiten ([Exp-Debug-Leiste](../../../../eng-us/features/6.0/exp-debug-bar.md),
[Fehlersuche und Logs](fehlersuche-und-logs.md)). Kopieren Sie diese Einstellungen nie in die Produktivumgebung.

## Ihre Erweiterung in Git

Halten Sie die Anwendung in einem eigenen Repository, ausgecheckt (oder per Symlink eingebunden) nach `extension/<ext>`,
mit einer eigenen `composer.json`, damit sie sich später wie jede Erweiterung installieren lässt
([Composer-Pakete und Packagist](composer-und-packagist.md)). Aktivieren Sie sie in `settings/override/site.ini.append.php`:

```ini
[ExtensionSettings]
ActiveExtensions[]=expdemo
```

## Tägliche Befehle

```bash
php bin/php/ezpgenerateautoloads.php -e                  # after adding or renaming a class
php bin/php/ezcache.php --clear-tag=ini --allow-root-user  # after a settings change
php bin/php/eztemplatecheck.php -s<siteaccess> <file.tpl> # template syntax
php -l <file.php>                                        # PHP syntax
php bin/php/console exp:velocity deploy                  # everything, in the right order
```

## Verwandte Seiten

- [Ihre erste Erweiterung](ihre-erste-erweiterung.md)
- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Standards für PHP und Composer](../../../../eng-us/standards/php-and-composer.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
