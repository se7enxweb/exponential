---
title: cPanel
audience: hosting
category: hosting
order: 30
summary: Was die Quellen von Exponential über Hosts mit cPanel sagen (Stufe 3, funktioniert erwartungsgemäß; PHP aus EasyApache pro Konto; Velocity womöglich nicht erlaubt) und ein vorsichtiger, allgemeiner Weg, dort ein Exponential-Konto einzurichten, mit einem Testplan, der die Erwartung in einen Beleg verwandelt.
keywords: cPanel, EasyApache, EA-PHP, PHP pro Konto, Stufe 3, Shared Hosting, Apache, LiteSpeed, .htaccess, Setup-Assistent, Composer, /opt/cpanel/composer/bin, ezupdate, Cron, Velocity, Testplan, per-account PHP, tier 3, shared hosting, setup wizard, test plan
type: help
---
# cPanel

Diese Seite ist für Hosting-Anbieter, die Server mit cPanel betreiben und Exponential darauf anbieten möchten. Sie beantwortet,
was die Quellen von Exponential über cPanel sagen, was das in der Praxis bedeutet und wie Sie es testen, bevor Sie es verkaufen.
Das Projekt hat Exponential nicht auf cPanel laufen sehen; diese Seite trennt daher sorgfältig, was die Quellen sagen, von
allgemeinen Ratschlägen.

> **Hinweis**
> Die Quellen von Exponential beschreiben keine Bildschirme oder Menüs von cPanel. Wo diese Seite eine Funktion des Panels nennt,
> tut sie es allgemein („die PHP-Auswahl des Panels“, „die Cron-Seite des Panels“); lesen Sie die Namen und Schritte Ihrer
> Version in der Dokumentation von cPanel nach.

## Inhalt

1. [Was die Quellen sagen](#was-die-quellen-sagen)
2. [Was es in der Praxis bedeutet](#was-es-in-der-praxis-bedeutet)
3. [Ein Konto einrichten (allgemein)](#ein-konto-einrichten-allgemein)
4. [Composer auf cPanel](#composer-auf-cpanel)
5. [Velocity auf cPanel](#velocity-auf-cpanel)
6. [Ein Testplan](#ein-testplan)
7. [Checkliste](#checkliste)

## Was die Quellen sagen

| Aussage | Quelle |
|---|---|
| Hosts mit cPanel sind **Stufe 3**, funktionieren erwartungsgemäß: eine begründete Erwartung, kein Testergebnis | [Stufe 3](../../betriebssystemunterstuetzung.md#stufe-3-voraussichtlich-lauffähig) |
| EasyApache (EA-PHP 8.x), PHP pro Konto | dieselbe Tabelle |
| Shared Hosting kann lang laufende Prozesse verbieten, sodass Velocity womöglich nicht erlaubt ist; nutzen Sie Apache oder LiteSpeed mit `.htaccess` | dieselbe Tabelle |
| Auf Shared Hosting ohne Shell-Zugang ist der Setup-Assistent im Browser der Installer | [Container, Cloud und Hosting-Panels](../../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels) |
| ezupdate sucht Composer unter anderem in `/opt/cpanel/composer/bin/` | [Einstellungen von ezupdate](../../../../en/us/features/6.0/extensions/ezupdate.md) |

Das ist alles, was die Quellen speziell über cPanel sagen.

## Was es in der Praxis bedeutet

Die Anforderungen von Exponential hängen nicht vom Panel ab. Ein cPanel-Konto, das Folgendes bietet, führt Exponential aus, nach
derselben Überlegung, die cPanel in Stufe 3 einordnet:

- PHP 8.0 oder später für das Konto, mit den erforderlichen Erweiterungen: `dom`, `libxml`, `simplexml`, `xsl`, `mbstring`,
  `iconv`, `intl`, `pcre`, `json`, `session`, `zlib`, eine Datenbankerweiterung, `gd` oder ImageMagick
  ([2.3 PHP-Erweiterungen](../../../installation/02-voraussetzungen.md#23-php-extensions)). `xsl` und `intl` sind kritische Tests des
  Setup-Assistenten und fehlen am häufigsten in einem Standard-Build von PHP.
- Die `php.ini`-Werte, die der Assistent prüft ([2.5](../../../installation/02-voraussetzungen.md#25-einstellungen-in-der-phpini)).
- Apache (oder LiteSpeed), das `.htaccess` liest, mit `mod_rewrite`.
- Eine Datenbank: 1. SQLite 3 braucht keine; sonst einer der Server in der offiziellen Reihenfolge, 2. PostgreSQL · 3. MongoDB ·
  4. MariaDB · 5. Oracle · 6. MySQL (Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich). Auf einem
  typischen cPanel-Server ist das der Server für MariaDB oder MySQL, den das Panel verwaltet.
- Ein Cronjob für `runcronjobs.php`.

## Ein Konto einrichten (allgemein)

Das Folgende ist ein allgemeiner Ratschlag, abgeleitet aus den Anforderungen von Exponential, nicht aus der Dokumentation von cPanel:

1. **Wählen Sie PHP 8.x für das Konto** in der PHP-Auswahl des Panels, und aktivieren Sie die erforderlichen Erweiterungen in den
   Einstellungen für Erweiterungen des Panels. Nutzen Sie dieselbe Version für die Kommandozeile, wenn das Konto eine Shell hat.
2. **Richten Sie den Document Root der Domain auf das Wurzelverzeichnis der Installation** (das Verzeichnis mit `index.php`).
3. **Kopieren Sie die Rewrite-Regeln**: `cp .htaccess_root .htaccess` (oder laden Sie `.htaccess_root` als `.htaccess` hoch)
   ([8.5.1](../../../installation/08-die-website-ausliefern.md#851-schritt-für-schritt)).
4. **Legen Sie die Datenbank und ihren Benutzer** im Panel an, wenn nicht SQLite. Für MariaDB oder MySQL nutzen Sie `utf8mb4`
   ([9.3](../../../installation/09-datenbanken.md#93-mysql-und-mariadb)). Seit 6.0.15 braucht der Assistent nur Rechte auf diese
   Datenbank ([Eine saubere Installation, die zu Shared Hosting passt](../../../../en/us/features/6.0/clean-install-defaults.md)).
5. **Laden Sie den Code mit `vendor/` hoch**, oder führen Sie Composer in einer Shell aus, wenn es eine gibt (nächster Abschnitt).
6. **Öffnen Sie den Setup-Assistenten** im Browser und folgen Sie seiner Systemprüfung ([Kapitel 5](../../../installation/05-setup-assistent.md)).
7. **Fügen Sie die Cron-Zeile** auf der Cron-Seite des Panels hinzu, mit dem PHP des Kontos ([Cron und Shell-Zugang](cron-und-shell-zugang.md)).
8. **Installieren Sie das Zertifikat** mit der Zertifikatsverwaltung des Panels und leiten Sie HTTP auf HTTPS um
   ([TLS-Zertifikate in Panels](tls-zertifikate-in-panels.md)).

> **Warnung**
> Löschen Sie keine Regeln aus `.htaccess`, damit etwas funktioniert. Die Regeln entscheiden, welche Dateien Apache ausliefern
> darf; alles andere geht an `index.php`, was `settings/*.ini` und die SQLite-Datenbank privat hält
> ([13.2](../../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf)).

## Composer auf cPanel

- Mit einer Shell: Führen Sie `composer install` mit der PHP-Version des Kontos aus, damit Composer Pakete für das PHP auflöst,
  das sie ausführen wird ([Kommandozeile und Web-PHP](../../../installation/02-voraussetzungen.md#php-auf-der-kommandozeile-und-im-web)).
- In der Administration: Die Erweiterung ezupdate durchsucht unter ihren Standardorten für Composer `/opt/cpanel/composer/bin/` und
  kann mit *Get Composer* ein per SHA-256 geprüftes `composer.phar` nach `var/ezupdate/` herunterladen. Aktualisieren und
  Installieren aus dem Browser sind aus, bis `[UpdateSettings] AllowUpdate` und `AllowInstall` eingeschaltet sind
  ([ezupdate](../../../../en/us/features/6.0/extensions/ezupdate.md)).
- Ohne beides: Bauen Sie den Baum mit `composer install` anderswo, auf derselben PHP-Version, und laden Sie ihn hoch.

## Velocity auf cPanel

Velocity ist ein lang laufender Prozess. Auf geteilten cPanel-Konten ist es womöglich nicht erlaubt; die Antwort der Quellen ist
Apache oder LiteSpeed mit `.htaccess`. Auf einem cPanel-Server, auf dem Sie als Hoster die Maschine kontrollieren, könnte Velocity
neben dem Apache des Panels laufen wie auf dem Plesk-Server des Projekts, aber diese Kombination ist in den Quellen nicht
beschrieben. Wenn Sie es versuchen, lesen Sie [Velocity neben Apache oder nginx](../server/velocity-neben-apache-oder-nginx.md) und
halten Sie die Regel eines Benutzers für `var/` ein ([8.7.2](../../../installation/08-die-website-ausliefern.md#872-ein-benutzer-oder-eine-gruppe)).

## Ein Testplan

Verwandeln Sie Stufe 3 auf Ihrer eigenen Plattform in einen Beleg, bevor Kunden davon abhängen:

| Schritt | Besteht, wenn |
|---|---|
| Mit dem Setup-Assistenten auf einem frischen Konto installieren | jeder kritische Test besteht; die Website und `/admin` antworten |
| `/settings/site.ini`, `/.env`, `/composer.json` anfragen | 404 oder eine Exponential-Seite, nie der Inhalt der Datei ([13.2](../../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf)) |
| Ein Bild in der Administration hochladen | das Bild und seine Varianten werden ausgeliefert |
| `runcronjobs.php` aus dem Cron des Panels ausführen | Ausgabe im Cron-Log, **Setup > Cronjobs** zeigt den Lauf |
| Sichern und auf einem zweiten Konto wiederherstellen | die wiederhergestellte Website funktioniert ([10.9](../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung)) |
| Eine Erweiterung aktualisieren (ezupdate oder Composer) | die Website funktioniert nach dem Update |

Wenn alles besteht, möchte das Projekt davon hören: Wenn Sie Exponential auf einem System der Stufe 3 betreiben, teilen Sie es dem
Projekt mit, und die Zeile rückt auf ([Die vier Stufen](../../betriebssystemunterstuetzung.md#die-vier-stufen)).

## Checkliste

- [ ] PHP 8.x mit `xsl`, `intl` und den anderen erforderlichen Erweiterungen für das Konto.
- [ ] Document Root im Wurzelverzeichnis der Installation; `.htaccess` aus `.htaccess_root`.
- [ ] Datenbank (oder SQLite) bereit; UTF-8.
- [ ] `vendor/` vorhanden; Weg für Composer entschieden.
- [ ] Cron-Zeile mit dem PHP des Kontos.
- [ ] Zertifikat und Umleitung auf HTTPS.
- [ ] Der Testplan hat auf Ihrer Plattform bestanden.

**Eine öffentliche Demo auf cPanel.** Setzen Sie `EXP_DEMO_MODE=1` nur für das Konto der Demo: `SetEnv EXP_DEMO_MODE 1` in der `.htaccess` der Website (Apache oder LiteSpeed) und `EXP_DEMO_MODE=1` in einer eigenen Zeile oben in den Cronjobs des Kontos. [Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md), [Beispiele für den Demomodus](../../../../en/us/examples/demo-mode/README.md).

## Verwandte Seiten

- [Control Panels: Überblick](README.md), [DirectAdmin](directadmin.md)
- [Shared Hosting](../modelle/shared-hosting.md)
- [Der Setup-Assistent auf Shared Hosting](../bereitstellung/der-setup-assistent-auf-shared-hosting.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
