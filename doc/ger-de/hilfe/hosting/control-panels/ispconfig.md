---
title: ISPConfig
audience: hosting
category: hosting
order: 50
summary: ISPConfig wird in den Quellen von Exponential nicht erwähnt und ist in keine Stufe eingeordnet. Diese Seite sagt das deutlich, listet auf, was ein mit ISPConfig verwalteter Server für eine Exponential-Website bereitstellen muss, ordnet jedem Bedarf eine allgemeine Aufgabe im Panel zu und gibt einen Testplan für Hoster, die die Kombination anbieten möchten.
keywords: ISPConfig, nicht getestet, keine Stufe, Hosting-Panel, PHP-FPM, Apache, nginx, .htaccess, Rewrite-Regeln, open_basedir, Cron, Datenbank, TLS, Testplan, Anforderungen, not tested, no tier, hosting panel, rewrite rules, test plan, requirements
type: help
---
# ISPConfig

Diese Seite ist für Hosting-Anbieter, deren Server mit ISPConfig verwaltet werden und die Exponential darauf anbieten möchten. Sie
beantwortet die erste Frage ehrlich: **Die Quellen von Exponential erwähnen ISPConfig überhaupt nicht.** Es ist in keine Stufe
eingeordnet, und nichts hier stammt aus einem Test. Was die Seite Ihnen geben kann, ist die Liste dessen, was eine
Exponential-Website braucht, übersetzt in die Aufgaben, die jedes Hosting-Panel erledigt, und einen Plan, die Kombination selbst zu
testen.

> **Hinweis**
> Nichts auf dieser Seite beschreibt die Bildschirme, Optionen oder Dateien von ISPConfig. Wie ISPConfig jede Aufgabe unten erledigt,
> lesen Sie in der Dokumentation von ISPConfig nach.

## Inhalt

1. [Stand in den Quellen](#stand-in-den-quellen)
2. [Warum es trotzdem funktionieren kann](#warum-es-trotzdem-funktionieren-kann)
3. [Was der Server bereitstellen muss](#was-der-server-bereitstellen-muss)
4. [Zwei Dinge, die Sie zuerst prüfen](#zwei-dinge-die-sie-zuerst-prüfen)
5. [Ein Testplan](#ein-testplan)
6. [Checkliste](#checkliste)

## Stand in den Quellen

| Frage | Antwort |
|---|---|
| Wird ISPConfig in der Dokumentation oder im Code genannt? | nein |
| Welche Stufe? | keine: es ist nicht eingeordnet |
| Hat das Projekt Exponential darauf betrieben? | nicht nach den Aufzeichnungen der Quellen |

Die Stufen sind in [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md#die-vier-stufen) definiert. Eingeordnete Panels: Plesk (Stufe 1
auf dem Entwicklungsserver), cPanel und DirectAdmin (Stufe 3) ([Control Panels: Überblick](README.md#panels-in-den-quellen)).

## Warum es trotzdem funktionieren kann

Die Überlegung, die cPanel und DirectAdmin in Stufe 3 einordnet, ist, dass Exponential von einem Panel selbst nichts braucht: Es
besteht aus reinen PHP-Dateien und einer Datenbank, hat keinen eigenen kompilierten Code und läuft auf fast jedem Betriebssystem,
das PHP 8.0 oder später und einen Webserver hat ([Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md)). Jeder Server, dessen Panel die
Punkte unten bereitstellen kann, auf einem unterstützten Betriebssystem, erfüllt die Anforderungen. Das ist eine Schlussfolgerung,
kein Testergebnis: Behandeln Sie ISPConfig höchstens als Stufe 3, bis Sie es getestet haben.

## Was der Server bereitstellen muss

| Bedarf | Wert | Aufgabe im Panel (allgemein) |
|---|---|---|
| Betriebssystem | eine Distribution der Stufe 1 oder 2, zum Beispiel Debian 12 oder 13, Ubuntu 22.04 oder 24.04, AlmaLinux 9 | die Basisinstallation unter dem Panel ([Stufe 2](../../betriebssystemunterstuetzung.md#stufe-2-bekanntermaßen-lauffähig)) |
| PHP | 8.0 oder später (auch für Velocity), mit `dom`, `libxml`, `simplexml`, `xsl`, `mbstring`, `iconv`, `intl`, `pcre`, `json`, `session`, `zlib`, einer Datenbankerweiterung, `gd` oder ImageMagick | PHP-Version und Module pro Website ([2.3](../../../installation/02-voraussetzungen.md#23-php-extensions)) |
| PHP für die Kommandozeile | dieselbe Version und dieselben Erweiterungen wie die der Website | für Composer, die Konsole und Cron ([Kommandozeile und Web-PHP](../../../installation/02-voraussetzungen.md#php-auf-der-kommandozeile-und-im-web)) |
| `php.ini` | `memory_limit` 64M oder mehr, `max_execution_time` 30 oder mehr, `date.timezone`, `file_uploads` an, `allow_url_fopen` an | PHP-Einstellungen pro Website ([2.5](../../../installation/02-voraussetzungen.md#25-einstellungen-in-der-phpini)) |
| PHP-Benutzer | PHP läuft als eigener Benutzer der Website | PHP-FPM-Pool pro Website ([8.7.2](../../../installation/08-die-website-ausliefern.md#872-ein-benutzer-oder-eine-gruppe)) |
| Document Root | das Wurzelverzeichnis der Installation (mit `index.php`) | der Document Root der Website |
| Rewrite-Regeln | Apache: `.htaccess` aus `.htaccess_root`; nginx: der abgeleitete Server-Block | die Webserver-Direktiven der Website ([8.5](../../../installation/08-die-website-ausliefern.md#85-apache-mit-php-fpm), [8.6](../../../installation/08-die-website-ausliefern.md#86-nginx-mit-php-fpm)) |
| Datenbank | in der offiziellen Reihenfolge: 1. SQLite 3 (kein Server) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL; Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich | eine Datenbank und ein Benutzer, UTF-8 ([Datenbanken und Benutzer](datenbanken-und-benutzer.md)) |
| Geplante Jobs | `runcronjobs.php` mit dem PHP der Website | die Cronjobs der Website ([Cron und Shell-Zugang](cron-und-shell-zugang.md)) |
| HTTPS | ein Zertifikat für jeden Namen der Website | die Zertifikatseinstellungen der Website ([TLS-Zertifikate in Panels](tls-zertifikate-in-panels.md)) |

## Zwei Dinge, die Sie zuerst prüfen

**1. Welcher Webserver die Website ausliefert und wie seine Regeln gesetzt werden.** Unter Apache funktioniert die mitgelieferte
`.htaccess`, wenn `.htaccess`-Dateien gelesen werden (`AllowOverride`) und `mod_rewrite` an ist. Unter nginx gibt es keine
`.htaccess`; die Regeln von Exponential müssen in die Konfiguration des Servers. Das Projekt liefert keine nginx-Datei mit: Kapitel 8
nennt einen Server-Block, der abgeleitet, nicht mitgeliefert ist, und bittet Sie, ihn auf Ihrer Website zu testen, bevor Sie sich
darauf verlassen ([8.6](../../../installation/08-die-website-ausliefern.md#86-nginx-mit-php-fpm)).

> **Warnung**
> Liefern Sie die Installation nie mit einer allgemeinen Konfiguration wie dem nginx-Muster „Datei ausliefern, wenn sie existiert,
> sonst `index.php`“ aus: Solche Konfigurationen liefern jede existierende Datei aus, einschließlich `settings/*.ini` und der
> SQLite-Datenbank ([13.2](../../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf)). Die
> Standardvorlage eines Panels für PHP-Websites ist genau eine solche Konfiguration, es sei denn, Sie fügen die Regeln von
> Exponential hinzu.

**2. `open_basedir` und `disable_functions`.** Panels schränken beides oft pro Website ein. Der Setup-Assistent warnt nur bei
`open_basedir`; die Seite Cronjobs braucht `proc_open`, `exec` und `posix_kill` für ihre Funktionen zum Starten, zur Crontab und zum
Anhalten ([Cronjobs, Bevor Sie beginnen](../../../anleitungen/cronjobs.md#2-bevor-sie-beginnen)); ezupdate sucht Composer zuerst unterhalb
des Wurzelverzeichnisses der Installation, weil `open_basedir` Systemordner verbergen kann
([ezupdate](../../../funktionen/6.0/erweiterungen/ezupdate.md)).

## Ein Testplan

| Schritt | Besteht, wenn |
|---|---|
| Auf einer frischen Website installieren (Setup-Assistent oder `exp:install` mit einer Shell) | jeder kritische Test besteht; Website und `/admin` antworten |
| `/settings/site.ini`, `/.env`, `/composer.json`, `/kernel/classes/ezini.php` anfragen | 404 oder eine Exponential-Seite, nie die Datei ([13.2](../../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf)) |
| Ein Bild hochladen, ansehen | Bild und Varianten werden ausgeliefert |
| Cron-Zeile | der Lauf erscheint auf **Setup > Cronjobs** |
| PHP-Version im Panel ändern | die Website antwortet weiter; sowohl Web-PHP als auch PHP der Kommandozeile haben sich geändert |
| Sichern und auf einer zweiten Website wiederherstellen | die wiederhergestellte Website funktioniert |
| Zwei Websites auf dem Server | das PHP keiner der Websites kann `settings/override` der anderen lesen |

Wenn alles besteht, teilen Sie es dem Projekt mit, damit ISPConfig in eine Stufe eingeordnet werden kann
([Die vier Stufen](../../betriebssystemunterstuetzung.md#die-vier-stufen)).

## Checkliste

- [ ] Betriebssystem der Stufe 1 oder 2 unter dem Panel.
- [ ] PHP 8.x mit jeder erforderlichen Erweiterung, Web und Kommandozeile gleich, ein Benutzer pro Website.
- [ ] Rewrite-Regeln von Exponential eingerichtet (`.htaccess` oder der abgeleitete nginx-Block); Test auf offengelegte Dateien bestanden.
- [ ] Entscheidungen zu `open_basedir` und `disable_functions` dokumentiert.
- [ ] Datenbank, Cron und Zertifikat eingerichtet.
- [ ] Der Testplan hat bestanden; das Angebot ist als nicht von den eigenen Tests des Projekts abgedeckt dokumentiert.

## Verwandte Seiten

- [Control Panels: Überblick](README.md), [Webmin](webmin.md)
- [nginx mit PHP-FPM](../server/nginx-mit-php-fpm.md), [Apache mit PHP-FPM](../server/apache-mit-php-fpm.md)
- [Anforderungen auf einen Blick](../grundlagen/anforderungen-auf-einen-blick.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
