---
title: Anmeldung über LDAP
audience: government, admin
category: public-sector
order: 60
summary: Was der LDAP-Login-Handler des Kernels tut (Mitarbeiter melden sich mit ihrem Verzeichniskonto an; ein Exponential-Benutzer wird angelegt und in Gruppen eingeordnet), die Einstellungen in settings/ldap.ini, die nötige PHP-Erweiterung ldap und die Tests, die nötig sind, weil es keine Anleitung für 6.x gibt.
keywords: LDAP, Active Directory, Verzeichnis, Login-Handler, Gruppenzuordnung, einheitliche Anmeldung, Mitarbeiterkonten, directory, login handler, ldap.ini, LDAPServer, LDAPBaseDn, LDAPLoginAttribute, LDAPGroupMappingType, single sign-in, staff accounts
type: help
---
# Anmeldung über LDAP

Diese Seite richtet sich an Administratoren, die ein Intranet an das Verzeichnis der Organisation anbinden. Der Kernel
liefert einen LDAP-Login-Handler (`kernel/classes/datatypes/ezuser/ezldapuser.php`) mit, der in `settings/ldap.ini`
konfiguriert wird. Die Dokumentation von 6.x hat keine Anleitung dafür; die Kommentare der Einstellungsdatei und die
älteren Funktionshinweise (`doc/eng-us/features/4.3/ldap_login_handler.txt`,
`doc/eng-us/features/3.8/ldap_user_binding.txt`) sind die Referenz. Testen Sie ihn mit Ihrem Verzeichnis, bevor Sie
sich darauf verlassen.

## Inhalt

1. [Wie er funktioniert](#wie-er-funktioniert)
2. [Voraussetzungen](#voraussetzungen)
3. [Einstellungen](#einstellungen)
4. [Gruppen](#gruppen)
5. [Testen](#testen)
6. [Hinweise zur Sicherheit](#hinweise-zur-sicherheit)

## Wie er funktioniert

1. `site.ini [UserSettings] LoginHandler[]` führt `LDAP` auf (nach `standard`, siehe „Anmeldeoptionen für
   Mitarbeiter“).
2. Ein Benutzer meldet sich mit den Zugangsdaten des Verzeichnisses an; der Handler durchsucht das Verzeichnis und
   bindet sich als der Benutzer.
3. Bei der ersten erfolgreichen Anmeldung wird ein Exponential-Benutzer mit Namen und E-Mail aus den Attributen des
   Verzeichnisses angelegt und gemäß der Gruppenzuordnung in Benutzergruppen eingeordnet.
4. Solche Konten haben in Exponential kein eigenes Passwort.

## Voraussetzungen

- Die PHP-Erweiterung `ldap` in dem PHP, das die Site ausliefert; der Handler prüft auf `ldap_connect`.
- Netzwerkzugang vom Webserver zum Verzeichnis. Der Handler hat keinen StartTLS-Schritt; für eine verschlüsselte
  Verbindung testen Sie mit Ihrem PHP eine Serveradresse `ldaps://` oder schützen Sie den Netzwerkweg.
- Ein Dienstkonto für die Suche, wenn das Verzeichnis keine anonyme Suche erlaubt.

## Einstellungen

`settings/override/ldap.ini.append.php`, `[LDAPSettings]` (mitgelieferte Werte in Klammern):

| Schlüssel | Bedeutung |
|---|---|
| `LDAPServer`, `LDAPPort` (389) | der Verzeichnisserver |
| `LDAPVersion` (2) | Protokollversion; Verzeichnisse brauchen heute meist 3 |
| `LDAPBaseDn` | wo Benutzer gesucht werden |
| `LDAPBindUser`, `LDAPBindPassword` | das Suchkonto |
| `LDAPSearchScope` (sub), `LDAPSearchFilters[]` | wie Benutzer gefunden werden; `LDAPEqualSign` (`--`) ersetzt `=` in diesen Werten |
| `LDAPLoginAttribute` (uid) | das Attribut, das mit dem Benutzernamen verglichen wird |
| `LDAPFirstNameAttribute`, `LDAPLastNameAttribute`, `LDAPEmailAttribute` | die Attribute, die in den Benutzer kopiert werden |
| `LDAPUserGroup[]`, `LDAPUserGroupType` | die Standardgruppe für neue Benutzer |

Halten Sie das Bind-Passwort in `settings/override/`, nie in der Versionsverwaltung.

## Gruppen

`LDAPGroupMappingType`:

| Wert | Bedeutung |
|---|---|
| `UseGroupAttribute` (mitgeliefert) | ein Benutzerattribut nennt die Gruppe(n); `LDAPCreateMissingGroups` entscheidet, ob fehlende Gruppen angelegt werden |
| `SimpleMapping` | `LDAPUserGroupMap[]` ordnet Gruppennamen des Verzeichnisses Exponential-Gruppen zu |
| `GetGroupsTree` | Gruppen werden Name zu Name zugeordnet und bei Bedarf unter `LDAPGroupRootNodeId` angelegt |

Rollen werden dann diesen Gruppen zugewiesen (siehe „Sektionen und Rollen für Intranet-Inhalte“).

## Testen

1. Setzen Sie auf einer Testinstallation Server, Base DN und Login-Attribut; schalten Sie während des Tests
   `LDAPDebugTrace` ein.
2. Melden Sie sich mit einem Test-Verzeichniskonto an; prüfen Sie den angelegten Benutzer, seine Namen, E-Mail und
   Gruppen.
3. Melden Sie sich mit einem falschen Passwort und mit einem deaktivierten Verzeichniskonto an: Beides muss
   fehlschlagen.
4. Schalten Sie `LDAPDebugTrace` wieder aus.

## Hinweise zur Sicherheit

- Eine erste LDAP-Anmeldung lehnt einen Benutzernamen ab, den bereits ein anderes Konto hat
  ([Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md#7-ein-konto-pro-login-2-minuten)).
- Ausscheidende: Das Deaktivieren des Verzeichniskontos stoppt neue Anmeldungen; der Exponential-Benutzer bleibt und
  kann unter **Benutzer > Benutzerkonten prüfen** deaktiviert werden.
- Anmeldungen werden im Kanal `access` des Audit-Protokolls festgehalten.

## Verwandte Seiten

- [Anmeldeoptionen für Mitarbeiter](anmeldeoptionen-fuer-mitarbeiter.md)
- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
