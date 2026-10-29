---
title: Mit Velocity ausliefern
audience: app-creator, developer, admin
category: app-creation
order: 530
summary: Code-Änderungen auf eine Website bringen, die Exponential Velocity ausliefert - exp:velocity deploy und seine vierzehn Schritte in Reihenfolge, die Optionen für Kernel-Klassen, gepackte Assets und reine Apache-Setups, Änderungen nur an Templates und die Prüfung des Ergebnisses.
keywords: Deployment, ausliefern, Velocity, Neustart, persistente Worker, Autoloads, Caches, Antwort-Cache, Packer, Anwendungsentwickler, deploy, exp:velocity deploy, restart, persistent workers, engine.phar, autoloads, caches, PHP-FPM, OPcache, response cache, packer, app creator
type: help
---
# Mit Velocity ausliefern

Diese Seite ist für Anwendungsentwickler, die eine Änderung auf einer Website live schalten, die Exponential Velocity
ausliefert. Velocity hält die Anwendung zwischen Anfragen im Speicher, daher wirkt eine neue Klasse oder Einstellung erst
nach einem Neustart, und Caches müssen in der richtigen Reihenfolge geleert werden. Ein einziger Befehl erledigt all das
([7. Eine PHP-Änderung mit einem Befehl ausliefern](../../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern)).

## Inhalt

1. [Der Befehl](#der-befehl)
2. [Was er tut, der Reihe nach](#was-er-tut-der-reihe-nach)
3. [Optionen](#optionen)
4. [Änderungen an Templates und Stylesheets](#änderungen-an-templates-und-stylesheets)
5. [Nach dem Deployment](#nach-dem-deployment)

## Der Befehl

```bash
./console exp:velocity deploy --dry-run --allow-root-user   # what it would do
./console exp:velocity deploy --allow-root-user             # the usual case
./console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

Ein echter Lauf gibt für jeden Schritt PASS, FAIL oder SKIP und die Dauer aus, hält beim ersten Fehler an und endet mit
dem Exit-Code 1.

## Was er tut, der Reihe nach

1. Autoloads der Erweiterungen (`--kernel`: auch die Autoloads des Kernels).
2. INI-Caches, Caches für Templates, Template-Overrides, Übersetzungen und Designs.
3. Das Engine-Archiv, nur neu gebaut, wenn sich Kernel-Dateien geändert haben (jede Datei wird zuerst geparst, sodass
   eine fehlerhafte Datei das Deployment stoppt, bevor irgendein Server berührt wird).
4. PHP-FPM sieht den neuen Code: ein Reload nur dann, wenn OPcache nicht selbst neu validiert
   (`[DeploySettings] PhpFpmReload`).
5. Neustart von Velocity; der neue Elternprozess muss antworten.
6. Caches für Inhalte, HTTP und Template-Blöcke, dann der Antwort-Cache von Velocity: zuletzt, damit eine vom alten Code
   gerenderte Seite nie erneut gecacht wird.
7. Aufwärmanfragen im Hintergrund (`[DeploySettings] WarmUpURL[]`).

## Optionen

| Option | Verwendung |
|---|---|
| `--kernel` | eine Kernel-Klasse wurde hinzugefügt oder umbenannt |
| `--packer` | ein Stylesheet, ein Skript oder eine Funktion `ezjscServer_*` hat sich geändert: auch gepackte Assets leeren |
| `--no-fpm` | nur Velocity |
| `--fpm-reload` | PHP-FPM trotzdem neu laden (php.ini oder Pool geändert, Dateien umbenannt) |
| `--no-velocity` | nur Apache oder nginx |
| `--no-autoload` | keine Änderungen an Klassen |
| `--rebuild-phar` | den Neubau des Engine-Archivs erzwingen |

## Änderungen an Templates und Stylesheets

Templates brauchen keinen Neustart:

```bash
./console exp:cache clear --id=template --allow-root-user
./console exp:velocity cache clear --apply --allow-root-user
```

Eine neue Template-Datei oder Override-Regel braucht zusätzlich `template-override`; ein Stylesheet braucht `--packer`
oder das Leeren der Caches `ezjscore-packer` und `template-block`.

## Nach dem Deployment

- `./console exp:velocity status`: Der neue Elternprozess antwortet.
- Öffnen Sie eine Seite jedes geänderten Typs, abgemeldet und angemeldet.
- Beobachten Sie `var/log/error.log` einige Minuten lang.
- Eine Klasse, auf die Einstellungen verweisen, muss ausgeliefert sein, bevor die Einstellung live geht, sonst scheitern
  Worker, die sie nicht kennen.

## Verwandte Seiten

- [Eine Deployment-Pipeline](deployment-pipeline.md)
- [Velocity-Engines: eine PHP-Änderung ausliefern](../../../bc/6.0/velocity-engines.md)
- [Velocity-Webserver](../../../funktionen/6.0/velocity-webserver.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
