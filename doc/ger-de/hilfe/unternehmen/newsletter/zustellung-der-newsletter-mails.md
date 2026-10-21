---
title: Zustellung der Newsletter-Mails
audience: businesses, admin, hosting
category: business-solutions
order: 70
summary: Wie Newsletter-Mails eine Exponential-Website verlassen - die drei Transporte pro Art von Mail, eine vollständige Probe, die Mails in Dateien schreibt, der schrittweise Wechsel zu SMTP oder sendmail, DNS-Einträge der Absenderdomain, Ratenbegrenzungen und Pakete sowie die Cron-Teile, die die Arbeit erledigen.
keywords: Newsletter-Zustellung, Mail-Transport, Datei-Transport, Probe, Ratenbegrenzung, Drosselung, Pakete, Cronjob, Mail-Warteschlange, Zustellbarkeit, newsletter delivery, mail transport, smtp, sendmail, file transport, rehearsal, SPF, DKIM, rate limit, throttle, batches, cronjob, mail queue, deliverability
type: help
---
# Zustellung der Newsletter-Mails

Diese Seite richtet sich an Führungskräfte, die verstehen müssen, was vorhanden sein muss, bevor ein Newsletter
hinausgeht, und an den Administrator oder Hosting-Partner, der es bereitstellt. Sie erklärt die Transporte, wie Sie
proben, ohne etwas zu senden, wie Sie sicher zu echten Mails wechseln und wie Sie innerhalb der Grenzen eines
Mail-Anbieters bleiben.

## Inhalt

1. [Die drei Transporte](#die-drei-transporte)
2. [Erst die Probe](#erst-die-probe)
3. [Von der Probe zu echten Mails](#von-der-probe-zu-echten-mails)
4. [Absenderdomain und Ruf](#absenderdomain-und-ruf)
5. [Ratenbegrenzungen und Pakete](#ratenbegrenzungen-und-pakete)
6. [Die Cron-Teile](#die-cron-teile)
7. [Wer was tut](#wer-was-tut)
8. [Checkliste für die Zustellung](#checkliste-für-die-zustellung)

## Die drei Transporte

`cjw_newsletter.ini [NewsletterMailSettings]` legt einen Transport pro Art von Mail fest
([Newsletter sicher versenden](../../../../eng-us/help/creators/website-creators/sending-newsletters.md#the-transports)):

| Einstellung | Verwendet für | Mitgelieferter Wert |
|---|---|---|
| `TransportMethodCronjob` | Die Ausgaben, die Cron versendet | `file` |
| `TransportMethodPreview` | Testversände aus dem Editor | `sendmail` |
| `TransportMethodDirectly` | Bestätigungen von Abonnements und Info-Mails | `file` |

Jede Methode ist `file` (nichts verlässt den Server; eine `.eml`-Datei pro Mail), `smtp` oder `sendmail`. Die Transporte
der Erweiterung sind von den eigenen Mail-Einstellungen der Website getrennt. Werte gehören in
`settings/override/cjw_newsletter.ini.append.php` und das SMTP-Passwort „in `settings/override/`, nie in die
Versionsverwaltung“ (dieselbe Seite).

> **Warnung**
> Der mitgelieferte Vorschau-Transport ist `sendmail`; der Kommentar in `cjw_newsletter.ini` sagt „the extension default
> really sends“ (der Standard der Erweiterung sendet wirklich). Setzen Sie für eine Probe alle drei Methoden auf `file`.

## Erst die Probe

Der Datei-Transport ermöglicht eine vollständige Probe: „abonnieren, Double-Opt-in, senden, Cron, abmelden“ ohne
Mail-Server und ohne echten Empfänger
([cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).

```ini
[NewsletterMailSettings]
TransportMethodCronjob=file
TransportMethodPreview=file
TransportMethodDirectly=file
FileTransportMailDir=var/log/mail
```

Jeder Empfänger bekommt eine eindeutig benannte Datei `<date>-<id>-<recipient>.eml` in `FileTransportMailDir`. Das
Verzeichnis muss für den Benutzer des Webservers und für den Cron-Benutzer beschreibbar sein, und ein Verzeichnis, das
sich nicht beschreiben lässt, ist ein Fehler und kein stiller Verlust (dieselbe Seite). Seit 4.1.17 zeigt das Dashboard
für den Datei-Transport den Postausgang und seine letzte Mail.

Ein Probe-Drehbuch für ein Unternehmen:

1. Abonnieren Sie auf der öffentlichen Website mit einer Testadresse; öffnen Sie die `.eml`-Datei der Bestätigung und
   ihren Link.
2. Schreiben Sie eine kurze Testausgabe und planen Sie sie ein.
3. Lassen Sie Cron laufen; prüfen Sie, dass pro bestätigtem Testabonnenten eine Datei erscheint.
4. Öffnen Sie eine Datei in einem Mail-Programm und prüfen Sie das Aussehen, die Links und den Abmeldelink.
5. Melden Sie sich über den Link ab und bestätigen Sie, dass sich der Status geändert hat.

Verwenden Sie Testadressen, die keinen echten Menschen erreichen können (zum Beispiel unter der reservierten Domain
`example.invalid`).

## Von der Probe zu echten Mails

Folgen Sie der Reihenfolge des Leitfadens für Website-Ersteller
([Newsletter sicher versenden](../../../../eng-us/help/creators/website-creators/sending-newsletters.md#from-rehearsal-to-real-mail)):

1. Proben Sie mit `file` für alle drei Methoden; lesen Sie die `.eml`-Dateien.
2. Stellen Sie `TransportMethodPreview` auf `smtp` um und senden Sie einen Test nur an Ihre eigene Adresse.
3. Prüfen Sie, wie er in mehreren Mail-Programmen aussieht und ob die Links und der Abmeldelink funktionieren.
4. Stellen Sie sicher, dass Ihre Absenderdomain bei Ihrem Mail-Anbieter eingerichtet ist (SPF, DKIM).
5. Stellen Sie `TransportMethodDirectly` und `TransportMethodCronjob` auf `smtp` um.

SMTP braucht `SmtpTransportServer`, `SmtpTransportPort`, `SmtpTransportUser`, `SmtpTransportPassword` und
`SmtpTransportConnectionType` (leer, `ssl`, `sslv2`, `sslv3` oder `tls`)
([cjw_newsletter](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#rehearse-the-whole-thing-without-sending-a-single-mail)).

## Absenderdomain und Ruf

Mails von einer Domain ohne SPF- und DKIM-Einträge werden eher als Spam behandelt; „sonst landen Mails im Spam“
(Leitfaden für Website-Ersteller, Schritt 4). Diese Einträge werden bei Ihrem DNS- und Mail-Anbieter gesetzt, nicht in
Exponential. Bitten Sie Ihren Anbieter oder Hosting-Partner, sie einzurichten und schriftlich zu bestätigen, welche
Server für Ihre Domain senden dürfen.

Das Präfix der Betreffzeile von Newsletter-Mails ist `EmailSubjectPrefix`; leer bedeutet seit 4.1.16
`[Newsletter <host of SiteURL>]` ([cjw_newsletter, Einstellungen, die Sie verwenden werden](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md#settings-you-will-use)).
Die meisten Unternehmen setzen ein eigenes Präfix oder keines.

## Ratenbegrenzungen und Pakete

Viele Mail-Anbieter begrenzen, wie viele Nachrichten ein Konto pro Minute oder Stunde senden darf. Release 4.2.0 kann
solche Grenzen einhalten ([deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md)):

| Einstellung (`[ThrottleSettings]`) | Mitgeliefert | Bedeutung |
|---|---|---|
| `Throttle` | `disabled` | `enabled`: Jeder Versand geht in Paketen innerhalb der Grenzen hinaus |
| `BatchSize` | `200` | Mails pro Paket |
| `PauseBetweenBatches` | `0` | Sekunden bis zum nächsten Paket desselben Versands |
| `MaxPerMinute[smtp]`, `MaxPerHour[smtp]` | auskommentiert | Grenzen pro Transport; 0 = keine Grenze |

Wird eine Grenze erreicht, hält der Versand an und wird beim nächsten Lauf der Warteschlange dort fortgesetzt, wo er
aufgehört hat. Ein Transport lässt sich unter Newsletter > Mengengrenzen und Pakete auch für einige Minuten pausieren;
ein pausierter Transport sendet nichts, selbst wenn die Drosselung ausgeschaltet ist (derselbe Leitfaden). Fragen Sie
Ihren Anbieter nach seinen Grenzen und setzen Sie sie etwas niedriger.

## Die Cron-Teile

Der Versand geschieht in Cron, nicht im Browser
([Newsletter mit cjw_newsletter](../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md#setting-it-up)):

```text
*/10 * * * *  cd /path/to/installation && php runcronjobs.php -q -s site cjw_newsletter
25   * * * *  cd /path/to/installation && php runcronjobs.php -q -s site cjw_newsletter_mailbox
```

Der Teil `cjw_newsletter` führt `cjw_newsletter_mailqueue_create.php` und `cjw_newsletter_mailqueue_process.php` aus
(`settings/cronjob.ini.append.php` der Erweiterung); `cjw_newsletter_mailbox` liest Bounce-Postfächer. Allgemeine
Einrichtung von Cron: [Cronjobs](../../../anleitungen/cronjobs.md).

## Wer was tut

| Aufgabe | Unternehmen | Administrator oder Hosting-Partner |
|---|---|---|
| Mail-Anbieter und Absenderadresse wählen | Entscheidet | Berät |
| SPF- und DKIM-Einträge | Genehmigt | Richtet ein |
| Transporte und SMTP-Einstellungen | | Richtet ein, hält das Passwort aus der Versionsverwaltung heraus |
| Cron-Teile und Bounce-Postfach | | Richtet ein und überwacht |
| Probe | Nimmt teil, nimmt ab | Führt durch |
| Ratenbegrenzungen | Nennt die Grenzen des Anbieters | Konfiguriert |

## Checkliste für die Zustellung

- [ ] Alle drei Transporte auf `file`; die Probe ist bestanden und abgenommen.
- [ ] Absenderadresse und Domain sind gewählt; SPF und DKIM sind beim Anbieter eingerichtet.
- [ ] Der Vorschau-Transport steht auf `smtp`; ein Test an eine interne Adresse ist in mehreren Mail-Programmen geprüft.
- [ ] Die Grenzen des Anbieters sind bekannt, und `[ThrottleSettings]` ist bei Bedarf gesetzt.
- [ ] Die Cron-Teile `cjw_newsletter` und `cjw_newsletter_mailbox` laufen für den richtigen Siteaccess.
- [ ] Das Bounce-Postfach ist konfiguriert ([Bounces und Listenpflege](bounces-und-listenpflege.md)).
- [ ] Die übrigen Transporte sind an einem geplanten Tag auf `smtp` umgestellt, und jemand beobachtet den ersten
      Versand.

## Verwandte Seiten

- [Newsletter für Unternehmen](README.md)
- [Ausgaben schreiben und versenden](ausgaben-schreiben-und-versenden.md)
- [Newsletter sicher versenden](../../../../eng-us/help/creators/website-creators/sending-newsletters.md)
- [Nach der Installation: Mail](../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
