# Velocity-Antwort-Cache

Diese Seite richtet sich an Administratoren, die öffentliche Seiten mit Velocity ausliefern und sie in einem Bruchteil
einer Millisekunde beantworten möchten. Der Antwort-Cache behält gerenderte öffentliche Seiten und beantwortet
wiederholte Anfragen innerhalb des Serverprozesses, bevor ein Worker gewählt wird, sodass ein Treffer überhaupt keinen
Worker kostet. Er verhält sich wie ein kleiner Reverse Proxy: bedingte Anfragen (`304`), `ETag`, `Age`,
stale-while-revalidate, negatives Caching, komprimierte Speicherung und Invalidierung durch eine einzige berührte Datei.
Gilt für Exponential Velocity 0.0.4.x.

Eine zwischengespeicherte öffentliche Seite wird mit etwa 0,2 ms CPU ausgeliefert (gemessen auf einem Host mit
12 Kernen: eine Startseite über TLS mit gzip, etwa 2.950 Seiten pro Sekunde von einem Server, 11.985 von vier Servern
auf einem Port). Das Rendern derselben Seite dauert Hunderte von Millisekunden. Eine stark besuchte Seite schickt nie
alle Besucher gleichzeitig zu PHP, denn eine Anfrage erneuert eine abgelaufene Seite, während die anderen aus der alten
Kopie beantwortet werden.

## Einschalten

**Der Cache ist aus, bis eine Einstellung `enabled: true` sagt.** Seit Release 0.0.4.39 bedeutet keine
Cache-Einstellung auch keinen Cache. (Davor war der eingebaute Standard eingeschaltet, und das Deaktivieren des
Cache-Moduls hielt ihn nicht auf.)

1. In einer Site-Datei oder einem Modul wie `mods-available/cache.conf`:

   ```json
   { "Q": { "web": { "cache": {
     "enabled": true,
     "dir": "/var/cache/qbix/example.com"
   } } } }
   ```

   Mit einem Konfigurationsverzeichnis schaltet `qbixctl enmod cache` ihn beim nächsten Neustart ein und
   `qbixctl dismod cache` aus. Ein Modul, das nur `dir` oder `defaultTtl` setzt, schaltet ihn nicht ein. Die Site-Datei
   (`--config`) wird zuletzt gelesen, sodass `enabled: false` dort über ein Modul gewinnt; eine im Control Panel
   gespeicherte Einstellung gewinnt über beide.

2. Lassen Sie die Anwendung sagen, wie lange eine Seite leben darf:

   ```php
   <?php
   Q_Response::header('Cache-Control: public, max-age=300');
   echo renderFeed();
   ```

3. Starten Sie den Server neu oder laden Sie ihn neu, damit er die Einstellung übernimmt.

4. Prüfen Sie: Fragen Sie dieselbe öffentliche Seite zweimal an.

   ```bash
   curl -sI https://example.com/ | grep -iE 'x-cache|age'
   ```

   Die zweite Antwort trägt `X-Cache: HIT` und einen `Age`-Header.

## Was behalten wird

Eine Antwort wird nur gespeichert, wenn alles Folgende zutrifft:

- die Anfrage ist ein `GET` auf einen Pfad, der nicht dem Server selbst gehört (`/Q/...` und `/.well-known/...` werden
  nie zwischengespeichert);
- der Status ist `200`, oder `404`/`410`, wenn `negativeTtl` gesetzt ist;
- die Anfrage trägt keinen `Authorization`-Header und keines der `skip.cookies`;
- die Antwort setzt kein Cookie;
- `Cache-Control` sagt weder `no-store` noch `private`.

Der Schlüssel besteht aus Host, Pfad, Query-String und der Kodierung, in der der Inhalt gespeichert ist (gzip für
Clients, die es akzeptieren). Brotli- und gzip-Clients teilen sich einen Eintrag. Ein `q=0` in `Accept-Encoding` zählt
als "nicht akzeptiert".

Das eigene `Cache-Control: no-cache` eines Besuchers umgeht den Cache nicht, sodass das Drücken von Neu laden den
Server nicht dazu bringen kann, jede Seite erneut zu rendern. Ein Cache-Warmer sendet stattdessen den Header
`x-cache-refresh`.

Die Lebensdauer ist `s-maxage`, sonst `max-age`, sonst `defaultTtl` (Standard `0`, was nichts speichert).

Einträge liegen, sofern verwendbar, für kleine Einträge in APCu und als eine Datei pro Seite auf der Festplatte
unterhalb von `dir`, in einem zweistufigen Verzeichnis.

## Nach einem Deployment alles invalidieren: der Generationsmarker

Ein geändertes Template oder Stylesheet ist für einen Cache unsichtbar, der Seiten danach beurteilt, was die Anwendung
sagt. Berühren Sie nach einem Deployment den Marker:

```bash
touch /var/cache/qbix/example.com/.generation
qbixconsole cache:clear
```

Nur der Änderungszeitpunkt der Datei zählt. Jeder Eintrag, der zu oder vor diesem Zeitpunkt gespeichert wurde, wird als
Fehltreffer behandelt und bei seiner nächsten Anfrage ersetzt. Jeder Prozess liest den Marker höchstens einmal pro
Sekunde, sodass jeder Worker und jeder andere Server, der die Datei teilt, ihn innerhalb einer Sekunde sieht. Aus
Exponential heraus tut `exp:velocity cache clear` dasselbe, und `exp:velocity deploy` ruft es zuletzt auf
([Velocity-Engines](../../bc/6.0/velocity-engines.md)).

## Ablesen: Header und Zustand

| Header | Wann |
|---|---|
| `X-Cache: HIT` | Aus dem Cache ausgeliefert |
| `X-Cache: STALE` | Aus einer abgelaufenen Kopie ausgeliefert, während eine neue gerendert wird |
| `Age` | Sekunden seit dem Speichern des Eintrags |
| `ETag` | Bei jeder gespeicherten Seite |

`/Q/health` (vollständige Zahlen erfordern Admin-Zugriff) meldet `hits`, `misses`, `hitRate`, `stale`,
`hitsFrom.index|memory|apcu|disk`, `memory.*` und `apcu.*`. Überwiegend `disk` bei eingeschaltetem APCu bedeutet, dass
APCu seinen Teil nicht leistet: Prüfen Sie `apc.enable_cli`.

## Einstellungen

Alle unter `Q.web.cache`.

| Konfigurationsschlüssel | Standard | Bedeutung |
|---|---|---|
| `enabled` | `false` | Den Cache einschalten |
| `dir` | `<app>/files/cache/reverse` | Wo Einträge und der Marker liegen |
| `defaultTtl` | `0` | Lebensdauer in Sekunden für eine Antwort, die keine angibt |
| `skip.cookies` | `["Q_sid", "PHPSESSID"]` | Cookie-Namen (nach Präfix verglichen), die eine Anfrage als persönlich kennzeichnen |
| `staleWhileRevalidate` | `0` | Sekunden, die eine abgelaufene Seite noch ausgeliefert wird, während eine Anfrage eine neue Kopie rendert |
| `revalidateLockSeconds` | `30` | Nach dieser Zeit gilt eine Anfrage, die eine veraltete Seite erneuert, als verschwunden |
| `negativeTtl` | `0` | Sekunden, die `404`- und `410`-Antworten behalten werden |
| `refreshHeader` | `x-cache-refresh` | Anfrage-Header, der die Seite erneut rendert und speichert |
| `generationFile` | `<dir>/.generation` | Der Generationsmarker |
| `apcu.enabled` | wenn APCu verwendbar ist | Kleine Einträge zusätzlich zur Festplatte in APCu halten (erfordert `apc.enable_cli=1` unter der CLI) |
| `apcu.maxSize` | `65536` | Größter Inhalt in Bytes, der in APCu gehalten wird |
| `memory.maxEntries` | `0` | Seiten, die im eigenen Speicher des Servers vor APCu gehalten werden; `0` schaltet die Schicht aus |
| `memory.maxBytes` | `33554432` | Höchstzahl an Bytes von Inhalten, die im Speicher gehalten werden |
| `memory.maxEntrySize` | `65536` | Größter Inhalt, der im Speicher gehalten wird |
| `minifyHtml` | `false` | Leerraum in zwischengespeichertem HTML zusammenfassen |
| `middleOut` | `false` | Gespeicherte Inhalte gegen ein gemeinsames Wörterbuch komprimieren (zuerst messen) |
| `fileMode` | `0666` abzüglich der umask | Modus der Eintragsdateien |
| `dirMode` | `0755` | Modus der Cache-Verzeichnisse |
| `sweep.every` | `300` | Sekunden zwischen Bereinigungen abgelaufener Dateien; `0` schaltet die Bereinigung aus |
| `sweep.budget` | `2000` | Höchstzahl an Dateien, die eine Bereinigung betrachtet |
| `sweep.maxAge` | `0` | Auch Dateien entfernen, die älter als so viele Sekunden sind |
| `pauseFile` | keiner | Solange diese Datei existiert, antworten weder dieser Cache noch der Cache der Anwendung (Wartungsfenster) |

## Über das Control Panel verwenden

Der Tab **Cache** des Panels (`/Q/panel`) bietet Schalter für den Cache, APCu und die Speicherschicht, die aktuelle
Trefferquote, Voreinstellungen für die Lebensdauern, **Alles leeren**, Löschen nach URL oder Muster, das Vorwärmen einer
Seite und einen Browser über die gespeicherten Seiten. Dort gespeicherte Einstellungen liegen im Speicher des Panels
und gewinnen über die Datei. Nur zehn Einstellungen werden von dort übernommen: `enabled`, `defaultTtl`,
`staleWhileRevalidate`, `negativeTtl`, `skip.cookies`, `apcu.enabled`, `apcu.maxSize`, `memory.maxEntries`,
`memory.maxBytes`, `minifyHtml`. Die API liegt unter `/Q/api/cache`.

## Der eigene Cache einer Anwendung

Der Antwort-Cache überspringt Anfragen mit einem Sitzungs-Cookie, weil er Besucher nicht auseinanderhalten kann. Eine
Anwendung, die gerenderte Seiten pro Besucher oder Rolle behält, kann zuerst gefragt werden, im Serverprozess, sodass
ein Treffer nie einen Worker weckt:

```json
{ "Q": { "web": { "appCache": {
    "file":  "/path/to/app/lib/PageCache.php",
    "class": "PageCache",
    "dir":   "/path/to/app/var/page-cache"
} } } }
```

- `class` benötigt ein statisches `fromDir($dir)` und ein `serve(array $request)`, das bei einem Treffer
  `[status, headers, body]` zurückgibt und sonst `null`.
- Die Anfrage trägt `scheme`, `host`, `uri`, `method`, `cookies`, `acceptEncoding`, `ifNoneMatch` und (seit 0.0.4.35)
  `headers` und `port`.
- Eine Anfrage mit einem Sitzungs-Cookie geht nur an den Cache der Anwendung. Jede andere Anfrage wird zuerst vom
  Antwort-Cache beantwortet und bei einem Fehltreffer vom Cache der Anwendung, und diese Antwort wird im Antwort-Cache
  behalten.
- Da der Antwort-Cache dann zuerst antwortet, richten Sie `Q.web.cache.generationFile` auf eine Datei, die jede
  Bereinigung der Anwendung neu schreibt (`exp:velocity` von Exponential verwendet die Statusdatei des HTTP-Caches).

## Grenzen und Fallstricke

- **Personalisierte Seiten nicht zwischenspeichern.** Alles mit einem Cookie, `Authorization`, `private` oder
  `no-store` wird übersprungen; sorgen Sie dafür, dass das in Ihren Templates so bleibt.
- **Aktualisierung über 0.0.4.29 hinweg**: Zwischengespeicherte Seiten werden unter der Kodierung abgelegt, in der sie
  gespeichert sind, sodass die erste Anfrage jeder Seite nach der Aktualisierung neu gerendert wird.
- **Aktualisierung auf 0.0.4.39**: Eine Installation, die sich auf den alten Standard verließ und nirgends `enabled`
  setzt, hört auf zu cachen; fügen Sie `{"Q":{"web":{"cache":{"enabled":true}}}}` hinzu.
- APCu unter der CLI benötigt `apc.enable_cli=1`; der Server weist beim Start darauf hin, wenn es aus ist.
- Die Speicherschicht spart bei großen Seiten etwa 12 Prozent CPU pro Treffer und bei kleinen nichts; messen Sie auf
  Ihrer eigenen Site.

Vollständige Schritte zur Aktualisierung: [Hinweise zur Aktualisierung der Velocity-Engine](../../bc/6.0/velocity-engine-upgrade-hinweise.md).

## Verwandte Seiten

- [Control Panel](velocity-control-panel.md) (Tab Cache), [statische Dateien und Bilder](velocity-statische-dateien-und-bilder.md) (Datei-Caching), [Scheduler](velocity-zeitplaner.md) (die Cache-Bereinigung), [Velocity: Exponential in einem Webserver mit persistenten Workern betreiben](velocity-server-mit-dauerhaften-workern.md)
- Spezifikationen: [Worker-Pool](../../spezifikationen/6.0/velocity-worker-pool.md), [Engine-Einstellungen](../../spezifikationen/6.0/velocity-engine-einstellungen.md), [HTTP/2 und Sicherheit](../../spezifikationen/6.0/velocity-http2-und-sicherheit.md) (was nie zwischengespeichert wird)
- Caching in Exponential: [HTTP-Cache](../../bc/6.0/http-caching.md), [der rollenbewusste HTTP-Cache](../../bc/6.0/httpcache.md), [der Antwort-Cache und wie man dem Netzwerk aus dem Weg geht](../../bc/6.0/antwort-cache-und-navigation.md), [Velocity-Engines](../../bc/6.0/velocity-engines.md)
- [Changelog: Exponential-Velocity-Engine](../../../changelogs/extensions/exponential-velocity.md)
- Chronik: [22. September](../../geschichte/velocity/2026-09b.md), [25. bis 30. September](../../geschichte/velocity/2026-09e.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
