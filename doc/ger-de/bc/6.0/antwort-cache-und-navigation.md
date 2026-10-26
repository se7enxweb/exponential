# Der Antwort-Cache, und dem Netzwerk aus dem Weg gehen

Lesen Sie diese Seite, wenn Sie Exponential unter dem Server Velocity (Qbix) betreiben und wissen möchten, was sein
Antwort-Cache jetzt für einen Besucher tut: was sich in 6.0.15 geändert hat, welche Einstellungen ihn steuern und die
Messungen hinter jeder Änderung. Jede Änderung ist mit ihrer Einstellung aufgeführt, sodass Sie jede einzelne
zurücknehmen können.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Gecachte Seiten beantworten bedingte Anfragen (`304`) mit einem inhaltsbasierten `ETag`; jede Antwort hat `Date`; stale-while-revalidate; gecachte 404er; HTML-Minifizierung; der Cache wird vor dem Routing geprüft; ein größerer Listen-Backlog; Pfade von Service Workern werden immer neu validiert; ein Navigations-Cache im Browser (`/index.js`). |
| Wer betroffen ist | Installationen, die Velocity betreiben. Apache und PHP-FPM sind nicht betroffen. |
| Wie Sie es prüfen | `./console exp:velocity config list` zeigt die wirksamen Einstellungen; zweimal `curl -sI https://your-host/` zeigt `X-Cache: HIT`. |
| Wie Sie etwas zurücknehmen | Setzen Sie den Schlüssel aus der Tabelle unten in `settings/override/velocity.ini.append.php` auf seinen Wert für das „alte Verhalten“ und starten Sie Velocity neu. |

## Die Einstellungen

Datei `settings/velocity.ini`, Block `[CacheSettings]`, Geltungsbereich: Installation. Die älteren Namen in
`[ServerSettings]` funktionieren weiter; ein Wert in `[CacheSettings]` hat Vorrang.

| Schlüssel | Ausgelieferter Wert | Altes Verhalten | Früherer Name in `[ServerSettings]` |
|---|---|---|---|
| `StaleWhileRevalidate` | `60` (Sekunden) | `0` | `CacheStaleWhileRevalidate` |
| `NotFoundSeconds` | `0` (Sekunden; bis 6.0.15 war es `60`) | `0` | `CacheNotFoundSeconds` |
| `HonorRequestNoCache` | `enabled` | (keiner: no-cache wurde aus dem Cache beantwortet) | |
| `MinifyHtml` | `enabled` | `disabled` | `MinifyCachedHtml` |
| `MiddleOutCompression` | `disabled` | `disabled` | `MiddleOutCompression` |

Bedingte Anfragen, `ETag`, `Date`, die Reihenfolge des Routings, der Backlog und die neu validierten Pfade haben keinen
Schalter: Es sind Korrekturen am Server.

## Die zwei Zahlen, zwischen denen alles liegt

```
cache hit      0.36 ms
full render  1382 ms
```

Fast alles Folgende handelt davon, Anfragen auf der linken Seite zu halten.

## Hinzugefügt: bedingte Anfragen für gecachte Seiten

Ein wiederkehrender Browser sendet die Validatoren, die er hält. Gelten sie noch, ist die Antwort `304` ganz ohne Body.

Statische Dateien beantworteten bedingte Anfragen von Anfang an; gecachte Seiten nie. Ein Browser, der die Seite
bereits hatte, sagte das in der Anfrage und bekam trotzdem 76 KB (9,7 KB komprimiert) geschickt.

```
before   200, 9.7 KB
after    304, 202 bytes of headers, no body
```

## Hinzugefügt: ein ETag, das aus der Seite abgeleitet ist, nicht aus der Uhr

Ein Eintrag trug nur `Last-Modified`, das festhielt, wann der *Eintrag gespeichert wurde*. Sein Neuaufbau verschob den
Zeitstempel, selbst wenn das HTML byte-genau gleich war, sodass das nächste Neuladen neu validierte, scheiterte und das
ganze Dokument übertrug. Eine Seite, die niemand bearbeitet hatte, kostete einmal pro Lebensdauer den vollen Preis,
immer wieder.

```
rebuild 1  etag="ae368d…-gzip"  last-modified=20:48:29
rebuild 2  etag="ae368d…-gzip"  last-modified=20:48:30
rebuild 3  etag="ae368d…-gzip"  last-modified=20:48:31
```

Das Tag ist ein Hash des Body, wie die Anwendung ihn erzeugt hat, vor der Komprimierung: gzip ist über Versionen
hinweg nicht byte-stabil, und ein Hash seiner Ausgabe würde für eine unveränderte Seite einen neuen Validator
erfinden. Die Formen identity und gzip erhalten verschiedene Tags, weil RFC 9110 einen Validator auf die gewählte
Repräsentation bezieht.

## Hinzugefügt: `Date` in jeder Antwort

Es fehlte bei einem 200 und bei einem 304. RFC 9110 verlangt es, und RFC 9111 berechnet das Alter einer Antwort
danach. Ohne es liest ein Client, dessen Uhr etwas vorgeht, das absolute `Expires` daneben als bereits vergangen und
validiert jede Navigation neu.

## Hinzugefügt: stale-while-revalidate (`StaleWhileRevalidate`)

Ein Eintrag läuft zu einem Zeitpunkt ab, nicht allmählich, daher verfehlte jede laufende Anfrage im selben Augenblick
den Cache, und jede renderte die Seite.

```
                      before                    after
8 requests at an      8 rendered, 1815-2131ms   1 rendered
expiry                each                      7 served at ~62ms
```

Genau eine Anfrage rendert; die übrigen erhalten die bereits vorhandene Kopie, gekennzeichnet mit `X-Cache: STALE` und
einem ehrlichen `Age`. Der Anspruch auf das Rendern ist ein Verzeichnis, weil `mkdir` atomar ist und kein
Aufräumprotokoll braucht, um korrekt zu sein. Ein Anspruch, der älter als `revalidateLockSeconds` ist, darf übernommen
werden, sodass ein Worker, der mitten im Rendern stirbt, keine Seite einfrieren kann.

`0` ergibt genau das alte Verhalten.

## Hinzugefügt: negatives Caching (`NotFoundSeconds`)

`put()` lehnte jeden Status außer 200 ab, daher durchlief jedes 404 jedes Mal den ganzen Weg von Routing und Rendering.
Gemessen über 278 öffentliche URLs: je 220 bis 340 ms, keine gecacht.

```
first 404   758 ms
second 404   27 ms   X-Cache: HIT
```

Nur 404 und 410 werden behalten. Ein 500 wird nie gemerkt (die nächste Anfrage ist genau die, bei der es
funktionieren könnte), und eine Weiterleitung wird nie als negativ gemerkt. Die Lebensdauer ist die des Servers, nicht
die der Antwort, weil ein Not-found das `Cache-Control` trägt, das die Anwendung zufällig gesetzt hat.

`0` ergibt das alte Verhalten.

**Seit 6.0.15 mit `0` ausgeliefert.** Ein aus dem Cache beantwortetes 404 erreicht nie PHP, daher sahen 404.log und
der Anfrage-Klassifizierer (security.log) ein wiederholtes Not-found oder einen wiederholten Abtastversuch höchstens
einmal pro Minute, wo Apache jedes zeigt. Setzen Sie wieder einen Wert, wo die Renderkosten toter Links wichtiger sind,
als jede Anfrage zu sehen. Eine Anfrage, die der Klassifizierer markiert, wird unabhängig von ihrem Status mit
`Cache-Control: private, no-store` beantwortet und so in keinem Fall behalten.

## Hinzugefügt: `no-cache` in der Anfrage erreicht PHP (`HonorRequestNoCache`)

Eine Anfrage, die `Cache-Control: no-cache` sendet (oder `Pragma: no-cache` ohne `Cache-Control`), wird nicht aus dem
Cache des Servers beantwortet: Sie erreicht PHP, wie unter Apache, und die Antwort wird erneut gespeichert. Das
gewöhnliche Neuladen eines Browsers (`max-age=0`) wird weiterhin aus dem Cache beantwortet. Braucht
exponential-velocity mit `Q.web.cache.honorRequestNoCache`. `disabled` ergibt das alte Verhalten.

## Hinzugefügt: HTML-Minifizierung (`MinifyHtml`)

30,8 % der Startseite waren Leerraum, den die Template-Engine sendet, weil Templates so geschrieben werden, dass
Menschen sie lesen können.

```
decompressed   94,128 -> 69,101 bytes   26.6% smaller
gzipped         9,707 ->  9,707 bytes   unchanged
```

Die gzip-Größe bewegt sich nicht, wie erwartet: gzip behandelt wiederholten Leerraum bereits. Die Ersparnis liegt im
dekomprimierten Dokument: was der Browser parst, was ein Service Worker speichert, was im Speicher liegt. Sie entsteht
einmal, beim Speichern.

`<pre>`, `<textarea>`, `<script>`, `<style>` und `<code>` werden genau so zurückgegeben, wie sie geschrieben sind.
Leerraum wird dort wörtlich dargestellt, ändert, was ein Formular sendet, oder kann durch das automatische Einfügen von
Semikolons eine Anweisung vorzeitig beenden.

## Geändert: Der Cache wird vor dem Routing geprüft, nicht danach

`handleRequest()` erreichte den Cache erst **in Zeile 322**, nach dem Auflösen der Host-Konfiguration, einem
`realpath()`, einem `file_exists()` für das Favicon, den Prüfungen der mitgelieferten Assets und den Well-known-Handlern,
die ein Cache-Treffer dann alle verwarf.

```
                        CPU per request
                        before   after
cached page, 9.3 KB     3.80 ms  0.54 ms
cached 404              1.30 ms  0.22 ms
```

Ihn nach oben zu verschieben ist sicher, weil nichts darüber entschied, ob die Antwort ausgeliefert werden darf: Der
Schlüssel enthält den Host, und ein Eintrag existiert nur, weil eine frühere Anfrage jede Prüfung darunter bestanden
hat. Die ACME-Challenge läuft weiterhin zuerst.

## Geändert: der Listen-Backlog

`stream_socket_server` von PHP setzt den Backlog auf 32. Ein Ansturm darüber hinaus wird weder eingereiht noch schlägt
er fehl: Der Kernel verwirft das SYN, und der Client sendet eine Sekunde später erneut.

```
                  backlog 32              backlog 1024
concurrency  64    745 req/s  p99 1067 ms   2,703 req/s  p99 19.5 ms
concurrency 128    603 req/s  p99 1317 ms   2,773 req/s  p99 37.9 ms
```

Bei 64 gleichzeitigen Anfragen ließ der Durchsatz nicht nach; er brach auf ein Viertel ein, und das p99 wurde zu runden
tausend Millisekunden (der Timer für erneutes Senden), während der Server größtenteils untätig war.

## Hinzugefügt: Pfade, die immer neu validiert werden

Ein Service Worker installiert sich in einem Browser und entscheidet dann, womit jede spätere Navigation beantwortet
wird. Mit der jahrelangen statischen Lebensdauer ausgeliefert, lässt sich ein Fehler darin nicht zurückziehen: Die
Korrektur liegt auf dem Server, während Browser weiter die alte Kopie ausführen. Das `index.js` im Wurzelverzeichnis,
`sw.js`, `service-worker.js`, `*.webmanifest` und `manifest.json` werden jetzt immer neu validiert.

## Hinzugefügt: ein Navigations-Cache im Browser (`/index.js`)

Der Worker ist `/index.js`, der Exponential Service Workers Index, registriert mit dem Geltungsbereich `/`. Bis zum
2026-09-30 war es `/sw.js`. `/sw.js` bleibt als einzeiliges Skript, das `/index.js` lädt, sodass ein unter dem alten
Namen registrierter Browser denselben Code ausführt, bis die Website `/index.js` darüber registriert. **Behalten Sie
`/sw.js`, bis kein Browser die alte Registrierung mehr haben kann.**

Das Letzte zwischen einem Besucher und der Seite ist ein Netzwerk-Umlauf, und ein Umlauf lässt sich nicht verkürzen,
nur überspringen.

```
kernel RTT to a real browser connection   12.47 ms
server-side answer to the same request     0.36 ms
```

Daher wird die Seite aus dem eigenen Speicher des Browsers beantwortet und das Netzwerk danach gefragt. Regeln:

- nur Navigationen per GET vom selben Ursprung;
- nie eine Seite, die mit einem Sitzungs-Cookie abgerufen wurde;
- nie etwas anderes als 200;
- nie `/admin`, `/user`, `/explayouts_ui` oder `/api`.

Erscheint ein Sitzungs-Cookie, meldet das Skript der Seite den Worker ab und leert seinen Cache, statt ihn einem
Besucher die Seite eines anderen ausliefern zu lassen.

**Notschalter:** Liefern Sie `index.js` als `self.registration.unregister()` aus. Jeder Browser entfernt ihn bei seiner
nächsten Update-Prüfung innerhalb von 24 Stunden, weil `index.js` `no-cache` ist.

Der Worker liefert zuerst die gespeicherte Kopie aus und aktualisiert sie im Hintergrund, sodass eine veröffentlichte
Änderung bei der **zweiten** Ansicht erscheint, nicht bei der ersten. Die Cache-Version ist `exp-nav-v5` (siehe
[Verhaltensänderungen, 16. bis 30. September 2026](verhaltensaenderungen-2026-09b.md)).

Solange die Website mit ihrer Wartungsseite antwortet (während einer Installation oder eines Wartungsfensters: ein 503
mit `X-Exp-Maintenance` und `Retry-After`), zeigt der Worker keine gespeicherte Seite. Die erste solche Antwort löscht
alle Seiten, die er hält, und führt den Tab, dem gerade eine gespeicherte Seite gezeigt wurde, zur Wartungsseite; bis
der Server wieder mit einer Seite antwortet, geht jede Navigation ans Netz. `v4` behielt seine Kopien bei einem 503 und
zeigte die Website während einer ganzen Installation bei jedem Neuladen. Die eigenen Anfragen des Workers werden beim
Server neu validiert (`cache: 'no-cache'`), damit der HTTP-Cache des Browsers sie nicht für die Dauer des `max-age`
einer Seite beantwortet.

Ein Browser ohne den Worker hält eine anonyme Seite weiterhin für ihr `max-age` (`[HTTPHeaderSettings]`,
`public, max-age=300` auf vielen Websites) und zeigt sie bei einem Link oder einer Eingabe in der Adressleiste
innerhalb dieser Zeit, ohne den Server zu fragen; ein Neuladen fragt und erhält die Wartungsseite. Halten Sie dieses
max-age auf einer Website, die neu installiert wird, kurz.

## Hinzugefügt: Middle-Out, Komprimierung mit gemeinsamem Wörterbuch (standardmäßig aus)

Gemessen an den 273 gecachten Seiten einer Installation: 1.626.195 Bytes als einfaches gzip gegenüber 1.290.140 mit
einem Wörterbuch von 32 KB, 20,7 % kleiner, 273 von 273 exakt hin und zurück.

Sie ist aus, und der Grund ist wichtig, bevor Sie sie einschalten: Der Cache hält bereits mit gzip komprimierte Bodys,
und ein Wörterbuch kann gzip-Ausgabe nicht verkleinern. Gegen diesen Cache eingeschaltet, wurden 271 von 272 Einträgen
ohnehin unkomprimiert gespeichert. Die ganze Geschichte, und wann sich das Einschalten lohnt, steht in
[Middle-Out-Kompression](middle-out-kompression.md).

## Was das zusammen ergibt

```
                              before        after
cached page, server side      0.36 ms       0.36 ms
CPU per cached request        3.80 ms       0.54 ms
peak throughput             2,961 req/s   8,941 req/s
p99 at 128 concurrent       1,317 ms       37.9 ms
reload of a held page         9.7 KB        202 bytes
expiry, 8 concurrent        8 renders     1 render
a cached 404                  758 ms        27 ms
document, decompressed       94,128 B      69,101 B
```

Die Einstellungen, die diese Konfiguration nachbilden, stehen in `settings/velocity.ini`; lesen Sie sie mit
`./console exp:velocity config list`.

## Verwandte Seiten

- [Antwort-Cache von Velocity](../../funktionen/6.0/velocity-antwort-cache.md)
- [Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md) und [Velocity-Engines](velocity-engines.md)
- [Middle-Out-Kompression](middle-out-kompression.md) und [HTTP-Caching für anonyme Besucher](http-caching.md)
- [Cache-Leerungen, die Verzeichnisse beiseiteschieben](../../funktionen/6.0/cache-leeren-durch-umbenennen.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](verhaltensaenderungen-2026-09b.md)
- [Chronik, 16. bis 30. September 2026](../../geschichte/2026/2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
