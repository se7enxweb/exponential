# RSS-Feeds: Exporte veröffentlichen und Importe einlesen

Diese Anleitung erklärt die RSS-Seite der Administrationsoberfläche (Setup > RSS, `/rss/list`): was ein RSS-Export und
ein RSS-Import sind, was jeder Teil der Seite aussagt, wie Sie einen Feed anlegen, prüfen und entfernen und was zu tun ist, wenn ein
Feedreader nichts erhält oder ein Import nichts hereinbringt.

Sie richtet sich an Administratoren, die Feeds oder Podcasts veröffentlichen, und an Betreiber, die Importe am Laufen halten. Jede Zahl
und jede Meldung unten wurde am Code geprüft (`kernel/private/classes/views/rss/list.php`,
`kernel/classes/ezrssexport.php`, `kernel/classes/ezrssimport.php`, `kernel/private/classes/cronjobs/rssimport.php`
und `kernel/private/classes/views/rss/feed.php`), und zwar am 6. Oktober 2026 auf dem Demonstrationsserver.

[Anleitungen](README.md) · Siehe auch: [RSS: Feeds für Apple Podcasts, eine seitenweise Feed-Liste und sicherere Exporte](../funktionen/6.0/rss-podcast-und-feed-liste.md),
[OPML-Exporte](../bc/6.0/opml.md), [Cronjobs](cronjobs.md)

## Kurz gesagt

- Ein **Export** ist ein Feed, den diese Site unter `/rss/feed/<address>` veröffentlicht. Er listet die neuesten Objekte unterhalb seiner
  **Quellen** auf (Orte, jeweils mit einer Klasse und wahlweise ihren Unterelementen), als RSS 1.0, RSS 2.0, Atom, OPML oder als
  Feed für Apple Podcasts.
- Ein **Import** liest den Feed einer anderen Site und legt unterhalb seines **Ziels** für jeden Eintrag, den er noch nicht gesehen
  hat, ein Objekt an. Der **rssimport-Cronjob** (`rssimport.php`) führt die aktiven Importe aus; ohne ihn wird nichts
  gelesen.
- Die Seite beginnt mit einer Übersicht (Exporte, aktive Exporte, Importe, aktive Importe, importierte Objekte), einer Suche
  und einem Filter (Alle, Aktiv, Inaktiv, Brauchen Aufmerksamkeit), dann folgt eine Karte pro Export und eine pro Import.
- **Ausgewählte entfernen** fragt zuerst nach und sagt, was das Entfernen bedeutet: Die Adresse eines entfernten Exports antwortet ihren
  Lesern nicht mehr; ein entfernter Import behält die Objekte, die er angelegt hat.

## 1. Die Seite öffnen

Setup > RSS, oder `/rss/list` im Administrations-Siteaccess. Die Seite erfordert die Richtlinie `rss/edit`. Der Editor-Siteaccess
bietet das RSS-Modul überhaupt nicht an.

## 2. Die Übersicht und die Bedienelemente

| Kennzahl | Was sie zählt |
|---|---|
| Exporte, Aktive Exporte | Veröffentlichte Exporte und diejenigen, die unter ihrer Adresse antworten |
| Importe, Aktive Importe | Importe und diejenigen, die der Cronjob liest |
| Importierte Objekte | Objekte, deren Remote-ID mit `RSSImport_` beginnt: jedes Objekt, das irgendein Import angelegt hat |

Darunter:

- **Feed finden** filtert die Karten auf dieser Seite jeder Liste nach Name, Adresse, Format, Quelle, Ziel oder ID.
- **Anzeigen** behält nur aktive, inaktive oder Aufmerksamkeit brauchende Karten.
- **Pro Seite** legt die Größe beider Listen fest (standardmäßig 25, 50, 250, `content.ini [RSSListSettings] ItemsPerPageList`)
  und wird für Ihren Benutzer gespeichert.
- Eine Zeile sagt, wie lange ein Feed zwischengespeichert wird: `site.ini [RSSSettings] CacheTime` Sekunden oder „bei jeder
  Anfrage neu geschrieben“, wenn der Wert 0 ist.

Die Suche und der Filter brauchen JavaScript; ohne JavaScript wird jede Karte angezeigt, und jeder Knopf funktioniert weiterhin.

## 3. Eine Export-Karte

Der Kopf zeigt den Namen (einen Link zu seiner Bearbeitungsseite), die ID, **Aktiv** oder **Inaktiv**, das Format in Worten
(*RSS 2.0*, *Atom 1.0*, *OPML 2.0 (Liste von Feeds)*, *Apple Podcasts (RSS 2.0 + iTunes)*) und **Braucht Aufmerksamkeit**,
wenn etwas nicht stimmt. **Feed öffnen** öffnet die öffentliche Adresse in einem neuen Tab; **Bearbeiten** öffnet das Formular.

| Feld | Was es aussagt |
|---|---|
| Feed-Adresse | Die Adresse, die Leser abonnieren: die öffentliche Site des Siteaccess des Exports, dann `/rss/feed/<address>` |
| Quellen | Bis zu fünf Quellen mit ihrer Klasse und „mit Unterelementen“, dann „und N weitere“; eine Quelle, deren Ort verschwunden ist, zeigt „(fehlt)“. OPML-Exporte zählen die Feeds, die sie aufführen |
| Einträge im Feed | Die Höchstzahl an Objekten, die ein Feed enthält, und „nur Hauptknoten“, wenn das eingestellt ist |
| Zuletzt geschrieben | Wann die zwischengespeicherte Kopie des Feeds zuletzt geschrieben wurde (die neueste aller Siteaccesses); „Seit dem Leeren des Caches nicht abgerufen“, bis ein Leser anfragt; „Bei jeder Anfrage“, wenn CacheTime 0 ist; inaktive Exporte werden nicht ausgeliefert |
| Links zeigen auf | Der Siteaccess, dessen Adresse die Links des Feeds verwenden |
| Geändert | Wann und von wem der Export zuletzt gespeichert wurde |

**Braucht Aufmerksamkeit** listet die Gründe unter der Karte auf: keine Feed-Adresse, keine Quelle, eine Quelle, die nicht mehr existiert.
Nur aktive Exporte werden markiert.

Sortieren Sie die Exporte mit den Schaltflächen unter **Sortieren nach** nach Name, Änderungsdatum, Status, Format, Adresse oder ID; die gerade verwendete Schaltfläche
kehrt die Reihenfolge um. Das Sortieren beginnt die Liste auf ihrer ersten Seite und lässt die Importe, wo sie sind.

## 4. Eine Import-Karte und der Cronjob

| Feld | Was es aussagt |
|---|---|
| Quell-URL | Der Feed, den der Import liest |
| Ziel | Der Ort, unter dem die neuen Objekte landen (ein Link), oder „fehlt“ |
| Legt an | Die Klasse der neuen Objekte und den Benutzer, dem sie gehören |
| Importiert | Wie viele Objekte dieser Import angelegt hat (Remote-ID `RSSImport_<id>_...`) |
| Neuester Eintrag | Wann der neueste davon veröffentlicht wurde: das letzte Mal, dass der Import etwas hereingebracht hat |

Über den Importen nennt eine Statusleiste den Cronjob-Teil, der `rssimport.php` ausführt, seinen Zeitplan in Worten und seinen nächsten
Lauf, mit **Cronjob öffnen** für diesen Teil auf der Cronjob-Seite. Sie warnt, wenn die Crontab diesen Teil nicht ausführt,
und ist rot, wenn kein Teil das Skript ausführt.

## 5. Anlegen und Entfernen

**Neuer Export** und **Neuer Import** öffnen wie bisher die Formulare.

Zum Entfernen: Kreuzen Sie die Karten an und drücken Sie **Ausgewählte entfernen** unter der jeweiligen Liste. Eine Bestätigung listet jeden Feed mit seiner
Adresse oder Quelle auf und sagt, was geschieht:

- Exporte: Die Adressen antworten nicht mehr, Leser erhalten von da an einen Fehler; die Quellen werden mit dem Export entfernt; die
  Inhalte bleiben unberührt;
- Importe: Der Cronjob liest sie nicht mehr; die Objekte, die sie angelegt haben, bleiben (ihre Anzahl wird angezeigt).

**N Exporte entfernen** entfernt sie, zusammen mit dem Entwurf eines gerade bearbeiteten Exports und den Quellen und OPML-Zeilen
beider; **Abbrechen** kehrt zurück und ändert nichts. Die Liste meldet danach einmal „Entfernt: ...“. Wer Ausgewählte entfernen
drückt, ohne etwas angekreuzt zu haben, erhält einen entsprechenden Hinweis, statt dass stillschweigend nichts geschieht.

Um einen Feed stattdessen vorübergehend anzuhalten, bearbeiten Sie ihn und entfernen Sie das Häkchen bei **Aktiv**.

## 6. Einen Export oder einen Import bearbeiten

Das Öffnen eines Feeds erstellt einen Entwurf davon; Leser sehen von einer Änderung nichts bis **OK**. **Abbrechen** verwirft den Entwurf
und kehrt zur RSS-Liste zurück. Solange jemand anderes denselben Feed geöffnet hat (`content.ini` DraftTimeout), nennt die Seite,
wer und bis wann, mit **Wiederholen** und **Zurück zur RSS-Liste**.

Das Export-Formular hat drei Teile, jedes Feld mit einer Zeile, wofür es da ist:

- **Der Feed**: Name, Beschreibung, Feed-Format, Zugriffs-URL (`rss/feed/` und dieser Wert; die vollständige öffentliche Adresse wird
  mit **Feed öffnen** angezeigt, wenn der Export aktiv ist), Aktiv, URL der Website und das Bild (nur RSS 2.0).
- **Was der Feed listet**: die Anzahl der Einträge, Nur Hauptknoten und eine Karte pro Quelle mit ihrem Ort
  (**Durchsuchen** öffnet den Inhaltsbrowser mit einem Hinweis, was der Ort bedeutet), Unterknoten, die Klasse (**Setzen** lädt
  ihre Attribute) und welche Attribute zu Titel, Beschreibung, Kategorie und Anhang werden. **Quelle hinzufügen** fügt eine
  Karte hinzu; **Diese Quelle entfernen** entfernt eine. Ausgeblendet für OPML, das stattdessen andere Feeds auflistet.
- **Apple Podcasts** oder die **OPML**-Felder, wenn dieses Format gewählt ist.

Was das Speichern eines Exports verhindert (eine Klasse ohne das gewählte Attribut, ein aktiver Export ohne Adresse),
wird oben aufgeführt.

Das Import-Formular folgt der Reihenfolge, in der ein Import eingerichtet wird: **1. Der Feed** (Name und Quell-URL; **Aktualisieren** liest den Feed
und ermittelt sein Format), **2. Wohin die Einträge kommen** (Ziel und Eigentümer), **3. Was aus jedem Eintrag wird** (Klasse und
die Zuordnung der Feed-Felder zu Attributen) und **4. Ausführen** (Aktiv). Ein Import braucht einen Namen und eine http- oder
https-Quell-URL, um gespeichert zu werden; andernfalls bleibt er ein Entwurf, und das Formular nennt den Grund. Hat er Objekte angelegt, nennt das Formular,
wie viele und wann das neueste kam.

## 7. Fehlerbehebung

| Problem | Ansehen | Behebung |
|---|---|---|
| Ein Leser erhält einen Fehler | Die Karte: Inaktiv? Feed-Adresse „nicht gesetzt“? | Bearbeiten, eine Adresse setzen, Aktiv ankreuzen |
| Ein Feed ist leer | Quellen: keine oder „(fehlt)“ | Eine Quelle hinzufügen, deren Ort existiert, mit der richtigen Klasse |
| Eine Änderung erscheint nicht im Feed | Zuletzt geschrieben | Auf CacheTime warten oder den RSS-Cache leeren (`php bin/php/ezcache.php --clear-id=rss_cache --allow-root-user`) |
| Ein Import bringt nichts | Die Statusleiste; Importiert und Neuester Eintrag | Den Teil mit `rssimport.php` einplanen; prüfen, ob sich die Quell-URL öffnen lässt; prüfen, ob Ziel und Legt an gesetzt sind |

## Quellen

- Modul und View: `kernel/rss/module.php`, `kernel/private/classes/views/rss/list.php`
  (`ListView::exportInfo()`, `importInfo()`, `importedObjects()`, `lastGenerated()`, `summary()`,
  `removeExports()`, `removeImports()`); der Pager `kernel/rss/ezrsslistpager.php`.
- Templates: `design/admin4/templates/rss/list.tpl`, `confirmremove.tpl`, `edit_export.tpl`, `edit_export_opml.tpl`, `edit_import.tpl`, `edit_export_denied.tpl`, `edit_import_denied.tpl`, `browse_*.tpl`, `exp_style.tpl`, `exp_list_script.tpl`
  (dieselben Dateien in `design/admin/templates/rss/`); der gemeinsame Pager `design/standard/templates/rss/pagination.tpl`.
- Der Cronjob-Teil: `Processlist::scriptCronjob()` in `kernel/private/classes/views/workflow/processlist.php`.
- Tests: `tests/tests/kernel/classes/expAdminListsRedesignTest.php` (ohne Datenbank).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
