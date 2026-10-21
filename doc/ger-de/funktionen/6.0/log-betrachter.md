# Der Log-Betrachter

Lesen Sie diese Seite, wenn Sie die Logdateien einer Installation aus der Administration heraus lesen, eine Datei beim
Schreiben verfolgen, die Zeilen einer fehlgeschlagenen Anfrage oder den Fehler hinter einer Referenz finden möchten, die
eine Fehlerseite angezeigt hat, oder wenn Sie den Betrachter konfigurieren, absichern oder erweitern.

Verwandt: [Einen Fehler über seine Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md) (Leitfaden),
[Das Prüfprotokoll](pruefprotokoll.md), [Cronjob-Konsole](cronjobs-in-der-konsole.md),
[Systeminformationen](../../anleitungen/systeminformationen.md).

## Kurz gesagt

- **Setup > Logdateien** (`setup/logs`, in der Gruppe **System** des Setup-Menüs und im Mega-Menü von admin4) zeigt
  die Logdateien der Installation: `var/log/error.log`, `warning.log`, `notice.log`, `debug.log`, `strict.log`,
  `deprecated.log`, `auth.log`, die Anfrage-Logs `404.log` und `security.log` ([Anfrage-Logs](anfrageprotokolle.md)), die
  Cronjob-Logs, die Logs jeder Site (`var/<site>/log/*.log`), die Logs, die Extensions
  schreiben (`expmcp.log`, die `expai`-Logs ...), und deren Rotationen `.1` .. `.N` sowie komprimierte Rotationen
  (`.gz`, nur lesbar).
- Das linke Menü enthält das Setup-Menü und einen **Navigator** über alle Dateien, gruppiert nach **Kernel**, **Site**,
  **Extensions**, **Webserver** und **Rotierte Archive**, jeweils mit Größe, dem Zeitpunkt des letzten Schreibens und
  der Anzahl Zeilen, um die die Datei pro Minute wächst.
- Jede Ansicht hat einen **Permalink** ohne Query-String, über den Schlüssel des Logs: `/setup/logs/view/error`,
  `/setup/logs/view/debug`, `/setup/logs/view/webserver-access`, mit ihren Filtern, ihrer Suche und ihrer Position als
  View-Parameter, z. B. `/setup/logs/view/error/(level)/error/(search)/timeout/(follow)/0`. **Permalink kopieren** legt
  ihn in die Zwischenablage. Siehe [Permalinks](#permalinks).
- Die **Logs des Webservers** (Zugriff und Fehler) werden angezeigt, wenn `logviewer.ini` sie über einen Schlüssel
  benennt; standardmäßig sind sie aus und werden nie erraten. Siehe [Webserver-Logs](#webserver-logs).
- Eine Datei wird mit kurzen Anfragen **live verfolgt** (eine Abfrage pro Sekunde, solange Zeilen eintreffen, immer
  seltener, solange nichts kommt), mit **Anhalten** und **Fortsetzen**. Nichts hält einen Serverprozess offen.
- **Durchsuchen** Sie eine Datei, alle Kernel-Logs oder alle Dateien, wahlweise mit ihren Rotationen: nach einem Text
  oder einem regulären Ausdruck (begrenzt, mit Zeitlimit), nach Stufen, einem Zeitraum, dem Siteaccess, einem Teil der
  Anfrage, der Client-Adresse oder einer Fehlerreferenz. Lange Suchen gehen mit **Weitere Ergebnisse** weiter.
- Zeilen werden **geparst** (`[ time ] [client] [siteaccess] [request] message`), **nach Stufe eingefärbt**, nach
  Anfrage gruppiert und **verknüpft**: die Zeilen derselben Anfrage, die Fehlerreferenz, die Audit-Einträge derselben
  Minute sowie Knoten, Objekte, Benutzer, Klassen, Anfragen und Templates in der Administration.
- **Datenschutz zuerst**: E-Mail-Adressen, IP-Adressen, Passwörter, Tokens, Schlüssel, Session-IDs und Cookies sind
  standardmäßig maskiert; die Werte geheimer Einstellungen werden nie angezeigt.
- **Standardmäßig nur lesend.** Das Herunterladen (eines Bereichs, maskiert und komprimiert) ist eingeschaltet;
  **Jetzt rotieren**, **Leeren** und **Archivierte Rotation entfernen** sind ausgeschaltet, bis
  `logviewer.ini [ActionSettings]` sie einschaltet. Jede Ansicht und jede Aktion wird im Audit festgehalten.
- Dasselbe aus einer Shell: `./console exp:logs` (`bin/php/explogs.php`).

## Wer ihn verwenden darf

Drei Funktionen des Moduls `setup`, die in einer neuen Installation **nur der Administrator** hat (diese Rolle hat jede
Richtlinie); vergeben Sie sie bei Bedarf unter **Rollen und Richtlinien** an eine andere Rolle:

| Funktion | Erlaubt |
| --- | --- |
| `setup/logs` | Setup > Logdateien öffnen, die Dateien verfolgen und durchsuchen, einen Bereich herunterladen (wenn `[ActionSettings] Download=enabled`). Damit öffnet sich auch die Setup-Übersicht. |
| `setup/managelogs` | Jetzt rotieren, eine Datei leeren, eine archivierte Rotation entfernen, jeweils nur, wenn in `[ActionSettings]` eingeschaltet. |
| `setup/unmasklogs` | Den Schalter **Unmaskiert zeigen**: E-Mail-Adressen, IP-Adressen und Geheimnisse wie geschrieben, nur für diesen Betrachter und nur solange eingeschaltet. Die Werte geheimer Einstellungen bleiben verborgen. |

Ein Benutzer ohne `setup/logs` (ein Redakteur) wird auf der Seite, beim Feed und bei den Aktionen abgewiesen und sieht
den Link nicht.

## Die Seite

Die Seite zeigt das im Navigator gewählte Log über seinen Permalink (`/setup/logs/view/<key>`, siehe
[Permalinks](#permalinks)); `/setup/logs` allein öffnet `[LogViewerSettings] DefaultKey` (`error`), sonst
`DefaultFile`, sonst das erste Kernel-Log. Sie öffnet am Ende der Datei: die letzten
`[TailSettings] InitialBytes` Bytes, ab der ersten vollständigen Zeile. Diese Zeilen sind Teil der Seite selbst, sodass
sie ohne JavaScript lesbar ist; mit JavaScript übernimmt das Skript der Seite die Liste und verfolgt die Datei.

Jeder Eintrag zeigt seine Zeit, seine Stufe, seine Quelle (die Bezeichnung, die eZDebug geschrieben hat, etwa
`eZTemplate` oder `kernel/content/view.php`, oder `cli` für eine Kommandozeile), den Siteaccess, die Client-Adresse und
die Anfrage, darunter die Meldung. Die Stufen sind eingefärbt (error, warning, notice, debug), in hell und dunkel, mit
einer Textbezeichnung und einem Symbol zusätzlich zur Farbe, bei einem Kontrast von mindestens 4,5:1, und bei
erzwungenen Farben (Windows-Hochkontrast) bleibt die Stufe an ihrer Bezeichnung und ihrem Rahmen erkennbar. Einträge
derselben Anfrage (gleiche Anfrageadresse und gleicher Client) teilen sich eine Markierung, sodass sich eine Anfrage als
ein Block liest.

Die Seite behält höchstens `[TailSettings] MaxLinesKept` Einträge; ältere verlassen die Seite (nie die Datei). Die
Liste ist **virtualisiert**: Nur die sichtbaren Einträge und ein Rand um sie herum sind im Dokument, sodass eine Liste
mit Tausenden von Einträgen so schnell scrollt wie eine mit zehn.

### Der Navigator

Jede Datei der erlaubten Verzeichnisse (siehe [Welche Dateien](#welche-dateien)), gruppiert:

- **Kernel**: die in `[LogViewerSettings] KernelLogs[]` genannten Dateien (error, warning, notice, debug, strict,
  deprecated, auth, cronjob, storage, setup, mail, trash purge, die Anfrage-Logs 404 und security) im globalen
  Log-Verzeichnis oder in dem von eZDebug; ein Log mit einem `LogLabel[<key>]` wird unter seiner Bezeichnung angezeigt.
- **Site**: die Logs im eigenen Log-Verzeichnis einer Site (`var/<site>/log`).
- **Extensions**: jede andere Datei, etwa `expmcp.log` oder `expai-models.log`.
- **Webserver**: die über einen Schlüssel in `LogList[]` konfigurierten Logs (siehe [Webserver-Logs](#webserver-logs)),
  angezeigt mit ihrer Bezeichnung (`LogLabel[]`) und dem Dateinamen daneben.
- **Rotierte Archive**: `.1` .. `.N` und `.gz`, nach ihrer Datei, nach Nummer.

Jeder Eintrag verlinkt auf seinen Permalink (`/setup/logs/view/<key>`, eine Rotation mit `/(rotation)/<n>`).

Jeweils mit Größe, dem Zeitpunkt des letzten Schreibens und der Zeilenrate. Die Rate ist das Wachstum zwischen zwei
Blicken im Abstand von mindestens 30 Sekunden, geteilt durch die durchschnittliche Zeilenlänge der Datei (einmal aus
ihren letzten 16 KB gemessen); sie zeigt "-", bis es zwei Blicke gibt. Das Auflisten kostet ein `scandir()` pro
Verzeichnis und ein `stat()` pro Datei; die Messwerte liegen in einer kleinen Datei in
`<cache dir>/logviewer/stats.json`.

## Permalinks

Jede Ansicht eines Logs hat eine Adresse ohne Query-String, kurz genug zum Abtippen und sicher zum Speichern als
Lesezeichen oder zum Versenden:

```
/setup/logs/view/<key>[/(name)/<value>]...
```

**Der Schlüssel** benennt ein Log, nie einen Pfad (`expLogFileLocator::keyFor()`, `resolveKey()`):

| Log | Schlüssel | Permalink |
| --- | --- | --- |
| `var/log/<name>.log` | `<name>` | `/setup/logs/view/error`, `/setup/logs/view/debug`, `/setup/logs/view/expmcp` |
| `var/<site>/log/<name>.log` | `<site>-<name>` | `/setup/logs/view/site-storage` |
| eine Datei in einem anderen Verzeichnis aus `Directories[]` | das Verzeichnis mit `-` statt `/`, dann `-<name>` | `/setup/logs/view/var-other-logs-app` |
| ein in `LogList[]` konfiguriertes Log | sein Schlüssel | `/setup/logs/view/webserver-access` |

Ein Schlüssel besteht aus Buchstaben, Ziffern, `.`, `_` und `-` (höchstens 64, beginnend mit einem Buchstaben oder
einer Ziffer); jedes andere Zeichen eines Dateinamens wird zu `-`. Eine Rotation ist derselbe Schlüssel mit
`/(rotation)/<n>` (`-1` für eine komprimierte Datei ohne Nummer). Ein Schlüssel findet immer nur eine Datei, die der
Navigator auflistet: Alles andere, `..` und Schrägstriche eingeschlossen, beantwortet die "nicht gefunden"-Seite des
Kernels (404), für einen nicht existierenden Schlüssel genauso wie für einen nicht erlaubten.

**Die View-Parameter**, in der Reihenfolge, in der ein Link sie schreibt (`expLogPermalink::PARAMS`); ein Parameter
mit seinem Standardwert wird weggelassen, sodass eine einfache Ansicht nur `/setup/logs/view/<key>` ist:

| Parameter | Wert | Standard | Bedeutung |
| --- | --- | --- | --- |
| `(rotation)` | eine Zahl | `0` | `.1` .. `.N` des Logs (`-1`: ein bloßes `.gz`) |
| `(level)` | `error,warning,notice,debug` (eine Kommaliste) | alle | die durchsuchten Stufen |
| `(search)` | Text | | der Suchtext |
| `(regex)` | `1` / `0` | `0` | der Suchtext ist ein regulärer Ausdruck |
| `(case)` | `1` / `0` | `0` | Groß-/Kleinschreibung beachten |
| `(from)`, `(to)` | eine Zeit, z. B. `2026-10-17T01:00` | | der Zeitraum |
| `(siteaccess)` | ein Name | | der Siteaccess |
| `(uri)` | Text | | Teil der Anfrageadresse |
| `(ip)` | der Anfang einer Adresse | | der Client |
| `(reference)` | `ERR-0123456789` | | eine Fehlerreferenz |
| `(scope)` | `file`, `kernel`, `all` | `file` | welche Logs durchsucht werden |
| `(rotations)` | `1` / `0` | `0` | auch ihre Rotationen |
| `(cursor)` | `<file>:<offset>` | | wo eine Suche weitergeht ("Weitere Ergebnisse") |
| `(offset)` | ein Byte-Offset | | der Eintrag im Fokus |
| `(follow)` | `1` / `0` | `1` | `0`: die Ansicht öffnet angehalten |
| `(wrap)` | `1` / `0` | die Präferenz des Betrachters | lange Zeilen auf dieser einen Seite umbrochen; ein geteilter Link trägt ihn nie (siehe "Lange Zeilen umbrechen") |
| `(unmask)` | `1` / `0` | `0` | unmaskiert angezeigt (nur mit `setup/unmasklogs`; nie Teil eines kopierten Permalinks) |
| `(done)`, `(ok)` | eine Aktion, `1` / `0` | | das Ergebnis von Rotieren, Leeren oder Entfernen (von der Aktion gesetzt, nicht kopiert) |

**Die Kodierung eines Werts.** Ein Wert wird geschrieben wie jeder View-Parameter
([wie ein Wert in der Adresse reist](../../anleitungen/urls-und-aliase.md#9-view-parameter-wie-ein-wert-in-der-adresse-reist),
`expViewParameters::encode()`): `/`, `(`, `)`, `~`, `%`, `+`, `\`, `?`, `#` und Steuerzeichen werden zu `~` und zwei
Hexadezimalziffern, sodass ein Wert nie ein `/` oder eine `(` enthält, die ihn beenden würde (ein Schrägstrich ist
`~2F`, ein `~` selbst `~7E`); der Rest bleibt lesbar, ein Leerzeichen `%20`, ein Doppelpunkt `:`, `ü` `%C3%BC`. Links
von früher, in denen auch jedes andere Zeichen `~XX` war (ein Leerzeichen `~20`, ein Doppelpunkt `~3A`, `ü`
`~C3~BC`), werden genauso gelesen. Beispiele:

```
/setup/logs/view/error/(level)/error/(search)/timeout/(follow)/0
/setup/logs/view/error/(level)/error,warning/(from)/2026-10-17/(to)/2026-10-17
/setup/logs/view/error/(reference)/ERR-0123456789/(scope)/kernel/(rotations)/1
/setup/logs/view/webserver-access/(search)/~2Fadmin~2Fsetup/(level)/error
/setup/logs/view/debug/(rotation)/1/(offset)/204800/(follow)/0
```

Die Seite hält ihre Adresse mit der Ansicht im Gleichschritt, wenn diese sich ändert (angehalten oder verfolgend, der
Eintrag im Fokus), sodass die Adressleiste und **Permalink kopieren** immer einen Link liefern, der dieselbe Ansicht
öffnet. Das Suchformular funktioniert auch ohne JavaScript: Es wird mit dem Schlüssel und den Feldern gesendet, und die
Seite antwortet mit `303` und dem Permalink.

Der Link, den **Permalink kopieren** liefert (und den das Feld darüber zeigt), trägt die Filter für Anfrage- und
Client-Adresse so, wie die Masken der Einstellungen sie zeigen, unabhängig davon, wer ihn kopiert: `(ip)/203.0.113.7`
wird zu `(ip)/203.0.x.x`, `(ip)/203.0.113` wird zu `(ip)/203.0.x`, eine E-Mail-Adresse in `(uri)` wird zu
`j***@***.com`. Ein Betrachter, der unmaskiert nach einer vollständigen Adresse gesucht hat, teilt sie daher nie; wer
den Link öffnet, findet jeden Eintrag mit dieser maskierten Adresse (was mehr als ein Client sein kann). Die
Adressleiste behält die Filter wie eingegeben, für die eigenen Neuladevorgänge des Betrachters. Mit `MaskIPs=disabled`
wird die Adresse allen angezeigt und bleibt im Link. Im Template enthält `$log_share_permalink` diesen Link
(`$log_permalink` ist der eigene der Ansicht); eine Template-Überschreibung ohne ihn fällt auf `$log_permalink` zurück.

### Lange Zeilen umbrechen

Lange Zeilen werden standardmäßig umbrochen. Der Schalter **Zeilen umbrechen** speichert die Wahl jedes Betrachters als
Benutzerpräferenz `admin_logviewer_wrap` (`1` oder `0`, geschrieben über `user/preferences/set_and_exit` wie die
anderen Präferenzen der Administration), sodass sie für jedes Log, jeden Permalink und den nächsten Besuch gleich
bleibt, und die Seite zeichnet sie, bevor etwas angezeigt wird (kein Aufblitzen). Ohne gespeicherte Wahl entscheidet
`[TailSettings] WrapLines` (Standard `enabled`). Verfolgen oder Anhalten und der Eintrag im Fokus gehören zu einer
Ansicht und reisen in ihrem Permalink mit; das Umbrechen ist eine Präferenz und tut das nicht.

**Alte Links.** Ein Link der ersten Form, `/setup/logs?file=var/log/error.log` (mit `q`, `levels[]` und den anderen
Suchfeldern von damals), antwortet mit `301 Moved Permanently` und dem Permalink derselben Datei und Suche, z. B.
`/setup/logs/view/error`; `/setup/logs?file=var/log/error.log.1` ist `/setup/logs/view/error/(rotation)/1`. Eine
Datei, die er nicht öffnen darf, antwortet mit 404. Der Feed (`setup/logsfeed`) und die Aktionen (`setup/logsaction`)
benennen eine Datei weiterhin über ihre interne ID (`var/log/error.log`; ein konfiguriertes Log `@<key>/<file name>`);
die Aktionen antworten mit dem Permalink.

## Webserver-Logs

Die Zugriffs- und Fehler-Logs des Webservers werden angezeigt, wenn sie in `logviewer.ini` über einen Schlüssel benannt
sind. Sie sind **standardmäßig aus und werden nie erraten**: Die Schlüssel `webserver-access` und `webserver-error`
sind ohne Pfad aufgeführt, und ein Schlüssel ohne Pfad wird nicht angezeigt.

```
[LogViewerSettings]
LogList[]=webserver-access
LogList[]=webserver-error
LogPath[webserver-access]=/var/www/vhosts/<domain>/logs/access_ssl_log
LogPath[webserver-error]=/var/www/vhosts/<domain>/logs/error_log
```

(die Pfade eines Plesk-Servers; anderswo z. B. `/var/log/apache2/access.log` und `error.log` oder die Logs des eigenen
virtuellen Hosts der Site).

- `LogList[]` listet die konfigurierten Schlüssel auf, `LogPath[<key>]` die Datei (absolut oder relativ zum
  Installationsverzeichnis; sie darf außerhalb der Installation liegen), `LogLabel[<key>]` den Namen, den der Navigator
  zeigt (Standards: "Web server access log", "Web server error log"), `LogGroup[<key>]` ihre Gruppe: `kernel`, `site`,
  `extensions` oder `webserver` (der Standard, auch für einen unbekannten Wert).
- Ein konfiguriertes Log kann auf dieselbe Weise jede andere Datei benennen, z. B. ein Log, das ein Programm außerhalb
  von `var/` schreibt. Nur die aufgeführten Schlüssel werden je geöffnet: Ein Permalink, eine Feed-Anfrage oder eine
  Aktion kann keinen anderen Pfad benennen.
- Die Rotationen neben der Datei werden ebenfalls aufgelistet: `<name>.N` und `<name>.N.gz` (`access_ssl_log.1`,
  `error_log.1.gz`); andere Dateien des Verzeichnisses nie (das `access_ssl_log.processed` von Plesk ist keine Rotation). Eine
  Rotation, die ein symbolischer Link aus dem Verzeichnis heraus ist, wird abgelehnt.
- **Nur lesen und herunterladen.** Diese Dateien gehören dem Webserver: Rotieren, Leeren und Entfernen werden für ein
  konfiguriertes Log nie angeboten, und `setup/logsaction` lehnt sie ab, gleichgültig, was `[ActionSettings]` sagt.
- **Der Benutzer, unter dem PHP läuft, muss die Datei lesen können** (der Pool-Benutzer von PHP-FPM oder der Benutzer
  von Exponential Velocity). Unter Plesk gehören die Dateien in `/var/www/vhosts/<domain>/logs/` dem Benutzer der Site;
  `/var/www/vhosts/system/<domain>/logs/` lässt ihn nicht hinein. Eine Datei, die nicht gelesen werden kann, wird als
  solche aufgeführt und nicht geöffnet; `open_basedir` muss den Pfad erlauben.
- Die Zeilen werden so gelesen, wie der Webserver sie schreibt: das **Zugriffs-Log** im Common- oder Combined-Format
  (`client - user [17/Oct/2026:01:00:00 +0200] "GET /path HTTP/1.1" 200 1234 "referer" "agent"`), mit dem Status als
  Stufe (5xx error, 4xx warning, sonst notice), Client und Pfad in die Gruppierung nach Anfrage verknüpft; das
  **Fehler-Log** von Apache 2.4 (`[Fri Oct 17 01:00:00.123456 2026] [module:level] [pid N] [client ip:port] message`)
  mit seiner Stufe. Die Maskierung gilt wie überall: Client-Adressen und E-Mail-Adressen sind standardmäßig maskiert.

## Eine Datei live verfolgen

Die Seite fragt `setup/logsfeed?action=tail&file=...&offset=<n>&ino=<n>` ab:

- **Zuerst ein günstiges `stat()`.** Eine Datei, die seit dem Offset nicht gewachsen ist, wird beantwortet, ohne sie zu
  öffnen.
- **Nur das Neue wird gelesen**, ab dem Byte-Offset, den die Seite bereits hat, höchstens `[TailSettings] MaxBytesPerPoll`
  pro Abfrage; eine Datei, die stärker gewachsen ist, wird über die nächsten Abfragen sofort aufgeholt (die Antwort sagt
  `more`). Eine Datei wird nie ganz gelesen.
- **Ein Abschnitt endet an der letzten vollständigen Zeile.** Eine Zeile, die noch geschrieben wird, wartet auf die
  nächste Abfrage.
- **Ein Abschnitts-Cache auf dem Server.** Der zuletzt gelesene Abschnitt jeder Datei wird `ChunkCacheTTL` Sekunden lang
  mit Inode, Größe und Zeit der Datei aufbewahrt, sodass ein zweiter Betrachter derselben Datei oder eine Abfrage, die
  erneut fragt, daraus beantwortet wird; nur die Bytes nach den zwischengespeicherten werden gelesen. Der Cache wird in
  eine temporäre Datei geschrieben und umbenannt, sodass niemand ihn halb liest.
- **Keine Dateisperren**, weder auf dem Log noch auf dem Cache; auch die Schreiber hängen ohne Sperren an.
- **Backoff im Leerlauf.** Die Seite fragt alle `PollInterval` Millisekunden ab, solange Zeilen eintreffen; nach jeder
  leeren Abfrage verdoppelt sich das Intervall bis zu `MaxPollInterval`; neue Zeilen setzen es zurück. Ein verborgener
  Browser-Tab fragt mit der langsamsten Rate ab.
- **Rotieren und Leeren.** Eine Datei mit einem anderen Inode (rotiert) oder kleiner als der Offset (geleert) wird von
  ihrem Anfang an neu gelesen, und die Seite markiert den Bruch.
- **Anhalten und Fortsetzen.** Anhalten beendet das Abfragen (die Schaltfläche oder die Leertaste, während das Log den
  Fokus hat); Fortsetzen macht dort weiter, wo angehalten wurde, sodass nichts verloren geht.
- Ein Archiv (eine Rotation, ein `.gz`) wird angezeigt, nie verfolgt.

**Warum kurze Abfragen und kein Stream.** Ein Server-Sent-Events-Stream hält eine Anfrage so lange offen, wie die Seite
offen ist. Unter PHP-FPM ist das ein Kindprozess pro offener Seite; unter Exponential Velocity ist es ein Worker pro
offener Seite, aus einem festen Pool genommen, für Minuten oder Stunden. Eine Abfrage ist eine Anfrage von wenigen
Millisekunden: Der Worker ist sofort wieder frei, wer auch immer eine Seite offen lässt. Die Cronjob-Konsole streamt,
weil ein Lauf endet; ein Log hat kein Ende.

### Gemessen

Gemessen auf einem Testserver am 17. Oktober 2026 (PHP 8.5, Load Average 10 bis 13 auf einem gemeinsam genutzten
Server), mit einem synthetischen Log von 50 MB (280.000 Einträge), das um 20 Einträge (3,7 KB) pro Sekunde wuchs und
einmal pro Sekunde abgefragt wurde.

Die Log-Arbeit einer Abfrage, im Prozess gemessen (getrusage und /proc/self/io;
60 s, 54 wachsende und 6 leere Abfragen):

| | Laufzeit | CPU | Gelesene Bytes |
|---|---|---|---|
| Öffnen der Datei (ihre letzten 64 KB, 349 Einträge maskiert und verknüpft) | 78 ms | 78 ms | 200 KB |
| Eine Abfrage mit 20 neuen Einträgen | 6,1 ms | 5,0 ms (max. 9,6) | 7,9 KB |
| Dieselbe Abfrage durch einen zweiten Betrachter (aus dem Abschnitts-Cache) | 5,9 ms | 4,4 ms | 7,9 KB, 54 von 54 Cache-Treffern |
| Eine leere Abfrage (nichts Neues: ein stat(), keine Datei geöffnet) | 0,25 ms | 0,4 ms | 0,1 KB |
| Zum Vergleich: die ganze Datei einmal lesen | 59 ms | 59 ms | 50 MB |
| Eine wörtliche Suche durch die ganze Datei | 484 ms | 437 ms | hält bei 32 MB an (`MaxBytesPerRequest`), "Weitere Ergebnisse" geht weiter |
| Ein Zeitraum von 10 s in der Mitte der Datei | 41 ms | 41 ms | 288 KB (binäre Suche über die Zeiten) |
| Ein katastrophaler regulärer Ausdruck, `(\w+\s?)+$` | 4,4 ms | 4,4 ms | angehalten mit "regex_limit" |

Der größte Teil der CPU einer wachsenden Abfrage entfällt auf das Maskieren und Verknüpfen ihrer Einträge (etwa
0,25 ms pro Eintrag); die Datei-E/A ist das, was hinzukam, plus die kleine Cache-Datei.

Die gesamte Anfrage über HTTPS, als Administrator, jeweils 30 Abfragen (`server_ms` ist
die Zeit, die PHP für die Anfrage aufgewendet hat, einschließlich Kernel-Start, Session und Richtlinienprüfung):

| | Serverzeit, Mittel (p95) | Roundtrip, Mittel (p95) |
|---|---|---|
| Apache + PHP-FPM, wachsend | 57 ms (69) | 81 ms (104) |
| Apache + PHP-FPM, leer | 56 ms (66) | 80 ms (95) |
| Velocity (:8080), wachsend | 47 ms (67) | 85 ms (120) |
| Velocity (:8080), leer | 37 ms (51) | 74 ms (100) |

Eine Abfrage kostet also so viel wie jede kleine Anfrage an die Administration; das Log selbst fügt höchstens einige
Millisekunden hinzu. Eine Seite, die einer ruhigen Datei folgt, verlangsamt sich von 1 s auf 15 s
(`MaxPollInterval`), sodass ein untätiger Betrachter etwa 4 Anfragen pro Minute stellt; ein verborgener Tab fragt gar
nicht ab. Jede Anfrage belegt einen PHP-FPM-Kindprozess oder einen Velocity-Worker nur für diese Millisekunden, nie so
lange, wie die Seite offen ist.

## Suchen

Das Suchformular (über der Liste) durchsucht:

- **Diese Datei**, **alle Kernel-Logs** oder **alle Dateien**, und mit **Rotierte Dateien einbeziehen** auch deren
  `.1` .. `.N` und `.gz` (`[SearchSettings] Rotations`). Dateien werden mit der neuesten zuerst durchsucht (die Datei,
  dann `.1`, `.2` ...); innerhalb einer Datei kommen die Einträge in der Reihenfolge, in der sie geschrieben wurden.
- **Text**: wörtlich (Standard) oder als **regulärer Ausdruck**, mit oder ohne Beachtung der Groß-/Kleinschreibung. Ein
  regulärer Ausdruck, der länger als `MaxPatternLength` ist oder sich nicht kompilieren lässt, wird mit einer Meldung
  abgelehnt.
- **Stufen**: error, warning, notice, debug.
- **Zeitraum**: von und bis (ein Datum allein bedeutet den ganzen Tag). Ein Zeitraum beginnt jede einfache Datei beim
  ersten Eintrag des Zeitraums, gefunden mit einer binären Suche über die Zeiten (etwa 30 kleine Lesevorgänge für eine
  Datei von 1 GB), sodass ein schmales Fenster in einer großen Datei wenig kostet; eine Datei wird verlassen, sobald
  ihre Einträge deutlich hinter dem Ende des Zeitraums liegen.
- **Siteaccess**, **Anfrage** (Teil der Adresse), **Client-Adresse** (ihr Anfang), **Fehlerreferenz**.

Jede Suchanfrage ist begrenzt: höchstens `MaxBytesPerRequest` gelesene Bytes, `TimeLimit` Millisekunden und
`MaxResults` Einträge. Eine Suche, die an einer Grenze anhält, sagt das und bietet **Weitere Ergebnisse** an, was dort
weitermacht, wo sie anhielt (ein Cursor `<file>:<offset>`). Ein regulärer Ausdruck läuft mit `pcre.backtrack_limit` auf
`BacktrackLimit` gesetzt und ohne JIT (das diese Grenze ignoriert); ein Muster, das für eine Zeile mehr braucht
(`(a+)+$` und dergleichen), beendet die Suche mit "The regular expression is too expensive", statt den Server zu
blockieren.

Eine Suche findet nur, was dem Betrachter angezeigt wird: Text, Anfrage und Client-Adresse müssen auch so passen, wie
sie angezeigt werden, sodass ein verborgener Wert nicht durch Suchen gefunden werden kann, Rateversuch für
Rateversuch. Bei eingeschalteter Maskierung vergleicht der Filter für die Client-Adresse die maskierte Adresse:
`203.0.` findet `203.0.113.7` (angezeigt als `203.0.x.x`), `203.0.113` findet nichts. Um nach einer vollständigen
Adresse aus einem Vorfallbericht zu suchen, schaltet ein Betrachter mit `setup/unmasklogs` **Unmaskiert zeigen** ein.
Die Werte geheimer Einstellungen und ein Passwort in einer Adresse sind für jeden Betrachter verborgen, sodass sie nie
gefunden werden, ob maskiert oder nicht.

Die Filter für Anfrage- und Client-Adresse werden mit dem Wert verglichen, wie er dem Betrachter angezeigt wird, und
nur damit, sodass ein von der Seite kopierter Wert ebenfalls ein Filter ist: `203.0.x.x` (ein `x` wird in der
Client-Adresse akzeptiert), `2001:db8:x:x`, `2001::x` und `::x` für komprimierte IPv6-Adressen, `token=***` in der
Anfrage. Groß-/Kleinschreibung spielt keine Rolle (`2001:DB8:`). Ein Filter, der ein Kennzeichen der Masken enthält
(`x` in einer Adresse, `***` in einer Anfrage), findet auch für einen unmaskierten Betrachter die Einträge, die ein
maskierter Betrachter damit finden würde, sodass ein aus einer maskierten Ansicht erstellter Link für beide
funktioniert. Das gilt auf jedem Weg, der filtert: die Kernel-Logs, `404.log` und `security.log` (deren Adressen
bereits maskiert geschrieben werden, sodass die maskierte Form für jeden Betrachter die einzige ist, die sie findet), die
Archive (`.N` und `.N.gz`, auch mit **Rotationen**), das Skript der Seite, die Links der Fehlerübersicht und die
Permalinks.

Eine komprimierte IPv6-Adresse mit einer Gruppe vor dem `::` (`2001::7334`, `fe80::1`) wird als `2001::x` angezeigt,
so wie die Anfrage-Logs sie schreiben; vor 6.0.15 zeigte der Betrachter sie unmaskiert, und die Anfrage-Logs schrieben
`x`.

## Das Zeilenformat und Querverweise

Eine Zeile, die einen Eintrag beginnt, wird in einem dieser Formate gelesen; der Rest des Eintrags sind die Zeilen bis
zur nächsten solchen Zeile:

| Schreiber | Format |
| --- | --- |
| eZDebug (`error.log`, `warning.log` ...) | `[ Oct 17 2026 01:54:58 ] [client] [siteaccess] [request or command line] Label:` und die Meldung in den nächsten Zeilen; ältere Dateien ohne Siteaccess und Anfrage |
| eZLog (`storage.log`, Extension-Logs) | `[ Oct 17 2026 01:54:58 ][ siteaccess ][ request ] message` oder `[ time ] message` |
| ISO-Zeit | `2026-10-17T01:54:58+02:00 message`, `[2026-10-17 01:54:58] message` |
| JSON-Zeilen | ein Objekt pro Zeile mit `time`/`ts`/`timestamp`, `level`, `message`/`msg`, `ip`, `url`/`uri` |

Die **Stufe** ist diejenige, die die Meldung nennt (`Error:`, `PHP Warning`, `[debug]` ...), sonst die der Datei
(`[LogViewerSettings] FileLevels[]`), sonst notice.

**Querverweise** aus einem Eintrag:

- **Dieselbe Anfrage**: die Einträge aller Kernel-Logs (und der ersten Rotation) mit derselben Anfrageadresse und
  demselben Client innerhalb von `[SearchSettings] RequestWindow` Sekunden, in zeitlicher Reihenfolge. Das ist auch die
  **Debug-Ausgabe** der Anfrage, wo sie protokolliert wurde: die notice- und debug-Zeilen von `notice.log` und
  `debug.log` dazu.
- **Fehlerreferenz**: Eine Fehlerseite zeigt eine Referenz wie `ERR-0123456789`, die neben den Details in `error.log`
  geschrieben wird. Die Suche danach (das Referenzfeld oder ein Klick auf eine Referenz in einer Zeile) findet den
  Eintrag in den Kernel-Logs und ihren Rotationen und zeigt die Zeilen seiner Anfrage.
- **Audit**: ein Link zur Audit-Konsole, gefiltert auf die Minute um den Eintrag
  (`audit/console/(from)/.../(to)/...`), für alle, die das Audit lesen dürfen.

## Links in die Administration

Werte in einer Meldung werden nach dem Maskieren zu Links (ein maskierter Wert ist nie ein Link):

| Gefunden | Öffnet |
| --- | --- |
| eine Knoten-ID (`node_id=123`, `NodeID: 123`, `/content/view/full/123`) | `content/view/full/123` |
| eine Objekt-ID oder eine Benutzer-ID | den Inhaltsfinder für dieses Objekt |
| ein Klassenbezeichner (`class 'article'`) | die Klasse (`class/view/<id>`), wenn die Klasse existiert |
| eine Anfrageadresse dieser Site | die Adresse selbst (nur vom gleichen Ursprung) |
| ein Template-Pfad (`design/<design>/templates/....tpl`) | `setup/templateview/...`, nur für Benutzer, die ihn öffnen dürfen |
| eine Fehlerreferenz | die Suche danach |

Links werden vom Skript der Seite mit DOM-Methoden aufgebaut; kein Text aus einem Log wird je als HTML eingefügt.

## Datenschutz: was maskiert wird

Die Organisationsregel dieses Projekts lautet, dass personenbezogene Daten von Kunden nie offengelegt werden; deshalb
maskiert der Betrachter standardmäßig (`[RedactionSettings]`):

- **E-Mail-Adressen** (`MaskEmails`): `jane.doe@example.com` wird zu `j***@***.com`.
- **IP-Adressen** (`MaskIPs`): `203.0.113.7` wird zu `203.0.x.x`, eine IPv6-Adresse behält ihre ersten beiden Gruppen.
  Die Client-Spalte wird genauso maskiert. Versionsnummern wie `6.0.15.1` bleiben unberührt.
- **Geheimnisse** (`MaskSecrets`): Passwörter in Adressen (`mysql://user:***@host`), die Werte von `password=`,
  `token=`, `api_key=`, `secret=` und ähnlichen Paaren, `Bearer`/`Basic`-Zugangsdaten, JSON Web Tokens, Session-IDs und
  Cookies, lange Folgen aus Buchstaben und Ziffern (Schlüssel, Hashes).
- **Geheime Einstellungen, immer**: Die Werte von `SecretSettings[]` (standardmäßig das Datenbankpasswort und das
  Passwort des Mail-Transports) und jeder Variable aus `SecretGroups[]`, deren Name besagt, dass sie ein Geheimnis
  enthält, werden in jeder Zeile durch `***` ersetzt, maskiert oder nicht, und ebenso ein Passwort in einer Adresse.

Ein Benutzer mit `setup/unmasklogs` kann **Unmaskiert zeigen** für sich selbst einschalten; das gilt für die Seite,
ihre Abfragen und ihre Downloads, und das Audit hält fest, dass die Datei unmaskiert gelesen wurde. Downloads werden
genauso maskiert wie die Seite. Screenshots und Berichte über ein Log sollten bei eingeschalteter Maskierung erstellt
werden.

## Aktionen

| Aktion | Benötigt | Eingeschaltet durch | Audit |
| --- | --- | --- | --- |
| Einen Bereich herunterladen (Bytes von, bis; standardmäßig die letzten `MaxDownloadBytes`), maskiert, mit gzip komprimiert | `setup/logs` | `[ActionSettings] Download=enabled` (Standard) | `system.log.download` |
| Jetzt rotieren: `.N-1` wird zu `.N` (die älteste jenseits von `site.ini [FileSettings] LogFileRotateCount` wird entfernt), die Datei wird zu `.1`; die nächste Zeile beginnt eine neue Datei | `setup/managelogs` | `Rotate=enabled` | `system.log.rotate` |
| Eine Datei leeren (ihre Größe wird 0) | `setup/managelogs` | `Truncate=enabled` | `system.log.truncate` |
| Eine archivierte Rotation entfernen (`.1` .. `.N`, `.gz`; nie eine aktuelle Datei) | `setup/managelogs` | `DeleteArchives=enabled` | `system.log.delete` |

Rotieren, Leeren und Entfernen werden für ein über einen Schlüssel konfiguriertes Log (das des Webservers) nie
angeboten: Es gehört seinem Programm. Nach einer Aktion öffnet die Seite den Permalink des Logs mit
`(done)/<action>/(ok)/1` (oder `0`), der das Ergebnis anzeigt.

Jede Aktion ist ein POST an `setup/logsaction` mit dem Formular-Token; Rotieren, Leeren und Entfernen zeigen zuerst eine
Bestätigungsseite, die die Datei, ihre Größe und das, was geschehen wird, nennt. Das Öffnen einer Datei hält
`system.log.read` einmal pro Datei und Session alle `[ViewerAuditSettings] ReadInterval` Sekunden fest, sodass das
Verfolgen einer Datei nicht pro Abfrage einen Eintrag schreibt. Die Audit-Namen stehen im Katalog des
[Prüfprotokolls](../../bc/6.0/audit.md) (Kanal `system`).

## Logs verwalten

**Setup > Logdateien > Logs verwalten** (`setup/managelogs`; `/setup/logsmanage` oder `/setup/logsmanage/<key>` für ein
Log) listet jedes Log, das der Betrachter öffnen darf, mit seiner Größe, einer Schätzung seiner Zeilen, dem letzten
Schreiben, seinem Wachstum (Bytes pro Stunde seit der letzten Rotation), seinen Archiven (`.1` .. `.N`, jeweils mit
Größe und Angabe, ob komprimiert) und den Grenzen, die für es gelten. Pro Log:

| Aktion | Was sie tut | Eingeschaltet durch |
| --- | --- | --- |
| Jetzt rotieren | `.N-1` wird zu `.N`, die älteste jenseits der Grenze `keep` wird entfernt, die Datei wird zu `.1` | `[ActionSettings] Rotate` |
| Leeren | die Datei wird zuerst nach `.1` kopiert (erst archivieren), dann an Ort und Stelle geleert (derselbe Inode, die Schreiber machen weiter) | `Truncate` |
| Archive komprimieren | jedes einfache Archiv `.N` wird zu `.N.gz` | `Compress` |
| Alte Archive entfernen | die Archive, die älter als N Tage sind, und die jenseits von `keep` | `DeleteArchives` |
| Herunterladen | das aktuelle Log oder ein Archiv, maskiert, als gzip (der Download des Betrachters, `setup/logsaction`) | `Download` |

**Alle über ihrer Grenze rotieren** rotiert jedes Log über seiner Größengrenze; **Alle Archive aufräumen** entfernt die
alten Archive jedes Logs.
Jede Aktion, für ein Log oder alle, zeigt zuerst ihre **Vorschau**: jeden Schritt, den sie ausführen würde (umbenennen,
kopieren, komprimieren, entfernen, leeren), mit den Dateien und ihren Größen, und ändert nichts. Ihr **Bestätigen**
sendet sie erneut: Der Plan wird unter einer Sperre pro Log angewendet (ein Log, das anderswo rotiert wird, antwortet
mit "locked"), und jeder Schritt wird erneut gegen die Festplatte geprüft, sodass ein veralteter Plan Schritt für
Schritt übersprungen wird (ein Ziel, das inzwischen erschienen ist, wird nie überschrieben). Jeder POST trägt das
Formular-Token. Ein über einen Schlüssel konfiguriertes Log (das des Webservers) wird aufgeführt, aber nie verwaltet:
Es gehört seinem Programm.

Die Grenzen eines Logs, der erste Fund gewinnt:
1. `logviewer.ini [RotationSettings]` `MaxSize[<key>]`, `Keep[<key>]`, `Compress[<key>]`, `MaxAgeDays[<key>]`;
2. `site.ini [FileSettings]` `LogFileMaxSize`, `LogFileRotateCount`, `LogFileCompress`, `LogFileMaxAge`;
3. 2 MB, 3 Archive, nicht komprimiert, keine Altersgrenze.

Die Seite zeigt, woher jede Grenze stammt (Datei, Abschnitt, Einstellung). Die Arbeit erledigt die Logverwaltung
(`expLogManagerFactory::get()`: `expLogManager`, wenn installiert, sonst ein eingebauter Ersatz mit derselben API).
Geplante Rotation kommt mit der Logverwaltung: der Cronjob-Teil `logs` (`cronjobs/explogrotate.php`,
`[CronjobPart-logs]`), der die Größen- und Altersgrenzen anwendet und in keinem standardmäßig laufenden Teil enthalten
ist.

Audit: ein Umbenennen oder Kopieren als `system.log.rotate` (Komprimieren mit `after.action` `compress`), Leeren als
`system.log.truncate`, Entfernen als `system.log.delete`.

### Alle Logs herunterladen

**Alle Logs herunterladen** (eine orangefarbene Schaltfläche am Anfang der Seite, `[ActionSettings] Download`) sendet
ein Archiv aller Logs, die der Betrachter auflistet, die des Webservers eingeschlossen, mit dem Namen
`logs-<host>-<YYYYMMDD-HHMM>.zip` (ein `.tar.gz`, wo die PHP-Erweiterung zip fehlt). Mit **Archive einschließen**
(standardmäßig angekreuzt) kommen ihre Rotationen mit. Die Pfade darin sind relativ: `var/log/error.log`,
`var/<site>/log/storage.log` und `webserver/access_ssl_log` für ein über einen Schlüssel konfiguriertes Log. Eine
`MANIFEST.txt` listet jede Datei mit ihrer Größe, dem letzten Schreiben (UTC) und dem sha256 des Gespeicherten.

- Der Inhalt wird Zeile für Zeile so maskiert, wie der Betrachter ihn anzeigt; eine komprimierte Rotation wird zum
  Maskieren entpackt. **Unmaskiert**, nur mit `setup/unmasklogs` angeboten und mit einer Warnung angezeigt, speichert
  die Dateien wie geschrieben (eine komprimierte Rotation unverändert).
- Es ist ein POST mit dem Formular-Token auf einer Seite, die `setup/managelogs` benötigt, und wird im Audit als
  `system.log.download` mit `after.bundle`, der Anzahl der Dateien, den Bytes und der Angabe, ob unmaskiert,
  festgehalten.
- Die gelesenen Logs dürfen zusammen höchstens `[ActionSettings] MaxBundleBytes` groß sein (Standard 256 MB); darüber
  sagt die Seite, wie viel es ist, und nichts wird erstellt. Das Archiv wird als Stream erstellt (der Speicher bleibt
  flach) in einem eigenen Verzeichnis unter `var/tmp/logbundle`, in Stücken gesendet und danach entfernt, auch wenn der
  Besucher vorzeitig geht.

## Zustand der Logs

**Setup > Logdateien > Zustand der Logs** (`setup/logs`; `/setup/logshealth`) zeigt auf einen Blick:

- den Speicherplatz, den alle Logs und ihre Archive belegen, gegenüber `[HealthSettings] DiskBudget` (Standard 256 MB;
  eine Warnung ab 80 %, ein Alarm darüber; 0: kein Budget);
- die am schnellsten wachsenden Logs und die Logs über ihrer Größengrenze (mit einem Link zu ihrer Verwaltung, für
  `setup/managelogs`);
- die Dateien, die der Benutzer des Webservers nicht schreiben kann, mit einer Warnung, wenn die Logs gemischte
  Eigentümer haben (zum Beispiel einige von root über die Kommandozeile geschrieben und einige vom Benutzer des
  Webservers), was die Rotation und neue Zeilen verhindert;
- den neuesten Fehler jedes Logs, maskiert, verknüpft mit seiner Zeile (der Permalink des Logs mit `(offset)/N`,
  angehalten);
- nur lesend: die pro Log geltenden Rotationseinstellungen (maximale Größe, behaltene Archive, Komprimierung, Alter),
  jeweils mit der INI-Datei, dem Abschnitt und der Einstellung, aus der sie stammen.

## Fehlerübersicht

**Setup > Logdateien > Fehlerübersicht** (`setup/logs`; `/setup/logsdigest`, `/setup/logsdigest/<key>` für ein Log,
`/(hours)/N` für ein anderes Fenster, 1 bis 168) listet die unterschiedlichen Fehler und Warnungen der letzten
`[DigestSettings] Hours` (24) der Kernel-Logs: jede Meldung maskiert, dann normalisiert (Zahlen, Hexadezimalwerte, IDs,
Werte in Anführungszeichen, Pfade und `ERR-`-Referenzen beiseitegelassen), gruppiert und gezählt, die häufigsten zuerst,
höchstens `MaxMessages` (50) pro Log, mit ihrer schwersten Stufe und dem Zeitpunkt, zu dem sie zuletzt gesehen wurde.
Jede Gruppe verlinkt auf die Ansicht des Logs, gefiltert nach Stufe, einer Suche nach ihrem Text und dem Beginn des
Fensters; ihre Zeit verlinkt auf die neueste solche Zeile. Jedes Log wird am Beginn des Fensters über eine binäre Suche
über seine Zeiten betreten und höchstens `MaxBytesPerFile` (8 MB) weit gelesen; ein nur teilweise gelesenes Log sagt
das.

**Als bekannt markieren** blendet eine Gruppe für den Betrachter aus (ein POST mit dem Formular-Token; die
Benutzerpräferenz `admin_logviewer_known`, höchstens 200 Gruppen); `/(known)/1` listet auch die bekannten auf, mit
**Wieder anzeigen**.

**Pro Log** listet `[DigestSettings] LogLevels[<key>]` andere Stufen auf (eine Kommaliste;
`LogLevels[404]=error,warning,notice` listet die Nicht-gefunden-Antworten auf, die Notices sind), und `GroupBy[<key>]`
gruppiert nach etwas anderem als der Meldung: `category-rule` (das erste Wort, `<category>/<rule>`: `security.log`),
`reason` (das Feld `reason=` mit beiseitegelassenen IDs und Pfaden: `404.log`), `reason-kind`, `uri` (die Methode und
der Pfad ohne Query) oder `message` (der Standard). Ein anders gruppiertes Log sagt das über seiner Tabelle. Siehe
[Anfrage-Logs](anfrageprotokolle.md).

Eine Gruppe eines nach `uri` gruppierten Logs verlinkt auch mit dem Anfragefilter: der Pfad der Gruppe bis zu ihrem
ersten Platzhalter, maskiert wie die Gruppe (`GET /blog/#` verlinkt mit `(uri)/~2Fblog~2F`), den die Suche mit der
Anfrage vergleicht, wie sie dem Betrachter angezeigt wird. Ein Pfad, der nicht mehr als `/` ist, fügt keinen Filter
hinzu. `expLogDigest::link( $group, $since, $grouping )` nimmt die Gruppierung des Logs; ohne sie wird ein Link wie
bisher aufgebaut.

## Archive

Eine Rotation öffnet sich im selben Betrachter, nur lesend, unter ihrem eigenen Permalink:
`/setup/logs/view/<key>/(archive)/N` (`.N` oder `.N.gz`, wenn es kein einfaches gibt). `(rotation)/N` wird weiterhin
gleich gelesen. Ein Archiv wird nie verfolgt (keine Abfragen); es kann durchsucht, heruntergeladen und mit
`DeleteArchives` entfernt, aber nie rotiert oder geleert werden. Alte Links der Form `?file=var/log/error.log.2`
antworten mit 301 und `/setup/logs/view/error/(archive)/2`.

## Meldungen kopieren

**Meldungen kopieren** (die erste Schaltfläche der Zeile unter der Dateiauswahl) kopiert die Zeilen, die der Betrachter
anzeigt, als reinen Text, wie es eine Auswahl von Hand tun würde: in Anzeigereihenfolge, ein Eintrag pro Zeile (Zeit,
Stufe, Datei, Quelle, Anfrage, Meldung; eine umbrochene Meldung behält ihre Zeilenumbrüche, eine nicht umbrochene zeigt
sie als ↵), maskiert wie angezeigt. Beim Verfolgen kopiert sie jede behaltene Zeile, angehalten die Zeilen auf dem
Bildschirm. Sie verwendet die Zwischenablage des Browsers (mit einem Ersatzweg für Seiten, die nicht über HTTPS
ausgeliefert werden) und meldet "12 Zeilen kopiert" in einer kurzen Nachricht und an Screenreader.

## Welche Dateien

Der Betrachter öffnet nur Dateien, die er auflistet, und er listet nur diese auf (`expLogFileLocator`):

- die Dateien **direkt in** einem Verzeichnis aus `[LogViewerSettings] Directories[]` (relativ zum
  Installationsverzeichnis; `{log_dir}` ist das Verzeichnis, in das eZDebug schreibt, `*` ein Verzeichnisname, sodass
  `var/*/log` das Log-Verzeichnis jeder Site ist); Unterverzeichnisse werden nie durchlaufen, sodass das Prüfprotokoll
  in `var/log/audit` bei der Audit-Konsole bleibt;
- deren Name zu `FilePatterns[]` passt (`*.log`, `*.log.N`, `*.log.N.gz`, `*.log.gz`) und zu keinem
  `ExcludePatterns[]`.

sowie die über einen Schlüssel in `LogList[]` konfigurierten Logs (siehe [Webserver-Logs](#webserver-logs)) mit ihren
Rotationen.

Die Seite fragt ein Log über seinen Schlüssel an ([Permalinks](#permalinks)), der nur eine Datei dieser Liste findet.
Der Feed und die Aktionen fragen eine Datei über ihre ID an: den Pfad relativ zum Wurzelverzeichnis
(`var/log/error.log`) oder `@<key>/<file name>` für ein konfiguriertes Log (nur seine Datei und seine Rotationen). Ein
Pfad wird abgelehnt, wenn er
`..`, `.`, einen Backslash, ein Steuerzeichen oder einen führenden Schrägstrich enthält; sein Verzeichnis muss sich zu
einem der erlaubten Verzeichnisse **auflösen** (`realpath`), und diese müssen sich innerhalb des
Installationsverzeichnisses auflösen, sodass einem symbolischen Link auf ein Verzeichnis anderswo nicht gefolgt wird.
Eine Datei, die ein symbolischer Link ist, wird nur geöffnet, wenn ihr Ziel eine Logdatei in einem erlaubten
Verzeichnis ist. Alles andere antwortet mit "not allowed", für eine nicht existierende Datei genauso wie für eine
außerhalb, sodass der Betrachter nicht zum Abtasten der Festplatte verwendet werden kann.

## Ratenbegrenzung

Jede Anfrage an den Feed (Abfragen, Suchen, verwandte Zeilen) zählt gegen die Session: höchstens
`[RateLimitSettings] Requests` in `Window` Sekunden. Eine Anfrage über der Grenze wird mit `429` und `Retry-After`
beantwortet, und die Seite wartet so lange. Der Zähler ist eine kleine Datei pro Session in
`<cache dir>/logviewer/rate`, benannt nach einem Hash der Session-ID; nichts wird in die Session geschrieben, sodass
eine Abfrage keinen Session-Schreibvorgang kostet.

## Tastatur und Barrierefreiheit

- **Leertaste** (während das Log den Fokus hat): anhalten oder fortsetzen.
- **Ende**: zum neuesten Eintrag springen (und wieder verfolgen); **Pos1**: der älteste behaltene; **Auf** und **Ab** bewegen zwischen Einträgen, **Eingabe** öffnet einen (seine Anfrage, Debug-Ausgabe und Audit).
- **Eingabe** / **Umschalt+Eingabe** im Suchfeld oder **n** / **N** im Log: nächster / vorheriger Treffer.
- Das Log ist ein fokussierbarer Bereich mit einer Bezeichnung; neue Einträge werden in einer höflichen Live-Region
  angekündigt, höchstens eine Ankündigung alle paar Sekunden ("12 neue Einträge"), nie eine pro Zeile.
- Jedes Bedienelement hat eine sichtbare Bezeichnung und einen Fokusring; Farben sind nie das einzige Kennzeichen einer
  Stufe.

Die Seite funktioniert in den Designs admin4, admin4l (und adminui, das auf sie zurückfällt) und im klassischen Admin,
mit den gemeinsamen Komponenten und Tokens (`.a4-setup-log*` in `stylesheets/modules/setup.css`), ohne Inline-CSS, hell
und dunkel, in Telefonbreite.

## Auf der Kommandozeile

`./console exp:logs` (`bin/php/explogs.php`) verwendet dieselben Einstellungen, Dateien, Maskierungen und Grenzen. Die
Kommandozeile ist der Betreiber des Servers: Es wird keine Richtlinie geprüft, und pro Lauf, der eine Datei liest, wird
ein `system.log.read` festgehalten.

```
./console exp:logs --list                                   the files, by group, with size, time and lines a minute
./console exp:logs --tail=var/log/error.log --lines=50      the last 50 entries
./console exp:logs --tail=var/log/error.log --follow        follow it, with the page's backoff (Ctrl-C ends)
./console exp:logs --search=timeout --file=var/log/error.log --rotations --level=error,warning
./console exp:logs --search='content/view/full/\d+' --regex --scope=kernel --from='2026-10-17 01:00' --to='2026-10-17 02:00'
./console exp:logs --reference=ERR-0123456789               where the reference is, and the lines of its request
./console exp:logs --related=var/log/error.log:123456       the lines of the request of the entry at that offset
```

Weitere Optionen: `--case`, `--scope=file|kernel|all`, `--sa=<siteaccess>`, `--uri=<part>`, `--ip=<start>`,
`--cursor=<cursor>` (ausgegeben als "More: ...", wenn eine Suche an einer Grenze anhielt), `--limit=<n>`,
`--interval=<ms>`, `--unmask`, `--json`, `--color=auto|always|never`. Exit-Code: 0 etwas aufgelistet oder gefunden,
1 nichts gefunden, 2 ein Fehler (eine Datei, die der Betrachter nicht öffnen darf, ein ungültiger regulärer
Ausdruck ...).

## Einstellungen

`settings/logviewer.ini`; überschreiben Sie sie in `settings/override/logviewer.ini.append.php` oder im Siteaccess der
Administration.

| Einstellung | Standard | Bedeutung |
| --- | --- | --- |
| `[LogViewerSettings] LogViewer` | `enabled` | `disabled`: die Seite und ihr Feed antworten mit 404 |
| `Directories[]` | `var/log`, `{log_dir}`, `var/*/log` | die Verzeichnisse, deren Dateien aufgelistet werden (siehe [Welche Dateien](#welche-dateien)) |
| `FilePatterns[]` | `*.log`, `*.log.[0-9]`, `*.log.[0-9][0-9]`, `*.log.[0-9].gz`, `*.log.[0-9][0-9].gz`, `*.log.gz` | die aufgelisteten Dateinamen |
| `ExcludePatterns[]` | (keine) | Dateinamen, die nie aufgelistet werden |
| `KernelLogs[]` | `error.log`, `warning.log`, `notice.log`, `debug.log`, `strict.log`, `deprecated.log`, `auth.log`, `cronjob*.log`, `storage.log`, `setup.log`, `mail.log`, `trashpurge.log`, `404.log`, `security.log` | die Namen der Gruppe Kernel |
| `DefaultKey` | `error` | das Log, das `/setup/logs` öffnet (ein Schlüssel, siehe [Permalinks](#permalinks)) |
| `DefaultFile` | `var/log/error.log` | die Datei, die geöffnet wird, wenn `DefaultKey` keine findet |
| `LogList[]` | `webserver-access`, `webserver-error` | die über einen Schlüssel konfigurierten Logs (siehe [Webserver-Logs](#webserver-logs)) |
| `LogPath[<key>]` | (keiner: aus) | die Datei eines konfigurierten Logs; ein Schlüssel ohne sie wird nicht angezeigt |
| `LogLabel[<key>]` | `Web server access log`, `Web server error log`, `Not found requests (404)` (404), `Security (request classifier)` (security) | der Name, den der Navigator und die Übersicht zeigen, übersetzt im Kontext `kernel/logviewer`; er benennt ein konfiguriertes Log oder ein beliebiges Log der Log-Verzeichnisse über seinen Schlüssel |
| `LogGroup[<key>]` | `webserver` | die Gruppe: `kernel`, `site`, `extensions`, `webserver` |
| `FileLevels[]` | `error.log*;error`, `*error*.log*;error`, `warning.log*;warning`, `strict.log*;warning`, `deprecated.log*;notice`, `notice.log*;notice`, `debug.log*;debug`, `404.log*;notice`, `security.log*;warning` | die Stufe von Zeilen, die keine nennen, nach Dateiname |
| `[TailSettings] InitialBytes` | `65536` | was die Seite beim Öffnen anzeigt |
| `MaxBytesPerPoll` | `262144` | das Meiste, was eine Abfrage liest |
| `PollInterval` | `1000` | ms zwischen Abfragen, solange Zeilen eintreffen |
| `MaxPollInterval` | `15000` | das längste Intervall des Backoffs |
| `MaxLinesKept` | `5000` | die Einträge, die die Seite behält |
| `ChunkCacheTTL` | `10` | Sekunden, die der letzte Abschnitt einer Datei aufbewahrt wird; 0 schaltet den Cache aus |
| `ChunkCacheMaxBytes` | `262144` | die meisten Bytes eines zwischengespeicherten Abschnitts |
| `WrapLines` | `enabled` | lange Zeilen umbrochen für einen Betrachter, der nicht gewählt hat (die Präferenz `admin_logviewer_wrap`) |
| `[SearchSettings] MaxBytesPerRequest` | `33554432` | das Meiste, was eine Suchanfrage liest |
| `TimeLimit` | `2000` | ms, die eine Suchanfrage liest |
| `MaxResults` | `200` | Einträge, die eine Suchanfrage zurückgibt |
| `MaxPatternLength` | `200` | der längste Suchtext |
| `BacktrackLimit` | `100000` | `pcre.backtrack_limit` einer Suche mit regulärem Ausdruck |
| `Rotations` | `enabled` | Rotationen dürfen durchsucht werden |
| `MaxGzipBytes` | `67108864` | das Meiste eines `.gz`, das eine Anfrage entpackt |
| `RequestWindow` | `2` | Sekunden um einen Eintrag, die als dieselbe Anfrage zählen |
| `[RedactionSettings] MaskEmails`, `MaskIPs`, `MaskSecrets` | `enabled` | die Masken (siehe [Datenschutz](#datenschutz-was-maskiert-wird)) |
| `SecretSettings[]` | `site.ini;DatabaseSettings;Password`, `site.ini;MailSettings;TransportPassword` | Werte, die nie angezeigt werden |
| `SecretGroups[]` | `site.ini;DatabaseSettings`, `site.ini;MailSettings` | Gruppen, deren als geheim benannte Werte nie angezeigt werden |
| `[ActionSettings] Download` | `enabled` | einen Bereich herunterladen |
| `Rotate`, `Truncate`, `DeleteArchives` | `disabled` | jetzt rotieren, leeren, archivierte Rotationen entfernen |
| `Compress` | `disabled` | archivierte Rotationen komprimieren (siehe [Logs verwalten](#logs-verwalten)) |
| `[RotationSettings] MaxSize[<key>]`, `Keep[<key>]`, `Compress[<key>]`, `MaxAgeDays[<key>]` | (keine: `site.ini [FileSettings]`) | die Grenzen eines Logs (siehe [Logs verwalten](#logs-verwalten)) |
| `[HealthSettings] DiskBudget` | `268435456` | der Speicherplatz, den alle Logs zusammen belegen dürfen (siehe [Zustand der Logs](#zustand-der-logs)); 0: keiner |
| `[DigestSettings] Hours`, `MaxMessages`, `MaxBytesPerFile`, `Levels[]` | `24`, `50`, `8388608`, `error`, `warning` | die Fehlerübersicht (siehe [Fehlerübersicht](#fehlerübersicht)) |
| `[DigestSettings] LogLevels[<key>]`, `GroupBy[<key>]` | `LogLevels[404]=error,warning,notice`; `GroupBy[404]=reason`, `GroupBy[security]=category-rule` | pro Log: die aufgelisteten Stufen und die Gruppierung (`message`, `category-rule`, `reason`, `reason-kind`, `uri`) |
| `MaxDownloadBytes` | `52428800` | der größte Bereich eines Downloads |
| `MaxBundleBytes` | `268435456` | die meisten Bytes an Logs, die ein Archiv von "Alle Logs herunterladen" enthält |
| `[RateLimitSettings] Requests`, `Window` | `120`, `60` | Feed-Anfragen pro Session und Fenster (Sekunden) |
| `[ViewerAuditSettings] ReadInterval` | `600` | Sekunden zwischen zwei `system.log.read` derselben Datei und Session |

Numerische Einstellungen werden innerhalb sicherer Grenzen gehalten, gleichgültig, was in der Datei steht (zum Beispiel
`PollInterval` mindestens 250 ms, `MaxBytesPerPoll` höchstens 4 MB).

## Für Entwickler

- Klassen in `kernel/classes/logviewer/`: `expLogViewerConfig` (Einstellungen), `expLogFileLocator` (Positivliste,
  Gruppen, Schlüssel, konfigurierte Logs), `expLogPermalink` (die Permalinks: `build()`, `state()`, `encode()`,
  `decode()`), `expLogTail` (Lesen ab Offset, Abschnitts-Cache, `.gz`), `expLogParser` (Einträge, Stufen, Links),
  `expLogSearch` (begrenzte Suche, Zeitsuche), `expLogRedactor` (Masken), `expLogRateLimit`, `expLogFileStats`
  (Zeilenrate), `expLogActions` (Herunterladen, Rotieren, Leeren, Entfernen) und die Fassade `expLogViewer`
  (Richtlinien, Audit, die Antworten des Feeds).
- Ansichten: `setup/logs` (die Seite; `setup/logs/view/<key>` mit den View-Parametern aus [Permalinks](#permalinks)),
  `setup/logsfeed` (JSON: `action=tail|search|related|reference|files`),
  `setup/logsaction` (POST). Template-Fetch: `fetch( 'setup', 'log_files' )`, der Navigator (leer ohne
  `setup/logs`).
- Ein Log Ihrer eigenen Extension erscheint unter **Extensions**, wenn es in ein erlaubtes Verzeichnis mit passendem
  Namen geschrieben wird (`var/log/myextension.log` über `eZLog::write()`); geben Sie ihm mit `FileLevels[]` eine Stufe
  oder schreiben Sie `Error:` / `Warning:` an den Anfang seiner Meldungen.
- Tests (ohne Datenbank): `tests/tests/kernel/logviewer/`: der Parser, das Lesen ab Offset und der Abschnitts-Cache, die
  Maskierung, die Pfadsicherheit, die Grenze für reguläre Ausdrücke, die Suche, die Ratenbegrenzung und die Aktionen
  auf Wegwerf-Dateien; die Schlüssel und die konfigurierten Logs (`expLogKeyTest`), die Permalinks und die alten Links
  (`expLogPermalinkTest`), die Formate des Webservers (`expLogWebServerFormatsTest`).

## Zeilen des Demo-Modus

Auf einer öffentlichen Demo (Umgebungsvariable `EXP_DEMO_MODE=1`) schreibt jeder Prozess eine Zeile `demo/mode on pid=<pid> sapi=<sapi> ...` in `security.log`, im Format des Anfrage-Klassifizierers, sodass **Setup > Logdateien** sie unter `demo/mode` gruppiert. Auf einer echten Site bedeutet eine solche Zeile, dass die Variable sie erreicht hat. [Demo-Modus](../../bc/6.0/demomodus.md).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
