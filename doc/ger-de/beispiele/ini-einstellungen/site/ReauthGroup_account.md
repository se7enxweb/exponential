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

> Regeln, in jeder Gruppe: "modul/ansicht \[token ...\]"  
>   modul/ansicht  die Ansicht (ansicht \* für jede Ansicht des Moduls); ein POST an sie wird gefragt  
>   \*              jede Anfrage, auch GET (eine Seite, die herunterlädt, oder ein Formular mit Datei-Upload)  
>   Name           nur ein POST, der dieses Feld trägt (einen von mehreren Namen)  
>   self, other    nur wenn das betroffene Konto das eigene / das eines anderen Benutzers ist  
>                  (content/edit eines Benutzerobjekts, user/password, user/setting; das  
>                  wartende Konto eines Freigabe-Elements von „Benutzerkonten freigeben“)  
>   credentials    nur wenn der POST Login, E-Mail oder Passwort eines Benutzers ändert  
>   !modul/ansicht für diese Ansicht nie gefragt (eine Ausnahme zu einer modul/\*-Regel)  
>   bulk           nur wenn mehrere Elemente auf einmal entfernt werden (BulkCount)  
> Eine Gruppe kann UserGroups\[\] nennen (Content-Objekt-IDs von Benutzergruppen): Sie fragt dann nur  
> deren Mitglieder; leer oder fehlend: jeden Benutzer.  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Die Sicherheitseinstellungen des eigenen Kontos.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ReauthGroup_account]
Enabled=true
*/ ?>
```

## Rules

> „Alle anderen Sitzungen abmelden“ fragt ebenfalls zuerst: Jemand an einem unbeaufsichtigten Bildschirm darf den Inhaber nicht aussperren  

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
