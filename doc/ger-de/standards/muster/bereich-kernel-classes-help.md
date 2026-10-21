---
title: Muster in kernel/classes/help
audience: developer
category: standards
order: 492
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von kernel/classes/help mit den Entwurfsmustern.
keywords: kernel/classes/help, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in kernel/classes/help

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `kernel/classes/help` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (31)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expHelp` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/help/exphelp.php:11` | Die gemeinsamen Hilfsfunktionen des Hilfesystems (doc/eng-us/features/6.0/help-system.md): seine Einstellungen, ob es eingeschaltet ist, das Installationsverzeichnis, aus dem es liest, und die URLs v… |
| `expHelpAllowlist` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/help/exphelpallowlist.php:29` | Die Dateien, die das Hilfesystem anzeigen darf (doc/eng-us/features/6.0/help-system.md, "Which files are shown"). |
| `expHelpAudience` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/help/exphelpaudience.php:29` | Für wen eine Hilfeseite ist, zu welchem Abschnitt der Hilfe sie gehört und welche Art von Seite sie ist (doc/eng-us/features/6.0/help-system.md, "Audiences, categories… |
| `expHelpContentExport` | class | – | `kernel/classes/help/content/exphelpcontentexport.php:21` | E-Books eines Handbuchs der als Inhalt gepflegten Hilfe (doc/eng-us/features/6.0/help-content.md, "E-books"): EPUB 3 (hier geschrieben, reines PHP mit ZipArchive) und… |
| `expHelpContentInstaller` | class | – | `kernel/classes/help/content/exphelpcontentinstaller.php:23` | Legt das Inhaltsmodell der als Inhalt gepflegten Hilfe auf einer installierten Website an, idempotent: die acht help_*-Klassen (Klassengruppe "Help"), die Sektio… |
| `expHelpContentProvider` | class implements expHelpContextProviderInterface, expHelpContextPageInterface | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Singleton / instance()](singleton.md), [Strategie / Handler](strategie-handler.md) | `kernel/classes/help/content/exphelpcontentprovider.php:19` | Als Inhalt gepflegte Hilfe, ausgeliefert in das Hilfesystem (exphelp): die Themen, die der aktuelle Benutzer sehen darf, gerendert mit dem Markdown-Renderer der Hilfe… |
| `expHelpContentRepository` | class | [Singleton / instance()](singleton.md) | `kernel/classes/help/content/exphelpcontentrepository.php:15` | Liest als Inhalt gepflegte Hilfe aus der Datenbank in einfache Arrays (die Formen, die expHelpContentValidator beschreibt), ohne jeden Berechtigungsfilter:… |
| `expHelpContentSchema` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/help/content/exphelpcontentschema.php:20` | Das Inhaltsmodell der als Inhalt gepflegten Hilfe (doc/eng-us/specifications/6.0/help-system.md, doc/eng-us/features/6.0/help-content.md): die acht help_*-Inhaltsk… |
| `expHelpContentSeeder` | class | – | `kernel/classes/help/content/exphelpcontentseeder.php:15` | Legt das anfängliche Glossar, die Themen, Kapitel und Handbücher aus einer Datendatei an (share/help/seed-glossary.json, share/help/seed-topics.json). |
| `expHelpContentValidator` | class | – | `kernel/classes/help/content/exphelpcontentvalidator.php:23` | Die maschinell prüfbaren Regeln des Hilfe-Styleguides (doc/eng-us/specifications/6.0/help-style-guide.md), auf einfachen Arrays: keine Datenbank. |
| `expHelpContextPageInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/classes/help/exphelpcontextpageinterface.php:16` | Ein Kontexthilfe-Anbieter (expHelpContextProviderInterface), der auch Seiten ausliefert, die keine Dateien unterhalb von doc/ sind: als Inhalt gepflegte Hilfethemen, w… |
| `expHelpContextProviderInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `kernel/classes/help/exphelpcontextproviderinterface.php:15` | Eine Klasse, die „Hilfe zu dieser Seite“ im Code beantwortet, für das, was ein Map[]-Eintrag pro Ansicht nicht ausdrücken kann (eine Ansicht, deren Hilfe von ihren Parametern abhängt, eine Exte… |
| `expHelpContextRegistry` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Registry](registry.md), [Singleton / instance()](singleton.md) | `kernel/classes/help/exphelpcontextregistry.php:44` | Kontexthilfe: welche Hilfeseite „Hilfe zu dieser Seite“ beantwortet, für ein Modul, eine Ansicht und ein Design. |
| `expHelpDocumentIndex` | class | [Singleton / instance()](singleton.md) | `kernel/classes/help/exphelpdocumentindex.php:34` | Der Index der Hilfeseiten (doc/eng-us/features/6.0/help-system.md, "The index"): ein Eintrag je Dokument der Allowlist (expHelpAllowlist: von Git verfolgte Da… |
| `expHelpExamplesGenerator` | class | [Builder](builder.md) | `kernel/classes/help/exphelpexamplesgenerator.php:25` | Schreibt die generierten Referenzbeispiele unter doc/eng-us/examples/ (doc/eng-us/examples/README.md, "How the generated pages are made"): eine Markdown-Seite je Templ… |
| `expHelpFrontMatter` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/help/exphelpfrontmatter.php:28` | Die Front Matter einer Hilfeseite (doc/eng-us/features/6.0/help-system.md, "Front matter"): ein Block ganz oben in einer Markdown-Datei zwischen zwei Zeilen aus "-… |
| `expHelpFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/help/exphelpfunctioncollection.php:10` | Die Fetch-Funktionen von exphelp (kernel/exphelp/function_definition.php). |
| `expHelpHighlighter` | class | [Registry](registry.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/help/exphelphighlighter.php:29` | Serverseitige Syntaxhervorhebung der Codeblöcke der Hilfeseiten (exphelp, doc/eng-us/features/6.0/help-system.md), ohne JavaScript oder eine externe Bi… |
| `expHelpIssueReport` | class | – | `kernel/classes/help/exphelpissuereport.php:18` | Einen Fehler melden (exphelp/report, doc/eng-us/features/6.0/help-system.md "Reporting an error"): wo er gemeldet wird (die Tracker von [IssueTrackers], die r… |
| `expHelpLicense` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/help/exphelplicense.php:17` | Die Lizenz der Dokumentation, die die Hilfe anzeigt (exphelp.ini [License], doc/eng-us/README-LICENSE.md). |
| `expHelpLinkCheck` | class | – | `kernel/classes/help/exphelplinkcheck.php:25` | Prüft die Links und Bilder der Hilfeseiten (doc/eng-us/features/6.0/help-system.md, "Checking the documentation"; "./console exp:help check"). |
| `expHelpLinks` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/help/exphelplinks.php:12` | Die Link- und Bildauflöser, die der Viewer expHelpMarkdownRenderer übergibt (doc/eng-us/features/6.0/help-system.md): ein relativer Link auf eine erlaubte Seite wird i… |
| `expHelpMarkdownRenderer` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/help/exphelpmarkdownrenderer.php:50` | Rendert das Markdown der Dokumentation (doc/**.md) in das HTML des Hilfe-Viewers (exphelp/viewer, doc/eng-us/features/6.0/help-system.md): CommonMark-Bl… |
| `expHelpNavigation` | class | – | `kernel/classes/help/exphelpnavigation.php:20` | Der Navigationsbaum der Hilfe (doc/eng-us/features/6.0/help-system.md, "Viewer"): jede Seite, die der Index ausliefert, in ihren Ordnern, als verschachtelte Listen. |
| `expHelpPdf` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/help/exphelppdf.php:18` | Eine Hilfeseite als PDF-Datei (doc/eng-us/features/6.0/help-system.md, „Print and PDF“): die Ansicht exphelp/pdf/ und `./console exp:help pdf ` fragen hier an. |
| `expHelpPdfCanvas` | class extends Cpdf | – | `kernel/classes/help/exphelppdfcanvas.php:18` | Die Seitenfläche des PDFs einer Hilfeseite (doc/eng-us/features/6.0/help-system.md, "Print and PDF"): die eigene PDF-Bibliothek des Kernels (lib/ezpdf, Cpdf) mit den wenigen… |
| `expHelpPdfFontSubset` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/help/exphelppdffontsubset.php:17` | Reduziert eine TrueType-Schrift auf die Glyphen, die ein PDF verwendet (doc/eng-us/features/6.0/help-system.md, „Print and PDF“). |
| `expHelpPdfWriter` | class | – | `kernel/classes/help/exphelppdfwriter.php:28` | Setzt eine ausgegebene Hilfeseite als PDF (doc/eng-us/features/6.0/help-system.md, "Print and PDF"). |
| `expHelpReadPolicyGrant` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `kernel/classes/help/exphelpreadpolicygrant.php:22` | Plant und vergibt die Richtlinie exphelp/read (bin/php/addexphelpreadpolicy.php): Lesen des Hilfesystems (doc/eng-us/features/6.0/help-system.md). |
| `expHelpRecordRenderer` | class | – | `kernel/classes/help/exphelprecordrenderer.php:36` | Reine Textseiten, die Listen von Datensätzen sind (doc/eng-us/release-overview, doc/branch-overview: die Release- und Branch-Historie aus dem ursprünglichen SVN-Repo… |
| `expHelpSearch` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `kernel/classes/help/exphelpsearch.php:34` | expHelpSearch: die Volltextsuche des Hilfesystems (doc/eng-us/features/6.0/help-system.md, "Search"). |
