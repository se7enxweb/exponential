# ezodf: Import und Export von OpenDocument

Diese Seite ist für Redakteure, die in OpenOffice oder LibreOffice Writer schreiben, und für Administratoren, die die
Konvertierung einrichten. `ezodf` („eZ OpenOffice.org LS“) importiert und exportiert OpenDocument-Writer-Dokumente:

- **Import**: Ein Redakteur lädt eine `.odt`-Datei hoch und erhält ein Inhaltsobjekt. Der Titel und ein Textkörper mit
  Überschriften, Absätzen, Listen, Tabellen und Bildern werden auf Klassenattribute abgebildet.
- **Export**: Dasselbe Objekt lässt sich mit einer Vorlage wieder als Writer-Dokument exportieren.
- **Bibliothek**: Eine allgemeine Bibliothek, um Writer-Dokumente aus Ihren eigenen Modulen zu erzeugen.

Für Konvertierungen braucht es OpenOffice oder LibreOffice 2.4 oder neuer.

## Verwenden

| Ansicht | Zweck |
|---|---|
| `ezodf/import`, `ezodf/upload_import` | Ein Writer-Dokument importieren |
| `ezodf/export`, `ezodf/upload_export` | Ein Objekt nach Writer exportieren |
| `ezodf/authenticate` | Für Import und Export anmelden |

Die Werkzeugleiste von [ezwt](ezwt.md) zeigt die Schaltflächen für Import und Export bei den Klassen, die in
`websitetoolbar.ini [WebsiteToolbarSettings] ODFDisplayClasses[]` aufgeführt sind.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `odf.ini` | `ODFSettings` | `ZipPath` | leer | Pfad des Programms zip |
| `odf.ini` | `ODFSettings` | `TmpDir` | `/tmp` | Temporärer Ordner |
| `odf.ini` | `ODFImport` | `DefaultImportClass` | `article` | Von einem Import angelegte Klasse |
| `odf.ini` | `ODFImport` | `DefaultImportImageClass` | `image` | Klasse importierter Bilder |
| `odf.ini` | `ODFImport` | `RegisteredClassArray[]` | article, folder, image, documentation_page, blog_post | Klassen, die ein Import anlegen darf |
| `odf.ini` | `ODFImport` | `ImportedImagesMediaNodeName`, `PlaceImagesInMedia` | `Imported images`, `false` | Wohin Bilder kommen |
| `odf.ini` | `ODFImport` | `OOConverterAddress`, `OOConverterPort` | `127.0.0.1`, `9090` | Der Konvertierungsdienst |
| `odf.ini` | `ODFExport` | `UseTemplate`, `TemplateName`, `TemplateRepository` | `true`, `ezpublish.ott`, `extension/ezodf/templates` | Writer-Vorlage für den Export |
| `odf.ini` | `ODFExport` | `ClassAttributeMappingToHeader` | `enabled` | Klassenattribute auf den Dokumentkopf abbilden |
| `odf.ini` | `<class>` | `DefaultImportTitleAttribute`, `DefaultImportBodyAttribute`, `Attribute[<odf field>]` | pro Klasse | Teile des Dokuments auf Attribute abbilden |

## Grenzen

Aus dem README der Extension:

- Die Zugriffsprüfung für Bilder in importierten Dokumenten ist nicht vollständig.
- Bilder werden beim Export übergangen, wenn der Benutzer sie nicht lesen kann.
- Das Speichern importierter Bilder im Medienordner funktioniert nicht richtig.

## Was sich in den Releases von Exponential 6 geändert hat

| Version | Änderung |
|---|---|
| 6.1.0 | Modulansichten sind auf einem dauerhaften Worker (Velocity) sicher; siehe [Verhaltensänderungen](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md#1-persistente-php-worker-modul-views-deklarieren-auf-dateiebene-nicht-mehr-ohne-schutz). |
| 6.1.1 bis 6.1.4 | `ezinfo.php`, Name der Extension, Lizenz vollständig, `<br>` wie in HTML5, die Beschreibung nennt Exponential. |
| 6.1.5 | Kommandozeilenskripte und Modulansichten sind Klassen, die die Dateien aufrufen; die Copyright-Hinweise nennen zuerst 1998 - 2026 7x & Exponential Foundation. |
| 6.1.6 | Englische und deutsche Übersetzungen für jeden Text, den die Administrationsoberfläche unübersetzt zeigte. |

## Verwandte Seiten

- [ezwt](ezwt.md), [ezdemo](ezdemo.md)
- [Velocity-Engines](../../../bc/6.0/velocity-engines.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/ezodf.md) und [Versionshinweise](../../../../changelogs/extensions/ezodf.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezodf.md)
- Monate: [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
