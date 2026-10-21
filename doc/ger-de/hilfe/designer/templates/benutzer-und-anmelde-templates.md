---
title: Benutzer- und Anmelde-Templates
audience: designer, developer
category: templates
order: 710
summary: Die Seiten des Moduls user für Anmeldung, Registrierung, Passwort und Profil gestalten - die Templates in user/, die Feldnamen, die das Modul liest, autocomplete und Barrierefreiheit, der angemeldete Zustand im Pagelayout und benutzerspezifische Ausgabe aus Caches heraushalten.
keywords: Anmelde-Template, user/login.tpl, register.tpl, forgotpassword.tpl, password.tpl, Benutzer bearbeiten, Anmeldeseite, Login, Password, RedirectURI, autocomplete, current_user, angemeldet, login template, user edit, sign in page, signed in
type: help
---
# Benutzer- und Anmelde-Templates

Diese Seite ist für Designer, die die Kontoseiten einer öffentlichen Website gestalten: Anmelden, Registrieren, Passwort
vergessen, Passwort ändern, Profil. Sie gehören zum Modul `user`, dessen Templates in `user/` des Designs liegen.

## Inhalt

1. [Die Templates](#die-templates)
2. [Feldnamen, die bleiben müssen](#feldnamen-die-bleiben-müssen)
3. [Barrierefrei und freundlich für Passwortmanager](#barrierefrei-und-freundlich-für-passwortmanager)
4. [Der angemeldete Zustand im Rahmen](#der-angemeldete-zustand-im-rahmen)
5. [Caching](#caching)

## Die Templates

`design/standard/templates/user/`: `login.tpl`, `register.tpl`, `success.tpl`, `activate.tpl`, `forgotpassword.tpl`,
`password.tpl`, `edit.tpl`, `setting.tpl` und die Mail-Templates (`forgotpasswordmail.tpl`, `password_changed_mail.tpl`,
`registrationinfo.tpl`). Kopieren Sie die, die Sie umgestalten, in Ihr Design. Das Design media hat eigene Fassungen (laut
Kommentar zeichnet sein Stylesheet `account.css` sie).

## Feldnamen, die bleiben müssen

Das Modul liest die gesendeten Felder nach Namen. In `user/login.tpl`: `Login`, `Password`, `LoginButton`, `RedirectURI`,
`Cookie` (angemeldet bleiben) und `RegisterButton`; das Formular sendet an `/user/login`. Behalten Sie die Namen und die
versteckten Eingaben; ändern Sie nur Markup und Klassen. Registrierungs- und Profilformulare zeichnen die Felder der
Benutzerklasse mit `attribute_edit_gui` ([Bearbeitungstemplates von Datentypen](datentyp-bearbeitungstemplates.md)).

## Barrierefrei und freundlich für Passwortmanager

```tpl
<label for="login-name">{'Username or email'|i18n( 'design/mytheme/user' )}</label>
<input id="login-name" name="Login" type="text" autocomplete="username" required>
<label for="login-password">{'Password'|i18n( 'design/mytheme/user' )}</label>
<input id="login-password" name="Password" type="password" autocomplete="current-password" required>
```

Verwenden Sie auf der Seite zum Ändern des Passworts `autocomplete="new-password"`. Zeigen Sie Fehler neben den Feldern und in
einer Zusammenfassung ([Barrierefreie Formulare](../theme-design/barrierefreie-formulare.md)). Die Seite „Passwort vergessen“ darf nicht
verraten, ob zu einer Adresse ein Konto existiert; die Templates des Kernels wurden dafür geändert, behalten Sie ihre
Formulierung.

## Der angemeldete Zustand im Rahmen

Im Pagelayout sagt `$current_user.is_logged_in`, ob ein Besucher angemeldet ist; `$current_user.contentobject.name` ist sein
Name. Verlinken Sie `/user/login`, `/user/logout` und `/user/edit` mit `ezurl` ([Pagelayout-Variablen](../theme-design/pagelayout-variablen.md)).

## Caching

Alles, was den Namen oder Zustand des Benutzers zeigt, muss außerhalb des View-Cache liegen (das Pagelayout wird nicht im
View-Cache gespeichert) und in einem Cache-Block nach dem Benutzer verschlüsselt sein (`$current_user.contentobject_id`)
([Cache-Block](cache-block.md)).

## Verwandte Seiten

- [Formulare in Templates](formulare-in-templates.md)
- [Rollen- und Richtlinienoperatoren](rollen-und-richtlinien-operatoren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
