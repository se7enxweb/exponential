# Signierte Links in der E-Mail "Neuer Benutzer registriert"

Wenn sich jemand auf der Site registriert, erhält der Administrator die E-Mail "Neuer Benutzer registriert bei <site>"
(`site.ini [UserSettings] RegistrationFeedback=email`, gesendet an `RegistrationEmail`, sonst an
`[MailSettings] AdminEmail`). Seit 6.0.15 ist diese E-Mail eine HTML-Mail mit einem Klartextteil. Sie sagt, in welchem
Zustand sich das neue Konto befindet, und enthält signierte Links in die Administration: das Konto ansehen, es freigeben
und es ablehnen. Jeder Link öffnet die Seite `user/registration/<token>`, die den angemeldeten Administrator um
Bestätigung bittet, bevor sich etwas ändert.

Vor 6.0.15 war die E-Mail reiner Text und hatte einen Link, `http://<host>/content/view/full/<main node>`. Dieser Link
konnte nicht funktionieren: Wenn die E-Mail verschickt wird, ist das Konto meist noch nicht veröffentlicht, hat also
keinen Knoten, und der Link verwendete `http`. Er ist entfallen.

## Kurz gesagt

- Die E-Mail führt das Konto auf (Name, Login, E-Mail-Adresse, Registrierungszeit, Zustand) und bis zu fünf Links.
  Der Textteil enthält denselben Inhalt als Zeilen "Bezeichnung: URL", für E-Mail-Programme, die kein HTML anzeigen.
- **Konto ansehen** öffnet die Seite des Kontos in der Administration. **Konto aktivieren** (oder **Freigeben**) und
  **Ablehnen und entfernen** öffnen eine Bestätigungsseite. **Freigabeeintrag öffnen** öffnet den Freigabeeintrag,
  wenn ein Workflow das Konto festhält. **Alle nicht aktivierten Benutzer** öffnet Benutzer > Nicht aktivierte
  Benutzer.
- Jeder Link ist **signiert** (HMAC-SHA-256 unter einem aus dem Site-Geheimnis abgeleiteten Schlüssel), funktioniert
  standardmäßig **7 Tage** lang und benennt ein Konto und eine Aktion. Links zum Freigeben und Ablehnen funktionieren
  **nur einmal**.
- Das Öffnen eines Links ändert nie etwas. Sie müssen **angemeldet** sein und die **Richtlinie** dafür haben, und die
  Änderung erfolgt erst, wenn Sie auf der Bestätigungsseite die Schaltfläche drücken (ein POST mit dem
  Formular-Token). E-Mail-Scanner, die jeden Link einer E-Mail öffnen, richten keinen Schaden an.
- Freigaben und Ablehnungen sowie jeder abgewiesene Link werden im Prüfprotokoll festgehalten.
- Ein aktives Konto wird über diese Links nie geändert.
- Das Aussehen der E-Mail stammt aus einem Layout-Template, `design:mail/layout.tpl`, das ein Design mit eigenen
  Farben und eigenem Logo überschreiben kann. Das Design media tut das.

## Was die E-Mail enthält

| Teil | Inhalt |
|---|---|
| Betreff | "Neuer Benutzer registriert bei <SiteURL>" |
| Überschrift und Einleitung | "Neuer Benutzer registriert", "Ein neuer Benutzer wurde registriert." |
| Tabelle | Name, Benutzername, E-Mail, Registriert am, Kontostatus |
| Hinweis | bei einem wartenden Konto: was vom Administrator erwartet wird |
| Schaltflächen | die Links unten, je nach Zustand |
| Kleingedrucktes | "Diese Links öffnen die Administration. Sie müssen sich anmelden und werden vor jeder Änderung um Bestätigung gebeten. Links, die das Konto ändern, funktionieren nur einmal. Alle Links verlieren am <date> ihre Gültigkeit." |
| Fußzeile | "Diese E-Mail wurde von <SiteURL> gesendet, weil sich ein neuer Benutzer registriert hat." |

### Die Links

| Link | Angezeigt, wenn | Öffnet |
|---|---|---|
| **Konto aktivieren** | das Konto auf die E-Mail-Bestätigung oder auf einen Administrator wartet | die Bestätigung zum Freigeben |
| **Freigeben** | das Konto auf einen Freigabeeintrag (Workflow) wartet | die Bestätigung zum Freigeben |
| **Konto ansehen** | immer (die Hauptschaltfläche, wenn das Konto nicht wartet) | die Seite des Kontos |
| **Ablehnen und entfernen** | das Konto wartet | die Bestätigung zum Ablehnen |
| **Freigabeeintrag öffnen** | ein Freigabeeintrag auf das Konto wartet | `collaboration/item/full/<id>` |
| **Alle nicht aktivierten Benutzer** | immer | `user/unactivated` |

Die ersten vier sind signierte Links auf `user/registration/<token>`. Die letzten beiden sind gewöhnliche Adressen der
Administration.

### Die Zustände

Die E-Mail und die Seite nennen den Zustand des Kontos. Er wird beim Versand der E-Mail ermittelt und erneut bei jedem
Öffnen der Seite, sodass die Seite immer den aktuellen Zustand zeigt.

| Zustand | Bedeutung | In der E-Mail |
|---|---|---|
| `email` | Das Konto ist deaktiviert und hat einen Aktivierungsschlüssel: Es wartet darauf, dass der Benutzer auf den Link der Aktivierungs-E-Mail klickt (`VerifyUserType=email`). | "Wartet darauf, dass der Benutzer die E-Mail-Adresse bestätigt." Sie können es jetzt aktivieren oder warten. |
| `administrator` | Das Konto ist deaktiviert, hat keinen Aktivierungsschlüssel und hat sich nie angemeldet: Nur ein Administrator kann es aktivieren. | "Deaktiviert, bis ein Administrator es aktiviert." |
| `workflow` | Die Veröffentlichung des Benutzerobjekts wartet auf einen Freigabeeintrag (ein Ereignis *Approve* im Veröffentlichungs-Workflow). | "Wartet vor der Veröffentlichung auf Freigabe." |
| `pending` | Das Konto ist aktiviert, aber sein Benutzerobjekt ist noch nicht veröffentlicht: Ein Workflow läuft noch. | "Aktiviert, aber noch nicht veröffentlicht." Kein Freigeben oder Ablehnen. |
| `disabled` | Das Konto ist deaktiviert und hat keinen Aktivierungsschlüssel, hat sich aber schon einmal angemeldet (`ezuservisit.login_count` > 0): Ein Administrator hat es deaktiviert. Es ist keine wartende Registrierung, sodass ein alter Link zum Aktivieren oder Ablehnen aus der E-Mail es weder aktiviert noch entfernt. | (nur auf der Seite: "Von einem Administrator deaktiviert") |
| `active` | Aktiviert und veröffentlicht. | "Aktiv." Nur Konto ansehen. |
| `missing` | Das Konto existiert nicht mehr (entfernt oder abgelehnt). | (nur auf der Seite) |

Freigeben und Ablehnen sind nur in den Zuständen `email`, `administrator` und `workflow` möglich. Ablehnen entfernt
nie ein geschütztes Konto: den anonymen Benutzer (`[UserSettings] AnonymousUserID`), das Administratorkonto
(`UserCreatorID` und das Konto mit dem Login `admin`, so wie Benutzer > Nicht aktivierte Benutzer es schützt) und den
angemeldeten Benutzer. Die Seite bietet für sie kein Ablehnen an, und ein Ablehnungslink für eines davon sagt, dass es
nichts zu tun gibt.

## Was Freigeben und Ablehnen tun

| Zustand | Freigeben | Ablehnen |
|---|---|---|
| `email`, `administrator` | Aktiviert das Konto genau so wie **Aktivieren** unter Benutzer > Nicht aktivierte Benutzer: Der Aktivierungsschlüssel wird entfernt, das Konto aktiviert und die Registrierung abgeschlossen (das Benutzerobjekt wird veröffentlicht, der Benutzer erhält die E-Mail "Registrierung freigegeben", sofern nicht `ActivationByAdministratorSendsApprovalMail=disabled` oder `EmailRegistrationInfo=disabled` gesetzt ist, und der Workflow `post_register` läuft). | Entfernt das Konto endgültig: Sein Benutzerobjekt wird gelöscht, wie es **Entfernen** unter Benutzer > Nicht aktivierte Benutzer tut. Der Benutzer wird nicht benachrichtigt. |
| `workflow` | Nimmt den Freigabeeintrag an, wie es dessen Schaltfläche **Freigeben** tut, und setzt den wartenden Workflow sofort fort statt beim nächsten Lauf des Workflow-Cronjobs, sodass das Konto sofort veröffentlicht wird. | Lehnt den Freigabeeintrag ab, wie es dessen Schaltfläche **Ablehnen** tut, und entfernt dann den wartenden Workflow-Prozess, den Freigabeeintrag (er würde ein Konto nennen, das nicht mehr existiert) und das Konto. |
| `pending`, `active`, `disabled`, `missing` | Nichts. Die Seite sagt, dass es nichts zu tun gibt. | Nichts. |

Nach der Aktion zeigt die Seite das Ergebnis: *aktiviert*, *freigegeben* oder *entfernt*, und den Zustand, in dem sich
das Konto jetzt befindet. Wurde das Konto zwischen dem Öffnen der Bestätigung und dem Drücken der Schaltfläche von
jemand anderem bearbeitet, sagt die Seite *Es wurde nichts getan*, und das Token ist verbraucht.

## Die Seite `user/registration/<token>`

Die Seite gehört zur Administration (`ui_context` administration). Sie zeigt:

- bei einem **Ansichts**-Link: das Konto (Name, Login, E-Mail-Adresse, Registrierungszeit, ID, Zustand), eine
  Schaltfläche zum Freigabeeintrag, zum veröffentlichten Konto oder, vor der Veröffentlichung, zu einer Vorschau seiner
  aktuellen Version, sowie **Freigeben** und **Ablehnen**, wenn das Konto wartet und Sie handeln dürfen. Diese
  Schaltflächen führen zur Bestätigung eines neuen Links zum Freigeben oder Ablehnen, sodass ein Ansichts-Link so lange
  verwendet werden kann, wie er gültig ist. Die Seite sagt, bis wann.
- bei einem Link zum **Freigeben** oder **Ablehnen**: dieselbe Kontokarte und eine Bestätigung mit **Freigeben** (oder
  **Ablehnen und entfernen**) und **Abbrechen**. Abbrechen führt zurück zur Ansichtsseite des Kontos und lässt den Link
  unverbraucht.
- bei einem abgewiesenen Link: warum er abgewiesen wird, und eine Schaltfläche zu Benutzer > Nicht aktivierte Benutzer.

| Sie sehen | Warum | HTTP-Status |
|---|---|---|
| *Der Link ist ungültig: Er ist unvollständig oder wurde verändert.* | Das Token ist fehlerhaft oder seine Signatur passt nicht (vom E-Mail-Programm abgeschnitten, bearbeitet oder mit einem anderen Site-Geheimnis signiert). | 403 |
| *Der Link ist abgelaufen.* | Er ist älter als `RegistrationActionTTL`. Verwenden Sie stattdessen Benutzer > Nicht aktivierte Benutzer. | 410 |
| *Der Link wurde bereits verwendet.* | Ein Link zum Freigeben oder Ablehnen wurde schon bestätigt. | 403 |
| *Der Link gehört zu einer anderen Aktion.* | Die Aktion des Tokens passt nicht. | 403 |
| Das Anmeldeformular | Sie sind nicht angemeldet. Nach der Anmeldung kommen Sie auf dieselbe Seite zurück. | |
| *Zugriff verweigert* | Sie sind angemeldet, aber Ihnen fehlt die Richtlinie. | 403 |

## Sicherheit

- **Signiert.** Ein Token lautet `<user id>.<action>.<expiry>.<nonce>.<signature>`. Die Signatur ist ein HMAC-SHA-256
  über die anderen vier Teile unter einem aus dem Site-Geheimnis abgeleiteten Schlüssel (`expMailSecret`,
  `mailpreferences.ini [SecretSettings] TokenSecret`, bei der ersten Verwendung in
  `settings/override/mailpreferences.ini.append.php` erzeugt; der hier verwendete Schlüssel wird nur für diesen Zweck
  daraus abgeleitet, nie das Geheimnis selbst). Ein Token kann ohne das Geheimnis weder erzeugt noch verändert oder auf
  ein anderes Konto oder eine andere Aktion übertragen werden. Ein Ändern des Geheimnisses macht jeden bereits
  versandten Link ungültig. Die Signatur wird in konstanter Zeit verglichen.
- **Ablaufend.** Jeder Link endet zu seinem Ablaufzeitpunkt, `RegistrationActionTTL` Sekunden nach dem Versand der
  E-Mail (standardmäßig 7 Tage).
- **Einmalig.** Wenn ein Link zum Freigeben oder Ablehnen bestätigt wird, wird seine Nonce in `ezsite_data`
  gespeichert (Zeile `exp_regaction_<nonce>`, der Wert ist ihr Ablaufzeitpunkt), bevor irgendetwas geschieht. Der Name
  ist der Primärschlüssel der Tabelle, sodass von zwei Anfragen mit demselben Link nur eine durchkommt. Zeilen
  abgelaufener Links werden von Zeit zu Zeit automatisch entfernt. Ein Ansichts-Link wird nicht verbraucht.
- **Anmeldung und Richtlinie.** Ein anonymer Besucher erhält das Anmeldeformular. Ein angemeldeter Benutzer darf
  handeln, wenn er
  - die Richtlinie *User / Activation* (`user/activation`, die Richtlinie von Benutzer > Nicht aktivierte Benutzer)
    hat oder
  - solange ein Freigabeeintrag wartet, die Rolle des Freigebenden dieses Eintrags hat;
  - nur bei einem Ansichts-Link auch *Content / Edit* für das Konto.

  Alle anderen erhalten *Zugriff verweigert*. Ein Benutzer, der nur ansehen darf, sieht keine Schaltflächen
  Freigeben oder Ablehnen.
- **Bestätigen per POST.** Ein GET ändert nie etwas; E-Mail-Scanner und Linkvorschauen öffnen Links. Das Konto wird
  nur durch einen POST des Bestätigungsformulars mit **Freigeben** oder **Ablehnen und entfernen** geändert, und das
  Formular trägt das Formular-Token der Sitzung (`ezformtoken`), sodass eine andere Site es nicht für Sie absenden
  kann.
- **Audit.** Festgehalten im Prüfprotokoll ([Audit-Ereignisse](../../bc/6.0/audit.md)):

  | Ereignis | Wann |
  |---|---|
  | `access.registration.approve` | ein Konto wurde aktiviert oder sein Freigabeeintrag angenommen; `before.state` und `after.state` |
  | `access.registration.reject` | ein Konto wurde abgelehnt und entfernt; `before.state`, `after.state` ist `missing` |
  | `access.registration.refused` | ein Link wurde abgewiesen; `after.reason` ist `malformed`, `signature`, `expired`, `action`, `used`, `policy` (kein Recht zu handeln) oder `state` (das Konto wartet nicht mehr), `after.action` die Aktion des Links |

## Einstellungen

In `site.ini [UserSettings]`, am besten gesetzt in `settings/override/site.ini.append.php` oder im Frontend-Siteaccess
(die E-Mail wird von dem Siteaccess gesendet, in dem sich der Benutzer registriert):

| Einstellung | Standard | Was sie bewirkt |
|---|---|---|
| `RegistrationAdminLinks` | `enabled` | `disabled`: Die E-Mail enthält keine Links, und `user/registration` antwortet mit *nicht gefunden*. |
| `RegistrationAdminBaseURL` | leer | Die Adresse der Administration, auf die die Links zeigen, z. B. `https://admin.example.com` oder `https://www.example.com/admin`. Leer: `https://` und die `SiteURL` des Administrations-Siteaccess, sonst die Adresse, die ihm die Siteaccess-Zuordnung gibt, mit `https`. Ein Wert ohne Schema erhält `https://`; `http://` wird nur verwendet, wenn es ausdrücklich geschrieben ist. |
| `RegistrationAdminSiteAccess` | leer | Der Administrations-Siteaccess. Leer: der Siteaccess, dessen `[SiteSettings] FrontSiteAccess` der aktuelle ist, sonst `admin`. |
| `RegistrationActionTTL` | `604800` | Wie lange ein Link funktioniert, in Sekunden (7 Tage). |

```ini
[UserSettings]
RegistrationAdminBaseURL=https://admin.example.com
RegistrationAdminSiteAccess=admin
RegistrationActionTTL=259200
```

Setzen Sie `RegistrationAdminBaseURL`, wenn die `SiteURL` des Administrations-Siteaccess keine Adresse ist, die der
Administrator erreichen kann (ein interner Hostname, `localhost`, ein Host, der über einen Port oder einen Pfad
zugeordnet wird, den die `SiteURL` nicht enthält). Eine `SiteURL` mit `localhost`, `127.0.0.1`, `::1` oder
`example.com` wird nie verwendet.

So schalten Sie die Links aus:

```ini
[UserSettings]
RegistrationAdminLinks=disabled
```

Die E-Mail wird dann nur mit den Angaben zum Konto gesendet.

## Die E-Mail anpassen

### Der Inhalt: `user/registrationfeedback.tpl`

Überschreiben Sie `design:user/registrationfeedback.tpl` in Ihrem Design, um den Wortlaut oder die Schaltflächen zu
ändern. Es erhält diese Variablen (aus `kernel/private/classes/views/user/register.php`):

| Variable | Inhalt |
|---|---|
| `$user` | der neue `eZUser` (`login`, `email`, `contentobject_id`) |
| `$object` | das Inhaltsobjekt des Benutzers (`name`, `id`, `published`) |
| `$hostname` | der Hostname der Site |
| `$registration_links` | `false`, wenn die Links aus sind, sonst ein Hash: `view`, `approve`, `reject` (die signierten Links), `pending` (die Adresse des Freigabeeintrags oder leer), `unactivated` (die Liste der nicht aktivierten Benutzer), `expires` (Zeitstempel) |
| `$registration_state` | `false` oder `email`, `administrator`, `workflow`, `pending`, `active`, `disabled` |

Es kann in einem `set-block` mit `scope=root` setzen: `subject`, `content_type`, `body_text`, `email_sender` und
`email_receiver`. Wenn `content_type` `html` enthält und `body_text` gesetzt ist, wird die E-Mail als
`multipart/alternative` gesendet, mit `body_text` als Klartextteil und der Ausgabe des Templates als HTML-Teil. Das
funktioniert mit jedem Mail-Transport (sendmail, file und SMTP). Ohne `body_text` ist die Ausgabe des Templates wie
bisher der gesamte Inhalt, sodass eine ältere Überschreibung, die reinen Text schreibt, weiter funktioniert.

Das mitgelieferte Template übergibt dem Layout nur Text und URLs; es schreibt kein eigenes HTML.

### Das Aussehen: `mail/layout.tpl`

Das gesamte HTML der E-Mail steht in `design:mail/layout.tpl`; sein Klartext-Gegenstück ist
`design:mail/layout_text.tpl`. Ein Design mit einem eigenen `templates/mail/layout.tpl` gibt jeder E-Mail, die das
Layout verwendet, seine Farben und sein Logo. Das Design media tut das:
`extension/sevenx_themes_media/design/media/templates/mail/layout.tpl` verwendet seinen gelben Akzent, eine weiße Karte
und sein Site-Logo als PNG (`images/site-logo-mail.png`; E-Mail-Programme zeigen kein SVG an).

Die Eingaben des Layouts, alle optional:

| Eingabe | Inhalt |
|---|---|
| `mail_title` | die Überschrift |
| `mail_intro` | der erste Absatz (Text) |
| `mail_rows` | ein Array aus `hash( 'label', ..., 'value', ... )`, als Tabelle angezeigt |
| `mail_notice` | ein hervorgehobener Satz (Text) |
| `mail_buttons` | ein Array aus `hash( 'label', ..., 'url', ..., 'kind', 'primary'\|'secondary'\|'danger' )` |
| `mail_links` | ein Array aus `hash( 'label', ..., 'url', ... )`, als Textlinks unter den Schaltflächen angezeigt |
| `mail_links_note` | Kleingedrucktes unter den Schaltflächen (Text) |
| `mail_footer` | der Text der Fußzeile; Standard: der Name der Site, verlinkt |
| `mail_logo_url` | die absolute `https`-Adresse eines PNG- oder JPG-Logos; keine: der Name der Site als Text |
| `mail_logo_alt` | der Alternativtext des Logos; Standard: der Name der Site |
| `mail_logo_link` | wohin das Logo verlinkt; Standard: die Site |
| `mail_logo_width`, `mail_logo_height` | die Anzeigegröße des Logos in Pixeln |

Das Layout bereinigt jeden Wert (wash); übergeben Sie reinen Text, kein HTML. Eine Überschreibung sollte beibehalten,
was das mitgelieferte Layout in E-Mail-Programmen (Gmail, Outlook, Apple Mail, Telefone) funktionieren lässt: Tabellen
mit `role="presentation"`, Inline-Styles mit `bgcolor`-Ersatzwerten, höchstens 600 Pixel breit, kein externes
Stylesheet, kein Flexbox oder Grid. Die Farbpalette ist ein Satz von `$mx_*`-Variablen am Anfang der Datei, sodass ein
Design in der Regel nur diese und das Logo ändert.

Andere E-Mails können dasselbe Layout verwenden: Binden Sie `design:mail/layout.tpl` und `design:mail/layout_text.tpl`
mit diesen Eingaben ein, wie es `registrationfeedback.tpl` tut.

### Die Seite

`design:user/registration.tpl` zeichnet die Seite für jedes Administrationsdesign mit den gemeinsamen
Komponentenklassen. Seine Variablen sind am Anfang der Datei beschrieben.

## Die E-Mails, die der neue Benutzer erhält

Die eigenen Registrierungs-E-Mails des Benutzers verwenden dasselbe Layout und sind ebenfalls HTML-Mails mit einem
Klartextteil:

| E-Mail | Template | Gesendet von |
|---|---|---|
| Aktivierung (die E-Mail-Adresse bestätigen) | `user/registrationinfo.tpl` | `eZUserOperationCollection::sendActivationEmail()` |
| Aktivierung erneut gesendet (Benutzer > Nicht aktivierte Benutzer, erneut senden) | `user/unactivated_resend_mail.tpl` | `expUnactivatedUsers::resend()` |
| Registrierung freigegeben | `user/registrationapproved.tpl` | `eZUserOperationCollection::sendUserNotification()` |

Ihre Links verwenden https. Die Aktivierungs-E-Mail verlinkt nicht mehr auf `content/view/full/` (das Konto hatte noch
keinen Knoten). Jeder Absender erhält die Multipart-Mail aus einem Template über
`expMailTemplateBody::apply( $mail, $tpl, $output )`: Wenn das Template `content_type` auf text/html und `body_text`
setzt, hat die E-Mail beide Teile; andernfalls ist sie, wie bisher, das, was das Template vorgibt.

## Dateien

| Datei | Rolle |
|---|---|
| `kernel/classes/user/expregistrationadminaction.php` | Tokens, Zustände, Richtlinie, Freigeben und Ablehnen |
| `kernel/classes/mail/expmailtemplatebody.php` | der Inhalt einer E-Mail aus einem Template (die Multipart-Mail) |
| `kernel/user/registration.php`, `kernel/private/classes/views/user/registration.php` | die Seite `user/registration` |
| `kernel/private/classes/views/user/register.php` | sendet die E-Mail mit den Links |
| `design/standard/templates/user/registrationfeedback.tpl` | der Inhalt der E-Mail |
| `design/standard/templates/mail/layout.tpl`, `mail/layout_text.tpl` | das E-Mail-Layout (HTML und Text) |
| `design/standard/templates/user/registration.tpl` | die Seite |
| `settings/site.ini [UserSettings]` | die vier Einstellungen |

## Verwandte Seiten

- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md)
- [E-Mail-Einstellungen](e-mail-einstellungen.md) (das Site-Geheimnis)
- [Prüfprotokoll](pruefprotokoll.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
