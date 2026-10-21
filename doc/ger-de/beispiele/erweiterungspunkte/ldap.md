---
title: Erweiterungspunkte in ldap.ini
audience: developer, admin
category: examples
summary: Einstellungen von ldap.ini, die Handler, Klassen oder Verzeichnisse auflisten, die eine Erweiterung ergänzen kann (2, am Namen erkannt).
keywords: Erweiterungspunkt, Erweiterung, ldap.ini, extension point, handler, extension, hook
type: generated
generated: exp:help:examples
---
# Erweiterungspunkte in ldap.ini

Einstellungen von `settings/ldap.ini`, deren Name sagt, dass sie Handler, Klassen, Filter, Provider oder Verzeichnisse enthalten, die eine Erweiterung ergänzen oder ersetzen kann. **Am Namen der Einstellung erkannt: eine Heuristik.** Lesen Sie den Kommentar der Einstellung (verlinkt), bevor Sie sich darauf verlassen.

Eine Erweiterung ergänzt eine Liste in ihrer eigenen `settings/ldap.ini.append.php` (die Einstellungen aktiver Erweiterungen werden nach denen des Kernels und vor `settings/override` gelesen):

## LDAPSettings LDAPSearchFilters

Ausgeliefert: eine leere Liste. [Die Einstellung](../ini-einstellungen/ldap/LDAPSettings.md#ldapsearchfilters).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ldap.ini.append.php
[LDAPSettings]
LDAPSearchFilters[]=expExampleLDAPSearchFilter    # Ihre Klasse oder Ihr Wert, zur Liste hinzugefügt
*/ ?>
```

## LDAPSettings LDAPGroupClass

Ausgeliefert: `exampleGroupDAClassName`. [Die Einstellung](../ini-einstellungen/ldap/LDAPSettings.md#ldapgroupclass).

```ini
<?php /* #?ini charset="utf-8"?
# extension/expexample/settings/ldap.ini.append.php
[LDAPSettings]
LDAPGroupClass=exampleGroupDAClassName    # durch Ihre Klasse oder Ihren Wert ersetzen
*/ ?>
```

## Siehe auch

- [Alle Erweiterungspunkte](README.md)
- [Ein Erweiterungsgerüst](../kochbuch/erweiterungsgeruest.md)
- [Die Blöcke von ldap.ini](../ini-einstellungen/ldap/README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/ldap.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
