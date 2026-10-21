# Lesezeichenordner: Upgrade und Kompatibilität

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Eine Tabelle `expbookmark_folder` und zwei Spalten `folder_id`, `priority` in `ezcontentbrowsebookmark`. Neue Klasse `eZContentBrowseBookmarkFolder`, die Fetch-Funktionen `content/bookmark_rows` und `content/bookmark_folders`, die expservices-Domain `expbookmark`, der Ordnerbaum auf der Lesezeichenseite, im Kasten Lesezeichen, im Durchsuchen-Dialog und im Editor. |
| Wer betroffen ist | Jede Installation: Die Datenbank muss aktualisiert werden, bevor der Code läuft, weil `eZContentBrowseBookmark` jetzt die neuen Spalten schreibt. |
| Wie Sie es prüfen | Setup, Upgrade-Prüfung, Datenbankkonsistenz; oder `php bin/php/ezexec.php <script that prints the difference> --allow-root-user`. Es darf keinen Unterschied melden. |
| Wie Sie es beheben | Führen Sie das SQL unten für Ihre Datenbank aus und leeren Sie dann alle Caches. |

## Schritte des Upgrades

1. Aktualisieren Sie die Dateien.
2. Führen Sie die Anweisungen des Abschnitts „Bookmark folders“ am Ende von
   `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` aus.
3. Leeren Sie alle Caches (`php bin/php/ezcache.php --clear-all --allow-root-user`), laden Sie PHP neu und führen Sie
   mit Velocity dessen Deploy aus.

MySQL:

```sql
ALTER TABLE ezcontentbrowsebookmark ADD COLUMN folder_id int(11) NOT NULL DEFAULT '0';
ALTER TABLE ezcontentbrowsebookmark ADD COLUMN priority int(11) NOT NULL DEFAULT '0';
ALTER TABLE ezcontentbrowsebookmark ADD INDEX ezcontentbrowsebookmark_folder (user_id, folder_id);
CREATE TABLE expbookmark_folder (
  created int(11) NOT NULL DEFAULT '0',
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL DEFAULT '',
  parent_id int(11) NOT NULL DEFAULT '0',
  priority int(11) NOT NULL DEFAULT '0',
  user_id int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY expbookmark_folder_user ( user_id, parent_id )
) ENGINE=InnoDB;
```

PostgreSQL und SQLite: derselbe Abschnitt in `update/database/postgresql/6.0/` und `update/database/sqlite/6.0/`.
Bei SQLite lässt sich der Abschnitt, nach einer vorherigen Sicherung (`cp -p`), mit einem kleinen Skript anwenden, das
überspringt, was existiert; ein zweiter Lauf des reinen SQL bricht bei der doppelten Spalte ab und ändert nichts.

Neue Installationen erhalten die Tabellen aus `share/db_schema.dba` und `kernel/sql/<engine>/`.

## Nichts zu migrieren

Jedes vorhandene Lesezeichen hat `folder_id` 0 (die oberste Ebene) und `priority` 0. Die Reihenfolge der flachen Liste
ist unverändert.

## Kompatibilität

- `eZContentBrowseBookmark::fetchListForUser()` und `fetch( 'content', 'bookmarks' )` geben alle Lesezeichen des
  Benutzers zurück, flach, die neuesten zuerst, mit Offset und Limit wie bisher. Ein Lesezeichen in einem Ordner ist in
  der Liste enthalten.
- `eZContentBrowseBookmark::createNew( $userID, $nodeID, $name )` funktioniert wie bisher; ein vorhandenes Lesezeichen
  für den Knoten wird ersetzt und behält seinen Ordner. Das neue vierte Argument wählt den Ordner.
- Die Aktion `ActionAddToBookmarks` nimmt ein optionales `BookmarkFolderID` an.
- `ezoe::bookmarks` antwortet wie bisher, sofern sein drittes Argument nicht `tree` ist.
- Templates, die Ordner ignorieren, funktionieren weiter. Overrides von `content/bookmark.tpl`, `content/browse.tpl`
  und `toolbar/full/admin_bookmarks.tpl` zeigen die flache Liste, bis sie aktualisiert werden.
- Das Löschen eines Ordners verschiebt seine Lesezeichen und Unterordner eine Ebene nach oben; kein Lesezeichen wird
  gelöscht, sofern das nicht verlangt wird.

## Datenmodell

`expbookmark_folder`

| Spalte | Typ | Bedeutung |
|---|---|---|
| `id` | int, auto increment | Primärschlüssel |
| `user_id` | int | Besitzer (Inhaltsobjekt-ID des Benutzers) |
| `parent_id` | int, 0 | Elternordner, 0 ist die oberste Ebene |
| `name` | varchar(255) | Name, ohne Tags und Steuerzeichen |
| `priority` | int | Reihenfolge innerhalb des Elternordners, aufsteigend |
| `created` | int | Unix-Zeit |

Index `expbookmark_folder_user (user_id, parent_id)`.

`ezcontentbrowsebookmark` (neue Spalten): `folder_id` int NOT NULL DEFAULT 0 (der Ordner, 0 ist die oberste Ebene) und
`priority` int NOT NULL DEFAULT 0 (die Reihenfolge innerhalb des Ordners, danach die neuesten zuerst); Index
`ezcontentbrowsebookmark_folder (user_id, folder_id)`.

Regeln: Die Tiefe ist unbegrenzt; ein Ordner kann nicht in sich selbst oder unter sich selbst verschoben werden (bei
jedem Verschieben geprüft); ein Ordner oder Lesezeichen eines anderen Benutzers existiert für einen Benutzer nicht (im
Modell und in den Diensten abgelehnt); beschädigte Daten (ein fehlender Elternordner, ein Zyklus) werden auf der
obersten Ebene angezeigt, und nichts geht verloren.
