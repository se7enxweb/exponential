---
title: Muster in extension/expqueue
audience: developer
category: standards
order: 428
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/expqueue mit den Entwurfsmustern.
keywords: extension/expqueue, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/expqueue

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/expqueue` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (27)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expQueue` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/expqueue/classes/expqueue.php:26` | Die Job-Warteschlange: was Produzenten aufrufen (enqueue, groups, cancel, pause/resume, retry, results) und die Zustandswechsel, die die Worker und die Administration vornehmen. |
| `expQueueAdmin` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/expqueue/classes/expqueueadmin.php:14` | Hilfsfunktionen des Admin-Moduls expqueue: das Seitenergebnis mit seinem Pfad, die Zeilen, die die Templates zeigen (jede Bezeichnung und Dauer hier aufbereitet, sodass das Temp… |
| `expQueueAuditBranch` | class implements expAuditTaxonomyBranch | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/expqueue/classes/expqueueauditbranch.php:15` | Der Audit-Taxonomiezweig von expqueue (registriert in settings/audit.ini.append.php): system.queue., ein Ereignis je Bedieneraktion an der Warteschlange und pe… |
| `expQueueCancelledException` | class extends expQueueException | [Ausnahmehierarchie](ausnahme.md) | `extension/expqueue/classes/expqueuecancelledexception.php:9` | Wird von expQueueContext::throwIfCancelled() ausgelöst, wenn der Job während seiner Ausführung abgebrochen wurde; der Job endet als abgebrochen. |
| `expQueueCommand` | class | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/expqueue/classes/expqueuecommand.php:17` | Die Befehle ./console ext:expqueue: (extension/expqueue/bin/php/.php); jede Datei ruft nur expQueueCommand::main( '' ) auf. |
| `expQueueConfig` | class | [Singleton / instance()](singleton.md) | `extension/expqueue/classes/expqueueconfig.php:15` | Die Einstellungen von expqueue (expqueue.ini), mit den Standardwerten, die gelten, wenn eine Einstellung fehlt. |
| `expQueueContext` | class | – | `extension/expqueue/classes/expqueuecontext.php:32` | Was ein Handler mit seinem Job erhält: den Lease (Heartbeat), kooperativen Abbruch, den Fortschritt, den Ergebnis-Checkpoint und die Rate-Limit-Übergabe seiner… |
| `expQueueEvent` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md), [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/expqueue/classes/expqueueevent.php:15` | Der Verlauf eines Jobs (Tabelle expqueue_event), nur anhängbar, mit dem Job entfernt. |
| `expQueueException` | class extends RuntimeException | [Ausnahmehierarchie](ausnahme.md) | `extension/expqueue/classes/expqueueexception.php:9` | Ein Fehler der Warteschlange selbst: eine unbekannte Klasse oder ein unbekannter Typ, ein erreichtes Kontingent, ein Job, der seinen Zustand nicht ändern kann. |
| `expQueueFailHandler` | class extends expQueueHandlerBase | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/expqueue/classes/handlers/expqueuefailhandler.php:16` | expqueue.fail: schlägt absichtlich fehl, um Wiederholungen, Backoff, die Dead Letter Queue und die Anpassung des Ratenlimits auszuprobieren. |
| `expQueueGroup` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/expqueue/classes/expqueuegroup.php:15` | Eine Menge zusammengehörender Jobs (Tabelle expqueue_group): „Teilbaum X in zehn Sprachen übersetzen“, ein Paketimport, ein Kettenlauf. |
| `expQueueHandler` | interface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `extension/expqueue/classes/expqueuehandler.php:27` | Ein Job-Handler: der Code, der die Jobs eines Typs ausführt. |
| `expQueueHandlerBase` | abstract class implements expQueueHandler | [Strategie / Handler](strategie-handler.md), [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md) | `extension/expqueue/classes/expqueuehandlerbase.php:12` | Eine Basis für Handler mit den optionalen Teilen: validate() wird aufgerufen, wenn ein Job in die Warteschlange gestellt wird (eine falsche Nutzlast wird dort sofort abgelehnt, nicht nachts um 3 Uhr. |
| `expqueueInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/expqueue/ezinfo.php:7` | – |
| `expQueueJob` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `extension/expqueue/classes/expqueuejob.php:25` | Ein Job (Tabelle expqueue_job). |
| `expQueueLock` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/expqueue/classes/expqueuelock.php:25` | Die Dispatcher-Sperre und das Verzeichnis der Warteschlange. |
| `expQueueNoopHandler` | class extends expQueueHandlerBase | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/expqueue/classes/handlers/expqueuenoophandler.php:12` | expqueue.noop: tut nichts und ist erledigt. |
| `expQueuePermanentException` | class extends expQueueException | [Ausnahmehierarchie](ausnahme.md) | `extension/expqueue/classes/expqueuepermanentexception.php:12` | Wird von einem Handler für einen Fehler ausgelöst, den ein weiterer Versuch nicht beheben kann (ungültige Eingabe, eine Ablehnung, ein fehlendes Objekt): Der Job geht sofort in die Dead-Letter-Warteschlange… |
| `expQueueResource` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/expqueue/classes/expqueueresource.php:22` | Der Nebenläufigkeitsbegrenzer einer Ressource (Tabelle expqueue_resource): ein Schlüssel wie „anthropic:standard“ oder „imaging“, den Jobs mit der Option resou… |
| `expQueueResult` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/expqueue/classes/expqueueresult.php:18` | Was run() eines Handlers zurückgibt, wenn ein Array (done) nicht ausreicht. |
| `expQueueRetryableException` | class extends expQueueException | [Ausnahmehierarchie](ausnahme.md) | `extension/expqueue/classes/expqueueretryableexception.php:13` | Wird von einem Handler für einen Fehler ausgelöst, der von selbst verschwinden kann: ein Timeout, eine Antwort 429 oder 5xx, eine belegte Ressource. |
| `expQueueScheduler` | class | – | `extension/expqueue/classes/expqueuescheduler.php:38` | Wählt den nächsten Job für einen Worker und weist ihn diesem Worker zu; hält die Warteschlange in Bewegung (Wartung). |
| `expQueueSchema` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/expqueue/classes/expqueueschema.php:28` | Die Tabellen von expqueue, einmal im generischen Schemaformat des Kernels definiert. |
| `expQueueService` | class | [Fassade / Dienst](fassade.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/expqueue/classes/expqueueservice.php:22` | Der systemd-Dienst der Worker der Warteschlange: ./console ext:expqueue:service install\|start\|stop\|status\|uninstall. |
| `expQueueSleepHandler` | class extends expQueueHandlerBase | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/expqueue/classes/handlers/expqueuesleephandler.php:16` | expqueue.sleep: wartet in Schritten, meldet den Fortschritt und beachtet zwischen den Schritten einen Abbruch. |
| `expQueueStats` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/expqueue/classes/expqueuestats.php:15` | Die Kennzahlen der Warteschlange für das Dashboard (expqueue/dashboard) und ./console ext:expqueue:status: Anzahlen je Zustand, je Klasse und je Ressource, die… |
| `expQueueWorker` | class | – | `extension/expqueue/classes/expqueueworker.php:24` | Führt Jobs nacheinander aus, bis ein Limit erreicht ist: die Schleife von ./console ext:expqueue:work, des Cron-Teils expqueue und des systemd-Dienstes. |

## Modulansichten (5)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/expqueue/dashboard` | `dashboard.php` | view | `extension/expqueue/modules/expqueue/module.php:14` |
| `/expqueue/list` | `list.php` | view | `extension/expqueue/modules/expqueue/module.php:20` |
| `/expqueue/job` | `job.php` | view | `extension/expqueue/modules/expqueue/module.php:28` |
| `/expqueue/group` | `group.php` | view | `extension/expqueue/modules/expqueue/module.php:34` |
| `/expqueue/action` | `action.php` | manage | `extension/expqueue/modules/expqueue/module.php:41` |

## Richtlinienfunktionen (2)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `expqueue/view` | – | `extension/expqueue/modules/expqueue/module.php:48` |
| `expqueue/manage` | – | `extension/expqueue/modules/expqueue/module.php:49` |

## Cronjob-Teile (1)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `expqueue` | `extension/expqueue/cronjobs/expqueue.php` | Der Cron-Teil „expqueue“ (php runcronjobs.php -q expqueue, jede Minute): der Standard-Worker der Warteschlange und das Sicherheitsnetz neben einem systemd-Dienst… |

## INI-Plugin-Punkte (5)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditEventSettings] Branches[expqueue]` | `expQueueAuditBranch` | `extension/expqueue/settings/audit.ini.append.php:5` |
| `[JobType_expqueue.noop] Handler` | `expQueueNoopHandler` | `extension/expqueue/settings/expqueue.ini.append.php:167` |
| `[JobType_expqueue.sleep] Handler` | `expQueueSleepHandler` | `extension/expqueue/settings/expqueue.ini.append.php:173` |
| `[JobType_expqueue.fail] Handler` | `expQueueFailHandler` | `extension/expqueue/settings/expqueue.ini.append.php:178` |
| `[Registry_expqueue_job_types] Entries[Handler]` | `expQueueHandler` | `extension/expqueue/settings/rad.ini.append.php:21` |
