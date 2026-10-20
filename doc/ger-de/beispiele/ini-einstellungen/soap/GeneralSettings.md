---
title: soap.ini [GeneralSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GeneralSettings] in soap.ini: EnableSOAP, Logging, UseDefaultAccess"
keywords: Einstellung, soap.ini, GeneralSettings, INI, setting, EnableSOAP, Logging, UseDefaultAccess
type: generated
generated: exp:help:examples
---
# soap.ini [GeneralSettings]

Der Block `[GeneralSettings]` von `settings/soap.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/soap.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/soap.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/soap.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/soap.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## EnableSOAP

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/soap.ini.append.php
[GeneralSettings]
EnableSOAP=false
*/ ?>
```

## Logging

Standard: `disabled # not supported yet.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/soap.ini.append.php
[GeneralSettings]
Logging=disabled # not supported yet.
*/ ?>
```

## UseDefaultAccess

> If enabled, the instance's default siteaccess will always be used.  
> If disabled, the matched siteaccess (host, port, etc) will be used, allowing  
> the usage of soap when multiple exponential websites are hosted on one instance  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/soap.ini.append.php
[GeneralSettings]
UseDefaultAccess=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von soap.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/soap.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
