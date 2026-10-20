---
title: setup.ini [RegistrationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RegistrationSettings] in setup.ini: Receiver"
keywords: Einstellung, setup.ini, RegistrationSettings, INI, setting, Receiver
type: generated
generated: exp:help:examples
---
# setup.ini [RegistrationSettings]

Der Block `[RegistrationSettings]` von `settings/setup.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/setup.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/setup.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/setup.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/setup.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Receiver

> Wohin der Registrierungsschritt des Setups seinen Bericht mailt, wenn kickstart.ini  
> \[registration\] Send=true (der Standard ist false). Leer: Es wird nichts gesendet.  
> Die alte Registrierungsadresse des Upstream-Projekts existiert nicht mehr und wird nie verwendet.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/setup.ini.append.php
[RegistrationSettings]
Receiver=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von setup.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/setup.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
