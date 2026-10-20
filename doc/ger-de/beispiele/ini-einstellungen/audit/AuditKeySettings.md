---
title: audit.ini [AuditKeySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AuditKeySettings] in audit.ini: GenerateKeys"
keywords: Einstellung, audit.ini, AuditKeySettings, INI, setting, GenerateKeys
type: generated
generated: exp:help:examples
---
# audit.ini [AuditKeySettings]

Der Block `[AuditKeySettings]` von `settings/audit.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/audit.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/audit.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/audit.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/audit.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## GenerateKeys

> Bei der ersten Verwendung in settings/override/audit.ini.append.php erzeugt (Z7); nie hier,  
> nie committet. In der Konsole nur als Fingerabdrücke angezeigt.  
> InstallationID=<uuid>  
> ActiveSigningKey=<key id>  
> SigningKey\[<key id>\]=<base64>  
> PseudonymKey=<base64>  
> Die Schlüssel beim ersten Ereignis erzeugen.  enabled | disabled (disabled: eine Installation,  
> die ihre eigenen Schlüssel bereitstellt, z. B. aus einem Secret Store, über settings/override).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/audit.ini.append.php
[AuditKeySettings]
GenerateKeys=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von audit.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/audit.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
