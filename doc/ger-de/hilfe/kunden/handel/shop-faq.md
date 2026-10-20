---
title: Shop-FAQ für Betreiber
audience: customer, businesses, admin
category: customer-success
order: 260
summary: Kurze Antworten auf die Fragen, die Betreiber eines laufenden Exponential-Shops am häufigsten stellen - Bestellungen, Zahlungen, Preise, Mehrwertsteuer, Versand, Kunden, Daten und Berichte - jeweils mit einem Verweis auf die Seite mit der Routine und der Quelle.
keywords: Shop-FAQ, Fragen von Betreibern, Bestellungen, Zahlungen, PayPal, Mehrwertsteuer, Versand, Erstattungen, Belege, Kunden, DSGVO, Berichte, Export, store FAQ, owner questions, orders, payments, VAT, shipping, refunds, receipts, customers, GDPR, reports
type: help
---
# Shop-FAQ für Betreiber

Diese Seite beantwortet die Fragen, die Eigentümer und Shop-Leitungen stellen, sobald ihr Shop läuft. Fragen dazu, ob
der Shop vor dem Start zu einem Unternehmen passt, beantwortet die [Shop-FAQ für Unternehmen](../../../../en/us/help/businesses/stores/store-faq.md).

## Bestellungen

**Wo sehe ich, was heute zu tun ist?**
Im Shop-Dashboard unter „Work to do“, die Bestellungen, die am längsten auf den Shop warten, zuerst.
Siehe [Tägliche Shop-Routine](taegliche-shop-routine.md).

**Welche Status sollte mein Team verwenden?**
Nur die, die Ihr Ablauf braucht; schreiben Sie sie auf das Arbeitsblatt für den Statusablauf. Siehe [Bestellstatus](bestellstatus.md).

**Kann ich verhindern, dass die Aushilfe in der Verpackung Bestellungen erstattet?**
Ja. Die Berechtigung zum Ändern von Status, `shop/setstatus`, lässt sich mit `FromStatus` und `ToStatus` einschränken.
Siehe [Bestellungen im Alltag](bestellungen-im-alltag.md).

**Bekommt der Kunde sein Geld zurück, wenn ich „Refunded“ setze?**
Nein. Eine Statusänderung bewegt nie Geld; erstatten Sie im Konto bei Ihrem Zahlungsanbieter und setzen Sie dann den
Status. Siehe [Retouren und Erstattungen](retouren-und-erstattungen.md).

**Sollte ich alte Bestellungen entfernen?**
In der Regel archivieren; entfernen Sie sie erst, wenn Ihre buchhalterische Frist abgelaufen ist. Siehe [Datenexport und Löschung](datenexport-und-loeschung.md).

## Zahlungen

**Ein Kunde hat mit PayPal bezahlt, aber die Bestellung ist nicht als bezahlt markiert. Warum?**
Meist erreicht die Benachrichtigung von PayPal die Website nicht, oder der „bezahlt“-Status zeigt auf einen Status,
den Sie nicht verwenden. Siehe [PayPal](paypal.md).

**Laufen Kartennummern über unsere Website?**
Nicht bei einem Gateway mit Weiterleitung wie PayPal; bei einem Kartenformular auf der eigenen Website schon. Siehe [Kartenzahlungen und PCI](kartenzahlungen-und-pci.md).

**Können wir einen weiteren Zahlungsanbieter hinzufügen?**
Ja, mit einer Gateway-Erweiterung, die ein Entwickler schreibt. Siehe [Zahlungsanbieter](zahlungsanbieter.md).

## Preise, Mehrwertsteuer und Rabatte

**Wie ändere ich Preise um Mitternacht?**
Bereiten Sie die Änderungen als Entwürfe vor und veröffentlichen Sie sie zum gewünschten Zeitpunkt, oder verwenden Sie
die zeitgesteuerte Veröffentlichung, wenn Ihre Website sie hat. Siehe [Preise und Währungen](preise-und-waehrungen.md).

**Ein Mehrwertsteuersatz ändert sich nächsten Monat. Was tue ich?**
Ändern Sie am Stichtag den Prozentsatz des USt-Typs und geben Sie eine Testbestellung auf. Siehe [USt-Typen und USt-Regeln](ust-typen-und-ust-regeln.md).

**Ändern sich alte Bestellungen, wenn ich einen Preis oder einen Mehrwertsteuersatz ändere?**
Nein. Jede Bestellposition behält ihren eigenen Preis, ihren Mehrwertsteuerwert und ihren Rabatt. Siehe [USt-Typen und USt-Regeln](ust-typen-und-ust-regeln.md).

**Können Kunden einen Gutscheincode eingeben?**
Nein, nicht ohne Erweiterung. Rabatte folgen den Kundengruppen. Siehe [Rabatte](rabatte.md).

## Versand und Belege

**Wie ändere ich die Versandkosten?**
Bitten Sie Ihren Partner, die Pauschale in einem Override zu ändern, und geben Sie eine Testbestellung auf. Siehe [Versand und Abwicklung](versand-und-abwicklung.md).

**Wo trage ich Sendungsnummern ein?**
Der Shop hat standardmäßig kein Feld dafür; einigen Sie sich auf einen Ort. Siehe [Versand und Abwicklung](versand-und-abwicklung.md).

**Ein Kunde hat seine Bestätigung verloren. Was schicke ich ihm?**
Seinen Beleg-Link, wenn dauerhafte Belege eingeschaltet sind, oder eine Weiterleitung der Kopie des Shops. Siehe [Rechnungen und Belege](rechnungen-und-belege.md).

**Erstellt der Shop Rechnungen?**
Nein. Er erstellt Belege; Rechnungen kommen aus Ihrer Buchhaltungssoftware oder einer Integration. Siehe [Rechnungen und Belege](rechnungen-und-belege.md).

## Kunden und Daten

**Müssen Kunden ein Konto haben?**
Nicht mit der ausgelieferten Einstellung; Gäste können kaufen. Siehe [Kundenkonten](kundenkonten.md).

**Ein Kunde bittet uns, seine Daten zu löschen. Wo fange ich an?**
Protokollieren Sie die Anfrage und folgen Sie dann der Routine. Bestellungen müssen möglicherweise für die Buchhaltung
aufbewahrt werden. Siehe [DSGVO-Anfragen](dsgvo-anfragen.md).

**Können Gäste den Wunschzettel nutzen?**
Nein, der Wunschzettel erfordert ein Konto. Siehe [Wunschzettel und Warenkörbe](wunschzettel-und-warenkoerbe.md).

## Berichte

**Kann ich Bestellungen in eine Tabellenkalkulation exportieren?**
Nicht aus der ausgelieferten Bestellliste. Übertragen Sie die Zahlen von Hand, oder bitten Sie Ihren Partner um einen
Export. Siehe [Shop-Berichte](shop-berichte.md).

**Ist der Umsatz im Dashboard das, was auf unserem Bankkonto angekommen ist?**
Nein. Es ist der Bestellwert; Ihre Buchhaltung bleibt der Maßstab. Siehe [Shop-Berichte](shop-berichte.md).

## Hilfe bekommen

**Wen frage ich, wenn etwas im Shop nicht zu funktionieren scheint?**
Zuerst Ihren Partner oder Hoster. Einen Fehler in Exponential selbst können Sie unter
<https://github.com/se7enxweb/exponential/issues> melden; ein Sicherheitsproblem vertraulich an security@se7enx.com
(`SECURITY.md`). Die Meldeseite der Hilfe in der Administration finden Sie unter `/exphelp/report`.

## Verwandte Seiten

- [Ihren Shop betreiben](README.md)
- [Shop-FAQ für Unternehmen](../../../../en/us/help/businesses/stores/store-faq.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
