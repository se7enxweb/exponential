---
title: Glossar für Mobile-App-Entwickler
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 95
summary: Die Begriffe des Abschnitts Mobile-App-Entwickler für App-Entwickler erklärt - von Knoten, Objekt, Klasse und Remote-ID bis zu Antwortgruppe, OutputFormat, Provider, ezpl, OAuth-Client, expk- und expt-Tokens, Umschlag, Bildalias und Siteaccess - jeder mit einem Link zur ausführlichen Erklärung.
keywords: Glossar, Begriffe, Definitionen, Knoten, Objekt, Klasse, Feld, Remote-ID, Antwortgruppe, OutputFormat, Translation, Provider, ezp, ezpl, OAuth, TokenTTL, API-Schlüssel, expk, expt, expservices, Umschlag, ezjscore, Bildalias, Siteaccess, Mobile App, glossary, terms, definitions, node, object, class, field, remote id, response group, API key, envelope, image alias, siteaccess, mobile app
type: help
---
# Glossar für Mobile-App-Entwickler

Die Begriffe dieses Abschnitts, für App-Entwickler erklärt. Jeder Eintrag verweist auf die Seite, die ihn ausführlich
erklärt. Das technische Glossar der gesamten Dokumentation steht unter [Glossar](../../glossar.md).

## Inhalt

1. [Inhalte](#inhalte)
2. [Die REST-API](#die-rest-api)
3. [Zugangsdaten](#zugangsdaten)
4. [Remote-Dienste und ezjscore](#remote-dienste-und-ezjscore)
5. [Serverbegriffe, die Sie hören werden](#serverbegriffe-die-sie-hören-werden)

## Inhalte

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Inhaltsklasse | Der Typ eines Elements und seine Liste von Feldern, erkennbar an `classIdentifier` (`folder`, `article`) | [Inhaltsklassen für Apps](inhaltsmodellierung/inhaltsklassen-fuer-apps.md) |
| Inhaltsobjekt | Ein Element; `objectId` in REST-Antworten | [Wie Exponential-Inhalte in eine App gelangen](wie-exponential-inhalte-in-eine-app-gelangen.md) |
| Knoten (Platzierung) | Ein Ort eines Objekts im Inhaltsbaum; `nodeId`. Ein Objekt kann mehrere haben, einer davon ist der Hauptknoten | dieselbe Seite |
| Feld (Attribut) | Ein Wert eines Objekts mit einem Datentyp | [Felder und Feldwerte](rest-api/felder-und-feldwerte.md) |
| Datentyp | Die Art eines Felds: `ezstring`, `ezxmltext`, `ezimage` und andere; `type` in einer Feldantwort | dieselbe Seite |
| Remote-ID | Eine Zeichenketten-ID eines Objekts oder Knotens, die über Installationen hinweg stabil bleiben kann; `objectRemoteId`, `nodeRemoteId` | [Knoten-IDs, Remote-IDs und Deep Links](inhaltsmodellierung/knoten-ids-remote-ids-und-deep-links.md) |
| Version | Ein gespeicherter Stand eines Objekts; Lesezugriffe liefern den veröffentlichten | [Entwürfe, Vorschau und Veröffentlichung](veroeffentlichungsablauf/entwuerfe-vorschau-und-veroeffentlichung.md) |
| Locale | Ein Sprachcode wie `eng-US` oder `ger-DE`, verwendet von `Translation` und `languageLocale` | [Mehrsprachige Inhalte](inhaltsmodellierung/mehrsprachige-inhalte.md) |
| URL-Alias | Die lesbare Adresse eines Knotens auf der Website; `fullUrl` in einer REST-Antwort | [Knoten-IDs, Remote-IDs und Deep Links](inhaltsmodellierung/knoten-ids-remote-ids-und-deep-links.md) |

## Die REST-API

| Begriff | Bedeutung | Lesen |
|---|---|---|
| `ezprestapi` | Die Erweiterung, die die REST-Inhalts-API in v1 und v2 bereitstellt | [REST-API](rest-api/README.md) |
| Provider | Die Klasse, die die Routen eines Präfixes bedient; `ezp7xRestApiProvider` für `ezp` und `ezpl` | [Routenreferenz](rest-api/routenreferenz.md) |
| `ezp`, `ezpl` | Die beiden Präfixe der Inhalts-API: `/api/ezp/...` und sein Alias `/api/ezpl/...` | dieselbe Seite |
| v1, v2 | Die API-Versionen im Pfad; Lesen in beiden, Schreiben in v2 | [API-Versionen v1 und v2](rest-api/api-versionen-v1-und-v2.md) |
| Antwortgruppe | Ein Teil der Antwort, den Sie mit `ResponseGroups=` anfordern (`Metadata`, `Locations`, `Fields`, `FieldValues`) | [Antwortgruppen](rest-api/antwortgruppen.md) |
| `OutputFormat=xhtml` | Fordert `renderedOutput` an, den von einem Template der Website gerenderten Knoten | [Gerenderte XHTML-Ausgabe](rest-api/gerenderte-xhtml-ausgabe.md) |
| `ezprestapiprovider` | Die veraltete ältere v1-Erweiterung, heute eine Zwischenschicht über `ezprestapi` | [API-Versionen v1 und v2](rest-api/api-versionen-v1-und-v2.md) |
| Antwort-Cache | Der serverseitige Cache des REST-Frameworks für `GET`-Antworten, je Benutzer | [Caching auf dem Server](offline-und-leistung/caching-auf-dem-server.md) |

## Zugangsdaten

| Begriff | Bedeutung | Lesen |
|---|---|---|
| OAuth-Client | Eine Anwendung, die ein Administrator unter **Setup > oAuth-Verwaltung** registriert, mit einer Kennung, einem Secret und einer Weiterleitungsadresse | [OAuth2-Autorisierungscode-Ablauf](anmeldung/oauth2-autorisierungscode-ablauf.md) |
| Autorisierungscode | Der kurzlebige Code, den die Autorisierungsseite an die Weiterleitungsadresse sendet | dieselbe Seite |
| Zugriffstoken, Refresh-Token | Was der Token-Endpunkt antwortet; das Zugriffstoken lebt `TokenTTL` Sekunden | dieselbe Seite |
| `oauth/authorize` | Die Richtlinie, die ein Benutzer braucht, um eine App zuzulassen | dieselbe Seite |
| Persönlicher API-Schlüssel | `expk_...`, von einem Benutzer für die eigenen Skripte erzeugt, mit Geltungsbereichen und einer Laufzeit | [Persönliche API-Schlüssel für Werkzeuge](anmeldung/persoenliche-api-schluessel-fuer-werkzeuge.md) |
| Geltungsbereich (Scope) | Wofür ein persönlicher API-Schlüssel verwendet werden darf: `read`, `publish`, `edit`, `remove` | dieselbe Seite |
| expservices-Token | `expt_...`, ein persönliches Token für die Remote-Dienste | [expservices-Tokens in Apps](anmeldung/expservices-tokens-in-apps.md) |
| Formular-Token | Das CSRF-Token, das eine Browsersitzung für Schreibzugriffe braucht; mit einem Bearer-Token nicht nötig | [Über expservices schreiben](json-endpunkte/ueber-expservices-schreiben.md) |

## Remote-Dienste und ezjscore

| Begriff | Bedeutung | Lesen |
|---|---|---|
| ezjscore | Die Erweiterung, deren Schnittstelle `/ezjscore/call/` registrierte PHP-Funktionen aufruft und JSON antwortet | [ezjscore-Serverfunktionen für Ihre App](json-endpunkte/ezjscore-serverfunktionen-fuer-ihre-app.md) |
| expservices | Über tausend benannte Dienste auf Basis von ezjscore, aufgerufen als `exp<domain>::<method>` | [expservices aus einer App aufrufen](json-endpunkte/expservices-aus-einer-app-aufrufen.md) |
| Umschlag | Die Form jeder expservices-Antwort: `{"ok":true,"data":...,"meta":...}` oder `{"ok":false,"error":...}` | [Der Umschlag](json-endpunkte/expservices-aus-einer-app-aufrufen.md#der-umschlag) |
| Katalog | Die Liste aller Dienste mit ihren Argumenten, aus `expservices::catalog` | [expservices aus einer App aufrufen](json-endpunkte/expservices-aus-einer-app-aufrufen.md#dienste-erkunden) |
| Bildalias | Eine benannte Größe eines Bilds (`small`, `medium`, `large` und die eigenen der Website), bei Bedarf erzeugt | [Bildaliase für Bildschirmgrößen](medien-und-bilder/bildaliase-fuer-bildschirmgroessen.md) |

## Serverbegriffe, die Sie hören werden

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Siteaccess | Eine Konfiguration der Installation (eine Website, eine Sprache, die Administrationsoberfläche), nach Host oder Pfad gewählt | [Mehrsprachige Inhalte](inhaltsmodellierung/mehrsprachige-inhalte.md) |
| Rolle und Richtlinie | Was ein Benutzer tun darf: Eine Rolle enthält Richtlinien wie `content/read`, beschränkt auf Bereiche oder Teilbäume | [Berechtigungen für App-Benutzer](veroeffentlichungsablauf/berechtigungen-fuer-app-benutzer.md) |
| Velocity | Der eigene Anwendungsserver von Exponential; nach Konfigurationsänderungen wird er neu gestartet | [Bereitstellen mit Velocity](../ersteller/anwendungsentwickler/mit-velocity-ausliefern.md) |
| Demomodus | Eine öffentliche Demo, die Änderungen des Administratorpassworts ablehnt | [Demomodus](../demomodus.md) |
| Prüfprotokoll | Die Aufzeichnung von Schlüssel- und Token-Ereignissen, Ablehnungen und Schreibzugriffen | [Berechtigungen für App-Benutzer](veroeffentlichungsablauf/berechtigungen-fuer-app-benutzer.md#app-aktivität-nachvollziehen) |

## Verwandte Seiten

- [FAQ für Mobile-App-Entwickler](faq-fuer-mobile-app-entwickler.md)
- [Glossar](../../glossar.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
