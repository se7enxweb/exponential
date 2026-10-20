---
title: Betriebssysteme für Hoster
audience: hosting, admin
category: hosting
order: 140
summary: Auf welchen Betriebssystemen ein Hosting-Anbieter Exponential betreiben kann, sortiert nach den vier Stufen des Projekts, mit der Referenzplattform, den Linux-Familien, Panels, Containern, Prozessorarchitekturen, Windows und der Prüfung einer Maschine.
keywords: Betriebssystem, OS, Linux, RHEL, AlmaLinux, Rocky Linux, CentOS Stream, Debian, Ubuntu, Alpine, Plesk, cPanel, DirectAdmin, FreeBSD, Windows, IIS, Container, Docker, Podman, Kubernetes, ARM, aarch64, Stufen, Referenzplattform, Hosting, operating system, containers, tiers, reference platform, hosting
type: help
---
# Betriebssysteme für Hoster

Diese Seite ist für Systemadministratoren und Produktmanager bei einem Hosting-Anbieter, die das Betriebssystem der
Server wählen, auf denen ein Exponential-Angebot läuft. Sie beantwortet: welche Systeme getestet sind, bekanntermaßen
funktionieren oder nur erwartungsgemäß funktionieren, was das für eine Hosting-Plattform bedeutet und worauf Sie bei
Panels, Containern, anderen Architekturen und Windows achten müssen. Die vollständige Aussage zur Unterstützung mit
jedem System ist [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md); diese Seite ordnet sie für Hoster.

## Inhalt

1. [Die kurze Antwort](#die-kurze-antwort)
2. [Die vier Stufen](#die-vier-stufen)
3. [Linux-Distributionen für das Hosting](#linux-distributionen-für-das-hosting)
4. [Panels](#panels)
5. [Container und Cloud](#container-und-cloud)
6. [Prozessorarchitekturen](#prozessorarchitekturen)
7. [Windows und die BSDs](#windows-und-die-bsds)
8. [Eine Maschine prüfen](#eine-maschine-prüfen)
9. [Checkliste](#checkliste)
10. [Verwandte Seiten](#verwandte-seiten)

## Die kurze Antwort

Exponential 6.0.x läuft auf fast jedem Betriebssystem, das PHP 8.0 oder später und einen Webserver hat. Es besteht aus
reinen PHP-Dateien und einer Datenbank; es hat keinen eigenen kompilierten Code und hängt von keiner bestimmten
C-Bibliothek ab ([Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md#was-das-betriebssystem-bereitstellen-muss)). Für einen
Hoster ist die praktische Wahl eine aktuelle Linux-Distribution mit einem gepflegten PHP 8.

## Die vier Stufen

| Stufe | Bedeutung | Beispiele für Hoster |
|---|---|---|
| **1. Hier getestet** | das Projekt betreibt es jeden Tag, oder seine CI läuft bei jedem Push darauf | AlmaLinux 9 (der Entwicklungsserver, mit Plesk); Ubuntu in GitHub Actions |
| **2. Funktioniert bekanntermaßen** | abgedeckt durch die Dokumentation, Pakete oder die CI der Velocity-Engine | RHEL 9 und 10, Rocky Linux, AlmaLinux, Oracle Linux, CentOS Stream 9 und 10; Debian 12 und 13; Ubuntu 22.04 und 24.04; Alpine; Plesk-Hosts; FreeBSD 14; Windows x64; Docker und Podman |
| **3. Funktioniert erwartungsgemäß** | ein gepflegtes PHP 8 existiert und nichts Bekanntes steht im Weg, aber niemand hat Exponential dort laufen sehen | Fedora, SUSE und openSUSE, Amazon Linux 2023, cPanel- und DirectAdmin-Hosts, Kubernetes |
| **4. Heute nicht möglich** | für das System gibt es kein PHP 8 | für einen Hoster ohne praktische Bedeutung |

Eine Zeile der Stufe 3 ist „eine begründete Erwartung, kein Testergebnis“. Wenn Sie Exponential auf einem System der
Stufe 3 betreiben, bittet das Projekt um eine Rückmeldung im [Software-Tracker](https://github.com/se7enxweb/exponential/issues),
und die Zeile rückt auf.

> **Tipp**
> Wählen Sie für eine neue Hosting-Plattform ein System der Stufe 1 oder 2. So bleibt Ihre Plattform auf den Wegen, die
> die Dokumentation und die Pakete des Projekts selbst beschreiben.

## Linux-Distributionen für das Hosting

| Familie | PHP 8 aus | Hinweise für Hoster |
|---|---|---|
| RHEL 9 und 10 und Nachbauten | AppStream (PHP 8.0 standardmäßig in EL 9; Modul-Streams `php:8.1` bis `php:8.3`), Remi (8.0 bis 8.5) | die dokumentierte Referenzplattform; Velocity liefert `.rpm`-Pakete für EL 9 und EL 10; das mitgelieferte 8.0 führt Velocity aus (ab Release 0.0.4.54) |
| Debian 12 und 13 | die Distribution (8.2, 8.4) oder das Repository von Ondřej Surý | Velocity liefert `.deb`-Pakete |
| Ubuntu 22.04 und 24.04 | die Distribution (8.1, 8.3) oder das PPA `ondrej/php` | Velocity liefert `.deb`-Pakete; die CI des Projekts läuft auf Ubuntu |
| Alpine (musl) | `apk` | die CI von Velocity weist seinen Server dort nach; als Container-Basis verbreitet |

Paketnamen je Familie stehen auf [PHP-Versionen und Erweiterungen](php-versionen-und-erweiterungen.md#pakete-je-systemfamilie)
und in [Kapitel 2.9](../../../installation/02-voraussetzungen.md#29-hinweise-zu-betriebssystemen).

### PHP 8.0 und Red Hat Enterprise Linux

Exponential Velocity läuft ab seinem Release 0.0.4.54 (20. Oktober 2026) unter PHP 8.0 bis 8.5; unter 8.0 ist nichts
Zusätzliches nötig. Für einen Hoster bedeutet das:

- **RHEL 9, AlmaLinux 9, Rocky Linux 9**: Das standardmäßige AppStream-PHP 8.0 führt Velocity aus. Das el9-rpm
  (`exponential-velocity-<release>-1.el9.noarch.rpm`) hängt von `php-cli >= 8.0` ab und installiert sich, ohne dass ein
  Modul-Stream aktiviert werden muss; ein neuerer Stream funktioniert weiterhin. Auch Composer installiert die Engine unter 8.0.
- **RHEL 8**: Das Standard-PHP ist 7.2. Aktivieren Sie den Stream 8.0 (`dnf module enable php:8.0`) und verwenden Sie das
  Phar oder Composer; ein el8-rpm gibt es nicht.
- **Was unter 8.0 anders ist**: Die Bildausgabe als AVIF braucht PHP 8.1 (die Aushandlung endet bei WebP); der
  Speicherhöchstwert je Anfrage im Dashboard braucht PHP 8.2 (die Spalte bleibt leer); `$_FILES` hat kein `full_path`,
  wie in PHP 8.0 selbst; eine mit `eval()` deklarierte Klasse mit statischen Eigenschaften führt dazu, dass ihr Worker
  nach dieser Anfrage ersetzt wird (korrekt, es kostet einen Fork).

Erweiterungen, systemd, SELinux und Überprüfung: die Seite
[PHP 8.0 and Red Hat Enterprise Linux](https://github.com/se7enxweb/exponential-velocity/blob/main/docs/rhel.md) der Engine
und [Kapitel 2](../../../installation/02-voraussetzungen.md#php-80-und-red-hat-enterprise-linux).

## Panels

| Panel | Stufe | Was die Dokumentation sagt |
|---|---|---|
| Plesk | 1 | der Entwicklungsserver des Projekts; PHP-FPM-Pools pro Domain, PHP unter `/opt/plesk/php/<version>/`, Dienst `plesk-php<XY>-fpm` |
| cPanel, DirectAdmin | 3 | PHP pro Konto; Shared Hosting kann lang laufende Prozesse verbieten, sodass Velocity womöglich nicht erlaubt ist: nutzen Sie Apache oder LiteSpeed mit `.htaccess` |

Auf Shared Hosting ohne Shell-Zugang ist der Setup-Assistent im Browser der Installer. Siehe
[Control Panels](../control-panels/README.md).

## Container und Cloud

Aus [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels):

- Jedes Image mit PHP 8 und den erforderlichen Erweiterungen kann Exponential ausführen. Velocity veröffentlicht Images
  unter `ghcr.io/se7enxweb/exponential-velocity` für amd64 und arm64, je eines pro PHP-Version und Variante.
- Halten Sie `var/` (Speicher, Logs, die SQLite-Datenbank) und `settings/override` auf einem **persistenten Volume**.
- Mit SQLite betreiben Sie **einen** Container, der schreibt: WAL braucht gemeinsamen Speicher auf einem Host, sodass
  mehrere Replikate keine Datenbankdatei über ein Netzwerk-Volume teilen können. Mehrere Replikate brauchen einen
  Datenbankserver (zuerst PostgreSQL) und gemeinsamen Speicher für `var/storage`.
- Virtuelle Maschinen in der Cloud sind gewöhnliche Linux-Maschinen, in der Stufe ihrer Distribution.
- Platform-as-a-Service-Angebote mit einer beschreibbaren persistenten Festplatte funktionieren erwartungsgemäß; ein
  schreibgeschütztes oder flüchtiges Dateisystem braucht `var/` auf persistentem Speicher und einen Datenbankserver.

Siehe [Container: Docker und Podman](../modelle/container-docker-und-podman.md) und [Kubernetes](../modelle/kubernetes.md).

## Prozessorarchitekturen

Exponential hat keinen Maschinencode; die Architektur zählt also nur über PHP und die Datenbank:

| Architektur | Stufe |
|---|---|
| x86_64 | 1 |
| aarch64 (ARM64: Graviton, Ampere, Apple Silicon, Raspberry Pi 4 und 5) | 2; Velocity veröffentlicht aarch64-Binärdateien und -Images |
| armv7, i386 | 3; 32-Bit-PHP begrenzt eine Website auf 30 Sprachen |
| ppc64le, s390x | 3; die CI von Velocity weist seinen Server auf beiden nach |
| riscv64 | 3; nur unter Emulation getestet |

## Windows und die BSDs

**Windows** (Stufe 2): die offiziellen PHP-8-Builds mit Apache oder IIS (FastCGI). Was dort fehlt
([Betriebssystemunterstützung: Windows](../../betriebssystemunterstuetzung.md#windows)):

- `pcntl` und `posix` gibt es nicht; asynchrones Veröffentlichen ist daher nicht verfügbar, und Velocity betreibt seine
  Worker als `php-cgi`-Unterprozesse.
- Die Windows-Einzeldatei-Binärdateien von Velocity sind ohne `intl` gebaut, das Exponential verlangt; betreiben Sie
  Velocity unter Windows über das Phar mit dem offiziellen PHP.
- Die Dokumentation und ihre Shell-Befehle sind für Linux geschrieben.

**FreeBSD 14 und DragonFly BSD** (Stufe 2): Die CI von Velocity startet dort und liefert aus; PHP 8 aus `pkg`.
Exponential selbst wird vom Projekt dort nicht betrieben. OpenBSD und NetBSD sind Stufe 3.

## Eine Maschine prüfen

```bash
php -v                          # 8.0 or later, for Velocity too
php -m                          # the required extensions
php -i | grep "Integer Size"    # 64 bits recommended
```

Lassen Sie dann den Installer den Rest prüfen: den **Systemtest** des Setup-Assistenten, den Kickstarter oder
`exp:install`. Mit installiertem Velocity vergleicht `php bin/php/console exp:velocity ext check` die Maschine mit der
Erweiterungsausstattung, die Velocity erwartet ([Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md#einen-rechner-in-zwei-minuten-prüfen)).

## Checkliste

- [ ] Das Betriebssystem der Plattform ist Stufe 1 oder Stufe 2.
- [ ] Eine gepflegte Quelle für PHP 8 gewählt (Distribution, Modul-Stream, Remi, die Pakete von Ondřej Surý oder die Builds des Panels).
- [ ] 64-Bit-PHP.
- [ ] Container halten `var/` und `settings/override` auf persistenten Volumes; ein Schreiber für SQLite.
- [ ] Panels, die lang laufende Prozesse verbieten, werden mit Apache oder LiteSpeed angeboten, nicht mit Velocity.

## Verwandte Seiten

- [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md)
- [Anforderungen auf einen Blick](anforderungen-auf-einen-blick.md)
- [Ein Hosting-Modell wählen](../modelle/ein-hosting-modell-waehlen.md)
- [Einen Webserver wählen](../server/einen-webserver-waehlen.md)
- [Kapitel 2.9: Hinweise zu Betriebssystemen](../../../installation/02-voraussetzungen.md#29-hinweise-zu-betriebssystemen)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
