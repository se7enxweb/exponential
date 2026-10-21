---
title: Muster in extension/xrowmetadata
audience: developer
category: standards
order: 477
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/xrowmetadata mit den Entwurfsmustern.
keywords: extension/xrowmetadata, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/xrowmetadata

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/xrowmetadata` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (38)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\Cronjob\Extension\Xrowmetadata\Archivesitemap` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/xrowmetadata/classes/runnable/cronjobs/archivesitemap.php:11` | Der Code von extension/xrowmetadata/cronjobs/archivesitemap.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Extension\Xrowmetadata\Mobilesitemap` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/xrowmetadata/classes/runnable/cronjobs/mobilesitemap.php:11` | Der Code von extension/xrowmetadata/cronjobs/mobilesitemap.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Extension\Xrowmetadata\Newssitemap` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/xrowmetadata/classes/runnable/cronjobs/newssitemap.php:11` | Der Code von extension/xrowmetadata/cronjobs/newssitemap.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\Cronjob\Extension\Xrowmetadata\Sitemap` | class extends CronjobPart | [Befehl (Konsole, Skripte, Cronjobs)](befehl.md) | `extension/xrowmetadata/classes/runnable/cronjobs/sitemap.php:11` | Der Code von extension/xrowmetadata/cronjobs/sitemap.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowmetadata\Sitemaps\Index` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowmetadata/classes/runnable/views/sitemaps/index.php:65` | Der Code von extension/xrowmetadata/modules/sitemaps/index.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Xrowmetadata\Sitemaps\Robots` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/xrowmetadata/classes/runnable/views/sitemaps/robots.php:10` | Der Code von extension/xrowmetadata/modules/sitemaps/robots.php, in eine Klasse verschoben (#207 Stufe 1). |
| `eZClusterDirectoryIterator` | class implements Iterator | [Iterator](iterator.md) | `extension/xrowmetadata/classes/ezclusterdirectoryiterator.php:3` | – |
| `ezfSolrDocumentFieldxrowMetadata` | class extends ezfSolrDocumentFieldBase | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/xrowmetadata/classes/ezfSolrDocumentFieldxrowMetadata.php:3` | – |
| `xrowMetaData` | class extends ezcBaseStruct | [Wertobjekt / Struktur](wertobjekt.md) | `extension/xrowmetadata/classes/structs/xrowmetadata.php:3` | – |
| `xrowMetaDataFunctions` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowmetadata/classes/xrowmetadatafunctions.php:3` | – |
| `xrowmetadataInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/xrowmetadata/ezinfo.php:8` | – |
| `xrowMetaDataLicense` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowmetadata/classes/xrowmetadatalicense.php:27` | Die Inhaltslizenzen von xrowmetadata: der Katalog, die Positivliste der Installation und welche Lizenz eine Seite hat. |
| `xrowMetaDataLicenseCatalogueGenerator` | class | [Builder](builder.md) | `extension/xrowmetadata/bin/php/xrowmetadatalicenses.php:84` | Erstellt den Lizenzkatalog share/licenses/licenses.json von xrowmetadata aus zwei heruntergeladenen Dateien, nie aus dem Netz: |
| `xrowMetaDataLicenseHeader` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowmetadata/classes/xrowmetadatalicenseheader.php:28` | Die Lizenz einer Seite als HTTP-Antwort-Header: |
| `xrowMetaDataLicenseOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/xrowmetadata/autoloads/xrowmetadatalicenseoperator.php:37` | Der Template-Operator xrowmetadata_license: die Inhaltslizenz eines Knotens oder der angezeigten Seite, bereit für den Seitenkopf und einen sichtbaren Hinweis. |
| `xrowMetaDataLicensePicker` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowmetadata/classes/xrowmetadatalicensepicker.php:15` | Was die Bearbeitungsformulare brauchen, um eine Lizenz zu wählen: den Katalog (xrowMetaDataLicense) als Optionsgruppen für die Auswahl, die geerbte Lizenz (Klassenstandard… |
| `xrowMetaDataLicensePickerOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/xrowmetadata/autoloads/xrowmetadatalicensepickeroperator.php:14` | Template-Operatoren der Lizenzauswahl (xrowMetaDataLicensePicker), für Templates, die keinen Inhalt eines Metadaten-Attributs zur Hand haben, etwa die Kla… |
| `xrowMetaDataOpenGraphImage` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowmetadata/classes/xrowmetadataopengraphimage.php:16` | Das Open-Graph-Bild eines Metadaten-Attributs: welches Objekt es ist, ob dieses Objekt noch angezeigt werden kann, und das Bild davon, das og:image und twi… |
| `xrowMetaDataOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/xrowmetadata/autoloads/xrowmetadataoperator.php:3` | – |
| `xrowMetaDataType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `extension/xrowmetadata/datatypes/xrowmetadata/xrowmetadatatype.php:3` | – |
| `xrowMobileSitemap` | class extends xrowSitemapList | – | `extension/xrowmetadata/classes/xrowmobilesitemap.php:3` | – |
| `xrowSitemap` | class extends xrowSitemapList | – | `extension/xrowmetadata/classes/xrowsitemap.php:3` | – |
| `xrowSitemapConverter` | class implements xrowSitemapImageConverterInterface, xrowSitemapVideoConverterInterface, xrowSitemapNewsConverterInterface | [Strategie / Handler](strategie-handler.md) | `extension/xrowmetadata/classes/plugins/standardplugin.php:3` | – |
| `xrowSitemapImageConverterInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `extension/xrowmetadata/classes/interfaces/xrowsitemapimageconverterinterface.php:3` | – |
| `xrowSitemapIndex` | class extends xrowSitemapList | – | `extension/xrowmetadata/classes/xrowsitemapindex.php:3` | – |
| `xrowSitemapItem` | abstract class extends ezcBaseStruct | [Schablonenmethode / abstrakte Basisklasse](schablonenmethode.md), [Wertobjekt / Struktur](wertobjekt.md) | `extension/xrowmetadata/classes/structs/xrowsitemapitem.php:3` | – |
| `xrowSitemapItemAlternateLink` | class extends xrowSitemapItem | [Wertobjekt / Struktur](wertobjekt.md) | `extension/xrowmetadata/classes/structs/xrowsitemapitemalternatelink.php:3` | – |
| `xrowSitemapItemFrequency` | class extends xrowSitemapItem | [Wertobjekt / Struktur](wertobjekt.md) | `extension/xrowmetadata/classes/structs/xrowsitemapitemfrequency.php:3` | – |
| `xrowSitemapItemImage` | class extends xrowSitemapItem | [Wertobjekt / Struktur](wertobjekt.md) | `extension/xrowmetadata/classes/structs/xrowsitemapitemimage.php:3` | – |
| `xrowSitemapItemModified` | class extends xrowSitemapItem | [Wertobjekt / Struktur](wertobjekt.md) | `extension/xrowmetadata/classes/structs/xrowsitemapitemmodified.php:3` | – |
| `xrowSitemapItemNews` | class extends xrowSitemapItem | [Wertobjekt / Struktur](wertobjekt.md) | `extension/xrowmetadata/classes/structs/xrowsitemapnewsitem.php:3` | – |
| `xrowSitemapItemPriority` | class extends xrowSitemapItem | [Wertobjekt / Struktur](wertobjekt.md) | `extension/xrowmetadata/classes/structs/xrowsitemapitempriority.php:3` | – |
| `xrowSitemapItemVideo` | class extends xrowSitemapItem | [Wertobjekt / Struktur](wertobjekt.md) | `extension/xrowmetadata/classes/structs/xrowsitemapvideoitem.php:3` | – |
| `xrowSitemapList` | class | – | `extension/xrowmetadata/classes/xrowsitemaplist.php:11` | – |
| `xrowSitemapNewsConverterInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `extension/xrowmetadata/classes/interfaces/xrowsitemapnewsconverterinterface.php:3` | – |
| `xrowSitemapTools` | class | [Fabrik und statische Fabrikmethoden](fabrik.md), [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowmetadata/classes/xrowsitemaptools.php:3` | – |
| `xrowSitemapVideoConverterInterface` | interface | [Schnittstelle (Vertrag)](schnittstelle.md) | `extension/xrowmetadata/classes/interfaces/xrowsitemapvideoconverterinterface.php:3` | – |
| `xrowSiteUpdater` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/xrowmetadata/classes/xrowsiteupdater.php:3` | – |

## Modulansichten (2)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/sitemaps/index` | `index.php` | – | `extension/xrowmetadata/modules/sitemaps/module.php:9` |
| `/sitemaps/robots` | `robots.php` | – | `extension/xrowmetadata/modules/sitemaps/module.php:12` |

## Template-Operatoren (3)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| metadata | `xrowMetaDataOperator` | `extension/xrowmetadata/autoloads/eztemplateautoload.php:4` |
| xrowmetadata_license | `xrowMetaDataLicenseOperator` | `extension/xrowmetadata/autoloads/eztemplateautoload.php:11` |
| xrowmetadata_license_enabled, xrowmetadata_license_choices, xrowmetadata_license_info, xrowmetadata_license_inherited, xrowmetadata_license_class_settings | `xrowMetaDataLicensePickerOperator` | `extension/xrowmetadata/autoloads/eztemplateautoload.php:19` |

## Cronjob-Teile (8)

| Cronjob | Datei | Beschreibung |
| --- | --- | --- |
| `archivesitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/archivesitemap.php` | Der Code von extension/xrowmetadata/cronjobs/archivesitemap.php, in eine Klasse verschoben (#207 Stufe 1). |
| `mobilesitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/mobilesitemap.php` | Der Code von extension/xrowmetadata/cronjobs/mobilesitemap.php, in eine Klasse verschoben (#207 Stufe 1). |
| `newssitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/newssitemap.php` | Der Code von extension/xrowmetadata/cronjobs/newssitemap.php, in eine Klasse verschoben (#207 Stufe 1). |
| `sitemap` | `extension/xrowmetadata/classes/runnable/cronjobs/sitemap.php` | Der Code von extension/xrowmetadata/cronjobs/sitemap.php, in eine Klasse verschoben (#207 Stufe 1). |
| `archivesitemap` | `extension/xrowmetadata/cronjobs/archivesitemap.php` | – |
| `mobilesitemap` | `extension/xrowmetadata/cronjobs/mobilesitemap.php` | – |
| `newssitemap` | `extension/xrowmetadata/cronjobs/newssitemap.php` | – |
| `sitemap` | `extension/xrowmetadata/cronjobs/sitemap.php` | – |

## INI-Plugin-Punkte (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[SolrFieldMapSettings] CustomMap[xrowmetadata]` | `ezfSolrDocumentFieldxrowMetadata` | `extension/xrowmetadata/settings/ezfind.ini.append.php:4` |
