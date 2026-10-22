---
title: Archivieren, Entfernen und Aufräumen
audience: ecommerce, customer, admin
category: e-commerce
order: 53
summary: Die drei Wege, auf denen Bestellungen in Exponential die Bestellliste verlassen, und was jeder mit den Daten tut - Archivieren, Entfernen einer einzelnen Bestellung und die Shop-Bereinigung, die alle Bestellungen löscht - mit ihren Ereignissen im Prüfpfad, der Bereinigung von Warenkörben und Wunschlisten und einer sicheren Aufbewahrungspraxis.
keywords: Bestellung archivieren, aus dem Archiv holen, Bestellung entfernen, removeorder, cleanupOrder, Bestellungen löschen, cleanup.php shop, Warenkorb-Bereinigung, Wunschlisten-Bereinigung, Aufbewahrung, Prüfpfad, commerce.order.delete, commerce.order.purge, archive order, unarchive, remove order, purge orders, basket cleanup, wishlist cleanup, retention, audit
type: help
---
# Archivieren, Entfernen und Aufräumen

Diese Seite ist für Shop-Manager, Händler und Administratoren, die die Bestellliste überschaubar halten. Bestellungen
können die Liste auf drei sehr unterschiedliche Weisen verlassen. Zwei davon zerstören Daten, die für die Buchhaltung,
für Kundenfragen oder für Datenschutzanfragen nötig sein können; es lohnt sich daher, genau zu wissen, was jede tut.

## Inhalt

1. [Drei Wege nebeneinander](#drei-wege-nebeneinander)
2. [Archivieren](#archivieren)
3. [Eine Bestellung entfernen](#eine-bestellung-entfernen)
4. [Das Skript zur Shop-Bereinigung](#das-skript-zur-shop-bereinigung)
5. [Warenkörbe und Wunschlisten](#warenkörbe-und-wunschlisten)
6. [Eine Aufbewahrungspraxis](#eine-aufbewahrungspraxis)

## Drei Wege nebeneinander

| | Archivieren | Eine Bestellung entfernen | Shop-Bereinigung |
|---|---|---|---|
| Wo | Bestellliste, Schaltfläche **Archiv** (`shop/archiveorder`) | `shop/removeorder` nach Bestätigung | `update/common/scripts/cleanup.php shop` |
| Richtlinie | `shop/administrate` | `shop/administrate` | Shell-Zugang |
| Bestelldaten | Bleiben erhalten; `is_archived` auf 1 gesetzt | Gelöscht | Alle Bestellungen gelöscht |
| Umkehrbar | Ja, `shop/unarchiveorder` | Nein | Nein |
| Statistik | Archivierte Bestellungen zählen weiter | Weg | Weg |
| Ereignis im Prüfpfad | `commerce.order.archive` / `.unarchive` | `commerce.order.delete` | `commerce.order.purge` |

## Archivieren

`eZOrder::archiveOrder()` setzt nur ein Kennzeichen. Die Bestellung verschwindet aus der Bestellliste und erscheint unter
**Web-Shop > Archiv** (`shop/archivelist`); ihre Posten, Summen, ihr Status und ihr Verlauf bleiben. Archivieren Sie
Bestellungen, sobald sie abgeschlossen sind, damit die Bestellliste nur noch offene Arbeit zeigt. Die eigene
Bestellliste des Kunden und seine Belege funktionieren weiter.

## Eine Bestellung entfernen

`shop/removeorder` fragt nach einer Bestätigung und ruft dann für jede gewählte Bestellung `eZOrder::cleanupOrder()` auf,
was die Bestellzeile, ihre Produktsammlung und Produktzeilen sowie ihren Statusverlauf löscht
(`kernel/classes/ezorder.php`). Die Bestellliste des Standarddesigns zeigt keine Schaltfläche zum Entfernen mehr (der Code
behält das alte `RemoveButton` aus Kompatibilitätsgründen, `kernel/private/classes/views/shop/orderlist.php`); das
Entfernen ist also ein bewusster Schritt.

Entfernen Sie eine Bestellung nur, wenn sie nicht existieren darf: eine Testbestellung auf einer Live-Website oder Daten,
die Sie gesetzlich löschen müssen und nicht aufbewahren müssen. Für Datenschutzanfragen folgen Sie
[Datenexport und Löschung](../../kunden/handel/datenexport-und-loeschung.md) und halten die Anfrage in Ihrem Protokoll
fest ([Vorlage für ein Protokoll der DSGVO-Anfragen](../../kunden/handel/vorlage-dsgvo-anfragenprotokoll.md)).

## Das Skript zur Shop-Bereinigung

`update/common/scripts/cleanup.php` mit dem Namen `shop` entfernt **alle** Warenkörbe, alle Wunschlisten und alle
Bestellungen (`eZBasket::cleanup()`, `eZWishList::cleanup()`, `eZOrder::cleanup()`). Es ist für Test- und
Entwicklungsdatenbanken gedacht, zum Beispiel vor der Übergabe eines Shops, dessen Testbestellungen verschwinden müssen.
Führen Sie es nie auf einem Live-Shop aus. Erstellen Sie auch auf einer Testkopie zuerst eine Sicherung
([Sicherungen und Wiederherstellungen](../../ersteller/website-ersteller/backups-und-wiederherstellungen.md)).

## Warenkörbe und Wunschlisten

| Daten | Entfernt durch | Wann |
|---|---|---|
| Warenkörbe abgelaufener Sessions | Cron-Skript `basket_cleanup.php`, Teil `infrequent` | Wie `site.ini [Session] BasketCleanup*` es festlegt ([Die Shop-Einstellungen in site.ini durchgegangen](../settings/shop-einstellungen-in-site-ini-durchgegangen.md#session-aufräumen-von-warenkörben)) |
| Wunschlisten | Nur durch den Kunden oder durch das Skript zur Shop-Bereinigung | Nie automatisch |
| Temporäre Bestellungen | Nicht durch die Warenkorb-Bereinigung entfernt | Nie als Bestellungen angezeigt ([Temporäre Bestellungen und Aktivierung](../kasse/temporaere-bestellungen-und-aktivierung.md)) |

## Eine Aufbewahrungspraxis

1. Klären Sie mit Ihrer Buchhaltung, wie lange Bestelldaten aufbewahrt werden müssen, und mit Ihrer
   Datenschutzberatung, wie lange Kundendaten aufbewahrt werden dürfen.
2. Archivieren Sie abgeschlossene Bestellungen wöchentlich ([Wöchentliche Checkliste für den Shop](../../kunden/handel/woechentliche-shop-checkliste.md)).
3. Prüfen Sie einmal im Jahr archivierte Bestellungen, die älter als die Aufbewahrungsfrist sind
   ([Betriebskalender](../betrieb/betriebskalender.md)).
4. Stellen Sie vor jedem Entfernen sicher, dass die Zahlen, die Sie brauchen, anderswo erhalten bleiben: monatliche
   Statistiken, das Buchhaltungssystem ([Shop-Kennzahlen und Exporte](../betrieb/shop-kennzahlen-und-exporte.md)).
5. Entfernen Sie in kleinen, protokollierten Stapeln und prüfen Sie danach den Prüfpfad.

## Verwandte Seiten

- [Bestellbetrieb](README.md)
- [Bestellungen im Alltag](../../kunden/handel/bestellungen-im-alltag.md)
- [DSGVO-Anfragen](../../kunden/handel/dsgvo-anfragen.md)
- [Prüfpfad für den Handel](../betrieb/audit-trail-fuer-den-handel.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
