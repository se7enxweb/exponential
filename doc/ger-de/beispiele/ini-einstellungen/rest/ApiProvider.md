---
title: rest.ini [ApiProvider]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ApiProvider] in rest.ini: ProviderClass"
keywords: Einstellung, rest.ini, ApiProvider, INI, setting, ProviderClass
type: generated
generated: exp:help:examples
---
# rest.ini [ApiProvider]

Der Block `[ApiProvider]` von `settings/rest.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ProviderClass

Standard: ein Hash mit 1: `auth=ezpRestAuthProvider`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[ApiProvider]
# ein leeres "ProviderClass[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ProviderClass[]
ProviderClass[auth]=ezpRestAuthProvider
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rest.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rest.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
