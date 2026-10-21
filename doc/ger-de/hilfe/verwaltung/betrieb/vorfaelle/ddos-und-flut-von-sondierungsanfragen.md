---
title: DDoS und Fluten von Sondierungsanfragen
audience: government, admin, hosting
category: public-sector
order: 80
summary: Runbook für eine öffentliche Website unter einer Flut von Anfragen - Sondierungen mit security.log und der Fehlerübersicht von echter Last unterscheiden, was Exponential blockiert und was nicht, die Website mit Caches, Anfrageregeln und einem vorgeschalteten Schutz am Laufen halten und mit Ihrem Netzanbieter zusammenarbeiten.
keywords: DDoS, Denial of Service, Flut von Sondierungsanfragen, Bot-Verkehr, Scanner, security.log, Schwellenwert, 404-Rate, request-shield, Anfrageregeln, statischer Cache, Velocity-Antwortcache, Ratenbegrenzung, Vorfall, denial of service, probe flood, bot traffic, threshold, 404 rate, request rules, static cache, Velocity response cache, rate limiting, incident
type: help
---
# DDoS und Fluten von Sondierungsanfragen

Dieses Runbook richtet sich an die Administration, wenn eine öffentliche Website durch Anfragen verlangsamt oder
überlastet wird: ein automatischer Scanner, ein Botnetz oder ein Ansturm von Interesse nach einer öffentlichen
Ankündigung (das ist kein Angriff und darf nicht blockiert werden).

## Inhalt
1. [Was Exponential tut und was nicht](#was-exponential-tut-und-was-nicht)
2. [Den Verkehr ansehen](#den-verkehr-ansehen)
3. [Die Website am Laufen halten](#die-website-am-laufen-halten)
4. [Davor blockieren](#davor-blockieren)
5. [Große Angriffe](#große-angriffe)
6. [Verwandte Seiten](#verwandte-seiten)

## Was Exponential tut und was nicht

- Der Anfrageklassifizierer **klassifiziert** Sondierungen, Injektionen, Pfadmanipulationen, Scanner und 404-Häufungen
  in `security.log`; er **blockiert nichts** ([Anfrage-Logs](../../../../funktionen/6.0/anfrageprotokolle.md)).
- Anfrageregeln (`requestrules.ini`) können ausgewählte Anfragen frühzeitig beantworten, zum Beispiel mit „not found“
  ([Anfrageregeln](../../../../funktionen/6.0/anfrageregeln.md)).
- Ratenbegrenzung, Blockieren und Bandbreite sind Aufgabe dessen, was davor steht: request-shield, der Webserver, ein
  Reverse Proxy, ein CDN oder Ihr Netzanbieter
  ([Sicherheitshärtung, request-shield](../../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)).

## Den Verkehr ansehen

```bash
./console exp:logs --tail=var/log/security.log --lines=100
./console exp:logs --search='threshold/404-rate' --file=var/log/security.log
```

**Setup > Logdateien**, **Fehlerübersicht**, Log „Security (request classifier)“ gruppiert die Zeilen nach Kategorie und
Regel; `404.log` nach Grund gruppiert zeigt, was angefragt wird. Das Zugriffslog des Webservers (oder das von Velocity,
`var/vc/qbix/log/site-access.log`) zeigt die Anfrageraten je Client. Die maskierten Adressen in den Logs von
Exponential reichen aus, um Muster zu erkennen; Ihr Netzwerkteam hat die vollständigen Adressen in seinen eigenen Logs.

| Muster | Wahrscheinlich |
|---|---|
| Viele `probe/*` und 404 aus wenigen Netzen | ein Scanner; harmlos, wenn nichts mit 200 antwortet |
| `injection/*` mit Status 200 | genauer ansehen, gemeinsam mit einer Entwicklerin oder einem Entwickler |
| Echte Seiten, viele Clients, nach einer Ankündigung | echte Last: skalieren und cachen, nicht blockieren |
| Riesige Mengen von allem | ein Denial-of-Service-Angriff: Ihr Anbieter |

## Die Website am Laufen halten

- **Caches vor PHP**: Unter Velocity liefert der Antwortcache Seiten ohne PHP aus
  ([Velocity-Antwortcache](../../../../funktionen/6.0/velocity-antwort-cache.md)); mit dem statischen Cache beantwortet
  der Webserver öffentliche Seiten als Dateien ([Statischer Cache](../verfuegbarkeit/statischer-cache.md)).
- **Wärmen Sie** die meistbesuchten Seiten nach dem Leeren eines Caches vor:
  `php bin/php/preload.php --siteaccess=site --allow-root-user`.
- Der **Wartungsmodus** mit einer kurzen Meldung ist das letzte Mittel für eine Website, die die Last nicht bewältigt;
  er antwortet, bevor Einstellungen oder die Datenbank berührt werden.

## Davor blockieren

Geben Sie die Muster an diejenigen weiter, die die vorgeschaltete Ebene betreiben: Pfade, die nur Scanner anfragen,
User-Agents von Angriffswerkzeugen, Netze mit Fluten. Blockieren Sie am Rand, nicht in Exponential.

## Große Angriffe

Volumetrische Angriffe werden von Netzanbietern und ihren Schutzdiensten abgewehrt. Klären Sie vor einem Vorfall, wer
die Ansprechperson bei Ihrem Anbieter ist und welchen Schutz Ihr Vertrag umfasst; Verwaltungsnetze haben dafür oft einen
zentralen Dienst. Melden Sie den Angriff Ihrem Sicherheitsteam oder der nationalen Behörde, wie es Ihre Regeln
verlangen.

## Verwandte Seiten
- [Anfrage-Logs: 404 und Sicherheit](../ueberwachung/anfrageprotokolle-404-und-sicherheit.md)
- [Kapazitätsplanung](../verfuegbarkeit/kapazitaetsplanung.md)
- [Langsame Website (Hosting-Runbook)](../../../hosting/runbooks/langsame-website.md)
- [Request-shield und Anfrageregeln (Hosting)](../../../hosting/sicherheit/request-shield-und-request-regeln.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
