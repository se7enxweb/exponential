---
title: site.ini [PasswordChangeRequiredSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PasswordChangeRequiredSettings] in site.ini: ExemptViews"
keywords: Einstellung, site.ini, PasswordChangeRequiredSettings, INI, setting, ExemptViews
type: generated
generated: exp:help:examples
---
# site.ini [PasswordChangeRequiredSettings]

Der Block `[PasswordChangeRequiredSettings]` von `settings/site.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ExemptViews

> "Change password at next sign-in": an administrator ticks it on a user account (the  
> account edit form, or ./console exp:user-password-change-required); after that user's  
> next sign-in every page sends them to user/password until they set a new password,  
> which clears it. The REST interface answers 403 password\_change\_required, SOAP refuses  
> the sign-in. Ignored for administrators in demo mode (EXP\_DEMO\_MODE=1).  
> Guide: doc/eng-us/features/6.0/password-change-at-next-sign-in.md  
>
> The views that stay open meanwhile: module/view, or a module alone for all its views.  
> user/password is always open. user2fa is the two-step sign-in (it comes first).  

Standard: eine Liste mit 3: `user/password`, `user/logout`, `user2fa`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PasswordChangeRequiredSettings]
# ein leeres "ExemptViews[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExemptViews[]
ExemptViews[]=user/password
ExemptViews[]=user/logout
ExemptViews[]=user2fa
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
