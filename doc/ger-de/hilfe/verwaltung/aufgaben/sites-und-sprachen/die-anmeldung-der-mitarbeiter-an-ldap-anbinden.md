---
title: Die Anmeldung der Mitarbeiter an LDAP anbinden
audience: government, admin
category: public-sector
order: 420
summary: Aufgabe für Administratoren - Mitarbeiter sich im Intranet mit ihrem Verzeichniskonto über den LDAP-Login-Handler des Kernels anmelden lassen, konfiguriert in ldap.ini und zuerst auf einer Testinstallation getestet, weil es keine Anleitung für 6.x gibt.
keywords: LDAP, Verzeichnis, Login-Handler, Gruppenzuordnung, Anmeldung der Mitarbeiter, directory, login handler, ldap.ini, LDAPServer, LDAPBaseDn, LDAPLoginAttribute, group mapping, staff sign-in
type: help
---
# Die Anmeldung der Mitarbeiter an LDAP anbinden

Diese Aufgabe richtet sich an Administratoren. Hintergrund: [Anmeldung über LDAP](../../aufbauen/intranets/anmeldung-ueber-ldap.md).

## Bevor Sie beginnen

- Die PHP-Erweiterung `ldap` in dem PHP, das die Site ausliefert.
- Server, Base DN, Login-Attribut und ein Suchkonto des Verzeichnisses von Ihrem Verzeichnisteam.
- Eine Testinstallation.

## Schritte

1. In `settings/override/ldap.ini.append.php`, `[LDAPSettings]`: `LDAPServer`, `LDAPPort`, `LDAPVersion=3`, wenn Ihr
   Verzeichnis es braucht, `LDAPBaseDn`, `LDAPBindUser`, `LDAPBindPassword`, `LDAPLoginAttribute`, die Attribute für
   Name und E-Mail, `LDAPUserGroupType` und `LDAPUserGroup[]` für die Standardgruppe sowie die Gruppenzuordnung
   (`LDAPGroupMappingType`).
2. Verketten Sie im Intranet-Siteaccess (oder im Override) die Handler:

   ```ini
   [UserSettings]
   LoginHandler[]
   LoginHandler[]=standard
   LoginHandler[]=LDAP
   ```

3. Leeren Sie den INI-Cache; mit Velocity rollen Sie die Änderung aus.
4. Schalten Sie während des Tests `LDAPDebugTrace=enabled` ein; danach wieder aus.

## Prüfen

- Ein Test-Verzeichniskonto meldet sich an; ein Benutzer wird mit den richtigen Namen, der richtigen E-Mail und den
  richtigen Gruppen angelegt.
- Ein falsches Passwort und ein deaktiviertes Verzeichniskonto schlagen fehl.
- Ein lokaler Administrator kann sich weiterhin anmelden (der Handler `standard` zuerst).

## Probleme

| Problem | Lösung |
|---|---|
| Nichts passiert, kein Fehler | die PHP-Erweiterung `ldap` fehlt |
| Benutzer landen in der falschen Gruppe | die Einstellungen der Gruppenzuordnung; prüfen Sie `LDAPGroupMappingType` und die Zuordnung |

## Verwandte Seiten

- [Anmeldeoptionen für Mitarbeiter](../../aufbauen/intranets/anmeldeoptionen-fuer-mitarbeiter.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
