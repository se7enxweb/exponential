---
title: Die Import- und Export-Werkzeuge von Exponential
audience: developer, admin
category: install-upgrade
order: 7
summary: Alle Import- und Export-Werkzeuge, die es in Exponential 6 und den von ihm benötigten Extensions tatsächlich gibt – xrowextract (CSV, XML, JSON, Site-Archive, Pakete), ezcsvimport/ezcsvexport, Pakete, der ZIP-Import von ezmultiupload, ezodf, RSS-Import, exp:layouts, die REST-API und expservices, Datenbank-Dumps – sowie das Grundgerüst eines eigenen Importskripts.
keywords: Import, Export, Paket, ZIP-Import, RSS-Import, Datenbank-Dump, Importskript, Remote-ID, import, export, xrowextract, csv, json, xml, ezpkg, package, ezpm, ezcsvimport, ezcsvexport, ezmultiupload, zip, ezodf, rss import, exp:layouts, REST, expservices, ezsqldumpschema, sevenx_dse, createAndPublishObject, remote id, script
type: install
---
# Die Import- und Export-Werkzeuge von Exponential

Diese Seite führt die Werkzeuge auf, die eine Migration nutzen kann, was jedes davon liest und schreibt, und die Befehle
so, wie sie im Code stehen. Greifen Sie zu ihnen, bevor Sie ein eigenes Skript schreiben; wenn Sie doch eines schreiben,
beginnen Sie mit dem [Grundgerüst in Abschnitt 4](#4-ein-eigenes-importskript-das-grundgerüst).

Bei jedem Werkzeug ist die Datei genannt, aus der die Angaben stammen. Werkzeuge aus einer Extension stehen zur
Verfügung, wenn diese Extension installiert und aktiv ist; die hier genannten Extensions werden alle von der
`composer.json` im Wurzelverzeichnis von Exponential 6.0.15 vorausgesetzt. Führen Sie Konsolenbefehle im
Installationsverzeichnis aus; auf einem Server, auf dem Sie als root arbeiten, ergänzen Sie `--allow-root-user`.

[Übersicht Migration](README.md) · [Checkliste](checkliste.md) · [Inhalte auf Exponential abbilden](inhaltsmodell-zuordnen.md)

## Inhalt

- [1. Import in Exponential](#1-import-in-exponential)
- [2. Export aus Exponential](#2-export-aus-exponential)
- [3. URLs, Weiterleitungen und Metadaten](#3-urls-weiterleitungen-und-metadaten)
- [4. Ein eigenes Importskript: das Grundgerüst](#4-ein-eigenes-importskript-das-grundgerüst)
- [5. Welches Werkzeug für welche Aufgabe](#5-welches-werkzeug-für-welche-aufgabe)

## 1. Import in Exponential

### xrowextract: Import von CSV, XML und JSON (`se7enxweb/xrowextract`)

Der vollständigste Importer. Er liest die XML-, CSV- oder JSON-Dateien, die xrowextract selbst schreibt, oder jede
Datei, deren Spalten Sie zuordnen, und legt Objekte einer Klasse unterhalb eines übergeordneten Knotens an oder
aktualisiert sie. Er liest die Datei als Datenstrom (keine Größenbeschränkung für XML und CSV), prüft die Rechte des
Benutzers, läuft als Hintergrundjob und setzt nach einer Unterbrechung wieder auf.
Aus `extension/xrowextract/bin/php/import.php`:

```bash
# dry run first (nothing is written without --apply)
./console ext:xrowextract:import --file=articles.xml --class=ng_article --parent=2
./console ext:xrowextract:import --file=articles.xml --class=ng_article --parent=2 --apply
# update existing objects matched by object id, in one language
./console ext:xrowextract:import --file=export.csv --match=object_id --language=ger-DE
# map columns of a foreign file to attributes
./console ext:xrowextract:import --file=export.csv --map=title=title,authors-ids=authors:ids
# large files: in the background, resumable
./console ext:xrowextract:import --file=big.xml --class=ng_article --parent=2 --apply --background
./console ext:xrowextract:import --file=big.xml --class=ng_article --parent=2 --apply --resume-from=40001
```

Die Administration bietet denselben Importer: die Schaltfläche **Import/Export** in der Knotenansicht, *In dieses
Element importieren* (`xrowextract/import/(parent)/<node id>`). Jeder Export von xrowextract enthält ein **typisiertes
Spaltenmanifest** (`<file>.manifest.json`: je Spalte Datentyp, Format, Sprache und Importziel); eine Datei mit Manifest
lässt sich exakt zurück zuordnen, und `--manifest`, `--no-manifest` sowie ein ZIP aus Datei und Manifest werden
akzeptiert.

Für eine Migration bedeutet das: Wandeln Sie die Quelle in CSV, XML oder JSON mit einer Spalte pro Attribut um (am
einfachsten treffen Sie das Format, indem Sie ein Beispielobjekt der Zielklasse mit xrowextract exportieren und dessen
Spalten übernehmen), und importieren Sie dann mit `--map`, zuerst als Probelauf.

### `ezcsvimport` (Kernel)

Der einfache Importer des Kernels (`kernel/private/classes/commands/ezcsvimport.php`):

```bash
./console exp:ezcsvimport --class=article --creator=14 --storage-dir=var/tmp/import/files 2 var/tmp/import/articles.csv
```

Die Argumente sind die ID des übergeordneten Knotens und die Datei. Die Datei wird mit `;` als Trennzeichen und `"` als
Anführungszeichen gelesen, **ohne Kopfzeile**: Spalte *n* geht über `fromString()` des Datentyps in das *n*-te Attribut
der Klasse. Spalten für Bilder und Binärdateien nennen eine Datei unterhalb von `--storage-dir`. Er legt nur an
(keine Aktualisierung); nutzen Sie ihn also für ein erstes Befüllen einer einfachen Klasse und für alles andere lieber
xrowextract.

### Pakete (`.ezpkg`)

Pakete transportieren Inhaltsklassen und Inhaltsobjekte (mit ihren Dateien) von einer Exponential-Installation in eine
andere ([Pakete](../../anleitungen/pakete.md)). Von der Shell aus (`ezpm.php` im Wurzelverzeichnis):

```bash
php ezpm.php export <package> -d var/tmp      # write an .ezpkg
php ezpm.php import var/tmp/<package>.ezpkg  # import it into the package repository
php ezpm.php help                            # every command
```

xrowextract ergänzt eine Kommandozeile für Pakete (`extension/xrowextract/bin/php/package.php`):

```bash
./console ext:xrowextract:package --export --node=2 --subtree --class=ng_article --file=var/tmp/articles.ezpkg
./console ext:xrowextract:package --inspect=my_package
./console ext:xrowextract:package --install=my_package --parent=2 --dry-run
./console ext:xrowextract:package --install=my_package --parent=2 --object-mode=update --class-mode=skip
./console ext:xrowextract:package --template --class=ng_article --variant=both --file=var/tmp/ng_article_template.ezpkg
```

Pakete sind das Werkzeug für den Umzug **zwischen Exponential-Installationen** (und aus älteren Releases dieses
Kernels). Andere Systeme erzeugen dieses Format nicht.

### Der ZIP-Import der Upload-Seite (`se7enxweb/ezmultiupload`)

Die Seite für den Upload mehrerer Dateien kann ZIP-Dateien zu Inhalten entpacken: Jede Datei wird je nach Typ zu einem
Bild- oder Dateiobjekt, und *Ordnerstruktur beibehalten* legt für jeden Ordner des Archivs ein Ordnerobjekt an. Die
Funktion ist ausgeschaltet, bis der Benutzer *Inhalt komprimierter ZIP-Dateien importieren* einschaltet, und
`ezmultiupload.ini [ZipSettings]` begrenzt Tiefe, Größe, Anzahl der Einträge und Kompressionsverhältnis (ZIP-Bomben
werden abgewiesen). Bilder, deren Klasse einen Alternativtext verlangt, werden übersprungen, weil ein ZIP keinen
enthält. Quelle: `extension/ezmultiupload/doc/upload-page.md`. Nutzen Sie ihn, um eine Mediathek zu übernehmen.

### Office-Dokumente (`se7enxweb/ezodf`)

Importiert OpenOffice-/LibreOffice-Writer-Dokumente als Inhaltsobjekte und exportiert Objekte als Writer-Dokumente
(und, mit dem Konvertierungsdienst, als Word und PDF). Dafür wird OpenOffice oder LibreOffice auf dem Server benötigt.
Quelle: `extension/ezodf/README`. Nützlich, wenn die alte „Site“ aus einer Sammlung von Dokumenten besteht.

### RSS-Import (Kernel)

**Setup > RSS** kann einen Feed in Inhalte importieren: Jeder neue Eintrag wird zu einem Objekt der gewählten Klasse
unterhalb eines Knotens, wobei die Felder des Feeds Attributen zugeordnet werden; der Cronjob-Teil `rssimport` führt
ihn aus ([RSS-Feeds](../../anleitungen/rss-feeds.md)). So lässt sich während einer Übergangszeit ein Blog weiter einlesen.
Ein Feed enthält nur seine neuesten Einträge und ist daher allein keine vollständige Migration.

### Layouts (`se7enxweb/explayouts`)

Layouts, gemeinsame Layouts und ihre Zuordnungsregeln wandern als eine ZIP-Transferdatei zwischen
Exponential-Installationen (`extension/explayouts_ui/bin/php/layouts.php`):

```bash
./console exp:layouts list [--drafts] [--json]
./console exp:layouts export [--all] [--only=<layouts>] [--shared=all|<layouts>] [--rules=none|selected|all] [--output=<file.zip>]
./console exp:layouts import <file.zip> [--dry-run] [--strategy=skip|overwrite|copy] [--publish] [--unmapped=drop|keep|fail]
```

### Die REST-API und `expservices`

- **REST** (`se7enxweb/ezprestapi`): Lesen unter `/api/ezp/v1/...` und `/api/ezp/v2/...`; einen Knoten anlegen ist
  `POST /api/ezp/v2/content/node/create`, löschen `.../content/node/delete/<id>`; Aktualisieren antwortet mit 501
  (noch nicht implementiert). Authentifizierung mit einem OAuth2-Token oder einem persönlichen API-Schlüssel. Details:
  [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md).
- **`expservices`** (im Verzeichnis `extension/` des Kernels): die Funktionen der Administration als benannte
  HTTP-Dienste, mit einem Shell-Client und Python-Beispielen ([Entfernte Dienste und Apps](../../anleitungen/entfernte-dienste-und-apps.md)).

Nutzen Sie die HTTP-Schnittstellen, wenn der Importer auf einem anderen Rechner läuft (zum Beispiel ein
Node.js-Skript). Für große Importe ist ein Skript auf dem Server ([Abschnitt 4](#4-ein-eigenes-importskript-das-grundgerüst))
schneller und einfacher.

### Umstieg von LoveStack

`./console exp:explovestack` prüft eine Installation, auf der LoveStack lief (nur lesend), und übernimmt deren
Datenbank als die von Exponential. Die Schritte stehen unter [LoveStack: Migration](../../../en/us/bc/6.0/love/MIGRATION.md).

## 2. Export aus Exponential

| Werkzeug | Schreibt | Einsatzzweck |
|---|---|---|
| CSV-Export von xrowextract: `./console ext:xrowextract:csv --class=ng_article --node=2 --output=articles.csv` (auch XML, JSON; `--list-classes`, `--list-columns`, `--preview=10`) | eine Datei pro Klasse, mit typisiertem Manifest | Inhalte in ein anderes System übertragen; Tabellenkalkulationen |
| Site-Archiv von xrowextract: `./console ext:xrowextract:archive --set=everything --format=tar.xz --output=var/backups/` | pro Klasse eine CSV (Objekt-ID, Remote-ID, Hauptknoten, übergeordneter Knoten, URL-Alias, Datumsangaben, alle Attribute), `manifest.json`, `README.txt`, als ZIP, TAR.GZ, TAR.BZ2 oder TAR.XZ. Passwort-Hashes werden nie exportiert | ein vollständiger, selbstbeschreibender Export einer Site |
| Pakete von xrowextract: `./console ext:xrowextract:package --export ...` | `.ezpkg` | andere Exponential-Installationen |
| `./console exp:ezcsvexport` | eine CSV pro Klasse für einen Teilbaum (`--storage-dir` für Dateien) | ein schneller Dump |
| `php ezpm.php export` | `.ezpkg` | Klassen und Inhalte in ein anderes Exponential |
| `./console exp:layouts export` | ein ZIP mit Layouts und Regeln | Layouts in ein anderes Exponential |
| `./console ext:bccie:export --object=ID --format=csv\|sylk` (`se7enxweb/bccie`) | gesammelte Informationen (Formulareinsendungen) als CSV oder SYLK | Formulardaten |
| `./console exp:mailconsent` (Export als CSV) | das Einwilligungsprotokoll der E-Mail-Einstellungen | Datenschutznachweise |
| `./console exp:ezsqldumpschema --output-types=all ...` | Datenbankschema und Daten im neutralen Format von Exponential oder als SQL | eine vollständige Datenbankkopie, für jede Datenbank-Engine |
| `sevenx_dse` (Datenbankeditor der Administration, `se7enxweb/sevenx_dse`) | SQL-Dump, CSV, TSV von Tabellen | einzelne Tabellen, durch einen Administrator |
| Lesezugriffe über REST v1/v2 | JSON von Knoten, Unterknoten und Feldern | ein anderes System, das Inhalte über HTTP abruft |
| ezodf | Writer-, Word- und PDF-Dokumente | Dokumente für Menschen |

Das Speicherverzeichnis (`var/<site>/storage/`) enthält die Originalbilder und -dateien; kopieren Sie es zusammen mit
jedem Export, der auf Dateien verweist. Details zum Wechsel weg von Exponential: [Migration weg von Exponential](exponential-verlassen.md).

## 3. URLs, Weiterleitungen und Metadaten

| Bedarf | Werkzeug |
|---|---|
| Eine alte Adresse, die jetzt einen Knoten zeigen oder auf ihn weiterleiten soll | ein eigener URL-Alias am Knoten (Reiter **URL-Aliase**), standardmäßig weiterleitend; im Code `eZURLAliasML::storePath()` mit der Aktion `eznode:<node id>` |
| Eine Gruppe alter Adressen mit einem Muster | URL-Platzhalter (**Setup > URL Platzhalter**), mit *Umleitende URL* für einen 301 |
| Eine Modulansicht oder eine Adresse Ihrer Wahl | globale URL-Aliase (**Setup > URL-Übersetzer**) |
| Die Aliase aller Knoten nach einem Import neu aufbauen | `./console exp:updateniceurls --update-nodes` |
| Die Alias-Tabellen prüfen | `./console exp:verify_aliases` (`--fix` behebt unbedenkliche Fehler) |
| Titel, Schlüsselwörter, Beschreibung pro Seite; XML-Sitemap | `xrowmetadata` (Datentyp und der Cronjob-Teil `sitemap`) |
| XML-Sitemap | `bcgooglesitemaps` (Cronjob-Teile `generate`, `generatemultilingual`) |
| Defekte Links und Beziehungen nach einem Import | der Link-Checker ([URLs und Aliase](../../anleitungen/urls-und-aliase.md)), `./console exp:contentcheck` |

All diese Werkzeuge sind unter [Links, URL-Aliase, Platzhalter und Suchstatistiken](../../anleitungen/urls-und-aliase.md) beschrieben.

## 4. Ein eigenes Importskript: das Grundgerüst

Wenn die oben genannten Werkzeuge Ihre Quelle nicht lesen können, schreiben Sie ein PHP-Skript, das den Export der
Quelle liest und über die API des Kernels Objekte anlegt. Führen Sie es mit dem Bootstrap von Exponential aus:

```bash
./console exp:ezexec path/to/your_import.php
```

Das folgende Grundgerüst verwendet nur Kernel-Funktionen, die es in 6.0.15 gibt (`kernel/classes/ezcontentfunctions.php`,
`kernel/classes/ezcontentobject.php`, `kernel/classes/datatypes/ezxmltext/`, `kernel/classes/ezurlaliasml.php`).
Es ist ein Ausgangspunkt, kein fertiger Importer: Das Lesen der Quelle, die Zuordnung und die Fehlerbehandlung sind
Ihre Aufgabe.

```php
<?php
// your_import.php: run with ./console exp:ezexec your_import.php
$cli = eZCLI::instance();
$parentNodeId = 2;                 // where the items go
$classIdentifier = 'article';     // the target class
$language = 'eng-GB';             // the target translation

// 1. Read your source export (WXR, JSON, CSV ...) into plain arrays. Your code.
$items = array(); // each: array( 'source_id' => ..., 'title' => ..., 'html' => ..., 'old_path' => ... )

foreach ( $items as $item )
{
    $remoteId = 'src-' . $item['source_id'];          // stable: re-runs update instead of duplicating
    $object = eZContentObject::fetchByRemoteID( $remoteId );

    $attributes = array( 'title' => $item['title'] ); // identifier => fromString() value

    if ( !$object )
    {
        $node = eZContentFunctions::createAndPublishObject( array(
            'parent_node_id'   => $parentNodeId,
            'class_identifier' => $classIdentifier,
            'remote_id'        => $remoteId,
            'language'         => $language,
            'attributes'       => $attributes,
        ), false );                                    // false: no notifications for imported content
        if ( !$node ) { $cli->error( "create failed: $remoteId" ); continue; }
        $object = $node->attribute( 'object' );
    }

    // 2. Rich text: HTML reduced to the simplified input (p, b/strong, i/em, a href, h1-h6, ul/ol/li, table/tr/td)
    //    and parsed into XmlText, as the RSS import does (kernel/private/classes/cronjobs/rssimport.php).
    $parser = new eZSimplifiedXMLInputParser( $object->attribute( 'id' ), false, 0, false );
    $document = $parser->process( str_replace( array( "\r", "\n", "\t" ), array( '', '', ' ' ), $item['html'] ) );
    if ( is_object( $document ) )
    {
        $attributes['body'] = eZXMLTextType::domString( $document );
    }

    eZContentFunctions::updateAndPublishObject( $object, array(
        'language'   => $language,
        'attributes' => $attributes,
    ), false );

    // 3. Keep the old address: a redirecting alias on the node (check what cleanURL() makes of it).
    if ( !empty( $item['old_path'] ) )
    {
        eZURLAliasML::storePath( $item['old_path'], 'eznode:' . $object->attribute( 'main_node_id' ), $language );
    }
    $cli->output( "ok $remoteId" );
}
```

Die `fromString()`-Formate der gängigen Datentypen, so wie der Kernel sie liest:

| Datentyp | Zeichenkette |
|---|---|
| `ezstring`, `eztext`, `ezemail`, `ezcountry` | der Text |
| `ezxmltext` | das gespeicherte XML (HTML vorher umwandeln, wie oben) |
| `ezinteger`, `ezfloat` | die Zahl |
| `ezboolean` | `1` oder `0` |
| `ezdate`, `ezdatetime` | ein Unix-Zeitstempel |
| `ezimage` | `path/to/file.jpg\|alternative text` |
| `ezbinaryfile` | `path/to/file\|original file name` |
| `ezurl` | `https://example.org\|link text` |
| `ezkeyword` | kommagetrennte Schlüsselwörter |
| `ezobjectrelationlist` | Objekt-IDs, getrennt durch `-` |
| `ezselection` | Optionsnamen, getrennt durch `\|` |
| `ezauthor` | Autoren, getrennt durch `&`, jeweils `name\|email\|id` |
| `ezmatrix` | Zeilen getrennt durch `&`, Zellen durch `\|` |
| `ezuser` | `login\|email\|...` (`eZUserType::parseStringRepresentation()`) |

Den Dateipfaden von Bild- und Dateiattributen wird der Parameter `storage_dir` von `createAndPublishObject()` /
`updateAndPublishObject()` vorangestellt, wenn Sie einen angeben (er wird unverändert vorangesetzt, lassen Sie ihn
also auf `/` enden).

Bewährte Vorgehensweisen für das Skript:

- Importieren Sie in **Abhängigkeitsreihenfolge**: zuerst Medien, dann Inhalte, die auf sie verweisen, dann Inhalte,
  die auf Inhalte verweisen; lösen Sie Beziehungen in einem zweiten Durchgang über die Remote-ID auf.
- Führen Sie es als eigenen **Import-Benutzer** mit genau den nötigen Rechten aus; setzen Sie Ersteller/Eigentümer
  anschließend, wenn die Autoren der Quelle wichtig sind ([Eigentümer ändern](../../anleitungen/eigentuemer-aendern.md)).
- Bauen Sie den Suchindex nach dem Import neu auf: `./console exp:updatesearchindex`. Leeren Sie den Inhalts-Cache:
  `./console exp:cache`.
- Protokollieren Sie in eine Datei, und machen Sie jeden Lauf wiederholbar.

## 5. Welches Werkzeug für welche Aufgabe

| Quelle | Erste Wahl | Danach |
|---|---|---|
| Ein anderes Exponential 6 | Pakete, Pakete von xrowextract, `exp:layouts` | eine Datenbankkopie, wenn die ganze Site umzieht |
| Ein älteres Release dieses Kernels | die Kette der Datenbank-Updates ([Kapitel 11](../11-aktualisieren.md)) | Pakete für Teile |
| Eine Tabellenkalkulation oder jedes System, das CSV schreibt | Import mit xrowextract und `--map` | `ezcsvimport` für einfache Klassen |
| Ein System mit XML- oder JSON-Export | in das Format von xrowextract umwandeln, oder ein eigenes Skript | |
| Ein System, das nur eine REST-API hat | ein eigenes Skript, das die API liest und über die Kernel-API schreibt | |
| Eine Mediathek | ZIP-Import von ezmultiupload | xrowextract für die Metadaten |
| Office-Dokumente | ezodf | |
| Ein Feed | RSS-Import | |

Diese Seite ist Teil der Exponential-Dokumentation und freie Dokumentation unter der GNU Free Documentation License
1.3 oder einer späteren Version ([die Lizenz dieser Dokumentation](../../../en/us/README-LICENSE.md)).
