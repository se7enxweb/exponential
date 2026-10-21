# Statische Dateien und Bildverarbeitung in Velocity

Diese Seite ist für Administratoren, die eine Website mit Exponential Velocity ausliefern und wissen möchten, wie Dateien
und Bilder ausgeliefert, zwischengespeichert und skaliert werden. Velocity beantwortet eine Anfrage nach einer Datei im
Document Root selbst, ohne PHP: Es setzt den Typ, prüft mit `ETag` und `Last-Modified`, komprimiert Text, sendet Bereiche
und konvertiert oder skaliert Bilder auf Anfrage. Gilt für Exponential Velocity 0.0.4.x. Referenz:
[Engine-Einstellungen](../../spezifikationen/6.0/velocity-engine-einstellungen.md),
[HTTP/2 und Sicherheit](../../spezifikationen/6.0/velocity-http2-und-sicherheit.md).

Was Sie gewinnen:

- Statische Dateien wecken nie einen Worker. Sie werden im Serverprozess beantwortet, bei häufigem Zugriff aus einer
  Kopie im Speicher ([Worker-Pool](../../spezifikationen/6.0/velocity-worker-pool.md)).
- Hängen Sie `?w=400` an eine Bild-URL, und der Server skaliert, wählt AVIF oder WebP für Browser, die sie annehmen,
  berücksichtigt `Save-Data` und speichert das Ergebnis zwischen. Kein Bilddienst, keine Änderung am Template.
- Eine verirrte `.env`, `.sql` oder Sicherung im Document Root lässt sich nicht herunterladen: Nur Dateien mit einer
  erlaubten Endung werden so ausgeliefert, wie sie sind, und die optionalen Pfadlisten in
  [Sicherheit](../../spezifikationen/6.0/velocity-http2-und-sicherheit.md) grenzen das weiter ein.

## Ein Bild mit einer Anfrage skalieren

```
/photos/hero.jpg?w=400         400 pixels wide, height in proportion
/photos/hero.jpg?h=300         300 pixels tall
/photos/hero.jpg?w=400&h=300   exactly 400 by 300 (stretches; give one dimension to keep proportions)
/photos/hero.webp              made from hero.png when no hero.webp exists
```

Prüfen, dass eine URL durch die Bildverarbeitung geht:

```bash
curl -sI 'https://host/photos/hero.jpg?w=200'
```

Erwartet: `Vary: Accept` und `Cache-Control: public, max-age=31536000, immutable` in der Antwort.

Wie eine Anfrage behandelt wird:

1. Die Quelle ist die Datei an der URL oder, bei einer Konvertierung, die erste gleichnamige Datei in der Reihenfolge
   `.png`, `.jpg`, `.jpeg`, `.gif`, `.webp`, `.bmp`.
2. `w` und `h` werden zwischen 1 und 4096 gehalten und nie über der eigenen Größe der Quelle (kein Hochskalieren).
3. Das Ausgabeformat geht von der Endung der URL aus und wird dann AVIF, wenn das `Accept` des Browsers `image/avif`
   aufführt und GD von PHP es schreiben kann, sonst WebP, wenn es `image/webp` aufführt. Die Antwort trägt `Vary: Accept`.
4. `Save-Data: on` senkt die Qualität auf 50 und begrenzt eine Konvertierung ohne Größe auf 800 Pixel Breite; diese
   Varianten werden getrennt zwischengespeichert (`_sd`).
5. Ein zwischengespeichertes Ergebnis, das neuer als die Quelle ist, wird ausgeliefert; andernfalls wird es im Worker
   erzeugt, in den Cache geschrieben und ausgeliefert.

Transparenz bleibt für PNG, WebP, GIF und AVIF erhalten; für JPEG wird auf Weiß zusammengesetzt. Ein animiertes GIF wird
nur durchgereicht, wenn es unskaliert angefragt wird; jedes Skalieren oder Konvertieren ergibt das erste Bild.

Ergebnisse werden unter `files/Q/cached/images/` des Anwendungsverzeichnisses zwischengespeichert (neben dem Document
Root, wenn eigenständig), zum Beispiel `files/Q/cached/images/photos/hero/400x.avif`. Das Ersetzen der Quelle erzeugt bei
der nächsten Anfrage jede Variante neu.

**Wegen `immutable` behält ein Browser, der eine alte Version hat, sie ein Jahr lang.** Wenn Sie ein Bild an Ort und
Stelle ersetzen, ändern Sie seine URL (`hero.jpg?w=400&v=2`; zusätzliche Parameter ändern den Cache-Schlüssel des Servers
nicht).

Anforderungen: die Erweiterung GD von PHP (ohne sie wird das Original ausgeliefert), WebP-Unterstützung in GD für WebP und
`imageavif()` (PHP 8.1 mit libavif) für AVIF. Prüfen mit `php --ri gd`.

| Konfigurationsschlüssel | Standard | Bedeutung |
|---|---|---|
| `Q.images.quality` | `82` | Kodierqualität, 1 bis 100 (PNG bildet sie auf eine Kompressionsstufe ab) |
| `Q.images.cache.maxSize` | `268435456` | Bytes zwischengespeicherter Varianten, bevor die am längsten nicht genutzten entfernt werden (auf 75 Prozent der Grenze; geprüft nach je 50 erzeugten Bildern) |

## Dateien ausliefern

Eine Datei, deren Endung auf der erlaubten Liste steht, wird direkt von der Platte gesendet. Die Liste umfasst Text und
Daten (`html`, `htm`, `txt`, `md`, `json`, `xml`, `yaml`, `yml`, `csv`, `tsv`, `log`), Code (`css`, `js`, `mjs`, `map`,
`wasm`), Bilder (`png`, `gif`, `webp`, `jpg`, `jpeg`, `svg`, `bmp`, `ico`, `avif`), Schriften (`woff`, `woff2`, `ttf`,
`otf`), Medien (`mp3`, `wav`, `ogg`, `mp4`, `webm`), `pdf` und `zip`. `Q.webserver.extensions` ersetzt sie.

| Antwort-Header | Wert |
|---|---|
| `Content-Type` | aus der Endung |
| `ETag`, `Last-Modified` | aus Zeit und Größe der Datei; ein passendes `If-None-Match` oder `If-Modified-Since` erhält `304` und keinen Body |
| `Cache-Control` | `public, max-age=0, must-revalidate`, sofern `Q.web.static.maxAge` nicht gesetzt ist |
| `Accept-Ranges` | Bytebereiche werden berücksichtigt |

### Wie lange ein Browser eine Datei behalten darf

Standardmäßig behält ein Browser eine Datei, fragt aber vor jeder Wiederverwendung nach (ein günstiges `304`). Um
versionierte Ressourcen länger leben zu lassen:

```json
{ "Q": { "web": { "static": { "maxAge": 86400 } } } }
```

Setzen Sie `maxAge` nur, wenn die URLs der Ressourcen eine Version oder einen Hash tragen; Service Worker und Manifest
bleiben von selbst frisch.

| Konfigurationsschlüssel | Standard | Bedeutung |
|---|---|---|
| `Q.web.static.maxAge` | `0` | Sekunden, gesendet als `Cache-Control: public, max-age=N`; `0` sendet `max-age=0, must-revalidate` |
| `Q.web.static.revalidate` | `sw.js`, `service-worker.js`, `*.webmanifest`, `manifest.json` | Muster für `fnmatch`, die **immer** neu geprüft werden, gleich, was `maxAge` ist, weil sie entscheiden, wie sich ein Client danach verhält; `[]` schaltet die Ausnahme ab |
| `Q.web.static.paths` | alle erlaubten Dateien | Optionale Muster, auf die ein Pfad passen muss, um so gesendet zu werden, wie er ist ([Sicherheit](../../spezifikationen/6.0/velocity-http2-und-sicherheit.md)) |
| `Q.webserver.fileCache.maxSize`, `.maxFile`, `.checkInterval` | `64MB`, `1MB`, `1` | Die Kopie häufig genutzter Dateien im Speicher: Gesamtgröße, größte Datei, Sekunden zwischen Prüfungen auf Änderungen |

## Kompression

Texttypen unter 5 MB werden mit gzip komprimiert, wenn `Accept-Encoding` es erlaubt (`q=0` zählt als abgelehnt), mit
`Vary: Accept-Encoding`. Um jede Datei einmal zu komprimieren und das Ergebnis zwischen Workern zu teilen, schalten Sie den
Cache für Vorkompression ein:

```json
{ "Q": { "webserver": { "precompress": { "enabled": true, "maxFiles": 1000, "minSize": 1024, "level": 6 } } } }
```

| Schlüssel (`Q.webserver.precompress`) | Standard | Bedeutung |
|---|---|---|
| `enabled` | `false` | Einschalten |
| `maxFiles` | `1000` | So viele zuletzt ausgelieferte Dateien behalten |
| `minSize` | `1024` | Dateien kleiner als dies nicht komprimieren (Bytes) |
| `level` | `6` | gzip-Stufe |
| `dir` | `qbixserver-precompress` im temporären Verzeichnis des Systems | Wo die `.gz`-Dateien liegen; setzen Sie es auf ein eigenes Verzeichnis für einen Dienst, der ein privates temporäres Verzeichnis hat |
| `fileMode`, `dirMode` | umask, `0755` | Berechtigungen (seit dem 23. September 2026 ist das Standardverzeichnis nicht mehr versehentlich für alle lesbar) |

## Verzeichnislisten und Galerien

Ein Verzeichnis ohne Indexdatei wird in einer responsiven Seite aufgelistet; Bildordner zeigen eine Galerie mit
Vorschaubildern. Das Document Root selbst lässt sich wie jedes andere Verzeichnis auflisten (21. September 2026). Wenn
Sie keine Listen wollen, geben Sie dem Verzeichnis eine Indexdatei oder liefern Sie über den Front-Controller aus, damit
nicht aufgeführte Pfade an die Anwendung gehen.

## Grenzen

- Eine Anfrage, die eine neue Größe erzeugt, bezahlt die Kodierkosten (AVIF ist am langsamsten). Zwei gleichzeitige erste
  Anfragen nach derselben Größe erzeugen sie beide.
- Keine Parameter `fit`, `crop`, `q` oder `dpr`: Andere Abfrageparameter werden ignoriert.
- Nicht geprüft: Ob die Bild-URLs einer Installation von Exponential (Bildvarianten unter `var/<site>/storage/images/`)
  diese Verarbeitung erreichen, hängt von ihrer Liste `Q.web.static.paths` ab. Prüfen Sie mit
  `curl -sI 'https://host/<image URL>?w=200'` und suchen Sie in der Antwort nach `Vary: Accept`.

## Verwandte Seiten

- [Velocity-Webserver](velocity-webserver.md), [Antwort-Cache](velocity-antwort-cache.md) (Seiten, nicht Dateien), [Pakete und Binärdateien](velocity-pakete-und-binaerdateien.md) (die Binärdateien bündeln GD), [uwebserver](velocity-uwebserver.md)
- [Generator für den statischen Cache](generator-fuer-den-statischen-cache.md)
- [Changelog: Engine Exponential Velocity](../../../changelogs/extensions/exponential-velocity.md)
- Chronik: [Juli 2026](../../../eng-us/history/velocity/2026-07.md) (Verzeichnislisten, Galerien, `Save-Data`), [22. September](../../../eng-us/history/velocity/2026-09b.md) (Lebensdauer statischer Dateien, komprimierte Speicherung), [23. September](../../../eng-us/history/velocity/2026-09c.md) (Berechtigungen des Caches)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
