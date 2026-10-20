---
title: Ein Paket vor der Veröffentlichung prüfen
audience: developer, admin, partner
category: install-upgrade
order: 58
summary: Die Prüfungen, die Sie an einem Seitenpaket ausführen, bevor es auf den Paketserver geht, der Reihe nach, von xmllint und einem Blick in das Archiv bis zu ezpm.php validate, der Schaltfläche Prüfen auf der Paketseite und dem Upload-Inspektor der Administration, mit dem, was jede prüft und was nicht.
keywords: prüfen, Validierung, ezpm validate, xmllint, tar -tzf, Upload-Inspektor, package/upload, ValidateButton, validateDirectory, PASS, FAIL, unsafe_path, unsafe_type, definition_incomplete, symbolischer Link, validate, validation, upload inspector, symbolic link
type: help
---
# Ein Paket vor der Veröffentlichung prüfen

Diese Seite ist für Entwickler und Prüfende, die ein Seitenpaket prüfen, bevor es veröffentlicht wird. Eine Version auf dem Paketserver wird
von jeder Installation genutzt, die seinen Index liest, sodass ein kaputtes Paket für alle auf einmal scheitert. Jede Prüfung unten fängt
etwas ab, das die anderen übersehen; führen Sie sie in dieser Reihenfolge aus, von der günstigsten bis zu der, die eine Testinstallation
braucht.

## Inhalt

1. [Die Prüfungen auf einen Blick](#die-prüfungen-auf-einen-blick)
2. [Die Definition mit xmllint prüfen](#die-definition-mit-xmllint-prüfen)
3. [In das Archiv sehen](#in-das-archiv-sehen)
4. [ezpm validate ausführen](#ezpm-validate-ausführen)
5. [Auf eine Testinstallation hochladen](#auf-eine-testinstallation-hochladen)
6. [Die Schaltfläche Prüfen](#die-schaltfläche-prüfen)
7. [Was keine Prüfung abdeckt](#was-keine-prüfung-abdeckt)
8. [Verwandte Seiten](#verwandte-seiten)

## Die Prüfungen auf einen Blick

| Prüfung | Braucht | Fängt ab |
|---|---|---|
| `xmllint --noout package.xml` | Nichts als die Datei | XML, das nicht wohlgeformt ist |
| `tar -tzf` und `find -type l` | Das Archiv und den Quellordner | Falschen Aufbau, Links, Dateien, die nicht hineingehören |
| `php ezpm.php validate` | Eine Installation von Exponential, aus der Shell | Die Definition, wie der Kernel sie liest, den Namen, fehlende Dokumente |
| Hochladen auf `package/upload` | Eine Testinstallation und die Richtlinie `package/import` | Alles, was der Upload-Inspektor prüft |
| Eine frische Installation | Eine Sandbox | Abhängigkeiten, Installationsskripte, die Website selbst |

## Die Definition mit xmllint prüfen

```bash
xmllint --noout packages/example_site/package.xml && echo well-formed
```

Keine Ausgabe von `xmllint` bedeutet, dass das Dokument wohlgeformt ist. Alle 16 Dateien `package.xml` der zwei Quell-Repositorys von 7x und
die `index.xml` von 6.0.15 bestehen diese Prüfung (18.10.2026). Über den Inhalt sagt sie nichts: Ein leerer `<name>` ist wohlgeformt.

## In das Archiv sehen

```bash
tar -tzf example_site.ezpkg | head -20
find packages/example_site -type l
```

Was Sie sehen sollten, und warum:

- [ ] `package.xml` als einfacher Eintrag `package.xml`, nicht in einem Ordner: Der Import sucht nach einem Eintrag genau dieses Namens
      (`eZPackage::importUnaudited()`). `packages/build.sh` führt `tar` aus jedem Paketordner heraus über `*` aus, was diesen Aufbau ergibt.
- [ ] Nur die Verzeichnisse, die das Paket nutzt: `documents/`, `files/`, `settings/`, `simplefiles/` und die Unterverzeichnisse seiner
      Installationselemente (`ezcontentclass/`, `ezcontentobject/`, `ezextension/`).
- [ ] Kein `.git`, `.DS_Store`, keine Backups von Editoren, kein `node_modules`: `build.sh` lässt sie mit seiner Liste `EXCLUDES` weg.
- [ ] `find -type l` gibt nichts aus. Ein symbolischer Link, harter Link oder ein Gerät im Archiv lässt jeden Import es abweisen
      (`unsafe_type`), ebenso ein Eintrag mit `..` oder einem absoluten Pfad (`unsafe_path`).

> **Warnung**
> Dateien oben in einem Paket neben `package.xml` überstehen `build.sh`, das den ganzen Ordner archiviert, aber keinen Export aus der
> Administration oder `ezpm.php export`: `eZPackage::exportToArchive()` kopiert nur die Definition, `documents/`, `files/`, `simplefiles/`,
> `settings/` und die Unterverzeichnisse der Installationselemente. `cjw_multisite_democontent` hält vier Dateien oben
> (`cjw-class-additions.xml`, `cjw-explayouts.json`, `cjw-paths.json`, `cjw-starter-import.json`), muss also aus seinem Quellordner neu
> gebaut und darf nie erneut exportiert werden.

## ezpm validate ausführen

`ezpm.php` im Wurzelverzeichnis des Projekts hat einen Befehl `validate` (`kernel/private/classes/commands/ezpm.php`). Er nimmt ein Archiv,
ein Paketverzeichnis oder den Namen eines importierten Pakets und importiert nichts:

```bash
php ezpm.php validate var/tmp/example_site.ezpkg
php ezpm.php validate packages/example_site
php ezpm.php help validate
```

Er führt `expPackageJson::validateDirectory()` aus (`kernel/classes/exppackagejson.php`), das prüft:

- dass es eine `package.xml` oder `package.json` gibt;
- jedes Dokument als XML und im JSON-Format gegen `share/package/schema/package-v1.json` und die SHA-256-Prüfsummen von `package.json`;
- die Definition, wie der Kernel sie liest (`eZPackage::parseDOMTree()`): einen Namen, einfache Tokens für Version und Typ;
- den Namen mit `eZPackage::isValidName()`;
- dass jedes Dokument, das ein Installationselement nennt (`<sub-directory>/<filename>.xml` und die Objektdateien von Inhaltselementen), im
  Paket liegt.

Er endet mit `PASS <name> (xml): N documents, M files` oder gibt eine Zeile `error` pro Problem und `FAIL <source>: N error(s)` mit dem
Exit-Code 1 aus, sodass er ein Build-Skript stoppen kann. Ein Archiv wird zuerst in ein privates Arbeitsverzeichnis entpackt, wobei unsichere
Einträge so abgewiesen werden wie bei einem Import, und das Verzeichnis wird danach entfernt.

> **Hinweis**
> `validate` führt die Prüfungen des Upload-Inspektors für Dateiname, gzip-Signatur, Größengrenzen und die Elemente `<ezpublish>` und
> `<packaging>` nicht aus. Das Hochladen unten tut es.

## Auf eine Testinstallation hochladen

`package/upload` in der Administration ist die strengste Tür ([Anleitung zu Paketen, Hochladen](../../../anleitungen/pakete.md#6-ein-paket-hochladen)).
Bevor irgendetwas entpackt wird, prüft `eZPackageUploadInspector::inspect()` in dieser Reihenfolge: Eine Datei wurde gesendet; ihr Name endet
auf eine erlaubte Endung; sie ist nicht größer als `MaxArchiveSize`; sie beginnt mit der gzip-Signatur; jeder Eintrag ist sicher und
innerhalb von `MaxEntries` und `MaxUnpackedSize`; es gibt oben eine `package.xml` oder `package.json` von höchstens 16 MiB; die Definition
hat eine Wurzel `<package>` ohne DOCTYPE, einen gültigen Namen, `<ezpublish>`, `<packaging>` und einen brauchbaren Hersteller. Dann führt er
dasselbe `validateDirectory()` aus wie `ezpm.php validate` und zeigt die ersten 20 Fehler.

Die Meldungen sind die aus `eZPackageUploadInspector::message()`, etwa „Der package.xml des Archivs fehlen die Versions- oder
Paketierungsangaben.“ Die Grenzen stehen in `settings/package.ini` `[UploadSettings]`.

Nutzen Sie eine Testinstallation: Ein Hochladen importiert das Paket wirklich, und derselbe Name lässt sich nicht zweimal importieren. Um es
erneut zu versuchen, entfernen Sie das Paket zuerst auf seiner Seite.

## Die Schaltfläche Prüfen

Die Seite eines importierten Pakets (`package/view/full/<name>`) hat eine Schaltfläche **Prüfen**. Sie führt `validateDirectory()` auf dem
Paket aus, wie es im Repository liegt, und zeigt das Ergebnis auf der Seite bis zur nächsten Aktion am Paket. Sie ist der schnellste Weg, ein
Paket zu prüfen, das der Setup-Assistent importiert hat, der selbst nicht prüft.

## Was keine Prüfung abdeckt

Keine der Prüfungen oben liest die Bedeutung eines Pakets. Diese bleiben einer Testinstallation und der Prüfung durch Menschen überlassen:

- ob sich jedes `<require>` finden lässt und neu genug ist ([Abhängigkeiten angeben](abhaengigkeiten-angeben.md));
- ob jedes `<settings-file>` existiert (ein fehlendes überspringt der Schritt zum Anlegen der Websites stillschweigend);
- ob die Installationsskripte auf den Datenbank-Engines laufen, die Sie unterstützen;
- ob die Attribute `md5sum` zu ihren Dateien passen;
- ob Lizenz, Changelog und Vorschaubild vorhanden und richtig sind.

Führen Sie vor der Veröffentlichung eine vollständige Installation in einer Sandbox aus: [In einer Sandbox testen](../eigene/in-einer-sandbox-testen.md).

## Verwandte Seiten

- [Anforderungen an die Dokumentation von Seitenpaketen](README.md)
- [Die Checkliste der Anforderungen](die-checkliste-der-anforderungen.md)
- [Pflichtfelder in package.xml](pflichtfelder-in-package-xml.md)
- [Mit build.sh neu bauen](../eigene/mit-build-sh-neu-bauen.md)
- [ezpm: der Paketmanager auf der Kommandozeile](../../../../eng-us/features/6.0/ezpm-package-manager-cli.md)
- [Das JSON-Paketformat](../../../../eng-us/bc/6.0/package-json-format.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
