---
title: requestclassifier.ini [RuleSet-probes]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RuleSet-probes] in requestclassifier.ini: Class, Patterns, Disabled, Excludes"
keywords: Einstellung, requestclassifier.ini, RuleSet-probes, INI, setting, Class, Patterns, Disabled, Excludes
type: generated
generated: exp:help:examples
---
# requestclassifier.ini [RuleSet-probes]

Der Block `[RuleSet-probes]` von `settings/requestclassifier.ini`, mit 4 Einstellung(en).

> The groups of the rule sets: \[RuleSet-<identifier>\]. For a set built on expRequestPatternRuleSet (the built-in  
> sets but threshold) these settings add to its built-in rules:  
>   Class=<class>               the class of the set (needed for an extension's set; empty: the built-in class)  
>   Patterns\[<rule>\]=<regex>    a rule of your own (or a built-in rule replaced: same name). PCRE without  
>                               delimiters, matched case-insensitively; no back references (\\1) or named groups.  
>                               A pattern that does not compile is left out with a warning. Avoid ## (the INI  
>                               comment marker) in a pattern.  
>   Disabled\[\]=<rule>           a built-in rule switched off  
>   Excludes\[<name>\]=<regex>    a request whose path matches is never flagged by this set (probes: the decoded  
>                               namespace path); Excludes\[<built-in name>\]= (empty) removes a built-in exclude  
>   Confidence\[<rule>\]=low|medium|high, DefaultConfidence=high  
> The rule names below are the built-in ones (the second part of the first token of a line).  

Bearbeiten Sie `settings/requestclassifier.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/requestclassifier.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/requestclassifier.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/requestclassifier.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> Matched against the decoded namespace path. Rules: wp-login, wp-admin, wp-content, wp-includes, wp-config,  
> xmlrpc, env, git, vcs (.svn .hg .bzr), ds-store, hidden (.htpasswd .htaccess .aws .ssh ...), dbtool (phpMyAdmin,  
> pma, adminer ...), phpunit, cgi-bin, server-status, actuator, phpinfo, config-file (composer.json, \*.ini ...),  
> exploit (hnap1, boaform ...), backup (an archive or dump at the namespace root, or with a backup-like name),  
> backup-copy (\*.bak, \*.old, \*~).  
> Built-in excludes, never a probe: download (/content/download/...), storage (var/storage, var/<site>/storage),  
> public-cache (var/<site>/cache/public: packed scripts and styles), assets (stylesheets, scripts, images and fonts  
> under design/, extension/ and share/).  
> Example, a path of your own:  
> Patterns\[old-admin\]=^/administrator(/|$)  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[RuleSet-probes]
Class=
*/ ?>
```

## Patterns

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[RuleSet-probes]
# ein leeres "Patterns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Patterns[]
*/ ?>
```

## Disabled

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[RuleSet-probes]
# ein leeres "Disabled[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Disabled[]
*/ ?>
```

## Excludes

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[RuleSet-probes]
# ein leeres "Excludes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Excludes[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von requestclassifier.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/requestclassifier.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
