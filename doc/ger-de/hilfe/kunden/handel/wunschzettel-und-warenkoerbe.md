---
title: Wunschzettel und Warenkörbe
audience: customer, admin, businesses
category: customer-success
order: 180
summary: Was ein Shop-Betreiber über Warenkörbe und Wunschzettel in Exponential wissen sollte - abgebrochene Warenkörbe im Dashboard, die Bereinigung von Warenkörben per Cronjob, was mit einem Warenkorb an der Kasse und beim Abmelden geschieht, der Wunschzettel für angemeldete Kunden und die Einstellungen, nach denen Sie Ihren Partner fragen sollten.
keywords: Warenkorb, Wunschzettel, abgebrochene Warenkörbe, Warenkorb-Bereinigung, Cronjob, ClearBasketOnCheckout, ClearBasketOnLogout, RedirectAfterAddToBasket, BasketViewName, basket, shopping cart, wishlist, abandoned baskets, basket cleanup, cron job
type: help
---
# Wunschzettel und Warenkörbe

Diese Seite ist für den Eigentümer und die Shop-Leitung. In Warenkörben und auf Wunschzetteln zeigen Kunden ihr
Interesse, bevor sie kaufen, und einige Einstellungen bestimmen, wie sich beide verhalten. Diese Seite erklärt, worauf
Sie achten sollten und was Sie Ihren Partner fragen; der Wunschzettel selbst ist unter
[Rabatte und Wunschzettel für Unternehmen](../../../../eng-us/help/businesses/stores/discounts-and-wishlists.md) beschrieben, die Schritte
an der Kasse unter [Warenkorb und Bestellablauf](../../unternehmen/shops/warenkorb-und-bestellablauf.md).

## Warenkörbe im Dashboard

Das Shop-Dashboard zeigt laufende und abgebrochene Warenkörbe und listet unter „Work to do“ die neuesten Warenkörbe und
nicht bestätigte Bestellvorgänge auf ([Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md)).
Nutzen Sie sie als Signale:

| Was Sie sehen | Was es bedeuten kann | Was zu tun ist |
|---|---|---|
| Abgebrochene Warenkörbe nehmen nach einer Änderung zu | Versandkosten, Preise oder das Kassenformular schrecken Kunden ab | Prüfen, was sich in dieser Woche geändert hat |
| Viele nicht bestätigte Bestellvorgänge | Kunden brechen auf der Bestätigungsseite oder beim Zahlungsschritt ab | Eine Testbestellung aufgeben; siehe [Zahlungsanbieter](zahlungsanbieter.md) |
| Warenkörbe nehmen zu, Bestellungen nicht | Etwas blockiert die Kasse | Noch heute die ganze Kasse testen |

## Wie sich Warenkörbe verhalten

Das sind Einstellungen in `settings/site.ini [ShopSettings]`; Ihr Partner ändert sie in einem Override.

| Einstellung | Ausgeliefert | Was sie für Ihre Kunden bedeutet |
|---|---|---|
| `ClearBasketOnCheckout` | `disabled` | Der Warenkorb wird erst geleert, wenn die Zahlung abgeschlossen ist, sodass ein Kunde, der die Zahlung abbricht, ihn noch hat |
| `ClearBasketOnLogout` | `disabled` | Das Abmelden leert den Warenkorb nicht |
| `RedirectAfterAddToBasket` | `basket` | Nach **Zum Warenkorb hinzufügen** sieht der Kunde den Warenkorb; `reload` lässt ihn dort, wo er war |

Die Kommentare in `settings/site.ini` erklären, dass manche Zahlungssysteme `ClearBasketOnCheckout=enabled` benötigen;
sprechen Sie mit Ihrem Partner, bevor Sie das ändern.

Die Warenkorbseite kann einen anderen Namen bekommen, zum Beispiel `/shop/cart/`, mit `settings/shop.ini [BasketSettings]
BasketViewName`; `/shop/basket/` funktioniert für alte Links weiterhin
([Name der Warenkorb-View im Shop](../../../../eng-us/bc/6.0/shop-basket-view-name.md)).

## Bereinigung von Warenkörben

Alte Warenkörbe werden vom Cronjob `basket_cleanup.php` entfernt, der im seltenen Teil der ausgelieferten
`settings/cronjob.ini` steht. `site.ini [Session] BasketCleanup=cronjob` ist die ausgelieferte Einstellung, die
Bereinigung findet also nur statt, wenn die Cronjobs laufen ([Cronjobs](../../../anleitungen/cronjobs.md)).

- [ ] Fragen Sie Ihren Hoster, ob der seltene Cronjob-Teil läuft, und wie oft.
- [ ] Wenn die „Nächsten Schritte“ des Dashboards vor der Warenkorb-Bereinigung und in Sitzungen gespeicherten
  Warenkörben warnen, geben Sie das an Ihren Partner weiter ([Shop-Dashboard und Bestellstatus](../../../../eng-us/features/6.0/store-dashboard.md)).

## Der Wunschzettel

Mit dem Wunschzettel (`shop/wishlist`, Richtlinie `shop/buy`) können angemeldete Kunden Produkte für später
aufbewahren. Er gehört zum Benutzerkonto, und zum Hinzufügen ist ein registrierter, angemeldeter Benutzer nötig
([Rabatte und Wunschzettel für Unternehmen](../../../../eng-us/help/businesses/stores/discounts-and-wishlists.md)). Das Design Ihrer
Website braucht die Schaltfläche **Add to wishlist** und einen Link zum Wunschzettel.

| Frage des Betreibers | Antwort |
|---|---|
| Können Gäste ihn nutzen? | Nein; er erfordert ein Konto |
| Können Kunden ihn teilen? | Der Kernel kennt keine geteilten Wunschzettel (dieselbe Seite) |
| Kann ich sehen, was sich Kunden wünschen? | Nicht in einem Shop-Bericht; die Shop-Ansichten haben keinen Bericht zu Wunschzetteln (`kernel/shop/module.php`) |

Der Wunschzettel ist ein guter Grund, Konten anzubieten, wenn Ihre Produkte gut überlegte Anschaffungen sind
([Kundenkonten](kundenkonten.md)).

## Personenbezogene Daten

Warenkörbe sind an eine Sitzung gebunden und Wunschzettel an einen Benutzer (`ezbasket`, `ezwishlist`), wie unter
[Personenbezogene Daten in Exponential](../../../../eng-us/help/businesses/security/personal-data-in-exponential.md) aufgeführt.
Berücksichtigen Sie sie, wenn Sie die Daten Ihres Shops in Ihrer Datenschutzerklärung beschreiben.

## Verwandte Seiten

- [Warenkorb und Bestellablauf](../../unternehmen/shops/warenkorb-und-bestellablauf.md)
- [Rabatte und Wunschzettel für Unternehmen](../../../../eng-us/help/businesses/stores/discounts-and-wishlists.md)
- [Shop-Berichte](shop-berichte.md)
- [Warenkorb, Kasse und Bestellungen](../../../../eng-us/help/creators/website-creators/basket-checkout-and-orders.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
