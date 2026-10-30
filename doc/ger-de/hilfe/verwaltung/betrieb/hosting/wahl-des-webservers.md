---
title: Webserver für Websites des öffentlichen Sektors
audience: government, hosting, admin
category: public-sector
order: 60
summary: Für IT-Teams, die entscheiden, was eine Exponential-Website in einer öffentlichen Stelle ausliefert. Velocity, FrankenPHP sowie Apache oder nginx mit PHP-FPM im Vergleich, die Fragen, die zwischen ihnen entscheiden, und wie Sie die Wahl an die Liste zugelassener Software Ihrer Organisation anpassen.
keywords: Webserver, Velocity, qbix, FrankenPHP, Apache, nginx, PHP-FPM, LiteSpeed, IIS, zugelassene Software, Standardplattform, HTTPS, Antwort-Cache, Entscheidung, web server, approved software, standard platform, response cache, decision
type: help
---
# Webserver für Websites des öffentlichen Sektors

Diese Seite richtet sich an Infrastruktur- und Anwendungsteams in öffentlichen Stellen, die wählen, was eine
Exponential-Website ausliefert. Sie beantwortet, welche Server und Engines das Projekt unterstützt, was jede für den
Betrieb bedeutet und wie Sie wählen, wenn Ihre Organisation eine Liste zugelassener Serversoftware führt. Die Fakten
stammen aus [Bereitstellen](../../../../anleitungen/bereitstellung.md), [Kapitel 8: Die Website ausliefern](../../../../installation/08-die-website-ausliefern.md)
und der Hosting-Seite [Einen Webserver wählen](../../../hosting/server/einen-webserver-waehlen.md).

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../../../beispiele/webserver/nginx.md), [LiteSpeed](../../../../beispiele/webserver/litespeed.md), [Caddy](../../../../beispiele/webserver/caddy.md), [IIS](../../../../beispiele/webserver/iis.md),
> [Velocity](../../../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../../hosting/control-panels/plesk.md), [cPanel](../../../hosting/control-panels/cpanel.md), [DirectAdmin](../../../hosting/control-panels/directadmin.md), [ISPConfig](../../../hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../../../anleitungen/seiten-cache-mit-litespeed.md).

## Inhalt

1. [Die drei Aufbauten](#die-drei-aufbauten)
2. [Die Wahl in einer Tabelle](#die-wahl-in-einer-tabelle)
3. [Fragen, die entscheiden](#fragen-die-entscheiden)
4. [Listen zugelassener Software](#listen-zugelassener-software)
5. [Zwei nebeneinander betreiben](#zwei-nebeneinander-betreiben)
6. [Checkliste](#checkliste)

## Die drei Aufbauten

| Aufbau | Wählen Sie ihn, wenn |
|---|---|
| Apache (oder nginx) mit PHP-FPM | der Server bereits einen Webserver hat oder der Standard Ihrer Organisation ein klassischer Webserver ist |
| Exponential Velocity (die Engine `qbix`) | Sie die schnellste Engine, dauerhafte Worker, einen eingebauten Antwort-Cache und keinen zu konfigurierenden Webserver wollen |
| FrankenPHP | Sie eine einzige geprüfte Binärdatei mit TLS und HTTP/2 wollen und kein PHP auf der Maschine pflegen möchten |

Velocity ist die empfohlene Engine für jede Phase; FrankenPHP ist die produktionsreife Alternative
([Bereitstellen](../../../../anleitungen/bereitstellung.md)). Die Aufbauten können auf verschiedenen Ports nebeneinander laufen,
sodass das Ausprobieren des einen nie die anderen lahmlegt.

## Die Wahl in einer Tabelle

Aus [Einen Webserver wählen](../../../hosting/server/einen-webserver-waehlen.md):

| | Velocity | FrankenPHP | Apache + PHP-FPM | nginx + PHP-FPM |
|---|---|---|---|---|
| PHP | 8.0 oder neuer | in die Binärdatei eingebaut | 8.0 oder neuer | 8.0 oder neuer |
| Separater Webserver nötig | nein | nein (Caddy ist eingebaut) | ja | ja |
| Eigenes HTTPS und Zertifikate | ja, einschließlich ACME | ja | nein (`mod_ssl` und ein Zertifikatswerkzeug) | nein |
| Anwendung bleibt zwischen Anfragen im Arbeitsspeicher | ja | nein | nein | nein |
| Antwort-Cache vor PHP | ja | noch nicht | nein | nein |
| Rewrite-Regeln | eingebaut | erzeugt | `.htaccess_root` | von Ihnen geschrieben |

Der eingebaute Server von PHP ist nur für die Entwicklung gedacht: Er hat kein TLS und darf niemals Besuchern
gegenüberstehen.

## Fragen, die entscheiden

| Frage | Wenn die Antwort lautet | Dann |
|---|---|---|
| Erlaubt Ihre Organisation einen langlebigen Anwendungsprozess auf ihren Servern? | nein | Apache oder nginx mit PHP-FPM |
| Hat die Maschine PHP 8.0 (zum Beispiel das Standard-PHP von RHEL 9)? | ja | jede Engine; Velocity läuft ab seinem Release 0.0.4.54 auf 8.0 |
| Muss die Webschicht auf Ihrer Liste zugelassener Software stehen? | ja | siehe den nächsten Abschnitt |
| Terminiert ein zentrales Team TLS auf einem Load Balancer oder Reverse Proxy? | ja | jede Engine dahinter, mit gesetzten vertrauenswürdigen Proxys ([Mehrere Webserver hinter einem Load Balancer](../verfuegbarkeit/mehrere-webserver-hinter-einem-load-balancer.md)) |
| Machen anonyme Besucher den Großteil des Verkehrs aus? | ja | der Antwort-Cache von Velocity beantwortet sie ohne PHP ([Antwort-Cache von Velocity und HTTP-Cache](../verfuegbarkeit/velocity-antwortcache-und-http-cache.md)) |

## Listen zugelassener Software

Viele öffentliche Stellen betreiben nur Serversoftware, die eine interne Bewertung bestanden hat. Praktische Folgen:

- **Apache und nginx** stehen bereits auf den meisten solcher Listen. Die Regeln, die Exponential braucht, stehen in
  `.htaccess_root`; für nginx sind sie Regel für Regel unter [nginx mit PHP-FPM](../../../hosting/server/nginx-mit-php-fpm.md)
  übersetzt.
- **Velocity** ist ein PHP-Anwendungsserver, der mit Exponential ausgeliefert wird, freie Software wie der Rest. Reichen
  Sie ihn als Teil der Anwendung mit seiner Dokumentation zur Bewertung ein:
  [Velocity-Webserver](../../../../funktionen/6.0/velocity-webserver.md) und
  [Velocity für Teams im öffentlichen Sektor](velocity-fuer-teams-im-oeffentlichen-sektor.md).
- **FrankenPHP** wird als festgelegtes Release heruntergeladen, dessen SHA-256-Prüfsumme vor dem Start geprüft wird
  ([Bereitstellen](../../../../anleitungen/bereitstellung.md)); `install --engine=frankenphp --check` berechnet den Hash einer
  installierten Binärdatei erneut. Beachten Sie, dass sein eingebettetes PHP keine `php.ini` der Maschine liest
  ([FrankenPHP](../../../hosting/server/frankenphp.md)).
- **Andere Server** (LiteSpeed, Caddy mit PHP-FPM, IIS, lighttpd) liegen in Stufe 3, voraussichtlich funktionsfähig
  ([Unterstützte Betriebssysteme](../../../betriebssystemunterstuetzung.md)). Ist Ihr Standard einer davon, dokumentieren Sie das Risiko.

Was Sie auch wählen, behalten Sie die Sicherheitsregeln der ausgelieferten Konfiguration bei. Entfernen Sie keine
Regeln aus `.htaccess`, um etwas zum Laufen zu bringen ([Bereitstellen](../../../../anleitungen/bereitstellung.md)).

## Zwei nebeneinander betreiben

Während einer Migration oder während einer laufenden Bewertung von Velocity können beide dieselbe Installation auf
verschiedenen Ports ausliefern: ein herkömmlicher Server für Besucher und Velocity auf einem Testport für Messungen.
Die Varianten sind unter [Velocity neben Apache oder nginx](../../../hosting/server/velocity-neben-apache-oder-nginx.md)
beschrieben. Laden Sie nach jeder PHP-Änderung das richtige PHP-FPM neu und starten Sie Velocity neu;
`exp:velocity deploy` erledigt beides in der richtigen Reihenfolge:

```bash
./console exp:velocity deploy --dry-run --allow-root-user
```

## Checkliste

- [ ] Der Aufbau ist gewählt und in Ihrer Entscheidungsdokumentation zum Hosting festgehalten.
- [ ] Er steht auf Ihrer Liste zugelassener Software, oder eine Bewertung läuft.
- [ ] Die PHP-Version passt zur Engine (8.0 oder neuer, Velocity eingeschlossen).
- [ ] HTTPS wird von der Engine, dem Webserver oder einem zentralen Proxy übernommen und ist dokumentiert.
- [ ] Der Ablauf der Bereitstellung nennt den richtigen PHP-FPM-Dienst oder Velocity.

## Verwandte Seiten

- [Hosting](README.md): Übersicht dieses Ordners
- [Apache mit PHP-FPM](../../../hosting/server/apache-mit-php-fpm.md), [Velocity für Hoster](../../../hosting/server/velocity-fuer-hoster.md), [FrankenPHP](../../../hosting/server/frankenphp.md)
- [Entscheidungsdokumentation zum Hosting](hosting-entscheidungsprotokoll.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
