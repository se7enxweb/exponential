---
title: Caching auf dem Server
audience: mobile-app-developer, app-creator, developer, hosting
category: mobile-apps
order: 610
summary: Wie der Server zwischenspeichert, was eine App liest - der REST-Antwort-Cache (je Benutzer, nur GET und HEAD, standardmäßig 600 Sekunden, Ablehnungen nie zwischengespeichert), seine Einstellungen und Cache-Gruppe, was das für die Aktualität nach dem Veröffentlichen bedeutet, der Antwort-Cache von Velocity und authentifizierte Anfragen, und die Header, die Antworten von expservices tragen.
keywords: REST-Antwort-Cache, ApplicationCache, DefaultCacheTTL, CacheTTL, REST-Cache, Tag content, Aktualität, veröffentlichen, Antwort-Cache von Velocity, Authorization, no-cache, Server-Cache, Mobile App, REST answer cache, rest cache, content tag, freshness, publish, Velocity response cache, server cache, mobile app
type: help
---
# Caching auf dem Server

Diese Seite richtet sich an App-Entwickler, die sich fragen, warum ein vor einer Minute veröffentlichtes Element noch
nicht in der App erscheint, und an das Serverteam, das den Cache abstimmt. Sie erklärt die Caches auf dem Server, die
API-Antworten berühren.

## Inhalt

1. [Der REST-Antwort-Cache](#der-rest-antwort-cache)
2. [Aktualität nach dem Veröffentlichen](#aktualität-nach-dem-veröffentlichen)
3. [Ihn abstimmen](#ihn-abstimmen)
4. [Velocity und API-Anfragen](#velocity-und-api-anfragen)
5. [Die Remote-Dienste](#die-remote-dienste)

## Der REST-Antwort-Cache

Das REST-Framework hält Antworten in einem Anwendungscache (`rest.ini [CacheSettings]`):

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `ApplicationCache` | `enabled` | der globale Schalter |
| `ApplicationCacheDefault` | `enabled` | jede Controller-Aktion zwischenspeichern, sofern ein Block nichts anderes sagt |
| `DefaultCacheTTL` | `600` | Sekunden, die eine Antwort aufbewahrt wird |

Seit 6.0.15 hält er eine Antwort je Benutzer und nur für `GET` und `HEAD`; eine Ablehnung wird nie zwischengespeichert,
und eine Cache-Datei, die sich nicht zurücklesen lässt, wird neu erzeugt
([Berechtigungen der REST-Schnittstelle](../../../anleitungen/api-schluessel.md#32-berechtigungen-der-rest-schnittstelle)). Der Cache
ist die Gruppe `rest` der `site.ini` („REST Application cache“), mit den Tags `content` und `rest`, sodass das Leeren der
Inhalts-Caches auch ihn leert.

## Aktualität nach dem Veröffentlichen

Mit den Standardwerten kann ein Lesezugriff eine bis zu zehn Minuten alte Antwort liefern. Für eine App bedeutet das:

- Nachdem ein Redakteur veröffentlicht hat, sieht die App die Änderung möglicherweise erst, wenn die zwischengespeicherte
  Antwort abläuft.
- Nachdem die App selbst geschrieben hat, kann ihr eigener nächster Lesezugriff noch die zwischengespeicherte Antwort
  sein. Zeigen Sie das Ergebnis des Schreibzugriffs aus dessen Antwort (oder aus einem frischen Lesezugriff über die
  Remote-Dienste), statt die Liste sofort erneut zu lesen.
- Braucht das Unternehmen schnellere Aktualisierungen, senkt das Serverteam die TTL für die Aktionen, die die App nutzt.

## Ihn abstimmen

Ein Block je Controller-Aktion oder je Controller legt eigene Regeln fest; die Kommentare der mitgelieferten `rest.ini`
zeigen das Muster für den alten Controller-Namen. Für den aktuellen Controller:

```ini
# settings/override/rest.ini.append.php
[ezp7xRestContentController_list_CacheSettings]
ApplicationCache=enabled
CacheTTL=120
```

Der Name lautet `<controllerClass>_<action>_CacheSettings` (die Aktionen sind `viewContent`, `list`, `viewFields`,
`viewField`, `countChildren`); ohne einen solchen Block sucht das System nach `<controllerClass>_CacheSettings`. Nach
einer Änderung an `rest.ini`: den INI-Cache leeren, PHP-FPM neu laden und Velocity neu starten
([REST-API v1 und v2: Konfiguration](../../../anleitungen/rest-api-v1-und-v2.md#5-konfiguration)). Die Beispielwerte dienen
der Veranschaulichung; wählen Sie sie mit dem Website-Inhaber.

## Velocity und API-Anfragen

Wird die Website von Velocity ausgeliefert, hält dessen Antwort-Cache gerenderte anonyme Seiten und nie eine Anfrage mit
einem `Authorization`-Header ([Antwort-Cache von Velocity: was er nie speichert](../../hosting/leistung/antwort-cache-von-velocity.md#was-er-nie-speichert)).
Authentifizierte REST-Aufrufe erreichen daher immer PHP (und den REST-Antwort-Cache). Anonyme Aufrufe öffentlicher
Remote-Dienste sind gewöhnliche `GET`-Anfragen; ob sie zwischengespeichert werden, hängt von den Headern ab, mit denen
sie gesendet werden.

## Die Remote-Dienste

Die Antworten der ezjscore-Aufrufe auf dem Demonstrationsserver trugen `Cache-Control: no-cache, must-revalidate` und
ein `Expires`-Datum in der Vergangenheit, sodass weder Browser noch Proxys sie behalten. Speichern Sie sie in der App
oder in Ihrem Backend zwischen ([Caching in der App](caching-in-der-app.md)).

## Verwandte Seiten

- [Caches: was jeder enthält und wie man ihn leert](../../../anleitungen/caches.md)
- [Caching in Anwendungen](../../ersteller/anwendungsentwickler/caching-in-anwendungen.md)
- [HTTP-Caching, Proxys und CDNs](../../ersteller/website-ersteller/http-caching-und-cdns.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
