# Benchmark: `exp:benchmark`

Diese Seite richtet sich an Websitebetreiber und Entwickler, die wissen möchten, wie schnell ihre Installation ist,
welcher Server sie schneller ausliefert und ob eine Änderung sie langsamer gemacht hat. `exp:benchmark` wird mit
Exponential 6.0.15 ausgeliefert. Es braucht nichts außer PHP mit der curl-Erweiterung: kein `ab`, kein `wrk`, keinen
externen Dienst.

Es kann vier Dinge:

- **http** (Standard): ruft Seiten ab und misst ihre Zeit, gegen einen Server oder mehrere unmittelbar nacheinander
  (A/B).
- **kernel**: misst die Teile einer Seite innerhalb von PHP, ohne HTTP: Bootvorgang, Template-Rendering kalt und
  warm, Laden der INI-Dateien, Abruf von Inhalten, Knotenliste, Datenbank-Roundtrip, Schreiben und Lesen im Cache,
  Nachschlagen eines Bild-Alias.
- **micro**: misst reine PHP-Hotpaths des Kernels ohne Webserver und ohne Datenbank (Kompilieren und Rendern von
  Templates, Parsen von INI-Dateien, Autoload, `eZURI`, Übersetzungen, Validierung von Datentypen), normalisiert gegen
  eine Kalibrierungsschleife, sodass sich Läufe auf verschiedenen Rechnern vergleichen lassen. Die CI des Repositorys
  führt ihn bei jedem Push aus.
- **baseline**: speichert einen Lauf als JSON, vergleicht einen späteren Lauf damit und endet mit Status 1, wenn sich
  eine Kennzahl um mehr als einen Schwellenwert verschlechtert hat. Genau das prüft ein CI-Job oder ein
  Deployment-Skript.

Schnellstart, im Wurzelverzeichnis von Exponential:

```bash
./console exp:benchmark --allow-root-user                 # the front page, three menu pages, the admin login
./console exp:benchmark kernel --allow-root-user          # the parts of a page, in-process
./console exp:benchmark micro --allow-root-user           # hot paths, no web server, no database
./console exp:bench --help                                 # exp:bench is the short name
```

`php bin/php/benchmark.php ...` ist derselbe Befehl ohne die Konsole.

Diese Seite ist die Referenz. Der Leitfaden [Benchmarking von Exponential](../../anleitungen/benchmarking.md) erklärt
jede Verwendung Schritt für Schritt mit echten Beispielausgaben: die Zahlen lesen, Läufe fair vergleichen, Abläufe für
Codeänderungen, Engines und Deployments, die Tempokarte der Tour, die CI-Prüfung und die Fehlersuche.

## Standardmäßig sicher

Ein Benchmark ist Last. Diese Regeln sorgen dafür, dass ein erster Versuch einer Website nicht schadet:

| Regel | Warum |
|---|---|
| 100 Anfragen pro URL, 4 gleichzeitig, 5 Aufwärmanfragen | Rücksichtsvoll genug für einen geteilten Server, ausreichend für einen Median. |
| Nur lesend: nur `GET`, keine Cookies, sofern Sie nicht `--cookie` angeben, nichts wird gesendet | Messen ändert niemals Inhalte. |
| Der Kernel-Modus ruft nur vorhandene Knoten und Objekte ab | Geschrieben werden nur Cache-Einträge: seine eigene Testdatei (die wieder entfernt wird) und alle Template-Cache-Blöcke, die eine vollständige Ansicht füllt, so wie es die Anfrage eines Besuchers täte. |
| Gegen einen anderen Host als das Loopback dieses Rechners, mehr als 1000 Anfragen pro URL, eine Parallelität über 16, ein Aufwärmen über 100 oder insgesamt mehr als 20 000 Anfragen ist `--force` nötig | Ein Tippfehler soll nicht zum Lasttest der Produktion werden. |
| Ein Host, den die Website nicht bedient (keine `SiteURL` und keine Host-Zuordnungseinstellung ihrer Siteaccesses nennt ihn), wird abgelehnt, sofern seine URLs nicht mit `--url` oder `--urls-file` angegeben werden | Sie können die Standardseitenliste nicht versehentlich auf den Server eines anderen richten. |
| TLS-Zertifikate werden geprüft; `--insecure` schaltet das für einen Server mit selbstsigniertem Zertifikat ab | |

`--cold-clear` ist die einzige Option, die schreibt: Sie leert vor dem Lauf den Inhaltsansichts-Cache dieser
Installation. Für einen Server, den die Installation nicht bedient, wird sie abgelehnt. Verwenden Sie sie nicht auf
einem Server, an dem andere arbeiten; `--cold` misst an den Caches vorbei, ohne etwas zu leeren.

## HTTP-Modus

### Was abgerufen wird

Ohne `--url` wird die Liste aus der Website selbst gebildet:

1. die Startseite `/`;
2. die ersten Seiten des Menüs, also Inhaltsseiten: die Kinder des Startseitenknotens
   (`[SiteSettings] IndexPage`), die einen URL-Alias haben, nach Priorität und dann nach Knoten-ID (`--pages=3`,
   `--pages=0` für keine);
3. die Suchseite `/content/search?SearchText=<word>`, wenn anonyme Besucher suchen dürfen (die Ansicht benötigt
   `content/read`, oder `content/search` steht in `[RoleSettings] PolicyOmitList`). Das Wort ist das erste Wort im
   Namen der ersten Menüseite oder `--search=<words>`; `--no-search` lässt die Seite weg;
4. die Anmeldeseite des Admins `/<admin siteaccess>/user/login`, wenn Siteaccesses über die URI zugeordnet werden
   (`--no-admin`, um sie wegzulassen).

Liste und Reihenfolge sind in jedem Lauf gleich, solange der Inhalt gleich ist, sodass zwei Läufe dieselben Seiten in
derselben Reihenfolge messen. Für eine Liste, die sich nie ändert, nennen Sie die Seiten in einer Datei
(`--urls-file`) und bewahren diese Datei zusammen mit der Baseline auf.

Der Server ist `https://` plus die `SiteURL` des Siteaccess (`-s`/`--siteaccess` wählt einen anderen) oder
`--base=<url>`.

Nennen Sie stattdessen Ihre eigenen Seiten:

```bash
./console exp:benchmark --url=/ --url=/news --url=/news/some-article --allow-root-user
./console exp:benchmark --urls-file=benchmark-urls.txt --allow-root-user   # one URL or path per line, # comments
./console exp:benchmark --url=https://staging.example.com/ --allow-root-user  # an absolute URL names its server
```

### Warm und kalt

| | Was antwortet | Option |
|---|---|---|
| **warm** (Standard) | was einem wiederkehrenden Besucher antwortet: der Antwort-Cache (Velocity, der HTTP-Cache), sonst der Ansichts-Cache | |
| **kalt** | die Anwendung: Jede Anfrage trägt einen Query-String, den noch kein Cache gesehen hat (`?_bench=<unique>`), und an dem jeder Antwort-Cache von Exponential vorbeigeht (`X-Exp-Cache: BYPASS (query string)`) | `--cold` |

`--cold` nutzt weiterhin den Ansichts-Cache und die kompilierten Templates, wie es die meisten echten Cache-Fehlschläge
tun. Um ein Rendering mit leerem Ansichts-Cache zu messen, leert `--cold-clear` ihn einmal vor dem Lauf und lässt das
Aufwärmen aus; dann ist die erste Anfrage jeder Seite die teure, und das zeigt sich in `max` und `p99`.

Die Spalte `cache` zählt den Header `X-Exp-Cache` (oder `X-Cache`) der Antworten: `HIT:100` bedeutet, dass jede
Anfrage aus dem Antwort-Cache beantwortet wurde, `BYPASS:50`, dass am Cache vorbeigegangen wurde, `-`, dass der Server
keinen solchen Header gesendet hat.

### Server vergleichen (A/B)

```bash
./console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 --allow-root-user
```

Jede Seite wird von jedem Server abgerufen, und die zweite Tabelle stellt sie nebeneinander:

```
  A/B, measured back to back (B/A below 1 means B is faster for the median, above 1 more req/s):
  url       A                 B                      A median  B median    B/A  A req/s  B req/s    B/A
  /         www.example.com   www.example.com:8080       9.86      2.08  x0.21      368     1327  x3.61
```

So bleibt der Vergleich fair:

- **Unmittelbar nacheinander.** Die Anfragen einer Seite werden in Runden aufgeteilt (`--rounds=2`). In jeder Runde
  erhält jeder Server seinen Anteil, einer nach dem anderen, und die Reihenfolge wird in jeder zweiten Runde umgekehrt.
  Wird der Rechner nach der Hälfte stärker belastet, spüren das beide Server; keiner wird nur in der ruhigen Minute
  gemessen.
- **Derselbe Client, dieselben Verbindungen.** Ein Verbindungspool pro Server für den ganzen Lauf, geöffnet während des
  Aufwärmens (das mindestens eine Anfrage pro Verbindung umfasst), sodass keine gemessene Anfrage einen TLS-Handshake
  bezahlt. Mit `--no-keepalive` öffnet jede Anfrage eine neue Verbindung; das verwenden Sie, um den Handshake zu
  messen.
- **Dieselbe Anfrage.** Dieselben Header, dasselbe `Accept-Encoding` (standardmäßig `gzip`, `--encoding=br`,
  `--encoding=none`), dieselbe HTTP-Version (was curl aushandelt, oder `--http=1.1` / `--http=2`).

Beurteilen Sie einen A/B-Vergleich nur anhand von Läufen, die unmittelbar nacheinander gemacht wurden. Auf einem
geteilten Rechner verschiebt die Hintergrundlast die Ergebnisse von einer Sitzung zur nächsten um 10 bis 30 %; eine
Zahl von heute Morgen, verglichen mit einer von heute Abend, sagt mehr über den Rechner als über die Server.

### Weitere Optionen

| Option | Wirkung |
|---|---|
| `--requests=N` (oder `--runs=N`), `--concurrency=N`, `--warmup=N`, `--rounds=N` | die Last pro URL und Server: gemessene Anfragen, gleichzeitige Anfragen, vorab ungemessene Anfragen (mindestens eine pro Verbindung, damit keine gemessene Anfrage eine öffnet), Runden, in die die Anfragen aufgeteilt werden |
| `--format=table`, `--format=json`, `--format=csv` | die Ausgabe; dasselbe wie `--json` und `--csv` |
| `--auth=user:password` | HTTP-Basic-Auth, z. B. für eine Staging-Website; oder setzen Sie `EXP_BENCHMARK_AUTH`, damit das Passwort nicht in der Shell-History landet. Es wird nie gespeichert. |
| `--header="Name: value"` (wiederholbar), `--cookie="..."` | zusätzliche Header; ein Sitzungs-Cookie misst eine Seite im angemeldeten Zustand. Werte von Cookie und Authorization werden als `(hidden)` gespeichert. |
| `--resolve=www.example.com:443:127.0.0.1` | einen Host unter einer anderen Adresse erreichen, wie `curl --resolve` |
| `--timeout=30` | Sekunden pro Anfrage |
| `--tool=ab`, `--tool=wrk`, `--tool=oha` | einen installierten Lastgenerator statt des eingebauten Clients verwenden. Die Tabelle zeigt `(ab)` in der Cache-Spalte und nur das, was dieses Werkzeug meldet (wrk läuft `--duration=10` Sekunden statt einer Anzahl von Anfragen). Die Kopfzeile nennt die installierten Werkzeuge. |

## Wiederholbare Läufe

Eine Zahl bedeutet nur etwas neben einer anderen, die auf dieselbe Weise gemessen wurde. Was zwei Läufe vergleichbar
hält:

- **Feste Anzahlen.** `--warmup=N` ungemessene Anfragen, dann `--runs=N` gemessene pro Seite und Server (Kernel:
  `--repeat`, Micro: `--repeat` und `--warmup`). Halten Sie sie von Lauf zu Lauf gleich; mehr Läufe machen p99
  stabiler (bei 100 Läufen ist p99 fast das Maximum, bei 1000 sagt es etwas aus).
- **Dieselben Seiten, dieselbe Reihenfolge.** Die Standardliste wird jedes Mal gleich gebildet (siehe oben);
  `--urls-file` legt sie fest.
- **Jede Statistik.** Median, p90, p99, Minimum, Maximum und die Standardabweichung (`sd`, Stichprobe, n - 1) für jede
  Zeile, in der Tabelle, in `--format=json` und in `--format=csv`.
- **Die Umgebung wird festgehalten.** `--format=json` und `--save` schreiben neben die Zeilen einen Block
  `environment`:

```json
"environment": {
    "date": "2026-10-06T02:30:26Z", "php": "8.5.11", "sapi": "cli", "engine": "php-cli",
    "opcache": false, "jit": false, "xdebug": false, "os": "Linux 5.14.0-687.5.3.el9_8.x86_64",
    "arch": "x86_64", "machine": "web.example.com", "cpus": 12, "load": 9.59,
    "exponential": "6.0.15stable", "git_commit": "3a5029891ec19884b64c8bf8e6bbeeb793b57c61", "ci": ""
}
```

  `opcache` und `engine` beschreiben das PHP, das den Benchmark ausgeführt hat (im HTTP-Modus ist das der Client;
  die gemessenen Server werden pro Zeile anhand ihres Headers `Server` im Feld `server` genannt). Der Commit wird aus
  `.git` gelesen, ohne git auszuführen (einschließlich eines verknüpften Worktrees und gepackter Refs), oder aus
  `GITHUB_SHA`. Ein Vergleich mit einer Baseline, die unter einer anderen PHP-Minor-Version oder einer anderen
  Einstellung für OPcache, JIT oder Xdebug entstanden ist, gibt einen Hinweis aus, der den Unterschied nennt, denn dann
  werden die Konfigurationen verglichen, nicht der Code.

### Rauschen und wie Sie es verringern

| Quelle | Wirkung | Was zu tun ist |
|---|---|---|
| Andere Arbeit auf dem Rechner (Load Average in der Kopfzeile) | 10 bis 30 % von einer Sitzung zur nächsten auf einem geteilten Server; vor allem in p90 und p99 | Messen Sie, wenn der Rechner ruhig ist; vergleichen Sie nur unmittelbar nacheinander gemachte Läufe; `--compare` wechselt die Server innerhalb eines Laufs ab |
| CPU-Frequenzskalierung und Turbo | die ersten Sekunden sind schneller oder langsamer als der Rest | Aufwärmläufe; auf einem dedizierten Benchmark-Rechner den Governor `performance` setzen |
| Xdebug geladen | alles um ein Mehrfaches langsamer | nie damit messen; die Kopfzeile zeigt `Xdebug ON` |
| OPcache aus (Standard der CLI) oder ein anderes JIT | Kernel- und Micro-Zahlen ändern sich um einen Faktor | die Einstellung der Baseline beibehalten; der Hinweis beim Vergleich nennt einen Unterschied |
| Antwort-Cache, Ansichts-Cache, kompilierte Templates | ein Treffer ist ein bis zwei Größenordnungen schneller als ein Rendering | sagen Sie, was Sie messen: warm (Standard), `--cold`, `--cold-clear` |
| Wenige Läufe | p99 von 100 Läufen liegt nahe am Maximum, ein einzelner Ausreißer | `--runs=500` oder mehr für Tail-Latenzen |
| Kleine Zahlen | ein Unterschied von 0,2 ms sind 50 % von 0,4 ms | die Rauschgrenze `--min-delta` des Vergleichs (http, kernel); Micro-Messungen leisten pro Iteration genug Arbeit, um Millisekunden zu dauern |
| Netzwerk und TLS | Handshakes und ein entfernter Client fügen eigene Zeit hinzu | vom Server selbst aus messen (Loopback oder sein eigener Name); Keep-alive ist an, und das Aufwärmen öffnet die Verbindungen |
| Ein anderer Rechner | alles | die Modi http und kernel nur auf dem Rechner der Baseline vergleichen; der Micro-Modus rechnet den Rechner heraus (siehe unten) |

## Kernel-Modus

```bash
./console exp:benchmark kernel --allow-root-user
./console exp:benchmark kernel --repeat=50 --node=144 --allow-root-user
./console exp:benchmark kernel --probe=render_warm --probe=db_query --allow-root-user
```

| Messung | Was gemessen wird |
|---|---|
| `boot` | ein neuer PHP-Prozess von seinem Start bis zur Verbindung mit der Datenbank: Autoload, Einstellungen, Siteaccess, Extensions |
| `process` | derselbe Kindprozess vom Start bis zum Ende, wie ihn der Elternprozess sah (er enthält `render_cold`) |
| `render_cold` | die vollständige Ansicht eines Knotens, einmal in diesem frischen Prozess gerendert: leere prozessinterne Caches, kompilierte Templates so, wie sie auf der Festplatte liegen |
| `render_warm` | dieselbe vollständige Ansicht im eigenen Prozess des Benchmarks, nach einem ungemessenen Rendering |
| `ini_load` | `content.ini` erneut aus dem INI-Cache gelesen |
| `content_fetch` | ein Inhaltsobjekt und seine Attribute aus der Datenbank |
| `node_list` | bis zu 20 Kinder des Startseitenknotens |
| `db_query` | ein indiziertes `SELECT` auf der Knotentabelle |
| `cache_write`, `cache_read` | ein Eintrag von 16 KB, geschrieben und gelesen über den Cluster-Dateihandler |
| `image_alias` | ein Bildattribut abgerufen und einer seiner vorhandenen Aliase nachgeschlagen (nie einer, der skaliert werden müsste) |

`boot`, `process` und `render_cold` starten `--boot-repeat=5` Prozesse; jede andere Messung läuft `--repeat=20`-mal.
Ohne `--node` verwenden die Render-Messungen die Startseite, es sei denn, deren vollständige Ansicht ist fast leer (eine
aus Layout-Blöcken aufgebaute Startseite rendert diese im Seitenlayout, nicht in der vollständigen Ansicht); dann die
Menüseite mit der größten vollständigen Ansicht. Die Spalte note nennt, was verwendet wurde. `mem peak` ist der
Speicher, den die Messung über das zuvor Belegte hinaus benötigte (unter PHP 8.0 und 8.1, die den Spitzenwert nicht
zurücksetzen können, ist es der bisherige Spitzenwert des Prozesses).

Kernel-Zahlen geben an, was eine Anfrage kostet, bevor Webserver, Antwort-Cache und Netzwerk etwas hinzufügen oder
abziehen. Verwenden Sie sie, um zu sehen, was eine Codeänderung kostet, und HTTP-Zahlen, um zu sehen, was ein Besucher
bekommt.

## Micro-Modus

```bash
./console exp:benchmark micro --allow-root-user
./console exp:benchmark --micro --repeat=50 --warmup=5 --allow-root-user
./console exp:benchmark micro --probe=template_compile --probe=ini_parse --allow-root-user
php bin/php/benchmark.php micro --format=json > micro.json      # without the console
```

Kein Webserver, keine Datenbank, kein Antwort-Cache: nur PHP und der Code des Kernels, daher läuft er gleich auf einem
Laptop, einem Server und einem CI-Runner. Jede Messung leistet pro Iteration eine feste Menge Arbeit, läuft
`--warmup=3` Iterationen ungemessen und `--repeat=30` gemessen.

| Messung | Eine Iteration |
|---|---|
| `template_compile` | fünf mitgelieferte Templates (`navigator/google.tpl`, beide Seitenlayouts, `content/edit.tpl`, `node/view/full.tpl`) geparst und zu PHP kompiliert, jedes Mal neu |
| `template_render` | das kompilierte `navigator/google.tpl` und ein festes Template aus Schleifen, Bedingungen, Operatoren, Escaping und einer Übersetzung, mit festen Variablen gerendert |
| `ini_parse` | sieben Dateien aus `settings/` mit `eZINI::parseFile()` geparst: kein INI-Cache und keine Überschreibungsverzeichnisse, sodass jede Installation denselben Text parst |
| `autoload_map` | `autoload/ezp_kernel.php` gelesen, wie es das erste Nachschlagen einer Klasse in einer Anfrage tut |
| `autoload_miss` | 2000 `class_exists()` für Namen, die niemand definiert, durch jeden registrierten Autoloader |
| `uri_parse` | 500 `eZURI`-Parsevorgänge von Modul-URLs mit geordneten und benannten Ansichtsparametern |
| `i18n_load` | `share/translations/ger-DE/translation.ts` (2,4 MB) geparst, kein Übersetzungs-Cache, keine Übersetzungen von Extensions |
| `i18n_lookup` | 3000 Nachschlagevorgänge seiner Meldungen nach Kontext und Quelle |
| `datatype_validate` | 360 Validierungen gesendeter Eingaben durch die Datentypen Ganzzahl, Gleitkommazahl, Textzeile, E-Mail und Datum (gültige und ungültige Werte) sowie von Ganzzahl-Klasseneinstellungen, gegen im Speicher aufgebaute Inhaltsklassenattribute |

Geschrieben werden nur kompilierte Templates und ein Fixture-Template in einem Verzeichnis des Cache-Verzeichnisses
(`benchmark-micro/`), das am Ende entfernt wird.

```
  probe               n   ops   min  median   p90   p99   max    sd    ops/s  x calib  err  note
  template_compile   30     5  56.6    65.3  82.6  85.6  85.9  8.94     76.6    6.978    0  5 shipped templates, ...
  ini_parse          30     7  4.32    5.23  5.87  6.13  6.16  0.47     1337    0.559    0  7 files of settings/ ...
  i18n_lookup        30  3000  3.27    3.39  3.77  4.08  4.12  0.22   885768    0.362    0  300 messages of ger-DE, 10 times
```

- Zeiten sind Millisekunden pro Iteration; `ops` ist die Arbeit, die eine Iteration leistet, `ops/s` die Operationen
  pro Sekunde beim Median.
- **x calib** ist der Median geteilt durch den Median der **Kalibrierungsschleife**: feste reine PHP-Arbeit
  (Ganzzahlarithmetik, Aufbau von Zeichenketten, Sortieren, ein regulärer Ausdruck, Hashing, ein assoziatives Array),
  gemessen vor und nach den Messungen im selben Prozess. Auf einem doppelt so schnellen Rechner halbieren sich beide,
  und das Verhältnis bleibt; dieses Verhältnis vergleicht die CI-Prüfung. Die Kopfzeile gibt den Kalibrierungsmedian
  und seine Drift zwischen vorher und nachher an; eine Drift von mehr als etwa 10 % bedeutet, dass sich die Last des
  Rechners während des Laufs geändert hat.

## Die Ergebnisse lesen

```
  target                 url    n   min  median   p90   p99   max    sd  req/s  err  status  bytes  cache
  www.example.com        /     40  7.51    10.7  18.0  29.7  30.1  5.31    307    0  200:40  16.6k  HIT:40
  www.example.com:8080   /     40  1.16    2.55  3.44  4.17  4.35  0.69   1110    0  200:40  15.3k  HIT:40
```

- Zeiten sind Millisekunden, von curl selbst für jede Anfrage gemessen (vom Beginn der Anfrage bis zum letzten Byte),
  nicht von der Schleife darum herum.
- **median** ist die typische Anfrage. **p90** und **p99** geben an, wie lange die langsamsten 10 % und 1 % mindestens
  warten; sie zeigen Warteschlangen, Garbage Collection, einen abgelaufenen Cache (p95 steht in der JSON- und
  CSV-Ausgabe). **sd** ist die Standardabweichung: klein im Vergleich zum Median bedeutet gleichmäßige Antworten, so
  groß wie der Median bedeutet, dass der Lauf verrauscht war oder die Seite mal gerendert wird und mal aus einem Cache
  kommt. Perzentile werden zwischen den beiden nächstgelegenen Messwerten interpoliert, nach der Methode von numpy,
  R Typ 7 und Excel `PERCENTILE.INC`. Bei 100 Anfragen liegt p99 nahe am Maximum.
- **req/s** sind die erfolgreichen Anfragen geteilt durch die Zeit, die die Durchgänge gedauert haben. Der Wert hängt
  von `--concurrency` ab: vier gleichzeitig und je 10 ms können etwa 400 nicht überschreiten.
- **err** zählt Transportfehler und Antworten ab 400; die Zeilen unter der Tabelle nennen sie. Eine abgelehnte
  Verbindung hat keine Zeit und bleibt bei den Zeitmessungen außen vor, sodass ein defekter Server nie schnell wirkt.
- **bytes** ist die mittlere Größe einer Antwort, wie sie übertragen wurde (komprimiert, wenn der Server komprimiert
  hat).

Am meisten zählt meist der Unterschied zwischen einem Cache-Treffer und einem Rendering, der ein bis zwei
Größenordnungen beträgt. Gemessen auf dem Demonstrationsserver am 5. Oktober 2026 (12 Kerne, mit anderer Arbeit
geteilt, Last etwa 8), 100 Anfragen pro Seite, Parallelität 4:

| | Apache + PHP-FPM, Median | Velocity (:8080), Median |
|---|---|---|
| warm, gecachte Seiten (4 Seiten) | 9,4 bis 11,4 ms | 1,3 bis 3,3 ms |
| kalt (`--cold`), dieselben Seiten gerendert | 70 bis 173 ms | 78 bis 216 ms |
| Anmeldeseite des Admins (nie gecacht) | 45 ms | 39 bis 41 ms |

Der Antwort-Cache entscheidet also über den größten Teil dessen, worauf ein Besucher wartet, und eine einzelne Zahl
„welcher Server ist schneller“ führt in die Irre: Die Reihenfolge der beiden Server war bei den meisten Seiten für
gecachte und für gerenderte Seiten umgekehrt.

## Einen Lauf speichern und Verschlechterungen erkennen

```bash
mkdir -p var/benchmark
./console exp:benchmark --save=var/benchmark/baseline.json --allow-root-user
# ... deploy a change ...
./console exp:benchmark --baseline=var/benchmark/baseline.json --allow-root-user
echo $?        # 0 no regression, 1 a metric regressed, 2 usage error or refused
```

Zeilen werden nach Server und Pfad zugeordnet (Kernel-Zeilen nach dem Namen der Messung). Eine Kennzahl gilt als
verschlechtert, wenn:

| Kennzahl | Verschlechtert, wenn |
|---|---|
| Median, p95 | sie um mehr als `--threshold` Prozent (Standard 15) **und** um mehr als `--min-delta` Millisekunden (Standard 1) gestiegen ist |
| req/s (HTTP-Zeilen) | sie um mehr als `--threshold` Prozent gefallen ist, während der Median um mehr als `--min-delta` gestiegen ist |
| Fehlerquote | sie um mehr als einen Prozentpunkt gestiegen ist |

Die Rauschgrenze gibt es, weil kleine Zahlen springen: Ein Datenbank-Roundtrip von 0,2 ms, der einmal 0,3 ms dauert,
ist 50 % langsamer und bedeutet nichts. Eine Seite, die im neuen Lauf fehlt, wird aufgeführt, aber nicht gezählt.
`--all-checks` zeigt jeden Vergleich, nicht nur die Verschlechterungen.

Zwei gespeicherte Läufe lassen sich vergleichen, ohne etwas zu messen, und ein gespeicherter Lauf lässt sich erneut
ausgeben:

```bash
./console exp:benchmark compare var/benchmark/baseline.json var/benchmark/today.json --allow-root-user
./console exp:benchmark show var/benchmark/today.json --allow-root-user
```

### In der CI

Das Repository führt den Micro-Modus bei jedem Push und Pull Request aus, der PHP, Templates, Einstellungen oder den
Benchmark selbst betrifft: der Workflow `.github/workflows/performance.yml`, Job „Micro benchmark (PHP 8.5)“.

```bash
php bin/php/benchmark.php micro --repeat=30 --warmup=3 --baseline=tests/benchmark/baseline.json --all-checks
```

- **Normalisiert.** CI-Runner unterscheiden sich von Lauf zu Lauf in der Geschwindigkeit. Ein Micro-Lauf wird mit der
  Baseline verglichen, nachdem der Median jeder Messung durch den eigenen Kalibrierungsmedian des Laufs geteilt wurde;
  ein langsamerer Runner ist also keine Verschlechterung, eine Messung, die relativ zur Kalibrierungsschleife
  langsamer wurde, schon. Der Vergleich gibt die Geschwindigkeit dieses Rechners gegenüber der der Baseline aus
  (`x0.86` = 14 % langsamer) und pro Messung die normalisierten Mediane und ihre Änderung.
- **Nachsichtig.** Nur eine Messung, die nach der Normalisierung mehr als 40 % langsamer ist, zählt (`--threshold=40`
  ist der Standard für micro; http und kernel behalten 15). Eine Micro-Messung, die fehlschlägt oder häufiger
  fehlschlägt als in der Baseline, zählt ebenfalls.
- **Nur eine Warnung.** Der Workflow ist von PHPUnit und Quality getrennt, und sowohl sein Job als auch der
  Benchmark-Schritt haben `continue-on-error: true`: Eine Verschlechterung erscheint als Warnungsannotation und in der
  Job-Zusammenfassung, die Prüfung bleibt grün, und die Haupt-CI kann dadurch weder rot noch unzuverlässig werden. Der
  Lauf wird als Artefakt `benchmark-micro` aufbewahrt.
- **Zur Pflicht machen**, sobald mehrere Läufe auf `main` deutlich innerhalb des Schwellenwerts geblieben sind:
  Entfernen Sie die beiden Zeilen `continue-on-error: true` aus `performance.yml` und nehmen Sie „Micro benchmark
  (PHP 8.5)“ in die erforderlichen Statusprüfungen des Branch-Schutzes von `main` auf. Erhöhen oder senken Sie
  `--threshold` im selben Schritt, wenn die Läufe zeigen, dass es nötig ist.

**Die Baseline erneuern**, nach einer Änderung, die die Geschwindigkeit ändern soll, nach dem Hinzufügen einer Messung
oder wenn PHP aktualisiert wird. Am besten aus einem CI-Lauf von `main`, der dort misst, wo die Prüfung läuft:

```bash
gh run list --workflow=performance.yml --branch=main --limit=5
gh run download <run id> -n benchmark-micro -D var/tmp/benchmark-micro
cp var/tmp/benchmark-micro/benchmark-micro.json tests/benchmark/baseline.json
```

oder lokal, auf einem ruhigen Rechner mit PHP 8.5, OPcache für die CLI ausgeschaltet (`php -d opcache.enable_cli=0`,
der Standard) und ohne Xdebug:

```bash
php bin/php/benchmark.php micro --save=tests/benchmark/baseline.json
```

Committen Sie `tests/benchmark/baseline.json` für sich allein mit einer Nachricht, die sagt, warum sich die
Geschwindigkeit geändert hat.

Die Modi http und kernel funktionieren in der CI genauso, ohne die Normalisierung:

```bash
./console exp:benchmark kernel --repeat=30 --baseline=ci/benchmark-kernel.json --threshold=20 --format=json \
    --allow-root-user > benchmark.json
```

- `--format=json` gibt den ganzen Lauf (Einstellungen, Umgebung, Zeilen, den Vergleich) auf stdout aus;
  `--format=csv` gibt die Zeilen aus. Der Fortschritt geht nach stderr, sodass die Ausgabe weitergeleitet werden kann.
  `-q` unterdrückt den Fortschritt und behält das Ergebnis. Auf GitHub Actions wird jede Verschlechterung zusätzlich
  als Annotation `::warning` ausgegeben.
- Messen Sie auf demselben Rechner wie die Baseline, ohne dass etwas anderes läuft, und bewahren Sie die Baseline im
  Repository des Projekts auf, nicht in `var/`. Eine Baseline von einem anderen Rechner vergleicht die Rechner.
- Der Kernel-Modus ist stabiler als http: kein Netzwerk, kein Webserver, kein Antwort-Cache. Der HTTP-Modus auf einem
  CI-Runner, der seinen eigenen Server startet (`php -S`, Velocity, ein Container), funktioniert mit
  `--base=http://127.0.0.1:<port>`; für Loopback gibt es keine Obergrenze an Anfragen.
- Cache-Treffer von 1 bis 3 ms schwanken auf einem ausgelasteten Rechner von Lauf zu Lauf um mehr als 15 %. Erhöhen Sie
  dafür `--threshold` oder `--min-delta`, oder prüfen Sie gerenderte Seiten mit `--cold`, deren Zahlen größer und
  stabiler sind.

Das gespeicherte Format hat eine Version (`"format": 1`); eine Baseline einer anderen Version wird abgelehnt, statt
falsch verglichen zu werden.

## Dateien

| Datei | |
|---|---|
| `bin/php/benchmark.php` | der Befehl (`exp:benchmark`, `exp:bench`) |
| `kernel/private/classes/commands/benchmark.php` | Optionen, Sicherheitsregeln, Ausgabe |
| `kernel/classes/expbenchmark.php` | Statistik, Formate, Prüfung auf Verschlechterung (keine Ein-/Ausgabe) |
| `kernel/classes/expbenchmarkhttp.php` | der HTTP-Client (curl_multi) und die Parser für ab, wrk und oha |
| `kernel/classes/expbenchmarkkernel.php` | die Kernel-Messungen |
| `kernel/classes/expbenchmarkmicro.php` | die Micro-Messungen und die Kalibrierungsschleife |
| `tests/benchmark/baseline.json` | die Micro-Baseline, mit der die CI-Prüfung vergleicht |
| `.github/workflows/performance.yml` | die CI-Prüfung (nur Warnung) |
| `tests/tests/kernel/classes/benchmark/` | die Tests: Statistik, Normalisierung, Vergleich, der Umgebungsblock, Formate und Parser ohne Netzwerk; der HTTP-Client gegen einen `php -S`-Server auf 127.0.0.1, den der Test startet und beendet |

Die Tests ausführen: `php vendor/bin/phpunit tests/tests/kernel/classes/benchmark/`.

## Verwandte Seiten

- [Benchmarking von Exponential](../../anleitungen/benchmarking.md): der Leitfaden, vom ersten Lauf bis zur CI-Prüfung.
- [Konsole](../../bc/6.0/konsole.md): jeder Befehl.
- [Velocity-Engines](../../bc/6.0/velocity-engines.md): die Server, die Sie vergleichen möchten.
- [HTTP-Caching](../../bc/6.0/http-caching.md) und [Cache-Aufwärmung](../../bc/6.0/http2-und-cache-vorwaermen.md): was die
  warmen Zahlen messen.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
