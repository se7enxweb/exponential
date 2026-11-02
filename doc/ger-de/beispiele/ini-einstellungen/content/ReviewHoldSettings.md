---
title: content.ini [ReviewHoldSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ReviewHoldSettings] in content.ini: AutoHold, AutoHoldClassList, ExemptPolicyList"
keywords: Einstellung, content.ini, ReviewHoldSettings, INI, setting, AutoHold, AutoHoldClassList, ExemptPolicyList
type: generated
generated: exp:help:examples
---
# content.ini [ReviewHoldSettings]

Der Block `[ReviewHoldSettings]` von `settings/content.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AutoHold

> Auto-hold of content submitted by visitors (doc/eng-us/features/6.0/review-hold.md, "Auto-hold"): the first  
> publication of an object of a class in AutoHoldClassList\[\], made by a user who is not exempt (ExemptPolicyList\[\]),  
> is put into the review hold (review\_hold/held) and its locations are hidden, in the publish operation itself (the  
> step auto-review-hold, right before the content edit handlers, the view cache and the search index), so visitors  
> never see it. A reviewer releases it in Dashboard > Recent content (moderation/recent) or in the website sidebar:  
> the hold is released and the locations hidden by the auto-hold are revealed. Recorded in the audit as  
> content.object.hold.auto. Needs the state group of the review hold (the upgrade step 6.0.15-review-hold); without  
> it nothing is held. Read in the siteaccess the content is published in.  
>
> enabled or disabled  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ReviewHoldSettings]
AutoHold=disabled
*/ ?>
```

## AutoHoldClassList

> The class identifiers whose new objects are held (comments, reviews, forum posts, user submissions ...).  

Standard: eine Liste mit 1: `comment`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ReviewHoldSettings]
# ein leeres "AutoHoldClassList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AutoHoldClassList[]
AutoHoldClassList[]=comment
*/ ?>
```

## ExemptPolicyList

> Who is never auto-held: a user who has any of these (module/function). Three are read in detail:  
>   state/assign   may place the held state on the object (the state/assign policy with its limitations)  
>   content/hide   may hide (and so reveal) the object's location  
>   moderation/act may run the actions of Dashboard > Recent content  
> any other module/function counts when the user has it at all.  

Standard: eine Liste mit 2: `state/assign`, `moderation/act`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ReviewHoldSettings]
# ein leeres "ExemptPolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExemptPolicyList[]
ExemptPolicyList[]=state/assign
ExemptPolicyList[]=moderation/act
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
