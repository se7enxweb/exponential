---
title: "Login-Handler: LDAP, Textdatei und eigene"
audience: integrator, developer, admin
category: integration
order: 220
summary: Menschen mit einem Konto, das in einem Verzeichnis oder einem anderen System liegt, bei Exponential anmelden lassen - wie die Kette der Login-Handler funktioniert, der LDAP-Handler und seine Gruppenzuordnung, der Textdatei-Handler, einen eigenen Login-Handler schreiben und die Punkte zur Sicherheit.
keywords: Login-Handler, LoginHandler, LDAP, ldap.ini, eZLDAPUser, LDAPVersion, Gruppenzuordnung, Textdatei, textfile, textfile.ini, eZTextFileUser, eigener Login-Handler, ExtensionDirectory, login_handler, Verzeichnis, Active Directory, Identitätsanbieter, Authentifizierung, login handler, group mapping, custom login handler, directory, identity provider, authentication
type: help
---
# Login-Handler: LDAP, Textdatei und eigene

Diese Seite ist für Integratoren, die Exponential mit einem Unternehmensverzeichnis oder einem anderen Benutzerspeicher
verbinden, damit sich Mitarbeitende oder Mitglieder mit dem Konto anmelden, das sie schon haben. Wenn sich jemand
anmeldet, fragt Exponential der Reihe nach die **Login-Handler**, die in `site.ini [UserSettings] LoginHandler[]`
genannt sind; der erste Handler, der Login und Passwort akzeptiert, meldet den Benutzer an. Jeder externe Benutzer
bekommt einen lokalen Exponential-Benutzer in einer Benutzergruppe, sodass Rollen und Richtlinien wie für alle anderen
funktionieren.

## Inhalt

1. [Die Kette](#die-kette)
2. [LDAP](#ldap)
3. [Textdatei](#textdatei)
4. [Ein eigener Login-Handler](#ein-eigener-login-handler)
5. [Punkte zur Sicherheit](#punkte-zur-sicherheit)
6. [Checkliste](#checkliste)

## Die Kette

```ini
# settings/override/site.ini.append.php
[UserSettings]
LoginHandler[]
LoginHandler[]=LDAP
LoginHandler[]=standard
```

`standard` ist die lokale Passwortprüfung. Der mitgelieferte Standard ist `standard` allein; `LDAP` und `textfile`
stehen auskommentiert darin. Behalten Sie `standard` in der Kette, nach dem externen Handler, damit sich ein lokaler
Notfall-Administrator auch anmelden kann, wenn das Verzeichnis nicht erreichbar ist. Die Zwei-Faktor-Anmeldung von
`sevenx_authentication_2fa` ist ebenfalls ein Login-Handler (`sevenxUser2fa`), an erster Stelle platziert
([Zwei-Faktor-Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md)).

Der Kernel findet einen Handler `<name>` als `kernel/classes/datatypes/ezuser/ez<name>user.php` mit der Klasse
`eZ<name>User` oder in einem Erweiterungsverzeichnis, das in `[UserSettings] ExtensionDirectory[]` aufgeführt ist, als
`extension/<ext>/login_handler/ez<name>user.php` (`eZUserLoginHandler`, `kernel/classes/datatypes/ezuser/ezuserloginhandler.php`).

## LDAP

Der Handler ist `eZLDAPUser` (`kernel/classes/datatypes/ezuser/ezldapuser.php`) auf Basis der PHP-Erweiterung `ldap`.
Einstellungen in `settings/override/ldap.ini.append.php`:

```ini
[LDAPSettings]
LDAPEnabled=true
LDAPVersion=3
LDAPServer=ldaps://ldap.example.com
LDAPPort=636
LDAPBaseDn=ou--people,dc--example,dc--com
LDAPBindUser=cn--exponential,ou--services,dc--example,dc--com
LDAPBindPassword=<the service account's password>
LDAPLoginAttribute=uid
LDAPSearchScope=sub
LDAPFirstNameAttribute=givenname
LDAPLastNameAttribute=sn
LDAPEmailAttribute=mail
Utf8Encoding=true
LDAPUserGroupType=name
LDAPUserGroup[]
LDAPUserGroup[]=Staff
```

| Einstellung | Bedeutung |
|---|---|
| `LDAPEqualSign=--` | In `LDAPBaseDn` und `LDAPSearchFilters[]` steht `--` für `=` (ein INI-Wert kann kein `=` enthalten) |
| `LDAPSearchFilters[]` | zusätzliche Filter, zum Beispiel `objectClass--inetOrgPerson` |
| `LDAPUserGroupType`, `LDAPUserGroup[]` | die Standardgruppe für neue Benutzer, nach `id` oder `name` |
| `LDAPGroupMappingType` | `UseGroupAttribute` (aus `LDAPUserGroupAttribute`), `SimpleMapping` (`LDAPUserGroupMap[]`, Name zu Name) oder `GetGroupsTree` |
| `LDAPCreateMissingGroups` | Gruppen anlegen, die es noch nicht gibt |
| `KeepGroupAssignment` | `enabled`: Ein von Hand in eine andere Gruppe verschobener Benutzer bleibt dort |
| `LDAPDebugTrace` | protokolliert Anmeldungen **und Passwörter**: nie in der Produktion |

Bekannte Lücken ([API- und Protokollstandards, LDAP](../../standards/apis-und-protokolle.md#ldap-rfc-4511)): Der
Standardwert `LDAPVersion=2` muss für jedes aktuelle Verzeichnis auf `3` gesetzt werden; StartTLS wird nicht
aufgerufen, die Verschlüsselung hängt also von einer Adresse `ldaps://` ab; ob `ldaps://` mit der getrennten
Einstellung `LDAPPort` funktioniert, ist nicht geprüft, testen Sie es also mit Ihrem Verzeichnis. Der Login wird
maskiert, bevor er in einen Filter kommt.

## Textdatei

`eZTextFileUser` liest Benutzer aus einer Textdatei mit Trennzeichen (`settings/textfile.ini [TextFileSettings]`):
`FileName`, `FilePath` (`root` = das Wurzelverzeichnis der Installation), `FileFieldSeparator` (Standard `tab`), die
Spaltennummern von Login (`LoginAttribute`), Passwort (`PasswordAttribute`), Vorname, Nachname und E-Mail sowie die
Standardgruppe. Es ist ein einfacher Weg, einen nächtlichen Export eines anderen Systems festlegen zu lassen, wer sich
anmelden darf.

> **Hinweis**
> Die Datei enthält Passwörter so, wie das andere System sie exportiert hat. Halten Sie sie außerhalb des
> Web-Wurzelverzeichnisses, nur für den Benutzer der Website lesbar, und bevorzugen Sie LDAP oder einen eigenen
> Handler, der das andere System fragt, damit Passwörter nie in einer Datei liegen.

## Ein eigener Login-Handler

Für einen Identitätsanbieter, der eine Passwortprüfung über eine API anbietet, schreiben Sie einen Handler in Ihrer
Erweiterung:

```ini
# extension/myintegration/settings/site.ini.append.php
[UserSettings]
ExtensionDirectory[]=myintegration
```

```php
<?php
// extension/myintegration/login_handler/ezmyidpuser.php, used as LoginHandler[]=myidp
class eZmyidpUser extends eZUser
{
    static function loginUser( $login, $password, $authenticationMatch = false )
    {
        $client = new myIntegrationIdpClient();             // your API client, configured from myintegration.ini
        $profile = $client->checkPassword( $login, $password );
        if ( !$profile )
        {
            return false;                                   // the next handler in the chain is asked
        }
        $user = eZUser::fetchByName( $login );
        if ( !$user instanceof eZUser )
        {
            $user = myIntegrationUserProvisioning::create( $login, $profile );   // a user object in the right group
        }
        if ( !$user instanceof eZUser || !$user->isEnabled() )
        {
            return false;
        }
        eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
        return $user;
    }
}
```

Der Klassenname ist `eZ` plus der Name des Handlers genau so, wie er in `LoginHandler[]` steht, plus `User`; der
Dateiname ist derselbe in Kleinbuchstaben. Lesen Sie `eZLDAPUser::loginUser()` für das vollständige Muster, wie der
lokale Benutzer und seine Gruppenzuordnung angelegt und aktualisiert werden.

## Punkte zur Sicherheit

- **Überall HTTPS**, wo ein Passwort eingetippt oder weitergeschickt wird.
- **Ein eigenes Bind-Konto** für LDAP mit Lesezugriff nur auf den Personenzweig.
- **An der Quelle deaktiviert heißt hier deaktiviert.** Entscheiden Sie, was passiert, wenn eine Person geht: Das
  Verzeichnis weist das Passwort ab, aber der lokale Benutzer existiert noch; deaktivieren Sie ihn mit einem geplanten
  Job oder von Hand ([Benutzerhygiene](../../funktionen/6.0/benutzerpflege.md)).
- **Fehlgeschlagene Anmeldungen zählen.** Fehlgeschlagene Anmeldungen werden im Audit aufgezeichnet
  (`access.session.login.failed`), und Alarmregeln beobachten sie.
- **Halten Sie das Bind-Passwort in `settings/override`**, nie in Git.

## Checkliste

- [ ] Kette der Handler entschieden; `standard` als Rückfall mit einem lokalen Notfall-Administrator behalten.
- [ ] LDAP: `LDAPVersion=3`, verschlüsselte Verbindung getestet, Bind-Konto mit minimalen Rechten,
      `LDAPDebugTrace=disabled`.
- [ ] Gruppenzuordnung mit Benutzern jeder Gruppe getestet.
- [ ] Ablauf beim Ausscheiden mit dem Kunden vereinbart.
- [ ] Anmeldung auf jedem Siteaccess getestet, der externe Benutzer annehmen soll.

## Verwandte Seiten

- [Single Sign-on und Social Login](single-sign-on-und-social-login.md)
- [Zwei-Faktor-Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md)
- [eZUser und Zugriff](../erweiterungsentwicklung/teil-8-referenz/ezuser-und-zugriff.md)
- [Dienstkonten, Rollen und Richtlinien](dienstkonten-rollen-und-richtlinien.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
