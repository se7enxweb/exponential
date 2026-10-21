---
title: "Lektion 6.4: Benachrichtigungen ausführen"
audience: trainer, admin
category: training
order: 64
summary: Die Teilnehmenden lesen die Statusseite der Benachrichtigungen, sehen einen Lauf vorab an, ohne E-Mails zu versenden, proben mit exp:notification:run einen Lauf in Dateien und räumen alte Ereignisse auf, zuerst mit einem Probelauf. 40 Minuten.
keywords: website-administration, benachrichtigungen, benachrichtigungsstatus, jetzt ausführen, vorschau (probelauf), exp:notification:run, sammelmail, digest, ereignisse, abonnements, datei-transport, e-mail, site administration, notifications, notification status, Run now, Preview (dry run), events, subscriptions, file transport, mail
type: help
---
# Lektion 6.4: Benachrichtigungen ausführen

Benutzer können Inhalten folgen und eine E-Mail bekommen, sofort oder in einer Sammelmail, wenn etwas Neues
veröffentlicht wird. Nichts wird versendet, bis ein Benachrichtigungslauf stattfindet, normalerweise über den
Cronjob-Teil `frequent`. Diese Lektion vermittelt die Seite des Administrators: die Statusseite, eine sichere Probe,
die keine E-Mail versendet, und das Aufräumen alter Ereignisse.

> **Hinweis**
> Testen Sie Benachrichtigungen nie auf einer Live-Website mit dem echten Mail-Transport. Jede Übung hier ändert
> entweder nichts (Probelauf) oder schreibt die E-Mail in Dateien.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 6: Cronjobs, Workflows und Benachrichtigungen](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, Support-Mitarbeiter |
| Voraussetzungen | [Lektion 6.1](cronjob-teile-und-die-crontab.md); ein Administratorkonto und eine Shell auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Weg von einem veröffentlichten Artikel zu einer versendeten Benachrichtigung beschreiben: Ereignis, Handler,
  Nachrichten, Lauf;
- die Kennzahlen und Listen von **Setup > Benachrichtigungen** lesen;
- einen Lauf mit **Vorschau (Probelauf)** und mit `exp:notification:run --dry-run` vorab ansehen;
- einen echten Lauf proben, der die E-Mails in Dateien schreibt, statt sie zu versenden;
- alte Ereignisse sicher entfernen, zuerst mit einem Probelauf.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Benachrichtigungen: ausführen, die Statusseite lesen und Probleme beheben](../../../../anleitungen/benachrichtigungen-administrator.md),
Abschnitte 1 bis 5. Was Benutzer sehen, steht unter [Benachrichtigungen](../../../../funktionen/6.0/benachrichtigungen.md).

1. **So funktioniert es, in einer Minute** (4 Minuten). Das Veröffentlichen schreibt ein *Ereignis*. Ein *Lauf* gibt
   jedes wartende Ereignis an die Handler, die die Folgenden finden und für jeden eine *Nachricht* schreiben, die sofort
   versendet oder für eine *Sammelmail* aufgehoben wird. Nichts wird versendet, bis ein Lauf stattfindet: der Cronjob,
   **Jetzt ausführen** oder `exp:notification:run`.
2. **Die Statusseite** (5 Minuten). **Setup**, dann **Benachrichtigungen** in der linken Spalte
   (`/notification/status`, Richtlinie `notification/administrate`). Zeigen Sie **Benachrichtigungen ausführen**,
   **Letzte Läufe**, **Ereignisse** (wartend), **Abonnements** und **E-Mail und Handler**.
3. **Sichere Probe** (4 Minuten). Ein Probelauf arbeitet in einer Transaktion, die zurückgerollt wird, und übergibt
   dem Mail-Transport nichts; Adressen werden maskiert. `--mail-file-dir=` macht einen echten Lauf, schreibt aber jede
   E-Mail in eine Datei in diesem Verzeichnis, nur für diesen Prozess.
4. **Aufräumen** (2 Minuten). **Behandelte Ereignisse ohne Restnachrichten entfernen** und
   **Ereignisse entfernen, die älter sind als** mit einem Zeitraum. In der Konsole zuerst
   `exp:notification:events cleanup --dry-run`.

### Häufige Fragen

**Brauche ich für Benachrichtigungen eine eigene Crontab-Zeile?** Nein, wenn die Crontab bereits `frequent` ausführt:
Das Benachrichtigungsskript gehört zu diesem Teil. Fügen Sie keine zweite Zeile hinzu.

**Zwei Läufe gleichzeitig?** Die Sperre des Cronjobs und die eigene Sperre des Benachrichtigungsdienstes sorgen dafür,
dass ein zweiter Lauf abgelehnt und nicht in eine Warteschlange gestellt wird.

**Wohin geht die E-Mail?** Das entscheidet `site.ini [MailSettings] Transport`: `sendmail`, `smtp` oder `file`. Mit
`file` wird die E-Mail nach `var/log/mail` geschrieben, und nichts verlässt den Rechner.

**Wie spät kann eine Sammelmail kommen?** Eine Sammelmail wird beim ersten Lauf nach ihrer Stunde versendet; die
Häufigkeit der Läufe bestimmt also die Verzögerung.

### Stolperfallen

- **Jetzt ausführen ist echt.** Es fragt nach einer Bestätigung und versendet dann die fälligen Nachrichten. Verwenden
  Sie im Kurs **Vorschau (Probelauf)** und die Konsole mit `--mail-file-dir=`.
- **Eine Kopie einer Produktionsdatenbank.** Sie enthält echte Abonnenten. Stellen Sie vor jedem Lauf den
  Datei-Transport ein oder `[MailSettings] DebugSending=enabled` mit Ihrer eigenen `DebugReceiverEmail`.
- **Tausende wartende Ereignisse.** Eine Schulungs-Website, die den Cronjob nie ausgeführt hat, zeigt eine große Zahl
  bei **Ereignisse**. Genau das ist die Lektion: Es wurde nie etwas versendet.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation. Arbeiten Sie in einem Terminal im Wurzelverzeichnis der Installation;
alle Testkonten verwenden Adressen unter `example.invalid`.

1. Öffnen Sie in der Administration **Setup**, dann **Benachrichtigungen**. Notieren Sie die Zahl der wartenden
   Ereignisse und der Abonnements.
2. Klicken Sie auf **Vorschau (Probelauf)** und lesen Sie die Ausgabe im Konsolenkasten auf der Seite. Nichts wird
   versendet.
3. Führen Sie im Terminal dieselbe Vorschau aus:

   ```bash
   php bin/php/console exp:notification:run --dry-run
   ```

4. Sorgen Sie dafür, dass ein Testkonto einem Schulungsordner folgt (öffnen Sie als dieser Benutzer den Ordner und
   verwenden Sie die Folgen- oder Benachrichtigungsoption der Website; Ihr Trainer zeigt, wo), und veröffentlichen Sie
   dann einen kurzen Artikel in diesem Ordner.
5. Listen Sie die Ereignisse auf:

   ```bash
   php bin/php/console exp:notification:events list
   ```

6. Proben Sie einen echten Lauf, der die E-Mails in Dateien schreibt, statt sie zu versenden:

   ```bash
   php bin/php/console exp:notification:run --mail-file-dir=var/tmp/notification-mail
   ```

7. Öffnen Sie das Verzeichnis `var/tmp/notification-mail` und lesen Sie eine Datei, falls eine Nachricht entstanden
   ist: Empfänger, Betreff und Text.
8. Sehen Sie ein Aufräumen vorab an, ohne etwas zu entfernen:

   ```bash
   php bin/php/console exp:notification:events cleanup --older-than=30d --dry-run
   ```

9. Laden Sie **Setup > Benachrichtigungen** neu und vergleichen Sie die Kennzahlen mit Schritt 1. Suchen Sie Ihren Lauf
   unter **Letzte Läufe**.

## Erwartetes Ergebnis

- Die Schritte 2 und 3 listeten auf, was versendet würde, und endeten, ohne etwas zu versenden.
- Schritt 6 endete mit einer Zeile, die mit `PASS:` beginnt, und den Zahlen der behandelten Ereignisse und der
  erzeugten Nachrichten.
- Schritt 7: eine Datei `.mail` an das Testkonto unter `example.invalid`, wenn das Konto dem Ordner folgt und E-Mails
  sofort erhalten möchte; bei der Einstellung Sammelmail wird die Nachricht stattdessen für die Sammelmail aufgehoben.
- Schritt 8 gab Zeilen *Would remove* aus und änderte nichts.
- Der Trainer prüft die Liste **Letzte Läufe** einer Teilnehmerin oder eines Teilnehmers am Beamer.

![Benachrichtigungsstatus: wartende Benachrichtigungen und die letzten Läufe](../../../../../eng-us/help/images/trainers/sa-notifications.webp)

## Quiz

1. Die Benutzer einer Website bekommen überhaupt keine Benachrichtigungen. Was ist die wahrscheinlichste Ursache?
2. Was bewirkt `--mail-file-dir=` bei einem Lauf?
   A. Nichts; es legt nur eine Logdatei fest B. Schreibt die E-Mails in Dateien, statt sie zu versenden C. Versendet
   die E-Mails und behält eine Kopie D. Macht den Lauf zu einem Probelauf
3. Welche Schaltfläche auf der Statusseite versendet echte E-Mails?
4. Ihre Crontab führt bereits den Teil `frequent` aus. Sollten Sie eine Zeile für den Teil `notification` hinzufügen?
5. Warum werden Adressen in einem Probelauf maskiert?

<details>
<summary>Antworten</summary>

1. Es findet kein Benachrichtigungslauf statt: Der Cronjob-Teil, der `notification.php` enthält, ist nicht eingeplant
   ([Benachrichtigungen, Abschnitt 1](../../../../anleitungen/benachrichtigungen-administrator.md#1-wie-es-funktioniert-in-einer-minute)).
2. **B.** Es macht einen echten Lauf, aber die E-Mails werden nur für diesen Prozess in Dateien geschrieben.
3. **Jetzt ausführen**, nach seiner Bestätigung.
4. Nein; `frequent` enthält es bereits. Eine zweite Zeile würde nur auf die Sperre treffen.
5. Damit ein Bildschirm oder ein Log, das im Kurs oder in einem Ticket geteilt wird, keine Adressen echter Personen
   zeigt; fügen Sie `--addresses` nur hinzu, wenn Sie sie brauchen.

</details>

## Weiterführende Literatur

- [Benachrichtigungen: ausführen, die Statusseite lesen und Probleme beheben](../../../../anleitungen/benachrichtigungen-administrator.md):
  jede Kennzahl, die Problemliste und die Fehlersuche.
- [Benachrichtigungen](../../../../funktionen/6.0/benachrichtigungen.md): was Benutzer sehen und einstellen.
- [Befehle für Benachrichtigungen](../../../../spezifikationen/6.0/benachrichtigungen-cli.md): jede Option der Konsolenbefehle.
- [E-Mail-Zustellung für Hoster](../../../hosting/betrieb/mailzustellung.md): Transporte und Zustellung auf einem
  Server.

## Verwandte Seiten

- [Modul 6: Cronjobs, Workflows und Benachrichtigungen](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
