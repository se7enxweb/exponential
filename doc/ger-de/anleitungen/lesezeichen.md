# Lesezeichen: den Weg zurück finden, in Ordnern

Diese Anleitung erklärt die Seite **Meine Lesezeichen** der Administration (`/content/bookmark`, Dashboard > Meine
Lesezeichen): was ein Lesezeichen ist, die Übersicht, die Ordner, das Finden, Sortieren und Blättern von Lesezeichen,
das Verschieben zwischen Ordnern, das Umbenennen und Entfernen von Ordnern und was zu tun ist, wenn sich etwas nicht
wie erwartet verhält.

Sie richtet sich an Redakteure und Administratoren. Jede Seite, jedes Feld und jede Regel unten wurde am 6. Oktober
2026 am Code dieses Repositorys geprüft; die Dateien sind unter [Quellen](#quellen) genannt.

[Anleitungen](README.md) · Siehe auch: [Lesezeichen-Ordner (Funktion)](../../en/us/features/6.0/bookmark-folders.md) ·
[Lesezeichen-Ordner (Upgrade)](../../en/us/bc/6.0/bookmark-folders.md) · [Blättern in Listen](../../en/us/features/6.0/admin-list-paging.md)

## Kurz gesagt

- Ein **Lesezeichen** verweist auf einen Eintrag des Inhaltsbaums. Es ist persönlich: Niemand sonst sieht Ihre
  Lesezeichen, und das Entfernen eines Lesezeichens ändert nie den Eintrag.
- Lesezeichen können in **Ordnern** beliebiger Tiefe liegen. Die Seite, der Kasten Lesezeichen am Rand jeder
  Admin-Seite und der Auswahldialog zeigen dieselben Ordner.
- Die Seite zeigt eine Übersicht, die Ordner mit ihren Zählern neben der Liste und eine Karte pro Lesezeichen,
  **nach Ordnern gruppiert**, mit seinem Typ, seiner Lage im Baum, dem Zeitpunkt der letzten Änderung und ob es
  verborgen oder nicht mehr vorhanden ist.
- Suche, Reihenfolge und Blättern bleiben in der Adresse erhalten, sodass ein Link auf die Seite dieselbe Liste zeigt.
- In **Ihre Reihenfolge** ordnen Sie Lesezeichen und Ordner an, indem Sie sie an ihrem Griff ziehen, mit den
  Pfeiltasten auf dem Griff, mit Nach oben und Nach unten oder mit einem Positionsfeld, das auch seitenübergreifend
  funktioniert.
- Jede Aktion funktioniert ohne JavaScript. Mit JavaScript können Sie außerdem **Alle auf dieser Seite auswählen**
  markieren und Lesezeichen auf einen Ordner ziehen.

## 1. Die Seite

Öffnen Sie **Dashboard > Meine Lesezeichen**. Von oben nach unten:

| Teil | Was er zeigt |
|---|---|
| Zahlen | Ihre Lesezeichen, Ihre Ordner, die Lesezeichen in keinem Ordner, die, deren Eintrag verborgen ist, und die, die **nicht mehr verfügbar** sind (rot, wenn es welche gibt). Lesezeichen und In keinem Ordner sind Links auf diese Listen |
| Ordner | Alle Lesezeichen, In keinem Ordner, dann jeder Ordner nach Tiefe eingerückt, jeweils mit der Zahl der Lesezeichen darin und in den Ordnern darunter. Der angezeigte Ordner ist markiert |
| Neuer Ordner | Ein Name und der Ordner, in den er kommt (der angezeigte Ordner ist vorausgewählt) |
| Der angezeigte Ordner | Wenn Sie einen Ordner öffnen: sein Pfad, wie viele Lesezeichen er direkt und mit seinen Unterordnern enthält, wie viele Ordner darunter liegen, sowie Umbenennen, Verschieben und Ordner entfernen (Abschnitt 4) |
| Lesezeichen finden, Reihenfolge | Die Suche und die fünf Reihenfolgen (Abschnitt 2) |
| Die Liste | Eine Karte pro Lesezeichen, unter einer Überschrift pro Ordner mit seiner Größe; „Fortsetzung von der vorigen Seite“, wenn ein Ordner über zwei Seiten reicht |
| Pro Seite und Seiten | 25, 50 oder 100 pro Seite und die Seitennavigation |
| Die Leiste unter der Liste | Die ausgewählten Lesezeichen in einen Ordner verschieben, Ausgewählte entfernen (fragt vorher nach) |

Jede Karte zeigt:

- ein Kästchen zum Auswählen, das Symbol der Klasse des Eintrags, seinen Namen (ein Link auf den Eintrag) und seine
  Klasse;
- **Verborgen**, wenn der Eintrag verborgen ist, **Versteckt durch übergeordneten Eintrag**, wenn ein Eintrag darüber
  verborgen ist: Besucher der Site sehen ihn nicht;
- **Nicht gefunden**, wenn der Eintrag entfernt wurde, im Papierkorb liegt oder nicht in einer Sprache dieser Site
  vorliegt, und **Kein Zugriff**, wenn Sie ihn nicht mehr lesen dürfen. Eine solche Karte sagt, was zu tun ist, und
  zeigt sonst nichts vom Eintrag;
- den **Ort** (die Namen der Einträge darüber) und wann der Eintrag zuletzt **geändert** wurde;
- **Anzeigen**, **Bearbeiten** (wenn Sie ihn bearbeiten dürfen) und, in Ihrer eigenen Reihenfolge, **Nach oben** und
  **Nach unten**.

## 2. Finden und Sortieren

**Lesezeichen finden** durchsucht die Namen, Klassen, Orte und Ordnernamen Ihrer Lesezeichen, ohne Rücksicht auf
Groß- und Kleinschreibung. Unter *Alle Lesezeichen* durchsucht es alles; innerhalb eines Ordners durchsucht es diesen
Ordner und die Ordner darunter. **Suche zurücksetzen** entfernt die Suche; wenn nichts passt, sagt die Seite das und
bietet an, alle Lesezeichen zu durchsuchen.

Die Reihenfolgen:

| Reihenfolge | Bedeutet |
|---|---|
| Ihre Reihenfolge | Die Reihenfolge des Kastens Lesezeichen, die Sie mit Nach oben und Nach unten festlegen |
| Name A bis Z | Natürliche Reihenfolge, sodass „Page 2“ vor „Page 10“ kommt |
| Zuletzt hinzugefügt | Das neueste Lesezeichen zuerst (Lesezeichen speichern kein Datum: ein späteres Lesezeichen hat eine höhere Nummer) |
| Typ | Nach Klassenname, dann nach Name; nicht mehr verfügbare Lesezeichen zuletzt |
| Zuletzt geändert | Der zuletzt geänderte Eintrag zuerst |

In jeder Reihenfolge bleiben die Lesezeichen **nach Ordnern gruppiert**: zuerst die ohne Ordner, dann die Ordner in der
Reihenfolge der Ordnerliste. Nach oben und Nach unten werden in Ihrer eigenen Reihenfolge ohne Suche angeboten, sodass
eine Schaltfläche ein Lesezeichen nie neben eines verschiebt, das Sie nicht sehen können.

Die Seitengröße kommt aus `admininterface.ini [PaginationSettings] ItemsPerPageList_content_bookmark[]` (25, 50 und
100, wenn die Einstellung fehlt); Ihre Wahl wird als Voreinstellung `admin_bookmark_list_limit` gespeichert.

## 3. Lesezeichen hinzufügen und verschieben

- **Einträge hinzufügen** (über der Liste oder in einem leeren Ordner) öffnet den Auswahldialog. Die gewählten Einträge
  werden dem angezeigten Ordner hinzugefügt; unter *Alle Lesezeichen* kommt ein neues Lesezeichen in keinen Ordner,
  und ein Eintrag, für den Sie bereits ein Lesezeichen hatten, behält seinen Ordner. Einträge, die Sie nicht lesen
  dürfen, werden nicht hinzugefügt. Auch der Kasten Lesezeichen und das Kontextmenü des Inhaltsbaums fügen Lesezeichen
  hinzu.
- **Verschieben**: Markieren Sie die Lesezeichen, wählen Sie den Ordner unter **Ausgewählte Lesezeichen verschieben
  nach** und klicken Sie auf **Auswahl verschieben**. *Kein Ordner (oberste Ebene)* nimmt sie aus jedem Ordner heraus.
- Mit der Maus können Sie ein Lesezeichen auch auf einen Ordner in der Ordnerliste **ziehen**. Ist das Lesezeichen
  markiert, werden alle markierten Lesezeichen mit verschoben.
- **Ausgewählte entfernen** öffnet zuerst eine kurze Bestätigung. Es werden nur die Lesezeichen entfernt.

## 4. Lesezeichen und Ordner anordnen

In **Ihre Reihenfolge** (ohne Suche) hat jede Karte links einen **Griff** (⠿), und jeder Ordner der Ordnerliste hat
ebenfalls einen. Die Reihenfolge, die Sie festlegen, ist die Reihenfolge des Kastens Lesezeichen und des
Auswahldialogs.

- **Ziehen** Sie ein Lesezeichen an seinem Griff an eine neue Stelle innerhalb seines Ordners. Die Liste des Ordners
  ist während des Ziehens umrandet, und die Karte bewegt sich mit dem Zeiger; wenn Sie loslassen, wird die neue
  Reihenfolge gespeichert, und die Seite zeigt „Die Reihenfolge wurde gespeichert“.
- **Legen Sie es** stattdessen **auf einem Ordner** der Ordnerliste **ab**, um es in diesen Ordner zu verschieben. Der
  Ordner wird umrandet und mit **In diesen Ordner verschieben** beschriftet, sodass sich Umordnen und Verschieben leicht
  unterscheiden lassen. Markierte Lesezeichen werden gemeinsam verschoben.
- **Ordner** werden genauso angeordnet: Ziehen Sie den Griff eines Ordners über oder unter einen Nachbarn mit demselben
  übergeordneten Ordner; eine Linie zeigt, wohin er kommt.
- **Tastatur**: Setzen Sie mit Tab den Fokus auf einen Griff und drücken Sie die Pfeiltaste nach oben oder unten. Der
  Eintrag rückt um eine Stelle, die Seite wird gespeichert, und der Griff hat wieder den Fokus, sodass Sie die Taste
  erneut drücken können.
- **Positionsfeld**: Jede Karte hat „Position im Ordner“. Geben Sie eine Zahl ein und klicken Sie auf **Verschieben**
  (oder drücken Sie die Eingabetaste): Das Lesezeichen kommt an diese Stelle im gesamten Ordner, auch wenn der Ordner
  über mehrere Seiten reicht.
- **Ohne JavaScript** werden die Griffe nicht angezeigt; **Nach oben**, **Nach unten** und das Positionsfeld tun
  dasselbe.

In den anderen Reihenfolgen (Name, zuletzt hinzugefügt, Typ, zuletzt geändert) und während einer Suche lässt sich die
Liste nicht anordnen: Die Seite sagt **Zum Anordnen zu „Ihre Reihenfolge“ wechseln** (oder die Suche zurückzusetzen)
und verlinkt darauf. Ein Lesezeichen, das auf einem Ordner der Ordnerliste abgelegt wird, wird trotzdem dorthin
verschoben.

## 5. Ordner

Öffnen Sie einen Ordner in der Ordnerliste. Sein Bereich bietet:

- **Umbenennen**: der neue Name; Tags werden entfernt, und Namen werden bei 255 Zeichen abgeschnitten.
- **Verschieben**: der Ordner, in den er kommt. Der Ordner selbst und die Ordner darunter werden nicht angeboten: Ein
  Ordner kann nicht in sich selbst liegen. *Nach oben* und *Nach unten* ändern seine Stelle unter seinen Nachbarn.
- **Ordner entfernen** sagt zuerst, was mit dem Inhalt geschieht:
  - **Inhalt behalten** (der Standard): Seine Lesezeichen und Ordner werden in den Ordner verschoben, in dem er lag
    (oder auf die oberste Ebene), und kein Lesezeichen wird entfernt;
  - **Den gesamten Inhalt mit entfernen**: Der Ordner, jeder Ordner darunter und alle ihre Lesezeichen werden entfernt.

  In beiden Fällen werden die Einträge, auf die die Lesezeichen verweisen, nicht geändert. Nach dem Entfernen zeigt die
  Seite den Ordner, in dem er lag.

## 6. Probleme und Antworten

| Sie sehen | Warum, und was zu tun ist |
|---|---|
| Eine Karte zeigt **Nicht gefunden** | Der Eintrag wurde entfernt oder in den Papierkorb verschoben, oder er existiert nur in einer Sprache, die dieser Siteaccess nicht anzeigt. Entfernen Sie das Lesezeichen, oder stellen Sie den Eintrag aus dem Papierkorb wieder her |
| Eine Karte zeigt **Kein Zugriff** | Eine Rolle wurde geändert, und Sie dürfen den Eintrag nicht mehr lesen. Entfernen Sie das Lesezeichen, oder wenden Sie sich an einen Administrator |
| Die Griffe, **Nach oben**, **Nach unten** und das Positionsfeld fehlen | Sie werden nur in *Ihre Reihenfolge* ohne Suche angezeigt. Wählen Sie *Ihre Reihenfolge* und setzen Sie die Suche zurück |
| „Die Reihenfolge wurde nicht gespeichert“ | Die Reihenfolge nannte ein Lesezeichen, das nicht mehr in diesem Ordner liegt, zum Beispiel nach einer Änderung in einem anderen Fenster. Laden Sie die Seite neu und ordnen Sie erneut an |
| „Es wurde nichts verschoben“ nach Auswahl verschieben | Es war kein Lesezeichen markiert, oder der gewählte Ordner gehört nicht Ihnen |
| Ein Link auf den Ordner eines anderen zeigt alle Ihre Lesezeichen | Ordner sind persönlich: Eine Adresse mit einem Ordner, der nicht Ihnen gehört, zeigt Ihre eigenen Lesezeichen |
| Die Seite ist nach einer Template-Änderung unverändert | Leeren Sie die Caches template, template-override, content und template-block sowie den Antwort-Cache von Velocity |

## 7. Für Entwickler

- Die View ist `kernel/private/classes/views/content/bookmark.php`. Sie liest die Ordner und Lesezeichen
  (`eZContentBrowseBookmarkFolder::fetchRowsForUser()`), die Knoten aller Lesezeichen in einer Abfrage und die Namen der
  Einträge darüber in einer zweiten und übergibt einfache Arrays an `expBookmarkPage`.
- `expBookmarkPage` (`kernel/classes/expbookmarkpage.php`) ermittelt die Ordner und ihre Zähler, den Bereich von
  `(folder)`, die Suche, die Reihenfolgen, die Gruppierung, das Blättern, die Zahlen, die Ziele von Nach oben und Nach
  unten und die Adresse. Sie liest keine Datenbank und wird ohne eine getestet.
- `eZContentBrowseBookmarkFolder::shift()` verschiebt einen Ordner oder ein Lesezeichen innerhalb seines Ordners um
  eine Stelle nach oben oder unten; `setOrder()` speichert die Reihenfolge einiger oder aller Einträge eines Ordners
  (die gesendeten Einträge nehmen die Plätze ein, die sie innehatten, siehe `expBookmarkPage::reorderSlots()`), und
  `moveToPosition()` setzt einen Eintrag an eine Stelle. Beide weisen eine ID ab, die nicht dem Benutzer gehört, nicht
  in diesem Ordner liegt oder wiederholt wird.
- Adressen: `content/bookmark/(folder)/<id>|top/(sort)/name|added|type|modified/(offset)/<n>?q=<text>`. Jedes POST
  antwortet mit einer Weiterleitung auf denselben Ordner, dieselbe Reihenfolge und dieselbe Suche, mit dem Ergebnis als
  Hinweis.
- Die POST-Namen sind die, die die Seite schon immer hatte: `RemoveButton`, `AddButton`, `DeleteIDArray[]`,
  `MoveSelectedButton` mit `FolderID`, und `BookmarkFolderAction` (create, rename, delete, move_bookmark,
  move_folder, place, reorder) mit `FolderName`, `ParentFolderID`, `FolderID`, `DeleteBookmarks`, `BookmarkID`,
  `BookmarkIDArray[]`. Neu: `BookmarkShiftButton` (`up-<id>`, `down-<id>`, `fup-<id>`, `fdown-<id>`), `BookmarkOrderButton`
  mit `OrderType` (bookmark oder folder), `OrderFolderID` und `OrderIDs` (die IDs in ihrer neuen Reihenfolge, durch
  Kommas getrennt), und `BookmarkPositionButton` (die ID) mit `BookmarkPosition[<id>]`; `BookmarkPositionDefault` ist
  die erste Schaltfläche des Formulars, sodass die Eingabetaste in einem Positionsfeld dieses Lesezeichen verschiebt.
- Nur die eigenen Lesezeichen und Ordner des Benutzers werden gelesen oder geändert; die IDs eines anderen Benutzers
  werden abgewiesen. Jedes Formular trägt das Formular-Token. `NeedRedirectBack` mit `RedirectURI` führt nur zu einer
  Seite dieser Site ([sichere Weiterleitungen](../../en/us/features/6.0/safe-redirects.md)).
- Die Template-Variablen `view_parameters` und `bookmark_notice` bleiben erhalten; die neue `bookmark_page` enthält
  alles Übrige. Der Baum des Kastens Lesezeichen und des Auswahldialogs (`content/bookmark_tree.tpl`) ist unverändert.
- Das Aussehen steht im Lesezeichen-Abschnitt von `stylesheets/modules/content.css` (design/admin und design/admin4),
  in der Gestaltungssprache der Seiten für Sektionen, Linklisten und Sitzungen.

Tests, ohne Datenbank: `php vendor/bin/phpunit tests/tests/kernel/classes/bookmarks/expBookmarkPageTest.php`.
Mit einer Installation: `php vendor/bin/phpunit tests/tests/kernel/classes/bookmarks/BookmarkFoldersTest.php --filter 'testShift|testSetOrderAndMoveToPosition'`.

## Quellen

- `kernel/private/classes/views/content/bookmark.php`, `kernel/classes/expbookmarkpage.php`,
  `kernel/classes/ezcontentbrowsebookmarkfolder.php`, `kernel/classes/ezcontentbrowsebookmark.php`
- `design/admin4/templates/content/bookmark.tpl`, `design/admin4/stylesheets/modules/content.css` (dasselbe in
  `design/admin`)
- `tests/tests/kernel/classes/bookmarks/expBookmarkPageTest.php`, `tests/tests/kernel/classes/bookmarks/BookmarkFoldersTest.php`
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
