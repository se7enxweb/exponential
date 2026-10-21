# Benachrichtigungen: die Anleitung für Benutzer

Exponential sagt Ihnen per E-Mail, wenn sich Inhalte ändern, denen Sie folgen, und informiert Sie über die Elemente der
Zusammenarbeit, an denen Sie beteiligt sind. Diese Seite ist für Redakteure und Besucher mit einem Konto: was Sie wählen
können, wie Sie einem Element folgen und nicht mehr folgen, wie die Zusammenfassung funktioniert und wie die E-Mails
aussehen. Administratoren finden die Statusseite, den Cronjob und die Fehlersuche in
[der Anleitung für Administratoren](../../anleitungen/benachrichtigungen-administrator.md). Die Seite funktioniert in den Designs
admin4 und admin4l; andere Designs behalten ihre eigenen Einstellungsseiten.

Sie brauchen ein Konto mit der Richtlinie `notification/use` (eine Rolle, die sie gibt; fragen Sie Ihren Administrator,
wenn der Menüeintrag fehlt). Die E-Mails gehen an die E-Mail-Adresse Ihres Kontos.

## Meine Benachrichtigungseinstellungen

Öffnen Sie **Meine Benachrichtigungseinstellungen** im linken Menü des Dashboards oder gehen Sie zu
`/notification/settings`. Die Seite öffnet sich mit drei Kästen: wie vielen Elementen Sie folgen, wie die Nachrichten
kommen (**Sofort**, **Täglich**, **Wöchentlich** oder **Monatlich**) und an welche Adresse sie gehen. Darunter stehen die
Liste der Elemente, die Einstellungen der Zusammenfassung und die Einstellungen zur Zusammenarbeit. Jede Änderung, die Sie
vornehmen, zeigt oben einen Hinweis („Your notification settings were saved.“, „Removed 2 notification(s).“).

### Elemente, denen ich folge

Die Liste zeigt jedes Element, dem Sie folgen, mit seinem Namen (einem Link), seiner Klasse, dem Ort, an dem es liegt
(„In Websites > Newsletter“), und darunter dem Datum der letzten Änderung. Sie erhalten eine E-Mail, wenn unter einem
Element, dem Sie folgen, etwas veröffentlicht wird, sofern Sie es lesen dürfen.

- **Einem Element folgen.** Drücken Sie **Einträge hinzufügen**, wählen Sie im Browser Inhalte aus und bestätigen Sie.
  Oder öffnen Sie ein Element in der Inhaltsstruktur und wählen Sie in seinem Menü **Benachrichtigen**: Eine Seite fragt
  „Über Updates benachrichtigen“, und nur die Schaltfläche **Benachrichtigen** abonniert. Das Öffnen dieser Adresse allein
  ändert nichts.
- **Nicht mehr folgen.** Haken Sie die Elemente an und drücken Sie **Ausgewählte entfernen**. Ein Kasten führt auf, was
  entfernt wird; **Entfernen** bestätigt, **Abbrechen** ändert nichts. Sie können auch die Seite **Benachrichtigen** des
  Elements erneut öffnen: Sie sagt „You already follow this item“ und bietet **Benachrichtigungen für diesen Eintrag
  beenden** an.
- **Elemente, deren Inhalt gelöscht wurde**, sind mit „Inhalt existiert nicht mehr“ markiert. Für sie lässt sich nichts
  senden; entfernen Sie sie.
- **Ein Element finden.** Bei vielen Elementen geben Sie einen Teil eines Namens in **Name enthält** ein und drücken
  **Filtern**; der Filter steht in der Adresse (`/notification/settings/(q)/news`). **Alle anzeigen** leert ihn. Wenn
  Sie Elementen mehrerer Klassen folgen, grenzt eine Typenliste weiter ein.
- **Blättern.** Die Liste zeigt 10, 25 oder 50 Elemente pro Seite, die Einstellung, die Sie in den anderen Listen der
  Administrationsoberfläche verwenden. **Alle auf dieser Seite auswählen** hakt die Seite an.

### Zusammenfassung per E-Mail

Normalerweise wird jede Änderung sofort gemailt, eine E-Mail pro Änderung. Eine Zusammenfassung hält die Nachrichten
zurück und sendet eine E-Mail mit allen:

| Wahl | Sie erhalten eine E-Mail |
|---|---|
| **Täglich, um** eine Stunde | jeden Tag zu dieser Stunde |
| **Einmal pro Woche, am** einem Wochentag | jede Woche an diesem Tag, zu der oben gewählten Stunde |
| **Einmal pro Monat, am Tag Nummer** | jeden Monat an diesem Tag (Tag 29 bis 31 bedeutet den letzten Tag eines kurzen Monats), zu der oben gewählten Stunde |

Haken Sie **Alle Nachrichten in einer Zusammenfassung erhalten** an, wählen Sie und drücken Sie
**Digest-Einstellungen speichern**. Entfernen Sie den Haken, um wieder sofort E-Mails zu erhalten. Die Stunde ist die
Zeit des Servers. Eine Zusammenfassung wird beim nächsten Lauf des Benachrichtigungs-Cronjobs nach ihrer Zeit gesendet;
sie kommt nur, wenn etwas geschehen ist.

### Benachrichtigung zur Zusammenarbeit

Der Kasten führt die Arten von Elementen der Zusammenarbeit auf (zum Beispiel **Freigabe**), die Ihnen E-Mails schicken.
Haken Sie die gewünschten an und drücken Sie **Einstellungen zur Zusammenarbeit speichern**. Sie werden über die
Elemente benachrichtigt, an denen Sie beteiligt sind, als Autor oder als Freigebender, zum Beispiel wenn etwas auf Ihre
Entscheidung wartet.

## Wie die E-Mails aussehen

Eine E-Mail über veröffentlichte Inhalte (Betreff und Text sind das Standard-Template; Ihre Website kann es überschrieben
haben):

```text
To: undisclosed-recipients:;
Subject: Article "NOTTEST first article" was published [example.com - NOTTEST UI folder]
From: Graham Brookins <info@se7enx.com>
Bcc: nottest-ui@nottest.invalid
Content-Type: text/plain; charset=utf-8
Message-ID: <node.21950.eznotification@example.com>
In-Reply-To: <node.21931.eznotification@example.com>

This email is to inform you that a new item has been published at example.com.
The item can be viewed by using the URL below.

NOTTEST first article - Graham Brookins
http://example.com/websites/nottest-ui-folder3/nottest-first-article


If you do not want to continue receiving these notifications,
change your settings at:
http://example.com/notification/settings

--
example.com notification system
```

Der Betreff nennt Klasse, Titel und Ort; der Absender ist der Autor. Die Empfänger stehen in `Bcc`, sodass niemand die
anderen sieht. Der Link am Ende führt zurück zu **Meine Benachrichtigungseinstellungen**. Eine E-Mail über ein
aktualisiertes Element sagt „was updated“ und „an updated item has been published“. Die Header `Message-ID`,
`In-Reply-To` und `References` lassen E-Mail-Programme die E-Mails eines Ortes zu einem Thread gruppieren.

Eine Zusammenfassung (die Elemente des Tages, jedes in der Form, die das Template der Klasse ihm gibt):

```text
Subject: [example.com] Digest for Sunday October 04 2026 10:40:33 pm
Bcc: nottest-ui@nottest.invalid

This digest email is to inform you on new items at example.com.

NOTTEST digest example

Current version-1
Title:  NOTTEST digest example
...
```

Eine E-Mail zur Zusammenarbeit (eine Freigabe, die auf Sie wartet):

```text
Subject: [example.com] Approval of "Sample: Editorial guidelines" awaits your  attention
Bcc: anna.sample@example.invalid

This email is to inform you that "Sample: Editorial guidelines" awaits your attention at example.com.
The publishing process has been halted and it is up to you to decide if it should continue or stop.
The approval can be viewed by using the URL below.
http://example.com/collaboration/item/full/63

If you do not want to continue receiving these notifications,
change your settings at:
http://example.com/notification/settings
```

Diese E-Mails wurden auf einer Testinstallation erstellt und in Dateien geschrieben; keine wurde gesendet.

## Warum habe ich keine E-Mail bekommen?

| Prüfen | Weil |
|---|---|
| Sie folgen dem Element oder einem darüber nicht | Ein Abonnement gilt für das Element und alles darunter. Sehen Sie in die Liste |
| Sie dürfen den neuen Inhalt nicht lesen | Benachrichtigungen gehen nur an die, die ihn lesen dürfen |
| Der Inhalt ist verborgen oder unsichtbar | Verborgene Inhalte werden nicht angekündigt |
| Sie haben eine Zusammenfassung gewählt | Nachrichten warten auf die Stunde der Zusammenfassung |
| Es ist eine Aktualisierung einer alten Version | Nur die aktuelle Version wird angekündigt |
| Der Redakteur hat ohne Benachrichtigung veröffentlicht | Korrekturen lassen sich veröffentlichen, ohne die Abonnenten zu informieren, wo die Website das erlaubt ([Veröffentlichen ohne Benachrichtigung](../../../eng-us/features/6.0/publish-without-notification.md)) |
| Noch ist nichts gelaufen | E-Mails werden vom Benachrichtigungs-Cronjob gesendet; fragen Sie Ihren Administrator |
| Ihr Konto ist abgeschaltet oder hat keine Adresse | |

## Verwandte Seiten

- [Die Anleitung für Administratoren](../../anleitungen/benachrichtigungen-administrator.md), [die Anleitung für Entwickler](../../anleitungen/benachrichtigungen-entwickler.md)
- [Die Spezifikation](../../spezifikationen/6.0/benachrichtigungen.md), [die Befehle](../../spezifikationen/6.0/benachrichtigungen-cli.md)
- [Hinweise zur Aktualisierung](../../../eng-us/bc/6.0/notification-ui-and-commands.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
