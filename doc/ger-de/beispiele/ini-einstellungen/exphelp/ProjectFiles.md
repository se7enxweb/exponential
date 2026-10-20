---
title: exphelp.ini [ProjectFiles]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ProjectFiles] in exphelp.ini: Enabled, Include, Exclude, Category, Type, Audiences, AudienceRules, LicenseName ..."
keywords: Einstellung, exphelp.ini, ProjectFiles, INI, setting, Enabled, Include, Exclude, Category, Type, Audiences, AudienceRules, LicenseName, LicensePage
type: generated
generated: exp:help:examples
---
# exphelp.ini [ProjectFiles]

Der Block `[ProjectFiles]` von `settings/exphelp.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Die Projektdateien (expHelpProjectFiles; doc/eng-us/features/6.0/help-system.md, "Project files"): die Markdown-Dateien  
> des Repositorys außerhalb von doc/, die das Projekt selbst dokumentieren (README.md, INSTALL.md, SECURITY.md,  
> CONTRIBUTING.md, CODE\_OF\_CONDUCT.md, COPYRIGHT.md, LICENSE.md, die README.md der Verzeichnisse bin/, design/ und kernel/).  
> Jede hat die ID project/<Pfad im Repository> (/exphelp/viewer/project/README.md;  
> /exphelp/viewer/README.md leitet dorthin weiter) und  
> ist eine Seite auf der obersten Ebene des Seitenbaums, neben den Ordnern, nicht in einem eigenen Ordner. Sie sind nur englisch und  
> werden nicht übersetzt, wie doc/changelogs/: Ein Leser einer anderen Sprache sieht sie mit dem Hinweis, dass die Seite nicht  
> übersetzt ist.  
> Durch keine Einstellung hier oder in einem Override änderbar: Es werden nur Dateien angezeigt, die git versioniert (der git-Index, "git  
> ls-files" oder das Release-Manifest, nie das Verzeichnis: eine private Notiz in der Wurzel, die nicht committet ist, wird nie  
> angezeigt, durchsucht oder verlinkt), nie eine Datei an einer Stelle, die .gitignore ignoriert, nur Markdown, nie eine versteckte Datei oder eine Datei  
> in einem versteckten Verzeichnis (.github/ eingeschlossen) und nie unter doc/, extension/ (die Dokumentation der Erweiterungen ist die  
> Kategorie Erweiterungen), irgendeinem Verzeichnis tests/, var/, vendor/, settings/override/, settings/siteaccess/ oder  
> node\_modules/.  
> enabled: Die Hilfe zeigt sie; disabled: keine  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
Enabled=enabled
*/ ?>
```

## Include

> Shell-Muster auf den Pfad im Repository: "\*" bleibt innerhalb eines Verzeichnisses, "\*\*" überquert Verzeichnisse  

Standard: eine Liste mit 4: `*.md`, `bin/**/README.md`, `design/*/README.md`, `kernel/**/README.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
# ein leeres "Include[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Include[]
Include[]=*.md
Include[]=bin/**/README.md
Include[]=design/*/README.md
Include[]=kernel/**/README.md
*/ ?>
```

## Exclude

> Ausgelassen, obwohl Include\[\] passt: Testdaten und die Kopien anderer Projekte (die README einer JavaScript-Bibliothek)  

Standard: eine Liste mit 5: `**/fixtures/**`, `**/_fixtures/**`, `**/javascript/**`, `**/third-party/**`, `**/thirdparty/**`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
# ein leeres "Exclude[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Exclude[]
Exclude[]=**/fixtures/**
Exclude[]=**/_fixtures/**
Exclude[]=**/javascript/**
Exclude[]=**/third-party/**
Exclude[]=**/thirdparty/**
*/ ?>
```

## Category

> Ihre Kategorie, ihr Seitentyp und ihre Zielgruppen (\[CategorySettings\], \[TypeSettings\], \[AudienceSettings\]); das eigene Front  
> Matter einer Datei gewinnt. AudienceRules\[\]: "<pattern>;<audiences>", der erste Treffer gewinnt, sonst Audiences\[\]  

Standard: `project-files`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
Category=project-files
*/ ?>
```

## Type

Standard: `project`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
Type=project
*/ ?>
```

## Audiences

Standard: eine Liste mit 2: `developer`, `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
# ein leeres "Audiences[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Audiences[]
Audiences[]=developer
Audiences[]=admin
*/ ?>
```

## AudienceRules

Standard: eine Liste mit 7: `README.md;admin,developer,editor,user,partner,hosting`, `INSTALL.md;admin,developer,hosting`, `SECURITY.md;admin,developer,hosting`, `CONTRIBUTING.md;developer,supporter,advocate,partner`, `CODE_OF_CONDUCT.md;developer,supporter,advocate,partner,trainer,user`, `COPYRIGHT.md;admin,developer,partner,businesses`, `LICENSE.md;admin,developer,partner,businesses`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
# ein leeres "AudienceRules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AudienceRules[]
AudienceRules[]=README.md;admin,developer,editor,user,partner,hosting
AudienceRules[]=INSTALL.md;admin,developer,hosting
AudienceRules[]=SECURITY.md;admin,developer,hosting
AudienceRules[]=CONTRIBUTING.md;developer,supporter,advocate,partner
AudienceRules[]=CODE_OF_CONDUCT.md;developer,supporter,advocate,partner,trainer,user
AudienceRules[]=COPYRIGHT.md;admin,developer,partner,businesses
AudienceRules[]=LICENSE.md;admin,developer,partner,businesses
*/ ?>
```

## LicenseName

> Die Lizenz der Projektdateien: Der Hinweis unter jeder nennt sie und verlinkt ihre Seite (eine Seiten-ID)  

Standard: `GNU General Public License, version 2 (or any later version)`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
LicenseName=GNU General Public License, version 2 (or any later version)
*/ ?>
```

## LicensePage

Standard: `project/LICENSE.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ProjectFiles]
LicensePage=project/LICENSE.md
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
