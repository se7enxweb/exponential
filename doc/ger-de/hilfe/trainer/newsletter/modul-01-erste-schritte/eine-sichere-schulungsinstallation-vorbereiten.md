---
title: "Lektion 1.2: Eine sichere Schulungsinstallation vorbereiten"
audience: trainer, admin
category: training
order: 12
summary: Trainer und Administratoren stellen jeden Mail-Transport einer Schulungsinstallation auf Dateien, prüfen das Ergebnis in den Einstellungen und auf dem Dashboard und weisen nach, dass keine Mail den Rechner verlassen kann. 60 Minuten.
keywords: newsletter mit cjw newsletter, schulungsinstallation, datei-transport, sicherheit, testadressen, MailSettings, Transport, TransportMethodCronjob, TransportMethodPreview, TransportMethodDirectly, FileTransportMailDir, example.invalid, newsletters with cjw newsletter, training installation, file transport, safety
type: help
---
# Lektion 1.2: Eine sichere Schulungsinstallation vorbereiten

Ein Newsletter geht an viele Menschen auf einmal und lässt sich nicht zurückholen. In einem Schulungsraum
vervielfacht sich dieses Risiko mit der Zahl der Teilnehmenden. Diese Lektion ist die Regel des ganzen Kurses: Bevor
jemand eine Ausgabe schreibt, wird jede Mail, die die Installation senden könnte, stattdessen in eine Datei
geschrieben, und jeder Testabonnent hat eine Adresse, die es nicht geben kann.

> **Hinweis**
> Spielen Sie diese Lektion vor dem Kurs selbst durch und dann noch einmal mit den Administratoren im Raum. Verwenden
> Sie für Schulungen niemals eine Live-Website oder eine Kopie einer Live-Datenbank mit echten Abonnenten.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Newsletter mit CJW Newsletter](../README.md) |
| Modul | [Modul 1: Erste Schritte](README.md) |
| Dauer | 60 Minuten (25 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer, Administratoren, die Schulungs- oder Testinstallationen vorbereiten |
| Voraussetzungen | [Lektion 1.1](was-ein-newsletter-system-leistet.md); Shell-Zugang zur Schulungsinstallation; ein Konto für die Administration |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die zwei Stellen nennen, an denen Mail-Transporte eingestellt werden: `site.ini [MailSettings]` des Kernels und
  `cjw_newsletter.ini [NewsletterMailSettings]` des Newsletters;
- alle vier Transporte in `settings/override/` auf `file` setzen;
- erklären, warum das mitgelieferte `TransportMethodPreview=sendmail` die Falle einer frischen Installation ist;
- das Ergebnis in der Administration und auf der Kommandozeile prüfen;
- Testadressen unter `example.invalid` wählen und begründen, warum.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [10.4 Mail: der Datei-Transport und echter Versand](../../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand),
[10.5 Benachrichtigungen und Newsletter](../../../../installation/10-nach-der-installation.md#105-benachrichtigungen-und-newsletter) und der
[Abschnitt zur Probe auf der Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail).

1. **Zwei Transportsysteme** (5 Minuten). Der Kernel sendet Passwort-Mails, Registrierungen und Formular-Mails über
   `site.ini [MailSettings] Transport` (mitgeliefert: `sendmail`). Der Newsletter hat seine eigenen drei Methoden in
   `cjw_newsletter.ini [NewsletterMailSettings]`:

   | Schlüssel | Verwendet für | Mitgelieferter Wert |
   |---|---|---|
   | `TransportMethodCronjob` | die Ausgaben, die Cron sendet | `file` |
   | `TransportMethodPreview` | Testversand einer Ausgabe | `sendmail` |
   | `TransportMethodDirectly` | Bestätigungen von Abonnements und Info-Mails | `file` |

   Zeigen Sie auf die mittlere Zeile: Auf einer frischen Installation **geht ein Testversand wirklich hinaus**.
   Deshalb überschreiben wir ihn.
2. **Die Installation über die Konsole** (3 Minuten). Eine mit `exp:install` angelegte Installation
   ([Kapitel 7](../../../../installation/07-installation-ueber-die-konsole.md)) richtet Mail als sendmail ein; ändern Sie das nach der
   Installation.
3. **Die Override-Dateien** (7 Minuten). Zeigen Sie beide Dateien am Beamer (Übungsschritte 2 und 3). Erklären Sie
   `FileTransportMailDir=var/log/mail`: eine `.eml`-Datei pro Empfänger, benannt `<date>-<id>-<recipient>.eml`. Das
   Verzeichnis muss für den Benutzer des Webservers und für den Cron-Benutzer beschreibbar sein.
4. **Prüfen** (5 Minuten). Caches, dann die Einstellungsansicht (**INI Einstellungen** im linken Menü des Newsletters
   öffnet `cjw_newsletter.ini` in der Einstellungsansicht), dann `ext:cjw_newsletter:status`. Das Dashboard zeigt den
   Hinweis „Mails werden in Dateien geschrieben“: In der Schulung ist das genau das, was wir wollen.
5. **Testadressen** (5 Minuten). Die Top-Level-Domain `.invalid` ist reserviert und kann nie zugestellt werden.
   Verwenden Sie `anna@example.invalid`, `ben@example.invalid` und so weiter. Selbst wenn ein Transport falsch wäre,
   würde eine solche Mail beim ersten Server zurückgewiesen, statt eine Person zu erreichen.

### Häufige Fragen

**Reicht es, die Newsletter-Methoden einzustellen?** Nein. Die Info-Mails des Anmeldeformulars und Kernel-Mails wie
Passworterinnerungen nehmen andere Wege. Setzen Sie auch `Transport=file` des Kernels.

**Kann ich stattdessen `DebugSending` verwenden?** `[MailSettings] DebugSending=enabled` leitet Kernel-Mails an eine
Adresse um, sendet aber trotzdem. Für Schulungen sind Dateien sicherer.

**Wohin schreibt der Datei-Transport des Kernels?** Standardmäßig nach `var/log/mail/`, eine Datei
`<time>-<random>.mail` pro Nachricht
([10.4](../../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand)).

**Brauche ich für diesen Kurs einen Mailserver?** Nein. Genau dafür ist der Datei-Transport da.

### Stolperfallen

- **Ein anderswo gesetzter Wert.** Einstellungen werden aus der Erweiterung, dem Siteaccess und den
  Override-Verzeichnissen zusammengeführt, und eine Datei, von der Sie nichts wussten, kann Sie überraschen. Prüfen
  Sie immer den *wirksamen* Wert in der Einstellungsansicht, nicht nur die Datei, die Sie bearbeitet haben.
- **Der INI-Cache.** Leeren Sie nach dem Bearbeiten von Einstellungen den INI-Cache (Übungsschritt 4). Ohne das
  bleibt der alte Wert.
- **Berechtigungen.** Wenn der Cron-Benutzer `var/log/mail` nicht beschreiben kann, endet der Versand mit einem
  Fehlerergebnis, nicht mit einem stillen Verlust. Prüfen Sie es mit `ls -ld var/log/mail`.
- **Eine echte Datenbank kopieren.** Füllen Sie eine Schulungsinstallation niemals mit einer Kopie echter Abonnenten,
  auch nicht für einen Tag.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, zum Beispiel eine mit `php bin/php/console exp:install`
installierte ([Kapitel 7](../../../../installation/07-installation-ueber-die-konsole.md)), mit aktiver Erweiterung `cjw_newsletter`.
Arbeiten Sie im Wurzelverzeichnis der Installation.

1. Lesen Sie zuerst die aktuellen Werte:

   ```bash
   grep -n "^Transport" settings/site.ini
   grep -n "TransportMethod\|FileTransportMailDir" extension/cjw_newsletter/settings/cjw_newsletter.ini
   ```

2. Öffnen (oder erstellen) Sie `settings/override/site.ini.append.php` und stellen Sie sicher, dass sie Folgendes
   enthält:

   ```ini
   [MailSettings]
   Transport=file
   ```

3. Öffnen (oder erstellen) Sie `settings/override/cjw_newsletter.ini.append.php` und stellen Sie sicher, dass sie
   Folgendes enthält:

   ```ini
   [NewsletterMailSettings]
   TransportMethodCronjob=file
   TransportMethodPreview=file
   TransportMethodDirectly=file
   FileTransportMailDir=var/log/mail
   ```

   Beide Override-Dateien beginnen mit `<?php /*` und enden mit `*/ ?>`, wie jede andere Datei in
   `settings/override/`.
4. Leeren Sie den INI-Cache und den Inhalts-Cache:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-id=content
   ```

5. Stellen Sie sicher, dass das Mail-Verzeichnis existiert und für die Benutzer des Webservers und von Cron
   beschreibbar ist:

   ```bash
   mkdir -p var/log/mail
   ls -ld var/log/mail
   ```

6. Öffnen Sie in der Administration **Newsletter** und klicken Sie im linken Menü auf **INI Einstellungen**. Setzen
   Sie den Haken bei **Nur vom Standard geänderte Einstellungen** und suchen Sie `[NewsletterMailSettings]`. Alle drei
   Methoden müssen `file` lauten.
7. Führen Sie den Statusbefehl aus und lesen Sie die Zeile zum Transport:

   ```bash
   php bin/php/console ext:cjw_newsletter:status
   ```

8. Öffnen Sie die **Übersicht** (das Dashboard) des Newsletters und lesen Sie **Probleme und Hinweise**. Notieren Sie
   den Hinweis, dass Mails in Dateien geschrieben werden.
9. Schreiben Sie eine Liste von sechs Testabonnenten für den Kurs, alle unter `example.invalid`, und heben Sie sie für
   Modul 3 auf.

## Erwartetes Ergebnis

- Beide Override-Dateien enthalten die Werte oben, und die Einstellungsansicht zeigt `file` für die drei
  Newsletter-Methoden.
- Der Statusbefehl meldet den Datei-Transport und das Verzeichnis des Postausgangs.
- Das Dashboard führt den Hinweis auf, dass Mails in Dateien geschrieben werden.
- `var/log/mail` existiert und ist beschreibbar.
- Der Trainer prüft vor dem Beginn von Modul 2 die Seite der Einstellungsansicht und die Liste der Testadressen jeder
  Person.

![Das Abzeichen Schreibt Mails in Dateien auf dem Newsletter-Dashboard: Eine Schulungsinstallation sendet keine echte Mail](../../../../../eng-us/help/images/trainers/nl-safe.webp)

## Quiz

1. Welche Transportmethode des Newsletters wird mit `sendmail` ausgeliefert, und wofür wird sie verwendet?
2. In welche Datei schreiben Sie Ihren eigenen Wert für `TransportMethodPreview`?
   A. `extension/cjw_newsletter/settings/cjw_newsletter.ini` B. `settings/override/cjw_newsletter.ini.append.php`
   C. `settings/site.ini` D. `var/log/mail`
3. Warum verwenden Testabonnenten Adressen unter `example.invalid`?
4. Sie haben die Override-Datei geändert, aber die Einstellungsansicht zeigt immer noch `sendmail`. Was haben Sie
   vergessen?
5. Welche Einstellung der `site.ini` lässt Kernel-Mails (zum Beispiel Passwort-Mails) in Dateien gehen?

<details>
<summary>Antworten</summary>

1. `TransportMethodPreview`, verwendet für den Testversand einer Ausgabe ([10.5](../../../../installation/10-nach-der-installation.md#105-benachrichtigungen-und-newsletter)).
2. **B.** Bearbeiten Sie nie die eigene Datei der Erweiterung; Overrides gehören nach `settings/override/` ([Funktionsseite](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#settings-you-will-use)).
3. Die Top-Level-Domain `.invalid` ist reserviert und kann nie zugestellt werden, also kann keine echte Person die
   Mail erhalten.
4. Das Leeren des INI-Caches (`php bin/php/ezcache.php --clear-tag=ini`), siehe [Caches](../../../../anleitungen/caches.md).
5. `[MailSettings] Transport=file` ([10.4](../../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand)).

</details>

## Weiterführende Informationen

- [10.4 Mail: der Datei-Transport und echter Versand](../../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand):
  die Mail-Einstellungen des Kernels.
- [10.5 Benachrichtigungen und Newsletter](../../../../installation/10-nach-der-installation.md#105-benachrichtigungen-und-newsletter):
  die Newsletter-Transporte und Cron-Teile.
- [7. Die Installation über die Konsole](../../../../installation/07-installation-ueber-die-konsole.md): eine Schulungsinstallation
  anlegen.
- [Caches](../../../../anleitungen/caches.md): welcher Cache nach einer Änderung der Einstellungen zu leeren ist.

## Verwandte Seiten

- [Modul 1: Erste Schritte](README.md)
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
