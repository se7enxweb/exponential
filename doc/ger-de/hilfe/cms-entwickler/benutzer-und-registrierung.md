---
title: Benutzer und Registrierung
audience: cms-developer
category: cms-development
order: 20
summary: Für CMS-Entwickler, die Mitgliederbereiche und Registrierung bauen - Benutzer als Inhaltsobjekte, Benutzergruppen, Selbstregistrierung und ihre Einstellungen, Aktivierung per E-Mail oder durch einen Administrator, Passwörter, Login-Handler, Zwei-Faktor-Anmeldung und die Templates des Moduls user.
keywords: Benutzer, Benutzerkonten, Benutzerklasse, Benutzergruppe, Registrierung, Selbstregistrierung, DefaultUserPlacement, VerifyUserType, Aktivierung, nicht aktivierte Benutzer, Passwortregeln, MinPasswordLength, WeakPasswordCheck, Login-Handler, LDAP, Zwei-Faktor, Mitgliederbereich, Profil, users, user accounts, registration, sign up, self registration, activation, password rules, login handler, two-factor, member area
type: help
---
# Benutzer und Registrierung

Diese Seite richtet sich an Entwickler, die Websites bauen, auf denen Besucher sich registrieren, anmelden und ein
Profil haben. In Exponential ist ein Benutzerkonto ein Inhaltsobjekt; ein Profil zu erweitern ist also dieselbe Arbeit
wie jede Klasse zu erweitern, und der Zugriff folgt demselben Rollensystem wie bei Inhalten.

## Inhalt

1. [Benutzer sind Inhalte](#benutzer-sind-inhalte)
2. [Benutzergruppen und Rollen](#benutzergruppen-und-rollen)
3. [Selbstregistrierung](#selbstregistrierung)
4. [Aktivierung und Freigabe](#aktivierung-und-freigabe)
5. [Passwörter](#passwörter)
6. [Anmeldung: Handler und Zwei-Faktor](#anmeldung-handler-und-zwei-faktor)
7. [Die Seiten und Templates des Moduls user](#die-seiten-und-templates-des-moduls-user)
8. [Benutzer in Templates und PHP](#benutzer-in-templates-und-php)
9. [Datenschutz](#datenschutz)

## Benutzer sind Inhalte

Ein Benutzer ist ein Objekt der Benutzerklasse (`site.ini [UserSettings] UserClassID=4`) mit einem `ezuser`-Attribut,
das Anmeldenamen, E-Mail-Adresse und Passwort-Hash enthält. Fügen Sie im Klasseneditor wie bei jeder Klasse Attribute
für ein Profil hinzu (ein Porträt, eine Biografie, ein Unternehmen). Gruppen sind Objekte der Benutzergruppenklasse
(`UserGroupClassID=3`). Jedes Konto, auch der anonyme Besucher (`AnonymousUserID=10`), liegt im Benutzerbaum unter
Knoten 5.

## Benutzergruppen und Rollen

Ein Benutzer gehört zu den Gruppen, in denen seine Knoten liegen; ein Benutzer mit zwei Platzierungen ist in zwei
Gruppen. Rollen werden Gruppen zugewiesen; das Verschieben oder Hinzufügen einer Platzierung ändert also, was ein
Benutzer tun darf. Typische Gruppen: *Members* für registrierte Besucher, *Editors*, *Administrators*. Halten Sie
Redakteure und Mitglieder in getrennten Gruppen, damit ein Mitglied nie über eine für das Personal gedachte Rolle
Bearbeitungsrechte erhält ([Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md)).

## Selbstregistrierung

Besucher registrieren sich unter `/user/register`, wenn die Rolle Anonymous `user/register` hat. Die Einstellungen
stehen in `site.ini [UserSettings]`:

| Einstellung | Mitgeliefert | Bedeutung |
|---|---|---|
| `DefaultUserPlacement` | `12` | der Knoten der Gruppe, in die neue Konten kommen |
| `DefaultSectionID` | `0` | die Sektion neuer Konten; 0 übernimmt die Sektion der Platzierung |
| `VerifyUserType` | `email` | das neue Konto muss über einen per E-Mail gesendeten Link bestätigt werden; leer schaltet das ab |
| `RegistrationFeedback` | `email` | der Administrator wird über neue Konten informiert (`RegistrationEmail` nennt die Adresse) |
| `RegisterAutoLogin` | `enabled` | ein sofort aktives Konto wird nach der Registrierung angemeldet |
| `RequireUniqueEmail` | `true` | ein Konto je E-Mail-Adresse |
| `GeneratePasswordIfEmpty` | `true` | ein leeres Passwort wird erzeugt und zugesendet |

Ändern Sie sie in den Einstellungen Ihres Siteaccess oder Ihrer Erweiterung, nie in `settings/site.ini`. Ein Workflow
bei der Registrierung kann Konten bis zur Freigabe zurückhalten ([Workflows und Auslöser](workflows-und-ausloeser.md)).
Schützen Sie das Registrierungsformular mit einem Captcha-Datentyp an der Benutzerklasse vor Spam (`recaptcha`,
`hcaptcha`; [Datentypen wählen](datentypen-waehlen.md)).

## Aktivierung und Freigabe

Nicht bestätigte Konten stehen unter **Benutzer** > **Nicht aktivierte Benutzer** (`user/unactivated`), wo ein
Administrator sie aktivieren oder entfernen kann; `ActivationByAdministratorSendsApprovalMail=enabled` sendet dem
Benutzer die Freigabe-E-Mail. Die E-Mail an den Administrator über eine neue Registrierung enthält signierte Links zum
Ansehen, Freigeben oder Ablehnen des Kontos (`RegistrationAdminLinks=enabled`;
[Signierte Links in der Registrierungs-E-Mail](../../funktionen/6.0/admin-links-der-registrierung.md)). Alte, nicht aktivierte
Konten werden bereinigt, wie in
[Rollen, Richtlinien und nicht aktivierte Benutzer, Abschnitt 7](../../anleitungen/rollen-und-richtlinien.md#7-nicht-aktivierte-benutzer-aufräumen)
beschrieben.

## Passwörter

| Einstellung | Mitgeliefert | Bedeutung |
|---|---|---|
| `[UserSettings] MinPasswordLength` | `10` | Mindestlänge |
| `[UserSettings] WeakPasswordCheck` | `enabled` | lehnt bekannte Passwörter und ihre Varianten ab, bei jedem neuen Passwort |
| `[UserSettings] HashType` | `php_default` | `password_hash()` von PHP; alte Hashes werden bei der nächsten Anmeldung erneuert |
| `[UserSettings] ForgotPasswordKeyLifetime` | `3600` | Sekunden, die ein Link "Passwort vergessen" gilt |
| `[PasswordSettings]` | alle Regeln aus | weitere Regeln für neue Passwörter ([Moderne Passwortänderung](../../funktionen/6.0/moderne-passwortaenderung.md)) |

Speichern, protokollieren oder versenden Sie Passwörter nie im Klartext, und lassen Sie
`PasswordInRegistrationEmail=disabled`. Administratoren setzen ein Passwort mit
`./console exp:resetuserpassword --help` zurück
([Das Passwort eines Benutzers zurücksetzen](../../funktionen/6.0/benutzerpasswort-zuruecksetzen.md)).

## Anmeldung: Handler und Zwei-Faktor

`[UserSettings] LoginHandler[]` listet die Handler, die der Reihe nach versucht werden; `standard` prüft die Datenbank.
Der Kernel hat außerdem Handler für LDAP (`ldap.ini`) und eine Textdatei, und eine Erweiterung kann in
`extension/<ext>/login_handler/` einen eigenen hinzufügen
([Handler, die Einstellungen benennen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/handler-ueber-einstellungen-benennen.md)).
Die Zwei-Faktor-Anmeldung kommt aus der Erweiterung `sevenx_authentication_2fa` der Standarddistribution
([Zwei-Faktor-Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md)). `MaxNumberOfFailedLogin` (0, aus, im Auslieferungszustand)
kann ein Konto nach fehlgeschlagenen Versuchen sperren.

## Die Seiten und Templates des Moduls user

Das Modul `user` hat unter anderem die Views `login`, `logout`, `register`, `activate`, `forgotpassword`, `password`,
`edit`, `preferences` und `unactivated` (`kernel/user/`). Ihre Templates liegen in `user/` der Design-Kette;
überschreiben Sie sie in Ihrer Design-Erweiterung passend zur Website und behalten Sie die Feldnamen bei, die das Modul
liest ([Templates für Benutzer und Anmeldung](../designer/vorlagen/benutzer-und-anmelde-templates.md)). Eine Profilseite ist
die Full View des Benutzerobjekts und wird daher mit einem Override für die Benutzerklasse gestaltet.

## Benutzer in Templates und PHP

```smarty
{def $current = fetch( 'user', 'current_user' )}
{if $current.is_logged_in}
    <a href={'/user/edit'|ezurl}>{$current.contentobject.name|wash()}</a>
{/if}
{undef $current}
```

Ausgaben, die vom Benutzer abhängen, dürfen nicht in einem Cache landen, den alle Besucher teilen: siehe
[Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md). In PHP gibt `eZUser::currentUser()` den angemeldeten
Benutzer zurück, und `eZUser::fetchByName()` oder `eZUser::fetchByEmail()` finden andere
([eZUser und Zugriff](../erweiterungsentwicklung/teil-8-referenz/ezuser-und-zugriff.md)).

## Datenschutz

Benutzerobjekte enthalten personenbezogene Daten. Erheben Sie nur, was die Website braucht, halten Sie Testkonten unter
`example.invalid` und planen Sie, wie Konten stillgelegt werden: **Deaktivieren und verschieben**
(`DeactivateAndMove`, standardmäßig aus) deaktiviert Konten und legt sie in einer verborgenen Gruppe ab, ohne sie zu
entfernen ([Benutzerkonten prüfen](../../funktionen/6.0/benutzerpflege.md)).

## Verwandte Seiten

- [Konten und Anmeldung](../konten-und-anmeldung.md): die Sicht der Benutzer
- [Sicherheits-Checkliste](sicherheits-checkliste.md)
- [Formulare und Informationssammlung](formulare-und-informationssammlung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
