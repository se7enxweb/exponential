---
title: logviewer.ini [DigestSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DigestSettings] in logviewer.ini: Hours, MaxMessages, MaxBytesPerFile, Levels, LogLevels, GroupBy"
keywords: Einstellung, logviewer.ini, DigestSettings, INI, setting, Hours, MaxMessages, MaxBytesPerFile, Levels, LogLevels, GroupBy
type: generated
generated: exp:help:examples
---
# logviewer.ini [DigestSettings]

Der Block `[DigestSettings]` von `settings/logviewer.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/logviewer.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logviewer.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logviewer.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logviewer.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Hours

> The error digest (Setup > Log files > Error digest, setup/logsdigest): the distinct messages of the last Hours  
> hours of each kernel log (or of one log), normalized (numbers, ids, hexadecimal values, quoted values and paths set  
> aside) and counted, most frequent first. Always masked. Known messages are hidden per user (the preference  
> admin\_logviewer\_known). The hours of the window when the page names none (1 to 168).  

Standard: `24`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[DigestSettings]
Hours=24
*/ ?>
```

## MaxMessages

> The most messages listed per log.  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[DigestSettings]
MaxMessages=50
*/ ?>
```

## MaxBytesPerFile

> The most bytes read per log from the start of the window (the start is found by a binary search on the times).  

Standard: `8388608`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[DigestSettings]
MaxBytesPerFile=8388608
*/ ?>
```

## Levels

> The levels listed.  

Standard: eine Liste mit 2: `error`, `warning`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[DigestSettings]
# ein leeres "Levels[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Levels[]
Levels[]=error
Levels[]=warning
*/ ?>
```

## LogLevels

> The levels listed for one log, by its key, instead of Levels\[\]: a comma list. The not found requests (404.log) are  
> notices, so the digest lists them only through this.  

Standard: ein Hash mit 1: `404=error,warning,notice`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[DigestSettings]
# ein leeres "LogLevels[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LogLevels[]
LogLevels[404]=error,warning,notice
*/ ?>
```

## GroupBy

> How the entries of a log are grouped, by its key:  
>   message        the normalized message (the default)  
>   category-rule  the first word of the line, <category>/<rule> (security.log: probe/wp-login, injection/sql ...)  
>   reason         the reason= field, with ids and paths set aside (404.log: module:foo, view:content/view, node:# ...)  
>   reason-kind    only the kind of the reason= field (module, view, disabled, node, object, rule, kernel)  
>   uri            the method and the path, without the query, numbers set aside (GET /blog/#)  
>   directive-blocked  the directive and the origin of what it blocked (csp.log: csp/script-src blocked=inline,  
>                  csp/img-src blocked=https://maps.example.com); the path of a blocked URI set aside  
> An entry that has no such field is grouped by its message.  

Standard: ein Hash mit 3: `404=reason`, `security=category-rule`, `csp=directive-blocked`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[DigestSettings]
# ein leeres "GroupBy[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupBy[]
GroupBy[404]=reason
GroupBy[security]=category-rule
GroupBy[csp]=directive-blocked
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logviewer.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logviewer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
