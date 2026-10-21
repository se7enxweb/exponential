---
title: package.ini [LicenseSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LicenseSettings] in package.ini: DefaultLicense, GroupList, LicenseList, AliasList"
keywords: Einstellung, package.ini, LicenseSettings, INI, setting, DefaultLicense, GroupList, LicenseList, AliasList
type: generated
generated: exp:help:examples
---
# package.ini [LicenseSettings]

Der Block `[LicenseSettings]` von `settings/package.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultLicense

> Die Lizenzen, die ein Paket in den Assistenten zum Erstellen von Paketen erhalten kann  
> (Setup > Pakete > Neues Paket erstellen, der Schritt „Paketinformationen“ jedes Erstellers). Die Lizenz ist  
> eine feste Auswahl aus dieser Liste: Der Schritt zeigt ein  
> Auswahlfeld und lehnt jeden Wert ab, der hier nicht steht.  
>
> Der in der package.xml eines Pakets gespeicherte Wert (<licence>) ist der  
> Bezeichner der Lizenz. Verwenden Sie den SPDX-Bezeichner, wo es einen gibt (https://spdx.org/licenses/),  
> sonst einen Bezeichner im SPDX-Stil "LicenseRef-<name>".  
>
> Der Bezeichner, der beim Öffnen des Schritts gewählt ist. Muss einer aus LicenseList sein.  

Standard: `GPL-2.0-or-later`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[LicenseSettings]
DefaultLicense=GPL-2.0-or-later
*/ ?>
```

## GroupList

> Die Gruppen des Auswahlfelds (<optgroup>), in der angezeigten Reihenfolge. Jede Gruppe hat einen  
> Block \[LicenseGroup\_<group>\] mit Name=, der Überschrift der Gruppe. Eine Lizenz, deren  
> Group= hier nicht steht, erscheint am Ende unter „Andere Lizenzen“.  

Standard: eine Liste mit 9: `software`, `documentation`, `cc-4.0`, `cc-3.0`, `cc-2.5`, `cc-2.0`, `cc-1.0`, `public-domain`, `proprietary`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[LicenseSettings]
# ein leeres "GroupList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupList[]
GroupList[]=software
GroupList[]=documentation
GroupList[]=cc-4.0
GroupList[]=cc-3.0
GroupList[]=cc-2.5
GroupList[]=cc-2.0
GroupList[]=cc-1.0
GroupList[]=public-domain
GroupList[]=proprietary
*/ ?>
```

## LicenseList

> Die wählbaren Lizenzen, in der Reihenfolge, in der sie innerhalb ihrer Gruppe angezeigt werden. Jede  
> hat einen Block \[License\_<identifier>\]:  
>   Name=         der offizielle Name der Lizenz (so angezeigt, wie er ist, nicht übersetzt)  
>   URL=          die offizielle Seite der Lizenz  
>   Group=        die Gruppe, unter der sie aufgeführt wird (siehe GroupList)  
>   Description=  optional, ein kurzer Text unter dem Auswahlfeld  
>
> Um eine Lizenz hinzuzufügen, in settings/override/package.ini.append.php:  
>   \[LicenseSettings\]  
>   LicenseList\[\]=Apache-2.0  
>   \[License\_Apache-2.0\]  
>   Name=Apache License 2.0  
>   URL=https://www.apache.org/licenses/LICENSE-2.0  
>   Group=software  
>
> Um die ganze Liste zu ersetzen, beginnen Sie im Override mit einer leeren Zeile LicenseList\[\]  
> und führen nur die gewünschten Lizenzen auf (und setzen DefaultLicense auf eine  
> davon).  

Standard: eine Liste mit 46: `GPL-2.0-or-later`, `GPL-2.0-only`, `GPL-3.0-or-later`, `GPL-3.0-only`, `LGPL-2.1-or-later`, `LGPL-2.1-only`, `LGPL-3.0-or-later`, `LGPL-3.0-only`, `AGPL-3.0-or-later`, `AGPL-3.0-only`, `MIT`, `GFDL-1.3-or-later` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[LicenseSettings]
# ein leeres "LicenseList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LicenseList[]
LicenseList[]=GPL-2.0-or-later
LicenseList[]=GPL-2.0-only
LicenseList[]=GPL-3.0-or-later
LicenseList[]=GPL-3.0-only
LicenseList[]=LGPL-2.1-or-later
LicenseList[]=LGPL-2.1-only
LicenseList[]=LGPL-3.0-or-later
LicenseList[]=LGPL-3.0-only
LicenseList[]=AGPL-3.0-or-later
LicenseList[]=AGPL-3.0-only
LicenseList[]=MIT
LicenseList[]=GFDL-1.3-or-later
LicenseList[]=GFDL-1.3-only
LicenseList[]=CC-BY-4.0
LicenseList[]=CC-BY-SA-4.0
LicenseList[]=CC-BY-ND-4.0
LicenseList[]=CC-BY-NC-4.0
LicenseList[]=CC-BY-NC-SA-4.0
LicenseList[]=CC-BY-NC-ND-4.0
LicenseList[]=CC-BY-3.0
# ... 26 weitere in der ausgelieferten Datei
*/ ?>
```

## AliasList

> Andere Namen, unter denen eine gespeicherte Lizenz bekannt ist, einem Bezeichner aus LicenseList zugeordnet.  
> Pakete, die erstellt wurden, bevor die Lizenz wählbar wurde, speichern "GPL", was immer  
> die GNU GPL Version 2 oder (nach Ihrer Wahl) jede spätere Version bedeutete, die Lizenz  
> von Exponential selbst. Ein Alias wird als die Lizenz angezeigt, für die er steht, und ein  
> abgeschickter Alias wird als Bezeichner dieser Lizenz gespeichert.  

Standard: ein Hash mit 1: `GPL=GPL-2.0-or-later`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[LicenseSettings]
# ein leeres "AliasList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AliasList[]
AliasList[GPL]=GPL-2.0-or-later
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
