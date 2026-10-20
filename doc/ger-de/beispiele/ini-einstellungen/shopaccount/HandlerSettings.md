---
title: shopaccount.ini [HandlerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HandlerSettings] in shopaccount.ini: Repositories, ExtensionRepositories"
keywords: Einstellung, shopaccount.ini, HandlerSettings, INI, setting, Repositories, ExtensionRepositories
type: generated
generated: exp:help:examples
---
# shopaccount.ini [HandlerSettings]

Der Block `[HandlerSettings]` von `settings/shopaccount.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/shopaccount.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/shopaccount.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/shopaccount.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/shopaccount.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Repositories

> A list of directories to search for shopaccount handlers  

Standard: eine Liste mit 1: `kernel/classes`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shopaccount.ini.append.php
[HandlerSettings]
# ein leeres "Repositories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Repositories[]
Repositories[]=kernel/classes
*/ ?>
```

## ExtensionRepositories

> A list of extensions which have shop account handlers  
> It's common to create a settings/shopaccount.ini.append file  
> in your extension and add the extension name to automatically  
> get handlers from the extension when it's turned on.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shopaccount.ini.append.php
[HandlerSettings]
# ein leeres "ExtensionRepositories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionRepositories[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von shopaccount.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shopaccount.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
