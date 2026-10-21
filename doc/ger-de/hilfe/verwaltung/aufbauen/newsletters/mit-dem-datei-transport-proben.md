---
title: Mit dem Datei-Transport proben
audience: government, admin
category: public-sector
order: 50
summary: Wie Sie den ganzen Newsletter-Zyklus (anmelden, bestätigen, senden, abmelden) proben, ohne eine einzige echte Mail zu senden, mit dem Datei-Transport von cjw_newsletter, und wie Sie Schritt für Schritt auf echten Versand umschalten.
keywords: Datei-Transport, Probe, Test, Newsletter-Test, file transport, rehearsal, test, eml, TransportMethodCronjob, TransportMethodPreview, TransportMethodDirectly, FileTransportMailDir, smtp, sendmail, newsletter test
type: help
---
# Mit dem Datei-Transport proben

Diese Seite richtet sich an Administratoren. Eine neue Installation schreibt Newsletter-Mails in Dateien, sodass sich
der ganze Ablauf gefahrlos proben lässt
([Newsletter mit cjw_newsletter](../../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md#proben-ohne-echte-mails),
[Newsletter sicher versenden](../../../ersteller/website-ersteller/newsletter-versenden.md)).

## Inhalt

1. [Die Einstellungen](#die-einstellungen)
2. [Die Probe](#die-probe)
3. [Auf echte Mails umschalten](#auf-echte-mails-umschalten)
4. [Testadressen](#testadressen)

## Die Einstellungen

`cjw_newsletter.ini [NewsletterMailSettings]`, überschrieben in `settings/override/cjw_newsletter.ini.append.php`:

| Schlüssel | Verwendet für | Wert für die Probe |
|---|---|---|
| `TransportMethodCronjob` | per Cron gesendete Ausgaben | `file` |
| `TransportMethodPreview` | Testversand aus dem Editor | `file` |
| `TransportMethodDirectly` | Bestätigungen und Info-Mails | `file` |
| `FileTransportMailDir` | wohin `file` je Mail eine `.eml` schreibt | `var/log/mail` (voreingestellt) |

Das Verzeichnis muss für den Benutzer des Webservers und den Cron-Benutzer beschreibbar sein.

## Die Probe

1. Abonnieren Sie auf der öffentlichen Site mit einer Testadresse eine Liste; finden Sie die Bestätigungs-`.eml` im
   Mail-Verzeichnis.
2. Öffnen Sie den Bestätigungslink aus der Datei; das Abonnement wird bestätigt.
3. Legen Sie eine Ausgabe an, senden Sie eine Vorschau, planen Sie sie mit **Send**.
4. Führen Sie Cron aus: `php runcronjobs.php -s <siteaccess> cjw_newsletter`; je Empfänger erscheint eine `.eml`.
5. Öffnen Sie eine Datei in einem Mailprogramm; prüfen Sie Inhalt, Links, den Abmeldelink und die Fußzeile.
6. Melden Sie sich mit dem Link ab; prüfen Sie den Status.

## Auf echte Mails umschalten

1. `TransportMethodPreview=smtp`; senden Sie einen Test nur an Ihre eigene Adresse.
2. Prüfen Sie SPF und DKIM der Absenderdomain bei Ihrem Mail-Anbieter.
3. Schalten Sie `TransportMethodDirectly` und `TransportMethodCronjob` auf `smtp` (oder `sendmail`) um.

Halten Sie SMTP-Zugangsdaten in `settings/override/`, nie in der Versionsverwaltung.

## Testadressen

Verwenden Sie Adressen auf einer Domain, die Sie kontrollieren, oder eine reservierte Testdomain wie `example.invalid`
mit dem Datei-Transport, damit ein Fehler nie echten Menschen eine Mail schickt. Testen Sie nie mit Adressen echter
Abonnenten.

## Verwandte Seiten

- [Newsletter sicher versenden](../../../ersteller/website-ersteller/newsletter-versenden.md)
- [Mail-Zustellung](../../../hosting/betrieb/mailzustellung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
