# Twig-Templates: Leistung (Geschwindigkeit)

Diese Seite ist für Site-Bauer und Entwickler, die wissen müssen, wie schnell `.twig`-Seiten sind, warum, und wie sie
ihre eigenen messen. Sie beschreibt, was die Twig-Engine tut, um schnell zu sein, gibt die Messungen einer
Entwicklungsinstallation vom 2026-10-29 mit ihren Bedingungen an und zeigt, wie man sie wiederholt. Der Vergleich aller
drei Engines an einem Ort, mit einem ruhigeren Lauf gecachter Seiten, dem Debug-Rendering, das ein Entwickler sieht, und
wo eine Anfrage ihre Zeit verbringt, ist [Geschwindigkeit der drei Template-Engines](template-engines-geschwindigkeit.md).

## Was .twig schnell macht

- **Einmal kompiliert.** Twig kompiliert eine `.twig` zu einer PHP-Klasse in `<Cache-Verzeichnis>/twig/compile-secure`.
  Der Klassenname enthält die Änderungszeit der Datei, so kompilieren die persistenten Worker von Velocity eine
  geänderte Datei einmal.
- **Eine Umgebung pro Prozess.** Velocity baut die Twig-Umgebung einmal pro Worker und meldet dabei jeden Operator und
  jede Template-Funktion als Twig-Filter und -Funktion an. PHP-FPM, das jede Anfrage neu beginnt, löst nur die Namen
  auf, die ein Template verwendet (alle rund 1.700 zu bauen kostete 10 bis 25 ms pro Anfrage).
- **Operatoren in PHP.** Die logischen, arithmetischen und Steuer-Operatoren, `wash`, `implode`, `contains` und
  `concat` laufen direkt in PHP, als statische Methoden, die das kompilierte Template ohne Nachschlagen aufruft, mit
  einem schnellen Weg für einfache Zahlen und Texte.
- **Direkte Aufrufe für alles Gebrückte.** Jeder andere Operator, die Template-Funktionen und die Hilfsfunktionen des
  Konverters werden ebenfalls als direkte statische Aufrufe kompiliert, sodass ein kompiliertes Template nie einen Namen
  in der Twig-Umgebung nachschlägt und keine Anfrage Twig seine Erweiterungen einrichten lässt. Vor dieser Änderung
  kostete das Einrichten etwa 8 ms bei jeder PHP-FPM-Anfrage, und das Debug-Rendering einer `.twig`-Seite brauchte 82
  bis 113 ms Skriptzeit gegen 25 bis 36 ms für ihre Smarty-Fassung; danach 25 bis 36 ms, so viel wie Smarty. Ein vor der
  Änderung kompiliertes Template funktioniert weiter.
- **Übersetzungen beim Kompilieren.** Ein konstanter `i18n`-Text wird beim Kompilieren übersetzt; die kompilierte
  Klasse wird pro Siteaccess und Sprache gehalten. Auf der Startseite der Media-Demo nahm das 37,5 ms Operatorzeit von
  jeder ungecachten Anfrage.
- **Ein Cache-Block für die ganze Seite**, das Benutzermenü des Besuchers danach frisch gerendert
  ([Caching](../../eng-us/guides/twig-templates-caching.md), Englisch).

## Gecachte Seiten

Eine gecachte Seite beantwortet der Antwort-Cache von Velocity oder der rollenabhängige HTTP-Cache, bevor ein Template
läuft; die Template-Engine macht dafür keinen Unterschied. Gemessen auf einer Entwicklungsinstallation von einem
anderen Server im selben Netz aus (Umlaufzeit etwa 3 ms), 8 Seiten des umgewandelten Media-Designs, 200 Anfragen pro
Seite, drei Läufe (3.600 gemessene Anfragen pro Engine und Server), am 2026-10-29. Die Last des gemessenen Servers lag
während der Läufe bei 9 bis 80 auf 12 Kernen (andere Arbeit lief), die des Clients bei 40 bis 80. "Server" ist die Zeit
bis zum ersten Byte der Seite minus die einer statischen Datei, die direkt davor über dieselbe Verbindung geholt wurde;
so ist die Umlaufzeit des Netzes herausgerechnet.

**Velocity (:8080):**

| Engine | Server Median | Server p95 | Server p99 | Gesamt Median | Gesamt p95 | Anfragen ≤ 33 ms | ≤ 20 ms | ≤ 13 ms |
|---|---|---|---|---|---|---|---|---|
| `.twig` | 0,1 ms | 23 ms | 74 ms | 6,1 ms | 56 ms | 96,7 % | 94,5 % | 93,3 % |
| `.stpl` | 0,1 ms | 27 ms | 67 ms | 6,0 ms | 55 ms | 96,1 % | 93,8 % | 92,2 % |
| `.tpl` | 0,1 ms | 28 ms | 76 ms | 5,4 ms | 56 ms | 96,2 % | 93,9 % | 92,4 % |

**Apache mit PHP-FPM (:443):**

| Engine | Server Median | Server p95 | Server p99 | Gesamt Median | Gesamt p95 | Anfragen ≤ 33 ms | ≤ 20 ms | ≤ 13 ms |
|---|---|---|---|---|---|---|---|---|
| `.twig` | 11,8 ms | 92 ms | 203 ms | 23,7 ms | 177 ms | 74,7 % | 66,6 % | 53,8 % |
| `.stpl` | 11,9 ms | 98 ms | 227 ms | 23,3 ms | 179 ms | 76,0 % | 66,8 % | 53,6 % |
| `.tpl` | 11,6 ms | 107 ms | 242 ms | 23,3 ms | 211 ms | 75,4 % | 66,7 % | 54,4 % |

Was die Zahlen sagen:

- Auf Velocity wird eine gecachte Seite in etwa der Umlaufzeit des Netzes ausgeliefert (Server-Median nahe null,
  Gesamt-Median 5 bis 6 ms mit Umlaufzeit): der Median liegt für alle drei Engines weit unter 13 ms.
- Die langsamen Anfragen (p95, p99, Maximum) sind für alle drei Engines dieselben und kommen von der Last der Maschine
  während der Läufe, nicht von einer Template-Engine; ein Lauf in einem ruhigen Zeitfenster ist nötig, um das Maximum
  pro Anfrage zu zeigen.
- Mit PHP-FPM kostet eine gecachte Seite etwa 12 ms PHP, bevor der HTTP-Cache antwortet. Das ist die Untergrenze von
  Apache mit PHP-FPM, für jede Engine.

Ein zweiter Lauf am selben Nachmittag (15:51 bis 16:32, Last 5 bis 18, 600 Anfragen je Seite, Engine und Server) ergab
auf Velocity für alle drei Engines eine mittlere Serverzeit von 0,1 ms und auf Apache mit PHP-FPM 7,5 bis 7,6 ms; zwei
`.twig`-Seiten hatten alle 600 Serverzeiten auf Velocity bei oder unter 33 ms. Die Tabellen und die Bedingungen dieses
Laufs stehen in [Geschwindigkeit der drei Template-Engines](template-engines-geschwindigkeit.md#1-gecachte-seiten).

## Ungecachte Seiten

Mit einer eindeutigen Query pro Anfrage (die Antwort-Caches antworten nicht, der Inhalts-View-Cache schon), vom selben
Client nach den gecachten Läufen, 2 × 50 Anfragen pro Seite, Last 8 bis 25:

| Server | `.twig` Median | `.tpl` Median | `.stpl` Median | p95 (alle) |
|---|---|---|---|---|
| Velocity | 0,100 bis 0,115 s | 0,106 bis 0,155 s | 0,092 bis 0,115 s | 0,17 bis 0,26 s |
| Apache mit PHP-FPM | 0,070 bis 0,092 s | 0,090 bis 0,109 s | 0,069 bis 0,094 s | 0,11 bis 0,25 s |

Auf derselben Maschine gemessen, die Anfragen der drei Engines verschränkt (jede Runde fragt dieselbe Seite bei jeder
Engine an, in wechselnder Reihenfolge), 20 Runden, Last 13 bis 15:

| Server | Seite | `.twig` / `.tpl` | `.twig` / `.stpl` |
|---|---|---|---|
| Velocity | Fitness (eine Liste) | 0,88 | 1,13 |
| Velocity | ein Artikel | 0,90 | 1,19 |
| Apache | Fitness | 0,98 | 1,06 |
| Apache | ein Artikel | 0,83 | 0,90 |

Eine ungecachte `.twig`-Seite ist so schnell wie die `.tpl`-Seite oder schneller, und liegt innerhalb von etwa 20 %
der `.stpl`.

## Der Leistungswächter

`tests/tests/lib/twig/expTwigPerformanceGuardTest.php` (PHPUnit, `@group performance`, ohne Server und Datenbank)
rendert drei Templates als `.tpl` und als die `.twig`, die der Konverter schreibt, 15 Runden zu 20 Renderings, die
Reihenfolge wechselt von Runde zu Runde, und verlangt, dass der Median der Verhältnisse `.twig`/`.tpl` höchstens 2,0
ist. Typische Ergebnisse auf einer belasteten Entwicklungsmaschine: 1,0 bis 1,7.

## Eigene Seiten messen

- **Gecachte Seiten:** von einem Client aus messen, dessen Adresse nicht in der Debug-Liste steht (`[DebugSettings]
  DebugByIP`, `DebugIPList[]`), sonst trägt jede Antwort den Debug-Bericht und umgeht die Seiten-Caches
  (`X-Exp-Cache: BYPASS (debug report)`). Eine statische Datei und die Seite über dieselbe Verbindung holen und die
  Zeit bis zum ersten Byte der statischen Datei abziehen:

  ```bash
  curl -s -o /dev/null -o /dev/null -o /dev/null -w '%{time_starttransfer} %{http_code}\n' \
       https://www.example.com/favicon.ico https://www.example.com/favicon.ico https://www.example.com/twig/fitness
  ```

- **Ungecachte Seiten:** an jede Anfrage eine eindeutige Query anhängen (`?bench=<n>`).
- **Eine Seite profilieren:** mit `[TwigSettings] Profile=enabled` und eingeschalteter Debug-Ausgabe schreibt jede
  Seite einen Hinweis "twig profile": Zeit in Operatoren, Template-Funktionen, Includes und Cache-Blöcken.
- Bei jedem Lauf die Last festhalten, Läufe zu verschiedenen Zeiten wiederholen und Engines im selben Lauf vergleichen.

## Verwandte Seiten

- [Geschwindigkeit der drei Template-Engines](template-engines-geschwindigkeit.md)
- [Fehlersuche in Twig-Templates](twig-templates-fehlersuche.md) (das Twig-Profil)
- [Twig templates: performance](../../eng-us/guides/twig-templates-performance.md) (Englisch)
- [Zwischen Template-Engines umwandeln](../hilfe/template-sprache/zwischen-template-engines-umwandeln.md)
