---
title: Registrierung von Mitgliedern
audience: businesses, website-creator, admin
category: business-solutions
order: 20
summary: Wie Menschen Mitglieder einer Exponential-Mitgliederwebsite werden - Registrierung öffnen, E-Mail-Bestätigung, wo neue Mitglieder abgelegt werden, Freigabe durch die Organisation, von der Geschäftsstelle angelegte Konten, Anmeldung, Zurücksetzen von Passwörtern, Spamschutz und eine saubere Mitgliederliste.
keywords: Registrierung von Mitgliedern, beitreten, registrieren, user/register, VerifyUserType, DefaultUserPlacement, Gruppe Mitglieder, Freigabe, Nicht aktivierte Benutzer, Anmeldung, Passwort zurücksetzen, CAPTCHA, Benutzerkonten prüfen, Mitgliederliste, member registration, join, sign up, members group, approval, unactivated users, login, password reset, check user accounts, member list
type: help
---
# Registrierung von Mitgliedern

Diese Seite ist für die Menschen, die eine Mitgliederorganisation führen, und für die Person, die die Website
einrichtet. Sie erklärt, wie jemand auf der Website Mitglied wird, was die Person erlebt, was die Organisation steuert
und was die Mitgliederliste sauber hält. Die technische Seite ist
[Mitgliederregistrierung und Bereiche nur für Mitglieder](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md).

## Inhalt

1. [Was ein neues Mitglied erlebt](#was-ein-neues-mitglied-erlebt)
2. [Die Registrierung öffnen](#die-registrierung-öffnen)
3. [Wo neue Mitglieder landen](#wo-neue-mitglieder-landen)
4. [Wenn die Organisation freigeben muss](#wenn-die-organisation-freigeben-muss)
5. [Von der Geschäftsstelle angelegte Konten](#von-der-geschäftsstelle-angelegte-konten)
6. [Anmelden und vergessene Passwörter](#anmelden-und-vergessene-passwörter)
7. [Bots fernhalten](#bots-fernhalten)
8. [Die Mitgliederliste sauber halten](#die-mitgliederliste-sauber-halten)
9. [Checkliste](#checkliste)
10. [Verwandte Seiten](#verwandte-seiten)

## Was ein neues Mitglied erlebt

1. Die Person öffnet **Register** (`user/register`) und füllt das Formular aus: Name, Login, E-Mail-Adresse, Passwort.
2. Die Website zeigt eine Bestätigungsseite (`user/success`) und schickt eine Aktivierungsmail.
3. Die Person klickt auf den Link in der Mail (`user/activate`); das Konto funktioniert.
4. Wenn die Organisation Mitglieder freigibt, bekommt die Person eine Mail „Registrierung freigegeben“, sobald jemand
   sie freigegeben hat.
5. Die Person meldet sich an (`user/login`) und bearbeitet ihr eigenes Konto (`user/edit`).

Jede Seite ist ein Template, das das Design der Website überschreibt, damit sie wie die Website aussieht
([The pages involved](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md#the-pages-involved)). Die
Passwortregeln gelten auch hier: Eine Mindestlänge von 10 und eine Prüfung auf schwache Passwörter sind ausgeliefert
(`settings/site.ini [UserSettings]`), und weitere Regeln lassen sich einschalten
([Ihr Passwort ändern](../../../../eng-us/features/6.0/modern-password-change.md)).

## Die Registrierung öffnen

In einer neuen Installation braucht die Registrierung die Richtlinie `user/register`. Für eine offene Registrierung
fügen Sie *User / Register* der Rolle Anonymous hinzu. Die Anmeldeseiten zeigen einen Link *Register* oder *Sign Up*
nur Besuchern, die sich registrieren dürfen; eine Website ohne offene Registrierung zeigt also keinen toten Link
([Rollen und Richtlinien, 8](../../../anleitungen/rollen-und-richtlinien.md#8-festlegen-wer-sich-registrieren-und-wer-knotendaten-lesen-darf)).

## Wo neue Mitglieder landen

`site.ini [UserSettings] DefaultUserPlacement` ist der Knoten der Benutzergruppe, in die neue Registrierungen kommen
(ausgeliefert `12`). Legen Sie eine Gruppe *Mitglieder* an, lassen Sie die Einstellung im öffentlichen Siteaccess darauf
zeigen und geben Sie dieser Gruppe eine Rolle *Mitglied* mit `content/read` für die Mitgliedersektion und `user/login`
für den öffentlichen Siteaccess, und sonst nichts
([A members role](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md#a-members-role)).

| Einstellung (`[UserSettings]`) | Empfohlen für eine Mitgliederwebsite |
|---|---|
| `DefaultUserPlacement` | die Gruppe Mitglieder (oder eine Gruppe Bewerber, wenn Sie freigeben) |
| `VerifyUserType` | `email` (ausgeliefert): Das Konto wird über einen Link aktiviert |
| `RegistrationEmail` | das Postfach der Person, die sich um Mitglieder kümmert |
| `PasswordInRegistrationEmail` | `disabled` (ausgeliefert): Passwörter werden nie gemailt |

## Wenn die Organisation freigeben muss

Manche Organisationen nehmen Mitglieder erst nach einer Prüfung auf. Exponential unterstützt das auf zwei Wegen
([Registrierung und Kontofreigabe](../extranets/registrierung-und-kontofreigabe.md)):

- **Nachträglich prüfen.** Die Geschäftsstelle bekommt die Mail „Neuer Benutzer registriert“ mit signierten Links
  **View**, **Activate** und **Reject and remove** für jede Registrierung. Die Links laufen standardmäßig nach 7 Tagen
  ab und geben nur einmal frei oder lehnen nur einmal ab; das Öffnen eines Links ändert nichts, bis die angemeldete
  Person der Geschäftsstelle bestätigt
  ([Signierte Links in der Mail „New user registered“](../../../../eng-us/features/6.0/registration-admin-links.md)).
- **Bis zur Freigabe zurückhalten.** Mit einem Freigabe-Workflow wartet das Konto auf ein Freigabeelement; die
  Geschäftsstelle gibt es aus der Mail oder dem Posteingang der Zusammenarbeit frei oder lehnt es ab, und der
  Workflow-Cronjob muss laufen.

Ein praktisches Muster: Registrierungen landen in einer Gruppe *Bewerber* ohne Mitgliederrechte; nach der Freigabe fügt
die Geschäftsstelle das Konto der Gruppe *Mitglieder* hinzu.

## Von der Geschäftsstelle angelegte Konten

Wenn Mitglieder auf Papier oder über ein anderes Mitgliedersystem beitreten, legt die Geschäftsstelle die Konten in der
Administration unter der Gruppe Mitglieder an. Die Mitglieder setzen dann ihr eigenes Passwort mit **Forgot your
password?**, dessen Schlüssel ablaufen ([Das Passwort eines Benutzers zurücksetzen](../../../../eng-us/features/6.0/reset-user-password.md)).
Für größere Zahlen ist ein Import durch einen Entwickler möglich
([Inhalte im Code anlegen](../../../../eng-us/help/creators/app-creators/creating-content-in-code.md)).

## Anmelden und vergessene Passwörter

- Anmelden, Abmelden, die Kontoseite und das Ändern des Passworts sind für Mitglieder unter
  [Ihr Konto und die Anmeldung](../../konten-und-anmeldung.md) beschrieben.
- Die Seite für vergessene Passwörter verrät nicht, ob zu einer Adresse ein Konto gehört (ezwebin 6.0.7,
  [ezwebin](../../../../eng-us/features/6.0/extensions/ezwebin.md)).
- Die zweistufige Anmeldung kann Mitgliedern als Option angeboten werden
  ([Zwei-Faktor und Anmeldung](../intranets/zwei-faktor-und-anmeldung.md#zweistufige-anmeldung)).

## Bots fernhalten

Registrierungsformulare ziehen automatische Anmeldungen an. Fügen Sie der Benutzerklasse ein CAPTCHA-Feld (reCAPTCHA
oder hCaptcha) hinzu und lassen Sie die E-Mail-Bestätigung eingeschaltet
([Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md),
[hcaptcha](../../../../eng-us/features/6.0/extensions/hcaptcha.md)).

## Die Mitgliederliste sauber halten

| Aufgabe | Wo |
|---|---|
| Nie bestätigte Registrierungen | **Benutzer > Nicht aktivierte Benutzer**: aktivieren, erneut senden, entfernen; älter als 30 Tage mit *Old* gekennzeichnet ([Rollen und Richtlinien, 7](../../../anleitungen/rollen-und-richtlinien.md#7-nicht-aktivierte-benutzer-aufräumen)) |
| Mitglieder, die sich nie oder lange nicht angemeldet haben | **Benutzer > Benutzerkonten prüfen**, Filter *Nie angemeldet* und *Not seen for N days* ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)) |
| Eine Person mit zwei Konten | der Filter *Doppelte E-Mail-Adresse* |
| Mitglieder, die gegangen sind | **Deaktivieren** oder, wenn eingeschaltet, **Deaktivieren und verschieben** |

## Checkliste

- [ ] Entschieden, ob die Registrierung offen oder geschlossen ist.
- [ ] Gruppe Mitglieder und Rolle Mitglied mit minimalen Rechten.
- [ ] `DefaultUserPlacement` im öffentlichen Siteaccess gesetzt.
- [ ] Seiten für Registrierung, Aktivierung und Passwort im Stil der Website gestaltet.
- [ ] CAPTCHA in der Benutzerklasse; E-Mail-Bestätigung eingeschaltet.
- [ ] Freigabeverfahren entschieden und getestet, wenn Mitglieder geprüft werden.
- [ ] Das Registrierungsformular sagt, welche Daten aufbewahrt werden und warum
      ([Einwilligung und Formulare](../sicherheit/einwilligung-und-formulare.md)).
- [ ] Eine Routine zum Bereinigen nicht aktivierter und ungenutzter Konten.

## Verwandte Seiten

- [Mitgliederregistrierung und Bereiche nur für Mitglieder](../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md)
- [Inhalte nur für Mitglieder](inhalte-nur-fuer-mitglieder.md)
- [Registrierung und Kontofreigabe](../extranets/registrierung-und-kontofreigabe.md)
- [Benutzerkonten und Passwörter](../sicherheit/benutzerkonten-und-passwoerter.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
