---
title: Verzeichnisanmeldung und LDAP
audience: businesses, admin, developer
category: business-solutions
order: 80
summary: Was Exponential wirklich bietet, damit sich Mitarbeiter mit den Konten des Firmenverzeichnisses anmelden - die Login-Handler des Kernels für LDAP und Textdateien, wie Benutzer und Gruppen angelegt werden, Single Sign-on und eigene Handler als Erweiterungspunkte und was nicht eingebaut ist.
keywords: LDAP, Verzeichnis, Active Directory, Single Sign-on, SSO, Login-Handler, LoginHandler, ldap.ini, textfile.ini, eZLDAPUser, eZTextFileUser, SingleSignOnHandlerArray, Gruppenzuordnung, LDAPGroupMappingType, SAML, OpenID Connect, Anmeldung im Intranet, directory, single sign-on, login handler, group mapping, intranet sign-in
type: help
---
# Verzeichnisanmeldung und LDAP

Diese Seite ist für IT-Verantwortliche, die möchten, dass sich Mitarbeiter mit dem Konto, das sie bereits haben, im
Intranet anmelden. Sie sagt genau, was der Kernel von Exponential dafür enthält, im Code geprüft, und was er nicht
enthält, damit ein Projekt auf Fakten geplant wird. Die Einstellungen stehen in `settings/ldap.ini`,
`settings/textfile.ini` und `settings/site.ini`; der Code liegt in `kernel/classes/datatypes/ezuser/`.

## Inhalt

1. [Was es gibt, in einer Tabelle](#was-es-gibt-in-einer-tabelle)
2. [Wie Login-Handler arbeiten](#wie-login-handler-arbeiten)
3. [Der LDAP-Login-Handler](#der-ldap-login-handler)
4. [Gruppen aus dem Verzeichnis](#gruppen-aus-dem-verzeichnis)
5. [Der Login-Handler für Textdateien](#der-login-handler-für-textdateien)
6. [Single Sign-on und eigene Handler](#single-sign-on-und-eigene-handler)
7. [Was nicht eingebaut ist](#was-nicht-eingebaut-ist)
8. [Punkte, die Sie mit Ihrem Partner klären](#punkte-die-sie-mit-ihrem-partner-klären)
9. [Verwandte Seiten](#verwandte-seiten)

## Was es gibt, in einer Tabelle

| Art der Anmeldung | Im Kernel | Standard | Quelle |
|---|---|---|---|
| Lokale Exponential-Konten (`standard`) | ja | an | `site.ini [UserSettings] LoginHandler[]=standard` |
| LDAP-Verzeichnis (`LDAP`) | ja, `eZLDAPUser` | aus (auskommentiert) | `kernel/classes/datatypes/ezuser/ezldapuser.php`, `settings/ldap.ini` |
| Eine Textdatei mit Trennzeichen (`textfile`) | ja, `eZTextFileUser` | aus (auskommentiert) | `kernel/classes/datatypes/ezuser/eztextfileuser.php`, `settings/textfile.ini` |
| Automatisches Single Sign-on | ein Erweiterungspunkt (`SingleSignOnHandlerArray[]`), kein Handler mitgeliefert | leer | `kernel/classes/datatypes/ezuser/ezuser.php`, `settings/site.ini` |
| Zweistufige Anmeldung und Social Login | Erweiterung `sevenx_authentication_2fa` | freiwillig | [Zwei-Faktor und Anmeldung](zwei-faktor-und-anmeldung.md) |
| Ihr eigener Login-Handler | ein Erweiterungspunkt (`ExtensionDirectory[]`) | keiner | `settings/site.ini` |

## Wie Login-Handler arbeiten

`site.ini [UserSettings] LoginHandler[]` listet die Handler, die der Reihe nach versucht werden, wenn sich jemand
anmeldet (`kernel/classes/datatypes/ezuser/ezuserloginhandler.php`). Die ausgelieferte Datei enthält:

```ini
LoginHandler[]=standard
#LoginHandler[]=LDAP
#LoginHandler[]=textfile
```

Um das Verzeichnis zu nutzen, fügt ein Partner `LDAP` der Liste in `settings/override/site.ini.append.php` hinzu und
füllt `ldap.ini` aus. Wenn `standard` in der Liste bleibt, können sich lokale Konten wie der Notfall-Administrator
weiterhin anmelden.

## Der LDAP-Login-Handler

Was `eZLDAPUser::loginUser()` tut, aus dem Code gelesen:

1. Es verbindet sich mit `LDAPServer` auf `LDAPPort` (standardmäßig 389) mit `LDAPVersion` (ausgeliefert `2`) und
   bindet anonym, oder mit `LDAPBindUser` und `LDAPBindPassword`, wenn das Verzeichnis keine anonymen Bindungen
   erlaubt.
2. Es sucht unterhalb von `LDAPBaseDn` nach dem Login im Attribut `LDAPLoginAttribute` (ausgeliefert `uid`), mit
   optionalen zusätzlichen `LDAPSearchFilters[]` und dem Suchbereich `LDAPSearchScope` (`sub`, `one` oder `base`). Das
   Login wird maskiert, bevor es in den Filter kommt.
3. Es bindet sich als der gefundene Eintrag mit dem Passwort, das die Person eingegeben hat. Wenn das fehlschlägt,
   schlägt die Anmeldung fehl.
4. Es legt das passende Exponential-Benutzerkonto an oder aktualisiert es, mit Vorname, Nachname und E-Mail aus
   `LDAPFirstNameAttribute` (`givenname`), `LDAPLastNameAttribute` (`sn`) und `LDAPEmailAttribute` (`mail`). Wenn das
   Verzeichnis keine Adresse hat, kann `LDAPEmailEmptyAttributeSuffix` eine aus dem Login bilden.
5. Das angelegte Konto hat einen leeren lokalen Passwort-Hash (`ezldapuser.php`): Das Passwort bleibt im Verzeichnis.

Folgen für das Unternehmen:

- Mitarbeiter verwenden ein einziges Passwort; wer im Verzeichnis aus dem Unternehmen ausscheidet, kann sich nicht mehr
  anmelden.
- Die Passwortregeln von Exponential gelten nicht für Verzeichniskonten; Konten ohne eigenes Passwort (LDAP, SSO) prüft
  auch `EndOtherSessions` nicht (`settings/site.ini [PasswordSettings]`).
- Eine erste LDAP-Anmeldung darf kein Login übernehmen, das ein anderes Konto bereits hat
  ([Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)).
- PHP braucht seine Erweiterung `ldap`. Das Velocity-Binary `standard` enthält LDAP
  ([Velocity-Pakete und -Binaries](../../../funktionen/6.0/velocity-pakete-und-binaerdateien.md)).

> **Warnung**
> `LDAPDebugTrace=enabled` schreibt Loginnamen und Passwörter in Logdateien, wie sein Kommentar in `ldap.ini` sagt.
> Verwenden Sie es nur während der Einrichtung, nie in der Produktion.

## Gruppen aus dem Verzeichnis

Neue Verzeichnisbenutzer werden in Exponential-Benutzergruppen abgelegt, und das gibt ihnen ihre Rollen. `ldap.ini`
bietet:

| Einstellung | Bedeutung |
|---|---|
| `LDAPUserGroup[]`, `LDAPUserGroupType` | Standardgruppe(n), nach Objekt-ID oder nach Name |
| `LDAPGroupMappingType=UseGroupAttribute` | (ausgeliefert) die Gruppe, die in einem Benutzerattribut genannt ist, `LDAPUserGroupAttribute` (ausgeliefert `employeetype`) |
| `LDAPGroupMappingType=SimpleMapping` | Gruppennamen des Verzeichnisses, mit `LDAPUserGroupMap[]` Gruppennamen von Exponential zugeordnet |
| `LDAPGroupMappingType=GetGroupsTree` | Gruppen automatisch nach Name zugeordnet, bei Bedarf unter `LDAPGroupRootNodeId` angelegt |
| `LDAPCreateMissingGroups` | mit `UseGroupAttribute`: Gruppen anlegen, die es nicht gibt |
| `KeepGroupAssignment` | `enabled`: Benutzer, die ein Administrator verschoben hat, werden bei ihrer nächsten Anmeldung nicht zurückverschoben |

Mit einer Zuordnung kann die Abteilungszugehörigkeit im Verzeichnis über den Zugriff auf die Abteilungen im Intranet
entscheiden ([Abteilungen, Rollen und Sektionen](abteilungen-rollen-und-sektionen.md)).

## Der Login-Handler für Textdateien

`eZTextFileUser` liest eine Datei mit Trennzeichen (`textfile.ini [TextFileSettings]`: `FileName`, `FilePath`,
`FileFieldSeparator`), deren Spalten Login, E-Mail, Passwort, Vorname und Nachname enthalten, und legt neue Benutzer in
`DefaultUserGroup` (ausgeliefert `11`) ab. Der Code vergleicht das eingegebene Passwort als Klartext mit der
Passwortspalte.

> **Warnung**
> Die Textdatei enthält daher lesbare Passwörter. Verwenden Sie sie nur für kurzlebige Tests oder Migrationen, halten
> Sie die Datei außerhalb des Web-Root mit strengen Dateirechten und entfernen Sie sie danach.

## Single Sign-on und eigene Handler

- **Automatisches Single Sign-on.** Für einen Besucher, der nicht angemeldet ist, ruft `eZUser` jede Klasse
  `eZ<name>SSOHandler` auf, die in `SingleSignOnHandlerArray[]` aufgeführt ist, und meldet den Benutzer an, den ihr
  `handleSSOLogin()` zurückgibt. Das Repository liefert keinen solchen Handler mit; einer wird als Erweiterung
  geschrieben, zum Beispiel um einem Header zu vertrauen, den ein Anmelde-Proxy des Unternehmens setzt.
- **Ihr eigener Login-Handler.** `ExtensionDirectory[]` nennt Erweiterungen, die `login_handler/<name>user.php` mit
  einer Klasse `eZ<name>User` enthalten; `sevenx_authentication_2fa` ist so gebaut.

## Was nicht eingebaut ist

Das Repository, durchsucht am 19. Oktober 2026, hat keinen SAML-Handler, keinen OpenID-Connect-Handler für die
Anmeldung von Mitarbeitern, keine eigene Active-Directory-Anbindung über LDAP hinaus und keine automatische Anlage von
Benutzern aus einem Verzeichnis (Benutzer werden bei ihrer ersten Anmeldung angelegt). Jedes davon ist ein
Erweiterungsprojekt; bitten Sie einen Partner um eine Schätzung ([Einen Partner auswählen](../auswahl/einen-partner-auswaehlen.md)).

## Punkte, die Sie mit Ihrem Partner klären

- [ ] Welches Verzeichnis, welcher Base DN, welches Attribut das Login enthält.
- [ ] Ein Bind-Konto nur mit Lesezugriff, sein Passwort in `settings/override/` aufbewahrt und außerhalb der
      Versionsverwaltung.
- [ ] Eine verschlüsselte Verbindung zum Verzeichnis; `ldap.ini` hat keine Einstellung für StartTLS, testen Sie also,
      was Ihr Server anbietet.
- [ ] Die Gruppenzuordnung und welche Gruppen welche Rollen bekommen.
- [ ] Ein lokaler Notfall-Administrator, mit `standard` erhalten.
- [ ] Was mit dem Exponential-Konto geschieht, wenn jemand geht
  ([Benutzerkonten prüfen](../../../funktionen/6.0/benutzerpflege.md)).

## Verwandte Seiten

- [Zwei-Faktor und Anmeldung](zwei-faktor-und-anmeldung.md)
- [Referenz der LDAP-Einstellungen](../../../beispiele/ini-einstellungen/ldap/LDAPSettings.md)
- [Erweiterungspunkte in ldap.ini](../../../beispiele/erweiterungspunkte/ldap.md)
- [Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
