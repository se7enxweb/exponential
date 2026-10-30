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

> Die Zeilen der Hilfe-Inhalte liegen in var/<site>/cache/exphelp/content. Eine Web-Anfrage liest nie jedes Hilfe-Objekt  
> (ein geleerter Cache): sie startet "php bin/php/exphelpcontent.php cache -s <siteaccess>" im Hintergrund und zeigt keine  
> Hilfe-Inhalte, bis das fertig ist. Das PHP-Kommandozeilen-Binary dafür; leer: neben dem laufenden PHP gefunden.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
CachePhpBinary=
*/ ?>
```

## CacheSiteAccessList

> Die Siteaccesses, deren Hilfe-Cache exp:velocity deploy füllt, zusätzlich zu denen, die ihn genutzt haben  
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

> Die Zielgruppen-Optionen der help\_\*-Klassen, in ihrer gespeicherten Reihenfolge. NUR ANHÄNGEN: ezselection speichert die Position  
> einer Option, daher ergibt eine umsortierte Liste für jedes Thema eine andere Zielgruppe. Der Befehl hängt fehlende an.  
> Die anderen Zielgruppen der Hilfe (\[AudienceSettings\] AudienceNames\[\]), damit die aus der Markdown-Hilfe gebauten Handbücher  
> (exp:help:content build-manuals) behalten, für wen jede Seite ist  

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

> Die Bereiche der help\_ids (area.topic\[.variant\]); der Validator warnt vor jedem anderen  
> die Seiten der Markdown-Hilfe als Themen (exp:help:content build-manuals): doc.<Pfad der Seite>  

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

> Die Zielgruppe der Hilfe (editor, admin, developer, user ...) jeder Zielgruppen-Option, in Kleinbuchstaben. build-manuals  
> ordnet eine Seite der Hilfe-Zielgruppe X der ersten Option zu, die auf X abgebildet wird.  

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

> Der Review-Workflow (Draft, Review, Released): wer die aktuelle Version eines Themas geschrieben hat, gibt es nicht frei,  
> welche Rollen er auch hat (der Tab Objektstatus, der Inhalts-Job und die Buttons der Hilfe gleichermaßen). enabled erlaubt es, für eine  
> Website mit nur einem Redakteur. Die Kommandozeile (seed-topics --release, build-manuals --release) ist nicht beschränkt.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
ReleaseByAuthor=disabled
*/ ?>
```

## SentenceWordsPublic

> Die einfache Sprache des Styleguides (Abschnitt 3), geprüft als NOTICE von exp:help:content check und auf der Seite des Themas:  
> Wörter pro Satz für die Zielgruppen aus PlainLanguageAudiences\[\] und für die anderen, Sätze pro Absatz für die  
> einfachen. 0 schaltet eine Prüfung ab. Eine Website mit Lesern, die kürzere Grenzen brauchen, setzt sie hier (15 und 3).  

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

> Der größte Abstand eines Review-Datums (Styleguide Abschnitt 8: Freigabe + 12 Monate; die Bereiche aus ShortReviewAreas\[\]  
> + 6 Monate); ein weiter entferntes Datum ist eine WARNUNG (review-date.too-far). 0 schaltet es ab.  

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

> "War das hilfreich?" unter jedem Released-Thema: ja, oder nein mit einem Grund aus einer festen Liste, gezählt pro Thema (Tabelle  
> exphelp\_feedback). Nichts darüber, wer geantwortet hat, wird behalten: kein Benutzer, keine Adresse, kein Freitext; eine Antwort pro Thema und  
> Sitzung zählt. Antworten, die älter sind als FeedbackKeepDays, entfernt der Cronjob-Teil exphelp.  

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

> enabled: der Rollen-Schritt von exp:help:content gibt auch Anonymous exphelp/feedback, für eine Website, die ihre Hilfe  
> öffentlich zeigt (die Templates der Erweiterung exphelpsite). Standardmäßig aus.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
AnonymousFeedback=disabled
*/ ?>
```

## EpubCheckCommand

> Die Prüfungen eines EPUB (epubcheck, Ace by DAISY) werden nicht mitgeliefert; eine Website, die sie hat, nennt ihre Befehle. Die  
> EPUB-Datei wird als letztes Argument angehängt; Ace bekommt zuerst --outdir <dir> --force. Leer: die Prüfung wird übersprungen.  
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

> report: führt die konfigurierten Prüfungen aus und berichtet sie; required: ein E-Book, das eine nicht besteht, wird nicht gespeichert; disabled  

Standard: `report`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
BookChecks=report
*/ ?>
```

## DitaHistoryDirectory

> Die Historie der Hilfe in Git (Auslieferungsweg 4): der Cronjob-Teil exphelp schreibt den DITA-Export jedes Handbuchs  
> in dieses Verzeichnis (außerhalb des Web-Roots) und committet, was sich geändert hat; der erste Lauf macht es zu einem Git-Repository. Leer:  
> aus. Der Export enthält die Released-Objekte, die DitaHistoryLogin lesen darf. DitaHistoryAuthor: "Name <mail>" der  
> Commits, sonst die Git-Konfiguration des Repositorys.  

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
