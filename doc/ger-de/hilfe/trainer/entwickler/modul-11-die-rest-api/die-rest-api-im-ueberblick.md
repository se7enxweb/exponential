---
title: "Lektion 11.1: Die REST-API auf einen Blick"
audience: trainer, developer
category: training
order: 111
summary: Die Teilnehmenden finden die Teile der REST-Schnittstelle, lesen die Routen von v1 und v2, erklären Provider, Präfixe und Versionen und finden jede REST-Einstellung in rest.ini. 40 Minuten.
keywords: entwickler, REST, API, ezprestapi, v1, v2, routen, provider, präfix, ezp, ezpl, auth, rest.ini, ApiPrefix, ProviderClass, RequireAuthentication, ezp7xRestApiProvider, ezp7xRestContentController, developers course, routes, prefix
type: help
---
# Lektion 11.1: Die REST-API auf einen Blick

Apps, andere Websites und Skripte sprechen mit Exponential über seine REST-Schnittstelle. Bevor die Teilnehmenden sie
aufrufen (Lektion 11.2) oder erweitern (Lektion 11.3), brauchen sie die Landkarte: welche Erweiterung was ausliefert,
welche Routen unter welcher Version antworten und welche Einstellungen ihr Verhalten bestimmen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 11: Die REST-API](README.md) |
| Dauer | 40 Minuten (20 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Modul 8](../modul-08-einstellungen-und-siteaccesses/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- das REST-Framework des Kernels und die Erweiterung `ezprestapi` nennen und sagen, was beide ausliefern;
- eine Route lesen: `/api/<prefix>/<version>/<path>`;
- sagen, welche Routen unter v1 und v2 antworten, und dass Schreibzugriffe nur unter v2 antworten;
- erklären, dass jeder Aufruf die Inhaltsrichtlinien des Benutzers prüft;
- die REST-Einstellungen mit `exp:ini` finden und lesen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [REST-API v1 und v2: eine Erweiterung](../../../../anleitungen/rest-api-v1-und-v2.md).

1. **Was liefert was aus** (5 Minuten). Das REST-Framework steckt im Kernel (`kernel/private/rest/`,
   `settings/rest.ini`); die Inhaltsrouten kommen aus der Erweiterung `ezprestapi`, deren Provider
   `ezp7xRestApiProvider` die Präfixe `ezp` und `ezpl` ausliefert; der `ezpRestAuthProvider` des Kernels liefert
   `auth` aus (den OAuth2-Token-Endpunkt). `ezprestapiprovider` ist veraltet.
2. **Eine Adresse** (3 Minuten). `/api/ezp/v1/content/node/2`: `ApiPrefix` `/api`, Präfix `ezp`, Version `v1`, Pfad
   `/content/node/2`.
3. **Die Routen** (5 Minuten). Zeigen Sie die Tabelle aus Abschnitt 2 des Leitfadens: Knoten, Kinder (`list` mit
   Offset, Limit und Sortierung), `childrenCount`, Felder, ein einzelnes Feld, dasselbe über das Objekt;
   Schreibzugriffe (anlegen, aktualisieren, löschen) nur unter v2.
4. **Berechtigungen** (3 Minuten). Jeder Lese- und Schreibzugriff prüft `content/read`, `content/create`,
   `content/edit` oder `content/remove` des Benutzers, unter dem die Anfrage läuft, unabhängig davon, womit er sich
   authentifiziert hat. Antworten: 200, 201, 400, 401, 403, 404.
5. **Einstellungen** (2 Minuten). `rest.ini`: `[System] ApiPrefix`, `[ApiProvider] ProviderClass[<prefix>]`,
   `[Authentication] RequireAuthentication`, `AuthenticationStyle`, `RequireHTTPS`.

### Häufige Fragen

**Warum v1 und v2?** Bestehende Clients rufen v1 auf und erhalten dieselben Antworten wie bisher; Schreibzugriffe
kamen mit v2 hinzu.

**Gibt es ein bekanntes Problem?** `listAtom` antwortet mit 500 (Abschnitt 8 des Leitfadens). Sagen Sie es den
Teilnehmenden, bevor sie es ausprobieren.

**Verwendet die REST-Schnittstelle den View-Cache?** Sie hat im Framework einen eigenen Antwort-Cache; die
Inhaltsberechtigungen werden bei jeder Anfrage geprüft.

### Stolperfallen

- **`ezprestapi` nicht aktiv.** Ohne die Erweiterung antworten die Inhaltsrouten als unbekannt. Prüfen Sie
  `ActiveExtensions`.
- **`rest.ini` ändern, ohne den INI-Cache zu leeren.** Leeren Sie ihn und laden Sie die PHP-Worker neu.
- **`RequireAuthentication=disabled` auf einer Live-Website.** Anfragen laufen dann als `DefaultUserID` oder anonym;
  nur für öffentliche Daten, die nur gelesen werden.

## Praktische Übung

Auf der Entwicklungsinstallation.

1. Prüfen Sie, dass `ezprestapi` aktiv ist: `php bin/php/console exp:ini get site.ini/ExtensionSettings/ActiveExtensions | grep ezprestapi`.
2. Lesen Sie die Provider: `php bin/php/console exp:ini where rest.ini/ApiProvider/ProviderClass`.
3. Lesen Sie die Authentifizierungseinstellungen: `php bin/php/console exp:ini list rest.ini/Authentication`.
4. Lesen Sie die Routentabelle im Provider: `grep -n "new ezpMvc" extension/ezprestapi/classes/rest_provider.php`.
5. Notieren Sie für jede Route ihren Pfad und ob sie unter v1, v2 oder beiden antwortet.
6. Rufen Sie die API ohne Zugangsdaten auf und lesen Sie den Status:
   `curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8087/api/ezp/v1/content/node/2` (mit Ihrer Adresse).
7. Suchen Sie den Ordner des REST-Frameworks im Kernel und listen Sie seine Klassen auf: `ls kernel/private/rest/classes/`.

## Erwartetes Ergebnis

- Schritt 1 gibt `ActiveExtensions[]=ezprestapi` aus (oder die Erweiterung steht im Wert).
- Schritt 2: `ProviderClass[ezp]=ezp7xRestApiProvider` und `ProviderClass[ezpl]`.
- Schritt 3: `RequireAuthentication=enabled` und die Art der Authentifizierung.
- Schritt 6: `401`, weil eine Authentifizierung verlangt wird.
- Der Trainer vergleicht die Routentabelle aus Schritt 5 mit der Tabelle des Leitfadens.

## Quiz

1. Welche Erweiterung liefert die Inhaltsrouten aus?
2. Unter welchen Versionen antworten Schreibzugriffe?
   A. nur v1 B. nur v2 C. v1 und v2 D. keine
3. Aus welchen Teilen besteht `/api/ezp/v2/content/node/2`?
4. Welche Richtlinien prüft die REST-Schnittstelle?
5. Was erhält eine Anfrage ohne Zugangsdaten, wenn eine Authentifizierung verlangt wird?

<details>
<summary>Antworten</summary>

1. `ezprestapi`, mit dem Provider `ezp7xRestApiProvider`.
2. **B.** Nur v2.
3. `ApiPrefix` `/api`, Präfix `ezp`, Version `v2`, Pfad `/content/node/2`.
4. Die Inhaltsrichtlinien des Benutzers: `content/read`, `content/create`, `content/edit`, `content/remove`.
5. `401`.

</details>

## Weiterführende Literatur

- [REST-API v1 und v2: eine Erweiterung](../../../../anleitungen/rest-api-v1-und-v2.md).
- [Entfernte Dienste und Apps](../../../../anleitungen/entfernte-dienste-und-apps.md).
- [API- und Protokollstandards](../../../../../eng-us/standards/apis-and-protocols.md).

## Verwandte Seiten

- [Modul 11: Die REST-API](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
