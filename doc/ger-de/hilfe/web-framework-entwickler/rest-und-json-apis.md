---
title: REST- und JSON-APIs
audience: web-framework-developer, developer
category: web-frameworks
order: 340
summary: Die API-Schichten von Exponential für Entwickler, die APIs mit API Platform, Laravel API Resources, dem Django REST Framework oder Spring-Controllern bauen - die REST-Inhalts-API in v1 und v2 mit API-Schlüsseln und OAuth2, ein eigener REST-Provider, ezjscore-Serverfunktionen für das JavaScript Ihrer Seiten, expservices zur Steuerung der Administrationsoberfläche und einfaches JSON aus einer Modul-View.
keywords: REST, API, JSON, ezprestapi, v1, v2, OAuth2, API-Schlüssel, Bearer, REST-Provider, ezjscore, ezjscServer, expservices, JSON-Antwort, API Platform, Django REST Framework, Laravel API Resources, Web-Framework-Entwickler, API keys, REST provider, JSON response, web framework developer
type: help
---
# REST- und JSON-APIs

Diese Seite richtet sich an Entwickler, die JSON aus Controllern oder über ein API-Framework bereitstellen. Exponential
bietet drei fertige API-Schichten und zusätzlich die Möglichkeit, einfaches JSON aus einer Modul-View auszuliefern.
Wählen Sie die Schicht danach, wer sie aufruft.

## Inhalt

1. [Welche Schicht](#welche-schicht)
2. [Die REST-Inhalts-API](#die-rest-inhalts-api)
3. [Eigene REST-Routen](#eigene-rest-routen)
4. [ezjscore-Serverfunktionen](#ezjscore-serverfunktionen)
5. [expservices](#expservices)
6. [Einfaches JSON aus einer View](#einfaches-json-aus-einer-view)
7. [Im Vergleich mit API-Frameworks](#im-vergleich-mit-api-frameworks)

## Welche Schicht

| Aufrufer | Schicht | Authentifizierung |
|---|---|---|
| Andere Programme, die Inhalte lesen oder schreiben: Apps, Frontends, Integrationen | REST v1 und v2 (`ezprestapi`) | persönliche API-Schlüssel, OAuth2 |
| Das JavaScript Ihrer eigenen Seiten | ezjscore-Serverfunktionen | die Sitzung, Formular-Token für Schreibzugriffe |
| Skripte und Apps, die Funktionen der Administrationsoberfläche steuern | expservices | API-Tokens |
| Ein kleiner Endpunkt Ihres Moduls | JSON aus einer Modul-View | die Sitzung oder Ihre eigene Prüfung |

## Die REST-Inhalts-API

Relativ zu `/api/ezp/<version>` ([REST-API v1 und v2 für Anwendungen](../ersteller/anwendungsentwickler/rest-api.md)):

| Methode | Pfad | v1 | v2 |
|---|---|---|---|
| `GET` | `/content/node/<id>` | ja | ja |
| `GET` | `/content/node/<id>/list(/offset/<n>)(/limit/<n>)` | ja | ja |
| `GET` | `/content/object/<id>`, `.../fields`, `.../field/<identifier>` | ja | ja |
| `POST` | `/content/node/create` | nein | ja |
| `DELETE` | `/content/node/delete/<id>` | nein | ja |

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v1/content/node/2
```

Jeder Aufruf prüft die Richtlinien des Benutzers (`content/read`, `content/create`, `content/edit`,
`content/remove`), unabhängig von der Art der Anmeldedaten. `settings/rest.ini` konfiguriert das Framework;
`[Authentication] RequireAuthentication=enabled` ist der Standard. Stellen Sie unter Apache mit FastCGI sicher, dass
der `Authorization`-Header PHP erreicht ([Leitfaden REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md),
[OAuth und API-Schlüssel](../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md)).

## Eigene REST-Routen

Eine Erweiterung fügt Routen über einen **REST-Provider** hinzu, der für ein Präfix registriert wird und eine
Routentabelle und Controller mitbringt – so wie `ezprestapi` den `ezp7xRestApiProvider` für `ezp` und `ezpl`
registriert. Routen-, Request-, Ergebnis- und Response-Filter sind weitere Erweiterungspunkte
([Erweiterungspunkte: REST-Provider](../../bc/6.0/rad-erweiterungspunkte.md#rest-provider)). Das Framework
authentifiziert; Ihr Controller muss autorisieren.

## ezjscore-Serverfunktionen

```ini
# extension/expdemo/settings/ezjscore.ini.append.php
<?php /* #?ini charset="utf-8"?
[ezjscServer_expdemo]
Class=expDemoServerFunctions
*/ ?>
```

`<root>/ezjscore/call/expdemo::count::42?ContentType=json` ruft `expDemoServerFunctions::count( array( '42' ) )` auf
und verpackt den Rückgabewert als `{"error_text": "", "content": ...}`. `ezjscore/call` prüft selbst keine Richtlinie:
Jede Funktion prüft für sich ([JSON-Endpunkte mit ezjscore](../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md)).

## expservices

`expservices` macht aus den Funktionen der Administrationsoberfläche über tausend benannte Dienste (Inhalte, Benutzer,
Rollen, Caches, Cronjobs, Shop, Feeds, Medien, Tags, Layouts, das Audit und mehr), die über ezjscore mit einem
einheitlichen Antwortformat, Discovery und Tokens aufgerufen werden
([Entfernte Dienste mit expservices](../ersteller/anwendungsentwickler/entfernte-dienste-expservices.md)).

## Einfaches JSON aus einer View

```php
header( 'Content-Type: application/json; charset=utf-8' );
echo json_encode( array( 'ok' => true, 'data' => $data ) );
eZExecution::cleanExit();
```

Für einen einzelnen Endpunkt in Ordnung; für alles Größere verwenden Sie eine der Schichten oben
([Responses](responses-weiterleitungen-und-fehler.md#json-und-andere-formate)).

## Im Vergleich mit API-Frameworks

| Funktion | API Platform, DRF, Laravel API Resources | Exponential |
|---|---|---|
| Ressourcen aus Modellen | generiert | die Inhalts-API deckt Inhalte ab; eigene Ressourcen sind Routen eines REST-Providers |
| Serialisierung | Serializer, Resources | Ihr Controller baut Arrays; REST gibt Felder zurück, optional als XHTML gerendert |
| OpenAPI-Dokumentation | generiert | nicht generiert |
| GraphQL | als Pakete verfügbar | nicht in Exponential 6; die Symfony-Plattformprodukte bieten es ([Die Plattformprodukte](die-plattformprodukte.md)) |
| Authentifizierung | Tokens, OAuth | API-Schlüssel, OAuth2, expservices-Tokens |

## Verwandte Seiten

- [Headless und entkoppelte Frontends](headless-und-entkoppelte-frontends.md)
- [Standard für APIs und Protokolle](../../standards/apis-und-protokolle.md)
- [Entfernte Dienste und Apps](../../anleitungen/entfernte-dienste-und-apps.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
