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

> Als Inhalt gepflegte Hilfe (doc/eng-us/features/6.0/help-content.md): Themen, Handbücher, Abbildungen und das Glossar als Inhalte  
> der Website, angelegt von ./console exp:help:content. Nichts hier zählt, bevor dieser Befehl gelaufen ist.  
> disabled: exphelp/topic, exphelp/manual und exphelp/context sind 404, und die Hilfe zeigt keine Themen  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
Enabled=enabled
*/ ?>
```

## ParentNode

> Wo der Ordner „Hilfe“ (remote\_id help-root) angelegt wird: media (Standard), content, users oder eine remote\_id. Nicht im  
> Inhaltsbaum der Website: Die Hilfe darf nicht in ihren Menüs, Listen und Feeds auftauchen.  

Standard: `media`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
ParentNode=media
*/ ?>
```

## Audiences

> Die Zielgruppenoptionen der Klassen help\_\*, in ihrer gespeicherten Reihenfolge. NUR ANHÄNGEN: ezselection speichert die Position  
> einer Option, eine umsortierte Liste gibt also jedem Thema eine andere Zielgruppe. Der Befehl hängt fehlende an.  

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

> Die Zielgruppen, deren Themen öffentlich sind (Sektion help\_public, ohne Anmeldung lesbar, wenn freigegeben)  

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

> Die Plattformoptionen (nur anhängen)  

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

> Die Bereiche der help\_ids (area.topic\[.variant\]); der Validator warnt bei jedem anderen  

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

> Die Zielgruppe der Hilfe (editor, admin, developer, user) jeder Zielgruppenoption, in Kleinbuchstaben  

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

> Kontexthilfe nach help\_id: Das Hilfe-Element „?“ dieser Seiten öffnet das freigegebene Thema (Schlüssel wie in \[ContextHelp\] Map\[\])  

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

> Die help\_ids, die andere Clients öffnen (eine mobile App, das JavaScript einer Website), damit "exp:help:content check" eine meldet, die kein  
> Thema hat oder deren Thema nicht Released ist (client.help-id-unknown, client.help-id-not-released). Standardmäßig keine.  
> ClientHelpIds\[\]  
> ClientHelpIds\[\]=account.log-in  
> enabled: Der Rollenschritt von exp:help:content gibt auch Anonymous exphelp/context, sodass das JSON öffentlicher, freigegebener  
> Themen ohne Anmeldung gelesen werden kann. Standardmäßig aus: eine sicherheitsrelevante Entscheidung jeder Website.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
AnonymousContextHelp=disabled
*/ ?>
```

## EbooksOnRelease

> enabled: Wird ein Objekt der Hilfe auf Released gesetzt, werden die E-Books (EPUB, PDF) jedes freigegebenen Handbuchs, das  
> es enthält, neu gebaut und als Dateiobjekte unter E-Books (remote\_id help-ebooks) gespeichert, eine neue Version, wenn  
> sich das Buch geändert hat (expHelpContentEbooks::onStateAssign(), beim Zuweisen von Zuständen aufgerufen).  
> disabled: Nur ./console exp:help:content ebooks speichert sie.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
EbooksOnRelease=enabled
*/ ?>
```

## ReviewMaxDays

> Überfällige Prüfungen, auf dem Dashboard der Hilfe für die aufgeführt, die das Thema bearbeiten oder seinen Zustand zuweisen können, und von  
> ./console exp:help:content review: ein Thema, dessen Prüfdatum verstrichen ist oder das länger als  
> ReviewMaxDays Tage seit seiner letzten Änderung in Review ist (der Kernel speichert kein Datum einer Zustandsänderung).  

Standard: `14`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
ReviewMaxDays=14
*/ ?>
```

## ReviewListLimit

> Höchstens so viele überfällige Themen auf dem Dashboard (der Rest wird gezählt)  

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
