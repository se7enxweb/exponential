# Spezifikation: der Worker-Pool und die Kompatibilitätsschicht von Velocity

Diese Seite ist die Referenz dafür, wie Velocity PHP ausführt: der Elternprozess und seine **Worker**, wie die Größe des Pools
bemessen wird, wann ein Worker ersetzt wird, die Zygote, der Benutzer, unter dem die Worker laufen, was zwischen Anfragen zurückgesetzt wird, und die
Kompatibilitätsschicht, mit der gewöhnlicher PHP-Code in einem langlebigen Prozess läuft. Lesen Sie sie, wenn Sie einen Pool dimensionieren, dem
Speicherverbrauch oder einem veralteten Wert zwischen Anfragen nachgehen oder Velocity als root betreiben. Sie gilt für Exponential Velocity 0.0.4.42.

## Kurz gesagt

- Der Elternprozess lädt die Anwendung einmal und forkt Worker; jeder Worker bedient viele Anfragen und wird nach
  jeder zurückgesetzt.
- Legen Sie die Größe des Pools mit `--workers=N` oder `[ServerSettings] Workers` in `settings/velocity.ini` fest; ohne diese Angabe
  bemisst der Server den Pool anhand von Speicher und Kernen.
- Beurteilen Sie den Speicher nach PSS, nie nach RSS: Ein vollständiger Exponential-Worker kostet etwa 11 MB.
- Worker laufen nie als root, es sei denn, Sie erlauben es.

## Modell

- Ein Elternprozess lädt die Anwendung einmal, wärmt sie optional vor und forkt dann Worker. Jeder Worker beantwortet eine
  Anfrage nach der anderen.
- Nach jeder Anfrage wird der Worker in den Zustand zurückversetzt, den er beim Forken hatte, sodass eine Anfrage nie die
  Variablen einer anderen sieht.
- Der Elternprozess nimmt jede Verbindung an und liest jede Anfrage. Statische Dateien, Treffer im Antwort-Cache
  und die eigenen Seiten des Servers beantwortet er selbst. Nur eine Anfrage, die PHP ausführt, geht an einen Worker: den ersten untätigen, der
  vor der Verwendung darauf geprüft wird, ob er noch lebt.
- Sind alle Worker beschäftigt, wartet die Anfrage im Elternprozess. Sie wird nie mangels eines Workers abgewiesen; die
  Grenze ist `maxConnections`, jenseits deren der Server mit `503` antwortet.
- Eine Anfrage, die nicht an einen Worker übergeben werden kann, kommt bis zu dreimal zurück an den Anfang der Warteschlange; danach erhält der
  Client `502`.

| Modus | Verhalten |
|---|---|
| Persistent (Standard) | Jeder Worker bedient viele Anfragen |
| `forkPerRequest` | Eine Anfrage pro Worker, dann ein frischer Fork: die Isolation eines frischen Prozesses um den Preis eines Forks pro Anfrage |
| php-cgi-Ausnahme | Für Skripte, die eine klassische SAPI brauchen |

## Größe des Pools

`--workers=N` legt sie fest. Ohne diese Angabe bemisst der Server den Pool anhand der Maschine und dessen, was ein Worker kostet: Für einen Worker wird angenommen, dass er mindestens so viel hält wie der Elternprozess nach dem Laden der Anwendung (nie weniger als 8 MB); die Anzahl ist das, was in den Speicher passt, der übrig bleibt, nachdem 1 GB zurückgelegt wurde, höchstens 8 pro Kern und insgesamt 64, nie weniger als 4. Die Anzahl wird dann gegen die Grenzen für Dateideskriptoren und Prozesse geprüft; in einem Terminal fragt der Server nach, bevor er einen größeren Pool startet, und **er fragt nie, wenn niemand antworten kann** (wenn ein Dienstmanager ihn startet). Alles über der automatischen Obergrenze ist ein bewusst gesetztes `--workers`.

### Was ein Worker kostet

Gemessen am 2026-09-26: PSS aus `/proc/<pid>/smaps_rollup`, Linux mit 12 Kernen, PHP 8.5.

| | Privat pro Worker | PSS pro Worker | Worker pro GB |
|---|---|---|---|
| Der Server allein, PHP-Seite mit einer Zeile | 1,3 bis 1,9 MB | 1,4 bis 1,9 MB | etwa 530 |
| Ein vollständiges CMS (Exponential, etwa 600 Klassen) | etwa 10 MB | etwa 11 MB | etwa 90 |

Gemeinsam genutzter Code und Daten, die vor dem Fork geladen werden: etwa 20 MB für den Server allein, 47 MB für das CMS, einmal bezahlt. Speicher wird immer als PSS oder privat beurteilt, nie als RSS (RSS zählt jede gemeinsam genutzte Copy-on-Write-Seite einmal pro Worker und überschätzt einen Pool um ein Vielfaches). Auf einer Exponential-Installation belegte ein fester Pool von 590 Workern 7.094 MB; der dynamische Pool belegte 626 MB. Mit der Standard-Event-Loop (`stream_select`, 1.024 Deskriptoren) hat der Pool eine Obergrenze knapp unter 1.000 Workern: 900 Worker bedienten 20.000 Anfragen ohne Fehler, bei 1.000 wurde jede Verbindung zurückgesetzt.

## Statische und dynamische Pools

Statisch (Standard): Jeder Worker wird beim Start geforkt und bleibt. Dynamisch (`spareWorkers` über 0): Beim Start werden nur die Reserve-Worker geforkt; sind alle beschäftigt, wird bis zur Größe des Pools ein weiterer geforkt; ein Worker über der Reserveanzahl, der `idleWorkerTimeout` Sekunden untätig war, wird stillgelegt, der am längsten untätige zuerst. Ein beschäftigter Worker wird nie stillgelegt; der Pool fällt nie unter die Reserveanzahl.

```json
{ "Q": { "webserver": { "spareWorkers": 8, "idleWorkerTimeout": 60 } } }
```

Der Tod eines Workers kostet nie eine Anfrage, die hätte bedient werden können: Ein `GET`, `HEAD` oder `OPTIONS`, dessen Beantwortung er noch nicht begonnen hatte, läuft noch einmal auf einem anderen Worker; ein `POST` wird nie zweimal ausgeführt und erhält ein `502`. Ein untätiger Worker, der sich beendet, wird innerhalb von zwei Sekunden bemerkt, ersetzt und abgeräumt (keine Zombies).

## Einstellungen (alle unter `Q.webserver`)

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `spareWorkers` | `0` | Worker, die bei Untätigkeit behalten werden; ein Wert über 0 macht den Pool dynamisch |
| `idleWorkerTimeout` | `60` | Sekunden, die ein Worker über der Reserveanzahl untätig bleiben darf |
| `maxRequests` | `1000` | Anfragen, die ein Worker bedient, bevor er ersetzt wird; `0` keine Grenze |
| `requestTimeout` | `30` | Sekunden, die eine Anfrage laufen darf, bevor der Client `504` erhält und der Worker ersetzt wird; die Anfrage wird nicht erneut ausgeführt; `0` keine Grenze |
| `workerMemoryCeiling` | `256` MB oder 3/4 von `memory_limit`, falls kleiner | Heap-Größe, ab der ein Worker ersetzt wird; `0` schaltet das aus |
| `forkPerRequest` | `false` | Eine Anfrage pro Worker |
| `zygote` | `true` | Worker, die nach dem Pool gestartet werden, aus einer Zygote forken (unten) |
| `warmup` | keiner | Ein Skript, das einmal im Elternprozess vor dem Forken läuft |
| `keepGlobals` | `[]` | Globale Variablen, die ein Worker zwischen Anfragen behält (`--keep-globals=A,B`) |
| `maxConnections` | `1024` | Gleichzeitig offene Verbindungen; darüber hinaus `503` |
| `user`, `group` | Eigentümer und Gruppe des Dokumentenstammverzeichnisses | Als wer die Worker laufen, wenn der Server root ist |
| `allowRootWorkers` | `false` | `user` root erlauben (`--allow-root-workers`) |
| `warmupAsUser` | `true` | Das Vorwärmen mit den effektiven IDs des Worker-Benutzers ausführen |
| `writable` | `[]` | Weitere Verzeichnisse, die beim Start dem Worker-Benutzer übergeben werden |
| `reusePort` | `false` | `SO_REUSEPORT` auf den HTTP- und HTTPS-Listenern, damit sich mehrere Server einen Port teilen |
| `eventLoop` | `"auto"` | `auto` (Revolt, falls installiert, sonst `select`), `iopoll` (nur ausdrücklich gewählt), `revolt`, `select`; die Umgebungsvariable `QBIX_EVENT_LOOP` hat Vorrang |
| `debug` | `false` | Ob Meldung, Datei, Zeile und Stacktrace eines nicht abgefangenen Fehlers in der Antwort gezeigt werden |
| `Q.compat.statTtl` | `0` | Sekunden (höchstens 10), die ein Worker Dateiinformationen über Anfragen hinweg behält |

Aus Exponential heraus wird der Pool in `settings/velocity.ini` eingestellt (`[ServerSettings]`: `Workers`, `SpareWorkers`, `Instances`, `Zygote`); siehe [Velocity-Engines](../../../eng-us/bc/6.0/velocity-engines.md).

## Wann ein Worker ersetzt wird

Nach seiner laufenden Anfrage wird ein Worker durch einen frischen Fork ersetzt, wenn er `maxRequests` Anfragen bedient hat; wenn sein Heap über `workerMemoryCeiling` liegt; wenn seine Ausgabepuffer unausgeglichen zurückgelassen wurden; oder wenn die Anwendung `Q_WebServer_Pool::retireAfterResponse($reason)` aufgerufen hat (für Code, der nur einmal pro Prozess laufen kann, zum Beispiel solcher, der Konstanten aus der Anfrage definiert). Der Grund wird protokolliert, zum Beispiel `worker 12345 replaced after 812 requests: heap 402 MB over the 384 MB ceiling`. Ein Worker, der nach `requestTimeout` Sekunden noch an einer Anfrage arbeitet, wird beendet.

Der Reiter „Workers“ des Control Panels (API `POST /Q/api/workers/resize` mit `{"workers": N}`) ändert die Größe, während der Pool läuft, und kann einen Worker oder alle wiederverwerten, ohne neu zu laden.

**Neu laden und anhalten**: `qbixconsole server:reload` (`qbixctl graceful`) führt den Server erneut aus: Zuerst werden die Listener geschlossen, offene Verbindungen erhalten bis zu fünf Sekunden, Worker drei. Der neue Server forkt einen neuen Pool aus dem neuen Code und der neuen Konfiguration. `qbixctl restart` bringt einen Server mit den Optionen zurück, mit denen er gestartet wurde (Release 0.0.4.40; neben der PID-Datei wird eine Aufzeichnung als `<pid file>.json` geschrieben).

## Die Zygote

`fork()` übergibt einem neuen Worker jeden Deskriptor, den der Server hält, einschließlich der Verbindung jedes Besuchers; ein Worker kann eine TLS-Verbindung nicht schließen, ohne ein close_notify auf den gemeinsam genutzten Socket zu schreiben. Ein dynamischer Pool forkt genau dann, wenn Verbindungen offen sind, und gemessen wurde: Nach einem 20 Sekunden langen Ansturm von 355 gerenderten Seiten blieben bis zu 50 Verbindungen in `CLOSE-WAIT`, gehalten allein von Workern.

Ist `zygote` eingeschaltet, wird am Ende des Starts, bevor die erste Verbindung angenommen wird, ein zusätzlicher Prozess geforkt. Er schließt, was ein Worker schließt, und wartet. Jeder spätere Worker wird von der Zygote geforkt: Der Server übergibt das Socket-Ende des Workers über einen Unix-Steuersocket (`SCM_RIGHTS`, Frist fünf Sekunden für die gesamte Übergabe), die Zygote forkt und antwortet mit der PID. Die Zygote hat nie eine Client-Verbindung gehalten, also erben ihre Worker keine. Der Pool prüft die Lebendigkeit mit dem Signal `0` und über `/proc` (ein Zombie zählt nicht). Voraussetzungen: die PHP-Erweiterungen `sockets`, `pcntl`, `posix`. Ein Selbsttest beim Start übergibt einen Socket an sich selbst und verwendet die Zygote nur, wenn er unversehrt ankommt (die Verbindung des Workers wird als Stream-Ressource übergeben, die ab PHP 8.1 unversehrt ist, sodass PHP 8.2 bis 8.5 sie alle verwenden). Schlägt eine Übergabe fehl, steht im Protokoll `zygote: the zygote did not fork a worker; forking workers from the server from now on`, und keine Anfrage schlägt fehl.

So prüfen Sie es:

```bash
P=<server pid>
for c in $(ps -o pid= --ppid $P); do n=$(ps -o pid= --ppid $c | wc -l); [ $n -gt 0 ] && echo "zygote $c: $n workers"; done
ss -Htanp '( sport = :443 )' | grep -v LISTEN | grep -v "pid=$P,"      # connections held by anyone but the server
```

Geschichte: 0.0.4.29 schaltete sie ohne die Prüfung standardmäßig ein, sodass unter PHP 8.2 und 8.3 jeder neue Worker eine Anfrage beantwortete und der Rest `502` war; 0.0.4.30 lief dort ohne sie; 0.0.4.32 verwendet sie unter 8.2 bis 8.5. Wenn Sie 0.0.4.29 unter PHP 8.2 oder 8.3 betreiben und nicht aktualisieren können, setzen Sie `Q.webserver.zygote` auf `false`.

## Der Benutzer, unter dem die Worker laufen

Ein als root gestarteter Server behält root nur im Serverprozess (Ports, Zertifikate, Neuladen). Die Zygote, jeder Worker, jeder Kindprozess bei Fork pro Anfrage und jede geplante Aufgabe geben root direkt nach dem Fork und vor der Ausführung von Anwendungscode ab (`setgid`, `initgroups`, `setuid`, dann eine Prüfung, dass root nicht zurückerlangt werden kann; ein Prozess, dessen Wechsel fehlschlägt, wird beendet). Wer: Der erste Treffer gewinnt.

| Wo | Benutzer | Gruppe |
|---|---|---|
| Kommandozeile | `--user=NAME` | `--group=NAME` |
| Konfiguration | `Q.webserver.user` | `Q.webserver.group` |
| Umgebung, dann die Datei `envvars` des Konfigurationsbaums | `QBIX_RUN_USER` (Velocity liest zuerst `VC_RUN_USER`) | `QBIX_RUN_GROUP` (`VC_RUN_GROUP`) |
| Standard | Eigentümer des Dokumentenstammverzeichnisses | Gruppe des Dokumentenstammverzeichnisses |

Ein Benutzer oder eine Gruppe, die nicht existiert, bricht den Start ab. `root` wird abgelehnt, sofern es nicht erlaubt ist. Ist nichts konfiguriert und gehört das Dokumentenstammverzeichnis root, bleiben die Worker wie bisher root, und die Startmeldung sagt das. Die Verzeichnisse, die der Server für die Worker beschreibt (`Q.web.cache.dir`, `Q.web.appCache.dir`, `Q.webserver.precompress.dir`, `Q.webserver.writable`), werden beim Start diesem Benutzer übergeben. Der Worker-Benutzer muss den Quelltextbaum der Engine lesen können. Beim Start wird `Workers as: <user>:<group> (...)` ausgegeben.

## Zurücksetzen zwischen Anfragen

| Zustand | Wie |
|---|---|
| Statische Klasseneigenschaften | Momentaufnahme beim Start, wiederhergestellt über zwischengespeicherte Reflection-Handles (0,28 ms für 424 Klassen, 0,91 ms für 586) |
| `$_GET`, `$_POST`, `$_COOKIE`, `$_FILES`, `$_SERVER`, `$_REQUEST` | Aus der Anfrage neu befüllt |
| `$_SESSION` | `session_write_close()`, dann geleert |
| Ausgabepuffer | Ein Erfassungspuffer pro Prozess, bei jeder Anfrage geleert; Puffer, die ein Skript offen lässt, gehören zu seiner Antwort |
| Eigene globale Variablen | Momentaufnahme und Wiederherstellung (ausgenommen Superglobals und `keepGlobals`) |
| Shutdown-Funktionen, Fehler- und Exception-Handler, Autoloader, `ini_set`, `putenv` | Durch Shims ersetzt: pro Anfrage gesammelt oder verfolgt und auf den Zustand beim Booten zurückgesetzt |
| Antwort-Header | Gelöscht |
| Zyklischer Garbage | Zwischen Anfragen eingesammelt |

Gemessene Gesamtzeit: 0,55 ms im Median für die Seite mit einer Zeile und 4,6 ms für das CMS (etwa 1,5 Prozent eines Renderings von 300 ms). Was bewusst bestehen bleibt: OPcache, geladene Klassen, APCu, internierte Zeichenketten, Zuordnungen der Autoloader. Was Sorgfalt erfordert (Aufgabe der Anwendung): der Transaktionszustand von Datenbankverbindungen, Datei-Handles, Stream-Kontexte, Signal-Handler, cURL-Handles und Register in globalen Variablen, die einmal mit `include_once` befüllt werden (nennen Sie sie in `keepGlobals`).

### Warum Worker nicht wachsen (behoben in 0.0.4.27)

Vor 0.0.4.27 hinterließ jede Anfrage einen Ausgabepuffer (etwa 2 MB pro Anfrage auf einer Exponential-Installation: ein Worker bei 1,1 GB nach 600 Anfragen), jede Anfrage ließ ihren Fehler-Handler auf dem internen Handler-Stapel von PHP zurück (etwa 1 MB Debug-Protokoll pro Anfrage), der Datei-Wrapper verlor bei jedem Dateisystemaufruf eine Registrierung (bis zu 14.000 pro Anfrage), zyklischer Garbage wurde nicht eingesammelt (etwa 47 KB pro Anfrage), und sich beendende Worker löschten die PID-Datei des Servers. Alles ist behoben; eine Zustandsprüfung pro Anfrage (Ausgabestapel zurück auf den einen leeren Erfassungspuffer, Heap unter der Obergrenze) ersetzt einen Worker, der sie nicht besteht, und protokolliert den Grund. Zusammengenommen wächst ein Worker, der um 2 MB pro Anfrage wuchs, jetzt um etwa 2 KB.

## Die Kompatibilitätsschicht

Die Quelltexttransformation (Stream-Wrapper plus `token_get_all()`) schreibt Aufrufe von 44 Funktionen um, die sich in einem langlebigen CLI-Prozess anders verhalten: `header`, `setcookie`, `setrawcookie`, `http_response_code`, `headers_sent`, `headers_list`, `header_remove`, die Familie `session_*`, `move_uploaded_file`, `is_uploaded_file`, `ini_get`, `ini_set`, `set_time_limit`, `getallheaders`, `apache_request_headers`, `phpinfo`, `register_shutdown_function`, die Funktionen für Fehler- und Exception-Handler, `spl_autoload_register` und `unregister`, `putenv`, die Funktionen für Dateiinformationen (`file_exists`, `is_dir`, `is_file`, `filemtime`, `filesize`, `clearstatcache`) und die Prozessfunktionen (`exec`, `system`, `passthru`, `shell_exec`, `proc_close`, `pclose`). `exit` und `die` werden so umgeschrieben, dass sie die Anfrage beenden, nicht den Worker (auch nach einem Schlüsselwort oder Operator: `$db or die(...)`, `else exit;`).

Von der Schicht garantiertes Verhalten (jeweils in einer genannten Release behoben):

- Eine PHP-Datei, die mit `file_get_contents()`, `md5_file()` oder `fopen()` gelesen wird, liefert die eigenen Bytes der Datei; nur `include` und `require` werden transformiert (0.0.4.33; das behob, dass die Dateikonsistenzprüfung von Exponential etwa 330 Kernel-Dateien als geändert auflistete).
- Dateien, die nach dem Start neu geschrieben werden, werden ohne Neustart übernommen (der Wrapper macht die Kompilierung eines Skripts ungültig, wenn sich die mtime ändert; eingebundene Dateien werden einmal gelesen und behalten, höchstens 4 MB pro Prozess, solange ein echter stat-Aufruf in derselben Anfrage noch übereinstimmt).
- `compress.zlib://`, `gzopen()` und `copy()` in eine oder aus einer `.gz`-Datei funktionieren über die Dateischicht, ebenso Stream-Optionen (0.0.4.38).
- Multipart-Felder mit verschachtelten Namen erreichen `$_POST` so, wie PHP sie über `parse_str()` aufbaut; `$_FILES` hat den Aufbau von PHP (0.0.4.37).
- `session.save_path` wird in der PHP-Form `[N;[MODE;]]/path` gelesen, und Sitzungen werden mit dessen Modus angelegt (0.0.4.31); jeder Sitzungswert wird ohne Warnung aus seinen eigenen Bytes dekodiert (0.0.4.42).
- `REQUEST_TIME` und `REQUEST_TIME_FLOAT` werden pro Anfrage gesetzt (0.0.4.42).
- `Q.compat.statTtl` behält, was ein Worker über Dateien weiß (ob ein Pfad existiert, mtime, Größe), über Anfragen hinweg bis zu dieser Anzahl von Sekunden (wie `opcache.revalidate_freq`); gemessen wurden bei einer Sekunde 13 Prozent weniger CPU pro gerenderter Seite. Die eigenen Schreibvorgänge des Workers, `clearstatcache()` und ein Programm, das er ausführt, werden in jedem Fall sofort gesehen.

### Vorwärmen und Preload

`Q.webserver.warmup` nennt ein Skript, das der Pool einmal im Elternprozess ausführt, nachdem die Quelltexttransformation installiert ist und bevor er forkt (typischerweise rendert es eine repräsentative Seite); jeder Worker erbt den vorgewärmten Speicherbereich per Copy-on-Write (etwa 21 MB privat pro vorgewärmtem Worker gegenüber etwa 209 MB ohne Vorwärmen bei einer umfangreichen Anwendung). Verwenden Sie `Q.webserver.preload` nicht für die Anwendung: Es wird geladen, bevor die Transformation existiert, sodass die Worker ein echtes `exit` und ein `header()`, das nichts tut, erben würden (der Server warnt beim Start). Was auch immer das Vorwärmen hinterlässt, wird zum Ausgangszustand jedes Workers; deshalb setzt ein Vorwärmskript die statischen Eigenschaften von Benutzerklassen auf ihre Standardwerte zurück und leert die globalen Variablen der Anfrage, sodass nur Konfiguration und Typregister bleiben. Wirft ein Vorwärmen auf halbem Weg eine Exception, stellt `Q_WebServer_WarmupGuard` vor der Momentaufnahme den Zustand des Elternprozesses wieder her (0.0.4.36), sodass Worker nie jede Anfrage als die Anfrage des fehlgeschlagenen Renderings beginnen. Prüfen Sie ein Vorwärmen am Verhalten: Mehrere verschiedene URLs müssen verschiedene Inhalte liefern, jeweils mit einem auflösbaren Stylesheet.

## Grenzen

- Obergrenze der Event-Loop unterhalb von etwa 1.000 Workern (verwenden Sie stattdessen `reusePort` oder `Instances`).
- Ein POST wird nie wiederholt.
- Kein Fork unter Windows.
- Kernel-Caches pro Anfrage, die die Anfragezeit als Schlüssel verwenden, brauchen Engine 0.0.4.42 oder neuer (`REQUEST_TIME_FLOAT` pro Anfrage).

## Siehe auch

- Funktionen: [Velocity-Webserver](../../../eng-us/features/6.0/velocity-web-server.md), [Server mit persistenten Workern](../../../eng-us/features/6.0/velocity-persistent-worker-server.md), [Scheduler](../../../eng-us/features/6.0/velocity-scheduler.md), [Antwort-Cache](../../../eng-us/features/6.0/velocity-response-cache.md)
- Spezifikationen: [Engine-Einstellungen](velocity-engine-einstellungen.md) (Event-Loop, Vorwärm-Cache beim Start), [HTTP/2 und Sicherheit](velocity-http2-und-sicherheit.md)
- Upgrade und Betrieb: [Velocity-Engines](../../../eng-us/bc/6.0/velocity-engines.md) (`[ServerSettings]` in `settings/velocity.ini`), [Upgrade-Hinweise zur Engine](../../../eng-us/bc/6.0/velocity-engine-upgrade-notes.md)
- Geschichte: [August 2026](../../../eng-us/history/velocity/2026-08.md) (Octane), [7. bis 21. September](../../../eng-us/history/velocity/2026-09a.md) (Kompatibilitätsschicht), [23. September](../../../eng-us/history/velocity/2026-09c.md) (Speicherkorrekturen), [24. September](../../../eng-us/history/velocity/2026-09d.md), [25. bis 30. September](../../../eng-us/history/velocity/2026-09e.md) (Zygote, Worker ohne root); [Velocity-Changelog](../../../changelogs/extensions/exponential-velocity.md)
