---
title: Welche API für Ihre App
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 11
summary: Für jeden Bildschirm einer App zwischen der REST-Inhalts-API, den Remote-Diensten von expservices und einer eigenen ezjscore-Serverfunktion entscheiden - nach Aufgabe, nach Zugangsdaten, nach Form der Antwort und nach dem, was das Serverteam tun muss.
keywords: REST, expservices, ezjscore, auswählen, Entscheidung, Vergleich, Mobile App, Headless, Suche, Bildalias, Aktualisieren, Zugangsdaten, Umschlag, choose, decision, comparison, mobile app, search, image alias, update, credential, envelope
type: help
---
# Welche API für Ihre App

Diese Seite richtet sich an App-Entwickler und technische Leitungen, die Bildschirm für Bildschirm entscheiden müssen,
welche der drei HTTP-Schnittstellen von Exponential sie aufrufen. Sie vergleicht sie in den Punkten, die für eine App
zählen: was jede kann, wie sie authentifiziert, wie ihre Antworten aussehen und was das Serverteam einrichten muss.

## Inhalt

1. [Die kurze Antwort](#die-kurze-antwort)
2. [Nach Aufgabe](#nach-aufgabe)
3. [Nach Eigenschaft](#nach-eigenschaft)
4. [Schnittstellen kombinieren](#schnittstellen-kombinieren)
5. [Wenn nichts passt](#wenn-nichts-passt)

## Die kurze Antwort

Lesen Sie Inhalte mit der **REST-API** (`/api/ezp/v1` oder `/api/ezp/v2`). Nutzen Sie die **Remote-Dienste**
(`expservices`) für das, was REST nicht bietet: Suche, Bildalias-URLs, URL-Aliase, Sprachen, das Aktualisieren von
Inhalten. Bitten Sie einen PHP-Entwickler nur dann um eine **ezjscore-Serverfunktion**, wenn keine der beiden hat, was
ein Bildschirm braucht.

## Nach Aufgabe

| Aufgabe | REST-API | expservices | Lesen |
|---|---|---|---|
| Ein Element mit seinen Feldern lesen | `GET /content/node/<id>?ResponseGroups=Fields` | `expnode::get`, `expnode::dataMap` | [Einen Knoten lesen](rest-api/einen-knoten-lesen.md) |
| Einen Ordner seitenweise auflisten | `GET /content/node/<id>/list/offset/<n>/limit/<n>` | `expnode::children` | [Unterknoten auflisten und Paginierung](rest-api/unterknoten-auflisten-und-paginierung.md) |
| Unterknoten zählen | `GET /content/node/<id>/childrenCount` | `expnode::childrenCount` | dieselbe Seite |
| Suchen | keine | `expsearch::search` | [Inhalte suchen](json-endpunkte/inhalte-suchen.md) |
| Bild in einer Bildschirmgröße | keine (nur der Originalpfad) | `expimage::url`, `expimage::urls` | [Bildaliase für Bildschirmgrößen](medien-und-bilder/bildaliase-fuer-bildschirmgroessen.md) |
| Element über den URL-Pfad (Deep Link) | keine | `expnode::getByPath` | [Knoten-IDs, Remote-IDs und Deep Links](inhaltsmodellierung/knoten-ids-remote-ids-und-deep-links.md) |
| Anlegen und veröffentlichen | `POST /api/ezp/v2/content/node/create` | `expnode::create` | [Inhalte anlegen und entfernen](rest-api/inhalte-anlegen-und-entfernen.md) |
| Aktualisieren | antwortet nach der Berechtigungsprüfung mit `501` | `expnode::update` | [Über expservices schreiben](json-endpunkte/ueber-expservices-schreiben.md) |
| Entfernen | `DELETE /api/ezp/v2/content/node/delete/<id>` | `expnode::remove` | [Inhalte anlegen und entfernen](rest-api/inhalte-anlegen-und-entfernen.md) |
| Sprachen der Website | keine | `explanguage::prioritized`, `explanguage::languages` | [Mehrsprachige Inhalte](inhaltsmodellierung/mehrsprachige-inhalte.md) |

Die Namen der expservices stammen aus den Dienstdeklarationen in `extension/expservices/classes/`; der vollständige
Katalog steht in der [expservices-Referenz](../../bc/6.0/backend-dienste-ueber-ezjscore.md).

## Nach Eigenschaft

| | REST-API | expservices | Eigene ezjscore-Funktion |
|---|---|---|---|
| Ausgeliefert mit | `ezprestapi` (vom Root-`composer.json` von 6.0.15 verlangt) | `expservices`, Teil der Standarddistribution | Ihrer Erweiterung |
| URL | `/api/ezp/<version>/...` | `/ezjscore/call/exp<domain>::<method>[::arg...]` | `/ezjscore/call/<name>::<function>[::arg...]` |
| Zugangsdaten | OAuth2-Token oder persönlicher API-Schlüssel | expservices-Token oder ein Sitzungscookie | was die Funktion prüft, meist die Sitzung |
| Anonyme Aufrufe | standardmäßig abgelehnt (`RequireAuthentication=enabled`) | erlaubt für Dienste, die als `public` markiert sind | Sache der Funktion |
| Form der Antwort | einfache JSON-Objekte; Fehler mit HTTP-Status | `{"error_text":"","content":{"ok":...}}`, Fehler darin mit HTTP 200 | `{"error_text":"","content":<value>}` |
| Paginierung | `offset`, `limit` im Pfad; `childrenCount` in den Metadaten | die letzten beiden URL-Argumente; `meta.total`, `meta.has_more` | Ihre eigene |
| Versioniert | ja, v1 und v2 | keine Version in der URL | nein |

Der Unterschied in der Fehlerbehandlung ist für Ihre HTTP-Schicht wichtig: REST meldet mit dem Statuscode, was schiefging,
während ein expservices-Aufruf, der den Dienst erreicht, mit HTTP 200 antwortet und den Fehler in `content.ok` und
`content.error.code` ablegt ([Der Umschlag von expservices](json-endpunkte/expservices-aus-einer-app-aufrufen.md#der-umschlag)).

## Schnittstellen kombinieren

Kombinieren ist normal und sicher: Beide laufen als ein Benutzer, und beide prüfen dieselben Rollen und Richtlinien.
Halten Sie im Code der App zwei Dinge getrennt:

- **Zwei Arten von Zugangsdaten.** Ein REST-OAuth-Token oder ein `expk_`-Schlüssel ist kein expservices-Token, und ein
  `expt_`-Token wird von REST nicht akzeptiert. Speichern und erneuern Sie sie getrennt
  ([Zugangsdaten auswählen](anmeldung/zugangsdaten-auswaehlen.md)).
- **Zwei Fehlermodelle.** Kapseln Sie jede Schnittstelle in einer eigenen Client-Klasse, die deren Antworten in einen
  einzigen Fehlertyp Ihrer App übersetzt.

## Wenn nichts passt

Ein Bildschirm, der Daten braucht, die keine Route und kein Dienst liefert (ein kombiniertes Dashboard, eine eigene
Berechnung), bekommt einen eigenen Serverendpunkt, in PHP in einer Erweiterung geschrieben: eine ezjscore-Serverfunktion
oder ein eigener REST-Provider. Das ist Arbeit für Anwendungsentwickler:
[JSON-Endpunkte mit ezjscore](../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md),
[REST-API für Anwendungen](../ersteller/anwendungsentwickler/rest-api.md#ein-eigener-rest-provider). Geben Sie ihnen den
Bildschirm, die benötigten Felder und den Benutzer, als der der Aufruf läuft.

## Verwandte Seiten

- [Was die APIs nicht können](was-die-apis-nicht-koennen.md)
- [Headless-Exponential](../ersteller/anwendungsentwickler/headless-exponential.md#die-api-wählen)
- [JSON-Endpunkte mit ezjscore: ezjscore, expservices oder REST?](../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md#ezjscore-expservices-oder-rest)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
