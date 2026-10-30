---
title: Muster in kernel/classes/contentjob
audience: developer
category: standards
order: 495
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/contentjob mit den Entwurfsmustern.
keywords: kernel/classes/contentjob, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/contentjob

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/contentjob` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (20)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expContentJob` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/contentjob/expcontentjob.php:32` | Ein Inhaltsjob: ein großes Entfernen oder Kopieren eines Teilbaums, das in Stapeln in einem Hintergrund-Worker statt in der Web-Anfrage läuft, sodass es nie an einem Anfra… |
| `expContentJobAddLocation` | class extends expContentJobBatchType | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/contentjob/expcontentjobaddlocation.php:22` | Der Inhaltsjob 'addlocation': gibt den Objekten vieler ausgewählter Knoten einen neuen Ort unter einem Zielknoten, pro Objekt über die kernel-eigene Ope… |
| `expContentJobAuditExport` | class implements expContentJobType | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/contentjob/expcontentjobauditexport.php:28` | Der Inhaltsjob 'auditexport': der Export der Audit-Konsole (audit/export) im Hintergrund, für einen Filter, der für einen Download im Browser zu groß ist… |
| `expContentJobBatchType` | abstract class implements expContentJobType | [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `kernel/classes/contentjob/expcontentjobbatchtype.php:28` | Die gemeinsame Grundlage der Inhaltsjob-Typen, die auf einer Liste von Knoten arbeiten: move, hide, reveal, section, state, addlocation und removelocation. |
| `expContentJobCopySubtree` | class implements expContentJobType | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/contentjob/expcontentjobcopysubtree.php:34` | Der Inhaltsjob 'copy': kopiert einen Teilbaum stapelweise unter ein neues Elternelement, Ebene für Ebene von oben, mit der Semantik von content/copysubtree und bi… |
| `expContentJobException` | class extends Exception | [Ausnahmehierarchie](ausnahme.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/contentjob/expcontentjobexception.php:13` | Der Fehler, mit dem ein Inhaltsjob ablehnt oder fehlschlägt; seine Meldung wird dem Benutzer angezeigt. |
| `expContentJobHelpManuals` | class implements expContentJobType | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/contentjob/expcontentjobhelpmanuals.php:27` | Der Content-Job 'helpmanuals': baut die Handbücher der Hilfe im Hintergrund aus der Markdown-Hilfe (doc/eng-us/features/6.0/help-content.md, „Manuals… |
| `expContentJobHideSubtree` | class extends expContentJobBatchType | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/contentjob/expcontentjobhidesubtree.php:25` | Der Inhaltsjob 'hide' (und über expContentJobRevealSubtree 'reveal'): verbirgt einen Knoten und seinen Teilbaum oder macht sie sichtbar, mit der Semantik von eZContentOb… |
| `expContentJobLock` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/contentjob/expcontentjoblock.php:30` | Teilbaumsperren der Inhaltsjobs. |
| `expContentJobMoveSubtree` | class extends expContentJobBatchType | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/contentjob/expcontentjobmovesubtree.php:24` | Der Inhaltsjob 'move': verschiebt einen Teilbaum unter ein neues Elternelement. |
| `expContentJobPackageInstall` | class implements expContentJobType | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/contentjob/expcontentjobpackageinstall.php:26` | Der Inhaltsjob 'packageinstall': installiert ein Paket aus dem Paket-Repository im Hintergrund, als der Benutzer, der es angefordert hat, mit den getroffenen Entscheidungen… |
| `expContentJobRemoveLocation` | class extends expContentJobBatchType | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/contentjob/expcontentjobremovelocation.php:23` | Der Inhaltsjob 'removelocation': entfernt viele ausgewählte Orte (Knoten) und behält ihre Objekte, über die kernel-eigene Operation, wie content/acti… |
| `expContentJobRemoveSubtree` | class implements expContentJobType | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/contentjob/expcontentjobremovesubtree.php:33` | Der Inhaltsjob 'remove': entfernt einen oder mehrere Teilbäume stapelweise, die tiefsten Knoten zuerst (Blätter vor ihren Eltern), jeden Stapel über die kernel-eigene… |
| `expContentJobRevealSubtree` | class extends expContentJobHideSubtree | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/contentjob/expcontentjobrevealsubtree.php:16` | Der Inhaltsjob 'reveal': macht einen verborgenen Knoten und seinen Teilbaum sichtbar, mit der Semantik von eZContentObjectTreeNode::unhideSubTree() (ein Knoten, der selbst verborgen… |
| `expContentJobSectionSubtree` | class extends expContentJobBatchType | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/contentjob/expcontentjobsectionsubtree.php:29` | Der Inhaltsjob 'section': weist jedem Objekt eines Teilbaums eine Sektion zu, mit der Semantik von eZContentObjectTreeNode::assignSectionToSubTree() (e… |
| `expContentJobStateSubtree` | class extends expContentJobBatchType | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/contentjob/expcontentjobstatesubtree.php:22` | Der Inhaltsjob 'state': setzt einen Objektzustand (einer Zustandsgruppe) auf jedes Objekt eines Teilbaums, pro Objekt über die kernel-eigene Operation, wie co… |
| `expContentJobStore` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/contentjob/expcontentjobstore.php:18` | Der Speicher der Inhaltsjobs: eine JSON-Datei je Job in var//jobs/content/, atomar geschrieben (eine temporäre Datei im selben Verzeichnis, auf die Pla… |
| `expContentJobTransfer` | class implements expContentJobType | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/contentjob/expcontentjobtransfer.php:25` | Der Content-Job 'transfer': ein Hintergrundlauf eines Import- oder Export-Tools (Export, CIE ...), wenn die Installation keine Job-Warteschlange hat (expKernelTransfe… |
| `expContentJobType` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/classes/contentjob/expcontentjobtype.php:22` | Eine Art von Inhaltsjob, registriert in content.ini [ContentJobSettings] JobTypes[]=. |
| `expContentJobWorker` | class | – | `kernel/classes/contentjob/expcontentjobworker.php:28` | Führt einen Inhaltsjob in Stapeln aus: bin/php/expcontentjob.php run , losgelöst gestartet von expContentJob::spawn() (oder vom Cronjob-Teil 'contentjobs', oder i… |

## Ereignis-Hooks (1)

| Ereignis | Art | Aufgerufen in |
| --- | --- | --- |
| `content/cache` | notify | `kernel/classes/contentjob/expcontentjobhidesubtree.php:112` |
