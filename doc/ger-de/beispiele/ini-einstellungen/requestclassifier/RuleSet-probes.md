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

> Die Gruppen der Regelsätze: \[RuleSet-<identifier>\]. Für einen auf expRequestPatternRuleSet aufbauenden Satz (die eingebauten  
> Sätze außer threshold) ergänzen diese Einstellungen seine eingebauten Regeln:  
>   Class=<class>               die Klasse des Satzes (nötig für den Satz einer Erweiterung; leer: die eingebaute Klasse)  
>   Patterns\[<rule>\]=<regex>    eine eigene Regel (oder eine ersetzte eingebaute: gleicher Name). PCRE ohne  
>                               Begrenzer, ohne Beachtung der Groß-/Kleinschreibung geprüft; keine Rückverweise (\\1) oder benannten Gruppen.  
>                               Ein Muster, das sich nicht kompilieren lässt, wird mit einer Warnung ausgelassen. Vermeiden Sie ## (das  
>                               Kommentarzeichen der INI) in einem Muster.  
>   Disabled\[\]=<rule>           eine abgeschaltete eingebaute Regel  
>   Excludes\[<name>\]=<regex>    eine Anfrage, deren Pfad passt, wird von diesem Satz nie markiert (probes: der dekodierte  
>                               Namespace-Pfad); Excludes\[<built-in name>\]= (leer) entfernt einen eingebauten Ausschluss  
>   Confidence\[<rule>\]=low|medium|high, DefaultConfidence=high  
> Die Regelnamen unten sind die eingebauten (der zweite Teil des ersten Tokens einer Zeile).  

Bearbeiten Sie `settings/requestclassifier.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/requestclassifier.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/requestclassifier.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/requestclassifier.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> Gegen den dekodierten Namespace-Pfad geprüft. Regeln: wp-login, wp-admin, wp-content, wp-includes, wp-config,  
> xmlrpc, env, git, vcs (.svn .hg .bzr), ds-store, hidden (.htpasswd .htaccess .aws .ssh ...), dbtool (phpMyAdmin,  
> pma, adminer ...), phpunit, cgi-bin, server-status, actuator, phpinfo, config-file (composer.json, \*.ini ...),  
> exploit (hnap1, boaform ...), backup (ein Archiv oder Dump in der Wurzel des Namespace oder mit einem nach Sicherung klingenden Namen),  
> backup-copy (\*.bak, \*.old, \*~).  
> Eingebaute Ausschlüsse, nie eine Sondierung: download (/content/download/...), storage (var/storage, var/<site>/storage),  
> public-cache (var/<site>/cache/public: gepackte Skripte und Styles), assets (Stylesheets, Skripte, Bilder und Schriften  
> unter design/, extension/ und share/).  
> Beispiel, ein eigener Pfad:  
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
