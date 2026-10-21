---
title: Zuverlässigkeit des App-Backends
audience: businesses, admin, app-creator
category: business-solutions
order: 100
summary: Wie ein Unternehmen die Exponential-Installation hinter seiner App, seinem Portal oder seiner Partnerintegration verfügbar hält - was die API unerreichbar machen kann, Health-Checks, Wartungsfenster, die mit 503 antworten, Ratenbegrenzungen, Caching, Skalierung, ablaufende Zugangsdaten und was die App tun soll, wenn das Backend nicht antwortet.
keywords: App-Backend, Zuverlässigkeit, Verfügbarkeit, REST API, Health-Check, Monitoring, Wartungsmodus, 503, Retry-After, Ratenbegrenzung, Velocity-Watchdog, Skalierung, Caching, Unternehmen, app backend, reliability, availability, health check, monitoring, maintenance mode, rate limit, Velocity watchdog, scaling, caching, business
type: help
---
# Zuverlässigkeit des App-Backends

Diese Seite ist für Inhaber und Produktverantwortliche, deren App, Kundenportal oder Partnerintegration von einer
Exponential-Installation abhängt, und für die Administratoren, die sie am Laufen halten. Wenn die Website langsam ist,
merken es Besucher; wenn die API hinter einer App ausfällt, sieht die App in der Tasche jedes Kunden kaputt aus. Diese
Seite listet auf, was das Backend unerreichbar machen kann, was Exponential gegen jedes Risiko bietet und was die App
selbst tun muss.

## Inhalt

1. [Was die API unerreichbar machen kann](#was-die-api-unerreichbar-machen-kann)
2. [Beobachten: Health-Checks](#beobachten-health-checks)
3. [Geplante Arbeit: Wartungsfenster](#geplante-arbeit-wartungsfenster)
4. [Last: Caching, Begrenzungen und Skalierung](#last-caching-begrenzungen-und-skalierung)
5. [Zugangsdaten, die nicht mehr funktionieren](#zugangsdaten-die-nicht-mehr-funktionieren)
6. [Was die App tun muss](#was-die-app-tun-muss)
7. [Checkliste zur Zuverlässigkeit eines App-Backends](#checkliste-zur-zuverlässigkeit-eines-app-backends)

## Was die API unerreichbar machen kann

| Ursache | Was hilft | Lesen |
|---|---|---|
| Der Serverprozess ist gestoppt | Der Watchdog von Velocity startet die Engine neu; Monitoring alarmiert | [Health-Checks und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md) |
| Geplante Wartung | Der Wartungsmodus antwortet mit 503 und `Retry-After` | unten |
| Zu viele Anfragen | Caching, Ratenbegrenzungen pro Schlüssel, mehr Kapazität | unten |
| Abgelaufene oder widerrufene Zugangsdaten | Lebensdauern der Schlüssel und die Übersicht der API-Schlüssel | unten |
| Eine geänderte Serverkonfiguration | Der Header `Authorization` muss PHP weiterhin erreichen | [REST API v1 und v2 für Anwendungen](../../../../eng-us/help/creators/app-creators/rest-api.md#configuration) |
| Eine Aktualisierung, die Antworten geändert hat | Release Notes; für die REST API blieben die Antworten in 6.0.15 „byte for byte the same“ (Byte für Byte gleich) | [REST API v1 und v2 für Unternehmen](rest-api-v1-und-v2-fuer-unternehmen.md#was-6015-für-bestehende-apps-geändert-hat) |

Der Bereich Zuverlässigkeit behandelt die Plattform als Ganzes ([Zuverlässigkeit](../zuverlaessigkeit/README.md)); diese
Seite ergänzt, was für Apps gilt.

## Beobachten: Health-Checks

Aus [Health-Checks und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md):

- Prüfen Sie von außen: die öffentliche Website und die Anmeldeseite der Administration, denn die Startseite „is often
  served from a cache ... so it can answer while the database is down“ (wird oft aus einem Cache ausgeliefert und kann
  daher antworten, während die Datenbank ausgefallen ist). Eine Seite, deren Inhalt Sie prüfen können, ist aussagekräftiger
  als ein Statuscode.
- Auf dem Server: `./console exp:systeminfo --json` beendet sich mit 1, wenn ein Health-Check fehlschlägt;
  Statusbefehle mit Exit-Codes eignen sich für automatische Prüfungen.
- Unter Velocity: Der Endpunkt `/Q/health` antwortet `{"status":"ok"}`, wenn der Serverprozess antwortet; „it does not
  render a page of the site, so keep the external page checks as well“ (er rendert keine Seite der Website, behalten Sie
  also auch die externen Seitenprüfungen). Der Watchdog startet die Engine nach einem Absturz neu und wartet nach drei
  fehlgeschlagenen Starts hintereinander länger.

Für eine App kommt eine Prüfung hinzu: eine **authentifizierte API-Anfrage** mit einem Monitoring-Schlüssel, der nur
einen kleinen öffentlichen Eintrag lesen darf, zum Beispiel `GET /api/ezp/v1/content/node/<id>`. Sie belegt REST-Routen,
Authentifizierung und Datenbank zusammen.

## Geplante Arbeit: Wartungsfenster

Während der Wartungsmodus eingeschaltet ist, antwortet die REST API (`index_rest.php`) wie die Website mit der
Wartungsseite und dem Status 503 ([Anleitung zum Wartungsmodus](../../../anleitungen/wartungsmodus.md)). Die Antworten
tragen `Retry-After` mit den Sekunden bis zum erwarteten Ende
([Geplante Wartung ist kein Ausfall](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md#geplante-wartung-ist-kein-ausfall)).

Für eine App bedeutet das: Behandeln Sie ein 503 mit `Retry-After` als „später wiederkommen“, zeigen Sie eine
freundliche Meldung und versuchen Sie es nach der angegebenen Zeit erneut. Kündigen Sie Wartungsfenster Partnern an,
die mit Ihnen integriert sind.

> **Hinweis**
> „Maintenance never ends by itself when the expected time passes“: Die Wartung endet nie von selbst, wenn die
> erwartete Zeit vorbei ist. Alarmieren Sie, wenn ein Fenster länger dauert als angekündigt.

## Last: Caching, Begrenzungen und Skalierung

| Hebel | Wirkung | Quelle |
|---|---|---|
| Der Antwortcache der REST API | Wiederholte Lesezugriffe aus dem Cache beantwortet; mit dem Tag content geleert | [Headless Exponential](../../../../eng-us/help/creators/app-creators/headless-exponential.md#caching-and-performance) |
| Caching in der App oder in einem CDN, beim Veröffentlichen geleert | Weniger Anfragen erreichen den Server | dieselbe |
| Blätterbare Listen mit Grenzen | Keine Anfrage holt alles | dieselbe; expservices blättert höchstens 200 Einträge |
| Ratenbegrenzung pro API-Schlüssel | Standardmäßig 120 Anfragen pro Schlüssel und Minute, in APCu gezählt; ohne APCu nicht durchgesetzt | [Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md#8-audit-und-grenzen) |
| Velocity und seine dauerhaften Worker | Die Anwendung bleibt zwischen Anfragen warm | [Anwendungen skalieren](../../../../eng-us/help/creators/app-creators/scaling-applications.md) |
| Mehrere Webserver | Horizontale Kapazität, mit gemeinsamem Speicher und einer Datenbank | [Anwendungen skalieren](../../../../eng-us/help/creators/app-creators/scaling-applications.md#several-web-servers) |

Bitten Sie Ihre Entwickler, vor dem Skalieren zu messen: Die Anleitung für App-Entwickler beginnt mit „Measure first“.

## Zugangsdaten, die nicht mehr funktionieren

Persönliche API-Schlüssel laufen ab (standardmäßig höchstens nach 365 Tagen) und enden, wenn ihr Eigentümer deaktiviert
wird; OAuth-Tokens laufen nach ihrer Lebensdauer ab (standardmäßig eine Stunde) und werden mit einem Refresh-Token
erneuert ([Apps und API-Schlüssel](apps-und-api-schluessel.md)). Ein vergessenes Ablaufdatum ist ein selbst verursachter
Ausfall:

- tragen Sie das Enddatum jedes Schlüssels in den Betriebskalender ein;
- prüfen Sie **Setup > oAuth-Verwaltung > API-Schlüssel** auf Schlüssel, die „expiring within seven days“ (innerhalb
  von sieben Tagen ablaufen);
- lassen Sie eine Integration nie unter dem Konto einer Person laufen, das deaktiviert werden kann, wenn sie geht.

## Was die App tun muss

Schreiben Sie das in die Anforderungen der App:

- [ ] Zwischengespeicherte Inhalte zeigen, wenn die API nicht antwortet, mit dem Hinweis, dass sie veraltet sein können.
- [ ] 503 mit `Retry-After` als geplante Pause behandeln; mit wachsenden Abständen erneut versuchen, nicht in einer
      engen Schleife.
- [ ] 401 als „Zugangsdaten abgelaufen oder widerrufen“ behandeln: das Token erneuern oder den Benutzer bitten, sich
      erneut anzumelden.
- [ ] 403 als „nicht erlaubt“ behandeln, nicht als Fehler, der wiederholt wird.
- [ ] Ratenbegrenzungen beachten; Anfragen bündeln und blättern.
- [ ] Jede Anfrage über HTTPS senden.
- [ ] Fehler mit Zeit und Status protokollieren, damit der Support sie den Logs des Servers zuordnen kann
      ([Einen Fehler über seine Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)).

## Checkliste zur Zuverlässigkeit eines App-Backends

- [ ] Externes Monitoring der Website, der Anmeldung der Administration und eines authentifizierten API-Lesezugriffs.
- [ ] Der Watchdog von Velocity installiert, wenn Velocity die Website ausliefert.
- [ ] Wartungsfenster Benutzern der App und Partnern angekündigt; die App behandelt 503.
- [ ] Ratenbegrenzungen eingerichtet, und APCu verfügbar, wo sie durchgesetzt werden müssen.
- [ ] Ablaufdaten von Schlüsseln und Tokens im Kalender; eine Zugangsberechtigung pro App und Partner.
- [ ] Backups in einer Übung wiederhergestellt, damit eine Wiederherstellung des Backends ein bekanntes Verfahren ist
      ([Wiederherstellungsübungen](../zuverlaessigkeit/wiederherstellungsuebungen.md)).
- [ ] Eine Ansprechperson für Störungen für das App-Team und für das Server-Team
      ([Störungsbehandlung für kleine Teams](../zuverlaessigkeit/stoerungsbehandlung-fuer-kleine-teams.md)).

## Verwandte Seiten

- [Headless mit der REST API](headless-mit-der-rest-api.md)
- [Checkliste mobil](checkliste-mobil.md)
- [Health-Checks und Monitoring](../zuverlaessigkeit/health-checks-und-monitoring.md)
- [Wartungsmodus und geplante Arbeiten](../zuverlaessigkeit/wartungsmodus-und-geplante-arbeiten.md)
- [Health-Checks und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
