---
title: Vorbereitung auf die Hochsaison
audience: customer, businesses, hosting
category: customer-success
order: 230
summary: Der Plan eines Shop-Betreibers für eine Aktion, die Weihnachtszeit oder einen großen Newsletter-Versand mit Exponential - sechs Wochen vorher, zwei Wochen vorher, die Woche davor, der Tag selbst und die Auswertung danach - mit wer was tut und was Sie Ihren Hoster fragen sollten.
keywords: Hochsaison, Weihnachtsgeschäft, Aktion, Kampagne, Black Friday, Lastspitzen, Änderungsstopp, Lasttest, Benchmark, Shop-Vorbereitung, Kapazität, Caching, peak season, holiday season, sale, campaign, peak traffic, change freeze, load test, capacity
type: help
---
# Vorbereitung auf die Hochsaison

Diese Seite ist für den Eigentümer und die Shop-Leitung, die mit geschäftigen Tagen rechnen. Sie ist ein Zeitplan, der
die Vorbereitung auf mehrere Wochen verteilt, sodass die letzten Tage ruhig bleiben. Warum sich Shop-Seiten unter Last
anders verhalten, und welche technischen Stellschrauben es gibt, steht unter
[Shop-Leistung zu Spitzenzeiten](../../unternehmen/shops/shop-leistung-zu-spitzenzeiten.md); diese Seite ist der Plan.

## Warum es einen Plan braucht

Produkt-, Kategorie- und Landingpages sind für alle gleich und können aus dem Cache ausgeliefert werden. Warenkorb,
Kasse, Kontoseiten und Zahlungsbenachrichtigungen sind persönlich und können das nicht
([Shop-Leistung zu Spitzenzeiten](../../unternehmen/shops/shop-leistung-zu-spitzenzeiten.md)). An einem Spitzentag
haben beide Arten viel zu tun, und auf den persönlichen Seiten wird das Geld verdient.

## Sechs Wochen vorher

| Aufgabe | Wer |
|---|---|
| Die Daten der Aktion oder Saison und den erwarteten Umfang festlegen (eine frühere Spitze, die Größe eines Newsletters) | Eigentümer |
| Dem Hoster die Daten und den erwarteten Umfang mitteilen | Eigentümer |
| Den Hoster bitten, die Antwortzeiten mit `exp:benchmark` zu messen und das Ergebnis als Ausgangswert aufzubewahren, einschließlich einer Produktseite, einer Kategorieseite und des Warenkorbs ([Benchmark](../../../../eng-us/features/6.0/benchmark.md)) | Hoster |
| Preise, Rabatte und Versandbedingungen für den Zeitraum festlegen | Eigentümer |
| Die Landingpages und Newsletter planen | Marketing |

## Zwei Wochen vorher

- [ ] Landingpages als Entwürfe erstellt und geprüft ([Landingpages mit Layouts](../../../../eng-us/help/creators/website-creators/landing-pages-with-layouts.md)).
- [ ] Ermäßigungsregeln vorbereitet, noch nicht aktiv ([Rabatte](rabatte.md)).
- [ ] Preisänderungen als Entwürfe vorbereitet ([Preise und Währungen](preise-und-waehrungen.md)).
- [ ] Geplante Updates von Exponential oder Erweiterungen jetzt erledigt, nicht später, und getestet.
- [ ] Beim Zahlungsanbieter nachgefragt, ob an Ihren Daten Wartungsarbeiten zu erwarten sind.
- [ ] Zusätzliche Hilfe für Verpackung und Kundenservice organisiert.

## Die Woche davor

Aus [Shop-Leistung zu Spitzenzeiten](../../unternehmen/shops/shop-leistung-zu-spitzenzeiten.md), mit ergänzten Namen:

| Aufgabe | Wer |
|---|---|
| Änderungsstopp: keine Änderungen an Code, Design oder Einstellungen bis nach der Spitze | Eigentümer informiert Partner und Hoster |
| Landingpages veröffentlicht; Caches vom Hoster vorgewärmt | Marketing, Hoster |
| Eine Testbestellung von Anfang bis Ende, mit Zahlung | Shop-Leitung |
| Cronjobs laufen, sodass alte Warenkörbe bereinigt werden ([Cronjobs](../../../anleitungen/cronjobs.md)) | Hoster |
| Backups überprüft ([Backups, die sich wiederherstellen lassen](../../unternehmen/zuverlaessigkeit/backups-die-sich-wiederherstellen-lassen.md)) | Hoster |
| Statusseite und Support-Kontakt des Zahlungsanbieters griffbereit | Shop-Leitung |
| Schriftlich festgehalten, wer über den Wartungsmodus entscheidet und wer den Hoster anruft | Eigentümer |
| Newsletter-Versand gestaffelt, wenn Ihr Newsletter-Werkzeug das erlaubt | Marketing |

## Am Tag selbst

| Zeit | Aufgabe | Wer |
|---|---|---|
| Start | Rabatte und Preise gehen live; ein Produkt im Warenkorb prüfen | Shop-Leitung |
| Jede Stunde | Dashboard: Bestellungen, Payment failed, offene Bestellungen | Shop-Leitung |
| Alle paar Stunden | Zahlungen im Konto des Anbieters stimmen mit den Bestellungen überein | Eigentümer oder Shop-Leitung |
| Wenn Seiten langsam sind | Den Hoster anrufen; er folgt seinem Runbook für eine langsame Website ([Langsame Website](../../hosting/runbooks/langsame-website.md)) | Shop-Leitung |
| Wenn die Website nicht erreichbar ist | [Website nicht erreichbar](../../hosting/runbooks/website-nicht-erreichbar.md) | Hoster |
| Ende | Rabatte und Aktionspreise zurückgesetzt | Shop-Leitung |

> **Warnung**
> Leeren Sie während der Spitze nicht alle Caches: Jede Seite muss dann im ungünstigsten Moment neu aufgebaut werden.
> Leeren Sie nur, was eine Änderung erfordert, und überlassen Sie das dem Hoster ([Shop-Leistung zu Spitzenzeiten](../../unternehmen/shops/shop-leistung-zu-spitzenzeiten.md)).

## Danach

- [ ] Umsatz pro Tag und abgebrochene Warenkörbe mit den Wochen davor verglichen ([Shop-Berichte](shop-berichte.md)).
- [ ] Alle Bestellungen der Spitze durch ihre Status geführt; nichts hängt in Pending fest.
- [ ] Die Notizen des Hosters zu den langsamsten Seiten und zu Fehlern.
- [ ] Festgehalten, was schiefging und was beim nächsten Mal früher vorzubereiten ist, solange es frisch ist
  ([Vorfallreaktion für kleine Teams](../../unternehmen/zuverlaessigkeit/stoerungsbehandlung-fuer-kleine-teams.md)).
- [ ] Der Änderungsstopp aufgehoben und verschobene Änderungen eingeplant.

## Was Sie Ihren Hoster fragen sollten

- Welche Kapazität haben wir für Warenkorb- und Kassenanfragen, nachdem gecachte Seiten den Server entlasten?
- Wird die Website mit Velocity und dessen Response-Cache ausgeliefert, und sind der View-Cache und der
  Template-Block-Cache eingeschaltet ([Velocity für Unternehmen](../../unternehmen/zuverlaessigkeit/velocity-fuer-unternehmen.md))?
- Können Sie einen Lasttest auf einer Kopie durchführen, mit einigen vollständigen Bestellvorgängen im Testmodus des Anbieters?
- Wer hat an unseren Daten Bereitschaft, und wie erreichen wir die Person?

## Verwandte Seiten

- [Shop-Leistung zu Spitzenzeiten](../../unternehmen/shops/shop-leistung-zu-spitzenzeiten.md)
- [Planung für Lastspitzen](../../unternehmen/zuverlaessigkeit/lastspitzen-planen.md)
- [Vor einer Kampagne](../nutzen/checklisten/vor-einer-kampagne.md)
- [Wöchentliche Shop-Checkliste](woechentliche-shop-checkliste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
