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

> The kernel notifies content/publish ( object id, version ), content/move ( node id, object id, old parent, new  
> parent ) and content/remove ( object ids, node ids ). The live mirror of the data tables is called by the kernel  
> itself, in every process (doc/eng-us/features/6.0/data-tables.md, "Live mirror").  

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

> Listener der Ereignisse der Runnables, in der Form von \[Event\] Listeners\[\]:  
> Listeners\[\]=<event>@<callback>  
> Die Ereignisse, wobei <kind> command, cronjob oder view ist:  
>   runnable/<kind>/before   benachrichtigt mit ( $runnable, $class, $scope ) vor run()  
>   runnable/<kind>/after    filtert das Ergebnis: ( $result, $runnable, $class, $scope ),  
>                            gibt das zu verwendende Ergebnis zurück  
> z. B.:  
> Listeners\[\]=runnable/view/after@myViewTimer::after  
> Diese werden für die Runnables in jedem Kontext angehängt, auch auf der Kommandozeile  
> (\[Event\] Listeners\[\] wird nur an Web-Anfragen angehängt).  
> Ein Befehl wird erzeugt, bevor sein Skript die Siteaccess- und Erweiterungs-  
> einstellungen gelesen hat, daher werden Implementation\[\] und Listeners\[\] für einen Befehl nur aus  
> settings/site.ini und settings/override/site.ini.append.php gelesen.  
>
> runcronjobs.php fragt einen weiteren Filter, bevor die Skripte eines Teils laufen, nachdem die  
> Siteaccess- und Erweiterungseinstellungen geladen sind (sein Listener kann aus beiden kommen):  
>   cronjob/part/run         ( true, $part, $siteaccess, $scripts, $single ); geben Sie  
>                            true zurück, um den Teil auszuführen; alles andere lässt ihn aus. $part  
>                            ist '' für \[CronjobSettings\] und für --script; $single ist  
>                            true für --script, dessen einziger Dateiname in $scripts steht  
> Listeners\[\]=cronjob/part/run@myCronjobStop::partRun  
> Anleitung: doc/eng-us/features/6.0/cronjob-part-filter.md  

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
