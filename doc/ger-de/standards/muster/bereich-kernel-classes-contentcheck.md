---
title: Muster in kernel/classes/contentcheck
audience: developer
category: standards
order: 487
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/contentcheck mit den Entwurfsmustern.
keywords: kernel/classes/contentcheck, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/contentcheck

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/contentcheck` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (13)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expContentCheck` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/contentcheck/expcontentcheck.php:19` | Was die beiden Inhaltsprüfungen gemeinsam haben (doc/eng-us/features/6.0/content-checks.md): ihre Einstellungen, wo sie antworten, die Richtlinienprüfung, die Formular-Tokens der … |
| `expContentCheckBrokenRelations` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/contentcheck/expcontentcheckbrokenrelations.php:38` | Sichtbarer, veröffentlichter Inhalt, der auf etwas verweist, das ein Besucher nicht erreichen kann (doc/eng-us/features/6.0/content-checks.md, "Broken relations"). |
| `expContentCheckFileFix` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/contentcheck/expcontentcheckfilefix.php:25` | Die Auflösungen der Liste fehlender Dateien (doc/eng-us/features/6.0/content-checks.md, "Resolving"): jede für eine Feldversion oder für viele, immer geplant f… |
| `expContentCheckFileHandler` | interface | [Schnittstelle (Vertrag)](schnittstelle.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/contentcheck/expcontentcheckfilehandler.php:15` | Gibt an, für welche Speicherdateien ein dateibasiertes Feld steht (doc/eng-us/features/6.0/content-checks.md, „Missing files“). |
| `expContentCheckFileImage` | class implements expContentCheckFileHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/contentcheck/expcontentcheckfileimage.php:13` | Die Dateien eines Bildfeldes (ezimage): das Original und die Aliase, die sein gespeichertes XML nennt (doc/eng-us/features/6.0/content-checks.md, „Missing files“). |
| `expContentCheckFiles` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/contentcheck/expcontentcheckfiles.php:26` | Fehlende Dateien und Bild-Aliase (doc/eng-us/features/6.0/content-checks.md, "Missing files"): die dateibasierten Felder der Inhalte unterhalb eines Knotens (Bilder, Da… |
| `expContentCheckFileTable` | class implements expContentCheckFileHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/contentcheck/expcontentcheckfiletable.php:16` | Die Datei eines Feldes, dessen Datentyp sie in einer Tabelle mit den Spalten von ezbinaryfile hält: contentobject_attribute_id, version, filename, original_file… |
| `expContentCheckQuarantine` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/contentcheck/expcontentcheckquarantine.php:25` | Die Aktionen der Speicherprüfung (doc/eng-us/features/6.0/content-checks.md, "Quarantine"). |
| `expContentCheckReferences` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/contentcheck/expcontentcheckreferences.php:37` | Der Referenzindex der Liste nicht referenzierter Inhalte (doc/eng-us/features/6.0/content-checks.md, "What counts as a reference"): jede Objekt-ID, Knoten-ID, Remote-ID… |
| `expContentCheckScope` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/contentcheck/expcontentcheckscope.php:24` | Der Startort der Inhaltsprüfungen (doc/eng-us/features/6.0/content-checks.md, "Where a check starts"): jeder Knoten, vom ganzen Baum (Knoten 1) bis zu einem… |
| `expContentCheckStorage` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/contentcheck/expcontentcheckstorage.php:28` | Die Speicherprüfung (doc/eng-us/features/6.0/content-checks.md, "Storage files"): die Dateien unter var//storage, die kein Inhalt verwendet (verwaiste Dateien), und die Da… |
| `expContentCheckTrash` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/contentcheck/expcontentchecktrash.php:23` | Verschiebt nicht referenzierte Objekte in den Papierkorb (doc/eng-us/features/6.0/content-checks.md, "Moving to the trash"): die Liste sendet die angehakten Objekte, eine Bestätigu… |
| `expContentCheckUnreferenced` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/contentcheck/expcontentcheckunreferenced.php:31` | Die Assets unterhalb eines Knotens, die nichts verwendet (doc/eng-us/features/6.0/content-checks.md, "Unreferenced objects"): veröffentlichte Objekte einer Asset-Klasse ([Unrefer… |
