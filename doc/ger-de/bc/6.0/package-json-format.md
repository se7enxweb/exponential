# Das JSON-Paketformat

Exponential-Pakete (`.ezpkg`, Setup > Pakete, `ezpm.php`) lassen sich in einer zweiten Serialisierung schreiben:
JSON. Es ist dasselbe Paketmodell wie das klassische XML-Format, Element für Element, sodass sich ein Paket ohne
Verlust von XML nach JSON und zurück umwandeln lässt, ein JSON-Paket über dieselben Paket-Handler installiert wird und
die eigene Serialisierung jedes Datentyps unverändert verwendet wird. Das klassische Format bleibt der Standard; an
einem klassischen Paket, seinem Archiv oder seiner Installation hat sich nichts geändert.

Verwenden Sie das JSON-Format, wenn ein Paket von Werkzeugen gelesen oder geschrieben wird, die mit JSON besser
umgehen als mit XML (Skripte, KI-Agenten, Review-Werkzeuge, Diffs in einem Pull Request), und das klassische Format
überall sonst.

## Kurz gesagt

| | Klassisch | JSON |
|---|---|---|
| Definition | `package.xml` | `package.json` |
| Ein Dokument des Pakets | `ezcontentobject/contentobjects.xml`, `ezcontentclass/class-article.xml`, `ezcontentobject/object-<remote id>.xml` ... | derselbe Name mit `.json` |
| Jede andere Datei (Bilder, Binärdateien, Templates, Einstellungen, Dateien von Erweiterungen) | unverändert | unverändert, Byte für Byte |
| Prüfsummen | MD5 nur der Design-Dateien | SHA-256 jeder Datei, in `package.json` |
| Archiv | `<name>-<version>-<release>.ezpkg` (gzip-komprimiertes tar) | `<name>-<version>-<release>.json.ezpkg` (gzip-komprimiertes tar) |
| Erkennung | am Inhalt: `package.xml` oder `package.json` auf der obersten Ebene des Archivs oder Verzeichnisses | |
| Schema | der Leser des Kernels | [`share/package/schema/package-v1.json`](../../../../share/package/schema/package-v1.json) (JSON Schema 2020-12) |

## Warum dieser Entwurf

- **1:1 mit dem XML, kein neues Inhaltsmodell.** Jedes XML-Dokument eines Pakets wird in
  [JsonML](http://www.jsonml.org/) gespiegelt, der etablierten verlustfreien Abbildung von XML auf JSON-Arrays. Weil
  das JSON das XML *ist*, musste kein Paket-Handler, kein Datentyp und kein Klassen-Serialisierer geändert werden: Der
  Kernel verwandelt ein JSON-Dokument zurück in dasselbe DOM, das die XML-Datei liefert
  (`eZPackage::fetchDOMFromFile()`), und die Handler laufen wie immer. Ein inhaltsspezifisches JSON-Modell hätte einen
  zweiten Serialisierer pro Datentyp bedeutet, für immer im Gleichschritt mit dem ersten zu halten.
- **Rich Text bleibt Rich Text.** Der Wert eines Attributs `ezxmltext` wird als seine XML-Zeichenkette behalten
  (`["#xml", "..."]`), sodass er sich wie das XML des Editors liest und nicht in verschachtelte Arrays zerfällt. Die
  Liste der so behandelten Datentypen ist `[PackageFormatSettings] RawXMLDatatypes[]` in `package.ini`.
- **Dasselbe Archiv.** Ein JSON-Paket ist ein `.ezpkg` (gzip-komprimiertes tar) wie jedes andere: die Upload-Seite,
  die Prüfungen beim Upload (`eZPackageUploadInspector`: sichere Pfade, Größen, Arten von Einträgen), `ezpm import`,
  der Paketserver und der Upload des Setup-Assistenten nehmen es ohne zweiten Codepfad an. Das Format wird aus dem
  Inhalt gelesen (welche Definitionsdatei auf der obersten Ebene liegt), nie aus dem Dateinamen; der Name
  `.json.ezpkg` hilft nur Menschen, die beiden Downloads auseinanderzuhalten.
- **Dateien über ihren Hash.** Binärdateien bleiben, wo das klassische Layout sie ablegt (`simplefiles/<key>.<ext>`,
  `files/...`), so benannt, wie die Paket-Handler sie benennen; `package.json` fügt den SHA-256 jeder Datei hinzu,
  sodass eine geänderte oder fehlende Datei gefunden wird, bevor etwas installiert wird.

## Ein Dokument

Jedes JSON-Dokument ist eine Hülle um das Wurzelelement des XML-Dokuments, das es spiegelt:

```json
{
    "$schema": "https://raw.githubusercontent.com/se7enxweb/exponential/main/share/package/schema/package-v1.json",
    "format": "exponential-package",
    "format-version": "1.0",
    "kind": "document",
    "source": "ezcontentobject/object-media-o-1200.xml",
    "root": ["ezremote:object",{"xmlns:ezremote":"http://ez.no/ezobject","xmlns:ezobject":"http://ez.no/object/","name":"Home Hero","remote_id":"media-o-1200","class_remote_id":"b86c970a6ad8e3d268581ec10991a958","ezremote:class_identifier":"ng_component_hero","initial_language":"eng-US"},
        ["ezobject:version-list",{"active_version":"16"},
            ["ezobject:version",{"xmlns:ezremote":"http://ez.no/ezobject","ezremote:version":"16","ezremote:status":"1"},
                ["ezobject:object-translation",{"language":"eng-US","object_name":"Home Hero"},
                    ["ezobject:attribute",{"ezremote:identifier":"title","name":"Title","type":"ezstring"},
                        ["text","Scale your fitness business with us"]
                    ],
                    ["ezobject:attribute",{"ezremote:identifier":"description","name":"Description","type":"ezxmltext"},
                        ["#xml","<section><paragraph>We help gym owners ...</paragraph></section>"]
                    ],
                    ["ezobject:attribute",{"ezremote:identifier":"image","name":"Image","type":"ezimage","image-file-key":"2f4d2aa580e85579751310ac32afc509","alternative-text":""}]
                ]
            ]
        ]
    ]
}
```

| Element | Bedeutung |
|---|---|
| `$schema` | das JSON Schema des Formats (optional) |
| `format` | immer `exponential-package` |
| `format-version` | `1.x`; ein Leser für 1.x liest jedes 1.x-Dokument |
| `kind` | `definition` für `package.json`, `document` für jedes andere Dokument |
| `source` | das XML-Dokument, das es spiegelt, relativ zum Paketverzeichnis (optional; von `validate` geprüft) |
| `root` | das Wurzelelement in JsonML |
| `checksums` | nur `package.json`: Pfad => SHA-256 jeder anderen Datei des Pakets |

### JsonML

| XML | JSON |
|---|---|
| `<name attr="v">children</name>` | `["name", {"attr": "v"}, child, ...]`; das Attributobjekt entfällt, wenn es keine Attribute gibt |
| Namespace-Deklaration `xmlns:p="uri"` | ein Attribut `"xmlns:p": "uri"`, vor den anderen Attributen, an dem Element, das sie deklariert |
| Text | eine Zeichenkette |
| `<!-- c -->` | `["#comment", " c "]` |
| `<![CDATA[x]]>` | `["#cdata", "x"]` |
| `<?target data?>` | `["?target", "data"]` |
| Rich Text (der Wert eines Attributs eines Datentyps in `RawXMLDatatypes[]`) | `["#xml", "<section ...>...</section>"]` |

Text, der nur aus Leerraum mit einem Zeilenumbruch besteht, ist Formatierung, wie der XML-Leser des Kernels ihn
bereits behandelt, und wird nicht geschrieben; jeder andere Text, auch ein einzelnes Leerzeichen, bleibt erhalten. Ein
Dokument wird abgelehnt, wenn es nicht dieses Format hat, wenn ein Name kein XML-Name ist, wenn ein Wert keine
Zeichenkette ist, wenn Rich Text ein DOCTYPE, eine Entity-Deklaration oder eine XML-Deklaration enthält oder wenn das
XML, das es ergibt, nicht wohlgeformt ist. Dokumente, die größer als `[PackageFormatSettings] MaxDocumentBytes`
(64 MB) oder tiefer als 512 Ebenen verschachtelt sind, werden abgelehnt.

### Welche Dateien Dokumente sind

`package.json`, die Datei jedes Installations- und Deinstallationselements (`<sub-directory>/<filename>.json`) und die
Objektdateien, die ein Element für Inhaltsobjekte nennt (`<object-file filename="object-x.xml"/>`, in
`ezcontentobject/`). Verweise innerhalb von Dokumenten behalten ihre klassischen Namen (`filename="object-x.xml"`):
Der Leser löst `x.xml` zu `x.json` auf, wenn es keine XML-Datei gibt. Jede andere Datei ist Nutzlast und wird
unverändert kopiert, auch wenn sie XML oder JSON ist (die `extension.xml` einer Erweiterung, die eigene
`cjw-paths.json` eines Site-Pakets).

### Metadaten des Pakets

Ein Paket kann in seiner Definition freie Zeichenketten der Form Schlüssel => Wert tragen,
`<metadata><entry key="...">value</entry></metadata>` (`"metadata"` in JsonML), für Werkzeuge, die Informationen mit
einem Paket aufbewahren müssen; Schlüssel sind 1 bis 100 Zeichen aus `A-Z a-z 0-9 . _ : -` und mit einem Namensraum
des Werkzeugs versehen (`expai.ai-generated`). `eZPackage::setMetadata()`, `eZPackage::metadata()`,
`$package.metadata` in Templates; Setup > Pakete zeigt sie auf der Seite des Pakets. Ein Paket ohne Metadaten wird
genau wie bisher geschrieben.

## Kommandozeile

```bash
# export in the JSON format (as <name>-<version>-<release>.json.ezpkg); --format=xml for the classic one
php ezpm.php export mypackage -d /path/to/dir --format=json

# convert an archive or a package directory; --to defaults to the other format
php ezpm.php convert mypackage-1.0-1.ezpkg mypackage-1.0-1.json.ezpkg --to=json
php ezpm.php convert mypackage-1.0-1.json.ezpkg /path/to/new-directory --to=xml

# check a package (archive, directory or repository package) without importing it:
# the schema, every document as XML, the definition, the documents the items name, the checksums
php ezpm.php validate mypackage-1.0-1.json.ezpkg

# import and install: the format is detected
php ezpm.php import mypackage-1.0-1.json.ezpkg
php ezpm.php install mypackage

# what an install would meet here, by remote id (nothing is written); --all lists every object
php ezpm.php check mypackage

# install the content objects as drafts
php ezpm.php install mypackage --as-drafts [--drafts-existing=update|skip]
```

`./console ezpm ...` führt dieselben Befehle aus. Exit-Code 1 bei jedem Fehler; `validate` gibt `PASS`/`FAIL` aus.

## Setup > Pakete

- **Herunterladen / JSON** auf jeder Paketkarte und **Herunterladen / Als JSON herunterladen** auf der Seite des
  Pakets: `package/export/<name>[/<repository>]/(format)/xml` oder `/(format)/json`. Ohne `(format)` wird das Paket in
  dem Format exportiert, in dem es gespeichert ist. Die Seite zeigt das Format des Pakets und seine Metadaten.
- **Hochladen** (`package/upload`) nimmt beide Formate an.
- **Installieren** (`package/install/<name>`) zeigt, worauf die Installation hier trifft (siehe unten), und bietet
  **Als Entwürfe installieren** an.
- **Prüfung** (ab 2026-10-12): Jeder Upload wird vor dem Import geprüft (jedes Dokument wohlgeformt, die
  JSON-Dokumente gegen `share/package/schema/package-v1.json`, die Prüfsummen von `package.json`) und mit der Liste
  dessen abgelehnt, was fehlgeschlagen ist; **Prüfen** auf der Seite eines Pakets führt dieselbe Prüfung aus. Die
  Installationsseite führt jedes Objekt auf, mit dem, was die Installation damit tut und wohin sein Hauptort kommt.
- **Im Hintergrund installieren**: Der Content-Job `packageinstall` installiert mit den Wahlen des Assistenten als
  anfragender Benutzer, vorgeschlagen oberhalb von `[ContentJobSettings] SynchronousLimit`; die Seite des Pakets zeigt
  seinen Fortschritt und führt die Entwürfe auf, wenn er fertig ist
  ([Content-Jobs](inhaltsjobs.md#jobtypen-anderer-teile-des-kernels)).
- **Inhaltsbrowser**: JSON-Dokumente werden nach ihrer Art aufgeführt (Paketdefinition, Inhaltsobjekt, Inhaltsklasse)
  und als JSON oder als das XML angezeigt, das sie spiegeln, ein XML-Dokument ebenso als JSON; eine Binärdatei als
  Karte mit ihrem SHA-256 und der Angabe, ob er zu `package.json` passt.
- **New package** wählt das Format, in dem das Paket gehalten wird (Classic XML oder JSON); ein JSON-Paket wird an Ort
  und Stelle umgewandelt, wenn der Assistent fertig ist (`expPackageJson::convertInPlace()`).

Nach jeder Installation als Entwürfe (in der Administration, mit `ezpm`, im Hintergrund) löst der Kernel das
Notify-Ereignis `package/install/drafts` aus (`eZPackage::notifyDraftsInstalled( $package, $result, $context )`),
sodass eine Erweiterung die Entwürfe kennzeichnen oder verzeichnen kann:

```ini
# extension/myext/settings/site.ini.append.php
[Event]
Listeners[]=package/install/drafts@myExtPackageListener::draftsInstalled
```

## Worauf die Installation hier trifft

`expPackageConflictReport::build( $package )` (und `ezpm check` und die Installationsseite) liest jedes Inhaltsobjekt
und jede Klasse des Pakets und sucht jedes über seine Remote-ID, ohne etwas zu schreiben:

| Aktion für das Objekt | Wann |
|---|---|
| `create` | kein Objekt hat seine Remote-ID |
| `update` | ein Objekt derselben Klasse hat seine Remote-ID: Eine Installation aktualisiert es, eine Installation als Entwürfe fügt einen Entwurf hinzu |
| `conflict` | die Remote-ID gehört zu einem Objekt einer anderen Klasse, oder die Knoten-Remote-ID eines Ortes gehört zu einem Knoten eines anderen Objekts |
| `error` | seine Klasse ist weder installiert noch im Paket |

Jeder Ort sagt, wohin er käme: `top` (der bei der Installation gewählte oberste Knoten), `installed` (der
Elternknoten ist im Baum), `in_package` (der Elternknoten ist ein anderes Objekt des Pakets) oder `missing`. Jede
Klasse sagt, ob eine Klasse mit ihrer Remote-ID oder ihrem Bezeichner existiert.

## Als Entwürfe installieren

Eine allgemeine Installationsoption des Kernels (`$installParameters['as_drafts'] = true`; `ezpm install --as-drafts`;
das Kästchen auf der Installationsseite) für Inhalte, die eine Person prüft, bevor sie live gehen, etwa von KI
geschriebene oder von einem anderen Team gelieferte Inhalte:

- **Nichts wird veröffentlicht**, und kein veröffentlichtes Objekt, kein Knoten, Name, keine Sektion und kein Flag
  „immer verfügbar“ wird geändert.
- Ein **neues Objekt** wird zu einem Objekt mit einem Entwurf: die aktive Version des Pakets (sonst seine letzte), in
  jeder Sprache dieser Version.
- Ein **vorhandenes Objekt** (dieselbe Remote-ID) erhält eine neue Entwurfsversion mit den Werten des Pakets;
  `drafts_existing=skip` (`--drafts-existing=skip`, „bleibt unverändert“) lässt es in Ruhe. Ein vorhandenes Objekt
  **einer anderen Klasse** ist ein Konflikt und bleibt immer in Ruhe (`conflict`).
- Nur Elemente für **Inhaltsobjekte** werden installiert. Klassen, Einstellungen, Design-Dateien, Templates, Overrides,
  Fetch-Aliase, Erweiterungen und Installationsskripte würden die Website sofort ändern; sie werden als nicht
  installiert aufgeführt (`$installParameters['skipped-items']`). Installieren Sie dafür das Paket normal oder zuerst
  das Klassenpaket.
- Ein Entwurf, dessen Elternknoten ein anderes neues Objekt des Pakets ist, **wartet** darauf
  (`$installParameters['drafts-waiting']`): Ein Entwurf als Elternknoten hat noch keinen Knoten. Veröffentlichen Sie den
  Elternknoten (sein Knoten erhält die Knoten-Remote-ID des Pakets) und installieren Sie das Paket dann erneut als
  Entwürfe: Die Elternknoten werden gefunden, und die Kinder erhalten ihren Platz.
- Das Paket wird **nicht als installiert markiert**: Die Installation als Entwürfe lässt sich wiederholen.
- `$installParameters['drafts']` führt jeden Entwurf auf (Remote-ID, Objekt-ID, Version, Name, `new_draft`,
  `draft_of_existing`, `skipped` oder `conflict`); die Installationsseite verlinkt jeden auf `content/versionview`, wo
  er geprüft und zur Veröffentlichung gesendet wird (Freigabe-Workflows gelten).

## PHP-API

| Aufruf | Tut |
|---|---|
| `$package->exportToArchive( $path, 'tar.gz', 'json' )` | Export im JSON-Format (`'xml'` klassisch, `false` wie gespeichert) |
| `$package->exportName( 'json' )` | `<name>-<version>-<release>.json.ezpkg` |
| `$package->attribute( 'format' )` | `xml` oder `json`: das Format, in dem das Paket gespeichert ist |
| `$package->setMetadata( $key, $value )`, `$package->metadata( $key )`, `$package->attribute( 'metadata' )` | Metadaten des Pakets |
| `eZPackage::import( $archive, $name )` | beide Formate, am Inhalt erkannt |
| `eZPackage::fetchDOMFromFile( $path )` | ein Dokument über seinen XML-Namen, aus der XML- oder der JSON-Datei |
| `eZPackage::definitionFilePath( $dir )` | `package.xml`, sonst `package.json` |
| `eZPackage::archiveDirectory( $dir, $archive )` | ein Verzeichnis als `.ezpkg` |
| `expPackageJson::convert( $source, $target, $to )` | ein Archiv oder Verzeichnis in ein Archiv oder Verzeichnis umwandeln |
| `expPackageJson::convertDirectory( $source, $target, $to )` | ein Paketverzeichnis umwandeln |
| `expPackageJson::validateDirectory( $dir )` | die Prüfungen von `ezpm validate` |
| `expPackageJson::extractArchive( $archive, $dir )` | mit den Prüfungen des Uploads entpacken |
| `expPackageJson::canonical( $dom )` | die kanonische Form, nach der zwei Dokumente verglichen werden |
| `expPackageConflictReport::build( $package )` | worauf eine Installation hier trifft |
| `expJsonSchemaValidator::validate( $data, $schema )` | Prüfung gegen ein JSON Schema (die Teilmenge, die die Schemas des Kernels verwenden) |

## Einstellungen

`package.ini`:

```ini
[PackageFormatSettings]
# datatypes whose attribute value is kept as an XML string in the JSON format (["#xml", "..."])
RawXMLDatatypes[]
RawXMLDatatypes[]=ezxmltext
# the largest JSON document read, in bytes
MaxDocumentBytes=67108864
```

## Hin und zurück, überprüft

`XML -> JSON -> XML` und `JSON -> XML -> JSON` bei jedem Paket aus `var/storage/packages/7x` (5 Pakete,
540 Dokumente, 390 davon mit Rich Text) und bei einem mit `ezpm` exportierten Test-Teilbaum (ein Ordner in eng-US und
ger-DE mit Rich-Text-Links, einer Einbettung und Schlüsselwörtern, ein Artikel mit einer Relation, ein Bild, eine
Datei, ein verschachtelter Artikel): jedes Dokument kanonisch gleich (C14N des DOM, das der Kernel liest, abgesehen von
Leerraum zur Formatierung), jede andere Datei Byte für Byte, das JSON ein Fixpunkt (erneut Byte für Byte geschrieben).
Das JSON-Paket des Test-Teilbaums wurde mit jedem Attributwert wie exportiert installiert, in beiden Sprachen; als
Entwürfe installiert, veröffentlichte es nichts. Tests: `tests/tests/kernel/classes/packages/expPackageJsonTest.php`.

Byte für Byte ist das XML nicht immer dasselbe wie die Originaldatei: Einrückung, Stil der Anführungszeichen oder
`<text></text>` für ein leeres Element unterscheiden sich zwischen den Werkzeugen, die es geschrieben haben; der Kernel
liest beides gleich.
