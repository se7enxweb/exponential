# uwebserver

Diese Seite ist für Entwickler und Tester, die einen schnellen, sicheren Server für statische Dateien brauchen oder einen
Vergleichswert, an dem sie den PHP-Server messen. `uwebserver` ist ein kleiner, in C geschriebener Webserver, der mit der
Velocity-Engine ausgeliefert wird. Er beantwortet HTTP/1.1 und HTTPS aus einer Ereignisschleife pro Prozess, ganz ohne
PHP. Er ist **nicht** der Anwendungsserver (das sind `sbin/qbixserver.php` und das Phar, die PHP ausführen). Gilt für
Exponential Velocity 0.0.4.42 und neuer.

**Nichts in einer Website startet ihn.** Die Engine, ihre Pakete, das Container-Image, die Release-Workflows und die
Installation von Exponential führen ihn nie aus, sodass eine Aktualisierung für eine Website nichts ändert. Nur Menschen
und Tests starten ihn.

Er ist standardmäßig sicher: Er lauscht nur auf `127.0.0.1:8000`, weigert sich, ohne `--user` als root zu starten,
startet nie mit einer Option, die er nicht versteht, und hatte vier Sicherheitsprüfungen, einen gefuzzten
Anfrage-Parser und Tests, die mit den Sanitizern sauber sind (Release 0.0.4.42).

## In einer Minute bauen und ausführen

Er braucht einen C-Compiler und OpenSSL 3 (`libssl-dev` oder `openssl-devel`):

```bash
make -C native/uwebserver                         # builds sbin/uwebserver (hardened, -Werror)
sbin/uwebserver --root=./web                      # http://127.0.0.1:8000/
```

Prüfen Sie ihn aus einer zweiten Shell:

```bash
curl -sI http://127.0.0.1:8000/
```

Erwartet: `HTTP/1.1 200` mit `Server: uwebserver` (oder `403`, wenn `./web` keine `index.html` hat und
Verzeichnislisten aus sind).

Mehr:

```bash
sbin/uwebserver --help                            # every option, grouped
man docs/uwebserver.1                             # the manual page
make -C native/uwebserver install PREFIX=/usr/local
make -C native/uwebserver sanitize                # AddressSanitizer + UndefinedBehaviorSanitizer build for tests
make -C native/uwebserver fuzz                    # the request-parsing fuzzer
```

`sbin/uwebserver` in der committeten Form ist mit GCC 11.5 auf EL 9, x86-64, gebaut und gegen das OpenSSL 3 des Systems
gelinkt. Bauen Sie ihn auf einem anderen System wie oben neu. `bin/uwebserver` ist ein Weiterleiter am früheren Pfad,
der `sbin/uwebserver` ausführt.

## Die Kommandozeile

Im Stil von GNU: `--name=VALUE`, `--name VALUE`, die Schreibweisen mit einem Strich (`-root=/srv/www`), gebündelte
Kurzoptionen (`-p8081`), Formen mit `--no-` und `--`, um die Optionen zu beenden.

Die ganze Kommandozeile wird zuerst gelesen. Eine unbekannte Option, ein fehlender oder fehlerhafter Wert, widersprüchliche
Optionen oder ein verirrtes Wort werden auf der Standardfehlerausgabe mit
`Try 'uwebserver --help' for more information.` und Exit-Status 2 gemeldet, und nichts startet. `--help` endet mit 0.

- `--check` (`--test-config`) prüft alles, was ein Start braucht, und liefert nichts aus.
- `--print-config` zeigt die geltende Konfiguration.
- `--config=FILE` (oder `UWEBSERVER_CONFIG`) liest dieselben Namen aus einer Datei.

### Lauschen

| Option | Standard | Bedeutung |
|---|---|---|
| `-l`, `--listen=[ADDR:]PORT` | keiner | HTTP dort; wiederholbar |
| `-b`, `--bind=ADDR` | `127.0.0.1` | Adresse von `--port` und `--tls-port` |
| `-p`, `--port=PORT` | `8000` | HTTP auf ADDR:PORT |
| `--tls-listen=[ADDR:]PORT` | keiner | HTTPS dort; braucht `--cert` und `--key` |
| `--tls-port=PORT` | `8443` | HTTPS auf ADDR:PORT |
| `--reuse-port` | aus | Andere Server die Ports teilen lassen (`SO_REUSEPORT`); ohne es scheitert ein zweiter Start mit „Address already in use“ |
| `--backlog=N` | `511` | Verbindungen, die auf Annahme warten |

`ADDR` ist eine numerische IPv4-Adresse, eine IPv6-Adresse in Klammern, `*` für jede IPv4-Adresse oder `localhost`.
Hostnamen werden nie aufgelöst. Jede Schnittstelle ist ein ausdrückliches `--listen='*:PORT'` entfernt.

### Inhalte (`--root=DIR`)

| Option | Standard | Bedeutung |
|---|---|---|
| `-r`, `--root=DIR` | keiner | Das Document Root |
| `--index=NAMES` | `index.html` | Dateien, die für ein Verzeichnis antworten |
| `--directory-listing` | aus | Ein Verzeichnis ohne Indexdatei auflisten (sonst `403`) |
| `--hidden-files` | aus | Namen ausliefern, die mit einem Punkt beginnen (sonst `404`) |
| `--symlinks=inside\|never` | `inside` | Links folgen, die im Root bleiben, oder keinen |
| `--mime-types=FILE` | keiner | Weitere Typen im Format `mime.types` |
| `--default-type=TYPE` | `application/octet-stream` | Typ anderer Dateien |
| `--charset=NAME` | `utf-8` | An Texttypen angehängt (leer: keiner) |
| `--server-name=NAME`, `--server-header` | `uwebserver`, an | Wert des Headers `Server` und ob er gesendet wird |
| `--cache-control=VALUE` | keiner | `Cache-Control` von Dateien |
| `--etag` | an | `ETag`, `If-None-Match`, `If-Range` |
| `--gzip-static` | aus | `FILE.gz` an Clients ausliefern, die gzip annehmen |
| `-H`, `--header='NAME: VALUE'` | keiner | An jede Antwort angehängt; wiederholbar |

Was er ausliefert: GET und HEAD, `Last-Modified` und `ETag` mit bedingten Anfragen (`304`), einen Bytebereich (`206` oder
`416`), eine Weiterleitung auf den Schrägstrich eines Verzeichnisses und `X-Content-Type-Options: nosniff` bei jeder
Datei und jedem Fehler.

Wie er im Root bleibt: Ein Pfad darf kein `..`, kein kodiertes `/` und kein NUL enthalten (`400`); Punktnamen sind
`404`; Dateien werden mit `openat2(2)` und `RESOLVE_BENEATH` geöffnet, sodass der Kernel einen Pfad, Link oder eine
Race Condition, die aus dem Root herausführt, ablehnt (Links im Baum müssen relativ sein). Ohne `--root` gibt er die
eingebauten Antworten des Benchmarks.

### Grenzen, TLS, Protokollierung, Rechte

| Option | Standard | Bedeutung |
|---|---|---|
| `-w`, `--workers=N` | `1` (höchstens 256) | Worker-Prozesse; einer, der endet, wird ersetzt |
| `--max-connections=N` | `1024` | Pro Worker |
| `--max-requests=N` | `1000` | Anfragen auf einer Verbindung, dann `Connection: close` (0: keine Grenze) |
| `--max-header-size=SIZE` | `8k` | Darüber: `431` |
| `--max-uri-length=SIZE` | `4k` | Darüber: `414` |
| `--max-body-size=SIZE` | `1m` | Darüber: `413` |
| `--header-timeout=SECONDS` | `10` | Der ganze Kopf muss ankommen |
| `--read-timeout`, `--write-timeout` | `30`, `30` | Zwischen Lesevorgängen eines Anfrage-Bodys; ohne Fortschritt beim Senden einer Antwort |
| `--keepalive-timeout=SECONDS` | `5` | Leerlauf zwischen Anfragen |
| `--tls-handshake-timeout=SECONDS` | `10` | Ein TLS-Handshake |
| `--cert`, `--key`, `--chain` | keiner | PEM-Dateien; der Schlüssel wird abgelehnt, wenn ein anderer Benutzer ihn ändern darf |
| `--tls-min-version=1.2\|1.3` | `1.2` | Älteste angenommene Version |
| `--tls-ciphers=LIST`, `--tls-ciphersuites=LIST` | Standards von OpenSSL | Chiffren für TLS 1.2 und Suiten für TLS 1.3, wie OpenSSL sie auflistet |

TLS 1.2 bietet nur AEAD-Chiffren mit Forward Secrecy an; keine Sitzungs-Tickets, keine Kompression, keine Neuaushandlung.

Weitere Optionen: `--access-log` (common, combined oder json), `--error-log`, `--quiet`, `--verbose`, `--daemon`,
`--pid-file` (gesperrt), `--user`, `--group`, `--chroot`, `--allow-root`. **Als root liefert er nur mit `--user` (wird
abgegeben, nachdem die Ports offen sind) oder `--allow-root` aus.**

## Aktualisierung von 0.0.4.41 oder älter

- Er lauscht standardmäßig auf `127.0.0.1:8000`, nicht auf jeder Schnittstelle an 8080. Um die alte Adresse zu behalten:
  `--listen='*:8080'`. `--port=N` allein bindet jetzt `127.0.0.1:N`.
- Als root braucht er `--user` oder `--allow-root`.
- Eine unbekannte Option startet nichts (Exit 2). `--cert` ohne `--key` ist ein Fehler, kein einfaches HTTP.
- Der Header `Server` ist `uwebserver` (ohne Version); die Startzeile geht auf die Standardfehlerausgabe. `/health` behält
  seine Schlüssel.
- Entfernt: die erzeugten Datensatztypen und die U-Laufzeit, die einkompiliert, aber nie aufgerufen wurden, und die
  ungenutzten Header `u_runtime.h`, `u_merkle_cache.h`.

## Grenzen

- Nur HTTP/1.1 (ALPN `http/1.1`); kein PHP.
- Linux mit OpenSSL 3. Die Eingrenzung mit `openat2` braucht Kernel 5.6 oder neuer (davor wird jeder Bestandteil mit
  `O_NOFOLLOW` geöffnet, und keinem Link wird gefolgt).

## Verwandte Seiten

- [Pakete und Binärdateien](velocity-pakete-und-binaerdateien.md), [Velocity-Webserver](velocity-webserver.md) (der Anwendungsserver), [statische Dateien und Bilder](velocity-statische-dateien-und-bilder.md)
- Spezifikationen: [HTTP/2 und Sicherheit](../../spezifikationen/6.0/velocity-http2-und-sicherheit.md), [Engine-Einstellungen](../../spezifikationen/6.0/velocity-engine-einstellungen.md) (Programme)
- Aktualisierung: [Hinweise zur Aktualisierung der Velocity-Engine](../../bc/6.0/velocity-engine-upgrade-hinweise.md) (die Standards von `0.0.4.42`)
- [Changelog: Engine Exponential Velocity](../../../changelogs/extensions/exponential-velocity.md)
- Chronik: [August 2026](../../geschichte/velocity/2026-08.md) (erster Benchmark-Server in C), [25. bis 30. September](../../geschichte/velocity/2026-09e.md) (der Neubau)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
