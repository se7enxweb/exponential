# Hinweise zum Upgrade: E-Mail-Einstellungen und die Mail-Schranke

Was sich ändert, wenn eine Installation auf ein 6.0.15 mit den E-Mail-Einstellungen wechselt: die neuen Tabellen, die
Mail-Schranke, die jetzt jede E-Mail passiert, die E-Mail, die pro Empfänger aufgeteilt wird, die E-Mail, die als ohne
Kategorie gezählt wird, und wie Sie das alte Verhalten dort behalten, wo Sie es brauchen. Lesen Sie die Seite, wenn Sie
aktualisieren, wenn Ihr Code oder Ihre Erweiterung E-Mails sendet oder wenn Ihr Design das Registrierungsformular
überschreibt. Das System, wie es jetzt ist: [die Anleitung für Benutzer](../../../eng-us/features/6.0/mail-preferences.md),
[die Anleitung für Administratoren](../../anleitungen/mail-einstellungen-administrator.md),
[die Spezifikation](../../spezifikationen/6.0/mail-einstellungen.md).

## Was Sie tun müssen

1. **Führen Sie das Datenbank-Update** der Linie aus, `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`. Es legt
   `expmail_category`, `expmail_preference`, `expmail_consent_log`, `expmail_suppression` und `expmail_pending` an.
   Prüfen Sie mit `./console exp:mail:status`: fünfmal `ok`.
2. **Füllen Sie die Fußzeile aus**, in einem Override der Einstellungen: `mailpreferences.ini [FooterSettings]
   OrganisationName` und `OrganisationAddress` (Abschnitt 2.2 der Anleitung für Administratoren). Ohne sie warnt die
   Statusseite, und optionale E-Mails gehen ohne Postanschrift hinaus.
3. **Prüfen Sie, wohin Links zeigen**: `[LinkSettings] BaseURL`; leer bedeutet `https://` + `SiteURL` des
   Standard-Siteaccess.
4. **Halten Sie das Geheimnis der Website geheim.** Es wird bei der ersten Verwendung in
   `settings/override/mailpreferences.ini.append.php` erzeugt. Kopieren Sie diese Datei bei jedem Umzug der
   Installation mit, und committen Sie sie nie.
5. **Geben Sie Rollen die neuen Richtlinien**, wo nötig: `mailpreferences/administrate`, `mailpreferences/export`. Die
   eigenen Seiten der Person brauchen keine.
6. Leeren Sie die Caches (`php bin/php/ezcache.php --clear-all --allow-root-user`), laden Sie PHP-FPM neu oder starten
   Sie Velocity neu.
7. Öffnen Sie **Setup > E-Mail-Einstellungen** und lesen Sie die Liste der Probleme. Sehen Sie sich dann die E-Mails an,
   die Ihr eigener Code sendet (Abschnitt „E-Mails ohne Kategorie“).

## Verhaltensänderungen

### Jede E-Mail passiert die Mail-Schranke

`eZMailTransport::send()` übergibt jede E-Mail vor dem Transport an `expMailGate::dispatch()`, für jeden Transport
(sendmail, SMTP, Datei und Transporte von Erweiterungen). Was die Schranke tut, hängt von der Kategorie ab, die die
E-Mail mit `eZMail::setCategory()` oder dem Header `X-Exp-Mail-Category` angibt:

| E-Mail | Vorher | Jetzt |
|---|---|---|
| Ohne Kategorie | gesendet | unverändert gesendet, und eine Zeile in `var/<vardir>/log/mailgate.jsonl` |
| Unverzichtbare Kategorie (`security`, `orders`, `legal`, `admin`) | gesendet | gesendet, mit dem Header `X-Exp-Mail-Category` |
| Optionale Kategorie | an alle darin gesendet | nur an die Empfänger gesendet, die die Kategorie eingeschaltet haben; mit Fußzeile und Headern `List-Unsubscribe` |

`eZMailTransport::send()` gibt für eine optionale E-Mail, deren Empfänger alle blockiert wurden, `true` zurück, als
wäre sie gesendet worden: Der Aufrufer hat nichts zu wiederholen. `expMailGate::lastResult()` sagt, was geschehen ist.

### Optionale E-Mails an mehrere Empfänger werden aufgeteilt

Eine optionale E-Mail mit mehreren Empfängern wird als eine E-Mail pro erlaubtem Empfänger gesendet, sodass jede ihren
eigenen Abmeldelink trägt und niemand die anderen Adressen sieht. Ein Empfänger behält sein Feld (To, Cc oder Bcc).
Nach dem Senden hat das `eZMail`-Objekt seine Empfänger und seinen Body zurück. Ein Log des Mailservers zeigt mehrere
Nachrichten, wo es eine zeigte. Um eine E-Mail für alle zu behalten, setzen Sie `[GateSettings] SplitRecipients=disabled`;
eine solche E-Mail erhält eine Fußzeile, die auf die Seite „Link zusenden“ verweist, und keinen Header
`List-Unsubscribe`, den große E-Mail-Anbieter von Massenversendern erwarten.

### Optionale E-Mails erhalten eine Fußzeile

Der Body optionaler E-Mails endet mit der Fußzeile aus `design:mailpreferences/mail/footer.tpl`: warum die Person sie
erhält, der Abmeldelink mit einem Klick, der Link zu den Einstellungen und die Organisation mit ihrer Postanschrift. In
einer HTML-E-Mail steht sie vor `</body>`. Code, der seine eigenen gesendeten E-Mails auswertet, oder Tests, die einen
ganzen Body vergleichen, sehen die Fußzeile.

### E-Mails ohne Kategorie werden gezählt

E-Mails ohne Kategorie werden nicht blockiert, aber die Statusseite und `exp:mail:status` zählen sie („128 E-Mails ohne
Kategorie in den letzten 7 Tagen“) und nennen die Datei, die sie gesendet hat, sodass sie eine Kategorie erhalten
können. Absender, die in `[GateSettings] EssentialSenders[]` aufgeführt sind (eine From-Adresse, eine Datei relativ zur
Installation oder eine Klasse im Aufrufstapel), werden stattdessen als unverzichtbar gezählt. Die ausgelieferte Liste
nennt die Absender des Kernels für Konten, Bestellungen, Audit und Setup.

### Das Registrierungsformular hat nicht angekreuzte Kästchen

`user/register` zeigt **E-Mails von uns (optional)** mit einem nicht angekreuzten Kästchen pro optionaler Kategorie
(`design:mailpreferences/parts/signup.tpl`, Variable `$mail_categories`), in `design/standard`, `design/admin` und
`design/admin4`. Ein Design, das `user/register.tpl` überschreibt, zeigt keine Kästchen, bis es den Teil einbindet:

```
{include uri='design:mailpreferences/parts/signup.tpl' categories=first_set( $mail_categories, array() )}
```

Innerhalb des Formulars, vor den Schaltflächen. Was ein neuer Benutzer ankreuzt, wird nach der Registrierung
gespeichert.

### Neue Links in den Kontoseiten und Menüs

Das Profil (`user/edit`), die Einstellungen der Benachrichtigungen und der Kasten des aktuellen Benutzers verweisen
auf die Seite der Einstellungen; das Menü `Mein Konto` hat **Meine E-Mail-Einstellungen** (`menu.ini Links[my_mail]`),
das Menü Setup **E-Mail-Einstellungen** (`Links[mailpreferences]`, Richtlinie `mailpreferences/administrate`). Designs,
die diese Templates überschreiben, zeigen die Links erst, wenn sie `design:mailpreferences/parts/account_link.tpl`
hinzufügen.

### Benachrichtigungen und andere E-Mails des Kernels

Die Benachrichtigungskategorien `content` und `collaboration` lesen die vorhandenen Regeln für Teilbäume und
Zusammenarbeit sowie die Einstellungen der Zusammenfassung (`expNotificationMailCategoryHandler`): Ein Benutzer, der
Elementen folgt, ist für Inhaltsbenachrichtigungen „ein“, nichts wird migriert, und die alten Seiten für
Benachrichtigungen funktionieren weiter. Ein Benutzer, der die Kategorie oder alle optionalen E-Mails ausschaltet,
erhält keine Benachrichtigungs-E-Mails; seine Regeln bleiben erhalten und wirken wieder, wenn er sie einschaltet.

E-Mails zu Inhalten und Zusammenarbeit folgen der Häufigkeit, die die Person auf der Seite der Einstellungen gewählt
hat: täglich oder wöchentlich warten die Elemente auf die allgemeine Zusammenfassung, die für jede einen Teil hat
(`notification/handler/ezcollaboration/view/digest_plain.tpl` ist neu). Das Template der Zusammenfassung
`ezgeneraldigest/view/plain.tpl` wurde neu geschrieben, weil es überhaupt keine Elemente renderte; ein Design, das es
überschreibt, sollte dieselbe Änderung übernehmen (ein `foreach` und literale Includes).

### Eine neue E-Mail-Adresse wartet auf ihre Bestätigung

Ändert ein Benutzer die E-Mail-Adresse seines Kontos (user/edit, die Bearbeitung eines Benutzers in der
Administration, die Kontodienste `changeEmail`), behält das Konto seine Adresse, bis die neue mit dem an sie gesendeten
Link bestätigt ist; die alte Adresse erhält einen Hinweis. Die Seite der Einstellungen zeigt die wartende Änderung.
Importe und Skripte, die das Konto mit `fromString()` (`login|email|...`) setzen, ändern sie wie bisher sofort.

### Opt-out-Kategorien werden abgelehnt

Eine optionale Kategorie mit `DefaultOn=true` (in den Einstellungen, den Einstellungen einer Erweiterung, einer Zeile
eines Administrators oder im Code) gilt für alle, die nie gewählt haben, als aus, und die Statusseite und
`exp:mail:status` warnen mit ihrem Bezeichner. Eine Website, deren Empfänger einem Gesetz unterliegen, das für diese
E-Mails Opt-out erlaubt, setzt `mailpreferences.ini [CategorySettings] AllowDefaultOn=enabled`; die Statusseite
vermerkt dann, dass Opt-out erlaubt ist.

### Das Entfernen eines Kontos löscht seine E-Mail-Einstellungen

`eZUser::removeUser()` (aufgerufen, wenn ein Benutzerobjekt endgültig gelöscht wird) ruft
`expMailPreferences::eraseForRemovedAccount()` auf: Die Einstellungen und ausstehenden Bestätigungen des Kontos werden
entfernt, sein Einwilligungsprotokoll wird anonymisiert (Adresse, IP und Benutzer-ID entfernt; die Zeilen bleiben als
Nachweis jeder Einwilligung und jedes Widerrufs), und eine Zeile `erase` wird hinzugefügt. Die Sperrliste wird nicht
angetastet: Sie enthält nur gesalzene Hashes und blockiert die Adresse weiterhin. Ein Konto, das nie eine
E-Mail-Einstellung hatte, hinterlässt nichts. Code, der nach einem Entfernen Zeilen des Einwilligungsprotokolls nach
Benutzer-ID zählte, findet keine.

### Optionale E-Mails haben immer einen Absender

Optionale E-Mails mit leerem From erhalten `site.ini [MailSettings] EmailSender`, sonst `AdminEmail`; das Log der
Schranke verzeichnet `from_fallback` mit der Datei, die sie gesendet hat, und die Statusseite zählt es. Ohne eine der
beiden Einstellungen wird die E-Mail nicht gesendet (`expMailGate::lastResult()`: blockiert, `no_sender`).
Unverzichtbare E-Mails und E-Mails ohne Kategorie sind unverändert (die Transporte füllen einen leeren Absender wie
bisher).

### Der öffentliche Name der Website, der Datenschutzhinweis und die Gültigkeit der Links

- Die Fußzeile und die E-Mail-Templates erhalten `$site_name` (und `$privacy_url`) von
  `expMailPreferencesService::renderTemplate()`: `OrganisationName`, wenn gesetzt, sonst den SiteName des öffentlichen
  Siteaccess. Ein Befehl oder ein Cronjob, der mit dem Siteaccess der Administration läuft, schreibt nicht mehr
  „Admin“ in die Fußzeile. Ein überschriebenes E-Mail-Template, das `ezini( 'SiteSettings', 'SiteName' )` liest, sollte
  stattdessen `$site_name` verwenden.
- `[FooterSettings] PrivacyURL` (neu, leer) verlinkt den Datenschutzhinweis aus der Fußzeile optionaler E-Mails und aus
  der Seite der Einstellungen. Leer: der Knoten von `menu.ini [SiteInfo] PrivacyPolicyID` des öffentlichen Siteaccess,
  wenn es ihn gibt, sonst kein Link, und die Statusseite sagt das.
- `[TokenSettings] TTL[unsubscribe]` und `TTL[manage]` zwischen 0 und 60 Tagen werden auf 60 Tage (5184000 s)
  angehoben, mit einer Warnung auf der Statusseite. 0 bedeutet weiterhin „läuft nie ab“.

### Was bewusst der Website überlassen bleibt

- **E-Mails ohne Kategorie** werden ohne Fußzeile und Abmeldelink gesendet, wie vor der Schranke, sodass vorhandener
  Code weiter funktioniert. Kommerzielle E-Mails, die so gesendet werden, sind nicht konform; die Statusseite zählt sie
  und nennt ihren Absender. Geben Sie solchen E-Mails eine Kategorie.
- **`SplitRecipients=disabled`** sendet eine E-Mail an alle Empfänger: Sie kann keinen persönlichen Header
  `List-Unsubscribe` tragen, und ihre Fußzeile verweist auf „Link zusenden“ statt auf einen Link mit einem Klick.
  Große E-Mail-Anbieter erwarten von Massenversendern das Abmelden mit einem Klick. Behalten Sie `enabled`.
- **DKIM** wird vom Mailserver signiert, nicht von Exponential. Signieren Sie `List-Unsubscribe` und
  `List-Unsubscribe-Post`.

## Wie Sie das alte Verhalten behalten

| Sie möchten | Tun Sie |
|---|---|
| Kein Blockieren nach Einstellungen | `[GateSettings] Gate=disabled`. Optionale E-Mails gehen an alle außer an Adressen auf der Sperrliste, weiterhin mit Fußzeile und Headern; die Statusseite sagt, dass die Schranke ausgeschaltet ist |
| Eine E-Mail für alle Empfänger | `[GateSettings] SplitRecipients=disabled` |
| Eine neue E-Mail-Adresse sofort, ohne Bestätigung | `[EmailChangeSettings] Confirm=disabled` (und `NotifyOldAddress=disabled` für keinen Hinweis) |
| Eine E-Mail Ihres Codes unberührt | Geben Sie ihr keine Kategorie. Sie wird genau wie bisher gesendet und nur gezählt; führen Sie ihre Datei oder Klasse in `EssentialSenders[]` auf, wenn sie unverzichtbar ist |
| Eine optionale Kategorie standardmäßig ein (Opt-out) | `[CategorySettings] AllowDefaultOn=enabled`, nur wo das Gesetz es erlaubt |
| Ihre eigene Fußzeile | Überschreiben Sie `mailpreferences/mail/footer.tpl` (und `footer_html.tpl`) in Ihrem Design und behalten Sie den Abmeldelink, den Link zur Verwaltung und die Anschrift |

Das Ausschalten der Schranke macht eine Website nicht konform mit den Gesetzen für E-Mails; lesen Sie zuerst
[die Checkliste zu den Gesetzen](../../spezifikationen/6.0/mail-einstellungen-compliance.md).

## Für Entwickler

- `eZMail::setCategory( $identifier )` und `eZMail::category()` sind neu; `setCategory()` schreibt außerdem den Header
  `X-Exp-Mail-Category`.
- Eine View, die mit einem eigenen Body antwortet (ein Download, eine einfache Antwort, ein Event-Stream), leert die
  Ausgabepuffer mit `eZExecution::discardOutputBuffers()`, nie mit `while ( ob_get_level() > 0 ) ob_end_clean();`:
  Unter Velocity hält der Worker einen Puffer, der sich nicht entfernen lässt, und diese Schleife endet nie (die
  Anfrage antwortet mit 504). `MailPreferencesPage::sendResponse()` erledigt die ganze Antwort für die Views des Moduls.
  Velocity sendet eine Antwort, wenn das Skript endet, daher kommt ein Event-Stream dort vollständig, aber auf einmal
  an; unter PHP-FPM wird er gestreamt.
- Die Kategorie einer Erweiterung wird in deren `settings/mailpreferences.ini.append.php` deklariert
  ([die Anleitung für Entwickler](../../anleitungen/mail-einstellungen-entwickler.md)).
- Tests, die E-Mails senden, müssen im eigenen Prozess den Datei-Transport erzwingen und Adressen mit `.invalid`
  verwenden; das Log der Schranke lässt sich mit `expMailGate::setLogFileForTest()` in eine Datei des Tests leiten.
- Der Bounce-Leser gibt jede gelesene Nachricht an die Klassen von `mailpreferences.ini [BounceSettings]
  MessageListeners[]` (statisch `mailMessage( $raw, $classification, $dryRun )`, `true`, wenn die Nachricht für den
  Listener war), nach seiner eigenen Klassifizierung und Sperrung. Ein Listener, der fehlschlägt, wird protokolliert,
  und das Lesen geht weiter; in einem Probelauf darf er nichts ändern. `AfterRead=delete` löscht auch die Nachrichten,
  die ein Listener übernommen hat. Die Liste ist standardmäßig leer; cjw_newsletter 4.2.0 fügt `CjwNewsletterMailin`
  hinzu (Wiederholungen bei Soft-Bounces, An- und Abmelden per E-Mail).

## Verwandte Seiten

- [Der Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Die Anleitung für Administratoren](../../anleitungen/mail-einstellungen-administrator.md)
- [Die Anleitung für Entwickler](../../anleitungen/mail-einstellungen-entwickler.md)
- [Die Spezifikation](../../spezifikationen/6.0/mail-einstellungen.md), [die Befehle](../../spezifikationen/6.0/mail-einstellungen-cli.md)
- [Die Checkliste zu den Gesetzen](../../spezifikationen/6.0/mail-einstellungen-compliance.md)
- [Hinweise zum Upgrade: Benachrichtigungen](benachrichtigungen-oberflaeche-und-befehle.md)
