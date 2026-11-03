# Aufrufzähler 6.0.15: Protokolle mehrerer Sites, Zustand und was als Aufruf zählt

Diese Seite ist für Sie, wenn Sie den Cronjob `updateviewcount.php` betreiben oder wenn Templates oder Extensions
`view_count` zeigen oder den Fetch `content/view_top_list` verwenden. Der Cronjob wurde für 6.0.15 neu gebaut, damit
Installationen mit mehreren Sites ihre Aufrufe richtig zählen; die Anleitung
[Seitenaufrufe zählen](../../anleitungen/seitenaufrufe-zaehlen.md) erklärt warum und wie man ihn einrichtet.

## Kurz gefasst

| | |
|---|---|
| Was sich geändert hat | Der Cronjob `updateviewcount.php` besteht jetzt aus den Klassen `expViewCount*` (`kernel/classes/viewcount/`) und läuft auch als `./console exp:viewcount:update`. Neue Einstellungen `logfile.ini [ViewCountSettings]` und `[AccessLogFileSettings] Host`, `Port`, `Hosts[]`; neue Methoden `eZViewCounter::addCounts()` und `eZSiteAccess::matchAddress()`. |
| Wen es betrifft | Installationen, die den Cronjob betreiben (er ist nicht von Haus aus aktiv). Sonst niemanden. |
| Was zu tun ist | Für eine Site nichts. Um mehrere Siteaccesses in einem Lauf zu zählen, siehe die Anleitung, Abschnitte 4 und 5. |
| Wie man es prüft | `./console exp:viewcount:update --dry-run --top=10 -s <siteaccess>` |

## Geändertes Verhalten

- **Der Zustand** ist nicht mehr `var/<site>/log/updateview.log` mit der zuletzt gelesenen Zeile, sondern
  `var/<site>/viewcount/<Protokoll>-<Hash>.json` (`[ViewCountSettings] StateDir`), eine Datei je Protokoll, darin
  Inode und Byte-Position jedes Siteaccess, und eine `.lock`-Datei daneben. Der erste Lauf über ein Protokoll ohne
  Zustand übernimmt die alte Datei (`updateview-<Protokollname>.log`, sonst `updateview.log`): Er beginnt nach der
  dort notierten Zeile oder zählt, wenn diese Zeile fehlt, nur neuere Zeilen. Die alte Datei bleibt liegen und wird
  nicht mehr geschrieben; nach dem ersten Lauf kann sie entfernt werden.
- **"/"** zählt für den Knoten der `site.ini [SiteSettings] IndexPage` des Siteaccess, nicht mehr für den
  Wurzelknoten (oder den obersten Knoten eines `PathPrefix`). Ist die `IndexPage` kein Knoten (`user/login`), wird
  "/" nicht gezählt.
- **Ein Pfad mit `PathPrefix`** wird erst mit jedem Präfix gesucht, dann, wie er ist; `PathPrefixExclude[]` gilt. Ist
  `logfile.ini PathPrefix[]` leer, wird der `PathPrefix` des Siteaccess verwendet.
- **`SitePrefix[]`** entfernt nur ein ganzes erstes Pfadelement (`mysite` gilt nicht mehr für `/mysite2/...`).
- **Nur Seitenaufrufe zählen**: `GET` mit Status `200` oder `304`, keine Robots (`BotUserAgents[]`, ein leerer
  User-Agent), keine Dateien (`SkipExtensions[]`), keine Modul- und Dienstpfade (`SkipPathPrefixes[]`). Der bisherige
  Cronjob zählte jede Zeile, deren Pfad Inhalt war, gleich mit welcher Methode, welchem Status oder User-Agent; für
  denselben Verkehr sind die Zahlen also niedriger. Leere Listen stellen das frühere Verhalten der jeweiligen Prüfung
  wieder her.
- **`/content/view/<Ansicht>/<Knoten>`** zählt für jede Ansicht, nicht nur `full` (`ViewModes[]` beschränkt es), und
  View-Parameter (`/(offset)/10`) und URL-Kodierung (`%C3%BC`) lassen einen Pfad seinen Knoten nicht mehr verfehlen.
- **Mehrere gezählte Siteaccesses in einem Protokoll** bekommen jede Zeile einmal, nach der Siteaccess-Erkennung des
  Kernels; der bisherige Cronjob zählte jede Zeile für jeden Lauf eines Siteaccess.
- **Rotation**: Der Rest der rotierten Kopie wird vor dem neuen Protokoll gelesen (`RotatedSuffixes[]`); ein
  gekürztes Protokoll wird mit einer Warnung von vorn gelesen.
- **Eine Transaktion je Lauf**: Alle Aufrufe eines Laufs werden zusammen geschrieben, der Zustand erst nach dem
  Commit. Knoten, die es nicht gibt, fallen mit einer Abfrage je 500 Knoten weg statt mit einer je Knoten.
- **Zeilen über 1023 Bytes** werden ganz gelesen (bis `MaxLineLength`, Vorgabe 64 KiB).
- **Ausgabe**: Der Cronjob gibt je Protokoll einen Bericht aus (gelesene Zeilen, Aufrufe, Gründe für nicht gezählte
  Zeilen, den Zustand) statt einer Zeile je Knoten.
- **Die Beschreibung des Cronjobs** spricht nicht mehr von einem "Zwischenspeicher der Aufrufe im Speicher"; einen
  solchen gibt es nicht. Setup > Cronjobs zeigt den neuen Text.

## Neue API

- `eZViewCounter::addCounts( array $counts )`: Knoten-ID => Aufrufe, in einer Transaktion addiert
  (`count = count + n` für vorhandene Zeilen, neue Zeilen für den Rest); gibt false zurück, wenn die Datenbank die
  Transaktion ablehnte.
- `eZSiteAccess::matchAddress( $uriString, $host, $port = 80 )`: der Siteaccess, der eine Adresse bedient hat, wie
  `match()` ihn findet, ohne die Weiterleitung von `RedirectOnNormalize` und ohne die Sprache des Browsers;
  `unmatched` ist gesetzt, wo nur `DefaultAccess` griff.
- `expViewCountUpdater`, `expViewCountConfiguration`, `expViewCountLogReader`, `expViewCountLogParser`,
  `expViewCountRequestFilter`, `expViewCountPathResolver`, `expViewCountState`, `expViewCountSite`: die Teile des
  Zählers, jeder für sich verwendbar (eine Extension kann mit ihrer eigenen Liste von `expViewCountSite` aus einer
  anderen Quelle zählen).

## Dank

Die Merkdatei je Protokoll, die Startseite, die für die `IndexPage` des Siteaccess zählt, und der Rückgriff auf den
Pfad ohne `PathPrefix` stammen aus einem Prototyp, den ein Site-Betreiber für eine Installation mit mehreren Sites
geschrieben hat.
