---
title: Builder
audience: developer
category: standards
order: 322
summary: Baut ein komplexes Objekt oder einen Text Schritt für Schritt auf. 9 Instanzen in Exponential.
keywords: Entwurfsmuster, Builder, Erzeugung, Musterkatalog, design pattern, builder, pattern catalogue
type: catalogue
---
# Builder

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Baut ein komplexes Objekt oder einen Text Schritt für Schritt auf.

|  |  |
| --- | --- |
| Gruppe | Anlegen |
| Instanzen | 9 |
| Erkannt an | Der Klassenname enthält Builder oder Generator. |

## Klassen nach Bereich

### extension/cjw_newsletter (1)

[Alle Muster von extension/cjw_newsletter](bereich-extension-cjw-newsletter.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `CjwNewsletterEditionBuilder` | class | `extension/cjw_newsletter/classes/editorial/cjwnewslettereditionbuilder.php:24` | Erstellt und befüllt Ausgaben: findet die neueste ungesendete Ausgabe einer Liste, kopiert eine Vorlagenausgabe unter ihre Liste und übernimmt Artikel aus einem Pool in eine… | – |

### extension/explayouts_api (1)

[Alle Muster von extension/explayouts_api](bereich-extension-explayouts-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsEzValueUrlGenerator` | class | `extension/explayouts_api/classes/explayoutsezvalueurlgenerator.php:2` | – | – |

### extension/explayouts_site_api (1)

[Alle Muster von extension/explayouts_site_api](bereich-extension-explayouts-site-api.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expLayoutsSiteApiItemValueUrlGenerator` | class | `extension/explayouts_site_api/classes/explayoutssiteapiitemvalueurlgenerator.php:2` | – | – |

### extension/ezfind (1)

[Alle Muster von extension/ezfind](bereich-extension-ezfind.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `ezfeZPSolrQueryBuilder` | class | `extension/ezfind/classes/ezfezpsolrquerybuilder.php:8` | – | – |

### extension/ezodf (1)

[Alle Muster von extension/ezodf](bereich-extension-ezodf.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZOOGenerator` | class | `extension/ezodf/classes/ezoogenerator.php:41` | – | – |

### extension/xrowmetadata (1)

[Alle Muster von extension/xrowmetadata](bereich-extension-xrowmetadata.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `xrowMetaDataLicenseCatalogueGenerator` | class | `extension/xrowmetadata/bin/php/xrowmetadatalicenses.php:84` | Erstellt den Lizenzkatalog share/licenses/licenses.json von xrowmetadata aus zwei heruntergeladenen Dateien, nie aus dem Netz: | – |

### kernel/classes/help (1)

[Alle Muster von kernel/classes/help](bereich-kernel-classes-help.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expHelpExamplesGenerator` | class | `kernel/classes/help/exphelpexamplesgenerator.php:25` | Schreibt die generierten Referenzbeispiele unter doc/eng-us/examples/ (doc/eng-us/examples/README.md, "How the generated pages are made"): eine Markdown-Seite je Templ… | – |

### kernel/classes/pdfexport (1)

[Alle Muster von kernel/classes/pdfexport](bereich-kernel-classes-pdfexport.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `expPDFExportGenerator` | class | `kernel/classes/pdfexport/exppdfexportgenerator.php:27` | Erzeugt das PDF eines Exports: in seine gespeicherte Datei oder in eine temporäre Datei, die dann gesendet und entfernt wird. | – |

### kernel/private (1)

[Alle Muster von kernel/private](bereich-kernel-private.md)

| Klasse | Art | Datei | Beschreibung | Erweiterungspunkt |
| --- | --- | --- | --- | --- |
| `eZAutoloadGenerator` | class | `kernel/private/classes/ezautoloadgenerator.php:19` | Hilfsklasse zum Erzeugen von Autoload-Arrays für Exponential. | – |
