# .htaccess und config.php von der Konsole aus einrichten

Diese Anleitung ist für alle, die eine Exponential-Website installieren oder betreiben: Administratoren, Hoster und
Entwickler, die Installationen per Skript aufsetzen. Zwei Dateien im Wurzelverzeichnis jeder Installation werden aus
mitgelieferten Vorlagen kopiert, und eine davon wird danach bearbeitet:

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../beispiele/webserver/nginx.md), [LiteSpeed](../beispiele/webserver/litespeed.md), [Caddy](../beispiele/webserver/caddy.md), [IIS](../beispiele/webserver/iis.md),
> [Velocity](../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../hilfe/hosting/control-panels/plesk.md), [cPanel](../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../hilfe/hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](seiten-cache-mit-litespeed.md).

| Mitgelieferte Vorlage | Datei, die die Website nutzt | Wofür |
|---|---|---|
| `.htaccess_root` | `.htaccess` | Die Rewrite- und Schutzregeln für Apache und LiteSpeed |
| `.htaccess_root_static` | `.htaccess` | Dieselben Regeln und dazu die, die den statischen Cache vor dem CMS ausliefern |
| `config.php-RECOMMENDED` | `config.php` | Die wenigen Einstellungen, die vor den INI-Dateien bekannt sein müssen: Dateimodi, INI-Prüfung, Cluster-Speicher, Log-Rotation |

Bisher hieß das `cp -va .htaccess_root .htaccess`, `cp -va config.php-RECOMMENDED config.php` und danach ein Editor,
um die `//` vor den gewünschten Zeilen zu entfernen. Die Konsole erledigt das alles für Sie, und zwar sicher:

```bash
./console exp:setup:status                          # von welcher Vorlage jede Datei stammt
./console exp:setup:htaccess --apply                # .htaccess_root -> .htaccess
./console exp:setup:htaccess --static --apply       # .htaccess_root_static -> .htaccess
./console exp:setup:config --apply                  # config.php-RECOMMENDED -> config.php
./console exp:config:list                           # jede Einstellung, ihr Wert, der vorgeschlagene, was sie tut
./console exp:config:get EZP_INI_FILEMTIME_CHECK    # ein Wert, für einen Menschen oder ein Skript
./console exp:config:set EZP_INI_FILEMTIME_CHECK false --apply
./console exp:config:unset EZP_INI_FILEMTIME_CHECK --apply
```

Zeit: 10 Minuten. Sie brauchen Shell-Zugang zum Installationsverzeichnis (dem Ordner mit `index.php` und `console`).
Führen Sie die Befehle als der Benutzer aus, dem die Installation gehört. Als root braucht jeder Befehl
`--allow-root-user`, wie jeder Exponential-Befehl; die Beispiele unten lassen es weg.

Jedes Ergebnis auf dieser Seite stammt aus einem echten Lauf des Befehls; das Installationsverzeichnis erscheint als
`/var/www/example`, sein Eigentümer als `www-data:www-data`, Benutzer und Gruppe der Beispielinstallation. Die Ausgabe der
Befehle ist englisch und wird so gezeigt, wie sie erscheint.

## 1. Die Regeln, die jeder Befehl einhält

Sie gelten für alle sieben Befehle, Sie können sich in Skripten darauf verlassen:

- **Ohne `--apply` wird nichts geschrieben.** Ohne die Option zeigt ein Befehl, was er tun würde, und endet mit 0
  (oder 1, wenn er ablehnen würde). `--dry-run` wird angenommen und bedeutet dasselbe wie kein `--apply`; beide
  zusammen sind ein Bedienfehler, Exit-Code 2.
- **Eine vorhandene Datei wird nie stillschweigend ersetzt.** `exp:setup:htaccess` und `exp:setup:config` zeigen den
  Unterschied zwischen Datei und Vorlage und lehnen ab (Exit-Code 1), solange Sie nicht `--force` angeben.
- **Vor jedem Schreiben wird gesichert.** Die Datei, wie sie war, kommt nach `var/backup/setup/<zeit>/` (Kopien) oder
  `var/backup/config/<zeit>/` (Änderungen an `config.php`), mit ihrem Modus und, wenn der Befehl als root läuft, mit
  Eigentümer und Gruppe. Die Sicherungsverzeichnisse bekommen den Modus `0700`, weil `config.php` Passwörter
  enthalten kann; `var/backup/` wird von der mitgelieferten `.htaccess` nicht ausgeliefert.
- **Geschrieben wird atomar.** Der neue Inhalt geht in eine temporäre Datei im selben Verzeichnis, die den richtigen
  Modus und Eigentümer bekommt und dann über die alte Datei umbenannt wird. Der Webserver liest nie eine halbe Datei.
- **`config.php` wird vor dem Schreiben geprüft.** Die neue Datei muss `php -l` bestehen und den gewünschten Wert
  zurücklesen lassen; sonst ändert sich nichts.
- **Passwörter erscheinen nie versehentlich.** Der Wert einer Einstellung, deren Name `PASS`, `SECRET`, `KEY`,
  `TOKEN`, `CREDENTIAL` oder `PWD` enthält, wird als `********` gezeigt, in Werten und in Diffs, außer mit
  `--show-secrets`.
- **Exit-Codes:** 0 erledigt oder nichts zu tun, 1 abgelehnt oder fehlgeschlagen (nichts geändert), 2 Bedienfehler.
- **Die Ausgabe endet mit `PASS:` oder `FAIL:`**, so sieht ein Skript oder ein Mensch das Ergebnis in der letzten Zeile.

## 2. Sehen, wo Sie stehen

```bash
./console exp:setup:status
```

In einem frisch ausgecheckten Verzeichnis:

```text
Installation: /var/www/example
  .htaccess   missing: ./console exp:setup:htaccess --apply copies .htaccess_root (with --static: .htaccess_root_static)
  config.php  missing: ./console exp:setup:config --apply copies config.php-RECOMMENDED
PASS: shown
```

Später sagt der Befehl, von welcher Vorlage `.htaccess` eine Kopie ist (`.htaccess_root`, `.htaccess_root_static`
oder keiner: von Hand geändert oder aus einer älteren Version), und ob `config.php` eine unveränderte Kopie ist, wie
viele Einstellungen sie setzt oder dass sie sich nicht kompilieren lässt.

## 3. .htaccess an ihren Platz bringen

Erst ansehen:

```bash
./console exp:setup:htaccess
```

```text
Installation: /var/www/example
Faster: Apache reads .htaccess on every request; put these rules in the virtual host with AllowOverride None instead (doc/eng-us/examples/web-servers/apache-virtual-host.md).
Would copy .htaccess_root to .htaccess (mode 0644, owner www-data:www-data)
PASS: dry run, nothing changed; --apply copies
```

Dann kopieren. `-v` gibt die Kopie so aus wie `cp -v`:

```bash
./console exp:setup:htaccess --apply -v
```

```text
Installation: /var/www/example
Faster: Apache reads .htaccess on every request; put these rules in the virtual host with AllowOverride None instead (doc/eng-us/examples/web-servers/apache-virtual-host.md).
'.htaccess_root' -> '.htaccess'
PASS: .htaccess_root copied to .htaccess (mode 0644, owner www-data:www-data)
Apache and LiteSpeed read .htaccess on the next request; nginx, Caddy and IIS never read it, and Velocity has the rules of .htaccess_root built in.
```

Ein zweiter Lauf ändert nichts:

```text
Installation: /var/www/example
Faster: Apache reads .htaccess on every request; put these rules in the virtual host with AllowOverride None instead (doc/eng-us/examples/web-servers/apache-virtual-host.md).
PASS: .htaccess is already a copy of .htaccess_root; nothing to do
```

Welche Webserver die Datei lesen:

| Server | Liest `.htaccess`? | Was stattdessen zu tun ist |
|---|---|---|
| Apache mit PHP-FPM oder mod_php | ja, mit `AllowOverride All` | nichts: `exp:setup:htaccess --apply` genügt |
| LiteSpeed Enterprise | ja, wie Apache | nichts weiter |
| OpenLiteSpeed | nur die Rewrite-Regeln, nur mit `autoLoadHtaccess` und erst nach einem Neustart | die Regeln in den virtuellen Host übernehmen: [LiteSpeed](../beispiele/webserver/litespeed.md) |
| Exponential Velocity | nein, die Regeln von `.htaccess_root` sind eingebaut | nichts; kopieren Sie sie trotzdem, wenn Apache denselben Baum ausliefern kann |
| nginx, Caddy, IIS | nein | die Beispielkonfigurationen unter [Webserver-Beispiele](../beispiele/webserver/README.md) verwenden |

### Gemessen: .htaccess gegen Regeln im virtuellen Host

Mit `AllowOverride All` sucht Apache bei jeder Anfrage in jedem Verzeichnis des Pfads nach einer `.htaccess` und
liest und wertet die gefundenen Regeln aus, bei jeder Anfrage. Mit `AllowOverride None` und denselben Regeln in der
Konfiguration des virtuellen Hosts liest er sie einmal beim Start. Um zu sehen, was das kostet, lieferten zwei
Wegwerf-Instanzen von Apache 2.4 dasselbe Verzeichnis mit den Regeln der mitgelieferten `.htaccess_root` aus: die eine
als `.htaccess` mit `AllowOverride All`, die andere mit den Regeln im `<Directory>`-Block ihrer Konfiguration und
`AllowOverride None`. PHP war nicht beteiligt, gemessen ist also die Arbeit von Apache selbst; `ab -k -c 8`, 20.000
Anfragen pro Runde, 9 abwechselnde Runden, der Median:

| Anfrage | `.htaccess`, `AllowOverride All` | Regeln in der Konfiguration, `AllowOverride None` | Unterschied |
|---|---|---|---|
| Ein statisches Bild vier Verzeichnisse tief (`/var/storage/images/media/images/x/photo.png`) | 988 Anfragen/s | 1.419 Anfragen/s | 44 % mehr Anfragen pro Sekunde |
| Eine Seitenadresse, umgeschrieben auf `index.php` (`/some/page/of/the/site`) | 1.079 Anfragen/s | 1.303 Anfragen/s | 21 % mehr Anfragen pro Sekunde |

Der Server war während der Messung mit anderer Arbeit ausgelastet (Load Average um 50), einzelne Runden schwankten
also; das Bild war ohne `.htaccess` in jeder Runde schneller, die Seitenadresse in fünf von neun, in den übrigen etwa gleich schnell. Tiefere Pfade
(Bilder, der Speicher, Assets von Extensions) zahlen mehr, weil es mehr Verzeichnisse zu durchsuchen gibt. Messen Sie
auf Ihrem eigenen Server genauso vor und nach dem Umstellen.

Zum Umstellen: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../beispiele/webserver/apache-virtueller-host.md)
in Ihren virtuellen Host (mit dem statischen Cache auch die aus
[`staticcache_offload.conf`](../../eng-us/examples/staticcache_offload.conf)), setzen Sie dort `AllowOverride None`,
prüfen Sie mit `apachectl configtest`, laden Sie Apache neu (`systemctl reload apache2`, auf Systemen im Stil von
Red Hat `httpd`) und testen Sie die Website. Behalten Sie `exp:setup:htaccess` für den Vergleich nach einem Upgrade:
sein Diff zeigt die Regeln, die eine neue Version geändert hat.

### Auf die Regeln für den statischen Cache umstellen

Wenn Sie einen statischen Cache erzeugen (Setup > Cache > Statischer Inhalts-Cache), liefert `.htaccess_root_static`
diese Seiten aus, bevor das CMS startet. Ihre Regeln sind die von `.htaccess_root` plus die Auslieferungsregeln, das
Umstellen ist also eine Kopie der anderen Datei. Da `.htaccess` nun existiert und abweicht, zeigt der Befehl den
Unterschied und lehnt ab:

```bash
./console exp:setup:htaccess --static
```

```text
Installation: /var/www/example
Faster: Apache reads .htaccess on every request; put these rules in the virtual host with AllowOverride None instead (doc/eng-us/examples/web-servers/apache-virtual-host.md).
.htaccess differs from .htaccess_root_static (- what .htaccess has, + what .htaccess_root_static has):
  --- .htaccess
  +++ .htaccess_root_static
  @@ -1,5 +1,27 @@
   # Copy this file to a new file called .htaccess in your Exponential root
  -# to secure your installation by turning on .htaccess based virtual host mode.
  +# to secure your installation by turning on .htaccess based virtual host mode
  +# AND serve the static cache ahead of the CMS.
  ...
  ... 73 more lines of the diff (12 lines removed, 64 added in all); -v shows the whole diff
FAIL: .htaccess exists and differs from .htaccess_root_static; nothing was changed. --force --apply replaces it (the current file is backed up to var/backup/setup/<time>/ first)
```

Der Diff wird nach 40 Zeilen gekürzt; `-v` zeigt ihn ganz. Lesen Sie ihn: Hat Ihre `.htaccess` eigene Regeln (eine
Weiterleitung, eine IP-Sperre), stehen sie in den `-`-Zeilen und sind nach der Kopie weg. Dann ersetzen Sie sie:

```bash
./console exp:setup:htaccess --static --force           # was passieren würde
./console exp:setup:htaccess --static --force --apply -v
```

```text
'.htaccess' -> 'var/backup/setup/20261029-130242/.htaccess'
'.htaccess_root_static' -> '.htaccess'
Backup: var/backup/setup/20261029-130242/.htaccess
PASS: .htaccess replaced with .htaccess_root_static (mode 0644, owner www-data:www-data)
```

Zurück geht es mit `./console exp:setup:htaccess --force --apply` (die einfachen Regeln) oder indem Sie die Sicherung
von Hand zurückkopieren.

## 4. config.php an ihren Platz bringen

```bash
./console exp:setup:config --apply
```

```text
Installation: /var/www/example
PASS: config.php-RECOMMENDED copied to config.php (mode 0644, owner www-data:www-data)
Next: ./console exp:config:list shows its settings, ./console exp:config:set <NAME> <value> sets one. A persistent server (Velocity, FrankenPHP worker) reads config.php again after a restart.
```

Eine Kopie von `config.php-RECOMMENDED` setzt nichts: Jede Einstellung darin ist ein Kommentar. Sie ist der
dokumentierte Ausgangspunkt, und die Befehle unten nehmen diese Kommentare als Liste der bekannten Einstellungen.

Gibt es `config.php` schon und weicht sie ab, zeigt der Befehl den Diff, sagt, wie viele Einstellungen sie setzt, und
lehnt ohne `--force` ab, genau wie bei `.htaccess`. Passwörter im Diff werden maskiert:

```text
config.php differs from config.php-RECOMMENDED (- what config.php has, + what config.php-RECOMMENDED has):
  ...
  -define( 'CLUSTER_STORAGE_PASS', '********' );
  +// define( 'CLUSTER_STORAGE_PASS', '********' );
  ...
  (config.php sets 3 setting(s); ./console exp:config:list --only-set shows them)
FAIL: config.php exists and differs from config.php-RECOMMENDED; nothing was changed. --force --apply replaces it (the current file is backed up to var/backup/setup/<time>/ first)
```

Wer eine funktionierende `config.php` durch die Vorlage ersetzt, verliert jede Einstellung darin. Um bewusst neu
anzufangen, nehmen Sie `--force --apply`; die alte Datei liegt in `var/backup/setup/<zeit>/config.php`.

## 5. Die Einstellungen lesen

```bash
./console exp:config:list
```

listet jede Einstellung, die `config.php-RECOMMENDED` beschreibt, nach ihrem Abschnitt gruppiert, mit dem Wert in
`config.php`, dem von der Vorlage vorgeschlagenen Wert und dem ersten Satz ihrer Beschreibung. Einstellungen, die
`config.php` definiert, die Vorlage aber nicht beschreibt, kommen zuletzt. Ein Teil der Ausgabe:

```text
config.php: 0 setting(s) set, 30 documented in config.php-RECOMMENDED

INI FILES OPTIMIZATIONS
  EZP_INI_FILEMTIME_CHECK              (not set)
      RECOMMENDED false - This new setting controls whether eZINI should check if the settings have been updated...

FILE PERMISSIONS
  EZP_INI_FILE_PERMISSION              (not set)
      RECOMMENDED 0644 - Set EZP_INI_FILE_PERMISSION constant to the permissions you want the INI cache files (v...
  EZP_DIR_MODE_MAX                     (not set)
      RECOMMENDED 0750 - Set EZP_DIR_MODE_MAX and EZP_FILE_MODE_MAX to the widest modes any directory and any fi...

CLUSTERING
  CLUSTER_STORAGE_HOST                 (not set)
      RECOMMENDED 'localhost' - Cluster storage host.
  CLUSTER_STORAGE_PASS                 (not set)
      RECOMMENDED '********' - Cluster storage password
  ...

(not set): the built-in default applies. RECOMMENDED: the value config.php-RECOMMENDED proposes, an optimized value or an example. ./console exp:config:get <NAME> --format=json shows the whole description.
```

`(not set)` heißt: Der eingebaute Standardwert gilt. `RECOMMENDED` ist der Vorschlag der Vorlage: bei manchen
Einstellungen der optimierte Wert, bei anderen (den Cluster-Einstellungen) ein Beispiel, das Sie ersetzen. Optionen:

| Option | Wirkung |
|---|---|
| `--only-set` | Nur die Einstellungen, die `config.php` setzt (schneller Überblick über einen Server) |
| `--format=json` | Alles als JSON: Name, Zustand, Typ, Wert, Literal, Zeile, Geheimnis, beschrieben, Vorschlag, Abschnitt, Zusammenfassung und die ganze Beschreibung |
| `--show-secrets` | Die Werte von Passwörtern und Schlüsseln anzeigen |

Ein einzelner Wert:

```bash
./console exp:config:get EZP_INI_FILEMTIME_CHECK
```

```text
false
```

Der Wert steht allein auf der Standardausgabe, ein Skript kann ihn also verwenden:
`check=$(./console exp:config:get EZP_INI_FILEMTIME_CHECK)`. Zeichenketten erscheinen ohne Anführungszeichen, Modi mit
ihrer führenden `0` (`0640`). Eine nicht gesetzte Einstellung ergibt eine Meldung auf der Standardfehlerausgabe und
den Exit-Code 1:

```text
EZP_INI_FILEMTIME_CHECK is not set in config.php (config.php-RECOMMENDED proposes false)
```

Ein Passwort bleibt verborgen, solange Sie nicht danach fragen:

```bash
./console exp:config:get CLUSTER_STORAGE_PASS                  # ********
./console exp:config:get CLUSTER_STORAGE_PASS --show-secrets
```

`--format=json` liefert den ganzen Eintrag, mit der ganzen Beschreibung aus der Vorlage:

```text
{
    "name": "EZP_FILE_MODE_MAX",
    "state": "set",
    "type": "octal",
    "value": 416,
    "raw": "0640",
    "text": "0640",
    "line": 112,
    "secret": false,
    "documented": true,
    "recommended": "0640",
    "recommended_type": "octal",
    "section": "FILE PERMISSIONS",
    ...
}
```

`value` ist der PHP-Wert (ein oktaler Modus ist die Zahl 416), `raw` das Literal, wie es in der Datei steht, `text`
der Wert, wie `exp:config:get` ihn ausgibt. `state` ist `set`, `not set` oder `conditional` für ein `define()`
innerhalb eines `if`, das nur gilt, wenn seine Bedingung erfüllt ist, und das die Befehle nie ändern.

## 6. Einen Wert setzen

```bash
./console exp:config:set EZP_INI_FILEMTIME_CHECK false
```

```text
EZP_INI_FILEMTIME_CHECK = false (bool): take the comment off the documented line 57
  --- config.php
  +++ config.php
  @@ -54,7 +54,7 @@
       when a new INI file override is added to the structure. Note that new files
       are not affected, but this is an edge situation.
   */
  -//define( 'EZP_INI_FILEMTIME_CHECK', false );
  +define( 'EZP_INI_FILEMTIME_CHECK', false );
   
   
   /*
PASS: dry run, config.php not changed; --apply writes it (backup to var/backup/config/<time>/ first)
```

und mit `--apply`:

```text
Backup: var/backup/config/20261029-130302/config.php
PASS: config.php written and checked with php -l (mode 0644, owner www-data:www-data)
A persistent server (Velocity, FrankenPHP worker) reads config.php again after a restart; PHP-FPM once OPcache sees the change (a reload of PHP-FPM when opcache.validate_timestamps=0).
```

Wohin der Wert kommt, in dieser Reihenfolge:

1. **Ein `define()` des Namens, das config.php bereits ausführt**: Nur sein Wert ändert sich, an Ort und Stelle.
   Kommentare, Abstände und alles andere in der Zeile bleiben.
2. **Die dokumentierte Zeile**, `//define( 'NAME', ... );`, wie `config.php-RECOMMENDED` sie mitbringt: Der Kommentar
   wird entfernt und der Wert dort geschrieben, so bleibt die Einstellung neben ihrer Beschreibung.
3. **Sonst** wird das `define()` zwischen zwei Markierungszeilen eingefügt, die die erste solche Einfügung vor der
   ersten Anweisung der Datei anlegt, die kein `define()` ist:

   ```php
   // BEGIN settings set by ./console exp:config:set
   define( 'MY_SITE_FLAG', true );
   // END settings set by ./console exp:config:set
   ```

Die Befehle ändern immer nur eine Anweisung `define( 'NAME', wert );`. Die Datei wird als PHP-Tokens gelesen, nie
ausgeführt und nie mit Suchen und Ersetzen bearbeitet, ein `define()` in einem Kommentar oder einer Zeichenkette wird
also nie mit einem echten verwechselt.

### Typen

Ein Wert wird mit einem Typ geschrieben, so wie PHP ihn sieht:

| Typ | Geschrieben als | Beispiel |
|---|---|---|
| `bool` | `true`, `false` (mit `--type=bool` auch 1/0, yes/no, on/off, enabled/disabled) | `exp:config:set CLUSTER_ENABLE_DEBUG false` |
| `int` | `3306` | `exp:config:set CLUSTER_STORAGE_PORT 3307` |
| `octal` | `0640`, für Modi; `640` und `0o640` werden angenommen | `exp:config:set EZP_DIR_MODE_MAX 750` schreibt `0750` |
| `float` | `1.5` | |
| `string` | `'text'`, Anführungszeichen und Backslashes maskiert | `exp:config:set CLUSTER_STORAGE_HOST db1.example.com` |
| `null` | `null` | |

Ohne `--type` kommt der Typ vom Wert in `config.php`, sonst vom Wert, den die Vorlage beschreibt: `EZP_DIR_MODE_MAX
750` wird zum oktalen `0750`, und `CLUSTER_STORAGE_PASS 12345` bleibt eine Zeichenkette. `true`, `false` und `null`
sind immer die PHP-Werte, weil mehrere Einstellungen `false` statt einer Zahl oder eines Textes annehmen
(`CLUSTER_EXPIRY_TIMEOUT false` schaltet den Header ab). Ein unpassender Wert wird abgelehnt:

```text
FAIL: CLUSTER_STORAGE_PORT: "abc" is not an integer (the documented value is of type int; give --type=<type> to write another type)
FAIL: EZP_INI_FILEMTIME_CHECK: "site.ini" is not true or false (the documented value is of type bool; give --type=<type> to write another type)
```

`EZP_INI_FILEMTIME_CHECK` kann auch eine INI-Datei nennen, die weiter geprüft wird. Sagen Sie das mit `--type`:

```bash
./console exp:config:set EZP_INI_FILEMTIME_CHECK site.ini --type=string --apply
```

```text
EZP_INI_FILEMTIME_CHECK = 'site.ini' (string): change the value on line 57
  -define( 'EZP_INI_FILEMTIME_CHECK', false );
  +define( 'EZP_INI_FILEMTIME_CHECK', 'site.ini' );
```

Um das Wort `false` als Text zu schreiben, nehmen Sie `--type=string`. Ein Wert, der mit `-` beginnt, würde als Option
gelesen; geben Sie ihn mit `--value=` an: `exp:config:set CUSTOM_LOG_ROTATE_FILES --value=-1 --type=int`.

### Passwörter und andere Geheimnisse

```bash
./console exp:config:set CLUSTER_STORAGE_PASS 'S3cret-example' --apply
```

```text
CLUSTER_STORAGE_PASS = '********' (string): take the comment off the documented line 201
  -// define( 'CLUSTER_STORAGE_PASS', '********' );
  +define( 'CLUSTER_STORAGE_PASS', '********' );
Backup: var/backup/config/20261029-130326/config.php
PASS: config.php written and checked with php -l (mode 0644, owner www-data:www-data)
WARN: config.php holds a password or secret and every user of the server can read it (mode 0644): chmod 0640 config.php, with the web server's group
```

Der Wert erscheint nie in der Ausgabe. Achten Sie aber auf die Shell-History: Ein auf der Befehlszeile getipptes
Passwort steht darin. Lesen Sie es lieber aus einer Datei oder Variablen, etwa `--value="$(cat /root/cluster-pass)"`,
oder setzen Sie `HISTCONTROL=ignorespace` und beginnen die Zeile mit einem Leerzeichen. Folgen Sie der Warnung und
schränken Sie den Modus von `config.php` ein; siehe [Sicherheit](#10-sicherheit) unten.

### Namen, die nicht beschrieben sind

```text
FAIL: MY_SITE_FLAG is not documented in config.php-RECOMMENDED and not set in config.php; check the name (./console exp:config:list), or give --allow-unknown to add it anyway
```

Die Prüfung fängt Tippfehler ab (`EZP_INI_FILEMTIM_CHECK` würde sonst geschrieben und stillschweigend ignoriert). Eine
eigene Konstante, die Ihre Extension liest, fügen Sie mit `--allow-unknown` hinzu; sie kommt in den markierten Block.
Ein Name, den `config.php` bereits definiert, lässt sich immer ändern.

## 7. Einen Wert entfernen

```bash
./console exp:config:unset EZP_DIR_MODE_MAX --apply
```

```text
EZP_DIR_MODE_MAX: comment out the define() on line 111 (the built-in default applies again)
  -define( 'EZP_DIR_MODE_MAX', 0750 );
  +//define( 'EZP_DIR_MODE_MAX', 0750 );
Backup: var/backup/config/20261029-130349/config.php
PASS: config.php written and checked with php -l (mode 0644, owner www-data:www-data)
```

Eine beschriebene Einstellung wird dort auskommentiert, wo sie steht, damit `exp:config:set` die Zeile beim nächsten
Mal wiederfindet. Eine Einstellung im markierten Block wird entfernt; geht die letzte, gehen die beiden
Markierungszeilen mit. Ein nicht gesetzter Name wird mit `PASS: ... nothing to do` gemeldet.

## 8. Eigentümer, Modi und Sicherungen im Einzelnen

| Lage | Modus der geschriebenen Datei | Eigentümer und Gruppe |
|---|---|---|
| Neue Datei (noch keine `.htaccess` oder `config.php`) | der Modus der Vorlage, wie bei `cp -a` | als root: Eigentümer und Gruppe der Vorlage, oder die des Installationsverzeichnisses, wenn die Vorlage root gehört; als anderer Benutzer: dieser Benutzer |
| Vorhandene Datei ersetzt (`--force`) oder bearbeitet (`exp:config:set`, `unset`) | der eigene Modus der Datei | Eigentümer und Gruppe der Datei |
| `--mode=0640` (Setup-Befehle) | dieser Modus; der Eigentümer muss lesen und schreiben dürfen | unverändert |
| `--owner=benutzer` oder `--owner=benutzer:gruppe` (Setup-Befehle) | unverändert | dieser Benutzer und diese Gruppe; nur root kann eine Datei einem anderen Benutzer geben |

Die Änderungszeit ist die Zeit des Schreibens, nicht die der Vorlage: Eine ersetzte `config.php` muss für OPcache
geändert aussehen. Sicherungen behalten Inhalt, Modus und (als root) Eigentümer der Datei, wie sie war:

```text
drwxr-xr-x www-data:www-data var
drwx------ www-data:www-data var/backup
drwx------ www-data:www-data var/backup/config
drwx------ www-data:www-data var/backup/config/<time>
-rw-r--r-- www-data:www-data var/backup/config/<time>/config.php
drwx------ www-data:www-data var/backup/setup
drwx------ www-data:www-data var/backup/setup/<time>
-rw-r--r-- www-data:www-data var/backup/setup/<time>/.htaccess
```

Die Befehle löschen nie eine Sicherung. Entfernen Sie alte selbst, wenn Sie sie nicht mehr brauchen.

Als root zu arbeiten ist in einem Installer oder Deploy-Skript der Normalfall. Der Befehl gibt die neue Datei dann
dem Eigentümer der Vorlage, so dass das PHP der Website sie lesen kann, und hinterlässt nie eine `config.php`, die
root gehört. Geben Sie `--owner` an, wenn die Installation jemand anderem gehört als ihre Vorlagen, etwa nach einem
Entpacken als root: `./console exp:setup:config --apply --owner=www-data:www-data --allow-root-user`.

## 9. Wenn etwas schiefgeht

**`FAIL: ... exists and differs ...`** Sie haben eine Kopie über eine geänderte Datei verlangt. Lesen Sie den Diff
(`-v` für den ganzen). Behalten Sie Ihre Änderungen: Lassen Sie die Datei, oder übertragen Sie sie nach
`--force --apply` von Hand aus der Sicherung in die neue.

**`FAIL: config.php does not compile (syntax error, ... on line 3); nothing was read or changed.`** Jeder
`exp:config:*`-Befehl lehnt eine `config.php` mit Syntaxfehler ab, weil nichts darin sicher das ist, wonach es
aussieht. Auch die Website steht: PHP bricht bei jeder Anfrage am Parse-Fehler ab. Die Konsole selbst läuft weiter
und warnt beim Start:

```text
Warning: config.php does not compile: syntax error, unexpected identifier "define" on line 3. Commands other than exp:setup:* and exp:config:* stop on it;
./console exp:setup:status shows it, ./console exp:setup:config --force --apply replaces it (backup first).
```

Korrigieren Sie die genannte Zeile von Hand, kopieren Sie die letzte Sicherung aus `var/backup/config/` oder
`var/backup/setup/` zurück, oder fangen Sie mit `./console exp:setup:config --force --apply` neu bei der Vorlage an
(die defekte Datei wird vorher gesichert).

**`FAIL: cannot write a temporary file in /var/www/example: nothing was changed`** Der Benutzer, der den Befehl
ausführt, darf im Installationsverzeichnis nicht schreiben. Führen Sie ihn als Eigentümer der Installation aus
(`sudo -u <eigentümer> ./console ...`) oder als root mit `--allow-root-user`.

**`FAIL: --owner=www-data: only root can give a file to another user`** Lassen Sie `--owner` weg oder arbeiten Sie
als root.

**`FAIL: ... is defined inside a condition or an expression on line N; change it by hand`** Das `define()` gehört zu
einem `if`, zu `defined( ... ) || define( ... )` oder Ähnlichem. Eine solche Zeile entscheidet selbst, wann sie gilt;
die Befehle überlassen sie Ihnen.

**`FAIL: ... is defined more than once (lines 12, 40)`** PHP nimmt die erste und warnt bei der zweiten. Entfernen Sie
eine von Hand und verwenden Sie dann wieder die Befehle.

**Die Änderung wirkt nicht.** Ein persistenter Server liest `config.php` einmal pro Worker: Starten Sie Velocity
(`./console exp:velocity restart`) oder den FrankenPHP-Worker neu. Mit PHP-FPM und `opcache.validate_timestamps=0`
laden Sie PHP-FPM neu. Prüfen Sie den Wert mit `./console exp:config:get <NAME>`.

**Die Website antwortet nach einer neuen `.htaccess` mit 500.** Dem Server fehlt ein Modul, das die Regeln nutzen
(`mod_rewrite`, oder `mod_headers` für die SVG-Regeln), oder `AllowOverride` lässt sie nicht zu. Legen Sie die
Sicherung zurück (`var/backup/setup/<zeit>/.htaccess`) und lesen Sie das Fehlerlog von Apache.

## 10. Sicherheit

- `config.php` kann das Passwort der Cluster-Datenbank enthalten. Committen Sie sie nie in eine Versionsverwaltung:
  Nehmen Sie `/config.php` in die `.gitignore` Ihres Projekts auf (das Repository von Exponential enthält keine
  `config.php`, nur die Vorlage), und schränken Sie ihren Modus ein: `chmod 0640 config.php`, mit der Gruppe des
  Webservers. Die Befehle behalten den gesetzten Modus bei jeder späteren Änderung.
- Werte von Namen mit `PASS`, `SECRET`, `KEY`, `TOKEN`, `CREDENTIAL` oder `PWD` werden in jeder Ausgabe und jedem Diff
  maskiert; `--show-secrets` zeigt sie, kopieren Sie solche Ausgaben also nicht in ein Ticket.
- Sicherungen in `var/backup/` enthalten frühere Fassungen von `config.php`, Passwörter eingeschlossen, in
  Verzeichnissen mit Modus `0700`. Schützen Sie `var/backup/` mit und räumen Sie es auf.
- Die mitgelieferte `.htaccess` antwortet auf jede Punktdatei und auf alles unter `var/`, das kein öffentliches Asset
  ist, mit 404, weder `.htaccess` selbst noch eine Sicherung werden also ausgeliefert. nginx und andere Server
  brauchen dieselben Regeln in ihrer Konfiguration; siehe [Webserver-Beispiele](../beispiele/webserver/README.md).
- [Sicherheit und Audit](sicherheit-und-audit.md) und [Sicherheitshärtung](../hilfe/sicherheitshaertung.md) behandeln den Rest.

## 11. In Skripten, Containern und CI

Die Befehle sind für den unbeaufsichtigten Lauf gemacht: Sie fragen nichts, geben zuletzt `PASS:` oder `FAIL:` aus
und enden mit 0, 1 oder 2.

```bash
#!/usr/bin/env bash
# provision.sh: die Dateien an ihren Platz bringen und setzen, was dieser Server braucht
set -euo pipefail
cd /var/www/example
./console exp:setup:htaccess --apply
./console exp:setup:config --apply
./console exp:config:set EZP_INI_FILEMTIME_CHECK false --apply
./console exp:config:set EZP_DIR_MODE_MAX 0750 --apply
./console exp:config:set EZP_FILE_MODE_MAX 0640 --apply
./console exp:config:list --only-set
```

Ein zweiter Lauf ändert nichts: Jeder Befehl meldet `nothing to do` und endet mit 0. In einem Container-Image führen
Sie dieselben Zeilen im Build-Schritt aus (als root, mit `--allow-root-user --owner=www-data:www-data`), oder beim
Start aus Umgebungsvariablen:

```bash
./console exp:config:set CLUSTER_STORAGE_HOST "$CLUSTER_DB_HOST" --apply --allow-root-user
./console exp:config:set CLUSTER_STORAGE_PASS --value="$CLUSTER_DB_PASS" --apply --allow-root-user
```

In der CI vergleichen Sie einen Server mit dem Erwarteten, ohne ihn zu ändern: `./console exp:config:list --only-set
--format=json` liefert ein stabiles Dokument zum Vergleichen, und `./console exp:setup:htaccess` endet mit 1, wenn
`.htaccess` von der Vorlage abweicht.

## 12. Nach einem Upgrade

Eine neue Version kann `.htaccess_root`, `.htaccess_root_static` und `config.php-RECOMMENDED` ändern. Ihre Kopien
bleiben, wie sie sind. Nach dem Upgrade:

```bash
./console exp:setup:status                 # ist .htaccess noch eine Kopie einer Vorlage?
./console exp:setup:htaccess               # der Diff zwischen Ihrer .htaccess und der neuen Vorlage
./console exp:setup:htaccess --force --apply    # die neuen Regeln übernehmen (vorher gesichert), wenn der Diff nichts Eigenes enthält
./console exp:config:list                  # Einstellungen, die neu in config.php-RECOMMENDED sind, erscheinen als (not set)
```

`exp:config:list` liest die neue Vorlage, eine Einstellung, die die Version hinzufügt, erscheint also mit ihrer
Beschreibung und ihrem Vorschlag; setzen Sie die gewünschten mit `exp:config:set`. Ihre bisherigen Einstellungen
behalten ihre Werte. Siehe [Aktualisieren](aktualisieren.md).

## Referenz

| Befehl | Kurzform für | Optionen |
|---|---|---|
| `exp:setup:status` | `exp:setupfiles status` | `--root` |
| `exp:setup:htaccess` | `exp:setupfiles htaccess` | `--static`, `--force`, `--apply`, `--dry-run`, `--mode`, `--owner`, `-v`, `--root` |
| `exp:setup:config` | `exp:setupfiles config` | `--force`, `--apply`, `--dry-run`, `--mode`, `--owner`, `--show-secrets`, `-v`, `--root` |
| `exp:config:list` | `exp:configphp list` | `--only-set`, `--format=text\|json`, `--show-secrets`, `--root`, `--file`, `--recommended` |
| `exp:config:get <NAME>` | `exp:configphp get` | `--format=text\|json`, `--show-secrets`, `--root`, `--file` |
| `exp:config:set <NAME> <wert>` | `exp:configphp set` | `--type`, `--value`, `--allow-unknown`, `--apply`, `--dry-run`, `--show-secrets`, `-v`, `--root`, `--file` |
| `exp:config:unset <NAME>` | `exp:configphp unset` | `--apply`, `--dry-run`, `--show-secrets`, `-v`, `--root`, `--file` |

`--root=<verzeichnis>` arbeitet an einer anderen Installation als der, zu der die Konsole gehört; `--file` und
`--recommended` nennen andere Dateien als `<root>/config.php` und `<root>/config.php-RECOMMENDED`. Ohne die Konsole
heißen dieselben Befehle `php bin/php/setupfiles.php <htaccess|config|status>` und
`php bin/php/configphp.php <list|get|set|unset>`.

Die erzeugten Seiten listen jede Option mit ihrem Hilfetext: [exp:setupfiles](../beispiele/befehle/setupfiles.md) und
[exp:configphp](../beispiele/befehle/configphp.md). Die Funktion in Kürze:
[Konsolenbefehle für .htaccess und config.php](../funktionen/6.0/befehle-fuer-htaccess-und-config-php.md).

## Verwandte Seiten

- [Installation: den Code beschaffen](../installation/03-den-code-beschaffen.md) und [Die Website ausliefern](../installation/08-die-website-ausliefern.md)
- [Bereitstellung](bereitstellung.md) und [Betrieb einer Website](betrieb-einer-website.md)
- [INI-Einstellungen von der Befehlszeile ändern: exp:ini](../funktionen/6.0/exp-ini-befehl.md), das Werkzeug für alles in `settings/`
- [Umgebungsvariablen](umgebungsvariablen.md) und `config.env.php`, für das, was sich pro Maschine unterscheidet
- [Dateimodi](../bc/6.0/dateimodi.md), für `EZP_DIR_MODE_MAX` und `EZP_FILE_MODE_MAX`
- [Der statische Cache](../funktionen/6.0/generator-fuer-den-statischen-cache.md), für `.htaccess_root_static`
