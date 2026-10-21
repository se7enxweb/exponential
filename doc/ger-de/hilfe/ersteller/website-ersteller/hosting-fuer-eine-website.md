---
title: Hosting für eine Website
audience: website-creator, admin
category: website-creation
order: 70
summary: Was eine Website auf Exponential von ihrem Hosting braucht - PHP, Erweiterungen, Datenbank, Webserver oder Velocity, Speicherplatz, Cronjobs und Mail - und die Fragen an einen Hosting-Anbieter.
keywords: Hosting, Anforderungen, PHP, Datenbank, Webserver, Velocity, Shared Hosting, Cron, Mail, Speicherplatz, Hosting-Anbieter, Website-Ersteller, hosting, requirements, web server, Apache, nginx, FrankenPHP, shared hosting, VPS, cron, mail, disk, hosting provider, website creator
type: help
---
# Hosting für eine Website

Diese Seite richtet sich an Website-Ersteller, die das Hosting für eine Website auswählen oder prüfen. Sie fasst
zusammen, was Exponential braucht, welche Wahlmöglichkeiten Sie haben und welche Fragen Sie einem Hosting-Anbieter
schicken. Die vollständige Referenz sind [Anforderungen](../../../installation/02-voraussetzungen.md) und
[Die Website ausliefern](../../../installation/08-die-website-ausliefern.md).

## Inhalt

1. [Die Checkliste](#die-checkliste)
2. [Wählen, wie die Website ausgeliefert wird](#wählen-wie-die-website-ausgeliefert-wird)
3. [Die Datenbank wählen](#die-datenbank-wählen)
4. [Was die Website sonst noch braucht](#was-die-website-sonst-noch-braucht)
5. [Fragen an einen Hosting-Anbieter](#fragen-an-einen-hosting-anbieter)

## Die Checkliste

Aus [2.1 Die Checkliste](../../../installation/02-voraussetzungen.md#21-die-checkliste):

| Bedarf | Minimum | Empfohlen |
|---|---|---|
| PHP | 8.0 | das neueste 8.x, das das System anbietet (8.5 wird unterstützt) |
| PHP für Velocity | 8.0 mit `sockets` (ab Velocity 0.0.4.54) | zusätzlich `pcntl` und `openssl` |
| `php.ini` | `memory_limit` 64M, `max_execution_time` 30, `date.timezone` gesetzt, `file_uploads` an, `allow_url_fopen` an | |
| Datenbank | SQLite 3 (kein Server) | in der offiziellen Reihenfolge: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL |
| Composer | 2.x | das aktuelle 2.x |
| Webserver | keiner (Velocity oder der eingebaute Server von PHP) | Velocity oder Apache/nginx mit PHP-FPM |
| Bildkonvertierung | GD oder `convert` von ImageMagick | beides |

Die Systemprüfung des Setup-Assistenten und der Schritt `SystemCheck` der Kommandozeilen-Installer testen all das und
geben für jeden Fehler die Lösung aus.

## Wählen, wie die Website ausgeliefert wird

Aus [8.1 Die Wahl in einer Tabelle](../../../installation/08-die-website-ausliefern.md#81-die-wahl-in-einer-tabelle):

| | Velocity | FrankenPHP (über Velocity) | Apache + PHP-FPM | nginx + PHP-FPM |
|---|---|---|---|---|
| Rolle | empfohlen für jede Stufe | produktionsreife Alternative | traditionell | traditionell |
| PHP | 8.0 oder neuer | in das Binary eingebaut | 8.0 oder neuer | 8.0 oder neuer |
| Eigene HTTPS-Zertifikate | ja, einschließlich ACME | ja | braucht `mod_ssl` und ein Zertifikatswerkzeug | braucht ein Zertifikatswerkzeug |
| Antwort-Cache vor PHP | ja | noch nicht | nein | nein |
| Geeignet für Shared Hosting | meist nicht (lang laufender Prozess) | meist nicht | ja | selten |

Also:

- **Ein Server oder VPS, den Sie kontrollieren:** Velocity
  ([Exponential Velocity](../../../../eng-us/features/6.0/velocity-web-server.md)).
- **Shared Hosting:** Apache mit PHP-FPM und den mitgelieferten Regeln aus `.htaccess_root`
  ([8.5 Apache mit PHP-FPM](../../../installation/08-die-website-ausliefern.md#85-apache-mit-php-fpm)).
- **Ein nginx, den Sie bereits betreiben:**
  [8.6 nginx mit PHP-FPM](../../../installation/08-die-website-ausliefern.md#86-nginx-mit-php-fpm).
- **Nur PHP 8.0 verfügbar:** jede der Möglichkeiten; Velocity läuft ab seinem Release 0.0.4.54 auch auf PHP 8.0
  ([PHP 8.0 und Red Hat Enterprise Linux](../../../installation/02-voraussetzungen.md#php-80-und-red-hat-enterprise-linux)).

## Die Datenbank wählen

| Website | Datenbank |
|---|---|
| Kleine Website, ein Server, wenige Redakteure | SQLite 3: nichts zu installieren, eine Datei in `var/storage/sqlite3/` |
| Website mit vielen Redakteuren oder wachsender Menge an Inhalten | PostgreSQL oder MariaDB/MySQL, wenn Ihr Hosting nur das anbietet |
| Hosting, das MongoDB oder Oracle anbietet | unterstützt; siehe die Hinweise pro Engine |

Einzelheiten, Grenzen und die Reihenfolge der Unterstützung: [Datenbankunterstützung](../../datenbankunterstuetzung.md),
[Datenbanken](../../../installation/09-datenbanken.md).

## Was die Website sonst noch braucht

| Bedarf | Warum | Lesen |
|---|---|---|
| Beschreibbare Verzeichnisse `var/` und `settings/` für den Benutzer des Webservers | Caches, Uploads, Logs, die SQLite-Datei | [8.7 Dateirechte und Eigentümer](../../../installation/08-die-website-ausliefern.md#87-dateiberechtigungen-und-eigentümer) |
| Ein Cronjob alle paar Minuten | `runcronjobs.php` führt die periodischen Jobs aus: Benachrichtigungen, Linkprüfungen, Aufräumen | [10.3 Cronjobs](../../../installation/10-nach-der-installation.md#103-cronjobs) |
| Ausgehende Mail | Formulare, Registrierung, Zurücksetzen von Passwörtern, Newsletter | [10.4 Mail](../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand) |
| HTTPS | jede öffentliche Website; Velocity kann Zertifikate selbst beziehen | [Velocity: HTTPS-Zertifikate](../../../../eng-us/features/6.0/velocity-https-certificates.md) |
| Backups der Datenbank, von `var/storage` und Ihren Einstellungen | Wiederherstellung nach einem Fehler | [Backups und Wiederherstellungen](backups-und-wiederherstellungen.md) |
| Speicherplatz für Medien | Bilder und Dateien wachsen mit der Website | [Bilder und Bildvarianten](bilder-und-bildvarianten.md) |
| Optional: ein Solr-Server | Suche für große Websites mit `ezfind` | [Suche mit ezfind und Solr](suche-mit-ezfind-und-solr.md) |

## Fragen an einen Hosting-Anbieter

Kopieren und senden:

- [ ] Welche PHP-Versionen bieten Sie an, und kann ich pro Website das neueste 8.x wählen?
- [ ] Sind die PHP-Erweiterungen aus [2.3 PHP-Erweiterungen](../../../installation/02-voraussetzungen.md#23-php-extensions)
  installiert?
- [ ] Darf ich einen lang laufenden Prozess betreiben (für Velocity) oder nur PHP-FPM hinter Ihrem Webserver?
- [ ] Welche Datenbanken bieten Sie an, und darf ich einen Datenbankbenutzer nur für die Website anlegen?
- [ ] Darf ich einen Crontab-Eintrag einrichten, der alle fünf Minuten läuft?
- [ ] Erlauben Sie ausgehende Mail, und von welchen Absenderdomains?
- [ ] Bekomme ich Shell-Zugang (SSH), um Composer und die Konsole auszuführen?
- [ ] Wie werden Backups erstellt, wie lange werden sie aufbewahrt, und wie stelle ich eines wieder her?
- [ ] Kann ich mein eigenes HTTPS-Zertifikat verwenden, oder stellen Sie eines bereit?
- [ ] Ist ImageMagick installiert?

> **Hinweis**
> 7x bietet Hosting für Exponential an; was es umfasst, steht auf der Website von 7x, nicht in dieser Hilfe
> ([Wer ist 7x?](../../wer-ist-7x.md)).

## Verwandte Seiten

- [Eine Installationsmethode wählen](eine-installationsmethode-waehlen.md)
- [Livegang: Hosts und HTTPS](livegang-hosts-und-https.md)
- [Unterstützte Betriebssysteme](../../betriebssystemunterstuetzung.md)
- [Hosting, Datenbank und Betriebssystem wählen](../../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
