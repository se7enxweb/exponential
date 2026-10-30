---
title: kernelservices.ini [ContainerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContainerSettings] in kernelservices.ini: Mode, CacheDir, AutoRebuild, WorkerScope"
keywords: Einstellung, kernelservices.ini, ContainerSettings, INI, setting, Mode, CacheDir, AutoRebuild, WorkerScope
type: generated
generated: exp:help:examples
---
# kernelservices.ini [ContainerSettings]

Der Block `[ContainerSettings]` von `settings/kernelservices.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Mode

> Diese vier werden gelesen, bevor die Erweiterungen aktiv sind: hier oder in  
> settings/override/kernelservices.ini.append.php setzen, nicht in einer Erweiterung.  
>
> compiled  der Web-Kernel wendet den kompilierten Container in CacheDir an: die  
>           einmal aufgezeichnete Erweiterungsaktivierung jedes Siteaccess, statt  
>           bei jeder Anfrage jedes Erweiterungsverzeichnis nachzuschlagen (der  
>           Standard). Ohne kompilierte Datei, oder wenn eine Anfrage nicht  
>           genau vom aufgezeichneten Zustand ausgeht, läuft er wie ohne  
>           Container.  
> dev       nichts Kompiliertes wird verwendet; Services werden aus den Einstellungen  
>           gelesen, wie sie gerade sind (für die Entwicklung)  
> disabled  der Container nimmt am Boot nicht teil; Services können weiterhin  
>           angefordert werden, gelesen aus den Einstellungen  

Standard: `compiled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[ContainerSettings]
Mode=compiled
*/ ?>
```

## CacheDir

> Wo der kompilierte Container liegt, relativ zum Installationsverzeichnis:  
> index.php und eine <siteaccess>.php pro Siteaccess.  

Standard: `var/cache/kernelservices`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[ContainerSettings]
CacheDir=var/cache/kernelservices
*/ ?>
```

## AutoRebuild

> enabled   eine Web-Anfrage eines Siteaccess ohne aktuelle kompilierte Datei  
>           zeichnet ihre eigene Erweiterungsaktivierung auf und schreibt sie, sodass die nächste  
>           Anfrage sie nutzt (nach einem INI-Cache-Leeren aus der  
>           Administration oder vor dem ersten Kompilieren)  
> disabled  nur exp:services:compile, der Deploy und ezcache.php auf der  
>           Kommandozeile schreiben sie  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[ContainerSettings]
AutoRebuild=enabled
*/ ?>
```

## WorkerScope

> Der Worker-Scope der Einstellungsdateien: jede Einstellungsdatei eines Siteaccess  
> wird einmal pro Worker gebaut und danach als Kopie ausgegeben. Bei Exponential  
> Velocity bedient ein Worker tausende Anfragen, das spart also das Laden jeder  
> Einstellungsdatei bei jeder Anfrage; bei PHP-FPM lebt ein Worker nur eine Anfrage lang.  
> auto      nur an, wenn der INI-Cache auch keine Dateizeiten prüft  
>           (EZP\_INI\_FILEMTIME\_CHECK false in config.php), sodass eine behaltene Einstellungsdatei  
>           genau das ist, was ihr erneutes Laden ergäbe (der Standard)  
> enabled   immer (eine bearbeitete Einstellungsdatei erreicht dann einen Velocity-Worker  
>           mit dem nächsten Kompilieren, INI-Cache-Leeren oder Neustart)  
> disabled  nie  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[ContainerSettings]
WorkerScope=auto
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
