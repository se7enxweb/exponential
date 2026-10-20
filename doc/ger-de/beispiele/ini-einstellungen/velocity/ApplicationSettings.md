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

> Globals the application keeps between requests.  
>
> A registry filled by a file the kernel reaches with include\_once cannot be  
> refilled once cleared, because include\_once will not run that file again.  
> Four registries in this kernel are built that way -- datatypes, workflow  
> events, notification events and payment gateways -- and clearing one leaves  
> a worker unable to resolve it for the rest of its life.  
>
> Those are facts about the kernel, so they are exp:velocity's built-in  
> defaults (expVelocity::DEFAULT\_KEEP\_GLOBALS) and need no setting. This list  
> ADDS to them, as does --keep-global=Name\[,Name\] on start, restart and  
> command; each name is kept once. \`exp:velocity command\` shows the result.  
>
> Nothing else belongs here. Request-scoped globals must be cleared, and  
> keeping one leaks a request into the next.  

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

> enabled (default) keeps the built-in list; disabled starts from nothing,  
> for a kernel that does not build those registries.  

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
