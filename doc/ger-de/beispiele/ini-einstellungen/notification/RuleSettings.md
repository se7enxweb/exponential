---
title: notification.ini [RuleSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RuleSettings] in notification.ini: RepositoryDirectories, ExtensionDirectories, Alias, LimitDeleteElements, RetryHours"
keywords: Einstellung, notification.ini, RuleSettings, INI, setting, RepositoryDirectories, ExtensionDirectories, Alias, LimitDeleteElements, RetryHours
type: generated
generated: exp:help:examples
---
# notification.ini [RuleSettings]

Der Block `[RuleSettings]` von `settings/notification.ini`, mit 5 Einstellung(en).

> IncludeClasses\[\]=article  
> IncludeClasses\[\]=file  

Bearbeiten Sie `settings/notification.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/notification.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/notification.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/notification.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RepositoryDirectories

Standard: eine Liste mit 1: `kernel/notification/rules`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[RuleSettings]
# ein leeres "RepositoryDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RepositoryDirectories[]
RepositoryDirectories[]=kernel/notification/rules
*/ ?>
```

## ExtensionDirectories

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[RuleSettings]
# ein leeres "ExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDirectories[]
*/ ?>
```

## Alias

Standard: ein Hash mit 3: `advanced=ezadvanced`, `general=ezgeneral`, `keyword=ezkeyword`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[RuleSettings]
# ein leeres "Alias[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Alias[]
Alias[advanced]=ezadvanced
Alias[general]=ezgeneral
Alias[keyword]=ezkeyword
*/ ?>
```

## LimitDeleteElements

> Höchstzahl der Elemente der Benachrichtigungs-Zusammenfassung, die in einer Abfrage entfernt werden.  
> Nötig, um die Grenzen des DBMS nicht zu überschreiten  
> (maximale Abfragelänge, maximale Zahl der Elemente in einer IN()-Klausel).  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[RuleSettings]
LimitDeleteElements=50
*/ ?>
```

## RetryHours

> Wie viele Stunden eine Benachrichtigung, die der Mail-Transport nicht angenommen hat, bei jedem Lauf erneut versucht wird, bevor sie aufgegeben  
> und auf der Seite des Benachrichtigungsstatus als verworfen gezählt wird.  

Standard: `72`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/notification.ini.append.php
[RuleSettings]
RetryHours=72
*/ ?>
```

## Siehe auch

- [Alle Blöcke von notification.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/notification.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
