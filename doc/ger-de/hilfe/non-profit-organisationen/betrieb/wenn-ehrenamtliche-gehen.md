---
title: Wenn Ehrenamtliche gehen
audience: non-profit, admin, businesses, editor
category: non-profits
order: 60
summary: Was eine Non-Profit-Organisation auf ihrer Exponential-Site tut, wenn eine ehrenamtliche Person, ein Mitarbeiter oder ein Vorstandsmitglied geht - das Konto deaktivieren (Benutzerkonten prüfen und das optionale Deaktivieren und verschieben), Gruppenmitgliedschaften entfernen, den Besitz von Inhalten übertragen, Passwörter und Wissen übergeben, das Audit-Protokoll lesen, und eine Checkliste - damit die Site nie von einer einzigen Person abhängt.
keywords: Offboarding, Ehrenamtliche gehen, Kontodeaktivierung, Benutzerkonten prüfen, Deaktivieren und verschieben, Kontenpflege, Gruppenmitgliedschaft, Besitzer von Inhalten, Übergabe, Administratorpasswort, Audit-Protokoll, Wissenstransfer, offboarding, volunteer leaves, account deactivation, Check user accounts, Deactivate and move, user hygiene, audit trail, handover
type: help
---
# Wenn Ehrenamtliche gehen

Diese Seite richtet sich an die Koordination und an die Administration. Ehrenamtliche kommen und gehen, ebenso
Vorstandsmitglieder und Mitarbeiter. Eine Site, die vom Konto, vom Passwort oder vom Gedächtnis einer einzigen Person
abhängt, ist jedes Mal in Gefahr, wenn jemand geht. Die ausführlichere Darstellung für kleine Gruppen finden Sie unter
[Übergabe beim Wechsel von Ehrenamtlichen](../../unternehmen/vereine/uebergabe-bei-wechsel-der-ehrenamtlichen.md).

## Inhalt

1. [In der Woche, in der jemand geht](#in-der-woche-in-der-jemand-geht)
2. [Konten deaktivieren](#konten-deaktivieren)
3. [Inhalte, für die die Person zuständig war](#inhalte-für-die-die-person-zuständig-war)
4. [Wenn die Person die Schlüssel hatte](#wenn-die-person-die-schlüssel-hatte)
5. [Nachlesen, was geschehen ist](#nachlesen-was-geschehen-ist)
6. [Vorbeugung](#vorbeugung)
7. [Checkliste für ausscheidende Personen](#checkliste-für-ausscheidende-personen)

## In der Woche, in der jemand geht

| Aufgabe | Warum |
|---|---|
| Das Konto deaktivieren | Ein ruhendes Konto ist eine offene Tür |
| Die Person aus Gruppen entfernen (Redakteure, Ehrenamtliche, Vorstand) | Rollen ergeben sich aus Gruppen |
| Die Person als Freigebende in Workflows entfernen | Sonst warten Freigaben auf niemanden |
| Den Empfänger von Formularen ändern, die an sie gemailt wurden | Bewerbungen gingen sonst an jemanden, der nicht mehr da ist |
| Gemeinsame Postfächer und Konten bei externen Diensten übernehmen | Die Spendenplattform, der Mailversand, die Domain |

## Konten deaktivieren

- **Benutzerkonten prüfen** listet Konten nach Filtern auf (zum Beispiel seit langer Zeit nicht mehr angemeldet) und
  wendet Aktionen auf sie an ([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)).
- **Deaktivieren und verschieben** (optional, `[UserSettings] DeactivateAndMove=enabled`) deaktiviert Konten und
  verschiebt alle ihre Platzierungen in eine einzige versteckte Gruppe; **Reaktivieren und zurückverschieben** stellt
  sie wieder her ([Deaktivieren und verschieben](../../../funktionen/6.0/benutzerpflege.md#deaktivieren-und-verschieben)). Das
  eignet sich für Ehrenamtliche, die vielleicht zurückkommen.
- „Ein Benutzerobjekt kommt nie in den Papierkorb“, daher ist das Entfernen eines Kontos endgültig (dieselbe Seite).
  Deaktivieren Sie lieber.

Aus Sicht der Eigentümer: [Ausscheidende Mitarbeiter](../../kunden/alltag/ausscheiden-von-mitarbeitenden.md).

## Inhalte, für die die Person zuständig war

Inhalte bleiben erhalten, wenn ein Konto deaktiviert wird. Sorgen Sie dafür, dass für jede Seite, um die sich die
Person gekümmert hat, jetzt jemand anderes verantwortlich ist:

- Aktualisieren Sie den „Besitzer“ in Ihrer Inhaltsübersicht.
- Ändern Sie den Besitzer des Objekts, wo es darauf ankommt ([Besitzer ändern](../../../anleitungen/eigentuemer-aendern.md)).
- Übertragen Sie Benachrichtigungen auf den neuen Besitzer ([Benachrichtigungen](../../../funktionen/6.0/benachrichtigungen.md)).

## Wenn die Person die Schlüssel hatte

Wenn ein Administrator geht:

- Ändern Sie die Passwörter aller gemeinsam genutzten Konten, die er kannte (Hosting-Panel, Domain-Registrar,
  Spendenplattform).
- Stellen Sie sicher, dass mindestens zwei Administratoren bleiben.
- Wenn niemand das Administratorpasswort kennt, gibt es einen Weg, es zurückzusetzen
  ([Benutzerpasswort zurücksetzen](../../../funktionen/6.0/benutzerpasswort-zuruecksetzen.md),
  [Das Administratorpasswort ändern](../../administratorpasswort-aendern.md)).
- Übernehmen Sie die Geräte für die Zwei-Faktor-Anmeldung: Die Codes gehen sonst mit der Person.

## Nachlesen, was geschehen ist

Das Audit-Protokoll zeigt, was ein Konto vor dem Ausscheiden getan hat, ohne Schuldzuweisung
([Das Audit-Protokoll](../../../funktionen/6.0/pruefprotokoll.md)). Das hilft, wenn sich eine Seite geändert hat und sich
niemand an den Grund erinnert ([Versionen und das Audit-Protokoll](../transparenz/versionen-und-das-audit-protokoll.md)).

## Vorbeugung

- Ein Konto pro Person, nie gemeinsam genutzt ([Ehrenamtliche als Redakteure](../ehrenamtliche/ehrenamtliche-als-redakteure.md)).
- Zwei Administratoren, mindestens einer davon Mitarbeiter oder Vorstandsmitglied.
- Domain-, Hosting- und Dienstkonten auf den Namen der Organisation.
- Einstellungen, Abläufe und Passwörter an einem Ort dokumentiert, den die Organisation kontrolliert.
- Einmal pro Quartal ein Durchlauf von **Benutzerkonten prüfen**.

## Checkliste für ausscheidende Personen

- [ ] Konto deaktiviert (oder deaktiviert und verschoben).
- [ ] Aus Gruppen und Freigabelisten entfernt.
- [ ] Empfänger von Formularen und Benachrichtigungen geändert.
- [ ] Gemeinsame Passwörter geändert, falls die Person welche kannte.
- [ ] Seiten, für die die Person zuständig war, haben einen neuen Besitzer.
- [ ] Ihre Exporte oder Dateien mit personenbezogenen Daten zurückgegeben oder gelöscht.
- [ ] Im [Arbeitsblatt Rollen und Berechtigungen](../vorlagen/arbeitsblatt-rollen-und-berechtigungen.md) vermerkt.

## Verwandte Seiten

- [Betrieb für Non-Profit-Organisationen](README.md)
- [Übergabe beim Wechsel von Ehrenamtlichen](../../unternehmen/vereine/uebergabe-bei-wechsel-der-ehrenamtlichen.md)
- [Eine Site übernehmen](../../kunden/partner-und-hoster/eine-website-uebernehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
