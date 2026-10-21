---
title: paymentgateways.ini [GatewaysSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GatewaysSettings] in paymentgateways.ini: AvailableGateways, GatewaysDirectories"
keywords: Einstellung, paymentgateways.ini, GatewaysSettings, INI, setting, AvailableGateways, GatewaysDirectories
type: generated
generated: exp:help:examples
---
# paymentgateways.ini [GatewaysSettings]

Der Block `[GatewaysSettings]` von `settings/paymentgateways.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/paymentgateways.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/paymentgateways.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/paymentgateways.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/paymentgateways.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AvailableGateways

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/paymentgateways.ini.append.php
[GatewaysSettings]
# ein leeres "AvailableGateways[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableGateways[]
*/ ?>
```

## GatewaysDirectories

Standard: eine Liste mit 1: `kernel/shop/paymentgateways`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/paymentgateways.ini.append.php
[GatewaysSettings]
# ein leeres "GatewaysDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GatewaysDirectories[]
GatewaysDirectories[]=kernel/shop/paymentgateways
*/ ?>
```

## Siehe auch

- [Alle Blöcke von paymentgateways.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/paymentgateways.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
