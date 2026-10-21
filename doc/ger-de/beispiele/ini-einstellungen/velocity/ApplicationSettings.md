---
title: velocity.ini [ApplicationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ApplicationSettings] in velocity.ini: KeepGlobals, KeepGlobalsDefaults"
keywords: Einstellung, velocity.ini, ApplicationSettings, INI, setting, KeepGlobals, KeepGlobalsDefaults
type: generated
generated: exp:help:examples
---
# velocity.ini [ApplicationSettings]

Der Block `[ApplicationSettings]` von `settings/velocity.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## KeepGlobals

> Globals, die die Anwendung zwischen Anfragen behält.  
>
> Ein Register, das von einer Datei befüllt wird, die der Kernel mit include\_once erreicht, lässt sich  
> nach dem Leeren nicht neu befüllen, weil include\_once diese Datei nicht erneut ausführt.  
> Vier Register dieses Kernels werden so aufgebaut (Datentypen, Workflow-  
> Ereignisse, Benachrichtigungsereignisse und Zahlungs-Gateways), und das Leeren eines davon lässt  
> einen Worker für den Rest seines Lebens unfähig, es aufzulösen.  
>
> Das sind Tatsachen über den Kernel, sie sind also die eingebauten  
> Standards von exp:velocity (expVelocity::DEFAULT\_KEEP\_GLOBALS) und brauchen keine Einstellung. Diese Liste  
> ERGÄNZT sie, ebenso --keep-global=Name\[,Name\] bei start, restart und  
> command; jeder Name wird einmal behalten. \`exp:velocity command\` zeigt das Ergebnis.  
>
> Sonst gehört nichts hierher. Globals mit Anfrage-Gültigkeit müssen geleert werden, und  
> eines zu behalten lässt eine Anfrage in die nächste durchsickern.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ApplicationSettings]
# ein leeres "KeepGlobals[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
KeepGlobals[]
*/ ?>
```

## KeepGlobalsDefaults

> enabled (Standard) behält die eingebaute Liste; disabled beginnt bei nichts,  
> für einen Kernel, der diese Register nicht aufbaut.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[ApplicationSettings]
KeepGlobalsDefaults=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
