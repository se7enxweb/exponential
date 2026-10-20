---
title: exphelp.ini [AudienceSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AudienceSettings] in exphelp.ini: Audiences, AudienceNames, AudienceIcons, PathRules, TypeDefaults"
keywords: Einstellung, exphelp.ini, AudienceSettings, INI, setting, Audiences, AudienceNames, AudienceIcons, PathRules, TypeDefaults
type: generated
generated: exp:help:examples
---
# exphelp.ini [AudienceSettings]

Der Block `[AudienceSettings]` von `settings/exphelp.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Audiences

> editor: schreibt und veröffentlicht Inhalte; admin: betreibt die Website; developer: baut darauf auf; user: Besucher oder  
> Mitglied einer Website; project-manager: plant und leitet darauf gebaute Projekte; sales-marketing: präsentiert, verkauft und bewirbt es  
> und lässt seine Community wachsen; partner: ein Unternehmen, das mit Exponential baut, hostet oder verkauft; advocate: ein Fürsprecher  
> der Community (Street Team); designer: gestaltet Themes und Templates, baut, liefert und pflegt Erweiterungen;  
> hosting: ein Hosting-Unternehmen; app-creator / website-creator: baut Anwendungen / Websites damit; supporter:  
> hilft dem Projekt zu wachsen; sponsor: finanziert es (doc/eng-us/help/<folder>/ von jedem in PathRules\[\] unten). Für wen eine Seite ist:  
> ihr Front Matter (audience: editor, admin), sonst die erste passende Regel aus PathRules\[\],  
> sonst ihr Dateiname (...-developer.md) oder ihre ersten Zeilen ("for editors"), sonst TypeDefaults\[<type>\].  
> Die Reihenfolge hier ist die der Zielgruppenkarten des Dashboards und der Suchfilter.  

Standard: eine Liste mit 19: `editor`, `admin`, `developer`, `user`, `project-manager`, `sales-marketing`, `partner`, `advocate`, `designer`, `hosting`, `app-creator`, `website-creator` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[AudienceSettings]
# ein leeres "Audiences[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Audiences[]
Audiences[]=editor
Audiences[]=admin
Audiences[]=developer
Audiences[]=user
Audiences[]=project-manager
Audiences[]=sales-marketing
Audiences[]=partner
Audiences[]=advocate
Audiences[]=designer
Audiences[]=hosting
Audiences[]=app-creator
Audiences[]=website-creator
Audiences[]=supporter
Audiences[]=sponsor
Audiences[]=trainer
Audiences[]=press
Audiences[]=businesses
Audiences[]=government
Audiences[]=customer
*/ ?>
```

## AudienceNames

Standard: ein Hash mit 19: `editor=Editors`, `admin=Administrators`, `developer=Developers`, `user=Users`, `project-manager=Project managers`, `sales-marketing=Sales & marketing`, `partner=Partners`, `advocate=Advocates`, `designer=Designers`, `hosting=Hosting companies`, `app-creator=Application creators`, `website-creator=Website creators` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[AudienceSettings]
# ein leeres "AudienceNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AudienceNames[]
AudienceNames[editor]=Editors
AudienceNames[admin]=Administrators
AudienceNames[developer]=Developers
AudienceNames[user]=Users
AudienceNames[project-manager]=Project managers
AudienceNames[sales-marketing]=Sales & marketing
AudienceNames[partner]=Partners
AudienceNames[advocate]=Advocates
AudienceNames[designer]=Designers
AudienceNames[hosting]=Hosting companies
AudienceNames[app-creator]=Application creators
AudienceNames[website-creator]=Website creators
AudienceNames[supporter]=Supporters
AudienceNames[sponsor]=Sponsors
AudienceNames[customer]=Customers
AudienceNames[government]=Governments
AudienceNames[businesses]=Businesses
AudienceNames[press]=Press, media and influencers
AudienceNames[trainer]=Trainers
*/ ?>
```

## AudienceIcons

> Symbolnamen; jeder muss eine Klasse .a4-bi-<name> in stylesheets/components.css haben  

Standard: ein Hash mit 19: `editor=pencil`, `admin=shield-lock`, `developer=code-square`, `user=person`, `project-manager=diagram-3`, `sales-marketing=graph-up-arrow`, `partner=building`, `advocate=people`, `designer=palette`, `hosting=database`, `app-creator=phone`, `website-creator=globe` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[AudienceSettings]
# ein leeres "AudienceIcons[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AudienceIcons[]
AudienceIcons[editor]=pencil
AudienceIcons[admin]=shield-lock
AudienceIcons[developer]=code-square
AudienceIcons[user]=person
AudienceIcons[project-manager]=diagram-3
AudienceIcons[sales-marketing]=graph-up-arrow
AudienceIcons[partner]=building
AudienceIcons[advocate]=people
AudienceIcons[designer]=palette
AudienceIcons[hosting]=database
AudienceIcons[app-creator]=phone
AudienceIcons[website-creator]=globe
AudienceIcons[supporter]=star
AudienceIcons[sponsor]=piggy-bank
AudienceIcons[customer]=person-check
AudienceIcons[government]=bank
AudienceIcons[businesses]=briefcase
AudienceIcons[press]=megaphone
AudienceIcons[trainer]=easel
*/ ?>
```

## PathRules

> <pattern>;<audience,audience>, der erste Treffer gewinnt ("\*" passt auch auf "/")  

Standard: eine Liste mit 78: `doc/eng-us/help/manual/*;editor,admin,user`, `doc/eng-us/help/leadership/*;project-manager`, `doc/eng-us/help/business/*;sales-marketing`, `doc/eng-us/help/partners/*;partner`, `doc/eng-us/help/advocates/*;advocate`, `doc/eng-us/help/designers/*;designer`, `doc/eng-us/help/hosting/*;hosting`, `doc/eng-us/help/creators/app-creators/*;app-creator`, `doc/eng-us/help/creators/website-creators/*;website-creator`, `doc/eng-us/help/creators/*;app-creator,website-creator`, `doc/eng-us/help/supporters/*;supporter`, `doc/eng-us/help/sponsors/*;sponsor` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[AudienceSettings]
# ein leeres "PathRules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PathRules[]
PathRules[]=doc/eng-us/help/manual/*;editor,admin,user
PathRules[]=doc/eng-us/help/leadership/*;project-manager
PathRules[]=doc/eng-us/help/business/*;sales-marketing
PathRules[]=doc/eng-us/help/partners/*;partner
PathRules[]=doc/eng-us/help/advocates/*;advocate
PathRules[]=doc/eng-us/help/designers/*;designer
PathRules[]=doc/eng-us/help/hosting/*;hosting
PathRules[]=doc/eng-us/help/creators/app-creators/*;app-creator
PathRules[]=doc/eng-us/help/creators/website-creators/*;website-creator
PathRules[]=doc/eng-us/help/creators/*;app-creator,website-creator
PathRules[]=doc/eng-us/help/supporters/*;supporter
PathRules[]=doc/eng-us/help/sponsors/*;sponsor
PathRules[]=doc/eng-us/help/customers/*;customer
PathRules[]=doc/eng-us/help/government/*;government
PathRules[]=doc/eng-us/help/businesses/*;businesses
PathRules[]=doc/eng-us/help/press/*;press
PathRules[]=doc/eng-us/help/trainers/*;trainer
PathRules[]=doc/eng-us/help/project/*;user,editor,admin,developer,partner,supporter
PathRules[]=doc/eng-us/help/build-two-ways/*;designer,website-creator,editor,admin,developer
PathRules[]=doc/eng-us/help/extension-development/*;developer,designer
# ... 58 weitere in der ausgelieferten Datei
*/ ?>
```

## TypeDefaults

> <type> => <audience,audience>, wenn sonst nichts etwas sagt  
> die eigene Dokumentation einer aktiven Erweiterung (extension/<name>/doc, ihre README ...)  

Standard: ein Hash mit 14: `help=editor,user`, `guide=admin`, `feature=admin,developer`, `bc=developer,admin`, `install=admin,developer`, `changelog=admin,developer`, `specification=developer`, `history=developer`, `example=developer,admin`, `generated=developer,admin`, `reference=developer,admin`, `project=developer,admin` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[AudienceSettings]
# ein leeres "TypeDefaults[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TypeDefaults[]
TypeDefaults[help]=editor,user
TypeDefaults[guide]=admin
TypeDefaults[feature]=admin,developer
TypeDefaults[bc]=developer,admin
TypeDefaults[install]=admin,developer
TypeDefaults[changelog]=admin,developer
TypeDefaults[specification]=developer
TypeDefaults[history]=developer
TypeDefaults[example]=developer,admin
TypeDefaults[generated]=developer,admin
TypeDefaults[reference]=developer,admin
TypeDefaults[project]=developer,admin
TypeDefaults[catalogue]=developer
TypeDefaults[extension]=editor,admin,developer
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
