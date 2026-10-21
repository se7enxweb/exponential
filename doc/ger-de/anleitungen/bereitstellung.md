# Exponential bereitstellen: Apache mit PHP-FPM, Velocity oder FrankenPHP

Diese Anleitung richtet sich an die Person, die eine Site auf einen Server bringt: einen Betreiber oder einen Entwickler, der seinen eigenen Server betreibt. Sie führt eine frisch installierte Exponential-Site auf einen Server, den Besucher über HTTPS erreichen. Danach zeigt sie, wie Sie eine Codeänderung sicher ausliefern und die Site schnell halten. Lesen Sie sie von oben nach unten. Jeder Befehl ist in der Installation vorhanden; wo ein Ergebnis gezeigt wird, ist es das, was der Befehl ausgibt.

Sie benötigen einen Linux-Server, Shell-Zugang im Installationsverzeichnis (dem Ordner, der `index.php` und `console` enthält) und eine Site, die bereits läuft. Wenn Sie noch keine Site haben, führen Sie zuerst `./console exp:install` aus ([Erste Schritte](erste-schritte.md), [Installation mit einem Befehl](../../eng-us/features/6.0/install-in-one-command.md)).

Die Befehle verwenden `./console`, die Kurzform für `bin/php/console`. Die Beispiele fügen `--allow-root-user` an, weil sie als root ausgeführt werden; lassen Sie es weg, wenn Sie als Benutzer der Site arbeiten.

## 1. Wählen Sie, wie die Site ausgeliefert wird

Exponential läuft hinter einer von drei Konfigurationen. Wählen Sie vorerst eine aus; Sie können später wechseln, ohne die Inhalte anzufassen.

| Konfiguration | Wählen Sie sie, wenn | Zeit bis zur laufenden Site |
|---|---|---|
| Apache (oder nginx) mit PHP-FPM | der Server bereits einen Webserver und ein Hosting-Panel hat oder Sie die klassische Konfiguration wünschen | 10 Minuten |
| Exponential Velocity (die Engine `qbix`) | Sie die schnellste Engine, persistente Worker, einen eingebauten Antwort-Cache und keinen zu konfigurierenden Webserver wünschen | 2 Minuten |
| FrankenPHP | Sie eine einzige geprüfte Binärdatei mit TLS und HTTP/2 und kein PHP auf der Maschine pflegen möchten | 5 Minuten |

Velocity ist die empfohlene Engine für jede Phase. Die drei können nebeneinander auf verschiedenen Ports laufen, sodass das Ausprobieren der einen die anderen nie stilllegt ([Engines im Vergleich](../../eng-us/bc/6.0/velocity-engines.md)).

## 2. Prüfen Sie die Maschine, bevor Sie beginnen

```bash
php -v | head -1
./console exp:velocity ext check --allow-root-user
```

Die erste Zeile zeigt die PHP-Version (8.0 oder neuer, auch für Velocity). Die zweite listet jede PHP-Extension auf, die die Installation benötigt, markiert, was fehlt, und gibt den Installationsbefehl für Ihr System aus, zum Beispiel `sudo dnf install -y php-odbc`. Installieren Sie, was sie für die von Ihnen genutzten Funktionen meldet, und starten Sie dann PHP-FPM oder den Server neu, damit die Extensions geladen werden. Fehlende optionale Extensions halten die Site nicht an.

## 3. Option A: Apache mit PHP-FPM

### 3.1 Richten Sie den Webserver auf die Installation aus

Das Document Root ist das Installationsverzeichnis selbst. Exponential führt seine eigenen Rewrite-Regeln in einer Datei, die Sie durch Kopieren aktivieren:

```bash
cp .htaccess_root .htaccess
```

Die Kopie schaltet den auf `.htaccess` basierenden Virtual-Host-Modus ein: Punktdateien, `.git` und Skripte unterhalb der Front-Controller antworten mit 404, `/api/` geht an `index_rest.php`, statische Design-Dateien werden direkt ausgeliefert und alles andere geht an `index.php`. Entfernen Sie keine Sicherheitsregeln daraus, um etwas zum Laufen zu bringen ([Sicherheitsvorgaben](../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md)).

Ein minimaler Apache-Virtual-Host für PHP-FPM (passen Sie die Pfade und den Socket an Ihr System an):

```apache
<VirtualHost *:80>
    ServerName example.com
    DocumentRoot /var/www/example.com
    DirectoryIndex index.php

    <Directory /var/www/example.com>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>
```

`AllowOverride All` sorgt dafür, dass Apache die eben angelegte `.htaccess` liest, und `mod_rewrite`, `mod_proxy` und `mod_proxy_fcgi` müssen geladen sein. Prüfen und neu laden:

```bash
apachectl configtest      # Syntax OK
systemctl reload apache2  # or httpd on Red Hat style systems
```

### 3.2 Testen Sie es

```bash
curl -sI http://example.com/ | head -1
```

Erwarten Sie `HTTP/1.1 200 OK` (oder eine Weiterleitung auf die erste Seite). Wenn Sie 500 erhalten, öffnen Sie die Fehlerprotokolle von PHP-FPM und Apache; wenn Sie für jede Seite 404 erhalten, wird `.htaccess` nicht gelesen (`AllowOverride`) oder `mod_rewrite` ist ausgeschaltet.

### 3.3 HTTPS

Beschaffen Sie ein Zertifikat mit Ihrem üblichen Werkzeug (certbot, Ihr Hosting-Panel) und fügen Sie einen Virtual Host `*:443` mit `SSLEngine on`, `SSLCertificateFile` und `SSLCertificateKeyFile`, demselben `Directory`-Block und demselben `SetHandler` hinzu. Testen Sie mit `curl -sI https://example.com/ | head -1`.

### 3.4 Nach jeder PHP-Änderung: das richtige PHP-FPM neu laden

Auf einem Server mit mehreren PHP-Versionen ist der FPM-Master, der Ihre Site bedient, nicht immer derjenige namens `php-fpm`. Auf einem Plesk-Server zum Beispiel startet `systemctl restart php-fpm` ein PHP neu, das keine Site verwendet. `exp:velocity deploy` (Abschnitt 7) findet den richtigen für Sie; die Einstellung ist `[DeploySettings] PhpFpmService`:

```bash
./console exp:velocity config get DeploySettings PhpFpmService --allow-root-user
```

Sie gibt `auto` aus; damit wird nach dem Pool gesucht, der nach der Hosting-Domain der Installation benannt ist, und dessen Dienst neu geladen.

## 4. Option B: Exponential Velocity (die Engine `qbix`)

Velocity ist ein PHP-Anwendungsserver, der mit Exponential ausgeliefert wird. Er benötigt weder Apache noch nginx noch FPM.

### 4.1 Wählen Sie die Engine aus und starten Sie sie

```bash
./console exp:velocity config set ServerSettings Engine qbix --allow-root-user
./console exp:velocity start --allow-root-user
./console exp:velocity status --allow-root-user
```

`config set` schreibt `settings/override/velocity.ini.append.php`, die eine Datei für die Einstellungen dieser Installation (die ausgelieferten Standardwerte bleiben in `settings/velocity.ini`). `status` listet die Adressen auf, die er bedient, die Anzahl der Prozesse, ob HTTPS eingeschaltet ist und wo die Protokolle liegen:

```
Process    52 processes, parent ... · port 8088, 8080
HTTPS      on, port 8080
Access     var/vc/qbix/log/site-access.log
Errors     var/vc/qbix/log/site-error.log
```

Einfaches HTTP liegt auf `Port` (ausgeliefert 8088), HTTPS auf `HTTPSPort` (ausgeliefert 8080), beide gebunden an `Host` (ausgeliefert `127.0.0.1`). Öffnen Sie `http://127.0.0.1:8088/` auf derselben Maschine. Um das Netzwerk zu bedienen, setzen Sie `Host`:

```bash
./console exp:velocity config set ServerSettings Host 0.0.0.0 --allow-root-user
./console exp:velocity restart --allow-root-user
```

Ports unter 1024 erfordern root; die übliche Konfiguration ist Velocity auf eigenen Ports hinter einem Hosting-Panel oder einer Firewall-Regel oder ein vorgeschalteter Webserver, der an ihn weiterleitet.

### 4.2 Stoppen, neu starten, neu laden, ohne Anfragen zu verlieren

```bash
./console exp:velocity graceful --allow-root-user    # re-exec, listening socket kept
./console exp:velocity restart --allow-root-user
./console exp:velocity stop --allow-root-user
```

### 4.3 Sehen Sie sich um

```bash
./console exp:velocity layout --allow-root-user      # every file the server uses
./console exp:velocity config list ServerSettings --allow-root-user
```

`layout` zeigt den Konfigurationsbaum im Stil von Apache unter Debian (`/etc/vc` mit `sites-available`, `sites-enabled`, `conf-available` und so weiter). Sites, Snippets und Module werden wie mit `a2ensite` ein- und ausgeschaltet: `./console exp:velocity site enable <name> --allow-root-user` ([Aufbau auf der Festplatte](../../eng-us/bc/6.0/velocity-ondisk-layout.md)). Der Server hat außerdem ein Control Panel ([Velocity Control Panel](../../eng-us/features/6.0/velocity-control-panel.md)).

Vertiefung: [Velocity-Webserver](../../eng-us/features/6.0/velocity-web-server.md), [persistente Worker](../../eng-us/features/6.0/velocity-persistent-worker-server.md), [Worker-Pool](../spezifikationen/6.0/velocity-worker-pool.md), [Engine-Einstellungen](../spezifikationen/6.0/velocity-engine-einstellungen.md).

## 5. Option C: FrankenPHP

FrankenPHP ist der Webserver Caddy mit eingebautem PHP, als eine einzige Datei. Exponential lädt eine festgelegte Release herunter und prüft deren SHA-256, bevor es sie ausführt.

```bash
./console exp:velocity install --engine=frankenphp --allow-root-user
./console exp:velocity start --engine=frankenphp --allow-root-user
./console exp:velocity status --engine=frankenphp --allow-root-user
```

Standardmäßig liefert er einfaches HTTP auf Port 8089 und HTTPS auf 8444 aus, sodass `https://` vom ersten Start an funktioniert (ein selbstsigniertes Zertifikat springt ein, bis Sie ihm ein echtes geben). Nützliche Varianten:

```bash
./console exp:velocity start --engine=frankenphp --no-https --allow-root-user   # plain HTTP, this start only
./console exp:velocity install --engine=frankenphp --check --allow-root-user    # re-hash the installed binary
```

PHP läuft im klassischen Modus (sauberer Zustand pro Anfrage, wie unter PHP-FPM). Die Engine hat noch keinen Antwort-Cache; wenn Sie auf einen angewiesen sind, verwenden Sie Velocity. Zwei Dinge machen bei einer Erstinstallation Ärger: Das eingebettete PHP hat keine `php.ini` der Maschine, setzen Sie also den Datenbank-Socket unter `[PHPSettings] IniOptions[]`, wenn Ihre Datenbank über einen Socket erreicht wird, und eine neu veröffentlichte Release kann die festgelegte Prüfsumme veralten lassen. Die vollständige, getestete Schritt-für-Schritt-Anleitung mit beiden Korrekturen ist [FrankenPHP als Web-Engine von Exponential](../../eng-us/bc/6.0/frankenphp.md).

Um alle drei Engines nebeneinander zu betreiben, geben Sie jeder ihren eigenen Port und verwenden Sie `--engine=<name>` oder `--all` bei den Steuerbefehlen.

## 6. HTTPS

| Konfiguration | Wie |
|---|---|
| Apache | das Zertifikat im Virtual Host `*:443` (Abschnitt 3.3) |
| Velocity | `[HTTPSSettings]` in `velocity.ini` und das Zertifikats-Subsystem |
| FrankenPHP | standardmäßig eingeschaltet; `[HTTPSSettings] Certificate` und `Key` liefern Ihr eigenes |

Für Velocity mit einem Zertifikat, das Sie bereits haben (eine PEM-Datei mit Zertifikat und Schlüssel; beide Einstellungen dürfen dieselbe Datei nennen):

```bash
./console exp:velocity config set HTTPSSettings Certificate /etc/ssl/example.com/fullchain.pem --allow-root-user
./console exp:velocity config set HTTPSSettings Key /etc/ssl/example.com/privkey.pem --allow-root-user
./console exp:velocity config set HTTPSSettings Enabled true --allow-root-user
./console exp:velocity restart --allow-root-user
curl -skI https://127.0.0.1:8080/ | head -1
```

Erwarten Sie `HTTP/2 200` (oder eine Weiterleitung). Der Velocity-Server kann außerdem selbst ein selbstsigniertes Zertifikat erzeugen, Let's-Encrypt-Zertifikate beziehen und erneuern und ein erneuertes Zertifikat ohne Neustart einwechseln: [Velocity HTTPS und Zertifikate](../../eng-us/features/6.0/velocity-https-certificates.md).

Strict-Transport-Security wird bei HTTPS-Antworten mit `[HTTPSSettings] HSTSMaxAge` gesendet (ausgeliefert 300 Sekunden). Erhöhen Sie den Wert erst dann auf ein Jahr (`31536000`), wenn jeder Name der Site dauerhaft über HTTPS ausgeliefert wird: Ein Browser merkt sich den Wert auch dann, wenn Sie ihn später senken ([HTTP/2 und Sicherheit](../spezifikationen/6.0/velocity-http2-und-sicherheit.md)).

## 7. Eine PHP-Änderung mit einem Befehl ausliefern

Nachdem Sie eine PHP-Klasse, einen Operator oder eine INI-Datei geändert haben, führen Sie aus:

```bash
./console exp:velocity deploy --dry-run --allow-root-user   # what it would do, nothing else
./console exp:velocity deploy --allow-root-user             # the usual case
./console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

Der Probelauf gibt die vierzehn Schritte aus, jeder mit `DRY` markiert:

```
[ 1/14] DRY   extension autoloads: php bin/php/ezpgenerateautoloads.php -e -q
[ 2/14] DRY   INI caches: php bin/php/ezcache.php --clear-tag=ini --allow-root-user
...
[ 8/14] DRY   PHP-FPM sees the new code: wait 11 s (no reload)  -- <pool>: OPcache revalidates every 10 s
[ 9/14] DRY   restart Velocity: php bin/php/velocity.php restart
[10/14] DRY   content cache ...
[13/14] DRY   Velocity response cache: exp:velocity cache clear
[14/14] DRY   warm-up requests: curl 2 URLs in the background
```

Ein echter Lauf gibt für jeden Schritt PASS, FAIL oder SKIP und die Dauer aus und hält beim ersten Fehler mit dem Exit-Status 1 an. Die Reihenfolge ist wichtig: Die Caches mit fertigen Seiten werden zuletzt geleert, nachdem PHP-FPM und Velocity den neuen Code ausführen, sodass eine vom alten Code gerenderte Seite nie wieder in den Cache gelangt. PHP-FPM wird nur dann neu geladen, wenn sein OPcache Dateien nicht selbst neu validiert: Ein Neuladen hält jeden Pool dieser PHP-Version an, bis seine längste Anfrage endet, daher wartet der Schritt stattdessen auf OPcache (`[DeploySettings] PhpFpmReload`). Der letzte Schritt ruft `[DeploySettings] WarmUpURL[]` im Hintergrund ab, damit der erste Besucher die Caches bereits aufgebaut vorfindet. Optionen: `--no-fpm` (nur Velocity), `--fpm-reload` (trotzdem neu laden: php.ini oder Pool geändert, Dateien umbenannt), `--no-velocity` (nur Apache), `--no-autoload`, `--rebuild-phar`, `--packer` (auch gepackte Skripte und Stylesheets leeren, wenn sich eine `ezjscServer_*`-Funktion geändert hat). Einzelheiten und jeder Schritt: [Eine PHP-Änderung bereitstellen](../../eng-us/bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy).

Änderungen an einem Template oder einem Stylesheet erfordern weniger: Leeren Sie die Caches ohne Neustart.

```bash
./console exp:cache clear --id=template --allow-root-user
./console exp:velocity cache clear --allow-root-user
```

Führen Sie nie nur `exp:cache clear --all` aus und vergessen dann den Webserver: FPM und Velocity behalten die alten Klassen im Speicher, bis sie neu geladen werden.

## 8. Machen Sie sie schnell

Arbeiten Sie diese Liste von oben nach unten ab; jeder Punkt ist ein Schalter oder ein Befehl, den es gibt, und `exp:cache status` zeigt, wo Sie stehen.

```bash
./console exp:cache status --allow-root-user
```

Er gibt eine Zeile pro Ebene aus, zum Beispiel `httpcache  the HTTP cache is on: ... entries`, `querycache  the SQL query cache is on`, `velocity  response cache ...: N files` und `php  OPcache not enabled for this server`, wenn der Opcode-Cache für die Kommandozeile ausgeschaltet ist (normal; entscheidend ist er für den Webprozess).

1. **Opcode-Cache.** Lassen Sie unter PHP-FPM `opcache.enable=1` in der PHP-Konfiguration des Pools. Velocity übergibt dem Serverprozess eigene Optionen für den Opcode-Cache; die ausgelieferten Werte stehen in `[PHPSettings]` von `settings/velocity.ini`. Gemessene Wirkung und wie Sie ein Profil erstellen: [Velocity und der Opcode-Cache](../../eng-us/features/6.0/velocity-opcode-cache-and-profile.md).
2. **Antwort-Cache (Velocity).** `[CacheSettings] Enabled=enabled`, `DefaultTtl=30` Sekunden. Eine daraus ausgelieferte Seite erreicht PHP nie. Leeren Sie ihn nach einer Template-Änderung mit `./console exp:velocity cache clear --allow-root-user`. Siehe [Antwort-Cache von Velocity](../../eng-us/features/6.0/velocity-response-cache.md).
3. **HTTP-Cache.** Der rollenbewusste Seiten-Cache für jede Engine: `./console exp:cache httpcache status --allow-root-user` ([HTTP-Cache](../../eng-us/bc/6.0/httpcache.md)).
4. **SQL-Abfrage-Cache.** Standardmäßig eingeschaltet; `./console exp:cache querycache status --allow-root-user` ([SQL-Abfrage-Cache](../../eng-us/bc/6.0/sql-query-cache.md)).
5. **Statische Dateien.** Velocity liefert sie mit einem langen `StaticMaxAge` aus und kann sie vorab komprimieren (`[ServerSettings] StaticMaxAge`, `PrecompressStatic`) ([statische Dateien und Bilder](../../eng-us/features/6.0/velocity-static-files-and-images.md)).
6. **Statischer Cache.** Ganze Seiten, als Dateien geschrieben, die Apache ausliefert; im Installer ausgeschaltet ausgeliefert. `exp:cache static status` sagt, ob er eingeschaltet ist ([Generator für den statischen Cache](../../eng-us/features/6.0/static-cache-generator.md), [Standardwerte](../../eng-us/bc/6.0/static-cache-defaults.md)).
7. **Worker.** `[ServerSettings] Workers` (ausgeliefert 4) ist die Anzahl der Velocity-Prozesse; erhöhen Sie sie mit der Anzahl der CPU-Kerne und messen Sie nach jeder Änderung erneut.

Messen Sie mit derselben Anfrage vorher und nachher; ein einzelner Seitenaufruf beweist nichts:

```bash
curl -s -o /dev/null -w '%{time_total}s\n' http://127.0.0.1:8088/
```

Führen Sie ihn dreimal aus: Die erste Anfrage füllt die Caches, die anderen zeigen die Zeit aus dem Cache.

## 9. Prüfen Sie, ob Sie fertig sind

- `./console exp:velocity status --allow-root-user` (Velocity) oder `curl -sI` auf Ihre Adresse (Apache) antwortet mit 200.
- Der Browser zeigt das Schloss auf der HTTPS-Adresse.
- `./console exp:velocity deploy --dry-run --allow-root-user` listet einen Schritt `reload PHP-FPM` mit dem richtigen Dienst auf.
- `./console exp:cache status --allow-root-user` zeigt die Ebenen, die Sie erwarten.

## In dieser Anleitung verwendete Einstellungen

Alle stehen in `settings/velocity.ini` (ausgelieferte Standardwerte). `exp:velocity config set` schreibt Ihre Werte nach
`settings/override/velocity.ini.append.php`; der Geltungsbereich ist die Installation.

| Block | Schlüssel | Standard | Was er bewirkt |
|---|---|---|---|
| `[ServerSettings]` | `Engine` | `php` | Die Engine, die `exp:velocity` startet, wenn kein `--engine` angegeben ist: `php`, `qbix` oder `frankenphp` |
| `[ServerSettings]` | `Host` | `127.0.0.1` | Adresse, auf der Velocity (`qbix`) lauscht; `0.0.0.0` für das Netzwerk |
| `[ServerSettings]` | `Port` | `8088` | Port für einfaches HTTP von Velocity |
| `[ServerSettings]` | `HTTPSPort` | `8080` | HTTPS-Port von Velocity |
| `[ServerSettings]` | `Workers` | `4` | Anzahl der Worker-Prozesse von Velocity |
| `[ServerSettings]` | `StaticMaxAge` | `31536000` | Mit statischen Dateien gesendete Cache-Lebensdauer in Sekunden |
| `[ServerSettings]` | `PrecompressStatic` | `enabled` (Standard im Code, nicht in der Datei aufgeführt) | Komprimierte Kopien statischer Dateien vorhalten |
| `[CacheSettings]` | `Enabled` | `enabled` | Der Antwort-Cache von Velocity |
| `[CacheSettings]` | `DefaultTtl` | `30` | Sekunden, die eine Antwort in diesem Cache bleibt |
| `[HTTPSSettings]` | `Enabled` | `false` | HTTPS auf dem `HTTPSPort` von Velocity |
| `[HTTPSSettings]` | `Certificate`, `Key` | leer | PEM-Dateien Ihres Zertifikats und Schlüssels |
| `[HTTPSSettings]` | `HSTSMaxAge` | `300` | Lebensdauer von Strict-Transport-Security in Sekunden |
| `[DeploySettings]` | `PhpFpmService` | `auto` | Der PHP-FPM-Dienst, den `exp:velocity deploy` neu lädt |
| `[FrankenPHPSettings]` | `Port`, `HTTPSPort` | `8089`, `8444` | Ports der FrankenPHP-Engine |
| `[PHPServerSettings]` | `Port` | `8087` | Port der Engine `php` (des eigenen Servers von PHP) |

## Verwandte Seiten

- Funktionen: [Velocity-Webserver](../../eng-us/features/6.0/velocity-web-server.md), [Velocity HTTPS und Zertifikate](../../eng-us/features/6.0/velocity-https-certificates.md), [Installation mit einem Befehl](../../eng-us/features/6.0/install-in-one-command.md).
- Spezifikationen: [Engine-Einstellungen von Velocity](../spezifikationen/6.0/velocity-engine-einstellungen.md), [Worker-Pool](../spezifikationen/6.0/velocity-worker-pool.md), [HTTP/2 und Sicherheit](../spezifikationen/6.0/velocity-http2-und-sicherheit.md).
- Das Ergebnis messen: [Benchmarks für Exponential](benchmarking.md) (Apache gegen Velocity, eine Bereitstellung auf Regressionen geprüft).
- Upgrade-Hinweise: [Velocity-Engines](../../eng-us/bc/6.0/velocity-engines.md), [FrankenPHP](../../eng-us/bc/6.0/frankenphp.md), [Upgrade-Hinweise zur Velocity-Engine](../../eng-us/bc/6.0/velocity-engine-upgrade-notes.md), [Cache-Konsole](../../eng-us/bc/6.0/cache-console.md).
- Geschichte: [Velocity-Chronik](../../eng-us/history/velocity/2026-09a.md), [Oktober 2026](../../eng-us/history/2026/2026-10.md).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
