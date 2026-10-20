---
title: requestclassifier.ini [ClassifierSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ClassifierSettings] in requestclassifier.ini: Classification, LogFile, RuleSets, Listeners, CompileCache"
keywords: Einstellung, requestclassifier.ini, ClassifierSettings, INI, setting, Classification, LogFile, RuleSets, Listeners, CompileCache
type: generated
generated: exp:help:examples
---
# requestclassifier.ini [ClassifierSettings]

Der Block `[ClassifierSettings]` von `settings/requestclassifier.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/requestclassifier.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/requestclassifier.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/requestclassifier.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/requestclassifier.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Classification

> enabled | disabled. With disabled no request is classified: probes are logged as the not-found answers they  
> are (404.log) and security.log stays empty.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[ClassifierSettings]
Classification=enabled
*/ ?>
```

## LogFile

> The file the lines are written to, in the log directory of the installation (var/log, or var/<site>/log with  
> site.ini \[FileSettings\] UseGlobalLogDir=disabled). It is rotated like the other logs (site.ini \[FileSettings\]  
> LogMaxSize, LogMaxCount).  

Standard: `security.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[ClassifierSettings]
LogFile=security.log
*/ ?>
```

## RuleSets

> The rule sets, in the order they are tried; the first one that has a verdict wins, so a request is in one line  
> with one rule. Each entry is an identifier; its class and its settings are in the group \[RuleSet-<identifier>\]  
> below. Built in:  
>   probes      category probe: things an Exponential site does not have and scanners look for  
>   traversal   category traversal: ../ and its encodings, null bytes  
>   injection   category injection: SQL, command, template and script injection in the URL  
>   scanner     category scanner: the user agents of known attack tools  
>   threshold   category threshold: more than \[ThresholdSettings\] Max404PerMinute not-found answers a minute  
> An extension adds its own set with RuleSets\[\]=<identifier> and a group \[RuleSet-<identifier>\] with  
> Class=<its class>, a class that implements expRequestRuleSetInterface (or extends expRequestPatternRuleSet) and  
> is in the extension's autoload. A class that does not exist or does not implement the interface is skipped with  
> a warning in the debug output. Leave a built-in set out of the list to switch it off.  

Standard: eine Liste mit 5: `probes`, `traversal`, `injection`, `scanner`, `threshold`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[ClassifierSettings]
# ein leeres "RuleSets[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RuleSets[]
RuleSets[]=probes
RuleSets[]=traversal
RuleSets[]=injection
RuleSets[]=scanner
RuleSets[]=threshold
*/ ?>
```

## Listeners

> Classes told about every verdict after its line is written: classes that implement  
> expRequestVerdictListenerInterface (onVerdict( $verdict, $context )), for example to feed a ban list or a  
> notification. None by default. A listener runs at the end of the request; one that fails is skipped.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[ClassifierSettings]
# ein leeres "Listeners[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Listeners[]
*/ ?>
```

## CompileCache

> enabled | disabled. With enabled the compiled patterns are kept in a PHP file in the cache directory (and in  
> APCu where it is available), so a worker that starts fresh (Velocity resets its state between requests) does not  
> compile them again. With disabled they are compiled once per request.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[ClassifierSettings]
CompileCache=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von requestclassifier.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/requestclassifier.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
