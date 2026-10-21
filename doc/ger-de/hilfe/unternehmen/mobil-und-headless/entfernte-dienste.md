---
title: Entfernte Dienste
audience: businesses, app-creator, admin
category: business-solutions
order: 60
summary: Was die Remote-Dienste expservices einem Unternehmen geben - die Funktionen der Administration als über tausend benannte HTTP-Dienste für Portale, Skripte, Werkzeuge von Partnern und Apps zur Fernverwaltung, mit Discovery, persönlichen Tokens, Berechtigungen pro Dienst, protokollierten Schreibzugriffen und Beispielportalen.
keywords: expservices, Remote-Dienste, HTTP-API, Portal, Fernverwaltung, Skripte, Integration, Discovery, Tokens, Audit, ezjscore, Unternehmen, remote services, HTTP API, portal, remote administration, scripts, integration, discovery, tokens, audit, business
type: help
---
# Entfernte Dienste

Diese Seite ist für Führungskräfte und Produktverantwortliche, die möchten, dass andere Programme mehr tun als Inhalte
lesen: ein Portal für Kunden oder Partner, eine App zur Fernverwaltung, ein Skript, das die tägliche Routine des Shops
erledigt, eine Integration mit einem anderen Geschäftssystem. Die Erweiterung `expservices` von Exponential macht die
Funktionen der Administration zu benannten Diensten, die solche Programme über HTTP aufrufen. Diese Seite erklärt, was
das bietet, wie es gesteuert wird und wann Sie es statt der REST API verwenden.

## Inhalt

1. [Was expservices ist](#was-expservices-ist)
2. [Was sich darüber erledigen lässt](#was-sich-darüber-erledigen-lässt)
3. [Wie es sicher gehalten wird](#wie-es-sicher-gehalten-wird)
4. [Darauf gebaute Portale](#darauf-gebaute-portale)
5. [REST API oder Remote-Dienste](#rest-api-oder-remote-dienste)
6. [Fragen, bevor Sie sich darauf verlassen](#fragen-bevor-sie-sich-darauf-verlassen)

## Was expservices ist

Aus [Entfernte Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md): `expservices` macht die Funktionen der
Administration zu „over a thousand named services (content, users, roles, caches, cronjobs, shop, feeds, media, tags,
layouts, the audit and more)“ (über tausend benannten Diensten für Inhalte, Benutzer, Rollen, Caches, Cronjobs, Shop,
Feeds, Medien, Tags, Layouts, das Audit und mehr), ausgeliefert über die Aufrufschnittstelle von ezjscore unter
`/ezjscore/call/`. Ein Dienst wird beim Namen aufgerufen und antwortet mit JSON. Er ist „part of the default
distribution“ (Teil der Standarddistribution) und braucht keinen Build-Schritt und keinen zusätzlichen Server.

Die **Discovery** listet jeden Dienst mit seinen Parametern auf, sodass Programme den Katalog nicht fest einprogrammieren
([Remote-Dienste mit expservices](../../../../eng-us/help/creators/app-creators/remote-services-expservices.md#calling-a-service)).

## Was sich darüber erledigen lässt

Die Gruppen der Dienste, aus
[Remote-Dienste: die ganze Administration als HTTP-API](../../../../eng-us/features/6.0/remote-services-expservices.md#domains):

| Gruppe | Beispiele |
|---|---|
| Kern | Sitzung, Systeminformationen, Einstellungen (lesen), Caches, Cronjobs, Erweiterungen, Pakete, Workflows, Velocity |
| Inhalte | Knoten, Objekte, Klassen, Versionen, Übersetzungen, Relationen, Orte, Papierkorb, Sektionen, Zustände, URL-Aliase, Suche, Inhaltsjobs |
| Benutzer und Zugriff | Benutzer, Gruppen, Rollen, Richtlinien, Sitzungen, Einstellungen, Benachrichtigungen, Abläufe rund um Konten |
| Handel, Gemeinschaft, Feeds | Produkte, Warenkorb und Bestellablauf, Bestellungen, USt, Währungen, Rabatte, Wunschlisten, Versand, Zahlungen, gesammelte Formulare, Umfragen, Foren, Kommentare, Bewertungen, RSS-Exporte und -Importe, Ausgabe jedes Teilbaums als RSS, Atom und JSON Feed |
| Medien und Sonstiges | Bilder und Aliase, Dateien und signierte Download-Links, Audio und Video, Tags, Layouts, Audit (nur lesen), Newsletter, Sitemaps, Statistiken, Designs, Sprachen |

Für ein Unternehmen sind die Dienste für Handel und Gemeinschaft der interessante Teil: Ein Partnerportal oder eine App
kann Produkte zeigen, einen Warenkorb und den Bestellablauf ausführen und die Bestellungen eines Kunden auflisten, nach
denselben Regeln wie die Website.

## Wie es sicher gehalten wird

Aus den „Rules every service follows“ (Regeln, die jeder Dienst befolgt) und den Einstellungen der Funktionsseite:

- Jeder Dienst erklärt seinen Zugriff: öffentlich, jeder angemeldete Benutzer oder eine bestimmte Richtlinie. „401
  without login, 403 without the policy“ (401 ohne Anmeldung, 403 ohne die Richtlinie).
- **Schreibzugriffe** brauchen ein POST, das Formular-Token und die Richtlinie, und „every successful write records“
  (jeder erfolgreiche Schreibzugriff zeichnet) ein Ereignis im Audit-Trail auf.
- Große Vorgänge gehen in Inhaltsjobs im Hintergrund, statt eine Anfrage zu blockieren.
- Ein **Hauptschalter** (`expservices.ini [Services] Enabled`): Wenn er ausgeschaltet ist, antwortet jeder Dienst mit
  403.
- Listen werden seitenweise geliefert: standardmäßig 25 Einträge, höchstens 200.
- **Persönliche Tokens** für Skripte und Apps werden einmal angezeigt, nur als Hash gespeichert, laufen nach 1 bis 365
  Tagen ab und werden beim Anlegen, Widerrufen oder Ablehnen im Audit-Trail aufgezeichnet
  ([Apps und API-Schlüssel](apps-und-api-schluessel.md#tokens-für-expservices)).

> **Hinweis**
> Jeder Dienst läuft als ein Benutzer, mit dessen Richtlinien. Ein Portal für Partner sollte Partnerkonten mit
> Partnerrollen verwenden, nie das Konto eines Administrators ([Extranets](../extranets/README.md)).

## Darauf gebaute Portale

Die Erweiterung liefert Beispiel-Frontends, die „news, shop, forums, media, feeds, search and login in the browser
without a build step“ (Neuigkeiten, Shop, Foren, Medien, Feeds, Suche und Anmeldung im Browser ohne Build-Schritt)
rendern: `expportal_jquery` (eine Referenz mit jQuery 4) und `expportal_reactive` (React mit React Bootstrap), unter
vier Designs in `extension/expservices/design/`
([Darauf gebaute Portale](../../../../eng-us/features/6.0/remote-services-expservices.md#portals-built-on-it)). Sie rufen die
Dienste beim Namen auf. Für ein Unternehmen sind sie ein Ausgangspunkt und eine Vorführung, kein fertiges Produkt: Ihre
Entwickler passen sie an Ihre Marke und Ihre Regeln an.

## REST API oder Remote-Dienste

Aus [Wann man es verwendet](../../../../eng-us/help/creators/app-creators/remote-services-expservices.md#when-to-use-it):

| Bedarf | Verwenden |
|---|---|
| Ein Client zur Fernverwaltung, ein Portal, Skripte, die die Website verwalten | expservices |
| Inhalte für Apps und Frontends, OAuth für Dritte | REST ([Headless mit der REST API](headless-mit-der-rest-api.md)) |
| Das JavaScript Ihrer eigenen Seite | eine Serverfunktion von ezjscore |
| KI-Werkzeuge, die innerhalb der Administration handeln | Werkzeuge von Exponential Intelligence |

Kurz: REST, um vielen Benutzern über eine App **Inhalte zu zeigen**, mit OAuth; Remote-Dienste, um **Dinge zu tun**:
Bestellungen, Benutzer, Caches, Feeds, als angemeldete Person oder als Skript.

## Fragen, bevor Sie sich darauf verlassen

- [ ] Welche Dienste braucht das Programm? (Bitten Sie die Entwickler um die Liste aus dem Discovery-Katalog.)
- [ ] Welches Benutzerkonto führt sie aus, mit welcher Rolle?
- [ ] Ist der Hauptschalter nur dort eingeschaltet, wo er gebraucht wird, und sind Tokens für Schreibzugriffe
      vorgeschrieben (der Standard)?
- [ ] Wer bekommt die Audit-Ereignisse der Schreibzugriffe, und wer prüft sie?
- [ ] Erreicht hinter Apache mit `mod_proxy_fcgi` der Header `Authorization` PHP? (Sonst kann der Header `X-Exp-Token`
      das Token tragen.)
- [ ] Was geschieht im Portal, wenn die Website im Wartungsmodus oder nicht erreichbar ist
      ([Zuverlässigkeit des App-Backends](zuverlaessigkeit-des-app-backends.md))?

## Verwandte Seiten

- [Apps und API-Schlüssel](apps-und-api-schluessel.md)
- [Verkaufen über Netzwerke und Partner](verkaufen-ueber-netzwerke-und-partner.md)
- [Entfernte Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md)
- [Remote-Dienste mit expservices](../../../../eng-us/help/creators/app-creators/remote-services-expservices.md)
- [Remote-Dienste: die ganze Administration als HTTP-API](../../../../eng-us/features/6.0/remote-services-expservices.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
