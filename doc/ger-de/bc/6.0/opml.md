# OPML-Exporte

Lesen Sie diese Seite, wenn Sie RSS-Feeds aus Exponential veröffentlichen oder die Templates zum Bearbeiten und
Auflisten von RSS überschreiben. Ein RSS-Export kann jetzt als **OPML-2.0-Dokument** geschrieben werden: eine Liste
anderer Feeds, das Format, das Feedreader importieren, wenn jemand seine Abonnements veröffentlicht. Das Upgrade
braucht ein Datenbank-Update; vorhandene Exporte ändern sich nicht.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neuer Formatwert `OPML` für `ezrss_export.rss_version`, neue Tabelle `ezrss_export_opml_item`, neue Spalte `ezrss_export.opml_head`, neue Einstellung `site.ini [RSSSettings] OPMLMaxOutlines` (Standard `5000`). Die Auswahlliste des Formats heißt *Feed-Format*; die gespeicherten Werte sind unverändert. Importe holen nur `http` und `https`. |
| Wer betroffen ist | Jede Installation, die die Datenbank aktualisiert (neue Tabelle und Spalte). Override-Templates der Bearbeitungsseite für RSS (neue Variablen; `rss_version_array` wird weiterhin gesetzt). Importe von Adressen, die nicht `http` oder `https` sind. |
| Wie Sie es prüfen | `grep -n "ezrss_export_opml_item" update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql` und nach dem Update, dass die Tabelle in Ihrer Datenbank existiert. |
| Wie Sie es beheben | Führen Sie das Datenbank-Update von 6.0.0 auf 6.0.15 für Ihre Engine aus (siehe [Upgrade](#upgrade)). |

## Upgrade

Die neue Tabelle und die neue Spalte stehen im Update-Skript jeder Datenbank-Engine:

```
update/database/mysql/6.0/dbupdate-6.0.0-6.0.15.sql
update/database/postgresql/6.0/dbupdate-6.0.0-6.0.15.sql
update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql
```

Eine Neuinstallation erhält sie aus `kernel/sql/*` und `share/db_schema.dba`. Sonst ist nichts nötig: `rss_version`
speichert weiterhin `1.0`, `2.0` und `ATOM`, und `OPML` ist ein vierter Wert daneben.

## Was ein OPML-Export ist

Ein RSS-Export war schon immer ein Feed von Artikeln, die aus Inhalten gesammelt werden. Ein auf OPML gesetzter
Export hat keine Inhaltsquelle und keine Zuordnung von Klassen. Er hat stattdessen **Outlines**, von denen jede einen
anderen Export dieser Installation, einen Inhaltsknoten oder eine einfache Adresse nennt. Die Adresse jeder Outline
wird beim Schreiben des Dokuments ermittelt, sodass das Umbenennen eines Feeds keinen toten Eintrag hinterlassen kann.

## Einen Export in ein OPML-Dokument verwandeln

`/rss/edit_export/<id>` → **Feed-Format** → `OPML 2.0 (list of feeds)`.

Die Seite zeigt dann die Bedienelemente für OPML anstelle der Inhaltsquellen. Die Auswahlliste des Formats wurde in
diesem Release neu beschriftet: Sie heißt *Feed-Format* statt *RSS Version*, und jede Option sagt, welches Format sie
ist — `RSS 1.0 (RDF)`, `RSS 2.0`, `Atom 1.0`, `OPML 2.0 (list of feeds)`. **Der Wert hinter jeder Option ist
unverändert**, sodass alles, was `rss_version` liest oder schreibt, weiter funktioniert.

Der Feed wird unter `/rss/feed/<access url>` als `text/x-opml` ausgeliefert.


## Datenbank

### `ezrss_export_opml_item` (neu)

Eine Zeile pro Outline. Entwurfs- und gültige Zeilen stehen unter `status` nebeneinander, genau wie bei
`ezrss_export_item`, sodass das Bearbeiten eines OPML-Exports so umkehrbar ist wie das Bearbeiten jedes anderen.

| Spalte | Typ | Bedeutung |
|---|---|---|
| `id` | auto | |
| `status` | int | `0` Entwurf, `1` gültig — Teil des Primärschlüssels |
| `rssexport_id` | int | der OPML-Export, zu dem sie gehört |
| `parent_id` | int | die Outline, in der diese steht, `0` für die oberste Ebene |
| `priority` | int | Position im Dokument, die niedrigste zuerst |
| `target_export_id` | int | der Export, den diese Outline aufführt, `0`, wenn keiner |
| `source_node_id` | int | der Inhaltsknoten, auf den diese Outline zeigt, `0`, wenn keiner |
| `subnodes` | int | die Kinder des Knotens einschließen |
| `outline_type` | varchar(50) | `rss`, `link`, `include`, `group`, `text` |
| `outline_text` | varchar(255) | der `text` der Outline; leer bedeutet, ihn vom Ziel zu übernehmen |
| `title` | varchar(255) | `title`; leer bedeutet, `text` zu verwenden |
| `description` | varchar(255) | `description` |
| `category` | varchar(255) | `category` |
| `language` | varchar(50) | `language` |
| `xml_url` | varchar(255) | Überschreibung; leer bedeutet, sie aus dem Ziel zu ermitteln |
| `html_url` | varchar(255) | Überschreibung |
| `url` | varchar(255) | verwendet von `link` und `include` |
| `is_comment` | int | `isComment` |
| `is_breakpoint` | int | `isBreakpoint` |
| `created` | int | Zeitstempel, geschrieben als `created` |

Primärschlüssel `(id, status)`, Index `ezrss_export_opml_rsseid (rssexport_id)`.

`outline_text` statt `text`, weil `text` in mehr als einem Dialekt ein Typname ist.

### `ezrss_export.opml_head` (neue Spalte)

`longtext` (`text` unter PostgreSQL). Die Kopffelder von OPML als JSON. Eine Spalte statt eines Dutzends: Sie werden
einmal geschrieben, einmal gelesen, nie durchsucht, und eine Installation ohne OPML-Export füllt sie nie aus.

Schlüssel: `ownerName`, `ownerEmail`, `ownerId`, `docs`, `expansionState`, `vertScrollState`, `windowTop`,
`windowLeft`, `windowBottom`, `windowRight`.


## PHP-API

### `eZRSSExportOPMLItem` — `kernel/classes/ezrssexportopmlitem.php`

Ein `eZPersistentObject` über `ezrss_export_opml_item`.

**Konstanten**

| | |
|---|---|
| `MAX_BULK` (250) | Zeilen, die ein Druck auf eine Schaltfläche hinzufügen oder entfernen darf |
| `MAX_TEXT` (255) | Breite der Textspalten |
| `MAX_SHORT` (50) | Breite der kurzen Spalten |
| `MAX_DEPTH` (20) | wie tief Outlines verschachtelt sein dürfen, bevor der Schreiber anhält |
| `ALLOWED_SCHEMES` (`http,https`) | Schemata, die die Adresse einer Outline verwenden darf |

**Zeilen**

- `create( $exportId, $targetExportId = 0, $priority = 0 )` — eine neue Zeile, Status Entwurf.
- `fetch( $id, $asObject = true, $status = STATUS_VALID )`
- `fetchList( $exportId, $status = STATUS_VALID )` — in der Reihenfolge des Dokuments.
- `fetchListCount( $exportId, $status = STATUS_VALID )`
- `fetchTree( $exportId, $status = STATUS_VALID, $limit = false )` — die Zeilen so angeordnet, wie OPML sie
  verschachtelt: `array( array( 'item' => …, 'children' => array( … ) ) )`. Ein verschwundener Elternknoten lässt
  seine Kinder auf der obersten Ebene, statt sie fallen zu lassen; zwei Zeilen, die ineinander stehen, werden nirgends
  geschrieben statt endlos.
- `selectedTargetIDs( $exportId, $status = STATUS_VALID )` — bereits aufgeführte Export-IDs, mit sich selbst als
  Schlüssel.
- `nextPriority( $exportId, $status = STATUS_DRAFT )`
- `addTargets( $exportId, array $targetIDs, $status = STATUS_DRAFT )` — gibt zurück, wie viele hinzugefügt wurden.
  Überspringt, was nicht existiert, was bereits aufgeführt ist, und den Export selbst; hält bei `MAX_BULK` an; prüft
  alles in einer Abfrage.
- `removeItems( $exportId, array $itemIDs )` — auf dieselbe Weise begrenzt.
- `removeByExport( $exportId, $status = false )` — `false` bedeutet jeden Status.
- `copyStatus( $exportId, $fromStatus, $toStatus )` — verwendet, wenn ein Export zum Bearbeiten geöffnet und wenn er
  veröffentlicht wird.

**Eine Zeile lesen**

- `targetExport()` / Attribut `target` — der aufgeführte Export oder `null`.
- `targetGone()` / Attribut `target_gone` — er wurde inzwischen gelöscht.
- `sourceNode()` / Attribut `source_node`
- `sourcePath()` / Attribut `source_path`
- `outline( $baseURL = '' )` / Attribut `outline` — die Zeile, wie OPML sie ausdrückt, oder `null`, wenn es nichts
  Schreibenswertes gibt. Überschreibungen gewinnen; was leer bleibt, kommt vom Ziel.

**Werte**

- `safeText( $value, $max = MAX_TEXT )` — entfernt Steuerzeichen, die XML nicht tragen kann, trimmt, kürzt auf `$max`
  Zeichen, ohne eines zu zerteilen. Alles, was keine Zeichenkette ist oder kein gültiges UTF-8, kommt leer zurück.
- `safeURL( $value, $max = MAX_TEXT )` — `safeText`, dann überleben nur `ALLOWED_SCHEMES`, protokollrelative und
  seitenrelative Adressen.
- `siteOf( $url )` — der Teil der Website, mit einem eventuellen `/rss/feed/…` wieder entfernt.
- `absolute( $baseURL, $path )`
- `outlineTypes()` — Typ ⇒ das Wort dafür in der Oberfläche.
- `opmlVersionOf( $rssVersion )` — `1.0` ⇒ `RSS1`, `2.0` ⇒ `RSS2`, `ATOM` ⇒ `ATOM`.

### `eZRSSExport` — Ergänzungen

- `isOPML()` / Attribut `is_opml`
- `opmlItemList()` / Attribut `opml_item_list`
- `opmlHead()` / Attribut `opml_head_data` — die Kopffelder als Hash, mit Standardwerten. Eine Spalte voller Unsinn
  wird gelesen, als sei nichts ausgefüllt.
- `setOPMLHead( array $head )` — behält nur die Schlüssel, die OPML definiert, und nur Werte, die zu ihnen passen:
  `docs` muss eine Adresse sein, `ownerEmail` eine E-Mail-Adresse, die Fenster- und Scrollfelder Zahlen oder Listen von
  Zahlen.
- `generateOPML()` — das Dokument, mit DOM gebaut.
- `emptyOPML( $title = '' )` — ein gültiges Dokument als Rückfall.
- `opmlDate( $timestamp )` — RFC 822, wie OPML es verlangt.
- `opmlMaxOutlines()` — `[RSSSettings] OPMLMaxOutlines`, Standard 5000, nie über 50000.
- `formatLabels()` / `formatLabel( $version )` — die Wörter für einen gespeicherten Formatwert. Ein Wert, für den
  niemand einen Namen hat, wird so angezeigt, wie er steht.
- `fetchList( $asObject = true, $offset = false, $limit = false, $sorts = null )` — nimmt jetzt einen Ausschnitt und
  eine Reihenfolge an; ohne Argumente gibt es weiterhin alles zurück.
- `fetchListCount()`
- `sortableFields()` — die einzigen Spalten, die eine Sortierklausel erreichen dürfen.
- `fetchBrowserList( $search, $offset, $limit, $sorts, $excludeID )` / `fetchBrowserListCount( $search, $excludeID )`
  — die Seite des Feed-Browsers. `$search` sucht in Name, Adresse und Beschreibung, wobei seine eigenen Platzhalter
  entschärft werden.

### `eZRSSListPager` — `kernel/rss/ezrsslistpager.php`

Gemeinsam genutzt von `/rss/list` und vom Browser innerhalb der Bearbeitungsseite.

- `limits()` — `array( 25, 50, 250 )`
- `limit( $requested, $remembered = false )`
- `offset( $offset, $limit, $count )`
- `data( $count, $limit, $offset, $offsetName, $suffix, $window = 9 )`
- `sort( $field, $direction, array $allowed, $default )` — fügt immer `id` als Entscheidung bei Gleichstand hinzu,
  sofern nicht bereits nach `id` sortiert wird.
- `suffix( array $parameters )` / `suffixExcept( array $state, array $names )`

### `eZRSSImport` — Ergänzungen

- `fetchableURL( $url )` — die zu holende Adresse oder `false`.
- `isFetchableURL( $url )` — dasselbe als Boolescher Wert.


## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `site.ini` | `[RSSSettings]` | `AvailableVersionList[]` | fügt `OPML` hinzu | global oder Siteaccess |
| `site.ini` | `[RSSSettings]` | `OPMLMaxOutlines` | `5000` (nie über `50000`) | global oder Siteaccess |
| `site.ini` | `[RSSSettings]` | `NumberOfObjectsList[]` | fügt `100`, `250`, `350`, `500`, `1000` hinzu | global oder Siteaccess |

Die ausgelieferten Zeilen:

```ini
AvailableVersionList[]=OPML

# The most outlines one OPML document will carry. Bounded at 50000 whatever
# this says.
OPMLMaxOutlines=5000

NumberOfObjectsList[]=100
NumberOfObjectsList[]=250
NumberOfObjectsList[]=350
NumberOfObjectsList[]=500
NumberOfObjectsList[]=1000
```


## Templates

| Datei | |
|---|---|
| `design/admin/templates/rss/edit_export_opml.tpl` | die OPML-Hälfte der Bearbeitungsseite |
| `design/standard/templates/rss/sortbutton.tpl` | eine sortierbare Überschrift innerhalb eines Formulars |
| `design/standard/templates/rss/pagination.tpl` | die Seitennavigation von `/rss/list` |
| `design/<design>/templates/parts/sortheader.tpl` (eingebunden als `design:parts/sortheader.tpl` vom `rss/list.tpl` der Administration) | eine sortierbare Überschrift als Link |

Variablen, die die Bearbeitungsview für OPML setzt: `rss_is_opml`, `opml_head`, `opml_items`, `opml_groups`,
`opml_outline_types`, `opml_browser_list`, `opml_browser_pager`, `opml_browser_search`, `opml_browser_limits`,
`opml_selected_ids`, `rss_version_options`.

`rss_version_array` wird weiterhin gesetzt, für Override-Templates, die es verwenden.

### Warum der Browser Schaltflächen verwendet

Der Feed-Browser sitzt innerhalb des Bearbeitungsformulars. Ein Link würde die Seite verlassen und alles mitnehmen, was
eingegeben wurde, daher ist jedes Bedienelement — Suche, Seitengröße, Spaltenüberschriften, Seitenzahlen — eine
Absende-Schaltfläche, die zuerst den Entwurf schreibt und dann handelt. Die Seite braucht kein JavaScript; das
vorhandene Skript vergrößert nur die Klickfläche und lässt die Eingabetaste im Suchfeld suchen statt speichern.


## Sicherheit

Tests: `tests/tests/kernel/classes/security/eZRSSSecurityTest.php`.

| ID | |
|---|---|
| **RSS-01** | Die Adresse einer Outline darf nur eine sein, der ein Leser folgen sollte. Ein OPML-Dokument wird von der Software anderer verarbeitet, die dem folgt, was sie findet. `javascript:`, `data:`, `vbscript:`, `file:`, `php://`, `gopher:` und alles andere werden entfernt, auch wenn sie mit einem Zeilenumbruch oder Tabulator aufgeteilt (`java\nscript:`) oder hinter führendem Leerraum versteckt sind. Geprüft beim Speichern **und** erneut beim Schreiben. |
| **RSS-02** | Zeichen, die kein XML-Dokument tragen kann, erreichen nie eines. Ein vor Jahren gespeichertes Steuerzeichen würde sonst unter einer öffentlichen Adresse ein Dokument erzeugen, das nichts parsen kann. Text wird über `createTextNode` geschrieben, sodass Markup in einem Titel escapt statt eingeschleust wird. |
| **RSS-03** | Eine Sortierspalte wird aus `sortableFields()` gewählt, nie aus der Anfrage übernommen, und eine Richtung ist eines von zwei Wörtern. Jede Sortierung trägt `id` als Entscheidung bei Gleichstand, sodass das Blättern durch eine Spalte voller gleicher Werte keine Zeile doppelt zeigen oder auslassen kann. |
| **RSS-04** | Eine Seitengröße ist eine aus `limits()`. Eine Anfrage, die ihre eigene Größe nennen könnte, könnte jede Zeile anfordern, und das ist ein Weg, den Server von einer Adresse aus zu erschöpfen. |
| **RSS-05** | Ein Offset wird in die Liste zurück und auf eine Seitengrenze gezogen. |
| **RSS-06** | Ein Import holt nur `http` und `https` und holt **genau die Zeichenkette, die geprüft wurde**. curl öffnet `file://` und folgt Weiterleitungen, sodass eine Feed-Adresse sonst ein Weg ist, die Festplatte zu lesen oder in das Netzwerk zu gelangen, in dem der Server steht. Ein Null-Byte wird abgelehnt, wo immer es steht. Importiertes XML wird mit `LIBXML_NONET`, `resolveExternals = false` und `substituteEntities = false` geparst, sodass ein Feed, der eine DTD deklariert, keine lokale Datei lesen und den Server nichts holen lassen kann. |
| **RSS-07** | Die Auswahlliste des Formats wurde neu beschriftet; die gespeicherten Werte haben sich nicht geändert. |

Außerdem:

- Jeder POST, der diese Views erreicht, ist bereits durch `ezformtoken` abgedeckt, das bei jedem POST eines
  angemeldeten Benutzers ein Token pro Sitzung prüft und das Feld in jedes Formular einfügt. Die Bedienelemente für
  OPML erben das.
- Ein Feed, der **nicht aktiv** ist, wird in einem Dokument nicht aufgeführt. Er kann nicht geholt werden; ihn
  aufzuführen gäbe einem Leser eine tote Adresse und würde den Namen eines Feeds veröffentlichen, den jemand bewusst
  außer Betrieb genommen hat.
- Ein Export kann sich nicht selbst aufführen.


## Nicht scheitern

| | |
|---|---|
| Öffentliche Adresse | `/rss/feed/…` fängt alles ab, was `generateOPML` werfen kann, und fällt auf `emptyOPML()` zurück. Eine Version, die niemand schreibt, oder ein Dokument, das sich nicht erzeugen ließ, antwortet mit einem Fehler statt mit einem leeren Body und einer 200. |
| Leerer Export | Ein Export ohne Outlines erzeugt trotzdem ein gültiges Dokument — OPML verlangt mindestens eine Outline im Body. |
| Fehlender Entwurf | Der Bearbeitungsentwurf kann zwischen dem Zeichnen der Seite und dem Drücken auf Speichern durch einen Timeout eingesammelt oder aus einem anderen Fenster entfernt werden. Die veröffentlichte Zeile wird wieder aufgenommen, sodass das Speichern ankommt; ein Export, der wirklich weg ist, wird gemeldet. *(Vorher war das ein fataler Fehler: `Call to a member function setAttribute() on null`.)* |
| Abbrechen einer Bearbeitung | Das Entfernen eines Entwurfs entfernt nur die eigenen Outlines des Entwurfs. *(Vorher leerte es das veröffentlichte Dokument.)* |
| Lange Werte | Text wird vor dem Speichern auf die Spaltenbreite gekürzt, nicht von der Datenbank — ein strenger Server lehnt die Zeile ab, statt sie zu kürzen, und das Speichern schlüge fehl, ohne etwas vorzuweisen. |
| Massenaktionen | Hinzufügen und Entfernen halten bei `MAX_BULK` an; die Seite bietet nie mehr an, daher kam eine längere Liste nicht von der Seite. |
| Verschachtelung | Der Schreiber hält bei `MAX_DEPTH` an. Zwei Outlines, die ineinander stehen, werden nirgends geschrieben, statt ihnen zu folgen. |
| Größe | `opmlMaxOutlines()` begrenzt, wie viel eines Dokuments auf einmal gebaut wird. |
| Unsinn in der Datenbank | Eine Kopfspalte, die kein JSON ist, wird gelesen, als sei nichts ausgefüllt. Eine Zeile, die auf einen gelöschten Feed zeigt, wird weggelassen. |


## Testen

**Unit- und Sicherheitstests, ohne Datenbank:**

```sh
php vendor/bin/phpunit --testsuite security     --filter eZRSSSecurityTest
php vendor/bin/phpunit --testsuite kernel-classes --filter eZRSSExportOPMLItemTest
```

**Funktional, gegen eine laufende Installation.** Browser- und Kommandozeilenprüfungen für diese Views werden als
private Arbeitsskripte gehalten und nicht mitgeliefert; die Schritte von Hand unten sind die unterstützte
Entsprechung, und die Unit-Tests oben brauchen keine Datenbank. Legen Sie Beispiel-Feeds in einer Kopie der
Installation an, nicht in einer, in der gerade Menschen bearbeiten: Ein zurückgelassener Entwurf sperrt die
Bearbeitungsseite eines Exports, bis der Timeout abläuft.

**Von Hand:**

1. `/rss/edit_export/<id>` → **Feed-Format** → `OPML 2.0` → **OK**.
2. Öffnen Sie den Export erneut. Durchsuchen Sie den Browser, ändern Sie die Seitengröße, sortieren Sie eine Spalte,
   kreuzen Sie einige Feeds an, **Ausgewählte Feeds hinzufügen**.
3. **Gruppe hinzufügen**, legen Sie Feeds mit dem Menü *In* hinein, **OK**.
4. Holen Sie `/rss/feed/<access url>` und prüfen Sie es mit einem OPML-Validator.

Führen Sie `php vendor/bin/phpunit --list-tests --testsuite security` aus, um die Tests zu sehen, ohne sie auszuführen
(für diese Seite nicht ausgeführt; führen Sie sie in einer Kopie der Installation aus).

## Verwandte Seiten

- [RSS: Feeds für Apple Podcasts, eine Feed-Liste mit Seiten und sicherere Exporte](../../funktionen/6.0/rss-podcast-und-feed-liste.md)
- [Spezifikation der Syndication](../../spezifikationen/6.0/syndikation.md)
- [Aufräumen von RSS-Importen](cleanuprss.md)
- [Der Tab Orte: Blättern und Sortieren](tab-orte-blaettern-und-sortieren.md) (teilt die sortierbare Überschrift)
- [September 2026, erste Hälfte: 14. September](../../geschichte/2026/2026-09a.md#14-september-pdf-rss-und-die-rad-werkzeuge)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
