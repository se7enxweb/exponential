---
title: site.ini [RunnableSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RunnableSettings] in site.ini: Implementation, Listeners"
keywords: Einstellung, site.ini, RunnableSettings, INI, setting, Implementation, Listeners
type: generated
generated: exp:help:examples
---
# site.ini [RunnableSettings]

Der Block `[RunnableSettings]` von `settings/site.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Implementation

> Befehle (bin/), Cronjob-Teile (cronjobs/) und Modulansichten sind Klassen, die  
> Exponential\\Runnable\\Command, CronjobPart oder ModuleView erweitern.  
> Anleitung: doc/eng-us/bc/6.0/cli\_cronjob\_view\_abstractions.md  
>
> Neuimplementierung: die Klasse, die statt einer Runnable-Klasse erzeugt wird. Sie muss  
> die Klasse erweitern, die sie ersetzt; ein Eintrag, der etwas anderes nennt, wird ignoriert.  
> Implementation\[<runnable class>\]=<subclass>  
> z. B.:  
> Implementation\[Exponential\\View\\Kernel\\Content\\History\]=myHistoryView  
> Implementation\[Exponential\\Command\\Kernel\\Trashpurge\]=myTrashpurgeCommand  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RunnableSettings]
# ein leeres "Implementation[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Implementation[]
*/ ?>
```

## Listeners

> Listeners of the runnables' events, in the form of \[Event\] Listeners\[\]:  
> Listeners\[\]=<event>@<callback>  
> The events, <kind> being command, cronjob or view:  
>   runnable/<kind>/before   notified with ( $runnable, $class, $scope ) before run()  
>   runnable/<kind>/after    filters the result: ( $result, $runnable, $class, $scope ),  
>                            returns the result to use  
> eg:  
> Listeners\[\]=runnable/view/after@myViewTimer::after  
> These are attached for the runnables in every context, the command line too  
> (\[Event\] Listeners\[\] is attached to web requests only).  
> A command is created before its script has read the siteaccess and extension  
> settings, so Implementation\[\] and Listeners\[\] for a command are read from  
> settings/site.ini and settings/override/site.ini.append.php only.  
>
> runcronjobs.php asks one more filter before the scripts of a part run, after the  
> siteaccess and extension settings are loaded (its listener may come from either):  
>   cronjob/part/run         ( true, $part, $siteaccess, $scripts, $single ); return  
>                            true to run the part, anything else leaves it out. $part  
>                            is '' for \[CronjobSettings\] and for --script; $single is  
>                            true for --script, whose one file name is in $scripts  
> Listeners\[\]=cronjob/part/run@myCronjobStop::partRun  
> Guide: doc/eng-us/features/6.0/cronjob-part-filter.md  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[RunnableSettings]
# ein leeres "Listeners[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Listeners[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
