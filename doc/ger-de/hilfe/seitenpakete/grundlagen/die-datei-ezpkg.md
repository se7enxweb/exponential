---
title: "Die Datei .ezpkg"
audience: admin, developer, hosting
category: install-upgrade
order: 12
summary: Was ein Archiv .ezpkg ist, was darin liegt und wo, welche Namen es bekommt, welche Prüfungen ein Archiv besteht, bevor irgendetwas davon geschrieben wird, und wie Sie eines von Hand öffnen, auflisten und prüfen.
keywords: ezpkg, Paketarchiv, tar.gz, gzip, tar, package.xml, documents, files, simplefiles, settings, ezcontentclass, ezcontentobject, ezextension, UploadSettings, MaxArchiveSize, MaxEntries, eZPackageUploadInspector, build.sh, package archive
type: help
---
# Die Datei .ezpkg

Diese Seite ist für Administratoren und Entwickler, die Paketarchive herunterladen, hochladen, entpacken oder bauen. Sie beantwortet: welches
Format eine Datei `.ezpkg` hat, was darin liegt, welche Prüfungen sie beim Import bestehen muss und wie Sie hineinsehen, ohne sie zu
installieren.

## Inhalt

1. [Das Format](#das-format)
2. [Was darin liegt](#was-darin-liegt)
3. [Dateinamen](#dateinamen)
4. [Wie Exponential ein Archiv liest](#wie-exponential-ein-archiv-liest)
5. [Die Prüfungen, bevor irgendetwas geschrieben wird](#die-prüfungen-bevor-irgendetwas-geschrieben-wird)
6. [Eine .ezpkg von Hand öffnen](#eine-ezpkg-von-hand-öffnen)
7. [Wie die Archive von 7x gebaut werden](#wie-die-archive-von-7x-gebaut-werden)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Format

Eine Datei `.ezpkg` ist ein **mit gzip komprimiertes tar-Archiv** eines Paketverzeichnisses. Nichts anderes: kein Kopf, keine Signatur,
kein Manifest außer der eigenen Definitionsdatei des Pakets. Die Endung kommt von `eZPackage::suffix()` (`ezpkg`). Wenn der Kernel selbst
ein Paket exportiert, schreibt `eZPackage::exportToArchive()` ein tar-Archiv und komprimiert es mit zlib; es kann auch `tar.bz2` und `zip`
schreiben, aber `tar.gz` und `ezpkg` sind dasselbe und der Standard.

Das JSON-Format eines Pakets ist ebenfalls eine `.ezpkg` mit demselben Aufbau; nur die Definitionsdatei und die Dokumente unterscheiden
sich. Siehe [Klassische und JSON-Pakete](klassische-und-json-pakete.md).

## Was darin liegt

Das Archiv enthält den **Inhalt** des Paketverzeichnisses, nicht das Verzeichnis: `package.xml` liegt oben im Archiv, nicht in einem nach
dem Paket benannten Ordner. Ein typischer Aufbau:

| Pfad im Archiv | Was es ist | Geschrieben von |
|---|---|---|
| `package.xml` (oder `package.json`) | Die Definition: Name, Typ, Version, Abhängigkeiten, Installationselemente | `eZPackage::storePackageFile()` |
| `documents/` | In `<documents>` genannte Dokumente, zum Beispiel `LICENCE` | `eZPackage::documentDirectory()` |
| `files/<collection>/<file type>/` | In `<files>` genannte Dateien: Vorschaubilder, Bilder, Designdateien (`files/default/thumbnail/thumbnail.png`) | `eZPackage::appendFile()` |
| `simplefiles/` | Nach Schlüssel aufbewahrte Dateien, etwa Bilder von Inhaltsobjekten (`simplefiles/24ec46f7.jpg`) | `eZPackage::appendSimpleFile()` |
| `settings/` | Die Installationsskripte eines Seitenpakets | dem Autor des Pakets |
| `ezcontentclass/` | Ein XML-Dokument pro Inhaltsklasse (`class-article.xml`) | dem Handler `ezcontentclass` |
| `ezcontentobject/` | `contentobjects.xml` und eine `object-<remote id>.xml` pro Objekt | dem Handler `ezcontentobject` |
| `ezextension/` | `extension-<name>.xml` und die Dateien der Erweiterung | dem Handler `ezextension` |
| `sql/[<database type>/]` | SQL-Dateien von Elementen `ezsql` | dem Handler `ezdb` |

`exportToArchive()` kopiert `documents`, `files`, `simplefiles`, `settings` und das `sub-directory` jedes Installationselements, sodass eine
Datei außerhalb dieser Verzeichnisse einen Export nicht übersteht. Das Verzeichnis `.cache/`, das das lokale Repository neben `package.xml`
führt, ist nie Teil eines Pakets.

Die Auflistung zweier echter Archive vom Paketserver:

```bash
curl -s https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/sevenx_multisite.ezpkg | tar -tzf -
curl -s https://exponential.packages.exponential.earth/exponential/6.0/6.0.15/sevenx_classes.ezpkg | tar -tzf - | head
```

```text
documents/
documents/LICENCE
ezcontentclass/
ezcontentclass/class-real_video.xml
ezcontentclass/class-comment.xml
...
```

## Dateinamen

| Wo | Name | Beispiel |
|---|---|---|
| Paketserver | `<name>.ezpkg` | `sevenx_multisite.ezpkg` |
| Export aus **Setup > Pakete** oder `ezpm export` (klassisch) | `<name>-<version>-<release>.ezpkg` | `mypackage-1.0.0-1.ezpkg` |
| Export im JSON-Format | `<name>-<version>-<release>.json.ezpkg` | `mypackage-1.0.0-1.json.ezpkg` |

Der Name der Datei spielt beim Import keine Rolle: Der Name des Pakets kommt aus `<name>` in seiner Definition
(`eZPackage::importUnaudited()`) und das Format daraus, welche Definitionsdatei oben liegt. Das Hochladen im Setup-Assistenten entfernt eine
Endung `-<digit>.<digits>-<digit>.ezpkg` vom Dateinamen, wenn es meldet, was es importiert hat (`eZStepSiteTypes::uploadPackage()`).

## Wie Exponential ein Archiv liest

`eZPackage::import()` (und `importUnaudited()` dahinter) arbeitet in dieser Reihenfolge:

1. Die tar-Köpfe prüfen (`expPackageArchive::index()`, nächster Abschnitt), dann das Archiv über `compress.zlib://` öffnen
   (ein gzip-komprimiertes Archiv oder ein unkomprimiertes tar-Archiv, so wie es ist). Ein Archiv, das die Archivbibliothek
   so liest, wie es ist (die Kennung `ustar` in jedem Kopf, nur Dateien, Verzeichnisse und GNU-Langnamen, wie der Export von
   Exponential schreibt), wird direkt geöffnet. Jedes andere wird zuerst in diese Form umgeschrieben, in eine temporäre Datei
   unter dem Cache-Verzeichnis, die entfernt wird, sobald sie geöffnet ist: die V7-Archive, die eZ Publish 3.x schrieb,
   PAX-Archive (bsdtar unter macOS und den BSDs, GNU tar `--format=posix`, Pythons `tarfile`), mit GNU tar neu gepackte
   Archive (`./` vor jedem Namen, harte und symbolische Links) und jedes mit einem tar-Typ, den die Bibliothek nicht liest.
2. Nur `package.xml` und `package.json` (nur einfache Dateien, nie ein Link) in ein temporäres Verzeichnis unter dem Cache-Verzeichnis
   (`packages/import/`) entpacken, die Definition lesen und die temporäre Kopie löschen.
3. Das Paket abweisen, wenn sein Name nicht gültig ist (`STATUS_INVALID_NAME`) oder wenn ein Paket dieses Namens bereits im Repository
   existiert (`STATUS_ALREADY_EXISTS`).
4. Das Repository wählen: das übergebene, sonst das aus `<vendor>` gebildete Verzeichnis.
5. Jeden Eintrag des Archivs prüfen (nächster Abschnitt).
6. Das ganze Archiv nach `var/storage/packages/<repository>/<name>/` entpacken und das Paket erneut lesen. Jede entpackte
   Datei erhält den Modus 0644 und jedes Verzeichnis 0755 (innerhalb der Grenzen der Installation für Dateimodi), was immer das
   Archiv angibt (`expPackageArchiveModes`).

Der Import eines Archivs wird im Prüfprotokoll als `system.package.import` festgehalten, mit dem SHA-256 des Archivs und nie seinem Inhalt
(siehe `doc/eng-us/bc/6.0/audit.md`).

## Die Prüfungen, bevor irgendetwas geschrieben wird

`eZPackageUploadInspector` enthält die Regeln. `eZPackage::importUnaudited()` ruft für jedes Archiv, das es entpackt, dessen
`entriesProblem()` auf, sodass der Setup-Assistent, `ezpm import` und die Seite zum Hochladen alle abgedeckt sind:

| Prüfung | Code der Abweisung | Grenze |
|---|---|---|
| Jeder tar-Kopf lesbar, seine Prüfsumme richtig, keine Daten abgeschnitten, jeder PAX-Kopf wohlgeformt | `unreadable` | |
| Ein Langname (GNU `L`/`K`, PAX `path`/`linkpath`) höchstens 4096 Bytes lang, ein PAX-Kopf höchstens 64 KiB | `tar_long_name` | |
| Kein Datei- oder Verzeichnisname in einem Pfad länger als 255 Bytes (kein Dateisystem speichert ihn) | `tar_name_segment` | |
| Höchstens `MaxEntries` Einträge | `too_many_entries` | `50000` |
| Jeder Pfad relativ, ohne `..`, umgekehrten Schrägstrich, NUL oder Steuerzeichen | `unsafe_path` | |
| Entpackte Größe höchstens `MaxUnpackedSize` (Kopien von Links zählen mit) | `too_large_unpacked` | `2147483648` (2 GiB) |
| Der Hersteller ergibt einen brauchbaren Verzeichnisnamen für das Repository | (Import abgewiesen) | |

Was ein Paket nicht enthalten kann, weist das Paket nicht ab. Es wird mit einem Hinweis übergangen, und der Rest des Pakets
wird importiert; die Hinweise stehen im Debug-Log und unter den Warnungen der Validierung auf der Seite zum Hochladen:

| Eintrag | Was geschieht | Hinweis |
|---|---|---|
| Ein harter oder symbolischer Link auf eine Datei des Pakets | Als Kopie dieser Datei importiert | |
| Ein symbolischer Link mit absolutem Ziel oder einem, dessen `..` das Paket verlässt | Übergangen | `link_outside` |
| Ein symbolischer Link auf ein Verzeichnis | Übergangen | `link_directory` |
| Ein Link auf einen Pfad, den das Paket nicht enthält (oder Links, die im Kreis zeigen) | Übergangen | `link_missing` |
| Ein Zeichen- oder Blockgerät, eine Fifo (tar-Typen `3`, `4`, `6`) | Übergangen | `special_file` |
| Eine Sparse-Datei (`S`, PAX `GNU.sparse`) oder ein tar-Typ, den kein Paketwerkzeug schreibt | Übergangen | `unsupported_entry` |

Ohne Hinweis gelesen werden: Dateien der Typen `0`, `\0` (V7, eZ Publish 3.x) und `7`; Verzeichnisse `5`, das GNU-Dump-Verzeichnis
`D` und ein Dateieintrag, dessen Name auf `/` endet; GNU-Langnamen `L` und `K` (auch mit einer Größe, die ein Vielfaches von
512 Bytes ist); erweiterte PAX-Köpfe (`path`, `linkpath`, `size`, `mtime`; erweiterte Attribute und die übrigen Einträge werden
nicht beachtet); der globale PAX-Kopf `g` und die GNU-Datenträgerbezeichnung `V`, die keinen Eintrag benennen; der Eintrag `./` selbst.

Die Seite zum Hochladen in der Administration (`package/upload`) führt zuerst das vollständige `inspect()` aus, das hinzufügt:

| Prüfung | Code der Abweisung | Einstellung |
|---|---|---|
| Der hochgeladene Name endet auf eine erlaubte Endung | `suffix` | `AllowedSuffixes[]`: `ezpkg`, `tar.gz`, `tgz` |
| Die Datei hat höchstens `MaxArchiveSize` Bytes | `too_large` | `268435456` (256 MiB) |
| Die Datei beginnt mit der gzip-Signatur oder ist ein unkomprimiertes tar-Archiv | `not_gzip` | |
| Eine Definitionsdatei oben, wohlgeformt, höchstens 16 MiB, mit gültigem Namen | `no_definition`, `definition_malformed`, ... | |

Die Prüfungen der tar-Köpfe laufen, bevor die Archivbibliothek einen einzigen Eintrag liest, Kopf für Kopf, wobei die
Datenblöcke anhand ihrer Größe übersprungen werden, und die Archivbibliothek bekommt nur ein Archiv in der Form, die sie
liest: Ihr GNU-Leser lief bei einem Kopftyp, den er nicht kennt (ein PAX-Kopf, eine mit `\0` typisierte Datei mit der
Kennung `ustar`), bis `max_execution_time` in einer Schleife, las einen Langnamen in der Größe, die der Kopf angab, in den
Speicher, und las nach einem Langnamen, dessen Größe ein Vielfaches von 512 Bytes ist, einen Block zu viel. Die Pakete jeder
Generation werden importiert: die V7-Archive von eZ Publish 3.x, die ustar-Archive der Exporte von 4.x und 5.x und Pakete, die
mit GNU tar (in jedem seiner Formate), bsdtar oder Pythons `tarfile` neu gepackt wurden. Die Archive, mit denen die Tests des
Kernels das prüfen, liegen in `tests/tests/kernel/classes/packages/fixtures/legacy/`, gebaut von
`build-legacy-package-fixtures.php` daneben.

Die Grenzen stehen in `settings/package.ini [UploadSettings]`; ändern Sie sie in `settings/override/package.ini.append.php`.

```ini
[UploadSettings]
MaxArchiveSize=268435456
MaxUnpackedSize=2147483648
MaxEntries=50000
AllowedSuffixes[]
AllowedSuffixes[]=ezpkg
AllowedSuffixes[]=tar.gz
AllowedSuffixes[]=tgz
```

> **Hinweis**
> `eZPackage::parseDOMTree()` weist auch eine Definition ab, deren `<version><number>`, `<version><release>` oder `<type value>` kein
> einfaches Token aus bis zu 64 Buchstaben, Ziffern und `._+-` ist (`eZPackage::isValidDefinitionToken()`). Ein solches Paket wird gar nicht
> gelesen. Siehe [Referenz zu package.xml](referenz-zu-package-xml.md).

## Eine .ezpkg von Hand öffnen

Sie brauchen nichts als `tar`. Arbeiten Sie in einem eigenen Arbeitsverzeichnis, nie in `var/storage/packages/`.

```bash
# list the entries
tar -tzf sevenx_multisite.ezpkg

# print the definition without unpacking anything else
tar -xzOf sevenx_multisite.ezpkg package.xml

# unpack into a new directory
mkdir sevenx_multisite && tar -xzf sevenx_multisite.ezpkg -C sevenx_multisite

# check a package without importing it (from the installation root)
php ezpm.php validate sevenx_multisite.ezpkg
```

`ezpm validate` prüft die Definition, jedes Dokument, das die Installationselemente nennen, und bei einem JSON-Paket das Schema und die
Prüfsummen; es gibt `PASS` oder `FAIL` aus und endet bei einem Fehlschlag mit 1 (siehe
[das JSON-Paketformat](../../../bc/6.0/package-json-format.md)). Um ein Archiv in eine Installation zu bringen, siehe
[Ein Paket von Hand herunterladen](../paketserver/ein-paket-von-hand-herunterladen.md) und
[Importieren oder installieren](importieren-oder-installieren.md).

## Wie die Archive von 7x gebaut werden

Die Pakete von 7x werden als einfache Ordner in Git geführt
([se7enxweb/sevenx-ezpackage](https://github.com/se7enxweb/sevenx-ezpackage), `packages/<name>/`). `packages/build.sh` macht aus jedem
Ordner ein Archiv aus dem Ordner heraus, weshalb es keinen obersten Ordner gibt:

```bash
cd "${folder%/}"
tar -zcf ../${folder}.ezpkg "${EXCLUDES[@]}" *
```

`EXCLUDES` lässt Dateien der Versionsverwaltung (`.git`, `.gitignore`, `.gitattributes`, `.gitmodules`, `.svn`, `.hg`), Reste von Editoren
und Systemen (`.DS_Store`, `.idea`, `.vscode`, `*~`, `*.swp`) und `node_modules` weg. Wie ein gebautes Archiv den Server erreicht, ist in
[Wie ein Release den Paketserver erreicht](../paketserver/wie-ein-release-den-paketserver-erreicht.md) beschrieben.

## Verwandte Seiten

- [Grundlagen der Seitenpakete](README.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Referenz zu package.xml](referenz-zu-package-xml.md)
- [Klassische und JSON-Pakete](klassische-und-json-pakete.md)
- [Ein Paket vor der Veröffentlichung prüfen](../anforderungen/ein-paket-vor-der-veroeffentlichung-pruefen.md)
- [Mit build.sh neu bauen](../eigene/mit-build-sh-neu-bauen.md)
- [Anleitung zu Paketen, 6. Ein Paket hochladen](../../../anleitungen/pakete.md#6-ein-paket-hochladen)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
