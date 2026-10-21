---
title: Die REST-API für Betreiber
audience: customer, businesses, partner
category: customer-success
order: 9
summary: Was ein Website-Betreiber über die REST-API von Exponential wissen muss - was sie liefert, Lesen in v1 und v2 und Schreiben in v2, wie sich Programme authentifizieren, warum Berechtigungen weiterhin gelten, wer die Zugangsdaten kontrolliert und was vor dem Live-Gang einer Integration zu fragen ist.
keywords: REST-API, Headless, ezprestapi, v1, v2, OAuth2, API-Schlüssel, Berechtigungen, Integration, App, Website-Betreiber, REST API, headless, API key, permissions, integration, app, site owner
type: help
---
# Die REST-API für Betreiber

Diese Seite ist für Betreiber, deren Website eine App, eine andere Website oder ein internes System versorgen wird.
Mit der REST-API können Programme Inhalte lesen und, in Version 2, anlegen, aktualisieren und löschen, als JSON über
HTTP ([REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)). Sie müssen die Routen nicht lesen; Sie müssen
wissen, was das für Kontrolle und Sicherheit bedeutet.

## Inhalt

1. [In Kürze](#in-kürze)
2. [Was unter Ihrer Kontrolle bleibt](#was-unter-ihrer-kontrolle-bleibt)
3. [Welche Zugangsdaten für welches Programm](#welche-zugangsdaten-für-welches-programm)
4. [Fragen vor dem Live-Gang](#fragen-vor-dem-live-gang)
5. [Einmal im Jahr](#einmal-im-jahr)

## In Kürze

Aus [REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md):

- Eine einzige Erweiterung liefert sie aus: `ezprestapi`. Das ältere `ezprestapiprovider` ist veraltet; sein letztes
  Release ist eine Kompatibilitätsschicht über `ezprestapi`.
- **Lesezugriffe** antworten in v1 und v2 (`/api/ezp/v1/...`, `/api/ezp/v2/...`); **Schreibzugriffe** antworten **nur in v2**.
- **Jeder Lese- und Schreibzugriff prüft die Richtlinien des Benutzers** (`content/read`, `content/create`,
  `content/edit`, `content/remove`), unabhängig davon, womit die Anfrage authentifiziert wurde.
- Programme authentifizieren sich mit einem OAuth2-Token oder einem persönlichen API-Schlüssel, gesendet als
  `Authorization: Bearer expk_...`.

Die Sicht für Unternehmen steht unter [REST-API v1 und v2 für Unternehmen](../../unternehmen/mobil-und-headless/rest-api-v1-und-v2-fuer-unternehmen.md).

## Was unter Ihrer Kontrolle bleibt

| Kontrolle | Wie |
|---|---|
| Was ein Programm lesen oder ändern darf | die Richtlinien des Kontos, als das es handelt; ein Programm erhält nie mehr als dieses Konto |
| Welche Inhalte öffentlich sind | dieselben Sektionen und Rollen wie bei der Website: Inhalte, die anonyme Besucher nicht lesen dürfen, werden an anonyme Anfragen nicht ausgeliefert |
| Wer Schlüssel erzeugen darf | die Richtlinie `apikey/create` ([Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md)) |
| Was ein Schlüssel darf | Bereiche (Scopes), die bei seiner Erzeugung gewählt werden, nie über die Richtlinien des Besitzers hinaus |
| Den Zugriff beenden | den Schlüssel widerrufen; das wirkt bei der nächsten Anfrage auf jedem Server |
| Nachweise | jeder erzeugte, widerrufene, erstmals verwendete oder abgewiesene Schlüssel steht als `access.apikey.*` im Prüfprotokoll |

> **Tipp**
> Geben Sie jeder Integration ein **eigenes Konto** mit einer Rolle, die nur erlaubt, was sie braucht, statt eines
> Schlüssels auf dem Konto einer Person. Wenn diese Person geht, funktioniert die Integration weiter, und Sie sehen im
> Prüfprotokoll, was die Integration getan hat.

## Welche Zugangsdaten für welches Programm

Aus [Apps und API-Schlüssel](../../unternehmen/mobil-und-headless/apps-und-api-schluessel.md):

| Programm | Zugangsdaten |
|---|---|
| Eine App, deren Benutzer sich anmelden | OAuth2 (die Benutzer autorisieren die App) |
| Ein Skript oder eine Integration, die als ein Konto handelt | ein persönlicher API-Schlüssel mit Bereichen |
| Ein Skript, das die Remote-Dienste nutzt | ein Token für Remote-Dienste ([Remote-Dienste (expservices)](remote-dienste-expservices.md)) |

Schlüssel werden **einmal** angezeigt, wenn sie erzeugt werden; gespeichert wird nur ein Hash, sodass niemand einen
Schlüssel erneut anzeigen kann, nicht einmal ein Administrator ([Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md)).
Bewahren Sie sie im Geheimnisspeicher des Programms auf, nie in einem Dokument oder einer E-Mail.

## Fragen vor dem Live-Gang

- [ ] Welches Konto verwendet die Integration, und welche Rolle hat es?
- [ ] Nur lesen, oder auch schreiben? Wenn sie schreibt: Gibt es eine Freigabe oder eine Prüfung dessen, was sie anlegt?
- [ ] Wo wird der Schlüssel aufbewahrt, wer kann ihn sehen, und wann läuft er ab?
- [ ] Was tut das Programm, wenn die API mit einem Fehler antwortet oder die Website in Wartung ist?
- [ ] Wer wird informiert, wenn die Integration ausfällt?
- [ ] Hat der Partner die Version von `ezprestapi` geprüft und dass die alte Provider-Erweiterung nicht mehr aktiv ist?

Weitere Fragen stehen unter [Eine Integration planen](eine-integration-planen.md).

## Einmal im Jahr

- Administratoren sehen jeden Schlüssel unter **Setup > oAuth-Verwaltung > API-Schlüssel**, mit Suche und Filtern;
  widerrufen Sie die, die niemand braucht ([Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md)).
- Beziehen Sie die Konten der Integrationen in die [Zugriffsüberprüfung](../../unternehmen/sicherheit/zugriffspruefungen.md) ein.
- Fragen Sie den Partner, ob ein Upgrade etwas für API-Clients ändert.

## Verwandte Seiten

- [REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)
- [Headless mit der REST-API](../../unternehmen/mobil-und-headless/headless-mit-der-rest-api.md)
- [Mobil und Apps](mobil-und-apps.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
