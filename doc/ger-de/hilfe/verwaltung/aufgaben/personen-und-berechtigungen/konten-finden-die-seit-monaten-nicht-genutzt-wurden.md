---
title: Konten finden, die seit Monaten nicht genutzt wurden
audience: government, admin
category: public-sector
order: 200
summary: Schritt für Schritt - Mitarbeiterkonten auflisten, die seit einer gewählten Zahl von Tagen niemand genutzt hat oder die nie genutzt wurden, mit Benutzer > Benutzerkonten prüfen, mit den Abteilungen bestätigen und sie deaktivieren.
keywords: ruhende Konten, inaktive Konten, ungenutzte Konten, nicht gesehen, Nie angemeldet, Letzter Besuch, Benutzerkonten prüfen, Kontenpflege, deaktivieren, Überprüfung des Zugangs, dormant accounts, inactive accounts, unused accounts, not seen, never signed in, last visit, Check user accounts, user hygiene, disable, access review
type: help
---
# Konten finden, die seit Monaten nicht genutzt wurden

Diese Aufgabe ist für Administratoren, die Konten pflegen oder einen Befund eines Audits zu ruhenden Konten beantworten.
Am Ende sind ungenutzte Konten gelistet, geprüft und deaktiviert. Die Seite ist **Benutzer > Benutzerkonten prüfen**
(`/user/hygiene`), beschrieben unter [Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Konten auflisten](#die-konten-auflisten)
3. [Bestätigen und deaktivieren](#bestätigen-und-deaktivieren)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Funktion `user/hygiene` (und `content/edit` für die Konten, um sie zu ändern). E-Mail-Adressen auf der Seite
  brauchen `role/assign`.
- Der Zeitraum Ihrer Richtlinie, zum Beispiel 90 oder 180 Tage.
- Die Seite braucht eine SQL-Datenbank; auf MongoDB listet sie nichts.

## Die Konten auflisten

1. Öffnen Sie **Benutzer > Benutzerkonten prüfen**.
2. Wählen Sie den Filter **Seit N Tagen nicht gesehen** und setzen Sie N (1 bis 36500; standardmäßig 180). Jeder Filter
   zeigt, wie viele Konten er enthält.
3. Optional: Wählen Sie **Benutzergruppe** = Staff (oder eine Abteilung), um Konten von Besuchern auszulassen.
4. Sortieren Sie nach **Letzter Besuch**, um die am längsten ungenutzten zuerst zu sehen.
5. Wiederholen Sie es mit **Nie angemeldet**: Konten, die angelegt, aber nie genutzt wurden. Der anonyme Benutzer wird
   nicht gezählt.
6. **Liste als CSV exportieren**, um sie an die Abteilungsleitungen zu senden.

Der letzte Besuch ist die Zeit der letzten Anmeldung oder der ersten Anfrage nach einer längeren Pause
(`site.ini [Session] ActivityTimeout`).

## Bestätigen und deaktivieren

1. Schicken Sie jeder Abteilung ihre Liste und ein Datum, bis zu dem sie antworten soll.
2. Markieren Sie zurück auf der Seite die Konten, die niemand beansprucht hat, und klicken Sie auf **Deaktivieren**.
3. Die Bestätigung sagt, wie viele sich ändern werden und welche ausgelassen werden und warum (der anonyme Benutzer, der
   Benutzer für das Anlegen, das Administratorkonto, `admin` und Sie selbst werden nie geändert). Klicken Sie auf
   **Bestätigen**.
4. Jedes Konto wird als `access.user.disable` aufgezeichnet.

## Prüfen, ob es funktioniert hat

- Filter **Deaktiviert**: Die Konten sind gelistet.
- Bewahren Sie die CSV-Datei und die Antworten der Abteilungen mit Ihrem Nachweis der Überprüfung auf.

## Verwandte Seiten
- [Pflege der Benutzerkonten](../../betrieb/personen-und-berechtigungen/pflege-von-benutzerkonten.md)
- [Die Kontenliste für eine Überprüfung exportieren](die-kontenliste-fuer-eine-ueberpruefung-exportieren.md)
- [Einen ausscheidenden Mitarbeiter deaktivieren und verschieben](einen-ausscheidenden-mitarbeiter-deaktivieren-und-verschieben.md)
- [Überprüfungen des Administratorzugangs](../../betrieb/personen-und-berechtigungen/ueberpruefung-administrativer-zugaenge.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
