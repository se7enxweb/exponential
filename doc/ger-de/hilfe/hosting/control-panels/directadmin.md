---
title: DirectAdmin
audience: hosting
category: hosting
order: 40
summary: Was die Quellen von Exponential über Hosts mit DirectAdmin sagen (Stufe 3, funktioniert erwartungsgemäß, in derselben Zeile wie cPanel), die Anforderungen von Exponential übersetzt in allgemeine Aufgaben im Panel, die Fragen, die Sie zu Ihren DirectAdmin-Servern beantworten müssen, und ein Testplan.
keywords: DirectAdmin, Stufe 3, Shared Hosting, PHP pro Konto, Apache, LiteSpeed, nginx, .htaccess, Setup-Assistent, Composer, Cron, Velocity, open_basedir, Testplan, tier 3, shared hosting, per-account PHP, setup wizard, test plan
type: help
---
# DirectAdmin

Diese Seite ist für Hosting-Anbieter, die Server mit DirectAdmin betreiben und Exponential darauf anbieten möchten. Sie beantwortet,
was die Quellen von Exponential über DirectAdmin sagen, welche Aufgaben im Panel eine Exponential-Website braucht und wie Sie die
Kombination testen, bevor Sie sie verkaufen.

> **Hinweis**
> Die Quellen von Exponential beschreiben keine Bildschirme, Menüs oder Dateiorte von DirectAdmin. Diese Seite nennt Funktionen des
> Panels allgemein; lesen Sie in der Dokumentation von DirectAdmin nach, wie Ihre Version jede Aufgabe erledigt.

## Inhalt

1. [Was die Quellen sagen](#was-die-quellen-sagen)
2. [Fragen zu Ihren DirectAdmin-Servern](#fragen-zu-ihren-directadmin-servern)
3. [Die Aufgaben im Panel für eine Exponential-Website](#die-aufgaben-im-panel-für-eine-exponential-website)
4. [Webserver auf dem Server](#webserver-auf-dem-server)
5. [Ein Testplan](#ein-testplan)
6. [Checkliste](#checkliste)

## Was die Quellen sagen

DirectAdmin erscheint in den Quellen an genau zwei Stellen, beide in [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md):

- In der Tabelle der Stufe 3, in einer Zeile gemeinsam mit cPanel: „EasyApache (EA-PHP 8.x), PHP pro Konto“ als Grund, warum die
  Zeile funktionieren sollte, und „Shared Hosting kann lang laufende Prozesse verbieten, sodass Velocity womöglich nicht erlaubt ist;
  nutzen Sie Apache oder LiteSpeed mit `.htaccess`“ als das, worauf zu achten ist. Der Punkt, der für DirectAdmin gilt, ist ein PHP 8.x
  pro Konto ([Stufe 3](../../betriebssystemunterstuetzung.md#stufe-3-voraussichtlich-lauffähig)).
- In „Container, Cloud und Hosting-Panels“: cPanel und DirectAdmin sind Stufe 3; auf Shared Hosting ohne Shell-Zugang ist der
  Setup-Assistent im Browser der Installer
  ([Container, Cloud und Hosting-Panels](../../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels)).

Stufe 3 ist eine begründete Erwartung, kein Testergebnis. Niemand im Projekt hat Exponential auf DirectAdmin laufen sehen.

## Fragen zu Ihren DirectAdmin-Servern

Beantworten Sie diese für Ihren eigenen Server-Aufbau, bevor Sie eine Produktbeschreibung schreiben:

| Frage | Warum sie für Exponential zählt |
|---|---|
| Welche PHP-Versionen 8.x kann ein Konto wählen, und sind `intl` und `xsl` eingebaut? | beide sind kritische Tests des Setup-Assistenten ([2.4](../../../installation/02-voraussetzungen.md#24-der-systemtest-des-setup-assistenten)) |
| Läuft PHP als eigener Benutzer des Kontos (PHP-FPM pro Konto oder Gleichwertiges)? | die Regel eines Benutzers für `var/` und die Abschottung zwischen Konten ([8.7.2](../../../installation/08-die-website-ausliefern.md#872-ein-benutzer-oder-eine-gruppe)) |
| Welcher Webserver liefert die Konten aus, und liest er `.htaccess`? | die Rewrite-Regeln werden als `.htaccess_root` mitgeliefert |
| Ist `open_basedir` pro Konto gesetzt? | der Assistent warnt nur, aber Werkzeuge, die nach Binärdateien des Systems suchen, sind betroffen ([ezupdate](../../../funktionen/6.0/erweiterungen/ezupdate.md)) |
| Welche Funktionen stehen in `disable_functions`? | die Seite Cronjobs braucht `proc_open`, `exec` und `posix_kill` ([Cronjobs](../../../anleitungen/cronjobs.md#2-bevor-sie-beginnen)) |
| Erhalten Konten eine Shell? | Composer und die Konsole brauchen eine; ohne sie installiert der Setup-Assistent |
| Auf welchen Datenbankservern können Konten Datenbanken anlegen? | die offizielle Reihenfolge und ihre praktischen Mindestwerte |

## Die Aufgaben im Panel für eine Exponential-Website

| Aufgabe | Was Exponential braucht | Details |
|---|---|---|
| PHP-Version | 8.0 oder später für Web und Kommandozeile, in beiden dieselben Erweiterungen | [PHP-Auswahl und FPM-Pools](php-auswahl-und-fpm-pools.md) |
| PHP-Einstellungen | `memory_limit` 64M oder mehr, `max_execution_time` 30 oder mehr, `date.timezone`, `file_uploads`, `allow_url_fopen`; Upload-Größen für die Dateien der Redakteure | [2.5](../../../installation/02-voraussetzungen.md#25-einstellungen-in-der-phpini) |
| Document Root | das Wurzelverzeichnis der Installation, wo `index.php` liegt | [8.5.1](../../../installation/08-die-website-ausliefern.md#851-schritt-für-schritt) |
| Rewrite-Regeln | `.htaccess` aus `.htaccess_root` kopiert | [13.2](../../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf) |
| Datenbank | in der offiziellen Reihenfolge: 1. SQLite 3 (kein Server) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL; Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich | [Datenbanken und Benutzer](datenbanken-und-benutzer.md) |
| Cron | `runcronjobs.php` mit dem PHP des Kontos, alle paar Minuten für `frequent` | [Cron und Shell-Zugang](cron-und-shell-zugang.md) |
| Zertifikat | HTTPS für jeden Namen der Website, Umleitung von HTTP | [TLS-Zertifikate in Panels](tls-zertifikate-in-panels.md) |

## Webserver auf dem Server

Die Quellen sagen nicht, welchen Webserver ein DirectAdmin-Server betreibt. Was sie über die Kandidaten sagen:

| Server | Stand in den Quellen |
|---|---|
| Apache httpd 2.4 mit PHP-FPM | Stufe 1; Regeln in `.htaccess_root` |
| nginx mit PHP-FPM | Stufe 2; der Server-Block ist abgeleitet, nicht mitgeliefert ([8.6](../../../installation/08-die-website-ausliefern.md#86-nginx-mit-php-fpm)) |
| LiteSpeed, OpenLiteSpeed | Stufe 3; liest die Rewrite-Regeln von Apache in den meisten Fällen |
| nginx vor Apache | Apache liest `.htaccess`; setzen Sie die Einstellungen für vertrauenswürdige Proxys wie bei jedem vorgeschalteten Proxy ([13.10](../../../installation/13-sicherheitshaertung.md#1310-hinter-einem-proxy-vertrauenswürdige-header)) |

Quelle: [Webserver](../../betriebssystemunterstuetzung.md#webserver). Wenn Konten nur von nginx ausgeliefert werden, kann ein Konto `.htaccess`
nicht nutzen; der abgeleitete Server-Block muss dann Teil der Serverkonfiguration sein, die Sie verwalten.

## Ein Testplan

| Schritt | Besteht, wenn |
|---|---|
| Frisches Konto, Setup-Assistent | jeder kritische Test besteht; Website und `/admin` antworten |
| `/settings/site.ini`, `/.env`, `/composer.json` von außen | 404 oder eine Exponential-Seite, nie die Datei |
| Ein Bild hochladen | Bild und Varianten werden ausgeliefert |
| Cron-Zeile aus dem Panel | der Lauf erscheint auf **Setup > Cronjobs** |
| Sichern und in ein zweites Konto wiederherstellen | die wiederhergestellte Website funktioniert |
| Zwei Konten auf einem Server | das PHP keines der Konten kann `settings/override` des anderen lesen |

Melden Sie einen erfolgreichen Lauf dem Projekt, damit die Stufe aufrücken kann ([Die vier Stufen](../../betriebssystemunterstuetzung.md#die-vier-stufen)).

## Checkliste

- [ ] Die Fragen oben für Ihren Server-Aufbau beantwortet und aufgeschrieben.
- [ ] PHP 8.x mit jeder erforderlichen Erweiterung; ein PHP-Benutzer pro Konto.
- [ ] Document Root, `.htaccess` und Datenbank bereit.
- [ ] Cron und Zertifikat eingerichtet.
- [ ] Der Testplan hat bestanden, einschließlich des Tests der Abschottung.

## Verwandte Seiten

- [Control Panels: Überblick](README.md), [cPanel](cpanel.md)
- [Shared Hosting](../modelle/shared-hosting.md), [Trennung von Mandanten](../sicherheit/trennung-von-mandanten.md)
- [Betriebssystemunterstützung](../../betriebssystemunterstuetzung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
