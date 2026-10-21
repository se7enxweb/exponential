---
title: Gemeinnützige Organisationen
audience: businesses, project-manager, editor, sales-marketing
category: business-solutions
order: 1
summary: Die Einstiegsseite des Bereichs für gemeinnützige Organisationen - was Hilfsorganisationen, Stiftungen und Organisationen vor Ort mit Exponential ohne Lizenzkosten bauen können, was es für Spenden, Ehrenamtliche und Kampagnen gibt und was nicht, die Seiten des Bereichs und die Lesereihenfolge.
keywords: gemeinnützige Organisation, Hilfsorganisation, Stiftung, NGO, Organisation vor Ort, Spenden, Ehrenamtliche, Kampagnen, Barrierefreiheit, kleines Budget, non-profit, nonprofit, charity, foundation, community organization, donations, volunteers, campaigns, accessibility, low budget
type: help
---
# Gemeinnützige Organisationen

Dieser Bereich richtet sich an Hilfsorganisationen, Stiftungen, Organisationen vor Ort und andere gemeinnützige
Organisationen, die ihre öffentliche Website, ihre Kampagnen und die Gewinnung von Ehrenamtlichen mit Exponential
betreiben. Er erklärt, was es für Spenden, die Anmeldung von Ehrenamtlichen, Kampagnen und Barrierefreiheit gibt, wie Sie
die laufenden Kosten niedrig halten, und er sagt klar, wo Sie einen externen Dienst brauchen. Behörden haben einen
eigenen Bereich: [Behörden: hier beginnen](../../../../eng-us/help/government/government-start-here.md).

## Inhalt

1. [Warum gemeinnützige Organisationen sich Exponential ansehen](#warum-gemeinnützige-organisationen-sich-exponential-ansehen)
2. [Was es gibt und was nicht](#was-es-gibt-und-was-nicht)
3. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
4. [Empfohlene Lesereihenfolge](#empfohlene-lesereihenfolge)
5. [Benachbarte Bereiche](#benachbarte-bereiche)

## Warum gemeinnützige Organisationen sich Exponential ansehen

- **Keine Lizenzgebühr.** „Die Nutzung von Exponential unter den Bedingungen der GNU GPL ist kostenlos“ (README,
  „License“). Das Geld fließt in Arbeit und Hosting, die Sie wählen, nicht in Lizenzen
  ([Lizenzierung für Käufer](../../geschaeft/lizenzen-fuer-kaeufer.md#keine-lizenzgebühren-und-wofür-käufer-tatsächlich-bezahlen)).
- **Geringe Anforderungen an das Hosting sind möglich.** SQLite 3 ist die erste unterstützte Datenbank, und „eine
  vollständige Exponential-Website läuft aus einer Datei auf der Platte, ohne Datenbankserver, der installiert,
  konfiguriert oder abgesichert werden müsste“ ([Unterstützung für SQLite-Datenbanken](../../../../eng-us/features/6.0/sqlite-database.md)).
- **Formulare, Newsletter und Landingpages sind enthalten.** Der Informationssammler, `cjw_newsletter` und Exponential
  Layouts kommen mit dem Produkt (`composer.json`).
- **Das Projekt selbst hat ein gemeinnütziges Zuhause**: Es wird von der Exponential Foundation betrieben, einer
  gemeinnützigen Organisation ([Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md)).

## Was es gibt und was nicht

| Bedarf | Was es gibt | Seite |
|---|---|---|
| Spenden auf der Website annehmen | Kein eigenes Spendenmodul. Möglichkeiten: auf eine externe Spendenseite verlinken oder feste „Spenden“-Artikel über den eingebauten Shop mit dem Gateway `ezpaypal` verkaufen | [Spenden und Spendenaufrufe](spenden-und-spendenaufrufe.md) |
| Regelmäßige Spenden | Für den Shop oder `ezpaypal` nicht dokumentiert; nutzen Sie die regelmäßigen Spenden der externen Plattform | dieselbe Seite |
| Anmeldung von Ehrenamtlichen | Formulare mit dem Informationssammler; Dateien mit `enhancedezbinaryfile`; CAPTCHA | [Ehrenamtliche und Anmeldeformulare](ehrenamtliche-und-anmeldeformulare.md) |
| Kampagnenseiten | Exponential Layouts, mit zeitlich begrenzten Regeln | [Kampagnen und Landingpages](kampagnen-und-landingpages.md) |
| Newsletter für Unterstützer | `cjw_newsletter` mit Double-Opt-in | [Newsletter](../newsletter/README.md) |
| Barrierefreie Website | Anleitungen für Templates, verpflichtende Alternativtexte, ein barrierefreies Administrationsdesign | [Barrierefreiheit für gemeinnützige Organisationen](barrierefreiheit-fuer-gemeinnuetzige.md) |
| Geringe laufende Kosten | SQLite, Velocity ohne separaten Webserver, keine Lizenzgebühr | [Betrieb mit kleinem Budget](betrieb-mit-kleinem-budget.md) |

> **Hinweis**
> Alles in diesem Bereich ist im Repository dokumentiert und auf jeder Seite belegt. Wo dieser Bereich sagt, dass etwas
> nicht vorhanden ist, hat eine Suche in Code und Dokumentation keine solche Funktion gefunden; bitten Sie Ihren
> Entwickler, das zu bestätigen, bevor Sie damit planen.

## Die Seiten dieses Bereichs

| Seite | Was sie beantwortet |
|---|---|
| [Website-Planung für gemeinnützige Organisationen](website-planung-fuer-gemeinnuetzige.md) | Ziele, Zielgruppen, die Seiten, die eine gemeinnützige Organisation braucht, Rollen und ein Arbeitsblatt zur Planung |
| [Spenden und Spendenaufrufe](spenden-und-spendenaufrufe.md) | Die ehrlichen Möglichkeiten, Spenden anzunehmen, und wie Sie eine gute Spendenseite bauen |
| [Ehrenamtliche und Anmeldeformulare](ehrenamtliche-und-anmeldeformulare.md) | Formulare für Ehrenamtliche, wer sie liest, Spam-Schutz, personenbezogene Daten |
| [Kampagnen und Landingpages](kampagnen-und-landingpages.md) | Kampagnenseiten mit Exponential Layouts, Ein- und Ausschalten nach Datum, Messen |
| [Barrierefreiheit für gemeinnützige Organisationen](barrierefreiheit-fuer-gemeinnuetzige.md) | Warum sie wichtig ist, was Templates und Redakteure leisten, wie Sie testen |
| [Betrieb mit kleinem Budget](betrieb-mit-kleinem-budget.md) | Hosting, Wartung und Zeit der Ehrenamtlichen gering halten, ohne die Website zu gefährden |
| [Checkliste für gemeinnützige Organisationen](checkliste-fuer-gemeinnuetzige.md) | Eine Checkliste von der Planung bis zum Start und die Routine danach |

## Empfohlene Lesereihenfolge

1. [Website-Planung für gemeinnützige Organisationen](website-planung-fuer-gemeinnuetzige.md)
2. [Betrieb mit kleinem Budget](betrieb-mit-kleinem-budget.md)
3. [Spenden und Spendenaufrufe](spenden-und-spendenaufrufe.md)
4. [Ehrenamtliche und Anmeldeformulare](ehrenamtliche-und-anmeldeformulare.md)
5. [Kampagnen und Landingpages](kampagnen-und-landingpages.md)
6. [Barrierefreiheit für gemeinnützige Organisationen](barrierefreiheit-fuer-gemeinnuetzige.md)
7. [Checkliste für gemeinnützige Organisationen](checkliste-fuer-gemeinnuetzige.md)

## Benachbarte Bereiche

- [Vereine und kleine Gruppen](../vereine/README.md): von Ehrenamtlichen geführte Vereine und Gruppen.
- [Newsletter](../newsletter/README.md): Neuigkeiten für Unterstützer per Mail.
- [Mitglieder](../mitglieder/README.md): Mitgliederbereiche und Registrierung.
- [Kosten](../kosten/README.md), einschließlich eines [durchgerechneten Beispiels für eine gemeinnützige Organisation](../kosten/durchgerechnetes-beispiel-gemeinnuetzige-organisation.md).
- [Sicherheit und Datenschutz](../sicherheit/README.md): Grundlagen der DSGVO, Einwilligung und Formulare.
- [Behörden: hier beginnen](../../../../eng-us/help/government/government-start-here.md) für öffentliche Stellen.

## Verwandte Seiten

- [Unternehmen: hier beginnen](../unternehmen-hier-beginnen.md)
- [Welcher Bereich passt zu Ihrer Organisation?](../welcher-bereich-zu-ihrer-organisation-passt.md)
- [Was Sie bauen können](../../../../eng-us/help/creators/website-creators/what-you-can-build.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
