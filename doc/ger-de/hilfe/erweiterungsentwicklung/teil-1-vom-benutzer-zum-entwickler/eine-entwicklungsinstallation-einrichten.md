---
title: Eine Entwicklungsinstallation einrichten
audience: user, developer, designer, app-creator
category: extension-development
order: 40
summary: Eine Entwicklungsinstallation mit wenigen Befehlen - Git-Klone, Composer, exp:install auf SQLite, Velocity, Debug-Einstellungen und die Regeln, die sie von der Live-Website getrennt halten.
keywords: Entwicklungsinstallation, lokal, Laptop, Sandbox, Staging, Debug, development installation, local, laptop, git clone, composer install, exp:install, sqlite, velocity, php -S, debug, sandbox, staging
type: help
---
# Eine Entwicklungsinstallation einrichten

Teil 1, Kapitel 4. Sie entwickeln nie auf einer Live-Website. Dieses Kapitel richtet eine eigene Installation ein, auf
der Sie Dinge kaputt machen, die Debug-Ausgabe lesen und jedes Beispiel dieses Buchs ausprobieren können. Es braucht nur
wenige Befehle; alles nach dem Klonen ist gleich, ob Sie auf einem Laptop, in einer virtuellen Maschine oder in einem
Container arbeiten.

## Was Sie bekommen

- Den Kernel und jede Erweiterung als **Git-Klone**, damit Sie ihre Historie lesen und Ihre Änderungen auf einem Branch
  halten können.
- Eine Datenbank in **einer einzigen SQLite-Datei**, damit kein Datenbankserver eingerichtet werden muss.
- Einen **Webserver**, der mit einem Befehl gestartet wird.
- Eingeschaltete **Debug-Ausgabe**, damit Fehler angezeigt statt versteckt werden.

## Schritt 1: den Code als Git-Klone holen

Sagen Sie Composer einmal, dass jedes Paket `se7enxweb/*` als Git-Klon installiert werden soll:

```bash
composer config --global 'preferred-install.se7enxweb/*' source
```

Klonen Sie dann den Kernel, legen Sie Ihren eigenen Branch an und installieren Sie die Pakete:

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git switch -c my-dev
composer install
```

Das erste `composer install` dauert einige Minuten: Es klont jede Erweiterung, die die `composer.json` des Kernels
verlangt, nach `extension/<name>`, jede ein eigenes Git-Repository. Die vollständige Erklärung, auch wie Sie später
aktualisieren, steht in [Den Code holen](../../../installation/03-den-code-beschaffen.md) und
[Mit Git entwickeln](../../mit-git-entwickeln.md).

Prüfen Sie:

```bash
ls -d extension/*/.git | wc -l
git -C extension/eztags log --oneline -3
```

Erwartet: eine Anzahl von Erweiterungsklonen und die letzten drei Commits eines davon.

## Schritt 2: mit einem Befehl installieren

```bash
php bin/php/console exp:install
```

Ohne Optionen installiert dies das Seitenpaket `sevenx_multisite` auf **SQLite**, in `eng-US`, und gibt eine
Zusammenfassung mit der Adresse der Website, der Anmeldung für die Administration und einem erzeugten
Administratorpasswort aus (das auch in `var/log/initial-admin-password` geschrieben wird, nur für seinen Eigentümer
lesbar). Jede Option und die anderen Datenbanken sind in
[Die Installation auf der Konsole](../../../installation/07-installation-ueber-die-konsole.md) dokumentiert.

Viele Entwickler legen eine Abkürzung an, damit `./console` im Wurzelverzeichnis funktioniert:

```bash
ln -s bin/php/console console
./console list | head
```

Die Abkürzung gehört nicht zu den ausgelieferten Dateien; dieses Buch schreibt `./console` und setzt voraus, dass Sie sie
angelegt haben.

> **Warnung**
> `exp:install` weigert sich, über einer vorhandenen Installation zu laufen, außer Sie geben `--force` an, und sein
> Standard `--db-action=remove` leert die Datenbank, die es bekommt. Auf Ihrem eigenen Entwicklungsrechner mit SQLite
> ist das genau, was Sie wollen. Richten Sie es nie auf eine Datenbank, die etwas enthält, das Ihnen wichtig ist.

## Schritt 3: einen Webserver starten

Am schnellsten geht es mit Velocity und der Server-Engine, die in PHP eingebaut ist, dem ausgelieferten Standard:

```bash
./console exp:velocity start
```

`settings/velocity.ini` wird mit `[ServerSettings] Engine=php` ausgeliefert, das `php -S` mit dem Router
`bin/php/velocity-router.php` ausführt; sein Port wird in `[PHPServerSettings]` festgelegt (standardmäßig 8087, siehe
[Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)). Öffnen Sie die Adresse, die die Zusammenfassung von
`exp:install` ausgegeben hat, und dann die Administration. Für eine Einrichtung mit Apache oder nginx enthält dasselbe
Kapitel vollständige Beispiele.

## Schritt 4: Fehler sichtbar machen

Eine Live-Website verbirgt ihre Fehler vor den Besuchern. Eine Entwicklungs-Website sollte sie zeigen. Nehmen Sie die
Änderungen in **Ihrer eigenen Einstellungs-Erweiterung oder im Override-Verzeichnis Ihres Entwicklungsrechners** vor,
nie in `settings/site.ini` selbst:

```bash
./console exp:ini set site.ini/DebugSettings/DebugOutput enabled global --allow-root-user
./console exp:ini set site.ini/TemplateSettings/ShowUsedTemplates enabled global --allow-root-user
./console exp:ini set site.ini/TemplateSettings/TemplateCompile disabled global --allow-root-user
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

| Einstellung | Standard | Auf einem Entwicklungsrechner | Was sie tut |
|---|---|---|---|
| `[DebugSettings] DebugOutput` | `disabled` | `enabled` | Fehler, Warnungen, Zeitmessung und SQL erscheinen unter jeder Seite |
| `[TemplateSettings] ShowUsedTemplates` | `disabled` | `enabled` | Die Debug-Ausgabe listet jedes Template auf, das eine Seite verwendet hat, und welcher Override es gewählt hat |
| `[TemplateSettings] TemplateCompile` | `enabled` | `disabled`, solange Sie Templates bearbeiten | Templates werden bei jeder Anfrage frisch gelesen statt aus dem kompilierten Cache |
| `[TemplateSettings] ShowTemplatePathComments` | `disabled` | `enabled`, wenn Sie ein Template suchen | HTML-Kommentare im Seitenquelltext nennen das Template jedes Teils |

`exp:ini set ... global` schreibt `settings/override/site.ini.append.php` und legt eine Sicherung an; `--dry-run` zeigt
die Änderung zuerst. Die Einstellungen werden in
[Debug-Ausgabe und Logs für Entwickler](debug-ausgabe-und-logs-fuer-entwickler.md) erklärt.

> **Tipp**
> Die Debug-Ausgabe wird jedem angezeigt, der die Seite öffnen kann, außer Sie schränken sie ein. Auf einem Rechner,
> den nur Sie erreichen, ist das in Ordnung. Überall sonst verwenden Sie `DebugByIP=enabled` mit `DebugIPList[]` oder
> `DebugByUser=enabled` mit `DebugUserIDList[]`, beide in `[DebugSettings]`.

## Schritt 5: ein Platz für Ihre eigenen Erweiterungen

Legen Sie einen Ordner für die Erweiterung an, die Sie in den Tutorials bauen werden, und stellen Sie ihn von Anfang an
unter Versionskontrolle:

```bash
mkdir -p extension/mysite_hello
git -C extension/mysite_hello init
```

Wenn Sie Ihre eigenen Erweiterungen lieber außerhalb von `extension/` aufbewahren, kann der Kernel zusätzliche Ordner
durchsuchen; siehe [Erweiterungen in mehr als einem Ordner](../../../funktionen/6.0/zusaetzliche-extension-verzeichnisse.md).

## Regeln, die Entwicklung und Live-Betrieb getrennt halten

| Regel | Warum |
|---|---|
| Entwickeln Sie nie auf der Live-Website | Ein Tippfehler in einem Template oder einer Einstellungsdatei legt die Seite für jeden Besucher lahm. |
| Kopieren Sie `settings/override/` nie von Live zur Entwicklung oder zurück | Es enthält Datenbankpasswörter und hostspezifische Werte. Jeder Rechner behält seine eigenen. |
| Verwenden Sie eine Kopie der Live-Inhalte nur, wenn es sein muss | Live-Inhalte können personenbezogene Daten enthalten. Ein frisches `exp:install` mit dem Demopaket genügt meistens. |
| Committen Sie Ihre Erweiterung, nicht den Kernel | Ihre Arbeit lebt in `extension/<yours>`; der Kernel wird über Composer und Git aktualisiert, nicht durch Ihre Änderungen. |
| Schalten Sie die Debug-Ausgabe aus, bevor etwas live geht | Die Debug-Ausgabe zeigt Pfade, SQL und Einstellungen. |

## Wenn etwas schiefgeht

| Symptom | Was Sie prüfen |
|---|---|
| `composer install` bricht mit einem Fehler zur PHP-Version ab | `php -v`; der Kernel braucht PHP 8.0 oder neuer |
| `exp:install` bricht vor der Zusammenfassung ab | seine eigene Meldung nennt die Prüfung, die fehlgeschlagen ist; das Log liegt in `var/log/` |
| Die Website zeigt eine leere Seite | `var/log/error.log` und das PHP-Fehlerlog Ihres Webservers |
| Eine Änderung an einem Template wirkt nicht | die Template-Kompilierung: Leeren Sie den Template-Cache oder setzen Sie `TemplateCompile=disabled`, solange Sie entwickeln |
| Eine neue Klasse wird nicht gefunden | `php bin/php/ezpgenerateautoloads.php -e`, dann die Caches leeren |

## Verwandte Seiten

- Zurück: [Die Werkzeuge, die Sie brauchen](werkzeuge-die-sie-brauchen.md)
- Weiter: [Ihre erste Stunde als Entwickler](ihre-erste-stunde-als-entwickler.md)
- [Den Code holen](../../../installation/03-den-code-beschaffen.md), [Die Installation auf der Konsole](../../../installation/07-installation-ueber-die-konsole.md), [Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)
- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
