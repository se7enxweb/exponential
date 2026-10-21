---
title: requestrules.ini [Rule-system_url_content_view_notfound]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Rule-system_url_content_view_notfound] in requestrules.ini: Description, Conditions, Action"
keywords: Einstellung, requestrules.ini, Rule-system_url_content_view_notfound, INI, setting, Description, Conditions, Action
type: generated
generated: exp:help:examples
---
# requestrules.ini [Rule-system_url_content_view_notfound]

Der Block `[Rule-system_url_content_view_notfound]` von `settings/requestrules.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/requestrules.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/requestrules.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/requestrules.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/requestrules.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Description

Standard: `Every content view typed as its system URL (any view mode) is not found, unless the user has the policy content/view_system_url.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[Rule-system_url_content_view_notfound]
Description=Every content view typed as its system URL (any view mode) is not found, unless the user has the policy content/view_system_url.
*/ ?>
```

## Conditions

Standard: ein Hash mit 3: `requested_via=system`, `module_view=content/view`, `policy=!content/view_system_url`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[Rule-system_url_content_view_notfound]
# ein leeres "Conditions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Conditions[]
Conditions[requested_via]=system
Conditions[module_view]=content/view
Conditions[policy]=!content/view_system_url
*/ ?>
```

## Action

Standard: `notfound`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[Rule-system_url_content_view_notfound]
Action=notfound
*/ ?>
```

## Siehe auch

- [Alle Blöcke von requestrules.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/requestrules.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
