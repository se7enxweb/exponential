---
title: Betriebskalender
audience: ecommerce, customer, businesses
category: e-commerce
order: 80
summary: Ein Jahr wiederkehrender Arbeit für einen Exponential-Shop, nach Häufigkeit und nach Rolle - was die täglichen, wöchentlichen und monatlichen Routinen auslassen (vierteljährlich, jährlich, vor Spitzenzeiten, nach Updates), mit dem Bildschirm, der Einstellung oder der Seite für jede Aufgabe.
keywords: Betriebskalender, Shop-Kalender, jährliche Aufgaben, vierteljährliche Aufgaben, Wechselkurse, USt-Prüfung, Aufbewahrung, Archiv, Zahlungsanbieter, Zertifikate, Hochsaison, Updates, Rollenprüfung, operations calendar, store calendar, yearly tasks, quarterly tasks, exchange rates, VAT review, retention, archive, payment provider, certificates, peak season, updates, roles review, audit verify
type: help
---
# Betriebskalender

Diese Seite richtet sich an Händler, Shop-Manager und die Agenturen, die Shops betreuen. Die täglichen, wöchentlichen
und monatlichen Routinen stehen unter [Ihren Shop führen](../../kunden/handel/README.md); dieser Kalender ergänzt
die selteneren Aufgaben, die leicht vergessen werden, und bringt alle nach Häufigkeit auf eine Seite.

## Inhalt

1. [Jeden Tag, jede Woche und jeden Monat](#jeden-tag-jede-woche-und-jeden-monat)
2. [Jedes Quartal](#jedes-quartal)
3. [Jedes Jahr](#jedes-jahr)
4. [Vor einer Spitzenzeit](#vor-einer-spitzenzeit)
5. [Nach jedem Update](#nach-jedem-update)
6. [Hintergrundjobs, die Sie beobachten sollten](#hintergrundjobs-die-sie-beobachten-sollten)

## Jeden Tag, jede Woche und jeden Monat

| Häufigkeit | Routine |
|---|---|
| Täglich | [Tägliche Shop-Routine](../../kunden/handel/taegliche-shop-routine.md) |
| Wöchentlich | [Wöchentliche Checkliste für den Shop](../../kunden/handel/woechentliche-shop-checkliste.md) |
| Monatlich | [Monatliche Checkliste für den Shop](../../kunden/handel/monatliche-shop-checkliste.md), [Shop-Kennzahlen und Exporte](shop-kennzahlen-und-exporte.md) |

Für Shops mit mehreren Währungen und automatischen Preisen ergänzen Sie **Auto-Umrechnungsfaktoren aktualisieren** und
**Auto-Preis aktualisieren** unter **Web-Shop > Währungen** in der von Ihnen festgelegten Häufigkeit
([Handler für Wechselkurse](../erweitern/wechselkurs-handler.md#kurse-und-preise-aktuell-halten)).

## Jedes Quartal

| Aufgabe | Wer | Wo |
|---|---|---|
| Rollen und Richtlinien der Shop-Mitarbeiter prüfen; Ausgeschiedene entfernen | Händler | [Statusübergänge und Berechtigungen](../bestellungen/statusuebergaenge-und-berechtigungen.md) |
| USt-Typen und -Regeln mit den aktuellen Sätzen abgleichen | Händler mit Steuerberater | [USt-Checkliste](../../kunden/handel/mehrwertsteuer-checkliste.md) |
| Rabattgruppen und ihre Mitglieder prüfen | Händler | [Rabatte](../../kunden/handel/rabatte.md) |
| Eine echte Bestellung je Zahlungsart aufgeben | Shop-Manager | [Einen Shop testen](einen-shop-testen.md) |
| Die „Nächsten Schritte“ des Dashboards lesen und jeden umsetzen | Shop-Manager | Shop-Dashboard |
| Die Audit-Kette des Handels prüfen | Administrator | `./console exp:audit verify --channel=commerce` |

## Jedes Jahr

| Aufgabe | Wer | Wo |
|---|---|---|
| Aufbewahrung: archivierte Bestellungen prüfen, die älter als der vereinbarte Zeitraum sind | Händler, Datenschutzberater | [Archivieren, Entfernen und Aufräumen](../bestellungen/archivieren-entfernen-und-aufraeumen.md#eine-aufbewahrungspraxis) |
| Zahlungsanbieter: Vertrag, Gebühren, Zugangsdaten, Callback-Adresse | Händler, Entwickler | [Durchgang durch paymentgateways.ini](../settings/paymentgateways-ini-durchgegangen.md) |
| Selbstbewertung der Kartensicherheit, falls Kartendaten Ihre Site berühren | Händler | [Kartenzahlungen und PCI-Grundlagen](../../unternehmen/shops/kartenzahlungen-und-pci-grundlagen.md) |
| Versandtarife und -bedingungen | Händler | [Versand und Abwicklung](../../kunden/handel/versand-und-abwicklung.md) |
| Prüfung der Barrierefreiheit des Shops | Entwickler, Agentur | [Barrierefreiheit im Shop](barrierefreiheit-im-shop.md) |
| Wiederherstellungsübung der Datenbank mit Bestellungen | Hoster oder Partner | [Wiederherstellungsübungen](../../unternehmen/zuverlaessigkeit/wiederherstellungsuebungen.md) |
| Die Kampagnen des Jahres und ihre Landingpages planen | Händler, Redakteure | [Content-Commerce mit Layouts](content-commerce-mit-layouts.md) |

## Vor einer Spitzenzeit

Vier bis sechs Wochen vor einer Aktion oder Saison: [Vorbereitung auf die Hochsaison](../../kunden/handel/vorbereitung-auf-die-hochsaison.md)
und [Shop-Performance zu Spitzenzeiten](../../unternehmen/shops/shop-leistung-zu-spitzenzeiten.md). Ergänzen Sie:
Landingpages mit Zeitbedingungen, Mitarbeiterrollen für zusätzliche Helfer und eine Testbestellung am Vortag.

## Nach jedem Update

Nach einem Update von Exponential oder einer Erweiterung: eine Testbestellung je Zahlungsart, der Zahlungs-Callback,
die Bestellmail und „So funktioniert Ihr Shop“ im Dashboard im Vergleich zu vorher
([Einen Shop testen](einen-shop-testen.md#nach-jedem-update)).

## Hintergrundjobs, die Sie beobachten sollten

| Job | Cronjob-Teil | Wenn er stoppt |
|---|---|---|
| `basket_cleanup.php` | `infrequent` | Alte Warenkörbe häufen sich an |
| `workflow.php` | `frequent` | Verschobene Workflow-Prozesse werden nie fortgesetzt |
| `audit.php` | `frequent` | Audit-Einträge werden nicht indiziert, rotiert oder archiviert |

Siehe [Cronjobs](../../../anleitungen/cronjobs.md).

## Verwandte Seiten

- [Einen Shop betreiben](README.md)
- [Das Jahr des Inhabers auf einen Blick](../../kunden/das-jahr-des-betreibers.md)
- [Entscheidungs-Checklisten](entscheidungs-checklisten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
