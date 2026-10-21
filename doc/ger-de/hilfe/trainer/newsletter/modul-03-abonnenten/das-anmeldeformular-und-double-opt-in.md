---
title: "Lektion 3.1: Das Anmeldeformular und Double-Opt-in"
audience: trainer, editor, admin
category: training
order: 31
summary: Die Teilnehmenden melden im öffentlichen Formular eine Testadresse unter example.invalid an, finden die Bestätigungsmail als Datei, bestätigen über ihren Link und beobachten, wie das Abonnement von wartend zu bestätigt wechselt. 60 Minuten.
keywords: newsletter mit cjw newsletter, abonnieren, anmeldeformular, double opt-in, bestätigungsmail, wartend, bestätigt, datei-transport, newsletter/subscribe, newsletter/configure, example.invalid, newsletters with cjw newsletter, subscribe, confirmation mail, pending, confirmed, file transport
type: help
---
# Lektion 3.1: Das Anmeldeformular und Double-Opt-in

Besucher melden sich über ein öffentliches Formular an. Double-Opt-in bedeutet, dass das Abonnement erst zählt,
nachdem die Person auf einen Link in einer Bestätigungsmail geklickt hat, was beweist, dass die Adresse ihr gehört.
Auf der Schulungsinstallation wird diese Mail in eine Datei geschrieben, daher spielen die Teilnehmenden beide
Rollen: den Besucher, der sich anmeldet, und das Postfach, das empfängt.

> **Hinweis**
> Diese Übung sendet eine Bestätigungsmail. Auf der Schulungsinstallation wird sie in eine Datei geschrieben, weil
> `TransportMethodDirectly=file` gilt (Lektion 1.2). Melden Sie nur Adressen unter `example.invalid` an. Prüfen Sie
> die Transporte, bevor Sie beginnen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Newsletter mit CJW Newsletter](../README.md) |
| Modul | [Modul 3: Abonnenten](README.md) |
| Dauer | 60 Minuten (15 Präsentation, 45 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Redakteure und Administratoren |
| Voraussetzungen | [Lektion 2.2](../modul-02-systeme-listen-und-ausgaben/eine-liste-anlegen-und-konfigurieren.md): eine Liste; Lesezugriff auf `var/log/mail` (Shell oder ein Dateibetrachter, den der Trainer bereitstellt) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- das öffentliche Anmeldeformular öffnen und eine Testadresse für eine Liste anmelden;
- erklären, warum ein neues Abonnement *wartend* ist und was es *bestätigt* macht;
- die Bestätigungsmail im Datei-Postausgang finden und ihren Konfigurationslink lesen;
- das Abonnement bestätigen und seinen neuen Status in der Administration prüfen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Anmeldeformulare auf der Website](../../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md#subscribe-forms-on-the-site),
[Status](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#statuses) und die
[Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail).

1. **Das Formular** (3 Minuten). `newsletter/subscribe` im öffentlichen Siteaccess. Es zeigt die Listen, die man
   abonnieren kann, die Felder **E-Mail**, **Vorname**, **Nachname** und **Anrede** und den Knopf **Abonnieren**. In
   der Administration zeigt **Formular Abonnement** im linken Menü des Newsletters dieselbe Ansicht. Anonyme Besucher
   brauchen die Richtlinie `newsletter/subscribe`.
2. **Wartend** (3 Minuten). Nach **Abonnieren** sagt die Seite, dass eine Mail gesendet wurde und das Abonnement erst
   nach dem Klick auf den Bestätigungslink aktiv ist. Das Abonnement ist jetzt *wartend*.
3. **Die Mail als Datei** (5 Minuten). Die Bestätigung geht über `TransportMethodDirectly`. Mit `file` ist sie eine
   `.eml`-Datei in `var/log/mail`. Öffnen Sie eine am Beamer: die Kopfzeilen, den Text, den Konfigurationslink
   `.../newsletter/configure/<hash>`.
4. **Bestätigen** (4 Minuten). Das Öffnen des Links zeigt die Seite zum Konfigurieren der Newsletter-Einstellungen,
   mit den Listen und den Feldern. **Bestätigen** bestätigt das Abonnement. Zurück in der Administration steigt die
   Zahl **Bestätigt** der Liste.

### Häufige Fragen

**Warum meldet man Personen nicht direkt an?** Ohne Double-Opt-in könnte jeder jeden anderen anmelden. Die Bestätigung
ist außerdem Ihr Nachweis, dass die Person zugestimmt hat
([Einwilligung](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#consent)).

**Der Link in der Datei beginnt mit `http://`, aber die Website ist `https://`.** Das Bestätigungs-Template baut den
Link aus dem Hostnamen; die meisten Server leiten weiter. Wenn Ihrer das nicht tut, sagen Sie es dem Trainer; es ist
ein Einrichtungspunkt für die Live-Website.

**Was, wenn ein Abonnent nie bestätigt?** Das Abonnement bleibt wartend und erhält nichts. Wartende Abonnements stehen
unter **Wartend** auf der Seite der Liste.

**Kann sich jemand zweimal mit derselben Adresse anmelden?** Nein; die Konfigurationsseite fordert dazu auf, eine
andere Adresse zu registrieren, um dieselbe Liste zweimal zu abonnieren.

### Stolperfallen

- **Keine Liste im Formular.** Die Liste muss das Abonnieren erlauben und im Siteaccess des Formulars angezeigt
  werden. Prüfen Sie die Konfiguration der Liste aus Lektion 2.2.
- **Es erscheint keine Datei.** Prüfen Sie den Transport in der Einstellungsansicht und die Berechtigungen von
  `var/log/mail`.
- **Den Link als Administrator öffnen.** Es funktioniert trotzdem, aber die Teilnehmenden verwechseln die beiden
  Rollen. Öffnen Sie den Link in einem privaten Browserfenster.
- **Eine echte Adresse.** Wenn eine Person eine eingetippt hat, halten Sie an und prüfen Sie gemeinsam den
  Postausgang: Mit dem Datei-Transport hat nichts den Rechner verlassen.

## Praktische Übung

Verwenden Sie die Schulungsinstallation (Datei-Transport, Lektion 1.2). Ersetzen Sie `<name>` durch Ihren Vornamen.

1. Öffnen Sie in einem privaten Browserfenster das Anmeldeformular der öffentlichen Website: `/newsletter/subscribe`
   im öffentlichen Siteaccess (der Trainer nennt die vollständige Adresse).
2. Haken Sie Ihre Liste `Weekly tips <name>` an.
3. **E-Mail**: `anna-<name>@example.invalid`. **Vorname**: `Anna`. **Nachname**: `Test`.
4. Klicken Sie auf **Abonnieren** und lesen Sie die Meldung.
5. Öffnen Sie in der Administration Ihre Liste und notieren Sie die Zahlen bei **Wartend** und **Bestätigt**.
6. Listen Sie die neuesten Dateien des Postausgangs auf:

   ```bash
   ls -lt var/log/mail | head -5
   ```

7. Öffnen Sie die neueste `.eml`-Datei, deren Name `anna-<name>` enthält, und suchen Sie die Zeile mit
   `/newsletter/configure/`.
8. Kopieren Sie diese Adresse in das private Browserfenster und öffnen Sie sie.
9. Prüfen Sie auf der Seite, dass Ihre Liste angehakt ist, und klicken Sie auf **Bestätigen**.
10. Laden Sie in der Administration die Seite Ihrer Liste neu und notieren Sie erneut **Wartend** und **Bestätigt**.
11. Wiederholen Sie die Schritte 1 bis 4 mit `ben-<name>@example.invalid`, aber bestätigen Sie nicht. Lassen Sie Ben
    für Lektion 3.2 wartend.

## Erwartetes Ergebnis

- Der Postausgang enthält eine Bestätigungsdatei für Anna und eine für Ben.
- Annas Abonnement ist bestätigt; Bens ist wartend.
- Auf der Seite der Liste zeigt **Bestätigt** 1 und **Wartend** 1.
- Der Trainer prüft das, indem er die Auflistung des Postausgangs und die Listenseite einer Person öffnet.

## Quiz

1. Welchen Status hat ein Abonnement direkt nach **Abonnieren**?
2. Welche Transportmethode befördert die Bestätigungsmail?
   A. `TransportMethodCronjob` B. `TransportMethodPreview` C. `TransportMethodDirectly` D. `Transport` des Kernels
3. Wo finden Sie die Bestätigungsmail auf der Schulungsinstallation?
4. Welche Abonnenten erhalten Ausgaben: wartende, bestätigte, angenommene?
5. Warum ist ein Double-Opt-in über technische Gründe hinaus wichtig?

<details>
<summary>Antworten</summary>

1. Wartend ([Status](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#statuses)).
2. **C.** Bestätigungen von Abonnements und Info-Mails ([10.5](../../../../installation/10-nach-der-installation.md#105-benachrichtigungen-und-newsletter)).
3. Als `.eml`-Datei in `var/log/mail` (`FileTransportMailDir`).
4. Nur bestätigte und angenommene.
5. Es beweist, dass die Person die Adresse besitzt und zugestimmt hat, und ist damit Ihr Nachweis der Einwilligung
   ([Einwilligung](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#consent)).

</details>

## Weiterführende Informationen

- [Newsletter mit cjw_newsletter](../../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md#subscribe-forms-on-the-site):
  Anmeldeformulare auf der Website.
- [Newsletter-Listen und Abonnements](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md):
  jeder Status.
- [Compliance und DSGVO](../../../hosting/sicherheit/compliance-und-dsgvo.md): Einwilligung und Nachweise.

## Verwandte Seiten

- [Modul 3: Abonnenten](README.md)
- [Newsletter mit CJW Newsletter: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
