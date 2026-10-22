---
title: JSON-Endpunkte für Apps
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 300
summary: Die Einstiegsseite der Seiten zu JSON-Endpunkten für App-Entwickler - die Aufrufschnittstelle von ezjscore, die Remote-Dienste von expservices, die eine App am wahrscheinlichsten nutzt, und wann Sie um eine eigene Serverfunktion bitten.
keywords: JSON, ezjscore, ezjscore/call, expservices, Remote-Dienste, Dienste, Suche, Bildalias, Serverfunktion, Umschlag, Mobile App, remote services, services, search, image alias, server function, envelope, mobile app
type: help
---
# JSON-Endpunkte für Apps

Dieser Ordner richtet sich an App-Entwickler, die mehr als die REST-Inhalts-API brauchen: Suche, Bildgrößen,
URL-Nachschlagen, Sprachen, Aktualisierungen. Diese kommen von den Remote-Diensten (`expservices`), die über die
Aufrufschnittstelle von ezjscore unterhalb von `/ezjscore/call/` bereitgestellt werden. Ein Bildschirm, der etwas
braucht, das kein Dienst liefert, kann eine eigene Serverfunktion bekommen.

## Inhalt

1. [Die Aufrufschnittstelle](#die-aufrufschnittstelle)
2. [Dienste, die eine App wahrscheinlich nutzt](#dienste-die-eine-app-wahrscheinlich-nutzt)
3. [Seiten in diesem Ordner](#seiten-in-diesem-ordner)

## Die Aufrufschnittstelle

```text
GET|POST https://example.com/ezjscore/call/<name>::<function>[::<argument>...]?ContentType=json
```

Der Router von ezjscore (`ezjscServerRouter`) lehnt jeden Namen ohne einen Block `[ezjscServer_<name>]` in
`ezjscore.ini` ab, ruft die registrierte PHP-Funktion auf und verpackt das Ergebnis als
`{"error_text":"","content":<result>}`
([JSON-Endpunkte mit ezjscore](../../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md#wie-ein-aufruf-funktioniert)).
`expservices` registriert auf diese Weise über tausend Dienste, benannt als `exp<domain>::<method>`.

Eine schnelle anonyme Prüfung, ob die Dienste aktiv sind:

```bash
curl -s 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
```

Ein Umschlag, der besagt, dass Sie anonym sind, bedeutet, dass sie aktiv sind; `Not a valid ezjscServerRouter argument`
in `error_text` bedeutet, dass `expservices` auf dieser Installation nicht aktiv ist.

## Dienste, die eine App wahrscheinlich nutzt

| Bedarf | Dienst | Zugriff |
|---|---|---|
| Suche | `expsearch::search` | `content/read` |
| URL eines Bilds in einer benannten Größe | `expimage::url`, `expimage::urls` | `content/read` |
| Die konfigurierten Bildgrößen | `expimage::aliases` | öffentlich |
| Ein Knoten über seinen URL-Pfad (Deep Links) | `expnode::getByPath` | `content/read` |
| Seit einem Datum veröffentlichte Nachfahren | `expnode::modifiedSince` | `content/read` |
| Die Sprachen der Website in Prioritätsreihenfolge | `explanguage::prioritized` | öffentlich |
| Ein Element aktualisieren | `expnode::update` | `content/edit` |
| Wer bin ich, Tokens erzeugen und widerrufen | `expsession::whoami`, `tokenCreate`, `tokenRevoke` | angemeldet |

Die Spalte Zugriff ist die eigene Deklaration des Diensts in `extension/expservices/classes/`.

## Seiten in diesem Ordner

| Seite | Beantwortet |
|---|---|
| [expservices aus einer App aufrufen](expservices-aus-einer-app-aufrufen.md) | URLs, Argumente, der Umschlag, Paginierung, Erkundung, Fehler |
| [Inhalte suchen](inhalte-suchen.md) | `expsearch::search` mit Text, Paginierung und Filtern |
| [Über expservices schreiben](ueber-expservices-schreiben.md) | anlegen, aktualisieren, entfernen; POST und Tokens |
| [ezjscore-Serverfunktionen für Ihre App](ezjscore-serverfunktionen-fuer-ihre-app.md) | um einen eigenen Endpunkt bitten und ihn aufrufen |

## Verwandte Seiten

- [Remote-Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md)
- [Remote-Dienste mit expservices](../../ersteller/anwendungsentwickler/entfernte-dienste-expservices.md)
- [Backend-Dienste über ezjscore (Referenz)](../../../bc/6.0/backend-dienste-ueber-ezjscore.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
