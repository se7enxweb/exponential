---
title: Muster in kernel/classes/transfer
audience: developer
category: standards
order: 519
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/transfer mit den Entwurfsmustern.
keywords: kernel/classes/transfer, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/transfer

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/transfer` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (10)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expKernelTransferCancelledException` | class extends RuntimeException | [Ausnahmehierarchie](ausnahme.md) | `kernel/classes/transfer/expkerneltransfercancelledexception.php:17` | Wird von einer Transfer-Aufgabe (expKernelTransferTask) ausgelöst, die angehalten wurde, weil ihr Lauf abgebrochen wurde (expKernelTransferContext::throwIfCancelled()); der Lauf… |
| `expKernelTransferContext` | class | – | `kernel/classes/transfer/expkerneltransfercontext.php:18` | Was eine Transfer-Aufgabe (expKernelTransferTask) von der Engine erhält, die sie ausführt: den Fortschritt, den sie meldet (an den Job der Engine und an den Verlauf des Laufs… |
| `expKernelTransferHistory` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/transfer/expkerneltransferhistory.php:38` | Der Laufverlauf der Import- und Export-Werkzeuge (doc/eng-us/features/6.0/transfer-runs.md): der Schreiber, den jedes Werkzeug für seine Läufe aufruft, und der Leser des… |
| `expKernelTransferQueueHandler` | class extends expQueueHandlerBase | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/transfer/expkerneltransferqueuehandler.php:37` | Der expqueue-Handler der Transfer-Läufe (expKernelTransferRunner, doc/eng-us/features/6.0/transfer-runs.md). |
| `expKernelTransferRegistry` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Registry](registry.md), [Singleton / instance()](singleton.md) | `kernel/classes/transfer/expkerneltransferregistry.php:30` | Die Transfer-Registry: jedes Import- und Export-Werkzeug der Installation, wie es sich in kernelservices.ini selbst deklariert (ein [Service_transfer.]-Block t… |
| `expKernelTransferRun` | class extends eZPersistentObject | [Active Record (eZPersistentObject)](active-record.md) | `kernel/classes/transfer/expkerneltransferrun.php:26` | Ein Lauf eines Import- oder Export-Tools: eine Zeile von exp_transfer_run (doc/eng-us/features/6.0/transfer-runs.md). |
| `expKernelTransferRunner` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/transfer/expkerneltransferrunner.php:36` | Ein einheitlicher Weg für jedes Import- und Export-Tool, seine Arbeit im Hintergrund auszuführen (doc/eng-us/features/6.0/transfer-runs.md), auf jeder Engine, die die Installation… |
| `expKernelTransferRunSchema` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/transfer/expkerneltransferrunschema.php:21` | Die Tabelle exp_transfer_run auf jeder Engine: ob sie existiert, und ihre Erstellung aus ihrer Definition in share/db_schema.dba über den Schema-Handler der Engine… |
| `expKernelTransferTask` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/classes/transfer/expkerneltransfertask.php:38` | Die Arbeit eines Hintergrundlaufs eines Import- oder Export-Werkzeugs, gleich welche Engine ihn ausführt (expKernelTransferRunner: expqueue, die Inhalts-Jobs des Kernels, der… |
| `expKernelTransferTool` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `kernel/classes/transfer/expkerneltransfertool.php:49` | Ein Import- oder Export-Tool der Transfer-Registry (expKernelTransferRegistry), wie es sich in einem Block [Service_transfer.] von kernelservice… |
