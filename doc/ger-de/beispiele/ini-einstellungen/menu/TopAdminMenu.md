---
title: menu.ini [TopAdminMenu]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TopAdminMenu] in menu.ini: HiddenTabs, SetupTabs, MegaMenuTabs, Tabs"
keywords: Einstellung, menu.ini, TopAdminMenu, INI, setting, HiddenTabs, SetupTabs, MegaMenuTabs, Tabs
type: generated
generated: exp:help:examples
---
# menu.ini [TopAdminMenu]

Der Block `[TopAdminMenu]` von `settings/menu.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## HiddenTabs

> Reiter, die das Hauptmenü weglässt, egal was Tabs\[\] sagt: Ein Siteaccess kann keinen Reiter  
> aus Tabs\[\] herausnehmen, den eine Erweiterung anhängt (die Einstellungen der Erweiterungen werden nach denen  
> des Siteaccess gelesen). Der Siteaccess editor blendet damit die Reiter der Administration aus.  
>
> Weniger Reiter, nichts verloren (admin4, doc/eng-us/features/6.0/admin4-design.md "Moving a tab into Setup"):  
> Design und Audit haben eine Gruppe im Setup-Menü und in der Übersicht (\[Leftmenu\_setup\] GroupTabs\[\]),  
> nur angezeigt, solange ihr Reiter nicht im Hauptmenü ist; ihre Seiten markieren dann den Reiter Setup und  
> zeichnen das Setup-Menü. Ohne den Reiter Dashboard führt das Logo zum Dashboard. In einer  
> menu.ini.append.php eines Siteaccess oder eines Overrides:  
>   HiddenTabs\[\]  
>   HiddenTabs\[\]=dashboard  
>   HiddenTabs\[\]=design  
>   HiddenTabs\[\]=audit  
> oder wiederholen Sie die ganze Liste (Tabs\[\] ohne Wert, dann die zu behaltenden Reiter); der Reiter einer Erweiterung  
> lässt sich nur mit HiddenTabs\[\] weglassen.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopAdminMenu]
# ein leeres "HiddenTabs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HiddenTabs[]
*/ ?>
```

## SetupTabs

> Reiter, die für einen Benutzer, der die Setup-Übersicht (setup/menu) öffnen darf, in das Setup-Menü wandern:  
> Ein solcher Benutzer findet die Gruppe des Reiters dort (\[Leftmenu\_setup\] GroupTabs\[\]) und hat keinen Reiter; ein Benutzer,  
> der die Übersicht nicht öffnen darf (eine Rolle, die nur die eigene Richtlinie des Reiters hält), behält den Reiter.  
> Standardmäßig leer, z. B. SetupTabs\[\]=audit in einer menu.ini.append.php eines Siteaccess oder eines Overrides.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopAdminMenu]
# ein leeres "SetupTabs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SetupTabs[]
*/ ?>
```

## MegaMenuTabs

> admin4 (Prototyp): Reiter, die ein Mega-Menü erhalten, ein Panel unter dem Kopf mit den Gruppen und  
> Links des Menüs des Reiters, geöffnet über eine kleine Schaltfläche neben dem Reiter (der Reiter bleibt ein Link).  
> Ein Reiter nimmt teil, wenn sein \[Topmenu\_<tab>\] ein MegaMenuTemplate nennt: setup (das Setup-Menü,  
> \[Leftmenu\_setup\]), users (\[Leftmenu\_user\_mega\]) und dashboard (\[Leftmenu\_my\_mega\]) tun das; ein Name ohne eines wird  
> ignoriert (im Debug-Bericht genannt). Das  
> Panel enthält die Links des Menüs mit ihren Namen, Symbolen (LinkIcons\[\]), Beschreibungen  
> (LinkDescriptions\[\]) und Zugriffsprüfungen, eine kurze Auswahl davon (MegaMenuLinks\[\] des Menü-  
> abschnitts), geladen von user/megamenu/<tab>, wenn es zum ersten Mal geöffnet wird. Ein Reitername besteht aus Klein-  
> buchstaben, Ziffern und \_; ein Reiter mit Mega-Menü sollte zugriffsgeprüft sein (PolicyList\[\] oder seine URL unter  
> CheckViewAccess). Angezeigt ab 576px, wo  
> das Hauptmenü eine Zeile ist (nicht im Telefonmenü). Standardmäßig leer: keine Schaltfläche, kein Skript. Zum Einschalten, in einer  
> menu.ini.append.php eines Siteaccess oder eines Overrides:  
>   MegaMenuTabs\[\]=setup  
>   MegaMenuTabs\[\]=users  
>   MegaMenuTabs\[\]=dashboard  
> (doc/eng-us/features/6.0/admin4-design.md, "Mega menu")  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopAdminMenu]
# ein leeres "MegaMenuTabs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MegaMenuTabs[]
*/ ?>
```

## Tabs

> Diese Liste enthält die Einträge des Hauptmenüs in der Administrationsoberfläche  
> Seit 4.3 standardmäßig ausgeblendet  
> Tabs\[\]=shop  
> Seit 6.0 standardmäßig eingeblendet  
> Seit 4.3 standardmäßig ausgeblendet  
> Tabs\[\]=design  
> Seit 6.0.6 standardmäßig eingeblendet  
> Die Audit-Konsole (doc/eng-us/bc/6.0/audit.md): nur mit der Richtlinie audit/read angezeigt  

Standard: eine Liste mit 8: `dashboard`, `content`, `media`, `users`, `shop`, `design`, `setup`, `audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[TopAdminMenu]
# ein leeres "Tabs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Tabs[]
Tabs[]=dashboard
Tabs[]=content
Tabs[]=media
Tabs[]=users
Tabs[]=shop
Tabs[]=design
Tabs[]=setup
Tabs[]=audit
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
