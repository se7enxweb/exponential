# Die Bestellliste nach jeder Spalte sortieren

Diese Seite ist für Shop-Administratoren, die mit **Shop > Bestellungen** arbeiten, und für Designer, die ihre eigene
Bestellliste bauen. Jede Spaltenüberschrift der Bestellliste sortiert sie jetzt. Vorher ließ sich die Liste nur nach
Zeit oder Kunde sortieren, über ein Paar von Einstellungslinks über der Tabelle. Hinzugefügt am 01.10.2026.

## Verwenden

1. Öffnen Sie **Shop > Bestellungen**.
2. Klicken Sie auf eine Überschrift: ID, Kunde, eine der beiden Summen, Zeit oder Status.
3. Klicken Sie erneut auf dieselbe Überschrift, um die Richtung umzukehren.

Spalte und Richtung sind Teil der Adresse, wie in den anderen Admin-Listen:

    /shop/orderlist/(sort)/<column>/(dir)/<asc|desc>

| Spalte | Sortiert nach |
|---|---|
| `id` | Bestell-ID |
| `customer` | Kunde |
| `total_ex_vat` | Summe ohne MwSt. |
| `total_inc_vat` | Summe mit MwSt. |
| `created` | Zeit |
| `status` | Status |

Beispiel: `/shop/orderlist/(sort)/total_inc_vat/(dir)/desc` listet die größten Bestellungen zuerst.

Blättern, Neuladen, das Speichern einer Statusänderung und ein kopierter Link behalten die Sortierung bei. Ohne diese
Parameter entscheiden wie bisher die gespeicherten Einstellungen. Die Template-Variable `sort_field` behält ihre
bisherigen Werte, sodass Website-Designs, die eine eigene Sortierauswahl zeigen, weiter funktionieren.

## Wie es funktioniert

- `eZOrder::activeSorted()` sortiert die ganze Liste in der Datenbank, mit einer Abfrage auf jeder Datenbank-Engine.
- Die Summen verwenden dieselbe Arithmetik wie die Bestellansicht.
- Bei Gleichstand entscheidet die Bestell-ID, sodass keine Bestellung auf zwei Seiten erscheint.
- Die Spalten werden gegen `eZOrder::sortColumnsForList()` geprüft.
- Die Überschriften verwenden das gemeinsame `parts/sortheader.tpl`, wie die Rollenliste. In `admin3` übernimmt der
  Sortierpfeil die Farbe der Überschrift, sodass er auf der dunklen Überschriftenleiste sichtbar ist; in `admin4` ist
  die sortierte Spalte lesbar.
- Die Ansicht ist `Exponential\View\Kernel\Shop\Orderlist` (`kernel/private/classes/views/shop/orderlist.php`).
  Sortierung und Richtung kommen als Benutzerparameter der Ansicht an, weshalb sie `(sort)/...` geschrieben werden und
  nicht als einfache Modulparameter.

Den Code ohne Browser prüfen:

```bash
grep -n "sort" kernel/private/classes/views/shop/orderlist.php
```

## Verwandte Seiten

- [Blättern in Admin-Listen](seitenweise-listen-im-admin.md)
- [Das Design admin4](admin4-design.md)
- [Shop-Dashboard](shop-dashboard.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik Oktober 2026](../../../eng-us/history/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
