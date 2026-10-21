---
title: Probleme mit E-Mails
audience: government, admin, hosting
category: public-sector
order: 120
summary: Runbook für E-Mails, die nicht ankommen (Passwort-Zurücksetzungen, Formularbestätigungen, Benachrichtigungen, Newsletter) oder dorthin gehen, wo sie nicht hingehören (eine Testkopie schreibt Bürgerinnen und Bürgern) - Transporteinstellungen, Cron, E-Mail-Einstellungen, Prüfungen der Absenderdomain und der Dateitransport.
keywords: E-Mail, E-Mail kommt nicht an, E-Mail zum Zurücksetzen des Passworts, Benachrichtigungen, Newsletter, SMTP, sendmail, Dateitransport, MailSettings, SPF, DKIM, DMARC, Cron, Testkopie, Vorfall, mail, e-mail not arriving, password reset mail, notifications, file transport, test copy, incident
type: help
---
# Probleme mit E-Mails

Dieses Runbook richtet sich an die Administration, wenn Bürgerinnen und Bürger oder Beschäftigte keine E-Mails von einer
öffentlichen Website erhalten oder wenn eine Testkopie E-Mails an echte Personen geschickt hat. Einstellungen und
Transporte beschreiben [E-Mail-Zustellung (Hosting)](../../../hosting/betrieb/mailzustellung.md) und
[Kapitel 10.4](../../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand).

## Inhalt
1. [Welche E-Mail fehlt?](#welche-e-mail-fehlt)
2. [Den Transport prüfen](#den-transport-prüfen)
3. [Prüfen, was E-Mails zurückhält](#prüfen-was-e-mails-zurückhält)
4. [E-Mails landen im Spam oder werden abgewiesen](#e-mails-landen-im-spam-oder-werden-abgewiesen)
5. [Eine Testkopie hat echten Personen geschrieben](#eine-testkopie-hat-echten-personen-geschrieben)
6. [Verwandte Seiten](#verwandte-seiten)

## Welche E-Mail fehlt?

| E-Mail | Gesendet von |
|---|---|
| Passwort-Zurücksetzung, Passwort geändert, Registrierung | der Anfrage selbst |
| Formularbestätigungen, E-Mails an Formularempfänger | der Anfrage selbst |
| Benachrichtigungen | dem Cronjob-Teil `frequent` (`notification.php`) |
| Newsletter | den Cronjobs der Newsletter-Erweiterung |
| Audit-Warnungen | dem Cronjob-Teil `audit` |

Wenn nur per Cron gesendete E-Mails fehlen, beginnen Sie mit der [Überwachung der Cronjobs](../ueberwachung/ueberwachung-der-cronjobs.md).

## Den Transport prüfen

```bash
./console exp:ini get 'site.ini/MailSettings/Transport' --allow-root-user
./console exp:ini where site.ini/MailSettings/Transport --allow-root-user
```

| Transport | Bedeutet |
|---|---|
| `sendmail` (ausgeliefert) | an das lokale Programm `sendmail` übergeben; sehen Sie im Mail-Log des Servers nach |
| `SMTP` | an `TransportServer` gesendet; prüfen Sie Server, Port, `TransportConnectionType` und Anmeldung |
| `file` | in `var/log/mail/` geschrieben: **es wird nichts gesendet**; **Setup > Systeminformationen** zeigt dies als Hinweis |

`settings/override` hat Vorrang vor den Siteaccess-Einstellungen, ein dort gesetzter `Transport` gilt also überall. Leeren
Sie nach einer Änderung den INI-Cache und laden Sie neu.

## Prüfen, was E-Mails zurückhält

- Jede E-Mail passiert die Mail-Schranke, die optionale E-Mails mit den E-Mail-Einstellungen der Empfängerin oder des
  Empfängers abgleicht ([E-Mail-Einstellungen](../../../../anleitungen/mail-einstellungen-administrator.md)); notwendige
  E-Mails wie „Your password was changed“ lassen sich nicht abschalten.
- Die Adresse im Konto der Empfängerin oder des Empfängers kann falsch sein; **Benutzer > Benutzerkonten prüfen** zeigt
  Adressen für Benutzer mit dem Recht, sie zu sehen.
- Newsletter: Rückläufer und die Sperrliste ([Einwilligung und Rückläufer bei E-Mails](../../../hosting/betrieb/einwilligung-und-bounces-bei-mails.md)).

## E-Mails landen im Spam oder werden abgewiesen

Der sendende Server muss für die Domain des Absenders senden dürfen: SPF-, DKIM- und DMARC-Einträge Ihrer Domain, eine
Absenderadresse (`EmailSender`, `AdminEmail`) in dieser Domain, `SenderHost` auf den Hostnamen der Website gesetzt.
Verwaltungsdomains haben oft strenge DMARC-Richtlinien; beziehen Sie Ihr Mail-Team ein. Ein Server, der über IPv6
sendet, braucht seine IPv6-Adresse ebenfalls im SPF-Eintrag.

## Eine Testkopie hat echten Personen geschrieben

1. Stoppen Sie es: Setzen Sie `Transport=file` für diese Installation, in der Variantendatei der Umgebung
   (`settings/override/site.ini.test.append.php` mit `EXP_ENV=test`), und leeren Sie den INI-Cache.
2. Finden Sie heraus, was gesendet wurde: das Log des Mailservers; das Prüfprotokoll für die Aktionen, die E-Mails
   ausgelöst haben.
3. Informieren Sie Ihre Datenschutzbeauftragte oder Ihren Datenschutzbeauftragten, wenn personenbezogene Daten die
   falschen Personen erreicht haben ([Meldung von Datenschutzverletzungen](meldung-von-datenschutzverletzungen.md)).
4. Verhindern Sie es künftig: Jede wiederhergestellte Kopie und jedes Staging-System beginnt mit dem Dateitransport
   ([Umgebungen: Entwicklung, Test und Produktion](../hosting/umgebungen-entwicklung-test-produktion.md)).

## Verwandte Seiten
- [Wiederherstellung auf einem Testserver](../datensicherung-und-wiederherstellung/wiederherstellung-auf-einem-testserver.md)
- [Umgebungsspezifische Einstellungen einrichten](../../aufgaben/betrieb/umgebungsspezifische-einstellungen-einrichten.md)
- [E-Mail-Einstellungen (Funktion)](../../../../funktionen/6.0/e-mail-einstellungen.md)
- [E-Mail-Zustellung (Hosting)](../../../hosting/betrieb/mailzustellung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
