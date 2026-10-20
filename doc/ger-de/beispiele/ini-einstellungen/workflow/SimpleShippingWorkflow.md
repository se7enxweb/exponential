---
title: workflow.ini [SimpleShippingWorkflow]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SimpleShippingWorkflow] in workflow.ini: ShippingCost, ShippingDescription"
keywords: Einstellung, workflow.ini, SimpleShippingWorkflow, INI, setting, ShippingCost, ShippingDescription
type: generated
generated: exp:help:examples
---
# workflow.ini [SimpleShippingWorkflow]

Der Block `[SimpleShippingWorkflow]` von `settings/workflow.ini`, mit 2 Einstellung(en).

> Setting for shipping cost for the simple shipping workflow  

Bearbeiten Sie `settings/workflow.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/workflow.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/workflow.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/workflow.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ShippingCost

Standard: `12`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/workflow.ini.append.php
[SimpleShippingWorkflow]
ShippingCost=12
*/ ?>
```

## ShippingDescription

Standard: `Shipping`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/workflow.ini.append.php
[SimpleShippingWorkflow]
ShippingDescription=Shipping
*/ ?>
```

## Siehe auch

- [Alle Blöcke von workflow.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/workflow.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
