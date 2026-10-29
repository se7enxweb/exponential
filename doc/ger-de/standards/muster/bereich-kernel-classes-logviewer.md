---
title: Muster in kernel/classes/logviewer
audience: developer
category: standards
order: 502
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/logviewer mit den Entwurfsmustern.
keywords: kernel/classes/logviewer, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/logviewer

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/logviewer` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (19)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expLogActions` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/logviewer/explogactions.php:13` | Die Aktionen des Log-Viewers außer dem Lesen (doc/eng-us/features/6.0/log-viewer.md, „Actions“): einen Bereich herunterladen, jetzt rotieren, eine Datei leeren, entfernen… |
| `expLogBundle` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/logviewer/explogbundle.php:17` | „Alle Logs herunterladen“ (Setup › Logs verwalten; doc/eng-us/features/6.0/log-viewer.md, „Downloading every log“): ein Archiv aller Logs, die der Viewer auflistet,… |
| `expLogDigest` | class | – | `kernel/classes/logviewer/explogdigest.php:23` | Der Fehler-Digest des Log-Viewers (Setup › Log files › Error digest, setup/logsdigest; doc/eng-us/features/6.0/log-viewer.md, „Error digest“): der… |
| `expLogFileLocator` | class | – | `kernel/classes/logviewer/explogfilelocator.php:21` | Welche Logdateien der Viewer öffnen darf und wo sie liegen (doc/eng-us/features/6.0/log-viewer.md, "Which files"). |
| `expLogFileStats` | class | – | `kernel/classes/logviewer/explogfilestats.php:16` | Die Zeilenrate des Navigators: wie viele Zeilen pro Minute jede Logdatei hinzugewinnt (doc/eng-us/features/6.0/log-viewer.md, "The navigator"). |
| `expLogHealth` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/logviewer/exploghealth.php:17` | Der Zustand der Logs (Setup › Log files › Health, setup/logshealth; doc/eng-us/features/6.0/log-viewer.md, „Health“): der Datenträger, auf dem die Logs und ihre Arch… |
| `expLogManager` | class implements expLogManagerInterface | [Fassade / Dienst](fassade.md), [Singleton / instance()](singleton.md) | `kernel/classes/logviewer/explogmanager.php:27` | Log-Verwaltung (doc/eng-us/features/6.0/log-rotation.md): die Logs der Installation mit ihren Archiven, Größen, ihrem Wachstum und ihren Limits; Rotation, Bereinigung… |
| `expLogManagerFactory` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/logviewer/explogmanagerfactory.php:12` | Der Log-Manager, den die Ansichten des Log-Viewers verwenden (doc/eng-us/features/6.0/log-viewer.md, „Managing logs“): der Kernel-Service expLogManager, wenn er… |
| `expLogManagerFallback` | class implements expLogManagerInterface | – | `kernel/classes/logviewer/explogmanagerfallback.php:20` | Log-Verwaltung auf den eigenen Klassen des Log-Viewers (expLogManagerInterface; doc/eng-us/features/6.0/log-viewer.md, „Managing logs“), verwendet, während der Kernel… |
| `expLogManagerInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/classes/logviewer/explogmanagerinterface.php:19` | Log-Verwaltung (doc/eng-us/features/6.0/log-rotation.md, „For developers“): was die Befehlszeile, der Cronjob-Teil „logs“ und die Aktionen des Log-Viewers… |
| `expLogParser` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/logviewer/explogparser.php:25` | Liest die Zeilen einer Logdatei als Einträge (doc/eng-us/features/6.0/log-viewer.md, "The line format"). |
| `expLogPermalink` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/logviewer/explogpermalink.php:25` | Die Permalinks des Log-Viewers (doc/eng-us/features/6.0/log-viewer.md, „Permalinks“): jede Ansicht eines Logs als Pfad ohne Query-String, kurz e… |
| `expLogRateLimit` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/logviewer/explogratelimit.php:16` | Das Anfragelimit des Feeds des Log-Viewers, je Sitzung ([RateLimitSettings], doc/eng-us/features/6.0/log-viewer.md, „Cost“), und das Gedächtnis, welche… |
| `expLogRedactor` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/logviewer/explogredactor.php:16` | Was der Log-Viewer maskiert, bevor eine Zeile den Server verlässt (doc/eng-us/features/6.0/log-viewer.md, "Privacy"). |
| `expLogSearch` | class | – | `kernel/classes/logviewer/explogsearch.php:26` | Durchsucht Logdateien und ihre Rotationen, innerhalb von Grenzen (doc/eng-us/features/6.0/log-viewer.md, "Searching"). |
| `expLogSearchLimitException` | class extends RuntimeException | [Ausnahmehierarchie](ausnahme.md) | `kernel/classes/logviewer/explogsearch.php:502` | Ein regulärer Ausdruck, der in einer Zeile pcre.backtrack_limit (oder eine andere PCRE-Grenze) überschritten hat. |
| `expLogTail` | class | – | `kernel/classes/logviewer/explogtail.php:26` | Liest, was eine Logdatei seit einem Byte-Offset hinzugewonnen hat (doc/eng-us/features/6.0/log-viewer.md, "Following a file"). |
| `expLogViewer` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/logviewer/explogviewer.php:16` | Der Log-Viewer (Setup › Log files, setup/logs; doc/eng-us/features/6.0/log-viewer.md): was seine Seite, sein Feed und seine Kommandozeile verwenden. |
| `expLogViewerConfig` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Singleton / instance()](singleton.md) | `kernel/classes/logviewer/explogviewerconfig.php:11` | Die Einstellungen des Log-Viewers (settings/logviewer.ini, doc/eng-us/features/6.0/log-viewer.md, „Settings“), einmal pro Anfrage mit sicheren Grenzen gelesen. |
