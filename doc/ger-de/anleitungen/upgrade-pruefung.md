# Die Upgrade-Prüfung: Dateien und Datenbank vor einem Upgrade

Diese Anleitung erklärt **Setup > Upgrade-Prüfung** (`/setup/systemupgrade`) der Administrationsoberfläche: was sie
vergleicht, was jeder Teil der Seite aussagt, wie jede Art von Befund zu lesen ist und wie Sie damit umgehen, und wie
Sie dieselbe Antwort in einer Shell erhalten.

Sie richtet sich an Administratoren, die kurz vor einem Upgrade stehen, und an alle, die wissen wollen, ob die Dateien
oder die Datenbank einer Installation vom Release abgewichen sind. Jede Zahl unten wurde am 6. Oktober 2026 auf dem
Demonstrationsserver am Code geprüft (`kernel/private/classes/views/setup/systemupgrade.php`,
`kernel/private/classes/expfileconsistencyreport.php`, `kernel/private/classes/expschemaconsistencyreport.php`), unter
Apache mit PHP-FPM und unter Velocity.

[Anleitungen](README.md) · Siehe auch: [Upgrade](aktualisieren.md), [Das Datei-Manifest](../funktionen/6.0/dateikonsistenzpruefung.md),
[Qualitätsprüfungen](../funktionen/6.0/qualitaetspruefungen.md)

## Kurz gesagt

- Die Seite hat zwei Prüfungen. **Überprüfe Dateikonsistenz** vergleicht jede in `share/filelist.md5` aufgeführte Datei
  (und in der `share/filelist.md5`, die eine aktive Extension selbst mitbringen kann) mit ihrer Prüfsumme. **Überprüfe
  Datenbankkonsistenz** vergleicht die Tabellen, Felder und Indizes der Datenbank mit den Schemadateien von Exponential
  und der aktiven Extensions.
- **Beide Prüfungen lesen nur.** Es wird keine Datei geschrieben und kein SQL ausgeführt. Das SQL, das die
  Datenbankprüfung zeigt, ist Text, den Sie lesen.
- Datei-Befunde sind gruppiert: **Geändert**, **Fehlt** und **Nicht lesbar** sind Probleme; **Nicht aufgeführt**,
  **Fehlerhafte Zeilen** und **Nicht in Reihenfolge** sind Hinweise, die einem Upgrade nicht im Weg stehen. Jede Gruppe
  sagt, was sie bedeutet und was zu tun ist.
- Datenbank-Befunde sind nach Tabelle gruppiert, jeweils mit dem SQL, das die Datenbank-Engine bräuchte. Unterschiede,
  für die die Engine kein SQL schreibt, sind **Engine-Hinweise** (zum Beispiel unter SQLite) und lassen die Prüfung
  nicht fehlschlagen.
- Jede Prüfung zeigt, wie lange sie gedauert hat. Jede kann heruntergeladen werden: die Dateien als CSV oder Text, die
  Datenbank als `.sql`-Datei.
- In einer Shell liefert `php bin/php/checkmanifest.php --all --extensions` dieselben Datei-Befunde wie die Seite, weil
  beide dieselbe Klasse verwenden.

## 1. Die Seite öffnen

In der Administrationsoberfläche: **Setup** > **Upgrade-Prüfung**, oder `/setup/systemupgrade` im Admin-Siteaccess.
Die Seite erfordert die Richtlinie `setup/setup`, wie jede Seite des Reiters Setup.

Sichern Sie vor dem Upgrade das Installationsverzeichnis und die Datenbank. Die Seite erinnert oben daran.

## 2. Was die Seite vor einer Prüfung zeigt

| Angabe | Was sie aussagt |
|---|---|
| Version | Die laufende Version von Exponential (`eZPublishSDK::version()`) |
| Dateien in share/filelist.md5 | Wie viele Dateien das Manifest des Releases aufführt. „share/filelist.md5 fehlt“ in Rot bedeutet, dass die Dateiprüfung nicht laufen kann |
| Manifest zuletzt committet / geschrieben | Wann sich das Manifest zuletzt geändert hat: das Datum seines letzten git-Commits in einem git-Checkout, sonst das Datum der Datei |
| Aktive Extensions mit eigenem Manifest | Wie viele aktive Extensions eine eigene `share/filelist.md5` mitbringen |
| Datenbank-Engine | `mysql`, `postgresql`, `sqlite` oder `mongo`: Die Schemaunterschiede hängen davon ab |

**Was die Dateiprüfung liest** (eingeklappt, ganz unten) führt das Manifest von Exponential mit seiner Anzahl Dateien,
gegebenenfalls seinen fehlerhaften Zeilen und seinen Daten auf, sowie jede aktive Extension, die geprüft wird: über
**ihr eigenes Manifest** (mit der Version in seinem Kopf, der Version der Extension und einer Warnung, wenn sie
voneinander abweichen oder wenn `files_count` im Kopf nicht der Anzahl der Zeilen entspricht) oder über **das Manifest
von Exponential** (die Extensions, die im Release enthalten sind: ezoe, ezjscore, ezformtoken, expservices). Aktive
Extensions ohne beides werden als **nicht geprüft** genannt.

## 3. Die Prüfung der Dateikonsistenz ausführen

Klicken Sie auf **Überprüfe Dateikonsistenz**. Während sie läuft, sagt die Seite das, und die Schaltflächen können
nicht zweimal gedrückt werden. Auf dem Demonstrationsserver liest sie mit warmem Datei-Cache etwa 8.600 Dateien in etwa
einer Sekunde (bei kaltem Cache oder ausgelastetem Server bis zu mehreren Sekunden); die Zeit wird beim Ergebnis
angezeigt.

Das Ergebnis beginnt mit einer Zeile:

- **Überprüfung der Dateikonsistenz ist in Ordnung.** (grün): Jede aufgeführte Datei stimmt überein. Hinweise, falls
  vorhanden, werden gezählt und unten aufgeführt.
- **Warnung, es ist nicht sicher ein Upgrade durchzuführen ohne die Änderungen in den folgende Dateien zu überprüfen**
  (gelb): Einige Dateien weichen ab. Die Zeile sagt, wie viele geändert sind, fehlen und nicht lesbar sind.
- Eine rote Zeile besagt, dass die Prüfung nicht laufen konnte: siehe [Wenn die Prüfung nicht laufen kann](#wenn-die-prüfung-nicht-laufen-kann).

Dann folgen:

- **Zahlen**: übereinstimmend, geändert, fehlend, nicht lesbar (nur wenn es welche gibt), nicht aufgeführt und
  aufzuräumende Manifestzeilen.
- **Gelesene Manifeste**: eine Zeile pro Manifest mit seinen Dateien, wie viele davon anzusehen sind, wann es
  geschrieben wurde und ob darin fehlende Dateien gesucht werden konnten (nur in einem git-Checkout).
- **Datei suchen**, **Wo** und **Anzeigen** (mit JavaScript): die Pfade durchsuchen, die Befunde des Kernels oder einer
  Extension oder eines Zustands behalten. Die Zahl unter den Chips sagt, wie viele angezeigt werden.
- Eine einklappbare Gruppe pro Zustand, jeweils mit **Was es bedeutet und was zu tun ist** und einer Tabelle: die
  Datei, das Manifest und seine Zeile sowie die Details (die ersten zehn Zeichen der aufgeführten und der aktuellen
  Prüfsumme; beim Darüberfahren mit der Maus alle).

Die Seite führt höchstens 2.000 Befunde auf. Gibt es mehr (zum Beispiel bei einem Manifest eines anderen Releases),
sagt die Gruppe das, und der Download enthält alle.

## 4. Jeder Befund und was zu tun ist

| Befund | Was er bedeutet | Was zu tun ist |
|---|---|---|
| **Geändert** | Die Datei weicht von der ab, die das Release ausgeliefert hat. | Wenn Sie sie absichtlich geändert haben, verlagern Sie die Änderung in einen Override, ein Design oder eine eigene Extension: Ein Upgrade ersetzt die Datei. Führen Sie sie dann in die neue Version zusammen. Wenn niemand sie absichtlich geändert hat, stellen Sie sie aus dem Release wieder her. Ein Maintainer, der sie im Quellcode geändert hat, aktualisiert die Zeile mit `php bin/php/checkmanifest.php --fix` und committet das Manifest mit der Änderung. |
| **Fehlt** | Die Datei ist aufgeführt, aber nicht vorhanden. | Kopieren Sie sie aus dem Release zurück. Ein Maintainer, der sie absichtlich entfernt hat, streicht die Zeile mit `php bin/php/checkmanifest.php --fix`. |
| **Nicht lesbar** | Die Datei ist vorhanden, konnte aber nicht gelesen werden: ein Verzeichnis, wo eine Datei hingehört, oder Berechtigungen, die den Webserver aussperren. | Prüfen Sie Eigentümer und Modus (`ls -l <file>`); der Benutzer des Webservers muss sie lesen können. |
| **Nicht aufgeführt** (Hinweis) | Git verfolgt die Datei, aber das Manifest führt sie nicht auf, daher kann die Prüfung nicht sagen, ob sie sich geändert hat. Wird nur in einem git-Checkout gesucht. | Auf einer installierten Site nichts. Ein Maintainer fügt sie mit `php bin/php/checkmanifest.php --fix` in dem Release hinzu, das die Datei hinzufügt. |
| **Fehlerhafte Zeilen** (Hinweis) | Eine Manifestzeile ist nicht „32 Hexadezimalziffern, zwei Leerzeichen, ein Pfad“, nennt einen Pfad außerhalb der Installation (`../`, `/`) oder führt eine Datei ein zweites Mal auf. Die Zeile wird übersprungen. | Nehmen Sie das Manifest aus dem Release, oder schreiben Sie es mit `bash bin/shell/generatefilelist.sh` neu. |
| **Nicht in Reihenfolge** (Hinweis) | Das Manifest von Exponential wird sortiert gehalten, damit sich seine Änderungen gut prüfen lassen; diese Zeile gehört vor die Zeile darüber. | `bash bin/shell/generatefilelist.sh` schreibt es sortiert. Auf einer installierten Site ist nichts zu tun. |

In einem Entwicklungs-Checkout mit laufender Arbeit sind geänderte Dateien zu erwarten: Es sind die Dateien, an denen
gearbeitet wird. Ein Manifest mit Windows-Zeilenenden (CRLF) wird wie eines mit LF gelesen: Die Prüfung meldet nicht
mehr jede Datei als fehlend.

### Manifeste von Extensions

Eine eigenständig veröffentlichte Extension kann `extension/<name>/share/filelist.md5` mitbringen, das in jedem ihrer
Releases aktualisiert wird. Es kann mit einem Kopf beginnen (`name:`, `version:`, `files_count:`), den die Seite
anzeigt. Seine Befunde werden mit dem Namen der Extension in der Spalte Manifest und unter **Wo** aufgeführt. Eine dort
geänderte Datei wird im eigenen Release der Extension behoben, nicht im Manifest von Exponential.

## 5. Den Bericht herunterladen

Neben **Überprüfe Dateikonsistenz**:

- **CSV herunterladen**: eine Zeile pro Befund, Spalten `manifest`, `area`, `state`, `path`, `expected_md5`,
  `actual_md5`, `line`, `detail`, UTF-8 mit Byte Order Mark. Eine Zelle, die eine Tabellenkalkulation als Formel
  ausführen würde, beginnt mit einem Apostroph.
- **Text herunterladen**: eine Zusammenfassung und die Befunde in Worten, gruppiert nach Manifest und Zustand, im
  Wortlaut der Prüfung auf der Kommandozeile.

Beide führen die Prüfung erneut aus und senden das Ergebnis als Datei; die Seite bleibt, wie sie ist. Neben
**Überprüfe Datenbankkonsistenz** sendet **SQL herunterladen** das SQL jeder Tabelle unter einem Kommentar, der sagt,
was es ändert.

## 6. Die Prüfung der Datenbankkonsistenz ausführen

Klicken Sie auf **Überprüfe Datenbankkonsistenz**. Sie liest `share/db_schema.dba` und die `share/db_schema.dba` jeder
aktiven Extension, die eine hat, liest das Schema der Datenbank und vergleicht beides mit dem Schema-Handler der
Datenbank-Engine (`dbschema.ini [SchemaSettings]`). Sie liest außerdem die Schemadateien der Extensions, die installiert
und nicht aktiv sind (ihre `share/db_schema.dba` und die `CREATE TABLE`-Anweisungen ihrer `sql/<engine>/*.sql`), nur um
zu sagen, zu welcher von ihnen eine Tabelle der Datenbank gehört; mit diesen wird nichts verglichen. Auf dem
Demonstrationsserver (SQLite, 11 Schemadateien) dauert das etwa 0,1 Sekunden.

Die Zahlen zählen **Tabellen mit zu prüfendem SQL**, fehlende Tabellen, Tabellen nicht im Schema, geänderte Tabellen,
Tabellen nicht aktiver Extensions und Engine-Hinweise. Dann folgt eine einklappbare Karte pro Tabelle mit dem, was abweicht (fehlende, zusätzliche und geänderte Felder und Indizes, mit der
ausgelieferten und der aktuellen Definition), und dem SQL für diese Tabelle:

| Art | Was sie bedeutet | Was zu tun ist |
|---|---|---|
| **Fehlende Tabelle** | Wird mit Exponential oder einer aktiven Extension ausgeliefert, ist aber nicht in der Datenbank. Die Funktion, die sie verwendet, schlägt fehl, bis sie vorhanden ist. | Führen Sie nach einer Sicherung ihre CREATE-Anweisung aus. |
| **Tabelle nicht im Schema** | In der Datenbank, aber in keinem Schema, nicht einmal in dem einer installierten, nicht aktiven Extension: meist eine Tabelle einer eigenen Extension ohne Schemadatei oder einer entfernten Extension. | Lassen Sie sie, es sei denn, Sie wissen, dass ihre Daten nicht mehr gebraucht werden. Die DROP-Anweisung wird nur der Vollständigkeit halber gezeigt und ist mit **Entfernt etwas** markiert. |
| **Tabelle einer nicht aktiven Extension** | In der Datenbank und in keinem aktiven Schema, aber das Schema einer installierten, nicht aktiven Extension nennt sie, zum Beispiel `ezfind_elevate_configuration` von ezfind (die Elevate-Konfiguration). Die Karte sagt „gehört zu ezfind (nicht aktiv)“. | Aktivieren Sie die Extension oder lassen Sie die Tabelle; sie zu löschen entfernt die Daten der Extension. Für sie wird kein SQL geschrieben: keine DROP-Anweisung auf der Karte, in **Alle Anweisungen** oder im Download, und sie lässt die Prüfung nicht fehlschlagen. |
| **Geänderte Tabelle** | Felder oder Indizes weichen von der ausgelieferten Definition ab. | Lesen Sie jede Anweisung und führen Sie nach einer Sicherung die aus, denen Sie zustimmen. Anweisungen, die ein Feld oder einen Index entfernen oder ändern, sind mit **Entfernt etwas** markiert: Sie können eigene Daten entfernen. |
| **Engine-Hinweise** | Der Schema-Handler der Engine schreibt für den Unterschied kein SQL. Unter SQLite legt die Prüfung zuerst beiseite, was SQLite nicht unterscheiden kann (`text` und `longtext`, die Anzeigebreite eines `auto_increment`-Schlüssels, ein ausgelassener oder als false geschriebener Standardwert), sodass eine Tabelle so übereinstimmt, wie die SQLite-Schemadateien sie anlegen; was übrig bleibt und weiterhin kein SQL hat, ist ein Engine-Hinweis. | Nichts. Sie werden in einer eingeklappten Gruppe aufgeführt und lassen die Prüfung nicht fehlschlagen. |

**Alle Anweisungen** (eingeklappt) enthält das gesamte SQL als einen Block, so wie die Seite es früher zeigte. Die
Seite führt nichts davon aus.

Unter MongoDB vergleicht die Prüfung stattdessen Collections: fehlende Collections, gruppiert nach Funktion,
Collections nicht im Schema und den `mongosh`-Befehl, der die fehlenden anlegt.

## 7. Dasselbe in einer Shell

```bash
php bin/php/checkmanifest.php --all                 # the manifest of Exponential; exit 0 when nothing is modified or missing
php bin/php/checkmanifest.php --all --extensions    # also every extension directory with its own manifest
php bin/php/checkmanifest.php --all --extensions --csv > report.csv   # the CSV of the download, without the byte order mark
```

Der Befehl braucht keinen Kernel, keine Einstellungen und keine Datenbank. Er liest die Manifeste mit derselben Klasse
wie die Seite (`expFileConsistencyReport`), daher sind die Befunde gleich, wenn beide denselben Baum betrachten. Zwei
Unterschiede im Umfang: `--extensions` liest jedes Extension-Verzeichnis, das ein Manifest hat, ob aktiv oder nicht,
während die Seite die aktiven liest; und der Befehl gibt Hinweise als `warning:`-Zeilen und Probleme als
`error:`-Zeilen aus. `--fix` schreibt das Manifest von Exponential neu (nur für Maintainer; die Seite schreibt es nie).
`--staged` ist das, was der git-Pre-Commit-Hook ausführt.

## Wenn die Prüfung nicht laufen kann

- **„Die Datei share/filelist.md5 existiert nicht.“** Die Installation hat kein Manifest. Kopieren Sie
  `share/filelist.md5` aus dem Release, das diese Installation ausführt (dieselbe Version wie auf der Seite), in das
  Verzeichnis, das der Webserver ausliefert.
- **„... kann nicht gelesen werden.“** Der Benutzer des Webservers darf das Manifest nicht lesen: Prüfen Sie seinen
  Eigentümer und Modus.
- **„... führt keine Dateien auf.“** Das Manifest ist leer oder hat keine gültige Zeile: Nehmen Sie es aus dem Release.
- **„Das Datenbankschema konnte nicht gelesen werden.“** Für die Engine gibt es in `dbschema.ini [SchemaSettings]`
  keinen Schema-Handler, oder die Datenbank hat nicht geantwortet.

## Sicherheit

- Beide Prüfungen und alle Downloads lesen nur. Die Seite hat keine Schaltfläche, die eine Datei schreibt oder die
  Datenbank ändert.
- Jede Schaltfläche sendet das Formular mit dem Formular-Token von ezformtoken; die Seite leitet nicht weiter.
- Jeder Lauf und jeder Download wird als `system.upgrade.run` in das Audit-Protokoll geschrieben, mit der Prüfung, dem
  Ergebnis, der Anzahl der Unterschiede und bei einem Download seinem Format (siehe [das Audit](../bc/6.0/audit.md)).
- Pfade, Prüfsummen, Tabellennamen und SQL werden auf der Seite escapt ausgegeben.

## Siehe auch

- [Das Datei-Manifest und wie ein Release es schreibt](../funktionen/6.0/dateikonsistenzpruefung.md)
- [Upgrade](aktualisieren.md)
- [Qualitätsprüfungen](../funktionen/6.0/qualitaetspruefungen.md): `checkmanifest --staged` im Pre-Commit-Hook
- [Datenbankunterstützung](../hilfe/datenbankunterstuetzung.md): die unterstützten Datenbanken in ihrer offiziellen Reihenfolge
  und welche davon die Datenbankprüfung abdeckt
- Changelog: [6.0.15](../../changelogs/6.0/6.0.15.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
