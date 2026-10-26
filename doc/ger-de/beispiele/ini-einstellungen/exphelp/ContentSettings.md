---
title: exphelp.ini [ContentSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContentSettings] in exphelp.ini: Enabled, CachePhpBinary, CacheSiteAccessList, ParentNode, Audiences, PublicAudiences, Platforms, Areas ..."
keywords: Einstellung, exphelp.ini, ContentSettings, INI, setting, Enabled, CachePhpBinary, CacheSiteAccessList, ParentNode, Audiences, PublicAudiences, Platforms, Areas, AudienceMap, ContextHelpIds, AnonymousContextHelp, EbooksOnRelease, ReviewMaxDays, ReviewListLimit, ReleaseByAuthor, SentenceWordsPublic, SentenceWordsTechnical, ParagraphSentencesPublic, PlainLanguageAudiences, ReviewMonths, ShortReviewMonths, ShortReviewAreas, Feedback, FeedbackKeepDays, AnonymousFeedback, EpubCheckCommand, AceCommand, BookChecks, DitaHistoryDirectory, DitaHistoryLogin
type: generated
generated: exp:help:examples
---
# exphelp.ini [ContentSettings]

Der Block `[ContentSettings]` von `settings/exphelp.ini`, mit 31 Einstellung(en).

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

## CachePhpBinary

> The rows of help content are kept in var/<site>/cache/exphelp/content. A web request never reads every help object  
> (an emptied cache): it starts "php bin/php/exphelpcontent.php cache -s <siteaccess>" in the background and shows no  
> help content until that is done. The PHP command line binary for it; empty: found next to the running PHP.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
CachePhpBinary=
*/ ?>
```

## CacheSiteAccessList

> The siteaccesses whose help cache exp:velocity deploy fills, besides those that have used it  
> (var/<site>/cache/exphelp/content/siteaccesses.json)  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# ein leeres "CacheSiteAccessList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CacheSiteAccessList[]
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

> The audience options of the help\_\* classes, in their stored order. ONLY APPEND: ezselection stores the position of  
> an option, so a reordered list gives every topic another audience. The command appends missing ones.  
> The other audiences of the help (\[AudienceSettings\] AudienceNames\[\]), so the manuals built from the Markdown help  
> (exp:help:content build-manuals) keep who each page is for  

Standard: eine Liste mit 42: `Visitors`, `Members`, `Editors`, `Administrators`, `Developers`, `Project managers`, `Sales & marketing`, `Partners`, `Advocates`, `Designers`, `Hosting companies`, `Application creators` ....

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
Audiences[]=Developers
Audiences[]=Project managers
Audiences[]=Sales & marketing
Audiences[]=Partners
Audiences[]=Advocates
Audiences[]=Designers
Audiences[]=Hosting companies
Audiences[]=Application creators
Audiences[]=Website creators
Audiences[]=Supporters
Audiences[]=Sponsors
Audiences[]=Customers
Audiences[]=Governments
Audiences[]=Businesses
Audiences[]=Press, media and influencers
Audiences[]=Trainers
# ... 22 weitere in der ausgelieferten Datei
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

> The areas of help\_ids (area.topic\[.variant\]); the validator warns about any other  
> the pages of the Markdown help as topics (exp:help:content build-manuals): doc.<path of the page>  

Standard: eine Liste mit 9: `account`, `content`, `media`, `search`, `users`, `setup`, `privacy`, `help`, `doc`.

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
Areas[]=doc
*/ ?>
```

## AudienceMap

> The help's audience (editor, admin, developer, user ...) of each audience option, in lower case. build-manuals  
> gives a page of the help audience X the first option that maps to X.  

Standard: ein Hash mit 42: `visitors=user`, `members=user`, `editors=editor`, `administrators=admin`, `developers=developer`, `project managers=project-manager`, `sales & marketing=sales-marketing`, `partners=partner`, `advocates=advocate`, `designers=designer`, `hosting companies=hosting`, `application creators=app-creator` ....

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
AudienceMap[developers]=developer
AudienceMap[project managers]=project-manager
AudienceMap[sales & marketing]=sales-marketing
AudienceMap[partners]=partner
AudienceMap[advocates]=advocate
AudienceMap[designers]=designer
AudienceMap[hosting companies]=hosting
AudienceMap[application creators]=app-creator
AudienceMap[website creators]=website-creator
AudienceMap[supporters]=supporter
AudienceMap[sponsors]=sponsor
AudienceMap[customers]=customer
AudienceMap[governments]=government
AudienceMap[businesses]=businesses
AudienceMap[press, media and influencers]=press
AudienceMap[trainers]=trainer
# ... 22 weitere in der ausgelieferten Datei
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

## ReleaseByAuthor

> The review workflow (Draft, Review, Released): the person who wrote a topic's current version does not release it,  
> whatever their roles (the Object states tab, the content job and the help's buttons alike). enabled allows it, for a  
> site with a single editor. The command line (seed-topics --release, build-manuals --release) is not limited.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
ReleaseByAuthor=disabled
*/ ?>
```

## SentenceWordsPublic

> The style guide's plain language (section 3), checked as NOTICE by exp:help:content check and on the topic's page:  
> words per sentence for the audiences of PlainLanguageAudiences\[\] and for the others, sentences per paragraph for the  
> plain ones. 0 switches a check off. A site with readers who need shorter limits sets them here (15 and 3).  

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
SentenceWordsPublic=20
*/ ?>
```

## SentenceWordsTechnical

Standard: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
SentenceWordsTechnical=25
*/ ?>
```

## ParagraphSentencesPublic

Standard: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
ParagraphSentencesPublic=4
*/ ?>
```

## PlainLanguageAudiences

Standard: eine Liste mit 2: `Visitors`, `Members`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# ein leeres "PlainLanguageAudiences[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PlainLanguageAudiences[]
PlainLanguageAudiences[]=Visitors
PlainLanguageAudiences[]=Members
*/ ?>
```

## ReviewMonths

> The longest a review date may be away (style guide section 8: release + 12 months; the areas of ShortReviewAreas\[\]  
> + 6 months); a date further away is a WARNING (review-date.too-far). 0 switches it off.  

Standard: `12`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
ReviewMonths=12
*/ ?>
```

## ShortReviewMonths

Standard: `6`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
ShortReviewMonths=6
*/ ?>
```

## ShortReviewAreas

Standard: eine Liste mit 2: `privacy`, `account`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# ein leeres "ShortReviewAreas[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ShortReviewAreas[]
ShortReviewAreas[]=privacy
ShortReviewAreas[]=account
*/ ?>
```

## Feedback

> "Was this helpful?" under each Released topic: yes, or no with a reason from a fixed list, counted per topic (table  
> exphelp\_feedback). Nothing about who answered is kept: no user, no address, no free text; one answer per topic and  
> session counts. Answers older than FeedbackKeepDays are removed by the cronjob part exphelp.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
Feedback=enabled
*/ ?>
```

## FeedbackKeepDays

Standard: `730`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
FeedbackKeepDays=730
*/ ?>
```

## AnonymousFeedback

> enabled: the roles step of exp:help:content also gives Anonymous exphelp/feedback, for a site that shows its help  
> publicly (the templates of the extension exphelpsite). Off by default.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
AnonymousFeedback=disabled
*/ ?>
```

## EpubCheckCommand

> The checks of an EPUB (epubcheck, Ace by DAISY) are not shipped; a site that has them names their commands. The  
> EPUB file is added as the last argument; Ace gets --outdir <dir> --force first. Empty: the check is skipped.  
> EpubCheckCommand=java -jar /opt/epubcheck/epubcheck.jar  
> AceCommand=ace  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
EpubCheckCommand=
*/ ?>
```

## AceCommand

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
AceCommand=
*/ ?>
```

## BookChecks

> report: run the configured checks and report them; required: an e-book that fails one is not stored; disabled  

Standard: `report`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
BookChecks=report
*/ ?>
```

## DitaHistoryDirectory

> The history of the help in git (delivery path 4): the cronjob part exphelp writes the DITA export of every manual  
> into this directory (outside the web root) and commits what changed; the first run makes it a git repository. Empty:  
> off. The export holds the Released objects that DitaHistoryLogin may read. DitaHistoryAuthor: "Name <mail>" of the  
> commits, else the repository's git configuration.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
DitaHistoryDirectory=
*/ ?>
```

## DitaHistoryLogin

Standard: `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
DitaHistoryLogin=admin
*/ ?>
```

## DitaHistoryAuthor

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
DitaHistoryAuthor=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
