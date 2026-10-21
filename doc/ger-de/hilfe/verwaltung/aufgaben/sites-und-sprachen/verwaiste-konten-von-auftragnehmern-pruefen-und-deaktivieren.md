---
title: Verwaiste Konten von Auftragnehmern prüfen und deaktivieren
audience: government, admin
category: public-sector
order: 200
summary: Aufgabe für Administratoren - einmal im Monat externe Konten, die nie oder lange nicht genutzt wurden, unter Benutzer > Benutzerkonten prüfen finden, die Liste für die Projektleiter exportieren und deaktivieren, was nicht mehr gebraucht wird.
keywords: verwaiste Konten, Benutzerkonten prüfen, Nie angemeldet, deaktivieren, CSV exportieren, monatliche Überprüfung, stale accounts, Check user accounts, user/hygiene, Not seen for N days, Never signed in, disable, export CSV, monthly review
type: help
---
# Verwaiste Konten von Auftragnehmern prüfen und deaktivieren

Diese Aufgabe richtet sich an Administratoren. Hintergrund:
[Verwaiste Konten überprüfen](../../aufbauen/extranets/verwaiste-konten-ueberpruefen.md).

## Schritte

1. **Benutzer > Benutzerkonten prüfen** (`/user/hygiene`); beschränken Sie die Liste auf die Benutzergruppe „Extern“.
2. Wählen Sie **Seit N Tagen nicht gesehen** mit zum Beispiel 60 Tagen.
3. **Liste als CSV exportieren** und senden Sie jedem Projektleiter die Konten seiner Auftragnehmer; fragen Sie, welche
   bleiben müssen.
4. Markieren Sie zurück auf der Seite die zu stoppenden Konten und drücken Sie **Deaktivieren**; lesen Sie die
   Bestätigung (wie viele sich ändern, wie viele ausgelassen werden und warum), dann **Bestätigen**.
5. Wiederholen Sie das mit **Nie angemeldet** für Konten, die älter als zwei Wochen sind.
6. Vergleichen Sie mit den Enddaten in Ihrem Zugangsregister und deaktivieren Sie Konten, deren Vertrag beendet ist.
7. Löschen Sie die exportierte CSV-Datei nach der Überprüfung.

## Prüfen

Der Filter **Deaktiviert** zeigt nun die Konten; sie können sich nicht anmelden, und ihre Sitzungen sind beendet.

## Probleme

| Problem | Lösung |
|---|---|
| Ein Konto wird ausgelassen | die Bestätigung sagt warum (zum Beispiel ist es bereits deaktiviert) |
| Das Konto muss entfernt werden | das Entfernen eines Benutzers ist endgültig; tun Sie es Konto für Konto am Knoten des Benutzers, nach der Aufbewahrungsfrist |

## Verwandte Seiten

- [Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md)
- [Den Zugang beenden](../../aufbauen/extranets/den-zugang-beenden.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
