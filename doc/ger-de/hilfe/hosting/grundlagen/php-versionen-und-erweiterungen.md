---
title: PHP-Versionen und Erweiterungen
audience: hosting, admin
category: hosting
order: 40
summary: Auf welchen PHP-Versionen Exponential 6.0.x und Velocity laufen, welche PHP-Erweiterungen erforderlich, empfohlen oder je Datenbank nötig sind, die Paketnamen in der RHEL-Familie, unter Debian, Ubuntu und Plesk, und warum das Web-PHP und das PHP der Kommandozeile übereinstimmen müssen.
keywords: PHP, PHP-Versionen, PHP 8.0, PHP 8.1, PHP 8.5, Erweiterungen, dom, xsl, intl, mbstring, iconv, zlib, sqlite3, pgsql, mongodb, mysqli, oci8, gd, ImageMagick, curl, openssl, pcntl, sockets, opcache, redis, fileinfo, Remi, AppStream, Plesk, php-fpm, PHP der Kommandozeile, PHP versions, extensions, command line PHP
type: help
---
# PHP-Versionen und Erweiterungen

Diese Seite ist für Systemadministratoren, die das PHP eines Servers für Exponential bauen, und für Produktmanager, die
entscheiden, welche PHP-Versionen ein Tarif anbietet. Sie beantwortet: welche PHP-Versionen Exponential und Velocity
ausführen, welche Erweiterungen erforderlich oder empfohlen sind, woher sie auf den üblichen Linux-Familien kommen und
warum die beiden PHPs eines Servers (Web und Kommandozeile) übereinstimmen müssen. Die Quelle ist
[Kapitel 2 des Installationsbuchs](../../../installation/02-voraussetzungen.md#22-php).

## Inhalt

1. [PHP-Versionen](#php-versionen)
2. [Erforderliche Erweiterungen](#erforderliche-erweiterungen)
3. [Datenbankerweiterungen](#datenbankerweiterungen)
4. [Empfohlene Erweiterungen](#empfohlene-erweiterungen)
5. [Pakete je Systemfamilie](#pakete-je-systemfamilie)
6. [Web-PHP und PHP der Kommandozeile](#web-php-und-php-der-kommandozeile)
7. [Checkliste](#checkliste)
8. [Verwandte Seiten](#verwandte-seiten)

## PHP-Versionen

| PHP | Stand |
|---|---|
| 8.0 | Die älteste unterstützte Version; der Kernel läuft darauf unter Apache mit mod_php oder PHP-FPM. Sie ist das mitgelieferte PHP von RHEL 9, AlmaLinux 9 und Rocky Linux 9. Velocity läuft darauf ab seinem Release 0.0.4.54 (bis dahin brauchte es 8.1). |
| 8.1 bis 8.4 | Unterstützt, einschließlich Velocity. |
| 8.5 | Unterstützt; die neueste getestete Version. |
| 7.4 und älter | Von 6.0.8 an nicht unterstützt; eine Website, die auf 7.4 bleiben muss, bleibt auf 6.0.7. |

`composer.json` erklärt `"php": "^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4 || ^8.5 || ^8.6 || ^8.7 || ^8.8"`. Die Versionen
nach 8.5 werden von der Bedingung akzeptiert, sind aber weder veröffentlicht noch getestet.

> **Tipp**
> Bieten Sie das neueste PHP Ihrer Plattform als Standard für neue Exponential-Websites an, und behalten Sie ältere
> Versionen für Websites, die sie brauchen. Jede neuere Version bringt Geschwindigkeit, und die älteren verlieren zuerst
> die Sicherheitsunterstützung.

**Welches Exponential-Release welches PHP braucht** ([Kapitel 3](../../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches)):

| Exponential | `"php"` in seiner `composer.json` | Auf PHP 8.0 |
|---|---|---|
| `v6.0.0` bis `v6.0.7` | `^7.4 \|\| ^8.1` | nicht installierbar |
| `v6.0.8` bis `v6.0.14` | `^8.1` und spätere 8.x | nicht installierbar |
| Linie 6.0.15 (`main`) | `^8.0` und spätere 8.x | installierbar |

**64 Bit.** `composer.json` schlägt `php-64bit` vor: Für die Unterstützung von mehr als 30 Sprachen ist eine
64-Bit-PHP-Installation auf allen beteiligten Produktions- und Entwicklungsmaschinen erforderlich. Die Sprachen eines
Objekts werden als Bits einer Ganzzahl gespeichert.

## Erforderliche Erweiterungen

| Erweiterung | Verlangt von | Warum |
|---|---|---|
| `dom` | `composer.json`; kritischer Test `dom_extension` | das Rich-Text-Feld wird als XML gespeichert |
| `libxml` | `composer.json` | die XML-Bibliothek unter `dom`, `simplexml` und `xsl` |
| `simplexml` | `composer.json` | XML-Dateien wie die Metadaten von Erweiterungen lesen |
| `xsl` | kritischer Test `xsl_extension` | der Setup-Assistent fährt ohne sie nicht fort |
| `mbstring` | `composer.json`; kritischer Test | Mehrbyte-Zeichenketten in jeder Sprache |
| `iconv` | `composer.json`; kritischer Test | Umwandlung von Zeichensätzen |
| `intl` | kritischer Test `intl_extension` | der Setup-Assistent fährt ohne sie nicht fort; internationale Hostnamen |
| `pcre`, `json`, `reflection`, `spl` | `composer.json` | Teil von PHP |
| `session` | `composer.json`; kritischer Test `php_session` | Benutzersitzungen |
| `zlib` | kritischer Test `zlib_extension` | Paketarchive, gzip-Datei-Handler, komprimierte Audit-Dateien |
| eine Datenbankerweiterung | kritischer Test `database_extensions` | siehe unten |
| `gd` oder ImageMagick | kritischer Test `image_conversion` | Bildvarianten |

**Für Velocity** verlangt das Engine-Paket `sockets` und schlägt `pcntl` (geordnetes Herunterfahren und der Worker-Pool)
und `openssl` (HTTPS) vor; in der Praxis sind alle drei für ein produktives Velocity nötig.

## Datenbankerweiterungen

Installieren Sie nur die Erweiterung der Datenbank, die die Website nutzt; mindestens eine ist erforderlich
(`settings/setup.ini`, `[database_extensions] Extensions=sqlite3;mysqli;pgsql;mongodb;oci8`, `Require=one`).

| Datenbank | Erweiterung | Woher sie kommt |
|---|---|---|
| 1. SQLite 3 | `sqlite3` | mit PHP gebündelt; RHEL-Familie `php-pdo`, Debian und Ubuntu `php<version>-sqlite3` |
| 2. PostgreSQL | `pgsql` | `php-pgsql`, `php<version>-pgsql` |
| 3. MongoDB | `mongodb` (PECL) und die Composer-Bibliothek `mongodb/mongodb` | `pecl install mongodb` oder `php-pecl-mongodb`; die Bibliothek mit `composer require mongodb/mongodb`; PHP 8.2 oder später |
| 4. MariaDB | `mysqli` | `php-mysqlnd`, `php<version>-mysql` |
| 5. Oracle | `oci8` (PECL) | gegen einen Oracle-Client gebaut (der Instant Client genügt); PECL oci8 3.4 für PHP 8.4 und 8.5, 3.2 für 8.1 bis 8.3 |
| 6. MySQL | `mysqli` | wie MariaDB |

Quelle: [Datenbankunterstützung, PHP-Erweiterungen](../../datenbankunterstuetzung.md#php-erweiterungen).

## Empfohlene Erweiterungen

| Erweiterung | Warum |
|---|---|
| `curl` | bessere Unterstützung für die Kommunikation mit anderen Servern, etwa das Herunterladen von Paketen über SSL (Vorschlag in `composer.json`) |
| `gd` | Bildbearbeitung ohne ImageMagick; der standardmäßig erste Bildkonverter |
| `openssl` | kryptografisch sichere Zufallsbytes; HTTPS in Velocity |
| `pcntl` | asynchrones Veröffentlichen (`bin/php/ezasynchronouspublisher.php`); der Worker-Pool von Velocity |
| `opcache` | der Cache für kompilierten Code von PHP; ab PHP 8.5 immer einkompiliert |
| `fileinfo` | liest den Typ eines hochgeladenen Bildes aus seinem Inhalt (`settings/image.ini`, `[ImageUploadSettings]`) |
| `redis` | nur für die Cache-Erweiterungen für Valkey und Redis (`sevenx_valkey`, `sevenx_valkey_cache`) |

Die Seite **Setup > Systeminformationen** der Administration warnt, wenn `curl`, `fileinfo`, `intl`, `xsl`, OPcache oder
APCu fehlt oder wenn sowohl `gd` als auch `imagick` fehlen ([Systeminformationen](../../../anleitungen/systeminformationen.md)).
`pcntl` und `posix` gibt es unter Windows nicht.

## Pakete je Systemfamilie

Aus [Kapitel 2.9](../../../installation/02-voraussetzungen.md#29-hinweise-zu-betriebssystemen):

**RHEL 9, AlmaLinux 9, Rocky Linux 9.** Das mitgelieferte PHP aus AppStream ist 8.0:

```bash
dnf install php php-fpm php-mysqlnd php-gd php-intl php-mbstring php-xml php-pdo php-opcache
```

`php-xml` bringt `dom`, `simplexml`, `xsl` und `libxml`; `php-pdo` bringt `sqlite3`; `php-common` bringt `iconv`,
`zlib`, `session` und `openssl`; `php-process` bringt `pcntl`; `php-pgsql` fügt PostgreSQL hinzu. Velocity läuft unter
diesem mitgelieferten 8.0 (ab Release 0.0.4.54; das el9-rpm installiert sich ohne Modul-Stream). Für ein neueres PHP
wechseln Sie auf einen anderen Modul-Stream (`dnf module reset php`, `dnf module enable php:8.3`, `dnf distro-sync`).
Unter RHEL 8 (Standard-PHP 7.2) aktivieren Sie zuerst `php:8.0`. Das
Remi-Repository stellt 8.0 bis 8.5 bereit; PECL-Erweiterungen und `oci8` kommen aus EPEL, von Remi oder vom Hersteller.

**Debian und Ubuntu.** Pakete heißen `php<version>-<extension>`: `php8.3-xml`, `php8.3-mbstring`, `php8.3-intl`,
`php8.3-common`, `php8.3-sqlite3`, `php8.3-mysql`, `php8.3-pgsql`, `php8.3-gd`, `php8.3-curl`, `php8.3-opcache`;
`pcntl` ist in `php8.3-cli`. Ersetzen Sie `8.3` durch die Version Ihres Release.

**Plesk.** Jede PHP-Version liegt unter `/opt/plesk/php/<version>/`; gebündelte Erweiterungen werden in den
PHP-Einstellungen des Panels eingeschaltet, PECL-Erweiterungen mit `/opt/plesk/php/<version>/bin/pecl install <extension>`.
Siehe [Plesk](../control-panels/plesk.md).

**Andere Panels.** Nutzen Sie die PHP-Auswahl des Panels, und lesen Sie in der Dokumentation Ihres Panels nach, wie es
Erweiterungen aktiviert.

## Web-PHP und PHP der Kommandozeile

Exponential nutzt PHP zweimal: Das PHP des Webservers (PHP-FPM, mod_php oder die Worker von Velocity) liefert Seiten
aus, und das PHP der Kommandozeile führt Composer, die Installer, die Konsole, die Cronjobs und Velocity selbst aus.
Beide müssen eine unterstützte Version sein und dieselben Erweiterungen haben
([Kapitel 2](../../../installation/02-voraussetzungen.md#php-auf-der-kommandozeile-und-im-web)).

Was schiefgeht, wenn sie sich unterscheiden:

- Composer, mit einem neueren PHP der Kommandozeile ausgeführt, wählt Paketversionen, die das Web-PHP nicht ausführen kann.
- Eine Installation über die Kommandozeile gelingt, und der Browser zeigt dann eine weiße Seite, weil dem Web-PHP eine
  Erweiterung fehlt.
- Ein Cronjob läuft mit einem anderen PHP als die Website und scheitert an einer Erweiterung, die nur das Web-PHP hat.

Auf Maschinen mit mehreren PHP-Versionen rufen Sie die richtige Binärdatei über ihren Pfad auf, zum Beispiel
`/opt/plesk/php/8.5/bin/php bin/php/console ...`, und verwenden denselben Pfad in der Crontab.

> **Warnung**
> Wenn Sie das PHP einer Website aktualisieren, aktualisieren Sie gleichzeitig das PHP der Kommandozeile, das ihr Cron
> und ihre Konsole nutzen. Siehe das [Runbook für das PHP-Upgrade](../runbooks/php-upgrade.md).

## Checkliste

- [ ] Die PHP-Versionen des Tarifs sind 8.0 oder später, auch überall, wo Velocity angeboten wird.
- [ ] 64-Bit-Builds von PHP.
- [ ] Jede erforderliche Erweiterung sowohl im Web-PHP als auch im PHP der Kommandozeile.
- [ ] Genau die Datenbankerweiterung(en), die der Tarif anbietet.
- [ ] `curl`, `gd`, `openssl`, `pcntl`, `opcache` und `fileinfo` installiert.
- [ ] `sockets`, `pcntl` und `openssl` für Velocity.
- [ ] Die Crontab und die Konsole nutzen dieselbe PHP-Binärdatei wie die Website.

## Verwandte Seiten

- [Anforderungen auf einen Blick](anforderungen-auf-einen-blick.md) und [PHP-Einstellungen für Exponential](php-einstellungen-fuer-exponential.md)
- [PHP-Auswahl und FPM-Pools](../control-panels/php-auswahl-und-fpm-pools.md)
- [Unterstützung für PHP 8.0](../../../../eng-us/bc/6.0/php-8.0-support.md)
- [Kapitel 2: Anforderungen](../../../installation/02-voraussetzungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
