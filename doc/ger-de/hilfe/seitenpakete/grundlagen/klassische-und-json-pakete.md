---
title: "Klassische und JSON-Pakete"
audience: developer, admin, designer
category: install-upgrade
order: 21
summary: Die zwei Speicherformate eines Exponential-Pakets - klassische package.xml und JSON package.json -, was sich unterscheidet, wie der Kernel sie auseinanderhält, was das für Seitenpakete und den Paketserver bedeutet und wie Sie umwandeln und prüfen. Das vollständige Format steht im Dokument zum JSON-Paketformat.
keywords: Paketformat, klassisch, XML, JSON, package.json, package.xml, json.ezpkg, JsonML, ezpm convert, ezpm validate, Prüfsummen, SHA-256, RawXMLDatatypes, MaxDocumentBytes, expPackageJson, package format, classic, checksums
type: help
---
# Klassische und JSON-Pakete

Diese Seite ist für Entwickler und Administratoren, die einem Paket im JSON-Format begegnen oder sich fragen, ob ein Seitenpaket es nutzen
sollte. Sie beantwortet: was die zwei Formate sind, was sich ändert und was nicht und was der Paketserver und die Installer erwarten. Die
vollständige Spezifikation ist [Das JSON-Paketformat](../../../bc/6.0/package-json-format.md); diese Seite ordnet es nur in das Bild der
Seitenpakete ein.

## Inhalt

1. [Zwei Serialisierungen eines Pakets](#zwei-serialisierungen-eines-pakets)
2. [Was sich unterscheidet](#was-sich-unterscheidet)
3. [Wie der Kernel sie auseinanderhält](#wie-der-kernel-sie-auseinanderhält)
4. [Seitenpakete und der Paketserver](#seitenpakete-und-der-paketserver)
5. [Umwandeln und prüfen](#umwandeln-und-prüfen)
6. [Welches Sie wählen](#welches-sie-wählen)
7. [Verwandte Seiten](#verwandte-seiten)

## Zwei Serialisierungen eines Pakets

Ein Paket hat ein Modell: Name, Typ, Version, Abhängigkeiten, Installationselemente, Dateien. Dieses Modell lässt sich auf zwei Arten
speichern:

- **Klassisch**: `package.xml` und XML-Dokumente (`ezcontentclass/class-article.xml`, `ezcontentobject/contentobjects.xml` ...). Der
  Standard und das Format jedes Pakets auf dem Paketserver.
- **JSON**: `package.json` und eine `.json`-Datei für jedes Dokument, jede ein JSON-Umschlag um das XML-Element in
  [JsonML](http://www.jsonml.org/), der verlustfreien Abbildung von XML auf JSON-Arrays.

Weil das JSON das XML Element für Element spiegelt, macht der Kernel aus einem JSON-Dokument wieder dasselbe DOM, das das XML ergäbe
(`eZPackage::fetchDOMFromFile()`), und jeder Pakethandler läuft unverändert. Alles auf [Referenz zu package.xml](referenz-zu-package-xml.md)
gilt für beide.

## Was sich unterscheidet

| | Klassisch | JSON |
|---|---|---|
| Definition | `package.xml` | `package.json` |
| Dokumente | `<name>.xml` | derselbe Name mit `.json` |
| Bilder, Templates, Dateien von Erweiterungen, Installationsskripte | wie sie sind | wie sie sind, Byte für Byte |
| Prüfsummen | MD5 nur von Dateien in `<files>` | SHA-256 jeder Datei, in `package.json` |
| Rich Text (`ezxmltext`) | XML im Dokument | als XML-Zeichenkette behalten, `["#xml", "..."]` (`[PackageFormatSettings] RawXMLDatatypes[]`) |
| Archivname beim Export | `<name>-<version>-<release>.ezpkg` | `<name>-<version>-<release>.json.ezpkg` |
| Archivformat | mit gzip komprimiertes tar | dasselbe |
| Schema | der Leser des Kernels | `share/package/schema/package-v1.json` |
| Größtes gelesenes Dokument | | `[PackageFormatSettings] MaxDocumentBytes`, 64 MB |

## Wie der Kernel sie auseinanderhält

Am Inhalt, nie am Dateinamen:

- `eZPackage::definitionFilePath()` nimmt `package.xml`, wenn es eine gibt, sonst `package.json`.
- Beim Import werden beide Namen oben aus dem Archiv entpackt, sodass jedes Format importiert wird (`eZPackage::importUnaudited()`).
- Ein Dokument, das in der Definition mit seinem XML-Namen genannt ist (`filename="class-article"`), wird aus der `.xml`-Datei gelesen oder
  aus der `.json`-Datei gleichen Namens, wenn es keine XML-Datei gibt.
- Das Paket behält das Format, in dem es gespeichert wurde (`$package->attribute( 'format' )`, `xml` oder `json`); ein Export ohne Format
  schreibt dieses Format.

## Seitenpakete und der Paketserver

- Jedes Paket im Ordner 6.0.15 des Paketservers ist klassisch: Seine 44 Archive heißen `<name>.ezpkg`, und keines ist eine `.json.ezpkg`.
  Die Quell-Repositorys (`se7enxweb/sevenx-ezpackage`, `se7enxweb/cjw_multisite-ezpackage`) führen `package.xml`-Dateien.
- Der Setup-Assistent, der Kickstarter und `exp:install` importieren über `eZPackage::import()`, würden also auch ein JSON-Archiv nehmen;
  der Eintrag im Index (`url`) muss nur darauf zeigen.
- Die Installationsskripte eines Seitenpakets (`settings/*.php`) sind Nutzdateien und bleiben in beiden Formaten PHP.

> **Hinweis**
> `ezpm convert` wandelt die Dokumente so um, wie sie sind (`expPackageJson::convertDirectory()`). Ein **Export** eines Repository-Pakets
> (`ezpm export`, **Herunterladen** in der Administration) schreibt die Definition zuerst neu über den Schreiber des Kernels
> (`exportToArchive()`). Vor 6.0.15 ließ dieser Schreiber `<settings>` und das `min-version` von Elementen `<require>` weg, und der Export
> verlor Dateien oben im Paket (die Nebendateien `cjw-*.json`); ab 6.0.15 bleiben alle drei erhalten (siehe
> [Referenz zu package.xml](referenz-zu-package-xml.md#was-verloren-geht-wenn-der-kernel-ein-paket-schreibt)). Archive von Seitenpaketen aus ihrem
> Quellordner zu bauen, ist weiterhin der verlässliche Weg, sie zu veröffentlichen.

## Umwandeln und prüfen

Aus dem Wurzelverzeichnis der Installation (`./console ezpm ...` führt dieselben Befehle aus):

```bash
# convert an archive; --to defaults to the other format
php ezpm.php convert mypackage-1.0.0-1.ezpkg mypackage-1.0.0-1.json.ezpkg --to=json

# convert into a new directory
php ezpm.php convert mypackage-1.0.0-1.json.ezpkg /path/to/new-directory --to=xml

# export a repository package in a chosen format
php ezpm.php export mypackage -d /path/to/dir --format=json

# check an archive, a directory or a repository package without importing it
php ezpm.php validate mypackage-1.0.0-1.json.ezpkg
```

`validate` prüft das Schema, jedes Dokument als XML, die Definition, die Dokumente, die die Elemente nennen, und die Prüfsummen und gibt
`PASS` oder `FAIL` aus. In der Administration stehen **Als JSON herunterladen** und **Prüfen** auf der Seite eines Pakets, und der
Paketassistent fragt, in welchem Format ein neues Paket gespeichert wird.

## Welches Sie wählen

| Situation | Format |
|---|---|
| Ein Seitenpaket für den gemeinsamen Paketserver | klassisch, wie jedes Paket dort |
| Inhaltspakete in Pull Requests prüfen | JSON liest und vergleicht sich für viele Menschen und Werkzeuge besser |
| Inhalte, die von Skripten oder KI-Werkzeugen geschrieben werden und als Entwürfe geprüft werden sollen | JSON, installiert mit `ezpm install --as-drafts` |
| Ein Paket für eine Installation, die älter ist als das JSON-Format | klassisch |

## Verwandte Seiten

- [Grundlagen der Seitenpakete](README.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Das JSON-Paketformat](../../../bc/6.0/package-json-format.md)
- [Klassische XML- und JSON-Pakete](../../designer/erweiterungen/klassische-und-json-paketformate.md): die Sicht des Designers
- [Die Datei .ezpkg](die-datei-ezpkg.md)
- [Ein Paket vor der Veröffentlichung prüfen](../anforderungen/ein-paket-vor-der-veroeffentlichung-pruefen.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
