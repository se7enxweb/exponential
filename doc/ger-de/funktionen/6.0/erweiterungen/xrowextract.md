# xrowextract: Inhalte als Dateien exportieren, importieren und verschieben

Diese Seite richtet sich an Redakteure und Administratoren, die Inhalte in einer Tabellenkalkulation, in einem anderen
System oder in einer Sicherung benötigen, und an alle, die solche Dateien wieder als Inhalte einlesen. `xrowextract` ist
der Tab **Export** des Admin. Er wandelt Inhalte in Dateien um und liest Dateien wieder als Inhalte ein.

Ab 2.5.0 (September 2026) ist es von einer einzelnen Seite „eine Klasse nach CSV" zu einem vollständigen Werkzeug für
den Datenaustausch gewachsen: Vorschauen, Filter, gespeicherte Vorlagen, Hintergrund-Jobs, Importe jeder Größe,
Inhaltspakete (`.ezpkg`), geplante Läufe und die Auslieferung an SFTP, S3 und andere Ziele.

Öffnen Sie es über **Export** im oberen Menü des Admin (`/xrowextract/csv`). Alles auf dieser Seite steht auch auf der
Kommandozeile zur Verfügung, siehe [Kommandozeile](#kommandozeile). Die vollständige Referenz (Einstellungen,
Richtlinien, Tabellen, Klassen) steht in der [Spezifikation von xrowextract](../../../spezifikationen/6.0/xrowextract.md);
die Geschichte der Arbeit steht in der [Chronik von xrowextract](../../../geschichte/erweiterungen/xrowextract.md) und in den
[Versionshinweisen](../../../../changelogs/extensions/xrowextract.md).

## Schnellstart: Artikel in eine Tabellenkalkulation

1. Öffnen Sie **Export > One class of content export**.
2. Wählen Sie die Klasse (zum Beispiel `ng_article`) und den Startknoten (zum Beispiel Knoten 2).
3. Klicken Sie auf **Preview**, um die ersten Zeilen so zu sehen, wie eine Tabellenkalkulation sie anzeigen wird.
4. Laden Sie die CSV-Datei herunter und öffnen Sie sie in Ihrem Tabellenkalkulationsprogramm.

Dasselbe auf der Kommandozeile, im Wurzelverzeichnis der Installation:

```bash
php extension/xrowextract/bin/php/csv.php --class=ng_article --node=2 --limit=20 --output=articles.csv
```

## Was Sie jetzt tun können, was vor 2.5 nicht ging

| Aufgabe | Wo | Seit |
|---|---|---|
| Eine Klasse als CSV, JSON oder XML exportieren, unterhalb eines Knotens oder über die gesamte Website | **One class of content export** | CSV seit 1.0; JSON, XML und Geltungsbereich gesamte Website 2.5.0 |
| Den Export vor dem Herunterladen als Tabelle sehen | **Preview** auf derselben Seite | 2.5.0 |
| Jede Klasse unterhalb ausgewählter Knoten in einem ZIP- oder TAR-Archiv exportieren, eine Datei pro Klasse | **Multi class of content export** (`/xrowextract/archive`) | 2.5.0 |
| Inhalte in mehreren Sprachen exportieren, eine Zeile pro Übersetzung | Karte Languages | 2.5.0 |
| Nach Datum, Sektion, Zustand, Sichtbarkeit, Name, Attribut, Eigentümer, Tiefe und mehr filtern, verknüpft mit und/oder | Karte Filters | 2.5.0 |
| Einen Export als benannte, wiederverwendbare Vorlage speichern, mit `{placeholders}` | Karte Presets (41 mitgeliefert, nach Zielgruppe gruppiert) | 2.5.3 |
| Einen großen Export im Hintergrund ausführen und von einer Jobs-Seite herunterladen | **Run in the background**, Tab **Jobs** | 2.5.0 |
| Eine CSV-, JSON- oder XML-Datei in Inhaltsobjekte importieren: Spalten zuordnen, Probelauf, anwenden | **Import content file** | 2.5.0 |
| Dateien jeder Größe hochladen (in Teilen, fortsetzbar) und Zeile für Zeile streamen | Importseite | 2.5.3 |
| Inhaltspakete (`.ezpkg`) untersuchen, vergleichen, installieren und exportieren | **Import content package** (`/xrowextract/package`) | 2.5.3 |
| Exporte und Importe nach Zeitplan ausführen und die Datei an SFTP, FTP/FTPS, einen NAS-Ordner, S3, WebDAV oder einen HTTP-Endpunkt ausliefern | **Schedules**, Ziele und Verlauf | 2.5.3 |
| Prüfen, was dieser Server für das Werkzeug leisten kann und was nicht | Hinweis auf jeder Seite, `ext:xrowextract:requirements` | 2.5.4 |

## Die Tabs

Nach 2.5.3 lauten die Tabs: **One class of content export**, **Multi class of
content export**, **Import content file**, **Import content package**,
**Schedules** und **Jobs**. (Davor hießen die Tabs für Import und Pakete
*Import* und *Package*; die Ansichten und Richtlinien behielten ihre Namen, nur
die Beschriftungen änderten sich.)

### One class of content export

Wählen Sie eine Klasse, einen Startknoten und einen Geltungsbereich, und laden Sie
dann herunter. Die Seite ist aus Karten aufgebaut:

* **Scope**: unterhalb eines Knotens (der Knoten wird gespeichert) oder jedes Objekt
  der Klasse auf der gesamten Website. Eine numerische Tiefe umfasst genau, höchstens
  oder mindestens so viele Ebenen unterhalb des Knotens.
* **Columns**: die Attribute der Klasse und ein Katalog besonderer Spalten
  (Objekt-ID, Remote-ID, Hauptknoten, Elternknoten, URL-Alias, Datumswerte und bei
  Benutzern Login und E-Mail-Adresse). Spaltensätze, das Format jedes Attributs und
  eine Spalte `xrowmetadata` sind enthalten. Die Seite nennt den Datentyp und die
  Metainformationen jeder Spalte.
* **Languages**: Anzahl pro Sprache; alle oder einige; eine Zeile pro Übersetzung.
* **Filters**: Schnellfilter für Datumsbereich, Sektion, Zustand, Sichtbarkeit und
  Name sowie mehrere Bedingungen, jeweils auf ein Klassenattribut oder ein Kernel-Feld
  (`name`, `published`, `modified`, `modified_subnode`, `section`, `owner` per ID oder
  Login, `priority`, `depth`, `class_identifier`, `class_name`, `node_id`,
  `contentobject_id`, `path`, `state`). Operatoren: enthält, beginnt mit, ist, ist
  nicht, größer/kleiner (auch `>=`, `<=`), ist leer / nicht leer, in Liste / nicht in
  Liste, zwischen / nicht zwischen, passt / passt nicht auf ein Muster mit `*`. Jede
  Zeile kann mit „not" umgekehrt werden. Zeilen werden mit **all (and)** oder **any (or)** verknüpft.

  Der Attributfilter des Kernels hat eine einzige Verknüpfung für den gesamten Filter,
  sodass „any" jeden aktiven Filter erweitert, nicht nur die Bedingungszeilen, wenn
  mehr als einer gesetzt ist.
* **Extended filters**: Jeder in `extendedattributefilter.ini` registrierte Filter
  (eztags, `XrowExtractHasChildren`, `XrowExtractRelation`,
  `XrowExtractUserStatus`) kann mit dem Sprachfilter verkettet werden; seine
  Parameter sind ein JSON-Objekt, zum Beispiel `{"has_children": true}` oder
  `{"object_id": 123, "reverse": true}`.
* **Named fetch**: Wendet einen Alias aus `fetchalias.ini` an (Modul `content`,
  Funktion `tree`, `list`, `tree_count` oder `list_count`). Seine Einstellungen für
  Knoten, Klasse, Sortierung, Tiefe, Limit/Offset und Hauptplatzierungen werden in das
  Formular übernommen; ein von ihm deklarierter Eintrag `Parameter[]` wird über
  **Parameters** ausgefüllt, geschrieben als `key=value,key=value`. Das Feld **Fetch
  parameters** am Ende der Karte zeigt die aufgelösten Filter als den wörtlichen Aufruf
  `fetch('content','tree', hash(...))`, den ein Template ausführen würde.
* **Output**: CSV (Trennzeichen, Zeilenenden, Anführungszeichen), JSON oder XML.
  Zellen, die mit `=`, `+`, `-` oder `@` beginnen, erhalten ein führendes `'`, damit
  eine Tabellenkalkulation sie als Text anzeigt und nicht als Formeln ausführt
  (`csv.ini [General] NeutralizeFormulas`).

**Preview** zeigt den Export so, wie eine Tabellenkalkulation ihn anzeigen wird, mit
demselben Code, der die Datei erzeugt, und liest ihn mit dem gewählten Trennzeichen
und den gewählten Anführungszeichen wieder ein. Die Vorschau zeigt die ersten 10, 25,
50 oder 100 Zeilen (pro Benutzer gespeichert), wie viele Zeilen und Spalten die Datei
haben wird, wie voll jede Spalte ist, Zeilen, deren Spalten sich verschieben würden,
und Zellen, die als Formeln neutralisiert wurden. Zeilen lassen sich filtern und
sortieren, Zellen aufklappen oder umbrechen, und die angezeigten Zeilen tabgetrennt
kopieren. Nichts davon ändert den Download.

### Multi class of content export (Website-Archiv)

Die Inhalte unterhalb eines oder mehrerer Knoten als ein Archiv: eine Datei für jede
Klasse (Objekt-ID, Remote-ID, Hauptknoten, Elternknoten, URL-Alias, Datumswerte, dann
jedes Attribut der Klasse) sowie `manifest.json` und `README.txt`. Vorgefertigte
Knotensätze (Inhalte und Medien, Inhalte, Medien, Benutzer, alles), einzelne Knoten,
die aus einer Liste mit Anzahlen oder von der Browse-Seite hinzugefügt werden, und
an- oder abgewählte Klassen (alle Klassen mit Inhalten sind angewählt).

* Formate: ZIP, TAR.GZ, TAR.BZ2, TAR.XZ; 7-Zip und RAR, wenn die Programme `7z`
  oder `rar` installiert sind.
* Objekte werden mit dem Lesezugriff des Benutzers an ihrer Hauptplatzierung gelesen
  und einmal geschrieben.
* Passwort-Hashes werden nie in einem Archiv exportiert, es sei denn, ein Benutzer
  mit der Richtlinie `xrowextract/password_hash` fordert sie an (siehe
  [Passwort-Hashes](#passwort-hashes)).
* Das Archiv wird in einen privaten Ordner im Cache-Verzeichnis geschrieben und nach
  dem Download entfernt.
* Seit 2.5.0 kann das Archiv JSON oder XML statt CSV verwenden, einen Satz **Sites**
  (die Wurzeln der Websites, optional gesetzt in `export.ini [SiteArchive]`), Sprachen
  und eine Spaltenauswahl pro Klasse.

### Presets

Eine Vorlage (Preset) ist eine vollständige, benannte Exportdefinition: Geltungsbereich,
Knoten (gespeichert per ID und Remote-ID, sodass er die Neunummerierung bei einer
Neuinstallation übersteht), Klasse, Spalten, Sprachen, jeder Filter, Sortierung und
Ausgabeeinstellung. **Save as preset** hält das Formular in seinem aktuellen Zustand
fest. Die Auswahl lädt eine Vorlage an Ort und Stelle zurück, oder **Run in the
background** löst sie auf und startet sie als Job, ohne sie vorher zu laden.

* Eine Vorlage kann Platzhalter `{placeholder}` enthalten (ein Knoten, ein Datum, eine
  Klasse), die Sie über **Parameters** ausfüllen, genauso wie den `Parameter[]` eines
  Named Fetch.
* Eine Vorlage kann eine andere Vorlage (`user:<id>`) oder einen Named Fetch
  (`alias:<name>` oder `alias:<name>:<siteaccess>`) *erweitern* und einzelne Schlüssel
  überschreiben.
* Zwei Ebenen: **Ihre eigenen Vorlagen** (privat oder mit jedem Export-Benutzer geteilt;
  bearbeitbar durch ihren Eigentümer oder einen Benutzer mit `xrowextract/all_jobs`) und
  **Website-Vorlagen**, die als Blöcke `[Preset_<id>]` in `xrowextract.ini` ausgeliefert
  werden. 41 Website-Vorlagen werden mit der Extension ausgeliefert, nach Zielgruppe
  gruppiert (Site, Editors, Developers, Partners, Users, Maintenance). Die Aufklappanzeige
  **INI** in jeder Zeile zeigt den Block, den Sie in Ihre eigenen Einstellungen kopieren
  können.
* Ein aus einer Vorlage gestarteter Hintergrund-Job hält fest, aus welcher; die
  Jobs-Seite zeigt sie neben dem Job.

Beispiel einer Website-Vorlage, in `settings/override/xrowextract.ini.append.php` oder
in einer eigenen Extension:

```ini
[Preset_hidden_news]
Name=Hidden news
Description=Everything hidden below a node, newest changes first.
Audience=Maintenance
View=csv
Definition={"scope":"tree","subtree":"{node}","sort_field":"modified","sort_ascending":false,"filters":{"visibility":"hidden"}}
Placeholders={"node":{"default":"2"}}
```

### Typisiertes Spaltenmanifest

Jeder Export trägt ein Manifest: pro Spalte ihr Schlüssel in der Datei, ID, Name,
Datentyp, gewähltes Format, Sprache und das Importziel, auf das sie zurück abgebildet
wird; die Metadaten der Klasse (Pflichtfeld, übersetzbar, Auswahloptionen,
Relationsziele, Bezeichner, Remote-ID, eine Versionssignatur); Zeilenzahl, Dateigröße
und SHA-256; Exportzeit, Filter, Vorlage, Zeitplan, Website und Siteaccess.

Es wird als `<file>.manifest.json` neben die Datei eines Hintergrund-Jobs oder der
Kommandozeile geschrieben, neben jede Klassendatei innerhalb eines Website-Archivs und
auf der Seite **One class** als **Manifest only** oder **Download with manifest (.zip)**
angeboten. XML-Dateien tragen es in einem Element `<manifest>` nach `<columns>`,
JSON-Dateien als Umschlag `{"manifest": ..., "rows": [...], "summary": ...}`. Setzen Sie
`csv.ini [Manifest] EmbedInJSON=disabled` oder `EmbedInXML=disabled`, um die
eingebettete Kopie abzuschalten; die Begleitdatei existiert weiterhin. Der Importer
ordnet jede Spalte, die das Manifest beschreibt, exakt zu; eine Datei ohne Manifest wird
wie bisher importiert.

### Import content file

Die andere Richtung. Laden Sie eine CSV-, JSON- oder XML-Datei hoch (oder nehmen Sie
**Try a sample**, das für jede Klasse funktioniert), wählen Sie die Klasse, den
Elternknoten und die Sprache, ordnen Sie die Spalten zu und klicken Sie auf **Preview**.
Die Vorschau sagt, in welche Klasse die Zeilen gehen, wohin, in welcher Sprache, und
benennt jede Änderung; sie ist ein Probelauf und schreibt nichts. **Apply** führt sie
aus.

* Uploads jeder Größe verwenden den **Upload in Teilen** mit Fortsetzung; die Datei wird
  Zeile für Zeile gestreamt, sodass der Speicherbedarf nicht mit der Datei wächst.
* Ein großer Import wird **als Hintergrund-Job eingereiht**, der seine Größe und den
  Fortsetzungspunkt meldet; ein Steuerelement **Resume** setzt einen unterbrochenen fort.
* Die Referenz der Dateiformate auf der Seite listet jede Spalte und jedes Element auf,
  das der Importer für CSV, JSON und XML versteht.
* Eine importierte Datei-URL muss Schema, Host und Port dieser Website haben.

### Import content package

Ein Inhaltspaket (`.ezpkg`) ist das eigene Paketformat des Kernels: Klassen, Objekte
und Dateien in einem Archiv. Der Tab **Import content package**:

* lädt ein Paket hoch (in Teilen) oder wählt eines aus dem Paket-Repository;
* **untersucht** es, ohne etwas zu schreiben: seine Klassen, Objekte, eine Prüfung der
  Datentypen und einen seitenweise blätternden **Inhaltsbrowser** für jede enthaltene
  Datei;
* **vergleicht** es mit dieser Website (was eine Installation anlegen oder ändern würde,
  bis hinunter zu den Feldern) oder mit einem anderen Paket;
* **installiert** es als Hintergrund-Job mit Live-Fortschritt und Live-Protokoll auf der
  Jobs-Seite, der abgebrochen werden kann. Der Job gibt an, wie viele Elemente angelegt
  wurden, wie viele bereits existierten und was getan wurde;
* führt einen **Installationsverlauf** mit **Export these again** und **Open in Import**;
* erstellt **Beispiel- und Vorlagenpakete** nur lesend aus vorhandenen Inhalten;
* ist auch ein **Format** der Seite Import file und ein **Exportformat** der Seiten
  One class und Multi class (**Export as package**).

Ein Paketarchiv wird nur angenommen, wenn jeder Eintrag eine einfache Datei oder ein
einfacher Ordner ist, und eine aus dem Inhaltsbrowser geöffnete Datei kann kein Skript
unter dem Origin des Admin ausführen.

### Zeitpläne, Ziele und Verlauf

**Schedules** führt eine gespeicherte Vorlage, ein Website-Archiv, ein **Export as
package** oder einen Import aus einem lokalen Ordner oder einem Ziel aus, stündlich,
täglich, wöchentlich oder monatlich oder nach einem Cron-Ausdruck mit fünf Feldern,
vollständig oder als Delta (nur Änderungen seit dem letzten erfolgreichen Lauf). Importe
machen immer zuerst einen Probelauf und werden nur angewendet, wenn dieser keine Fehler
gefunden hat. Ein Zeitplan, der auf etwas verweist, das nicht mehr existiert, wird mit
einer Warnung übersprungen.

Starten Sie die fälligen Zeitpläne mit dem Cronjob-Teil oder mit dem System-Cron:

```bash
php runcronjobs.php xrowextract
```

Die Seite zeigt vorgefertigte Crontab-Zeilen (alle `CronjobEveryMinutes`, Standard 5).

**Ziele** (Destinations) sind die Orte, an die die Datei ausgeliefert wird:

| Typ | Hinweise |
|---|---|
| SFTP | OpenSSH-Client des Systems; Schlüssel oder Passwort; dem Host-Schlüssel wird ausdrücklich vertraut |
| FTP / FTPS | |
| Lokaler oder NAS-Ordner | nur unterhalb von `xrowextract.ini [Destinations] LocalPathRoots[]` |
| S3-kompatibler Speicher | Signaturversion 4 |
| WebDAV | |
| HTTP POST | optional signierter Webhook (siehe unten) |

Zugangsdaten werden mit libsodium verschlüsselt; die Schlüsseldatei wird bei der ersten
Verwendung mit dem Modus 0600 unter `settings/override/xrowextract-secrets.key` erzeugt.
Geht sie verloren, sind gespeicherte Geheimnisse nicht mehr lesbar; geben Sie sie erneut
ein. Committen Sie sie niemals.

**Verlauf** (History) listet jeden Lauf auf, mit wer, was, Zeilen, Größe, Prüfsumme,
Auslieferung und Warnungen; mit Filtern und Seiten. Fehlgeschlagene geplante Läufe
erscheinen als rotes Abzeichen auf dem Tab Jobs, bis sie als gesehen markiert werden.
Benachrichtigungen: E-Mail bei einem Fehler (immer an den Eigentümer), bei Erfolg
(optional) und ein Webhook. Wenn
`xrowextract.ini [Notifications] WebhookSecret` gesetzt ist, trägt der Webhook
`X-Xrowextract-Signature: sha256=<HMAC-SHA256 of "<timestamp>.<sha256 of the body>">`.

### Jobs

Alles, was im Hintergrund läuft (Exporte, Importe, Paketinstallationen, Zeitpläne), wird
unter **Jobs** aufgeführt, mit Kacheln für die Gesamtzahl sowie abgeschlossene, laufende,
eingereihte und fehlgeschlagene Jobs, dem Benutzer, der den jeweiligen Job gestartet hat,
als verlinkte Blase, einem Fortschrittsbalken und einem Protokoll mit einer kurzen
Zeitleiste des Fortschritts. Ein eingereihter oder laufender Job kann abgebrochen werden.
Ein Job, dessen ausführender Prozess stirbt, wird als fehlgeschlagen markiert, statt auf
„running" stehen zu bleiben. Ohne die Richtlinie `xrowextract/all_jobs` sieht ein
Benutzer nur seine eigenen Jobs. Abgeschlossene Jobs werden
`csv.ini [Jobs] RetentionDays` Tage lang aufbewahrt (Standard 7).

## Passwort-Hashes

Für eine Migration in ein anderes System können der Passwort-Hash und der Hash-Typ von
Benutzerkonten als besondere Spalten exportiert werden. Nur Benutzer mit der Richtlinie
`xrowextract/password_hash` sehen die Option (Administratoren haben sie), und
`csv.ini [General] AllowPasswordHashExport=disabled` schaltet sie für alle ab.
Der Standard in den mitgelieferten Einstellungen ist `enabled`.

## Kommandozeile

Jede Funktion hat einen Befehl. Führen Sie die Befehle im Wurzelverzeichnis der
Installation aus, entweder als `php extension/xrowextract/bin/php/<name>.php` oder über
die Konsole.

| Befehl | Was er tut |
|---|---|
| `ext:xrowextract:csv` | Eine Klasse als Datei (alle Optionen der Seite, `--preview=10`, `--list-classes`, `--list-columns`, `--preset`, `--list-presets`, `--show-preset`, `--where`, `--fetch-alias`, `--alias-param`) |
| `ext:xrowextract:archive` | Ein Website-Archiv |
| `ext:xrowextract:import` | Ein Import jeder Größe, gestreamt; kann als Hintergrund-Job laufen; `--manifest`, `--no-manifest` |
| `ext:xrowextract:package` | Inhaltspakete: untersuchen, `--install`, `--export`, `--compare`, `--template`, `--clean`, `--keep` |
| `ext:xrowextract:schedule` | `--list`, `--run`, `--enable`, `--disable`, `--cron`, `--crontab`, `--create` |
| `ext:xrowextract:destination` | `--list`, `--test`, `--send`, `--trust-host-key` |
| `ext:xrowextract:history` | Der Exportverlauf |
| `ext:xrowextract:job` | Führt Hintergrund-Exportjobs aus, listet sie auf oder räumt sie auf (`--clean` entfernt die Ordner abgeschlossener Jobs nach `RetentionDays`) |
| `ext:xrowextract:requirements` | Was dieser Server bereitstellt |

Beispiele, die auf jeder Installation mit Artikeln unter Knoten 2 funktionieren:

```bash
# 20 newest articles as CSV to a file
php extension/xrowextract/bin/php/csv.php --class=ng_article --node=2 --limit=20 --output=articles.csv

# a spreadsheet preview of the same export in the terminal
php extension/xrowextract/bin/php/csv.php --class=ng_article --node=2 --preview=10

# which classes exist below node 2, and which columns a class offers
php extension/xrowextract/bin/php/csv.php --list-classes --node=2
php extension/xrowextract/bin/php/csv.php --list-columns --class=ng_article

# articles changed in the last 30 days that are not hidden, as JSON
php extension/xrowextract/bin/php/csv.php --class=ng_article --node=2 --format=json \
  --visibility=visible --date-field=modified --date=30 --output=recent.json

# the same selection with a condition: articles whose name contains "news"
php extension/xrowextract/bin/php/csv.php --class=ng_article --node=2 \
  --where "name contains news" --output=news.csv
```

Bedingungen von `--where` lauten `"<field> <op> <value>"`, mehrere verknüpft mit ` && `
(und) oder ` || ` (oder), nicht beides in einem Wert. Operatoren: `contains`, `starts`,
`eq` (`=`), `ne` (`!=`), `gt`, `lt`, `gte`, `lte`, `empty`, `filled`, `in`,
`not_in`, `between` und `not_between` (geschrieben `A..B`), `like` und `not_like`
(Platzhalter `*`). `--user=<login>` exportiert mit dem Lesezugriff dieses Logins
(Standard `admin`), `--changed-since=<date>` macht einen Delta-Lauf, und `--lenient`
überspringt mit einer Warnung, was sich nicht mehr auflösen lässt, statt zu scheitern.

Verwenden Sie `--help` bei jedem Befehl für die genaue Liste der Optionen. Übergeben Sie
`--allow-root-user`, wenn Sie als root arbeiten.

## Richtlinien

Setzen Sie sie auf einer Rolle unter **Benutzerkonten > Rollen und Richtlinien**, Modul
`xrowextract`.

| Funktion | Erlaubt |
|---|---|
| `csv` | Die Exportseiten One class und Multi class |
| `import` | Die Tabs Import content file und Import content package, den Inhaltsbrowser und den Vergleich von Paketen, den Upload in Teilen |
| `jobs` | Die Jobs-Seite, ihre Statusabfrage und Downloads |
| `all_jobs` | Die Jobs aller Benutzer sehen, abbrechen und herunterladen; geteilte Vorlagen bearbeiten |
| `schedule` | Zeitpläne anlegen, ändern, ausführen, aktivieren und deaktivieren |
| `destinations` | Auslieferungsziele und ihre Zugangsdaten verwalten |
| `history` | Der Exportverlauf |
| `password_hash` | Den Passwort-Hash von Benutzerkonten exportieren |

Eine Rolle kann das Ausführen von Zeitplänen erlauben, ohne die Zugangsdaten eines Ziels
zu sehen oder zu ändern, oder allein das Lesen des Verlaufs.

## Voraussetzungen und die Serverprüfung

Erforderlich sind PHP 8.1 oder neuer mit `mbstring`, `ctype` und `json` sowie
beschreibbare Ordner `var` und Cache. Einzelne Funktionen brauchen mehr: `dom` und
`xmlreader` zum Importieren von XML, `zip` für ZIP-Downloads und -Archive, `dom`,
`zlib`, `proc_open()`, `tar` und `gzip` sowie einen beschreibbaren Speicherordner für
Inhaltspakete, `proc_open()`, `exec()` und ein PHP-Kommandozeilenprogramm für
Hintergrund-Jobs und Zeitpläne, `sodium` für die Geheimnisse der Ziele, `curl` für die
Ziele HTTP, S3, WebDAV und FTP und den OpenSSH-Client für SFTP. Jede Seite nennt die
Funktionen, die diese Seite auf diesem Server nicht anbieten kann, und was ihnen fehlt.
Zur Prüfung auf der Kommandozeile:

```bash
php extension/xrowextract/bin/php/requirements.php
php extension/xrowextract/bin/php/requirements.php --feature=package,jobs --strict --json
```

Der Befehl gibt pro Voraussetzung PASS oder FAIL aus, WARN für eine fehlende optionale
mit den Funktionen, die dadurch wegfallen, und endet mit 1, wenn eine erforderliche
fehlt. Führen Sie ihn als Benutzer des Webservers aus: Deaktivierte Funktionen und
beschreibbare Ordner hängen vom Benutzer und von der PHP-Konfiguration ab.

## Grenzen und Wissenswertes

* Datentypen mit einem Export-Handler sind in `csv.ini [General]
  ExportableDatatypes[]` aufgeführt; jeder hat einen Handler-Block (`HandlerFile`,
  `HandlerClass`), den Sie aus Ihrer eigenen Extension ersetzen können. Handler für
  Datum und Uhrzeit, Uhrzeit, Schlüsselwörter, Tags und Objektrelationen kamen in 2.5.0
  hinzu.
* Der Export mehrerer Klassen liest Objekte mit dem Lesezugriff des **Benutzers**.
* Die Tabellen `xrowextract_schedule`, `xrowextract_destination` und
  `xrowextract_history` werden bei der ersten Verwendung aus `share/db_schema.dba`
  angelegt.
* Das Werkzeug veröffentlicht die Beispielobjekte des Erstellers für **Package template**
  nicht auf der öffentlichen Website: Sie werden unterhalb der Medienwurzel (oder
  `export.ini [PackageTemplate] ScratchNodeID`) angelegt und wieder entfernt. Lassen Sie
  diese Einstellung nie auf die öffentliche Startseite oder auf einen Knoten zeigen, den
  ein Layout erreicht.
* Unter Velocity (den persistenten PHP-Workern) überdauern Caches pro Anfrage seit 2.5.3
  keine Anfrage mehr, und Upload und Download von Paketen funktionieren, ohne die
  Dateischicht des Servers zu berühren.

## Verwandte Seiten

- [Spezifikation von xrowextract](../../../spezifikationen/6.0/xrowextract.md)
- [xrowmetadata](xrowmetadata.md): der Datentyp, für den der Export eine Spalte hat
- [eztags](eztags.md): Tags werden über die Handler für Schlüsselwörter und Tags exportiert
- [Pakete vergleichen und importieren](../pakete-vergleichen-und-importieren.md)
- [Abstraktionen für Kommandozeile und Cronjobs](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)
- [Velocity-Engines](../../../bc/6.0/velocity-engines.md)
- [Chronik der Extension](../../../geschichte/erweiterungen/xrowextract.md) und [Versionshinweise](../../../../changelogs/extensions/xrowextract.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/xrowextract.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2024-04](../../../geschichte/erweiterungen/monate/2024-04.md), [2024-07](../../../geschichte/erweiterungen/monate/2024-07.md), [2024-08](../../../geschichte/erweiterungen/monate/2024-08.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
