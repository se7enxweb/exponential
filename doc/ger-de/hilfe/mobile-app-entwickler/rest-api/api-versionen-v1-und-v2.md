---
title: API-Versionen v1 und v2
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 195
summary: Welche Version der REST-API eine App aufrufen sollte - Lesezugriffe sind unter v1 und v2 identisch, Schreibzugriffe gibt es nur unter v2, Links bleiben in ihrer Version, der Alias ezpl, die veraltete Kompatibilitätsschicht ezprestapiprovider, was 6.0.15 für bestehende Apps geändert hat und wie eine App über Server-Upgrades hinweg funktionsfähig bleibt.
keywords: Versionen, v1, v2, ezpl, ezprestapi, ezprestapiprovider, veraltet, Kompatibilitätsschicht, Kompatibilität, Upgrade, bestehende App, 6.0.15, REST, Mobile App, versions, deprecated, shim, compatibility, upgrade, existing app, mobile app
type: help
---
# API-Versionen v1 und v2

Diese Seite richtet sich an App-Entwickler, die eine neue App beginnen, und an diejenigen, die eine App pflegen, die
die REST-API bereits aufruft. Sie erklärt, was die Version im Pfad heute bedeutet und was Sie von Server-Upgrades
erwarten können.

## Inhalt

1. [Was die Version bedeutet](#was-die-version-bedeutet)
2. [Welche Version eine neue App aufrufen sollte](#welche-version-eine-neue-app-aufrufen-sollte)
3. [Bestehende Apps auf v1](#bestehende-apps-auf-v1)
4. [Der veraltete Provider](#der-veraltete-provider)
5. [Eine App über Upgrades hinweg funktionsfähig halten](#eine-app-über-upgrades-hinweg-funktionsfähig-halten)

## Was die Version bedeutet

Seit Exponential 6.0.15 bedient eine Erweiterung, `ezprestapi` 1.2.6 oder neuer, beide Versionen mit einem Provider
(`ezp7xRestApiProvider`) und einem Controller ([REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md#kurz-gesagt)):

- **Lesezugriffe** antworten unter `/api/ezp/v1/...` und `/api/ezp/v2/...` mit denselben Inhalten.
- **Schreibzugriffe** (Anlegen, Aktualisieren, Löschen) antworten nur unter v2; ein Schreibzugriff an v1 existiert
  dort nicht und wird abgewiesen.
- **Links innerhalb einer Antwort bleiben in der Version der Anfrage**, sodass ein v1-Client weiterhin v1-Links folgt.
- **`ezpl`** ist ein Alias-Präfix von `ezp`: `/api/ezpl/v2/...` antwortet genauso wie `/api/ezp/v2/...`.

## Welche Version eine neue App aufrufen sollte

Verwenden Sie **v2 für alles**. Es bietet jeden Lesezugriff von v1 und zusätzlich die Schreibzugriffe, und eine
einzige Basis-URL hält den Client einfach. Es gibt keinen Grund, mit v1 zu beginnen.

## Bestehende Apps auf v1

Eine App, die vor Jahren gegen v1 gebaut wurde, funktioniert weiterhin. Der Leitfaden berichtet, dass die Antworten
von v1 und v2 vor und nach der Änderung mit aufgezeichneten Antworten von 33 URLs verglichen wurden, unter Apache und
unter Velocity, darunter das `GET /api/ezp/v1/content/node/<id>/list/offset/0/limit/20?ResponseGroups=Fields` der
mobilen App, und Byte für Byte identisch waren, außer dass `OutputFormat=xhtml` `renderedOutput` wieder füllt
([Aktivierungskombinationen](../../../anleitungen/rest-api-v1-und-v2.md#7-aktivierungskombinationen-vorher-und-seitdem)).

Was sich bei älteren Installationen geändert hat, liegt auf dem Server: Bei manchen Aktivierungsreihenfolgen der
beiden alten Erweiterungen antworteten v2-Adressen mit `404`, und v1-Lesezugriffe liefen ohne
Berechtigungsprüfungen. Seit den aktuellen Releases verhält sich jede Kombination gleich, und jeder Lesezugriff wird
geprüft. Wenn eine alte App nach einem Server-Upgrade plötzlich `403` erhält, fehlt dem verwendeten Konto
`content/read` für diese Inhalte; vorher wurde die Prüfung übersprungen.

## Der veraltete Provider

`ezprestapiprovider` war der ursprüngliche v1-Provider. Sein letztes Release, 6.0.4, ist eine
Kompatibilitätsschicht über `ezprestapi`, und Installationen werden gebeten, `ezprestapi` zu aktivieren und
`ezprestapiprovider` zu entfernen ([Umstieg von ezprestapiprovider](../../../anleitungen/rest-api-v1-und-v2.md#6-migration-von-ezprestapiprovider)).
Für OAuth-Clients, Tokens, API-Schlüssel und v1-URLs ändert sich nichts. Für einen App-Entwickler ist das eine
Aufgabe des Serverteams, kein App-Release.

## Eine App über Upgrades hinweg funktionsfähig halten

- **Lesen Sie die Basis-URL aus der Konfiguration**, nicht aus dem Code, damit ein Umzug auf einen anderen Host oder
  ein anderes Präfix kein Release erfordert.
- **Ignorieren Sie unbekannte Schlüssel** in Antworten; neue Schlüssel können hinzukommen.
- **Prüfen Sie die API in Debug-Builds beim Start** (`/content/node/<id>` mit einer bekannten ID) und protokollieren
  Sie den Status.
- **Verfolgen Sie die Release Notes** des Servers: [ezprestapi](../../../funktionen/6.0/erweiterungen/ezprestapi.md) und
  die Changelogs von 6.0.
- **Testen Sie gegen die nächste Serverversion**, bevor sie in die Produktion geht
  ([Eine lokale Testinstallation](../testen/eine-lokale-testinstallation.md)).

## Verwandte Seiten

- [REST-API v1 und v2 für Unternehmen](../../unternehmen/mobil-und-headless/rest-api-v1-und-v2-fuer-unternehmen.md)
- [ezprestapiprovider](../../../funktionen/6.0/erweiterungen/ezprestapiprovider.md)
- [Routenreferenz](routenreferenz.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
