# Lesezeichen für Systemseiten: Hinweise zu Upgrade und Entwicklung

Exponential 6.0.15 lässt ein Lesezeichen außer auf einen Knoten auch auf die Seite einer Modulansicht (eine
„Systemseite“) zeigen. Diese Seite listet, was sich für Code, Templates, Extensions und Datenbanken ändert. Die
Funktion selbst: [Lesezeichen für Systemseiten](../../funktionen/6.0/lesezeichen-fuer-systemseiten.md); für Benutzer:
[Systemseiten als Lesezeichen](../../hilfe/systemseiten-als-lesezeichen.md).

## Datenbank

- Neue Tabelle `expbookmark_view` (`bookmark_id` Primärschlüssel, `user_id` mit Index, `module_uri` longtext,
  `created`), in `share/db_schema.dba`, den Kernel-Schemas von MySQL, PostgreSQL und SQLite und den Update-Dateien
  6.0.0 bis 6.0.15.
- Neues Update-Skript `update/common/scripts/6.0/createbookmarkviewtable.php` (`--dry-run`), für jede Datenbank über
  ihren Schema-Handler (`expBookmarkView::install()`).
- `ezcontentbrowsebookmark` bleibt unverändert. Ein Lesezeichen einer Systemseite ist eine Zeile mit `node_id = 0`.
- Nichts wird migriert; vorhandene Lesezeichen bleiben Knoten-Lesezeichen.
- Die Adresse wird auch zerlegt gespeichert: `module_name` und `view_name` (varchar 100, Vorgabe `''`) und
  `view_parameters` (longtext, JSON aus `ordered`, `unordered` und `query`), mit dem Index `expbookmark_view_module`
  (`module_name`, `view_name`). Eine neue Tabelle hat sie. Eine Tabelle aus einem frühen Build von 6.0.15 bekommt sie
  von den Update-Schritten `6.0.15-bookmark-view-columns` (Stufe database, jede Engine) und `6.0.15-bookmark-view-fill`
  (Stufe scripts: die Zeilen aus ihrer Adresse gefüllt), die `./console exp:upgrade --apply` und
  `createbookmarkviewtable.php` ausführen; die Update-Dateien von MySQL und PostgreSQL fügen Spalten und Index ebenfalls
  hinzu (abgesichert), die von SQLite kann das nicht. Bis dahin lässt `expBookmarkView::definition()` die Spalten weg
  (`hasParsedColumns()`); die Reihenfolge von Code und Update spielt also keine Rolle.

## Code, der Lesezeichen liest

| Vorher | Jetzt |
|---|---|
| `eZContentBrowseBookmark::fetchListForUser( $user, $offset, $limit )` lieferte jedes Lesezeichen | Liefert nur die Lesezeichen von **Knoten** (die Liste des Durchsuchen-Dialogs und von `fetch( 'content', 'bookmarks' )`, wie sie immer gemeint war). Ein viertes Argument `false` liefert beide Arten |
| `$bookmark.node` immer ein Knoten oder null bei einem entfernten Knoten | Auch null bei einem Lesezeichen einer Systemseite (`node_id = 0`); `fetchNode()` fragt Knoten 0 nicht ab |
| — | Neue Funktionsattribute `kind` (`'node'` oder `'view'`) und `module_uri` (`''` bei einem Knoten) |
| `eZContentBrowseBookmark::remove()` | Entfernt auch die Adresse eines Systemseiten-Lesezeichens |
| `eZContentBrowseBookmark::removeByNodeID( 0 )` hätte alle Systemseiten-Lesezeichen entfernt | Tut bei 0 oder weniger nichts |
| `fetch( 'content', 'bookmark_rows' )` | Jede Lesezeichen-Zeile hat `kind`; eine Systemseiten-Zeile außerdem `uri`, `module`, `view`, `can_open` (`expBookmarkView::decorateRows()`) |
| `eZContentBrowseBookmarkFolder::fetchRowsForUser()`, `fetchTreeForUser()` | Unverändert: beide Arten, ohne die Ergänzung |

Templates, die Lesezeichen-Zeilen selbst zeichnen, sollten `$row.bookmark.node` prüfen (wie `bookmark_tree.tpl` es
immer tat) oder `$row.kind`; ein Template, das annahm, jedes Lesezeichen habe einen Knoten, bekommt aus `bookmark_rows`
null für Systemseiten, `fetch( 'content', 'bookmarks' )` liefert aber weiterhin nur Knoten.

`expBookmarkPage::items()` und `item()` nehmen ein drittes bzw. viertes Argument, die Bezeichnung von Systemseiten
(Standard `'System page'`); Einträge haben `kind` und `uri` (Systemseiten auch `module`, `view`), und `summary()` hat
`views`.

## Neue API

| Wo | Was |
|---|---|
| `expBookmarkView` (`kernel/classes/expbookmarkview.php`) | Das persistente Objekt von `expbookmark_view` und die Regeln: `isAvailable()`, `exists()`, `install()`, `normalizeURI()`, `moduleURI()`, `withRequestParameters()`, `defaultName()`, `urisForUser()`, `findForUser()`, `access()`, `decorateRows()`, `createNew()`, `rename()`, `removeForBookmarks()`, `handleAction()`, `pageState()` |
| `fetch( 'content', 'view_bookmark', hash( 'uri', $requested_uri_string, 'path', $module_result.path ) )` | `available`, `uri`, `name`, `bookmark`, `folder_id` für die gezeigte Seite |
| POST an `content/bookmark` | `BookmarkViewAction=add` (`BookmarkViewURI`, `BookmarkViewName`, `BookmarkFolderID`, optional `RedirectURI` zurück zu einer Seite der Website) und `BookmarkViewAction=rename` (`BookmarkID`, `BookmarkName`); die Karten der Seite senden `BookmarkRenameButton` mit `BookmarkRenameName[<id>]` |
| expservices `expbookmark` | `addPageBookmark` (POST `uri`, `name`, `folder_id`; 422 keine Modulansicht, 403 nicht erlaubt, 409 vor dem Update), `renameBookmark` (POST `id`, `name`; 409 bei einem Knoten-Lesezeichen); exportierte Lesezeichen tragen `kind` und bei Systemseiten `uri`, `can_open`; `bookmarks` (flach) listet beide Arten |
| ezoe | Die Lesezeichenliste des Online-Editors zählt und listet nur Knoten-Lesezeichen |

Beispiel, ein Lesezeichen aus Code:

```php
$uri = expBookmarkView::moduleURI( 'setup/systemupgrade' );        // false, wenn es keine Modulansicht ist
$check = expBookmarkView::access( $uri );                            // der Zugriff des aktuellen Benutzers, wie bei einer Anfrage
if ( $uri !== false && $check['result'] )
    expBookmarkView::createNew( eZUser::currentUserID(), $uri, 'Upgrade-Prüfung', $folderID );
```

## Arten, Einstellungen und Gründe

Hinzugekommen mit den gespeicherten Suchen und den ausgegrauten Lesezeichen:

| Wo | Was |
|---|---|
| `settings/bookmark.ini` | Neu: `[ViewBookmarkSettings] NonBookmarkableViews[]`, `QueryParameters[]`, `ModuleIcons[]`, `DefaultIcon`; `[BookmarkTypeSettings] TypeList[]`, `TypeClass[]` |
| Moduldefinitionen | Ein Eintrag einer Ansicht in `$ViewList` kann `'bookmarkable' => false` tragen (`user/logout` tut es) |
| `expBookmarkType` (abstrakt), `expBookmarkTypeSystemPage`, `expBookmarkTypeSearch` | Die Arten: `identifier()`, `accepts( $module, $view )`, `queryParameters()`, `icon( $uri )`, `label()` |
| `expBookmarkView` | Neu: `bookmarkable( $uri )` (`result`, `reason`), `queryOf()`, `pathOf()`, `canonicalQuery( $module, $view, $query )`, `configuredQueryParameters()`, `moduleIcon()`, `types()`, `typeFor()`, `reasonText( $reason )`. `moduleURI()` behält die Schlüssel des Query-Strings der Art der Ansicht (vorher verwarf es jeden Query-String); `parts()` übergeht den Query-String; `pageState()` nimmt den Query-String der Anfrage als drittes Argument (Standard `$_SERVER['QUERY_STRING']`) und ist auf einer Ansicht, die nicht gemerkt werden kann, nicht verfügbar; `handleAction()` lehnt eine solche Ansicht ab |
| `expBookmarkView::decorateRows()` | Eine Zeile einer Systemseite hat auch `reason`, `reason_text`, `bookmark_type`, `type_label` und `icon`; eine Ansicht, die nicht mehr gemerkt werden darf, hat `can_open` falsch mit dem Grund `not-bookmarkable`. Der `type` der Zeile bleibt `bookmark` |
| `expBookmarkPage::viewItem()` | Einträge haben auch `icon`, `reason` und `reason_text`; der Typ (`class_name`) ist die Bezeichnung der Art der Zeile, wenn sie eine hat |
| Templates | `bookmark_tree.tpl` und die Karten von `bookmark.tpl` zeichnen `a4-bi-<icon>` statt des Zahnrads und zeigen `reason_text`; `design/admin3/templates/pagelayout.tpl` bindet die Schaltfläche auch ein |

Eine gespeicherte Adresse kann jetzt auf einen Query-String enden (`content/search?SearchText=news`); Code, der
`module_uri` direkt liest und an `/` trennt, sollte `expBookmarkView::parts()` oder `pathOf()` verwenden oder
`module_name` und `view_name` lesen.

## Zerlegte Adresse und Umbenennen

| Wo | Was |
|---|---|
| `expBookmarkView::parse( $uri )` | `module`, `view`, `ordered` (Liste), `unordered` und `query` (Name => Wert) einer Adresse; ohne Datenbank und ohne Moduldefinition |
| `expBookmarkView::parsedFields( $uri )` | Die Werte von `module_name`, `view_name` und `view_parameters`; `createNew()` schreibt sie |
| `expBookmarkView::definitionWithAllColumns()`, `hasParsedColumns( $refresh )`, `PARSED_COLUMNS` | Die vollständige Definition; ob die Tabelle die Spalten hat (einmal je Prozess den Katalog gefragt); `definition()` lässt sie weg, solange sie fehlen |
| `expBookmarkView::renamedURI( $uri, $from, $to )`, `renameModuleView( $from, $to, $apply )` | Eine Adresse mit umbenanntem Modul oder umbenannter Ansicht (`'role'` zu `'roles'`, `'role/view'` zu `'roles/show'`), Parameter und Query-String bleiben; die Lesezeichen aller Benutzer verschoben, `$apply = false` meldet nur |
| `expBookmarkView::staleParsedRows()`, `fillParsedColumns()` | Die Zeilen, deren Spalten nicht zu ihrer Adresse passen, und deren Füllen |
| `expUpgradeStepBookmarkViewColumns`, `expUpgradeStepBookmarkViewFill` | Die Update-Schritte `6.0.15-bookmark-view-columns` und `6.0.15-bookmark-view-fill` (`settings/upgrade.ini`) |
| `update/common/scripts/6.0/createbookmarkviewtable.php` | Führt beide Schritte nach dem Anlegen der Tabelle aus; `--rename=<alt>=<neu>` verschiebt Lesezeichen, mit `--dry-run` sagt es nur, was sich ändern würde |

## Tests

- `tests/tests/kernel/classes/bookmarks/expBookmarkViewTest.php`: Adressen, Query-Strings, Namen, Einträge, Suche und Sortierung, die ausgeschlossenen Ansichten, die Arten (auch die einer Erweiterung), die Symbole und die Gründe (ohne Datenbank)
- `tests/tests/kernel/classes/bookmarks/BookmarkViewSchemaSqlTest.php`: die Tabelle überall, wo sie steht; das SQLite-Update zweimal; eine frühe Tabelle bleibt den Update-Schritten überlassen; die Schritte in `upgrade.ini`
- `tests/tests/kernel/classes/bookmarks/BookmarkViewParsedColumnsTest.php`: auf einer SQLite-Datenbank im Speicher funktioniert eine frühe Tabelle weiter, die Schritte fügen die zerlegten Spalten hinzu und füllen sie, ein Modul und eine Ansicht werden umbenannt
- `tests/tests/kernel/classes/bookmarks/BookmarkViewsTest.php`: das Modell auf einer laufenden Installation (ohne eine oder ohne die Tabelle übersprungen)
