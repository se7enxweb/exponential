# Spezifikation: Velocity – HTTP/2, Anfrageverarbeitung und Sicherheit

Diese Seite ist die Referenz dafür, was der Webserver Velocity annimmt, was er ablehnt und warum: HTTP/2, die
Rahmung von HTTP/1.1-Anfragen, Body-Grenzen, die Grenze des Document Root, Antwort-Header und die
Administrationsoberfläche. Lesen Sie sie, wenn Sie Velocity vor einer öffentlichen Site betreiben, seine
Sicherheits-Header setzen oder einen Fork pflegen und die Prüfungen übernehmen müssen. Sie gilt für Exponential
Velocity 0.0.4.42. Melden Sie Probleme dieser Art vertraulich an die Maintainer statt in einem öffentlichen Issue.

## Kurz gesagt

- HTTP/2 und HTTP/1.1 wenden dieselben Regeln an: dieselben gesperrten Pfade, dieselbe Body-Grenze, dasselbe Cache- und Skriptverhalten.
- Fehlerhafte oder mehrdeutige Anfragen werden abgelehnt (`400`, `413`, HTTP/2 `PROTOCOL_ERROR` / `FRAME_SIZE_ERROR`).
- Nichts außerhalb des Document Root wird ausgeliefert oder ausgeführt, auch nicht über einen symbolischen Link.
- Sicherheits-Header auf statischen Dateien müssen ausdrücklich eingeschaltet werden: Setzen Sie `Q.webserver.headers`, `headersOnScripts` und `hsts`.

## Einstellungen auf dieser Seite

| Einstellung | Standard | Seit | Bedeutung |
|---|---|---|---|
| `Q.webserver.headers` | `{}` | 0.0.4.35 | Header, die jeder Antwort hinzugefügt werden, die der Server selbst erzeugt ([Details](#header-auf-jeder-antwort)) |
| `Q.webserver.headersOnScripts` | `false` | 0.0.4.35 | Fügt sie auch Skriptantworten hinzu, sofern das Skript diesen Header nicht gesendet hat |
| `Q.webserver.hsts` | keiner | 0.0.4.35 | `Strict-Transport-Security` über TLS |
| `Q.webserver.followSymlinks` | `false` | 0.0.4.25 | `true` stellt die Auslieferung über Links wieder her, die den Document Root verlassen |
| `Q.webserver.scripts` | keine (muss eingeschaltet werden) | — | Skripte, die über ihren Namen ausgeführt werden dürfen, zum Beispiel `["/index.php"]` |
| `Q.webserver.frontControllers` | keine (muss eingeschaltet werden) | — | Pfadmuster, die Skripten zugeordnet sind, zum Beispiel `{"^/api/": "index_rest.php"}` |
| `Q.web.static.paths` | keine (muss eingeschaltet werden) | — | Muster, auf die eine Datei passen muss, damit sie unverändert gesendet wird |
| `Q.webserver.timeout.read` | 30 s | 0.0.4.38 | Sekunden, die für den Kopf einer Anfrage ab ihrem ersten Byte erlaubt sind |
| `Q.webserver.timeout.linger` | 5 s | 0.0.4.38 | Stille, nach der das Leerlesen eines zu großen Bodys aufhört |
| `Q.webserver.timeout.lingerTotal` | 30 s | 0.0.4.38 | Längste gesamte Leselaufzeit für einen zu großen Body |
| `post_max_size` (PHP) | Wert von PHP | 0.0.4.38 | Body-Grenze für beide Protokolle; `0` bedeutet keine Grenze |

Die Schlüssel stehen in der Velocity-Konfiguration (JSON); wie Exponential sie schreibt, ist in
[Velocity-Engine-Einstellungen](velocity-engine-einstellungen.md) beschrieben.

## HTTP/2

HTTP/2 wird auf dem TLS-Listener ausgehandelt (ALPN) und vom selben Worker-Pool beantwortet wie HTTP/1.1. Es wurde
am 22. September 2026 in fünf Schritten gebaut: HPACK (geprüft gegen die eigenen Testvektoren der Spezifikation),
Frames und die Zustandsmaschine der Verbindung, die Aushandlung auf dem echten Listener, eine Seite statt eines leeren
Fensters für URLs, die der Server nicht ausliefert, und Body-Komprimierung mit Flusskontrolle. Jeder Browser handelt
es aus; `curl --http1.1` zeigt, was HTTP/1.1-Clients sehen.

### Was HTTP/2 garantiert

- **Dieselben Regeln wie HTTP/1.1.** Die Liste gesperrter Pfade, die Positivliste der Dateiendungen, die Eingrenzung
  symbolischer Links und die eigenen `/Q/`-Ansichten des Servers gelten für beide Protokolle. Bis 0.0.4.25 lieferte
  HTTP/2 Dateien aus, die HTTP/1.1 ablehnte, darunter eine Konfigurationsdatei der Site und `/.git/config`, weil die
  Listen nur auf einem Weg geprüft wurden. Die HTTP/2-Route liegt jetzt hinter jeder Ablehnung des HTTP/1.1-Wegs;
  `tests/unit-http2-route-order.php` prüft die Reihenfolge.
- **Begrenzte Ressourcen** (0.0.4.9). Jede Ressource, die eine Gegenstelle den Server festhalten lassen kann, ist
  begrenzt: gleichzeitige Streams, Größe des Header-Blocks, dekodierte Header-Größe (HPACK-Verstärkung), gepufferte
  DATA, Flusskontrollfenster. Ein Header-Block, der über seine eigenen Längen falsche Angaben macht, wird abgelehnt.
  Streams, die bei vielen Neuladevorgängen abgebrochen werden, gelten nicht als Rapid-Reset-Angriff. Jedes `GOAWAY`
  protokolliert, warum es gesendet wurde.
- **Dieselbe Body-Grenze wie HTTP/1.1** (0.0.4.38). Es gilt `post_max_size`. Eine `content-length` über der Grenze
  wird auf ihrem eigenen Stream mit `413` beantwortet, bevor DATA aufbewahrt werden. Ein Body ohne diese Angabe wird
  bei dem DATA-Frame abgelehnt, der ihn über die Grenze bringt. Der Stream wird mit `NO_ERROR` zurückgesetzt, damit
  der Client aufhört zu senden; andere Streams laufen weiter.
- **Dasselbe Verhalten für Skripte.** Cookies, die ein Skript setzt, `SERVER_NAME` ohne den Port, `HTTPS` und
  `REQUEST_SCHEME`, der komprimierte Body, der Antwort-Cache (HTTP/2 verwendet ihn, sodass Seiten nicht von Grund auf
  gerendert werden) und die Zähler des Dashboards funktionieren alle über HTTP/2.

### Frames, die abgelehnt werden

Geprüft in `handle()` vor der Verzweigung nach dem Frame-Typ (RFC 9113):

| Abschnitt | Frame | Antwort |
|---|---|---|
| 4.2 | Größer als die angekündigte `SETTINGS_MAX_FRAME_SIZE` | `FRAME_SIZE_ERROR` |
| 6.1 | `DATA` auf Stream 0 | `PROTOCOL_ERROR` |
| 6.2 | `HEADERS` auf Stream 0 | `PROTOCOL_ERROR` |
| 5.1.1 | Eine gerade Stream-ID von einem Client | `PROTOCOL_ERROR` |
| 6.5 | `SETTINGS`-Länge kein Vielfaches von 6; `ACK` mit Nutzdaten | `FRAME_SIZE_ERROR` |
| 6.7 | `PING`, das nicht 8 Oktette lang ist | `FRAME_SIZE_ERROR` |
| 6.9 | `WINDOW_UPDATE` mit 0 | `PROTOCOL_ERROR` |
| 6.4 | `RST_STREAM`, das nicht 4 Oktette lang ist | `FRAME_SIZE_ERROR` |
| 6.8 | `GOAWAY` kürzer als 8 Oktette | `FRAME_SIZE_ERROR` |

Unbekannte Frame-Typen werden angenommen und verworfen; laut RFC 9113 Abschnitt 4.1 wird das Protokoll auf diese Weise erweitert.

Tests, ausgeführt aus dem Engine-Verzeichnis: `php tests/unit-http2-protocol-errors.php`, `tests/unit-http2-route-order.php`,
`tests/http2-request-body-limit.php`.

## Anfrageverarbeitung bei HTTP/1.1

| Regel | Verhalten | Seit |
|---|---|---|
| Sowohl `Content-Length` als auch `Transfer-Encoding` oder zwei verschiedene `Content-Length`-Werte | `400`. RFC 9112 Abschnitt 6.1: Die Ablehnung verhindert Request Smuggling | 0.0.4.25 |
| Reine LF-Zeilenenden | Abgelehnt (sie belegten außerdem einen Verbindungsplatz bis zum Lese-Timeout) | 0.0.4.25 |
| Rahmung nur aus dem Kopf gelesen | Ein Body, der den Text `Content-Length` enthält (ein hochgeladenes Log), kann die Rahmung nicht verändern | 0.0.4.38 |
| `Content-Length` beliebiger Größe | Bodys von 0 bis 47 und 58 bis 255 Byte wurden vor 0.0.4.27 als „keine Zahl“ abgelehnt; behoben | 0.0.4.27 |
| Body über `post_max_size` | `413` mit `Connection: close`. Der Rest wird gelesen und verworfen, bis der Client aufhört (`Q.webserver.timeout.linger` 5 s Stille, `lingerTotal` 30 s insgesamt), sodass der Client die `413` lesen kann statt eines Resets | 0.0.4.38 |
| `post_max_size = 0` | Keine Grenze, wie in PHP | 0.0.4.38 |
| Chunked-Bodys | Stück für Stück beim Eintreffen geparst: `413` bei dem Chunk, der die Grenze überschreitet, vollständig genau dort, wo der letzte Chunk und die Trailer enden, `400` bei einer fehlerhaften Größenangabe | 0.0.4.38 |
| Kopf einer Anfrage | `Q.webserver.timeout.read` Sekunden ab seinem ersten Byte; ein Body darf so lange dauern, wie er weiter eintrifft | 0.0.4.38 |
| Letzte Antwort vor `keepAlive.max` und jede `5xx` | `Connection: close` (vorher schlug etwa eine von tausend Anfragen fehl) | 0.0.4.29 |
| `header('WWW-Authenticate: ...')` | Antwortet mit `401`, sofern der Aufruf keinen Code nennt | 0.0.4.35 |
| Große binäre Uploads unterhalb der Grenze | Die Frame-Grenze der Worker folgt `post_max_size` (ein Upload von etwa 7,5 MB ergab früher `502`) | 0.0.4.38 |

## Nichts außerhalb des Document Root wird ausgeliefert oder ausgeführt

Eine Anfrage wird zu einer Datei aufgelöst. Liegt diese Datei, nachdem jeder symbolische Link verfolgt wurde, nicht
unterhalb des Document Root, lautet die Antwort `403`. Das gilt für statische Dateien und, noch wichtiger, für
PHP-Skripte: Ein Link mit dem Namen `*.php` machte früher aus der Möglichkeit, eine einzige Datei anzulegen, die
Möglichkeit, Code von überall dort auszuführen, wo der Benutzer des Servers lesen konnte. Das wurde in 0.0.4.25 an
allen vier Dispatch-Stellen behoben. Drei von vier zu schließen genügte nicht, weil der Dispatch mit einem
Worker-Pool `handlePhp()` auf einem anderen Weg erreicht.

`Q.webserver.followSymlinks` (Standard `false`) stellt das ältere Verhalten für Installationen wieder her, die
bewusst über Links ausliefern, die den Root verlassen. Für Exponential siehe
[Velocity-Engines](../../bc/6.0/velocity-engines.md), Abschnitt „Symbolic links out of the document root“.

### Welche Skripte ausgeführt und welche Dateien unverändert gesendet werden

Drei Einstellungen, die eingeschaltet werden müssen, entscheiden das: `Q.webserver.scripts` (Skripte, die über ihren
Namen ausgeführt werden dürfen), `Q.webserver.frontControllers` (Pfadmuster, die Skripten zugeordnet sind) und
`Q.web.static.paths` (Muster, auf die eine Datei passen muss, damit sie unverändert gesendet wird). Alles andere geht
an den Front-Controller, so wie die `.htaccess` einer Anwendung es dorthin schicken würde. Ein mitgeliefertes Werkzeug
oder ein Kommandozeilenskript wird also nicht schon deshalb ausgeführt, weil jemand es anfordert, und geschützte
Uploads bleiben hinter der Download-Ansicht der Anwendung. Der Antwort-Cache beginnt eine neue Generation, wenn sich
die Listen ändern. Die Voreinstellung `exponential` bringt die eigenen Listen von Exponential mit, wenn die
Konfiguration keine nennt.

## Header auf jeder Antwort

Eine statische Datei erreicht die Anwendung nie, deshalb trüge sie hinter Velocity allein keinen der
Sicherheits-Header, die ein Frontend sendet. Drei Einstellungen beheben das (0.0.4.35; ohne sie ändert sich nichts).
Beispiel:

```json
{ "Q": { "webserver": {
    "headers": {
        "X-Content-Type-Options": "nosniff",
        "X-Frame-Options": "SAMEORIGIN",
        "Referrer-Policy": "strict-origin-when-cross-origin",
        "Permissions-Policy": "camera=(), microphone=(), payment=(), usb=()"
    },
    "headersOnScripts": true,
    "hsts": { "maxAge": 300 }
} } }
```

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `Q.webserver.headers` | `{}` | Name auf Wert. Wird jeder Antwort hinzugefügt, die der Server selbst erzeugt: statische Dateien über HTTP/1.1 und HTTP/2 (einschließlich der Kopie im Arbeitsspeicher), `304`-Antworten, Bildvarianten, seine eigenen Fehlerseiten und Weiterleitungen |
| `Q.webserver.headersOnScripts` | `false` | Fügt sie auch der Antwort eines Skripts hinzu, jeweils nur dort, wo das Skript diesen Header nicht gesendet hat (ein eigenes `X-Frame-Options: DENY` einer Anwendung bleibt `DENY`) |
| `Q.webserver.hsts` | keiner | `Strict-Transport-Security` auf jeder Antwort über TLS (einschließlich Skriptantworten), nie über reines HTTP. Eine Zahl ist das max-age; ein Objekt nimmt `maxAge`, `includeSubDomains`, `preload`; eine Zeichenkette wird unverändert gesendet. Ein eigener HSTS-Eintrag einer Domain hat Vorrang |

Beachten Sie das max-age von HSTS: Ein Browser, der es gesehen hat, lehnt für diese Dauer reines HTTP zu diesem
Hostnamen ab, und zwar auf jedem Port. Beginnen Sie klein (300 Sekunden, wie oben).

Zur Prüfung fordern Sie eine statische Datei an und lesen die Header:

```bash
curl -sI https://<your-host>/favicon.ico | grep -iE 'x-content-type|x-frame|referrer|permissions|strict-transport'
```

Erwartet: eine Zeile für jeden Header, den Sie konfiguriert haben.

Sicherheitsregeln:

- Ein bereits vorhandener Header wird, gleich in welcher Groß- und Kleinschreibung, nie ersetzt oder doppelt gesendet.
- Namen, die keine HTTP-Token sind, Werte, die CR, LF oder NUL enthalten, und die Rahmungs-Header (`Content-Length`,
  `Content-Type`, `Connection`, `Transfer-Encoding`, `Date`, ...) werden ignoriert, sodass ein Tippfehler keinen
  Header in jede Antwort einschleusen kann.
- Jede Antwort läuft durch einen einzigen Serialisierer, sodass ein Header-Wert nie eigene Header schreiben kann
  (0.0.4.25; vorher gab es die Schutzprüfung an einer der sieben Stellen, die Header schrieben).

## Die Administrationsoberfläche

- Das Control Panel, `/Q/api/` und der Cluster-Beitritt waren bis 0.0.4.27 für jeden offen, der den Port erreichen
  konnte. Sie folgen jetzt der Zugriffsregel in [Control Panel](../../funktionen/6.0/velocity-control-panel.md).
- Log-Injektion über eine Anfragezeile, Dashboard-Injektion über den Host-Header, Personalisierung im Antwort-Cache
  und die Verstärkung der dekodierten Größe bei HPACK wurden im selben Audit geschlossen.
- Hostnamen werden streng geprüft, bevor sie einen Zertifikats-Client, einen Resolver oder ein Log erreichen (ein
  Name, der mit einem Zeilenumbruch oder einem Bindestrich endete, kam früher durch).
- Passwörter für das Panel sind bcrypt, werden gegen Regeln geprüft und durch eine Sperre geschützt. Der
  Zugangsdatenspeicher muss eine Eigentümerregel erfüllen, sonst wird das Panel gesperrt.
- Der Antwort-Cache liefert nie eine personalisierte Seite aus. Er überspringt Anfragen, die die Sitzungs-Cookie-Namen
  aus `skip.cookies` tragen, verglichen nach Präfix. Der Vergleich verlangte früher, dass auf den Namen unmittelbar
  `=` folgt, und verfehlte das Sitzungs-Cookie von Exponential, das nach dem Namen einen Digest trägt; behoben in 0.0.4.26.
- Worker geben die Root-Rechte ab ([Worker-Pool](velocity-worker-pool.md)).
- Befehle der Q-Shell erhalten keinen der Sockets und keine Umgebung des Servers ([Q-Shell](../../funktionen/6.0/velocity-q-shell.md)).
- Cache-Dateien liegen standardmäßig in einem Verzeichnis, das nicht für alle lesbar ist (0.0.4.15), mit
  konfigurierbarem `fileMode` und `dirMode`.

## Tests, die das absichern

| Test | Was er abdeckt |
|---|---|
| `tests/unit-malformed-requests.php` | 21 Fälle über einen rohen Socket, weil `curl` die meisten davon nicht sendet |
| `tests/unit-http2-protocol-errors.php` | Die abgelehnten Frames |
| `tests/unit-http2-route-order.php` | HTTP/2-Route hinter jeder HTTP/1.1-Ablehnung |
| `tests/unit-response-headers.php` | Jede Art von Antwort über HTTP/1.1, HTTP/1.1 mit TLS und HTTP/2 |
| `tests/unit-request-body-limit.php` | Body-Grenzen (22 von 39 Fällen schlagen gegen v0.0.4.37 fehl) |
| `tests/http2-request-body-limit.php` | Body-Grenze über HTTP/2 |

## Hinweise für alle, die einen Fork pflegen

Zwei der obigen Korrekturen wurden gefunden, indem der Server betrieben wurde, nicht indem man ihn las: der Weg zur
Ausführung über symbolische Links (drei von vier Dispatch-Stellen lassen sich schließen, während die vierte die Datei
weiterhin ausführt) und die Rahmungsfehler (`curl` weigert sich, die Anfragen zu senden, die sie aufdecken). Die
Eingrenzungsprüfung ist eine Funktion mit vier Aufrufstellen, und ihr Fehlen bleibt unsichtbar, bis jemand einen
Link anlegt. Übernehmen Sie sie.

## Verwandte Seiten

- Funktionen: [Webserver Velocity](../../funktionen/6.0/velocity-webserver.md), [Control Panel](../../funktionen/6.0/velocity-control-panel.md), [HTTPS und Zertifikate](../../funktionen/6.0/velocity-https-zertifikate.md), [Statische Dateien und Bilder](../../funktionen/6.0/velocity-statische-dateien-und-bilder.md), [uwebserver](../../funktionen/6.0/velocity-uwebserver.md) (seine eigene Härtung)
- Spezifikationen: [Worker-Pool](velocity-worker-pool.md), [Engine-Einstellungen](velocity-engine-einstellungen.md)
- Upgrade-Hinweise: [Upgrade-Hinweise zur Velocity-Engine](../../bc/6.0/velocity-engine-upgrade-hinweise.md) (Antwort-Header, Upload-Grenzen), [Velocity-Engines](../../bc/6.0/velocity-engines.md)
- Chronik: [22. September](../../geschichte/velocity/2026-09b.md) (HTTP/2), [23. September](../../geschichte/velocity/2026-09c.md) und [24. September](../../geschichte/velocity/2026-09d.md) (Sicherheitskorrekturen), [25. bis 30. September](../../geschichte/velocity/2026-09e.md) (Header, Body-Grenzen); [Velocity-Changelog](../../../changelogs/extensions/exponential-velocity.md)
