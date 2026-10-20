---
title: Ihren Shop betreiben
audience: customer, businesses, admin
category: customer-success
order: 1
summary: Der Bereich für Shop-Betreiber - Abläufe, Checklisten und Entscheidungen für den Betrieb des in Exponential eingebauten Shops nach dem Start, mit Verweisen auf die Referenzseiten zu jeder Ansicht und jeder Einstellung.
keywords: Shop-Betreiber, Shop betreiben, Onlinehandel, Onlineshop, Bestellungen, Zahlungen, Mehrwertsteuer, Versand, Shop-Routine, Shop-Checklisten, store owner, shop owner, running a store, e-commerce, webshop, orders, payments, VAT, shipping
type: help
---
# Ihren Shop betreiben

Dieser Bereich ist für Organisationen, die bereits mit dem in Exponential eingebauten Shop verkaufen: die
Eigentümer, die Shop-Leitung, die Person, die packt und versendet, und alle, die mit dem Hoster oder Partner sprechen.
Es geht um den laufenden Shop: was jeden Tag, jede Woche und jeden Monat zu tun ist, wer im Team es erledigt, was zu
prüfen ist und was Sie Ihren Anbieter fragen sollten.

Der Shop ist Teil von Exponential selbst. Die `README.md` des Repositorys nennt „Advanced e-Commerce Stores (Built in)“
unter den wichtigsten Funktionen, und alle Shop-Ansichten sind Views eines einzigen Moduls, `kernel/shop/module.php`.
Was der Shop kann und was nicht, ist für Entscheider unter [Was der eingebaute Shop leistet](../../../../eng-us/help/businesses/stores/what-the-built-in-shop-does.md)
beschrieben. Diese Seiten wiederholen das nicht; sie sagen Ihnen, wie Sie ihn betreiben.

## Hier beginnen

| Wenn Sie ... möchten | Lesen Sie |
|---|---|
| wissen, worauf Sie jeden Morgen schauen | [Tägliche Shop-Routine](taegliche-shop-routine.md) |
| eine ruhige Woche abarbeiten | [Wöchentliche Shop-Checkliste](woechentliche-shop-checkliste.md) |
| einen Monat abschließen | [Monatliche Shop-Checkliste](monatliche-shop-checkliste.md) |
| sich auf einen Sale oder das Weihnachtsgeschäft vorbereiten | [Vorbereitung auf die Hochsaison](vorbereitung-auf-die-hochsaison.md) |
| schnell eine Antwort finden | [Shop-FAQ für Betreiber](shop-faq.md) |

## Katalog und Preise

- [Produkte und Katalog](produkte-und-katalog.md): den Katalog aufgeräumt halten, wer was pflegt.
- [Ein Produkt anlegen](ein-produkt-anlegen.md): die Checkliste des Betreibers für jedes neue Produkt.
- [Preise und Währungen](preise-und-waehrungen.md): Preise, Währungen und Wechselkurse sicher ändern.
- [USt-Typen und USt-Regeln](ust-typen-und-ust-regeln.md) und die [Mehrwertsteuer-Checkliste](mehrwertsteuer-checkliste.md).
- [Rabatte](rabatte.md): Gruppenrabatte und Aktionen von Hand durchführen.

## Bestellungen und Zahlungen

- [Bestellungen im Alltag](bestellungen-im-alltag.md) und [Bestellstatus](bestellstatus.md).
- [Zahlungsanbieter](zahlungsanbieter.md), [PayPal](paypal.md), [Kartenzahlungen und PCI](kartenzahlungen-und-pci.md).
- [Versand und Abwicklung](versand-und-abwicklung.md).
- [Retouren und Erstattungen](retouren-und-erstattungen.md).
- [Rechnungen und Belege](rechnungen-und-belege.md) und [Bestellbestätigung per E-Mail](bestellbestaetigung-per-e-mail.md).

## Kunden und ihre Daten

- [Kundenkonten](kundenkonten.md) und [Wunschzettel und Warenkörbe](wunschzettel-und-warenkoerbe.md).
- [Shop-Berichte](shop-berichte.md): das Dashboard und die Statistik als Routine.
- [DSGVO-Anfragen](dsgvo-anfragen.md), die [Vorlage für ein DSGVO-Anfragenprotokoll](vorlage-dsgvo-anfragenprotokoll.md) und
  [Datenexport und Löschung](datenexport-und-loeschung.md).

## Wer üblicherweise was macht

| Rolle in Ihrem Team | Typische Arbeit im Shop | Benötigte Berechtigung (`kernel/shop/module.php`) |
|---|---|---|
| Eigentümer | Entscheidungen über Preise, Mehrwertsteuer, Zahlung, Rabatte; Monatszahlen | `shop/setup`, `shop/administrate` |
| Shop-Leitung | Bestellungen, Kunden, das Dashboard, Kundenfragen | `shop/administrate`, `shop/setstatus` |
| Packen und Versand | Führt Bestellungen durch die Status der Auftragsabwicklung | `shop/setstatus`, begrenzt durch `FromStatus` und `ToStatus` |
| Produktredaktion | Produktseiten, Bilder, Preise als Felder des Produkts | Inhalte in den Produktordnern bearbeiten |
| Hoster oder Partner | Einstellungen, Zahlungsanbieter, Cronjobs, Backups, Updates | Zugriff auf Server und Einstellungen |

Die Rollen selbst richtet Ihr Administrator ein ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).

> **Hinweis**
> Das Shop-Modul hat fünf Funktionen: `buy`, `administrate`, `setup`, `edit_status` und `setstatus`
> (`kernel/shop/module.php`). Geben Sie jeder Person die kleinste Menge, die ihre Arbeit abdeckt.

## Verwandte Seiten

- [Was der eingebaute Shop leistet](../../../../eng-us/help/businesses/stores/what-the-built-in-shop-does.md)
- [Einen Shop im Alltag betreiben (für Unternehmen)](../../../../eng-us/help/businesses/stores/running-a-store-day-to-day.md)
- [Einen Shop betreiben: Überblick](../../../../eng-us/help/creators/website-creators/shop-overview.md)
- [Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
