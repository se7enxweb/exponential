---
title: Einen Leistungskatalog aufbauen
audience: government, website-creator, project-manager, admin
category: public-sector
order: 10
summary: Index der Anleitungen zum Aufbau eines Katalogs öffentlicher Leistungen in Exponential - eine Klasse für Leistungen, Seiten in einfacher Sprache, Lebenslagen und Themen, Leistungen finden, Ansprechpartner, Links zu Online-Verfahren, Pflege und Verbesserung.
keywords: Leistungskatalog, öffentliche Leistungen, Leistungsseiten, Lebenslagen, Themen, A bis Z, Ansprechpartner, Online-Verfahren, Verantwortung für Inhalte, Überprüfungsdaten, service catalog, public services, service pages, life events, topics, A to Z, contact points, online transactions, content ownership, review dates
type: help
---
# Einen Leistungskatalog aufbauen

Dieser Ordner richtet sich an Site-Ersteller, Projektleiter und Administratoren, die den Teil einer Site des
öffentlichen Sektors aufbauen, der Leistungen auflistet: was Menschen beantragen, melden, buchen, bezahlen oder
anfragen können. Ein Leistungskatalog ist eine Reihe einheitlicher Leistungsseiten, die Menschen nach Thema, nach
Lebenslage, nach Name und über die Suche finden können und die jeweils dorthin verweisen, wo die Leistung tatsächlich in
Anspruch genommen wird. Die Anleitungen zeigen, wie Sie das mit den Inhaltsklassen, Schlagworten, Beziehungen und der
Suche von Exponential bauen, und wo das Produkt endet.

## Inhalt

1. [Die Seiten](#die-seiten)
2. [Lesereihenfolge](#lesereihenfolge)
3. [Was Exponential bietet und was nicht](#was-exponential-bietet-und-was-nicht)

## Die Seiten

| Seite | Die Antwort in einer Zeile |
|---|---|
| [Leistungen modellieren](leistungen-modellieren.md) | Eine Klasse für Leistungen mit Pflichtfeldern für Voraussetzungen, Kosten, Antragsweg, Kontakt und Überprüfungsdatum |
| [Die Leistungsseite und einfache Sprache](die-leistungsseite-und-einfache-sprache.md) | Eine feste Reihenfolge der Seite und Schreibregeln, damit jede Leistungsseite dieselben Fragen beantwortet |
| [Lebenslagen und Themen](lebenslagen-und-themen.md) | Schlagworte für Themen und Lebenslagen, damit eine Leistung überall erscheint, wo Menschen sie suchen |
| [Leistungen finden](leistungen-finden.md) | Menüs, eine Liste von A bis Z, Suche und die Wörter, die Menschen verwenden |
| [Ansprechpartner und Dienststellen](ansprechpartner-und-dienststellen.md) | Ein Objekt je Schalter oder Dienststelle, von jeder Leistung verknüpft, die sie bearbeitet |
| [Auf Online-Verfahren verlinken](auf-online-verfahren-verlinken.md) | Klare Links zu den Systemen, in denen Menschen beantragen oder bezahlen, und was nicht im CMS gebaut werden sollte |
| [Leistungen aktuell halten](leistungen-aktuell-halten.md) | Verantwortliche, Überprüfungsdaten, Listen überfälliger Seiten und eine monatliche Routine |
| [Messen und verbessern](messen-und-verbessern.md) | Suchstatistik, Rückmeldeformulare und Analysen, die den Datenschutz achten |

## Lesereihenfolge

1. **Leistungen modellieren** und **Die Leistungsseite und einfache Sprache**: Sie legen jede Seite fest.
2. **Lebenslagen und Themen** und **Leistungen finden**: wie Menschen dorthin gelangen.
3. **Ansprechpartner und Dienststellen** und **Auf Online-Verfahren verlinken**: worauf jede Seite verweist.
4. **Leistungen aktuell halten** und **Messen und verbessern**: die Arbeit nach dem Start.

Nach oben: [Eine Site für den öffentlichen Sektor aufbauen](../README.md), der Index aller Anleitungen zum Aufbauen,
und [Behörden: hier beginnen](../../verwaltungen-hier-beginnen.md), der Anfang des Bereichs für Behörden. Die Seiten zu den
Verwaltungsebenen beschreiben Leistungen für jede Art von Organisation, zum Beispiel
[Ratssitzungen und kommunale Leistungen](../../../../../eng-us/help/government/levels/municipal-and-local/council-meetings-and-local-services.md).

## Was Exponential bietet und was nicht

| Geboten | Lesen |
|---|---|
| Inhaltsklassen mit Pflichtfeldern | [Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md) |
| Schlagworte mit Baum und Synonymen (eztags) | [Verschlagworten mit eztags](../../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md) |
| Beziehungen und mehrere Orte je Objekt | [Beziehungen und Wiederverwendung](../../../../../eng-us/help/creators/website-creators/relations-and-reuse.md) |
| Eingebaute Suche, oder eZ Find mit Solr | [Site-Suche](../../../../../eng-us/help/creators/website-creators/site-search.md) |
| Suchstatistik | **Setup > Suchstatistiken** |
| Formulare mit dem Information Collector | [Formulare mit dem Information Collector](../../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md) |

| Nicht geboten | Übliche Antwort |
|---|---|
| Fallbearbeitung, Zahlungen, Terminvergabe | getrennte Systeme, von den Leistungsseiten verlinkt |
| Ein strukturierter Export als Leistungsverzeichnis (zum Beispiel im Format eines nationalen Leistungsverzeichnisses) | Templates oder ein Exportskript, für Ihr Format entwickelt |
| Automatische Erinnerungen an Überprüfungen per Mail | eine Liste überfälliger Seiten in einem Template, nach Plan geprüft |

## Verwandte Seiten

- [Verwaltungsebenen](../../../../../eng-us/help/government/levels/README.md)
- [Ihre Website planen](../../../../../eng-us/help/creators/website-creators/planning-your-website.md)
- [Ihr Inhaltsmodell entwerfen](../../../../../eng-us/help/creators/website-creators/designing-your-content-model.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
