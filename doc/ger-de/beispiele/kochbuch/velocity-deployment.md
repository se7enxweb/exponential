---
title: Deployment mit Velocity
audience: admin, developer
category: examples
order: 130
summary: Eine Änderung an PHP, INI, Template oder Stylesheet mit exp:velocity deploy live schalten – was jeder Schritt tut, welche Option jede Art von Änderung braucht, zuerst ein Probelauf, und wie Sie prüfen, dass auf jedem Server der neue Code antwortet.
keywords: Deployment, ausrollen, Velocity, PHP-FPM, Reload, Neustart, Probelauf, Cache leeren, Response-Cache, deploy, exp:velocity deploy, reload, restart, dry-run, kernel, packer, no-velocity, no-fpm, autoload, engine.phar, cache clear, PhpFpmService, DeploySettings, response cache
type: example
---
# Deployment mit Velocity

Eine laufende Website hält Code im Speicher: PHP-FPM-Worker haben ihren OPcache, die persistenten Worker von Velocity
haben die Klassen, die Einstellungen und die kompilierten Templates, die sie beim Start geladen haben. Eine Änderung
erreicht die Besucher erst, wenn diese erneuert **und** danach die Caches geleert werden, die vom alten Code erzeugte
Ausgaben enthalten. `exp:velocity deploy` erledigt beides, in der richtigen Reihenfolge, mit einem Befehl. Sie rollen
die Beispielerweiterung des Kochbuchs aus.

Voraussetzungen: eine Shell im Wurzelverzeichnis der Installation, als root (der Reload von PHP-FPM und der Neustart
eines Velocity, das auf Ports unter 1024 lauscht, brauchen das); Velocity installiert oder nicht (ohne Velocity werden
die Velocity-Schritte übersprungen).

## 1. Zuerst nachsehen

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user
```

Jeder Schritt wird mit dem ausgegeben, was er tun würde (`DRY`); nichts wird verändert. Prüfen Sie den gefundenen
PHP-FPM-Dienst: Auf einer Maschine mit mehreren PHP-Versionen ist der Dienst, der die Website ausliefert, oft nicht
der mit dem Namen `php-fpm`.

## 2. Welches Deployment für welche Änderung

| Sie haben geändert | Ausführen |
|---|---|
| Eine PHP-Klasse, einen Operator, Datentyp, ein Ereignis oder eine Ansicht einer Erweiterung | `exp:velocity deploy` |
| Eine **neue** oder umbenannte Klasse des Kernels (`kernel/`, `lib/`) | `exp:velocity deploy --kernel` |
| Eine ezjscore-Serverfunktion oder die Einstellungen des Packers | `exp:velocity deploy --packer` |
| Ein Stylesheet oder Skript eines Designs | `exp:velocity deploy --packer` (die gepackten Dateien und die Template-Blöcke, die sie verlinken) |
| Eine INI-Datei | `exp:velocity deploy` (es leert die INI-Caches und startet Velocity neu) |
| Nur ein Template | kein Deployment: `php bin/php/ezcache.php --clear-tag=template --allow-root-user` (bei einer neuen Template-Datei zusätzlich `--clear-id=template-override`), danach `./console exp:velocity cache clear` |
| Inhalte | nichts: Das Veröffentlichen lässt ablaufen, was es berührt |

```bash
php bin/php/console exp:velocity deploy --allow-root-user
```

## 3. Die Schritte

| # | Schritt | Von Hand |
|---|---|---|
| 1 | Autoloads der Erweiterungen | `php bin/php/ezpgenerateautoloads.php -e` |
| 2 | Autoloads des Kernels, nur mit `--kernel` (beschränkt auf `kernel/` und `lib/`) | `php bin/php/ezpgenerateautoloads.php -k` |
| 3 | INI-Caches | `php bin/php/ezcache.php --clear-tag=ini` |
| 4 | Caches template, template-override, translation und design_base | `--clear-id=<id>`, einer je Aufruf |
| 5 | Engine-Archiv, wenn Velocity aus einem läuft (nur neu gebaut, wenn sich darin etwas geändert hat) | `php bin/php/console exp:phar build` |
| 6 | PHP-FPM neu laden | `systemctl reload <[DeploySettings] PhpFpmService>` |
| 7 | Velocity neu starten, wenn es läuft | `exp:velocity restart` |
| 8 | Caches content, exphttpcache, (`--packer`: ezjscore-packer,) template-block | `--clear-id=<id>` |
| 9 | der Response-Cache von Velocity | `exp:velocity cache clear` |

Jeder Schritt gibt `PASS`, `FAIL` oder `SKIP` mit seiner Dauer aus; der erste Fehler beendet den Lauf mit
Exit-Status 1 und listet die nicht ausgeführten Schritte auf. Die Caches gerenderter Ausgaben (Schritte 8 und 9)
kommen **nach** dem Reload und dem Neustart: Früher geleert, würde eine zwischenzeitlich angefragte Seite vom alten
Code gerendert und erneut gecacht.

Optionen: `--kernel`, `--packer`, `--no-autoload`, `--no-fpm`, `--no-velocity`, `--rebuild-phar`, `--engine=<name>`,
`--dry-run`, `--json`.

## 4. Einstellungen

`settings/velocity.ini [DeploySettings]`, geändert in `settings/override/velocity.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[DeploySettings]
# auto: find the pool of this installation; a unit name (or several, comma-separated); disabled when Velocity
# serves the site alone
PhpFpmService=auto

*/ ?>
```

`auto` sucht die Pool-Konfiguration, die nach der Hosting-Domain der Installation benannt ist oder ihr Verzeichnis
nennt, in `/opt/plesk/php/*/etc/php-fpm.d`, `/etc/php-fpm.d`, `/etc/php/*/fpm/pool.d` und
`/etc/opt/remi/php*/php-fpm.d`. Der Reload erfolgt ohne Unterbrechung (`systemctl reload`): Keine Anfrage geht
verloren. Läuft der Befehl als anderer Benutzer als root, wird der Schritt mit einem Hinweis übersprungen, der den
Dienst nennt.

## 5. Prüfen, dass der neue Code antwortet

```bash
php bin/php/console exp:velocity status --allow-root-user       # the server and its workers, started a moment ago
php bin/php/console exp:phar check --allow-root-user            # the engine archive is current (when one is used)
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/   # 200 from the public site
```

Sehen Sie sich dann die Änderung selbst auf jedem Server an, der die Website ausliefert: am HTTPS-Port von Apache
oder nginx (PHP-FPM) und am Port von Velocity, wenn beide laufen. Ist eine Änderung auf dem einen sichtbar und auf dem
anderen nicht, läuft dort noch der alte Code: Der Reload hat ein anderes PHP-FPM gefunden, oder Velocity wurde nicht
neu gestartet.

Für diese Seite geprüft (2026-10-18): Die Schritte, ihre Reihenfolge und die Optionen sind die aus
`doc/eng-us/install/08-serving-the-site.md` (Abschnitt 8.3.14) und `settings/velocity.ini [DeploySettings]`. Die
Befehle wurden für diese Seite nicht ausgeführt.

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| Die Änderung wirkt am Port von Velocity, nicht an dem des Webservers | das neu geladene PHP-FPM ist nicht das der Website; setzen Sie `PhpFpmService` auf die Unit |
| `Class '...' not found` nach dem Hinzufügen einer Kernel-Klasse | ohne `--kernel` ausgerollt |
| Ein neues Stylesheet wird nicht verwendet | ohne `--packer` ausgerollt; danach ein harter Reload im Browser |
| Die Seite zeigt einige Sekunden lang noch den alten Text | der Response-Cache von Velocity: Schritt 9 lief vor Ihrer Anfrage, oder leeren Sie ihn erneut |
| Nur `exp:velocity restart`, und die Änderung scheint verloren | der Content-Cache wurde nach dem Neustart nicht geleert; verwenden Sie `deploy` |

## Abgeleitet aus

- `doc/eng-us/install/08-serving-the-site.md`, Abschnitte 8.3.14 und 8.3.15
- `settings/velocity.ini` `[DeploySettings]`; `kernel/classes/expvelocitydeploy.php`
- `doc/eng-us/bc/6.0/velocity-engines.md`

## Verwandte Seiten

- [Kochbuch](README.md)
- [Die Website ausliefern: Velocity, Apache, nginx und FrankenPHP](../../installation/08-die-website-ausliefern.md)
- [Velocity-Engines](../../../eng-us/bc/6.0/velocity-engines.md), [Engine-Archiv (phar)](../../../eng-us/bc/6.0/phar.md),
  [Bereitstellung](../../anleitungen/bereitstellung.md)
- [Caching](caching.md); die Konfigurationen für Webserver und Velocity in [Beispiele](../README.md)
- [Befehle](../befehle/README.md): `exp:velocity` und die anderen Konsolenbefehle
- Die Programmierregeln und der Musterkatalog: [die Standards](../../../eng-us/standards/README.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert
  werden.
