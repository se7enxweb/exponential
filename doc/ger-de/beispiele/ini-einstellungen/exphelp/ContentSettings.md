---
title: exphelp.ini [ContentSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContentSettings] in exphelp.ini: Enabled, ParentNode, Audiences, PublicAudiences, Platforms, Areas, AudienceMap, ContextHelpIds ..."
keywords: Einstellung, exphelp.ini, ContentSettings, INI, setting, Enabled, ParentNode, Audiences, PublicAudiences, Platforms, Areas, AudienceMap, ContextHelpIds, AnonymousContextHelp, EbooksOnRelease, ReviewMaxDays, ReviewListLimit
type: generated
generated: exp:help:examples
---
# exphelp.ini [ContentSettings]

Der Block `[ContentSettings]` von `settings/exphelp.ini`, mit 12 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Help maintained as content (doc/eng-us/features/6.0/help-content.md): topics, manuals, figures and the glossary as content  
> of the site, created by ./console exp:help:content. Nothing here matters until that command ran.  
> disabled: exphelp/topic, exphelp/manual and exphelp/context are 404 and the help shows no topics  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
Enabled=enabled
*/ ?>
```

## ParentNode

> Where the folder "Help" (remote\_id help-root) is created: media (default), content, users, or a remote\_id. Not in  
> the site's content tree: help must not show up in its menus, listings and feeds.  

Standard: `media`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
ParentNode=media
*/ ?>
```

## Audiences

> The audience options of the help\_\* classes, in their stored order. ONLY APPEND: ezselection stores the position of  
> an option, so a reordered list gives every topic another audience. The command appends missing ones.  

Standard: eine Liste mit 4: `Visitors`, `Members`, `Editors`, `Administrators`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# ein leeres "Audiences[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Audiences[]
Audiences[]=Visitors
Audiences[]=Members
Audiences[]=Editors
Audiences[]=Administrators
*/ ?>
```

## PublicAudiences

> The audiences whose topics are public (section help\_public, readable without login when Released)  

Standard: eine Liste mit 1: `Visitors`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# ein leeres "PublicAudiences[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PublicAudiences[]
PublicAudiences[]=Visitors
*/ ?>
```

## Platforms

> The platform options (only append)  

Standard: eine Liste mit 3: `Web`, `Admin`, `Mobile`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# ein leeres "Platforms[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Platforms[]
Platforms[]=Web
Platforms[]=Admin
Platforms[]=Mobile
*/ ?>
```

## Areas

> The areas of help\_ids (area.topic\[.variant\]); the validator warns about any other  

Standard: eine Liste mit 8: `account`, `content`, `media`, `search`, `users`, `setup`, `privacy`, `help`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# ein leeres "Areas[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Areas[]
Areas[]=account
Areas[]=content
Areas[]=media
Areas[]=search
Areas[]=users
Areas[]=setup
Areas[]=privacy
Areas[]=help
*/ ?>
```

## AudienceMap

> The help's audience (editor, admin, developer, user) of each audience option, in lower case  

Standard: ein Hash mit 4: `visitors=user`, `members=user`, `editors=editor`, `administrators=admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# ein leeres "AudienceMap[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AudienceMap[]
AudienceMap[visitors]=user
AudienceMap[members]=user
AudienceMap[editors]=editor
AudienceMap[administrators]=admin
*/ ?>
```

## ContextHelpIds

> Context help by help\_id: the "?" Help control of these pages opens the Released topic (keys as \[ContextHelp\] Map\[\])  

Standard: ein Hash mit 2: `content/edit=content.publish-article`, `content/history=content.versions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# ein leeres "ContextHelpIds[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ContextHelpIds[]
ContextHelpIds[content/edit]=content.publish-article
ContextHelpIds[content/history]=content.versions
*/ ?>
```

## AnonymousContextHelp

> The help\_ids other clients open (a mobile app, a site's JavaScript), so "exp:help:content check" reports one that no  
> topic has or whose topic is not Released (client.help-id-unknown, client.help-id-not-released). None by default.  
> ClientHelpIds\[\]  
> ClientHelpIds\[\]=account.log-in  
> enabled: the roles step of exp:help:content also gives Anonymous exphelp/context, so the JSON of public, Released  
> topics can be read without login. Off by default: a security-relevant choice of each site.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
AnonymousContextHelp=disabled
*/ ?>
```

## EbooksOnRelease

> enabled: when an object of the help is set to Released, the e-books (EPUB, PDF) of every Released manual that  
> contains it are built again and stored as file objects below E-books (remote\_id help-ebooks), a new version when  
> the book changed (expHelpContentEbooks::onStateAssign(), called when states are assigned).  
> disabled: only ./console exp:help:content ebooks stores them.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
EbooksOnRelease=enabled
*/ ?>
```

## ReviewMaxDays

> Overdue reviews, listed on the help's dashboard to who can edit the topic or assign its state, and by  
> ./console exp:help:content review: a topic whose review date has passed, or that has been in Review longer than  
> ReviewMaxDays days since it last changed (the kernel keeps no date of a state change).  

Standard: `14`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
ReviewMaxDays=14
*/ ?>
```

## ReviewListLimit

> At most this many overdue topics on the dashboard (the rest are counted)  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
ReviewListLimit=10
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
