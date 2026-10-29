# Velocity-Engines — Qbix, FrankenPHP, der eingebaute Server von PHP

Lesen Sie diese Seite, wenn Sie Exponential unter Velocity betreiben (`exp:velocity`, `vc`), zwischen seinen Engines
wählen oder PHP-Änderungen auf einer Website bereitstellen, die Velocity ausliefert. Velocity steuert einen von drei
Webservern mit denselben Verben (`start`, `stop`, `graceful`, `restart`, `kill`, `status`, `deploy`, `command`,
`config`, `install`) und derselben `velocity.ini`.

## Velocity ist seit 6.0.15 Teil von Exponential

Seit 6.0.15 **verlangt** die `composer.json` im Wurzelverzeichnis `se7enxweb/exponential-velocity ~0.0.4.55` (über `se7enxweb/exponential-php-floor`, auf PHP 8.0 und neuer); vorher
schlug sie das Paket nur vor. Was sich dadurch für eine bestehende Installation ändert:

| | |
|---|---|
| Was sich ändert | `composer install` installiert das Velocity-Paket mit dem Rest von Exponential, die Engine `qbix` ist also immer verfügbar; `exp:velocity start --engine=qbix` bricht auf einer frischen Installation nicht mehr mit `velocity: no server script at ...` ab. |
| PHP | Velocity 0.0.4.54 läuft auf PHP 8.0 bis 8.5, demselben Bereich wie der Kernel; er braucht `ext-sockets` (`pcntl` und `openssl` empfohlen). |
| Was sich nicht ändert | Die ausgelieferte Standard-Engine bleibt `php`; eine Website, die Apache, nginx oder FrankenPHP ausliefert, wird weiter so ausgeliefert. Nichts startet von sich aus einen Server. |
| So prüfen Sie es | `php bin/php/console exp:velocity status` meldet das Engine-Paket und seine Version. |
| Was zu tun ist | Nichts außer `composer install`. Um den Server zu verwenden, setzen Sie `Engine=qbix` und stellen mit `exp:velocity deploy` bereit. |

Das ist der Wendepunkt der Linie 6.0: Der Internet- und Webanwendungsserver ist ein Eckpfeiler des Kernprodukts, und
der ganze Weg von einer Anfrage bis zur fertig dargestellten Seite gehört zu einem Produkt. Velocitys eigener Server
ist die empfohlene Engine für jede Stufe; FrankenPHP ist die produktionsreife Alternative. Die Geschichte für jeden
Leser: [Velocity ist Teil von Exponential 6.0.15](../../hilfe/velocity-ist-teil-von-exponential-6-0-15.md).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | `velocity.ini [ServerSettings] Engine` wählt die Standard-Engine: `php` (ausgeliefert), `qbix` (empfohlen) oder `frankenphp`. `exp:velocity deploy` führt jeden Schritt aus, den eine PHP-Änderung braucht, in der richtigen Reihenfolge, mit PASS, FAIL oder SKIP. Mehrere Qbix-Server können sich einen Port teilen (`Instances`). |
| Wer betroffen ist | Jeder Benutzer von Velocity. Eine geänderte PHP-Klasse erreicht die Worker von Velocity erst nach einem Neustart; `deploy` erledigt das in der richtigen Reihenfolge. Das PHP-FPM, das die Website ausliefert, muss in `[DeploySettings] PhpFpmService` genannt werden, wenn `auto` es nicht findet. |
| Wie Sie es prüfen | `php bin/php/console exp:velocity status` und `php bin/php/console exp:velocity deploy --dry-run --allow-root-user` |
| Wie Sie es beheben | Setzen Sie `Engine=qbix` für jede echte Website; stellen Sie PHP-Änderungen mit `exp:velocity deploy` bereit (fügen Sie `--kernel` hinzu, wenn eine Kernel-Klasse hinzugefügt oder umbenannt wurde). |

```ini
[ServerSettings]
Engine=php          # shipped default: development, always works
Engine=qbix         # recommended: every stage, development to production
Engine=frankenphp   # production-ready alternative
```

`Engine` ist die **Standard**-Engine — die, die `start`, `stop` und `status` ohne `--engine` verwenden. Ausgeliefert
wird `php`, weil der eingebaute Server von PHP nichts braucht, was einer Installation fehlen könnte; jede Website, von
der Entwicklung bis zur Produktion, setzt `qbix` (den eigenen Server von Velocity), die empfohlene und schnellste
Engine. Wofür eine Engine *gedacht* ist (ihre Rolle), ändert sich dadurch nicht.

| | `qbix` | `frankenphp` | `php` |
|---|---|---|---|
| Rolle | **empfohlen**: jede Phase, von der Entwicklung bis zur Produktion | produktionsreife Alternative | Entwicklung (ausgelieferter Standard) |
| Port (ausgeliefert) | 8088 (`[ServerSettings]`) | 8089, HTTPS 8444 | 8087 |
| Was | der mitgelieferte Qbix-Server, ein PHP-Prozess | FrankenPHP: Caddy mit eingebautem PHP, eine Binärdatei | `php -S`, der eigene Server von PHP |
| Woher sie kommt | Composer (`se7enxweb/exponential-velocity`, früher `se7enxweb/qbix-webserver`) | von `exp:velocity install` heruntergeladen, SHA-256 geprüft | das PHP, das `exp:velocity` ausführt |
| PHP | das der Maschine | das eigene der Binärdatei (offizielles 1.12.x: PHP 8.5) | das der Maschine |
| Anfragen | persistente Worker oder Worker pro Anfrage | klassischer Modus: ein Thread-Pool, sauberer Zustand pro Anfrage | `Workers` Prozesse, sauberer Zustand pro Anfrage |
| Antwort-Cache | ja (`[CacheSettings]`) | noch nicht | nein |
| TLS / HTTP/2 | ja | ja | nein |
| graceful | erneutes exec, Socket bleibt erhalten | Neuladen der Konfiguration, keine abgebrochene Verbindung | Neustart |
| Konfigurationsbaum `/etc/vc` | ja | nein — ein erzeugtes Caddyfile | nein — ein Router-Skript |

Schalten Sie einen Befehl mit `--engine` auf eine andere Engine um, mehrere mit `--engine=php,qbix`, alle mit `--all`.

## Eine PHP-Änderung bereitstellen (`exp:velocity deploy`)

Ein Befehl erledigt alles, was eine geänderte PHP-Klasse, ein geänderter Operator oder eine geänderte INI-Datei
braucht, bevor sie live ist, in der einen Reihenfolge, die funktioniert, und meldet für jeden Schritt PASS, FAIL oder
SKIP mit seiner Dauer:

```bash
php bin/php/console exp:velocity deploy --allow-root-user            # the usual case
php bin/php/console exp:velocity deploy --kernel --allow-root-user   # a kernel class was added or renamed
php bin/php/console exp:velocity deploy --dry-run --allow-root-user  # what it would do, and nothing else
```

| # | Schritt | Von Hand |
|---|---|---|
| 1 | Autoloads der Erweiterungen | `php bin/php/ezpgenerateautoloads.php -e` |
| 2 | Autoloads des Kernels, nur mit `--kernel` | `php bin/php/ezpgenerateautoloads.php -k`, beschränkt auf `kernel/` und `lib/` |
| 3 | INI-Caches und der kompilierte Kernel-Service-Container (Cache-Eintrag `kernelservices`, [Kernel-Dienste](kernel-dienste.md)) | `php bin/php/ezcache.php --clear-tag=ini` (`./console exp:services:compile` nur für den Container) |
| 4 | Caches template, template-override, translation und design_base | `--clear-id=template`, `--clear-id=template-override`, `--clear-id=translation`, `--clear-id=design_base`, eine ID pro Aufruf |
| 5 | Engine-Archiv, wenn Velocity aus einem läuft | `php bin/php/phar.php build`: nur neu gebaut, wenn sich eine Datei in `kernel/`, `lib/` oder `autoload/` geändert hat, jede Datei wird vorher geparst |
| 6 | PHP-FPM: warten, bis sein OPcache die geänderten Dateien neu validiert hat (kein Neuladen), oder es neu laden, wenn darauf kein Verlass ist | `sleep <revalidate_freq + 1>`, oder `systemctl reload <[DeploySettings] PhpFpmService>` |
| 7 | Velocity neu starten, wenn er läuft | `exp:velocity restart` |
| 8 | Caches content, exphttpcache, ezjscore-packer (nur mit `--packer`) und template-block | `--clear-id=content`, `--clear-id=exphttpcache`, `--clear-id=ezjscore-packer`, `--clear-id=template-block` |
| 9 | der Antwort-Cache von Velocity | `exp:velocity cache clear` |
| 10 | Vorwärmen: `[DeploySettings] WarmUpURL[]`, jede einmal von einem Hintergrundprozess angefragt | `curl URL...` |

**`config.php` zählt als PHP-Änderung.** Die persistenten Worker der qbix-Engine lesen sie einmal, wenn sie starten.
Führen Sie nach `./console exp:config:set <NAME> <Wert> --apply` (oder `exp:setup:config --force --apply`)
`exp:velocity restart` aus, oder `deploy`, das in Schritt 7 neu startet. FrankenPHP im klassischen Modus und PHP-FPM
lesen sie bei jeder Anfrage, sobald OPcache die neue Datei sieht; bei `opcache.validate_timestamps=0` laden Sie PHP-FPM
neu ([.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md)).

**Auf die Reihenfolge kommt es an.** Die Caches mit gerenderter Ausgabe (8 und 9) werden erst geleert, wenn PHP-FPM
und Velocity den neuen Code ausführen. Werden sie vorher geleert, wird jede in der Zwischenzeit angefragte Seite vom
alten Code gerendert und erneut gecacht, und die Änderung sieht aus, als hätte sie nicht funktioniert.

**Der erste fehlgeschlagene Schritt beendet den Lauf.** Eine Kernel-Datei, die sich nicht parsen lässt, lässt
Schritt 5 fehlschlagen, bevor PHP-FPM oder Velocity berührt wurden; ein Neustart, der nicht hochkommt, lässt jeden
Cache, wie er war. Der Exit-Status ist 1, und die nicht ausgeführten Schritte werden als `NOT RUN` aufgeführt.

**PHP-FPM wird nur neu geladen, wenn es sein muss** (`[DeploySettings] PhpFpmReload=auto`). Ein Neuladen beschränkt
sich nicht auf diese Website: Der Master bittet jedes Kind jedes Pools, seine Anfrage zu beenden, und startet kein
neues Kind, bis das letzte fertig ist, für bis zu `process_control_timeout` (300 s unter Plesk). Jede Anfrage, die in
der Zwischenzeit eintrifft, auf jeder Website dieser PHP-Version, wartet auf die längste noch laufende Anfrage.
Gemessen auf einem Host: Ein offener Event-Stream von 25 s ließ die nächste Seite nach einem Neuladen 22,4 s warten,
und eine ohne eigenen Stream wartete immer noch 3,9 s auf die Anfragen anderer Websites; eine Anfrage von 135 s an
anderer Stelle machte aus den wartenden 504er. Validiert der OPcache des Pools Dateien neu
(`opcache.validate_timestamps` eingeschaltet, `revalidate_freq` höchstens 60 s, gelesen aus `php-fpm -i` und der
Pool-Datei), wird jede geänderte PHP-Datei ohnehin innerhalb von `revalidate_freq` Sekunden neu kompiliert; Schritt 6
wartet daher so lange plus eine Sekunde, und die Caches gerenderter Ausgabe werden trotzdem erst danach geleert.
`--fpm-reload` (oder `PhpFpmReload=always`) lädt trotzdem neu: nach einer Änderung an php.ini oder am Pool, einer
neuen PHP-Erweiterung oder PHP-Dateien, die an Ort und Stelle entfernt oder umbenannt wurden.

**Der erste Besucher nach einem Deploy baut die Caches nicht neu.** Schritt 10 fragt jede
`[DeploySettings] WarmUpURL[]` einmal an (nacheinander, durch ein abgekoppeltes `curl`, ohne darauf zu warten): Eine
Startseite und eine Anmeldeseite der Administration pro Engine kompilieren die Pagelayouts und bauen die Caches für
INI, Overrides, Übersetzungen, Designs und gepackte Dateien neu, bevor jemand fragt, und mehrere erste Besucher bauen
sie nicht mehr alle gleichzeitig.

**design_base wird immer geleert.** Er enthält die Liste der Design-Verzeichnisse, wie sie beim letzten Lesen der
Erweiterungen war; nach dem Aktivieren einer Erweiterung werden ihre Templates daher erst gefunden, wenn er geleert
ist: Die Seite antwortet mit 200 und einem leeren Inhaltsbereich.

**Die gepackten Skripte und Styles gehen mit den Template-Blöcken.** Der Kopf der Seiten der Administration wird in
einem Template-Block gehalten und nennt die gepackten Dateien. Leeren Sie `ezjscore-packer` allein, verweisen diese
Köpfe auf Dateien, die es nicht mehr gibt: Die Administration verliert ihre Skripte und Styles, bis auch
`template-block` geleert wird. `--packer` leert ihn daher in Schritt 8, unmittelbar vor `template-block`, und nirgends
sonst. Er wird nur gebraucht, wenn sich geändert hat, was eine Server-Funktion des Packers zurückgibt (eine Klasse
`ezjscServer_*` oder die Einstellungen, die sie liest); eine bearbeitete `.js`- oder `.css`-Datei erhält von selbst
eine neue gepackte Datei, da der Name der gepackten Datei den Zeitstempel der Quelldateien trägt.

Optionen: `--kernel`, `--no-autoload`, `--no-fpm`, `--fpm-reload` (PHP-FPM auch dann neu laden, wenn sein OPcache neu
validiert), `--no-velocity`, `--rebuild-phar` (das Engine-Archiv auch dann neu bauen, wenn es aktuell ist), `--packer`
(die gepackten Skripte und Styles leeren, zusammen mit den Template-Blöcken), `--engine=<name>` (die neu zu startende
Engine), `--dry-run`, `--json`.

Der Bau des Archivs prüft jeweils 200 Dateien mit einem Aufruf von `php -n -l` statt mit einem Prozess pro Datei. Bei
1.049 Dateien dauerte das bisher neun Minuten pro Bau; jetzt dauern die Prüfungen etwa zwei Sekunden und der ganze Bau
etwa 26. Ein Bau schreibt außerdem `dist/engine.phar.index.json`, sodass `exp:phar build` sowie jeder Start, Neustart
und jedes graceful nur dann neu bauen, wenn sich etwas im Archiv geändert hat.

```
velocity deploy: /var/www/vhosts/example.com/doc/example.com
  [ 1/14] PASS      1.4s  extension autoloads: var/autoload/ezp_extension.php written
  [ 2/14] PASS      0.3s  INI caches: Clearing ini: Query cache (SQL results), Global INI cache, INI cache, ...
  [ 3/14] PASS      0.4s  template cache: Clearing template: Template cache
  [ 4/14] PASS      0.3s  template-override cache: Clearing template-override: Template override cache
  [ 5/14] PASS      0.6s  translation cache: Clearing translation: TS Translation cache
  [ 6/14] PASS      1.3s  design_base cache: Clearing design_base: Design base cache
  [ 7/14] PASS     31.7s  engine archive: engine.phar rebuilt: 1 changed (kernel/classes/expvelocitydeploy.php)
  [ 8/14] PASS      0.2s  reload PHP-FPM: reloaded plesk-php85-fpm (auto: pool /opt/plesk/php/8.5/etc/php-fpm.d/example.com.conf)
  [ 9/14] PASS      7.5s  restart Velocity: 4 instances started (engine.phar is current, not rebuilt)
  [10/14] PASS      0.3s  content cache: Clearing content: Content view cache
  [11/14] PASS      0.4s  exphttpcache cache: Clearing exphttpcache: HTTP cache (role-aware pages)
  [12/14] PASS      0.4s  ezjscore-packer cache: Clearing ezjscore-packer: eZJSCore Public Packer cache
  [13/14] PASS      0.4s  template-block cache: Clearing template-block: Template block cache
  [14/14] PASS      0.0s  Velocity response cache: response cache cleared (...)
velocity deploy: PASS, 14 steps in 45.2s
```

Dieser Lauf hatte `--packer` (Schritt 12); ohne die Option sind es 13 Schritte.

Hat sich keine Datei im Engine-Archiv geändert, meldet Schritt 6 `engine.phar is current, not rebuilt`, und der
ganze Lauf dauert etwa 7 Sekunden.

### Welches PHP-FPM

```ini
[DeploySettings]
PhpFpmService=auto        # the default
PhpFpmService=disabled    # served by Velocity alone
PhpFpmService=plesk-php85-fpm
```

`auto` findet die Pool-Konfiguration, die nach der Hosting-Domain der Installation benannt ist
(`/var/www/vhosts/<domain>/...`, wie Plesk Pools benennt), oder eine, die das Verzeichnis der Installation nennt, in
`/opt/plesk/php/*/etc/php-fpm.d` (Dienst `plesk-php<XY>-fpm`), `/etc/php-fpm.d` (`php-fpm`),
`/etc/php/*/fpm/pool.d` (`php<X.Y>-fpm`) und `/etc/opt/remi/php*/php-fpm.d` (`php<XY>-php-fpm`). Der Dienst ist der
Master des Pools, nicht das `php-fpm` der Maschine, wenn das ein anderes ist: Auf einem Plesk-Server startet
`systemctl restart php-fpm` ein PHP neu, auf dem keine Website dieser Art läuft. `--dry-run` zeigt, was gefunden wurde
und warum.

Der Schritt ist ein Neuladen, daher wird keine Anfrage abgebrochen. Er braucht root; unter einem anderen Benutzer oder
ohne gefundenen Pool wird er mit einem Hinweis übersprungen, der nennt, was zu tun ist.

### Autoloads des Kernels

`ezpgenerateautoloads.php -k` durchläuft die ganze Installation. Halten Sie Arbeitskopien der Installation darin (zum
Beispiel Git-Worktrees in einem versteckten Verzeichnis), wird jede Kernel-Klasse zweimal gefunden, und das Array kann
am Ende in die Kopie zeigen: Ein einfacher Lauf mit `-k` schrieb in einer Installation 1029 Einträge, jeden davon in
einer solchen Kopie, und dauerte 17,6 Sekunden. `deploy --kernel` schließt jedes Verzeichnis der obersten Ebene außer
`kernel/` und `lib/` aus, wo die Klassen des Kernels liegen, und braucht für dasselbe Array weniger als eine Sekunde.
Für einen Lauf von Hand führen Sie solche Verzeichnisse in `.autoloadignore` auf (ein Muster pro Zeile, am
Wurzelverzeichnis der Installation verankert) oder übergeben `--exclude=<dir>`.

### Ein abgeschnittener Neustart oder Deploy

`exp:velocity restart` und der Neustart-Schritt von `deploy` führen Stopp und Start als eine abgekoppelte Einheit aus.
Der Befehl startet sich selbst in einer eigenen Sitzung neu (`setsid -f`) und, als root unter systemd, in einem
vorübergehenden Scope namens `vc-restart-<date>-<token>` (`systemctl list-units 'vc-restart-*'`), wartet dann darauf
und gibt aus, was dieser ausgegeben hat, mit dessen Exit-Code. Ein Aufrufer, der zwischen Stopp und Start beendet wird
(ein Timeout, ein Ausgabenlimit, ein geschlossenes Terminal, das Ende einer Login-Sitzung), lässt den Server nicht mehr
gestoppt zurück: Der Neustart wird von selbst zu Ende geführt. Der Server, den er startet, lebt in diesem Scope, nicht
in der Sitzung des Aufrufers.

Während er läuft, liegt `restart-in-progress.json` neben der PID-Datei (`var/vc/qbix/run/`), mit der PID des
abgekoppelten Prozesses, seinem Scope und seinem Beginn; die Datei verschwindet, wenn der Start abgeschlossen ist.
Seine Ausgabe steht in `restart-<token>.log` im selben Verzeichnis, bis der Aufrufer sie gelesen hat.

Ein ausdrückliches `exp:velocity stop` (oder `kill`) schreibt dort `stopped.json`; `start`, `restart` und `graceful`
entfernen es. Der Server ist nur dann absichtlich gestoppt, solange diese Datei existiert. `deploy` startet daher einen
gestoppten Server ohne diese Datei (ein Neustart, der vor dieser Änderung abgeschnitten wurde, oder ein Absturz) und
lässt einen absichtlich gestoppten in Ruhe.

Die Priorität des Servers ist `[ServerSettings] Nice` (Standard 5), egal mit welcher der Aufrufer läuft: Ein unter
`nice -n 19` ausgeführter Deploy ließ früher jeden Worker auf 19. `exp:velocity status` nennt einen Elternprozess,
der mit einem anderen Wert läuft. `Nice=inherit` behält den des Aufrufers.

```ini
[ServerSettings]
Nice=5                    # inherit: the caller's priority, as before
[ControlSettings]
DetachedRestart=enabled   # disabled: restart in the calling process, as before
```

`EXP_VELOCITY_DETACHED_RESTART=0` in der Umgebung schaltet das Abkoppeln für einen Aufruf aus.

### Der Watchdog (`exp:velocity watchdog`)

Ein Neustart ist ein Stopp, gefolgt von einem Start. Wird das, was ihn ausführt, dazwischen beendet, bleibt der Server
gestoppt, bis jemand ihn startet. Der Neustart läuft jetzt als eine abgekoppelte Einheit, daher sollte das nicht mehr
vorkommen. Der Watchdog ist ein zweites Sicherheitsnetz für das, was trotzdem passieren kann: ein Absturz, ein Beenden
wegen Speichermangels oder ein Stopp durch ein anderes Werkzeug.

Er ist aus, bis Sie ihn installieren (root, systemd):

```
./console exp:velocity watchdog install --apply --allow-root-user   # vc-watchdog.service + .timer, conf-available/watchdog.conf, enabled
./console exp:velocity watchdog status --allow-root-user    # timer, markers, last check and last start
./console exp:velocity watchdog run --allow-root-user       # one check now (--json for a script)
./console exp:velocity conf disable --apply watchdog --allow-root-user   # pause it (conf enable: resume)
./console exp:velocity watchdog remove --apply --allow-root-user    # timer off, units and configuration file removed
```

Alle `Interval` Sekunden (standardmäßig 45) führt der Timer eine Prüfung der Standard-Engine aus (oder der mit
`--engine` genannten Engine). Er startet die Engine nur, wenn **alle** diese Bedingungen erfüllt sind:

- Kein Prozess der Engine läuft. Ein Server, der läuft, aber nicht antwortet, bleibt unberührt: Der Watchdog stoppt,
  beendet oder startet nie einen laufenden Server neu;
- niemand hat sie absichtlich gestoppt. `exp:velocity stop` und `kill` schreiben `stopped.json` neben die PID-Datei
  (`var/vc/qbix/run/`), und `start`, `restart` und `graceful` entfernen sie;
- kein Neustart ist im Gange. Der abgekoppelte Neustart schreibt `restart-in-progress.json` und entfernt die Datei,
  wenn sein Start abgeschlossen ist. Eine Markierung, deren Prozess nicht mehr existiert oder die älter als
  `RestartTimeout` ist, zählt nicht mehr;
- die optionale `LockFile` ist nicht belegt (eine Deploy-Warteschlange). Ist die Sperre länger als `RestartTimeout`
  belegt, während der Server gestoppt ist, zählt auch sie nicht mehr;
- er wartet nicht gerade ab. Nach drei fehlgeschlagenen Starts in Folge wartet er `Backoff` Sekunden, bevor er es
  erneut versucht.

Der Server, den er startet, kommt in einen eigenen systemd-Scope (`vc-<engine>-watchdog-start-<time>`). Ohne ihn würde
systemd den Server zusammen mit dem Oneshot-Dienst des Watchdogs beenden, und der Dienst hat außerdem
`KillMode=process`. Jede Prüfung, die mehr tut, als den Server laufend vorzufinden, schreibt eine Zeile in
`var/log/velocity-watchdog.log` (im Log-Viewer als „Velocity watchdog“):

```
[2026-10-17T17:02:15+00:00] qbix action=start reason=down detail="not running, down for 0 s; start: ..." pid=12345
[2026-10-17T17:03:00+00:00] qbix action=skip reason=intentionally-stopped detail="stopped on purpose 40 s ago; ..." pid=12360
```

Die Gründe sind `down`, `restart-stale`, `intentionally-stopped`, `restart-in-progress`, `lock-held`, `backoff`,
`paused`, `overlap` (die vorherige Prüfung läuft noch) und `error`.

Das Audit-Protokoll (doc/eng-us/bc/6.0/audit.md) erhält nur von einer Prüfung ein Ereignis, die handelt oder etwas
Bemerkenswertes entscheidet, nie von der Routineprüfung „läuft, nichts zu tun“. Ein `watchdog run` verzeichnet nicht
das allgemeine `system.command.run`, das jeder andere Befehl verzeichnet (er ruft für seinen eigenen Lauf
`eZScript::setAuditCommandRun( false )` auf). Stattdessen verzeichnet er:

| Ereignis | Wann | Gründe |
|---|---|---|
| `system.velocity.watchdog.start` | er hat die Engine gestartet (`success`), oder der Start ist fehlgeschlagen (`failed`, Warnung) | `down`, `restart-stale` |
| `system.velocity.watchdog.backoff` | er hat es nicht versucht, weil drei Starts in Folge fehlgeschlagen sind | `backoff` |
| `system.velocity.watchdog.skip` | er hat eine gestoppte Engine in Ruhe gelassen | `intentionally-stopped`, `restart-in-progress`, `lock-held`, `overlap`, `error` |

Grund und Detail stehen im `after` des Ereignisses. Eine Prüfung, die die Engine laufend vorfindet (`none`), und ein
pausierter Watchdog verzeichnen nichts.

Ein `skip` wird nur verzeichnet, wenn sich sein Grund ändert: der erste Skip eines Zeitraums (ein absichtlich
gestoppter Server, ein laufender Neustart, die belegte Sperre, eine Überschneidung, ein Fehler) oder ein Skip mit einem
anderen Grund als dem zuletzt verzeichneten. Der Watchdog hält diesen letzten Grund in `watchdog-last.json` neben den
Markierungen (`var/vc/<engine>/run/`) und entfernt die Datei, sobald eine Prüfung die Engine laufend vorfindet oder sie
startet, sodass der nächste Stopp wieder ein `skip`-Ereignis ergibt. Ein Server, der einen Tag lang absichtlich
gestoppt ist, ergibt ein Ereignis, nicht etwa 1.900. Ereignisse `start` und `backoff` werden wie bisher verzeichnet.
`velocity-watchdog.log` wird nicht dedupliziert: Es behält eine Zeile pro Prüfung, die mehr tut, als den Server laufend
vorzufinden, sodass das Log weiterhin jede Prüfung eines gestoppten Zeitraums zeigt.

```ini
[WatchdogSettings]
Interval=45            # seconds between checks (15-3600)
RestartTimeout=300     # how long a restart marker or a held lock may hold it back
Backoff=600            # the wait after three failed starts in a row
LockFile=              # a lock (flock) that means "deploying, wait"; empty: none
UnitName=vc-watchdog   # give each installation on a host its own
UnitDir=/etc/systemd/system
```

`install` schreibt diese Werte nach `conf-available/watchdog.conf` im Konfigurationsbaum (`/etc/vc`), als JSON unter
`watchdog`, und aktiviert die Datei in `conf-enabled`. Werte in der aktivierten Datei haben Vorrang vor `velocity.ini`,
sodass ein Host sie ohne INI-Override ändern kann. Eine vorhandene Datei bleibt bei einer erneuten Installation
erhalten.

## Sie nebeneinander betreiben

Für Tests können die drei gleichzeitig laufen: Jede hat ihren eigenen Port, ihre PID-Datei, ihre Logs und ihre
Konfiguration, daher kollidiert nichts.

```bash
php bin/php/console exp:velocity start  --all --allow-root-user   # php :8087, frankenphp :8089, qbix :8088
php bin/php/console exp:velocity status --all --allow-root-user   # role, default, running, port for each
php bin/php/console exp:velocity stop   --engine=qbix --allow-root-user
php bin/php/console exp:velocity stop   --all --allow-root-user
```

`--all` und Listen von Engines funktionieren mit start, stop, restart, graceful, kill und status. Jede Engine wird in
einer eigenen Zeile gemeldet; eine, die fehlschlägt, hält die anderen nicht auf, und der Exit-Code ist ungleich null,
wenn eine fehlgeschlagen ist.

`status` zeigt für jede laufende Engine die zu öffnenden Adressen als vollständige URLs, die ein Terminal anklickbar
macht: jede über einen Pfad erreichbare Website (der Standard-Siteaccess unter `/`, dann die anderen aus
`AvailableSiteAccessList`; weggelassen, wenn Siteaccesses nur über den Host zugeordnet werden), die Anmeldung der
Administration, Setup > Systeminformationen und Setup > Caches, die HTTPS-Adresse neben jeder, wenn die Engine TLS
ausliefert, und das Dashboard des Qbix-Servers. Eine gestoppte Engine zeigt, wo sie antworten wird, und den Befehl, der
sie startet. Unter den Adressen stehen der Prozess, die Version, die Konfiguration, HTTPS (der Port und, wenn es aus
ist, was es einschaltet) und die Logs -- Pfade relativ zur Installation -- und die Einstellungen, auf die die Engine
nicht reagiert, eine pro Zeile. `status` ohne `--engine` beginnt mit einer Zeile pro Engine -- Rolle, Zustand, URL --
und dem Befehl, der jede laufende stoppt, und zeigt dann die Details der laufenden (die der Standard-Engine, wenn keine
läuft), sodass eine Engine auffällt, die von einem Test her noch läuft. `status --all` zeigt dieselbe Übersicht und
dann den vollständigen Status jeder Engine, auch der gestoppten, mit jeder Adresse, unter der sie antworten werden:
alles und jede URL auf einen Blick. `status --engine=<name>` zeigt nur diese eine. `start --all` (und
`restart`/`graceful --all`) endet mit dem, was `status --all` zeigt; `start` einer Engine mit der Übersicht und dem
vollständigen Status dieser Engine. Exit-Code und `--json` beschreiben weiterhin die Standard-Engine.

`[FrankenPHPSettings]` und `[PHPServerSettings]` können `Port`, `HTTPSPort`, `Host`, `Workers`, `SpareWorkers` und
`DocumentRoot` für ihre Engine setzen. Ein leerer Wert fällt auf `[ServerSettings]` zurück, das zugleich die eigene
Gruppe der Qbix-Engine ist.

Eine Website läuft auf einer Engine. Setup > Systeminformationen beschreibt daher nur die Engine im Detail, die die
Seite ausliefert, und nennt jede andere laufende Engine der Installation in einer Zeile, als Testaufbau.

**Upgrade:** Bis zu dieser Änderung war der Standard `qbix`. Eine Installation, deren Deploy-Skripte
`exp:velocity restart` im Sinne des Qbix-Servers ausführen, setzt `Engine=qbix` in
`settings/override/velocity.ini.append.php`.

## Die Programme der Qbix-Engine: `sbin/` und `bin/`

Ab Exponential Velocity 0.0.4.41 hält das Engine-Paket seine Programme so, wie es UNIX-Systeme tun: den Server und
seine Administrationsbefehle in `sbin/`, die Shell in `bin/` (die `docs/layout.md` der Engine, „Programs“). Jeder
frühere Pfad führt weiterhin dasselbe Programm im selben Prozess aus, daher muss sich in einer Installation nichts
ändern, und `exp:velocity` funktioniert mit der Engine vor und nach diesem Release, indem es danach entscheidet, welche
Dateien vorhanden sind:

| | Engine 0.0.4.40 und älter | Engine 0.0.4.41 und neuer |
|---|---|---|
| der Server | `qbixserver.php` | `sbin/qbixserver.php`; `qbixserver.php` leitet dorthin weiter |
| Steuerung | `qbixctl.php` | `sbin/qbixctl.php`; `qbixctl.php` leitet dorthin weiter |
| Konsole | `qbixconsole.php` | `sbin/qbixconsole.php`; `qbixconsole.php` leitet dorthin weiter |
| Shell | `qshell.php` | `bin/qshell.php`; `qshell.php` leitet dorthin weiter |
| Archiv | `bin/qbixserver.phar` | `sbin/qbixserver.phar`; `bin/qbixserver.phar` ist dieselbe Datei |

- `[ServerSettings] ScriptPath` behält seinen ausgelieferten Wert,
  `vendor/se7enxweb/exponential-velocity/qbixserver.php`: Mit der neuen Engine ist das die Weiterleitung, und ein
  damit gestarteter Server zeigt dieselbe Kommandozeile wie bisher, sodass `ps`, Muster für `pkill -f` und eine
  Überwachung, die danach sucht, weiter passen. Ihn auf `.../sbin/qbixserver.php` zu setzen funktioniert ebenfalls;
  existiert die genannte Datei nicht (die Kopie in vendor ist noch die ältere Engine), wird der andere Pfad verwendet.
- `start`, `stop`, `graceful`, `restart` und `status` zählen einen Server dieser Installation, gleich von welchem der
  beiden Pfade er läuft: Einer, der mit `exp:velocity start` gestartet wurde, führt `ScriptPath` aus, einer, der mit
  dem eigenen `qbixctl start` der Engine gestartet wurde, führt `sbin/qbixserver.php` aus
  (`expVelocity::scriptPaths()`).
- `exp:velocity ctl`, `ssl` und `ext` führen das `sbin/qbixctl.php` der Engine aus, oder `qbixctl.php` bei einer
  älteren Engine (`expVelocity::engineFile()`), und `cache clear` lädt die Steuerungsklasse der Engine aus deren
  `src/`, das nicht umgezogen ist (`expVelocity::engineDir()`).

## Mehrere Qbix-Server auf einem Port (`Instances`)

Ein Qbix-Server beantwortet gecachte Seiten in seinem eigenen Prozess, bevor ein Worker beteiligt ist: etwa 0,3 ms CPU
pro Seite, daher ist ein Server durch einen Kern begrenzt. `[ServerSettings] Instances=N` betreibt stattdessen N Server
auf denselben HTTP- und HTTPS-Ports. Die erzeugte Konfiguration setzt `Q.webserver.reusePort` der Engine, sodass jede
Instanz ihre Listener mit `SO_REUSEPORT` öffnet und der Kernel neue Verbindungen auf sie verteilt.

```ini
# settings/override/velocity.ini.append.php
[ServerSettings]
Instances=4
Workers=148        # per instance: the old 590 divided by four
SpareWorkers=12    # per instance: the old 48 divided by four
```

- Jede Instanz hat ihren eigenen Worker-Pool, ihre PID-Datei und ihr Log, neben denen der ersten: `server.pid`,
  `server.1.pid`, ... und `console.log`, `console.1.log`, .... Instanz 0 behält die konfigurierten Namen, sodass
  `Instances=1` (der Standard) genau der einzelne Server wie bisher ist.
- `start`, `stop`, `kill` und `status` erreichen jede Instanz; `status` führt die Elternprozesse auf. `graceful` lädt
  sie nacheinander neu, sodass der Port weiter antwortet.
- Die Festplattenebene des Antwort-Caches wird geteilt (dasselbe Cache-Verzeichnis); die Arbeitsspeicher- und
  APCu-Ebenen jeder Instanz sind ihre eigenen und wärmen sich getrennt auf.
- Nur die Qbix-Engine: FrankenPHP und der Server von PHP betreiben je einen Prozessbaum.
- Linux und die BSDs (`SO_REUSEPORT`).

Gemessen auf einer Testinstallation (12 Kerne, 2026-09-27), gecachte Startseite über TLS mit gzip:

| | eine Instanz | vier Instanzen | FrankenPHP |
|---|---:|---:|---:|
| anonym, 64 gleichzeitig | ~2.950/s | **11.985/s**, p95 10 ms | 4.153/s |
| angemeldet (der HTTP-Cache), 8 gleichzeitig | 1.435/s | **3.991/s** | 1.937-3.413/s |

Vollständige Renderings werden mit mehr Instanzen nicht schneller; sie sind durch die Worker und durch Exponential
selbst begrenzt.

## Stopp, Neustart und Status der Qbix-Engine

Als Server dieser Installation zählt jeder ihrer Prozesse, nicht nur die, die die PID-Dateien nennen:

- ein PHP-Prozess, der das Serverskript der Engine ausführt (als realer Pfad, sodass jeder Pfad zum selben Baum
  zählt), dessen `--pid` eine der PID-Dateien dieser Installation ist oder, ohne eine solche, dessen `--root` ihr
  Document Root ist -- nie eine Shell, `timeout` oder ein anderer Wrapper, der das Skript nur erwähnt, nie die
  eigenen Befehle `--stop` und `--reload` der Engine, nie `exp:velocity` selbst oder etwas darüber und nie ein Server
  einer anderen Installation oder der Tests eines Engine-Checkouts, die dasselbe Skript verwenden;
- alles, was diese gestartet haben (die Zygote trägt den Titel `qbixserver: zygote` und nennt kein Skript);
- jeder PHP-Prozess, dessen Ausgabe noch in das Log-Verzeichnis des Servers geht (das Verzeichnis von `LogFile`): was
  ein Server hinterlassen hat, als er endete.

`stop` fragt jede Instanz mit PID-Datei über das eigene `--stop` des Servers, sendet SIGTERM an jeden Prozess der
obersten Ebene (die Elternprozesse und alles, dessen Elternprozess verschwunden ist), dann an jeden Prozess, wenn die
Server ihre Pools nicht innerhalb von drei Sekunden heruntergefahren haben. Was nach `[ControlSettings] StopTimeout`
(15 s) noch da ist, wird mit SIGKILL beendet, und das Ergebnis sagt das. Er ist nur erfolgreich, wenn kein Prozess der
Installation übrig ist und ihre Ports frei sind; ein Port, den noch ein anderes Programm belegt, wird mit dessen Prozess
genannt.

`restart` baut zuerst das Engine-Archiv neu (der laufende Server bleibt unberührt, wenn das fehlschlägt), stoppt wie
oben, startet und meldet Erfolg nur, wenn jede Instanz einen **neuen** Elternprozess hat und der HTTP-Port innerhalb
von 30 Sekunden eine Anfrage beantwortet (`GET /Q/health`, beliebiger Status). Andernfalls schlägt er mit einer
Meldung und einem Exit-Code ungleich null fehl. Da der Stopp Prozesse ohne die PID-Dateien findet, wird ein auf halbem
Weg unterbrochener Neustart -- ein Timeout des Aufrufers zwischen „gestoppt“ und „gestartet“ -- vom nächsten
`restart` zu Ende geführt, und ein `start`, der eine Instanz ohne ihren Elternprozess findet, beendet zuerst die
Überreste. Ein `start` beendet nie einen Server, dessen Instanzen alle ihre Elternprozesse haben, ob er antwortet oder
nicht; er meldet `already running, but it does not answer` und überlässt das `restart`.

`status` meldet `running` nur für einen Server, der eine HTTP-Anfrage beantwortet (zweimal gefragt, je fünf Sekunden);
Prozesse, die nicht antworten, sind `not answering`, und der Befehl endet mit einem Exit-Code ungleich null. Unter
`Problem` nennt er eine Instanz ohne ihren Elternprozess, Prozesse der Installation ohne laufenden Server (und die
Ports, die sie belegen), Verbindungen, die an einem Port anstehen und nicht angenommen werden, und einen
Elternprozess, der im Kernel blockiert ist (`futex_do_wait`) und auf kein Signal außer SIGKILL reagiert.

Der Server begrenzt auch seinen eigenen Stopp: `[ControlSettings] ShutdownTimeout` (12 s, unter `StopTimeout`) wird
zu `Q.webserver.shutdownTimeout` der Engine; danach werden der Server und jeder Prozess, den er gestartet hat, beendet.
Seine Worker halten an einer Stelle an, an der sie keine APCu-Sperre halten, sodass ein Timeout einer Anfrage die
Sperre nicht mehr belegt und den Server daran blockiert zurücklässt (die Ursache des Hängers vom 2026-10-07).

Ab Engine 0.0.4.50 leert ein Stopp außerdem sauber: Jede HTTP/2-Verbindung erhält `GOAWAY` mit ihrem letzten Stream,
die Streams bis dahin werden vollständig beantwortet und spätere mit `REFUSED_STREAM` abgelehnt, sodass der Client sie
wiederholt; eine untätige Keep-alive-Verbindung unter HTTP/1.1 wird sofort geschlossen. Ein HEAD über HTTP/2 wird nur
mit den Headern des GET beantwortet, auch aus dem Antwort-Cache.

## Gestreamte Antworten

Ab Engine 0.0.4.49 streamt ein persistenter Pool-Worker eine Antwort, statt sie vollständig zu sammeln: eine Antwort
mit `X-Accel-Buffering: no` (zum Beispiel der gestreamte Audit-Export) und Server-Sent Events
(`Accept: text/event-stream`, von Exponential Intelligence verwendet). Vier Einstellungen in
`settings/velocity.ini [ServerSettings]` werden an die Engine übergeben; alle sind standardmäßig leer, sodass die
eigenen Standardwerte der Engine gelten:

| Einstellung | Standard der Engine | Bedeutung |
|---|---|---|
| `StreamTimeout` | 300 | Sekunden, die ein Stream (oder eine Anfrage mit `Accept: text/event-stream` vor ihrem ersten Byte) statt des Anfrage-Timeouts von 30 s laufen darf; danach endet er sauber, und eine EventSource verbindet sich neu. 0: kein Limit. |
| `StreamShare` | 0.25 | Der Anteil von `Workers`, den offene Streams gleichzeitig belegen dürfen (mindestens einer). |
| `MaxStreams` | (leer) | Dasselbe als Zahl von Workern, wird statt `StreamShare` verwendet, wenn größer als 0. Jenseits der Grenze wird ein Event-Stream mit 503 und `Retry-After` beantwortet, sodass Seiten immer den Rest des Pools behalten. |
| `RetryWithin` | 10 | Sekunden, nachdem eine Anfrage ihren Worker erreicht hat, innerhalb derer ein GET, dessen Worker ohne Antwort gestorben ist, einmal erneut ausgeführt wird (nie ein Event-Stream, nie eine Antwort, die schon begonnen hatte). |

```ini
# settings/override/velocity.ini.append.php
[ServerSettings]
StreamTimeout=120
MaxStreams=8
```

Danach `./console exp:velocity restart`. Jeder offene Stream belegt einen Worker für seine ganze Lebensdauer, halten
Sie die Grenze daher deutlich unter `Workers`.

Das Vorwärmen leert die Antwort, die es gerendert hat (ihre Header und ihren Status), bevor die Worker geforkt werden,
sodass die erste Anfrage jedes Workers genauso leer beginnt wie jede spätere (vor dem 2026-10-11 konnte sie die Header
der Startseite mitbringen).

## FrankenPHP

```bash
php bin/php/console exp:velocity config set --apply ServerSettings Engine frankenphp --allow-root-user
php bin/php/console exp:velocity install --allow-root-user     # optional: start installs it
php bin/php/console exp:velocity start --allow-root-user
```

### Die Binärdatei

`install` holt das Release-Asset für diese Maschine von GitHub, für die in `[FrankenPHPSettings] Version` festgelegte
Version, und lehnt es ab, sofern sein SHA-256 nicht dem daneben festgelegten Wert entspricht (`Sha256[<asset>]`). Es
landet in `var/vc/frankenphp/bin/frankenphp-<version>-<asset>` (von Git ignoriert), neben einer `.sha256`-Datei.
`start` installiert es selbst, wenn es fehlt (`AutoInstall=enabled`).

| Option | Wirkung |
|---|---|
| `install --check` | die installierte Binärdatei erneut hashen |
| `install --force` | erneut herunterladen |
| `install --from=<file>` | eine von Hand hierher kopierte Datei installieren — für eine Maschine ohne Zugang zu GitHub; genauso geprüft |
| `install --trust-github-digest` | für eine `Version` ohne Zeilen `Sha256[]`: den Digest akzeptieren, den GitHub für das Release veröffentlicht (schwächer: dieselbe Quelle wie die Datei) |

`Variant` wählt den Build unter Linux:

| Variante | Asset | Wann |
|---|---|---|
| `gnu` (Standard) | `frankenphp-linux-<arch>-gnu` | jede Distribution mit glibc 2.17+; schnellerer Allokator als musl, lädt `.so`-Erweiterungen (oci8 …) |
| `musl` | `frankenphp-linux-<arch>` | Alpine, oder wo nichts vom System übernommen werden darf |
| `mimalloc` | `frankenphp-linux-x86_64-mimalloc` | musl mit dem Allokator mimalloc, nur x86_64 |

macOS erhält `frankenphp-mac-arm64` oder `-x86_64`; für Windows wird nichts heruntergeladen.

**Ein neues Release** bedeutet, `Version` und jede Zeile `Sha256[]` gemeinsam zu ändern (die Digests stehen auf der
GitHub-API-Seite des Release, `ApiUrl`). Die alte Binärdatei bleibt in `var/vc/frankenphp/bin`, daher ist der Weg
zurück nur die Einstellung.

**Ein eigener Build** — eine andere PHP-Version, zusätzliche Caddy-Module (zum Beispiel der Antwort-Cache
`cache-handler`) — wird über `BinaryPath` genannt. Dann wird nichts heruntergeladen; `BinarySha256` wird, wenn
gesetzt, von `install` geprüft. Solche Builds entstehen mit `static-builder-gnu.Dockerfile` /
`static-builder-musl.Dockerfile` von FrankenPHP.

### PHP-Einstellungen

Die Binärdatei liest **keine php.ini der Maschine**. Ohne Einstellungen liefe PHP mit seinen einkompilierten
Standardwerten — `display_errors` eingeschaltet, ein Speicherlimit von 128 MB. Daher liefert
`[FrankenPHPSettings] IniOptions[]` `display_errors=Off`, `log_errors=On`, `memory_limit=512M`, `opcache.enable=1` und
die Upload-Limits mit; `[PHPSettings] IniOptions[]` wird zuerst angewendet, ein späterer Schlüssel gewinnt.
`PHPIniFile=` verweist PHP stattdessen auf eine eigene php.ini (`PHPRC`).

OPcache braucht hier kein `opcache.revalidate_freq=0`: Jede Anfrage hat ihre eigene Anfragezeit, daher werden
bearbeitete und neu erzeugte PHP-Dateien übernommen.

Jeder Wert erreicht PHP als INI-Text, in dem ein `;` einen Kommentar beginnt; ein Wert, der eines enthält
(`session.save_path=0;0660;<dir>`), wird daher in Anführungszeichen in das Caddyfile geschrieben. Bevor das so war,
kam der Sitzungspfad als `0` an, keine Sitzung wurde je gefunden, und niemand konnte auf dieser Engine angemeldet
bleiben.

`[PHPSettings] IniOptions[]` setzt außerdem `apc.shm_size=256M` für beide Engines: Der Standard der Kommandozeile ist
32 MB, in denen sich der Antwort-Cache und der SQL-Abfrage-Cache gegenseitig verdrängten (ein Rendering unter Velocity
kostete 727 ms CPU, mit 256 MB 303 ms).

### Das erzeugte Caddyfile

`var/vc/frankenphp/run/Caddyfile` wird bei jedem `start`, `graceful` und `restart` aus `velocity.ini` geschrieben und
mit `frankenphp validate` geprüft, bevor es das vorherige ersetzt — eine fehlerhafte Konfiguration erreicht nie den
laufenden Server. `exp:velocity ctl caddyfile` gibt es aus; `ctl validate`, `ctl adapt`, `ctl version`,
`ctl list-modules`, `ctl build-info` führen damit die eigenen Befehle der Binärdatei aus.

Das Routing ist das von `.htaccess_root`: Die aufgeführten Pfade für Designs, Erweiterungen, Bilder und Caches sind
Dateien, `api/` geht an `index_rest.php`, das Baummenü an `index_treemenu.php`, alles andere an `index.php`. Es
verwendet bewusst **nicht** `php_server`, dessen Standard ist, jede existierende Datei auszuliefern —
`settings/*.ini`, die SQLite-Datenbank, die Quellen des Kernels.

Eigene Direktiven (Header, Weiterleitungen, zusätzliche Routen) gehören in eine Datei, die
`[FrankenPHPSettings] SiteInclude` nennt und die in jeden Site-Block importiert wird. Bearbeiten Sie nicht die erzeugte
Datei.

### HTTPS

FrankenPHP liefert HTTPS auf `HTTPSPort` (8444) neben einfachem HTTP auf `Port` aus, und zwar **standardmäßig**
(`[FrankenPHPSettings] HTTPS=enabled`), sodass eine Entwicklungsmaschine ab dem ersten Start `https://` hat:

```bash
php bin/php/console exp:velocity start --engine=frankenphp              # http :8089 + https :8444
php bin/php/console exp:velocity start --engine=frankenphp --no-https   # plain HTTP, this start only
php bin/php/console exp:velocity start --engine=frankenphp --https      # insist on HTTPS, this start only
```

`HTTPS=disabled` schaltet es dauerhaft aus. Es verwendet `[HTTPSSettings] Certificate` und `Key`, wenn beide gesetzt
sind (beide müssen existieren). Sind beide leer, erzeugt es ein **selbstsigniertes Zertifikat** für diese Maschine --
`localhost`, `127.0.0.1`, `::1` und der Hostname, SHA-256, ein Jahr gültig -- in `var/vc/frankenphp/tls/` (Schlüssel
0600) und erneuert es einen Monat vor Ablauf. Ein Browser warnt einmal davor; es ist für die Entwicklung und für einen
vorgeschalteten Reverse Proxy gedacht, nicht für Besucher -- eine öffentliche Website nennt ihr Zertifikat oder
terminiert TLS davor. Kann das selbstsignierte Zertifikat nicht erzeugt werden (keine Erweiterung openssl, ein nicht
beschreibbares Verzeichnis), startet der Server mit einfachem HTTP, und die Startmeldung sagt, warum; mit `--https`
oder einem genannten Zertifikat, das nicht existiert, wird der Start stattdessen abgelehnt.
`[HTTPSSettings] Enabled=true` mit einem Zertifikat schaltet es wie bisher ein. `status` sagt, welches Zertifikat
verwendet wird, und führt die HTTPS-Adresse neben der HTTP-Adresse auf, auch für einen mit `--https` gestarteten
Server. `--https` gehört zur Engine frankenphp: Der eingebaute Server hat kein TLS, und der Qbix-Server nimmt
`[HTTPSSettings]`.

### Steuerung

`stop` und `graceful` verwenden die Admin-API von Caddy, standardmäßig auf einem Unix-Socket unter
`var/vc/frankenphp/run/admin.sock` (nur für den Besitzer; kein Port, der mit einer anderen Installation kollidieren
könnte). Ein Pfad, der länger ist, als ein Socket erlaubt, fällt auf `localhost:<Port + 10000>` zurück;
`AdminAddress` setzt ihn ausdrücklich. `stop` fällt auf SIGTERM zurück, wenn die API nicht antwortet; `graceful`
fällt auf einen Neustart zurück.

`graceful` lädt die neue Konfiguration, ohne eine Verbindung abzubrechen; die PHP-Threads starten mit den neuen
Einstellungen des Interpreters neu. Gemessen: eine Änderung der Thread-Zahl unter einer Anfrageschleife, 66 von 66
Anfragen beantwortet, der Prozess blieb erhalten.

Caddy hält seinen Zustand unter `var/vc/frankenphp/caddy/` (`XDG_DATA_HOME`, `XDG_CONFIG_HOME`), nicht im
Home-Verzeichnis des Dienstbenutzers.

### Logs

Alles, was Velocity (`vc`) schreibt, liegt unter `var/vc/`, **ein Verzeichnis pro Server**, sodass beisammen bleibt,
was zusammengehört:

```
var/vc/
├── qbix/
│   ├── etc/    the configuration tree            (/etc/vc with root)
│   ├── lib/    what is known about each site     (/var/lib/vc with root)
│   ├── log/    site-access.log, site-error.log, velocity-layout.log   [LogSettings] Dir
│   └── run/    server.pid, console.log
├── frankenphp/
│   ├── bin/    the binary (exp:velocity install)
│   ├── caddy/  Caddy's state
│   ├── tls/    the self-signed certificate
│   ├── log/    access.log, error.log              [FrankenPHPSettings] LogDir
│   └── run/    server.pid, console.log, Caddyfile, admin.sock
└── php/
    ├── log/    server.log                         [PHPServerSettings] LogFile
    └── run/    server.pid
```

`status` nennt jede Datei. Caddy schreibt JSON, daher sind `access.log` und `error.log` von FrankenPHP (oder
`AccessLog`/`ErrorLog`) seine eigenen — ein Wechsel der Engine hinterlässt nie eine Datei in zwei Formaten.
`LogSettings Enabled` und `FileMode` gelten; `Format` nicht. Caches bleiben, wo sie waren (`[CacheSettings] Dir`,
`var/tmp/precompress`), ebenso `var/tmp/velocity-server.json`, die Konfiguration des Qbix-Servers vor der
vc-Struktur, die ältere Aufbauten lesen.

**Upgrade:** Bis 2026-09 lagen die Logs in `var/log/qbix/` (Qbix-Server, und `frankenphp-access.log`/
`frankenphp-error.log` von FrankenPHP) und `var/tmp/velocity-php.log`, die PID-Dateien, Konsolen-Logs, das Caddyfile
und der Admin-Socket in `var/tmp/`, der Konfigurationsbaum von Qbix in `var/vc/etc` und `var/vc/lib`. Dieser Baum wird
beim nächsten Start nach `var/vc/qbix/` verschoben, mit allem, was darin bearbeitet wurde, wenn dort noch nichts ist;
die anderen alten Dateien bleiben, wo sie sind. Ein vor dem Upgrade gestarteter Server wird weiterhin gefunden und
gestoppt (FrankenPHP über seinen alten Caddyfile-Pfad, da sein Admin-Socket umgezogen ist). Eine Installation, die
`[LogSettings] Dir`, `PidFile` oder `LogFile` selbst setzt, behält, was sie gesetzt hat; ein Skript, das
`var/tmp/velocity.pid` liest, liest jetzt `var/vc/qbix/run/server.pid`.

## Der eingebaute Webserver von PHP

```bash
php bin/php/console exp:velocity start --engine=php --allow-root-user
```

`php -S Host:Port -t DocumentRoot bin/php/velocity-router.php`, mit dem PHP, das `exp:velocity` ausführt (oder
`[PHPServerSettings] PHPBinary`), dessen php.ini und `[PHPSettings] IniOptions[]`. Nichts zu installieren. `Workers`
wird zu `PHP_CLI_SERVER_WORKERS` (Linux); das Anfrageprotokoll ist die Konsole des Servers,
`var/vc/php/log/server.log` — Anfragen und die Fehler von PHP in einer Datei, da `php -S` sie nicht trennt.

Es ist ein Entwicklungsserver, wie das PHP-Handbuch sagt: einfaches HTTP, kein Neuladen (`graceful` startet neu),
statische Dateien ohne Cache-Lebensdauer. Lassen Sie `Host` auf 127.0.0.1.

`bin/php/velocity-router.php` tut, was `.htaccess_root` tut. Es beurteilt einen statischen Pfad **so, wie er sich
auflöst**: `/design/standard/stylesheets/../../../settings/site.ini` beginnt wie ein Stylesheet, und der eingebaute
Server würde die Punkte auflösen und die Datei senden. Ein aufgelöster Pfad außerhalb des Wurzelverzeichnisses oder
außerhalb der Liste, oder ein Skript, ergibt 404.

## Einstellungen, die jede Engine ignoriert

`start` und `status` nennen jede Einstellung, die eine Installation gesetzt hat und auf die die verwendete Engine
nicht reagiert — `CacheSettings` bei FrankenPHP, `ForkPerRequest` und `KeepGlobals` (dort wird nichts zwischen
Anfragen behalten), `LogSettings Format`, die Einstellungen `Brand*` und des Dashboards des Qbix-Servers,
`HTTPSSettings` beim eingebauten Server. Gemeinsame Einstellungen: `Host`, `Port`, `HTTPSPort`, `DocumentRoot`,
`Workers` (FrankenPHP: Threads; unter Last `SpareWorkers` mehr), `StaticMaxAge`, `HTTP2`, `HTTPSSettings`,
`LogSettings`, `PHPSettings`, `EnginePhar`, `ControlSettings StopTimeout`.

## Was als Datei ausgeliefert wird

Alle drei Engines folgen `.htaccess_root`: Die dort aufgeführten Asset-Verzeichnisse (`expVelocity::STATIC_PATHS` —
Stylesheets, Bilder und Skripte von Designs und Erweiterungen, `var/*/storage/images`, die öffentlichen Caches, Icons,
Paketvorschauen, `favicon.ico`, `robots.txt`) werden als Dateien gesendet; alles andere geht an einen Front-Controller
— `/api/` an `index_rest.php`, das Baummenü der Administration an `index_treemenu.php`
(`expVelocity::FRONT_CONTROLLERS`), der Rest an `index.php`. Keine andere `.php`-Datei läuft, weil ihr Pfad angefragt
wurde (`expVelocity::ENTRY_SCRIPTS`: `index.php`, `index_rest.php`), und eine Datei außerhalb der Liste wird nicht
herausgegeben: Die Originale in `var/*/storage/original` erreichen einen Besucher nur über `content/download`, das
prüft, wer fragt.

| | frankenphp | php | qbix |
|---|---|---|---|
| Assets | `file_server @static` | der Router gibt die Datei zurück | `Q.web.static.paths` |
| Skripte und Punkt-Pfade unter einem Asset-Verzeichnis | `not path_regexp` `NEVER_STATIC` → `index.php` | dieselbe Liste → `index.php` | nicht statisch (`.php` ist es nie, Punkt-Pfade sind gesperrt) |
| andere `.php` über ihren Namen | `rewrite @front /index.php` | Router → `index.php` | `Q.webserver.scripts` |
| `/api/`, Baummenü | `rewrite @rest`, `@treemenu` | Router | `Q.webserver.frontControllers` |

Die drei Qbix-Schlüssel brauchen einen qbix-webserver, der sie kennt; ein älterer ignoriert sie, führt jede über ihren
Namen angefragte `.php`-Datei aus und sendet jede Datei mit einer ausgelieferten Endung — `.htaccess` wird auf seinem
Pool-Pfad nicht gelesen.

Keine der drei Engines liest `.htaccess`. Kopieren Sie die Datei trotzdem (`./console exp:setup:htaccess --apply`),
wenn Apache denselben Baum ausliefern kann, zum Beispiel neben Velocity oder während eines Umzugs
([.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md)).

## Symbolische Links aus dem Document Root heraus

`[ServerSettings] FollowSymlinks=disabled` (der Standard) lehnt eine Datei ab, zu der ein Link innerhalb des Document
Root nach außerhalb führt: Die Engine qbix antwortet mit 403 (die eigene Prüfung des Servers,
`Q.webserver.followSymlinks`), die Engine php mit 404 (der Router vergleicht den aufgelösten Pfad mit dem
Wurzelverzeichnis). Ein Link, den ein Upload, ein Installationsprogramm oder ein gemeinsamer Asset-Baum angelegt hat,
würde sonst herausgeben, worauf er zeigt.

`enabled` ist für Installationen, die absichtlich verlinken — aus einem gemeinsamen Checkout verlinkte Erweiterungen,
`var/storage` auf einer anderen Festplatte. Velocity schreibt dann `followSymlinks: true` in die Qbix-Konfiguration
und übergibt `EXP_VELOCITY_FOLLOW_SYMLINKS=1` an den Router. Ein Pfad mit Segmenten `..` wird in jedem Fall
abgelehnt, und keine Engine liefert eine `.php`-Datei als Datei aus. Der Dateiserver von Caddy folgt Links immer; bei
FrankenPHP nennt `status` `FollowSymlinks=disabled` als Einstellung, auf die es nicht reagiert.

Verben, die zum Qbix-Server gehören: `ssl`, `layout migrate`, `site|conf|mod enable|disable` verweigern auf den anderen
Engines den Dienst; `layout` führt stattdessen die Dateien auf, die die Engine verwendet; `cache clear` ist erfolgreich,
ohne etwas zu leeren.

## Setup > Systeminformationen

- **„Webserver“** nennt die Software (FrankenPHP mit seiner Version und der von Caddy sowie dem Modus; den eingebauten
  Server von PHP mit seinen Workern).
- **„Velocity: <engine> (<role>)“** erscheint, wenn eine Velocity-Engine die Seite ausliefert: ihre Rolle, ob sie die
  Standard-Engine ist und wie die anderen gestartet werden, ihre Adresse und wer sie erreichen kann, Version, Prozess,
  Logs, die Hinweise zu Einstellungen, die sie nicht verwendet, und die **Ansichten, die der Server selbst
  beantwortet**, jede verlinkt, mit der Angabe, wer sie öffnen darf (siehe unten). Andere laufende Engines der
  Installation werden in einer Zeile genannt. Ein Server, der auf einem anderen Port antwortet, als velocity.ini der
  Engine vorgibt (von Hand gestartet), wird als solcher gekennzeichnet. Der Kasten liest die Prozesstabelle einmal und
  sendet seinem eigenen Server keine Health-Check-Anfrage (`expVelocity::status( false )`), sodass er der Seite keine
  Wartezeit hinzufügt; `exp:velocity status` behält die Prüfung.
- **„Phar-App-Engine“** (das Kernel-Archiv, `EnginePhar`) wird nur für die Engine qbix angezeigt. Das Archiv läuft
  auch auf den anderen Engines und Servern (`EXP_ENGINE_PHAR`), wird dort aber nicht beschrieben.
- **„OPcache und APCu“**, für jeden Server — Velocity oder nicht, Apache, nginx mit php-fpm: eingeschaltet oder warum
  nicht, Speicher, Einträge, Trefferquote, Neustarts und die Einstellungen, die entscheiden, ob eine bearbeitete
  PHP-Datei übernommen wird (`validate_timestamps`, `revalidate_freq`). `apc.enable_cli` wird nur für
  Kommandozeilen-Server (qbix, php) aufgeführt, die davon abhängen. Die Zahlen sind die des antwortenden
  Serverprozesses selbst. Dargestellt als Balken (Speicher, Füllstand, Trefferquote; grün, ab 75 % gelb, ab 90 % rot —
  bei der Trefferquote umgekehrt), mit Angabe eines anstehenden Zurücksetzens.
- **Setup > Caches** leert beide, in einem Abschnitt „PHP-Caches dieses Serverprozesses“ unter „Den kompletten Cache
  leeren“ (der sie nicht einschließt), für Benutzer mit `setup/managecache`, als POST, der mit dem Formular-Token
  geprüft wird:
  - *OPcache zurücksetzen*: `opcache_reset()` unter php-fpm, FrankenPHP und `php -S` — die nächste Anfrage kompiliert
    von Grund auf. Unter einem Kommandozeilen-Server (qbix) sieht OPcache den Prozess nie untätig, sodass ein
    Zurücksetzen bis zum Neustart des Servers anstehen würde; dort wird stattdessen jedes gecachte Skript ungültig
    gemacht (`opcache_invalidate()`), was sofort wirkt. Nur ein Neustart gibt diesen Speicher zurück.
  - *APCu leeren*: `apcu_clear_cache()`; unter qbix schließt das die Arbeitsspeicherebene seines Antwort-Caches ein.
- Ohne Velocity nennt „Webserver“ den Server aus `SERVER_SOFTWARE` (zum Beispiel nginx 1.14.1, SAPI fpm-fcgi), und es
  gibt keinen Velocity-Kasten.

Tokens werden nie in Links gesetzt; die Seite selbst ist nur für Administratoren.

## Ansichten und wer sie öffnen darf

| Engine | Ansicht | Was | Wer sie öffnen darf |
|---|---|---|---|
| qbix | `/Q/dashboard` | Live-Dashboard | diese Maschine; anderswo mit `[DashboardSettings] Token`. **Ist ein Passwort für das Panel gesetzt, öffnet es nur die Anmeldung des Panels, auch von dieser Maschine aus — das Token nicht** (Qbix `Dashboard::handle()`) |
| qbix | `/Q/stats`, `/Q/metrics`, vollständiges `/Q/health` | Zahlen des Dashboards, Prometheus, Gesundheitsbericht | diese Maschine; anderswo mit dem Token oder der Anmeldung des Panels; oder alle mit `Remote=enabled` und ohne Token |
| qbix | `/Q/phpinfo` | phpinfo() einschließlich der Umgebung des Prozesses | nur diese Maschine; sobald ein Token oder ein Passwort für das Panel existiert, nur damit, auch von dieser Maschine aus |
| qbix | `/Q/panel` | Control Panel | nur diese Maschine, dann das Passwort des Panels (der erste Besuch setzt es) |
| qbix | `/Q/docs`, `/Q/cluster/status`, Status von `/Q/health` | Dokumentation, Zustand des Clusters, „ok“ | alle |
| frankenphp | `/Q/health` | leeres 200 | alle |
| frankenphp | Admin-API | `var/vc/frankenphp/run/admin.sock` | keine Webseite: nur der Benutzer, der den Server betreibt |
| php | `/Q/health` | leeres 200 | alle |

Die Qbix-Regeln sind die eigenen des Servers (`Q_WebServer::adminAllowed()`, v0.0.4.27); die Seite liest das Token,
die Remote-Einstellung und das Passwort des Panels des laufenden Servers, statt die von velocity.ini anzunehmen.

## PHP 8.0 und Red Hat Enterprise Linux

Die Engine `qbix` läuft ab dem Release 0.0.4.54 (20. Oktober 2026) unter PHP 8.0 bis 8.5; ihre `composer.json`
verlangt `"php": ">=8.0"` (bisher `>=8.1`), und unter 8.0 wird nichts zusätzlich gebraucht, kein Polyfill-Paket und
keine Erweiterung. Die `composer.json` von Exponential verlangt die Engine in `~0.0.4.57`, daher installiert
`composer install` unter PHP 8.0 sie ebenfalls.

- **RHEL 9, AlmaLinux 9, Rocky Linux 9**: Das Standard-PHP 8.0 aus AppStream funktioniert, mit dem el9-rpm
  (`exponential-velocity-<release>-1.el9.noarch.rpm`, das von `php-cli >= 8.0` abhängt) oder mit Composer; kein
  Modul-Stream muss aktiviert werden, und ein neuerer funktioniert weiterhin.
- **RHEL 8**: Das Standard-PHP ist 7.2. Führen Sie `dnf module enable php:8.0` aus und verwenden Sie das Phar oder
  Composer; ein el8-rpm gibt es nicht.

Was sich unter PHP 8.0 unterscheidet, und nur das:

| | Unter PHP 8.0 |
|---|---|
| Bildausgabe in AVIF | braucht PHP 8.1; die Aushandlung endet bei WebP |
| Der Speicherhöchstwert pro Anfrage im Dashboard | braucht PHP 8.2; die Spalte ist leer |
| `$_FILES` `full_path` | fehlt, wie in PHP 8.0 selbst |
| Eine mit `eval()` deklarierte Klasse mit statischen Eigenschaften | ihr Worker wird nach dieser Anfrage ersetzt (korrekt; es kostet einen Fork) |

Den Rest erledigt die Engine selbst: Sie liest deklarierte statische Standardwerte aus dem Quelltext (das
`getDefaultValue()` von PHP 8.0 gibt den aktuellen Wert zurück), ruft unter 8.0 `setAccessible()` für private und
geschützte statische Eigenschaften auf und leert den Stat-Cache für das Log-Ende im Dashboard. Die Zygote läuft unter
8.0. Erweiterungen, systemd, SELinux und die Überprüfung stehen auf der Seite
[PHP 8.0 and Red Hat Enterprise Linux](https://github.com/se7enxweb/exponential-velocity/blob/main/docs/rhel.md) der
Engine, ihre Mindestanforderungen in den
[Anforderungen](https://github.com/se7enxweb/exponential-velocity/blob/main/docs/requirements.md), das Release in
ihrem [Changelog](https://github.com/se7enxweb/exponential-velocity/blob/main/CHANGELOG.md). Für den Kernel unter
PHP 8.0 siehe [Unterstützung für PHP 8.0](php-8.0-unterstuetzung.md).

## Die Engine selbst: Releases, Hinweise zum Upgrade und Funktionsseiten

Diese Seite handelt davon, die Engine aus Exponential heraus zu steuern. Was die Engine ist, wie sie entstanden ist und
was jedes ihrer Releases geändert hat, ist an anderer Stelle dokumentiert:

| Sie möchten | Lesen Sie |
|---|---|
| Was sich in einem Release geändert hat | [Changelog der Velocity-Engine](../../../changelogs/extensions/exponential-velocity.md) |
| Was Sie beim Upgrade der Engine ändern müssen (0.0.4.27 bis 0.0.4.42) | [Hinweise zum Upgrade der Velocity-Engine](velocity-engine-upgrade-hinweise.md) |
| Die Geschichte Monat für Monat | [Velocity-Chronik](../../geschichte/velocity/README.md) |
| Webserver, Control Panel, Cache, HTTPS, Shell, Pakete | [Webserver](../../funktionen/6.0/velocity-webserver.md), [Control Panel](../../funktionen/6.0/velocity-control-panel.md), [Antwort-Cache](../../funktionen/6.0/velocity-antwort-cache.md), [HTTPS](../../funktionen/6.0/velocity-https-zertifikate.md), [Q-Shell](../../funktionen/6.0/velocity-q-shell.md), [Pakete und Binärdateien](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md) |
| Jede Einstellung der Engine | [Einstellungen der Engine](../../spezifikationen/6.0/velocity-engine-einstellungen.md), [Worker-Pool](../../spezifikationen/6.0/velocity-worker-pool.md), [HTTP/2 und Sicherheit](../../spezifikationen/6.0/velocity-http2-und-sicherheit.md) |

Die Zugriffsregeln unter „Ansichten und wer sie öffnen darf“ oben sind diejenigen, die `exp:velocity` in die
Konfiguration der Engine schreibt; sie können strenger sein als die eigenen Standardwerte der Engine, die auf der Seite
zum Control Panel beschrieben sind.

**Demomodus unter Velocity.** Die Worker von Velocity erben die Umgebung des Serverprozesses. Die gemeinsame
`/etc/vc/envvars` liest jedoch der Server **jeder** Installation auf dem Host, so wie jeder virtuelle Host
`/etc/apache2/envvars` liest; `EXP_DEMO_MODE` wird von dort nie übernommen, und ein Worker lehnt einen solchen Wert ab
(eine Zeile `demo/refused` in `var/log/security.log`). Für eine öffentliche Demo fügen Sie `export EXP_DEMO_MODE=1` zu
den Site-envvars der Demo hinzu, `/etc/vc/sites-available/<site>.envvars` (`<site>` und die Zeile `site envvars` zeigt
`./console exp:velocity layout`); `exp:velocity start` liest sie nur für den Server dieser Installation, sodass
Neustarts und Deploys aus jeder Shell es behalten. Die Umgebung, die den Server dieser Installation startet (das
`Environment=` ihrer eigenen Unit, siehe
[`velocity-demo-mode.conf`](../../../eng-us/examples/demo-mode/velocity-demo-mode.conf)), funktioniert ebenfalls. Die
Datei kann `PATH`, `LD_*`, `PHPRC` und die anderen Variablen des Loaders und der Shell nicht setzen
([Verzeichnisstruktur](velocity-verzeichnisstruktur.md)). Ein Neustart mit dem eigenen `qbixctl` der Engine liest die
Site-envvars nicht. [Demomodus](demomodus.md#velocity-nur-eine-installation).

## Verwandte Seiten

- [Velocity: Exponential in einem Webserver mit persistenten Workern betreiben](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)
- [Hinweise zum Upgrade der Velocity-Engine](velocity-engine-upgrade-hinweise.md)
- [Die Verzeichnisstruktur von Velocity](velocity-verzeichnisstruktur.md)
- [HTTP/2 und das Vorwärmen von Caches](http2-und-cache-vorwaermen.md)
- [FrankenPHP](frankenphp.md)
- [Engine-Archiv (phar)](phar.md)
- [Anleitung zum Bereitstellen](../../anleitungen/bereitstellung.md)
- [.htaccess und config.php von der Konsole aus einrichten](../../anleitungen/setup-dateien-von-der-konsole.md)
- [Anleitung zum Benchmarking](../../anleitungen/benchmarking.md): die Engines gegeneinander messen
