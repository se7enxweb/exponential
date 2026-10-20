---
title: mailpreferences.ini [FooterSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FooterSettings] in mailpreferences.ini: OrganisationName, OrganisationAddress, Template, PrivacyURL"
keywords: Einstellung, mailpreferences.ini, FooterSettings, INI, setting, OrganisationName, OrganisationAddress, Template, PrivacyURL
type: generated
generated: exp:help:examples
---
# mailpreferences.ini [FooterSettings]

Der Block `[FooterSettings]` von `settings/mailpreferences.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/mailpreferences.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mailpreferences.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mailpreferences.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mailpreferences.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## OrganisationName

> Required by CAN-SPAM, CASL and the EU rules: who sends the mail and a  
> valid postal address. The gate adds both to every optional mail; the status  
> page and exp:mail:status warn while they are empty.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[FooterSettings]
OrganisationName=
*/ ?>
```

## OrganisationAddress

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[FooterSettings]
OrganisationAddress=
*/ ?>
```

## Template

> The template of the footer (text and HTML parts); the built-in text is used  
> when the template does not exist.  

Standard: `design:mailpreferences/mail/footer.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[FooterSettings]
Template=design:mailpreferences/mail/footer.tpl
*/ ?>
```

## PrivacyURL

> The privacy notice, linked from the footer of optional mail and from the  
> preference page (GDPR art. 13): a full https:// address or a path of the  
> site (/privacy). Empty: the node of menu.ini \[SiteInfo\] PrivacyPolicyID of  
> the public siteaccess when there is one, else no link.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[FooterSettings]
PrivacyURL=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mailpreferences.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
