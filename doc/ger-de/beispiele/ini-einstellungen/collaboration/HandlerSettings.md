---
title: collaboration.ini [HandlerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HandlerSettings] in collaboration.ini: Extensions, Repositories, Active"
keywords: Einstellung, collaboration.ini, HandlerSettings, INI, setting, Extensions, Repositories, Active
type: generated
generated: exp:help:examples
---
# collaboration.ini [HandlerSettings]

Der Block `[HandlerSettings]` von `settings/collaboration.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/collaboration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/collaboration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/collaboration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/collaboration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Extensions

> A list of extensions which have collaboration code  
> It's common to create a settings/collaboration.ini.append file  
> in your extension and add the extension name to automatically  
> get code from the extension when it's turned on.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collaboration.ini.append.php
[HandlerSettings]
# ein leeres "Extensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Extensions[]
*/ ?>
```

## Repositories

Standard: eine Liste mit 1: `kernel/classes/collaborationhandlers`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collaboration.ini.append.php
[HandlerSettings]
# ein leeres "Repositories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Repositories[]
Repositories[]=kernel/classes/collaborationhandlers
*/ ?>
```

## Active

Standard: eine Liste mit 1: `ezapprove`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collaboration.ini.append.php
[HandlerSettings]
# ein leeres "Active[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Active[]
Active[]=ezapprove
*/ ?>
```

## Siehe auch

- [Alle Blöcke von collaboration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/collaboration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
