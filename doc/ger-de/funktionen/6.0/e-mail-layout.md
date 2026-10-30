# Das E-Mail-Layout: jede E-Mail als HTML mit einem Textteil

Seit 6.0.15 ist jede E-Mail, die Exponential versendet, eine HTML-Mail mit einem Textteil (`multipart/alternative`) in
einem gemeinsamen Layout: der Name der Website in einem dunklen Kopf, eine Überschrift, ein kurzer Text, eine
Schaltfläche, wo es einen Link gibt, die Adresse der Schaltfläche als Text darunter (für ein Mailprogramm, das keine
Schaltflächen zeigt), die Sicherheitshinweise im Kleingedruckten und eine Fußzeile. Mailprogramme, die nur Text
anzeigen, erhalten denselben Inhalt als sauberen Text.

Vor 6.0.15 waren die meisten dieser E-Mails reiner Text mit Adressen in eigenen Zeilen und stellenweise holprigen
Formulierungen ("Use this code within this link for fast login by clicking this link to continue your login").

## Welche E-Mails

| E-Mail | Empfänger | Template |
|---|---|---|
| Registrierungsinfo, Konto freigegeben, Aktivierungsmail erneut gesendet | der Benutzer | `user/registrationinfo.tpl`, `user/registrationapproved.tpl`, `user/unactivated_resend_mail.tpl` |
| Neuer Benutzer registriert | der Administrator | `user/registrationfeedback.tpl` |
| Freigabe eines Kontos (Workflow-Ereignis) | die Freigebenden | `workflow/eventtype/expuserapprove_mail.tpl` |
| Passwort vergessen: der Link zum Zurücksetzen, dann das neue Passwort | der Benutzer | `user/forgotpasswordmail.tpl` |
| Ihr Passwort wurde geändert | der Benutzer | `user/password_changed_mail.tpl` |
| Neue Anmeldung bei Ihrem Konto | der Benutzer | `user/new_signin_mail.tpl` |
| Neue Anmeldung eines Benutzers | die Administratoren | `user/new_signin_admin_mail.tpl` |
| Ein Abonnement bestätigen, eine neue E-Mail-Adresse bestätigen, der Link zu den Einstellungen | die Person | `mailpreferences/mail/confirm.tpl`, `email_change.tpl`, `link.tpl` |
| Die E-Mail-Adresse Ihres Kontos wird geändert | die alte Adresse | `mailpreferences/mail/email_change_notice.tpl` |
| Einem Freund empfehlen | der Freund | `content/tipafriendmail.tpl` |
| Gesammelte Informationen (Formulare, Feedback) | der Empfänger des Formulars | `content/collectedinfomail.tpl`, `content/collectedinfomail/*.tpl` (alle über `content/collectedinfomail_body.tpl`) |
| Bestellbestätigung | der Kunde, eine Kopie an den Administrator | `shop/orderemail.tpl` |
| Benachrichtigungen (neue und geänderte Inhalte, Freigaben, Zusammenfassungen) | die Abonnenten | die Text-Templates von `notification/handler/*/view/`, beim Versand zu HTML gemacht |
| Audit-Warnungen | die Empfänger des Audits | beim Versand zu HTML gemacht (`expAuditMailSink`) |
| Code der Zwei-Schritt-Anmeldung (sevenx_authentication_2fa) | der Benutzer | `mail/2fa_code.tpl` der Erweiterung |
| Berichte von xrowextract und ezoracle, die eigenen Systemmails des Newsletters (cjw_newsletter) | die Empfänger, die sie konfigurieren | beim Versand zu HTML gemacht |

Die Newsletter von cjw_newsletter behalten ihre eigenen Skins. Die Website-Registrierung des Setup-Assistenten
(`setup/registration_email.tpl`) bleibt reiner Text: Sie ist ein Bericht für das Projekt, keine E-Mail an einen
Menschen.

## Wie es funktioniert

- `design:mail/layout.tpl` ist das HTML-Layout und `design:mail/layout_text.tpl` sein Gegenstück als reiner Text. Ein
  Mail-Template übergibt nur Texte und Adressen (`mail_title`, `mail_intro`, `mail_code`, `mail_rows`,
  `mail_paragraphs`, `mail_notice`, `mail_buttons`, `mail_links_note`, `mail_outro`, `mail_footer`, `mail_site_name`,
  `mail_preheader`; die Liste mit ihrer Bedeutung steht am Anfang von `layout.tpl`). Das Layout maskiert jeden Wert,
  ein Name oder ein Kommentar, den ein Besucher eingegeben hat, kann also kein Markup hinzufügen.
- Das Template setzt `content_type` auf `text/html` und `body_text` auf die Ausgabe von `layout_text.tpl`; der Absender
  übergibt beides an `expMailTemplateBody::apply()` (oder `applyRendered()`), das die beiden Teile baut.
- E-Mails, die als Text geschrieben sind (Benachrichtigungen, Warnungen, Berichte), laufen über
  `expMailTemplateBody::fromText()`: Der Text bleibt der Textteil; für den HTML-Teil werden seine Absätze in das Layout
  gesetzt, Adressen werden zu Links, und die erste Adresse in einer eigenen Zeile wird zur Schaltfläche.
  `notification.ini [MailSettings] HTMLLayout=disabled` versendet Benachrichtigungen nur als Text.
- Ein Code zum Eintippen (der Anmeldecode, ein neues Passwort) wird groß in einem eigenen Kasten gezeigt, leicht zu
  markieren und zu kopieren.
- Das Layout ist für Mailprogramme gebaut: Tabellen, Inline-Styles und `bgcolor` (Mailprogramme verwerfen Stylesheets,
  die Regel der Administration ohne Inline-CSS gilt daher nicht für Mail-Inhalte), höchstens 600 Pixel breit, lesbar
  bei 375 Pixeln. Programme mit einem dunklen Modus (Apple Mail, iOS, Outlook für Mac) erhalten dunkle Farben über eine
  Media Query; die anderen zeigen die hellen Farben, die jedes Element selbst trägt.
- Die Fußzeile einer optionalen E-Mail (warum Sie sie erhalten, abbestellen, Ihre Einstellungen verwalten) steht in der
  Karte des Layouts, im Textteil und im HTML-Teil.

## Ihr eigenes Aussehen

Überschreiben Sie `mail/layout.tpl` (und bei Bedarf `mail/layout_text.tpl`) in Ihrem Design mit denselben Eingaben: das
Logo (`mail_logo_url`, `mail_logo_alt`, `mail_logo_width`, `mail_logo_height`), die Farben am Anfang der Datei, die
Schriften. Jede E-Mail übernimmt dann das neue Aussehen. Ein Inhalts-Template wird wie bisher überschrieben; setzen Sie
darin weiterhin `content_type` und `body_text`.

Der Test `tests/tests/kernel/classes/mail/expMailLayoutMailsTest.php` zeigt, wie jede E-Mail mit Beispieldaten und ohne
Datenbank gerendert wird; er prüft auch ein geändertes Layout.
