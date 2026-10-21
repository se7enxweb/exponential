# Velocity-Pakete, Docker-Images und Binärdateien

Diese Seite richtet sich an Administratoren, die Velocity außerhalb einer Exponential-Installation installieren: als
Systemdienst, als Container oder als einzelne Binärdatei. Eine Exponential-Installation hat die Engine bereits über
Composer und braucht nichts davon (siehe [Aus Exponential](#aus-exponential)). Gilt für Exponential Velocity 0.0.4.28
und neuer.

## Eine Form wählen

Jedes Velocity-Release wird in den Formen veröffentlicht, in denen tatsächlich installiert wird:

| Form | Für |
|---|---|
| **Composer-Paket** `se7enxweb/exponential-velocity` | Exponential-Installationen (der übliche Weg; `exp:velocity` von Exponential findet die Engine in `vendor/`) |
| **deb- und rpm-Pakete** | Einen Systemdienst unter Debian 12 und 13, Ubuntu 22.04 und 24.04, Enterprise Linux 9 und 10, mit dem PHP der Distribution |
| **Docker-Images** unter `ghcr.io/se7enxweb/exponential-velocity` | Container, amd64 und arm64, ein Image pro PHP-Version und Variante |
| **Statische Binärdateien** | Eine Datei mit eingebautem PHP: Linux x86-64 und aarch64, macOS arm64, Windows x64 |
| **Das Phar** `sbin/qbixserver.phar` | Den Server als ein PHP-Archiv, ausgeführt von jedem PHP 8.0+ (8.1+ vor Release 0.0.4.54) |
| **Ein Quell-Kit** | Ein Build-Kit für eine Plattform, Architektur oder PHP-Version, die kein Release abdeckt |

Alle Dateien eines Release haben Prüfsummen in `SHA256SUMS`.

## Wozu

Sie wählen die Form, die zur Maschine passt, statt irgendetwas zu bauen: apt oder dnf für einen Server, Docker für eine Plattform, eine einzelne Datei für einen Laptop oder eine Appliance. Der Satz an Extensions wird einmal festgelegt (die *Extension-Grundausstattung*), sodass jede Form gegen dieselbe Liste geprüft wird, und `qbixctl ext:check` zeigt Ihnen, was einem bestimmten PHP fehlt, und den genauen Befehl, der es installiert.

## Betriebssystempakete

```bash
curl -LO https://github.com/se7enxweb/exponential-velocity/releases/download/v<release>/exponential-velocity_<release>-1+deb12_all.deb
sudo apt install ./exponential-velocity_<release>-1+deb12_all.deb          # Debian, Ubuntu
sudo dnf install ./exponential-velocity-<release>-1.el9.noarch.rpm         # EL 9, on the stock PHP 8.0
sudo dnf install ./exponential-velocity-<release>-1.el10.noarch.rpm        # EL 10
```

| Distribution | Paket | PHP |
|---|---|---|
| Debian 12 | `exponential-velocity_<release>-1+deb12_all.deb` | 8.2 |
| Debian 13 | `...+deb13_all.deb` | 8.4 |
| Ubuntu 22.04 | `...+ubuntu22.04_all.deb` | 8.1 |
| Ubuntu 24.04 | `...+ubuntu24.04_all.deb` | 8.3 |
| EL 9 | `exponential-velocity-<release>-1.el9.noarch.rpm` | 8.0, das Standard-PHP aus AppStream (das Paket hängt von `php-cli >= 8.0` ab; kein Modul-Stream nötig, ein neuerer funktioniert trotzdem) |
| EL 10 | `...el10.noarch.rpm` | 8.4 |

Die Pakete sind architekturunabhängig (der Server ist PHP). Bei der Installation wird nichts gestartet. Starten Sie den Dienst, wenn die Konfiguration fertig ist:

```bash
sudo systemctl enable --now exponential-velocity
systemctl status exponential-velocity
sudo systemctl reload exponential-velocity     # graceful: finishes requests in flight
qbixctl ext:check                              # what this PHP has and lacks
```

Der Dienst läuft als Systembenutzer `qbix` mit `/var/lib/exponential-velocity` als Zustandsverzeichnis und kann Ports unter 1024 belegen. Seine Einstellungen stehen in `/etc/default/exponential-velocity`:

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `QBIX_ROOT` | `/usr/share/exponential-velocity/web` | Document Root (eine Willkommensseite, bis Sie es auf Ihre Anwendung richten) |
| `QBIX_SITE` | `/etc/qbix/sites-enabled/default.conf` | Die Site-Datei |
| `QBIX_OPTS` | leer | Beliebige weitere `qbixserver`-Optionen, zum Beispiel `--workers=16 --https-port=8443` |

Der Server lauscht auf 8080, bis `/etc/qbix/ports.conf` etwas anderes festlegt.

### PHP 8.0 und Red Hat Enterprise Linux

Ab Release 0.0.4.54 (20. Oktober 2026) läuft die Engine auf PHP 8.0 bis 8.5 (`"php": ">=8.0"`, vorher `>=8.1`), auf
8.0 ohne jede Zugabe: kein Polyfill-Paket, keine Extension.

- **RHEL 9 und seine Nachbauten**: Das el9-rpm hängt von `php-cli >= 8.0` ab und installiert sich auf dem
  Standard-PHP 8.0 aus AppStream, ohne einen Modul-Stream zu aktivieren; ein neuerer Stream funktioniert trotzdem.
  Composer funktioniert ebenfalls auf 8.0.
- **RHEL 8**: Das Standard-PHP ist 7.2. Aktivieren Sie den 8.0-Stream (`sudo dnf module enable php:8.0`) und
  verwenden Sie das Phar oder Composer; es gibt kein el8-rpm.
- **Was auf 8.0 anders ist**: Die Bildausgabe in AVIF benötigt PHP 8.1 (die Aushandlung endet bei WebP); der
  Speicherhöchstwert pro Anfrage im Dashboard benötigt PHP 8.2 (die Spalte ist leer); `$_FILES` hat kein `full_path`,
  wie in PHP 8.0 selbst; eine mit `eval()` deklarierte Klasse mit statischen Eigenschaften führt dazu, dass ihr Worker
  nach dieser Anfrage ersetzt wird (korrekt, kostet einen Fork). Die Zygote läuft auf 8.0.

Extensions, die systemd-Unit, SELinux und die Überprüfung der Installation beschreibt die Seite
[PHP 8.0 und Red Hat Enterprise Linux](https://github.com/se7enxweb/exponential-velocity/blob/main/docs/rhel.md) der
Engine.

### Was wohin kommt

```
/usr/share/exponential-velocity/        the server: phar, console tools, baseline, designs, docs
/usr/sbin/qbixserver, qbixctl, qbixconsole      the programs (0.0.4.41 and later)
/usr/bin/qbixserver, qbixctl, qbixconsole       links to the three above (their former paths)
/usr/bin/vc-qshell                      the shell (0.0.4.42 and later)
/etc/qbix/                              the configuration tree: qbix.conf, ports.conf, envvars, *-available, *-enabled
/etc/default/exponential-velocity       the service's settings
/var/lib/exponential-velocity/          state, owned by qbix
```

Alles unter `/etc` ist Konfiguration: Ein Upgrade überschreibt nie eine Datei, die Sie geändert haben. Aktivieren und deaktivieren Sie Sites, Snippets und Module mit `qbixctl ensite`, `dissite`, `enconf`, `disconf`, `enmod`, `dismod` ([Verzeichnisstruktur](../../bc/6.0/velocity-verzeichnisstruktur.md)).

### Umstieg vom Paket `qbix-webserver`

Installieren Sie `exponential-velocity` darüber. Das alte Paket wird entfernt (das neue ersetzt und stellt es bereit), und einmalig gilt: `/etc/default/qbix-webserver` wird zu `/etc/default/exponential-velocity` mit umgeschriebenen Pfaden; der Zustand in `/var/lib/qbix-webserver` wird kopiert; ein aktivierter oder laufender alter Dienst wird gestoppt und an seiner Stelle der neue aktiviert oder gestartet; `/usr/share/qbix-webserver` wird zu einem Link. Hat apt den alten Dienst entfernt, bevor das neue Paket entpackt wurde, führen Sie `sudo systemctl enable --now exponential-velocity` aus. Der Dienstbenutzer bleibt `qbix`, und `/etc/qbix` bleibt unverändert.

## Docker

```
ghcr.io/se7enxweb/exponential-velocity:<release>-php<version>-<variant>   fixed
ghcr.io/se7enxweb/exponential-velocity:php<version>-<variant>             moves with each release
ghcr.io/se7enxweb/exponential-velocity:latest                             newest PHP, standard variant
```

`<version>` ist 8.2, 8.3, 8.4 oder 8.5; `<variant>` ist `mini`, `lite`, `standard` oder `full`. Jeder Tag ist für mehrere Architekturen gebaut. Verwenden Sie in der Produktion einen festen Tag.

```bash
docker run -d --name web -p 8080:8080 \
  -v "$PWD/public:/app" \
  ghcr.io/se7enxweb/exponential-velocity:php8.3-standard
```

Das Document Root ist `/app`; jede Server-Option lässt sich übergeben, indem Sie den Befehl ersetzen (`qbixserver --root=/srv/site/public --port=8080 --workers=16`). Die Varianten standard und full werden außerdem mit Oracle-, Firebird- und ODBC-Treibern gebaut. Das alte Image `ghcr.io/se7enxweb/qbix-webserver` ist veraltet: Es behält seine Tags, erhält aber keine neuen; ändern Sie den Namen und behalten Sie den Tag.

## Varianten

| Variante | Enthält | Größe (Linux x86-64, PHP 8.3) | Für |
|---|---|---|---|
| `mini` | Nur, was der Server braucht: Prozessisolation, Sockets, TLS, Sessions, seinen eigenen Metrikspeicher | 16 MB | Statische Websites, kleine Skripte |
| `lite` | mini plus, was eine typische Anwendungsplattform verlangt: XML, Bilder (gd), MySQL/MariaDB, SQLite, cURL, intl, zip, opcache | 68 MB | Die meisten Anwendungen |
| `standard` | lite plus die empfohlene Stufe: PostgreSQL, MongoDB, Caches (apcu, redis, memcached, igbinary), LDAP, SOAP, bcmath und mehr | 73 MB | Der Standard; davon geht die Dokumentation aus |
| `full` | Alles, was static-php-cli auf dieser Plattform bauen kann, nach bestem Bemühen | etwa 90 bis 120 MB | Eine Extension außerhalb von standard |
| `source` | Ein Build-Kit: das Phar, die Grundausstattung, das Konsolenwerkzeug und ein Rezept für jede Plattform, PHP-Version und Variante | entfällt | Eine Plattform, die kein Release abdeckt |

Ein `full`-Build lässt einige Extensions weg, wo sie sich nicht bauen lassen (zum Beispiel `rar` und `gmssl` überall); `php sbin/qbixctl.php ext:plan --variant=full --platform=windows-x64` listet auf, welche und warum.

### Dateinamen der Binärdateien

```
qbixserver-<platform>-php<version>-<variant>[.exe]     the server, one file
php-<platform>-php<version>-<variant>[.exe]            the same PHP as a plain interpreter
qbixserver-windows-x64-php<version>-<variant>-gui.exe  Windows, no console window
qbixserver-source-kit-<release>.tar.gz                 the source kit
```

Plattformen: `linux-x86_64`, `linux-aarch64`, `macos-arm64`, `windows-x64`.

```bash
curl -LO https://github.com/se7enxweb/exponential-velocity/releases/latest/download/qbixserver-linux-x86_64-php8.3-standard
curl -LO https://github.com/se7enxweb/exponential-velocity/releases/latest/download/SHA256SUMS
sha256sum --ignore-missing -c SHA256SUMS
chmod +x qbixserver-linux-x86_64-php8.3-standard && ./qbixserver-linux-x86_64-php8.3-standard --version
```

Prüfen Sie, was das PHP einer beliebigen Binärdatei enthält: `QBIX_STATIC_BUILD=1 ./php-linux-x86_64-php8.3-standard sbin/qbixctl.php ext:check --variant=standard`.

## Eigene Binärdateien und Signieren

`--pack=DIR --output=FILE` bündelt eine Anwendung in eine Binärdatei (fügen Sie unter Windows `--gui` hinzu). Eine Binärdatei kann von mehreren Personen signiert und gegen einen Schwellenwert geprüft werden: `--sign-binary --key=alice.pem --signer=Alice`, dann `--verify-binary --m=2`; Signaturen können in Sigstore Rekor veröffentlicht werden. `--verify-binary` meldet eine unsignierte Datei als solche (statt PHP-Warnungen auszugeben), und `--sign-binary` beendet sich mit einem Wert ungleich null, wenn es nicht signieren kann (behoben in 0.0.4.27).

## Plattformen, die wir beobachten, aber nicht ausliefern

Eine Plattformmatrix führt das Phar auf Systemen aus, für die es keine Binärdatei gibt: musl, DragonFly, FreeBSD, NetBSD, OpenBSD, illumos (auf Anforderung), RISC-V und ARMv5. Die Plattformdokumentation nennt drei ehrliche Stufen (erprobt, erwartet, derzeit nicht). Windows-Binärdateien haben kein `pcntl`/`posix` und kein `intl`.

## Aus Exponential

`exp:velocity` von Exponential verwendet die Composer-Kopie der Engine; das Paket wird mit dem Rest der Installation installiert; Auswahl und Betrieb beschreibt [Velocity-Engines](../../bc/6.0/velocity-engines.md). Das Engine-Archiv und das eigene Archiv von Exponential sind verschiedene Dinge: siehe [Phar](../../bc/6.0/phar.md).

## Grenzen

- Die Pakete benötigen PHP 8.0 oder neuer (8.1 vor Release 0.0.4.54); EL 9 installiert auf seinem mitgelieferten PHP 8.0, für EL 8 gibt es kein Paket (verwenden Sie das Phar oder Composer nach `dnf module enable php:8.0`).
- Einige empfohlene Extensions (`mongodb`, `redis`, `memcached`) stammen unter EL aus EPEL oder Remi; `qbixctl ext:check` nennt sie.
- Release-Dateien gibt es nur für Tags, die einen Abschnitt im Changelog der Engine haben; drei frühe Tags (`v0.0.4.21`, `v0.0.4.22`, `v0.0.4.26`) haben kein Release.

## Verwandte Seiten

- [uwebserver](velocity-uwebserver.md), [HTTPS und Zertifikate](velocity-https-zertifikate.md), [Q-Shell](velocity-q-shell.md) (`vc-qshell`), [Velocity-Webserver](velocity-webserver.md)
- Spezifikation: [Engine-Einstellungen](../../spezifikationen/6.0/velocity-engine-einstellungen.md) (Programme und die Struktur von `sbin/` und `bin/`)
- Aktualisierung: [Hinweise zur Aktualisierung der Velocity-Engine](../../bc/6.0/velocity-engine-upgrade-hinweise.md) (Umbenennungen von Paketen und Pfaden), [Verzeichnisstruktur von Velocity](../../bc/6.0/velocity-verzeichnisstruktur.md), [Velocity-Engines](../../bc/6.0/velocity-engines.md)
- [Changelog: Exponential-Velocity-Engine](../../../changelogs/extensions/exponential-velocity.md)
- Chronik: [August 2026](../../geschichte/velocity/2026-08.md) (erste Binärdateien), [23. September](../../geschichte/velocity/2026-09c.md) (Plattformmatrix), [24. September](../../geschichte/velocity/2026-09d.md) (Pakete), [25. bis 30. September](../../geschichte/velocity/2026-09e.md) (Umbenennung, `sbin/`)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
