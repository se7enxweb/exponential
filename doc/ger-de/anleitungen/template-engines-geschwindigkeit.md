# Geschwindigkeit der drei Template-Engines: Twig, Smarty und Exponential-Templates

Diese Seite ist für Site-Builder, Betreiber und Entwickler, die mit Zahlen wissen wollen, wie schnell Seiten sind, deren
Templates Exponential-Templates (`.tpl`), Smarty-Templates (`.stpl`) oder Twig-Templates (`.twig`) sind. Sie berichtet
nur, was gemessen wurde, sagt unter welchen Bedingungen, erklärt, was jede Zahl bedeutet und was nicht, und zeigt, wie
Sie die Messungen in Ihrer eigenen Installation wiederholen. Nichts hier ist geschätzt.

Alle Messungen stammen vom 29. Oktober 2026 aus einer Entwicklungsinstallation: das Media-Demo-Design (die Website
Fit & Healthy) in drei Siteaccesses mit denselben Inhalten, einer mit dem ursprünglichen `.tpl`-Design, einer mit dem
nach `.stpl` umgewandelten und einer mit dem nach `.twig` umgewandelten Design (je 517 Templates, umgewandelt mit
`exp:template:convert`). Die Maschine hat 12 CPU-Kerne und wurde mit anderer Arbeit geteilt; die Last steht bei jedem
Lauf dabei. Apache mit PHP-FPM (PHP 8.5, Port 443) und Exponential Velocity (Port 8080) bedienten dieselbe Installation
und dieselbe Datenbank (SQLite).

## Kurz gesagt

- **Gecachte Seiten hängen nicht von der Template-Engine ab.** Eine gecachte Seite wird beantwortet, bevor ein Template
  läuft. Auf Velocity lag die mittlere Serverzeit einer gecachten Seite bei allen drei Engines bei 0,1 ms; auf Apache
  mit PHP-FPM bei 7,5 ms (`.tpl`) und 7,6 ms (`.twig`). Unterschiedlich ist der Server, nicht die Engine.
- **Ungecachte Seiten liegen in einem schmalen Band.** Wenn kein Antwort-Cache antwortet, lag der Median der Seitenzeit
  auf Velocity bei 0,100 bis 0,115 s für `.twig`, 0,106 bis 0,155 s für `.tpl` und 0,092 bis 0,115 s für `.stpl`; auf
  Apache bei 0,070 bis 0,092 s, 0,090 bis 0,109 s und 0,069 bis 0,094 s (Mediane je Seite, Last 8 bis 25).
- **In denselben Runden nebeneinander gemessen** brauchte eine ungecachte `.twig`-Seite das 0,83- bis 0,98-Fache der
  Zeit der `.tpl`-Seite und das 0,90- bis 1,19-Fache der Zeit der `.stpl`-Seite.
- **Mit eingeschalteter Debug-Ausgabe** (jede Anfrage rendert die ganze Seite und den Debug-Bericht) brauchten
  `.twig`- und `.stpl`-Seiten etwa gleich lang, im Median 0,037 bis 0,044 s, `.tpl`-Seiten 0,059 bis 0,110 s.
- **Der größte Teil einer ungecachten Anfrage ist nicht die Template-Engine**: Einstellungen, Übersetzungen, die
  Datenbankverbindung, die Regeln des HTTP-Caches und der Debug-Bericht kosten mehr als das Rendern der Templates
  (Profil unten).

## Was gemessen wird, und die Begriffe

| Begriff | Bedeutung |
|---|---|
| **gecacht** | die Seite wird von Velocitys Antwort-Cache oder vom rollenbewussten HTTP-Cache beantwortet (`X-Exp-Cache: HIT`); kein Template läuft |
| **ungecacht** | bei jeder Anfrage eine eindeutige Query (`?bench=<n>`), sodass kein Antwort-Cache antwortet (`X-Exp-Cache: BYPASS (query string)`); der Content-View-Cache, Cache-Blöcke und kompilierte Templates werden genutzt, wie bei den meisten echten Cache-Fehlschlägen |
| **Debug-Rendering** | die Adresse des Clients steht in der Debug-Liste (`[DebugSettings] DebugByIP`, `DebugIPList[]`), also trägt jede Antwort den Debug-Bericht und umgeht die Seiten-Caches (`X-Exp-Cache: BYPASS (debug report)`); das sieht ein Entwickler, angemeldet, mit der Debug-Leiste |
| **Serverzeit** | die Zeit bis zum ersten Byte der Seite minus der einer statischen Datei, die direkt davor über dieselbe Verbindung geholt wurde, sodass die Netzwerk-Laufzeit herausfällt (sie kann leicht negativ sein, wenn die statische Datei die langsamere war) |
| **gesamt** | die ganze Anfragezeit des Clients, Netzwerk eingeschlossen |
| **Toolbar-Zeit** | die Skriptzeit, die die Debug-Leiste zeigt („Exp Debug ... s“): die Zeit, die PHP für die Anfrage brauchte |
| **Median, p95, p99** | die Hälfte, 95 % und 99 % der Anfragen lagen bei oder unter dieser Zeit |
| **Last** | der Load-Average der Maschine (1 Minute) zu Beginn der Anfragen; 12 heißt, alle 12 Kerne sind beschäftigt |

## 1. Gecachte Seiten

**Bedingungen.** Von einem zweiten Server im selben Netz (Laufzeit etwa 1 bis 3 ms), dessen Adresse nicht in der
Debug-Liste steht. 8 Seiten je Engine (Startseite, drei Listenseiten, drei Artikel, eine Autorenseite), 200 Anfragen je
Seite nach 20 Aufwärmanfragen, drei Läufe nacheinander: 600 gemessene Anfragen je Seite, Engine und Server. Gelaufen am
29. Oktober 2026 von 15:51 bis 16:32, Last 5,2 bis 18,3 zu Beginn der Blöcke (andere Arbeit lief auf der Maschine).
Daten: `var/tmp/bench-outside/20261029-155155.tsv` (eine Zeile je Anfrage) und `quiet-200x3.log` (die Zusammenfassung).

Die Autorenseite fehlt in den Tabellen unten: Sie antwortet mit einer Weiterleitung (302), die kein Cache behält, und
misst deshalb etwas anderes. Die übrigen 7 Seiten ergeben 4.200 Anfragen je Engine und Server.

**Velocity (Port 8080), Serverzeit:**

| Engine | Median | p95 | p99 | Max | ≤ 13 ms | ≤ 20 ms | ≤ 33 ms |
|---|---|---|---|---|---|---|---|
| `.twig` | 0,1 ms | 3,0 ms | 20,4 ms | 805 ms | 98,4 % | 99,0 % | 99,6 % |
| `.stpl` | 0,1 ms | 3,2 ms | 25,7 ms | 88 ms | 98,0 % | 98,7 % | 99,5 % |
| `.tpl` | 0,1 ms | 2,8 ms | 17,2 ms | 1.112 ms | 98,6 % | 99,1 % | 99,7 % |

Gesamte Anfragezeit auf Velocity, Netzwerk eingeschlossen: Median 3,0 ms (`.twig`), 2,2 ms (`.stpl`), 2,9 ms (`.tpl`).

Zwei `.twig`-Seiten hatten alle 600 Serverzeiten bei oder unter 33 ms: die Liste Fitness (p99 3,9 ms, Maximum 30,5 ms,
99,5 % bei oder unter 13 ms) und der Artikel „Why Track Your Workouts?“ (p99 3,3 ms, Maximum 17,7 ms, 99,8 % bei oder
unter 13 ms).

**Apache mit PHP-FPM (Port 443), Serverzeit:**

| Engine | Median | p95 | p99 | Max | ≤ 13 ms | ≤ 20 ms | ≤ 33 ms |
|---|---|---|---|---|---|---|---|
| `.twig` | 7,6 ms | 22,5 ms | 50,6 ms | 2.065 ms | 88,5 % | 93,9 % | 96,9 % |
| `.tpl` | 7,5 ms | 18,9 ms | 45,6 ms | 140 ms | 90,9 % | 95,4 % | 98,0 % |

Gesamte Anfragezeit auf Apache: Median 10,8 ms (`.twig`) und 9,9 ms (`.tpl`).

Die `.stpl`-Seiten auf Apache stehen nicht in der Tabelle: In zwei der drei Läufe hat der HTTP-Cache sie nicht
beantwortet (`X-Exp-Cache: BYPASS (empty)` bei 400 von 600 Anfragen je Seite), diese Anfragen wurden also gerendert,
und ihr Median von 63 ms misst ein Rendering, keinen Cache. Auf Velocity wurden alle drei Engines durchgehend aus dem
Cache beantwortet.

**Was die Zahlen sagen:**

- Die Mediane der drei Engines sind auf jedem Server gleich: Eine gecachte Anfrage erreicht nie eine Template-Engine.
- Velocity beantwortet eine gecachte Seite in etwa einer Zehntelmillisekunde Serverzeit; der größte Teil der 2 bis 3 ms
  gesamt ist das Netzwerk. Apache mit PHP-FPM braucht etwa 7,5 ms, bevor der HTTP-Cache antwortet, bei jeder Engine:
  PHP startet, liest die Einstellungen und die Sitzung und erst dann den Cache. Das ist die Untergrenze von Apache mit
  PHP-FPM auf dieser Maschine.
- Die langsamen Anfragen (p99, Maximum) sind selten und treten bei allen drei Engines gleichermaßen auf; einzelne
  Maxima von 0,8 bis 2 s sind Anfragen, die auf die beschäftigte Maschine gewartet haben, keine langsamen Templates.
  Sie stehen hier, weil sie vorkamen.

## 2. Ungecachte Seiten

**Bedingungen.** Derselbe externe Client, bei jeder Anfrage eine eindeutige Query, 4 Seiten je Engine, 100 Anfragen je
Seite und Server (2 × 50), am 29. Oktober 2026 von 11:14 bis 11:24, Last 7,8 bis 25,6. Daten:
`var/tmp/bench-outside-uncached/20261029-111445.tsv` und seine `.summary.txt`.

Median der Serverzeit je Seite (die Spanne ist die schnellste und die langsamste der 4 Seiten):

| Server | `.twig` | `.tpl` | `.stpl` | p95, alle Engines |
|---|---|---|---|---|
| Velocity | 0,100 bis 0,115 s | 0,106 bis 0,155 s | 0,092 bis 0,115 s | 0,17 bis 0,26 s |
| Apache mit PHP-FPM | 0,070 bis 0,092 s | 0,090 bis 0,109 s | 0,069 bis 0,094 s | 0,11 bis 0,25 s |

In diesem Lauf renderte Apache schneller als Velocity; bei einer Last von 18 bis 25 konkurrieren Velocitys Worker mit
der anderen Arbeit auf der Maschine. Lesen Sie daraus kein allgemeines „Apache ist schneller“: Die Benchmarking-Anleitung
zeigt Läufe, in denen es umgekehrt war, und gecachte Seiten, die die meisten Besucher bekommen, lieferte Velocity oben
schneller aus.

Dieser Lauf und die Läufe nebeneinander unten entstanden vor der Änderung am Render-Pfad aus Abschnitt 3; die Zahlen
des Debug-Renderings in Abschnitt 3 zeigen die Engines danach.

**Nebeneinander in denselben Runden.** Ungecachte Zeiten schwanken mit der Last der Maschine; fairer ist es deshalb,
in jeder Runde dieselbe Seite von den drei Engines direkt nacheinander zu holen, in wechselnder Reihenfolge, sodass eine
Lastspitze alle drei gleich trifft. 20 Runden je Seite, Anfragen von der Maschine selbst (ihre Adresse steht in der
Debug-Liste, jede Antwort trägt also auch den Debug-Bericht), am 29. Oktober 2026 um 12:04 (Velocity) und 12:05
(Apache), Last 13 bis 15. Daten: `var/tmp/bench-interleaved/20261029-120441.tsv` (Velocity) und
`20261029-120506.tsv` (Apache).

| Server | Seite | `.twig` Median | `.tpl` Median | `.stpl` Median | `.twig` / `.tpl` | `.twig` / `.stpl` |
|---|---|---|---|---|---|---|
| Velocity | Fitness (eine Liste) | 0,101 s | 0,114 s | 0,089 s | 0,88 | 1,13 |
| Velocity | ein Artikel | 0,113 s | 0,125 s | 0,095 s | 0,90 | 1,19 |
| Apache | Fitness | 0,127 s | 0,130 s | 0,120 s | 0,98 | 1,06 |
| Apache | ein Artikel | 0,150 s | 0,181 s | 0,167 s | 0,83 | 0,90 |

Eine ungecachte `.twig`-Seite war 2 bis 17 % schneller als die `.tpl`-Seite und zwischen 10 % schneller und 19 %
langsamer als die `.stpl`-Seite. Unterschiede dieser Größe ändern sich von Lauf zu Lauf: Lesen Sie sie als „etwa so
schnell wie `.stpl`, etwas schneller als `.tpl`“, nicht als genaue Faktoren.

## 3. Das Debug-Rendering: was ein Entwickler sieht

**Bedingungen.** Anfragen mit eingeschalteter Debug-Ausgabe, von der Maschine selbst, 100 Anfragen je Seite und Engine,
gemessen als Skriptzeit der Toolbar und als Gesamtzeit.

| Lauf (29. Oktober 2026) | Server | Last | `.tpl` Toolbar-Median | `.stpl` Toolbar-Median | `.twig` Toolbar-Median | Daten |
|---|---|---|---|---|---|---|
| 16:25 | Velocity | 4,5 bis 12,9 | nicht gemessen | 38 bis 40 ms | 37 bis 39 ms | `var/tmp/debug-view/20261029-162548.tsv` |
| 16:40 | Apache | 4,9 bis 16,7 | nicht gemessen | 42 bis 44 ms | 42 bis 43 ms | `var/tmp/debug-view/20261029-164016.tsv` |
| 17:29 | Velocity | 4,6 bis 8,1 | 71 ms | 38 ms | 39 ms | `var/tmp/debug-view/20261029-172903.tsv` |
| 17:39 | Apache | 3,7 bis 9,0 | 60 ms | 39 ms | 44 ms | `var/tmp/debug-view/20261029-173946.tsv` |
| 17:52 | Velocity | 4,1 bis 16,6 | 110 ms | 37 ms | 38 ms | `var/tmp/debug-view/20261029-175207.tsv` |
| 18:03 | Apache | 4,2 bis 9,5 | 63 ms | 34 ms | 38 ms | `var/tmp/debug-view/20261029-180311.tsv` |

Gemessen wurden die Startseite und die Liste Fitness; die Spannen umfassen beide. Die Gesamtzeiten der Läufe ab 17:29
(`var/tmp/trpc/owner-view-c-172903.log`, `owner-view-d-175207.log`) haben dieselbe Form: Mediane von 0,037 bis 0,044 s
für `.twig` und `.stpl`, 0,059 bis 0,110 s für `.tpl`.

**Vor der Änderung am Render-Pfad.** Bis zum Nachmittag des 29. Oktober schlug ein kompiliertes `.twig` jeden
gebrückten Operator und jede Template-Funktion bei jedem Aufruf in der Twig-Umgebung nach, und der erste Nachschlag einer
Anfrage ließ Twig alle seine Erweiterungen einrichten. Im Debug-Rendering einer `.twig`-Seite lag die Skriptzeit bei 82
bis 113 ms, gegen 25 bis 36 ms für dieselbe Seite in Smarty; mit direkten Aufrufen aus dem kompilierten Template sank
sie auf 25 bis 36 ms, so viel wie bei Smarty (gemessen, als die Änderung entstand). Läufe davor, um 12:13 bei einer Last
von 17 bis 22 (`var/tmp/debug-view/20261029-121332.tsv`), zeigen Toolbar-Mediane von 80 bis 96 ms für `.twig`, 78 bis
81 ms für `.stpl` und 148 bis 172 ms für `.tpl`: Bei dieser Last war jede Engine langsamer. Vergleichen Sie Engines
deshalb innerhalb eines Laufs, nie über Läufe hinweg, die bei verschiedener Last entstanden.

## 4. Warum die Zahlen so aussehen

**Gecachte Seiten sind unabhängig von der Engine.** Velocitys Antwort-Cache und der rollenbewusste HTTP-Cache behalten
die ganze Antwort und antworten, bevor der Kernel ein Template lädt. Welche Engine die Seite einmal geschrieben hat,
spielt für die nächsten tausend Anfragen keine Rolle. Um eine Website schnell zu machen, sorgen Sie zuerst dafür, dass
ihre Seiten gecacht werden; die Engine kommt später.

**Warum `.tpl` beim vollen Rendern langsamer ist.** Die umgewandelten `.stpl`- und `.twig`-Designs halten die ganze
Seite in einem Cache-Block, mit Schlüsseln aus Knoten, Adresse, Seiteninhalt und den Rollen des Besuchers, und rendern
das eigene Menü des Besuchers danach frisch ([Caching](../../eng-us/guides/twig-templates-caching.md),
[Smarty: Caching](../../eng-us/guides/smarty-templates-caching.md), beide Englisch). Eine gerenderte Anfrage liest
dann einen Cache-Eintrag für den Seitenrahmen. Das ursprüngliche `.tpl`-Design liest mehrere Cache-Blöcke und rendert
bei jeder Anfrage die Teile dazwischen. Dasselbe Muster in einem `.tpl`-Pagelayout würde den größten Teil dieses
Abstands schließen; das eigentliche Rendern der Engines unterscheidet sich viel weniger als das Caching der Designs.

**Was Twig schnell macht.** Jedes `.twig` wird einmal zu einer PHP-Klasse kompiliert; das kompilierte Template ruft
die Operatoren und Funktionen des Template-Systems als statische Methoden auf, ohne sie nachzuschlagen; die Logik-,
Rechen- und Steueroperatoren, `wash`, `implode`, `contains` und `concat` laufen direkt in PHP; konstante Übersetzungen
werden beim Kompilieren erledigt; eine Twig-Umgebung bleibt je Prozess erhalten, sodass Velocitys dauerhafte Worker sie
einmal aufbauen ([Twig-Templates: Leistung](twig-templates-leistung.md)).

**Wo eine ungecachte Anfrage ihre Zeit verbringt.** Ein Sampling-Profil von Velocity, das die `.twig`-Startseite mit
eingeschalteter Debug-Ausgabe ausliefert (29. Oktober 2026, 9.384 Samples, `var/tmp/trpc/vprof-twig-1.txt`), als
inklusive Anteile der Anfrage (die Anteile überlappen sich, der Anteil einer Funktion enthält, was sie aufruft):

| Teil der Anfrage | Anteil |
|---|---|
| Einrichten des Übersetzers (Laden der Übersetzungsdateien) | 13 % |
| die Regeln des HTTP-Caches für die Antwort (`ezpHttpCacheListener::contract`, Aufbau seiner Konfiguration) | 13 % |
| Verbinden mit SQLite und Anwenden seiner Einstellungen | 11 % |
| der Debug-Bericht | 11 % |
| Laden von Template-Klassen (Autoload) | 5 % |
| alle kompilierten Templates zusammen, das `.twig` und die `.tpl`, die es einbindet | 24 % |

Das Rendern ist etwa ein Viertel einer solchen Anfrage. Der Rest ist Arbeit, die jede Engine bezahlt: Sie ist für `.tpl`,
`.stpl` und `.twig` gleich.

## 5. In Arbeit (nicht ausgeliefert)

Daran wird nach den Messungen oben gearbeitet; es ist **noch nicht** Teil eines Releases. Es steht hier, damit die
Zahlen im Zusammenhang gelesen werden, nicht als Versprechen:

- Werte, die Velocitys Worker zwischen Anfragen behalten, wenn sie sich von einer Anfrage zur nächsten nicht ändern (das
  Einrichten des Übersetzers, die Regelkonfiguration des HTTP-Caches).
- Die SQLite-Verbindung eines Velocity-Workers zwischen Anfragen offen halten, statt bei jeder Anfrage zu verbinden und
  die Einstellungen anzuwenden.
- Besucher, deren Adresse in der Debug-Liste steht, ebenfalls aus den Seiten-Caches bedienen, wo heute der
  Debug-Bericht jede solche Antwort an ihnen vorbeiführt.

## 6. Die Messungen wiederholen

Nutzen Sie `exp:benchmark` ([Benchmarking](benchmarking.md)); vergleichen Sie Engines im selben Lauf, mit denselben
Zahlen und denselben warmen Caches. Die Beispiele nehmen drei Siteaccesses mit denselben Inhalten an, über den Pfad
erreichbar: das `.tpl`-Design unter `/`, das `.stpl`-Design unter `/<stpl-Siteaccess>/` und das `.twig`-Design unter
`/<twig-Siteaccess>/`.

**Gecachte Seiten**, von einem Client, dessen Adresse nicht in der Debug-Liste steht (sonst umgeht jede Antwort die
Caches), Apache und Velocity direkt nacheinander:

```bash
./console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 \
    --url=/fitness --url=/<stpl-Siteaccess>/fitness --url=/<twig-Siteaccess>/fitness \
    --runs=200 --warmup=20 --save=var/benchmark/engines-cached.json --allow-root-user
```

Prüfen Sie die Spalte `cache`: `HIT:200` heißt, jede Anfrage wurde aus dem Cache beantwortet. Eine Zeile mit `BYPASS`
misst etwas anderes.

**Ungecachte Seiten**: dasselbe mit `--cold` (bei jeder Anfrage eine Query, die kein Cache kennt).

**Das Debug-Rendering**: derselbe Befehl von einem Client in der Debug-Liste. Die Spalte `cache` sagt dann
`BYPASS:<n>`, und die Zeiten sind die eines vollen Renderings mit dem Debug-Bericht. In der Entwicklungsinstallation,
von der Maschine selbst, bei Last 6,1 am 29. Oktober 2026 um 19:15 (Apache, je 40 Anfragen, 2 gleichzeitig):

```bash
./console exp:benchmark --url=/fitness --url=/smarty/fitness --url=/twig/fitness --runs=40 --warmup=5 \
    --concurrency=2 --allow-root-user
```

```
  target            url               n   min  median   p90   p99   max    sd  req/s  err  status  bytes  cache
  ----------------  ---------------  --  ----  ------  ----  ----  ----  ----  -----  ---  ------  -----  ---------
  www.example.com  /fitness         40  57.4    73.6  92.2   107   108  11.6   25.2    0  200:40  12.2k  BYPASS:40
  www.example.com  /smarty/fitness  40  43.5    60.6  81.4  95.4  97.1  13.4   31.9    0  200:40  11.9k  BYPASS:40
  www.example.com  /twig/fitness    40  39.6    53.6  60.9  63.7  63.8  6.01   36.2    0  200:40  11.9k  BYPASS:40
```

**Wohin die Zeit in einer Seite geht**: Setzen Sie `[TwigSettings] Profile=enabled` bei eingeschalteter Debug-Ausgabe;
jede Seite schreibt einen Hinweis „twig profile“ ([Fehlersuche in Twig-Templates](twig-templates-fehlersuche.md#4-das-twig-profil)).

**Ohne Server**: Der Leistungswächter rendert dieselben Templates als `.tpl` und als `.twig` in abwechselnden Runden und
schlägt fehl, wenn Twig mehr als doppelt so langsam ist:

```bash
php vendor/bin/phpunit --group performance tests/tests/lib/twig/expTwigPerformanceGuardTest.php
```

**Regeln für einen fairen Vergleich**: Notieren Sie bei jedem Lauf den Load-Average und wiederholen Sie einen Lauf, der
bei einer Last über der Zahl der Kerne entstand; vergleichen Sie Mediane, keine einzelnen Anfragen; vergleichen Sie
Engines innerhalb eines Laufs (abwechselnd, wenn Sie können); halten Sie den Debug-Zustand für alle Engines gleich;
sagen Sie, ob die Seite gecacht war; vergleichen Sie nie die gecachte Seite einer Engine mit der gerenderten einer
anderen.

## Verwandte Seiten

- [Twig-Templates: Leistung (Geschwindigkeit)](twig-templates-leistung.md)
- [Smarty templates: performance](../../eng-us/guides/smarty-templates-performance.md) (Englisch)
- [The three template engines compared](../../eng-us/guides/template-engines.md) (Englisch)
- [Benchmarking](benchmarking.md)
- [Templates nebeneinander](../hilfe/template-sprache/tpl-twig-smarty-nebeneinander.md)
