---
title: "Lektion 5.2: Newsletter, praktische Aufgabe"
audience: trainer, editor, admin
category: training
order: 52
summary: Eine praktische Aufgabe von 60 Minuten für das Ende des Newsletter-Kurses, auf einer Schulungsinstallation jeden Newsletter-Transport auf Dateien setzen, zwei Testabonnenten unter example.invalid mit Double-Opt-in anmelden, eine Ausgabe schreiben und über die Cronjob-Teile versenden, die entstandenen Mail-Dateien lesen, einen Abonnenten abmelden und eine Adresse sperren, bewertet mit einem Bewertungsraster aus sechs Kriterien. 85 Minuten mit Bewertung.
keywords: prüfungen und praktische übungen, newsletter, praktische aufgabe, bewertungsraster, datei-transport, double-opt-in, ausgabe, mail-warteschlange, eml-dateien, abbestellen, sperrliste, assessments and practical exercises, newsletters, cjw_newsletter, practical task, rubric, file transport, TransportMethodPreview, example.invalid, double opt-in, edition, mail queue, runcronjobs, eml files, unsubscribe, blacklist
type: help
---
# Lektion 5.2: Newsletter, praktische Aufgabe

Die Teilnehmenden durchlaufen einen vollständigen Newsletter-Zyklus, vom ersten Abonnement bis zur Abbestellung, und
weisen jeden Schritt mit den Dateien nach, die die Installation geschrieben hat. Nichts wird versendet: Jeder
Transport schreibt in Dateien, und jeder Abonnent hat eine Adresse unter `example.invalid`.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. **Versenden Sie in dieser Aufgabe niemals
> Mails.** Verwenden Sie eine Schulungsinstallation, deren Newsletter-Transporte alle auf `file` stehen (Schritt 1
> prüft das), und nur Testabonnenten unter `example.invalid`. Der Transport für Testversände,
> `TransportMethodPreview`, ist im Auslieferungszustand `sendmail`: Er muss geändert werden, bevor irgendjemand einen
> Testversand auslöst.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 5: Die Kurse Newsletter und Migration prüfen](README.md) |
| Dauer | 85 Minuten (5 Einweisung, 60 Aufgabe, 20 Bewertung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Teilnehmende am Ende des Kurses Newsletter mit CJW Newsletter |
| Voraussetzungen | [Lektion 5.1](newsletter-schriftlicher-test.md); ein Administratorkonto und eine Shell auf der eigenen Schulungsinstallation, mit aktivem `cjw_newsletter` und installiertem Newsletter-Baum |

## Lernziele

Wer diese Aufgabe besteht, hat gezeigt, dass er Folgendes kann:

- die Newsletter-Transporte prüfen und so einstellen, dass keine Mail die Installation verlässt;
- Testbenutzer anmelden und das Double-Opt-in über die Mail-Dateien abschließen;
- eine Ausgabe erstellen und über die beiden Cron-Schritte versenden;
- die entstandenen `.eml`-Dateien und das Dashboard lesen;
- einen Abonnenten abmelden und eine Adresse sperren.

## Hinweise für Trainer

### So führen Sie sie durch

1. **Vorher**: eine Schulungsinstallation pro Teilnehmer mit aktiver Newsletter-Erweiterung und einem
   Newsletter-System mit einer Liste, Mail-Transport der Website `Transport=file`, keine Crontab. Setzen Sie auch
   selbst alle drei `TransportMethod*` auf `file`; Schritt 1 ist die Prüfung der Teilnehmenden, nicht die einzige
   Absicherung.
2. **Einweisung**: Lesen Sie den Hinweis oben laut vor. Teilen Sie die Schritte und das Bewertungsraster aus;
   60 Minuten.
3. **Bewertung**: Lesen Sie die Notizen der Teilnehmenden, listen Sie `var/log/mail/` der Installation auf, öffnen
   Sie das Newsletter-Dashboard (**Newsletter**, `/newsletter/index`) und die Abonnements der Liste.

### Häufige Fragen

**Wo ist der Bestätigungslink?** In der `.eml`-Datei der Bestätigungsmail in `FileTransportMailDir`. Die
Teilnehmenden öffnen die Datei in einem Texteditor und kopieren den Link in den Browser.

**Der Cron-Lauf hat nichts versendet.** Die Ausgabe wurde nicht eingeplant, oder die Warteschlange wurde aufgebaut,
aber nicht abgearbeitet. Genau das prüft die Aufgabe; lassen Sie die Teilnehmenden es selbst herausfinden.

### Stolperfallen

- **Jemand löst vor Schritt 1 einen Testversand aus.** Stand `TransportMethodPreview` noch auf `sendmail`, brechen
  Sie die Aufgabe ab, prüfen Sie das Mail-Log der Installation und behandeln Sie es als Vorfall. Deshalb setzt der
  Trainer die Werte vorher.
- **Echte Adressen.** Jede Adresse, die nicht unter `example.invalid` liegt, ergibt 0 Punkte bei **Sicherheit**.

## Praktische Übung

Die Aufgabe, wie sie an die Teilnehmenden ausgeteilt wird. Verwenden Sie Ihre eigene Schulungsinstallation. Jede
Adresse, die Sie eintippen, endet auf `@example.invalid`.

1. Prüfen Sie die Newsletter-Transporte:

   ```bash
   ./console exp:ini get cjw_newsletter.ini/NewsletterMailSettings/TransportMethodCronjob --allow-root-user
   ./console exp:ini get cjw_newsletter.ini/NewsletterMailSettings/TransportMethodPreview --allow-root-user
   ./console exp:ini get cjw_newsletter.ini/NewsletterMailSettings/TransportMethodDirectly --allow-root-user
   ```

   Ist einer davon nicht `file`, setzen Sie ihn in `settings/override/cjw_newsletter.ini.append.php` und leeren Sie
   den INI-Cache. Machen Sie erst weiter, wenn alle drei `file` ausgeben. Notieren Sie `FileTransportMailDir`.
2. Öffnen Sie auf der öffentlichen Website das Abonnementformular (`/newsletter/subscribe`) und melden Sie
   `reader01@example.invalid` für die Liste an.
3. Suchen Sie die Bestätigungsmail als Datei im Mail-Verzeichnis. Öffnen Sie sie in einem Texteditor, kopieren Sie den
   Bestätigungslink und öffnen Sie ihn. Notieren Sie den Zustand des Abonnements davor und danach.
4. Melden Sie `reader02@example.invalid` auf dieselbe Weise an und bestätigen Sie das Abonnement.
5. Öffnen Sie in der Administration **Newsletter**, suchen Sie die Liste und schreiben Sie mit
   **Newsletter hier erstellen** eine Ausgabe: einen Betreff mit Ihrem Namen und zwei kurze Absätze. Veröffentlichen
   Sie sie.
6. Machen Sie einen Testversand der Ausgabe an `reader01@example.invalid` und prüfen Sie, dass er als Datei erscheint.
7. Planen Sie die Ausgabe für den Versand an die Liste ein.
8. Führen Sie die beiden Cron-Schritte von Hand aus und notieren Sie jeweils die Ausgabe:

   ```bash
   php runcronjobs.php -s <siteaccess> cjw_newsletter_mailqueue_create
   php runcronjobs.php -s <siteaccess> cjw_newsletter_mailqueue_process
   ```

9. Listen Sie das Mail-Verzeichnis auf und öffnen Sie eine Datei der Ausgabe. Notieren Sie die Betreffzeile und den
   Empfänger.
10. Melden Sie `reader02@example.invalid` mit dem Abmeldelink in dessen Ausgabedatei ab. Prüfen Sie den Zustand.
11. Setzen Sie `blocked01@example.invalid` auf die Sperrliste (**Blacklists**) und versuchen Sie dann, die Adresse
    über das öffentliche Formular anzumelden. Notieren Sie, was geschieht.
12. Führen Sie `./console ext:cjw_newsletter:status` aus und kopieren Sie die Zusammenfassung in Ihre Notizen.

## Erwartetes Ergebnis

- Alle drei Transporte geben `file` aus; das Mail-Verzeichnis enthält die Bestätigungsmails, den Testversand und eine
  Ausgabedatei pro bestätigtem Abonnenten.
- `reader01` ist bestätigt; `reader02` ist vom Benutzer entfernt; `blocked01` steht auf der Sperrliste und erhält
  nichts.
- Die Ausgabe ist auf dem Newsletter-Dashboard unter **Letzte Versände** aufgeführt.
- Der Trainer prüft die Verzeichnisliste, die Abonnements der Liste und die Notizen.

## Bewertungsraster

| Kriterium | 0: Nicht gezeigt | 1: Teilweise | 2: Größtenteils | 3: Vollständig | Nachweis |
|---|---|---|---|---|---|
| Sicherheit (Pflicht) | Ein Transport nicht `file` oder eine echte Adresse verwendet | Geprüft, ein Wert übrig geblieben | Alle `file`, nicht nachgewiesen | Alle drei geprüft und `file`, nur Adressen unter `example.invalid` | Notizen, Ausgabe von `exp:ini get` |
| Double-Opt-in | Kein Abonnement | Angemeldet, nicht bestätigt | Eines bestätigt | Beide über die Mail-Dateien bestätigt, Zustände notiert | Notizen, die Abonnements der Liste |
| Ausgabe | Keine | Erstellt, nicht veröffentlicht | Veröffentlicht, Testversand fehlt | Veröffentlicht, Testversand als Datei | Die Ausgabe, das Mail-Verzeichnis |
| Versand per Cron | Nicht versendet | Ein Schritt ausgeführt | Beide ausgeführt, Ergebnis nicht geprüft | Beide ausgeführt, Ausgabedateien gelesen | Cron-Ausgabe, das Mail-Verzeichnis |
| Abbestellen und Sperren | Keins von beiden | Eines | Beides, Wirkung nicht geprüft | Beides, Zustände und Ablehnung notiert | Die Abonnements der Liste, **Blacklists** |
| Den Zustand lesen | Nichts | Nur Dashboard oder Status, nicht erklärt | Beides, teilweise erklärt | Status-Zusammenfassung und Dashboard erklärt | Notizen |

**Bestanden**: 12 von 18 Punkten oder mehr und **Sicherheit** mit 3. In einem Newsletter-Kurs ist eine einzige
versehentlich versendete echte Mail ein echter Vorfall; dafür gibt es keine Teilpunkte.

## Quiz

Fragen für die Nachbesprechung nach der Bewertung.

1. Welcher der drei Transporte war auf einer frischen Installation der gefährliche, und warum?
2. Was hat der erste Cron-Schritt getan, und was der zweite?
3. Warum konnten Sie das Double-Opt-in ohne jeden Mailserver abschließen?
4. Was würden Sie ändern, um diese Liste wirklich zu versenden, und was muss vorher vorhanden sein?

<details>
<summary>Antworten</summary>

1. `TransportMethodPreview`, im Auslieferungszustand `sendmail`: Ein Testversand würde den Rechner verlassen
   ([10.5](../../../../installation/10-nach-der-installation.md#newsletter-cjw_newsletter)).
2. Der erste hat die Mail-Warteschlange der fälligen Ausgaben aufgebaut; der zweite hat versendet, was in der
   Warteschlange wartete ([10.5](../../../../installation/10-nach-der-installation.md#newsletter-cjw_newsletter)).
3. Der Datei-Transport hat die Bestätigungsmail als Datei mit dem Link darin geschrieben
   ([cjw_newsletter](../../../../funktionen/6.0/erweiterungen/cjw_newsletter.md#den-gesamten-ablauf-erproben-ohne-eine-einzige-mail-zu-versenden)).
4. Die Transporte auf `smtp` oder `sendmail` gesetzt, mit einem Absender aus einer Domain, die Sie kontrollieren, und
   SPF-Einträgen für jede Adressfamilie, über die der Server sendet, dazu Einwilligungsnachweise und eine
   Bounce-Verarbeitung
   ([10.4](../../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand),
   [Mail-Einwilligung und Bounces](../../../hosting/betrieb/einwilligung-und-bounces-bei-mails.md)).

</details>

## Weiterführende Literatur

- [cjw_newsletter: Newsletter in der Administration](../../../../funktionen/6.0/erweiterungen/cjw_newsletter.md): die
  Probe, ohne eine einzige Mail zu versenden.
- [Eine Prüfungssitzung durchführen](../modul-01-pruefungen-gestalten/eine-pruefung-durchfuehren.md): Transporte
  vor der Sitzung vorbereiten.

## Verwandte Seiten

- [Lektion 5.1: Newsletter, schriftlicher Test](newsletter-schriftlicher-test.md)
- [Modul 5: Die Kurse Newsletter und Migration prüfen](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
