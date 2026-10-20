---
title: mailpreferences.ini [SecretSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SecretSettings] in mailpreferences.ini: TokenSecret"
keywords: Einstellung, mailpreferences.ini, SecretSettings, INI, setting, TokenSecret
type: generated
generated: exp:help:examples
---
# mailpreferences.ini [SecretSettings]

Der Block `[SecretSettings]` von `settings/mailpreferences.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/mailpreferences.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mailpreferences.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mailpreferences.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mailpreferences.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TokenSecret

> Das Website-Geheimnis der Links und der Adress-Hashes: bei der ersten Verwendung  
> in settings/override/mailpreferences.ini.append.php erzeugt (nie committet).  
> Eine Änderung macht jeden Link in gesendeten Mails ungültig und jeden gespeicherten Hash  
> unauffindbar.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[SecretSettings]
TokenSecret=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mailpreferences.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
