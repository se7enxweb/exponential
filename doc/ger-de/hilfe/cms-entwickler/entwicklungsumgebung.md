---
title: Entwicklungsumgebung
audience: cms-developer
category: cms-development
order: 39
summary: Für CMS-Entwickler eine funktionierende Entwicklungsumgebung mit wenigen Befehlen. Die Seite behandelt den Code als Git-Klone, eine Installation mit einem Befehl auf SQLite, Velocity mit der Engine des eingebauten PHP-Servers, Debug-Ausgabe und Template-Einstellungen, einen Platz für Ihre Erweiterungen, die Arbeit im Team und die Regeln, die Entwicklung und Live-Betrieb trennen.
keywords: Entwicklungsumgebung, lokale Einrichtung, Git, Composer, Klone, exp:install, SQLite, Velocity, php -S, velocity-router.php, Engine=php, Port 8087, Debug-Ausgabe, ShowUsedTemplates, TemplateCompile, Debug-Leiste, Teamarbeit, Staging, development environment, local setup, clones, debug output, debug bar, team workflow
type: help
---
# Entwicklungsumgebung

Diese Seite richtet sich an Entwickler, die den Rechner einrichten, auf dem sie Websites bauen. Eine
Entwicklungsinstallation braucht wenige Befehle: Kernel und Erweiterungen als Git-Klone, eine Datenbank in einer
SQLite-Datei und einen Webserver, der mit einem Befehl startet. Das Kapitel
[Eine Entwicklungsinstallation einrichten](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/eine-entwicklungsinstallation-einrichten.md)
des Buchs enthält jedes Detail; diese Seite ist die Zusammenfassung für CMS-Entwickler mit dem Projekt drumherum.

## Inhalt

1. [Was Sie brauchen](#was-sie-brauchen)
2. [Schritt 1: der Code als Git-Klone](#schritt-1-der-code-als-git-klone)
3. [Schritt 2: mit einem Befehl installieren](#schritt-2-mit-einem-befehl-installieren)
4. [Schritt 3: ausliefern](#schritt-3-ausliefern)
5. [Schritt 4: Fehler sichtbar machen](#schritt-4-fehler-sichtbar-machen)
6. [Schritt 5: die Erweiterungen Ihres Projekts](#schritt-5-die-erweiterungen-ihres-projekts)
7. [Arbeit im Team](#arbeit-im-team)
8. [Regeln, die Entwicklung und Live-Betrieb trennen](#regeln-die-entwicklung-und-live-betrieb-trennen)
9. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)

## Was Sie brauchen

PHP 8.0 oder später mit den Erweiterungen, die die `composer.json` im Hauptverzeichnis verlangt (dom, mbstring, json,
iconv, simplexml und weitere; SQLite 3 für die Standarddatenbank, GD oder ImageMagick für Bilder), Composer und Git
([Werkzeuge, die Sie brauchen](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/werkzeuge-die-sie-brauchen.md),
[Anforderungen](../../installation/02-voraussetzungen.md)).

## Schritt 1: der Code als Git-Klone

```bash
composer config --global 'preferred-install.se7enxweb/*' source
git clone https://github.com/se7enxweb/exponential.git mysite
cd mysite
git switch -c my-dev
composer install
```

Ist `preferred-install` auf `source` gesetzt, ist jede `se7enxweb/*`-Erweiterung ein Git-Klon unter
`extension/<name>` mit ihrer Geschichte; Sie können also nachlesen, warum Code so ist, wie er ist, und Korrekturen
zurückschicken ([Mit Git entwickeln](../mit-git-entwickeln.md), [Den Code holen](../../installation/03-den-code-beschaffen.md)).

## Schritt 2: mit einem Befehl installieren

```bash
php bin/php/console exp:install --apply
```

Ohne Optionen installiert das das Seitenpaket `sevenx_multisite` auf SQLite in `eng-US` und gibt die Adresse der
Website, die Anmeldung der Administration und ein erzeugtes Administratorpasswort aus (auch in
`var/log/initial-admin-password`, nur für seinen Eigentümer lesbar). Andere Datenbanken und Pakete:
[Die Installation über die Konsole](../../installation/07-installation-ueber-die-konsole.md). Es weigert sich, über eine vorhandene
Installation zu laufen, sofern nicht `--force` angegeben ist; richten Sie es nie auf eine Datenbank, die wichtig ist.

Eine Abkürzung, die viele Entwickler anlegen: `ln -s bin/php/console console`, dann `./console list`.

## Schritt 3: ausliefern

```bash
./console exp:velocity start
```

Velocity, der Anwendungsserver von Exponential, wird mit `settings/velocity.ini [ServerSettings] Engine=php`
ausgeliefert: Es führt den eingebauten Server von PHP (`php -S`) mit dem Router `bin/php/velocity-router.php` aus, auf
dem Port aus `[PHPServerSettings]` (mitgeliefert 8087). Öffnen Sie die Adresse, die der Installer ausgegeben hat. Die
persistente Engine `qbix`, FrankenPHP und Apache oder nginx mit PHP-FPM sind die Möglichkeiten für die Produktion und
für Tests wie in der Produktion ([Die Website ausliefern](../../installation/08-die-website-ausliefern.md),
[Lektion 13.2: Velocity für Entwickler](../trainer/entwickler/modul-13-rad-velocity-und-mitwirken/velocity-fuer-entwickler.md)).

## Schritt 4: Fehler sichtbar machen

```bash
./console exp:ini set site.ini/DebugSettings/DebugOutput enabled global --allow-root-user
./console exp:ini set site.ini/TemplateSettings/ShowUsedTemplates enabled global --allow-root-user
./console exp:ini set site.ini/TemplateSettings/TemplateCompile disabled global --allow-root-user
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Fehler, SQL und die verwendeten Templates erscheinen unter jeder Seite, und Template-Änderungen sind sofort sichtbar.
Beschränken Sie die Debug-Ausgabe auf jedem Rechner, den andere erreichen können, auf Ihre Adresse (`DebugByIP`,
`DebugIPList[]`) ([Debug-Ausgabe und Logs für Entwickler](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/debug-ausgabe-und-logs-fuer-entwickler.md),
[Debug-Leiste](../../bc/6.0/debug-leiste.md)). Logs liegen in `var/log/`.

## Schritt 5: die Erweiterungen Ihres Projekts

```bash
mkdir -p extension/exp_mysite/settings extension/exp_mysite/design/exp_mysite/templates
git -C extension/exp_mysite init
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' exp_mysite global --allow-root-user
```

Was hineingehört: [Erweiterungen, die Sie schreiben werden](erweiterungen-die-sie-schreiben-werden.md). In der Administration
angelegte Klassen gehören in Paketen in dieses Repository
([Eine Inhaltsklasse anlegen](eine-inhaltsklasse-anlegen.md#schritt-5-die-klasse-in-andere-installationen-übertragen)).

## Arbeit im Team

- Eine Entwicklungsinstallation je Entwickler, jede aus denselben Projekt-Repositorys.
- Klassen und Demo-Inhalte werden in Paketen übertragen, Layouts in Übertragungsdateien von `exp:layouts`,
  Einstellungen und Templates in der Erweiterung. Bauen Sie einen Entwicklerrechner daraus neu auf, nie aus einer Kopie
  von Live.
- Eine gemeinsame Staging-Installation, bereitgestellt wie Live ([Eine Website bereitstellen](eine-website-bereitstellen.md)), ist
  der Ort, an dem Redakteure und Tester Arbeit sehen, bevor sie live geht.
- Führen Sie die Prüfungen des Projekts aus, bevor Sie eine Änderung teilen
  ([Prüfungen, bevor Sie eine Änderung teilen](../trainer/entwickler/modul-12-tests-und-qualitaet/pruefungen-bevor-sie-eine-aenderung-teilen.md)).

## Regeln, die Entwicklung und Live-Betrieb trennen

| Regel | Warum |
|---|---|
| Entwickeln Sie nie auf der Live-Website | ein Tippfehler in einem Template oder einer Einstellungsdatei legt die Seite lahm |
| Kopieren Sie `settings/override/` nie zwischen Rechnern | es enthält Passwörter und hostspezifische Werte |
| Verwenden Sie Kopien von Live-Inhalten nur, wenn Sie müssen | Live-Inhalte können personenbezogene Daten enthalten |
| Testkonten und E-Mails verwenden `example.invalid` | kein Test darf einer echten Person eine E-Mail schicken |
| Schalten Sie die Debug-Ausgabe vor dem Live-Gang ab | sie verrät Pfade, SQL und Einstellungen |

## Wenn etwas schiefgeht

| Symptom | Prüfen Sie |
|---|---|
| `composer install` bricht mit einem Fehler zur PHP-Version ab | `php -v`; Exponential 6 braucht PHP 8.0 oder später |
| Eine leere Seite | `var/log/error.log` und das PHP-Fehlerlog |
| Eine Template-Änderung wirkt nicht | `TemplateCompile`, die Template- und Override-Caches |
| Eine neue Klasse wird nicht gefunden | `php bin/php/ezpgenerateautoloads.php -e`, dann die Caches leeren und den Server neu starten |

Mehr: [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md).

## Verwandte Seiten

- [Ihre erste Stunde als Entwickler](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/ihre-erste-stunde-als-entwickler.md)
- [Modul 1: Die Entwicklungsinstallation](../trainer/entwickler/modul-01-die-entwicklungsinstallation/README.md)
- [Demomodus für Entwickler](../erweiterungsentwicklung/teil-8-referenz/demomodus-fuer-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
