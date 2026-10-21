---
title: site.ini [DebugSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DebugSettings] in site.ini: DebugOutput, AlwaysLog, DeprecatedLog, ScriptDebugOutput, DebugByIP, DebugIPList, DebugByUser, DebugUserIDList ..."
keywords: Einstellung, site.ini, DebugSettings, INI, setting, DebugOutput, AlwaysLog, DeprecatedLog, ScriptDebugOutput, DebugByIP, DebugIPList, DebugByUser, DebugUserIDList, Debug, DebugRedirection, DisplayDebugWarnings, DebugLogOnly, DebugToolbar, DisplayIncludedFiles, QuickSettingsList, UsedTemplateEditUrl
type: generated
generated: exp:help:examples
---
# site.ini [DebugSettings]

Der Block `[DebugSettings]` von `settings/site.ini`, mit 16 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DebugOutput

> Hauptschalter für das Debugging; wenn deaktiviert, wird kein Debugging angezeigt  
> Wenn aktiviert, entscheiden DebugByIP und DebugIPList, was geschieht.  
> Verwenden Sie enabled oder disabled  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DebugOutput=disabled
*/ ?>
```

## AlwaysLog

> Steuert, welche Debug-Typen immer protokolliert werden.  
> Das stellt die Protokollierung sicher, auch wenn die Debug-Ausgabe aus ist  
>
> Verfügbare Debug-Typen sind:  
> error - für schwere Fehler, sehr wichtig zu protokollieren  
> warning - kleinere Fehler, wichtig für die Entwicklung, aber nicht für eine Live-Website  
> debug - vorübergehendes Debugging, meist nur im Webbrowser wichtig  
> notice - allgemeine Hinweise; das Aktivieren kann die Logdateien schnell füllen  
> strict - strikte Hinweise, schlagen Änderungen an Ihrem Code für beste Interoperabilität und Zukunftssicherheit vor  
> deprecated - Veraltungen von PHP (mit DeprecatedLog=enabled), jede eindeutige Meldung einmal pro Anfrage  

Standard: eine Liste mit 1: `error`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
# ein leeres "AlwaysLog[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AlwaysLog[]
AlwaysLog[]=error
*/ ?>
```

## DeprecatedLog

> Veraltungen von PHP (E\_DEPRECATED, E\_USER\_DEPRECATED, eZDebug::writeDeprecated()).  
> enabled: in var/log/deprecated.log geschrieben, jede eindeutige Meldung einmal pro  
> Anfrage, und in der Debug-Ausgabe nur gezählt (die Zusammenfassung der Debug-Leiste und  
> die Zahl der Warnungen); keine wird im Speicher gehalten oder mit den Meldungen aufgeführt.  
> disabled: Warnungen in warning.log und der Debug-Ausgabe, wie bisher.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DeprecatedLog=enabled
*/ ?>
```

## ScriptDebugOutput

> Debug-Schalter für Skripte, wirkt nur, wenn DebugOutput auf enabled steht  
> Standardmäßig aus, um für Skripte kein Debugging auszugeben.  
> Verwenden Sie enabled oder disabled  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
ScriptDebugOutput=disabled
*/ ?>
```

## DebugByIP

> Ob das Debugging pro IP-Adresse gesetzt wird. Wenn enabled, verwendet es  
> DebugIPList für gültige IPs, sonst erhalten alle IPs Debugging.  
> Verwenden Sie enabled oder disabled  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DebugByIP=disabled
*/ ?>
```

## DebugIPList

> Ein Array mit IP-Adressen oder Netzen, die Debug-Informationen erhalten  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
# ein leeres "DebugIPList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DebugIPList[]
*/ ?>
```

## DebugByUser

> Beispiele  
> DebugIPList\[\]=1.2.3.4  
> DebugIPList\[\]=192.0.0.42  
> DebugIPList\[\]=192.0.0.0/27  
> Ob das Debugging pro Benutzer-ID gesetzt wird. Wenn enabled, erhalten nur Benutzer  
> mit IDs (in DebugUserIDList) Debugging.  
> Verwenden Sie enabled oder disabled  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DebugByUser=disabled
*/ ?>
```

## DebugUserIDList

> Ein Array mit Benutzer-IDs, die Debug-Informationen erhalten  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
# ein leeres "DebugUserIDList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DebugUserIDList[]
*/ ?>
```

## Debug

> Beispiele  
> DebugUserIDList\[\]=14  
> Steuert, wie das Debugging angezeigt wird: inline bedeutet, dass das Debugging  
> in der Seite selbst angezeigt wird, popup öffnet ein neues Popup-Fenster mit dem Debugging.  
> Das Popup-Fenster funktioniert eventuell nicht in allen Browsern.  
> Verwenden Sie disabled, inline oder popup  
> Wenn Sie popup verwenden, muss es eine Rewrite-Regel für .html-Dateien geben,  
> oder Ihre Website muss im Modus ohne virtuellen Host laufen. Außerdem haben manche Browser  
> Probleme mit dieser Funktion.  

Standard: `inline`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
Debug=inline
*/ ?>
```

## DebugRedirection

> Steuert, ob Weiterleitungen debuggbar sein sollen; auf enable setzen, um eine Weiterleitungs-  
> seite mit Debug-Informationen zu erhalten.  
> Nützlich für die Entwicklung; Produktivsysteme sollten es aus haben  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DebugRedirection=disabled
*/ ?>
```

## DisplayDebugWarnings

> Ob Debug-Warnungen/-Fehler auf der Seite angezeigt werden.  
> Wenn nicht, erscheinen sie nur im Debug-Log.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DisplayDebugWarnings=disabled
*/ ?>
```

## DebugLogOnly

> Ob das Anzeigen von Debug-Zeichenketten in der Debug-Ausgabe übersprungen wird.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DebugLogOnly=disabled
*/ ?>
```

## DebugToolbar

> Entwickler-Werkzeugleiste mit Funktionen zum Leeren des Caches und für Schnelleinstellungen  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DebugToolbar=enabled
*/ ?>
```

## DisplayIncludedFiles

> Liste der eingebundenen PHP-Dateien anzeigen, die zum Rendern der aktuellen Seite verwendet wurden  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DisplayIncludedFiles=disabled
*/ ?>
```

## QuickSettingsList

> Liste der Schnelleinstellungen für die Entwickler-Werkzeugleiste  
> Sie können jede Einstellung mit enabled/disabled verwenden  

Standard: eine Liste mit 6: `DebugSettings;DebugOutput;site.ini;Debug output`, `DebugSettings;DebugRedirection;site.ini;Debug redirection`, `TemplateSettings;Debug;site.ini;Template debug`, `TemplateSettings;ShowXHTMLCode;site.ini;Inline template debug`, `TemplateSettings;ShowUsedTemplates;site.ini;List of used templates`, `DatabaseSettings;SQLOutput;site.ini;SQL debug output`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
# ein leeres "QuickSettingsList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
QuickSettingsList[]
QuickSettingsList[]=DebugSettings;DebugOutput;site.ini;Debug output
QuickSettingsList[]=DebugSettings;DebugRedirection;site.ini;Debug redirection
QuickSettingsList[]=TemplateSettings;Debug;site.ini;Template debug
QuickSettingsList[]=TemplateSettings;ShowXHTMLCode;site.ini;Inline template debug
QuickSettingsList[]=TemplateSettings;ShowUsedTemplates;site.ini;List of used templates
QuickSettingsList[]=DatabaseSettings;SQLOutput;site.ini;SQL debug output
*/ ?>
```

## UsedTemplateEditUrl

> Der Link des Symbols „Bearbeiten“ jedes Templates in der Liste der verwendeten  
> Templates der Debug-Ausgabe (TemplateSettings ShowUsedTemplates), als sprintf()-Format:  
>  %1$s die Adresse von visual/templateedit im aktuellen Siteaccess, gefolgt  
>       von der Template-Datei, z. B. /visual/templateedit/design/standard/templates/page.tpl  
>  %2$s der aktuelle Siteaccess  
>  %3$s die Template-Datei, zum Beispiel design/standard/templates/page.tpl  
> Leer (der Standard): %1$s/(siteAccess)/%2$s, der Link, den die Liste immer hatte.  
> Beispiel, der Editor des Administrations-Siteaccess aus einem öffentlichen Siteaccess:  
> UsedTemplateEditUrl=/admin/visual/templateedit/%3$s/(siteAccess)/%2$s  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
UsedTemplateEditUrl=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
