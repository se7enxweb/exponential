---
title: Shared Hosting oder ein kleiner Server
audience: individual, website-creator, customer
category: personal-sites
order: 1634
summary: Wo die Exponential-Website einer einzelnen Person laufen sollte - Shared Hosting mit Apache oder LiteSpeed und dem Einrichtungsassistenten, ein kleiner virtueller Server mit Velocity oder PHP-FPM oder ein Rechner zu Hause - was laut den Quellen des Projekts wo funktioniert, was Sie einen Hoster fragen sollten und welche Arbeit Ihnen jede Wahl lässt.
keywords: Shared Hosting, VPS, virtueller Server, kleiner Server, Heimserver, Velocity, Apache, LiteSpeed, nginx, PHP-FPM, .htaccess, Cron, E-Mail, Shell, FTP, Fragen an den Hoster, Privatperson, shared hosting, virtual server, small server, home server, hosting questions
type: help
---
# Shared Hosting oder ein kleiner Server

Diese Seite ist für Privatpersonen, die entscheiden, wo ihre Website liegen wird. Es gibt drei realistische Orte;
jeder funktioniert, und jeder lässt Ihnen unterschiedliche Arbeit. Der Bereich für Hosting enthält die Einzelheiten
für Hoster ([Ein Hosting-Modell wählen](../../hosting/modelle/ein-hosting-modell-waehlen.md)).

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../../beispiele/webserver/nginx.md), [LiteSpeed](../../../beispiele/webserver/litespeed.md), [Caddy](../../../beispiele/webserver/caddy.md), [IIS](../../../beispiele/webserver/iis.md),
> [Velocity](../../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hosting/control-panels/plesk.md), [cPanel](../../hosting/control-panels/cpanel.md), [DirectAdmin](../../hosting/control-panels/directadmin.md), [ISPConfig](../../hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../../anleitungen/seiten-cache-mit-litespeed.md).

## Inhalt

1. [Die drei Orte](#die-drei-orte)
2. [Shared Hosting](#shared-hosting)
3. [Ein kleiner virtueller Server](#ein-kleiner-virtueller-server)
4. [Ein Rechner zu Hause](#ein-rechner-zu-hause)
5. [Fragen an jeden Hoster](#fragen-an-jeden-hoster)
6. [Später umziehen](#später-umziehen)

## Die drei Orte

| | Shared Hosting | Kleiner virtueller Server | Rechner zu Hause |
|---|---|---|---|
| Sie verwalten | Die Website | Die Website und das Betriebssystem | Alles, einschließlich der Verbindung |
| Shell-Zugang | Oft keiner | Ja | Ja |
| Velocity | Meist nicht erlaubt | Ja | Ja |
| Ausgeliefert durch | Apache oder LiteSpeed mit `.htaccess` | Velocity, oder Apache oder nginx mit PHP-FPM | Wie bei einem Server |
| Installieren mit | Dem Einrichtungsassistenten | `exp:install` oder dem Kickstarter | Wie bei einem Server |
| Updates des Systems | Der Hoster | Sie | Sie |
| Passt zu | Persönlichen Websites, kleinen Blogs, Familienseiten | Portfolios, Vereinen, allen, die Kontrolle möchten | Dem Lernen; kleinen privaten Familienseiten, mit Vorsicht |

## Shared Hosting

Die Quellen des Projekts sagen es deutlich: „Your hosting will not let you run a long-lived process. Shared hosting
usually will not. Apache or LiteSpeed with `.htaccess` is the answer there." (Ihr Hosting lässt Sie keinen dauerhaft
laufenden Prozess betreiben. Shared Hosting meist nicht. Apache oder LiteSpeed mit `.htaccess` ist dort die Antwort;
`README.md` im Wurzelverzeichnis, zitiert unter
[Shared Hosting](../../hosting/modelle/shared-hosting.md#was-die-quellen-sagen)). Was Sie wissen müssen:

- **Installieren Sie mit dem Einrichtungsassistenten**, per FTP und Browser ([Eine Installationsmethode wählen](eine-installationsmethode-waehlen.md)).
- **SQLite** macht ein Datenbankkonto überflüssig; oder nutzen Sie die Datenbank des Panels.
- **Cron**: Planen Sie `runcronjobs.php` auf der Cron-Seite des Panels mit derselben PHP-Version wie die Website ein;
  Newsletter und zeitgesteuertes Veröffentlichen hängen davon ab
  ([Shared Hosting, Cronjobs](../../hosting/modelle/shared-hosting.md#cronjobs)).
- **PHP-Einstellungen** pro Konto: Speicher, Upload-Größen, die Funktionen, die die Cronjob-Seite braucht
  ([PHP-Einstellungen für Exponential](../../hosting/grundlagen/php-einstellungen-fuer-exponential.md)).
- **Kein Velocity** in den meisten Fällen; mit den Cache-Korrekturen von 6.0.15 ist die Website trotzdem schnell.

## Ein kleiner virtueller Server

Ein virtueller privater Server gibt Ihnen einen ganzen (kleinen) Rechner:

- **Velocity** liefert die Website ohne Apache oder nginx aus und kümmert sich selbst um HTTPS-Zertifikate
  ([Die Website ausliefern, Velocity](../../../installation/08-die-website-ausliefern.md#83-exponential-velocity-der-empfohlene-weg)).
- **Oder** Apache oder nginx mit PHP-FPM, wenn Sie sich damit auskennen.
- **Sie** halten das Betriebssystem aktuell, die Firewall geschlossen, die Sicherungen am Laufen und die E-Mail
  funktionsfähig ([VPS-Hosting](../../hosting/modelle/vps-hosting.md)).
- Größe: Eine kleine Website braucht wenig; siehe
  [Arbeitsspeicher, CPU und Festplatte bemessen](../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md).

Wenn das Patchen eines Betriebssystems nach einer Pflicht klingt, die Sie auslassen werden, wählen Sie stattdessen
Shared Hosting oder Managed Hosting. Ein ungepatchter Server ist schlimmer als ein langsamerer.

## Ein Rechner zu Hause

Möglich, und gut zum Lernen. Für eine öffentliche Website bringt er Probleme mit sich, die Ihnen ein Hosting abnimmt:
eine wechselnde Adresse, ein Internetanbieter, der eingehende Verbindungen oder ausgehende E-Mail blockieren kann,
Strom- und Hardwareausfälle und Ihr Heimnetz, das dem Internet ausgesetzt ist. Eine Familienseite, die nur Verwandte
nutzen, ist der vernünftigste Fall; bewahren Sie Sicherungen trotzdem getrennt von diesem Rechner auf.

## Fragen an jeden Hoster

| Frage | Warum |
|---|---|
| Welche PHP-Versionen, und kann ich 8.x pro Website wählen? | PHP 8.0 oder neuer (PHP 7.4 ohne Support nutzbar) ist erforderlich |
| Sind `intl`, `mbstring`, `xsl`, `gd` oder ImageMagick, `sqlite3` verfügbar? | Erforderliche oder benötigte Erweiterungen ([PHP-Versionen und Erweiterungen](../../hosting/grundlagen/php-versionen-und-erweiterungen.md)) |
| Kann ich Cron alle paar Minuten ausführen? | Newsletter, zeitgesteuertes Veröffentlichen |
| Kann die Website E-Mails versenden, und zwar von meiner Domain? | Registrierung, Zurücksetzen des Passworts, Formulare, Newsletter |
| Gibt es Shell-Zugang? | Composer, Installation über die Konsole, Wartung |
| Sind dauerhaft laufende Prozesse erlaubt? | Velocity |
| Wie werden Sicherungen gemacht, und kann ich sie herunterladen? | Ihre eigene Kopie |
| Ist HTTPS enthalten? | Die Anmeldung muss verschlüsselt sein |

Mehr unter [Was Sie Ihren Hoster fragen sollten](../../kunden/gesund-halten/was-sie-ihren-hoster-fragen-sollten.md).

## Später umziehen

Auf Shared Hosting zu beginnen und später auf einen Server umzuziehen ist normal. Ein Umzug umfasst: den Code, die
Datenbank (bei SQLite eine Datei), `var/storage` und Ihre Einstellungen, danach die neue Adresse in den Einstellungen
und im DNS ([Sicherungen für eine Person](../im-betrieb/sicherungen-fuer-eine-person.md),
[Domainnamen und HTTPS](domainnamen-und-https.md)). Proben Sie ihn einmal mit einer Kopie.

## Verwandte Seiten

- [Einrichtung für Privatpersonen](README.md)
- [Shared Hosting](../../hosting/modelle/shared-hosting.md)
- [VPS-Hosting](../../hosting/modelle/vps-hosting.md)
- [Hosting für eine Website](../../ersteller/website-ersteller/hosting-fuer-eine-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
