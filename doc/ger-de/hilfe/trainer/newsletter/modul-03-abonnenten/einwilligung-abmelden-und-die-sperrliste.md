---
title: "Lektion 3.4: Einwilligung, Abmelden und die Sperrliste"
audience: trainer, editor, admin
category: training
order: 34
summary: Die Teilnehmenden melden einen Testabonnenten über die öffentliche Seite ab, setzen eine Adresse auf die Sperrliste und erklären, wie die Sperrliste und die Unterdrückungsliste der E-Mail-Einstellungen zusammenarbeiten. 45 Minuten.
keywords: newsletter mit cjw newsletter, abmelden, einwilligung, sperrliste, blacklist, unterdrückungsliste, e-mail-einstellungen, vom benutzer entfernt, dsgvo, datenschutz, newsletters with cjw newsletter, unsubscribe, consent, suppression list, e-mail preferences, removed by user, gdpr, privacy
type: help
---
# Lektion 3.4: Einwilligung, Abmelden und die Sperrliste

Ein Newsletter ist nur so gut wie das Vertrauen seiner Leser. Das Gehen muss so einfach sein wie das Kommen, und eine
Adresse, die nie Mail bekommen darf, darf nie Mail bekommen, egal auf welcher Liste sie steht. Diese Lektion
behandelt die Abmeldeseite, die Sperrliste und die Unterdrückungsliste der E-Mail-Einstellungen des Kernels, die der
Newsletter seit 4.2 mitnutzt.

> **Hinweis**
> Verwenden Sie die Testabonnenten unter `example.invalid` aus den Lektionen 3.1 bis 3.3. Jede Mail, die ein Schritt
> schreibt, landet in einer Datei in `var/log/mail` (Lektion 1.2).

## Auf einen Blick

| | |
|---|---|
| Kurs | [Newsletter mit CJW Newsletter](../README.md) |
| Modul | [Modul 3: Abonnenten](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure und Administratoren; Ansprechpersonen für Datenschutz sind willkommen |
| Voraussetzungen | [Lektion 3.3](csv-import-und-export.md): Anna, Ben, Carla, Dora, Emil und Fay auf der Liste der Person |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Prüfpunkte zur Einwilligung nennen, die ein Newsletter braucht (Double-Opt-in, Abmeldelink, ein klares
  Formular, kurzlebige Exporte);
- einen Abonnenten auf der öffentlichen Abmeldeseite abmelden und den Status *vom Benutzer entfernt* finden;
- eine Adresse mit einer Notiz auf die Sperrliste setzen und erklären, dass sie Vorrang vor jeder Liste hat;
- erklären, wie die Sperrliste und die Unterdrückungsliste des Kernels verbunden sind.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Einwilligung](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#consent),
[Bounces und die Sperrliste](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#bounces-and-the-blacklist),
[E-Mail-Einstellungen: einrichten und betreiben](../../../../anleitungen/mail-einstellungen-administrator.md) und die
Anleitung zur Zustellbarkeit der Erweiterung (verlinkt von der [Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).

1. **Die Checkliste zur Einwilligung** (5 Minuten). Double-Opt-in für jedes öffentliche Abonnement; ein Abmeldelink
   in jeder Ausgabe; ein Anmeldeformular, das sagt, was Abonnenten bekommen und wie oft; Exporte nur so lange
   aufbewahrt wie nötig.
2. **Abmelden** (5 Minuten). Jede Ausgabe trägt einen persönlichen Abmeldelink (die Skins geben einen aus; Skins
   verwenden dafür den Platzhalter, Lektion 6.1). Die Seite `newsletter/unsubscribe/<hash>` fragt einmal nach;
   **Abmelden** setzt das Abonnement auf *vom Benutzer entfernt*. Eine entfernte Person wird von einem Import nie
   wieder angemeldet.
3. **Die Sperrliste** (5 Minuten). **Blacklists** im linken Menü: **Adresse suchen**, **Filtern**, **Eine
   E-Mail-Adresse zur Backlist hinzufügen** (Felder **E-Mail** und **Notiz**, Knopf **Zur Blacklist hinzufügen**) und
   **Ausgewählte löschen**, das zuerst nachfragt. Eine Adresse auf der Sperrliste bekommt von keiner Liste Mail.
4. **Die Unterdrückungsliste** (5 Minuten). Die E-Mail-Einstellungen von Exponential führen eine Unterdrückungsliste
   für jede optionale Mail der Website. Seit 4.2 setzt ein Hard-Bounce oder eine Beschwerde eine Adresse dorthin, und
   der Newsletter setzt sie auf seine Sperrliste. Wer eine Unterdrückung in Setup aufhebt, nimmt die Adresse auch von
   der Sperrliste des Newsletters.

### Häufige Fragen

**Abmelden oder Sperrliste: Was nimmt man für eine Person, die gehen möchte?** Abmelden, damit die Person später
zurückkommen kann. Auf die Sperrliste gehören Adressen, die nie angeschrieben werden dürfen, zum Beispiel auf eine
rechtliche Aufforderung hin oder bei einer Funktionsadresse, die sich beschwert hat.

**Hilft das Löschen eines Benutzers beim Recht auf Vergessenwerden?** Die E-Mail-Einstellungen haben einen
Löschweg, der auch die Daten des Newsletter-Benutzers entfernt; siehe
[E-Mail-Einstellungen](../../../../../eng-us/features/6.0/mail-preferences.md). Folgen Sie dem Verfahren Ihrer Organisation.

**Kann eine Person alle ihre Mail-Einstellungen an einem Ort verwalten?** Ja, auf der Seite der E-Mail-Einstellungen
der Website; die Newsletter-Kategorie erscheint dort.

### Stolperfallen

- **Abmelden in der Administrationsansicht der Person.** Verwenden Sie die öffentliche Seite mit dem Link aus einer
  Mail-Datei, wie es ein Abonnent tun würde.
- **Die echte Adresse einer teilnehmenden Person auf die Sperrliste setzen.** Im Kurs nur `example.invalid`.
- **Einträge der Sperrliste entfernen.** **Ausgewählte löschen** fragt zuerst nach; entfernen Sie im Kurs nur den
  Eintrag, den Sie selbst hinzugefügt haben.

## Praktische Übung

Verwenden Sie die Schulungsinstallation. Ersetzen Sie `<name>` durch Ihren Vornamen.

1. Suchen Sie Annas Bestätigungsdatei aus Lektion 3.1 in `var/log/mail` und kopieren Sie erneut ihre Adresse
   `/newsletter/configure/`. (Mails mit Ausgaben tragen einen eigenen Abmeldelink; Sie sehen ihn in Modul 4.)
2. Öffnen Sie die Adresse in einem privaten Browserfenster. Die Seite fordert dazu auf, die gewünschten Newsletter
   auszuwählen und zum Abmelden die Auswahl aufzuheben.
3. Entfernen Sie den Haken bei Ihrer Liste `Weekly tips <name>` und klicken Sie auf **Bestätigen**.
4. Öffnen Sie in der Administration die Abonnements Ihrer Liste und klicken Sie auf **Entfernt**. Anna wird
   aufgeführt.
5. Klicken Sie im linken Menü auf **Blacklists**.
6. Klicken Sie auf **Eine E-Mail-Adresse zur Backlist hinzufügen**. **E-Mail**: `emil-<name>@example.invalid`.
   **Notiz**: `Training: asked never to be contacted`. Klicken Sie auf **Zur Blacklist hinzufügen**.
7. Verwenden Sie **Adresse suchen** und **Filtern**, um Emils Eintrag zu finden.
8. Öffnen Sie die Abonnements Ihrer Liste und klicken Sie auf **Blacklisted**. Notieren Sie, ob Emil aufgeführt ist.
9. Setzen Sie auf der Seite **Benutzer** den **Status** auf *Blacklisted* und suchen Sie.
10. Diskutieren Sie in Paaren: Warum war für Anna und für Emil jeweils dieses Werkzeug das richtige?

## Erwartetes Ergebnis

- Annas Abonnement ist entfernt; sie wird unter **Entfernt** aufgeführt (prüfen Sie den genauen Wortlaut des Status
  auf Ihrer Installation).
- Emil steht mit der Notiz auf der Sperrliste; die Seite **Benutzer** führt ihn als gesperrt auf.
- Jedes Paar kann sagen, wann man abmeldet und wann man sperrt.
- Der Trainer prüft die Seite der Sperrliste auf Einträge außerhalb von `example.invalid` und entfernt sie nach
  Rückfrage.

## Quiz

1. Nennen Sie zwei Punkte der Checkliste zur Einwilligung.
2. Ein Abonnent klickt auf den Abmeldelink und bestätigt. Welchen Status bekommt das Abonnement?
   A. auf der Sperrliste B. vom Benutzer entfernt C. Bounce D. wartend
3. Was hat Vorrang: ein bestätigtes Abonnement oder ein Sperrlisteneintrag für dieselbe Adresse?
4. Was setzt seit 4.2 eine Adresse automatisch auf die Unterdrückungsliste?
5. Wann sollten Sie das Abmelden der Sperrliste vorziehen?

<details>
<summary>Antworten</summary>

1. Zwei beliebige aus: Double-Opt-in, ein Abmeldelink in jeder Ausgabe, ein klares Anmeldeformular, kurzlebige
   Exporte ([Einwilligung](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#consent)).
2. **B.** ([Status](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#statuses)).
3. Die Sperrliste; sie hat Vorrang vor jeder Liste ([Bounces und die Sperrliste](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#bounces-and-the-blacklist)).
4. Ein Hard-Bounce oder eine Beschwerde (Anleitung zur Zustellbarkeit, verlinkt von der [Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).
5. Wenn die Person vielleicht zurückkommen möchte; die Sperrliste ist für Adressen, die nie Mail bekommen dürfen.

</details>

## Weiterführende Informationen

- [Newsletter-Listen und Abonnements](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#consent):
  die Checkliste zur Einwilligung.
- [E-Mail-Einstellungen: einrichten und betreiben](../../../../anleitungen/mail-einstellungen-administrator.md): die
  Unterdrückungsliste und das Einwilligungsprotokoll.
- [Compliance und DSGVO](../../../hosting/sicherheit/compliance-und-dsgvo.md): das größere Bild zum Datenschutz.

## Verwandte Seiten

- [Modul 3: Abonnenten](README.md)
- [Newsletter mit CJW Newsletter: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
