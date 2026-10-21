# Benchmarks für Exponential: Geschwindigkeit messen, vergleichen und absichern

Diese Anleitung zeigt jede Möglichkeit, die Geschwindigkeit einer Exponential-Installation mit `exp:benchmark` zu
messen: Seiten über HTTP durch Apache oder Velocity, aus dem Cache und gerendert; die Teile einer Seite innerhalb des
Kernels; reine PHP-Hot-Paths ohne Server und ohne Datenbank; gespeicherte Läufe, Baselines und Regressionen; die
Geschwindigkeitskarte der Demonstrationstour; und die Performance-Prüfung, die die CI des Repositorys bei jedem Push
ausführt. Sie erklärt, was jede Zahl bedeutet, wie Sie zwei Läufe oder zwei Maschinen vergleichen, ohne sich selbst zu
täuschen, und was zu tun ist, wenn eine Zahl falsch aussieht.

Sie richtet sich an Betreiber, die eine Site optimieren, und an Entwickler, die wissen wollen, was eine Änderung
kostet. Lesen Sie zuerst die Abschnitte 1 bis 3; danach steht jeder Abschnitt für sich. Jeder Befehl, jede Option und
jeder Standardwert unten wurde am Code geprüft, und jede Ausgabe ist echt: Sie wurde von den Befehlen auf dem
Demonstrationsserver (12 CPUs, geteilt mit anderer Arbeit bei einer Last von 5 bis 11) am 5. Oktober 2026 ausgegeben,
nur lesend, wobei lange Zeilen an den markierten Stellen gekürzt sind.

[Anleitungen](README.md) · Funktionsreferenz: [Benchmark: exp:benchmark](../../eng-us/features/6.0/benchmark.md) · Im
Installationshandbuch: [10.12 Performance-Tuning](../installation/10-nach-der-installation.md#1012-performance-tuning)

## Kurz gesagt

- `./console exp:benchmark --allow-root-user` ruft die Startseite, drei Menüseiten, die Suchseite und die
  Anmeldeseite der Administration jeweils 100-mal nach 5 Aufwärmanfragen ab, 4 gleichzeitig, und gibt pro Seite
  Median, p90, p99, Minimum, Maximum und Standardabweichung aus.
- `--compare=https://site,https://site:8080` misst zwei Server unmittelbar nacheinander (Apache mit PHP-FPM gegen
  Velocity); `--cold` misst gerenderte Seiten statt Cache-Treffern.
- `exp:benchmark kernel` misst die Teile einer Seite im selben Prozess; `exp:benchmark micro` misst reine
  PHP-Hot-Paths ohne Webserver und ohne Datenbank, normalisiert gegen eine Kalibrierungsschleife, sodass Läufe auf
  verschiedenen Maschinen vergleichbar sind.
- `--save=run.json` bewahrt einen Lauf auf, `--baseline=run.json` vergleicht damit und endet bei einer Regression mit
  Exit-Code 1, `--format=json` gibt alles aus, einschließlich der Umgebung, in der der Lauf gemacht wurde.
- Ein einzelner Lauf auf einer ausgelasteten Maschine beweist wenig: Vergleichen Sie Läufe, die unmittelbar
  nacheinander gemacht wurden, mit denselben Anzahlen, denselben Seiten und derselben PHP-Konfiguration. Abschnitt 10
  enthält eine Checkliste.
- Die CI des Repositorys führt den Micro-Modus gegen `tests/benchmark/baseline.json` als Prüfung aus, die nur warnt
  (Abschnitt 13).

## Inhalt

- [1. Was exp:benchmark misst und was nicht](#1-was-expbenchmark-misst-und-was-nicht)
- [2. Bevor Sie beginnen](#2-bevor-sie-beginnen)
- [3. Die Modi im Überblick](#3-die-modi-im-überblick)
- [4. HTTP-Modus: Seiten so, wie ein Besucher sie erhält](#4-http-modus-seiten-so-wie-ein-besucher-sie-erhält)
- [5. Kernel-Modus: die Teile einer Seite](#5-kernel-modus-die-teile-einer-seite)
- [6. Micro-Modus: Hot Paths ohne Server und ohne Datenbank](#6-micro-modus-hot-paths-ohne-server-und-ohne-datenbank)
- [7. Ausgabe: Tabelle, JSON und CSV](#7-ausgabe-tabelle-json-und-csv)
- [8. Die Zahlen lesen](#8-die-zahlen-lesen)
- [9. Läufe vergleichen: Baselines und Regressionen](#9-läufe-vergleichen-baselines-und-regressionen)
- [10. Rauschen und wie man es verringert](#10-rauschen-und-wie-man-es-verringert)
- [11. Arbeitsabläufe Schritt für Schritt](#11-arbeitsabläufe-schritt-für-schritt)
- [12. Die Geschwindigkeitskarte der Tour und „Erneut messen“](#12-die-geschwindigkeitskarte-der-tour-und-erneut-messen)
- [13. Die Performance-Prüfung der CI](#13-die-performance-prüfung-der-ci)
- [14. Fehlerbehebung](#14-fehlerbehebung)
- [15. Alle Optionen](#15-alle-optionen)
- [Quellen](#quellen)

## 1. Was exp:benchmark misst und was nicht

| Es misst | Es misst nicht |
|---|---|
| Die Zeit vom Senden einer Anfrage bis zum Eintreffen des letzten Bytes der Antwort, pro Anfrage, von curl selbst gemessen (HTTP-Modus) | Was ein Browser danach tut: Bilder, Stylesheets, Skripte, Layout, Zeichnen. Verwenden Sie dafür die Entwicklerwerkzeuge des Browsers oder Lighthouse. |
| Den Antwort-Cache, den View-Cache und das Rendern, je nachdem, was antwortet (warm oder `--cold`) | Das Netzwerk zwischen einem Besucher und dem Server: Es misst von dort aus, wo es läuft, meist vom Server selbst |
| Wie viele Anfragen pro Sekunde ein Server bei der gewählten Parallelität beantwortet | Das Maximum, das ein Server tragen kann: Der Standardwert von 4 gleichzeitig ist höflich, kein Lasttest |
| Die eigenen Kosten des Kernels für Boot, Rendern, INI-, Datenbank- und Cache-Operationen, im selben Prozess (Kernel-Modus) | Seiten für angemeldete Benutzer, außer Sie übergeben das Sitzungs-Cookie (`--cookie`) |
| Reine PHP-Codepfade mit fester Arbeit, unabhängig von Inhalt, Datenbank und Server (Micro-Modus) | Alles, was schreibt: Jeder Modus ist bis auf Cache-Einträge nur lesend (siehe Abschnitt 2) |

Der Befehl beantwortet drei Fragen gut: *Wie lange wartet ein Besucher auf diese Seite*, *welcher von zwei Servern
beantwortet sie schneller, gemessen in derselben Minute*, und *hat diese Änderung irgendetwas langsamer gemacht*. Er
beantwortet nicht die Frage „wie viele Besucher verträgt diese Maschine“; dafür braucht es einen Lastgenerator gegen
eine Kopie der Site, und `--tool=wrk` oder `--tool=oha` (Abschnitt 4.6) ist der Anfang davon, nicht das Ende.

## 2. Bevor Sie beginnen

Führen Sie jeden Befehl aus dem Wurzelverzeichnis der Installation aus (dem Verzeichnis mit `console`, `index.php` und
`settings/`). `./console exp:benchmark` und `php bin/php/benchmark.php` sind derselbe Befehl; der Kurzname
`exp:bench` funktioniert ebenfalls. Die Beispiele fügen `--allow-root-user` hinzu, weil der Demonstrationsserver sie
als root ausführt; lassen Sie es weg, wenn Sie als Benutzer der Site arbeiten. Als root gibt der Befehl zuerst
`With great power comes great responsibility.` aus und wartet zehn Sekunden.

Was er braucht: PHP mit der curl-Extension. Kein `ab`, kein `wrk`, kein externer Dienst. HTTP- und Kernel-Modus
brauchen die Datenbank und die Einstellungen der Installation; der Micro-Modus braucht keins von beiden.

Exit-Codes, für Skripte:

| Code | Bedeutung |
|---|---|
| 0 | der Lauf wurde beendet; mit `--baseline` keine Regression |
| 1 | eine Kennzahl hat sich gegenüber `--baseline` verschlechtert (oder, bei `compare`, gegenüber der ersten Datei) |
| 2 | ein Bedienfehler, eine Datei, die nicht gelesen werden kann, oder ein Lauf, der von den Sicherheitsregeln abgelehnt wurde |

Die Sicherheitsregeln, damit ein erster Versuch nie einer Site schadet:

| Regel | Warum |
|---|---|
| Der HTTP-Modus sendet nur `GET`, keine Cookies außer mit `--cookie`, und sendet nie POST-Anfragen | Messen verändert nie Inhalte |
| Gegen einen anderen Host als die Loopback-Adresse dieser Maschine brauchen mehr als 1000 Anfragen pro URL, mehr als 16 gleichzeitig, ein Aufwärmen über 100 oder mehr als 20 000 Anfragen insgesamt `--force` | ein Tippfehler soll nicht zum Lasttest des Produktivbetriebs werden |
| Die Standard-Seitenliste wird nur an Hosts gesendet, die diese Installation bedient (die `SiteURL` ihrer Siteaccesses und die Host-Matching-Einstellungen); ein anderer Host muss mit `--url` oder `--urls-file` benannt werden | Sie können die Standardwerte nicht versehentlich auf den Server eines anderen richten |
| TLS-Zertifikate werden geprüft; `--insecure` schaltet das für einen Server mit selbstsigniertem Zertifikat ab | |
| Der Kernel-Modus liest nur; seine Schreibvorgänge sind seine eigene Cache-Testdatei (die wieder entfernt wird) und die Template-Cache-Blöcke, die eine vollständige Ansicht füllt, wie es auch ein Besucher täte | |
| Der Micro-Modus schreibt kompilierte Templates und ein Fixture-Template nach `<cache directory>/benchmark-micro/` und entfernt das Verzeichnis am Ende | |
| `--cold-clear` ist die einzige Option, die etwas leert: den Inhalts-View-Cache dieser Installation, einmal, vor dem Lauf. Sie wird für jeden Server abgelehnt, den diese Installation nicht bedient. | nicht auf einem Server verwenden, auf dem andere Personen arbeiten |

## 3. Die Modi im Überblick

| Modus | Befehl | Misst | Braucht |
|---|---|---|---|
| http (Standard) | `exp:benchmark` | Seiten über HTTP, ein Server oder mehrere unmittelbar nacheinander | eine laufende Site |
| kernel | `exp:benchmark kernel` | Boot, Rendern kalt und warm, INI, Abruf von Inhalten, Knotenliste, Datenbank, Cache, Bild-Alias, im selben Prozess | die Datenbank |
| micro | `exp:benchmark micro` oder `--micro` | Kompilieren und Rendern von Templates, INI-Parsing, Autoload, `eZURI`, Übersetzungen, Datentyp-Validierung | nur PHP und die Composer-Pakete |
| compare | `exp:benchmark compare <baseline.json> <run.json>` | zwei gespeicherte Läufe gegeneinander, misst nichts | zwei gespeicherte Läufe |
| show | `exp:benchmark show <run.json>` | gibt einen gespeicherten Lauf als Tabelle aus (oder `--format=json/csv`) | einen gespeicherten Lauf |

Das erste Wort nach den Optionen ist der Modus; ohne eines ist es `http`, und `--micro` ist dasselbe wie `micro`.

## 4. HTTP-Modus: Seiten so, wie ein Besucher sie erhält

### 4.1 Die Seitenliste

Ohne `--url` wird die Liste aus der Site selbst erstellt, in dieser Reihenfolge:

1. die Startseite `/`;
2. die ersten Seiten des Menüs, also Inhaltsseiten: die Kinder des Startseitenknotens (`[SiteSettings]
   IndexPage`), die einen URL-Alias haben, sortiert nach Priorität und dann nach Knoten-ID. `--pages=3` ist der
   Standard, `--pages=0` lässt sie weg;
3. die Suchseite `/content/search?SearchText=<word>`, wenn anonyme Besucher suchen dürfen (die Ansicht erfordert
   `content/read`, oder `content/search` steht in `[RoleSettings] PolicyOmitList`). Das Wort ist das erste Wort mit
   drei oder mehr Buchstaben im Namen der ersten Menüseite, `exponential`, wenn es keines gibt, oder das, was
   `--search=<words>` angibt. `--no-search` lässt die Seite weg;
4. die Anmeldeseite der Administration `/<admin siteaccess>/user/login`, wenn Siteaccesses über die URI erkannt
   werden. `--no-admin` lässt sie weg.

Der Server ist `https://` plus die `SiteURL` des Siteaccess (`-s <siteaccess>` wählt einen anderen) oder
`--base=<url>`.

Die Liste ist in jedem Lauf dieselbe, solange der Inhalt derselbe ist. Für eine Liste, die sich nie ändert, benennen
Sie sie:

```bash
./console exp:benchmark --url=/ --url=/news --url=/news/some-article --allow-root-user
./console exp:benchmark --urls-file=benchmark-urls.txt --allow-root-user     # one URL or path per line, # comments
./console exp:benchmark --url=https://staging.example.com/ --allow-root-user  # an absolute URL names its own server
```

Bewahren Sie diese Datei neben der Baseline auf, zu der sie gehört.

### 4.2 Aufwärmen und gemessene Läufe

Für jede Seite und jeden Server sendet der Befehl zuerst `--warmup=5` Anfragen, die nicht gemessen werden, dann
`--requests=100` gemessene (`--runs` ist dieselbe Option unter einem zweiten Namen), `--concurrency=4` gleichzeitig.
Das Aufwärmen umfasst mindestens eine Anfrage pro Verbindung, mit `--concurrency=8` also 8, auch wenn `--warmup`
weniger angibt: Jede Verbindung ist vor der ersten gemessenen Anfrage offen, und keine gemessene Anfrage bezahlt einen
TLS-Handshake.

Die gemessenen Anfragen werden auf `--rounds=2` Runden aufgeteilt. Mit einem Server ändert das nichts; mit mehreren
(Abschnitt 4.4) gibt jede Runde jedem Server seinen Anteil unmittelbar nacheinander, und die Reihenfolge wird in jeder
zweiten Runde umgekehrt.

Für Zahlen, die von einem Lauf zum nächsten vergleichbar sind, lassen Sie `--runs`, `--warmup` und `--concurrency`
gleich. Mehr Läufe machen die Ausläufer stabiler: Mit 100 Läufen ist p99 fast das Maximum; mit 1000 beginnt es, etwas
zu bedeuten.

### 4.3 Aus dem Cache und gerendert: warm, `--cold`, `--cold-clear`

| | Was antwortet | Option |
|---|---|---|
| **warm** (Standard) | was ein wiederkehrender Besucher erhält: der Antwort-Cache von Velocity oder der HTTP-Cache, wenn die Seite dort im Cache liegt, sonst der View-Cache | keine |
| **kalt** | die Anwendung: Jede Anfrage trägt einen Query-String, den kein Cache gesehen hat (`?_bench=<unique>`), und an dem geht jeder Antwort-Cache von Exponential vorbei (`X-Exp-Cache: BYPASS (query string)`) | `--cold` |
| **kalt, leerer View-Cache** | die erste Anfrage jeder Seite rendert von Grund auf | `--cold-clear` (leert den Inhalts-View-Cache einmal und überspringt das Aufwärmen) |

`--cold` verwendet weiterhin den View-Cache und die kompilierten Templates, wie es die meisten echten Cache-Fehlschläge
tun. Mit `--cold-clear` zeigt sich die teure erste Anfrage in `max` und `p99`, und der Rest des Laufs ist wieder warm.

Die Spalte `cache` zählt die Antworten von `X-Exp-Cache` (oder `X-Cache`): `HIT:40` bedeutet, dass jede Anfrage aus
dem Antwort-Cache kam, `BYPASS:20`, dass jede Anfrage an ihm vorbeiging, `-`, dass der Server keinen solchen Header
gesendet hat.

### 4.4 Zwei Server unmittelbar nacheinander: Apache und Velocity

```bash
./console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 --runs=40 --warmup=5 \
    --save=var/benchmark/ab.json --allow-root-user
```

Auf dem Demonstrationsserver antwortet Apache mit PHP-FPM auf 443 und Velocity auf 8080, dieselbe Installation und
dieselbe Datenbank. Die Ausgabe (ohne die Rundenzeilen):

```
exp:benchmark http  6 URL(s) x 2 server(s), 40 requests each, concurrency 4, warm-up 5, warm, tool curl  (installed: ab)
  saved: var/benchmark/ab.json
  http mode, web.example.com, PHP 8.5.11, 2026-10-05T19:30:26-07:00
  php-cli, OPcache off, Xdebug off, 12 CPUs, load 9.59, commit 3a5029891e
  times in milliseconds; req/s counts successful requests; sd = standard deviation
  target                 url                                  n   min  median   p90   p99   max    sd  req/s  err  status  bytes  cache
  ---------------------  ----------------------------------  --  ----  ------  ----  ----  ----  ----  -----  ---  ------  -----  ------
  www.example.com        /                                   40  7.51    10.7  18.0  29.7  30.1  5.31    307    0  200:40  16.6k  HIT:40
  www.example.com:8080   /                                   40  1.16    2.55  3.44  4.17  4.35  0.69   1110    0  200:40  15.3k  HIT:40
  www.example.com        /fitness                            40  6.53    8.65  10.9  13.2  13.4  1.59    415    0  200:40  11.9k  HIT:40
  www.example.com:8080   /fitness                            40  0.85    1.38  1.80  2.62  2.85  0.39   2260    0  200:40  11.2k  HIT:40
  www.example.com        /workout                            40  7.06    9.62  12.0  13.5  14.1  1.53    379    0  200:40  11.4k  HIT:40
  www.example.com:8080   /workout                            40  0.63    1.27  2.12  2.40  2.45  0.45   2157    0  200:40  10.8k  HIT:40
  www.example.com        /fit-healthy-site-info              40  7.48    10.5  15.7  16.5  16.5  2.66    333    0  200:40   9.7k  HIT:40
  www.example.com:8080   /fit-healthy-site-info              40  0.72    1.84  2.71  3.05  3.13  0.61   1671    0  200:40   9.2k  HIT:40
  www.example.com        /content/search?SearchText=fitness  40   285     334   396   407   409  32.8   11.3    0  200:40  11.1k  -
  www.example.com:8080   /content/search?SearchText=fitness  40  1.01    2.06  3.73  4.16  4.17  0.98   1302    0  200:40  10.6k  HIT:40
  www.example.com        /admin/user/login                   40  35.8    43.6  51.6  55.5  55.8  4.94   87.1    0  200:40   7.9k  -
  www.example.com:8080   /admin/user/login                   40  34.2    39.9  49.6  92.6   101  12.3   87.9    0  200:40   7.9k  -

  A/B, measured back to back (B/A below 1 means B is faster for the median, above 1 more req/s):
  url                                 A                 B                      A median  B median    B/A  A req/s  B req/s      B/A
  /                                   www.example.com   www.example.com:8080       10.7      2.55  x0.24      307     1110    x3.61
  /fitness                            www.example.com   www.example.com:8080       8.65      1.38  x0.16      415     2260    x5.45
  /content/search?SearchText=fitness  www.example.com   www.example.com:8080        334      2.06  x0.01     11.3     1302  x114.90
  /admin/user/login                   www.example.com   www.example.com:8080       43.6      39.9  x0.91     87.1     87.9    x1.01
  (two rows of the A/B table left out)
```

Was das besagt: Bei Seiten aus dem Cache antwortet Velocity in 1,3 bis 2,6 ms gegenüber 8,7 bis 10,7 ms bei Apache.
Die Suchseite wird vom Antwort-Cache von Velocity ausgeliefert (`HIT:40`) und von Apache jedes Mal gerendert (`-`),
daher der Faktor 115: Das vergleicht einen Cache mit einem Rendervorgang, nicht zwei Engines. Die Anmeldeseite der
Administration wird nie gecacht; dort liegen die beiden innerhalb von 10 %.

Dieselben Seiten gerendert, an jedem Antwort-Cache vorbei:

```bash
./console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 --url=/ --url=/fitness \
    --cold --runs=20 --warmup=2 --allow-root-user
```

```
  target                 url        n   min  median   p90   p99   max    sd  req/s  err  status  bytes  cache
  ---------------------  --------  --  ----  ------  ----  ----  ----  ----  -----  ---  ------  -----  ---------
  www.example.com        /         20   139     158   193   206   208  19.4   20.3    0  200:20  16.9k  BYPASS:20
  www.example.com:8080   /         20   196     258  2014  2046  2049   652   7.02    0  200:20  15.2k  BYPASS:20
  www.example.com        /fitness  20  77.7    94.2   113   126   128  12.6   35.1    0  200:20  12.0k  BYPASS:20
  www.example.com:8080   /fitness  20   103     131   149   157   158  16.3   25.8    0  200:20  11.2k  BYPASS:20
```

Hier ist die Reihenfolge umgekehrt: Beim Rendern war Apache in diesem Lauf schneller. Und die Velocity-Zeile von `/`
hat eine Standardabweichung (652 ms), die größer ist als ihr Median, und ein p90 von 2 Sekunden: Einige Anfragen
haben etwa zwei Sekunden gewartet, der Rest dauerte etwa 250 ms. Das ist das Muster von etwas Gelegentlichem (ein
Worker, der gestartet wird, eine Sperre, ein Cache, der neu aufgebaut wird), nicht von langsamem Code; messen Sie diese
Seite erneut, bevor Sie irgendetwas folgern, und suchen Sie im Log des Servers nach den langsamen Anfragen. Abschnitt 8
erklärt, wie solche Zeilen zu lesen sind.

Die Lehre aus beiden Tabellen zusammen: Eine einzelne Zahl für „welcher Server ist schneller“ führt in die Irre.
Messen Sie aus dem Cache und gerendert, und beurteilen Sie jede Seite nach ihrer eigenen Zeile.

### 4.5 Anfragen, Verbindungen und Header

| Option | Standard | Wirkung |
|---|---|---|
| `--concurrency=N` | 4 | gleichzeitige Anfragen, pro Server |
| `--no-keepalive` | Keep-Alive an | eine neue Verbindung für jede Anfrage, um auch den TLS-Handshake zu messen |
| `--http=1.1`, `--http=2` | was curl aushandelt | die HTTP-Version |
| `--encoding=gzip` | `gzip` | das gesendete `Accept-Encoding`; `--encoding=br` oder `none` für keines |
| `--timeout=30` | 30 | Sekunden pro Anfrage; eine Anfrage, die länger dauert, zählt als Fehler |
| `--auth=user:password` | keine, oder die Variable `EXP_BENCHMARK_AUTH` | HTTP-Basic-Authentifizierung, z. B. für eine Staging-Site; setzen Sie die Variable, um das Passwort aus der Shell-Historie herauszuhalten. Wird nie gespeichert. |
| `--header="Name: value"` | keiner | ein zusätzlicher Header; für mehrere wiederholen. Werte von `Cookie` und `Authorization` werden als `(hidden)` gespeichert. |
| `--cookie="..."` | keines | ein `Cookie`-Header, z. B. ein Sitzungs-Cookie, um eine Seite für angemeldete Benutzer zu messen |
| `--resolve=host:port:address` | keine | einen Host unter einer anderen Adresse erreichen, wie `curl --resolve` |
| `--insecure` | prüfen | TLS-Zertifikate nicht prüfen |

### 4.6 Externe Lastgeneratoren: `--tool=ab|wrk|oha`

Wenn `ab`, `wrk` oder `oha` installiert ist (die Kopfzeile listet auf, was gefunden wurde: `(installed: ab)`), übergibt
`--tool=` jede Seite an dieses Werkzeug statt an den eingebauten Client und wertet dessen Zusammenfassung in dieselben
Zeilen aus. Nur was das Werkzeug meldet, wird ausgefüllt: Die Tabelle zeigt `(ab)` dort, wo die Cache-Spalte stünde,
`wrk` läuft `--duration=10` Sekunden lang statt einer Anzahl von Anfragen, und keines von ihnen wechselt die Server in
Runden ab. Verwenden Sie den eingebauten Client für Vergleiche und ein externes Werkzeug, wenn Sie dessen eigenen
Bericht oder eine höhere Last wollen.

## 5. Kernel-Modus: die Teile einer Seite

```bash
./console exp:benchmark kernel --allow-root-user
./console exp:benchmark kernel --repeat=10 --boot-repeat=3 --allow-root-user
./console exp:benchmark kernel --probe=render_warm --probe=db_query --node=144 --allow-root-user
```

| Messpunkt | Was ein Durchlauf ist |
|---|---|
| `boot` | ein neuer PHP-Prozess von seinem Start bis zur Verbindung mit der Datenbank: Autoload, Einstellungen, Siteaccess, Extensions |
| `process` | derselbe Kindprozess vom Start bis zum Ende, wie ihn der Elternprozess gesehen hat (enthält `render_cold`) |
| `render_cold` | die vollständige Ansicht eines Knotens, einmal in diesem frischen Prozess gerendert |
| `render_warm` | dieselbe vollständige Ansicht im eigenen Prozess des Benchmarks, nach einem nicht gemessenen Rendervorgang |
| `ini_load` | `content.ini` erneut aus dem INI-Cache gelesen |
| `content_fetch` | ein Inhaltsobjekt und seine Attribute aus der Datenbank |
| `node_list` | bis zu 20 Kinder des Startseitenknotens |
| `db_query` | ein indiziertes `SELECT` auf der Knotentabelle |
| `cache_write`, `cache_read` | ein Eintrag von 16 KB, über den Cluster-Datei-Handler geschrieben und gelesen |
| `image_alias` | ein Bildattribut abgerufen und einer seiner vorhandenen Aliase nachgeschlagen (nie einer, der skaliert werden müsste) |

`boot`, `process` und `render_cold` starten `--boot-repeat=5` Prozesse; die anderen Messpunkte laufen `--repeat=20`-mal.
Ohne `--node` verwenden die Render-Messpunkte die Startseite, außer ihre vollständige Ansicht ist fast leer (eine aus
Layout-Blöcken gebaute Startseite rendert diese im Pagelayout); dann die Menüseite mit der größten vollständigen
Ansicht. `--probe` (wiederholbar) führt nur die genannten Messpunkte aus. Ausgabe auf dem Demonstrationsserver:

```
  kernel mode, web.example.com, PHP 8.5.11, 2026-10-05T19:38:55-07:00
  php-cli, OPcache off, Xdebug off, 12 CPUs, load 11.63, commit 38e10264bf
  probe           n   min  median   p90   p99   max    sd  ops/s  err  mem peak  note
  -------------  --  ----  ------  ----  ----  ----  ----  -----  ---  --------  ----------------------------------------------
  boot            3  66.7    74.2  75.1  75.3  75.3  4.66   13.9    0         -  new process until the database is connected
  process         3   455     531   542   545   545  48.6   1.96    0     16.0M  whole child process, start to exit
  render_cold     3   126     140   155   159   159  16.5   7.05    0     16.0M  node 26210, first full view in a fresh process
  render_warm    10  38.7    47.0  53.4  60.2  60.9  6.63   21.2    0    512.1k  node 26210, full view, 36.1 KB of HTML
  ini_load       10  0.29    0.31  0.38  0.67  0.70  0.12   2837    0     92.5k  content.ini from the INI cache
  content_fetch  10  0.05    0.06  0.16  0.92  1.01  0.30   6526    0     21.3k  object 25576 with its attributes
  node_list      10  0.23    0.26  0.59  2.62  2.84  0.81   1885    0         0  up to 20 children of node 89
  db_query       10  0.00    0.00  0.05  0.24  0.26  0.08  31798    0      2.9k  one indexed SELECT, sqlite
  cache_write    10  0.11    0.17  0.32  0.62  0.65  0.16   4387    0      1.2k  eZFSFileHandler
  cache_read     10  0.03    0.03  0.06  0.16  0.17  0.04  20965    0     29.3k  eZFSFileHandler
  image_alias    10  0.61    0.73  3.32  17.1  18.6  5.65    389    0    541.1k  attribute 172, alias small
```

Lesen Sie es von oben: Ein frischer Prozess kostet 74 ms, bevor er irgendetwas tun kann, das erste Rendern darin
140 ms, dasselbe Rendern in einem warmen Prozess 47 ms. Diese Differenz ist das, was persistente Worker (Velocity,
PHP-FPM) und der Opcode-Cache einsparen. `ops/s` ist 1000 geteilt durch den Mittelwert. `mem peak` ist der Speicher,
den der Messpunkt über das hinaus brauchte, was vorher belegt war (unter PHP 8.0 und 8.1, die den Spitzenwert nicht
zurücksetzen können, der bisherige Spitzenwert des Prozesses). Die Notiz nennt den verwendeten Knoten, das Objekt, den
Alias und die Datenbank, sodass ein späterer Lauf gegen dieselben geprüft werden kann.

Kernel-Zahlen sind das, was eine Anfrage kostet, bevor Webserver, Antwort-Cache und Netzwerk etwas hinzufügen oder
wegnehmen: das Maß dafür, was eine Codeänderung kostet. HTTP-Zahlen sind das, was ein Besucher erhält.

## 6. Micro-Modus: Hot Paths ohne Server und ohne Datenbank

```bash
./console exp:benchmark micro --allow-root-user
./console exp:benchmark --micro --repeat=50 --warmup=5 --allow-root-user
./console exp:benchmark micro --probe=template_compile --probe=ini_parse --allow-root-user
php bin/php/benchmark.php micro --format=json > micro.json      # no console, no database
```

Der Micro-Modus misst die reine PHP-Arbeit des Kernels mit fester Eingabe: Nichts hängt vom Inhalt, von der
Datenbank, vom Webserver oder von den Override-Einstellungen der Installation ab, sodass ein Laptop, ein Server und ein
CI-Runner alle dieselbe Arbeit messen. Jeder Messpunkt erledigt pro Iteration eine feste Menge an Arbeit, führt
`--warmup=3` Iterationen ungemessen und `--repeat=30` gemessen aus:

| Messpunkt | Eine Iteration (Operationen) | Wofür er steht |
|---|---|---|
| `template_compile` | fünf mitgelieferte Templates (`navigator/google.tpl`, beide Pagelayouts, `content/edit.tpl`, `node/view/full.tpl`) geparst und zu PHP kompiliert, jedes Mal neu (5) | die erste Anfrage nach einer Template-Änderung oder dem Leeren eines Caches |
| `template_render` | das kompilierte `navigator/google.tpl` und ein festes Template aus Schleifen, Bedingungen, Operatoren, Escaping und einer Übersetzung, mit festen Variablen gerendert (2) | jeder Seitenaufruf, der kein Cache-Treffer ist |
| `ini_parse` | `site.ini`, `content.ini`, `template.ini`, `i18n.ini`, `image.ini`, `design.ini` und `menu.ini` aus `settings/` mit `eZINI::parseFile()` geparst, kein INI-Cache, keine Override-Verzeichnisse (7) | eine Anfrage, nachdem der INI-Cache geleert wurde |
| `autoload_map` | `autoload/ezp_kernel.php` gelesen (1) | das erste Nachschlagen einer Klasse in jeder Anfrage |
| `autoload_miss` | `class_exists()` für Namen, die niemand definiert, durch jeden registrierten Autoloader (2000) | jede Prüfung „gibt es diese Klasse“ |
| `uri_parse` | zehn Modul-URLs mit geordneten und benannten View-Parametern, von `eZURI` geparst, 50-mal (500) | der Beginn jeder Anfrage |
| `i18n_load` | `share/translations/ger-DE/translation.ts` (2,4 MB) geparst, kein Übersetzungs-Cache, keine Übersetzungen von Extensions (1) | eine Anfrage, nachdem der Übersetzungs-Cache geleert wurde |
| `i18n_lookup` | 300 Meldungen dieser Datei nach Kontext und Quelltext nachgeschlagen, 10-mal (3000) | jeder übersetzte Text |
| `datatype_validate` | 18 gesendete Eingaben, validiert von den Datentypen für Ganzzahl, Gleitkommazahl, Textzeile, E-Mail und Datum (gültige und ungültige Werte) und den Klasseneinstellungen der Ganzzahl, 20-mal (360) | jedes Speichern eines Bearbeitungsformulars |

Die Attribute der Inhaltsklasse, die die Validierung braucht, werden im Speicher aufgebaut; nichts fragt die Datenbank
ab.

Vor den Messpunkten und noch einmal danach wird eine **Kalibrierungsschleife** gemessen: feste reine PHP-Arbeit
(Ganzzahlarithmetik, Aufbau von Zeichenketten, Sortieren von 12 000 Zeichenketten, ein regulärer Ausdruck, Hashing, ein
assoziatives Array). Der Median jedes Messpunkts geteilt durch den Kalibrierungsmedian ist sein Wert `x calib`. Auf
einer doppelt so schnellen Maschine halbieren sich beide, und das Verhältnis bleibt; dieses Verhältnis vergleicht
Abschnitt 13.

```
exp:benchmark micro  30 measured and 3 warm-up iterations per probe, PHP 8.5.11, OPcache off
  micro mode, web.example.com, PHP 8.5.11, 2026-10-05T19:33:47-07:00
  php-cli, OPcache off, Xdebug off, 12 CPUs, load 5.55, commit ec5699e4d1
  times in milliseconds per iteration; ops/s at the median; x calib = median / calibration loop median (9.359 ms, drift -7.8 %)
  probe               n   ops   min  median   p90   p99   max    sd    ops/s  x calib  err  note
  -----------------  --  ----  ----  ------  ----  ----  ----  ----  -------  -------  ---  ------------------------------
  template_compile   30     5  56.6    65.3  82.6  85.6  85.9  8.94     76.6    6.978    0  5 shipped templates, parsed ...
  template_render    30     2  4.47    6.24  6.93  7.48  7.52  0.68      320    0.667    0  2 templates rendered, compiled
  ini_parse          30     7  4.32    5.23  5.87  6.13  6.16  0.47     1337    0.559    0  7 files of settings/ (...)
  autoload_map       30     1  0.73    0.82  0.93  1.00  1.00  0.07     1217    0.088    0  autoload/ezp_kernel.php, 1322 classes
  autoload_miss      30  2000  0.84    0.96  1.09  1.16  1.18  0.09  2092688    0.102    0  2 registered autoloaders
  uri_parse          30   500  2.21    2.72  3.37  4.02  4.16  0.42   183599    0.291    0  10 module URLs, ...
  i18n_load          30     1   179     197   211   233   241  13.1     5.08   21.046    0  share/translations/ger-DE/...
  i18n_lookup        30  3000  3.27    3.39  3.77  4.08  4.12  0.22   885768    0.362    0  300 messages of ger-DE, 10 times
  datatype_validate  30   360  3.07    3.97  4.64  5.47  5.78  0.54    90725    0.424    0  18 inputs 20 times: ...
```

(Die Notizen sind gekürzt.) Dieselben Messpunkte auf einem GitHub-Actions-Runner, dessen Lauf als
`tests/benchmark/baseline.json` committet ist, brauchten 19,3 ms für `template_compile` und 91 ms für `i18n_load` bei
einem Kalibrierungsmedian von 5,3 ms und einer Standardabweichung von höchstens 2 % des Medians: Ein dedizierter
Runner ist viel stabiler als ein geteilter Server, und deshalb kann die CI-Prüfung überhaupt vergleichen.

- Die Zeiten sind Millisekunden pro Iteration; `ops` ist die Arbeit, die eine Iteration erledigt; `ops/s` sind
  Operationen pro Sekunde beim Median (`ops × 1000 / median`).
- Die Kopfzeile nennt den Kalibrierungsmedian und seine **Drift**, die Veränderung zwischen der Schleife vor und nach
  den Messpunkten. Mehr als etwa 10 % bedeutet, dass sich die Last der Maschine während des Laufs geändert hat; führen
  Sie ihn erneut aus.
- `template_render` meldet `compiled` in seiner Notiz, wenn die Template-Kompilierung eingeschaltet ist, wie im
  Produktivbetrieb; ist die Kompilierung unter `[TemplateSettings]` ausgeschaltet, meldet er `interpreted`, und seine
  Zahlen sind nicht mit einem kompilierten Lauf vergleichbar.

Der Micro-Modus ist für die Frage „hat diese Änderung den Code des Kernels langsamer gemacht“ gedacht, in der CI und
auf einem Entwicklerrechner. Über Caches, die Datenbank oder einen Server sagt er nichts; dafür verwenden Sie den
Kernel- und den HTTP-Modus.

## 7. Ausgabe: Tabelle, JSON und CSV

`--format=table` (der Standard) gibt die oben gezeigten Tabellen aus. `--format=json` (oder `--json`) gibt den ganzen
Lauf als ein JSON-Dokument auf stdout aus, `--format=csv` (oder `--csv`) eine Zeile pro Tabellenzeile mit einer
Kopfzeile. Bei JSON oder CSV gehen die Fortschrittszeilen nach stderr, sodass die Ausgabe weitergeleitet oder
umgeleitet werden kann; `-q` schaltet den Fortschritt ganz ab. `--save=<file>` schreibt das JSON-Dokument in jedem
Format in eine Datei und legt dabei das Verzeichnis an, und `show <file>` gibt einen gespeicherten Lauf erneut in jedem
Format aus.

```bash
./console exp:benchmark --runs=200 --warmup=10 --format=json --allow-root-user > var/benchmark/run.json
./console exp:benchmark show var/benchmark/run.json --format=csv -q --allow-root-user > run.csv
```

### 7.1 Das JSON-Dokument

| Feld | Inhalt |
|---|---|
| `tool`, `format` | `exp:benchmark` und die Formatversion, `1`. Eine Baseline einer anderen Formatversion wird abgelehnt, statt falsch verglichen zu werden. |
| `mode` | `http`, `kernel` oder `micro` |
| `started`, `machine`, `php`, `exponential` | wann, wo (Hostname), mit welchem PHP und welchem Exponential |
| `settings` | die Optionen, mit denen der Lauf gemacht wurde: Anzahlen, Ziele, kalt, Header (Geheimnisse verborgen), Messpunkte |
| `environment` | siehe 7.2 |
| `rows` | eine pro gemessener Seite und Server, Kernel-Messpunkt oder Micro-Messpunkt; siehe 7.3 |
| `pairs` | http mit `--compare`: pro Seite jeder spätere Server gegen den ersten (`base_median`, `other_median`, `median_ratio`, `base_rps`, `other_rps`, `rps_ratio`) |
| `tools` | http: die gefundenen externen Lastgeneratoren, Name => Pfad |
| `calibration` | micro: die Zusammenfassung der Kalibrierungsschleife (`n`, `min`, `mean`, `median`, `p90`, `p95`, `p99`, `max`, `stddev`), `before_median`, `after_median`, `drift_pct` und die `checksum` der Schleife |
| `comparison` | mit `--baseline`: das Ergebnis des Vergleichs (Abschnitt 9), mit `baseline` (der Datei) und `baseline_started` |

### 7.2 Der Umgebungsblock

```json
"environment": {
    "date": "2026-10-06T02:33:47Z",
    "php": "8.5.11",
    "sapi": "cli",
    "engine": "php-cli",
    "opcache": false,
    "jit": false,
    "xdebug": false,
    "os": "Linux 5.14.0-687.5.3.el9_8.x86_64",
    "arch": "x86_64",
    "machine": "web.example.com",
    "cpus": 12,
    "load": 5.55,
    "exponential": "6.0.15stable",
    "git_commit": "ec5699e4d13bb2b249a3fad20a53439f7f7e00bc",
    "ci": ""
}
```

- `date` ist UTC. `engine` ist `php-<sapi>`, mit `(engine archive)`, wenn der Kernel aus `engine.phar` lief.
- `opcache` gibt an, ob der Opcode-Cache **in dem PHP, das den Benchmark ausgeführt hat**, aktiv ist, `jit` den Modus
  `opcache.jit`, wenn der JIT eingeschaltet ist (sonst `false`), `xdebug` den `xdebug.mode`, wenn Xdebug geladen ist
  (sonst `false`). Im HTTP-Modus beschreiben diese Werte den Client; die gemessenen Server werden pro Zeile über ihren
  `Server`-Header benannt (`server`).
- `load` ist der Lastdurchschnitt der letzten Minute beim Start des Laufs.
- `git_commit` wird aus `.git` gelesen, ohne git auszuführen (einschließlich eines verknüpften Worktrees und gepackter
  Refs), oder in einem GitHub-Workflow aus `GITHUB_SHA` übernommen; leer, wenn keins von beiden vorhanden ist. `ci` ist
  `github-actions`, `ci` (bei einer beliebigen Variable `CI`) oder leer.

Die Tabellenausgabe gibt dasselbe als eine Zeile aus: `php-cli, OPcache off, Xdebug off, 12 CPUs, load 9.59, commit
3a5029891e`. Wenn Xdebug geladen ist, steht dort `Xdebug ON (...): times are not representative`.

### 7.3 Die Zeilen

Jede Zeile hat `key` (wonach ein späterer Lauf zugeordnet wird: `<target> <name>`), `kind`, `target`, `name`, `tool`,
`n` (die Stichproben), `min`, `mean`, `median`, `p90`, `p95`, `p99`, `max`, `stddev` (alle in Millisekunden),
`requests` (Stichproben plus Fehlschläge), `errors`, `error_messages` und `rps`. Dann, je nach Art:

| Art | Weitere Felder |
|---|---|
| `http` | `ttfb_median` (Zeit bis zum ersten Byte), `status` (Code => Anzahl, `failed` für keine Antwort), `bytes` (mittlere Größe wie übertragen), `cache` (Antwort von `X-Exp-Cache` => Anzahl), `server` (der `Server`-Header, den die meisten Antworten trugen; leer, wenn der Server keinen sendet, wie es bei Velocity der Fall ist). `rps` sind erfolgreiche Anfragen pro Sekunde der tatsächlich verstrichenen Zeit. |
| `kernel` | `mem_peak` (Bytes), `note`. `rps` ist 1000 / Mittelwert. |
| `micro` | `ops` (Operationen pro Iteration), `normalized` (Median / Kalibrierungsmedian), `note`. `rps` sind Operationen pro Sekunde beim Median. |

Eine Zeile, die gespeichert wurde, bevor es ein Feld gab (eine Baseline aus einer älteren Version hat kein `p90`), wird
trotzdem geladen; der fehlende Wert wird als `-` ausgegeben.

## 8. Die Zahlen lesen

| Zahl | Was sie Ihnen sagt | Achtung |
|---|---|---|
| **median** | die typische Anfrage: Die Hälfte war schneller, die Hälfte langsamer | die Zahl zum Vergleichen; robust gegen einige Ausreißer |
| **p90**, **p99** | wie lange die langsamsten 10 % und 1 % mindestens gewartet haben: Warteschlangen, Garbage Collection, ein abgelaufener Cache, ein Worker, der gestartet wird | bei 100 Läufen ist p99 fast das Maximum; bei 20 *ist* es das Maximum |
| **min** | das Beste, was der Server ohne Hindernisse schafft | optimistisch; gut, um die Untergrenze zu sehen |
| **max** | die schlechteste einzelne Anfrage | ein einzelnes Ereignis; beurteilen Sie nie allein danach |
| **sd** | die Standardabweichung (Stichprobe, n - 1): wie weit die Anfragen streuen | klein im Vergleich zum Median (10 % oder weniger) = stabil; so groß wie der Median = zwei Arten von Antwort vermischt (Cache-Treffer und Rendern) oder gelegentliche Hänger |
| **req/s** | erfolgreiche Anfragen pro Sekunde bei der gewählten Parallelität | durch die Parallelität begrenzt: 4 gleichzeitig zu je 10 ms können etwa 400 nicht überschreiten |
| **err**, **status** | Transportfehler und Antworten ab 400; die Zeilen unter der Tabelle nennen sie | eine abgelehnte Verbindung hat keine Zeit und wird bei den Zeiten nicht berücksichtigt, sodass ein defekter Server nie schnell aussieht |
| **cache** | welcher Cache geantwortet hat | vergleichen Sie nur Zeilen mit derselben Cache-Antwort |
| **x calib** (micro) | der Median relativ zur Kalibrierungsschleife | über Maschinen hinweg vergleichbar; die absoluten Zeiten sind es nicht |

Perzentile werden zwischen den beiden nächstgelegenen Stichproben interpoliert, nach der Methode des Standards von
numpy, R-Typ 7 und `PERCENTILE.INC` von Excel. p95 steht in der JSON- und CSV-Ausgabe und ist das, was die
Regressionsprüfung neben dem Median verwendet.

Drei Muster, die man kennen sollte:

- **Median niedrig, p90 und p99 hoch, sd größer als der Median** (die Velocity-Zeile `/` im kalten Beispiel in 4.4):
  Die meisten Anfragen sind in Ordnung, und einige hängen. Suchen Sie nach etwas, das gelegentlich passiert: ein
  gestarteter Worker, eine Sperre, ein neu aufgebauter Cache, ein anderer Prozess auf der Maschine. Messen Sie erneut,
  bevor Sie dem Code die Schuld geben.
- **min nahe am Median, sd klein**: eine stabile Antwort. Veränderungen von wenigen Prozent zwischen Läufen sind nur
  dann echt, wenn sie sich wiederholen.
- **Eine Zeile mit `HIT` neben einer Zeile mit `-` oder `BYPASS`**: Die beiden messen Verschiedenes (einen Cache und
  einen Rendervorgang). Das Verhältnis zwischen ihnen ist nicht die Geschwindigkeit der Server.

## 9. Läufe vergleichen: Baselines und Regressionen

### 9.1 Speichern, ändern, vergleichen

```bash
mkdir -p var/benchmark
./console exp:benchmark --save=var/benchmark/before.json --allow-root-user
# ... make the change, deploy it ...
./console exp:benchmark --baseline=var/benchmark/before.json --save=var/benchmark/after.json --allow-root-user
echo $?        # 0 no regression, 1 a metric regressed, 2 usage error or refused
```

Zeilen werden über `key` zugeordnet (Server und Pfad; Kernel- und Micro-Zeilen über den Messpunkt). Für http- und
Kernel-Zeilen gilt eine Kennzahl als verschlechtert, wenn:

| Kennzahl | Verschlechtert, wenn |
|---|---|
| median, p95 | sie um mehr als `--threshold` Prozent (Standard 15) **und** um mehr als `--min-delta` Millisekunden (Standard 1) gestiegen ist |
| req/s (http-Zeilen) | sie um mehr als `--threshold` Prozent gefallen ist, während der Median um mehr als `--min-delta` gestiegen ist |
| Fehlerquote | sie um mehr als einen Prozentpunkt gestiegen ist |

Die Rauschgrenze gibt es, weil kleine Zahlen springen: 0,2 ms, die zu 0,3 ms werden, sind 50 % langsamer und bedeuten
nichts. Eine Zeile, die im neuen Lauf fehlt, wird aufgeführt („not measured this time“), aber nicht gezählt.
`--all-checks` zeigt jeden Vergleich, nicht nur die Regressionen. Micro-Läufe werden anders verglichen, nach der
Normalisierung (Abschnitt 13.2), mit einem Standard-Schwellenwert von 40 %.

Zwei gespeicherte Läufe lassen sich vergleichen, ohne etwas zu messen:

```bash
./console exp:benchmark compare var/benchmark/before.json var/benchmark/after.json --all-checks --allow-root-user
./console exp:benchmark compare var/benchmark/before.json var/benchmark/after.json --format=json --allow-root-user
```

`compare --format=json` gibt nur den Vergleich aus: `threshold`, `min_delta`, `regressions`, `checks` (jeweils mit
`key`, `metric`, `baseline`, `current`, `change_pct`, `regressed`), `missing`, `new` und
`environment_differences`; ein Micro-Vergleich ergänzt `method: "normalized"` und `calibration` (`baseline`,
`current`, `speed`).

### 9.2 Ein durchgerechnetes Beispiel: derselbe Code, dreizehn „Regressionen“

Auf dem Demonstrationsserver wurde der A/B-Lauf aus 4.4 gespeichert und neun Minuten später wiederholt, ohne
irgendeine Änderung dazwischen:

```
  against var/benchmark/before.json: 13 regression(s), threshold 15 %, noise floor 1.0 ms
  row                                                   metric  baseline   now   change
  ----------------------------------------------------  ------  --------  ----  -------  ---------
  https://www.example.com /fitness                      median      8.65  12.4   +43.6%  REGRESSED
  https://www.example.com /fitness                      p95         12.5  18.6   +48.8%  REGRESSED
  https://www.example.com /fitness                      rps          415   284   -31.6%  REGRESSED
  https://www.example.com:8080 /fitness                 p95         2.21  4.78  +116.3%  REGRESSED
  https://www.example.com:8080 /workout                 median      1.27  2.77  +117.8%  REGRESSED
  ...
  FAIL: slower than the baseline
```

Nichts ist langsamer geworden; die Maschine war stärker ausgelastet (Last 9 bis 11 auf 12 CPUs, andere Sites darauf).
Cache-Treffer von 1 bis 3 ms verdoppeln sich auf einer geteilten Maschine von einer Minute zur nächsten, und 40
Anfragen pro Seite sind zu wenige, um das auszugleichen. Was Sie stattdessen tun sollten:

- **gerenderte** Seiten vergleichen (`--cold`), deren Zahlen größer sind und sich prozentual weniger bewegen;
- `--min-delta` erhöhen (z. B. `--min-delta=5`), sodass einige Millisekunden bei einem Cache-Treffer nie zählen;
- mehr Läufe verwenden (`--runs=200`) und Vorher und Nachher auf einer ruhigen Maschine kurz nacheinander messen;
- für Codeänderungen den Kernel- oder Micro-Modus verwenden, bei denen kein Netzwerk und kein Antwort-Cache im Weg
  sind.

### 9.3 Zwei Maschinen vergleichen

Eine Baseline von einer anderen Maschine vergleicht die Maschinen. Für den HTTP- und den Kernel-Modus ist das alles,
was Sie bekommen können: Führen Sie beide am selben Tag mit denselben Optionen aus und lesen Sie die Differenz als
„Maschine plus Konfiguration“. Für Code verwenden Sie den Micro-Modus, der die Geschwindigkeit der Maschine
herausrechnet (Abschnitt 13.2), und sehen sich die Zeile `calibration loop: baseline ... ms, now
... ms (this machine x0.84 the speed of the baseline's)` an, um zu erkennen, wie sich die Maschinen unterscheiden.

Wenn die beiden Läufe mit einer anderen PHP-Minor-Version oder einer anderen Einstellung für OPcache, JIT oder Xdebug
gemacht wurden, sagt der Vergleich das, z. B. `note: the baseline was made with another set-up, OPcache: off -> on;
the numbers compare the set-ups too`.

## 10. Rauschen und wie man es verringert

| Quelle | Wirkung | Was zu tun ist |
|---|---|---|
| Andere Arbeit auf der Maschine (die Last in der Kopfzeile) | 10 bis 30 % von einer Sitzung zur nächsten auf einem geteilten Server; vor allem bei p90 und p99; Cache-Treffer können sich verdoppeln | messen, wenn es ruhig ist; nur unmittelbar nacheinander gemachte Läufe vergleichen; `--compare` wechselt die Server innerhalb eines Laufs ab |
| CPU-Frequenzskalierung und Turbo | die ersten Sekunden laufen mit einer anderen Geschwindigkeit als der Rest | Aufwärmläufe; auf einer dedizierten Benchmark-Maschine der Governor `performance` |
| Xdebug geladen | alles um ein Mehrfaches langsamer | nie damit messen; die Kopfzeile meldet `Xdebug ON` |
| OPcache aus (der Standard auf der Kommandozeile) oder ein anderer JIT-Modus | Kernel- und Micro-Zahlen ändern sich um einen Faktor | die Einstellung der Baseline beibehalten; der Vergleich nennt einen Unterschied |
| Antwort-Cache, View-Cache, kompilierte Templates | ein Treffer ist eine oder zwei Größenordnungen schneller als ein Rendervorgang | angeben, was Sie messen: warm, `--cold`, `--cold-clear` |
| Zu wenige Läufe | p99 aus 100 Läufen liegt nahe an einem einzelnen Ausreißer | `--runs=500` oder mehr für Latenz-Ausläufer |
| Kleine Zahlen | 0,2 ms sind 50 % von 0,4 ms | `--min-delta`; Micro-Messpunkte erledigen pro Iteration genug Arbeit, um Millisekunden zu dauern |
| Netzwerk und TLS | Handshakes und ein entfernter Client fügen ihre eigene Zeit hinzu | vom Server selbst aus messen; Keep-Alive an; das Aufwärmen öffnet die Verbindungen |
| Geänderter Inhalt | eine andere Seitenliste, andere Seiten hinter derselben URL | `--urls-file`; Commit und Datum festhalten (der Umgebungsblock tut das) |
| Eine andere Maschine | alles | HTTP und Kernel nur auf der Maschine der Baseline vergleichen; Micro rechnet die Maschine heraus |

### 10.1 Checkliste für einen fairen Vergleich

1. Dieselbe Maschine (oder Micro-Modus), dasselbe PHP, dieselbe Einstellung für OPcache, JIT und Xdebug: Prüfen Sie die
   Umgebungszeile beider Läufe.
2. Derselbe Befehl: dieselben Seiten (`--urls-file`), `--runs`, `--warmup`, `--concurrency`, warm oder kalt.
3. Nichts anderes läuft, was Sie anhalten können; notieren Sie die Last in der Kopfzeile beider Läufe.
4. Vorher und Nachher kurz nacheinander; für zwei Server ein einziger `--compare`-Lauf, nie zwei getrennte.
5. Die Caches im selben Zustand: dieselbe Spalte `cache` in beiden Läufen.
6. Führen Sie es zweimal aus. Ein Unterschied, der sich nicht wiederholt, ist Rauschen.
7. Beurteilen Sie zuerst den Median, dann p90 und p99, max nie allein.

## 11. Arbeitsabläufe Schritt für Schritt

### 11.1 Vor und nach einer Codeänderung

1. Auf der Maschine, auf der Sie arbeiten, mit dem alten Code:
   `./console exp:benchmark micro --save=var/benchmark/micro-before.json --allow-root-user` und
   `./console exp:benchmark kernel --repeat=30 --save=var/benchmark/kernel-before.json --allow-root-user`.
2. Nehmen Sie die Änderung vor (und stellen Sie sie bereit, siehe 11.5).
3. `./console exp:benchmark micro --baseline=var/benchmark/micro-before.json --allow-root-user` und
   `./console exp:benchmark kernel --repeat=30 --baseline=var/benchmark/kernel-before.json --allow-root-user`.
4. Eine Regression, die sich in einem zweiten Lauf wiederholt, ist echt. Grenzen Sie sie mit `--probe=<name>` ein.
5. Dann die Sicht des Besuchers: ein kalter HTTP-Lauf der Seiten, die die Änderung berührt,
   `./console exp:benchmark --url=/the/page --cold --runs=50 --allow-root-user`, vorher und nachher.

### 11.2 Apache gegen Velocity

1. Beide Server müssen dieselbe Installation ausliefern (Velocity auf einem eigenen Port, z. B. 8080; siehe
   [Velocity-Engines](../../eng-us/bc/6.0/velocity-engines.md#running-them-side-by-side)).
2. Seiten aus dem Cache: `./console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 --allow-root-user`.
3. Gerenderte Seiten: dasselbe mit `--cold --runs=30`.
4. Lesen Sie die A/B-Tabelle pro Seite, und vergleichen Sie nur Zeilen mit derselben `cache`-Antwort (Abschnitt 8).
5. Wiederholen Sie die Messung zu einer anderen Tageszeit. Berichten Sie sowohl das Ergebnis aus dem Cache als auch
   das gerenderte; eine einzige Zahl für „den schnelleren Server“ führt in die Irre (Abschnitt 4.4).

### 11.3 OPcache und Velocity optimieren

Für PHP-FPM wird OPcache in den PHP-Einstellungen des Pools gesetzt; für Velocity in `settings/velocity.ini [PHPSettings]
IniOptions[]` (ausgeliefert mit `opcache.enable_cli=1`, `revalidate_freq=0` und `file_update_protection=0`, samt
Begründung in den Kommentaren der Datei), und das Worker-Modell in `[ServerSettings]` (`Workers`, `SpareWorkers`,
`ForkPerRequest`, `StatTtl`). So messen Sie eine Einstellung:

1. Speichern Sie einen kalten Lauf der wichtigen Seiten gegen den Server, den Sie optimieren:
   `./console exp:benchmark --base=https://www.example.com:8080 --cold --runs=50 --save=var/benchmark/tune-before.json --allow-root-user`.
2. Ändern Sie eine Einstellung und starten Sie dann diesen Server neu, damit seine Worker mit ihr starten (`./console exp:velocity restart
   --allow-root-user` für Velocity, `systemctl reload <php-fpm unit>` für PHP-FPM; siehe
   [Bereitstellung](bereitstellung.md)).
3. `./console exp:benchmark --base=https://www.example.com:8080 --cold --runs=50 --baseline=var/benchmark/tune-before.json --all-checks --allow-root-user`.
4. Behalten Sie die Einstellung nur, wenn sich der Gewinn in einem zweiten Paar von Läufen wiederholt. Eine Einstellung
   pro Schritt.

Die Kommentare in `settings/velocity.ini` halten Messungen fest, die genau so gemacht wurden: Der Opcode-Cache allein
brachte eine gerenderte Inhaltsseite von 527 ms auf 391 ms (+26 % req/s), der Tracing-JIT machte sie wieder langsamer
(417 ms), und persistente Worker (`ForkPerRequest=disabled`) brachten Seiten der Administration für angemeldete
Benutzer von 256–284 ms auf 73–80 ms.

### 11.4 Eine Engine wählen

Velocity ist die empfohlene Engine für jede Stufe; FrankenPHP und Apache mit PHP-FPM sind die Alternativen
([Bereitstellung](bereitstellung.md#1-wählen-sie-wie-die-site-ausgeliefert-wird)). Die drei können nebeneinander auf verschiedenen
Ports laufen, also messen Sie sie auf Ihrer eigenen Site, bevor Sie sich entscheiden:

1. Starten Sie die Kandidaten auf eigenen Ports ([Engines im Vergleich](../../eng-us/bc/6.0/velocity-engines.md)).
2. `./console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080,https://www.example.com:8443 --allow-root-user`
   (drei oder mehr Server: Jeder wird mit dem ersten verglichen).
3. Dasselbe mit `--cold` und mit einer Seite für angemeldete Benutzer (`--cookie=...`), wenn Redakteure wichtig sind.
4. Wägen Sie die Zahlen aus dem Cache (was die meisten Besucher erhalten), die gerenderten Zahlen (Cache-Fehlschläge,
   Redakteure) und p99 (das schlechteste Erlebnis) ab, nicht eine einzelne Zahl.

### 11.5 Prüfen, dass eine Bereitstellung keine Regression gebracht hat

1. Vor der Bereitstellung: `./console exp:benchmark --cold --runs=50 --save=var/benchmark/pre-deploy.json --allow-root-user`.
2. Bereitstellen, z. B. `./console exp:velocity deploy --allow-root-user` ([Velocity-Engines](../../eng-us/bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy)).
3. Wärmen Sie die Caches vor, die die Bereitstellung geleert hat (`php bin/php/warm.php --allow-root-user`), damit Sie
   Gleiches mit Gleichem vergleichen.
4. `./console exp:benchmark --cold --runs=50 --baseline=var/benchmark/pre-deploy.json --min-delta=5 --allow-root-user;
   echo $?`. Exit-Code 1 listet die verschlechterten Zeilen auf.
5. Ein Bereitstellungsskript kann den Exit-Code verwenden; auf einer geteilten Maschine geben Sie ihm
   `--threshold=25` oder mehr und ein `--min-delta`, sonst schlägt es ständig falschen Alarm (Abschnitt 9.2).

## 12. Die Geschwindigkeitskarte der Tour und „Erneut messen“

Die geführte Tour des Demonstrationsservers zeigt nach der Velocity-Station eine Geschwindigkeitskarte: „Dieselben
Seiten, Apache und Velocity“. Das Dashboard der Administration desselben Servers zeigt dieselben Zahlen mit einer
Schaltfläche **Erneut messen**. Beide gehören zur eigenen Einstellungs-Extension der Demonstrations-Site, die nicht mit
Exponential ausgeliefert wird; sie werden hier beschrieben, weil sie die sichtbarste Verwendung von `exp:benchmark`
sind.

Was sie zeigen: für jede Seite aus `showcase.ini [SpeedSettings] Pages[]` (auf dem Demonstrationsserver: die
Startseite, eine aus Layouts aufgebaute Landingpage, die Rezeptliste und die Tour selbst) die mittlere Zeit (Median)
über Apache (`ApacheBase`) und über Velocity (`VelocityBase`), einmal **aus dem Cache** (ein warmer Lauf,
`BenchmarkRequests=20` Anfragen pro Seite und Server) und einmal **gerendert** (ein `--cold`-Lauf,
`BenchmarkColdRequests=8`), sowie die Zeit von Apache geteilt durch die von Velocity. Unter der Tabelle: wann gemessen
wurde, wie lange es gedauert hat und aus wie vielen Anfragen jeder Median besteht.

Wie gemessen wird: **Erneut messen** sendet an `showcase/measure` (das die Richtlinie `showcase/measure` erfordert),
das `./console exp:benchmark --url=<each page> --compare=<ApacheBase>,<VelocityBase> --requests=20 --concurrency=2
--format=json` ausführt (und dasselbe mit `--cold`), die Mediane, p95, req/s, Fehler und Cache-Antworten in
`var/<site>/showcase/speed.json` ablegt und zum Dashboard zurückkehrt. `showcase/speed` liefert diese Datei als JSON an
die Tour und das Dashboard aus; es liest die Datei und misst nie, sodass Besucher nie Anfragen auslösen. Eine Messung
läuft höchstens einmal pro `MinInterval` Sekunden (60) und immer nur eine gleichzeitig; ein Lauf, der länger als zwei
Minuten dauert, wird abgebrochen. Wenn `exp:benchmark` fehlschlägt, greift die Karte auf aufeinanderfolgende
curl-Anfragen zurück (`Rounds=5` pro Seite und Server, Serverzeit = Zeit bis zum ersten Byte minus TLS-Handshake).

Ihre Grenzen, die die Grenzen jedes kleinen Benchmarks sind:

- **Wenige Anfragen.** 20 Anfragen aus dem Cache und 8 gerenderte pro Seite: Der Median ist fair, p95 liegt nahe am
  Maximum.
- **Ein Moment.** Eine Messung auf einer geteilten Maschine; die nächste kann um 30 % abweichen (Abschnitt 9.2).
- **Vom Server selbst aus.** Kein Netzwerk dazwischen, die Zeiten sind also die der Server, nicht die eines Besuchers.
- **Nur aus dem Cache und gerendert.** Keine Seiten für angemeldete Benutzer, keine Last.
- **Eine Momentaufnahme.** Die Karte zeigt die letzte Messung, bis ein Administrator erneut misst.

Für alles, worüber Sie eine Entscheidung treffen wollen, führen Sie `exp:benchmark` selbst mit mehr Läufen aus
(Abschnitt 11.2).

## 13. Die Performance-Prüfung der CI

### 13.1 Was läuft

Der Workflow `.github/workflows/performance.yml` (Name „Performance“, Job „Micro benchmark (PHP 8.5)“) läuft bei jedem
Push und Pull Request auf `main`, der PHP-Dateien, Templates, `settings/`, die deutsche Übersetzung,
`tests/benchmark/` oder den Workflow selbst berührt, sowie von Hand (Workflow Dispatch):

```bash
composer install --no-dev --prefer-dist --no-progress --no-interaction --ignore-platform-req=php --ignore-platform-req=ext-mongodb
php bin/php/benchmark.php micro --repeat=30 --warmup=3 \
    --baseline=tests/benchmark/baseline.json --all-checks \
    --save="$RUNNER_TEMP/benchmark-micro.json"
```

unter PHP 8.5 mit `opcache.enable_cli=0` und ohne Xdebug, so wie die Baseline gemessen wurde. Er schreibt den
Vergleich und den Lauf in die Job-Zusammenfassung, gibt für jeden verschlechterten Messpunkt eine
`::warning`-Annotation aus und bewahrt den Lauf 30 Tage lang als Artefakt `benchmark-micro` auf.

### 13.2 Kalibrierung und Normalisierung

Ein CI-Runner ist eine geteilte virtuelle Maschine, deren Geschwindigkeit sich von einem Lauf zum nächsten stärker
ändert als jede Regression, die es zu erkennen lohnt. Deshalb vergleicht die Prüfung keine Millisekunden. In jedem
Lauf:

1. wird die Kalibrierungsschleife (Abschnitt 6) `--warmup`-mal ungemessen und `--repeat`-mal gemessen ausgeführt,
   dann laufen die Messpunkte, dann wird die Schleife noch einmal `--repeat`-mal gemessen; der Kalibrierungsmedian `C`
   ist der Median aller gemessenen Schleifendurchläufe;
2. wird der Median `M` jedes Messpunkts zu `N = M / C`, der Zeit des Messpunkts in „Kalibrierungsschleifen“;
3. ist für jeden Messpunkt in beiden Läufen die Veränderung `(N_now / N_baseline - 1) × 100 %`.

Bei einem Runner, der bei allem doppelt so langsam ist, verdoppeln sich `M` und `C` beide, sodass `N` und die
Veränderung gleich bleiben. Eine Änderung, die einen Messpunkt langsamer macht, während die Schleife gleich bleibt,
erhöht `N`. Der Vergleich gibt die Maschinengeschwindigkeit aus, die er herausgerechnet hat:

```
  against tests/benchmark/baseline.json: 0 regression(s), threshold 40 % of the median relative to the calibration loop
  calibration loop: baseline 9.359 ms, now 11.121 ms (this machine x0.84 the speed of the baseline's)
  row                      metric        baseline     now  change
  -----------------------  ------------  --------  ------  ------  --
  micro template_compile   median/calib     6.978   5.641  -19.2%  ok
  micro template_render    median/calib     0.667   0.525  -21.3%  ok
  micro ini_parse          median/calib     0.559   0.620  +10.8%  ok
  micro autoload_map       median/calib     0.088   0.093   +5.4%  ok
  micro autoload_miss      median/calib     0.102   0.108   +6.2%  ok
  micro uri_parse          median/calib     0.291   0.251  -13.9%  ok
  micro i18n_load          median/calib    21.046  18.837  -10.5%  ok
  micro i18n_lookup        median/calib     0.362   0.304  -16.0%  ok
  micro datatype_validate  median/calib     0.424   0.282  -33.5%  ok
  PASS: no regression
```

Die Normalisierung ist nicht perfekt: Die Schleife ist reine CPU-Arbeit, während `ini_parse`, `i18n_load` und
`template_compile` auch Dateien lesen, und Maschinen unterscheiden sich in ihren Dateisystemen und Caches ebenso sehr
wie in ihren CPUs. Das ist der Grund für den großzügigen Schwellenwert. Beachten Sie die Streuung oben, von -33 % bis
+11 %, zwischen zwei Läufen desselben Codes auf derselben Maschine.

### 13.3 Der Schwellenwert

Ein Messpunkt verschlechtert sich, wenn sein normalisierter Median um mehr als 40 % gestiegen ist
(`expBenchmark::DEFAULT_MICRO_THRESHOLD`; übergeben Sie `--threshold=<percent>` für einen anderen Wert), oder wenn er
häufiger fehlgeschlagen ist als in der Baseline. Ein Messpunkt, der in der Baseline, aber nicht im Lauf vorkommt, wird
aufgeführt, aber nicht gezählt; ein neuer Messpunkt wird aufgeführt, bis die Baseline ihn enthält.

### 13.4 Warum sie nur warnt

Eine rote Prüfung, die sich als Rauschen herausstellt, bringt allen bei, rote Prüfungen zu ignorieren. Deshalb kann die
Prüfung noch nichts blockieren: Der Workflow ist von PHPUnit und Quality getrennt, sein Job und sein Benchmark-Schritt
haben `continue-on-error: true`, eine Regression erscheint als gelbe Warnungs-Annotation und als Warnung in der
Job-Zusammenfassung, und der Workflow-Lauf bleibt grün. Er hat eine eigene Concurrency-Gruppe, sodass ein abgebrochener oder langsamer Lauf nie die
anderen aufhält.

### 13.5 Sie verpflichtend machen

Tun Sie das, sobald mehrere Läufe auf `main` deutlich innerhalb des Schwellenwerts geblieben sind (sehen Sie sich die
Spalte `change` der Job-Zusammenfassungen an):

1. Entfernen Sie in `.github/workflows/performance.yml` `continue-on-error: true` aus dem Job `micro` und aus dem
   Schritt „Micro benchmark against the baseline (warning only)“, und benennen Sie den Schritt um.
2. Wenn die Läufe zeigen, dass es nötig ist, setzen Sie `--threshold=` im selben Schritt.
3. Fügen Sie in den Einstellungen des Repositorys, im Branch-Schutz von `main`, „Micro benchmark (PHP 8.5)“ zu den
   erforderlichen Statusprüfungen hinzu.
4. Vermerken Sie im Kopfkommentar des Workflows, dass er jetzt blockiert.

### 13.6 Die Baseline erneuern

Wenn eine Änderung die Geschwindigkeit absichtlich verändert, wenn ein Messpunkt hinzugefügt oder geändert wurde oder
wenn PHP auf den Runnern auf eine andere Minor-Version wechselt, erneuern Sie `tests/benchmark/baseline.json`. Am
besten aus einem CI-Lauf von `main` nach der Änderung, weil dieser dort misst, wo die Prüfung läuft:

```bash
gh run list --workflow=performance.yml --branch=main --limit=5
gh run download <run id> -n benchmark-micro -D var/tmp/benchmark-micro
cp var/tmp/benchmark-micro/benchmark-micro.json tests/benchmark/baseline.json
./console exp:benchmark show tests/benchmark/baseline.json --allow-root-user     # check it: 0 errors, every probe there
git add tests/benchmark/baseline.json
git commit -m "Updated: The micro benchmark baseline follows <the change that changed the speed>"
```

Oder lokal, auf einer ruhigen Maschine mit PHP 8.5, OPcache für die Kommandozeile aus (der Standard) und ohne Xdebug:

```bash
php bin/php/benchmark.php micro --save=tests/benchmark/baseline.json
```

Committen Sie die Baseline für sich allein, mit einer Nachricht, die sagt, warum sich die Geschwindigkeit geändert hat.

### 13.7 Wenn sie warnt

1. Öffnen Sie die Job-Zusammenfassung: welche Messpunkte, um wie viel, und die Zeile mit der
   Kalibrierungsgeschwindigkeit.
2. Ist es ein einzelner Lauf? Führen Sie den Job erneut aus (Re-run jobs im Tab „Actions“). Eine Warnung, die sich
   nicht wiederholt, ist Rauschen.
3. Wiederholt sie sich? Reproduzieren Sie sie lokal, vor und nach der Änderung, mit dem Messpunkt allein:
   `./console exp:benchmark micro --probe=<probe> --repeat=50 --allow-root-user`, einmal auf dem Commit davor und einmal
   auf Ihrem, und vergleichen Sie die Werte `x calib`.
4. Eine echte Regression: Beheben Sie sie, oder, wenn die Kosten beabsichtigt sind (ein Messpunkt erledigt jetzt
   absichtlich mehr Arbeit), erneuern Sie die Baseline (13.6) und sagen Sie das in der Commit-Nachricht.
5. Viele Messpunkte auf einmal langsamer bei einem großen Unterschied in der Kalibrierungsgeschwindigkeit: Der
   Runner-Typ hat sich geändert (eine andere CPU-Familie). Erneuern Sie die Baseline aus einem CI-Lauf.

## 14. Fehlerbehebung

| Symptom | Ursache | Lösung |
|---|---|---|
| `REFUSED: <host> is not served by this installation` | die Standard-Seitenliste geht nur an die eigenen Hosts dieser Installation | benennen Sie die URLs mit `--url` oder `--urls-file`, oder verwenden Sie `--base` mit einem Host eines Siteaccess |
| `REFUSED: --requests above 1000 against a host that is not loopback needs --force` (oder Parallelität, Aufwärmen, Gesamtzahl) | die Sicherheitsgrenzen aus Abschnitt 2 | senken Sie die Anzahlen, messen Sie über `127.0.0.1` mit `--resolve`, oder fügen Sie `--force` hinzu, wenn es Ihr Server ist und Sie es so meinen |
| eine Zeile mit `n` 0, `err` gleich der Anzahl der Anfragen und `status` `failed:<count>` | der Server ist nicht erreichbar: falscher Port, Firewall, läuft nicht (eine abgelehnte Verbindung hat keine Zeit und ist daher keine Stichprobe) | `curl -I <url>`; die Zeilen unter der Tabelle nennen den curl-Fehler |
| `err`-Zähler mit `status` `401` | die Site liegt hinter HTTP-Basic-Authentifizierung | `--auth=user:password` oder `EXP_BENCHMARK_AUTH` |
| `SSL certificate problem` in den Fehlerzeilen | ein selbstsigniertes oder abgelaufenes Zertifikat | beheben Sie das Zertifikat, oder `--insecure` für einen Testserver |
| die Suchseite fehlt in der Standardliste | anonyme Besucher dürfen keine Inhalte lesen, oder `--no-search` | prüfen Sie die Rolle für anonyme Benutzer; oder benennen Sie die Seite mit `--url` |
| die Anmeldeseite der Administration fehlt | Siteaccesses werden über den Host erkannt, nicht über die URI | `--url=https://admin.example.com/user/login` |
| Cache-Treffer weichen zwischen zwei Läufen um 50 bis 100 % ab | Last auf der Maschine; kleine Zahlen | Abschnitt 9.2: `--cold`, `--min-delta`, mehr Läufe, unmittelbar nacheinander |
| eine Zeile mit sd größer als ihr Median und einem hohen p90 | gelegentliche Hänger zwischen schnellen Antworten | erneut messen; im Log des Servers nach den langsamen Anfragen suchen (Abschnitt 8) |
| `cache` zeigt `HIT` mit `--cold` | ein vorgeschalteter Cache, der seinen Schlüssel aus etwas anderem bildet (ein CDN, ein Proxy, der Query-Strings ignoriert) | messen Sie den Ursprungsserver direkt (`--resolve`), oder umgehen Sie den Proxy |
| `ERROR: Baseline not found or not readable` | falscher Pfad, oder die Datei liegt in einem Verzeichnis, das der Benutzer nicht lesen kann | prüfen Sie den Pfad; `--save` legt das Verzeichnis an, `--baseline` nicht |
| `ERROR: Saved with another format version than 1` | die Baseline stammt aus einer inkompatiblen Version des Befehls | erstellen Sie eine neue Baseline |
| `ERROR: A micro run compares only with a micro run` | `--baseline` nennt einen http- oder Kernel-Lauf für einen Micro-Lauf | verwenden Sie die Micro-Baseline |
| `ERROR: Both runs need a calibration loop ...` | eine von Hand erstellte oder abgeschnittene Micro-Datei | erstellen Sie die Baseline mit `micro --save` |
| `note: the baseline was made with another set-up, OPcache: off -> on` | die beiden Läufe unterscheiden sich in PHP, OPcache, JIT oder Xdebug | machen Sie beide Läufe auf dieselbe Weise, oder erstellen Sie eine neue Baseline |
| die Kopfzeile meldet `Xdebug ON ... times are not representative` | Xdebug ist in dem PHP geladen, das den Befehl ausführt | führen Sie ihn mit einem PHP ohne Xdebug aus, oder mit `php -n` und den nötigen Extensions |
| ein Micro-Messpunkt zeigt `failed: ...` in seiner Notiz und `err` = Läufe | der Messpunkt konnte nicht laufen (eine Datei fehlt, ein Datentyp ist nicht registriert) | die Notiz nennt den Fehler; führen Sie den Befehl aus dem Wurzelverzeichnis der Installation mit den mitgelieferten `settings/` und `share/translations/` aus |
| die Notiz von `template_render` meldet `interpreted` | die Template-Kompilierung ist in diesem Siteaccess ausgeschaltet | vergleichen Sie nur mit Läufen, die auf dieselbe Weise gemacht wurden; im Produktivbetrieb ist sie eingeschaltet |
| Kernel-`boot` schlägt fehl: `failed: could not start ...` | die PHP-Binärdatei des Kindprozesses kann nicht gestartet werden | führen Sie den Befehl mit der PHP-Kommandozeilen-Binärdatei der Installation aus |
| die Kernel-Zeile `image_alias` ist leer: `no published image attribute found` | die Site hat kein veröffentlichtes Bild mit einem Original | nichts zu beheben; die anderen Messpunkte laufen trotzdem |
| der Befehl wartet zehn Sekunden, bevor er startet | er läuft als root | führen Sie ihn als Benutzer der Site aus, oder nehmen Sie die Pause hin |
| der CI-Job zeigt eine Warnung, aber die Prüfung ist grün | sie warnt absichtlich nur | Abschnitt 13.7 |

## 15. Alle Optionen

Standardwerte in eckigen Klammern. „http“-Optionen gelten für den HTTP-Modus, „kernel“ und „micro“ für diese Modi; der
Rest für alle.

| Option | Modus | Bedeutung |
|---|---|---|
| `--base=<url>` | http | der Server [`https://` + `SiteURL` des Siteaccess] |
| `--compare=<a>,<b>[,...]` | http | zwei oder mehr Server, unmittelbar nacheinander gemessen; jeder spätere wird mit dem ersten verglichen |
| `--url=<url or path>` | http | eine zu messende Seite; für mehrere wiederholen. Eine absolute URL benennt ihren Server. |
| `--urls-file=<file>` | http | eine URL oder ein Pfad pro Zeile, `#` leitet einen Kommentar ein |
| `--pages=<n>` | http | Menüseiten in der Standardliste [3] |
| `--no-search`, `--search=<words>` | http | die Suchseite weglassen; die Wörter, nach denen sie sucht [erstes Wort der ersten Menüseite] |
| `--no-admin` | http | die Anmeldeseite der Administration weglassen |
| `--requests=<n>`, `--runs=<n>` | http | gemessene Anfragen pro Seite und Server [100] |
| `--warmup=<n>` | http, micro | ungemessene Anfragen pro Seite und Server, mindestens eine pro Verbindung [5]; micro: ungemessene Iterationen pro Messpunkt [3] |
| `--concurrency=<n>` | http | gleichzeitige Anfragen [4] |
| `--rounds=<n>` | http | Runden, auf die die Anfragen aufgeteilt werden, mit abwechselnder Server-Reihenfolge [2] |
| `--cold` | http | ein eindeutiger Query-String bei jeder Anfrage |
| `--cold-clear` | http | den Inhalts-View-Cache dieser Installation einmal vor dem Lauf leeren, kein Aufwärmen |
| `--insecure`, `--auth`, `--header`, `--cookie`, `--resolve`, `--http`, `--encoding`, `--timeout`, `--no-keepalive` | http | Abschnitt 4.5 [prüfen, keine, keiner, keines, keine, ausgehandelt, gzip, 30, Keep-Alive] |
| `--tool=curl\|ab\|wrk\|oha` | http | der Client [curl, eingebaut] |
| `--duration=<s>` | http | Sekunden pro Seite für wrk [10] |
| `--repeat=<n>` | kernel, micro | Durchläufe pro Messpunkt [kernel 20, micro 30] |
| `--boot-repeat=<n>` | kernel | Kindprozesse für boot, process und render_cold [5] |
| `--node=<id>` | kernel | der zu rendernde und abzurufende Knoten [Startseite oder die größte Menüseite] |
| `--probe=<name>` | kernel, micro | nur diese Messpunkte; für mehrere wiederholen [alle] |
| `--micro` | | dasselbe wie der Modus `micro` |
| `--format=table\|json\|csv` | | die Ausgabe [table]; `--json` und `--csv` sind dasselbe |
| `--save=<file>` | | den Lauf als JSON schreiben |
| `--baseline=<file>` | | mit einem gespeicherten Lauf vergleichen; Exit-Code 1 bei einer Regression |
| `--threshold=<percent>` | | wie viel schlechter eine Kennzahl werden darf [15; micro 40, nach der Normalisierung] |
| `--min-delta=<ms>` | | um wie viele Millisekunden eine Latenz zusätzlich wachsen muss [1; von micro nicht verwendet] |
| `--all-checks` | | jeden Vergleich zeigen, nicht nur die Regressionen |
| `--force` | http | mehr als die höflichen Grenzen gegen einen Host erlauben, der nicht Loopback ist |
| `-s <siteaccess>`, `-q`, `--allow-root-user`, `-d` | | die allgemeinen Optionen jedes Skripts: Siteaccess, still (Fortschritt aus, Ergebnis bleibt), als root ausführen, Debug-Ausgabe |

## Quellen

- [Benchmark: exp:benchmark](../../eng-us/features/6.0/benchmark.md): die Funktionsreferenz, mit der Dateiliste.
- [10.12 Performance-Tuning](../installation/10-nach-der-installation.md#1012-performance-tuning): die Stellschrauben, geordnet
  nach Wirkung.
- [Bereitstellung](bereitstellung.md): Apache mit PHP-FPM, Velocity, FrankenPHP; eine Änderung ausliefern.
- [Velocity-Engines](../../eng-us/bc/6.0/velocity-engines.md): die Server, die Sie vergleichen wollen, eine PHP-Änderung
  bereitstellen, OPcache unter Velocity.
- [FrankenPHP](../../eng-us/bc/6.0/frankenphp.md): die dritte Engine.
- [HTTP-Caching](../../eng-us/bc/6.0/http-caching.md) und [Caches vorwärmen](../../eng-us/bc/6.0/http2-and-cache-warming.md): was die
  warmen Zahlen messen.
- [Qualitätsprüfungen](../../eng-us/features/6.0/quality-checks.md): die anderen Prüfungen, die die CI des Repositorys ausführt.
- [Konsole](../../eng-us/bc/6.0/console.md): jeder Befehl.
- Code: `kernel/private/classes/commands/benchmark.php` (Optionen, Sicherheitsregeln, Ausgabe),
  `kernel/classes/expbenchmark.php` (Statistik, Formate, Vergleiche, Umgebung),
  `kernel/classes/expbenchmarkhttp.php` (der HTTP-Client), `kernel/classes/expbenchmarkkernel.php` (Kernel-Messpunkte),
  `kernel/classes/expbenchmarkmicro.php` (Micro-Messpunkte, Kalibrierung), `tests/benchmark/baseline.json`,
  `.github/workflows/performance.yml`, Tests in `tests/tests/kernel/classes/benchmark/`.
- Perzentile: Hyndman und Fan, „Sample quantiles in statistical packages“, *The American Statistician* 50 (1996),
  Methode 7.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
