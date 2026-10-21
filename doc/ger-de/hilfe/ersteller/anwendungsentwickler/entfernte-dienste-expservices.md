---
title: Entfernte Dienste mit expservices
audience: app-creator, developer
category: app-creation
order: 360
summary: Exponential aus Skripten, Apps und anderen Frontends mit expservices steuern - über tausend benannte Dienste, Discovery, die Antworthülle, die Clients für Shell und Python, Tokens, Richtlinien je Dienst und eine eigene Dienstdomäne.
keywords: entfernte Dienste, Discovery, Antworthülle, API-Token, Shell-Client, Dienste, Domäne, Anwendungsentwickler, expservices, remote services, ezjscore call, discovery, envelope, API token, shell client, Python, services, domain, expServiceBase, app creator
type: help
---
# Entfernte Dienste mit expservices

Diese Seite richtet sich an Anwendungsentwickler, die eine entfernte Verwaltungs-App, ein JavaScript-Frontend oder ein
Skript bauen, das die Funktionen der Administration steuert. `expservices` macht aus den Funktionen der Administration
über tausend benannte Dienste (Inhalte, Benutzer, Rollen, Caches, Cronjobs, Shop, Feeds, Medien, Tags, Layouts, das
Audit und mehr), die über ezjscore aufgerufen werden
([Entfernte Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md),
[Backend-Dienste von ezjscore](../../../bc/6.0/backend-dienste-ueber-ezjscore.md)).

## Inhalt

1. [Prüfen, ob es antwortet](#prüfen-ob-es-antwortet)
2. [Einen Dienst aufrufen](#einen-dienst-aufrufen)
3. [Authentifizierung](#authentifizierung)
4. [Eine eigene Dienstdomäne](#eine-eigene-dienstdomäne)
5. [Wann es passt](#wann-es-passt)

## Prüfen, ob es antwortet

```bash
curl -s 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
```

Sie erhalten eine Antworthülle, die besagt, dass Sie anonym sind. Ein 403 bei jedem Aufruf bedeutet, dass der
Hauptschalter aus ist: `./console exp:ini get expservices.ini/Services/Enabled` muss `enabled` ausgeben.

## Einen Dienst aufrufen

Jede Antwort hat innerhalb von `content` aus ezjscore dieselbe Form: `{"ok":true,"data":...,"meta":{...}}` oder
`{"ok":false,"error":{...}}`. Die mitgelieferten Clients packen sie aus:

```bash
export EXPSERVICES_URL=https://example.com
extension/expservices/bin/expservices-client.sh whoami
extension/expservices/bin/expservices-client.sh call expsystem::version
```

Discovery listet jeden Dienst mit seinen Parametern auf, sodass Clients den Katalog nicht fest einprogrammieren müssen.

## Authentifizierung

Persönliche API-Tokens für Skripte; eine angemeldete Sitzung für Seiten derselben Website. Jeder Dienst deklariert die
Richtlinien, die er braucht, und prüft sie als der aufrufende Benutzer. Hinter Apache mit `mod_proxy_fcgi` stellen Sie
sicher, dass der Header `Authorization` bei PHP ankommt. Die Token-Tabelle wird aus
`extension/expservices/sql/<engine>/schema.sql` angelegt.

## Eine eigene Dienstdomäne

Eine Domäne ist eine Klasse `exp<Domain>Services`, die `expServiceBase` erweitert, als `[ezjscServer_exp<domain>]` in
der `ezjscore.ini.append.php` Ihrer Erweiterung registriert ist und als `<root>/ezjscore/call/exp<domain>::<function>`
aufgerufen wird. Orientieren Sie sich bei Antworthülle, Richtliniendeklaration und Discovery-Eintrag an den
mitgelieferten Domänen in `extension/expservices/classes/`; lesende Dienste lassen sich Exponential Intelligence mit
`'ai' => true` als Werkzeuge anbieten ([Exponential Intelligence](exponential-intelligence.md)).

## Wann es passt

| Bedarf | Verwenden |
|---|---|
| Ein entfernter Verwaltungs-Client, ein Portal, Skripte, die die Website verwalten | expservices |
| Inhalte für Apps und Frontends, OAuth für Dritte | REST ([Die REST-API](rest-api.md)) |
| Das JavaScript Ihrer eigenen Seite | eine ezjscore-Serverfunktion ([JSON-Endpunkte mit ezjscore](json-endpunkte-mit-ezjscore.md)) |
| KI-Werkzeuge, die innerhalb der Administration handeln | Werkzeuge von Exponential Intelligence ([Exponential Intelligence erweitern](exponential-intelligence-erweitern.md)) |

## Verwandte Seiten

- [OAuth und API-Schlüssel](oauth-und-api-schluessel.md)
- [Headless Exponential](headless-exponential.md)
- [Entfernte Dienste (Funktion)](../../../../eng-us/features/6.0/remote-services-expservices.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
