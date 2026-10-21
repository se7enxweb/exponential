# Berechtigungsprüfungen für Benutzer mit vielen Rollenzuweisungen

Lesen Sie diese Seite, wenn Ihre Installation Benutzer mit vielen Rollenzuweisungen hat, zum Beispiel Mitglieder von
Dutzenden oder Hunderten von Teamrooms, deren Mitgliederrollen pro Teamroom-Teilbaum zugewiesen sind, und deren Seiten
langsam sind oder deren Inhaltslisten leer bleiben. Sie beschreibt, was die Berechtigungsprüfungen eines solchen
Benutzers vorher kosteten, was sie jetzt tun, die Messungen und die Einstellung, die die kürzere Berechtigungsbedingung
abschaltet.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | `eZContentObject::checkAccess()` und `canCreateClassList()` laden die Orte eines Objekts einmal pro Aufruf statt einmal pro Richtlinie. Die Inhaltsabrufe führen die Lese-Richtlinien zusammen, die sich nur im Teilbaum unterscheiden, für den ihre Rolle zugewiesen ist, und lassen diejenigen weg, die keinen Zugriff auf einen abgerufenen Knoten geben können. |
| Gemessen | Für ein Mitglied von 120 Teamrooms (1.203 Lese-Richtlinien): Die Prüfung des Lesezugriffs auf einen Artikel brauchte 120 Abfragen und 27,8 ms, jetzt 1 Abfrage und 1,0 ms; das Erstellen-Menü eines Ordners 601 Abfragen und etwa 100 ms, jetzt 2 Abfragen und 2 ms; das Auflisten der Kinder eines Ordners 24,4 ms, jetzt 1,2 ms. |
| Behoben | Unter SQLite schlug jeder Inhaltsabruf eines Benutzers mit 1.000 oder mehr Lese-Richtlinien fehl („Expression tree is too large“) und lieferte nichts. Lange OR-Ketten werden jetzt in Gruppen geschrieben, sodass Tausende von Richtlinien oder Teilbäumen unter der Grenze bleiben. |
| Geprüft | Auf dem Demonstrationsserver, für jeden Benutzer, die Lese-Richtlinien jeder Rolle und sechs Wegwerf-Benutzer, die jede Art von Einschränkung abdecken: 3.294 Abrufe, 28.224 Zugriffsprüfungen und 2.712 Erstellen-Listen liefern dieselben Antworten wie zuvor (siehe [Auf dem Demonstrationsserver geprüft](#auf-dem-demonstrationsserver-geprüft)). |
| Ergebnis | Dieselben Antworten: `checkAccess()` und `canCreateClassList()` vergleichen dieselben Orte, die Abrufe liefern dieselben Knoten. |
| Einstellung | `site.ini [RoleSettings] PermissionSQLOptimization=enabled` (Standard); `disabled` verknüpft wie zuvor jede Lese-Richtlinie per OR in der Bedingung des Abrufs. |
| Wer handeln muss | Niemand. |

## Warum es langsam war

Eine für einen Teilbaum zugewiesene Rolle bringt jede ihrer Richtlinien einmal pro Zuweisung mit, mit einer
Einschränkung `User_Subtree` für den Teilbaum. Ein Mitglied von 120 Teamrooms, dessen 5 Mitgliederrollen je 2
Lese-Richtlinien haben, hat 1.200 Lese-Richtlinien, plus die der Rollen aller Mitglieder. Drei Stellen arbeiteten sie
eine nach der anderen ab:

- **`checkAccess()`** verglich jede Richtlinie mit den Orten des Objekts und fragte die Datenbank für jede Richtlinie mit
  einer Einschränkung `Subtree`, `User_Subtree` oder `ParentDepth` erneut nach diesen Orten. Es hält bei der ersten
  Richtlinie an, die erlaubt, sodass ein Artikel im 120. Teamroom 120 Abfragen für eine Prüfung kostete. Eine
  vollständige Ansicht prüft Lesen, Bearbeiten, Entfernen, Verschieben und Erstellen für das Objekt und für jedes Kind,
  das sie auflistet. Für ein Objekt, das nie veröffentlicht wurde, lud es die Elternknoten, und für eine Einschränkung
  `Node` den Knoten der Einschränkung, ebenfalls für jede Richtlinie.
- **`canCreateClassList()`**, die Klassen, die das Erstellen-Menü eines Containers anbietet, befragte
  `classListFromPolicy()` zu jeder Erstellen-Richtlinie, und das lud die Orte für jede erneut: 601 Abfragen für jede
  Containeransicht.
- **Die Inhaltsabrufe** (`fetch( 'content', 'list' )`, `tree`, Zählungen, Suche, das Menü der Inhaltsstruktur)
  verknüpften jede Lese-Richtlinie per OR in der SQL-Bedingung: 1.203 Gruppen aus
  `class_id IN (...) AND path_string LIKE '/1/2/x/%'`, 146 KB SQL für jeden Abruf, und die Datenbank wertete alle für
  jede Kandidatenzeile aus. SQLite hält bei einer Ausdruckstiefe von 1.000 an, sodass ab 1.000 Richtlinien jeder Abruf
  fehlschlug und die Listen leer waren.

Bei einem Datenbankserver, der über das Netzwerk erreicht wird, ist jede Abfrage ein Roundtrip von einigen Zehntel
Millisekunden: Die 120 Abfragen einer Zugriffsprüfung kosten dort 25 bis 60 ms, die 601 Abfragen eines Erstellen-Menüs
120 bis 300 ms.

## Was es jetzt tut

1. **Orte einmal pro Aufruf.** `checkAccess()` behält die Orte des Objekts, die Pfade der Elternknoten eines
   unveröffentlichten Objekts (`accessCheckParentPaths()`) und den Hauptknoten jeder Einschränkung `Node` für alle
   Richtlinien, die es innerhalb des Aufrufs vergleicht. `canCreateClassList()` reicht die Orte von einem Aufruf von
   `classListFromPolicy()` an den nächsten weiter (dessen neuer dritter Parameter, per Referenz). Über den Aufruf hinaus
   wird nichts behalten, sodass ein Ort, der sich später in der Anfrage ändert, gesehen wird.
2. **Zusammengeführte Richtlinien.** `eZContentObjectTreeNode::mergeLimitationList()` macht aus den Richtlinien, deren
   Einschränkungen bis auf `User_Subtree` gleich sind, eine Richtlinie mit all ihren Teilbäumen: Die Richtlinien einer
   Liste werden per OR verknüpft, ebenso die Werte einer Einschränkung, sodass `(A AND subtree 1) OR (A AND subtree 2)`
   gleich `A AND (subtree 1 OR subtree 2)` ist. Gleiche Teilbäume werden einmal aufgeführt. Die Teilbäume einer
   zusammengeführten Richtlinie stehen in Klammern: Die anderen Einschränkungen der Richtlinie werden per AND mit ihnen
   verknüpft, und `Class AND subtree 1 OR subtree 2` würde Teilbaum 2 jeder Klasse geben. Die 1.203 Lese-Richtlinien
   des obigen Mitglieds werden zu 5, die Bedingung zu 13,7 KB. Die zuletzt
   zusammengeführten Listen werden gemerkt: Die Liste des aktuellen Benutzers kommt mit jedem Abruf, und ihr Vergleich
   mit demselben Array kostet nichts. `createPermissionCheckingSQL()` führt jede Liste zusammen, sodass auch Suche,
   Papierkorb, das Menü der Inhaltsstruktur und die Abrufe von Extensions profitieren.
3. **Nur, was Zugriff geben kann.** Ein Abruf eines Teilbaums oder von Kindern liefert Knoten unter bekannten Pfaden.
   `pruneLimitationList()` behält die Werte von `Subtree` und `User_Subtree`, die auf dem Weg zu diesen Pfaden oder
   darunter liegen, und lässt eine Richtlinie weg, von deren Werten keiner übrig bleibt. Bei einem Klassenfilter, der
   Klassen einschließt, bleibt eine Richtlinie nur für andere Klassen außen vor; bei einem, der Klassen ausschließt,
   eine Richtlinie nur für diese Klassen. In einem Teamroom schrumpft die Bedingung auf die Richtlinien dieses
   Teamrooms: 432 Byte. Die Pfade stammen aus der Bedingung, die der Abruf ohnehin bildet; ein Abruf von Kindern
   (Tiefe 1) fragt den Pfad seines Elternknotens in einer eigenen Abfrage ab, und nur dann, wenn die Liste 20 oder mehr
   Teilbaumwerte hat (`PERMISSION_PATH_LOOKUP_MIN_VALUES`).

Was das Ausdünnen nie tut:

- Es macht die Bedingung nie leer, was gar keine Bedingung bedeuten würde: Wenn keine Richtlinie übrig bleibt, lautet
  die Bedingung `0 = 1`, und der Abruf findet nichts, wie es die vollständige Bedingung täte.
- Es behält eine Richtlinie, deren `Subtree` außerhalb liegt, die aber eine Einschränkung `Node` hat: `Node` und
  `Subtree` sind Alternativen, der Knoten kann im abgerufenen Teilbaum liegen.
- Es behält jeden Wert, der kein Pfad aus Knoten-IDs ist, da SQL ihn mit `LIKE` vergleicht und ein `%` oder `_` darin
  mehr als seinen Text treffen könnte.
- Es lässt nie eine Richtlinie weg, deren Einschränkung `Class` keine Werte hat: Die Bedingung überspringt eine solche
  Einschränkung, sie grenzt also nichts ein. Klassen-IDs werden als Zahlen verglichen, wie die Datenbank sie vergleicht,
  und der Typ des Klassenfilters wird so gelesen, wie ihn die Abrufe lesen (`true` ist ein Einschlussfilter).
- Es macht nie aus einer Bedingung keine Bedingung. Eine Richtlinie, deren Einschränkungen ein Lesezugriff ignoriert
  (etwa nur `ParentClass`), fügt der Bedingung nichts hinzu und wurde auch zuvor aus dem OR weggelassen; wenn nach dem
  Weglassen der anderen nur solche Richtlinien übrig bleiben, lautet die Bedingung `0 = 1`, da die vollständige
  Bedingung keinen Knoten des Abrufs getroffen hätte.
- `subTreeMultiPaths()` bildet die Joins der Bedingung einmal für mehrere Teile, daher führt es nur zusammen und dünnt
  nicht aus.

4. **Kurze OR-Ketten.** Eine Datenbank liest eine OR-Kette aus n Teilen als einen Ausdruck mit n Ebenen Tiefe, und
   SQLite lehnt einen ab, der tiefer als 1.000 ist. Mit aktivierter Einstellung schreibt `permissionSQLOr()` eine Kette
   aus mehr als 100 Teilen (die Teilbäume einer zusammengeführten Richtlinie, die Richtlinien der Bedingung) in
   geklammerten Gruppen zu 100, und die Gruppen wiederum in Gruppen. Bis zu 100 Teilen ändert sich nichts am SQL; eine
   Million Teile bleiben unter 400 Ebenen.

## Messungen

Auf einer Kopie einer lokalen Installation (SQLite, PHP 8.4) mit 120 Teamrooms zu je 261 Knoten (ein Teamroom-Ordner
mit 10 Ordnern zu je 25 Artikeln), insgesamt 31.610 Knoten. Der Testbenutzer hat 5 Mitgliederrollen mit je 2 Lese-,
2 Bearbeiten-, einer Erstellen- und einer Entfernen-Richtlinie, zugewiesen für alle 120 Teamrooms; Lesen ergibt 1.203
Richtlinien. Die Zeiten sind Mediane aus 7 Läufen.

Zugriffsprüfungen für ein Objekt:

| Prüfung | Vorher | Jetzt |
|---|---|---|
| `checkAccess( 'read' )`, Artikel im 1. Teamroom | 1 Abfrage, 0,25 ms | 1 Abfrage, 0,30 ms |
| `checkAccess( 'read' )`, Artikel im 61. Teamroom | 61 Abfragen, 13,2 ms | 1 Abfrage, 0,65 ms |
| `checkAccess( 'read' )`, Artikel im 120. Teamroom | 120 Abfragen, 27,8 ms | 1 Abfrage, 1,0 ms |
| `canCreateClassList()`, ein Teamroom-Ordner | 601 Abfragen, 100 bis 150 ms | 2 Abfragen, etwa 2 ms |

Abrufe mit den 1.203 Lese-Richtlinien: Vorher schlug jeder davon unter SQLite fehl. Um mit der früheren Bedingung zu
vergleichen, liefen dieselben Abrufe mit den ersten 800 Richtlinien (3 der Mitgliederrollen für alle Teamrooms, die
vierte für 40 davon):

| Abruf | 800 Richtlinien, vorher | 800 Richtlinien, jetzt | 1.203 Richtlinien, jetzt |
|---|---|---|---|
| Kinder eines Ordners in einem Teamroom, 25 | 24,4 ms | 1,2 ms | 1,1 ms |
| Teamrooms unter der Inhaltswurzel | 30,9 ms | 6,4 ms | 7,5 ms |
| Artikel eines Teamrooms (Klassenfilter) | 93,3 ms | 65,6 ms | 65,4 ms |
| Ein Teamroom, die 20 neuesten | 98,1 ms | 98,6 ms | 72,8 ms |
| Der ganze Baum, die 20 neuesten | 1.429 ms | 870 ms | 855 ms |

Die Abrufe eines ganzen Teamrooms werden von der Abfrage selbst dominiert (SQLite verwendet für `LIKE` keinen Index),
nicht von der Berechtigungsbedingung. Der ganze Baum mit allen Teamrooms wertet weiterhin 240 Teilbaumbedingungen pro
Zeile aus; dort kann nichts weggelassen werden.

Was die Schritte kosten, für die 1.203 Richtlinien: Zusammenführen 2,5 ms beim ersten Mal und 0,0005 ms für jeden
Abruf danach, Ausdünnen für einen Teamroom 0,16 ms, Schreiben der Bedingung 3,0 ms für alle Richtlinien, 0,27 ms
zusammengeführt und 0,01 ms ausgedünnt, das Nachschlagen des Pfads bei einem Abruf von Kindern 0,03 ms.

Das sind lokale Zahlen ohne Netzwerk. Mit MySQL oder PostgreSQL auf einem eigenen Server wiegen die eingesparten
Abfragen schwerer, jede ein Roundtrip; wie der Planer der jeweiligen Datenbank eine lange OR-Bedingung behandelt, wurde
hier nicht gemessen.

## Auf dem Demonstrationsserver geprüft

Die Abrufe, Zugriffsprüfungen und Erstellen-Listen jedes Benutzers auf dem Demonstrationsserver (ein deaktivierter
Benutzer ausgenommen: Ein deaktivierter Benutzer ist bei jeder Anfrage anonym), der Lese-Richtlinien jeder Rolle für
sich und von sechs Wegwerf-Benutzern wurden mit dem Code vor der Änderung, mit der Änderung in der zuerst eingereichten
Fassung und mit der Änderung in ihrer jetzigen Fassung berechnet. Die Wegwerf-Benutzer decken jede Art von
Einschränkung ab: eine Mitgliederrolle aus 5 Lese-, 3 Erstellen- und 2 Bearbeiten-Richtlinien, zugewiesen für 120
Teilbäume (und ein Redakteur mit derselben, für den Browser), Mischungen aus Owner, Group, Section und Class, schlicht
zugewiesen, für eine Sektion, für Teilbäume und über die Benutzergruppe, `Node`, `Node` neben `Subtree`,
`ParentDepth`, `ParentClass`, `Language` und ein Status, 1.100 Lese-Richtlinien (10 Richtlinien für 110 Teilbäume),
eine Einschränkung mit einem Handler, eine ohne, eine Einschränkung `Class` ohne Werte und eine Lese-Richtlinie nur mit
einer Einschränkung `ParentClass`. Der Inhalt, an dem sie geprüft wurden, umfasst einen Artikel mit zwei Orten und
einen Artikel, der nie veröffentlicht wurde.

| | Vorher | Jetzt | Wie zuerst eingereicht |
|---|---|---|---|
| Abrufe (Liste, Zählung, Kalender, Suche, Schlüsselwörter): 3.294 | | gleich | 125 abweichend |
| `checkAccess()` Lesen, Bearbeiten, Entfernen, Verschieben, Erstellen: 28.224 | | gleich | gleich |
| `canCreateClassList()` von Objekten und Knoten: 2.712 | | gleich | gleich |

Die Abrufe des Benutzers mit 1.100 Richtlinien schlugen vorher unter SQLite fehl (154-mal); 140 davon wurden mit der
Vereinigung der Abrufe über Blöcke von je 400 Richtlinien verglichen, und die 3 Suchen und Schlüsselwortzählungen, die
der alte Code mit nichts beantwortete, sind jetzt konsistent mit den Baumabrufen dieses Benutzers. Die erste
Einreichung listete einem echten Benutzer und den Wegwerf-Benutzern Knoten auf, die sie nicht lesen dürfen (die
Teilbäume einer zusammengeführten Richtlinie ohne Klammern), und verbarg Knoten vor der Rolle mit einer Einschränkung
`Class` ohne Werte.

Zahlen auf dem Demonstrationsserver (SQLite, PHP 8.5, 330 Knoten), Median aus 5, Abfragen eines Aufrufs:

| Aufruf | Benutzer | Vorher | Jetzt |
|---|---|---|---|
| `checkAccess( 'read' )`, Artikel im 120. Teilbaum | Mitglied von 120 Teilbäumen | 359 Abfragen, 102,8 ms | 2 Abfragen, 1,8 ms |
| `checkAccess( 'edit' )`, derselbe Artikel | Mitglied von 120 Teilbäumen | 239 Abfragen, 47,0 ms | 1 Abfrage, 0,8 ms |
| `canCreateClassList()`, Ordner | Mitglied von 120 Teilbäumen | 363 Abfragen, 82,0 ms | 3 Abfragen, 3,6 ms |
| Kinder eines Knotens | Mitglied von 120 Teilbäumen | 7 Abfragen, 22,9 ms | 8 Abfragen, 1,7 ms |
| Ganzer Baum, 20 neueste | Mitglied von 120 Teilbäumen | 22,2 ms | 5,9 ms |
| Ganzer Baum, Anzahl der Artikel | Mitglied von 120 Teilbäumen | 21,2 ms | 4,9 ms |
| Suche | Mitglied von 120 Teilbäumen | 50,6 ms | 20,3 ms |
| Kinder eines Knotens | 1.100 Lese-Richtlinien | schlägt fehl | 2,0 ms |
| `checkAccess( 'read' )`, `canCreateClassList()`, Abrufe | anonym, Administrator | unverändert | unverändert |

Die zusätzliche Abfrage eines Abrufs von Kindern ist das Nachschlagen des Pfads seines Elternknotens.

## Grenzen

- Die Zugriffsprüfungen vergleichen die Richtlinien weiterhin eine nach der anderen; nur die Datenbankarbeit wird
  geteilt.
- Die Listen `IN ()` einer Richtlinie (`Class`, `Section`, `Node`) werden so geschrieben, wie die Richtlinie sie hat;
  das Zusammenführen verlängert sie nicht.
- `Owner` und `Group` in der Bedingung sind wie zuvor die des aktuellen Benutzers; eine `Limitation`, die einem Abruf
  für einen anderen Benutzer übergeben wird, wird wie jede andere Liste zusammengeführt und eingegrenzt.

## Fehlersuche

`debug.ini [GeneralCondition] kernel-content-treenode=enabled` schreibt für jeden Abruf, wie viele Richtlinien er
erhalten hat, wie viele zusammengeführt übrig blieben und wie viele in seiner Bedingung stehen, mit den Pfaden, auf die
er eingegrenzt wurde. Um mit der früheren Bedingung zu vergleichen, setzen Sie `PermissionSQLOptimization=disabled`.

## Der ursprüngliche Projekt-Patch

Ein älterer Projekt-Patch verfolgte dieselben Ziele. Er wurde nicht unverändert übernommen:

- Er ließ Richtlinien weg, bevor die Bedingung gebildet wurde, und gab eine leere Liste zurück, wenn keine übrig blieb.
  Eine leere Liste bedeutet keine Bedingung, sodass ein Benutzer ohne Zugriff auf einen Teamroom alles davon sah.
- Er ließ eine Richtlinie weg, deren `Subtree` außerhalb des abgerufenen Knotens lag, auch wenn die Richtlinie eine
  Einschränkung `Node` hatte, und verbarg so Knoten, die der Benutzer lesen darf.
- Er merkte sich die gekürzte Liste pro Anfrage nur anhand der ersten drei Ebenen des Pfads: Ein anderer Benutzer, eine
  andere Richtlinienliste oder ein Parameter `Limitation` eines Abrufs in derselben Anfrage bekam die Liste des ersten.
- Er behielt die Orte jedes Objekts für den Rest einer Anfrage `content/view` in einer globalen Variablen, die dort, wo
  Knoten entfernt werden, wieder abgeschaltet werden musste, und lieferte nach einem Verschieben in derselben Anfrage
  alte Orte.
- Er funktionierte nur mit einem `define()` in `config.php` und für Listen ab 10 Richtlinien.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `settings/site.ini` | `RoleSettings` | `PermissionSQLOptimization` | `enabled` | `disabled` verknüpft wie zuvor jede Lese-Richtlinie in einer einzigen Kette per OR in der Bedingung eines Abrufs. Die Orte in `checkAccess()` und `canCreateClassList()` werden in beiden Fällen einmal geladen, und die Teilbäume einer Richtlinie mit mehreren stehen in beiden Fällen in Klammern. |

## Für Autoren von Extensions

- `eZContentObjectTreeNode::createPermissionCheckingSQL()` nimmt einen optionalen vierten Parameter, den Abrufbereich
  von `permissionFetchScope()`. Ohne ihn wird die Liste zusammengeführt, aber nicht ausgedünnt.
- `createPathConditionAndNotEqParentSQLStrings()` gibt die geladenen Pfade über einen optionalen sechsten Parameter
  zurück.
- `classListFromPolicy()` nimmt die Orte des Objekts als optionalen dritten Parameter per Referenz; mit zwei Parametern
  aufgerufen, lädt es sie wie zuvor.
- `mergeLimitationList()`, `pruneLimitationList()` und `permissionSQLOr()` sind öffentlich und funktionieren ohne die
  Datenbank.

## Tests

- `eZContentPermissionSQLOptimizationTest` (ohne Datenbank): Zusammenführen (nur Richtlinien, die sich in
  `User_Subtree` unterscheiden, gleiche Teilbäume einmal, die gemerkte Liste), 600 Zuweisungen, die die Bedingung von
  drei Richtlinien ergeben, die Einstellung, Ausdünnen nach Pfad, nach einem `Node` neben einem `Subtree`, nach Werten,
  die kein Pfad sind, und nach Klassenfilter, und die Bedingung, die kein Knoten erfüllt, wenn nichts übrig bleibt;
  eine zusammengeführte Richtlinie, die ihre Klasse für jeden Teilbaum behält, eine Einschränkung `Class` ohne Werte,
  als Zahlen verglichene Klassen-IDs, der Klassenfiltertyp `true`, Richtlinien ohne Bedingung, die nach dem Ausdünnen
  unangetastet bleiben, OR-Ketten aus 100 Teilen, die wie zuvor geschrieben werden, und eine Bedingung aus 1.500
  Richtlinien und 2.500 Teilbäumen, die unter SQLite läuft (die frühere Kette wird dort abgelehnt).
- `eZContentPermissionSQLOptimizationLiveTest` (auf einer Installation): ein Benutzer, dessen Rolle für 40 Teilbäume
  zugewiesen ist. `checkAccess()` und `canCreateClassList()` brauchen höchstens 3 und 5 Abfragen (vorher 41 und mehr),
  und Abrufe von Teilbaum, Kindern, ganzem Baum, mit Klassenfilter und über mehrere Knoten sowie ihre Zählungen liefern
  mit aktivierter und deaktivierter Einstellung dieselben Knoten; PO-04 tut das für eine Richtlinie nur für Ordner und
  eine für eine leere Sektion, wo sich über ihre Klasse hinaus per OR verknüpfte Teilbäume zeigen würden.

## Verwandte Seiten

- [Einschränkungen von Inhaltsrichtlinien durch Extensions](handler-fuer-inhaltseinschraenkungen.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
