# Lesezeichen für Systemseiten

Seit Exponential 6.0.15 kann ein Lesezeichen außer auf einen Knoten auch auf eine **Systemseite** zeigen, eine Seite
einer Modulansicht wie `setup/info`, `exphelp/dashboard` oder `audit/console/(user)/14`. Lesezeichen von Systemseiten
haben einen eigenen Namen, liegen in denselben [Lesezeichen-Ordnern](lesezeichenordner.md) wie Knoten-Lesezeichen und
werden mit ihnen angeordnet, verschoben und entfernt.

[Funktionen](README.md) · Hilfe für Benutzer: [Systemseiten als Lesezeichen](../../hilfe/systemseiten-als-lesezeichen.md) ·
Anleitung: [Lesezeichen](../../anleitungen/lesezeichen.md) · Hinweise zu Upgrade und API:
[Lesezeichen für Systemseiten (Upgrade)](../../bc/6.0/lesezeichen-fuer-systemseiten.md)

## Wo es zu finden ist

| Ort | Was Sie bekommen |
|---|---|
| Über dem Pfad jeder Modulansicht (admin4, admin4l, admin und die Designs, die darauf zurückfallen) | **Seite merken**: eine Schaltfläche, die an Ort und Stelle ein Formular mit Name (der Seitentitel, änderbar), Ordner und **Lesezeichen hinzufügen** öffnet. Eine gemerkte Seite zeigt **Gemerkt**; das Formular speichert dann Name und Ordner |
| Kasten Lesezeichen (rechte Spalte) | Dieselbe Schaltfläche auf Modulansichten statt des deaktivierten *Lesezeichen setzen*. Der Baum listet Systemseiten mit einem Zahnrad-Symbol; eine Seite, die der Benutzer nicht mehr öffnen darf, erscheint ausgegraut und ohne Link |
| Meine Lesezeichen (`content/bookmark`) | Eine Zahl **Systemseiten** und ein Hinweis, wie man eine hinzufügt; eine Karte je Systemseite mit Adresse, Modul und Ansicht, **Öffnen**, **Umbenennen** und allem, was eine Knoten-Karte zum Anordnen, Verschieben und Entfernen hat. Eine Seite, die der Benutzer nicht öffnen darf, ist mit **Kein Zugriff** markiert und hat keinen Link. Die Suche findet Adressen; die Sortierung *Typ* ordnet sie unter *Systemseite* ein |
| Durchsuchen-Dialog, Online-Editor | Nicht aufgeführt: Beide wählen Inhalte aus |
| Remote-Dienste | `expbookmark::addPageBookmark` (uri, name, folder_id) und `expbookmark::renameBookmark` (id, name); jedes exportierte Lesezeichen hat `kind` (`node` oder `view`) und bei einer Systemseite `uri` und `can_open` |

## Was gespeichert wird

Ein Lesezeichen einer Systemseite ist eine Zeile von `ezcontentbrowsebookmark` mit `node_id = 0`; Name, Ordner und
Priorität sind also die, die jedes Lesezeichen hat. Seine Adresse ist eine Zeile der neuen Tabelle `expbookmark_view`:

| Spalte | Enthält |
|---|---|
| `bookmark_id` | Die ID der Lesezeichen-Zeile (Primärschlüssel) |
| `user_id` | Den Besitzer, wie in der Lesezeichen-Zeile |
| `module_uri` | Die Adresse: Modul, Ansicht und die Parameter der Ansicht, ohne Siteaccess und ohne Abfrage oder Fragment (`content/bookmark/(folder)/top`) |
| `created` | Wann die Seite gemerkt wurde |

Eine Adresse wird je Benutzer einmal gemerkt: Erneutes Merken ändert den Namen und, wenn einer gewählt ist, den Ordner.

## Regeln

- **Was gemerkt werden kann**: die Seite einer Modulansicht dieser Installation. Die Adresse muss mit einem
  vorhandenen Modul beginnen; ein vorangestellter Siteaccess-Name wird entfernt; ein URL-Alias (ein Knoten), eine
  Adresse einer anderen Website, `..`-Schritte, umgekehrte Schrägstriche und Steuerzeichen werden abgelehnt. Die
  Schaltfläche erscheint nicht auf Knotenseiten, beim Bearbeiten oder Durchsuchen und nicht auf Fehlerseiten.
- **Zugriff beim Hinzufügen**: Der Benutzer muss die Seite in diesem Moment öffnen dürfen (`expViewAccess::check()`,
  die Regeln einer Anfrage: Siteaccess-Regeln, die Login-Richtlinie, die Richtlinienfunktionen der Ansicht,
  `PolicyOmitList`).
- **Zugriff beim Anzeigen**: Wird bei jeder Anzeige der Lesezeichen erneut geprüft (einmal je Adresse und Anfrage).
  Eine Seite, die der Benutzer nicht mehr öffnen darf oder deren Ansicht es nicht mehr gibt, wird ohne Link gezeigt;
  sie kann weiterhin umbenannt, verschoben und entfernt werden.
- **Namen**: standardmäßig der Seitentitel (der Pfad der Seite, der letzte Schritt zuerst, wie ihn der Fenstertitel
  zeigt); Tags und Steuerzeichen werden entfernt, höchstens 255 Zeichen, nie leer. Knoten-Lesezeichen zeigen weiter
  den Namen ihres Knotens und werden nicht umbenannt.
- **Entfernen**: Ein entferntes Lesezeichen nimmt seine Adresse mit; ein Ordner, der mit allem darin entfernt wird,
  nimmt die Adressen der Systemseiten-Lesezeichen darin mit. Das Entfernen eines Knotens (`removeByNodeID()`) berührt
  Lesezeichen von Systemseiten nie.
- **Audit**: Wie bei Knoten-Lesezeichen schreibt das Hinzufügen oder Entfernen kein Audit-Ereignis: Lesezeichen sind
  persönlich.

## Installieren und aktualisieren

Eine neue Installation bekommt die Tabelle aus `share/db_schema.dba`, auf jeder Datenbank. Eine Installation von vor
6.0.15 bekommt sie aus:

- dem SQL-Update ihrer Datenbank: `update/database/<mysql|postgresql|sqlite>/6.0/dbupdate-6.0.0-6.0.15.sql`
  (Abschnitt *Bookmarks of system pages*, abgesichert, kann also erneut laufen), oder
- auf jeder Datenbank, auch Oracle und MongoDB:

  ```
  php update/common/scripts/6.0/createbookmarkviewtable.php --dry-run -s <Admin-Siteaccess>   # sagt, ob sie fehlt
  php update/common/scripts/6.0/createbookmarkviewtable.php -s <Admin-Siteaccess>
  ```

Solange die Tabelle fehlt, bietet die Administration **Seite merken** nicht an, und Knoten-Lesezeichen funktionieren
genau wie bisher. Leeren Sie nach dem Update die Caches (`php bin/php/ezcache.php --clear-all`) und erzeugen Sie die
Autoloads des Kernels neu, wenn der Code von Hand kopiert wurde (`php bin/php/ezpgenerateautoloads.php -k`).

## Templates und Stile

| Datei | Teil |
|---|---|
| `design/standard/templates/content/bookmark_view_action.tpl` | Die Schaltfläche und ihr Formular (`mode='toolbar'` oder `'box'`) |
| `design/admin4/templates/pagelayout.tpl`, `design/admin4l/templates/pagelayout.tpl`, `design/admin/templates/pagelayout.tpl` | Binden es über dem Pfad ein |
| `design/admin4/templates/toolbar/full/admin_bookmarks.tpl` und die Kopie in admin | Der Kasten Lesezeichen: die Schaltfläche auf Modulansichten |
| `design/standard/templates/content/bookmark_tree.tpl` | Der Baum: Zeilen von Systemseiten (`row.kind = 'view'`) |
| `design/admin4/templates/content/bookmark.tpl` und die Kopie in admin | Die Karten der Systemseiten, Umbenennen |
| `design/admin4/stylesheets/modules/content.css` und die Kopie in admin | `.a4-content-bookmark-pageaction`, `.a4-content-bookmark-pageform`, `.a4-content-bookmark-kind`, `-uri`, `-rename`, `-viewhint`; nur Tokens, also folgen Hell und Dunkel dem Thema |
| `design/standard/stylesheets/exp_bookmarks.css` | `.exp-bm-view`, `.exp-bm-kind` im Baum |

Keine Inline-Stile, kein Skript: Das Formular ist ein `<details>`-Element und sendet an `content/bookmark`
(`BookmarkViewAction=add`, `BookmarkViewURI`, `BookmarkViewName`, `BookmarkFolderID`, `RedirectURI`).
