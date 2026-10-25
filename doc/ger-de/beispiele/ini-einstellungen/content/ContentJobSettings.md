---
title: content.ini [ContentJobSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContentJobSettings] in content.ini: SynchronousLimit, NowLimit, BatchSize, BatchPause, QueuedGrace, QueuedTimeout, MaxAttempts, KeepDays ..."
keywords: Einstellung, content.ini, ContentJobSettings, INI, setting, SynchronousLimit, NowLimit, BatchSize, BatchPause, QueuedGrace, QueuedTimeout, MaxAttempts, KeepDays, PhpBinary, JobTypes
type: generated
generated: exp:help:examples
---
# content.ini [ContentJobSettings]

Der Block `[ContentJobSettings]` von `settings/content.ini`, mit 10 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SynchronousLimit

> Inhaltsaufträge: Das Entfernen großer Teilbäume (content/removeobject, content/removenode) und das Kopieren von Teilbäumen  
> (content/copysubtree) läuft stapelweise in einem Hintergrund-Worker statt in der Web-Anfrage, sodass es  
> nie an einer Zeitgrenze der Anfrage, einer Speichergrenze oder einer riesigen Transaktion scheitert. Jeder Stapel ist eine  
> eigene Transaktion, gefolgt von einem Prüfpunkt: Ein Auftrag macht nach jeder  
> Unterbrechung (auch kill -9) dort weiter, wo er aufgehört hat. Klassen in kernel/classes/contentjob/; der Befehl  
> bin/php/expcontentjob.php (./console exp:expcontentjob list|show|run|resume|cancel|remove|copy).  
> Aufträge liegen in var/<site var dir>/jobs/content/ (ein Cluster braucht dafür ein gemeinsames var-Verzeichnis).  
>
> Der Benutzer wählt pro Operation, ob sie jetzt (in der Anfrage) oder als Hintergrundauftrag läuft; dies  
> legt die zuerst angebotene Wahl fest: unter dieser Zahl von Knoten „jetzt“, ab ihr „Auftrag“. 0 = immer „Auftrag“.  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
SynchronousLimit=50
*/ ?>
```

## NowLimit

> „Jetzt“ wird über dieser Zahl von Knoten abgelehnt, mit dem Hinweis, es als Auftrag auszuführen. 0 = keine Obergrenze.  

Standard: `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
NowLimit=1000
*/ ?>
```

## BatchSize

> Knoten pro Stapel (jeder eine Transaktion).  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
BatchSize=50
*/ ?>
```

## BatchPause

> Pause zwischen den Stapeln in Millisekunden (0 = keine), um anderen Schreibern Raum zu lassen.  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
BatchPause=0
*/ ?>
```

## QueuedGrace

> Einen wartenden Auftrag, dessen Worker nicht innerhalb so vieler Sekunden gestartet ist, startet der Cronjob-Teil  
> 'contentjobs' (Gruppe frequent); ein laufender Auftrag, dessen Worker gestorben ist, wird sofort gestartet.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
QueuedGrace=60
*/ ?>
```

## QueuedTimeout

> Ein wartender Auftrag, den innerhalb so vieler Sekunden kein Worker gestartet hat (keine Cronjob-Läufe, der Start ging verloren), schlägt fehl  
> und gibt seine Sperren zurück, wenn er noch nichts getan hat, sodass er nie einen Teilbaum blockiert; setzen Sie ihn fort, um es erneut zu versuchen.  
> Ein Worker, der gar nicht gestartet werden kann, lässt den Auftrag sofort fehlschlagen, mit dem Grund. 0 = läuft nie ab.  

Standard: `600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
QueuedTimeout=600
*/ ?>
```

## MaxAttempts

> Ein Auftrag, dessen Worker so oft gestartet wird, ohne einen Stapel abzuschließen, wird als fehlgeschlagen markiert (mit dem  
> letzten Fehler), statt endlos wiederholt zu werden. Setzen Sie ihn fort, sobald die Ursache behoben ist.  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
MaxAttempts=5
*/ ?>
```

## KeepDays

> Abgeschlossene (fertige oder abgebrochene) Aufträge entfernt der Cronjob-Teil nach so vielen Tagen. 0 = nie.  

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
KeepDays=30
*/ ?>
```

## PhpBinary

> Die PHP-Kommandozeilen-Binärdatei für den Worker; leer = das PHP, das die Anfrage ausführt (PHP\_BINDIR/php).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
PhpBinary=
*/ ?>
```

## JobTypes

> The job types: JobTypes\[<name>\]=<class implementing expContentJobType>.  
> A package install in the background (Setup > Packages > Install, "Install in the background"), doc/eng-us/bc/6.0/package-json-format.md  
> An export of the audit console in the background (Audit > Export, "Export in the background"), doc/eng-us/bc/6.0/audit.md  
> The help's manuals built from the Markdown help in the background (a new installation queues it), doc/eng-us/features/6.0/help-content.md  
> A background run of an import or export tool (Export, CIE ...) when there is no job queue,  
> doc/eng-us/features/6.0/transfer-runs.md (transfer.ini)  

Standard: ein Hash mit 13: `remove=expContentJobRemoveSubtree`, `copy=expContentJobCopySubtree`, `move=expContentJobMoveSubtree`, `hide=expContentJobHideSubtree`, `reveal=expContentJobRevealSubtree`, `section=expContentJobSectionSubtree`, `state=expContentJobStateSubtree`, `addlocation=expContentJobAddLocation`, `removelocation=expContentJobRemoveLocation`, `packageinstall=expContentJobPackageInstall`, `auditexport=expContentJobAuditExport`, `helpmanuals=expContentJobHelpManuals` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentJobSettings]
# ein leeres "JobTypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
JobTypes[]
JobTypes[remove]=expContentJobRemoveSubtree
JobTypes[copy]=expContentJobCopySubtree
JobTypes[move]=expContentJobMoveSubtree
JobTypes[hide]=expContentJobHideSubtree
JobTypes[reveal]=expContentJobRevealSubtree
JobTypes[section]=expContentJobSectionSubtree
JobTypes[state]=expContentJobStateSubtree
JobTypes[addlocation]=expContentJobAddLocation
JobTypes[removelocation]=expContentJobRemoveLocation
JobTypes[packageinstall]=expContentJobPackageInstall
JobTypes[auditexport]=expContentJobAuditExport
JobTypes[helpmanuals]=expContentJobHelpManuals
JobTypes[transfer]=expContentJobTransfer
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
