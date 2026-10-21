---
title: Registrierung von Mitgliedern und Bereiche nur für Mitglieder
audience: website-creator, admin
category: website-creation
order: 580
summary: Besucher sich registrieren und anmelden lassen - user/register, E-Mail-Bestätigung, wo neue Benutzer abgelegt werden, die Rolle für Mitglieder, Seiten für Anmeldung und Zurücksetzen des Passworts, eine Sektion nur für Mitglieder sowie Datenschutz und Spam.
keywords: Registrierung, Mitgliederbereich, Anmeldung, Passwort zurücksetzen, Passwort vergessen, Mitgliederrolle, Zwei-Faktor, Website-Ersteller, registration, user/register, members area, login, VerifyUserType, DefaultUserPlacement, RegistrationEmail, password reset, forgot password, member role, two-factor, website creator
type: help
---
# Registrierung von Mitgliedern und Bereiche nur für Mitglieder

Diese Seite richtet sich an Website-Ersteller, die Besuchern Konten geben: einen Mitgliederbereich, ein Intranet, ein
Kundenportal oder einfach Kommentare unter einem Namen.

## Inhalt

1. [Die beteiligten Seiten](#die-beteiligten-seiten)
2. [Einstellungen für die Registrierung](#einstellungen-für-die-registrierung)
3. [Eine Rolle für Mitglieder](#eine-rolle-für-mitglieder)
4. [Ein Bereich nur für Mitglieder](#ein-bereich-nur-für-mitglieder)
5. [Passwörter und Anmeldesicherheit](#passwörter-und-anmeldesicherheit)
6. [Checkliste](#checkliste)

## Die beteiligten Seiten

| Seite | Ansicht | Für Besucher geöffnet durch |
|---|---|---|
| Registrieren | `user/register` | `site.ini [SiteAccessSettings] AnonymousAccessList[]` |
| Registrierung abgeschlossen | `user/success` | dasselbe |
| Das Konto aktivieren (Link in der Mail) | `user/activate` | dasselbe |
| Passwort vergessen | `user/forgotpassword` | dasselbe |
| Anmelden, abmelden | `user/login`, `user/logout` | immer |
| Eigenes Konto bearbeiten | `user/edit` | angemeldet |

Überschreiben Sie ihre Templates (`user/register.tpl`, `user/login.tpl` ...) in Ihrem Design, damit sie wie die Website
aussehen.

## Einstellungen für die Registrierung

`site.ini [UserSettings]`:

| Schlüssel | Bedeutung |
|---|---|
| `DefaultUserPlacement` | der Knoten der Benutzergruppe, in die neue Benutzer abgelegt werden (mitgeliefert `12`) |
| `VerifyUserType` | `email`: das Konto wird über einen Link in einer Mail aktiviert |
| `RegistrationEmail` | wer über neue Registrierungen informiert wird |
| `PasswordInRegistrationEmail` | `disabled`: das Passwort wird nie per Mail verschickt |
| `MinPasswordLength`, `WeakPasswordCheck` | Passwortregeln ([Moderne Passwortänderung](../../../funktionen/6.0/moderne-passwortaenderung.md)) |

Legen Sie eine Benutzergruppe „Members“ an und setzen Sie `DefaultUserPlacement` im öffentlichen Siteaccess auf deren
Knoten, damit Registrierungen nie in einer Gruppe der Redaktion landen.

## Eine Rolle für Mitglieder

Legen Sie eine Rolle „Member“ mit `content/read` für die Mitgliedersektion und `user/login` für den öffentlichen
Siteaccess an und weisen Sie sie der Gruppe Members zu ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).
Geben Sie ihr nichts weiter.

## Ein Bereich nur für Mitglieder

Weisen Sie den Ordner der Mitglieder einer eigenen Sektion zu, die Anonymous nicht lesen darf
([Sektionen und Objektzustände für Website-Ersteller](sektionen-und-objektzustaende.md#rezept-ein-mitgliederbereich)). Besucher,
die ihn öffnen, werden zur Anmeldung aufgefordert. Stattdessen kann auch ein ganzer Siteaccess eine Anmeldung verlangen
(`[SiteAccessSettings] RequireUserLogin=true` in seinen Einstellungen), was zu einem Intranet passt.

## Passwörter und Anmeldesicherheit

- Das Zurücksetzen von Passwörtern ist eingebaut, und seine Schlüssel laufen ab
  ([Benutzerpasswort zurücksetzen](../../../funktionen/6.0/benutzerpasswort-zuruecksetzen.md)).
- Eine zweistufige Anmeldung für Mitglieder ist verfügbar ([Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).
- Sitzungen und Abmeldungen: [Sitzungen](../../../anleitungen/sitzungen.md).

## Checkliste

- [ ] Die Registrierung legt Benutzer nur in der Gruppe Members ab.
- [ ] E-Mail-Bestätigung an; CAPTCHA an der Benutzerklasse ([Spamschutz für Formulare](spamschutz-fuer-formulare.md)).
- [ ] Die Rolle Member gewährt nur, was Mitglieder brauchen.
- [ ] Das Registrierungsformular sagt, welche Daten gespeichert werden und warum.
- [ ] Inaktive und nicht bestätigte Konten werden aufgeräumt
  ([Pflege der Benutzerkonten](../../../funktionen/6.0/benutzerpflege.md)).

## Verwandte Seiten

- [Rechte für Besucher](rechte-fuer-besucher.md)
- [Konten und Anmeldung](../../konten-und-anmeldung.md)
- [Sektionen und Objektzustände für Website-Ersteller](sektionen-und-objektzustaende.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
