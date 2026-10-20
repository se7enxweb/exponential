---
title: Unterstützte Betriebssysteme
audience: admin, developer
category: install-upgrade
order: 11
summary: Exponential 6.0.x läuft auf fast jedem Betriebssystem mit PHP 8.0 oder neuer und einem Webserver. Diese Seite ordnet sie ehrlich in Stufen ein (hier getestet, bekanntermaßen lauffähig, voraussichtlich lauffähig, heute nicht möglich), von Linux, den BSDs, macOS und Windows bis zu IBM i, Haiku und dem Commodore 64, mit den Webservern und PHP-SAPIs, die es ausliefern.
keywords: Betriebssystem, Plattform, Systemvoraussetzungen, Anforderungen, Container, Prozessorarchitektur, Webserver, operating system, OS, platform, Linux, RHEL, AlmaLinux, Rocky Linux, CentOS Stream, Fedora, Debian, Ubuntu, SUSE, openSUSE, Arch, Alpine, Gentoo, Slackware, NixOS, Amazon Linux, Plesk, cPanel, FreeBSD, OpenBSD, NetBSD, DragonFly, macOS, Windows, IIS, WSL2, illumos, Solaris, OpenIndiana, AIX, HP-UX, IBM i, Docker, Podman, Kubernetes, ARM, Raspberry Pi, Graviton, RISC-V, Android, Termux, Haiku, Apache, nginx, Caddy, LiteSpeed, Velocity, FrankenPHP, PHP-FPM, SAPI, requirements
featured: true
context: setup/info
type: help
---
# Unterstützte Betriebssysteme

Diese Seite richtet sich an Administratoren, die einen Rechner für Exponential auswählen, und an Entwickler, die wissen
möchten, ob ihr eigener Computer es ausführen kann. Die kurze Antwort: **Exponential 6.0.x läuft auf fast jedem
Betriebssystem, das PHP 8.0 oder neuer und einen Webserver hat.** Es besteht aus einfachen PHP-Dateien und einer
Datenbank; es hat keinen eigenen kompilierten Code, keine eigene Architektur und hängt von keiner bestimmten
C-Bibliothek ab.

Die lange Antwort ist der Rest dieser Seite, ehrlich in Stufen geordnet, denn „läuft überall, wo PHP läuft“ nützt nur,
wenn Sie auch wissen, was nachweislich funktioniert hat, was funktionieren sollte und was heute nicht funktionieren
kann, und warum.

## Inhalt

1. [Was das Betriebssystem bereitstellen muss](#was-das-betriebssystem-bereitstellen-muss)
2. [Die vier Stufen](#die-vier-stufen)
3. [Stufe 1: hier getestet](#stufe-1-hier-getestet)
4. [Stufe 2: bekanntermaßen lauffähig](#stufe-2-bekanntermaßen-lauffähig)
5. [Stufe 3: voraussichtlich lauffähig](#stufe-3-voraussichtlich-lauffähig)
6. [Stufe 4: heute nicht möglich (theoretisch)](#stufe-4-heute-nicht-möglich-theoretisch)
7. [Container, Cloud und Hosting-Panels](#container-cloud-und-hosting-panels)
8. [Prozessorarchitekturen](#prozessorarchitekturen)
9. [Webserver](#webserver)
10. [PHP-SAPIs](#php-sapis)
11. [Plattformspezifische Hinweise](#plattformspezifische-hinweise)
12. [Einen Rechner in zwei Minuten prüfen](#einen-rechner-in-zwei-minuten-prüfen)
13. [Verwandte Seiten](#verwandte-seiten)

## Was das Betriebssystem bereitstellen muss

Alles Folgende stammt aus `composer.json`, `settings/setup.ini` und [Kapitel 2 der
Installationsanleitung](../installation/02-voraussetzungen.md); ist ein Punkt eine Empfehlung statt einer Voraussetzung, steht
das dabei.

| Bedarf | Minimum | Warum |
|---|---|---|
| PHP | **8.0** (`composer.json`: `"php": "^8.0 || ^8.1 || ... || ^8.8"`; der Einrichtungsassistent: `[phpversion] MinimumVersion=8.0.0`) | der Kernel; 8.0 ist das Standard-PHP von RHEL 9 und seinen Nachbauten |
| PHP für Velocity | **8.0**, mit `sockets` (`pcntl` und `openssl` empfohlen); 8.1 vor Velocity 0.0.4.54 | Velocity, der empfohlene Webserver, ist ein PHP-Programm |
| PHP-Erweiterungen | `dom`, `libxml`, `simplexml`, `xsl`, `mbstring`, `iconv`, `intl`, `pcre`, `json`, `session`, `zlib`, eine Datenbankerweiterung, `gd` oder ImageMagick | die kritischen Tests des Einrichtungsassistenten und `composer.json` |
| Eine Datenbank | SQLite 3 braucht nur die Erweiterung `sqlite3`; die anderen brauchen ihren Server irgendwo im Netzwerk | siehe [Unterstützte Datenbanken](datenbankunterstuetzung.md) |
| Ein Webserver | keiner, wenn Velocity oder der eingebaute Server von PHP die Site ausliefert; sonst alles, was eine URL an `index.php` weiterreichen kann | siehe [Webserver](#webserver) |
| 64-Bit-PHP | empfohlen | die Sprachen eines Objekts sind Bits einer einzigen Ganzzahl; `composer.json` empfiehlt `php-64bit` „for support of more than 30 languages“ |
| Composer 2 | zur Installation | lädt die Zeta Components und die Erweiterungen herunter |
| Beschreibbare Verzeichnisse | `var/`, `settings/override`, `settings/siteaccess` und die anderen, die der Assistent prüft | Caches, Uploads, Protokolle, die SQLite-Datenbank |

Nichts in dieser Liste nennt ein Betriebssystem. Deshalb ist die folgende Liste der Systeme lang.

## Die vier Stufen

| Stufe | Bedeutung |
|---|---|
| **1. Hier getestet** | Das Projekt betreibt Exponential täglich darauf, oder seine CI führt die Testsuiten bei jedem Push darauf aus. |
| **2. Bekanntermaßen lauffähig** | Die Dokumentation des Projekts, seine Pakete oder die eigene CI der Velocity-Engine decken es ab, oder es ist dasselbe PHP auf demselben Kernel wie ein System der Stufe 1. Das Projekt betreibt Exponential nicht täglich darauf. |
| **3. Voraussichtlich lauffähig** | Es gibt ein gepflegtes PHP 8.x dafür, und nichts Bekanntes steht im Weg, aber niemand hier hat Exponential darauf laufen sehen. **Das ist eine begründete Erwartung, kein Testergebnis.** |
| **4. Heute nicht möglich** | Es gibt kein PHP 8 dafür, oder die Hardware kann PHP 8 überhaupt nicht ausführen. Aufgeführt, damit die Frage eine klare Antwort bekommt. |

Wenn Sie Exponential auf einem System der Stufe 3 betreiben, teilen Sie es dem Projekt bitte im
[Community-Tracker oder im Software-Tracker](https://github.com/se7enxweb/exponential/issues) mit, dann rückt die Zeile
eine Stufe auf.

## Stufe 1: hier getestet

| System | Was läuft | Nachweis |
|---|---|---|
| **AlmaLinux 9** (x86_64, RHEL-9-Familie) | Der Entwicklungsserver des Projekts: Apache 2.4 mit PHP-FPM 8.5 aus dem Plesk-Panel und Exponential Velocity auf derselben Installation; Sites mit SQLite, MariaDB, MongoDB und Oracle (in einem Container) laufen dort, und ein PostgreSQL-Server steht zur Verfügung | läuft täglich |
| **Ubuntu (GitHub Actions `ubuntu-latest`)** | PHPUnit auf PHP 8.1, 8.2, 8.3, 8.4 und 8.5; die Datenbanktests gegen MySQL 8.0 und PostgreSQL 15; Syntax- und Start-Rauchtest auf PHP 8.0; PHPStan; der Performance-Benchmark auf PHP 8.5 | `.github/workflows/phpunit.yml`, `quality.yml`, `performance.yml`, bei jedem Push |

## Stufe 2: bekanntermaßen lauffähig

### Linux-Distributionen

| Distribution | PHP 8 aus | Hinweise |
|---|---|---|
| **Red Hat Enterprise Linux 9 und 10**, **Rocky Linux**, **AlmaLinux**, **Oracle Linux**, **CentOS Stream 9 und 10** | AppStream (PHP 8.0 als Standard in EL 9; Modul-Streams `php:8.1` bis `php:8.3`), Remi (8.0 bis 8.5) | die dokumentierte Referenzplattform ([Kapitel 2](../installation/02-voraussetzungen.md#29-hinweise-zu-betriebssystemen)); Velocity liefert `.rpm`-Pakete für EL 9 und EL 10; das Paket für EL 9 lässt sich auf dem Standard-PHP 8.0 installieren |
| **Debian 12 und 13** | die Distribution (8.2, 8.4) oder das Repository von Ondřej Surý | Velocity liefert `.deb`-Pakete für beide |
| **Ubuntu 22.04 und 24.04** | die Distribution (8.1, 8.3) oder das PPA `ondrej/php` | Velocity liefert `.deb`-Pakete für beide; die CI läuft auf Ubuntu |
| **Alpine Linux** (musl) | `apk` (`php83-*` und andere) | Velocitys CI weist den Server auf Alpine x86_64, aarch64 und i386 nach; Velocitys Erweiterungsmanifest kennt seine Pakete |
| **Plesk**-Hosts (auf einer der obigen) | Plesks eigene PHP-Builds unter `/opt/plesk/php/<version>/` | der Entwicklungsserver des Projekts; PHP-FPM-Pools pro Domain, Dienst `plesk-php<XY>-fpm` |

### Andere Systeme

| System | Hinweise |
|---|---|
| **FreeBSD 14**, **DragonFly BSD** | Velocitys CI startet und liefert auf echten BSD-Kerneln in einer VM aus; PHP 8 aus `pkg` (`php83-*`). Exponential selbst betreibt das Projekt dort nicht. |
| **macOS** auf Apple Silicon (arm64) und Intel | PHP 8 aus Homebrew; Velocity veröffentlicht ein `macos-arm64`-Binary und forkt Worker mit `pcntl` wie unter Linux |
| **Windows** (x64) | Offizielle Builds von PHP für Windows; Apache oder IIS mit FastCGI. Der Kernel kennt Windows-Pfade (ImageMagicks `convert.exe`, Servervariablen von IIS und nginx unter Windows in seinen Tests). Was fehlt, steht unter [Windows](#windows). |
| **Docker und Podman** (Linux-Container) | Velocity veröffentlicht Images unter `ghcr.io/se7enxweb/exponential-velocity` für amd64 und arm64, eines pro PHP-Version und Variante |

## Stufe 3: voraussichtlich lauffähig

Für diese Systeme gibt es ein gepflegtes PHP 8, und nichts Bekanntes steht im Weg. Auf keinem wurde Exponential bisher
laufen gesehen.

| System | Warum es funktionieren sollte | Worauf zu achten ist |
|---|---|---|
| **Fedora** | aktuelles PHP in der Distribution; der Upstream der RHEL-Familie | nichts bekannt |
| **SUSE Linux Enterprise**, **openSUSE** Leap und Tumbleweed | PHP-8-Pakete (`php8-*`) | Paketnamen weichen von RHEL und Debian ab |
| **Arch Linux**, **Manjaro** | immer das neueste PHP | das neueste PHP kann neuer sein als das neueste getestete (8.5) |
| **Gentoo** | PHP-8-Ebuilds mit USE-Flags | `intl`, `xsl` und die Datenbankerweiterung mit einbauen |
| **Slackware** | PHP 8 in aktuellen Releases | nichts bekannt |
| **NixOS** | PHP 8 in nixpkgs, Erweiterungen in der Konfiguration deklariert | das Installationsverzeichnis muss beschreibbar bleiben, was der Nix-Store nicht ist: die Site außerhalb davon halten |
| **Amazon Linux 2023** | PHP-8-Pakete | nichts bekannt |
| **cPanel**- und **DirectAdmin**-Hosts | EasyApache (EA-PHP 8.x), PHP pro Konto | Shared Hosting kann lang laufende Prozesse verbieten, sodass Velocity womöglich nicht erlaubt ist; Apache oder LiteSpeed mit `.htaccess` verwenden |
| **OpenBSD**, **NetBSD** | PHP 8 in Ports und pkgsrc | in Velocitys Plattformmatrix, dort noch in Arbeit |
| **illumos**: OmniOS, OpenIndiana, SmartOS | PHP-8-Pakete | Velocitys CI startet seinen Server auf OmniOS, das Ausliefern einer Anfrage scheitert dort aber noch (ein offenes Issue in der Engine); Apache oder nginx mit PHP-FPM ist der Weg, den man versuchen sollte |
| **Oracle Solaris 11** | es gibt PHP-8-Pakete | nirgends im Projekt ein Runner |
| **IBM AIX** auf Power | IBM liefert PHP 8 in der AIX Toolbox | kein Runner, kein Emulator |
| **IBM i** (PASE) | für IBM i wird ein gepflegtes PHP 8 veröffentlicht | kein Runner; Datenbank über die üblichen Erweiterungen über das Netzwerk |
| **Windows Subsystem for Linux 2** | ein echter Linux-Kernel; jede der obigen Distributionen | die Site im Linux-Dateisystem halten, nicht unter `/mnt/c`, wegen der Geschwindigkeit und der Sperren von SQLite |
| **Kubernetes** | es führt die obigen Container aus | siehe [Container](#container-cloud-und-hosting-panels): ein schreibender Prozess für SQLite |
| **Haiku** | PHP ist in HaikuPorts | Velocitys CI kann es nicht unbeaufsichtigt installieren; der eingebaute Server von PHP oder Apache sind die Wege, die man versuchen sollte |
| **Android** über **Termux** | Termux paketiert ein aktuelles PHP 8 | ein Telefon ist ein Entwicklungsspielzeug, kein Server: Speicher, Hintergrundbeschränkungen, keine feste Adresse |

**Systeme, die das Projekt keiner Stufe zuordnen konnte.** **HP-UX** auf Itanium: Uns ist kein aktueller PHP-8-Build
dafür bekannt; behandeln Sie es als Stufe 4, sofern Ihr Anbieter keinen hat. **OS/2 und ArcaOS**: Frühere
PHP-Versionen wurden portiert; ob es eine Portierung von PHP 8 gibt, ist hier nicht überprüft.

## Stufe 4: heute nicht möglich (theoretisch)

Klar gesagt, weil diese Fragen aufkommen und eine klare Antwort verdienen. In jedem Fall ist das Hindernis dasselbe:
**Es gibt kein PHP 8 für das System**, und Exponential ist PHP. An dem Tag, an dem jemand PHP 8 auf eines davon
portiert, laufen die Dateien unverändert.

| System | Warum nicht |
|---|---|
| **Commodore 64**, **Apple II**, andere 8-Bit-Rechner | ein Prozessor der 6502-Klasse mit 64 KB Speicher. PHP braucht eine 32- oder 64-Bit-CPU mit Speicherverwaltung und Dutzende Megabyte; nichts an diesen Rechnern passt, hier ist es also eine Grenze der Hardware, keine fehlende Arbeit. |
| **AmigaOS 4**, **MorphOS**, **AROS** | echte Multitasking-Systeme mit Gigabytes an Speicher auf moderner Hardware, aber ihre PHP-Portierungen enden bei 5.x. Die Lücke ist Portierungsarbeit (C-Bibliothek, Toolchain, Sockets), nicht Hardware. Die README der Velocity-Engine beschreibt, was diese Portierung erfordern würde. |
| **Klassischer Amiga** (68000, AmigaOS 3.x und älter) | keine Portierung von PHP 7 oder 8; den 68000-Rechnern fehlt außerdem die Speicherverwaltung, die PHP 8 erwartet |
| **MS-DOS**, **FreeDOS** | ein Singletasking-16-Bit-System; keine Portierung von PHP 8, kein Netzwerkstack, den PHP so wie er ist nutzen könnte, kein Prozessmodell für einen Webserver |
| **Klassisches Mac OS** (System 7 bis Mac OS 9) | keine PHP-Portierung einer aktuellen Version; kooperatives Multitasking; keine POSIX-Schicht |
| **BeOS** | seit 2001 eingestellt; sein Nachfolger Haiku steht in Stufe 3 |
| **Plan 9**, **9front** | uns ist keine PHP-Portierung bekannt; PHP setzt eine POSIX-C-Bibliothek voraus, die Plan 9 nicht bietet |
| **RISC OS** | uns ist keine Portierung von PHP 8 bekannt |
| **IBM z/OS** (UNIX System Services) | uns ist kein aktuelles PHP 8 für USS bekannt. Linux auf IBM Z (s390x) ist etwas anderes: Velocitys CI weist seinen Server dort nach, Linux auf demselben Mainframe ist also der Weg. |
| **Windows XP**, **Windows 7** und andere Windows-Versionen, die Microsoft nicht mehr unterstützt | aktuelle PHP-8-Builds zielen auf unterstützte Windows-Versionen; ob ein bestimmter PHP-8-Build auf einer älteren Version noch startet, ist hier nicht überprüft, und ein solcher Rechner erhält ohnehin keine Sicherheitsupdates |

Seiten wie diese sind bei Stufe 4 ehrlich, damit niemand ein Wochenende mit einem Rechner verbringt, der nicht
funktionieren kann, und damit alle, die einen davon zum Laufen bringen wollen, wissen, dass die Arbeit eine Portierung
von PHP ist und keine von Exponential.

## Container, Cloud und Hosting-Panels

- **Docker, Podman, Kubernetes.** Jedes Image mit PHP 8 und den obigen Erweiterungen führt Exponential aus; Velocitys
  Images (`ghcr.io/se7enxweb/exponential-velocity`, amd64 und arm64) enthalten den Server. Halten Sie `var/` (Speicher,
  Protokolle, die SQLite-Datenbank) und `settings/override` auf einem persistenten Volume. Betreiben Sie mit SQLite
  **einen** schreibenden Container: Das WAL von SQLite braucht gemeinsamen Speicher auf einem Host, daher können
  mehrere Replikate keine Datenbankdatei über ein Netzwerk-Volume teilen. Mehrere Replikate brauchen einen
  Datenbankserver (zuerst PostgreSQL; siehe [Unterstützte Datenbanken](datenbankunterstuetzung.md)) und gemeinsamen Speicher
  für `var/storage`.
- **Virtuelle Maschinen in der Cloud** (Amazon EC2 einschließlich Graviton, Google Compute Engine, Azure, Hetzner,
  DigitalOcean und die übrigen) sind gewöhnliche Linux-Rechner: Stufe 2 oder 3 je nach ihrer Distribution.
- **Platform-as-a-Service**-Angebote, die PHP 8 mit einem beschreibbaren persistenten Datenträger betreiben, sind
  voraussichtlich lauffähig (Stufe 3). Angebote mit schreibgeschütztem oder flüchtigem Dateisystem erfordern, dass
  `var/` auf persistenten Speicher verlegt wird, und einen Datenbankserver.
- **Hosting-Panels.** Plesk ist Stufe 1 (der Entwicklungsserver). cPanel und DirectAdmin sind Stufe 3; auf Shared
  Hosting ohne Shell-Zugang ist der Einrichtungsassistent im Browser der Installer.

## Prozessorarchitekturen

Exponential hat keinen Maschinencode, daher spielt die Architektur nur über PHP und die Datenbank eine Rolle:

| Architektur | Status |
|---|---|
| **x86_64** (amd64) | Stufe 1 |
| **aarch64** (ARM64): Apple Silicon, AWS Graviton, Ampere, Raspberry Pi 4 und 5 | Stufe 2: Velocity veröffentlicht aarch64-Binaries und -Images und weist sie in der CI nach; PHP und jede Datenbank hier gibt es dafür |
| **armv7**, **armv5** (Raspberry Pi 2 und 3, ältere Boards) | Stufe 3: Velocitys CI weist seinen Server dort nach; 32-Bit-PHP beschränkt eine Site auf 30 Sprachen |
| **i386** (32-Bit-x86) | Stufe 3, mit derselben Grenze von 30 Sprachen |
| **ppc64le** (IBM Power), **s390x** (IBM Z, Big-Endian) | Stufe 3: Velocitys CI weist seinen Server auf beiden nach, und seine vollständige Suite besteht auf s390x, das Big-Endian ist und wo Fehler bei der Byte-Reihenfolge auffallen würden |
| **riscv64** | Stufe 3: Velocitys CI besteht unter Emulation mit abgeschaltetem JIT von PCRE (`pcre.jit=0`), weil der Emulator, nicht RISC-V, mit eingeschaltetem JIT abstürzt; echte Hardware ist ungetestet |

## Webserver

Jeder Webserver, der eine Anfrage an PHP weitergeben und eine URL auf `index.php` umschreiben kann, liefert Exponential
aus. Die Einrichtung der einzelnen Server ist Thema von [Kapitel 8, Die Site ausliefern](../installation/08-die-website-ausliefern.md).

| Server | Status | Hinweise |
|---|---|---|
| **Exponential Velocity** (die `qbix`-Engine) | für jede Phase empfohlen; Stufe 1 | ein PHP-Programm, das HTTP und HTTPS selbst ausliefert, mit einem Pool persistenter Worker; PHP 8.0+ (ab dem Release 0.0.4.54); kein anderer Webserver nötig |
| **Apache httpd 2.4** mit PHP-FPM (oder mod_php) | Stufe 1 | die klassische Einrichtung und Shared Hosting; Rewrite-Regeln in `.htaccess_root` |
| **FrankenPHP** (über `exp:velocity`, `Engine=frankenphp`) | produktionsreife Alternative | Caddy mit eingebautem PHP, ein einziges Binary |
| **nginx** mit PHP-FPM | Stufe 2 | Regeln im Server-Block, siehe [8.6](../installation/08-die-website-ausliefern.md#86-nginx-mit-php-fpm) |
| **LiteSpeed**, **OpenLiteSpeed** | Stufe 3 | liest in den meisten Fällen die Rewrite-Regeln von Apache |
| **Caddy** mit PHP-FPM | Stufe 3 | `php_fastcgi` und eine Umschreibung auf `index.php` |
| **Microsoft IIS** mit FastCGI | Stufe 3 | das URL-Rewrite-Modul (`web.config`); die Anfragebehandlung des Kernels hat Tests für die Servervariablen von IIS 7 |
| **lighttpd** | Stufe 3 | `mod_rewrite` |
| **Der eingebaute Server von PHP** (`exp:velocity start --engine=php`) | nur für die Entwicklung | ein Prozess, kein HTTPS |

## PHP-SAPIs

| SAPI | Verwendung |
|---|---|
| **FPM** (`fpm-fcgi`) | der übliche Weg hinter Apache, nginx, Caddy und den anderen; ein Pool pro Site |
| **CLI** | Composer, die Installer, die Konsole (`bin/php/console`), Cronjobs und Velocity selbst, dessen Worker CLI-Prozesse sind |
| **Apache-Modul** (`apache2handler`, mod_php) | funktioniert; bindet PHP an das Prefork-Modell von Apache, daher wird PHP-FPM bevorzugt |
| **CGI / FastCGI** (`cgi-fcgi`) | IIS unter Windows; Velocitys Worker unter Windows sind `php-cgi`-Prozesse |
| **LiteSpeed** (`litespeed`, LSAPI) | die eigene SAPI von LiteSpeed |
| **FrankenPHP** | seine eigene eingebettete SAPI |
| **Eingebauter Webserver** (`cli-server`) | nur für die Entwicklung |

Das Web-PHP und das PHP der Kommandozeile werden getrennt konfiguriert und sind oft unterschiedliche Builds; geben Sie
beiden dieselbe Version und dieselben Erweiterungen ([Kapitel 2](../installation/02-voraussetzungen.md#php-auf-der-kommandozeile-und-im-web)).

## Plattformspezifische Hinweise

### Linux

Die Referenzplattform. Die Paketnamen je Familie stehen in
[Kapitel 2](../installation/02-voraussetzungen.md#29-hinweise-zu-betriebssystemen), und
`php bin/php/console exp:velocity ext install-hint <extension>` gibt den Installationsbefehl für das System aus, auf dem
es läuft.

### PHP 8.0 und Red Hat Enterprise Linux

Exponential Velocity läuft ab seinem Release 0.0.4.54 (20. Oktober 2026) unter PHP 8.0 bis 8.5, ohne Zusätze auf 8.0.
Auf RHEL 9 und seinen Nachbauten funktioniert das Standard-PHP 8.0 aus AppStream, mit dem el9-RPM (es hängt von
`php-cli >= 8.0` ab, kein Modul-Stream ist zu aktivieren) oder mit Composer. Auf RHEL 8, dessen Standard-PHP 7.2 ist,
aktivieren Sie den Stream 8.0 (`dnf module enable php:8.0`) und verwenden das Phar oder Composer; ein el8-RPM gibt es
nicht. Unter PHP 8.0 stehen die Bildausgabe als AVIF (braucht 8.1) und die Spitze des Speicherverbrauchs je Anfrage im
Dashboard (braucht 8.2) nicht zur Verfügung, `$_FILES` hat kein `full_path`, und eine mit `eval()` deklarierte Klasse
mit statischen Eigenschaften führt dazu, dass ihr Worker nach dieser Anfrage ersetzt wird. Einzelheiten:
[Kapitel 2](../installation/02-voraussetzungen.md#php-80-und-red-hat-enterprise-linux) und die Seite
[PHP 8.0 and Red Hat Enterprise Linux](https://github.com/se7enxweb/exponential-velocity/blob/main/docs/rhel.md) der
Engine (Erweiterungen, systemd, SELinux, Überprüfung).

### BSD

PHP 8 und jede Datenbankerweiterung sind in FreeBSDs `pkg`, in den OpenBSD-Ports und in NetBSDs pkgsrc enthalten.
`pcntl` (für asynchrones Veröffentlichen und den Worker-Pool von Velocity) ist verfügbar. ImageMagick findet der
Einrichtungsassistent unter `/usr/local/bin`.

### macOS

Die Homebrew-Formel `php` bringt die Erweiterungen mit, die Exponential braucht; Velocity läuft mit geforkten Workern
wie unter Linux. Für die Entwicklung brauchen SQLite und Velocity (oder der eingebaute Server von PHP) nichts weiter.

### Windows

- Verwenden Sie die offiziellen PHP-8-Builds für Windows (x64, nicht threadsicher für FastCGI) mit Apache oder IIS.
  Sie enthalten die Erweiterungen, die Exponential braucht, darunter `intl`.
- `pcntl` und `posix` gibt es unter Windows nicht: Asynchrones Veröffentlichen (`bin/php/ezasynchronouspublisher.php`)
  ist nicht verfügbar, und Velocity betreibt seine Worker als `php-cgi`-Unterprozesse ohne gemeinsame Nutzung des
  Speichers per Copy-on-Write.
- Velocitys Windows-Binaries aus einer einzigen Datei werden **ohne `intl`** gebaut, das das Setup von Exponential
  voraussetzt; betreiben Sie Velocity unter Windows daher über das Phar mit dem offiziellen PHP statt über sein Binary
  (der Phar-Weg unter Windows ist laut den eigenen Plattformhinweisen der Velocity-Engine voraussichtlich lauffähig,
  aber ungetestet).
- Die Dokumentation und die Shell-Befehle in dieser Hilfe sind für Linux geschrieben; übertragen Sie Pfade und
  Berechtigungen entsprechend.
- WSL2 führt die obigen Linux-Stufen aus und ist unter Windows der einfachere Weg für die Entwicklung.

### Container

Siehe [Container, Cloud und Hosting-Panels](#container-cloud-und-hosting-panels).

## Einen Rechner in zwei Minuten prüfen

```bash
php -v                 # 8.0 or later, for Velocity too
php -m                 # the extensions listed above
php -i | grep "Integer Size"   # 64 bits recommended
```

Lassen Sie dann den Installer den Rest prüfen: die Seite **Systemtest** des Einrichtungsassistenten, oder
`exp:install` und den Kickstarter, die dieselben kritischen Tests ausführen und zu jedem Fehler die Abhilfe ausgeben.
Ist Velocity installiert, vergleicht `php bin/php/console exp:velocity ext check` den Rechner mit dem Satz von
Erweiterungen, den Velocity erwartet.

## Verwandte Seiten

- [Kapitel 2, Voraussetzungen](../installation/02-voraussetzungen.md) und [Kapitel 8, Die Site ausliefern](../installation/08-die-website-ausliefern.md).
- [Unterstützte Datenbanken](datenbankunterstuetzung.md): die unterstützten Datenbanken, in Reihenfolge.
- [Velocity-Pakete und -Binaries](../../eng-us/features/6.0/velocity-packages-and-binaries.md),
  [Der Webserver Velocity](../../eng-us/features/6.0/velocity-web-server.md), [Velocity-Engines](../../eng-us/bc/6.0/velocity-engines.md).
- [Unterstützung für PHP 8.0](../../eng-us/bc/6.0/php-8.0-support.md), [Continuous Integration](../spezifikationen/6.0/kontinuierliche-integration.md).
- [Deployment](../anleitungen/bereitstellung.md).
- [Freie Dokumentation](freie-dokumentation.md): Diese Seiten dürfen frei kopiert, übersetzt und verbessert werden.
