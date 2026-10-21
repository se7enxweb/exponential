# FrankenPHP als Web-Engine von Exponential

Lesen Sie diese Seite, wenn Sie Exponential mit FrankenPHP ausliefern möchten: dem Webserver Caddy mit PHP und seinen
Erweiterungen, in eine eigenständige Binärdatei kompiliert und von `exp:velocity` gesteuert. Sie behandelt Installation,
Konfiguration, Betrieb, Fehlersuche und Optimierung. Für das Gesamtbild aller drei Engines (`qbix`, `frankenphp`,
`php`) und die Einstellungen, die sie teilen, lesen Sie zuerst [Velocity-Engines](velocity-engines.md); wo sich die
beiden Seiten überschneiden, ist jene maßgeblich.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | `exp:velocity` steuert FrankenPHP (`--engine=frankenphp` oder `[ServerSettings] Engine=frankenphp`). `exp:velocity install` lädt ein festgelegtes Release herunter und prüft dessen SHA-256. Das Caddyfile wird bei jedem `start`, `graceful` und `restart` aus `velocity.ini` erzeugt. PHP läuft im klassischen Modus (sauberer Zustand pro Anfrage); der Worker-Modus wird nicht angeboten. |
| Wer betroffen ist | Websites, die statt PHP-FPM eine geprüfte Binärdatei mit TLS und HTTP/2 möchten. FrankenPHP hat noch keinen Antwort-Cache (`[CacheSettings]` wird ignoriert). Sein eingebettetes PHP hat keine `php.ini` der Maschine, daher braucht MySQL einen ausdrücklichen Socket. |
| Wie Sie es prüfen | `php bin/php/console exp:velocity install --engine=frankenphp --check --allow-root-user` und `php bin/php/console exp:velocity status --all --allow-root-user` |
| Wie Sie es beheben | Setzen Sie `[PHPSettings] IniOptions[]` für den MySQL-Socket ([§6.1](#61-die-korrektur-für-den-mysql-socket-die-wichtigste-falle)); korrigieren Sie einen veralteten Pin `Sha256[...]` ([§2.4](#24-wenn-der-pin-veraltet-ist--das-vorgehen-bei-einem-neu-geschnittenen-release-die-falle-in-die-wir-gerieten)); öffnen Sie den HTTPS-Port in der Firewall. |

Alles hier wurde am 2026-09-26 auf einer Testinstallation überprüft: FrankenPHP v1.12.7 (PHP 8.5.11, Caddy v2.11.4)
lieferte `https://www.example.com:8070/site/` aus (HTTP 200, Zertifikat von Let's Encrypt), neben der Engine qbix auf
8080. Hostnamen und Adressen in den Beispielen sind Platzhalter; verwenden Sie Ihre eigenen.

> Die Beispielbefehle verwenden `php bin/php/console`. `php bin/php/velocity.php` und die Kurzform `exp:vc` sind
> gleichwertig. Alle Beispiele fügen `--allow-root-user` hinzu, weil die Testinstallation die Werkzeuge als root
> ausführt; lassen Sie es weg, wenn Sie als Benutzer der Website arbeiten.

## 1. Was FrankenPHP hier ist und wann Sie es wählen

FrankenPHP ist eine Datei pro Plattform (150–180 MB), die den Webserver Caddy, den PHP-Interpreter und die
Erweiterungen von PHP bündelt. `exp:velocity install` lädt sie für eine festgelegte Version aus den GitHub-Releases des
Projekts FrankenPHP herunter und prüft ihren SHA-256, bevor sie je ausgeführt wird.

Auf dieser Engine läuft PHP im **klassischen Modus**: ein Pool von PHP-Threads innerhalb eines Prozesses, jede Anfrage
beginnt mit einem sauberen Zustand, genau wie unter php-fpm. Der Worker-Modus (persistente PHP-Skripte über Anfragen
hinweg) wird **bewusst nicht angeboten**, weil der Kernel von Exponential den Zustand der Anfrage in globalen Variablen
hält und eine Anfrage in die nächste durchsickern würde. Das ist eine echte Einschränkung, die Sie kennen sollten: Sie
erhalten die Isolation und die Startkosten pro Anfrage wie bei php-fpm, nicht den warmen Zustand eines langlebigen
Workers.

Der Server wird über ein **Caddyfile konfiguriert, das bei jedem `start`, `graceful` und `restart` aus `velocity.ini`
erzeugt wird**, und über die Admin-API von Caddy auf einem Unix-Socket gesteuert.

### Eine Engine wählen

Die vollständige Vergleichstabelle steht in [`velocity-engines.md`](velocity-engines.md). Kurz gesagt:

| Engine | Rolle | Wählen Sie sie, wenn |
|---|---|---|
| `qbix` | **empfohlen**: jede Phase, von der Entwicklung bis zur Produktion | Sie die schnellste Engine möchten: persistente Worker, der Antwort-Cache und der rollenabhängige HTTP-Cache im Server beantwortet, HTTP/2, das Panel `/Q/` und der Konfigurationsbaum `/etc/vc` |
| `frankenphp` | produktionsreife Alternative | Sie eine geprüfte Binärdatei, TLS + HTTP/2, Isolation wie bei php-fpm und kein PHP/php-fpm der Maschine zu pflegen möchten |
| `php` | Entwicklung (ausgelieferter Standard) | Sie gar nichts installieren möchten; nur einfaches HTTP, kein Neuladen |

FrankenPHP hat **noch keinen Antwort-Cache** (`[CacheSettings]` wird ignoriert — die Engine sagt das unter `status`).
Verlassen Sie sich auf den Antwort-Cache von Velocity im Server, ist das heute Sache von qbix. FrankenPHP liefert
statische Assets direkt aus und spricht HTTP/2.

Die Engine-Klasse ist `expVelocityFrankenPHP` (`kernel/classes/expvelocityfrankenphp.php`); das Installationsprogramm
ist `expVelocityFrankenPHPInstaller` (`kernel/classes/expvelocityfrankenphpinstaller.php`).


## 2. Die Installation, Schritt für Schritt, wie getestet

Das ist die Abfolge, die die funktionierende Installation `https://www.example.com:8070/site/` ergab. Zum Kopieren und
Einfügen.

### 2.1 Die Engine wählen (optional — Sie können auch einfach `--engine=frankenphp` übergeben)

`Engine` in `[ServerSettings]` ist die *Standard*-Engine, die ohne `--engine` verwendet wird. Auf der Testinstallation
bleibt der Standard `qbix` (es liefert die Live-Website aus); FrankenPHP läuft daneben und wird ausdrücklich mit
`--engine=frankenphp` angesprochen. Um stattdessen FrankenPHP zum Standard zu machen:

```bash
php bin/php/console exp:velocity config set ServerSettings Engine frankenphp --allow-root-user
```

`config set` schreibt nach `settings/override/velocity.ini.append.php`. Sie können diese Datei auch von Hand bearbeiten
(siehe §3).

### 2.2 Die Engine konfigurieren (ein ausgearbeitetes Beispiel)

Alles Folgende gehört in `settings/override/velocity.ini.append.php` unter `[FrankenPHPSettings]` (dazu
`[HTTPSSettings]` und `[PHPSettings]`). Das ist der genau so getestete Block, mit Anmerkungen:

```ini
[FrankenPHPSettings]
# Own address/ports so frankenphp never clashes with the qbix engine on 8080.
Port=8089                 # plain HTTP (packaged default, kept)
HTTPSPort=8070            # public HTTPS
HTTPS=enabled
Host=203.0.113.10          # bind the public interface (same one qbix binds)
DocumentRoot=             # empty = installation root; /site/ resolves the 'site' siteaccess
# Corrected checksum for the v1.12.7 gnu asset because FrankenPHP re-cut the
# release and the packaged pin went stale (see §2.4). This value is GitHub's
# own published release digest, verified against the downloaded asset.
Sha256[frankenphp-linux-x86_64-gnu]=1897afb7b80d0f108af27815a449f6b6e57fa3d7847eb8fe842f0928c443e3e2

[HTTPSSettings]
# The real Let's Encrypt certificate Plesk holds for this host — the same file
# the qbix engine serves. Browser-trusted, read-only. Exported outside the
# document root; re-export after each renewal (see §4).
Enabled=true
Certificate=/usr/local/psa/var/certificates/<certificate file of the domain>
Key=/usr/local/psa/var/certificates/<certificate file of the domain>

[PHPSettings]
# FrankenPHP's embedded PHP has no machine php.ini, so no default MySQL socket.
# Point mysqli/pdo_mysql at the real socket or DB connections fail (see §6).
IniOptions[]=mysqli.default_socket=/var/lib/mysql/mysql.sock
IniOptions[]=pdo_mysql.default_socket=/var/lib/mysql/mysql.sock
```

> Hinweis: `Host`, `DocumentRoot` und die beiden `IniOptions[]` sind die Einstellungen, mit denen die Installation auf
> der Testinstallation funktionierte. `Sha256[…]` wird nur wegen des neu geschnittenen Release gebraucht (§2.4); eine
> neue Version mit intaktem Pin braucht hier nichts.

### 2.3 Die Binärdatei installieren

```bash
php bin/php/console exp:velocity install --engine=frankenphp --allow-root-user
```

Das lädt das Release-Asset für diese Maschine (`frankenphp-linux-x86_64-gnu` für `Variant=gnu` auf x86_64/glibc) von
`https://github.com/php/frankenphp/releases/download/v1.12.7/…` herunter, prüft seinen SHA-256 gegen den Pin, führt
`frankenphp version` aus, um zu bestätigen, dass es läuft, und legt es hier ab:

```
var/vc/frankenphp/bin/frankenphp-1.12.7-linux-x86_64-gnu   (git-ignored)
var/vc/frankenphp/bin/frankenphp-1.12.7-linux-x86_64-gnu.sha256
```

Optionen der Installation (des Verbs `install`):

| Option | Wirkung |
|---|---|
| *(keine)* | die festgelegte Version herunterladen, wenn sie noch nicht installiert ist |
| `--force` | erneut herunterladen, auch wenn vorhanden |
| `--from=<file>` | eine Binärdatei installieren, die Sie von Hand hierher kopiert haben (Maschine ohne Zugang zu GitHub) — **trotzdem gegen den festgelegten Sha256 geprüft** |
| `--check` | die installierte Binärdatei erneut hashen und melden, ob sie passt |
| `--trust-github-digest` | für eine `Version` **ohne** Pin `Sha256[]`: den Digest akzeptieren, den GitHub für das Release veröffentlicht (schwächer — dieselbe Quelle wie die Datei) |

`start` installiert auch von selbst, wenn die Binärdatei fehlt und `AutoInstall=enabled` gilt (der Standard). Setzen Sie
`AutoInstall=disabled`, um ein ausdrückliches `install` zu verlangen.

### 2.4 Wenn der Pin veraltet ist — das Vorgehen bei einem neu geschnittenen Release (die Falle, in die wir gerieten)

FrankenPHP hat **v1.12.7 neu veröffentlicht**, nachdem unser Pin gesetzt war. Das neu geschnittene Archiv hat einen
anderen SHA-256, daher lehnte `install` es ab:

```
SHA-256 mismatch for frankenphp-linux-x86_64-gnu: expected <old>, got <new> -- nothing installed
```

Die **falsche** Lösung ist, die Prüfung auszuschalten. Die **richtige, sichere** Lösung ist, den neuen Download gegen
den *maßgeblichen* Release-Digest von GitHub zu prüfen und, wenn er passt, den Pin zu aktualisieren. GitHub berechnet
den Digest auf dem Server aus dem hochgeladenen Asset, daher ist er eine stärkere Instanz als ein lokal neu
berechneter Hash.

```bash
# 1. Ask GitHub for the authoritative digest of the asset for this version:
gh api repos/php/frankenphp/releases/tags/v1.12.7 \
  --jq '.assets[]|select(.name=="frankenphp-linux-x86_64-gnu")|.digest'
# -> sha256:1897afb7b80d0f108af27815a449f6b6e57fa3d7847eb8fe842f0928c443e3e2
```

Entspricht dieser Wert dem, was der Download tatsächlich ist, setzen Sie ihn als Pin in
`settings/override/velocity.ini.append.php` unter `[FrankenPHPSettings]` (ohne das Präfix `sha256:`):

```ini
Sha256[frankenphp-linux-x86_64-gnu]=1897afb7b80d0f108af27815a449f6b6e57fa3d7847eb8fe842f0928c443e3e2
```

Führen Sie dann die Installation erneut aus:

```bash
php bin/php/console exp:velocity install --engine=frankenphp --force --allow-root-user
```

**Warum das das Standardvorgehen ist, wenn ein Pin veraltet:** Setzen Sie einen Pin nie auf das, was Sie zufällig
heruntergeladen haben — das hebelt die Prüfung aus. Setzen Sie ihn nur auf den Digest, den GitHub selbst für das
Release veröffentlicht, also dieselbe Instanz wie das Asset. Für eine Version, die *ohne* Pin `Sha256[]` ausgeliefert
wird, tut `install --trust-github-digest` das Gleiche automatisch (es holt den Digest von `ApiUrl` und verwendet ihn) —
bequem, aber schwächer als ein von einem Menschen geprüfter Pin, weil es der Release-Seite vertraut statt einem Wert,
den Sie committet haben.

Das offizielle Verb `install` der Engine wurde **nicht** fest darauf programmiert, GitHub zu vertrauen; Sie setzen den
Pin ausdrücklich, deshalb ist die Änderung eine einzeilige, prüfbare Bearbeitung in der Override-Datei.

### 2.5 Die Firewall für den HTTPS-Port öffnen

FrankenPHP bindet `Host` und lauscht auf `Port` / `HTTPSPort`; die Firewall des Betriebssystems muss den HTTPS-Port
erlauben. Auf der Testinstallation ist die Firewall **firewalld**:

```bash
firewall-cmd --permanent --zone=public --add-port=8070/tcp
firewall-cmd --reload
firewall-cmd --list-ports          # confirm 8070/tcp (and 8080/tcp for qbix) are present
```

Welche Firewall wirkt, ist von Maschine zu Maschine verschieden:

- **firewalld** (die Testinstallation): `firewall-cmd --state` meldet `running`; verwaltet wird sie mit
  `firewall-cmd`. Überprüft: `8070/tcp` und `8080/tcp` sind hier offen.
- **Von Plesk verwaltet**: Manche Plesk-Installationen steuern firewalld oder nftables über die Erweiterung Firewall
  von Plesk. Verschwindet eine mit `firewall-cmd` hinzugefügte Regel beim nächsten Anwenden durch Plesk, fügen Sie sie
  stattdessen in Plesk hinzu (Tools & Settings → Firewall).
- **iptables/nftables direkt**: Prüfen Sie mit `iptables -L -n` / `nft list ruleset`; fügen Sie dort eine Regel für den
  Port hinzu.

Faustregel: Fügen Sie den Port mit dem Werkzeug hinzu, dem das Regelwerk auf der Maschine *gehört*, sonst hält die
Änderung nicht.

### 2.6 Starten und prüfen (getestet)

```bash
php bin/php/console exp:velocity start --engine=frankenphp --allow-root-user
```

Prüfen — das sind genau die Prüfungen, die auf der Testinstallation ausgeführt wurden, alle bestanden:

```bash
# Site answers 200 (‑k because a self-signed cert would otherwise fail; here the
# cert is real, but ‑k keeps the check independent of the client trust store):
curl -k -o /dev/null -w '%{http_code}\n' https://203.0.113.10:8070/site/     # -> 200

# Certificate issuer (should be Let's Encrypt for the shared Plesk cert):
echo | openssl s_client -servername www.example.com -connect 203.0.113.10:8070 \
  2>/dev/null | openssl x509 -noout -issuer
# -> issuer=C=US, O=Let's Encrypt, CN=YR1

# qbix on 8080 is undisturbed:
curl -k -o /dev/null -w '%{http_code}\n' https://203.0.113.10:8080/          # -> 200
```

`/site/` ist der Pfad, der den Siteaccess `site` von Exponential über das Routing von `.htaccess_root` auflöst, wenn
`DocumentRoot` das Wurzelverzeichnis der Installation ist. Das bloße `/` löst den Standard-Siteaccess auf; `status` gibt
die vollständige Liste der URLs aus.

FrankenPHP läuft vollständig **neben** qbix: eigener `Port`/`HTTPSPort`, eigene PID-Datei, eigene Logs, eigener
Admin-Socket. Nichts am Start von frankenphp berührt den laufenden qbix-Prozess — verwechseln Sie die beiden nicht.

## 3. Vollständige Referenz der Konfiguration

FrankenPHP liest drei Blöcke in `velocity.ini` (Standardwerte) / `settings/override/velocity.ini.append.php` (Ihre
Overrides): `[FrankenPHPSettings]`, `[PHPSettings]` und `[HTTPSSettings]`. Außerdem liest es eine Handvoll gemeinsamer
Schlüssel aus `[ServerSettings]`, `[LogSettings]` und `[ControlSettings]`. Einstellungen, die nur zu einer anderen
Engine gehören, werden **gemeldet, nicht stillschweigend verworfen** — `status` und `start` führen jede auf, die eine
Installation gesetzt hat und die diese Engine ignoriert.

Vorrang für `Port`, `HTTPSPort`, `Host`, `DocumentRoot`: der Wert aus `[FrankenPHPSettings]`, wenn gesetzt, sonst der
Wert aus `[ServerSettings]`. Das erlaubt der Engine, neben den anderen zu laufen. `Workers` und `SpareWorkers` sind die
Ausnahme: Leer fallen sie **nicht** auf `[ServerSettings]` zurück (den Prozess-Pool des Qbix-Servers), sondern
standardmäßig auf die doppelte Zahl der CPU-Kerne und keine zusätzlichen -- siehe §8.

### 3.1 `[FrankenPHPSettings]`

| Schlüssel | Bedeutung | Standard (velocity.ini) | Beispiel |
|---|---|---|---|
| `Port` | Port für einfaches HTTP | `8089` | `8089` |
| `HTTPSPort` | HTTPS-Port (wenn TLS eingeschaltet ist) | `8444` | `8070` |
| `HTTPS` | `enabled`/`true` = TLS auf `HTTPSPort` neben HTTP ausliefern; `disabled` = dauerhaft aus | `enabled` | `enabled` |
| `Host` | Bindeadresse; leer = `[ServerSettings] Host` | *(leer → 127.0.0.1)* | `203.0.113.10` |
| `Workers` | Zahl der PHP-Threads (klassischer Modus) = `num_threads`; leer = doppelte Zahl der CPU-Kerne (nie `[ServerSettings] Workers`) | *(leer → 2 × Kerne)* | `24` |
| `SpareWorkers` | zusätzliche Threads, die unter Last gestartet werden; `max_threads = Workers + SpareWorkers`; leer = 0 (nie `[ServerSettings] SpareWorkers`) | *(leer → 0)* | `0` |
| `DocumentRoot` | Web-Wurzel; leer = Wurzelverzeichnis der Installation (wo `index.php` liegt) | *(leer)* | *(leer)* |
| `Version` | herunterzuladendes Release von FrankenPHP (ohne führendes `v`) | `1.12.7` | `1.12.7` |
| `Variant` | Linux-Build: `gnu`, `musl`, `mimalloc` | `gnu` | `gnu` |
| `Sha256[<asset>]` | SHA-256-Pin pro Release-Asset; ein Download, der nicht passt, wird gelöscht und nie ausgeführt | 7 Pins ausgeliefert | `Sha256[frankenphp-linux-x86_64-gnu]=…` |
| `BinaryPath` | eine eigene Binärdatei statt des Downloads verwenden; nichts wird geholt | *(leer)* | `/opt/frankenphp` |
| `BinarySha256` | optionaler Pin, der für `BinaryPath` geprüft wird | *(leer)* | `<hex>` |
| `BinaryDir` | wohin `install` den Download legt | `var/vc/frankenphp/bin` | |
| `AutoInstall` | beim ersten Start installieren, wenn die Binärdatei fehlt | `enabled` | |
| `DownloadUrl` | URL-Vorlage des Assets; `{version}`, `{asset}` werden ausgefüllt | `https://github.com/php/frankenphp/releases/download/v{version}/{asset}` | |
| `ApiUrl` | URL der Release-Metadaten (nur von `--trust-github-digest` gelesen) | `https://api.github.com/repos/php/frankenphp/releases/tags/v{version}` | |
| `AdminAddress` | Adresse der Admin-API von Caddy; `auto` = Unix-Socket, sonst `unix/<path>` oder `host:port` | `auto` | `auto` |
| `ConfigFile` | wohin das erzeugte Caddyfile geschrieben wird | `var/vc/frankenphp/run/Caddyfile` | |
| `PidFile` | PID-Datei | `var/vc/frankenphp/run/server.pid` | |
| `LogFile` | Konsolenausgabe des Servers | `var/vc/frankenphp/run/console.log` | |
| `LogDir` | Verzeichnis für Zugriffs- und Fehlerprotokoll; leer = `var/vc/frankenphp/log` | *(leer)* | |
| `AccessLog` | Pfad des Zugriffsprotokolls überschreiben | *(leer)* | |
| `ErrorLog` | Pfad des Fehlerprotokolls überschreiben | *(leer)* | |
| `Compression` | Codecs, die Clients angeboten werden, die danach fragen, in der Reihenfolge der Bevorzugung, aus `zstd`, `br`, `gzip`; oder `disabled` (siehe §8 Komprimierung) | `zstd br gzip` | `br zstd gzip` |
| `SiteInclude` | Caddyfile-Fragment, das in jeden Site-Block importiert wird (Ihre eigenen Direktiven) | *(leer)* | `settings/caddy/extra.caddy` |
| `PHPIniFile` | eine php.ini für das eingebettete PHP (setzt `PHPRC`) | *(leer)* | |
| `IniOptions[]` | Einstellungen des Interpreters, *nach* `[PHPSettings] IniOptions[]` angewendet | `display_errors=Off`, `log_errors=On`, `memory_limit=512M`, `opcache.enable=1`, `post_max_size=64M`, `upload_max_filesize=32M` | siehe §6 |
| `ExtraOptions[]` | zusätzliche Argumente, unverändert an `frankenphp run` angehängt | *(leer)* | |

Für `Sha256[]` / `Variant` erkannte Assets (aus dem Installationsprogramm): `frankenphp-linux-x86_64` (musl),
`-x86_64-gnu` (glibc), `-x86_64-mimalloc`, `-aarch64`, `-aarch64-gnu`, `frankenphp-mac-arm64`,
`frankenphp-mac-x86_64`. Für Windows wird nichts heruntergeladen — verwenden Sie `BinaryPath`.

### 3.2 `[PHPSettings]`

Mit der Engine qbix gemeinsam. `IniOptions[]` werden **zuerst** angewendet, dann `[FrankenPHPSettings] IniOptions[]`
(ein späterer Schlüssel gewinnt). Die ausgelieferten Standardwerte schalten OPcache für CLI/eingebettetes PHP ein und
bemessen die internierten Zeichenketten; siehe §6 und §8.

### 3.3 `[HTTPSSettings]`

| Schlüssel | Bedeutung | Standard |
|---|---|---|
| `Enabled` | `true` + ein Zertifikat schaltet TLS ein (zusätzlich zu `[FrankenPHPSettings] HTTPS`) | `false` |
| `Certificate` | Pfad des PEM-Zertifikats (relativ wird gegen das Wurzelverzeichnis aufgelöst) | *(leer)* |
| `Key` | Pfad des privaten PEM-Schlüssels | *(leer)* |

`Certificate` und `Key` müssen beide existierende Dateien nennen, sonst wird der Start abgelehnt. Lassen Sie beide
leer für ein selbstsigniertes Zertifikat (§4).

### 3.4 Gemeinsame Schlüssel, die diese Engine beachtet

- `[ServerSettings]`: `Port`, `HTTPSPort`, `Host`, `DocumentRoot` (Rückfallwerte -- nicht `Workers`/`SpareWorkers`,
  siehe §8), `HTTP2` (`enabled` → `h2` im TLS-Handshake anbieten, ausgegeben als `protocols h1 h2`), `StaticMaxAge`
  (Sekunden; `>0` sendet `Cache-Control: public, max-age=<n>` bei statischen Assets).
- `[LogSettings]`: `Enabled` (aus = überhaupt kein Zugriffs- oder Fehlerprotokoll), `FileMode` (oktaler Modus der
  Logdateien). **`Format` wird ignoriert** — Caddy schreibt JSON.
- `[ControlSettings]`: `StopTimeout` (Sekunden; wird auch zur `grace_period` von Caddy).
- `EnginePhar` (`[ServerSettings]`): den Kernel aus `dist/engine.phar` ausführen, wenn auf `enabled`/einen Pfad
  gesetzt; als `EXP_ENGINE_PHAR` übergeben. Bauen Sie es zuerst mit `bin/php/phar.php build`, sonst wird der Start
  abgelehnt.

### 3.5 Einstellungen, die diese Engine ignoriert (und das sagt)

`status`/`start` führen nur die auf, die eine Installation tatsächlich gesetzt hat. Auf der Testinstallation ist die
aktuelle Liste:

- `CacheSettings` — auf der Engine frankenphp noch kein Antwort-Cache.
- `ForkPerRequest` — nicht nötig; im klassischen Modus beginnt jede Anfrage sauber.
- `KeepGlobals` — zwischen Anfragen wird nichts behalten.
- `FollowSymlinks=disabled` — der Dateiserver von Caddy folgt Links aus dem Document Root heraus immer; es gibt dafür
  keinen Schalter. (Sicherheitshinweis in §9.)
- `PreloadWarmup` — nur Qbix-Server.
- `LogSettings Format=<x>` — Caddy schreibt JSON.
- `Brand*` / `HTTP2ErrorImage` — nur die eigenen Seiten des Qbix-Servers.
- `DashboardSettings` — nur das Dashboard des Qbix-Servers.
- `ControlSettings ExtraOptions` — Optionen von Qbix; verwenden Sie `[FrankenPHPSettings] ExtraOptions[]`.


## 4. HTTPS und Zertifikate

FrankenPHP liefert HTTPS auf `HTTPSPort` neben einfachem HTTP auf `Port` aus, **standardmäßig eingeschaltet**
(`HTTPS=enabled`), sodass eine Entwicklungsmaschine ab dem ersten Start `https://` hat. TLS verwendet eine von drei
Quellen für Zertifikate, in dieser Reihenfolge:

1. **Genanntes Zertifikat** — `[HTTPSSettings] Certificate` **und** `Key` sind beide gesetzt. Beide müssen
   existierende Dateien nennen, sonst wird der Start abgelehnt (mit `--https` oder einem genannten Zertifikat ist eine
   fehlende Datei ein harter Fehler; ohne fällt es auf ein selbstsigniertes zurück und sagt, warum). Das verwendet die
   Testinstallation.
2. **Selbstsigniert** — beide leer. Die Engine erzeugt ein Zertifikat für diese Maschine (`localhost`, `127.0.0.1`,
   `::1`, der Hostname und eine bestimmte Bindeadresse außerhalb des Loopbacks), SHA-256, RSA 2048, 365 Tage gültig, in
   `var/vc/frankenphp/tls/` (Schlüssel `0600`, Zertifikat `0644`). Es wird automatisch erneuert, wenn weniger als 30 Tage
   übrig sind. Browser warnen davor — in Ordnung für die Entwicklung und hinter einem Reverse Proxy, **nicht für
   Besucher**. Braucht die Erweiterung `openssl`; ohne sie startet der Server mit einfachem HTTP, und die Startmeldung
   sagt, warum.
3. **Let's Encrypt / ACME** — FrankenPHP/Caddy kann Zertifikate über ACME beziehen, aber diese Engine setzt
   `auto_https off` im erzeugten Caddyfile, daher steuert Velocity ACME **nicht** für Sie. Zwei unterstützte Wege:
   - **Ein vorhandenes Zertifikat mitnutzen** (der Ansatz der Testinstallation): Lassen Sie `[HTTPSSettings]
     Certificate`/`Key` auf ein Zertifikat zeigen, das bereits ein anderes System verwaltet — hier das Zertifikat von
     Let's Encrypt, das **Plesk** für den Host hält, außerhalb des Document Root exportiert. Es ist dieselbe Datei, die
     die Engine qbix ausliefert. Von Browsern anerkannt, keine ACME-Arbeit in Velocity.
   - **Eine eigene ACME-/Caddy-Konfiguration** über `SiteInclude` (§5/§10): Fügen Sie TLS-Direktiven von Caddy (z. B.
     einen Aussteller `acme`) in das importierte Fragment ein. Für Fortgeschrittene; die Schleife der Erneuerung gehört
     dann Ihnen.

### Erneuerung (mitgenutztes Zertifikat von Plesk)

Plesk erneuert das Zertifikat von Let's Encrypt nach eigenem Zeitplan und schreibt dabei eine **neue Datei unter einem
neuen Namen**; die exportierte Kopie veraltet dann. Der Export — das Kopieren von Zertifikat und Schlüssel, die Plesk der
Domain gerade zuweist (unter `/usr/local/psa/var/certificates/`), in die in `[HTTPSSettings] Certificate`/`Key`
genannten Pfade — muss **nach jeder Erneuerung wiederholt** werden, gefolgt von einem Neuladen der Engine, damit sie die
neue Datei übernimmt:

```bash
# after copying the renewed certificate and key into place:
php bin/php/console exp:velocity graceful --engine=frankenphp --allow-root-user
```

> Ein `graceful` lädt die Konfiguration neu (und liest damit die Zertifikatsdateien erneut), ohne Verbindungen
> abzubrechen. Da der Zertifikatspfad in `velocity.ini` fest ist, müssen Sie bei einer Änderung des *Pfads* auch
> `[HTTPSSettings] Certificate`/`Key` aktualisieren.

### TLS-Overrides pro Start

```bash
php bin/php/console exp:velocity start --engine=frankenphp --no-https   # plain HTTP, this start only
php bin/php/console exp:velocity start --engine=frankenphp --https      # insist on HTTPS, this start only
```

`--https`/`--no-https` gehören zur Engine frankenphp (der eingebaute Server `php` hat kein TLS; die Engine qbix nimmt
`[HTTPSSettings]`). Ein mit `--https` gestarteter Server liefert TLS aus, auch wenn die Einstellungen das nicht sagen —
`status` liest das laufende Caddyfile, nicht nur die Einstellungen, und meldet daher die Wahrheit.


## 5. Das erzeugte Caddyfile

`var/vc/frankenphp/run/Caddyfile` wird bei jedem `start`, `graceful` und `restart` aus `velocity.ini` neu erzeugt,
atomar geschrieben und **mit `frankenphp validate` geprüft, bevor es das vorherige ersetzt** — eine fehlerhafte
Konfiguration erreicht nie den laufenden Server (ein fehlerhaftes `SiteInclude` lässt die laufende Datei unberührt). Der
Kopf sagt: *"Do not edit: it is rewritten on every start, graceful and restart."*

### Was es enthält (überprüfte Ausgabe)

- **Globaler Block**: `auto_https off` (Velocity verwaltet TLS, nicht ACME); `admin <AdminAddress>`;
  `persist_config off`; `grace_period <StopTimeout>s`; `servers { protocols h1 [h2] }`; ein Block `log` für Fehler
  (JSON, Stufe INFO); und ein Block `frankenphp { … }` mit `num_threads <Workers>`,
  `max_threads <Workers+SpareWorkers>` und einer Zeile `php_ini "<key>" "<value>"` pro aufgelöster INI-Option (§6).
- **Snippet `(exp)`** — in jeden Site-Block importiert: `root * <DocumentRoot>`; ein Block `log` für Zugriffe
  (`format json`); `encode <codecs>` aus `Compression` (z. B. `encode zstd br gzip`; weggelassen bei
  `Compression=disabled`); und ein `route { … }`, das `.htaccess_root` umsetzt:
  - `respond /Q/health 200` — Health-Check.
  - Matcher `@static` = `expVelocity::STATIC_PATHS` **und nicht** `expVelocity::NEVER_STATIC`;
    `header @static Cache-Control` (bei `StaticMaxAge>0`); `file_server @static`.
  - `@rest` `^/(api/|index_rest\.php)` → `rewrite /index_rest.php`.
  - `@treemenu` `^/([^/]+/)?content/treemenu` → `rewrite /index_treemenu.php`.
  - `@front` (alles andere) → `rewrite /index.php`.
  - `php { env EXP_VELOCITY_ENGINE "<version>"; [env EXP_ENGINE_PHAR "<phar>"] }`.
- **Site-Blöcke**: `http://:<Port>` und, wenn TLS eingeschaltet ist, `https://:<HTTPSPort> { tls "<cert>" "<key>" }`.
  Jeder hat `bind <Host>`, `import exp` und `import "<SiteInclude>"`, wenn gesetzt.

`STATIC_PATHS` (der genaue reguläre Ausdruck) passt auf Stylesheets/Bilder/JavaScript/Schriften von Designs und
Erweiterungen, `share/icons/`, Icon-Themes von Erweiterungen
(`extension/<name>/icons/`, Bilder mit genau einer Dateiendung), `var/*/storage/images`, Originale im Speicher für Bilder, öffentliche Caches und
texttoimage-Caches, Styles von Paketen, `var/storage/packages/`, `favicon.ico`, `robots.txt`, `index.js`, `sw.js`,
`w3c/p3p.xml`. `NEVER_STATIC` = `(?i)(\.(php\d?|phtml|phar)$|/\.)` — kein PHP-Quelltext und keine Punktdatei wird je
als Datei ausgeliefert, auch nicht unterhalb eines Asset-Verzeichnisses; beide fallen an `index.php` durch. Deshalb
verwendet die Engine **nicht** `php_server` von Caddy (dessen Standard `settings/*.ini`, die SQLite-Datenbank und die
Quellen des Kernels ausliefern würde).

### Die Binärdatei untersuchen und steuern — `exp:velocity ctl …`

`ctl` führt die eigenen Unterbefehle der Binärdatei aus, mit dem Caddyfile dieser Installation:

| Befehl | Tut |
|---|---|
| `ctl caddyfile` | das Caddyfile ausgeben, das diese Einstellungen erzeugen (berührt die Datei auf der Festplatte nicht) |
| `ctl validate` | das Caddyfile schreiben und dann mit `frankenphp validate` prüfen |
| `ctl adapt` | schreiben und adaptieren (die JSON-Konfiguration zeigen, die Caddy ausführen würde) |
| `ctl version` | `frankenphp version` (Versionen von FrankenPHP, PHP und Caddy) |
| `ctl list-modules` | die Caddy-Module auflisten, mit denen die Binärdatei gebaut wurde |
| `ctl build-info` | Go-Build-Informationen der Binärdatei |
| `ctl environ` | die Umgebung, die die Binärdatei sieht |

```bash
php bin/php/console exp:velocity ctl caddyfile --engine=frankenphp --allow-root-user
php bin/php/console exp:velocity ctl version   --engine=frankenphp --allow-root-user
php bin/php/console exp:velocity ctl list-modules --engine=frankenphp --allow-root-user | grep cache
```

### Eigene Caddy-Konfiguration — `SiteInclude`

Legen Sie Ihre eigenen Direktiven (Header, Weiterleitungen, zusätzliche Routen, einen eigenen TLS-Aussteller) in eine
Datei und nennen Sie sie mit `[FrankenPHPSettings] SiteInclude`. Sie wird in **jeden** Site-Block importiert (den für
HTTP und den für HTTPS). Die erzeugte Datei wird bei jedem Start neu geschrieben, bearbeiten Sie sie daher nie direkt.
Ein `SiteInclude`, das Caddy ablehnt, besteht die Prüfung nicht, und die laufende Konfiguration bleibt unberührt.

Beispiel `settings/caddy/extra.caddy`:

```caddy
header {
    Strict-Transport-Security "max-age=31536000"
    X-Content-Type-Options "nosniff"
}
```

```ini
[FrankenPHPSettings]
SiteInclude=settings/caddy/extra.caddy
```


## 6. PHP-Konfiguration für den eingebetteten Interpreter

Das ist das Wichtigste, was Sie über FrankenPHP hier verstehen müssen:

**Die Binärdatei liest keine php.ini der Maschine, auf der sie läuft.** Ihr PHP ist einkompiliert und läuft ohne
Konfiguration mit den einkompilierten Standardwerten von PHP — `display_errors` eingeschaltet, ein Speicherlimit von
128 MB und **kein Pfad zum MySQL-Socket**. Alles, was die php.ini der Maschine normalerweise liefern würde, muss daher
hier geliefert werden, als Zeilen `php_ini` im Caddyfile.

Zwei Quellen speisen diese Zeilen, in dieser Reihenfolge (das Spätere gewinnt):

1. `[PHPSettings] IniOptions[]` (mit qbix gemeinsam)
2. `[FrankenPHPSettings] IniOptions[]`

Jede ist `key=value`, genau wie nach `php -d`. Die Engine gibt sie als `php_ini "<key>" "<value>"` innerhalb des globalen
Blocks `frankenphp { … }` aus.

Alternativ verweist `[FrankenPHPSettings] PHPIniFile=<path>` das eingebettete PHP über `PHPRC` auf eine vollständige
eigene php.ini.

### 6.1 Die Korrektur für den MySQL-Socket (die wichtigste Falle)

Mit `[DatabaseSettings] Server=localhost` verbinden sich mysqli/pdo_mysql über einen **Unix-Socket**, dessen Pfad aus
der php.ini kommt. Das eingebettete PHP hat keine php.ini, daher keinen Standard-Socket, und die Verbindung schlägt fehl:

```
No such file or directory in .../kernel/classes/dbdrivers/ezmysqli/ezmysqlidb.php
```

Exponential zeigt dann die allgemeine Seite **„An unexpected error has occurred.“**.

**Lösung** — teilen Sie dem eingebetteten PHP mit, wo der Socket ist:

```ini
[PHPSettings]
IniOptions[]=mysqli.default_socket=/var/lib/mysql/mysql.sock
IniOptions[]=pdo_mysql.default_socket=/var/lib/mysql/mysql.sock
```

Finden Sie den echten Pfad des Sockets auf Ihrer Maschine:

```bash
php -i | grep -i "mysqli.default_socket\|pdo_mysql.default_socket"
# test installation: mysqli.default_socket => /var/lib/mysql/mysql.sock => /var/lib/mysql/mysql.sock
```

**Alternative** — verwenden Sie TCP statt eines Sockets: Setzen Sie `[DatabaseSettings] Server=127.0.0.1` in
`site.ini`. Dann verbindet sich mysqli über TCP, und es wird kein Socket-Pfad gebraucht — **aber MySQL muss auf TCP
lauschen** (kein `skip-networking`, und so gebunden, dass das Loopback erreichbar ist). Der Weg über den Socket ist
meist schneller und braucht keine Änderung an der Konfiguration von MySQL, deshalb hat ihn die Testinstallation gewählt.

**Warum das allgemein gilt:** Jedes Verhalten, das die php.ini der Maschine normalerweise liefert — ein
Standard-Socket, eine Zeitzone, die Konfiguration einer Erweiterung, ein `sendmail_path`, ein `open_basedir`, ein
`session.save_path` — **fehlt** unter FrankenPHP, sofern Sie es nicht als Zeile `IniOptions[]` (oder über
`PHPIniFile`) hinzufügen. Funktioniert etwas unter Apache/php-fpm, aber nicht unter FrankenPHP, vermuten Sie einen Wert
der php.ini, den das eingebettete PHP nie gesehen hat, und fügen Sie ihn hier hinzu.

### 6.2 Erweiterungen, die die Binärdatei mitbringt, gegenüber denen, die die Anwendung braucht

Die offiziellen Builds von FrankenPHP bringen eine breite Auswahl an PHP-Erweiterungen mit, aber **nicht jede**
Erweiterung, die Ihre Exponential-Installation unter dem PHP der Maschine laden könnte. Die Variante `gnu` kann externe
`.so`-Erweiterungen per `dlopen` laden (z. B. `oci8`); die Variante `musl` ist vollständig statisch und kann das nicht.
So sehen Sie, was die Binärdatei tatsächlich hat:

```bash
# The binary is PHP too; -m lists modules:
var/vc/frankenphp/bin/frankenphp-1.12.7-linux-x86_64-gnu -r 'print_r(get_loaded_extensions());'
# or via ctl:
php bin/php/console exp:velocity ctl list-modules --engine=frankenphp --allow-root-user   # Caddy modules
```

Braucht die Anwendung eine Erweiterung, die der Binärdatei fehlt, und verwenden Sie `gnu`, laden Sie die passende `.so`
mit einer Zeile `IniOptions[]=extension=/path/to/ext.so`. Gibt es sie nicht für die genaue PHP-Version/ABI der
Binärdatei, brauchen Sie einen **eigenen Build** (§10) — eine echte Einschränkung des Modells mit einer einzigen
Binärdatei.

### 6.3 Ausgelieferte INI-Standardwerte

`[FrankenPHPSettings] IniOptions[]` liefert aus (als Ersatz für die fehlende php.ini): `display_errors=Off`,
`log_errors=On`, `memory_limit=512M`, `opcache.enable=1`, `post_max_size=64M`, `upload_max_filesize=32M`.
`[PHPSettings] IniOptions[]` liefert die Optimierung von OPcache aus (siehe §8). Beachten Sie: `opcache.revalidate_freq=0`
aus `[PHPSettings]` schadet hier nicht (jede Anfrage hat ihre eigene Anfragezeit), daher werden bearbeitete/neu
erzeugte PHP-Dateien immer übernommen.

## 7. Betrieb

### Verben

Alle üblichen Verben von Velocity funktionieren; die Engine erreichen Sie mit `--engine=frankenphp` (oder machen Sie
sie zum Standard). Fügen Sie `--all` / `--engine=a,b` für mehrere Engines hinzu.

```bash
php bin/php/console exp:velocity start    --engine=frankenphp --allow-root-user
php bin/php/console exp:velocity stop     --engine=frankenphp --allow-root-user
php bin/php/console exp:velocity restart  --engine=frankenphp --allow-root-user
php bin/php/console exp:velocity graceful --engine=frankenphp --allow-root-user   # reload, no dropped connections
php bin/php/console exp:velocity kill     --engine=frankenphp --allow-root-user   # SIGKILL, no grace
php bin/php/console exp:velocity status   --engine=frankenphp --allow-root-user
```

- **`start`** prüft alles, was zu einer Ablehnung führen kann (Binärdatei vorhanden oder automatische Installation,
  Zertifikat, Prüfung des Caddyfile, Engine-Phar), *bevor* es irgendetwas tut, lehnt dann ab, wenn der Port bereits
  belegt ist (und nennt den Verursacher), startet dann und fragt bis zu 20 s lang `/Q/health` ab. Es meldet die Version
  und, falls HTTPS entgegen den Einstellungen ausgeschaltet wurde, warum.
- **`graceful`** erzeugt und prüft das Caddyfile neu und lädt dann über die Admin-API neu (`frankenphp reload`). Anders
  als das Neuladen von qbix **kann das keinen halb neu geladenen Server hinterlassen**: Eine Konfiguration, die Caddy
  ablehnt, wird nie angewendet, und die alte liefert weiter aus; ein abgelehntes Neuladen wird *gemeldet*, nicht mit
  einem Neustart beantwortet. Antwortet die Admin-API gar nicht, fällt es auf einen vollständigen Neustart zurück.
- **`stop`** bittet die Admin-API um `/stop` (laufende Anfragen dürfen innerhalb von `grace_period` fertig werden),
  fällt dann auf SIGTERM zurück und meldet, wenn der Server nach `StopTimeout` noch läuft.
- **`restart`** prüft die Bereitschaft, solange der alte Server noch läuft, dann Stopp + Start.

### Der Admin-Socket

`stop` und `graceful` sprechen mit der Admin-API von Caddy. Standardmäßig ist das ein **Unix-Socket** unter
`var/vc/frankenphp/run/admin.sock`, nur für den Besitzer les- und schreibbar — kein TCP-Port, der mit einer anderen
Installation kollidieren könnte. Ist der Pfad der Installation für einen Socket zu lang (> 104 Bytes), fällt es auf
`localhost:<Port + 10000>` zurück. Überschreiben Sie das mit `AdminAddress`. `status` zeigt die Adresse und ob die API
erreichbar ist. Die Engine spricht HTTP direkt über den Socket (ohne Header Origin, den die API ablehnen würde).

### Wo alles liegt

```
var/vc/frankenphp/
├── bin/    frankenphp-<version>-<asset>  (+ .sha256)   the binary (git-ignored)
├── caddy/  config/ + data/               Caddy's XDG state (certs, autosave)
├── tls/    selfsigned.crt, selfsigned.key             self-signed cert (if used)
├── log/    access.log, error.log         JSON; [FrankenPHPSettings] LogDir
└── run/    server.pid, console.log, Caddyfile, admin.sock
```

`status` nennt jede Datei. `status` meldet außerdem Prozess/Threads, Version und Herkunft der Binärdatei (`download`
oder `BinaryPath`), den Zustand von HTTPS und das Zertifikat sowie die Hinweise zu ignorierten Einstellungen.

### Neben qbix betreiben

Auf der Testinstallation überprüft: frankenphp (8089/8070) läuft gleichzeitig mit qbix (8088/8080), jedes mit eigenem
Port, eigener PID, eigenen Logs, eigenem Admin-Socket und eigener Konfiguration. `exp:velocity status --all` zeigt
beide. Starten, Stoppen und Neuladen von frankenphp berührt nie den qbix-Prozess — das Zertifikat wird nur lesend
geteilt, und qbix liest sein Zertifikat aus der erzeugten Konfiguration in `/etc/vc`, daher startet eine Änderung der
Zertifikatseinstellung von frankenphp qbix nicht neu.

### Die Binärdatei aktualisieren

Ändern Sie `Version` **und jede Zeile `Sha256[]` gemeinsam** (die Digests stehen auf der GitHub-API-Seite des Release =
`ApiUrl`), dann `install`. Die alte Binärdatei bleibt in `var/vc/frankenphp/bin`, daher ist ein Zurückrollen nur die
Einstellung. Siehe §2.4 zum Vorgehen bei einem veralteten Pin.

```bash
# after editing Version + Sha256[…] in the override:
php bin/php/console exp:velocity install --engine=frankenphp --allow-root-user
php bin/php/console exp:velocity restart --engine=frankenphp --allow-root-user
```

### Festlegen / eigene Builds

`BinaryPath` zeigt auf eine Binärdatei, die Sie gebaut haben (eine andere PHP-Version, zusätzliche Caddy-Module wie den
Antwort-Cache `cache-handler`); nichts wird heruntergeladen, und `BinarySha256` (wenn gesetzt) wird geprüft. Eigene
Builds entstehen mit `static-builder-gnu.Dockerfile` / `static-builder-musl.Dockerfile` von FrankenPHP — siehe §10.


## 8. Leistung und Optimierung

### Threads (klassischer Modus)

`Workers` = Zahl der PHP-Threads (Grund-Pool), `SpareWorkers` = zusätzliche Threads, die unter Last gestartet werden;
das Caddyfile erhält `num_threads Workers` und `max_threads Workers+SpareWorkers`. Jeder PHP-Thread startet den Kernel
pro Anfrage (klassischer Modus), daher skalieren CPU und Speicher mit der Zahl der Threads. Es gibt keine Kosten für
einen Fork pro Anfrage wie bei einem Prozess-Pool, aber auch keine Wiederverwendung warmer Worker.

**Standard: doppelte Zahl der CPU-Kerne, keine zusätzlichen.** Leer gelassen ist `Workers` gleich `2 × Kerne` (24 auf
einer Maschine mit 12 Kernen) und `SpareWorkers` 0 -- der eigene Standard von FrankenPHP. Keiner fällt auf
`[ServerSettings]` zurück: Diese bemessen den Prozess-Pool des Qbix-Servers, in dem die meisten Prozesse untätig sind,
während die Threads von FrankenPHP alle gleichzeitig um die CPU konkurrieren. Bis zum 2026-09-26 fielen sie zurück, und
die Testinstallation lief mit `num_threads 590`/`max_threads 638`.

**Warum nicht mehr.** Gemessen auf der Testinstallation (12 Kerne, gerenderte Seiten unter `/site/`, je zwei
Durchgänge, die Maschine sonst ausgelastet):

| Threads | 16 gleichzeitig | 64 gleichzeitig | CPU pro Seite bei 64 | p95 bei 64 |
|---:|---:|---:|---:|---:|
| 12 | 16,4 req/s | 16,0 req/s | 233 ms | 4,4 s |
| 24 | 15,6 req/s | 16,0 req/s | 261 ms | 4,5 s |
| 48 | 17,2 req/s | 16,5 req/s | 270 ms | 4,5 s |
| 96 | 16,7 req/s | 14,7 req/s | 294 ms | 5,1 s |
| 590 | 17,5 req/s | 14,6 req/s | 290 ms | 4,9 s |

Bei 16 gleichzeitigen Anfragen macht die Zahl keinen Unterschied. Bei 64 leisten 12–48 Threads etwa 10 % mehr als 96
oder 590, mit bis zu 24 % weniger CPU pro Seite: Jenseits der Zahl der Kerne konkurrieren zusätzliche Threads nur
miteinander und mit der Datenbank. Die Grenze ist die Maschine -- etwa 0,25 s CPU für PHP und 0,25 s für die Datenbank
pro gerenderter Seite --, nicht FrankenPHP; bei 64 gibt es keine Grenze. Die doppelte Zahl der Kerne wird einem Thread
pro Kern vorgezogen, um die CPU beschäftigt zu halten, während Threads auf die Datenbank warten, die die Hälfte jeder
Seite ausmacht. Setzen Sie `Workers` erst ausdrücklich, nachdem Sie Ihre eigenen Seiten gemessen haben.

### Worker-Modus

Nicht verfügbar. Der Kernel von Exponential hält den Zustand der Anfrage in globalen Variablen, daher ist der klassische
Modus (sauberer Zustand pro Anfrage) die einzige sichere Wahl. Versuchen Sie nicht, den Worker-Modus von FrankenPHP von
Hand einzuschalten — er würde Zustand zwischen Anfragen durchsickern lassen.

### OPcache

Die wirksamste Optimierung. Das eingebettete PHP beachtet `opcache.*` über `IniOptions[]`. Ausgeliefert:
`opcache.enable=1` und (aus `[PHPSettings]`) `opcache.enable_cli=1`, `opcache.memory_consumption=256`,
`opcache.max_accelerated_files=20000`, `opcache.interned_strings_buffer=32`, `opcache.revalidate_freq=0`. Gemessen an
dieser Codebasis brachte OPcache allein den Großteil des Gewinns (~+26 %); das Tracing-JIT war bei dieser Last
*langsamer* und fehlt bewusst. Behalten Sie `revalidate_freq=0`, damit bearbeitete/neu erzeugte PHP-Dateien bemerkt
werden.

### Stellschrauben der Go-Laufzeit

FrankenPHP ist ein Go-Programm; die Umgebungsvariablen der Go-Laufzeit gelten und können über die Umgebung des
Prozesses übergeben werden (z. B. im Wrapper von systemd/dem Starter; ein `SiteInclude`, das sie setzt, ist *nicht* der
richtige Ort — das ist Umgebung des Prozesses, keine Konfiguration von Caddy):

- `GOMEMLIMIT` — weiche Speichergrenze für den Go-Heap; nützlich, um den Prozess innerhalb eines cgroup-Limits zu halten.
- `GOMAXPROCS` — Höchstzahl der Betriebssystem-Threads, die gleichzeitig Go-Code ausführen; legen Sie sie auf die Kerne
  fest, die Caddy verwenden soll.

Setzen Sie sie dort, wo der Prozess gestartet wird. Velocity startet die Binärdatei direkt; exportieren Sie sie in der
Shell/im Dienst, der `exp:velocity start` ausführt — `ExtraOptions[]` ist *nicht* für Umgebungsvariablen gedacht;
verwenden Sie die Umgebung des startenden Prozesses.

### Ausliefern statischer Dateien

Statische Assets liefert der `file_server` von Caddy direkt aus (ohne PHP), mit `Cache-Control: public,
max-age=<StaticMaxAge>`, wenn `StaticMaxAge>0` (ausgelieferter Standard 31536000 = ein Jahr). `HTTP2=enabled` lässt einen
Browser die Dutzende von Assets, auf die eine Seite von Exponential verweist, über eine Verbindung bündeln.

### Komprimierung

Caddy komprimiert nichts, sofern man es nicht anweist, daher verließ bis September 2026 jede Seite diese Engine
unkomprimiert. `[FrankenPHPSettings] Compression` setzt jetzt eine Direktive `encode` in das erzeugte Caddyfile (§5):

```ini
[FrankenPHPSettings]
# Codecs offered to clients that ask, in order of preference; or disabled.
Compression=zstd br gzip
```

- Die Codecs werden in der Reihenfolge der Bevorzugung genannt, aus `zstd`, `br` und `gzip` (alle drei sind in die
  offizielle Binärdatei eingebaut; prüfen Sie es mit `exp:velocity ctl list-modules --engine=frankenphp`, suchen Sie
  nach `http.encoders.*`). Unbekannte Namen werden verworfen, ein doppelt genannter Name zählt einmal, und `disabled`,
  `off`, `none`, `0` oder ein leerer Wert lassen `encode` weg.
- Ein Client erhält den ersten Codec der Liste, den er akzeptiert. Ein Browser sendet
  `Accept-Encoding: gzip, deflate, br, zstd`, daher erhält er mit dem Standard zstd, den günstigsten der drei beim
  Komprimieren; setzen Sie `br` an den Anfang für die kleinsten Seiten.
- Caddy überspringt kleine Bodys und Typen, die nicht schrumpfen (Bilder, Archive), und fügt `Vary: Accept-Encoding`
  hinzu, sodass Caches eine Kopie pro Kodierung behalten.

Gemessen an der Startseite der Testinstallation (`/site/`, 95.724 Bytes unkomprimiert):

| `Accept-Encoding` | Bytes über die Leitung |
|---|---:|
| `identity` (keine) | 95.724 |
| `gzip` | 12.121 |
| `zstd` | 11.862 |
| `br` | 9.982 |
| ein Browser (`gzip, deflate, br, zstd`) | 11.862 (zstd) |

In einem Benchmark über das Loopback brachte die Komprimierung bei mäßiger Last bis zu einem Drittel mehr Anfragen pro
Sekunde und wenig bei Sättigung, wo das Rendern die Grenze ist; für echte Besucher zählen die 88 % weniger Bytes. Die
Einstellung gilt ab dem nächsten `start`, `restart` oder `graceful`; bestätigen Sie sie mit
`grep encode var/vc/frankenphp/run/Caddyfile` und
`curl -s -o /dev/null -D - -H 'Accept-Encoding: gzip' https://<host>:<HTTPSPort>/ | grep -i content-encoding`.

### Antwort-Cache

Auf dieser Engine **noch keiner** — `[CacheSettings]` wird ignoriert. Brauchen Sie heute einen Antwort-Cache im Server,
ist das die Engine qbix, oder ein vorgeschalteter Reverse Proxy / das Caddy-Modul `cache-handler` (Letzteres erfordert
einen eigenen Build, §10).

### Hinweise zum Benchmarking

Verteilen Sie Anfragen reihum auf die Engines, damit Schwankungen alle gleich treffen; messen Sie eine echte
Inhaltsseite und die anonyme Startseite; beobachten Sie p99, nicht nur den Mittelwert. `curl -w` für die Latenz,
`ab`/`wrk`/`hey` für den Durchsatz. Vergleichen Sie mit qbix auf derselben Maschine, um die Wirkung des Caches zu sehen.


## 9. Fehlersuche und Fehlerbehebung

### Wo jedes Log liegt

| Log | Pfad | Format |
|---|---|---|
| Konsolenausgabe des Servers | `var/vc/frankenphp/run/console.log` (`LogFile`) | Text + JSON-Zeilen von Caddy |
| Zugriffsprotokoll | `var/vc/frankenphp/log/access.log` (`LogDir`/`AccessLog`) | **JSON** |
| Fehlerprotokoll | `var/vc/frankenphp/log/error.log` (`LogDir`/`ErrorLog`) | **JSON**, Stufe INFO |
| Fehler der Anwendung Exponential | `var/log/error.log` (und pro Siteaccess unter `var/<sa>/log/`) | eZ-Format |

`status` gibt alle Pfade der Engine aus (relativ zur Installation). `[LogSettings] Enabled=disabled` schaltet das
Zugriffs- und Fehlerprotokoll ganz aus.

### JSON von Caddy lesen

Jede Zeile des Zugriffs- und Fehlerprotokolls ist ein JSON-Objekt. Formatieren und filtern Sie mit `jq`:

```bash
tail -n 50 var/vc/frankenphp/log/error.log  | jq .
tail -n 50 var/vc/frankenphp/log/access.log | jq '{ts, status, uri: .request.uri, dur: .duration}'
tail -f  var/vc/frankenphp/log/access.log | jq 'select(.status >= 500)'
```

Fehler beim Start werden in `console.log` ausgegeben; die eigene Meldung von `start` der Engine zeigt die letzten
Fehlerzeilen, wenn ein Start fehlschlägt.

### Tabelle zur Fehlerbehebung

| Symptom | Ursache | Lösung |
|---|---|---|
| `SHA-256 mismatch … expected … got …` bei der Installation | festgelegter `Sha256[]` ist veraltet (Release neu geschnitten) oder falsches Asset/falsche Variante | gegen den Digest von GitHub prüfen (`gh api … .digest`), den Pin setzen, `install --force` erneut ausführen. §2.4 |
| `velocity.ini pins no SHA-256 for <asset>` | neue `Version`, kein Pin | `Sha256[<asset>]=<hex>` hinzufügen, oder `install --trust-github-digest`. §2.3 |
| `No such file or directory in …ezmysqlidb.php` / Datenbankverbindung schlägt fehl | eingebettetes PHP hat keinen Standard-MySQL-Socket | `mysqli.default_socket` + `pdo_mysql.default_socket` als `IniOptions[]` hinzufügen, oder `Server=127.0.0.1` verwenden. §6.1 |
| Seite „An unexpected error has occurred.“ | meist das obige Socket-Problem; oder ein fataler Fehler der Anwendung | `var/log/error.log` und `var/vc/frankenphp/log/error.log` prüfen; vorübergehend `display_errors=On` über `IniOptions[]` setzen, um ihn zu sehen |
| Port von außen nicht erreichbar | Firewall des Betriebssystems geschlossen, oder `Host` an das Loopback gebunden | `<HTTPSPort>/tcp` öffnen (§2.5); `Host` auf die öffentliche Schnittstelle setzen |
| Zertifikatswarnung im Browser | selbstsigniertes Zertifikat in Verwendung | ein echtes Zertifikat in `[HTTPSSettings]` nennen, oder TLS in einem Proxy terminieren. §4 |
| Start abgelehnt: „port … already in use“ | eine andere Engine/Installation auf diesem Port | `[FrankenPHPSettings] Port`/`HTTPSPort` ändern, oder die andere stoppen. `status --all` |
| Start abgelehnt: „HTTPS: … Certificate and Key must both name files that exist“ | Pfad des genannten Zertifikats falsch/fehlt | die Pfade korrigieren, oder beide für ein selbstsigniertes leeren. §4 |
| 502 / leer bei einem AJAX- oder REST-Pfad | fataler Fehler der Anwendung im Pfad `index_rest.php`/`index.php` | das JSON von `error.log` lesen; Datenbank/Socket und memory_limit prüfen |
| 500 auf jeder Seite | Caddyfile wird ausgeliefert, aber der Start der Anwendung scheitert (Datenbank, Berechtigungen, fehlende Erweiterung) | `error.log`; Datenbank-Socket prüfen, `var/` beschreibbar, benötigte PHP-Erweiterung vorhanden (§6.2) |
| Siteaccess wird nicht aufgelöst (falsche/leere Website) | `DocumentRoot` oder der Pfad `/site/`; Zuordnung des Siteaccess | `DocumentRoot` leer lassen (= Wurzelverzeichnis) und den Pfad-Siteaccess verwenden (`/site/`), oder in `site.ini` nach Host zuordnen; `status` führt die URLs auf |
| Berechtigung verweigert bei Log/PID/Socket | `var/vc/frankenphp/` für den ausführenden Benutzer nicht beschreibbar, oder `FileMode`/Verzeichnismodus | sicherstellen, dass `var/vc/frankenphp/` dem ausführenden Benutzer gehört; `[LogSettings] FileMode` prüfen |
| Eine Funktion geht unter Apache, ergibt hier aber 500 | ein Wert der php.ini, den das eingebettete PHP nie gesehen hat (Zeitzone, Konfiguration einer Erweiterung, Socket, open_basedir) | als `IniOptions[]` hinzufügen, oder `PHPIniFile` auf eine vollständige php.ini zeigen lassen. §6.1 |
| Fehlende PHP-Erweiterung | die Binärdatei bringt sie nicht mit | bei `gnu`: `IniOptions[]=extension=/path/ext.so`; sonst ein eigener Build. §6.2/§10 |
| `graceful` meldet „reload rejected“ | das neue Caddyfile (meist `SiteInclude`) ist ungültig | das `SiteInclude` korrigieren; die laufende Konfiguration wurde beibehalten; `ctl validate` zeigt den Fehler |
| Admin-API „did not answer“ | Socket entfernt, oder `AdminAddress` seit dem Start geändert | `restart` (graceful fällt automatisch darauf zurück) |


## 10. Erweitern und Entwicklung

### Caddy-Module hinzufügen / ein eigener Build von FrankenPHP

Die heruntergeladene Binärdatei ist das unveränderte FrankenPHP mit seinem eingebauten Satz an Modulen
(`ctl list-modules` zählt sie auf). Um Caddy-Module hinzuzufügen (z. B. den Antwort-Cache `cache-handler`, einen
Ratenbegrenzer, ein Plugin für einen ACME-Aussteller) oder eine andere PHP-Version/einen anderen Satz an Erweiterungen,
bauen Sie Ihre eigene Binärdatei mit dem statischen Builder von FrankenPHP (`static-builder-gnu.Dockerfile` /
`static-builder-musl.Dockerfile`) und verweisen Velocity darauf:

```ini
[FrankenPHPSettings]
BinaryPath=/opt/frankenphp-custom
BinarySha256=<hex>     # optional; install verifies it when set
```

Ist `BinaryPath` gesetzt, wird nichts heruntergeladen. Alles andere (Erzeugen des Caddyfile, Verben, Logs) funktioniert
genauso.

### Eigenes SiteInclude

Fügen Sie Ihre eigenen Caddy-Direktiven (Header, Weiterleitungen, zusätzliche Routen, einen TLS-Aussteller) über
`[FrankenPHPSettings] SiteInclude` hinzu — in jeden Site-Block importiert, bei der Prüfung geprüft, nie überschrieben.
§5.

### PHP-INI / Erweiterungen hinzufügen

Verwenden Sie `IniOptions[]` (pro Schlüssel) oder `PHPIniFile` (eine vollständige php.ini). §6. Denken Sie daran, dass
das eingebettete PHP bei *nichts* beginnt, daher muss alles hinzugefügt werden, was die php.ini der Maschine liefern
würde.

### Änderungen der Anwendung einbinden

Da der klassische Modus PHP pro Anfrage neu lädt und OPcache `revalidate_freq=0` verwendet, werden bearbeitete
Templates, neu erzeugte Caches und geänderte PHP-Dateien **ohne Neustart** übernommen — im selben Takt wie bei php-fpm.
Ein Neustart wird nur für Änderungen an der *Konfiguration der Engine* gebraucht (`velocity.ini` /
`[FrankenPHPSettings]`), die das Caddyfile neu erzeugen:

```bash
php bin/php/console exp:velocity graceful --engine=frankenphp --allow-root-user   # config change, no dropped conns
```

Für eine Änderung an Klassen/INI, die die Anwendung selbst cacht, folgen Sie dem üblichen Cache-Ablauf des Projekts
(`bin/php/ezpgenerateautoloads.php -e`, `bin/php/ezcache.php --clear-all`) — das betrifft die Anwendung und ist
unabhängig von der Engine. Siehe `AGENTS.md`.

### Wie die Engine die Konfiguration neu erzeugt

`start`/`graceful`/`restart` rufen `caddyfileText()` → `writeCaddyfile(true)` auf (atomares Schreiben einer temporären
Datei + `frankenphp validate` + Umbenennen). Ein Fehler bei der Prüfung lässt die vorherige Datei unversehrt.
`ctl caddyfile` gibt den Text aus, ohne zu schreiben; `ctl validate`/`ctl adapt` schreiben und führen dann die
Binärdatei aus. Es gibt keinen zu migrierenden Zustand und keinen Baum `/etc/vc` — das Caddyfile ist die gesamte
Konfiguration (die Verben `layout`/`migrate`/`site|conf|mod` gehören zu qbix und verweigern hier den Dienst).


## 11. Checkliste zur Überprüfung und Kurzreferenz

### Checkliste nach der Installation / nach einer Änderung

1. `php bin/php/console exp:velocity ctl caddyfile --engine=frankenphp --allow-root-user`
   — das Caddyfile ist das, was Sie erwarten (Ports, Host, Zertifikat, Zeilen php_ini).
2. `php bin/php/console exp:velocity ctl validate --engine=frankenphp --allow-root-user`
   — es besteht die Prüfung.
3. `php bin/php/console exp:velocity install --engine=frankenphp --check --allow-root-user`
   — die Binärdatei passt zu ihrem SHA-256.
4. `php bin/php/console exp:velocity start --engine=frankenphp --allow-root-user`
   — sie startet und beantwortet den Health-Check.
5. `curl -k -o /dev/null -w '%{http_code}\n' https://<host>:<HTTPSPort>/site/`
   — 200.
6. `echo | openssl s_client -servername <host> -connect <ip>:<HTTPSPort> | openssl x509 -noout -issuer`
   — der erwartete Aussteller.
7. Laden Sie eine echte Inhaltsseite und eine Admin-Seite; bestätigen Sie, dass die Datenbank funktioniert (kein
   „unexpected error“), CSS/JS geladen werden und ein REST-/AJAX-Pfad antwortet.
8. `php bin/php/console exp:velocity status --all --allow-root-user` — frankenphp läuft, andere Engines (qbix) sind
   unberührt.
9. Firewall: `firewall-cmd --list-ports` enthält `<HTTPSPort>/tcp`.

### Kurzreferenz der Befehle

```bash
# lifecycle
php bin/php/console exp:velocity start|stop|restart|graceful|kill|status --engine=frankenphp --allow-root-user
php bin/php/console exp:velocity status --all --allow-root-user

# install / upgrade
php bin/php/console exp:velocity install --engine=frankenphp [--force|--check|--from=<file>|--trust-github-digest] --allow-root-user

# config
php bin/php/console exp:velocity config get  FrankenPHPSettings Port --allow-root-user
php bin/php/console exp:velocity config set  ServerSettings Engine frankenphp --allow-root-user
php bin/php/console exp:velocity config paths --allow-root-user

# inspect the binary / caddyfile
php bin/php/console exp:velocity ctl caddyfile|validate|adapt|version|list-modules|build-info|environ --engine=frankenphp --allow-root-user

# stale-pin fix
gh api repos/php/frankenphp/releases/tags/v<VERSION> \
  --jq '.assets[]|select(.name=="frankenphp-linux-x86_64-gnu")|.digest'
# -> set Sha256[frankenphp-linux-x86_64-gnu]=<hex> in the override, then install --force

# find the mysql socket for IniOptions[]
php -i | grep -i "mysqli.default_socket\|pdo_mysql.default_socket"

# firewall (firewalld)
firewall-cmd --permanent --zone=public --add-port=<HTTPSPort>/tcp && firewall-cmd --reload

# logs (Caddy JSON)
tail -f var/vc/frankenphp/log/access.log | jq 'select(.status>=500)'
tail -n 50 var/vc/frankenphp/log/error.log | jq .
```

### Wichtige Dateien

- Engine-Klasse: `kernel/classes/expvelocityfrankenphp.php`
- Installationsprogramm: `kernel/classes/expvelocityfrankenphpinstaller.php`
- Einstellungen (Standardwerte): `settings/velocity.ini` — `[FrankenPHPSettings]`, `[PHPSettings]`, `[HTTPSSettings]`
- Overrides: `settings/override/velocity.ini.append.php`
- Erzeugtes Caddyfile: `var/vc/frankenphp/run/Caddyfile` (nicht bearbeiten)
- Binärdatei: `var/vc/frankenphp/bin/frankenphp-<version>-<asset>`
- Referenz über alle Engines: [`doc/eng-us/bc/6.0/velocity-engines.md`](velocity-engines.md)

## Verwandte Seiten

- [Velocity-Engines — Qbix, FrankenPHP, der eingebaute Server von PHP](velocity-engines.md)
- [Befehle zur Serversteuerung](../../../eng-us/features/6.0/web-server-and-solr-commands.md) (`exp:webserver`, `exp:frankenphp`, `exp:solr`)
- [Verhaltensänderungen vom 16. bis 30. September 2026: Engines und die Serverbefehle](../../../eng-us/bc/6.0/behaviour-changes-2026-09b.md#engines-and-the-server-commands)
- [Der Velocity-Server mit persistenten Workern](../../../eng-us/features/6.0/velocity-persistent-worker-server.md)
- [Chronik, 16. bis 30. September 2026](../../../eng-us/history/2026/2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Anleitung zum Bereitstellen](../../anleitungen/bereitstellung.md)
