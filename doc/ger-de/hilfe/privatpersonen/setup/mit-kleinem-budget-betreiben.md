---
title: Mit kleinem Budget betreiben
audience: individual, website-creator, customer
category: personal-sites
order: 1633
summary: Wie eine Person die Kosten und die beweglichen Teile einer Exponential-Website gering hält - keine Lizenzgebühr, SQLite statt eines Datenbankservers, ein kleiner Rechner, Velocity oder ein gängiger Webserver, Caching, wenige Erweiterungen und ein Arbeitsblatt für Ihre eigenen jährlichen Kosten ohne erfundene Preise.
keywords: Budget, Kosten, günstig, geringe Kosten, freie Software, keine Lizenzgebühr, SQLite, Velocity, Shared Hosting, VPS, kleiner Server, Caching, Erweiterungen, jährliche Kosten, Arbeitsblatt, Privatperson, budget, cost, low cost, no license fee, yearly cost, worksheet
type: help
---
# Mit kleinem Budget betreiben

Diese Seite ist für Privatpersonen, die ihre Website aus eigener Tasche bezahlen. Exponential selbst kostet nichts;
Geld kosten der Ort, an dem es läuft, seine Adresse und Ihre Zeit. Diese Seite zeigt, wo sich sparen lässt. Sie
nennt keine Preise: Tragen Sie Ihre eigenen im Arbeitsblatt am Ende ein.

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

## Inhalt

1. [Wofür Sie nie bezahlen](#wofür-sie-nie-bezahlen)
2. [Wofür Sie bezahlen](#wofür-sie-bezahlen)
3. [Weniger bewegliche Teile](#weniger-bewegliche-teile)
4. [SQLite: kein Datenbankserver](#sqlite-kein-datenbankserver)
5. [Velocity oder ein gängiger Webserver](#velocity-oder-ein-gängiger-webserver)
6. [Schlank bleiben](#schlank-bleiben)
7. [Ihr Arbeitsblatt für die jährlichen Kosten](#ihr-arbeitsblatt-für-die-jährlichen-kosten)

## Wofür Sie nie bezahlen

- **Eine Lizenzgebühr.** Exponential ist freie Software unter der GNU GPL, Version 2 oder später
  ([Lizenzierung für Käufer](../../geschaeft/lizenzen-fuer-kaeufer.md)).
- **Mitgelieferte Erweiterungen.** Alles in der `composer.json` im Wurzelverzeichnis ist inbegriffen.
- **Die Dokumentation**, die unter der GNU FDL frei ist ([Freie Dokumentation](../../freie-dokumentation.md)).

Siehe auch [Wofür Sie nicht bezahlen](../../unternehmen/kosten/wofuer-sie-nicht-bezahlen.md).

## Wofür Sie bezahlen

| Posten | Bezahlt an | Kann es null sein? |
|---|---|---|
| Hosting oder ein Server | Ein Hosting-Unternehmen | Nur auf einem Rechner, den Sie ohnehin betreiben, mit einer Verbindung, die Sie kontrollieren |
| Domainname | Einen Registrar, jährlich | Nein, wenn Sie einen eigenen Namen möchten |
| Zertifikat für HTTPS | Oft kostenlos (Let's Encrypt), manchmal beim Hoster enthalten | Meist ja |
| E-Mail-Zustellung | Ihren Hoster oder einen E-Mail-Anbieter | Oft im Hosting enthalten |
| Speicher für Sicherungen außer Haus | Einen Speicheranbieter oder eine eigene Festplatte | Ja, mit einer Festplatte zu Hause |
| KI-Anbieter, wenn Sie den Schreibassistenten nutzen | Den Anbieter | Ja, wenn Sie ihn nicht nutzen oder ein lokales Modell verwenden |
| Hilfe | Einen Partner oder Freiberufler | Ja, wenn Sie es selbst machen |

## Weniger bewegliche Teile

Jeder zusätzliche Dienst ist etwas, das bezahlt, aktualisiert und gesichert werden muss. Die kleinste
funktionierende Einrichtung ([Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md)):

| Teil | Minimum |
|---|---|
| PHP | 8.0 oder neuer, mit den erforderlichen Erweiterungen |
| Datenbank | SQLite 3, kein Server |
| Webserver | Mit Velocity keiner nötig; sonst Apache oder nginx mit PHP-FPM oder der Server des Hosters |
| Suche | Die eingebaute Suche; kein Solr |
| Cache-Server | Keiner; für eine kleine Website genügen die Datei-Caches |

## SQLite: kein Datenbankserver

SQLite 3 ist die Standarddatenbank und steht in der offiziellen Rangfolge an erster Stelle. Die ganze Datenbank ist
eine Datei unter `var/storage/sqlite3/`: kein Server zu betreiben, kein Konto anzulegen, Sicherungen sind eine Kopie,
die mit dem eigenen Sicherungsbefehl von SQLite erstellt wird
([SQLite für Hoster](../../hosting/grundlagen/sqlite-fuer-hoster.md), [SQLite-Datenbank](../../../funktionen/6.0/sqlite-datenbank.md)).
Sie passt zu einer Website, die von einem Rechner ausgeliefert wird, und das ist eine persönliche, Familien- oder
Vereinsseite. Wächst Ihre Website stark, kann sie später zu PostgreSQL umziehen
([Datenbankunterstützung](../../datenbankunterstuetzung.md)).

## Velocity oder ein gängiger Webserver

**Exponential Velocity** ist seit 6.0.15 Teil von Exponential und liefert die Website selbst aus, ohne Apache oder
nginx; es kann Zertifikate von Let's Encrypt selbst beziehen und verlängern
([Velocity ist Teil von Exponential 6.0.15](../../velocity-ist-teil-von-exponential-6-0-15.md),
[Velocity: HTTPS und Zertifikate](../../../funktionen/6.0/velocity-https-zertifikate.md)). Es braucht einen dauerhaft
laufenden Prozess, also einen kleinen eigenen Server, kein Shared Hosting.

Auf Shared Hosting erledigt Apache oder LiteSpeed mit `.htaccess` die Arbeit; die Cache-Korrekturen der Linie 6.0.15
machen die Website auch dort schnell ([Shared Hosting oder ein kleiner Server](shared-hosting-oder-ein-kleiner-server.md)).

## Schlank bleiben

- **Bilder**: Laden Sie sinnvolle Größen hoch; den Rest erledigen die Bildvarianten
  ([Bildvarianten für Portfolios](../portfolios/bildvarianten-fuer-portfolios.md)).
- **Caching**: Lassen Sie die mitgelieferten Caches eingeschaltet ([Performance und Caching](../../ersteller/website-ersteller/leistung-und-caching.md)).
- **Erweiterungen**: Aktivieren Sie nur, was Sie nutzen; jede ist Code, der aktuell gehalten werden muss.
- **Externe Dienste**: Jedes eingebettete Skript (Analyse, Video, Karten) kostet Privatsphäre und manchmal Geld.
- **Spam**: Blockieren Sie Bots früh, damit sie Ihre Datenbank und Ihr Postfach nicht füllen
  ([Grundlagen zu Spam und Sicherheit](../im-betrieb/grundlagen-zu-spam-und-sicherheit.md)).

## Ihr Arbeitsblatt für die jährlichen Kosten

Tragen Sie Ihre eigenen Zahlen ein; diese Seite nennt keine.

| Posten | Anbieter | Pro Jahr | Verlängerungsdatum |
|---|---|---|---|
| Hosting oder Server | | | |
| Domainname | | | |
| Zertifikat (falls nicht kostenlos) | | | |
| E-Mail (falls separat) | | | |
| Speicher für Sicherungen | | | |
| KI-Anbieter (optional) | | | |
| Hilfe, Stunden pro Jahr x Satz (optional) | | | |
| **Summe** | | | |
| Ihre Zeit: Stunden pro Monat für die Pflege | | | |

Tragen Sie die Verlängerungsdaten in Ihren Kalender ein: Eine abgelaufene Domain nimmt die Website und Ihre E-Mail
vom Netz.

## Verwandte Seiten

- [Einrichtung für Privatpersonen](README.md)
- [Shared Hosting oder ein kleiner Server](shared-hosting-oder-ein-kleiner-server.md)
- [Gesamtbetriebskosten](../../unternehmen/kosten/gesamtbetriebskosten.md)
- [Anforderungen auf einen Blick](../../hosting/grundlagen/anforderungen-auf-einen-blick.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
