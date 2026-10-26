# Mail: Envelope-Absender für sendmail, Absender von "Seite empfehlen", SMTP-Fehler und zufällige Kennungen (6.0.15)

Ab 6.0.15 gibt die Mail-Schicht sendmail nur einen einfachen Envelope-Absender, `content/tipafriend` sendet als die
Website, ein SMTP-Fehler jeder Art ist ein fehlgeschlagener Versand statt einer Fehlerseite, und MIME-Grenzen und
Message-Ids sind zufällig.

## Was sich ändert

- **sendmail `-f`.** Mit `site.ini [MailSettings] SendmailEnvelopeSender=enabled` (dem Standard) wurde der Absender
  als `-f'<adresse>'` übergeben. PHPs `mail()` wendet `escapeshellcmd()` auf die ganze Optionszeichenkette an, was
  diese Quotierung aufhebt (das Muster von PHPMailer CVE-2016-10033). Gemessen: Der Absender
  `"a' -X/tmp/x.php -OQueueDirectory=/tmp '"@example.com` besteht `eZMail::validate()` (ein quotierter lokaler Teil),
  und sendmail erhielt `-X/tmp/x.php` und `-OQueueDirectory=/tmp` als eigene Argumente. Ein solcher Absender ist über
  `content/tipafriend` erreichbar und über ein Formular von `content/collectinformation`, das den Absender setzt. Jetzt
  wird `-f` nur für eine Adresse aus Buchstaben, Ziffern und `._%+=-` vor einem einzigen `@` und einem Hostnamen danach
  gegeben (`eZSendmailTransport::isShellSafeAddress()`), ohne zusätzliche Quotierung; jeder andere Absender wird
  sendmail nicht übergeben, das dann seinen eigenen Standard verwendet, und eine Warnung geht ins Debug-Log.
  Gewöhnliche Adressen sind nicht betroffen.
- **Seite empfehlen.** Ohne `site.ini [TipAFriend] FromEmail` (der Standard) wurde die Mail mit der Adresse des
  Besuchers als Absender und Envelope-Absender gesendet. Sie wird jetzt mit der Adresse der Website gesendet, der
  ersten gültigen von `[MailSettings] EmailSender` und `AdminEmail` (geschrieben als `adresse` oder
  `Name <adresse>`), und die Adresse des Besuchers kommt in `Reply-To`. Der Anzeigename des Absenders ist dann
  `[SiteSettings] SiteName` (ohne Steuerzeichen und Anführungszeichen), nicht der Name, den der Besucher eingegeben
  hat, sodass ein Besucher die Mail nicht als "Bank support <Adresse der Website>" ankommen lassen kann. Eine Antwort
  erreicht weiterhin den Besucher, und die Mail besteht die SPF- und DMARC-Prüfungen der Domain der Website. Nur wenn
  keine der beiden Einstellungen eine gültige Adresse ist, ist die Adresse des Besuchers der Absender, wie bisher. Mit
  gesetztem `FromEmail` wird `Reply-To` ebenfalls hinzugefügt.
- **SMTP-Fehler.** `eZSMTPTransport::sendMail()` fing nur `ezcMailException`. Gemessen mit einem nachgebildeten
  SMTP-Server auf 127.0.0.1, der nur `AUTH NTLM` anbietet: `ezcMailSmtpTransport::authNtlm()` warf
  `ezcBaseExtensionNotFoundException('mcrypt')`, die bis in die Anfrage gelangte (Registrierung, vergessenes
  Passwort). Jeder andere Fehler wird jetzt protokolliert, und der Versand gibt `false` zurück.
- **SMTP-Anmeldeverfahren.** Die neue Einstellung `site.ini [MailSettings] TransportAuthMethod` wählt das Verfahren
  (`PLAIN`, `LOGIN`, `CRAM-MD5`, `DIGEST-MD5`, `NTLM`, `XOAUTH2`, Groß- und Kleinschreibung beliebig). Leer, der
  Standard, behält die Wahl der Bibliothek; ein unbekannter Wert schreibt eine Warnung und wird ignoriert.
- **Zugangsdaten im Klartext.** Ein Benutzername und Passwort, die über eine unverschlüsselte Verbindung
  (`TransportConnectionType` leer oder `tcp`) an einen anderen Host als Loopback gehen, schreiben eine Warnung. Nichts
  wird abgelehnt.
- **Zufällige Kennungen.** Der Teil `multipart/alternative` einer aus einem HTML-Template gebauten Mail erhielt die
  Grenze der Bibliothek (`date( "YmdGHjs" ) . ':' . getmypid() . ':' . counter`), und jede per SMTP gesendete Mail
  eine Message-Id aus denselben Teilen: Die PHP-Prozess-ID stand in jeder Mail. Die Grenze ist jetzt die zufällige des
  Roh-Bodys, und eine Mail ohne eigene Message-Id erhält für jede Erzeugung `<32 Hexadezimalzeichen@Domain des
  Absenders>` (`localhost` ohne brauchbare Absender-Domain). Eine mit `eZMail::setMessageID()` gesetzte bleibt.
- **Eine Mail ohne Absender.** `eZMail::senderText()` einer Mail ohne Absender ergab "Trying to access array offset
  on false" und "mb_encode_mimeheader(): Passing null to parameter #1" (PHP 8.5). Es gibt jetzt still `''` zurück.
- **Unverändert.** Der Newsletter (`cjw_newsletter`) versendet über seine eigenen Transporte und seinen eigenen
  Return-Path; die Benachrichtigungen verwenden `EmailSender` wie bisher. Für einfache Absenderadressen ändert sich an
  beiden nichts.

## Einstellungen

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `site.ini [MailSettings] TransportAuthMethod` | leer | das SMTP-Anmeldeverfahren; leer = das stärkste, das der Server anbietet |

## Was beim Upgrade zu tun ist

- Eine Website, deren SMTP-Server `NTLM` anbietet (häufig bei Microsoft Exchange), konnte überhaupt nicht per SMTP
  senden; setzen Sie `TransportAuthMethod=LOGIN` (oder `PLAIN`) und verwenden Sie `TransportConnectionType=tls` oder
  `ssl`.
- Ein sendmail-Setup, das sich auf eine Absenderadresse mit quotiertem lokalem Teil oder ein Adressliteral als
  Envelope-Absender verließ, erhält jetzt den Standard-Envelope-Absender von sendmail; setzen Sie `EmailSender` auf
  eine einfache Adresse.
- Mails von "Seite empfehlen" kommen jetzt von der Adresse der Website. Setzen Sie `[TipAFriend] FromEmail`, um eine
  andere zu wählen.

## Code und Tests

- `eZSendmailTransport::isShellSafeAddress()` (`lib/ezutils/classes/ezsendmailtransport.php`),
  `eZSMTPTransport::authMethod()` und `sendsCredentialsInClear()` (`lib/ezutils/classes/ezsmtptransport.php`),
  `Tipafriend::siteSender()` und `siteSenderName()` (`kernel/private/classes/views/content/tipafriend.php`),
  `expMailTemplateBody` (`kernel/classes/mail/expmailtemplatebody.php`), `ezpMail::messageIdHost()`
  (`kernel/private/classes/ezpmail.php`), `eZMail::composeEmailName()` und `encodeMimeHeader()`.
- Tests ohne Datenbank: `eZSendmailEnvelopeSenderTest`, `eZSMTPTransportTest` (mit dem nachgebildeten Server
  `tests/tests/lib/ezutils/fixtures/smtp_fake_server.php`), `eZMailTest`, `expMailTemplateBodyTest`,
  `ezpMailMessageIdTest`, `expTipafriendSenderTest`.
