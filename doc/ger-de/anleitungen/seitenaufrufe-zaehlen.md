# Seitenaufrufe zählen: Aufrufe aus den Zugriffsprotokollen, für eine Site oder viele

Diese Anleitung erklärt, wie Exponential zählt, wie oft jede Seite aufgerufen wurde, warum der Zähler für 6.0.15
neu gebaut wurde und wie man ihn einrichtet: für eine einzelne Site, für Siteaccesses mit je einem eigenen
Zugriffsprotokoll und für viele Siteaccesses, die sich ein Protokoll teilen. Sie behandelt auch den Betrieb als
Cronjob und auf der Konsole, was sich der Zähler zwischen zwei Läufen merkt, die Rotation der Protokolle, Robots und
die Umstellung vom bisherigen Cronjob.

Sie richtet sich an Administratoren und Entwickler, die mehrere Sites aus einer Installation betreiben oder
Aufrufzahlen brauchen, denen sie trauen können. Jede Einstellung und jede Option wurde am Code geprüft
(`kernel/classes/viewcount/`, `kernel/classes/ezviewcounter.php`,
`kernel/private/classes/cronjobs/updateviewcount.php`, `kernel/private/classes/commands/expviewcountupdate.php`,
`settings/logfile.ini`). Die Zahlen in Abschnitt 12 stammen vom Demonstrationsserver, ausgegeben am 3. November 2026
von den gezeigten Befehlen.

[Anleitungen](README.md) · Verwandt: [Cronjobs](cronjobs.md) · Geändertes Verhalten: [Aufrufzähler 6.0.15](../bc/6.0/aufrufzaehler-fuer-mehrere-sites.md) ·
Einstellungen: [logfile.ini](../beispiele/ini-einstellungen/logfile/README.md) · Befehl: [exp:viewcount:update](../beispiele/befehle/expviewcountupdate.md)

## Kurz gefasst

- Der Aufrufzähler liest das **Zugriffsprotokoll** des Webservers, behält die Zeilen, die Seitenaufrufe sind, sucht
  den Knoten, den jede gezeigt hat, und addiert die Aufrufe in der Tabelle `ezview_counter`. Templates lesen sie als
  `view_count` eines Knotens, der Fetch `content/view_top_list` listet die meistbesuchten Knoten, und die Liste der
  Unterelemente hat eine Spalte für die Aufrufe.
- Er läuft als Cronjob-Skript `updateviewcount.php` (nicht von Haus aus aktiv) oder als
  `./console exp:viewcount:update`, das `--dry-run`, `--top`, `--format=json` und die Pflege seines Zustands
  hinzufügt.
- **Eine Site**: `StorageDir` und `LogFileName` in `logfile.ini [AccessLogFileSettings]` setzen, wie bisher. Mehr
  ist nicht nötig.
- **Ein Protokoll je Siteaccess**: `[AccessLogFileSettings]` in die
  `settings/siteaccess/<siteaccess>/logfile.ini.append.php` jedes Siteaccess schreiben und die Siteaccesses in
  `[ViewCountSettings] SiteAccessList[]` aufzählen (oder `AllSiteAccesses=enabled`). Ein Lauf zählt alle; jeder
  behält seine eigene Stelle in seinem eigenen Protokoll.
- **Viele Siteaccesses in einem Protokoll**: Das Protokoll wird einmal gelesen, und jede Zeile geht an den
  Siteaccess, den die Siteaccess-Erkennung des Kernels (`MatchOrder`: uri, host, host_uri ...) für sie findet;
  `/bold/News` zählt also für `bold`, `/News` für die Site des Hosts.
- Jeder Lauf macht **an dem Byte weiter, an dem der letzte aufgehört hat**, je Protokoll und je Siteaccess, und
  bemerkt Rotation und Kürzung. Robots, Fehler, Weiterleitungen, Dateien und Modulseiten werden nicht gezählt.

## 1. Warum der Zähler neu gebaut wurde

Der Cronjob, den 6.0.14 und alle früheren Versionen mitbrachten, war für eine Site mit einem Protokoll geschrieben.
Installationen, die mehrere Sites aus einer Datenbank bedienen, stießen auf diese Probleme; der neue Zähler löst
alle:

| Problem des bisherigen Cronjobs | Was passierte | Was 6.0.15 tut |
|---|---|---|
| **Eine gemeinsame Merkdatei.** Wo der letzte Lauf aufgehört hatte, stand in einer Datei, `var/<site>/log/updateview.log`, gleich welches Protokoll gelesen wurde. | Zwei Siteaccesses mit eigenen Zugriffsprotokollen (zwei virtuelle Hosts) überschrieben sich bei jedem Lauf gegenseitig die Marke. Jeder Lauf suchte dann in seinem Protokoll die letzte Zeile des anderen, fand sie nicht und zählte nichts, oder fand keinen Anfang und zählte das ganze Protokoll noch einmal. | Eine Zustandsdatei je Protokoll und darin eine Stelle je Siteaccess (Abschnitt 7). |
| **Weitermachen am Text der letzten Zeile.** Der Lauf suchte im Protokoll die zuletzt gelesene Zeile und behielt von einer Zeile nur die ersten 1023 Bytes. | Zwei gleiche Zeilen (derselbe Besucher lädt in derselben Sekunde neu), eine Zeile über 1023 Bytes (lange User-Agents und Referer) oder ein rotiertes Protokoll ließen die Suche scheitern: Bis zur nächsten Rotation wurde nichts gezählt. | Weitermachen nach Inode und Byte-Position, mit einem Hash der ersten Zeile, an dem das Protokoll erkannt wird. |
| **"/" landete beim Wurzelknoten.** Die Aufrufe der Startseite wurden als URL-Alias "" (leer) gesucht, mit einem `PathPrefix` als "Präfix/". | Der Wurzelknoten (Knoten 2) oder der oberste Knoten des Präfixes bekam die Aufrufe der Startseite, nicht die Seite, die der Siteaccess unter "/" zeigt. | "/" ist die `site.ini [SiteSettings] IndexPage` des Siteaccess. |
| **Sites mit PathPrefix verloren Aufrufe.** Mit `PathPrefix[]` wurde ein Pfad nur mit Präfix gesucht. | Seiten außerhalb des Präfixes (gemeinsame Ordner, `PathPrefixExclude[]`) wurden nie gezählt. | Erst mit den Präfixen, dann der Pfad, wie er ist; `PathPrefixExclude[]` gilt; nennt `logfile.ini` keinen, wird der `PathPrefix` des Siteaccess genommen. |
| **Ein gemeinsames Protokoll mischte die Sites.** Der Lauf jedes Siteaccess zählte jede Zeile des Protokolls. | Mit einem Apache-Protokoll für mehrere Siteaccesses (Erkennung nach URI, `/bold/...`, oder mehrere Hosts in einem `vhost_combined`-Protokoll) wurden die Aufrufe jeder Site für jede gezählt, also jeder Aufruf mehrfach, und Pfade mit Präfix und Sprachen des falschen Siteaccess gesucht. | Zeilen gehen nach der Siteaccess-Erkennung des Kernels an die Siteaccesses, und `Hosts[]` beschränkt einen Siteaccess auf seine Hosts. |
| **Rotation verlor oder verdoppelte Zeilen.** | Wurde ein Protokoll zwischen zwei Läufen rotiert, wurden die Zeilen nach dem letzten Lauf und vor der Rotation nie gelesen; mit `copytruncate` wurde im neuen Protokoll eine Zeile gesucht, die nicht darin stand. | Erst der Rest der rotierten Kopie (`access_log.1`, `access_log.1.gz`), dann das neue Protokoll von vorn. |
| **Alles wurde gezählt.** | Robots, `POST`-Anfragen, Antworten `404` und `301`, die Weiterleitung und die Seite, zu der sie führte, zählten gleich. | Nur `GET` mit `200` oder `304`, keine Robots, Dateien oder Modulseiten; jede Regel lässt sich ändern oder abschalten (Abschnitt 8). |

Die Beschreibung des Cronjobs sprach außerdem davon, einen "Zwischenspeicher der Aufrufe im Speicher in die
Datenbank zu schreiben". Einen solchen Zwischenspeicher gab es nie: Seitenaufrufe zählen nichts, nur dieser Cronjob
zählt, aus dem Zugriffsprotokoll. Die Beschreibung sagt jetzt, was er tut.

Die Merkdatei je Protokoll, das "/" der `IndexPage` des Siteaccess und der Rückgriff auf den Pfad ohne `PathPrefix`
wurden zuerst in einem Prototyp erprobt, den ein Site-Betreiber für eine Installation mit mehreren Sites geschrieben
hat; 6.0.15 baut auf diesen Ideen auf und ergänzt den Rest.

## 2. Was ein Lauf tut

1. **Welche Siteaccesses, welche Protokolle.** Die Siteaccesses aus `--siteaccess-list`, `--all-siteaccesses`,
   `[ViewCountSettings] SiteAccessList[]` oder `AllSiteAccesses=enabled`, sonst der Siteaccess des Laufs (`-s`).
   Jeder liest seine eigene `logfile.ini [AccessLogFileSettings]`; Siteaccesses, die dieselbe Datei nennen, bilden
   ein Protokoll. Administrationen werden aus den Listen herausgelassen (nach der Regel des Siteaccess-Menüs:
   `AdminSiteAccessList[]`, sonst ein Admin-Design oder ein `FrontSiteAccess`).
2. **Sperre.** Jedes Protokoll hat neben seinem Zustand eine Sperrdatei; ein zweiter Lauf wartet bis zu
   `LockTimeout` Sekunden, lässt das Protokoll dann aus und endet mit Exit-Code 3.
3. **Die neuen Zeilen lesen.** Ab der Stelle jedes Siteaccess, nur vollständige Zeilen (die Zeile, die der Server
   gerade schreibt, wartet auf den nächsten Lauf).
4. **Die Seitenaufrufe behalten** (Abschnitt 8) und jede Zeile ihrem Siteaccess geben (Abschnitt 5).
5. **Den Knoten finden.** "/" ist die `IndexPage`; `/content/view/<Ansicht>/<Knoten>` ist dieser Knoten; alles andere
   ist ein URL-Alias, gesucht in den Sprachen des Siteaccess (`SiteLanguageList[]`), zuerst mit seinen
   Pfad-Präfixen. View-Parameter (`/(offset)/10`) und der Query-String gehören nicht zum Pfad. Jeder Pfad wird je
   Lauf einmal gesucht.
6. **Schreiben.** Knoten, die es nicht mehr gibt, fallen weg; die Aufrufe des ganzen Laufs werden in einer
   Transaktion zu `ezview_counter` addiert (`count = count + n`, es geht also nichts verloren, was parallel gezählt
   wurde).
7. **Merken.** Erst nach dem Commit wird der Zustand jedes Protokolls geschrieben. Ein Lauf, der scheitert, schreibt
   nichts, und der nächste liest dieselben Zeilen noch einmal.

## 3. Eine Site (nichts ändert sich)

```ini
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
StorageDir=/var/log/httpd
LogFileName=access_log
```

Einmal von Hand ausführen, dann in einen Cronjob-Teil aufnehmen:

```bash
./console exp:viewcount:update --dry-run --top=10 -s site
./console exp:viewcount:update -s site
```

```ini
# settings/override/cronjob.ini.append.php
[CronjobPart-viewcount]
Scripts[]=updateviewcount.php
```

`php runcronjobs.php -s site viewcount` zählt dann stündlich oder so oft, wie der Zeitplan des Teils sagt
([Cronjobs](cronjobs.md)). `SitePrefix[]` und `PathPrefix[]` behalten ihre Bedeutung. Läuft der Cronjob für eine
Administration (`-s admin`, wie in vielen Installationen), wird das Protokoll aus deren Einstellungen gelesen, und
"/" und die URL-Aliase sind die von `DefaultAccess`.

## 4. Ein Protokoll je Siteaccess

Zwei Sites auf zwei virtuellen Hosts, jede mit eigenem Protokoll:

```ini
# settings/siteaccess/site/logfile.ini.append.php
[AccessLogFileSettings]
StorageDir=/var/www/vhosts/example.com/logs
LogFileName=access_ssl_log
```

```ini
# settings/siteaccess/shop/logfile.ini.append.php
[AccessLogFileSettings]
StorageDir=/var/www/vhosts/shop.example.com/logs
LogFileName=access_ssl_log
PathPrefix[]=Shop
```

```ini
# settings/override/logfile.ini.append.php
[ViewCountSettings]
SiteAccessList[]
SiteAccessList[]=site
SiteAccessList[]=shop
```

Ein Lauf (`./console exp:viewcount:update` oder der Cronjob für irgendeinen Siteaccess) liest beide Protokolle, jedes
ab seiner eigenen Stelle. Den Cronjob je Siteaccess einmal laufen zu lassen (`-s site`, `-s shop`), ohne
`SiteAccessList[]`, geht ebenso: Jeder Lauf zählt seinen Siteaccess und behält seine Stelle; der bisherige Cronjob
konnte das nicht.

## 5. Viele Siteaccesses in einem Protokoll

Ein einzelner virtueller Host, dessen Siteaccesses am Pfad erkannt werden (`MatchOrder=uri;host`, `/bold/...`,
`/starter/...`), schreibt ein Protokoll für alle. Das Protokoll einmal für alle setzen und jede Site zählen:

```ini
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
StorageDir=/var/www/vhosts/example.com/logs
LogFileName=access_ssl_log
# der Host der Anfragen, da die Zeilen des combined-Formats ihn nicht enthalten
Host=www.example.com

[ViewCountSettings]
AllSiteAccesses=enabled
```

Das Protokoll wird einmal gelesen. Jede Zeile geht an den Siteaccess, den die Erkennung des Kernels für ihren Host
und Pfad findet (`eZSiteAccess::matchAddress()`, also `match()` ohne die Weiterleitung von `RedirectOnNormalize` und
ohne die Sprache des Browsers); der Teil des Pfads, der den Siteaccess bestimmt, wird entfernt, und der Rest wird mit
`IndexPage`, `PathPrefix` und Sprachen dieses Siteaccess gesucht. Zeilen eines Siteaccess, der nicht gezählt wird
(die Administration, `/admin/...`), werden als `other-siteaccess` übersprungen.

Mehrere Hosts in einem Protokoll: das Apache-Format `vhost_combined` verwenden (`LogFormat "%v:%p %h %l %u %t
\"%r\" %>s %O \"%{Referer}i\" \"%{User-Agent}i\""`); der Zähler liest den Host jeder Zeile, und die Erkennung nach
Host wirkt je Zeile. `Hosts[]` beschränkt einen Siteaccess auf bestimmte Hosts, etwa wenn ein Protokoll auch einen
Host enthält, der nicht zu dieser Installation gehört.

`MatchSiteAccess` entscheidet, wann Zeilen zugeordnet werden: `auto` (Vorgabe), wenn mehr als ein gezählter
Siteaccess ein Protokoll liest, `enabled` immer (ein Siteaccess aus einem Protokoll mehrerer Sites), `disabled` nie.

## 6. Ausführen

### Der Cronjob

`cronjobs/updateviewcount.php` tut, was `./console exp:viewcount:update` ohne Optionen tut; seine Ausgabe geht in
das Protokoll des Cronjobs. Er steht in `cronjob.ini`, ist aber nicht aktiv (`#Scripts[]=updateviewcount.php`); ihn
wie in Abschnitt 3 in einen Teil aufnehmen.

### Der Befehl

| Option | Was sie tut |
|---|---|
| `--dry-run` | Liest und berichtet; schreibt weder Zahlen noch Zustand. |
| `--top=N` | Listet die N meistbesuchten Knoten des Laufs und die N meistgefragten Pfade, die kein Knoten sind (ein fehlender `PathPrefix`, eine Modulseite für `SkipPathPrefixes[]`). |
| `--all-siteaccesses` | Jeder Siteaccess aus `AvailableSiteAccessList[]` mit Protokoll, außer den Administrationen. |
| `--siteaccess-list=site,shop` | Diese Siteaccesses. |
| `--log-file=PFAD` | Ein Protokoll für jeden gezählten Siteaccess statt ihrer Einstellungen: ein Versuch mit einer Kopie oder ein älteres rotiertes Protokoll (`.gz` wird auch gelesen). |
| `--state-dir=PFAD` | Der Zustand an anderer Stelle, für einen solchen Versuch. |
| `--from-start` | Nur mit `--dry-run`: die Protokolle von der ersten Zeile an lesen, ohne den Zustand. |
| `--skip-to-end` | Nichts zählen, die Protokolle als gelesen markieren: ab jetzt zählen. |
| `--reset-state` | Nichts zählen, den Zustand vergessen: Der nächste Lauf liest die Protokolle von vorn. |
| `--format=json` | Der Bericht als JSON, Format `exp-viewcount/1`. |

Exit-Codes: `0` in Ordnung (auch wenn nichts Neues da war), `1` ein Fehler (ein Protokoll fehlt oder ist nicht
lesbar, die Datenbank lehnte die Zahlen ab, der Zustand ließ sich nicht schreiben), `2` ein Bedienfehler, `3` ein
anderer Lauf hielt die Sperre eines Protokolls.

Der Bericht nennt je Protokoll die Siteaccesses, was seit dem letzten Lauf mit dem Protokoll geschah (`new`,
`resumed`, `rotated`, `truncated`, `migrated`), die gelesenen Bytes und Zeilen, die Aufrufe und warum die übrigen
Zeilen nicht gezählt wurden:

| Grund | Zeilen |
|---|---|
| `malformed` | keine Zeile eines Zugriffsprotokolls, oder eine Anfrage, die nicht `METHODE Ziel` ist |
| `too-long` | länger als `MaxLineLength` |
| `method`, `status` | eine andere Methode oder ein anderer Status als `Methods[]`, `Statuses[]` |
| `bot` | ein User-Agent mit einem Eintrag aus `BotUserAgents[]`, oder keiner (`SkipEmptyUserAgent`) |
| `asset`, `excluded` | eine Datei (`SkipExtensions[]`) oder ein Pfad aus `SkipPathPrefixes[]` |
| `other-host`, `other-siteaccess` | nicht unter den `Hosts[]` des Siteaccess; ein Siteaccess, der nicht gezählt wird |
| `before-resume` | schon gezählt (der Zustand des bisherigen Cronjobs, Abschnitt 10) |
| `not-content`, `no-node` | ein Pfad, der kein Knoten ist; ein Knoten, den es nicht mehr gibt |

Er enthält keine Client-Adressen und keine User-Agents.

## 7. Wo er aufhört, und die Rotation der Protokolle

Der Zustand ist eine JSON-Datei je Protokoll in `[ViewCountSettings] StateDir` (Vorgabe `var/<site>/viewcount/`),
benannt nach dem Protokoll und einem Hash seines Pfads, mit einer Sperrdatei daneben:

```json
{
    "format": "exp-viewcount-state/1",
    "log": "/var/www/vhosts/example.com/logs/access_ssl_log",
    "sites": {
        "site": { "inode": 2363541, "device": 2052, "offset": 1048576, "head": "9c1f…", "lines": 4210, "updated": "2026-11-03T10:00:02-08:00" },
        "bold": { "inode": 2363541, "device": 2052, "offset": 1048576, "head": "9c1f…", "lines": 4210, "updated": "2026-11-03T10:00:02-08:00" }
    }
}
```

Sie wird in eine temporäre Datei geschrieben und umbenannt, ist also nie halb geschrieben. Jeder Siteaccess hat seine
eigene Stelle: Ein später hinzugefügter Siteaccess liest das Protokoll von vorn, die anderen machen weiter, wo sie
waren, und jede Zeile wird trotzdem einmal gelesen.

Bei jedem Lauf wird das Protokoll mit dem Zustand verglichen:

- **resumed**: dieselbe Datei (Inode und erste Zeile) und mindestens so lang: ab der Position weiterlesen.
- **rotated**: Das Protokoll wurde umbenannt (`create`) oder kopiert und geleert (`copytruncate`): Die Kopie aus
  `RotatedSuffixes[]` (Vorgabe `.1`, `.1.gz`, `.0`), die die alte Datei ist (gleiche Inode oder gleiche erste Zeile,
  so wird auch eine komprimierte Kopie gefunden), wird von der Position bis zum Ende gelesen, dann das neue
  Protokoll von vorn.
- **truncated**: kürzer als die Position und keine Kopie gefunden: das neue Protokoll von vorn. Die Zeilen zwischen
  dem letzten Lauf und der Kürzung sind verloren, der Bericht warnt davor. Den Zähler öfter laufen lassen, als das
  Protokoll rotiert wird, oder die Kopie in `RotatedSuffixes[]` nennen.

**Plesk** hängt die Zeilen von `access_log` und `access_ssl_log` beim Erstellen seiner Statistik an
`access_log.processed` und `access_ssl_log.processed` an und leert das laufende Protokoll. Die `.processed`-Datei ist
eine andere Datei, also keine rotierte Kopie des laufenden Protokolls. Stattdessen die `.processed`-Datei lesen
(`LogFileName=access_ssl_log.processed`): Sie wächst nur, und ihre eigenen rotierten Kopien (`.processed.1.gz`)
findet die Vorgabe von `RotatedSuffixes[]`. Die Aufrufe kommen dann nach jedem Lauf der Statistik an. Oder das
laufende Protokoll lesen und den Zähler öfter laufen lassen als die Statistik, wobei die Zeilen zwischen dem
letzten Lauf und dem Lauf der Statistik verloren gehen.

## 8. Was als Seitenaufruf zählt

Die Vorgaben in `logfile.ini [ViewCountSettings]`:

- `Methods[]=GET`; `Statuses[]=200` und `304` (ein Browser, der eine Seite neu prüft, die er hat, ist ein Aufruf;
  ein `301` nicht, sein Ziel wird gezählt, wenn es abgerufen wird).
- `BotUserAgents[]`: Teile des User-Agents, in beliebiger Schreibweise: `bot`, `crawl`, `spider`, `curl/`,
  `python`, `headless`, `warmer` (Cache-Vorwärmer) und weitere. `SkipEmptyUserAgent=enabled`.
- `SkipExtensions[]`: Stylesheets, Skripte, Bilder, Schriften, Medien, Archive.
- `SkipPathPrefixes[]`: `/var/`, `/design/`, `/extension/`, `/user/`, `/content/search`, `/content/edit/`,
  `/api/`, `/ezjscore/` und weitere, verglichen mit dem Pfad, wie er angefragt wurde, und ohne den Teil des
  Siteaccess.
- `ViewModes[]`: leer, also zählt `/content/view/<jede Ansicht>/<Knoten>`; `ViewModes[]=full` beschränkt es.

Eine leere Liste schaltet ihre Prüfung ab. Wie bei jeder INI-Liste werden Einträge in einem Override an die
mitgelieferte Liste angehängt; eine leere Zeile `Methods[]` zuerst leert sie, die Einträge danach ersetzen also die
Vorgaben:

```ini
# settings/override/logfile.ini.append.php: auch HEAD-Anfragen zählen, und keine Prüfung des Status
[ViewCountSettings]
Methods[]
Methods[]=GET
Methods[]=HEAD
Statuses[]
```

Andere Protokollformate: Die Formate common, combined und `vhost_combined` werden Zeile für Zeile erkannt (Apache,
nginx, `plesklog` von Plesk, Velocity, auch mit Feldern nach dem User-Agent und ohne Client-Adresse). Ein eigenes
Format nimmt `LineRegexp`, einen regulären Ausdruck mit den benannten Gruppen `time`, `request` und `status` und
wahlweise `host`, `ip`, `agent`, `referer`.

## 9. Die Einstellungen

`[AccessLogFileSettings]`, je Siteaccess:

| Einstellung | Bedeutung |
|---|---|
| `StorageDir`, `LogFileName` | Das Zugriffsprotokoll (ein relatives `StorageDir` liegt unter der Installation). |
| `SitePrefix[]` | Ein erstes Pfadelement, das entfernt wird, nur ein ganzes (`mysite` schneidet nicht von `mysite2`); nicht verwendet, wenn Zeilen zugeordnet werden. |
| `PathPrefix[]` | Präfixe, die zuerst versucht werden; leer: der `site.ini PathPrefix` des Siteaccess. |
| `Host`, `Port` | Host und Port der Anfragen des Protokolls, wenn die Zeilen keinen enthalten (für die Zuordnung). |
| `Hosts[]` | Nur Zeilen dieser Hosts zählen für den Siteaccess. |

`[ViewCountSettings]`, für den Lauf: `SiteAccessList[]`, `AllSiteAccesses`, `MatchSiteAccess`, `StateDir`,
`LockTimeout`, `RotatedSuffixes[]`, `MaxLineLength`, `LineRegexp`, `ViewModes[]`, `Methods[]`, `Statuses[]`,
`SkipExtensions[]`, `SkipPathPrefixes[]`, `BotUserAgents[]`, `SkipEmptyUserAgent`. Jede ist in
`settings/logfile.ini` beschrieben und auf der erzeugten Seite [logfile.ini [ViewCountSettings]](../beispiele/ini-einstellungen/logfile/ViewCountSettings.md).

## 10. Umstellung vom bisherigen Cronjob

- Die Einstellungen, die er las, behalten ihre Bedeutung; für eine Site muss nichts geändert werden.
- Seine Zustandsdatei wird übernommen: Hat ein Protokoll noch keinen Zustand, sucht der Zähler im
  Protokollverzeichnis `updateview-<Protokollname>.log` (der Name je Protokoll, den der Prototyp verwendete) und
  `updateview.log` und beginnt nach der dort notierten Zeile. Steht diese Zeile nicht mehr im Protokoll, zählt er
  nur Zeilen, die neuer sind als deren Zeitpunkt (`before-resume` im Bericht). Die alte Datei bleibt liegen und wird
  nicht mehr geschrieben.
- Die Zahlen können niedriger sein als vorher, mit Absicht: Robots, Fehler, Weiterleitungen und Modulseiten zählen
  nicht mehr, und die Startseite zählt für ihren eigenen Knoten statt für den Wurzelknoten. Um wie früher zu zählen,
  die Listen aus Abschnitt 8 leeren.
- Einzelheiten und Gründe: [Aufrufzähler 6.0.15](../bc/6.0/aufrufzaehler-fuer-mehrere-sites.md).

## 11. Fehlerbehebung

| Symptom | Ursache und Abhilfe |
|---|---|
| "no siteaccess has an access log to count" | `StorageDir` oder `LogFileName` ist für jeden gezählten Siteaccess leer. Setzen (Abschnitte 3 bis 5). |
| "the access log … does not exist or cannot be read" | Falscher Pfad, oder der Cronjob läuft als Benutzer, der die Protokolle des Webservers nicht lesen darf (Plesk: der Benutzer des Abonnements darf es, `root` auch). |
| Viele `not-content`-Zeilen | `--dry-run --top=20` listet die Pfade. Seiten, die es nicht mehr gibt, ein fehlender `PathPrefix[]` oder Modulseiten für `SkipPathPrefixes[]`. |
| Alles ist `other-siteaccess` | Der Host der Zeilen ist nicht der Host der Siteaccesses: `Host` setzen (combined-Format) oder `vhost_combined` verwenden. |
| Aufrufe einer Site für eine andere gezählt | Zwei Siteaccesses lesen ein Protokoll mit `MatchSiteAccess=disabled`; `auto` verwenden. |
| Warnungen "truncated" | Das Protokoll wird zwischen den Läufen geleert (Plesk-Statistik, `copytruncate`): öfter laufen lassen oder die Endung der Kopie in `RotatedSuffixes[]` aufnehmen. |
| Exit-Code 3 | Ein anderer Lauf las das Protokoll; er endet von selbst. `LockTimeout` erhöhen, wenn sich Läufe absichtlich überschneiden. |
| Ab jetzt zählen, nicht aus dem alten Protokoll | Einmal `./console exp:viewcount:update --skip-to-end`. |

## 12. Geprüft auf dem Demonstrationsserver

Jeder Siteaccess der Demonstrationsinstallation (elf Sites und neun Administrationen, `MatchOrder=uri;host`) las die
Protokolle in einem Probelauf:

```bash
./console exp:viewcount:update -s site --all-siteaccesses --dry-run --from-start \
    --log-file=/var/www/vhosts/<host>/logs/access_ssl_log.processed.1.gz --top=5
```

- Ein Apache-Protokoll vom Juni 2026 (50 570 Zeilen): 616 Aufrufe von 5 Knoten, 579 davon für die Startseite der
  Site (der Knoten ihrer `IndexPage`, nicht der Wurzelknoten); nicht gezählt: 30 601 Robots, 11 163 andere Status,
  4 722 Dateien, 3 132 andere Methoden, 298 Pfade inzwischen entfernter Seiten.
- Das Velocity-Protokoll vom September 2026 (75 906 Zeilen, meist ein Cache-Vorwärmer): 39 Aufrufe; 65 065
  Robot-Zeilen.
- Die Administrationen wurden nach der Regel des Menüs ausgelassen.
- Ein echter Lauf in eine Kopie der Datenbank (mit der Sicherung von SQLite erstellt, die laufende Datei nur lesend
  geöffnet) addierte genau die 616 Aufrufe; ein zweiter Lauf über dasselbe Protokoll addierte keinen; die laufende
  Tabelle blieb unverändert.
