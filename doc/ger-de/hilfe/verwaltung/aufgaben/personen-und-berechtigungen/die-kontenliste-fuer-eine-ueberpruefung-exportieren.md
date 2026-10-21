---
title: Die Kontenliste für eine Überprüfung exportieren
audience: government, admin, project-manager
category: public-sector
order: 290
summary: Schritt für Schritt - jedes Konto oder eine gefilterte Auswahl als CSV aus Benutzer > Benutzerkonten prüfen für eine Überprüfung des Zugangs exportieren, mit dem Inhalt der Datei, wer E-Mail-Adressen einschließen darf und wie die Datei als personenbezogene Daten zu behandeln ist.
keywords: Konten exportieren, CSV, Kontenliste, Überprüfung des Zugangs, Benutzerkonten prüfen, Kontenpflege, Rezertifizierung, Nachweis für das Audit, personenbezogene Daten, export accounts, account list, access review, Check user accounts, user hygiene, recertification, audit evidence, personal data
type: help
---
# Die Kontenliste für eine Überprüfung exportieren

Diese Aufgabe ist für Administratoren, die eine Überprüfung des Zugangs oder ein Audit vorbereiten. Am Ende haben Sie
eine CSV-Datei der Konten mit ihrem Zustand, dem letzten Besuch, den Gruppen und Rollen. Der Export ist Teil von
**Benutzer > Benutzerkonten prüfen** ([Benutzerkonten prüfen, „Die CSV“](../../../../funktionen/6.0/benutzerpflege.md#die-csv)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Was die Datei enthält](#was-die-datei-enthält)
4. [Mit der Datei umgehen](#mit-der-datei-umgehen)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- `user/hygiene`. E-Mail-Adressen werden nur für Benutzer eingeschlossen, die auch `role/assign` haben.
- Entscheiden Sie den Umfang: alle Konten, nur Mitarbeiter (eine Benutzergruppe) oder ein Filter (seit N Tagen nicht
  gesehen, gesperrt ...).

## Schritte

1. Öffnen Sie **Benutzer > Benutzerkonten prüfen** (`/user/hygiene`).
2. Wählen Sie den Filter und bei Bedarf **Benutzergruppe** und eine Suche.
3. Klicken Sie auf **Liste als CSV exportieren**. Die ganze gefilterte Liste wird exportiert, jede Seite, bis zu
   100.000 Konten.
4. Speichern Sie die Datei in Ihrem Ordner für die Überprüfung.

Der Export wird im Audit-Protokoll als `data.export.csv` mit der Zahl der Zeilen, dem Filter und der Angabe, ob
E-Mail-Adressen eingeschlossen waren, aufgezeichnet, niemals mit den Werten.

## Was die Datei enthält

| Spalte | Inhalt |
|---|---|
| User ID | die ID des Kontos |
| Name, Login | wie auf der Seite |
| E-mail | nur mit `role/assign` |
| Status | die Zustände, durch Komma getrennt (Deaktiviert, Gesperrt, Nie angemeldet ...) |
| Last visit | `YYYY-MM-DD HH:MM UTC`, leer, wenn nie |
| Sign-ins | Anzahl |
| Groups, Roles | die Gruppen und die Rollen (eigene und geerbte) |

UTF-8 mit Byte Order Mark, durch Komma getrennt; Zellen, die wie Formeln aussehen, erhalten einen führenden Apostroph,
damit eine Tabellenkalkulation sie als Text zeigt.

## Mit der Datei umgehen

Die Datei enthält personenbezogene Daten (Namen, Logins, eventuell E-Mail-Adressen). Bewahren Sie sie nur so lange auf,
wie die Überprüfung es braucht, an einem Ort mit eingeschränktem Zugang, und mailen Sie sie nicht herum; lassen Sie die
Spalte E-Mail weg (exportieren Sie als Benutzer ohne `role/assign`), wenn die Überprüfung sie nicht braucht. Die
Datenschutzmaßnahmen der Plattform sind im [Datenschutzstandard](../../../../standards/datenschutz.md) beschrieben.

## Verwandte Seiten
- [Prüfen, wer Administratorrechte hat](pruefen-wer-administratorrechte-hat.md)
- [Konten finden, die seit Monaten nicht genutzt wurden](konten-finden-die-seit-monaten-nicht-genutzt-wurden.md)
- [Überprüfungen des Administratorzugangs](../../betrieb/personen-und-berechtigungen/ueberpruefung-administrativer-zugaenge.md)
- [Pflege der Benutzerkonten](../../betrieb/personen-und-berechtigungen/pflege-von-benutzerkonten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
