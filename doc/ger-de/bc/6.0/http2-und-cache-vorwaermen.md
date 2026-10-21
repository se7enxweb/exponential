# HTTP/2, und den Antwort-Cache gefüllt halten

Lesen Sie diese Seite, wenn Sie Exponential mit Velocity (dem Server mit persistenten Workern) ausliefern: Sie erklärt
die Unterstützung für HTTP/2, den Cache-Vorwärmer `exp:warm`, die Grenzen, die eine HTTP/2-Verbindung schützen, wie Sie
eine Zahl von Workern wählen und was ein persistenter Worker zwischen Anfragen zurücksetzen muss. Zu jedem Teil gehören
die Messungen dahinter. Die Zahlen beschreiben eine Testinstallation und deren Templates; messen Sie Ihre eigene, bevor
Sie einstellen.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Velocity spricht HTTP/2 selbst (`velocity.ini [ServerSettings] HTTP2`, ausgeliefert als `enabled`), mit einer lesbaren Ersatzseite bei einem fehlgeschlagenen Stream. Neuer Befehl `exp:warm` (`bin/php/warm.php`) hält den Antwort-Cache gefüllt. Der Antwort-Cache wird beim eigenen Sitzungs-Cookie der Website übersprungen (`CacheSkipCookies`, aus `site.ini` abgeleitet). Ressourcengrenzen für HTTP/2 ab qbix-webserver 0.0.4.9. Optionales Vorwärm-Rendering im Elternprozess (`PreloadWarmup`, Standard `disabled`). |
| Wer betroffen ist | Benutzer von Velocity. Die Anmeldung über HTTP/2 braucht qbix-webserver 0.0.4.7 oder neuer; das Erneuern durch `exp:warm` braucht 0.0.4.6 oder neuer. Websites mit `SessionNameHandler=default` und eingeschaltetem Antwort-Cache brauchen die korrigierte Liste der Cookies (siehe [Der Antwort-Cache achtet in der Grundeinstellung auf das falsche Cookie](#der-antwort-cache-achtet-in-der-grundeinstellung-auf-das-falsche-cookie)). |
| Wie Sie es prüfen | `grep -n "^HTTP2\|^Workers\|^PreloadWarmup" settings/velocity.ini` und `php vendor/se7enxweb/exponential-velocity/tests/run-unit.php` |
| Wie Sie es beheben | Aktualisieren Sie das Engine-Paket; planen Sie `exp:warm` häufiger als das Cache-Fenster; setzen Sie `Workers` anhand einer Lastreihe; lassen Sie `PreloadWarmup` aus, sofern Sie keine vollständige Regressionsprüfung von Seiten und Assets durchgeführt haben. |

Das Engine-Paket liegt in `vendor/se7enxweb/exponential-velocity`; der ältere Pfad `vendor/se7enxweb/qbix-webserver`
zeigt auf dieselben Dateien.

## Die Kurzfassung

```
document, warm cache      67–88 ms
document, cold cache     ~800–1600 ms
TLS handshake             27–42 ms   (nginx on the same box: 25–31 ms)
```

Die Anwendung ist schnell, wenn die Antwort schon im Cache liegt, und langsam, wenn nicht, weil die Startseite 529
Datenbankabfragen kostet. Die Arbeit hat daher zwei Hälften: den Transport so gut wie möglich machen und dafür sorgen,
dass nie ein Besucher derjenige ist, der den Cache leer vorfindet.


## HTTP/2

Vom Server selbst gesprochen. Kein Proxy, keine Erweiterung, keine asynchrone Bibliothek:

- **TLS/ALPN** — `stream_socket_enable_crypto()` mit `alpn_protocols` im Stream-Kontext. Das ausgehandelte Protokoll
  wird aus `stream_get_meta_data()['crypto']['alpn_protocol']` gelesen.
- **Framing** — ein von Hand geschriebener Codec für neun Oktette.
- **HPACK** — von Grund auf geschrieben: Integer-Codec, statische Tabelle, dynamische Tabelle mit Verdrängung und die
  vollständige Huffman-Tabelle mit 257 Einträgen. Geprüft gegen die durchgerechneten Beispiele in RFC 7541, Anhang C,
  22 von 22.
- **Zustandsautomat** — Streams, beide Fenster der Flusskontrolle, CONTINUATION, Padding, GOAWAY.
- **Ereignisschleife** — die vorhandene Schleife `stream_select()` des Servers.

### Einschalten

```ini
; settings/velocity.ini
[ServerSettings]
HTTP2=enabled
HTTP2ErrorImage=
```

Die ausgelieferte `settings/velocity.ini` setzt `HTTP2=enabled`; setzen Sie `HTTP2=disabled`, um es auszuschalten.
Gehen Sie vorsichtig damit um: Das Protokoll wird während des TLS-Handshakes vereinbart, und ein Client fällt danach
nicht zurück, daher ist ein Fehler eine Seite, die nie ankommt, statt einer, die langsam ankommt.

### Warum es eine Ersatzseite gibt

Wegen genau dieser Eigenschaft. Ein Fehler im Framing, ein defekter Header-Block oder ein Throwable in einem Handler
würden sonst überhaupt nichts erzeugen — ein leeres Fenster und keinen Status oder Log, an den die Person kommt, die
darauf schaut. Daher wird überall dort, wo ein Stream noch offen ist, etwas darauf gesendet: eine lesbare Seite, die
nennt, was fehlgeschlagen ist, auf welchem Stream und zu welcher Zeit.

Sie ist konfigurierbar, da eine Fehlerseite die eine Seite ist, die sich nicht in der Vorschau ansehen lässt, bevor sie
gebraucht wird:

```
Q.web.http2.errorPage.template   a file used instead, given {title} {message}
                                 {detail} {image} {stream} {time}
Q.web.http2.errorPage.title      heading
Q.web.http2.errorPage.message    the sentence under it
Q.web.http2.errorPage.image      URL, data: URI, or a path to inline;
                                 "none" for no picture
Q.web.http2.errorPage.detail     false hides the technical line
```

`HTTP2ErrorImage` in `velocity.ini` setzt das Bild, ohne die eigene Konfiguration des Servers anzutasten. Die
Standardillustration wird inline gezeichnet und eingebettet, weil die Seite genau dann erscheint, wenn die Verbindung,
die sie trägt, unzuverlässig ist, und nicht davon abhängen darf, dass eine zweite Anfrage gelingt.

### Was HTTP/2 nicht bringt

Es lässt eine Seite nicht schneller rendern. Es macht die *Unterressourcen* der Seite günstiger: Diese Website
verweist auf 43, und über HTTP/1.1 holt ein Browser sie etwa sechs auf einmal in Wellen. Gemessen dauerte die
Auslieferung der Assets über HTTP/1.1 393–418 ms gegenüber 85–104 ms über HTTP/2.


## Den Cache gefüllt halten

Der Antwort-Cache ist im Server standardmäßig eingeschaltet und beachtet das eigene `Cache-Control` jeder Antwort. Wenn
anonyme Seiten `public, max-age=300` senden, bleibt eine Seite fünf Minuten im Cache, und dann wartet die nächste
Person, die fragt, auf ein vollständiges Rendern.

`exp:warm` macht aus dieser Person ein Skript.

```bash
php bin/php/console exp:warm              # every published page
php bin/php/console exp:warm --verbose    # name the ones that had to render
php bin/php/console exp:warm --limit=40
php bin/php/console exp:warm --json
```

Mit einem Timer, kürzer als das Cache-Fenster:

```
*/4 * * * * cd <root> && php bin/php/warm.php --allow-root-user >> var/log/cache-warm.log 2>&1
```

Gemessen auf der Testinstallation: 142 Seiten, 9,6 s aus dem kalten Zustand, **0,3 s, wenn schon alles warm ist**,
keine Fehlschläge.

### Drei Dinge, die es richtig machen muss, jedes auf die harte Tour gelernt

**Der Host-Header muss dem entsprechen, was ein Browser sendet, Port eingeschlossen.** Der Cache schlüsselt nach Host,
Pfad und Kodierung. Ein Browser, der `https://www.example.com:8080/site` anfragt, sendet `www.example.com:8080`; mit
`www.example.com` vorzuwärmen, füllt einen anderen Eintrag, den nie jemand liest. Der Vorwärmer meldet dann Erfolg,
während jeder Besucher weiterhin das Rendern bezahlt — gemessen 1036 ms für eine Seite, die der Cache in 3 ms hätte
beantworten können. Der Host wird jetzt aus `SiteURL` plus dem HTTPS-Port des Dienstes gebildet, wobei 443 weggelassen
wird, weil ein Browser es weglässt.

**Er muss echte URL-Aliase verwenden, nicht `path_identification_string`.** Letzteres ist ein interner Bezeichner —
`bold_agency`, `fit_healthy` —, und ihn anzufragen ergibt 404.

**Er muss im eigenen Teilbaum dieses Siteaccess bleiben.** Das ist eine Multisite: Die Inhaltswurzel enthält einen
Teilbaum pro Website, und die anderen antworten unter anderen Hosts. Von der Inhaltswurzel aus vorzuwärmen, fragt diesen
Host nach den Seiten einer anderen Website und erhält zu Recht 404. Die Wurzel wird aus `SiteSettings/IndexPage`
genommen.


## Was aus PHP heraus nicht erreichbar ist

Beides gegen diesen Server getestet, beides über die Stream-Schicht von PHP nicht verfügbar:

- **Wiederaufnahme von TLS-Sitzungen.** Es wird nie ein Sitzungs-Ticket ausgestellt, daher bezahlt jede Verbindung einen
  vollständigen Handshake. `openssl s_client -sess_out` schreibt keine Sitzungsdatei.
- **OCSP-Stapling.** `OCSP response: no response sent`. In diesem Fall kostet das nichts — das verwendete Zertifikat von
  Let's Encrypt trägt überhaupt keinen OCSP-Responder, daher gibt es für einen Browser keine Abfrage der Sperrung.

Keines davon ist ein Mangel der Installation; es sind Grenzen eines in PHP geschriebenen TLS-Servers.


## Was ehrlich gesagt bleibt

Der Server beantwortet eine warme Seite in 67–88 ms und erreicht beim Handshake die Zeit von nginx. Alles, was ein
Browser darüber hinaus zeigt, ist die Entfernung zum Server und die 43 Unterressourcen der Seite — 5 Stylesheets,
4 Skripte und 41 Bilder. Diese Zahl zu senken, ist der größte verbleibende Punkt, und das ist Arbeit am Theme, nicht am
Server.

`/showcase` rendert in 4,5–21 s, weit schlechter als jede andere Seite, und verzerrt den Vorwärmzyklus. Die Seite
verdient eine eigene Untersuchung.


## Korrekturen, und was der Vorwärmer seitdem lernen musste

Drei Fehler wurden im Vorwärmer gefunden, nachdem dies zuerst geschrieben wurde, und jeder meldete Erfolg, während er
geschah.

**Er wärmte Adressen vor, die niemand anfragt.** Die Pfade kamen aus `urlAlias()`, das die nackte Form liefert, daher
füllte er `/fitness`, während Besucher `/site/fitness` anfragten -- ein eigener Cache-Eintrag, weil der Cache nach Host
und Pfad schlüsselt und die Testinstallation denselben Inhalt sowohl über eine Host-Zuordnung als auch über eine
URI-Zuordnung erreicht. Gemessen: `/fitness` 116 ms warm, `/site/fitness` 496 ms kalt. Beide Formen werden jetzt
vorgewärmt, ebenso `/site` und `/site/`, die ebenfalls verschieden sind.

**Er erneuerte nichts.** Eine Seite anzufragen, ergab einen Cache-Treffer und ließ den Ablauf des Eintrags unberührt,
daher liefen Einträge zwischen den Läufen ab, egal wie oft der Cron feuerte. Anfragen tragen jetzt `X-Cache-Refresh`,
für das der Server an der gespeicherten Kopie vorbei liest, sodass die Seite neu gerendert und erneut gespeichert wird.
Das braucht `qbix-webserver` 0.0.4.6 oder neuer.

**Dann wurde er selbst der Grund für langsame Seiten.** Erneuern heißt rendern, daher wurde ein Zyklus zu 285
vollständigen Renderings. Eine Admin-Seite maß 141–287 ms zwischen den Zyklen und 679–1146 ms während eines Zyklus. Die
Parallelität ist 1.

Das Merkmal, das man sich merken sollte: **Eine gecachte Antwort, die langsam ist, ist ein Kapazitätsproblem, kein
Problem des Renderns.** Der Header `expires` sagte, die Seite komme aus dem Cache, während die Uhr 1590 ms sagte, und
dieser Widerspruch war die Antwort.

## Der Antwort-Cache achtet in der Grundeinstellung auf das falsche Cookie

Der eigene Standard des Servers nennt `PHPSESSID` und `Q_sid`. Das sind die Namen von PHP und von Qbix, und ein Browser,
der ein veraltetes von beiden trug -- von irgendeiner Qbix-Anwendung auf demselben Host --, umging den Cache bei jeder
Anfrage, während eine echte Sitzung glatt hindurchging. 74 ms aus dem Cache gegenüber etwa 1300 ms gerendert, für
Seiten, die Byte für Byte identisch waren.

`expVelocity` leitet die Liste jetzt aus `site.ini` ab und schreibt sie in die Konfiguration des Servers. Überschreiben
Sie sie mit `[ServerSettings]CacheSkipCookies`.

**Korrigiert:** Die erste Version dieser Ableitung las allein `[Session]SessionNamePrefix`, was nur mit
`SessionNameHandler=custom` richtig ist. Mit `default` -- dem Standard von `site.ini` -- benennt PHP das Sitzungs-Cookie,
`PHPSESSID`, und das Präfix wird gar nicht verwendet. Eine Installation mit dem Standard-Handler erhielt eine Liste mit
`eZSESSID`, traf nie ihr eigenes Sitzungs-Cookie und beantwortete bei eingeschaltetem Antwort-Cache eine Anfrage eines
angemeldeten Benutzers aus dem Cache oder speicherte sie für alle anderen. Die Liste ist jetzt das Sitzungs-Cookie, wie
der Handler es benennt -- `session.name` für `default`, `SessionNamePrefix` für `custom` (als Präfix abgeglichen, sodass
es `<prefix><digest>` abdeckt) -- plus `is_logged_in`, das der Kernel genau für diesen Zweck bei angemeldeten Besuchern
setzt.

## Die Anmeldung funktionierte über HTTP/2 vor 0.0.4.7 nicht

Cookies, die ein Skript setzt, werden in einer gepoolten Antwort getrennt von seinen Headern getragen, und der Pfad für
HTTP/2 las sie nie, daher wurde jedes `Set-Cookie` verworfen. Die Anmeldung antwortete mit 302 an die richtige Stelle
und setzte keine Sitzung.

Es verbarg sich gut: Ein Browser, der schon eine Sitzung hielt, arbeitete weiter, und eine über HTTP/1.1 erhaltene
Sitzung ist über HTTP/2 genauso gut. Nur eine frische Anmeldung auf einer Verbindung, die h2 ausgehandelt hatte, konnte
es sehen -- jedes private Fenster und jeder automatisierte Test.


## Die HTTP/2-Verbindung härten

Eine HTTP/2-Verbindung lässt eine Gegenstelle einen Server bitten, in ihrem Namen Zustand zu halten: offene Streams,
einen Header-Block, der zusammengesetzt wird, einen Body, der empfangen wird, Bytes, die zum Senden anstehen. Jedes davon
war bis `qbix-webserver` 0.0.4.9 unbegrenzt.

Nichts davon braucht einen authentifizierten Benutzer, einen fehlerhaften Frame oder einen Bug. Es ist gewöhnliche
Nutzung des Protokolls, ins Übermaß getrieben, und das macht es schwer zu sehen: **Keine Anfrage wird abgeschlossen,
daher wird währenddessen nichts in ein Zugriffsprotokoll geschrieben.** Das erste Zeichen ist ein Worker, der nicht
antwortet.

| Grenze | Standard | was sie verhindert |
|---|---|---|
| `concurrentStreams` | 128 | gleichzeitig offene Streams |
| `headerListSize` | 64 kB | CONTINUATION ohne END_HEADERS |
| `bodySize` | 64 MB | einen Body, der vollständig gehalten wird, bevor die Anwendung ihn sieht |
| `readBuffer` | 4 MB | angekündigte und nie vervollständigte Frames |
| `writeBuffer` | 8 MB | eine Gegenstelle, die viel anfordert und aufhört zu lesen |
| `resetStreams` | 256 | wiederholtes Öffnen und Abbrechen |
| `reflexFrames` | 1000 | PING und SETTINGS, die eine Antwort verlangen |
| `idleSeconds` | 120 | Sockets, die offen gehalten werden und nichts sagen |

Alle lassen sich unter `Q.web.http2.limits` überschreiben. Ein konfigurierter Wert von null oder weniger wird
**ignoriert**, statt als „unbegrenzt“ gelesen zu werden, weil unbegrenzt genau der Zustand ist, den diese Grenzen hinter
sich lassen sollen.

Zwei davon sollte man im Einzelnen kennen.

**Die Grenze für Streams war angekündigt und nie gezählt worden.** SETTINGS teilte jeder Gegenstelle monatelang 128
mit, während nichts das durchsetzte, daher war eine Gegenstelle, die uns glaubte, das Einzige, was die Zahl niedrig
hielt. Eine Gegenstelle darf eine angekündigte Grenze als echt behandeln; eine, die das nicht tut, hatte keinen
Widerstand.

**Der Schreibpuffer kam mit einer Fehlerkorrektur.** Als das Abschneiden großer Antworten behoben wurde, begann das,
was nicht in den Socket passte, im Speicher zu warten — das lässt eine große Antwort einen vollen Sendepuffer
überstehen, bedeutet aber auch, dass eine Gegenstelle, die sehr viel anfordert und dann einfach aufhört zu lesen, dem
Prozess eine unbegrenzte Speicheranforderung aufzwingt. Es ist die leisere Hälfte von Slowloris, weil die Anfrage
vollkommen gültig war.

Untätige Verbindungen werden von der Ereignisschleife eingesammelt statt von je einem Timer, da nur die Schleife sie
zusammen sehen kann und ein Timer pro Verbindung selbst eine Ressource ist, die eine Gegenstelle vervielfachen könnte.

### Der HPACK-Decoder las über seinen Puffer hinaus

Durch Fuzzing gefunden statt durch Lesen: 600 zufällige Header-Blöcke erzeugten Lesezugriffe bis zu
**119.315.352 Bytes hinter dem Ende**.

Unter PHP 8 ist das eine Warnung und eine leere Zeichenkette statt eines Absturzes, deshalb blieb es unbemerkt. Aber
eine Warnung wird einmal pro Byte in das Fehlerprotokoll geschrieben, in einer Häufigkeit, die die Gegenstelle wählt,
und mit eingeschaltetem `display_errors` stattdessen in den Body der Antwort.

Die schlimmere Hälfte ist leiser. `substr()` mit einer Länge über das Ende hinaus gibt zurück, was da ist, daher wurden
bei einem Block, der einen Header-Wert von 200 Bytes angab und 12 lieferte, diese 12 **als der Wert** dekodiert. Ein
lügender Block schlug nicht fehl; er erzeugte einen Header, den die Gegenstelle nie gesendet hatte.

Beide Stellen prüfen jetzt vor dem Lesen, und ein Block, der sich nicht dekodieren lässt, schließt die Verbindung mit
`COMPRESSION_ERROR`. Das verlangt RFC 7541, und es ist keine Bürokratie: Die dynamische Tabelle von HPACK wird von jedem
Block der Verbindung geteilt, daher ist die Tabelle falsch, sobald einer falsch gelesen wurde, und jeder Block danach
dekodiert zu etwas, das niemand gesendet hat. Eine teilweise Wiederherstellung lohnt keinen Versuch.

### Etwas davon prüfen

Die eigene Suite des Servers enthält einen Fall pro Angriff. Jeder **führt** den Angriff gegen die Verbindung aus und
stellt sicher, dass er abgewiesen wird — sicherzustellen, dass eine Konstante für eine Grenze existiert, hätte gegen den
Zustand bestanden, den dies ersetzt hat, in dem die Grenze für Streams angekündigt und nie angewendet wurde.

```bash
php vendor/se7enxweb/exponential-velocity/tests/run-unit.php
```

Kein Server, kein Socket, kein Zertifikat. `tests/run.sh` führt das aus, bevor irgendetwas startet, weil es keinen Sinn
hat, einen Port zu binden, um festzustellen, dass der Frame-Codec defekt ist.

### Was das nicht ist

Es ist kein Sicherheitsbeweis. Die bekannten Klassen der Erschöpfung von HTTP/2 sind begrenzt, und jede hat einen Test;
das ist eine andere Aussage als „keine verbleibenden Mängel“, und die beiden Bugs oben wurden durch Fuzzing und Messen
gefunden statt durch Lesen, was das ehrliche Signal dafür ist, was Lesen allein übersehen hat.

`Panel.php`, `WebSocket.php`, `Trust.php` und `Autohost.php` hatten keinen solchen Durchgang. Das Speicherverhalten bei
einem langen Dauertest — hunderttausend Anfragen gegen einen residenten Prozess — wurde überhaupt nicht gemessen.


## Messen, und eine Zahl von Workern wählen

`vendor/se7enxweb/exponential-velocity/tests/bench-load.php` erhöht die Parallelität, bis der Durchsatz nicht mehr
steigt, und meldet, wo der Knick liegt.

```bash
php vendor/se7enxweb/exponential-velocity/tests/bench-load.php \
    https://www.example.com:8080/site --levels=1,4,16,32,64 --requests=200
```

Es braucht nichts Installiertes: Das curl von PHP spricht HTTP/2, und `curl_multi` liefert die Parallelität. Jeder Lauf
schreibt eine CSV-Datei unter `var/storage/generated/stats/`, benannt nach Datum, Ziel, Protokoll, Stufen, Zahl der
Anfragen und Zahl der Worker, sodass zwei Läufe nebeneinander sortiert werden und sagen, was sie waren, ohne geöffnet
zu werden.

### Was es auf der Testinstallation fand

11 Kerne, 46 GB und **167,6 MB PSS pro Worker** — die ehrliche Zahl, da RSS 200 MB meldet, indem es Seiten doppelt
zählt, die nach dem Fork geteilt sind.

| Worker | Spitze Anfr./s | Last | resident |
|---|---|---|---|
| 8 | 233,3 | — | 1,7 GB |
| **16** | **307,0** | 7,8 | 1,5 GB |
| 32 | 303,4 | 15,6 | 2,9 GB |
| 64 | 272,8 | 10,5 | 3,2 GB |

Sechzehn ist das Optimum, bei etwa dem 1,5-Fachen der Zahl der Kerne. Zweiunddreißig brachte nichts und verdoppelte die
durchschnittliche Last; vierundsechzig war schlechter als sechzehn.

`[ServerSettings]Workers` in `settings/velocity.ini` oder einem Override.

### Die Klippe zählt mehr als die Spitze

Pro Anfrage, bei 16 Workern:

| gleichzeitig | p50 | p90 | p99 | Anfr./s |
|---|---|---|---|---|
| 1 | 44 ms | 45 ms | 51 ms | 22 |
| 4 | 42 ms | 46 ms | 124 ms | 95 |
| **16** | **28 ms** | 46 ms | 295 ms | **307** |
| 32 | 70 ms | 445 ms | 465 ms | 227 |
| 64 | 188 ms | 857 ms | 882 ms | 153 |

Bei sechzehn *sinkt* p50 auf 28 ms, während der Durchsatz seine Spitze erreicht — Anfragen stehen gerade so weit an, dass
jeder Worker beschäftigt bleibt. Darüber hinaus geht p90 von 46 auf 445 ms, eine zehnfache Klippe, während der
Durchsatz fällt. **Das ist die Betriebsgrenze, und sie kommt lange vor dem Speicher.**

### Die Decke darüber ist Arithmetik, keine Messung

Bei 167,6 MB pro Worker auf 46 GB: 128 Worker wollen 21 GB und swappen, 512 wollen 84 GB, 2500 wollen 409 GB. Diese
Konfigurationen sind unmöglich statt langsam, und um das zu sagen, braucht es keinen Benchmark. Rechnen Sie voraus,
bevor Sie eine Reihe laufen lassen.

Diese Zahl pro Worker ist der eine Hebel, den das Vorwärmen bewegt: Das Rendern im Elternprozess vor dem Fork senkt sie
etwa um das Zehnfache (siehe *Speicher im Pool teilen*), was diese Decke entsprechend anhebt — aber nie die Klippe des
Durchsatzes oder die Decke der Ereignisschleife, die der Speicher nicht berührt.

### Was ein Besucher bezahlt

    dns     7 ms
    tcp    +0.3 ms
    tls    +18 ms
    ttfb   +41 ms
    -------------
    total   67 ms     9.4 kB gzipped

Eine vollständige Seite — Dokument plus 35 Unterressourcen über eine Verbindung — dauert etwa 200 ms.

### Zwei Vorbehalte zu allem

Der Generator ist **geschlossen**, wie `ab` und `wrk`: Eine langsame Antwort verzögert die Anfrage, die gefolgt wäre,
daher sind die nie gesendeten Anfragen genau die, die am langsamsten gewesen wären. Lesen Sie die Perzentile als „wie er
bedient hat, was er angenommen hat“, nicht als das, was ein Benutzer gesehen hätte. Ein offener Generator mit fester
Ankunftsrate ist die andere Hälfte und wird nicht mitgeliefert.

Und diese Zahlen beschreiben die Templates *dieser* Website auf *dieser* Maschine. Sie sind keine Eigenschaft des
Servers und sollten nicht als solche zitiert werden.


## Die Decke, die die einzelne Ereignisschleife setzt

Die Speicherarithmetik oben sagt, wie viele Worker *passen*. Es gibt eine zweite, niedrigere Decke, die sagt, wie viele
*laufen* können, und sie hat überhaupt nichts mit Speicher zu tun.

Der Elternprozess ist ein Prozess mit einer Ereignisschleife. Er allein nimmt Verbindungen an und bedient den WebSocket
des Dashboards; die Worker bearbeiten nur Anfragen, die ihnen übergeben werden. Forken Sie genug Worker, und sie
entziehen diesem einen Prozess die CPU. Hier gemessen: bei **1250 Workern auf 12 Kernen lief jede Anfrage nach 45 s in
einen Timeout**, und das Dashboard blieb auf einem roten „verbinde“, das sich nie auflöste — der WebSocket-Upgrade kam
nie an die Reihe der CPU. Die Rückkehr zu einigen hundert stellte den Betrieb sofort wieder her. Die Last war die ganze
Zeit mäßig; das ist das Aushungern eines Platzes im Scheduler, keine Sättigung.

Es gibt also drei Grenzen, und sie kommen in dieser Reihenfolge, wenn die Zahl steigt: die **Klippe des Durchsatzes**
(hier ~16, wo p90 umkippt — die wirkliche Betriebsgrenze), die **Decke der Ereignisschleife** (einige hundert, wo der
Elternprozess nicht mehr reaktionsfähig bleiben kann) und die **Decke des Speichers** (Arithmetik, die höchste der drei).
Stellen Sie auf die erste ein. Die anderen sind Fehlerarten, von denen man deutlich unterhalb bleiben sollte, keine
Ziele.

### Die Überwachung darf nicht zur Last werden

Eine Folgerung, gelernt, indem es kaputtging: Das Dashboard las `/proc/<pid>/smaps_rollup` für **jeden** Worker, um den
echten Speicher (PSS) zu melden, und tat das innerhalb der Ereignisschleife, alle paar Sekunden, solange ein Dashboard
offen war. Der Kernel berechnet `smaps_rollup`, indem er alle Mappings eines Prozesses durchläuft, daher blockierte
dieses Lesen bei Hunderten von Workern die Schleife lange genug, um den ganzen Server festzufahren — dasselbe Symptom wie
bei der Decke oben, aus einer anderen Ursache. Die Lösung war **Stichproben**: höchstens ~24 Worker lesen, den
Durchschnitt auf die Zahl hochrechnen, den Elternprozess genau dazu lesen. Jede Arbeit pro Worker auf dem Pfad einer
Anfrage muss genauso begrenzt sein, weil die Größe des Pools es nicht ist.


## Speicher im Pool teilen: ein Rendering im Elternprozess vorwärmen

Ein Worker baut den Arbeitsbestand pro Anfrage des Frameworks — kompilierte Templates, das aufgelöste Layout, den
Objektgraphen — beim ersten Mal auf, wenn er eine Seite ausliefert, und behält ihn: gemessen konstant bei ~209 MB privat
über 800 Anfragen, einmal pro Worker. Dieser Bestand liegt in der Arena des Zend-Allokators, die anonymer Speicher ist,
den `fork()` per Copy-on-Write teilt. Wird er also im **Elternprozess** aufgebaut, bevor die Worker existieren, erbt ihn
jeder Worker geteilt und bezahlt nur für das, was eine Anfrage über die geteilte Basis hinaus braucht.

`bin/php/velocity-warmup.php`, eingebunden über `Q.webserver.preload` und gesteuert von `[ServerSettings]PreloadWarmup`,
rendert vor dem Fork einmal eine repräsentative Seite im Elternprozess. Gemessene Wirkung: **~17–24 MB privat pro
warmem Worker damit, gegenüber ~209 MB ohne** — der Unterschied zwischen einem Vorwärmen des ganzen Pools, das einige GB
kostet, und einem, das über hundert kostet.

Es ist standardmäßig ausgeschaltet, und der Grund ist der ganze Sinn des nächsten Abschnitts: Ein Rendern hinterlässt
Zustand, der Snapshot der statischen Daten des Pools friert diesen Zustand als Basis jedes Workers ein, und der falsche
dort eingefrorene Zustand wird echten Besuchern ausgeliefert. Das Vorwärmen richtig zu machen *heißt*, das Zurücksetzen
richtig zu machen.


## Der globale Bereich, den ein persistenter Worker schützen muss

Das ist die tragende Lehre des ganzen Modells, und sie ist nicht auf das Vorwärmen beschränkt: **Jeder Zustand, den
eine Anfrage in einer statischen Eigenschaft oder in `$GLOBALS` hinterlässt, wird von der nächsten Anfrage in diesem
Worker geerbt.** Bei einem Prozess pro Anfrage warf das Betriebssystem diesen Zustand beim Beenden weg; bei einem
persistenten Worker überlebt er, und bei einem Vorwärmen wird er in jedem Worker auf einmal eingefroren. Drei echte
Mängel hier, jeder eine andere eingefrorene globale Variable, jeder lieferte einem Besucher etwas Falsches aus, haben das
bewiesen:

- Ein eingefrorenes **Routing der Anfrage** ließ jede URL die Startseite ausliefern.
- Eine eingefrorene **teilweise Zuordnung der Template-Overrides** ließ einige Seitentypen ohne ihr Override.
- Ein eingefrorenes Flag **„Assets bereits ausgegeben“** ließ die Suchseite ihr CSS auslassen.

Der Zustand teilt sich in drei Arten. Die Disziplin besteht darin, zu wissen, zu welcher Art jede globale Variable
gehört, weil der falsche Schritt für eine Art der richtige für eine andere ist.

### 1. Auf die Anfrage bezogen — muss zwischen Anfragen (oder nach einem Vorwärmen) geleert werden

Identität, die Anfrage, wohin sie geroutet wurde, und jeder Griff in die Außenwelt. Nie teilen; nie bestehen lassen. In
Exponential sind das:

| Global / statisch | Was es enthält |
|---|---|
| `eZRequestedModule`, `eZRequestedModuleParams`, `eZRequestedURI` | die Anfrage und ihr Routing |
| `eZURIRequestInstance`, `eZGlobalRequestURI`, `eZModuleViewStack` | die geparste URI und der View-Stapel |
| Instanz von `eZSys` (`eZSys::setInstance(null)`) | Serverpfade, Skriptname, die URL, aus der die Seite Links baut |
| `ezpKernel::$instance` | das Kernel-Objekt mit der letzten Anfrage/Antwort |
| `eZCurrentAccess` | der aufgelöste Siteaccess |
| `eZUserGlobalInstance*`, `eZUserBuiltins` | der aktuelle Benutzer — ihn zu teilen, gibt die Identität eines Besuchers an den nächsten weiter |
| `eZDBGlobalInstance` | die Datenbankverbindung — ein geforktes Kind, das den Socket des Elternprozesses teilt, vermischt den Verkehr auf der Leitung und beschädigt ihn; schließen Sie sie vor dem Fork |
| `eZHTTPToolInstance`, `eZExpiryHandlerInstance` | Helfer pro Anfrage für Anfrage und Ablauf |
| Cache der Inhaltsobjekte (`eZContentObject::clearCache()`) | die Objekte der gerenderten Seite |

### 2. Caches, die *vollständig* sein müssen, um korrekt zu sein — leeren, damit sie neu gebaut werden

Die subtile Art. Das sind Leistungs-Caches, keine Identität, daher sieht ihr Teilen sicher aus — aber ein Cache, den
*eine* Seite gefüllt hat, ist **teilweise**, und ein teilweiser Cache ist schlimmer als ein leerer, weil Code ihm als
vollständig vertraut und aufhört zu suchen. Zwei davon hier, und beide haben zugebissen:

| Global / statisch | Warum teilweise falsch ist |
|---|---|
| `eZOverrideTemplateCacheMap` | nur für die Templates der vorgewärmten Seite gebaut; andere Seitentypen fanden kein Override und renderten das falsche Template |
| `ezjscPackerTemplateFunctions::$loaded` (und `$persistentVariable`) | der Asset-Packer setzt `$loaded['css_files'] = true`, sobald eine Seite ihr Stylesheet ausgibt; als true eingefroren, glaubt die nächste Seite, ihr CSS sei schon da, und lässt das `<link>` aus — deshalb kam gerade die *Suchseite* ohne Styles zurück |

Leeren Sie diese, damit jede Anfrage einen vollständigen neu baut. Der freigegebene Speicher geht an die Arena zurück,
nicht an das Betriebssystem, daher kostet ihr Leeren nicht den Nutzen des Vorwärmens.

### 3. Registries — teuer, bei jeder Anfrage identisch, sicher zu behalten

Typ- und Pfad-Registries, geparste Konfiguration, Tabellen für Locales und Zeichensätze. Ihretwegen spart das Vorwärmen
Speicher: Einmal bauen, teilen. Behalten Sie sie.

`eZDataTypes*`, `eZWorkflowTypes*`, `eZNotificationEventTypes*`, `eZModuleGlobalPathList`, die INI-Caches, die Tabellen
für Locales und Zeichensätze und die *Konfiguration* der Template-Designs (Basen, Einstellungen,
Compiler-Verzeichnis — aber **nicht** die Override-Cache-Zuordnung aus Art 2). `[ServerSettings]KeepGlobals` nennt die,
die das Zurücksetzen zwischen Anfragen erhält; die Menge, die das Vorwärmen behält, folgt derselben Idee.

### Wie man sie in einer Codebasis findet, die nicht dafür gebaut wurde

Sie melden sich nicht von selbst. Zwei Techniken fanden jede oben genannte:

- **`$GLOBALS` nach einem Rendern ausgeben** und jeden Schlüssel nach Name und Größe einordnen (ein kurzes Skript, das
  `$GLOBALS` durchläuft, genügt). Die auf die Anfrage bezogenen fallen auf; die großen sind meist die Caches.
- **Nach dem Symptom suchen, nicht nach dem Zustand.** Der CSS-Mangel wurde nicht durch Lesen globaler Variablen
  gefunden, sondern indem die Codebasis danach durchsucht wurde, wo `css_files` gesetzt wird, was direkt zur statischen
  Variablen `$loaded` des Packers führte.

Und prüfen Sie am Verhalten, nie durch Ansehen: Bestätigen Sie nach jeder Änderung an dem, was das Vorwärmen rendert
oder leert, dass **mehrere verschiedene URLs verschiedene Inhalte liefern und jede ein Stylesheet trägt, das sich
auflöst.** Fehler beim Zurücksetzen sind in einer einzelnen Anfrage unsichtbar; sie erscheinen erst bei der zweiten,
anderen.

### Die Liste ist nicht die Lösung — die Disziplin ist es

Alles oben ist ein echter, tragender Katalog, und er ist trotzdem **nicht vollständig**. Drei Lecks wurden beim Namen
gefunden und behoben; ein viertes erschien trotzdem — die Startseite renderte das Layout und den Breadcrumb eines
anderen Knotens, weil das Rendern noch ein weiteres Stück Kontext der Anfrage an den geteilten Bereich gekoppelt hatte,
das kein Eintrag oben nennt. Das ist die eigentliche Lehre, und sie ist stärker als jede Liste: **In einem Framework mit
vielen `$GLOBALS`, das nicht für einen geteilten Worker gebaut wurde, können Sie den auf die Anfrage bezogenen Zustand
nicht aufzählen, also versuchen Sie es nicht.** Eine von Hand gepflegte Liste zum Leeren konvergiert langsam, wenn
überhaupt, und jede Lücke darin liefert eine falsche Seite aus.

Der robuste Entwurf ist das Gegenteil einer Liste zum Leeren: Halten Sie den *gesamten* Bereich nach dem Bootstrap
einmal als Snapshot fest — jede statische Eigenschaft und jeden Schlüssel von `$GLOBALS`, der existiert, nachdem der
Kernel initialisiert ist, aber bevor eine Anfrage ausgeliefert wird — und stellen Sie nach jeder Anfrage (oder nach
einem Vorwärm-Rendering) *diesen* wieder her, statt zu benennen, was entfernt werden soll. Das Zurücksetzen der Engine
zwischen Anfragen arbeitet für statische Daten bereits so; ein Vorwärmen, das vor dem Snapshot rendert, muss `$GLOBALS`
derselben Regel unterwerfen, und bis es das tut, ist das Rendern im Elternprozess auf einer Website mit echtem Inhalt
nicht sicher einzuschalten. Der Speicher, den es sparen würde, ist selten die Beschränkung, die zählt — die Klippe des
Durchsatzes und die Decke der Ereignisschleife kommen zuerst —, daher ist das eine Optimierung, zu der man erst greift,
nachdem diese angegangen sind, und nur mit einer vollständigen Regressionsprüfung von Seiten und Assets, die genau die
Klasse von Fehlern erkennt, die eine Liste zum Leeren übersieht.

**Das wurde umgesetzt, und es funktioniert.** `bin/php/velocity-warmup.php` rendert im Elternprozess, setzt dann *jede*
statische Eigenschaft von Benutzerklassen auf ihren deklarierten Standard zurück und leert die globalen Variablen der
Anfrage, wobei nur die reinen Registries für Konfiguration und Typen erhalten bleiben (`eZINI`, `eZDataType`,
`eZWorkflowType`, `eZModule`, `eZExtension`, `eZLocale`, `eZCharsetInfo`, `eZTextCodec`) — teuer neu zu bauen, ohne
Zustand einer Anfrage. Dieser eine Schritt beseitigte alle vier Klassen von Verunreinigung auf einmal, einschließlich
des aktuellen Knotens, den der Breadcrumb las, wo das Benennen von Lecks nach drei Korrekturen nicht konvergiert hatte.
Die Registries bleiben geteilt, daher hält der Speicher: ~21 MB pro warmem Worker gegenüber ~209 ohne. Die eine Regel,
die es sicher machte, war, in der Ausnahmeliste *nur* reine Konfiguration zu behalten — auch die Registries
zurückzusetzen, ließ Seiten in Timeouts laufen und verdoppelte den Speicher, und die Caches für Templates oder
Inhaltsklassen zu behalten, ließ das Leck des Breadcrumbs direkt zurückkommen. Die Prüfung, die genau dieses Leck
erkennt, ist ein Paar: Die Startseite darf **keinen** Breadcrumb haben und eine tiefe Seite muss **ihren eigenen**
haben — eine einzelne URL zeigt es nicht.

## Verwandte Seiten

- [Velocity-Engines — Qbix, FrankenPHP, der eingebaute Server von PHP](velocity-engines.md)
- [Hinweise zum Upgrade der Velocity-Engine](velocity-engine-upgrade-hinweise.md)
- [Antwort-Cache und Navigation](antwort-cache-und-navigation.md)
- [Velocity, der Server mit persistenten Workern](../../../eng-us/features/6.0/velocity-persistent-worker-server.md) und [Antwort-Cache von Velocity](../../../eng-us/features/6.0/velocity-response-cache.md)
- [Preloader für den Cache der Website](preload.md)
- [Anleitung zum Bereitstellen](../../anleitungen/bereitstellung.md)
