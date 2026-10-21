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

> enabled | disabled. Mit disabled wird keine Anfrage eingeordnet: Sondierungen werden als die Nicht-gefunden-Antworten protokolliert, die sie  
> sind (404.log), und security.log bleibt leer.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[ClassifierSettings]
Classification=enabled
*/ ?>
```

## LogFile

> Die Datei, in die die Zeilen geschrieben werden, im Log-Verzeichnis der Installation (var/log, oder var/<site>/log mit  
> site.ini \[FileSettings\] UseGlobalLogDir=disabled). Sie wird wie die anderen Logs rotiert (site.ini \[FileSettings\]  
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

> Die Regelsätze, in der Reihenfolge, in der sie versucht werden; der erste mit einem Urteil gewinnt, eine Anfrage steht also in einer Zeile  
> mit einer Regel. Jeder Eintrag ist ein Bezeichner; seine Klasse und seine Einstellungen stehen in der Gruppe \[RuleSet-<identifier>\]  
> unten. Eingebaut:  
>   probes      Kategorie probe: Dinge, die eine Exponential-Website nicht hat und nach denen Scanner suchen  
>   traversal   Kategorie traversal: ../ und seine Kodierungen, Null-Bytes  
>   injection   Kategorie injection: SQL-, Befehls-, Template- und Skript-Injektion in der URL  
>   scanner     Kategorie scanner: die User-Agents bekannter Angriffswerkzeuge  
>   threshold   Kategorie threshold: mehr als \[ThresholdSettings\] Max404PerMinute Nicht-gefunden-Antworten pro Minute  
> Eine Erweiterung fügt ihren eigenen Satz mit RuleSets\[\]=<identifier> und einer Gruppe \[RuleSet-<identifier>\] mit  
> Class=<its class> hinzu, einer Klasse, die expRequestRuleSetInterface implementiert (oder expRequestPatternRuleSet erweitert) und  
> im Autoload der Erweiterung steht. Eine Klasse, die nicht existiert oder die Schnittstelle nicht implementiert, wird mit  
> einer Warnung in der Debug-Ausgabe übersprungen. Lassen Sie einen eingebauten Satz aus der Liste weg, um ihn abzuschalten.  

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

> Klassen, die nach dem Schreiben der Zeile über jedes Urteil informiert werden: Klassen, die  
> expRequestVerdictListenerInterface implementieren (onVerdict( $verdict, $context )), zum Beispiel um eine Sperrliste oder eine  
> Benachrichtigung zu speisen. Standardmäßig keine. Ein Listener läuft am Ende der Anfrage; einer, der scheitert, wird übersprungen.  

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

> enabled | disabled. Mit enabled werden die kompilierten Muster in einer PHP-Datei im Cache-Verzeichnis gehalten (und in  
> APCu, wo es verfügbar ist), sodass ein frisch startender Worker (Velocity setzt seinen Zustand zwischen Anfragen zurück) sie  
> nicht neu kompiliert. Mit disabled werden sie einmal pro Anfrage kompiliert.  

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
