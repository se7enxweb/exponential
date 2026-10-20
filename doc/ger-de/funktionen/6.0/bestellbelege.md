# Dauerhafte Bestellbelege

Diese Seite ist für Shop-Betreiber, die möchten, dass Kunden ihren Bestellbeleg jederzeit erreichen. Jede Bestellung kann
einen Beleg mit einer Adresse haben, die nie abläuft. Ein Kunde kann ihn als Lesezeichen speichern, drucken,
herunterladen oder über den Link in der Bestell-E-Mail auf jedem Gerät öffnen, mit oder ohne Konto.

Vorher war `shop/orderview/<id>` der einzige Link. Bei einer Bestellung ohne Konto funktionierte er nicht mehr, sobald die
Browsersitzung abgelaufen war, und seine Adresse sagte nichts darüber, wer ihn sehen darf.

## Einschalten

1. Tragen Sie dies in `settings/override/shop.ini.append.php` ein:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [OrderViewSettings]
   OrderLinkView=orderreceipt

   */ ?>
   ```

2. Leeren Sie den INI- und den Template-Cache:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   php bin/php/ezcache.php --clear-tag=template --allow-root-user
   ```

3. Geben Sie eine Testbestellung auf. Die Seite nach dem Bestellabschluss, die Bestelllisten und die Bestellansicht des
   Kunden im Standard-Design sowie die Bestellbestätigung per E-Mail verlinken jetzt auf `shop/orderreceipt/<token>`.

Diese Stellen nehmen ihren Link von der Bestellung (`eZOrder` `link_url`), die je nach Einstellung der Beleg oder
`shop/orderview` ist. Die Administrationsoberfläche verlinkt Bestellungen weiterhin auf `shop/orderview`, wo Bestellungen
verwaltet werden.

## Die Adresse

```
https://example.com/shop/orderreceipt/<token>
```

Das Token ist signiert: Es trägt die Bestell-ID, ihre Erstellungszeit und ihren Kunden, signiert mit bcrypt bei niedrigen
Kosten über einen SHA-256 aus einem Geheimnis und den Nutzdaten. Das Salt wird aus demselben Geheimnis abgeleitet, sodass
jede Bestellung genau eine Beleg-Adresse hat, für immer, und nichts in der Datenbank gespeichert wird. Das falsch
geschriebene `shop/orderreciept/<token>` leitet dauerhaft auf die kanonische Schreibweise weiter. `eZOrder` erhält
`receipt_url`.

## Wer einen Beleg öffnen kann

| Besucher | Öffnet den Beleg? |
|---|---|
| Jeder, der den Link hat | Ja, aus jedem Browser und auf jedem Gerät, mit oder ohne Konto (seit dem 26. September 2026). Das signierte Token ist der Schlüssel, sodass der Link so privat ist wie der Beleg. |
| Der angemeldete Kunde, der die Bestellung aufgegeben hat | Ja, ohne den Link |
| Ein Benutzer mit `shop / administrate` | Ja, jeden Beleg |
| Jeder mit einem falschen Token | Nein: dieselbe Antwort wie bei einem Beleg, der nicht seiner ist, das Anmeldeformular |

Die Seite bietet **Drucken** (nur den Beleg, gleich, welches Website-Design ihn umgibt) und **Herunterladen** (eine
eigenständige `receipt-<number>.html`). Die Fußzeile sagt, dass der Link privat gehalten werden sollte.

Die erste Version (24. September) verlangte eine Anmeldung, sodass ein Kunde, der auf den Link in der E-Mail klickte, auf
einer Anmeldeseite landete. Am 26. September wurde das Token selbst zum Schlüssel, und die Einstellung
`AnonymousOrderLinkView` wurde entfernt, weil jetzt jede Bestellung auf ihren Beleg verlinkt, wenn Sie es verlangen.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich | Bedeutung |
|---|---|---|---|---|---|
| `settings/shop.ini` | `OrderViewSettings` | `OrderLinkView` | `orderview` | global | Worauf das System eine aufgegebene Bestellung verlinkt: nach dem Bestellabschluss, in der Bestellliste und Historie des Kunden und in der Bestellbestätigung per E-Mail. `orderview` behält das klassische `shop/orderview/<id>`; `orderreceipt` verlinkt `shop/orderreceipt/<token>`, und die E-Mail trägt dann die vollständige Adresse des Belegs. |
| `settings/shop.ini` | `OrderReceiptSettings` | `Secret` | leer | global | Geheimnis, das die Beleg-Adressen signiert. Leer bedeutet, dass bei der ersten Verwendung eines erzeugt und in `<VarDir>/secrets/orderreceipt.key` aufbewahrt wird (Modus 0600). Setzen Sie es (32 Zeichen oder mehr) in einer Überschreibungsdatei, nie in der mitgelieferten Datei, um Belege zwischen Installationen zu teilen, die eine Datenbank teilen. **Eine Änderung ändert jede Beleg-Adresse.** |
| `settings/shop.ini` | `OrderReceiptSettings` | `BcryptCost` | `5` | global | bcrypt-Kosten der Signatur, 4 bis 6. Belege, die mit früheren Kosten erstellt wurden, funktionieren weiter. |

## Eine Korrektur am Bestellvorgang nebenbei

**Abbrechen** im Schritt „Bestellung bestätigen“ kam früher mit „You have no products in your basket“ zurück, weil der
Bestellvorgang den Warenkorb an seine temporäre Bestellung hängt und die Suche nach dem Warenkorb ihn dann nicht fand.
`eZBasket::currentBasket()`:

- nimmt jetzt den Warenkorb der Sitzung, solange seine Bestellung noch temporär ist;
- gibt einen Warenkorb (mit allen Artikeln) zurück, sobald seine temporäre Bestellung durch Abbrechen verworfen wurde;
- gibt nie einen Warenkorb zurück, dessen Bestellung abgeschlossen ist;
- findet für eine Anfrage ohne Sitzungs-ID keinen gespeicherten Warenkorb.

Die Ansicht „Bestellung bestätigen“ führt die Bestätigungsoperation nicht mehr erneut für eine bereits aufgegebene
Bestellung aus (Zurück-Schaltfläche, Lesezeichen, zweiter Tab). Sie geht zur Bestellansicht oder, wenn keine Bestellung
wartet, zum Warenkorb.

## Verwandte Seiten

- [Shop-Dashboard und Bestellstatus](../../../eng-us/features/6.0/store-dashboard.md), [Sortierung der Bestellliste](sortierung-der-bestellliste.md)
- [Name der Warenkorbansicht im Shop](../../../eng-us/bc/6.0/shop-basket-view-name.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik, 16. bis 30. September 2026](../../../eng-us/history/2026/2026-09b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
