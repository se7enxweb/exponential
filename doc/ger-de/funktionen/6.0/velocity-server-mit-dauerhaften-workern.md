# Velocity: Exponential in einem Webserver mit dauerhaften Workern betreiben

Diese Seite richtet sich an Administratoren einer Exponential-Installation, die sie unter Velocity betreiben möchten,
und sie ist der Einstieg zu allem, was Velocity in Exponential betrifft. Exponential wurde für die Arbeitsweise von
mod_php und PHP-FPM gebaut: ein PHP-Prozess pro Anfrage, der danach abgebaut wird. **Velocity**
(`./console exp:velocity`) betreibt dieselbe Installation unter einem modernen Anwendungsserver, der einmal startet
und einen Pool von Workern am Leben hält. Das Ergebnis ist eine Website, die zwischengespeicherte Seiten mit deutlich
unter einer Millisekunde CPU-Zeit beantwortet und nicht zwischengespeicherte Seiten mit einer warmen
Datenbankverbindung, warmen Datei-Caches und einem warmen Opcode-Cache rendert.

Im Folgenden: was zwischen dem 22. und 30. September 2026 hinzukam, wie Sie es starten, welche Einstellungen zuerst
wichtig sind und wo die ausführlichen Anleitungen stehen.

## Wozu Sie es verwenden

| Sie möchten | Velocity bietet Ihnen |
|---|---|
| Eine schnelle Website, ohne Apache oder Nginx zu installieren | Einen mitgelieferten Server (die Qbix-Engine), FrankenPHP oder den eingebauten Server von PHP, alle mit denselben Befehlen gestartet. |
| HTTPS ab dem ersten Start | Ein selbstsigniertes Zertifikat wird für Sie erzeugt; eigene Zertifikate werden verwendet, wenn Sie sie angeben. |
| HTTP/2 | Im mitgelieferten Server eingebaut (`[ServerSettings] HTTP2=enabled`). |
| Ganze Seiten beantwortet, bevor der Kernel startet | Den rollenbewussten HTTP-Cache und den Antwort-Cache des Servers. |
| Weniger Datenbankabfragen pro Seite | Den SQL-Abfrage-Cache (`settings/querycache.ini`). |
| Ein Befehl, um PHP-Änderungen auszurollen | `./console exp:velocity deploy`. |

Velocity ist die empfohlene Engine für jede Stufe (Entwicklung, Alpha, Beta,
Demo, Stable, Produktion). FrankenPHP ist die produktionsreife Alternative, und
der eingebaute Server von PHP dient nur der Entwicklung. Der ausgelieferte Standard in
`settings/velocity.ini` ist `Engine=php`, weil er keine Installation erfordert;
ändern Sie ihn für jede echte Website auf `qbix`.

## Starten, prüfen, stoppen

```bash
./console exp:velocity start             # default engine, shows status straight away
./console exp:velocity status            # every engine: role, state, URL, stop command
./console exp:velocity status --all      # complete block for each engine, stopped ones too
./console exp:velocity restart
./console exp:velocity graceful          # reload workers one at a time, port keeps answering
./console exp:velocity stop
./console exp:velocity --help
```

Nützliche Schalter (alle von `--help` dokumentiert):

| Schalter | Wirkung |
|---|---|
| `--engine=<php\|qbix\|frankenphp>` | Steuert für diesen Befehl eine Engine. |
| `--all` | Steuert jede Engine. |
| `--https` / `--no-https` | HTTPS für einen Start (FrankenPHP liefert standardmäßig HTTPS mit einem selbstsignierten Zertifikat aus). |
| `--json` | Maschinenlesbare Ausgabe, wo das Verb sie unterstützt. |

GNU- und BSD-Schreibweisen funktionieren (`--keep-global V`, `-keep-global=V`, `-json`,
`--no-json`); alles nach `--` wird unverändert an die Engine übergeben.

`status` listet jede zu öffnende Adresse als anklickbare URL auf, die HTTPS-Adresse
neben jeder einfachen, und die Backend-Seiten (Anmeldung zur Administration, **Setup >
Systeminformationen**, **Setup > Caches**). Unter jeder Engine zeigt es den Prozess,
die Version, die Konfiguration, die Log-Pfade und ob APCu eingeschaltet ist und für
welche Caches.

### Engines nebeneinander

Jede Engine hat ihren eigenen Port, ihre eigene PID-Datei und ihre eigenen Logs, sodass alle drei für Tests
gleichzeitig laufen können.

| Engine | Standard-Port | HTTPS-Port | Abschnitt in `velocity.ini` |
|---|---|---|---|
| php (eingebauter Server) | 8087 | keiner | `[PHPServerSettings]` |
| qbix (mitgelieferter Server) | 8088 | `[ServerSettings] HTTPSPort` (8080) | `[ServerSettings]` |
| frankenphp | 8089 | 8444 | `[FrankenPHPSettings]` |

Alles, was Velocity für einen Server schreibt, liegt gemeinsam unter
`var/vc/<engine>/`: `etc/`, `lib/`, `log/` und `run/` für die Qbix-Engine;
`bin/`, `caddy/`, `tls/`, `log/` und `run/` für FrankenPHP. Siehe
[FrankenPHP und der eingebaute Server von PHP](../../bc/6.0/frankenphp.md) und
[Velocity-Engines](../../bc/6.0/velocity-engines.md).

## Einstellungen, die Sie zuerst kennen sollten

Alle folgenden Schlüssel stehen in `settings/velocity.ini`. Geltungsbereich ist die gesamte Installation
(Velocity liest die Datei beim Start; `exp:velocity` verwirft eine veraltete zwischengespeicherte Kopie,
bevor es die Server-Konfiguration erstellt, sodass eine Änderung beim nächsten
`start`, `restart` oder `graceful` gelesen wird).

| Block | Schlüssel | Standard | Wirkung |
|---|---|---|---|
| `[ServerSettings]` | `Engine` | `php` | Engine, die die Befehle steuern, wenn kein `--engine` angegeben ist. |
| `[ServerSettings]` | `Workers` | `4` | Worker-Prozesse des mitgelieferten Servers. |
| `[ServerSettings]` | `Instances` | `1` | Anzahl der Server, die sich dieselben Ports teilen. Jeder beantwortet auf einem Kern etwa 3.000 bis 3.500 zwischengespeicherte Seiten pro Sekunde, sodass `Instances=N` zwischengespeicherte Seiten N Kerne nutzen lässt. |
| `[ServerSettings]` | `ForkPerRequest` | `disabled` | `enabled` spaltet für jede Anfrage einen frischen Worker ab. Seit dem 30. September werden Worker zwischen Anfragen behalten, was angemeldete Admin-Seiten in der Messung, die zu der Änderung führte, von 256 bis 284 ms auf 73 bis 80 ms verkürzte. |
| `[ServerSettings]` | `User`, `Group`, `AllowRootWorkers` | leer, leer, `disabled` | Worker geben nach dem Start die Root-Rechte ab und laufen als dieser Benutzer (der Benutzer der Website, wenn leer), sodass Dateien, die sie in `var/` schreiben, der Website gehören und nicht root. |
| `[ServerSettings]` | `StatTtl` | `0` | Sekunden, die sich ein Worker Ergebnisse von Datei-stat-Aufrufen über Anfragen hinweg merken darf. Mit `1` wurden 13 Prozent weniger CPU pro gerenderter Seite gemessen. |
| `[ServerSettings]` | `Zygote` | leer | Ob Worker, die nach dem Pool gestartet werden, von der Zygote (hält keine Besucherverbindung) oder vom Server abgespalten werden. Leer behält den Standard der Engine. |
| `[ServerSettings]` | `EnginePhar` | `disabled` | Die Engine aus `dist/engine.phar` ausführen. Siehe [Engine-Archiv](../../bc/6.0/phar.md). Es macht Seiten nicht schneller. |
| `[ServerSettings]` | `StaticMaxAge` | `31536000` | Sekunden, die ein Browser eine statische Datei behalten darf, ohne erneut nachzufragen. |
| `[ServerSettings]` | `HTTP2` | `enabled` | HTTP/2 für HTTPS-Verbindungen. |
| `[ServerSettings]` | `ResponseHeaders[]` | vier Sicherheits-Header | Header, die statischen Dateien, Fehlerseiten, Weiterleitungen und 304-Antworten hinzugefügt werden. |
| `[ServerSettings]` | `ResponseHeadersOnScripts` | `enabled` | Dieselben Header bei Antworten von `index.php`, `index_rest.php` und `index_treemenu.php`, nur wo das Skript sie nicht gesendet hat. |
| `[HTTPSSettings]` | `HSTSMaxAge`, `HSTSIncludeSubDomains`, `HSTSPreload` | `300`, `disabled`, `disabled` | `Strict-Transport-Security` bei jeder HTTPS-Antwort, nie bei einfachem HTTP. `0` schreibt nichts. |
| `[CacheSettings]` | `Enabled` | `enabled` | Der eigene Antwort-Cache des Servers. `disabled` schaltet ihn wirklich aus (seit dem 28. September; vorher fiel ein deaktivierter Wert auf den Standard des Servers zurück). |
| `[PHPSettings]` | `IniOptions[]` | Optionen für opcache und APCu | PHP-Optionen, die an den Serverprozess übergeben werden, eine pro Zeile, wie nach `-d`. Ausgeliefert: `opcache.enable_cli=1`, `opcache.memory_consumption=256`, `opcache.max_accelerated_files=20000`, `opcache.interned_strings_buffer=32`, `opcache.revalidate_freq=0`, `opcache.file_update_protection=0`, `apc.shm_size=256M` (die vollständige Liste und die Gründe stehen in `settings/velocity.ini`). |

Zwei dieser Optionen wurden durch Messen gefunden und sind wichtig, wenn Sie Ihren eigenen
Server abstimmen. **`opcache.file_update_protection=0`**: Die Anfrage eines dauerhaften Workers
beginnt, wenn der Worker startet, sodass jede danach geschriebene Datei (INI-Caches,
kompilierte Templates, Überschreibungs-Caches nach einem Deployment oder dem Leeren eines Cache)
während der gesamten Lebensdauer des Workers als „zu neu“ galt und bei jedem Include neu kompiliert wurde;
der Opcode-Cache bediente 2,7 Prozent der Includes, wo PHP-FPM 97 Prozent bedient,
und eine gerenderte Seite kostete etwa das Vierfache der CPU-Zeit von PHP-FPM. **`apc.shm_size=256M`**:
Der Kommandozeilen-Standard von 32 MB ließ den Antwort-Cache und den SQL-Abfrage-Cache
sich gegenseitig verdrängen (gemessen am 27. September: 51 Prozent Trefferquote des Abfrage-Cache und
727 ms CPU für eine gerenderte Startseite mit 32 MB, 303 ms mit 256 MB). Der Speicher
wird reserviert, nicht belegt, bis er sich füllt. Prüfen Sie, womit Ihr Server läuft:
`grep -n 'IniOptions' settings/velocity.ini`.

Die einzige Änderung, die die Website in der ersten Runde messbar deutlich schneller machte,
war ein Opcode-Cache für den Serverprozess (18 bis 26 Prozent); deshalb
liefert `IniOptions[]` `opcache.enable_cli=1` und `apc.shm_size=256M` mit. Drei weitere
Ideen wurden gemessen und als nicht wiederholenswert festgehalten: die Engine
aus einem Archiv zu laden, reguläre Ausdrücke im Stream-Wrapper des Servers
durch String-Funktionen zu ersetzen und der Tracing-JIT. Wenn Sie messen, wechseln Sie die Anfragen
zwischen den verglichenen Servern ab, statt sie nacheinander zu messen.

Ändern und lesen Sie Einstellungen mit dem Befehl, statt von Hand zu bearbeiten:

```bash
./console exp:velocity config list ServerSettings
./console exp:velocity config get ServerSettings Workers
./console exp:velocity config set ServerSettings Workers 8
./console exp:velocity config unset ServerSettings Workers
./console exp:velocity config paths      # which of the three look-alike files is the real one
```

## Die Caches, die Velocity nutzen kann

Velocity arbeitet am besten mit Caches, die vor dem Renderer gestapelt sind.

1. **Browser-Cache** für anonyme Besucher: `StaticMaxAge` und die Seiten-Header
   (siehe [HTTP-Caching für anonyme Besucher](../../bc/6.0/http-caching.md)).
2. **Antwort-Cache des Servers** (`[CacheSettings]`): ganze anonyme Seiten, mit
   `StaleWhileRevalidate=60`, sodass eine ablaufende Seite von einer Anfrage gerendert wird,
   während die anderen die gespeicherte Kopie erhalten, `NotFoundSeconds=0`, sodass jede fehlende
   Seite PHP und seine Logs erreicht, `HonorRequestNoCache`, sodass auch eine `no-cache`-Anfrage
   dies tut, und `MinifyHtml`.
3. **Rollenbewusster HTTP-Cache** (`settings/httpcache.ini`, standardmäßig aus):
   gerenderte Seiten, pro Berechtigungskontext gespeichert und beantwortet, bevor der Kernel
   startet, auch für angemeldete Besucher. Siehe [HTTP-Cache](../../bc/6.0/httpcache.md).
4. **SQL-Abfrage-Cache** (`settings/querycache.ini`, standardmäßig `Mode=off`): siehe
   [SQL-Abfrage-Cache](../../bc/6.0/sql-abfrage-cache.md).
5. **Cache-Aufwärmer** (`exp:warm`): durchläuft die veröffentlichten Inhalte als anonymer
   Besucher, damit niemand für das erste Rendern nach einem Ablauf bezahlt.

APCu ist der gemeinsame Speicher, den der HTTP-Cache und der SQL-Abfrage-Cache verwenden. Der
mitgelieferte Server läuft unter Kommandozeilen-PHP, wo APCu ausgeschaltet ist, sofern es nicht angefordert wird,
deshalb fordert Velocity es an, sobald einer der drei Caches es braucht (vor dem 28.
September nahm das Ausschalten des Antwort-Cache den beiden anderen stillschweigend APCu weg).
`status` gibt an, welche Caches APCu erhalten und warum.

Leeren Sie nach einer Änderung an einem Template oder Stylesheet jeden Cache:

```bash
./console exp:velocity cache clear
```

Für die Schaltflächen unter **Setup > Caches** auf der Kommandozeile siehe
[Cache-Konsole](../../bc/6.0/cache-konsole.md).

## Eine PHP-Änderung mit einem Befehl ausrollen

Eine PHP-Änderung braucht die Autoloads, die INI- und Template-Caches, das Engine-Archiv, ein Neuladen von PHP-FPM, einen Neustart von Velocity und danach die Caches, die gerenderte Ausgabe enthalten, in dieser Reihenfolge. Ein Befehl erledigt alles und gibt für jeden Schritt PASS, FAIL oder SKIP aus:

```bash
./console exp:velocity deploy --dry-run     # what it would do, nothing else
./console exp:velocity deploy               # the usual case
./console exp:velocity deploy --kernel      # a kernel class was added or renamed
```

Die Tabelle der Schritte, die Gründe für die Reihenfolge, jede Option und ein Beispiellauf stehen unter [Velocity-Engines: eine PHP-Änderung ausrollen](../../bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy).

## Weitere Befehle

| Befehl | Zweck |
|---|---|
| `./console exp:velocity ssl show` / `ssl renew` | Die Zertifikate anzeigen, die HTTPS verwendet (`--json` für die Überwachung); ein neues selbstsigniertes erzeugen, das ein laufender Server ohne Neustart übernimmt. |
| `./console exp:velocity ext check` | Meldet, welche PHP-Extensions des Standardsatzes fehlen, mit dem Installationsbefehl für dieses Betriebssystem. |
| `./console exp:velocity layout` / `layout migrate` | Der Konfigurationsbaum im Stil von Debians Apache. Siehe [Verzeichnisstruktur von Velocity](../../bc/6.0/velocity-verzeichnisstruktur.md). |
| `./console exp:velocity site\|conf\|mod enable\|disable <name>` | Aktivieren und deaktivieren wie mit `a2ensite`. |
| `./console exp:webserver` | Engine-unabhängiges Frontend: konfiguriert beim ersten Start Engine, Port und Document Root. |
| `./console exp:frankenphp` | Wrapper, der auf die FrankenPHP-Engine festgelegt ist. |
| `./console exp:phar build\|info\|clean` | Das Engine-Archiv. |
| `./console exp:vc` | Vorgesehener Kurzalias von `exp:velocity`. Die Konsole liest einen `@alias <name>`-Tag aus dem Docblock von `bin/php/<script>.php`; seit der Befehlscode nach `kernel/private/classes/commands/` umgezogen ist, stehen die Tags (`vc`, `fp` für `exp:frankenphp`, `search` für `exp:solr`) dort, und `bin/php/velocity.php` hat keinen, sodass `./console exp:vc` in diesem Baum „Command exp:vc was not found“ antwortet (geprüft am 2. Oktober 2026). Verwenden Sie `exp:velocity`. Prüfen Sie es erneut mit `./console exp:vc --help`. |

Die Programme der Qbix-Engine sind mit dem Engine-Release
0.0.4.41 nach `sbin/` und `bin/` umgezogen; `exp:velocity` findet beide Strukturen. Das Engine-Paket wurde
von `se7enxweb/qbix-webserver` in `se7enxweb/exponential-velocity` umbenannt; ein älterer
Checkout unter dem früheren Verzeichnisnamen wird weiterhin gefunden.

## Was sich in der Anwendung ändern musste

Ein dauerhafter Worker behält, was eine Anfrage hinterlässt. Dasselbe Release
behob daher unter anderem: statische Funktionsvariablen, die die Asset-Pfade eines Siteaccess
in die nächste Anfrage trugen (jetzt statische Klasseneigenschaften, die der Worker zurücksetzt), doppelt
registrierte Event-Listener, SQL-Profilzähler, die nie zurückgesetzt wurden, REST-Anfragen,
die mit „Cannot redeclare“ abbrachen, das Warm-up, das Sprache, Einstellung für versteckte Knoten und
Template-Pfade der öffentlichen Website in jedem Worker hinterließ,
und den Phar-Stream-Wrapper, der für öffentliche Anfragen registriert blieb (ein
Weg vom Upload zur Ausführung unter Kommandozeilen-PHP; geschlossen). Die Seite
**Systeminformationen** nennt jetzt die Engine, ihr Release, ihre Caches und APCu, und
jede Log-Zeile enthält den Siteaccess und die vollständige Adresse der Anfrage.

### Aufräum-Handler und Shutdown-Funktionen, einmal pro Anfrage

Die Aufräum- und Fatal-Error-Handler von `eZExecution`, sein Flag für das saubere Beenden und die
Shutdown-Funktionen, die es registriert (sein eigener Aufräum-Handler und
`eZSession::stop()`), gehören zu einer Anfrage. Zwischen Anfragen setzt ein Worker die statischen
Eigenschaften jeder Klasse auf den Stand nach seinem Warm-up zurück, und er führt die Shutdown-Funktionen,
die eine Anfrage registriert hat, aus und vergisst sie dann. Bis zu diesem Release registrierte ein
Worker daher ab seiner zweiten Anfrage keine Shutdown-Funktion mehr
(kein Aufräumen und keine Fehlerseite nach einem Fatal Error, kein `eZSession::stop()`), und
jede Anfrage führte die Handler des Warm-ups neben ihren eigenen aus. Jetzt beginnt der erste
`eZExecution`-Aufruf einer Anfrage mit dem Zustand eines frischen Prozesses, ein
zweimal in einer Anfrage hinzugefügter Handler läuft einmal, und eine Shutdown-Funktion einer früheren
Anfrage (die des Warm-ups, in der ersten Anfrage eines Workers) tut nichts. Unter
Apache/PHP-FPM und auf der Kommandozeile bedient ein Prozess eine Anfrage, dort ändert sich
also nichts.

Für Entwickler: Sichern Sie eine Registrierung, die einmal pro Anfrage geschehen muss, mit
`eZExecution::requestKey()` (dem `REQUEST_TIME_FLOAT` der Anfrage) ab, nie mit einem
`true`-Flag, das ein Worker von seinem Warm-up an für immer behält:

```php
if ( self::$registered !== eZExecution::requestKey() )
{
    self::$registered = eZExecution::requestKey();
    register_shutdown_function( array( __CLASS__, 'flush' ) );
}
```

`eZExecution::addCleanupHandler()` und `addFatalErrorHandler()` dürfen auch einfach
bei jeder Anfrage aufgerufen werden: Derselbe Handler wird nur einmal behalten.

Ab Exponential Velocity 0.0.4.51 beendet die Engine außerdem die Anfrage des Warm-ups, bevor
sie ihre Worker abspaltet: Die Shutdown-Funktionen, die das Rendern des Warm-ups registriert hat, werden
verworfen, ohne zu laufen, und die Header, Fehler- und Exception-Handler, die Session,
die ini- und Umgebungsänderungen und die Ausgabepuffer, die es hinterließ, werden zurückgesetzt. Das gilt
für jede Anwendung auf der Engine; `eZExecution::runForRequest()` bleibt die
eigene Absicherung des Kernels, für eine ältere Engine. Auch das `$_SESSION` einer Anfrage endet mit
der Anfrage, wie unter PHP-FPM.

## Grenzen und Vorsichtsmaßnahmen

- Der Antwort-Cache speichert nur anonyme Seiten. Eine Seite mit einem Formular-Token wird
  als `private` gekennzeichnet, damit ein gemeinsamer Cache sie nie speichert.
- Eine Klasse, die einer INI-Datei hinzugefügt wird, während Worker laufen, ist Workern, die gestartet wurden,
  bevor es sie gab, nicht bekannt. Führen Sie nach einer solchen Änderung `exp:velocity deploy` aus.
- Mischen Sie bei einem Start nicht die alten und neuen Engine-Pfade.

## Verwandte Seiten

- Anleitungen: [Velocity-Engines](../../bc/6.0/velocity-engines.md), [HTTP/2 und Cache-Aufwärmen](../../bc/6.0/http2-und-cache-vorwaermen.md), [Antwort-Cache und Navigations-Cache](../../bc/6.0/antwort-cache-und-navigation.md), [HTTP-Cache](../../bc/6.0/httpcache.md), [SQL-Abfrage-Cache](../../bc/6.0/sql-abfrage-cache.md)
- Funktionen: [Velocity-Webserver](velocity-webserver.md), [Velocity-Antwort-Cache](velocity-antwort-cache.md), [Opcode-Cache und Profil](velocity-opcode-cache-und-profil.md), [Befehle zur Serversteuerung](befehle-fuer-webserver-und-solr.md), [Wartungsmodus](wartungsmodus.md) (Velocity pausiert seine Caches, solange er eingeschaltet ist)
- Spezifikationen: [Velocity-Worker-Pool](../../spezifikationen/6.0/velocity-worker-pool.md), [Velocity-Engine-Einstellungen](../../spezifikationen/6.0/velocity-engine-einstellungen.md), [Sicherheitsvorgaben vom September 2026](../../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../bc/6.0/verhaltensaenderungen-2026-09b.md)
- Changelogs: [6.0.15](../../../changelogs/6.0/6.0.15.md), [Exponential-Velocity-Engine](../../../changelogs/extensions/exponential-velocity.md)
- [Chronik, 16. bis 30. September 2026](../../geschichte/2026/2026-09b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
