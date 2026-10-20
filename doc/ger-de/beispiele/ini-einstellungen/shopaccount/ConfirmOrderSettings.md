---
title: shopaccount.ini [ConfirmOrderSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ConfirmOrderSettings] in shopaccount.ini: Handler, Alias"
keywords: Einstellung, shopaccount.ini, ConfirmOrderSettings, INI, setting, Handler, Alias
type: generated
generated: exp:help:examples
---
# shopaccount.ini [ConfirmOrderSettings]

Der Block `[ConfirmOrderSettings]` von `settings/shopaccount.ini`, mit 2 Einstellung(en).

> Alias\[ezdefault\]=eztest  
> Settings for how order emails should be handled in the shop/checkout  
> default is to send confirmation emails to both customer and admin.  

Bearbeiten Sie `settings/shopaccount.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/shopaccount.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/shopaccount.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/shopaccount.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Handler

Standard: `ezdefault`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shopaccount.ini.append.php
[ConfirmOrderSettings]
Handler=ezdefault
*/ ?>
```

## Alias

> Erlaubt, einen Handler durch einen anderen zu ersetzen  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shopaccount.ini.append.php
[ConfirmOrderSettings]
# ein leeres "Alias[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Alias[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von shopaccount.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shopaccount.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
