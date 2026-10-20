---
title: exphelp.ini [IssueTrackers]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [IssueTrackers] in exphelp.ini: TrackerList, ExtensionOwners, BundledExtensions, ExtensionListLimit, CommunityLinks"
keywords: Einstellung, exphelp.ini, IssueTrackers, INI, setting, TrackerList, ExtensionOwners, BundledExtensions, ExtensionListLimit, CommunityLinks
type: generated
generated: exp:help:examples
---
# exphelp.ini [IssueTrackers]

Der Block `[IssueTrackers]` von `settings/exphelp.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TrackerList

> Where errors are reported (exphelp/report). Each tracker has \[Tracker\_<key>\] Name, Description, URL (the GitHub  
> issue list, https://github.com/<owner>/<repository>/issues; anything else is ignored) and NewIssueURL.  

Standard: eine Liste mit 3: `software`, `velocity`, `community`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IssueTrackers]
# ein leeres "TrackerList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TrackerList[]
TrackerList[]=software
TrackerList[]=velocity
TrackerList[]=community
*/ ?>
```

## ExtensionOwners

> An active extension whose website (extension.xml, ezinfo.php, composer.json or its git origin) is a GitHub  
> repository of one of these owners is offered as a tracker of its own on the report page  

Standard: eine Liste mit 1: `se7enxweb`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IssueTrackers]
# ein leeres "ExtensionOwners[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionOwners[]
ExtensionOwners[]=se7enxweb
*/ ?>
```

## BundledExtensions

> Extensions shipped inside the kernel's repository: their errors go to the software tracker  

Standard: eine Liste mit 4: `expservices`, `ezformtoken`, `ezjscore`, `ezoe`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IssueTrackers]
# ein leeres "BundledExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
BundledExtensions[]
BundledExtensions[]=expservices
BundledExtensions[]=ezformtoken
BundledExtensions[]=ezjscore
BundledExtensions[]=ezoe
*/ ?>
```

## ExtensionListLimit

> The most active extensions named in the environment summary (the rest is counted)  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IssueTrackers]
ExtensionListLimit=60
*/ ?>
```

## CommunityLinks

> Where to ask before reporting, <label>|<https URL>  

Standard: eine Liste mit 3: `Exponential community forums|https://share.exponential.earth/forums`, `Telegram community chat|https://t.me/exponentialcms`, `Exponential documentation|https://exponential.doc.exponential.earth/Exponential`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[IssueTrackers]
# ein leeres "CommunityLinks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CommunityLinks[]
CommunityLinks[]=Exponential community forums|https://share.exponential.earth/forums
CommunityLinks[]=Telegram community chat|https://t.me/exponentialcms
CommunityLinks[]=Exponential documentation|https://exponential.doc.exponential.earth/Exponential
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
