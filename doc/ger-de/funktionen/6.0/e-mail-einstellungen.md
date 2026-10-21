# E-Mail-Einstellungen: die Anleitung für Benutzer

Exponential lässt jeden Menschen auf einer Seite wählen, welche E-Mails er von einer Website erhält, und sie jederzeit mit
einem Klick abbestellen. Diese Seite ist für Besucher, Mitglieder und Redakteure: wo die Seite ist, was jede Wahl
bedeutet, wie Sie eine E-Mail abbestellen, was bei der Registrierung geschieht und wie Sie Ihre Daten herunterladen.
Administratoren finden die Einrichtung, die Statusseite und die Sperrliste in
[der Anleitung für Administratoren](../../anleitungen/mail-einstellungen-administrator.md); Entwickler finden Kategorien und die
Schranke für E-Mails in [der Anleitung für Entwickler](../../anleitungen/mail-einstellungen-entwickler.md).

## Kurz gesagt

- Optionale E-Mails (Benachrichtigungen, Newsletter, Angebote, Hinweise) sind **aus, bis Sie sie einschalten**.
- Notwendige E-Mails zu Ihrem Konto (Zurücksetzen des Passworts, Aktivierung, Bestellungen, rechtliche Hinweise) werden
  immer gesendet und enthalten nie Werbung.
- Jede optionale E-Mail endet damit, warum Sie sie erhalten haben, mit einem Link **Abmelden**, der mit einem Klick
  funktioniert, und einem Link zu Ihrer Einstellungsseite. Für beides ist keine Anmeldung nötig.
- Ihre Wahl und jede Änderung daran lassen sich als JSON oder CSV herunterladen.

## Wo die Seite ist

| Sie sind | Öffnen Sie |
|---|---|
| Auf der öffentlichen Website angemeldet | Ihr Profil (**Profil bearbeiten**), den Kasten **E-Mail-Einstellungen** > **Meine E-Mail-Einstellungen öffnen** oder `/mailpreferences/settings` |
| In der Administrationsoberfläche angemeldet (Admin, Editor) | **Mein Konto** > **Meine E-Mail-Einstellungen**, den Link im Kasten des aktuellen Benutzers oder den Kasten oben in **Meine Benachrichtigungseinstellungen** |
| Nicht angemeldet oder ohne Konto | den Link **Verwalten** am Ende jeder optionalen E-Mail oder `/mailpreferences/request` („Send me a link“) |

Die Seite sieht überall gleich aus. Sie hat vier Teile, von oben nach unten.

### 1. Optionale E-Mails senden (der Hauptschalter)

Eine Zeile sagt, ob optionale E-Mails **An** oder **Aus** sind, mit einer Schaltfläche:

- **Alle optionalen E-Mails ausschalten** hält jede optionale Art auf einmal an. Ihre Auswahl darunter bleibt erhalten,
  ruhend.
- **Optionale E-Mails einschalten** bringt sie genau so zurück, wie sie waren.

Notwendige E-Mails sind von diesem Schalter nicht betroffen.

### 2. Was Sie erhalten möchten

Ein Schalter pro Art von E-Mail, mit Namen und Inhalt. Nichts ist an, bis Sie es einschalten. Haken Sie an, was Sie
möchten, und drücken Sie **Meine Auswahl speichern**; entfernen Sie den Haken und speichern Sie, um es anzuhalten. Der
Hinweis oben sagt, was geschehen ist („Your choices were saved.“, „Nothing was changed.“).

| Art (Standardinstallation) | Was sie ist | Wie oft |
|---|---|---|
| Inhaltsbenachrichtigungen | Neue und geänderte Inhalte in den Teilen der Website, denen Sie folgen | Sofort, tägliche Zusammenfassung oder wöchentliche Zusammenfassung |
| Zusammenarbeit und Freigaben | Inhalte, die auf Ihre Freigabe warten, Nachrichten Ihrer Elemente der Zusammenarbeit | Sofort, tägliche Zusammenfassung oder wöchentliche Zusammenfassung |
| Newsletter | Die Newsletter, die Sie abonnieren | der eigene Zeitplan des Newsletters |
| Angebote und Neuigkeiten | Angebote, Veranstaltungen und Neuigkeiten zu Produkten und Diensten | der Zeitplan des Absenders |
| Anwendungs- und Systemhinweise | Hinweise zur Website und ihren Funktionen, die nicht Ihr Konto betreffen | wenn es geschieht |

Eine Website kann eigene Arten hinzufügen; sie erscheinen in derselben Liste.

**Newsletter** und **Angebote und Neuigkeiten** brauchen eine Bestätigung: Wenn Sie eine davon einschalten, sagt die Seite
„We sent a confirmation link to ... This starts once you open it.“, und der Schalter zeigt **Wartet auf Ihre
Bestätigung**. Öffnen Sie den Link in dieser E-Mail und drücken Sie **Ja, bestätigen**. Bis Sie das tun, wird nichts
dieser Art gesendet; nach sieben Tagen verfällt die Anfrage, und der Schalter ist wieder aus. Inhaltsbenachrichtigungen
und die übrigen beginnen sofort.

Wenn Sie schon Elementen folgten, bevor es diese Seite gab (**Meine Benachrichtigungseinstellungen**), werden
Inhaltsbenachrichtigungen bereits als an gezeigt, mit der Häufigkeit Ihrer Zusammenfassung. Was Sie hier wählen, gewinnt.

### 3. Immer gesendet

Die notwendigen Arten, aufgeführt mit ihrer Beschreibung: Kontosicherheit, Bestellungen und Belege, Rechtliche Hinweise
und Servicehinweise, Verwaltungswarnungen. „These messages are needed to run your account or are required by law. They
cannot be turned off, but they never contain advertising.“

### 4. Ihre E-Mail-Daten

Die letzten 20 Änderungen an Ihren Einstellungen: wann, was, die Änderung, wo sie gemacht wurde (Einstellungsseite, Link
in einer E-Mail, Registrierungsformular, Bestätigungslink, von einem Administrator geändert, ...) und der genaue Text, den
Sie gesehen haben, als Sie sie vornahmen. **Download my e-mail data (JSON)** und **(CSV)** geben Ihnen alles: jede Art mit
ihrem Zustand, offene Bestätigungen und die ganze Historie.

## Eine E-Mail abbestellen

Jede optionale E-Mail endet so (Textversion; die HTML-Version hat Links):

```
--
You get this e-mail because you turned on "Content notifications" on Example Site.
Unsubscribe with one click: https://www.example.com/mailpreferences/unsubscribe/m1...
Choose which e-mail you get: https://www.example.com/mailpreferences/manage/m1...

Example Organisation
Example Street 1, 12345 Town
```

- **Abmelden** öffnet eine Seite, die „Stop e-mail of the kind "Content notifications"?“ fragt, mit einer Schaltfläche,
  **Abmelden**. Ein Druck darauf hält diese Art sofort an: „... gets no more e-mail of the kind ...“. Das Öffnen des Links
  allein ändert nichts, sodass ein Virenscanner, der Links öffnet, Sie nicht abmelden kann.
- Viele E-Mail-Programme (Gmail, Apple Mail, Outlook, Yahoo) zeigen neben dem Absender eine eigene Schaltfläche zum
  **Abmelden**. Sie verwendet denselben Link und meldet Sie ab, ohne eine Seite zu öffnen.
- **Choose which e-mail you get** öffnet Ihre vollständige Einstellungsseite, ohne Anmeldung.

Die Links funktionieren, solange die Website existiert; sie sind persönlich, leiten Sie also keine E-Mail mit ihnen
weiter.

Ein Link, der nicht mehr funktioniert (zum Beispiel vom E-Mail-Programm abgeschnitten), zeigt das Formular „Send me a
link“ mit dem Hinweis „This link does not work any more“.

## Einen Link zusenden lassen (ohne Konto)

`/mailpreferences/request`: Geben Sie Ihre Adresse ein und drücken Sie **Link zusenden**. Die Seite antwortet „Please check
your inbox. If we send e-mail to ..., a message with your personal link is on its way. The link works for 24 hours.“ Sie
sagt dasselbe für jede Adresse, sodass niemand daraus herausfinden kann, wer auf einer Liste steht. Eine Adresse kann drei
Links pro Stunde anfordern.

## Bei der Registrierung

Das Registrierungsformular hat einen Kasten **E-Mail from us (optional)** mit einem nicht angehakten Kästchen pro
optionaler Art. Haken Sie nur an, was Sie möchten; nichts ist für Sie angehakt. Eine Art, die eine Bestätigung braucht,
sendet ihren Link nach der Registrierung. Was Sie angehakt haben, wird mit dem Text erfasst, den das Formular zeigte.

## Ihre E-Mail-Adresse ändern

Eine neue Adresse muss bestätigt werden: Ein Link geht an die neue Adresse („Use ... for this account?“), und das Konto
behält die alte Adresse, bis Sie **Ja, bestätigen** drücken.

## Ihre Rechte

- **Ihre Daten sehen**: die Historie auf der Seite und der Download.
- **Widerrufen**: jeder Schalter, der Hauptschalter oder der Abmeldelink, jederzeit.
- **Löschen**: Fragen Sie den Administrator der Website. Ihre Einstellungen werden entfernt und die Historie anonymisiert;
  ein Eintrag, dass Sie widerrufen haben, wird ohne Ihre Adresse aufbewahrt, sodass die Website nachweisen kann, dass sie
  aufgehört hat, Ihnen zu schreiben.
- **Alles endgültig anhalten**: Der Abmeldelink einer an alle gesendeten E-Mail („Stop all optional e-mail?“) setzt Ihre
  Adresse außerdem auf die Sperrliste der Website (nur eine verschlüsselte Form der Adresse wird aufbewahrt). Das erneute
  Einschalten optionaler E-Mails auf Ihrer Seite hebt diese Sperre auf.

## Sprachen

Die Seiten und E-Mails gibt es auf Englisch und Deutsch; die deutsche Website (zum Beispiel
`/bold_ger/mailpreferences/settings` auf einer Demo-Installation) zeigt „E-Mail-Einstellungen“.

## Verwandte Seiten

- [E-Mail-Einstellungen: die Anleitung für Administratoren](../../anleitungen/mail-einstellungen-administrator.md)
- [Benachrichtigungen: die Anleitung für Benutzer](benachrichtigungen.md)
- [Die rechtliche Checkliste](../../spezifikationen/6.0/mail-einstellungen-compliance.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
