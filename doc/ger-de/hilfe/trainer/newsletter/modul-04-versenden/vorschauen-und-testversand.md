---
title: "Lektion 4.1: Vorschauen und Testversand"
audience: trainer, editor
category: training
order: 41
summary: Die Teilnehmenden prüfen eine Ausgabe mit den Vorschaureitern und der Vorschau als Abonnent, senden über den Datei-Transport einen Test an Adressen unter example.invalid und lesen die Datei der Testmail. 45 Minuten.
keywords: newsletter mit cjw newsletter, testversand, testnewsletter versenden, vorschau, vorschau als abonnent, testgruppen, betreff-präfix [Test], eml-datei, TransportMethodPreview, newsletters with cjw newsletter, test send, send test newsletter, preview, preview as a subscriber, test groups, eml file
type: help
---
# Lektion 4.1: Vorschauen und Testversand

Niemand sollte eine Ausgabe senden, die er nicht als Mail gesehen hat. Die Vorschau zeigt die gerenderte Ausgabe auf
dem Bildschirm; ein Testversand erzeugt die echte Mail für einige Testadressen. In dieser Lektion machen die
Teilnehmenden beides und lesen die Testmail als Datei, genau so, wie ein Mailprogramm sie empfangen würde.

> **Hinweis**
> Testversände laufen über `TransportMethodPreview`, das als `sendmail` ausgeliefert wird: Auf einer nicht
> vorbereiteten Installation verlässt ein Testversand den Rechner. Vor dieser Übung prüft der Trainer in der
> Einstellungsansicht, dass `TransportMethodPreview=file` gilt (Lektion 1.2). Testadressen liegen immer unter
> `example.invalid`.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Newsletter mit CJW Newsletter](../README.md) |
| Modul | [Modul 4: Versand](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Redakteure, die Newsletter versenden |
| Voraussetzungen | [Modul 2](../modul-02-systeme-listen-und-ausgaben/README.md): eine Ausgabe mit Artikeln; [Modul 3](../modul-03-abonnenten/README.md): Testabonnenten |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Ausgabe in den Vorschaureitern **HTML / Text** und **Text** prüfen;
- mit **Vorschau als Abonnent** die Ausgabe mit der Sprache und den Interessen eines Abonnenten sehen;
- mit **Testnewsletter versenden** einen Test an eine oder mehrere Adressen unter `example.invalid` senden;
- die Datei der Testmail finden und sie als Test erkennen (Betreff-Präfix, Kopfzeile).

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Von der Probe zur echten Mail](../../../../../eng-us/help/creators/website-creators/sending-newsletters.md#from-rehearsal-to-real-mail),
[10.5](../../../../installation/10-nach-der-installation.md#105-benachrichtigungen-und-newsletter) und die Anleitungen der
Erweiterung zu Zustellbarkeit und Rendering (verlinkt von der [Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).

1. **Zuerst den Transport prüfen** (3 Minuten). Öffnen Sie **INI Einstellungen** am Beamer und zeigen Sie
   `TransportMethodPreview=file`. Sagen Sie laut, warum: Der mitgelieferte Wert sendet.
2. **Vorschau** (3 Minuten). Die Reiter **HTML / Text** und **Text** auf der Seite der Ausgabe. **Vorschau als
   Abonnent** (auf der Versandseite) zeigt die Ausgabe so, wie ein Abonnent sie bekommt: seine Sprache, die bedingten
   Teile und die Artikel zu seinen Interessen.
3. **Das Testformular** (4 Minuten). Bei einer Ausgabe, die noch nicht gesendet wurde, ist das Testformular mit dem
   **Newsletter Standard E-Mail-Testempfänger** der Liste vorbelegt. Mehrere Adressen werden durch `;` getrennt.
   **Testnewsletter versenden** rendert und sendet sofort; es wird kein Cron gebraucht.
4. **Wie ein Test gekennzeichnet ist** (3 Minuten). Der Betreff beginnt mit `[TestSendSettings] SubjectPrefix`
   (`[Test]`), die Kopfzeile `X-Cjwnl-Test: 1` ist gesetzt, und jede Adresse bekommt ihre eigene Mail. Testmails
   werden nie in den Statistiken gezählt.
5. **Testgruppen** (2 Minuten). **Testgruppen** auf dem Dashboard: benannte Gruppen von Testadressen für eine Liste.
   Das Testformular bietet die Gruppen der Liste an.

### Häufige Fragen

**Ändert ein Testversand die Statistiken der Abonnenten?** Nein; Tests werden nie gezählt.

**Warum zeigt mein Test ungefüllte Platzhalter?** Die Testadressen sind vielleicht keine Abonnenten mit Daten.
Verwenden Sie **Vorschau als Abonnent**, um die Fassung eines echten Abonnenten zu sehen.

**Kann ich in mehreren Mailprogrammen testen?** Auf einer echten Website ja, mit Ihren eigenen Testpostfächern, nach
dem Umstieg in Lektion 5.2. In diesem Kurs öffnen Sie stattdessen die `.eml`-Datei in einem Mailprogramm auf Ihrem
eigenen Rechner.

### Stolperfallen

- **Eine echte Testadresse in der Liste.** Der Testempfänger der Liste wurde in Lektion 2.2 gesetzt; prüfen Sie, dass
  er unter `example.invalid` liegt, bevor Sie den Knopf drücken.
- **Newsletter versenden statt Testnewsletter versenden drücken.** Der echte Versand ist die nächste Lektion. Auf der
  Schulungsinstallation würde er nur Dateien schreiben, aber er ändert den Zustand der Ausgabe. Lesen Sie den Knopf,
  bevor Sie ihn drücken.
- **Die falsche Datei ansehen.** Der Postausgang enthält auch die Bestätigungsdateien aus Modul 3; sortieren Sie nach
  Zeit.

## Praktische Übung

Verwenden Sie die Schulungsinstallation; `TransportMethodPreview=file` ist geprüft (Schritt 1). Ersetzen Sie `<name>`
durch Ihren Vornamen.

1. Öffnen Sie **Newsletter**, klicken Sie auf **INI Einstellungen** und suchen Sie `TransportMethodPreview` unter
   `[NewsletterMailSettings]`. Es muss `file` lauten. Wenn nicht, halten Sie an und rufen Sie den Trainer.
2. Öffnen Sie Ihre Ausgabe `Tips for week 2` (die Sie noch nicht gesendet haben).
3. Lesen Sie die Reiter **HTML / Text** und **Text** noch einmal und beheben Sie Tippfehler (bearbeiten und
   veröffentlichen).
4. Suchen Sie das Testversandformular auf der Seite der Ausgabe. Es zeigt den Testempfänger der Liste,
   `test-<name>@example.invalid`.
5. Fügen Sie nach einem Semikolon eine zweite Adresse hinzu:
   `test-<name>@example.invalid;review-<name>@example.invalid`.
6. Klicken Sie auf **Testnewsletter versenden** und lesen Sie die Ergebnisseite.
7. Listen Sie die neuesten Dateien im Postausgang auf:

   ```bash
   ls -lt var/log/mail | head -5
   ```

8. Öffnen Sie die Datei für `review-<name>`. Suchen Sie die Zeile `Subject:` und die Kopfzeile `X-Cjwnl-Test`.
9. Optional: Kopieren Sie die `.eml`-Datei auf Ihren eigenen Computer und öffnen Sie sie in einem Mailprogramm, um den
   HTML-Teil zu sehen.
10. Öffnen Sie **Newsletter versenden** bei der Ausgabe (drücken Sie den Knopf auf dieser Seite nicht) und verwenden
    Sie **Vorschau als Abonnent** für einen Ihrer Testabonnenten.

## Erwartetes Ergebnis

- Zwei neue `.eml`-Dateien in `var/log/mail`, eine pro Testadresse, jede mit einem Betreff, der mit `[Test]` beginnt.
- Die Ausgabe ist weiterhin ungesendet: **Newsletter Verarbeitungsinformation** ist leer.
- Die Person hat die Ausgabe so gesehen, wie ein Abonnent sie bekommt.
- Der Trainer prüft das, indem er die Testdatei einer Person am Beamer öffnet.

![Testgruppen, die eine Ausgabe erhalten, bevor sie an die Liste geht](../../../../../eng-us/help/images/trainers/nl-test-groups.webp)

## Quiz

1. Welche Transportmethode befördert einen Testversand, und was ist ihr mitgelieferter Wert?
2. Wie senden Sie einen Test an zwei Adressen auf einmal?
3. Was davon kennzeichnet eine Testmail?
   A. der Betreff beginnt mit `[Test]` B. sie wird in den Statistiken gezählt C. sie läuft über Cron
   D. alle Empfänger stehen in einer Mail
4. Was zeigt **Vorschau als Abonnent**, was die Vorschaureiter nicht zeigen?
5. Richtig oder falsch: Ein Testversand braucht den Lauf des Cronjobs.

<details>
<summary>Antworten</summary>

1. `TransportMethodPreview`, ausgeliefert als `sendmail` ([10.5](../../../../installation/10-nach-der-installation.md#105-benachrichtigungen-und-newsletter)).
2. Beide in das Testformular eingeben, durch ein Semikolon getrennt.
3. **A.** Tests werden nicht gezählt, nicht in die Warteschlange gestellt, und jede Adresse bekommt ihre eigene Mail
   (Anleitung zur Zustellbarkeit, verlinkt von der
   [Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).
4. Die Ausgabe mit der Sprache, den bedingten Teilen und den Interessen dieses Abonnenten (Rendering-Anleitung).
5. **Falsch.** Ein Test wird sofort gerendert und gesendet.

</details>

## Weiterführende Informationen

- [Newsletter sicher versenden](../../../../../eng-us/help/creators/website-creators/sending-newsletters.md#from-rehearsal-to-real-mail):
  Testen vor dem echten Versand.
- [10.5 Benachrichtigungen und Newsletter](../../../../installation/10-nach-der-installation.md#105-benachrichtigungen-und-newsletter):
  die drei Transportmethoden.
- [cjw_newsletter: Newsletter in der Administration](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-features-of-420):
  die Anleitungen zu Testgruppen und Rendering.

## Verwandte Seiten

- [Modul 4: Versand](README.md)
- [Newsletter mit CJW Newsletter: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
