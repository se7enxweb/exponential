---
title: requestrules.ini [Rule-system_url_full_view_to_alias]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Rule-system_url_full_view_to_alias] in requestrules.ini: Description, Conditions, Action, ActionArgs, FallbackAction"
keywords: Einstellung, requestrules.ini, Rule-system_url_full_view_to_alias, INI, setting, Description, Conditions, Action, ActionArgs, FallbackAction
type: generated
generated: exp:help:examples
---
# requestrules.ini [Rule-system_url_full_view_to_alias]

Der Block `[Rule-system_url_full_view_to_alias]` von `settings/requestrules.ini`, mit 5 Einstellung(en).

> --- Fertig mitgelieferte Regeln -----------------------------------------------  
> Hier definiert und aus. Schalten Sie eine für einen Siteaccess ein, indem Sie ihren Namen dort  
> RuleList\[\] hinzufügen, z. B. in settings/siteaccess/site/requestrules.ini.append.php:  
>
>   \[RequestRuleSettings\]  
>   RuleList\[\]=system\_url\_full\_view\_to\_alias  

Bearbeiten Sie `settings/requestrules.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/requestrules.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/requestrules.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/requestrules.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Description

Standard: `A full view typed as its system URL (content/view/full/<node>) goes to the node's URL alias, unless the user has the policy content/view_system_url. Without an alias: not found.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[Rule-system_url_full_view_to_alias]
Description=A full view typed as its system URL (content/view/full/<node>) goes to the node's URL alias, unless the user has the policy content/view_system_url. Without an alias: not found.
*/ ?>
```

## Conditions

Standard: ein Hash mit 4: `requested_via=system`, `module_view=content/view`, `view_mode=full`, `policy=!content/view_system_url`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[Rule-system_url_full_view_to_alias]
# ein leeres "Conditions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Conditions[]
Conditions[requested_via]=system
Conditions[module_view]=content/view
Conditions[view_mode]=full
Conditions[policy]=!content/view_system_url
*/ ?>
```

## Action

Standard: `redirect_to_alias`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[Rule-system_url_full_view_to_alias]
Action=redirect_to_alias
*/ ?>
```

## ActionArgs

Standard: ein Hash mit 1: `status=301`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[Rule-system_url_full_view_to_alias]
# ein leeres "ActionArgs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ActionArgs[]
ActionArgs[status]=301
*/ ?>
```

## FallbackAction

Standard: `notfound`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[Rule-system_url_full_view_to_alias]
FallbackAction=notfound
*/ ?>
```

## Siehe auch

- [Alle Blöcke von requestrules.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/requestrules.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
