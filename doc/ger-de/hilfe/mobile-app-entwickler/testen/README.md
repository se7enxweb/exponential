---
title: Apps gegen Exponential testen
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 800
summary: Die Einstiegsseite zu den Testseiten für App-Entwickler - wo Sie testen (eine öffentliche Demo, eine lokale Installation, eine Staging-Kopie der Produktion), wofür sich jede eignet und welche Tests eine App auf Exponential braucht.
keywords: Testen, Demo, lokale Installation, Staging, Prüfen, Testdaten, Integrationstests, Vertragstests, Mobile App, testing, demo, local installation, staging, probe, test data, integration tests, contract tests, mobile app
type: help
---
# Apps gegen Exponential testen

Dieser Ordner richtet sich an App-Entwickler, die eine Exponential-Installation brauchen, gegen die sie entwickeln
und testen. Die Wahl der Installation ist wichtig: Eine öffentliche Demo ist schnell angesehen, aber nur auf einer
Installation, die Sie selbst kontrollieren, können Sie frei Schlüssel, Klassen und Testinhalte anlegen.

## Inhalt

1. [Wo Sie testen](#wo-sie-testen)
2. [Tests, die eine App braucht](#tests-die-eine-app-braucht)
3. [Seiten in diesem Ordner](#seiten-in-diesem-ordner)

## Wo Sie testen

| Installation | Geeignet für | Nicht geeignet für |
|---|---|---|
| Eine öffentliche Demo | die Administrationsoberfläche und die Inhalte ansehen; prüfen, welche APIs antworten | Schlüssel, Schreibzugriffe, eigene Klassen; sie wird nach Zeitplan neu aufgebaut |
| Eine eigene lokale Installation | alles während der Entwicklung; automatisierte Tests | Produktionsdaten und Last |
| Eine Staging-Kopie der Produktionswebsite | abschließende Prüfungen mit den echten Klassen, Rollen und Inhalten | zerstörende Tests |

## Tests, die eine App braucht

- **Vertragstests** gegen eine lokale oder Staging-Installation: ein Lesezugriff pro Route und Dienst, den die App
  verwendet, mit Prüfung der Schlüssel und Typen, die sie auswertet ([Felder und Feldwerte](../rest-api/felder-und-feldwerte.md)).
- **Berechtigungstests**: Der Benutzer der App kann lesen, was sie anzeigt, und nichts, was er nicht darf, und seine
  Schreibzugriffe werden außerhalb seines Ordners abgewiesen ([Berechtigungen für App-Benutzer](../veroeffentlichungsablauf/berechtigungen-fuer-app-benutzer.md)).
- **Fehlertests**: `401`, `403`, `404`, `429` und Zeitüberschreitungen werden behandelt ([Fehler und Statuscodes](../rest-api/fehler-und-statuscodes.md)).
- **Offline-Tests**, wenn die App synchronisiert ([Synchronisation und Offline-Lesen](../offline-und-leistung/synchronisation-und-offline-lesen.md)).

## Seiten in diesem Ordner

| Seite | Beantwortet |
|---|---|
| [Gegen eine Demo testen](gegen-eine-demo-testen.md) | Eine Installation prüfen, und was eine öffentliche Demo erlaubt |
| [Eine lokale Testinstallation](eine-lokale-testinstallation.md) | In wenigen Minuten eine installieren und für eine App vorbereiten |

## Verwandte Seiten

- [Anwendungen testen](../../ersteller/anwendungsentwickler/anwendungen-testen.md)
- [Checkliste für den Start von Apps](../checklisten/checkliste-fuer-den-start.md)
- [Demomodus-FAQ](../../demomodus-faq.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
