# Lesezeichenordner

Lesezeichen sind keine flache Liste mehr. Ein Benutzer kann sie in einem Baum virtueller Ordner beliebiger Tiefe
ordnen, und jedes Lesezeichen bleibt einen Klick entfernt: Der Baum ist standardmäßig aufgeklappt und wird in der
Seitenleiste, im Auswahldialog und im Online-Editor gezeigt.

## Wo es ist

| Ort | Was Sie bekommen |
|---|---|
| Meine Lesezeichen (`content/bookmark`) | Eine Übersicht, die Ordner mit ihren Anzahlen neben der Liste, die Lesezeichen nach Ordner gruppiert mit ihrer Klasse, Platzierung, letzten Änderung und ob sie verborgen oder verschwunden sind; Suche, fünf Sortierungen und Blättern; Ordner anlegen, umbenennen, verschieben und entfernen (mit der Angabe, was mit dem Inhalt geschieht); ausgewählte Lesezeichen verschieben (auswählen oder auf einen Ordner ziehen), innerhalb eines Ordners ordnen, ausgewählte nach einer Bestätigung entfernen. Siehe die [Anleitung zu Lesezeichen](../../anleitungen/lesezeichen.md) |
| Lesezeichen-Kasten (rechte Spalte) | Der ganze Baum, Ordner auf- und zuklappbar (im Browser gemerkt, mit der Seite geteilt), „Lesezeichen setzen“ mit wählbarem Ordner (standardmäßig oberste Ebene) |
| Auswahldialog | Der Baum der Lesezeichen neben dem Inhalt; ein Container in einem Ordner öffnet sich mit einem Klick |
| Online-Editor, TinyMCE 3 und 8 | Der Reiter Lesezeichen zeigt eine Überschriftenzeile pro Ordner, nach Tiefe eingerückt |
| Entfernte Dienste | `expbookmark::tree`, `rows`, `folders`, `bookmarks`, `createFolder`, `renameFolder`, `deleteFolder`, `moveFolder`, `moveBookmark`, `place`, `addBookmark`, `removeBookmark` |

Designs: admin4 (und admin4l, das dessen Templates verwendet), admin (und admin2, admin3, die darauf zurückgreifen) und
standard.

## Verwenden

Die [Anleitung zu Lesezeichen](../../anleitungen/lesezeichen.md) geht die Seite Schritt für Schritt durch. Kurz:

- **Neuer Ordner**: das Formular *Neuer Ordner* neben der Liste; der angezeigte Ordner ist als Ort vorausgewählt.
- **Lesezeichen verschieben**: Haken Sie sie an und verwenden Sie *Ausgewählte Lesezeichen verschieben nach*, oder ziehen Sie sie auf einen Ordner der Ordnerliste. Ordner werden mit *Verschieben* im Bereich des angezeigten Ordners verschoben; ein Ordner kann nicht in sich selbst oder einen Ordner darunter, daher werden diese nicht angeboten.
- **Einen Ordner entfernen**: *Ordner entfernen* fragt, ob sein Inhalt in den Ordner verschoben wird, in dem er lag (der Standard, kein Lesezeichen wird entfernt), oder mit ihm entfernt wird.
- **Suche, Sortierung, Seiten**: Die Suche umfasst Namen, Klassen, Platzierungen und Ordner; die Sortierungen sind Ihre eigene, Name, zuletzt hinzugefügt, Typ und zuletzt geändert; Lesezeichen bleiben nach Ordner gruppiert.
- Ohne JavaScript funktioniert die Seite genauso; das Skript fügt *Alle auswählen* und das Ziehen hinzu.

## Templates

Alte Templates funktionieren weiter: `fetch( 'content', 'bookmarks' )` gibt wie bisher alle Lesezeichen des Benutzers zurück, flach, die neuesten zuerst.
Neue Fetch-Funktionen:

```
{def $rows    = fetch( 'content', 'bookmark_rows',    hash() )}   {* the tree, flat, in display order *}
{def $folders = fetch( 'content', 'bookmark_folders', hash() )}   {* the folders, flat, with depth *}
```

Eine Zeile hat `type` (`folder` oder `bookmark`), `depth`, `id`, `name`, `path` (Ordnernamen darüber) und bei einem Ordner `parent_id` und `count`
(Lesezeichen darin, Unterordner eingeschlossen), bei einem Lesezeichen `folder_id` und `bookmark` (das `eZContentBrowseBookmark`). `bookmark_rows` nimmt
`folder_id`, um nur den Inhalt eines Ordners aufzulisten. `design:content/bookmark_tree.tpl` gibt einen Baum aus (`mode='page'`, `'sidebar'` oder `'browse'`).

## PHP

`eZContentBrowseBookmarkFolder` (`kernel/classes/ezcontentbrowsebookmarkfolder.php`): `createNew`, `fetch`, `fetchForUser`, `fetchListForUser`,
`fetchChildren`, `fetchTreeForUser` (verschachtelt, zwei Abfragen), `fetchRowsForUser`, `rename`, `moveFolder`, `moveBookmark`, `place`, `reorder`,
`removeFolder( $deleteBookmarks )`, `isInside`, `subtreeIDs`, `handleAction`. `eZContentBrowseBookmark` erhielt `folder_id`, `priority`,
`fetchListForUserInFolder`, `folder()` und einen optionalen Ordner für `createNew( $userID, $nodeID, $name, $folderID )`.

## Online-Editor

`ezoe::bookmarks::offset::limit` ist unverändert. Mit einem dritten Argument `tree` antwortet er mit den Lesezeichen in der Reihenfolge des Baums,
jedes mit `folder_id` und `folder_path`, und mit `folders` (id, name, parent_id, depth, count).

## Tests

`tests/tests/kernel/classes/bookmarks/BookmarkFoldersTest.php` (Modell und Upgrade-SQL), `tests/tests/extension/expservices/users/expBookmarkServicesTest.php`
(Dienste). Beide arbeiten auf der Live-Datenbank (Zeilen namens BMTEST, die wieder entfernt werden) und werden ohne Installation übersprungen.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
