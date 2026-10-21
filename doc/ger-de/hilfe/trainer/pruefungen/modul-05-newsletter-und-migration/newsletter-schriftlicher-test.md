---
title: "Lektion 5.1: Newsletter, schriftlicher Test"
audience: trainer, editor, admin
category: training
order: 51
summary: Ein schriftlicher Test mit dreizehn Fragen für das Ende des Kurses Newsletter mit CJW Newsletter, zu Systemen, Listen und Ausgaben, Double-Opt-in, Abonnementzuständen, dem Versand per Cron, den eigenen Mail-Transporten des Newsletters, Bounces und der Sperrliste, mit einem Lösungsschlüssel, der die Dokumentation zitiert. 25 Minuten für die Teilnehmenden.
keywords: prüfungen und praktische übungen, newsletter, schriftlicher test, newsletter-system, liste, ausgabe, double-opt-in, abonnementstatus, mail-warteschlange, cronjob, datei-transport, bounces, sperrliste, assessments and practical exercises, newsletters, cjw_newsletter, written test, newsletter system, list, edition, double opt-in, subscription status, mail queue, TransportMethodCronjob, TransportMethodPreview, file transport, blacklist
type: help
---
# Lektion 5.1: Newsletter, schriftlicher Test

Dieser Test schließt den Newsletter-Kurs ab. Seine wichtigsten Fragen betreffen Mails, die das Haus verlassen: welcher
der drei Transporte der Erweiterung was versendet, warum eine Schulungsinstallation jede Mail in Dateien schreibt und
was Cron zwischen „Senden“ und dem Posteingang tut.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Keine Frage erfordert, dass eine Mail
> versendet wird.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 5: Die Kurse Newsletter und Migration prüfen](README.md) |
| Dauer | 40 Minuten (5 Einweisung, 25 Test, 10 Besprechung der Antworten) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Teilnehmende am Ende des Kurses Newsletter mit CJW Newsletter |
| Voraussetzungen | Der Newsletter-Kurs; [Lektion 1.2](../modul-01-pruefungen-gestalten/schriftliche-tests-schreiben.md) für den Trainer |

## Lernziele

Wer diesen Test besteht, hat gezeigt, dass er Folgendes kann:

- Systeme, Listen und Ausgaben erklären und sagen, wo sie liegen;
- das Double-Opt-in und die Abonnementzustände beschreiben;
- die beiden Cron-Schritte des Versands erklären;
- die drei Newsletter-Transporte und ihre ausgelieferten Werte nennen;
- erklären, wie Bounces und die Sperrliste Listen sauber halten.

## Hinweise für Trainer

### So führen Sie ihn durch

1. Teilen Sie die Fragen ohne den Schlüssel aus; 25 Minuten.
2. Gehen Sie die Antworten mit [cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md) und
   [10.5](../../../../installation/10-nach-der-installation.md#newsletter-cjw_newsletter) auf dem Projektor durch.

### Häufige Fragen

**Unser Release ist älter als 4.2.0.** Das Dashboard und einige Funktionen unterscheiden sich; die Fragen 1 bis 11
gelten für die Releases, die in [cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md) dokumentiert
sind.

### Stolperfallen

- **Frage 9.** Viele Teilnehmende nehmen an, dass auf einer frischen Installation jeder Transport `file` ist. Der
  Transport für Testversände ist im Auslieferungszustand `sendmail`; genau deshalb gibt es diese Frage.

## Der Test

1. Newsletter-Systeme, Listen und Ausgaben: Sind sie Einstellungen, eigene Datenbanktabellen oder Inhalte?
2. Welche Einstellung nennt den Knoten, dessen Unterelemente die Newsletter-Systeme sind?
3. Was ist Double-Opt-in?
4. Nennen Sie vier Zustände, die ein Abonnement haben kann.
5. Eine Redakteurin plant eine Ausgabe ein. Welche zwei Cron-Schritte folgen, und was tut jeder?
6. Welcher Cronjob-Teil sammelt Bounce-Mails ein, und gehört er zu `cjw_newsletter`?
7. Nach wie vielen Bounces erhält ein Benutzer standardmäßig keine Newsletter mehr?
8. Wozu dient die Sperrliste?
9. Nennen Sie die drei Newsletter-Transporte und ihre ausgelieferten Werte.
10. Wohin schreibt der Datei-Transport, und was muss für dieses Verzeichnis gelten?
11. Sie wollen Abonnieren, Bestätigen, Versenden und Abbestellen ohne eine einzige echte Mail proben. Welche Werte der
    Einstellungen verwenden Sie?
12. Welcher Befehl zeigt den Zustand der Erweiterung in einer Shell?
13. Warum verwenden Schulungsinstallationen Testabonnenten unter `example.invalid`?

<details>
<summary>Antworten</summary>

1. Inhalte, der Klassen `cjw_newsletter_system`, `_list` und `_edition`, die wie alle anderen Inhalte bearbeitet
   werden ([cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-parts)).
2. `[NewsletterSettings] RootFolderNodeId` in `cjw_newsletter.ini`
   ([cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#settings-you-will-use)).
3. Abonnenten bestätigen das Abonnement über eine Mail, bevor sie Newsletter erhalten
   ([cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md),
   [10.5](../../../../installation/10-nach-der-installation.md#newsletter-cjw_newsletter)).
4. Vier davon: ausstehend, bestätigt, freigegeben, vom Benutzer entfernt, von einem Administrator entfernt, Soft
   Bounce, Hard Bounce, gesperrt ([cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-parts)).
5. `cjw_newsletter_mailqueue_create` baut die Mail-Warteschlange der fälligen Ausgaben auf;
   `cjw_newsletter_mailqueue_process` versendet, was in der Warteschlange wartet; der Teil `cjw_newsletter` führt
   beide aus ([10.5](../../../../installation/10-nach-der-installation.md#newsletter-cjw_newsletter)).
6. `cjw_newsletter_mailbox`; er gehört nicht zu `cjw_newsletter`
   ([10.5](../../../../installation/10-nach-der-installation.md#newsletter-cjw_newsletter)).
7. Nach 3, `[BounceSettings] BounceThresholdValue`
   ([cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#settings-you-will-use)).
8. Für Adressen, die niemals Mails erhalten
   ([cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#the-parts)).
9. `TransportMethodCronjob` `file` (Ausgaben), `TransportMethodPreview` `sendmail` (Testversände),
   `TransportMethodDirectly` `file` (Abonnementbestätigungen und Info-Mails)
   ([10.5](../../../../installation/10-nach-der-installation.md#newsletter-cjw_newsletter)).
10. In `FileTransportMailDir`, standardmäßig `var/log/mail`, eine `.eml`-Datei pro Mail; das Verzeichnis muss für den
    Benutzer des Webservers und für den Cron-Benutzer beschreibbar sein
    ([cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).
11. Alle drei `TransportMethod*` auf `file`, in `settings/override/cjw_newsletter.ini.append.php`
    ([cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).
12. `./console ext:cjw_newsletter:status`
    ([cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#dashboard-commands-and-background-runs-4117)).
13. Die Top-Level-Domain `.invalid` stellt niemals zu, sodass selbst ein falsch eingestellter Transport keinen echten
    Menschen erreichen kann.

</details>

## Bewertung

- Ein Punkt pro Frage; Frage 5 braucht beide Schritte, Frage 9 alle drei Transporte mit ihren Werten.
- Bestanden: 9 von 13 Punkten oder mehr, mit den Fragen 9 und 11 richtig: Beide entscheiden darüber, ob echte Mails
  eine Schulungs- oder Testinstallation verlassen.

## Weiterführende Literatur

- [cjw_newsletter: Newsletter in der Administration](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md)
- [10.4 Mail](../../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand) und
  [10.5 Newsletter](../../../../installation/10-nach-der-installation.md#newsletter-cjw_newsletter)
- [Mail-Einwilligung und Bounces](../../../hosting/betrieb/einwilligung-und-bounces-bei-mails.md)

## Verwandte Seiten

- [Lektion 5.2: Newsletter, praktische Aufgabe](newsletter-praktische-aufgabe.md)
- [Modul 5: Die Kurse Newsletter und Migration prüfen](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
