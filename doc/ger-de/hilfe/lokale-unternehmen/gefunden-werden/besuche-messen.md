---
title: Besuche messen
audience: local-business, businesses, customer
category: local-business
order: 27
summary: Wie ein kleines Unternehmen herausfindet, ob die Website Kunden bringt - was Exponential von selbst aufzeichnet (Suchbegriffe der Website-Suche, Aufrufzahlen, Shop-Statistiken, Formulareinsendungen), einen Analysedienst mit bcwebsitestatistics hinzufügen, Datenschutz und Einwilligung, und fünf Zahlen, die es wert sind, beobachtet zu werden.
keywords: Analyse, Statistiken, Besuche, bcwebsitestatistics, Google Analytics, Suchstatistiken, Formulareinsendungen, Shop-Statistiken, Datenschutz, Einwilligung, messen, lokales Unternehmen, analytics, statistics, visits, consent, measuring, local business
type: help
---
# Besuche messen

Diese Seite ist für Inhaber eines kleinen Unternehmens, die wissen wollen, ob die Website ihre Kosten wert ist. Sie
brauchen kein Dashboard voller Diagramme. Ein paar Zahlen, einmal im Monat angesehen, sagen Ihnen das meiste, was Sie
wissen müssen.

## Inhalt

1. [Was Exponential von selbst aufzeichnet](#was-exponential-von-selbst-aufzeichnet)
2. [Einen Analysedienst hinzufügen](#einen-analysedienst-hinzufügen)
3. [Datenschutz und Einwilligung](#datenschutz-und-einwilligung)
4. [Fünf Zahlen, die es wert sind, beobachtet zu werden](#fünf-zahlen-die-es-wert-sind-beobachtet-zu-werden)
5. [Fragen Sie Ihre Kunden](#fragen-sie-ihre-kunden)

## Was Exponential von selbst aufzeichnet

Aus [Besucherstatistiken und Analyse](../../ersteller/website-ersteller/besucherstatistik-und-analyse.md):

| Aufzeichnung | Sagt Ihnen | Wo |
|---|---|---|
| Statistiken der Website-Suche | wonach Besucher auf Ihrer Website suchen und was sie nicht finden | [Website-Suche und wonach Besucher suchen](../../kunden/alltag/website-suche-und-wonach-besucher-suchen.md) |
| Formulareinsendungen | wie viele Kontakt- oder Angebotsanfragen eingegangen sind | **Setup > Gesammelte Informationen** ([Formulareinsendungen](../../ersteller/website-ersteller/formulareinsendungen.md)) |
| Shop-Statistiken | Bestellungen, Bestseller, abgebrochene Warenkörbe | Shop-Dashboard ([Shop-Berichte](../../kunden/handel/shop-berichte.md)) |
| Webserver-Protokolle | Aufrufe pro Seite | Ihr Hoster |

## Einen Analysedienst hinzufügen

Die Erweiterung `bcwebsitestatistics` (in der Standard-`composer.json`) fügt jeder Seite das Skript von Google
Analytics hinzu und kann Shop-Bestellungen über einen Workflow melden
([bcwebsitestatistics](../../../funktionen/6.0/erweiterungen/bcwebsitestatistics.md)). Sie braucht ein Konto bei diesem
Dienst und Ihre eigene Tracking-Kennung in `bcwebsitestatistics.ini`. Einen anderen Analysedienst kann Ihr Ersteller
als Skript im Pagelayout hinzufügen
([Einen Analysedienst hinzufügen](../../ersteller/website-ersteller/besucherstatistik-und-analyse.md#einen-analysedienst-ergänzen)).

Analyse ist ein externer Dienst: Das Konto, seine Bedingungen und seine Daten liegen beim Anbieter.

## Datenschutz und Einwilligung

Ein Analyseskript sendet Daten über Ihre Besucher an ein anderes Unternehmen. In vielen Ländern braucht das ein
Einwilligungsbanner und eine Zeile in Ihrer Datenschutzerklärung. Dies ist keine Rechtsberatung; fragen Sie Ihren
Berater. Siehe [Datenschutz und Einwilligung](../../ersteller/website-ersteller/besucherstatistik-und-analyse.md#datenschutz-und-einwilligung)
und [Grundlagen der DSGVO](../../unternehmen/sicherheit/dsgvo-grundlagen.md).

Wenn Sie überhaupt keine Analyse wollen, reichen die Aufzeichnungen, die Exponential selbst führt, und das Fragen der
Kunden (unten) für ein lokales Unternehmen oft aus.

## Fünf Zahlen, die es wert sind, beobachtet zu werden

| Zahl | Wo | Was eine Änderung bedeutet |
|---|---|---|
| Nachrichten über das Kontaktformular pro Monat | Gesammelte Informationen | die Website macht aus Besuchern Anfragen |
| Anrufe oder Besuche, die die Website erwähnen | fragen Sie an der Theke oder am Telefon | die Website wirkt auch offline |
| Häufigste Suchbegriffe der Website-Suche | Suchstatistiken | etwas, das Besucher wollen, ist schwer zu finden |
| Bestellungen pro Monat (wenn Sie verkaufen) | Shop-Dashboard | der Shop wird genutzt |
| Aufrufe der Seite mit Öffnungszeiten oder Kontakt | Analyse, falls verwendet | Menschen planen zu kommen |

Tragen Sie die Zahlen in eine einfache Monatstabelle ein, und vergleichen Sie mit demselben Monat im Vorjahr; lokale
Unternehmen sind saisonabhängig ([Saisonale Änderungen](../im-betrieb/saisonale-aenderungen.md)).

## Fragen Sie Ihre Kunden

Die billigste und verlässlichste Messung: Fragen Sie neue Kunden „Wie haben Sie uns gefunden?“, und führen Sie einen
Monat lang eine Strichliste. Viele lokale Unternehmen lernen daraus mehr als aus jedem Diagramm.

## Verwandte Seiten

- [Analyse und was Sie messen sollten](../../kunden/alltag/analyse-und-was-sie-messen-sollten.md)
- [Den ROI messen](../../kunden/nutzen/den-roi-messen.md)
- [Wöchentliche und monatliche Routine](../im-betrieb/woechentliche-und-monatliche-routine.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
