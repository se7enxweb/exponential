---
title: admininterface.ini [WindowControlsSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [WindowControlsSettings] in admininterface.ini: HiddenTabs, AdditionalTabs"
keywords: Einstellung, admininterface.ini, WindowControlsSettings, INI, setting, HiddenTabs, AdditionalTabs
type: generated
generated: exp:help:examples
---
# admininterface.ini [WindowControlsSettings]

Der Block `[WindowControlsSettings]` von `settings/admininterface.ini`, mit 2 Einstellung(en).

> Settings used by window controls template  

Bearbeiten Sie `settings/admininterface.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/admininterface.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## HiddenTabs

> Additional tabs the node view leaves out, whatever AdditionalTabs\[\] says: a  
> siteaccess cannot take a tab out of AdditionalTabs\[\] that an extension appends  
> (extension settings are read after the siteaccess's). The editor siteaccess  
> hides the Layouts tab with it.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[WindowControlsSettings]
# ein leeres "HiddenTabs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HiddenTabs[]
*/ ?>
```

## AdditionalTabs

> List of additional tab identifiers  
> AdditionalTabs\[\]=customtab  
> The audit trail of a node (doc/eng-us/bc/6.0/audit.md, "Placement"), only with audit/read  

Standard: eine Liste mit 3: `roles`, `policies`, `audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[WindowControlsSettings]
# ein leeres "AdditionalTabs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdditionalTabs[]
AdditionalTabs[]=roles
AdditionalTabs[]=policies
AdditionalTabs[]=audit
*/ ?>
```

## Siehe auch

- [Alle Blöcke von admininterface.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/admininterface.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
