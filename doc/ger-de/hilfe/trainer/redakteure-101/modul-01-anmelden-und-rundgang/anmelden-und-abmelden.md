---
title: "Lektion 1.2: Anmelden und abmelden"
audience: trainer, editor
category: training
order: 12
summary: Die Teilnehmenden melden sich in der Administration oder im Editor an, kommen mit den üblichen Anmeldemeldungen zurecht, wissen, wie ein vergessenes Passwort funktioniert, und melden sich sicher ab. 45 Minuten.
keywords: redakteure 101, anmelden, login, einloggen, abmelden, logout, passwort, passwort vergessen, zweistufige anmeldung, editor-siteaccess, administration, sitzung, cookies, editors 101, sign in, sign out, forgot password
type: help
---
# Lektion 1.2: Anmelden und abmelden

Jeder Arbeitstag beginnt mit der Anmeldung, und die meisten Supportanfragen neuer Redakteure drehen sich darum. In
dieser Lektion melden sich die Teilnehmenden mit ihrem eigenen Schulungskonto an, lernen die Meldungen kennen, die sie
sehen können, erfahren, was **Passwort vergessen?** tut, und melden sich richtig ab.

> **Hinweis**
> Alle Teilnehmenden brauchen ein eigenes Konto auf der Schulungsinstallation. Geben Sie niemals das
> Administratorkonto der Installation heraus, und verwenden Sie auf einer Schulungs-Website niemals die E-Mail-Adressen
> echter Personen: Verwenden Sie Adressen unter `example.invalid`.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Redakteure 101](../README.md) |
| Modul | [Modul 1: Anmelden und Rundgang](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Neue Redakteure und Autoren |
| Voraussetzungen | [Lektion 1.1](was-exponential-fuer-redakteure-ist.md); ein Schulungskonto mit der Rolle Editor, vom Trainer vorbereitet |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Adresse der Administration oder des Editors öffnen und sich mit einem Benutzernamen oder einer E-Mail-Adresse
  anmelden;
- die Meldung für ein falsches Passwort und die Meldung für eine fehlende Berechtigung erkennen und jeweils sagen, was
  zu tun ist;
- erklären, was **Passwort vergessen?** tut und wie lange sein Link gültig ist;
- sich mit der Schaltfläche **Abmelden** abmelden und erklären, warum sie ausgegraut ist, solange ein Element zum
  Bearbeiten geöffnet ist.

## Hinweise für Trainer

### Die Konten vorbereiten

Melden Sie sich vor dem Kurs auf der Schulungsinstallation (zum Beispiel einer, die mit
`php bin/php/console exp:install` installiert wurde, siehe [7. Die Installation über die Konsole](../../../../installation/07-installation-ueber-die-konsole.md))
als der Administrator an, dessen Passwort das Installationsprogramm ausgegeben hat, und legen Sie unter **Benutzer**
einen Benutzer pro Teilnehmer an, in der Benutzergruppe der Redakteure, mit der Rolle **Editor**. Geben Sie jedem einen
Namen wie `learner01` und eine E-Mail-Adresse wie `learner01@example.invalid`. Schreiben Sie Benutzernamen und
Passwörter auf Karten, eine pro Teilnehmer. Prüfen Sie ein Konto, indem Sie sich vor Kursbeginn damit anmelden.

### So präsentieren Sie es

1. **Die Adresse** (3 Minuten). Die Administration ist ein eigener Teil der Installation mit einer eigenen Adresse:
   meist `/admin` nach der Adresse der Website oder ein eigener Hostname wie `admin.example.com`. Viele Installationen
   haben außerdem den **Editor**, eine einfachere Administration für Menschen, die schreiben und veröffentlichen,
   zum Beispiel unter `edit.example.com`. Zeigen Sie beide auf dem Projektor, wenn Ihre Installation beide hat.
   Quelle: [Anmelden in der Administration](../../../anmelden-in-der-administration.md#die-adresse),
   [Anmelden im Editor](../../../anmelden-im-editor.md#die-adresse).
2. **Das Formular** (3 Minuten). Melden Sie sich auf dem Projektor an: **Benutzername** (oder die E-Mail-Adresse des
   Kontos; mit den mitgelieferten Einstellungen funktioniert beides), **Passwort** (die Augen-Schaltfläche zeigt, was
   eingegeben wurde), **Anmelden**. Die Zeile unter dem Titel nennt den Teil der Installation, zum Beispiel
   *Administration of Editor*.
3. **Die Meldungen** (4 Minuten). Geben Sie absichtlich ein falsches Passwort ein und zeigen Sie die Meldung:
   *Das System konnte keinen Login durchführen. Stellen Sie sicher, dass Benutzername und Password richtig sind.*
   Die Seite sagt nie, welches der beiden falsch war. Erklären Sie dann *Zugriff verweigert! Sie haben keine
   Berechtigung ...*: Das Passwort war richtig, aber die Rolle darf diesen Teil der Installation nicht nutzen.
4. **Passwort vergessen?** (2 Minuten). Erklären Sie die Schritte, ohne sie auszuführen: die E-Mail-Adresse des
   Kontos, **Neues Passwort erstellen**, eine E-Mail mit einem Link, der einmal und standardmäßig eine Stunde lang
   funktioniert, und eine zweite E-Mail mit dem neuen Passwort. Eine Schulungsinstallation kann meist keine E-Mails
   senden, führen Sie es also nicht vor.
5. **Zweistufige Anmeldung** (1 Minute). Erwähnen Sie, dass bei eingeschalteter zweistufiger Anmeldung eine zweite
   Seite **Bestätigen Sie, dass Sie es sind** nach einem Code aus einer App oder einer E-Mail fragt. Modul 8 zeigt, wo
   sie eingeschaltet wird.
6. **Abmelden** (2 Minuten). Die letzte Schaltfläche der Kopfzeile, ein Pfeil, der einen Kasten verlässt, ist
   **Abmelden**. Solange ein Element zum Bearbeiten geöffnet ist, ist sie ausgegraut: Speichern oder verwerfen Sie
   zuerst den Entwurf.

### Häufige Fragen

**Kann ich meine E-Mail-Adresse statt meines Benutzernamens verwenden?** Ja, auf den meisten Websites: Die
mitgelieferte Einstellung akzeptiert beides, weil die E-Mail-Adresse jedes Kontos eindeutig ist
([Anmelden in der Administration](../../../anmelden-in-der-administration.md#anmelden)).

**Bin ich dann auch auf der Website angemeldet?** Nein. Eine Anmeldung gehört zu einem Browser und zu einem Teil der
Installation: Wer im Editor angemeldet ist, ist dadurch weder in der Administration noch auf der öffentlichen Website
angemeldet.

**Wie lange bleibe ich angemeldet?** Solange Sie arbeiten. Eine Sitzung, die lange nicht genutzt wird, endet (mit den
mitgelieferten Einstellungen nach drei Tagen), und Sie melden sich erneut an.

**Mein Browser hat in einem kleinen Fenster nach Benutzername und Passwort gefragt, bevor die Seite erschien.** Das
ist ein zusätzliches Passwort des Webservers, wie es auf Test- und Staging-Servern üblich ist. Es ist kein
Exponential-Konto; fragen Sie die Person, die Ihnen die Adresse gegeben hat.

### Stolperfallen

- **Feststelltaste.** Groß- und Kleinschreibung müssen stimmen. Zeigen Sie auf die Feststelltaste, wenn jemand
  zweimal scheitert.
- **Blockierte Cookies.** Landet jemand ohne Meldung wieder auf dem Anmeldeformular, blockiert sein Browser das
  Sitzungscookie. Erlauben Sie Cookies für die Website oder verwenden Sie ein privates Fenster.
- **Sperre.** Eine Installation kann ein Konto nach einer bestimmten Zahl falscher Passwörter sperren (der
  mitgelieferte Wert sperrt nie). Ist Ihre auf Sperren eingestellt, setzen Sie die fehlgeschlagenen Anmeldungen des
  Teilnehmers als Administrator zurück, auf der Seite des Benutzers unter **Benutzerkonten-Einstellungen
  konfigurieren** > **Zurücksetzen**.
- **Gemeinsame Konten.** Lassen Sie niemals zwei Teilnehmende ein Konto teilen: Entwürfe gehören ihrem Ersteller, und
  die Übungen der nächsten Module setzen voraus, dass alle nur ihre eigenen Entwürfe sehen.

## Praktische Übung

Verwenden Sie die Schulungsinstallation, die der Trainer vorbereitet hat, und die Kontokarte, die Sie bekommen haben.

1. Öffnen Sie die Adresse der Administration (oder des Editors), die der Trainer an die Tafel geschrieben hat.
2. Geben Sie unter **Benutzername** Ihren Benutzernamen ein. Geben Sie unter **Passwort** absichtlich ein falsches
   Passwort ein und klicken Sie auf **Anmelden**.
3. Lesen Sie die Meldung, die erscheint, und schreiben Sie sie auf.
4. Geben Sie jetzt Ihr richtiges Passwort ein. Klicken Sie auf die Augen-Schaltfläche im Passwortfeld, um zu prüfen,
   was Sie eingegeben haben, und klicken Sie dann auf **Anmelden**.
5. Suchen Sie auf dem **Dashboard** oben die Begrüßung mit Ihrem Namen.
6. Melden Sie sich mit der letzten Schaltfläche der Kopfzeile (**Abmelden**) ab.
7. Melden Sie sich erneut an und geben Sie diesmal unter **Benutzername** die E-Mail-Adresse Ihres Kontos (von Ihrer
   Karte) ein.
8. Öffnen Sie die Anmeldeseite in einem zweiten Tab desselben Browsers: Sie sehen, dass Sie noch angemeldet sind.
9. Melden Sie sich wieder ab und prüfen Sie, dass die Adresse jetzt das Anmeldeformular zeigt.
10. Suchen Sie auf dem Anmeldeformular **Passwort vergessen?** (wo die Website es anbietet) und öffnen Sie es, aber
    senden Sie das Formular nicht ab: Lesen Sie, was es abfragt, und gehen Sie zurück.

## Erwartetes Ergebnis

- Schritt 3: Die Teilnehmenden haben die Meldung *Das System konnte keinen Login durchführen ...* aufgeschrieben.
- Schritt 5: Das Dashboard begrüßt die Teilnehmenden mit Namen (zum Beispiel *Guten Morgen, ...*), gefolgt von den
  Blöcken des Dashboards wie **Meine Entwürfe** und **Meine Warteliste**.
- Schritt 7: Die Anmeldung mit der E-Mail-Adresse funktioniert (es sei denn, die Installation hat `AuthenticateMatch`
  geändert; dann erklärt der Trainer, dass die Website nur den Benutzernamen akzeptiert).
- Schritt 10: Die Seite fragt nach der E-Mail-Adresse des Kontos und bietet **Neues Passwort erstellen** an.
- Der Trainer prüft das, indem er alle Teilnehmenden bittet, ihre Begrüßung auf dem Dashboard zu zeigen.

![Das Anmeldeformular mit Benutzername, Passwort und der Schaltfläche Anmelden](../../../../../eng-us/help/images/trainers/e101-sign-in.webp)

![Das Dashboard nach der Anmeldung, das die Teilnehmenden mit Namen begrüßt, darunter Meine Entwürfe und Meine Warteliste](../../../../../eng-us/help/images/trainers/e101-dashboard-greeting.webp)

## Quiz

1. Sie sehen nach Eingabe des richtigen Passworts *Zugriff verweigert! Sie haben keine Berechtigung ...*. Was ist
   die wahrscheinlichste Ursache?
   A. Ein falsches Passwort B. Ihre Rolle darf diesen Teil der Installation nicht nutzen C. Die Feststelltaste D. Die
   Website ist nicht erreichbar
2. Wie lange funktioniert der Link einer E-Mail von **Passwort vergessen?** mit den mitgelieferten Einstellungen?
3. Die Schaltfläche **Abmelden** ist ausgegraut. Was tun Sie?
4. Richtig oder falsch: Wer sich im Editor anmeldet, ist damit auch auf der öffentlichen Website angemeldet.
5. Sie werden nach der Anmeldung ohne Meldung auf das Anmeldeformular zurückgeschickt. Was prüfen Sie zuerst?

<details>
<summary>Antworten</summary>

1. **B.** Das Konto stimmt, aber seine Rolle hat keine Berechtigung, sich hier anzumelden
   ([Anmelden im Editor](../../../anmelden-im-editor.md#wenn-die-anmeldung-nicht-funktioniert)).
2. Einmal und standardmäßig eine Stunde lang ([Anmelden in der Administration](../../../anmelden-in-der-administration.md#passwort-vergessen)).
3. Ein Element ist zum Bearbeiten geöffnet: Speichern oder verwerfen Sie zuerst den Entwurf und melden Sie sich dann
   ab.
4. **Falsch.** Eine Anmeldung gehört zu einem Browser und zu einem Teil der Installation.
5. Ob der Browser Cookies für die Website blockiert; die Anmeldung braucht eines.

</details>

## Weiterführende Literatur

- [Anmelden im Editor](../../../anmelden-im-editor.md): jede Meldung der Anmeldung im Editor.
- [Anmelden in der Administration](../../../anmelden-in-der-administration.md): die Administration, Sperren und Sicherheit.
- [Ihr Konto und die Anmeldung](../../../konten-und-anmeldung.md): Passwörter und die zweistufige Anmeldung.
- [7. Die Installation über die Konsole](../../../../installation/07-installation-ueber-die-konsole.md): wie der Trainer eine
  Schulungsinstallation einrichtet.

## Verwandte Seiten

- [Modul 1: Anmelden und Rundgang](README.md)
- [Redakteure 101: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
