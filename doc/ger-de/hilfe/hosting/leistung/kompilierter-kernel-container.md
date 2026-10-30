---
title: Der kompilierte Kernel-Container
audience: hosting, admin
category: hosting
order: 130
summary: Was var/cache/kernelservices/ ist, was es bei jeder Anfrage unter PHP-FPM und Velocity spart, wann das Deployment und das Leeren des INI-Caches es neu aufbauen, wie Sie es mit exp:services:check prüfen und wie Sie es abschalten.
keywords: Kernel-Dienste, kompilierter Container, Startzeit, Deployment, kernel services, compiled container, var/cache/kernelservices, kernelservices.ini, exp:services:compile, exp:services:check, ContainerSettings, Mode, AutoRebuild, WorkerScope, EZP_INI_FILEMTIME_CHECK, boot time, Velocity worker scope, clear-tag=ini, deploy
type: help
---
# Der kompilierte Kernel-Container

Diese Seite ist für Systemadministratoren, die Exponential-Websites ausrollen und betreiben. Seit 6.0.15 hält der Kernel
einen **kompilierten Container** in `var/cache/kernelservices/`: für jeden Siteaccess eine Aufzeichnung, wie der Kernel
die Erweiterungen am Anfang jeder Anfrage aktiviert. Damit sucht eine Anfrage nicht mehr jedes Erweiterungsverzeichnis
auf der Platte, und unter Exponential Velocity baut ein Worker die Einstellungsdateien eines Siteaccess einmal statt bei
jeder Anfrage. Die Seite für Entwickler ist [der Kernel-Service-Container](../../php-entwickler/der-kernel-service-container.md);
die vollständige Referenz steht unter [Kernel-Dienste](../../../bc/6.0/kernel-dienste.md).

## Was es spart

Gemessen auf einer Testinstallation mit 62 aktiven Erweiterungen und 18 Siteaccesses (der Start einer Anfrage bis zum
Lauf des Moduls):

| | Vorher | Nachher |
|---|---|---|
| PHP-FPM, Start einer Anfrage | 38 bis 50 ms | 27 bis 36 ms (-15 bis -35 %) |
| Velocity, Start einer Anfrage | 21 bis 31 ms | 7 bis 14 ms (-51 bis -72 %) |

Das Rendern ändert sich dadurch nicht; eine Seite, die aus dem HTTP-Cache kommt, startet den Kernel nie und gewinnt
nichts.

## Normalerweise nichts zu tun

| Wann | Was geschieht |
|---|---|
| `exp:velocity deploy` | der INI-Schritt (`ezcache.php --clear-tag=ini`) kompiliert ihn neu; die Worker von Velocity bemerken den neuen bei ihrer nächsten Anfrage |
| `php bin/php/ezcache.php --clear-tag=ini` oder `--clear-all` auf der Kommandozeile | entfernt und neu kompiliert (ein, zwei Sekunden) |
| INI-Cache in der Administration geleert | entfernt; jeder Siteaccess zeichnet sich bei seiner nächsten Web-Anfrage neu auf |
| Erste Anfrage nach einer Installation | der Siteaccess zeichnet sich auf; die nächste Anfrage verwendet ihn |

Eine Anfrage verwendet den kompilierten Container nur, wenn sie von genau dem Zustand ausgeht, der aufgezeichnet wurde;
andernfalls arbeitet sie wie bisher. Ein veralteter Container macht eine Anfrage daher langsamer, nie anders.

## Prüfen

```bash
./console exp:services:check      # PASS or FAIL, the siteaccesses compiled, the generation, the worker scope
./console exp:services:compile    # compile it now
```

`exp:services:check` endet bei einem Fehler mit 1 und kann daher in einer Deployment-Prüfung oder einem Monitoring
laufen.

## Einstellungen

`settings/override/kernelservices.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[ContainerSettings]
# compiled (default), dev (nothing compiled is used) or disabled (the container takes no part in the boot)
Mode=compiled
# a web request records a siteaccess that has no current compiled file (default enabled)
AutoRebuild=enabled
# keep each siteaccess's settings files for a Velocity worker: auto = when config.php sets EZP_INI_FILEMTIME_CHECK
# to false (the INI cache does not look at file times either)
WorkerScope=auto
*/ ?>
```

Zum Abschalten setzen Sie `Mode=disabled` und leeren den INI-Cache: Der Kernel startet dann genau wie vor 6.0.15.

## Gut zu wissen

- **Eigentümer.** Velocity läuft als root, PHP-FPM als Benutzer der Website. Eine Datei, die der Container als root
  schreibt, wird dem Eigentümer von `var/` übergeben, damit der andere sie ersetzen kann.
- **Eine Einstellungsdatei von Hand bearbeiten.** Wie beim INI-Cache: Mit `EZP_INI_FILEMTIME_CHECK` false in
  `config.php` bemerkt nichts die Änderung, bis der INI-Cache geleert wird (oder das Deployment läuft), und das baut auch
  den Container neu.
- **Ein neues Erweiterungsverzeichnis**, das ohne Änderung der Einstellungen auf der Platte erscheint, braucht ein
  Deployment oder `exp:services:compile`, so wie es neue Autoloads braucht.
- **Mehrere Websites in einer Installation** (Multi-Site-Hosting, Host-Zuordnung): eine kompilierte Datei pro
  Siteaccess, und ein Velocity-Worker hält die Einstellungen jedes Siteaccess getrennt.

## Mehr erfahren

- [Kernel-Dienste (Referenz)](../../../bc/6.0/kernel-dienste.md)
- [Caches leeren und vorwärmen](caches-leeren-und-vorwaermen.md)
- [Velocity-Worker und Pools](../server/velocity-worker-und-pools.md)
- [Änderungen ausrollen](../betrieb/aenderungen-ausrollen.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
