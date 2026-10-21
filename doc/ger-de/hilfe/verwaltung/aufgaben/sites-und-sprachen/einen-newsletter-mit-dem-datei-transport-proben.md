---
title: Einen Newsletter mit dem Datei-Transport proben
audience: government, admin
category: public-sector
order: 350
summary: Aufgabe für Administratoren - einen ganzen Newsletter-Zyklus (anmelden, bestätigen, senden, abmelden) durchspielen, während cjw_newsletter die Mails in .eml-Dateien schreibt, sodass keine echte Person eine Mail erhält.
keywords: Probe, Datei-Transport, Test, Newsletter, rehearsal, file transport, eml, cjw_newsletter, TransportMethodCronjob, FileTransportMailDir, test, newsletter
type: help
---
# Einen Newsletter mit dem Datei-Transport proben

Diese Aufgabe richtet sich an Administratoren. Hintergrund:
[Mit dem Datei-Transport proben](../../aufbauen/newsletter/mit-dem-datei-transport-proben.md).

## Schritte

1. In `settings/override/cjw_newsletter.ini.append.php`, `[NewsletterMailSettings]`: `TransportMethodCronjob=file`,
   `TransportMethodPreview=file`, `TransportMethodDirectly=file`; `FileTransportMailDir=var/log/mail` (beschreibbar für
   die Benutzer von Webserver und Cron).
2. Melden Sie sich auf der öffentlichen Site mit einer Testadresse auf einer Domain an, die Sie kontrollieren (oder
   `example.invalid`).
3. Öffnen Sie die Bestätigungs-`.eml` im Mail-Verzeichnis; öffnen Sie ihren Link: Das Abonnement ist bestätigt.
4. Legen Sie eine Ausgabe an, senden Sie eine Vorschau, **Send**.
5. Führen Sie `php runcronjobs.php -s <siteaccess> cjw_newsletter` aus; je Empfänger erscheint eine `.eml`.
6. Öffnen Sie sie in einem Mailprogramm: Inhalt, Links, Fußzeile, Abmeldelink.
7. Melden Sie sich über den Link ab; prüfen Sie den Status in den Abonnements der Liste.

## Prüfen

Alle Mails sind Dateien; nichts hat den Server verlassen. Das Abonnement war ausstehend, dann bestätigt, dann entfernt.

## Probleme

| Problem | Lösung |
|---|---|
| Es erscheinen keine Dateien | Verzeichnisrechte, oder der Cron-Teil lief für einen anderen Siteaccess |
| Eine echte Mail ist hinausgegangen | eine andere Einstellung überschreibt den Transport; prüfen Sie jede Override-Datei |

## Verwandte Seiten

- [Newsletter sicher versenden](../../../ersteller/website-ersteller/newsletter-versenden.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
