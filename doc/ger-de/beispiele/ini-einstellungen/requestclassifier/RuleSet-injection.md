---
title: requestclassifier.ini [RuleSet-injection]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RuleSet-injection] in requestclassifier.ini: Class, Patterns, Disabled"
keywords: Einstellung, requestclassifier.ini, RuleSet-injection, INI, setting, Class, Patterns, Disabled
type: generated
generated: exp:help:examples
---
# requestclassifier.ini [RuleSet-injection]

Der Block `[RuleSet-injection]` von `settings/requestclassifier.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/requestclassifier.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/requestclassifier.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/requestclassifier.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/requestclassifier.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> Gegen die Query und den einmal dekodierten Pfad geprüft; ein abgeschickter Inhalt wird nie angesehen. Regeln: sql-union,  
> sql-tautology (' or '1'='1), sql-boolean (or 1=1--), sql-time (sleep(, benchmark(), sql-schema  
> (information\_schema), sql-stacked (;drop table), sql-function, cmd-file (/etc/passwd), cmd-chain (;id, |cat, $(...),  
> Backticks), jndi, ssti ({{7\*7}}, ${7\*7}, #{...}, <%= %>), xss-script (<script, %3Cscript), xss-event (onerror=,  
> <svg/on...), xss-url (javascript:), php-code.  
> Eine Suche nach "union station", O'Reilly, "select or drop" oder <b> ist keine Injektion.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[RuleSet-injection]
Class=
*/ ?>
```

## Patterns

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[RuleSet-injection]
# ein leeres "Patterns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Patterns[]
*/ ?>
```

## Disabled

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[RuleSet-injection]
# ein leeres "Disabled[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Disabled[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von requestclassifier.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/requestclassifier.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
