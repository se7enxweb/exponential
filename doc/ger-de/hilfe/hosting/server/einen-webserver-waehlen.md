---
title: Einen Webserver wählen
audience: hosting
category: hosting
order: 20
summary: Eine Entscheidungshilfe für Hoster - welchen Webserver oder welche Engine Sie für welchen Tarif anbieten, mit den Fakten, die es entscheiden (PHP-Version, dauerhaft laufende Prozesse, Trennung, HTTPS, Caching), und einem Entscheidungsprotokoll zum Kopieren.
keywords: Webserver, wählen, Entscheidung, Velocity, qbix, FrankenPHP, Apache, nginx, LiteSpeed, Caddy, IIS, PHP-FPM, Shared Hosting, VPS, Trennung, dauerhaft laufender Prozess, PHP 8.0, PHP 8.1, web server, choose, decision, shared hosting, isolation, long-running process
featured: true
type: help
---
# Einen Webserver wählen

Diese Seite ist für Produktmanager und Systemadministratoren bei Hosting-Unternehmen, die entscheiden müssen, was die Exponential-Websites
eines Tarifs ausliefert. Sie beantwortet: welche Server und Engines möglich sind, welche Fragen zwischen ihnen entscheiden, was jede Wahl
für den Betrieb bedeutet und wie Sie die Entscheidung festhalten. Die Fakten stammen aus
[Kapitel 8: Die Website ausliefern](../../../installation/08-die-website-ausliefern.md), [Unterstützung von Betriebssystemen](../../betriebssystemunterstuetzung.md)
und der `README.md` des Repositorys.

## Inhalt

1. [Die Wahl in einer Tabelle](#die-wahl-in-einer-tabelle)
2. [Fünf Fragen, die es entscheiden](#fünf-fragen-die-es-entscheiden)
3. [Nach Hosting-Modell](#nach-hosting-modell)
4. [Trennung zwischen Kunden](#trennung-zwischen-kunden)
5. [Was jede Wahl Sie im Betrieb kostet](#was-jede-wahl-sie-im-betrieb-kostet)
6. [Server mischen](#server-mischen)
7. [Ein Entscheidungsprotokoll zum Kopieren](#ein-entscheidungsprotokoll-zum-kopieren)
8. [Checkliste](#checkliste)

## Die Wahl in einer Tabelle

Aus [Kapitel 8, Die Wahl in einer Tabelle](../../../installation/08-die-website-ausliefern.md#81-die-wahl-in-einer-tabelle):

| | Exponential Velocity (`qbix`) | FrankenPHP (über Velocity) | Apache + PHP-FPM | nginx + PHP-FPM |
|---|---|---|---|---|
| Rolle | für jede Phase **empfohlen** | produktionsreife Alternative | klassisch | klassisch |
| PHP | 8.0 oder neuer (ab Velocity 0.0.4.54) | das in die Binärdatei eingebaute PHP | 8.0 oder neuer | 8.0 oder neuer |
| Eigener Webserver nötig | nein | nein (Caddy ist eingebaut) | ja | ja |
| Eigenes Werkzeug für HTTPS oder Zertifikate nötig | nein: eigene Zertifikate, selbstsignierter Rückfall, ACME | nein: Ihr Zertifikat oder ein selbstsigniertes | ja (`mod_ssl` plus certbot oder ein Panel) | ja |
| Anwendung zwischen Anfragen im Speicher gehalten | ja (persistente Worker) | nein (sauberer Zustand pro Anfrage) | nein | nein |
| Antwort-Cache vor PHP | ja | noch nicht | nein (nur die eigenen Caches des Kernels) | nein |
| Rewrite-Regeln | eingebaut | erzeugtes Caddyfile | `.htaccess_root` (`exp:setup:htaccess --apply`) | von Ihnen geschrieben |
| Passt zu Shared Hosting | meist nicht | meist nicht | ja | selten |

Andere Server, die eine URL auf `index.php` umschreiben können, funktionieren ebenfalls. Das README nennt LiteSpeed und OpenLiteSpeed,
Caddy, Microsoft IIS, lighttpd, H2O und Traefik (Letzteres meist vor einem anderen Server) und sagt deutlich, dass sie alle funktionieren
und Exponential keinen bestimmten verlangt. [Unterstützung von Betriebssystemen](../../betriebssystemunterstuetzung.md#webserver) ordnet LiteSpeed, Caddy
mit PHP-FPM, IIS und lighttpd in Stufe 3 ein: sollte funktionieren.

> **Hinweis**
> Der eingebaute Server von PHP (`exp:velocity start --engine=php`) ist nur deshalb die mitgelieferte Standard-Engine, weil er nichts
> Installiertes braucht. Er ist für die Entwicklung, hat kein TLS und darf nie Besuchern gegenüberstehen.

## Fünf Fragen, die es entscheiden

| Frage | Wenn die Antwort ... ist | Dann |
|---|---|---|
| Darf die Website eines Kunden in diesem Tarif einen langlebigen Prozess betreiben? | nein (typisches Shared Hosting) | Apache oder LiteSpeed mit `.htaccess`. Shared Hosting erlaubt das meist nicht (`README.md`) |
| Hat die Maschine PHP 8.0? | ja (das Standard-PHP von Red Hat Enterprise Linux 9 und seinen Nachbauten) | jede Engine; Velocity läuft ab seinem Release 0.0.4.54 auf 8.0, mit dem el9-RPM oder mit Composer ([Kapitel 2](../../../installation/02-voraussetzungen.md#php-80-und-red-hat-enterprise-linux)) |
| Müssen Kunden auf einer Maschine durch das Betriebssystem voneinander getrennt sein? | ja | ein Velocity-Server pro Website, jeder als eigener Benutzer, oder ein PHP-FPM-Pool pro Website |
| Betreiben Sie bereits ein abgestimmtes Apache oder nginx, das Sie behalten wollen? | ja | behalten Sie es; Velocity kann dahinter oder daneben stehen |
| Wollen Sie die schnellste Antwort für wiederholte Seiten, ohne einen Cache-Server hinzuzufügen? | ja | Velocity mit seinem Antwort-Cache |

Das sind die Bedingungen, die Kapitel 8 für einen klassischen Server nennt: wenn Ihr Hosting keine langlebigen Prozesse erlaubt, wenn Sie
bereits ein abgestimmtes Apache oder nginx betreiben, das Sie behalten wollen, wenn die Maschine nur PHP 8.0 hat oder wenn mehrere Kunden
auf einer Maschine durch das Betriebssystem voneinander getrennt sein müssen.

## Nach Hosting-Modell

| Hosting-Modell | Übliche Wahl | Warum |
|---|---|---|
| Shared Hosting mit einem Control Panel | Apache (oder LiteSpeed) mit PHP-FPM und `.htaccess` | keine langlebigen Prozesse von Kunden; die Regeln werden als `.htaccess_root` ausgeliefert |
| VPS oder dedizierter Server pro Kunde | Velocity | eine Website pro Maschine, root für die Ports 80 und 443 verfügbar |
| Mehrere Kunden auf einem VPS oder dedizierten Server | Velocity pro Website (eigener Benutzer, eigene Ports, ein Proxy davor) oder PHP-FPM-Pool pro Website | Trennung durch das Betriebssystem |
| Container | Velocity oder FrankenPHP im Container | ein Prozessbaum pro Website; für Velocity gibt es Images ([Pakete und Binärdateien von Velocity](../../../funktionen/6.0/velocity-pakete-und-binaerdateien.md)) |
| Windows-Hosting | IIS oder Apache mit FastCGI-PHP | `pcntl` und `posix` gibt es unter Windows nicht ([Unterstützung von Betriebssystemen](../../betriebssystemunterstuetzung.md#windows)) |

Die Modelle selbst werden auf [Ein Hosting-Modell wählen](../modelle/ein-hosting-modell-waehlen.md) verglichen.

## Trennung zwischen Kunden

Das README ist ausdrücklich: Ein einziger Serverprozess, der mehrere Websites ausliefert, ist eine Bequemlichkeit beim Routing, keine
Sicherheitsgrenze; der Prozess, der eine Website ausliefert, kann jede Datei lesen, die die anderen halten. Für einen Tarif, der mehrere
Kunden auf eine Maschine legt:

- **Velocity**: Betreiben Sie einen Velocity-Server pro Website, jeden mit `[ServerSettings] User` und `Group` auf den Benutzer dieses Kunden
  gesetzt, auf eigenen Ports, hinter einem Proxy. Siehe [Velocity neben Apache oder nginx](velocity-neben-apache-oder-nginx.md).
- **PHP-FPM**: Geben Sie jeder Website einen eigenen Pool, der als Benutzer der Website läuft. Siehe
  [Apache mit PHP-FPM](apache-mit-php-fpm.md) und [Benutzer und Pools pro Kunde](../sicherheit/benutzer-und-pools-pro-kunde.md).

Die Seite der Sicherheit steht auf [Trennung von Mandanten](../sicherheit/trennung-von-mandanten.md).

## Was jede Wahl Sie im Betrieb kostet

| | Velocity | FrankenPHP | Apache oder nginx mit PHP-FPM |
|---|---|---|---|
| Installation | `composer require se7enxweb/exponential-velocity`, dann `exp:velocity start` | `exp:velocity install` lädt die Binärdatei herunter und prüft sie | Pakete der Distribution oder das Panel |
| Nach einer Änderung einer PHP-Klasse oder INI | `exp:velocity deploy` (Neustart inbegriffen) | `exp:velocity deploy` (derselbe Befehl, `--engine=frankenphp`) | das PHP-FPM neu laden, das die Website ausliefert, oder `exp:velocity deploy --no-velocity` |
| Zertifikate | Velocity selbst, einschließlich ACME | Ihre Dateien oder selbstsigniert; von Ihnen erneuert | `mod_ssl` oder nginx plus certbot oder das Panel |
| Dienst beim Systemstart | eine systemd-Unit, die Sie schreiben (Kapitel 8 gibt eine abgeleitete), oder die eigenen Units der Engine | dasselbe | die Units der Distribution |
| Was zu überwachen ist | `exp:velocity status --json`, `/Q/health`, die Logs in `var/vc/qbix/` | `status`, die JSON-Logs von Caddy in `var/vc/frankenphp/log/` | die Logs des Servers und von PHP-FPM |

> **Warnung**
> Unter Velocity halten die Worker Klassen, INI-Werte und kompilierte Templates im Speicher. Eine geänderte Klasse oder Einstellung
> erreicht sie erst nach einem Neustart. Schulen Sie die Support-Mitarbeitenden auf `exp:velocity deploy`, bevor Sie Tarife mit Velocity
> verkaufen ([Kapitel 8, Eine Änderung ausrollen](../../../installation/08-die-website-ausliefern.md#8314-eine-änderung-bereitstellen-expvelocity-deploy)).

## Server mischen

Die Möglichkeiten schließen sich nicht aus. Eine häufige Form ist, dass Velocity die Website ausliefert, während Apache oder nginx
während einer Migration auf denselben Dateien bestehen bleibt; beide teilen dann `var/`, die Caches und die Sitzungen und müssen deshalb
als derselbe Benutzer schreiben. `exp:velocity deploy` bringt beide in einem Lauf auf den neuesten Stand, weil es auch das PHP-FPM neu
lädt, das die Website ausliefert. Details: [Velocity neben Apache oder nginx](velocity-neben-apache-oder-nginx.md).

## Ein Entscheidungsprotokoll zum Kopieren

```text
Plan name:                      <plan>
Server or engine:               Velocity (qbix) | FrankenPHP | Apache + PHP-FPM | nginx + PHP-FPM | other: ...
PHP version and source:         <version>, <distribution package | panel | binary>
Long-running processes allowed: yes | no
Isolation between customers:    one machine per customer | one Velocity per site | one FPM pool per site
TLS terminated by:              Velocity | FrankenPHP | Apache/nginx | load balancer or CDN
Response caching:               Velocity response cache | kernel caches only | proxy in front
Who restarts after a deploy:    <role>
Tested on staging on:           <date>, by <role>
```

## Checkliste

- [ ] Die Antwort des Tarifs auf die Frage, ob ein langlebiger Prozess laufen darf, ist aufgeschrieben.
- [ ] Die PHP-Version auf der Maschine ist 8.0 oder neuer, auch wo Velocity angeboten wird.
- [ ] Trennung zwischen Kunden entschieden: pro Maschine, pro Velocity-Instanz oder pro PHP-FPM-Pool.
- [ ] Die vier Prüfungen des Routings aus [Webserver und Engines](README.md#was-jede-einrichtung-gemeinsam-hat) bestehen.
- [ ] Der Support weiß, wie eine PHP-Änderung den Server erreicht (`exp:velocity deploy` oder das Neuladen von FPM).
- [ ] Ein Server der Stufe 3 wurde auf Staging getestet, bevor er verkauft wurde.

## Verwandte Seiten

- [Webserver und Engines](README.md)
- [Velocity für Hoster](velocity-fuer-hoster.md)
- [Apache mit PHP-FPM](apache-mit-php-fpm.md)
- [Ein Hosting-Modell wählen](../modelle/ein-hosting-modell-waehlen.md)
- [Hosting, Datenbank und Betriebssystem wählen](../../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md)
- [.htaccess und config.php von der Konsole einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md): welche Server `.htaccess` lesen
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
