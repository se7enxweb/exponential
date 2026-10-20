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

> Von CAN-SPAM, CASL und den EU-Regeln verlangt: wer die Mail sendet, und eine  
> gültige Postanschrift. Das Gate fügt beides jeder optionalen Mail hinzu; die Status-  
> seite und exp:mail:status warnen, solange sie leer sind.  

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

> Das Template der Fußzeile (Text- und HTML-Teil); der eingebaute Text wird verwendet,  
> wenn das Template nicht existiert.  

Standard: `design:mailpreferences/mail/footer.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[FooterSettings]
Template=design:mailpreferences/mail/footer.tpl
*/ ?>
```

## PrivacyURL

> Die Datenschutzerklärung, verlinkt aus der Fußzeile optionaler Mail und von der  
> Einstellungsseite (DSGVO Art. 13): eine vollständige https://-Adresse oder ein Pfad der  
> Website (/privacy). Leer: der Knoten aus menu.ini \[SiteInfo\] PrivacyPolicyID des  
> öffentlichen Siteaccess, wenn es einen gibt, sonst kein Link.  

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
