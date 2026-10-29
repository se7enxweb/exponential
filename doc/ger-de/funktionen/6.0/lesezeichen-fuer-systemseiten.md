# Lesezeichen für Systemseiten

Seit Exponential 6.0.15 kann ein Lesezeichen außer auf einen Knoten auch auf eine **Systemseite** zeigen, eine Seite
einer Modulansicht wie `setup/info`, `exphelp/dashboard`, `role/view/3`, `audit/console/(user)/14` oder eine Suche
(`content/search?SearchText=news`). Lesezeichen von Systemseiten haben einen eigenen Namen, liegen in denselben
[Lesezeichen-Ordnern](lesezeichenordner.md) wie Knoten-Lesezeichen und werden mit ihnen angeordnet, verschoben und
entfernt. Sie hängen weder vom Host noch vom Siteaccess ab, werden bei jeder Anzeige auf Zugriff geprüft, sind
ausgegraut und nennen den Grund, wenn sie nicht geöffnet werden können, und lassen sich über `bookmark.ini` erweitern.

[Funktionen](README.md) · Hilfe für Benutzer: [Systemseiten als Lesezeichen](../../hilfe/systemseiten-als-lesezeichen.md) ·
Anleitung: [Lesezeichen](../../anleitungen/lesezeichen.md) · Hinweise zu Upgrade und API:
[Lesezeichen für Systemseiten (Upgrade)](../../bc/6.0/lesezeichen-fuer-systemseiten.md) · Einstellungen:
[bookmark.ini](../../beispiele/ini-einstellungen/bookmark/README.md)

## Wo es zu finden ist

| Ort | Was Sie bekommen |
|---|---|
| Über dem Pfad jeder Modulansicht, in jedem Design der Administration (admin4, admin4l, admin, admin3 und die Designs, die darauf zurückfallen, der Siteaccess editor eingeschlossen) | **Seite merken**: eine Schaltfläche, die an Ort und Stelle ein Formular mit Name (der Seitentitel, änderbar), Ordner und **Lesezeichen hinzufügen** öffnet. Eine gemerkte Seite zeigt **Gemerkt**; das Formular speichert dann Name und Ordner |
| Kasten Lesezeichen (rechte Spalte) | Dieselbe Schaltfläche auf Modulansichten statt des deaktivierten *Lesezeichen setzen*. Der Baum listet Systemseiten mit dem Symbol ihrer Art oder ihres Moduls; eine Seite, die der Benutzer dort nicht öffnen kann, ist ausgegraut, ohne Link, mit dem Grund als Tooltip und für Screenreader |
| Meine Lesezeichen (`content/bookmark`) | Eine Zahl **Systemseiten** und ein Hinweis, wie man eine hinzufügt; eine Karte je Systemseite mit Symbol, Typ (*Systemseite*, *Suche* oder die Bezeichnung der Art einer Erweiterung), Adresse, Modul und Ansicht, **Öffnen**, **Umbenennen** und allem, was eine Knoten-Karte zum Anordnen, Verschieben und Entfernen hat. Eine Seite, die der Benutzer nicht öffnen kann, ist mit **Kein Zugriff** markiert, hat keinen Link und nennt den Grund. Die Suche findet Adressen; die Sortierung *Typ* ordnet Systemseiten nach der Bezeichnung ihrer Art ein |
| Durchsuchen-Dialog, Online-Editor | Nicht aufgeführt: Beide wählen Inhalte aus |
| Frontend (Siteaccesses der Site) | Nicht angeboten: Die Seiten der Besucher sind Inhalte und werden als Knoten gemerkt, und die Site-Designs haben keinen Kopf der Administration |
| Remote-Dienste | `expbookmark::addPageBookmark` (uri, name, folder_id) und `expbookmark::renameBookmark` (id, name); jedes exportierte Lesezeichen hat `kind` (`node` oder `view`) und bei einer Systemseite `uri` und `can_open` |

## Was gespeichert wird

Ein Lesezeichen einer Systemseite ist eine Zeile von `ezcontentbrowsebookmark` mit `node_id = 0`; Name, Ordner und
Priorität sind also die, die jedes Lesezeichen hat. Seine Adresse ist eine Zeile der Tabelle `expbookmark_view`:

| Spalte | Enthält |
|---|---|
| `bookmark_id` | Die ID der Lesezeichen-Zeile (Primärschlüssel) |
| `user_id` | Den Besitzer, wie in der Lesezeichen-Zeile (indiziert) |
| `module_uri` | Die Adresse: Modul, Ansicht und die Parameter der Ansicht, geordnete (`role/view/3`) und ungeordnete (`/(offset)/20`), ohne Host, Siteaccess und Fragment; einen Query-String nur mit den Schlüsseln, die ihre Art behält, sortiert (`content/search?SearchText=news&SectionID=1`) |
| `created` | Wann die Seite gemerkt wurde |

Modul und Ansicht werden bei der Anzeige aus der Adresse gelesen (`expBookmarkView::parts()`); die Adresse ist also das
eine, was zu prüfen, zu vergleichen und zu ändern ist: Ein umbenanntes Modul oder eine umbenannte Ansicht ist ein
`UPDATE expbookmark_view SET module_uri = ...` der Zeilen, die mit dem alten Namen beginnen. Eine Adresse wird je
Benutzer einmal gemerkt: Erneutes Merken ändert den Namen und, wenn einer gewählt ist, den Ordner.

## Regeln

- **Nur interne Modulansichten.** Die Adresse muss mit einem Modul dieser Installation beginnen; ein vorangestellter
  Siteaccess-Name wird entfernt; ein URL-Alias (ein Knoten), eine Adresse mit Schema (`https:`, `javascript:` ...),
  `//host`, Schritte `..` und `.`, Backslashes und Steuerzeichen werden abgelehnt, ebenso alles über 2000 Zeichen.
- **Nie eine Seite eines Moments.** `bookmark.ini [ViewBookmarkSettings] NonBookmarkableViews[]` listet die Ansichten,
  die nur ein abgeschicktes Formular verarbeiten, an- oder abmelden, bestätigen, herunterladen oder bearbeiten
  (`content/action`, `content/edit`, `user/logout`, `role/edit`, `switchlanguage/*` ...), und eine Moduldefinition kann
  dasselbe mit `'bookmarkable' => false` im Eintrag einer Ansicht in `$ViewList` sagen (wie `user/logout`). Eine solche
  Ansicht bekommt kein **Seite merken**, wird beim Abschicken abgelehnt, und ein vorhandenes Lesezeichen von ihr ist
  ausgegraut.
- **Query-Strings nur, wo sie etwas bedeuten.** Eine Modulansicht nimmt ihre Parameter in ihrer Adresse; der
  Query-String wird verworfen, außer den Schlüsseln, die ihre Art behält (`QueryParameters[]`, die gespeicherten
  Suchen): einzelne Werte von höchstens 255 Zeichen, sortiert, kodiert.
- **Zugriff beim Hinzufügen**: Der Benutzer muss die Seite in diesem Moment öffnen können (`expViewAccess::check()`,
  die Regeln einer Anfrage: Siteaccess-Regeln, die Anmelde-Richtlinie, die Richtlinien-Funktionen der Ansicht,
  `PolicyOmitList`, der Inhalt, den eine Ansicht zeigt).
- **Zugriff bei der Anzeige**: bei jeder Anzeige der Lesezeichen neu geprüft, in dem Siteaccess, in dem sie gezeigt
  werden (einmal je Adresse und Anfrage). Eine Seite, die der Benutzer dort nicht öffnen kann, ist ausgegraut und nennt
  den Grund (`expBookmarkView::reasonText()`): in diesem Siteaccess abgeschaltet, keine Rolle erlaubt sie, der
  Siteaccess darf nicht benutzt werden, der Inhalt darf nicht gelesen werden, die Ansicht oder das Modul gibt es nicht
  mehr, die Ansicht darf nicht gemerkt werden. Sie kann trotzdem umbenannt, verschoben und entfernt werden.
- **Unabhängig von Host und Siteaccess**: Nichts von Host oder Siteaccess wird gespeichert, und der Link wird mit dem
  aktuellen Siteaccess gebildet (`ezurl`); dasselbe Lesezeichen öffnet sich also auf jedem Host und in jedem
  Administrations-Siteaccess, der die Seite anbietet.
- **Namen**: standardmäßig der Seitentitel (der Pfad der Seite, der letzte Schritt zuerst, wie ihn der Fenstertitel
  zeigt); Tags und Steuerzeichen werden entfernt, höchstens 255 Zeichen, nie leer. Knoten-Lesezeichen zeigen weiter
  den Namen ihres Knotens und werden nicht umbenannt.
- **Formulare**: Hinzufügen, Umbenennen, Verschieben und Entfernen sind POST-Formulare an `content/bookmark`, durch das
  Formular-Token (ezformtoken) geschützt wie jedes Formular der Administration; ein GET ändert nichts.
- **Entfernen**: Wer ein Lesezeichen entfernt, entfernt seine Adresse; wer einen Ordner samt Inhalt entfernt, entfernt
  die Adressen der Lesezeichen von Systemseiten darin. Das Entfernen eines Knotens (`removeByNodeID()`) berührt
  Lesezeichen von Systemseiten nie.
- **Audit**: Wie bei Knoten-Lesezeichen schreibt das Hinzufügen oder Entfernen kein Audit-Ereignis: Lesezeichen sind
  persönlich.

## Einstellungen

`settings/bookmark.ini` (alle Einstellungen: [bookmark.ini](../../beispiele/ini-einstellungen/bookmark/README.md)):

| Einstellung | Standard | Zweck |
|---|---|---|
| `[ViewBookmarkSettings] NonBookmarkableViews[]` | 40 Ansichten von Aktionen, Anmeldung und Bearbeitungsformularen | Ansichten, die nie gemerkt werden können, `modul/ansicht` oder `modul/*` |
| `[ViewBookmarkSettings] QueryParameters[modul/ansicht]` | `content/search` = `SearchText;SectionID;SubTreeArray` | Die Schlüssel des Query-Strings, die das Lesezeichen einer Ansicht behält |
| `[ViewBookmarkSettings] ModuleIcons[modul]`, `DefaultIcon` | `setup` = `tools`, `role` = `shield-lock`, ... ; `gear` | Das Symbol eines Lesezeichens einer Systemseite nach ihrem Modul (`a4-bi-<name>`) |
| `[BookmarkTypeSettings] TypeList[]`, `TypeClass[art]` | `search` = `expBookmarkTypeSearch` | Die Arten von Lesezeichen, in dieser Reihenfolge gefragt |

Beispiel: den Status-Filter einer Liste Ihrer Erweiterung behalten und ihren Seiten ein Symbol geben:

```ini
<?php /* #?ini charset="utf-8"?
# extension/myreports/settings/bookmark.ini.append.php
[ViewBookmarkSettings]
NonBookmarkableViews[]=myreports/export
QueryParameters[myreports/list]=Status
ModuleIcons[myreports]=bar-chart-line
*/ ?>
```

## Eine Art von Lesezeichen hinzufügen

Eine Art entscheidet für die Modulansichten, die sie nimmt, welche Schlüssel des Query-Strings ihre Lesezeichen
behalten, ihr Symbol und die Typ-Bezeichnung auf Meine Lesezeichen. Gespeicherte Suchen sind die Art, die der Kernel
mitbringt (`expBookmarkTypeSearch`); jede andere Ansicht ist eine *Systemseite* (`expBookmarkTypeSystemPage`). Eine
Erweiterung fügt eine Art mit einer Klasse hinzu, die `expBookmarkType` erweitert:

```php
<?php
// extension/mylayouts/classes/mylayoutbookmarktype.php
class myLayoutBookmarkType extends expBookmarkType
{
    public function identifier() { return 'layout'; }
    public function accepts( $module, $view ) { return $module === 'mylayouts' && $view === 'edit'; }
    public function queryParameters( $module, $view ) { return array(); }
    public function icon( $uri ) { return 'layout-sidebar-inset'; }
    public function label() { return ezpI18n::tr( 'extension/mylayouts', 'Layout' ); }
}
```

```ini
<?php /* #?ini charset="utf-8"?
# extension/mylayouts/settings/bookmark.ini.append.php
[BookmarkTypeSettings]
TypeList[]=layout
TypeClass[layout]=myLayoutBookmarkType
*/ ?>
```

Erzeugen Sie die Autoloads der Erweiterungen neu (`php bin/php/ezpgenerateautoloads.php -e`) und leeren Sie den
INI-Cache. Die Arten werden in der Reihenfolge von `TypeList[]` gefragt; die erste, deren `accepts()` wahr ist,
entscheidet. Eine Art macht nie eine Ansicht merkbar, die `NonBookmarkableViews[]` oder die Moduldefinition
ausschließt, und über den Zugriff entscheidet immer `expViewAccess`. Eine Klasse, die fehlt oder `expBookmarkType`
nicht erweitert, wird mit einer Warnung in der Debug-Ausgabe übersprungen.

## Installieren und aktualisieren

Eine neue Installation bekommt die Tabelle aus `share/db_schema.dba`, auf jeder Datenbank-Engine. Eine Installation
von vor 6.0.15 bekommt sie aus:

- dem SQL-Update ihrer Engine: `update/database/<mysql|postgresql|sqlite>/6.0/dbupdate-6.0.0-6.0.15.sql`
  (Abschnitt *Bookmarks of system pages*, abgesichert, kann also erneut laufen), oder
- auf jeder Engine, Oracle (ezoracle) und MongoDB eingeschlossen, über den Schema-Handler der Engine:

  ```
  php update/common/scripts/6.0/createbookmarkviewtable.php --dry-run -s <Admin-Siteaccess>   # sagt, ob sie fehlt
  php update/common/scripts/6.0/createbookmarkviewtable.php -s <Admin-Siteaccess>
  ```

Solange die Tabelle fehlt, bietet die Administration **Seite merken** nicht an, und Knoten-Lesezeichen funktionieren
genau wie vorher. `settings/bookmark.ini` wird mit dem Kernel ausgeliefert; eine Installation, die einen älteren
Einstellungsbaum ohne sie behalten hat, funktioniert trotzdem: Ausgeschlossen ist dann nur, was Moduldefinitionen
ausschließen, kein Query-String wird behalten, und jede Systemseite hat das Zahnrad-Symbol. Leeren Sie nach dem Update
die Caches (`php bin/php/ezcache.php --clear-all`) und erzeugen Sie die Kernel-Autoloads neu, wenn der Code von Hand
kopiert wurde (`php bin/php/ezpgenerateautoloads.php -k`).

Lesezeichen aus den ersten Builds von 6.0.15 funktionieren weiter: Ihre Adressen hatten nie einen Query-String, und
eine Seite, die jetzt ausgeschlossen ist, ist ausgegraut und nennt den Grund, statt zu scheitern.

## Templates und Stile

| Datei | Teil |
|---|---|
| `design/standard/templates/content/bookmark_view_action.tpl` | Die Schaltfläche und ihr Formular (`mode='toolbar'` oder `'box'`) |
| `design/admin4/templates/pagelayout.tpl`, `design/admin4l/templates/pagelayout.tpl`, `design/admin/templates/pagelayout.tpl`, `design/admin3/templates/pagelayout.tpl` | Binden sie über dem Pfad ein |
| `design/admin4/templates/toolbar/full/admin_bookmarks.tpl` und die Kopie in admin | Der Kasten Lesezeichen: die Schaltfläche auf Modulansichten |
| `design/standard/templates/content/bookmark_tree.tpl` | Der Baum: Zeilen von Systemseiten (`row.kind = 'view'`, `row.icon`, `row.reason_text`) |
| `design/admin4/templates/content/bookmark.tpl` und die Kopie in admin | Die Karten der Systemseiten: Symbol, Typ, Grund, Umbenennen |
| `design/admin4/stylesheets/modules/content.css` und die Kopie in admin | `.a4-content-bookmark-pageaction`, `.a4-content-bookmark-pageform`, `.a4-content-bookmark-kind`, `-uri`, `-rename`, `-viewhint`; nur Tokens, so folgen hell und dunkel dem Theme |
| `design/standard/stylesheets/exp_bookmarks.css` | `.exp-bm-view`, `.exp-bm-kind` im Baum |

Keine Inline-Stile, kein Skript: Das Formular ist ein `<details>`-Element und schickt an `content/bookmark`
(`BookmarkViewAction=add`, `BookmarkViewURI`, `BookmarkViewName`, `BookmarkFolderID`, `RedirectURI`).
