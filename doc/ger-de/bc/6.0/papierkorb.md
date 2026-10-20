# Die Ansicht des Papierkorbs: wer es verschoben hat, wo es lag, was darunter liegt

Lesen Sie diese Seite, wenn Sie den Papierkorb in der Administration (`content/trash`) verwenden, sein Template
überschreiben oder mehrere Webserver betreiben. Die Ansicht des Papierkorbs zeigt jetzt für jedes Element, wo es lag,
was darunter lag, ob es an seinen Platz zurückkehren kann und wer es in den Papierkorb verschoben hat. Alles auf der
Seite stammt aus Daten, die es gibt; das eine neue Datum ist, wer ein Element in den Papierkorb verschoben hat, und das
wird ab dieser Version festgehalten.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Reichhaltigere Zeilen, eine Zusammenfassungszeile, Filter nach Benutzer, Typ und Datum, Sortierung (seit Januar 2024) und eine Aufzeichnung, wer jedes Element in den Papierkorb verschoben hat, in den Spalten `trashed_by` und `trashed_via` von `ezcontentobject_trash` (bis zum 9. Oktober 2026 in `<VarDir>/trash/trashed.json`). |
| Wer betroffen ist | Redakteure (mehr Informationen); Overrides von `content/trash.tpl`; jede Installation führt das Datenbank-Update aus, das die beiden Spalten hinzufügt (bis dahin behält der Kernel die alte Datei). |
| Wie Sie es prüfen | Öffnen Sie `content/trash` in der Administration; die Zeilen zeigen **Gelöscht am** mit „von &lt;Benutzer&gt;“ oder „von Unbekannt“. |
| Wie Sie es beheben | Führen Sie das Datenbank-Update aus (siehe „Wo „verschoben von“ gehalten wird“), dann `php update/common/scripts/6.0/movetrashrecords.php`. Ein altes Template-Override funktioniert ohne die Details weiter (siehe „Template-Overrides“). |

Die Admin-Designs, die die Seite haben, sind `design/admin` und `design/admin4`; `admin2` und `admin3` haben kein
eigenes `trash.tpl` und fallen auf `design/admin` zurück.

## So verwenden Sie es

1. Öffnen Sie den Papierkorb in der Administrationsoberfläche (`content/trash`).
2. Lesen Sie jede Zeile. **Gelöscht am** zeigt das Datum und **von wem**, mit der Herkunft des Entfernens
   (`web admin` oder `cli <script>`). Elemente, die vor Beginn der Aufzeichnung verschoben wurden, zeigen „von
   Unbekannt“ und den letzten Bearbeiter.
3. Filtern Sie nach Benutzer, Typ oder Zeitraum. Die Filter stehen in der Adresse, sodass Blättern und Sortieren sie
   behalten:

   ```
   content/trash/(trashed_by)/14/(class)/1/(from)/2026-10-01/(to)/2026-10-02
   ```

   **Alle zeigen** hebt sie auf. **Papierkorb leeren** leert weiterhin den ganzen Papierkorb, unabhängig vom Filter.
4. **Wiederherstellen** funktioniert wie bisher (`content/restore/<object id>`). Die Spalte **Ursprüngliche
   Platzierung** sagt, ob der Elternknoten noch existiert, ebenfalls im Papierkorb liegt oder verschoben wurde.

## Was jede Zeile zeigt

| Spalte | Inhalt |
| --- | --- |
| Name | Der Name (verlinkt auf die Versionsansicht), die Objekt-ID, die Zahl der Knoten, die darunter lagen, seine Sprachen, „Noch im Baum unter“, wenn das Objekt noch eine Platzierung hat, und ein Umschalter **Details**: Besitzer, zuletzt geändert von, veröffentlicht, geändert, Sprachen, andere Platzierungen. |
| Typ, Sektion | Wie bisher. |
| Ursprüngliche Platzierung | Der vollständige Pfad mit Namen. Ein Vorfahr, der noch im Baum ist, ist ein Link; einer im Papierkorb oder einer, der fehlt, steht kursiv. Darunter der Zustand des Elternknotens: **vorhanden** (wird an seinem ursprünglichen Platz wiederhergestellt), **ebenfalls im Papierkorb** (stellen Sie ihn zuerst wieder her), **verschoben** oder **existiert nicht mehr** (die Seite zum Wiederherstellen fragt dann nach einem Platz). |
| Gelöscht am | Das Datum und **von wem**, mit der Herkunft des Entfernens (`web admin`, `cli <script>`). Elemente, die vor Beginn der Aufzeichnung verschoben wurden, zeigen „von Unbekannt“ und den letzten Bearbeiter, als solcher gekennzeichnet. |
| (letzte) | Eine Schaltfläche **Wiederherstellen**: der vorhandene Ablauf `content/restore/<object id>`. |

Über der Tabelle deckt eine Zusammenfassungszeile den ganzen Papierkorb ab: Elemente, wie viele direkt entfernt wurden,
wie viele darunter lagen, das älteste und wie viele ein festgehaltenes „verschoben von“ haben. Die Filter folgen
darauf.

## Filter

| Filter | Werte |
|---|---|
| `In den Papierkorb verschoben von` | Die Benutzer mit einem Eintrag, oder *Unbekannt*. |
| `Typ` | Die im Papierkorb vorhandenen Klassen. |
| `In den Papierkorb verschoben aus` / `bis` | Ein Zeitraum; beide Tage eingeschlossen. |

**Filter** sendet das Formular ab, und die Ansicht leitet auf dieselbe Liste mit den Filtern als View-Parametern
weiter, sodass Blättern, Sortieren und die alphabetische Navigation sie behalten:

```
content/trash/(trashed_by)/14/(class)/1/(from)/2026-10-01/(to)/2026-10-02/(sort_field)/name/(sort_order)/1
```

Ungültige Werte (ein nicht numerischer Benutzer, ein unmögliches Datum) werden verworfen.

## Sortieren (seit Januar 2024)

Die Liste des Papierkorbs ist seit Januar 2024 sortierbar (Upstream-Pull-Request #31, zusammengeführt als `fa42a6e960`
und `75361f4bea`). Klicken Sie auf einen Spaltenkopf, um danach zu sortieren; klicken Sie erneut, um umzukehren. Bisher
war die Liste aufsteigend nach Name geordnet. Jetzt ist der Standard **Gelöscht am, neueste zuerst**, sodass das
Element, das Sie gerade entfernt haben, oben steht.

| View-Parameter | Werte | Standard |
|---|---|---|
| `(sort_field)` | `name`, `class_name`, `section`, `trashed` | `trashed` |
| `(sort_order)` | `0` absteigend, `1` aufsteigend | `0` |

Beispiel, die ältesten Elemente im Papierkorb zuerst:

```
content/trash/(sort_field)/trashed/(sort_order)/1
```

Akzeptiert werden die Werte, die das View-Skript prüft (`kernel/private/classes/views/content/trash.php`); alles andere
fällt auf die Standardwerte zurück.

## Elemente eine Zeit lang im Papierkorb behalten

Der Cronjob-Teil `trashpurge` (`cronjobs/trashpurge.php`) leert den ganzen Papierkorb. Um stattdessen jedes Element eine
Anzahl von Tagen zu behalten, setzen Sie:

```ini
# settings/override/content.ini.append.php
[TrashSettings]
KeepItemsForDays=90
```

Der Cronjob löscht dann nur, was seit mindestens so vielen Tagen im Papierkorb liegt, gezählt ab dem Zeitpunkt des
Verschiebens (die Spalte `trashed`) rückwärts vom Beginn des Laufs. Tage sind Kalendertage in der Zeitzone des Servers:
Ein Element, das auf die Sekunde genau vor 90 Tagen verschoben wurde, wird gelöscht; über einen Wechsel der Sommerzeit
verschiebt sich die Grenze um eine Stunde. Leer oder `0`, der Standard, löscht wie bisher alles. Ein Wert, der keine
ganze Zahl von Tagen bis 36500 ist (`90 days`, `-1`), löscht nichts und meldet einen Fehler auf der Kommandozeile und
im Fehlerprotokoll, sodass ein Tippfehler den Papierkorb nicht leeren kann. `bin/php/trashpurge.php` ist nicht
betroffen; übergeben Sie ihm `--trashed-days=<days>`. Beide lehnen ein so großes Alter ab, dass die Datumsrechnung
überläuft (es passte bisher auf jedes Element oder auf ein Datum, das nicht so viele Tage zurücklag).

Die Einstellung wird aus `content.ini` so gelesen, wie der Siteaccess sie sieht, für den der Cronjob läuft:
`runcronjobs.php -s <siteaccess>`, der unter Setup > Cronjobs gewählte Siteaccess oder ohne `-s` der
Standard-Siteaccess. Ein Wert in `settings/override/` gilt für jeden Siteaccess und hat Vorrang vor einem in
`settings/siteaccess/<name>/`; der Papierkorb ist einer für die ganze Datenbank, setzen Sie ihn also dort. Der Cronjob
liest die Einstellung aus den Dateien, nicht aus dem INI-Cache: Ein gerade hinzugefügter Wert, in einer neuen
Override-Datei oder auf einem Server, dessen `config.php` die Prüfung von Änderungen an INI-Dateien abschaltet, zählt
schon beim nächsten Lauf, ohne dass der Cache zuerst geleert werden muss.

Elemente, die verschoben wurden, bevor es die Spalte `trashed` gab (ein Upgrade von 5.x; das Datenbank-Update fügt sie
mit 0 hinzu), zählen als 1970 verschoben und gehen beim ersten Lauf. Um sie zu behalten, geben Sie ihnen zuerst einen
Zeitpunkt, zum Beispiel den des Upgrades: `UPDATE ezcontentobject_trash SET trashed = <timestamp> WHERE trashed = 0`.
Eine Website, die den Zeitpunkt des Verschiebens in einer eigenen Tabelle führte (eine Erweiterung, die
`eZContentObjectTrashNode` ersetzt hat), kopiert ihn vor dem Umstellen auf dieselbe Weise nach `trashed`.

Der Cronjob-Teil steht standardmäßig in keinem Teil von `cronjob.ini`; fügen Sie ihn einem hinzu, zum Beispiel:

```ini
# settings/override/cronjob.ini.append.php
[CronjobPart-trashpurge]
Scripts[]
Scripts[]=trashpurge.php
```

## Hinweise zum Upgrade

### Template-Overrides

Das Template funktioniert weiter mit einer View von vor dieser Änderung (einer, die nur `view_parameters` setzt): Es
holt die Liste dann selbst, wie schon immer, und lässt die Details weg. Um die neuen Spalten in Ihrem Override zu
erhalten, führen Sie es mit dem ausgelieferten `design/admin/templates/content/trash.tpl` oder
`design/admin4/templates/content/trash.tpl` zusammen.

### Wo „verschoben von“ gehalten wird

Seit dem 9. Oktober 2026 in der Zeile des Papierkorbs selbst, geschrieben in derselben Transaktion wie der Rest davon:

| Spalte | Inhalt |
|---|---|
| `trashed_by` | die ID des Inhaltsobjekts des Benutzers, der das Objekt in den Papierkorb verschoben hat; 0, wenn unbekannt (Zeilen von vor dem 9. Oktober 2026). Ein ohne Anmeldung ausgeführtes Skript verzeichnet den anonymen Benutzer |
| `trashed_via` | woher das Entfernen kam: `web <siteaccess>` oder `cli <script>` (bei `ezexec.php` das Skript, das es ausführt) |

- **Geschrieben** von `eZContentObjectTrashNode::createFromNode()`. Jedes Verschieben in den Papierkorb geht dort
  durch: `eZContentObjectTreeNode::removeNodeFromTree()` (aufgerufen von `removeSubtrees()`, das das Löschen der
  Administration, die Inhaltsjobs und Skripte alle verwenden) legt dort die Zeile des Papierkorbs an. Jeder Knoten
  eines entfernten Teilbaums erhält seinen eigenen Wert.
- **Mit der Zeile verschwunden**: Das endgültige Löschen oder Wiederherstellen eines Objekts entfernt seine Zeile im
  Papierkorb und damit den Eintrag.
- **Auf jedem Webserver gleich**, in den Sicherungen der Datenbank und mit SQL lesbar.

**Das Datenbank-Update fügt die Spalten hinzu.** Führen Sie das Update Ihrer Engine aus
(`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, siehe die
[Anleitung zum Aktualisieren](../../anleitungen/aktualisieren.md)). Die Anweisungen für die beiden Spalten sind unter
MySQL und PostgreSQL abgesichert und können erneut laufen; auf einer Datenbank, die eine frühere Kopie der Datei
angewendet hat, scheitern spätere Anweisungen der Datei an dem, was existiert, und das ist zu erwarten.
**Setup > System Upgrade** (`setup/systemupgrade`) führt die beiden Spalten bis dahin als fehlend auf.

Code und Update können in beliebiger Reihenfolge kommen. Solange die Spalten nicht existieren, meldet das
`eZContentObjectTrashNode::hasTrashedByColumns()`, und der Kernel arbeitet mit der alten Tabelle: Das Verschieben von
Inhalten in den Papierkorb speichert die Zeile ohne sie und verzeichnet wie bisher in `trashed.json`, wer es getan hat,
und die Ansicht des Papierkorbs, ihr Filter nach Benutzer und das Wiederherstellen lesen die Zeile ohne sie. Das Skript
des nächsten Abschnitts kopiert, was die Datei enthält, sobald das Update gelaufen ist. Die Prüfung fragt den Katalog
der Datenbank (`information_schema`, `PRAGMA table_info`, `user_tab_columns`), nie eine Abfrage, die innerhalb einer
Transaktion scheitern könnte; sind die Spalten gefunden, wird die Antwort für den Prozess behalten, solange sie fehlen,
wird beim nächsten Verschieben in den Papierkorb erneut gefragt, sodass ein laufender Velocity-Worker das Update ohne
Neustart übernimmt.

Unter SQLite führen Sie die beiden Zeilen `ALTER TABLE ezcontentobject_trash ADD COLUMN trashed_by|trashed_via` von
`update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql` einmal aus (ein zweiter Lauf scheitert mit „duplicate column
name“ und ändert nichts) und danach die Zeile `CREATE INDEX IF NOT EXISTS
ezcontentobject_trash__ezcobj_trash_trashed_by` (`<table>__<index>` ist der Name, den `eZSQLiteSchema` dem Index der
`.dba` gibt, sodass die Konsistenzprüfung der Datenbank ihn findet; sie kann erneut laufen). Oracle (die Erweiterung
`ezoracle`, ihr eigenes Paket) braucht dieselben zwei Spalten:
`ALTER TABLE ezcontentobject_trash ADD ( trashed_by INTEGER DEFAULT 0 NOT NULL, trashed_via VARCHAR2(100) )`;
`trashed_via` bleibt dort nullable, weil Oracle eine leere Zeichenkette als NULL speichert. Der Index ist
`CREATE INDEX ezcobj_trash_trashed_by ON ezcontentobject_trash ( trashed_by )`; die Update-Datei von ezoracle
`update/database/ezpublish/6.0/dbupdate-6.0.0-6.0.15-trash-columns.sql` fügt die Spalten und den Index hinzu, jeweils
nur, wenn sie fehlen.

Unter MongoDB ist nichts auszuführen: Eine Collection hat keine Spalten, und der Kernel schreibt `trashed_by` und
`trashed_via` von Anfang an in jedes neue Dokument des Papierkorbs. `hasTrashedByColumns()` fragt das Schema des
Treibers (das ausgelieferte `share/db_schema.dba`, `expMongoDB::declaredColumns()`), das beide deklariert. Ein vor der
Änderung verschobenes Dokument hat keine solchen Felder; der Treiber liest und vergleicht ein fehlendes Feld als den
deklarierten Standardwert der Spalte, so wie eine SQL-Tabelle einer alten Zeile den Standard gibt (`trashed_by` 0,
`trashed_via` ''), sodass der Filter „Unbekannt“ diese Dokumente findet und `movetrashrecords.php` ihnen den Benutzer
gibt, den seine Datei nennt. Die Liste des Papierkorbs und ihre Anzahl kommen unter MongoDB aus einer Aggregation
(`eZContentObjectTrashNode::trashListMongo()`; die SQL-Liste verknüpft Tabellen, was MongoDB nicht kann), mit denselben
Filtern, Sortierschlüsseln und demselben Blättern. Zwei Unterschiede: Einem Benutzer, dessen `content/read` durch
Richtlinien eingeschränkt ist, wird ein leerer Papierkorb gezeigt (die Richtlinien sind SQL; eine Warnung wird
protokolliert), und ein `AttributeFilter` listet ebenso nichts auf.

| Engine | Die Spalten kommen aus |
|---|---|
| MySQL oder MariaDB, PostgreSQL, SQLite | dem Datenbank-Update `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` |
| Oracle | dem obigen `ALTER TABLE` |
| MongoDB | nichts auszuführen: Neue Dokumente tragen die Felder, ältere werden als die Standardwerte gelesen |

`trashed_via` enthält nur druckbares ASCII (alles andere wird zu `?`) und höchstens 100 Zeichen, sodass es in eine
Spalte passt, deren Länge Bytes zählt, und nie eine Bytefolge enthält, die PostgreSQL ablehnt.

### Was bisher festgehalten wurde: `trashed.json`

Bis zum 9. Oktober 2026 war der Eintrag eine JSON-Datei im var-Verzeichnis, `<VarDir>/trash/trashed.json`, ein Eintrag
pro Objekt (`node_id`, `trashed`, `user_id`, `user_name`, `via`, `recorded`). Sie lag lokal auf einem Webserver und
außerhalb der Transaktionen und Sicherungen der Datenbank. Sobald die Spalten existieren, schreibt der Kernel sie nicht
mehr (davor tut er es weiterhin), aber:

- die Ansicht des Papierkorbs liest sie weiterhin für Zeilen, deren `trashed_by` 0 ist, wenn der Eintrag zur Zeile
  passt (dieselbe Knoten-ID und derselbe Zeitpunkt des Verschiebens), sodass nichts bisher Festgehaltenes vor dem
  Kopieren verloren geht;
- das endgültige Löschen oder Wiederherstellen eines Objekts entfernt weiterhin seinen Eintrag;
- `update/common/scripts/6.0/movetrashrecords.php` kopiert die Einträge in die Spalten ihrer Zeilen (Zeilen, die ein
  `trashed_by` haben, behalten es, sodass es erneut laufen kann). `--dry-run` zählt nur; `--remove-file` löscht die
  Datei, wenn jeder Eintrag eine Zeile im Papierkorb hat (Exit-Status 2, wenn es die Datei behält, 1 bei einem Fehler:
  Die Spalten fehlen, oder die Datei enthält kein JSON-Objekt, das dann weder kopiert noch entfernt wird). Führen Sie
  es einmal pro var-Verzeichnis aus (`-s <siteaccess>` für einen Siteaccess mit eigenem var-Verzeichnis); in einem
  Cluster auf jedem Webserver.
- Es gibt die Einträge in der Datei aus, die Zeilen, die ein `trashed_by` erhalten haben, die Zeilen, die ihres
  behalten haben, die Einträge ohne Zeile im Papierkorb (ihre Objekte wurden endgültig gelöscht oder wiederhergestellt
  oder seitdem erneut verschoben: Sie bleiben in der Datei, und mit ihnen behält `--remove-file` sie) und die Zeilen,
  deren Eintrag keinen Benutzer nennt (bleiben auf 0).
- Die Datei hielt den Namen des Benutzers zu jenem Zeitpunkt fest; die Spalten halten die ID. Ein seitdem entfernter
  Benutzer erscheint als `#<id>`.

```bash
php update/common/scripts/6.0/movetrashrecords.php --dry-run
php update/common/scripts/6.0/movetrashrecords.php --remove-file
php update/common/scripts/6.0/movetrashrecords.php -s othersite --remove-file
```

Eine Website, die den Benutzer in einer eigenen Tabelle festgehalten hat (eine Erweiterung, die
`eZContentObjectTrashNode` ersetzt hat), kopiert ihn stattdessen mit SQL nach `trashed_by`.

## Für Entwickler

### Filtern in PHP

`eZContentObjectTrashNode::trashList()` und `trashListCount()` nehmen die passenden Parameter an: `ClassIDList`,
`TrashedFrom`, `TrashedTo` (Zeitstempel, eingeschlossen), `ContentObjectIDList` (ein leeres Array passt auf nichts) und
`ExcludeContentObjectIDList`. Die IN-Listen gehen über `eZDBInterface::generateSQLINStatement()`, sodass Engines mit
einer Grenze für IN-Listen sie bewältigen.

### Der alte Eintrag: `Exponential\Service\TrashRecord`

`kernel/private/classes/services/trashrecord.php` liest `trashed.json` (`all()`, `entryFor()`), vergisst Einträge
(`forget()`), kopiert sie in die Spalten (`moveToColumns()`, optional nur für einige Objekte) und entfernt die Datei
(`removeFile()`; die Sperrdatei bleibt). `columnsExist()` sagt, ob das Datenbank-Update gelaufen ist. `record()`, das
die Datei schrieb, ist veraltet; der Kernel ruft es nur auf, solange die Spalten fehlen; `via()` liefert
`eZContentObjectTrashNode::currentVia()`.

Der Filter nach Benutzer läuft in SQL: `trashList()` nimmt `TrashedBy` (die ID des Inhaltsobjekts eines Benutzers) und
`TrashedByUnknown` an, jeweils mit `TrashedByFileObjectIDList` für die Zeilen, die nur aus der alten Datei bekannt sind.

### Der Presenter: `Exponential\Service\TrashList`

Die View (`kernel/private/classes/views/content/trash.php`) fragt die Datenbank nach dem, was eine Seite braucht; sie
liest nie den ganzen Papierkorb. `context()` sagt nur, ob die Spalten `trashed_by` und `trashed_via` existieren, und
liest die Einträge der alten Datei für Zeilen ohne `trashed_by`. Für jedes Element der Seite ermittelt `describe()` den
Pfad (die Zeilen des Papierkorbs unter seinen Vorfahren, einmal für die Seite gelesen), den Zustand des Elternknotens,
die Knoten darunter (eine Zählung auf dem indizierten `path_string`, `TrashList::belowCondition()`), Besitzer, letzten
Bearbeiter, Daten, Sprachen und verbleibende Platzierungen. `summary()` und `userOptions()` zählen in SQL
(`userOptions()` gruppiert nach `trashed_by`, das den Index `ezcobj_trash_trashed_by` hat); beide zählen Zeilen des
Papierkorbs, sodass ein an zwei Stellen entferntes Objekt doppelt zählt, so wie es doppelt aufgeführt ist. Unter
MongoDB, dessen Treiber weder das `NOT EXISTS` der Zusammenfassung noch `SUM( CASE ... )` noch `GROUP BY` übersetzt,
kommen beide aus Aggregationen mit denselben Zahlen (ein Dokument, das älter als `trashed_by` ist, zählt als 0); die
Knoten unter einem Element werden über die Übersetzung des `LIKE` durch den Treiber gezählt, und die Zeilen des
Papierkorbs unter den Vorfahren über sein `IN`. Die Klasse (`kernel/private/classes/services/trashlist.php`) stellt
außerdem `classOptions()` sowie `filters()`, `filterURI()` und `listParams()` für die Filter in der URL bereit.

### Ein großer Papierkorb

Gemessen unter SQLite mit synthetischen Zeilen im Papierkorb (Gruppen aus einem Element und neun darunter, die auf
veröffentlichte Objekte zeigen), als Administrator, eine Seite mit 50 Elementen, die PHP-Schritte der View ohne das
Template, für die Filter keiner / nach Benutzer / Unbekannt; die Zeiten schwanken zwischen Läufen um etwa ein Fünftel.
Eine zweite Messung auf einer Kopie der Datenbank einer echten Website (30.000 Zeilen in Vierergruppen, eine Seite mit
50, kein Filter) ergab vorher 2,5 s und 56 MB Höchstwert für den ganzen Prozess, nachher 0,26 s und 12 MB, einschließlich
des Kernels; die Listenabfrage und ihre Zählung waren dann der größte Teil.

| Zeilen im Papierkorb | Vorher | Nachher (mit dem Index) | Speicher vorher | Speicher nachher |
|---|---|---|---|---|
| 30.000 | 0,84–1,04 s | 0,17–0,32 s | 48 MB | 2–3 MB |
| 100.000 | 2,7–3,2 s | 0,54–0,90 s | 160 MB | 2–3 MB |

Vorher las die View jede Zeile des Papierkorbs (`SELECT *`, einschließlich des langen `path_identification_string`) und
zählte die Knoten unter jedem Element der Seite, indem sie alle durchging. Was bleibt, ist die Listenabfrage selbst
(`trashList()` und `trashListCount()`, je etwa 0,13–0,32 s bei 100.000 Zeilen) und die Zusammenfassungszeile
(0,18–0,24 s). Der Index auf `trashed_by` senkte die Benutzerliste bei 100.000 Zeilen von etwa 100 ms auf 16 ms.

Die Knoten unter einem Element werden unter SQLite mit einem Bereich auf `path_string` gezählt (das `LIKE` ohne den
Index beantwortet) und auf den anderen Engines mit `LIKE '<path>%'`, wie es die Teilbaumabfragen des Kernels tun. Ein
Bereich wird dort nicht verwendet, weil eine Locale-Kollation (PostgreSQL mit `en_US.UTF-8`) beim Vergleichen die
Schrägstriche ignoriert. Wie bei diesen Teilbaumabfragen beantwortet PostgreSQL das `LIKE` nur dann aus einem Index,
wenn die Datenbank die Kollation C verwendet oder der Index `text_pattern_ops` hat.

### Tests

`tests/tests/kernel/classes/trash/eZContentObjectTrashRecordTest.php` läuft auf der eigenen Datenbank der Installation
(keine Testdatenbank). Er legt unter der Medienwurzel einen Ordner mit einem Kind und einem Enkel an, verschiebt ihn in
den Papierkorb und prüft die Spalten der Zeilen im Papierkorb, die Beschreibung, die Filter und dass ein Eintrag der
alten Datei gelesen, in seine Zeile kopiert und beim endgültigen Löschen vergessen wird. Danach löscht er alles
endgültig, was er angelegt hat. Er braucht die Spalten `trashed_by` und `trashed_via`.

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/trash/eZContentObjectTrashRecordTest.php
```

`eZContentObjectTrashOldSchemaTest.php` ist sein Gegenstück für eine Datenbank ohne die Spalten (eine Kopie der
Datenbank einer Website von vor dem Update; auf einer aktualisierten übersprungen): Verschieben in den Papierkorb, der
Eintrag in der Datei, das Holen der Zeile, die Filter der View, Wiederherstellen und endgültiges Löschen.
`TrashedByColumnsTest.php` braucht keine Datenbank: die Schema- und Update-Dateien, `createFromNode()`, `currentVia()`
und `definition()` vor und nach dem Update. `TrashListPagedTest.php` braucht ebenfalls keine: `belowCondition()` auf
einer SQLite-Tabelle im Speicher (genau die Zeilen darunter, kein Geschwister, dessen ID mit denselben Ziffern beginnt,
aus dem Index auf `path_string` beantwortet), und die Zusammenfassung und die Benutzerliste unter MongoDB gegen eine
Ersatzverbindung, und mit `EXP_TEST_MONGO_SCRATCH=<host>:<port>/<scratch or test database>` gegen einen echten Server.

Die Testmatrix des Papierkorbs im Browser (Liste, Entfernen, Wiederherstellen, Leeren; unter Apache und Velocity; beide
Admin-Designs; 960 px breit bei Skalierung 2) besteht mit der neuen Seite.

## Zugehörige Korrekturen vom selben Tag

- **Papierkorb leeren löscht in Stapeln**: jeweils 100 Elemente, jeder Stapel in einer eigenen Transaktion, mit Leeren
  des Inhalts-Caches und einer Pause von einer Sekunde zwischen den Stapeln, genau wie `bin/php/trashpurge.php` und der
  Cronjob-Teil `trashpurge` (`Exponential\Service\Trash::purgeInBatches()`).
- „Remove timed out sessions“ in Setup entfernt außerdem die Warenkörbe dieser Sessions, wie
  `bin/php/ezsessiongc.php` (`SessionGarbageCollector::collect()`).
- Der Cronjob-Teil `clusterpurge` löscht Dateien, die vor 30 Tagen abgelaufen sind; er übergab eine bloße 30, was
  30 Sekunden bedeutete.
- Das Entfernen eines Teilbaums, der jede Platzierung eines Objekts enthält, entfernt auch das Objekt (das Flag der
  Hauptplatzierung eines Knotens, der schon in einem Stapel war, war veraltet).
- Das Entfernen des Attributs einer Mediendatei löscht die gespeicherte Datei nur, wenn keine andere Medienzeile sie
  nennt (eine Kopie teilt sich die Datei der Quelle).
- Ein kopiertes Objekt mit einem Bild besitzt seine eigenen Bilddateien, und das Entfernen der Kopie lässt die Quelle
  unberührt. Das Entfernen eines Entwurfs entfernt jede Bilddatei dieses Entwurfs, nicht nur die, die sein XML nennt.

## Verwandte Seiten

- [Inhaltsjobs](../../../eng-us/features/6.0/content-jobs.md) und das [Upgrade der Inhaltsjobs](../../../eng-us/bc/6.0/content-jobs.md)
- [Upgrade-Checkliste vom 1. und 2. Oktober 2026](verhaltensaenderungen-2026-10.md)
- [Audit-Ereignismodell](../../spezifikationen/6.0/audit-ereignismodell.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Geschichte: [Oktober 2026](../../../eng-us/history/2026/2026-10.md), [Januar 2024](../../../eng-us/history/2024/2024-01a.md) (Sortierung)
