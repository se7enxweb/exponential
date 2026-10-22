---
title: ezjscore-Serverfunktionen für Ihre App
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 340
summary: Wenn eine App einen Endpunkt braucht, den keine REST-Route und kein Dienst bietet - worum Sie einen PHP-Entwickler bitten, wie eine ezjscore-Serverfunktion registriert und aufgerufen wird, ihre Antworthülle, die Berechtigungsregeln, denen sie folgen muss, die Authentifizierung aus einer nativen App und wann ein eigener REST-Provider die bessere Wahl ist.
keywords: ezjscore, Serverfunktion, ezjscServer, ezjscore/call, eigener Endpunkt, JSON, RequirePolicy, FunctionList, Richtlinie ezjscore/call, REST-Provider, Auftrag, Mobile App, server function, custom endpoint, ezjscore/call policy, REST provider, brief, mobile app
type: help
---
# ezjscore-Serverfunktionen für Ihre App

Diese Seite richtet sich an App-Entwickler, die einen Endpunkt brauchen, den es noch nicht gibt, zum Beispiel einen
Aufruf, der einem Startbildschirm alles liefert, was er zeigt. Sie erklärt, worum Sie einen PHP-Entwickler bitten und
wie die App das Ergebnis aufruft. Die Anleitung für die Serverseite ist
[JSON-Endpunkte mit ezjscore](../../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md).

## Inhalt

1. [Wann Sie um eine bitten](#wann-sie-um-eine-bitten)
2. [Was gebaut wird](#was-gebaut-wird)
3. [Sie aufrufen](#sie-aufrufen)
4. [Authentifizierung aus einer nativen App](#authentifizierung-aus-einer-nativen-app)
5. [Ein Auftrag für den PHP-Entwickler](#ein-auftrag-für-den-php-entwickler)

## Wann Sie um eine bitten

| Bedarf | Bessere Wahl |
|---|---|
| Knoten, Unterknoten, Felder lesen | die REST-API, kein neuer Code |
| Suche, Bildalias-URLs, Aktualisierungen, Sprachen | ein vorhandener Remote-Dienst, kein neuer Code |
| Eine Antwort, die mehreres davon für einen Bildschirm verbindet | eine Serverfunktion oder Ihr eigenes Backend ([Ein Backend für Ihre App](../anmeldung/ein-backend-fuer-ihre-app.md)) |
| Daten aus einer eigenen Tabelle einer Erweiterung | eine Serverfunktion oder eine Remote-Dienst-Domäne der Erweiterung |
| Routen, die versioniert sein und OAuth wie die Inhalts-API nutzen sollen | ein eigener REST-Provider ([REST-API für Anwendungen](../../ersteller/anwendungsentwickler/rest-api.md#ein-eigener-rest-provider)) |

## Was gebaut wird

Eine Serverfunktion ist eine öffentliche statische PHP-Methode, die in einem Block `[ezjscServer_<name>]` der Datei
`ezjscore.ini.append.php` einer Erweiterung registriert ist. Der Router lehnt jeden Namen ohne einen solchen Block ab,
ruft `<class>::<function>( array $args )` auf und verpackt den Rückgabewert
([Wie ein Aufruf funktioniert](../../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md#wie-ein-aufruf-funktioniert)).
Schreibt die Erweiterung sie stattdessen als expservices-Domäne (eine Klasse, die `expServiceBase` erweitert), bekommt
sie den Umschlag, den Katalogeintrag und API-Tokens ohne Mehraufwand
([Eine eigene Dienstdomäne](../../ersteller/anwendungsentwickler/entfernte-dienste-expservices.md#eine-eigene-dienstdomäne)).

## Sie aufrufen

```bash
curl -s 'https://example.com/ezjscore/call/<name>::<function>::<argument>?ContentType=json'
```

```json
{"error_text": "", "content": {"...": "the function's return value"}}
```

Eine einfache Serverfunktion antwortet mit ihrem eigenen Wert in `content`; eine expservices-Domäne antwortet mit dem
Umschlag `{"ok":...}` in `content` ([expservices aus einer App aufrufen](expservices-aus-einer-app-aufrufen.md#der-umschlag)).
Prüfen Sie zuerst `error_text`: Es ist gesetzt, wenn der Router den Aufruf abgelehnt hat.

ezjscore liefert eine Testfunktion mit, mit der Sie die Schnittstelle prüfen können: `ezjsc::time`.

## Authentifizierung aus einer nativen App

Eine einfache Serverfunktion läuft als aktueller Benutzer der Anfrage: über ein Sitzungscookie oder anonym. Sie versteht
von sich aus weder REST-Schlüssel noch OAuth-Tokens noch expservices-Tokens. Für eine App ist das ein Grund, eine
expservices-Domäne zu bevorzugen, die expservices-Tokens annimmt, oder einen REST-Provider, der die
REST-Authentifizierung nutzt.

Klassische Serverfunktionen lassen sich zusätzlich mit der Richtlinie `ezjscore/call` und der Beschränkung
`FunctionList` schützen; wie streng, legt `RequirePolicy` (nicht gesetzt, `disabled` oder `enabled`) im Block der
Funktion fest ([expservices-Referenz: Zugriff](../../../bc/6.0/backend-dienste-ueber-ezjscore.md#zugriff)).

## Ein Auftrag für den PHP-Entwickler

- Der Bildschirm und das genaue JSON, das er braucht (eine Beispielantwort).
- Der Benutzer, als der der Aufruf läuft, und welche Inhalte dieser Benutzer lesen darf.
- Lesen oder Schreiben; Schreibzugriffe nur per `POST`, mit dem Formular-Token bei Sitzungen oder einem Bearer-Token
  bei expservices.
- Paginierung: `limit` und `offset` als letzte Argumente.
- Fehlerfälle und wie die App sie sehen soll.
- Caching: wie frisch die Antwort sein muss.
- „Berechtigungen in jeder Funktion prüfen; jedes Argument validieren und umwandeln; Daten zurückgeben, kein HTML“ sind
  die Regeln aus [JSON-Endpunkte mit ezjscore: Sicherheit](../../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md#sicherheit).

## Verwandte Seiten

- [Welche API für Ihre App](../welche-api-fuer-ihre-app.md)
- [Remote-Dienste mit expservices](../../ersteller/anwendungsentwickler/entfernte-dienste-expservices.md)
- [REST- und JSON-Aufrufe (Kochbuch)](../../../beispiele/kochbuch/rest-json-abruf.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
