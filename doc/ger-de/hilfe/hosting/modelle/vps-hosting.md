---
title: VPS-Hosting
audience: hosting, admin
category: hosting
order: 40
summary: Wie Sie Exponential auf einem virtuellen privaten Server anbieten - Distribution und PHP wählen, mit Velocity oder einem klassischen Webserver ausliefern, die Datenbank, Benutzer und Berechtigungen, ein Startdienst und worum sich Hoster und Kunde jeweils kümmern.
keywords: VPS, virtueller privater Server, virtuelle Maschine, Cloud-Maschine, root-Zugang, AlmaLinux, Red Hat Enterprise Linux, Debian, Ubuntu, PHP, Velocity, systemd, Apache, nginx, PHP-FPM, SQLite, PostgreSQL, Berechtigungen, Zuständigkeiten, virtual private server, virtual machine, cloud machine, root access, permissions, responsibilities
type: help
---
# VPS-Hosting

Diese Seite ist für Hosting-Anbieter, die Exponential auf einem virtuellen privaten Server (VPS) anbieten: eine virtuelle
Maschine pro Kunde oder pro kleiner Gruppe von Websites, mit root- oder sudo-Zugang. Sie beantwortet, welches Betriebssystem
und welches PHP in das Image gehören, wie die Website ausgeliefert werden sollte, welche Datenbank passt, wie Sie Benutzer und
Berechtigungen einrichten und wie Sie die Arbeit zwischen Hoster und Kunde aufteilen.

## Inhalt

1. [Was die Quellen über virtuelle Maschinen sagen](#was-die-quellen-über-virtuelle-maschinen-sagen)
2. [Das Basis-Image](#das-basis-image)
3. [Die Website ausliefern](#die-website-ausliefern)
4. [Die Datenbank](#die-datenbank)
5. [Benutzer, Berechtigungen und Cron](#benutzer-berechtigungen-und-cron)
6. [Wer was tut](#wer-was-tut)
7. [Checkliste](#checkliste)

## Was die Quellen über virtuelle Maschinen sagen

Virtuelle Maschinen in der Cloud (Amazon EC2 einschließlich Graviton, Google Compute Engine, Azure, Hetzner, DigitalOcean und
die übrigen) sind gewöhnliche Linux-Maschinen: Stufe 2 oder 3 nach ihrer Distribution
([Container, Cloud und Hosting-Panels](../../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels)). Ein VPS von Ihrer eigenen
Plattform ist dasselbe: Seine Unterstützungsstufe ist die der Distribution, die Sie darauf installieren.

| Distribution im Image | Stufe | PHP 8 aus (laut den Quellen) |
|---|---|---|
| AlmaLinux 9 | 1 (der Entwicklungsserver des Projekts) | AppStream (8.0 mitgeliefert; Modul-Streams `php:8.1` bis `php:8.3`), Remi (8.0 bis 8.5) oder das PHP von Plesk |
| Red Hat Enterprise Linux 9 und 10, Rocky Linux, Oracle Linux, CentOS Stream 9 und 10 | 2 | AppStream, Remi |
| Debian 12 und 13 | 2 | die Distribution (8.2, 8.4) oder das Repository von Ondřej Surý |
| Ubuntu 22.04 und 24.04 | 2 | die Distribution (8.1, 8.3) oder das PPA `ondrej/php` |
| Alpine Linux | 2 | `apk` |
| Fedora, SUSE, Arch, Amazon Linux 2023 und andere | 3 | ihre eigenen Pakete |

Quelle: [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md#stufe-2-bekanntermaßen-lauffähig). Die Prozessorarchitektur zählt nur über PHP
und die Datenbank: x86_64 ist Stufe 1, aarch64 Stufe 2
([Prozessorarchitekturen](../../betriebssystemunterstuetzung.md#prozessorarchitekturen)).

## Das Basis-Image

Ein VPS-Image für Exponential enthält:

- **PHP 8.0 oder später**, für Apache, PHP-FPM und Velocity gleichermaßen (Velocity läuft ab seinem Release 0.0.4.54 unter 8.0).
  Die Empfehlung ist das neueste 8.x, das Ihre Distribution anbietet; 8.5 wird unterstützt
  ([2.2 PHP](../../../installation/02-voraussetzungen.md#22-php)).
- **Die erforderlichen Erweiterungen**: `dom`, `libxml`, `simplexml`, `xsl`, `mbstring`, `iconv`, `intl`, `pcre`, `json`,
  `session`, `zlib`, eine Datenbankerweiterung, `gd` oder ImageMagick; für Velocity außerdem `sockets`, mit `pcntl` und
  `openssl` ([2.3 PHP-Erweiterungen](../../../installation/02-voraussetzungen.md#23-php-extensions)).
- **64-Bit-PHP**, empfohlen für die Unterstützung von mehr als 30 Sprachen.
- **Composer 2** und **Git**, für Installationen und Updates aus dem Quellcode
  ([Composer und Git auf Servern](../grundlagen/composer-und-git-auf-servern.md)).
- **Dasselbe PHP für Web und Kommandozeile.** Beide müssen eine unterstützte Version sein und dieselben Erweiterungen haben
  ([Kommandozeile und Web-PHP](../../../installation/02-voraussetzungen.md#php-auf-der-kommandozeile-und-im-web)).

Unter Red Hat Enterprise Linux 9 und seinen Nachbauten ist die dokumentierte Paketauswahl für das mitgelieferte PHP:

```bash
dnf install php php-fpm php-mysqlnd php-gd php-intl php-mbstring php-xml php-pdo php-opcache
```

Dieses mitgelieferte PHP 8.0 führt auch Velocity aus (das el9-rpm installiert sich darauf ohne Modul-Stream). Für ein
neueres PHP wechseln Sie auf einen anderen Modul-Stream (`dnf module reset php`, `dnf module enable php:8.3`,
`dnf distro-sync`). Sobald eine Installation existiert, lassen Sie sie die Maschine prüfen:

```bash
php bin/php/console exp:velocity ext check
php bin/php/console exp:velocity ext install-hint intl xsl
```

Der zweite Befehl gibt den Installationsbefehl für die Pakete des Systems aus, auf dem er läuft
([Herausfinden, was fehlt](../../../installation/02-voraussetzungen.md#herausfinden-was-fehlt)).

## Die Website ausliefern

Auf einem VPS darf der Kunde lang laufende Prozesse ausführen; die empfohlene Engine ist also verfügbar:

| Option | Wann auf einem VPS |
|---|---|
| **Exponential Velocity** | der Standard: kein anderer Webserver, HTTPS mit eigenen Zertifikaten und ACME, persistente Worker, Antwort-Cache |
| **FrankenPHP** (über `exp:velocity`) | eine geprüfte Binärdatei mit eigenem PHP |
| **Apache oder nginx mit PHP-FPM** | das Image bringt bereits ein Panel oder einen abgestimmten Webserver mit, oder die Maschine hat nur PHP 8.0 |

Velocity bindet sich in der Auslieferung an `127.0.0.1` auf den Ports 8088 (HTTP) und 8080 (HTTPS). Um den Besuchern direkt
gegenüberzustehen, wird es auf `Host=0.0.0.0`, `Port=80` und `HTTPSPort=443` gesetzt und als root gestartet; die Worker
wechseln dann zum Benutzer der Website
([8.3.5 Adresse, Ports, Benutzer und Gruppe](../../../installation/08-die-website-ausliefern.md#835-adresse-ports-benutzer-und-gruppe)).
`exp:velocity` hat keinen Befehl, der einen Startdienst installiert; Kapitel 8 zeigt eine abgeleitete systemd-Unit, die
`exp:velocity start` ausführt ([8.3.11](../../../installation/08-die-website-ausliefern.md#8311-velocity-als-dienst-betreiben)).

> **Tipp**
> Legen Sie den Startdienst in Ihre Vorlage für VPS-Images, mit dem Installationspfad und dem Datenbankdienst in `After=` als
> Platzhaltern, die Ihre Bereitstellung füllt. Dann kommt jede Kundenmaschine nach einem Neustart wieder hoch.

Mehr: [Velocity für Hoster](../server/velocity-fuer-hoster.md),
[Apache mit PHP-FPM](../server/apache-mit-php-fpm.md), [nginx mit PHP-FPM](../server/nginx-mit-php-fpm.md).

## Die Datenbank

In der offiziellen Reihenfolge: 1. SQLite 3 (der Standard) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL.
Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich.

Ein VPS liefert eine Website von einer Maschine aus, und genau dort ist **1. SQLite 3** eine vollwertige
Produktionsdatenbank. Wählen Sie einen Server, wenn mehr als eine Maschine in die Datenbank schreiben muss, wenn viele
Redakteure den ganzen Tag gleichzeitig große Änderungen veröffentlichen oder wenn Sie Replikation oder Benutzer und Rechte
auf Ebene der Datenbank brauchen; dann ist **2. PostgreSQL** der empfohlene Datenbankserver
([Datenbankunterstützung](../../datenbankunterstuetzung.md)). Der Datenbankserver kann auf dem VPS selbst oder auf einer getrennten
Datenbankmaschine Ihrer Plattform laufen.

Siehe [Datenbanken in der offiziellen Reihenfolge](../grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md).

## Benutzer, Berechtigungen und Cron

- **Ein Benutzer schreibt.** Der PHP-FPM-Pool, die Velocity-Worker, Cronjobs und Konsolenskripte laufen alle als Benutzer
  der Website ([8.7.2](../../../installation/08-die-website-ausliefern.md#872-ein-benutzer-oder-eine-gruppe)). Lassen Sie Worker nie als root
  laufen (`[ServerSettings] AllowRootWorkers=disabled`, der Standard).
- **Keine für alle beschreibbaren Dateien.** Setzen Sie die gehärteten `[FileSettings]`-Werte `0770` und `0660`, die
  `site.ini` empfiehlt ([13.11](../../../installation/13-sicherheitshaertung.md#1311-dateiberechtigungen-und-eigentümer)).
- **Cron in der Crontab des Benutzers der Website**: `crontab -e -u <site user>` oder `php bin/php/console crontab:edit` als
  dieser Benutzer ([Kapitel 10, Cronjobs](../../../installation/10-nach-der-installation.md#103-cronjobs)).

## Wer was tut

Ein VPS-Angebot muss sagen, wer sich um welche Schicht kümmert. Die Quellen schreiben keine Aufteilung vor; dies sind die
Möglichkeiten, zwischen denen ein Hoster üblicherweise wählt:

| Schicht | Nicht verwalteter VPS | Verwalteter VPS |
|---|---|---|
| Hypervisor, Netzwerk, Snapshots | Hoster | Hoster |
| Updates von Betriebssystem und PHP | Kunde | Hoster |
| Updates von Exponential (Composer, Erweiterungen) | Kunde | Hoster oder Kunde, wie vereinbart ([Managed Hosting](managed-hosting.md)) |
| Backups von Datenbank und Dateien | Kunde oder Snapshots des Hosters | Hoster, mit Wiederherstellungstests |
| Zertifikate | Kunde (ACME von Velocity, certbot) | Hoster |
| Überwachung | Kunde | Hoster |

> **Warnung**
> Ein Snapshot des Hypervisors von einer laufenden Maschine ersetzt kein Datenbank-Backup. Für SQLite gilt: Kopieren Sie
> nie eine Datenbankdatei, die in Benutzung ist; nutzen Sie `.backup` oder `VACUUM INTO`
> ([Backup und Wiederherstellung](../../datenbankunterstuetzung.md#backup-und-wiederherstellung)).

## Checkliste

- [ ] Distribution der Stufe 1 oder 2, PHP 8.0 oder später für Web und Kommandozeile, in beiden dieselben Erweiterungen.
- [ ] `exp:velocity ext check` ohne Befund für die genutzten Funktionen.
- [ ] Velocity mit einem Startdienst oder Apache/nginx mit PHP-FPM; HTTPS mit einem Zertifikat einer CA.
- [ ] Datenbank in der offiziellen Reihenfolge gewählt und dokumentiert.
- [ ] Alles läuft als Benutzer der Website; `[FileSettings]` gehärtet.
- [ ] Cron-Zeilen für `runcronjobs.php` in der Crontab des Benutzers der Website.
- [ ] Backups von Datenbank, `var/storage`, `settings/override`, `settings/siteaccess`, einmal auf einer anderen Maschine wiederhergestellt.

## Verwandte Seiten

- [Ein Hosting-Modell wählen](ein-hosting-modell-waehlen.md), [Dedizierte Server](dedizierte-server.md)
- [Betriebssysteme für Hoster](../grundlagen/betriebssysteme-fuer-hoster.md)
- [Checkliste: neuer Server](../checklisten/checkliste-fuer-einen-neuen-server.md)
- [Kapitel 2: Anforderungen](../../../installation/02-voraussetzungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
