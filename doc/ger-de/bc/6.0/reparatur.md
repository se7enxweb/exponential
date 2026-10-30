# Eine Installation reparieren, deren Bibliotheken fehlen

Lesen Sie diese Seite, wenn Ihre Website „the libraries of this installation are missing“ anzeigt, jede Seite mit HTTP
503 beantwortet, oder wenn Sie wissen möchten, was Exponential tut, wenn `vendor/` fehlt. Ohne ihre
Composer-Bibliotheken kann die Website nicht starten: Jede Klasse aus einer Bibliothek ist „not found“ (die erste ist
meist `ezcBaseOptions`). Das geschieht, wenn `vendor/` verschoben, umbenannt oder gelöscht wurde, wenn eine
Installation ohne das Verzeichnis kopiert wurde oder wenn `composer install` nie ausgeführt wurde.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../beispiele/webserver/nginx.md), [LiteSpeed](../../beispiele/webserver/litespeed.md), [Caddy](../../beispiele/webserver/caddy.md), [IIS](../../beispiele/webserver/iis.md),
> [Velocity](../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hilfe/hosting/control-panels/plesk.md), [cPanel](../../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../../hilfe/hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

Exponential 6.0.15 erkennt diesen Zustand und zeigt statt der allgemeinen Fehlerseite eine eigene Seite, die erklärt,
was falsch ist und wie es sich beheben lässt. Mit einem einmaligen Reparaturschlüssel kann ein Administrator die
Installation außerdem von dieser Seite aus im Browser reparieren.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Eine eigene Seite mit HTTP 503 für fehlende Bibliotheken, mit einer optionalen Reparatur im Browser, die ein einmaliger Schlüssel startet. |
| Wer betroffen ist | Jede Installation, deren `vendor/` fehlt. Solange die Bibliotheken vorhanden sind, ändert sich nichts. |
| Wie Sie es prüfen | `ls vendor/autoload.php ../vendor/autoload.php` — existiert keine der beiden Dateien, wird die Seite angezeigt. |
| Wie Sie es beheben | Stellen Sie `vendor/` wieder her oder führen Sie `composer install` aus; oder erzeugen Sie einen Reparaturschlüssel und reparieren Sie von der Seite aus. |

## In zwei Minuten beheben

**Wurde `vendor/` verschoben oder umbenannt**, stellen Sie es wieder in das Verzeichnis der Installation. Fertig.

**Andernfalls** installieren Sie die Bibliotheken entweder selbst:

```bash
composer install --no-dev          # in the installation directory; --no-dev on a production server
php bin/php/ezcache.php --clear-all --allow-root-user
```

(unter Exponential Velocity starten Sie ihn danach neu), oder reparieren Sie im Browser:

```bash
sudo -u <web server user> php bin/php/exprepair.php --create-key
# Repair key (shown once, used once): AbC...
```

Öffnen Sie eine beliebige Seite der Website, fügen Sie den Schlüssel in das Feld ein und drücken Sie die
Eingabetaste. Die Seite führt die Reparatur aus und öffnet die Website wieder, wenn sie fertig ist.

## Die Seite

Jede Seite der Website antwortet mit **HTTP 503** und `Retry-After: 60` und wird nie gecacht, denn die Website ist
nicht kaputt, nur unvollständig installiert. Die Seite zeigt:

- das Exponential-Logo, die Erklärung und drei Schritte für den Administrator:
  1. Wurde `vendor/` verschoben oder umbenannt, stellen Sie es wieder in das Verzeichnis der Installation.
  2. Andernfalls installieren Sie die Bibliotheken: Führen Sie im Verzeichnis der Installation `composer install` aus
     (auf einem Produktivserver mit `--no-dev`).
  3. Leeren Sie dann die Caches (`php bin/php/ezcache.php --clear-all`) und starten Sie unter Exponential Velocity
     dieses neu.
- das Reparaturfeld (siehe unten) oder wie Sie es einrichten;
- die Fehlerreferenz (dieselbe Referenz steht in `var/log/error.log`), das technische Detail bei eingeschalteter
  Debug-Ausgabe und die Copyright-Fußzeile.

Die allgemeine Fehlerseite (500 oder 503 bei anderen Fehlern) ist unverändert. Der Fall wird anhand des Zustands der
Installation gewählt, nicht anhand der Fehlermeldung: Der Autoloader von Composer ist nicht geladen, und weder
`vendor/autoload.php` noch `../vendor/autoload.php` existiert (`eZExecution::errorCase()`). Daher funktioniert es auch
bei ausgeschalteter Debug-Ausgabe.

## Im Browser reparieren

### Schritt 1: einen Reparaturschlüssel erzeugen, als Benutzer des Webservers

```bash
sudo -u <web server user> php bin/php/exprepair.php --create-key
# Repair key (shown once, used once): AbC...
```

Der Schlüssel wird einmal angezeigt. Gespeichert wird nur sein Hash, in
`settings/override/exprepair.ini.append.php` (Modus 0640; committen Sie den Baum `settings/override/` nie). Der Befehl
läuft ohne die Bibliotheken, sodass Sie ihn verwenden können, während die Website ausgefallen ist.

### Schritt 2: die Reparatur auf der Seite starten

Öffnen Sie eine beliebige Seite der Website. Der Cursor steht im Schlüsselfeld: Fügen Sie den Schlüssel ein und
drücken Sie die Eingabetaste. Die Seite zeigt dann die Statusleiste des Installationsprogramms:

- die drei Schritte, jeweils **wartend**, **laufend**, **erledigt** oder **fehlgeschlagen**;
- einen Fortschrittsbalken, die verstrichene Zeit und den Benutzer, unter dem die Reparatur läuft;
- das Ende des Logs, alle zwei Sekunden aktualisiert;
- wenn sie fertig ist, öffnet sich die Seite nach fünf Sekunden erneut (oder über die Schaltfläche).

Die Schritte sind fest:

| Schritt | Befehl |
|---|---|
| Die Composer-Bibliotheken installieren | `composer install --no-dev --no-interaction --no-plugins --no-scripts --no-progress` |
| Die Autoload-Arrays neu erzeugen | `php bin/php/ezpgenerateautoloads.php -e`, dann `-k` (mit ausgeschlossenen Arbeitskopien innerhalb der Installation) |
| Die Caches leeren | `php bin/php/ezcache.php --clear-all` |

`--no-plugins` bedeutet, dass das Installer-Plugin, das Erweiterungspakete in `extension/` ablegt, nicht ausgeführt
wird, sodass in `extension/` nichts geändert wird. Erweiterungen, die Git-Checkouts sind, behalten ihre
Arbeitsverzeichnisse. Die Pakete werden stattdessen in `vendor/se7enxweb/` installiert, wo nichts sie lädt (der
Autoloader von Composer bildet nur Pakete ab, die Autoloading deklarieren, etwa die Velocity-Engine).

### Schritt 3: wenn die Lock-Datei nicht zu composer.json passt

Verlangt `composer.json` Versionen, die `composer.lock` nicht enthält, verweigert `composer install` (Exit-Code 4). Die
Statusleiste sagt das und bietet **Update the lock file and install** an. Nach einer Bestätigung führt es
`composer update --no-dev --no-interaction --no-plugins --no-scripts --no-progress` aus, das eine neue
`composer.lock` schreibt und diese Versionen installiert, gefolgt von den beiden anderen Schritten. Dieser zweite Lauf
ist durch das Token des fehlgeschlagenen Laufs berechtigt, innerhalb von 15 Minuten; ein neuer Schlüssel ist nicht
nötig.

Um die Lock-Datei zu aktualisieren, ohne etwas zu installieren (zum Beispiel in einem Repository, vor einem Release):

```bash
sudo -u <web server user> composer update --no-install --no-plugins --no-scripts --no-dev
```

## Wie es abgesichert ist

- **Die Web-Anfrage führt keinen Befehl aus.** Sie prüft den Schlüssel, schreibt den Auftrag nach
  `var/repair/status.json` und startet den Worker im Hintergrund (`setsid php bin/php/exprepair.php --run`). Der Worker
  führt die feste Befehlsliste mit `proc_open` und Argument-Arrays aus; nichts aus der Anfrage erreicht eine Shell.
- **Einmaliger Schlüssel.** Der Start einer Reparatur entfernt den Hash des Schlüssels; die nächste Reparatur braucht
  einen neuen Schlüssel. Fünf falsche Schlüssel innerhalb von 15 Minuten sperren das Formular für 15 Minuten
  (`var/repair/failures.json`).
- **Starten funktioniert nur, solange die Bibliotheken fehlen.** Sobald `vendor/autoload.php` existiert, geht eine
  Startanfrage wie jede andere Anfrage an den Kernel.
- **Status und Update brauchen das Token des Laufs**, einen zufälligen Wert, der beim Start des Laufs zurückgegeben
  wird.
- **Ein Lauf auf einmal** (`var/repair/worker.lock`). Ein Lauf, der zwei Minuten lang nichts mehr meldet, darf durch
  einen neuen ersetzt werden.
- **Der richtige Besitzer für jede Datei.** Der Worker läuft als Besitzer der Installation (Besitzer und Gruppe von
  `index.php`, der Benutzer des Webservers). Als root gestartet, wie unter Exponential Velocity, wechselt er vor allem
  anderen zu diesem Benutzer (`posix_initgroups`, `posix_setgid`, `posix_setuid`) und setzt `HOME` für Composer.
- **Kein Cron und kein Dienst nötig.** Die Seite startet den Worker selbst.
- `var/repair/` wird mit Modus 0770 und einer `.htaccess` angelegt, die den Webzugriff verweigert.

## Wo die Anfragen beantwortet werden

Die Anfragen der Seite tragen `exp_repair`:

| Anfrage | Beantwortet von | Wann |
|---|---|---|
| `POST exp_repair=start`, `exp_repair_key` | `index.php`, vor dem Kernel (`ezpRepairQueue::handleRunRequest()`) | nur solange die Bibliotheken fehlen |
| `GET exp_repair=status&token=…` | `index.php`, vor dem Kernel | jederzeit, mit dem Token des Laufs |
| `POST exp_repair=update`, `token` | `index.php`, vor dem Kernel | nachdem ein Lauf an der Lock-Datei gescheitert ist, innerhalb von 15 Minuten |

Sie werden vor `autoload.php` beantwortet, weil die Reparatur die Bibliotheken auf halbem Weg zurückbringt: Ab dann
startet der Kernel wieder, und seine Seiten würden die Antworten der Seite ersetzen.

## Kommandozeile

Führen Sie diese als Benutzer des Webservers aus. Es ist reines PHP, und es funktioniert, solange die Bibliotheken
fehlen.

```bash
php bin/php/exprepair.php --create-key   # a new one-time key (shown once)
php bin/php/exprepair.php --disable      # turn the page's repair off (removes the key)
php bin/php/exprepair.php --status       # the state of the last repair and the end of its log
php bin/php/exprepair.php --run          # run the queued repair (the page starts this itself)
```

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/override/exprepair.ini.append.php` | `RepairSettings` | `Enabled` | von `--create-key` als `true` geschrieben | Installation |
| `settings/override/exprepair.ini.append.php` | `RepairSettings` | `KeyHash` | der `password_hash` des Schlüssels | Installation |
| `settings/override/exprepair.ini.append.php` | `RepairSettings` | `Composer` | `composer` im PATH | Installation |
| `settings/error.ini` | `ErrorSettings` | `StaticErrorPage[dependencies]` | die eingebaute Seite | Installation |

`--create-key` und `--disable` schreiben die erste Datei:

```ini
[RepairSettings]
Enabled=true
KeyHash=<password_hash of the key>
# optional: the composer binary to use (default: composer on the PATH)
Composer=/usr/local/bin/composer
```

Um für den Fall fehlender Bibliotheken eine eigene Seite zu verwenden, setzen Sie in
`settings/error.ini [ErrorSettings]`:

```ini
# placeholders: {status} {title} {message} {steps} {repair} {reference} {home} {detail}
StaticErrorPage[dependencies]=design/standard/errors/dependencies.html
```

## Dateien

| Datei | Was |
|---|---|
| `lib/ezutils/classes/ezexecution.php` | `errorCase()`, `renderErrorCasePage()`: die Seite |
| `lib/ezutils/classes/ezprepairqueue.php` | `ezpRepairQueue`: Schlüssel, Warteschlange, Worker, das Feld und seine Statusleiste |
| `bin/php/exprepair.php` | die Kommandozeile und der Worker (`--run`) |
| `index.php` | beantwortet die Anfragen der Reparatur vor dem Kernel |
| `var/repair/` | `status.json`, `repair.log`, `worker.lock`, `failures.json` |

## Fehlerbehebung

| Was Sie sehen | Warum | Was zu tun ist |
|---|---|---|
| „To repair from this page, create a one-time repair key“ | Es ist kein Schlüssel eingerichtet, oder die letzte Reparatur hat ihn verbraucht. | `php bin/php/exprepair.php --create-key` |
| „No repair key is set up on the server.“ | Der Schlüssel wurde von einer bereits gestarteten Reparatur verbraucht. | Erzeugen Sie einen neuen Schlüssel; `--status` zeigt diesen Lauf. |
| „That is not the repair key.“ | Ein verbrauchter oder falsch eingegebener Schlüssel. | Erzeugen Sie einen neuen Schlüssel. Fünf falsche Schlüssel sperren das Formular für 15 Minuten. |
| Schritt 1 fehlgeschlagen; das Log sagt, die Lock-Datei erfülle composer.json nicht | `composer.lock` ist veraltet. | Drücken Sie **Update the lock file and install**, oder aktualisieren Sie die Lock-Datei auf der Kommandozeile. |
| Schritt 1 mit „composer: not found“ fehlgeschlagen | Composer ist nicht im PATH des Webserver-Benutzers. | Setzen Sie `Composer=/path/to/composer` in der Einstellungsdatei. |
| Die Schritte verlassen nie „wartend“ | Der Worker konnte nicht starten (PHP-Binärdatei, `exec` deaktiviert). | Führen Sie `php bin/php/exprepair.php --run` als Benutzer des Webservers aus; prüfen Sie `var/repair/worker.out`. |
| Velocity schlägt nach einer Reparatur weiter fehl | Seine Worker haben den alten Zustand geladen. | `./console exp:velocity restart --allow-root-user` |

## Verwandte Seiten

- [Reparatur im Browser](../../funktionen/6.0/reparatur-im-browser.md)
- [Verhaltensänderungen vom 1. und 2. Oktober 2026](verhaltensaenderungen-2026-10.md)
- [Velocity-Engines](velocity-engines.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
