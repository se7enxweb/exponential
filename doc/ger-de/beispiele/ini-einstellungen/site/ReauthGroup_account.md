---
title: site.ini [ReauthGroup_account]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ReauthGroup_account] in site.ini: Enabled, Rules"
keywords: Einstellung, site.ini, ReauthGroup_account, INI, setting, Enabled, Rules
type: generated
generated: exp:help:examples
---
# site.ini [ReauthGroup_account]

Der Block `[ReauthGroup_account]` von `settings/site.ini`, mit 2 Einstellung(en).

> Rules, in every group: "module/view \[token ...\]"  
>   module/view    the view (view \* for every view of the module); a POST to it is asked  
>   \*              every request, GET too (a page that downloads, or a form with a file upload)  
>   Name           only a POST that carries this field (any of several names)  
>   self, other    only when the account addressed is the user's own / another user's  
>                  (content/edit of a user object, user/password, user/setting; the  
>                  pending account of an approval item of "Approve user accounts")  
>   credentials    only when the POST changes the login, e-mail or password of a user  
>   !module/view   never asked for this view (an exception to a module/\* rule)  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> The security settings of one's own account.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthGroup_account]
Enabled=true
*/ ?>
```

## Rules

> "Sign out all other sessions" asks first too: someone at an unattended screen must not lock the owner out  

Standard: eine Liste mit 6: `user/password self`, `user/passkeys *`, `user2fa/setup`, `apikey/list`, `content/edit self credentials`, `user/sessions self SaveMailButton RevokeOthersButton`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthGroup_account]
# ein leeres "Rules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Rules[]
Rules[]=user/password self
Rules[]=user/passkeys *
Rules[]=user2fa/setup
Rules[]=apikey/list
Rules[]=content/edit self credentials
Rules[]=user/sessions self SaveMailButton RevokeOthersButton
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
