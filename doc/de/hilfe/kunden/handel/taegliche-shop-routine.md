---
title: Tägliche Shop-Routine
audience: customer, admin, businesses
category: customer-success
order: 10
summary: Eine fünfzehnminütige Morgenroutine für die Person, die einen Exponential-Shop betreibt - das Dashboard, wartende Bestellungen, fehlgeschlagene Zahlungen, das Bestellpostfach und die Übergabe am Ende des Tages.
keywords: tägliche Routine, Shop-Routine, Morgenkontrolle, Shop-Dashboard, offene Bestellungen, fehlgeschlagene Zahlung, Bestellpostfach, Shop-Leitung, daily routine, store routine, morning check, store dashboard, open orders, payment failed, order mailbox, store manager
type: help
---
# Tägliche Shop-Routine

Diese Seite ist für die Shop-Leitung, oder für den Eigentümer, der zugleich den Shop leitet. Sie gibt eine kurze
Routine für jeden Arbeitstag vor, in der Reihenfolge, die Probleme am frühesten erkennt, und sagt, wer im Team welchen
Schritt erledigt. Sie setzt voraus, dass der Shop live ist; das vollständige Bild der täglichen, wöchentlichen und
jährlichen Aufgaben für Unternehmen finden Sie unter
[Einen Shop im Alltag betreiben](../../../../en/us/help/businesses/stores/running-a-store-day-to-day.md), und diese Seite ist die Fassung,
die Sie an die Wand hängen können.

## Bevor Sie beginnen

Sie brauchen ein eigenes Konto mit der Richtlinie `shop/administrate`, die den Reiter Shop, das Dashboard, die
Bestellliste und die Kundenliste öffnet (`kernel/shop/module.php`). Melden Sie sich mit der Anmeldung in zwei
Schritten an, wenn Ihre Website sie anbietet ([Zwei-Faktor-Anmeldung](../../../../en/us/guides/two-factor-login.md)). Teilen
Sie nie ein Shop-Konto zwischen mehreren Personen: Die Statushistorie der Bestellungen und das Prüfprotokoll können
dann nicht mehr sagen, wer was getan hat.

## Die Routine

| Schritt | Wo | Worauf achten | Üblicherweise erledigt von |
|---|---|---|---|
| 1 | **Shop** (das Dashboard, `shop/dashboard`) | Offene Bestellungen, und wie viele zu lange warten | Shop-Leitung |
| 2 | Dashboard, **Work to do** | Bestellungen, die auf den Shop warten, die längsten zuerst | Shop-Leitung |
| 3 | Bestellliste, nach Status sortiert | Bestellungen im Status **Payment failed** oder **Awaiting payment** | Shop-Leitung |
| 4 | Dashboard, **Nächste Schritte** | Alles, was seit gestern neu ist | Shop-Leitung, informiert den Eigentümer |
| 5 | Das Bestellpostfach | Die Kopie des Shops von jeder Bestell-E-Mail, Antworten von Kunden | Shop-Leitung oder Kundenservice |
| 6 | Das Konto beim Zahlungsanbieter | Zahlungen ohne Bestellung, wartende Erstattungen | Eigentümer oder wer erstatten darf |
| 7 | Statusänderungen | Jede Bestellung in den Status gesetzt, der der tatsächlichen Arbeit entspricht | Wer die Arbeit erledigt hat |

Was das Dashboard zeigt, ist unter [Shop-Dashboard und Bestellstatus](../../../../en/us/features/6.0/store-dashboard.md)
beschrieben: Bestellungen heute sowie in 7 und 30 Tagen, offene Bestellungen, zu lange wartende Bestellungen,
abgebrochene Warenkörbe, Bestseller und die Liste „Nächste Schritte“, die aus Ihren Daten und Einstellungen ermittelt wird.

## Schritt für Schritt

### 1. Das Dashboard öffnen

**Shop** öffnet das Dashboard. Lesen Sie zuerst die Zeile „Auf einen Blick“. Ein plötzlicher Rückgang der Bestellungen
heute, verglichen mit einem gewöhnlichen Tag, ist oft das erste Anzeichen dafür, dass etwas auf der Website oder beim
Zahlungsanbieter nicht funktioniert.

### 2. Die wartenden Bestellungen abarbeiten

Die Liste „Work to do“ stellt die Bestellungen, die am längsten warten, an den Anfang. Öffnen Sie jede, prüfen Sie
Artikel, Optionen, Adresse und einen eventuellen Kommentar des Kunden, und setzen Sie sie weiter. Bestellungen, die auf
den Kunden warten (Pending, Awaiting payment, Payment failed, Ready for pickup), werden nach einer kürzeren Frist
hervorgehoben als die anderen ([Shop-Dashboard und Bestellstatus](../../../../en/us/features/6.0/store-dashboard.md)).

### 3. Fehlgeschlagene Zahlungen ansehen

Sortieren Sie die Bestellliste nach Status ([Sortierung der Bestellliste](../../../../en/us/features/6.0/order-list-sorting.md)). Eine
fehlgeschlagene Zahlung liegt meist an der Karte eines Kunden. Mehrere an einem Vormittag deuten auf den
Zahlungsschritt oder den Anbieter hin; siehe [Zahlungsanbieter](zahlungsanbieter.md).

### 4. Die „Nächsten Schritte“ lesen

Die Liste ist die eigene Checkliste des Shops: offene Bestellungen, die älter als zwei Tage sind, kein Zahlungsschritt
an der Kasse, ein USt-Typ mit 0 Prozent in Gebrauch, Einstellungen zur Warenkorb-Bereinigung, die in Sitzungen
gespeicherte Warenkörbe löschen würden. Eine leere Liste ist ein gutes Zeichen. Alles, wofür eine Einstellung geändert
werden muss, geht an Ihren Hoster oder Partner.

### 5. Das Bestellpostfach prüfen

Der Shop sendet eine Kopie jeder Bestell-E-Mail an die Adresse in `site.ini [MailSettings] AdminEmail`
(`kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php`). Wenn die Kopien nicht mehr ankommen, sind
wahrscheinlich auch die E-Mails an die Kunden ausgeblieben; siehe [Bestellbestätigung per E-Mail](bestellbestaetigung-per-e-mail.md).

### 6. Mit dem Zahlungsanbieter abgleichen

Vergleichen Sie einmal am Tag die Zahlungen im Konto bei Ihrem Anbieter mit den bezahlten Bestellungen. Eine Zahlung
ohne Bestellung oder eine als bezahlt markierte Bestellung ohne Zahlung muss noch am selben Tag geprüft werden.

### 7. Status setzen, sobald die Arbeit erledigt ist

Setzen Sie jede Bestellung in dem Moment weiter, in dem die Arbeit geschieht: Packed, wenn das Paket fertig ist,
Shipped, wenn es das Haus verlässt. Die Wartezeiten im Dashboard sind nur so zutreffend wie die Status. Die Bedeutung
jedes Status steht unter [Bestellstatus](bestellstatus.md).

## Am Ende des Tages

- [ ] Keine Bestellung, die älter ist als Ihre zugesagte Bearbeitungszeit, steht noch in einem „neuen“ Status.
- [ ] Zu jeder Bestellung im Status Shipped ist die Sendungsnummer dort notiert, wo Ihr Team es vereinbart hat (der Shop hat standardmäßig kein Feld dafür).
- [ ] Kundenfragen sind beantwortet oder übergeben.
- [ ] Alles Ungewöhnliche ist für morgen notiert, oder für die Vertretung.

## Wenn die zuständige Person abwesend ist

Die Vertretung braucht ein eigenes Konto, den Statusablauf Ihres Shops auf einer Seite und die Kontakte von Hoster
und Zahlungsanbieter. Eine Übergabeliste finden Sie unter
[Einen Shop im Alltag betreiben](../../../../en/us/help/businesses/stores/running-a-store-day-to-day.md).

> **Tipp**
> Drucken Sie den Abschnitt „So funktioniert Ihr Shop“ des Dashboards einmal aus und legen Sie ihn zu dieser Routine. Er
> beschreibt die Kasse genau so, wie Ihre Installation konfiguriert ist, und genau das braucht eine Vertretung.

## Verwandte Seiten

- [Bestellungen im Alltag](bestellungen-im-alltag.md)
- [Wöchentliche Shop-Checkliste](woechentliche-shop-checkliste.md)
- [Shop-Berichte](shop-berichte.md)
- [Einen Shop im Alltag betreiben (für Unternehmen)](../../../../en/us/help/businesses/stores/running-a-store-day-to-day.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
