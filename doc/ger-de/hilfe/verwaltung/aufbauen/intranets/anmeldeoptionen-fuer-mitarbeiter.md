---
title: Anmeldeoptionen für Mitarbeiter
audience: government, admin, project-manager
category: public-sector
order: 50
summary: Die Anmeldeoptionen eines Exponential-Intranets (lokale Konten, die Login-Handler LDAP und Textdatei des Kernels, zweistufige Anmeldung aus sevenx_authentication_2fa), wie Login-Handler verkettet werden und was nicht verfügbar ist.
keywords: Anmeldung, Login-Handler, Single Sign-on, zweistufig, Mitarbeiterkonten, Verzeichnis, sign-in, login handler, LoginHandler, standard, LDAP, textfile, single sign-on, SingleSignOnHandlerArray, two-step, 2FA, staff accounts, directory
type: help
---
# Anmeldeoptionen für Mitarbeiter

Diese Seite richtet sich an Administratoren und Projektleiter, die wählen, wie sich Mitarbeiter anmelden. Der Kernel
probiert die Login-Handler aus `site.ini [UserSettings] LoginHandler[]` der Reihe nach; der mitgelieferte Wert ist
`standard`, mit `LDAP` und `textfile` als auskommentierte Beispiele.

## Inhalt

1. [Die Möglichkeiten](#die-möglichkeiten)
2. [Handler verketten](#handler-verketten)
3. [Zweistufige Anmeldung hinzufügen](#zweistufige-anmeldung-hinzufügen)
4. [Was nicht verfügbar ist](#was-nicht-verfügbar-ist)

## Die Möglichkeiten

| Möglichkeit | Wo | Hinweis |
|---|---|---|
| Lokale Konten (`standard`) | der Kernel | Passwortregeln in `[PasswordSettings]`; die Sperre nach fehlgeschlagenen Anmeldungen ist voreingestellt aus (`MaxNumberOfFailedLogin=0`) |
| LDAP-Verzeichnis (`LDAP`) | der Kernel, `settings/ldap.ini` | siehe „Anmeldung über LDAP“; keine Anleitung für 6.x |
| Textdatei (`textfile`) | der Kernel, `settings/textfile.ini` | Benutzer aus einer Datei gelesen; für Sonderfälle |
| Zweistufige Anmeldung | `sevenx_authentication_2fa` | ein Code aus einer App oder per E-Mail nach dem Passwort ([Zwei-Faktor-Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)) |

## Handler verketten

```ini
# settings/override/site.ini.append.php (or the intranet siteaccess)
[UserSettings]
LoginHandler[]
LoginHandler[]=standard
LoginHandler[]=LDAP
```

Handler werden der Reihe nach probiert; `standard` an erster Stelle lässt lokale Administratorkonten sich anmelden,
wenn das Verzeichnis nicht verfügbar ist. `ExtensionDirectory[]` nennt Erweiterungen, die weitere Handler bereitstellen.

## Zweistufige Anmeldung hinzufügen

Die zweistufige Anmeldung funktioniert mit lokalen Konten; für Mitarbeiter mit administrativen Rechten ist sie der
wichtigste Schutz gegen gestohlene Passwörter (siehe „Zweistufige Anmeldung für Mitarbeiter“).

## Was nicht verfügbar ist

Geprüft in `settings/site.ini`: Mit dem Kernel wird kein Handler für SAML oder OpenID Connect geliefert
(`SingleSignOnHandlerArray[]` ist leer). Eine Anbindung an Ihren Identitätsanbieter braucht eine Erweiterung, die einen
Login- oder Single-Sign-on-Handler bereitstellt; behandeln Sie das als Entwicklungsarbeit mit einer
Sicherheitsprüfung.

## Verwandte Seiten

- [Anmeldung über LDAP](anmeldung-ueber-ldap.md)
- [Konten und Anmeldung](../../../konten-und-anmeldung.md)
- [Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
