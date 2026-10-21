---
title: requestrules.ini [Rule-system_url_full_view_audit]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Rule-system_url_full_view_audit] in requestrules.ini: Description, Conditions, Action, ActionArgs"
keywords: Einstellung, requestrules.ini, Rule-system_url_full_view_audit, INI, setting, Description, Conditions, Action, ActionArgs
type: generated
generated: exp:help:examples
---
# requestrules.ini [Rule-system_url_full_view_audit]

Der Block `[Rule-system_url_full_view_audit]` von `settings/requestrules.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/requestrules.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/requestrules.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/requestrules.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/requestrules.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Description

Standard: `Writes a line to var/log/requestrules.log for every full view typed as its system URL by a user without content/view_system_url, and changes nothing. Put it first to see who would be affected before switching a stricter rule on.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[Rule-system_url_full_view_audit]
Description=Writes a line to var/log/requestrules.log for every full view typed as its system URL by a user without content/view_system_url, and changes nothing. Put it first to see who would be affected before switching a stricter rule on.
*/ ?>
```

## Conditions

Standard: ein Hash mit 4: `requested_via=system`, `module_view=content/view`, `view_mode=full`, `policy=!content/view_system_url`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[Rule-system_url_full_view_audit]
# ein leeres "Conditions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Conditions[]
Conditions[requested_via]=system
Conditions[module_view]=content/view
Conditions[view_mode]=full
Conditions[policy]=!content/view_system_url
*/ ?>
```

## Action

Standard: `log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[Rule-system_url_full_view_audit]
Action=log
*/ ?>
```

## ActionArgs

Standard: ein Hash mit 1: `message=system URL {typed_uri} (node {node_id}) by {user_login}`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[Rule-system_url_full_view_audit]
# ein leeres "ActionArgs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ActionArgs[]
ActionArgs[message]=system URL {typed_uri} (node {node_id}) by {user_login}
*/ ?>
```

## Siehe auch

- [Alle Blöcke von requestrules.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/requestrules.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
