---
title: ezjscore.ini [eZJSCore]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [eZJSCore] in ezjscore.ini (ezjscore): JavaScriptOptimizer, CssOptimizer, LoadFromCDN, PreferredLibrary, ExternalScripts, LocalScripts"
keywords: Einstellung, ezjscore.ini, eZJSCore, INI, setting, JavaScriptOptimizer, CssOptimizer, LoadFromCDN, PreferredLibrary, ExternalScripts, LocalScripts
type: generated
generated: exp:help:examples
---
# ezjscore.ini [eZJSCore]

Der Block `[eZJSCore]` von `extension/ezjscore/settings/ezjscore.ini` (Erweiterung ezjscore), mit 6 Einstellung(en).

Bearbeiten Sie `extension/ezjscore/settings/ezjscore.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezjscore.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezjscore.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezjscore.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## JavaScriptOptimizer

> Deaktiviert/aktiviert den Packer für js / css (zum Debuggen von Apache-Rewrite-Regeln)  
> Normalerweise der Einfachheit halber über \[TemplateSettings\]DevelopmentMode gesteuert,  
> kann aber auch gezielt über diese Einstellung gesteuert werden, wenn sie gesetzt ist.  
> Die Packer-Stufe mit einer Ganzzahl von 0 bis 3 statt \[dis|en\]abled erzwingen  
> Packer=disabled  

Standard: eine Liste mit 1: `ezjscJavascriptOptimizer`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[eZJSCore]
# ein leeres "JavaScriptOptimizer[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
JavaScriptOptimizer[]
JavaScriptOptimizer[]=ezjscJavascriptOptimizer
*/ ?>
```

## CssOptimizer

Standard: eine Liste mit 1: `ezjscCssOptimizer`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[eZJSCore]
# ein leeres "CssOptimizer[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CssOptimizer[]
CssOptimizer[]=ezjscCssOptimizer
*/ ?>
```

## LoadFromCDN

> # Einstellungen der CDN-Skripte (jQuery-Integration)  
> Die folgenden Einstellungen in diesem Block gehören zur jQuery-Integration  
> von ezjscore und werden von den Aufrufen ezjsc::jquery verwendet.  
> Sie müssen sie nur ändern, wenn Sie:  
> - diese Skripte lokal laden müssen (Intranet / ohne Abhängigkeit von einer Internetverbindung)  
> - Unterstützung für eine andere JS-Bibliothek oder ein anderes Framework hinzufügen wollen  
> - die Bibliothek selbst aktualisieren wollen  
> Ihre eigenen JS-Skripte müssen Sie hiermit nicht einrichten; es genügt, sie direkt  
> über {ezscript\[\_require\]('somescript.js')} oder  
> {ezscript\_require(array('ezjsc::jquery', 'somescript\_that\_uses\_jquery.js'))} einzubinden  
> Das Laden der jQuery-Dateien von externen Servern aktivieren / deaktivieren  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[eZJSCore]
LoadFromCDN=disabled
*/ ?>
```

## PreferredLibrary

> Bevorzugte Bibliothek (jquery)  
> Dies ist ein Hinweis für Erweiterungen, die ezjscore verwenden,  
> damit sie wahlweise beide unterstützen und anhand dieser Einstellung wählen können  

Standard: `jquery`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[eZJSCore]
PreferredLibrary=jquery
*/ ?>
```

## ExternalScripts

> Liste externer JavaScript-Bibliotheken für jQuery mit ihrer CDN-URL  
> Beginnt sie mit ://, wird http oder https gewählt, je nachdem, ob die Seite über https ausgeliefert wird  
> Sicherheitshärtung (H-06, CWE-829/319): Verwenden Sie https für externe Skripte, damit das  
> JavaScript der Administration und des Frontends auf dem Netzwerkweg nicht manipuliert werden kann. Lassen Sie  
> am besten LoadFromCDN=disabled (lokale Skripte) oder ergänzen Sie Subresource Integrity.  
> jQuery: dieselben Releases aus dem CDN wie die lokalen Dateien unten (die CDN-Einträge nannten früher  
> jQuery 1.10.2 und jQuery UI 1.10.3, älter als die lokalen Kopien).  

Standard: ein Hash mit 3: `jquery=https://code.jquery.com/jquery-4.0.0.min.js`, `jqueryMigrate=https://code.jquery.com/jquery-migrate-4.0.2.min.js`, `jqueryUI=https://code.jquery.com/ui/1.14.2/jquery-ui.min.js`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[eZJSCore]
# ein leeres "ExternalScripts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExternalScripts[]
ExternalScripts[jquery]=https://code.jquery.com/jquery-4.0.0.min.js
ExternalScripts[jqueryMigrate]=https://code.jquery.com/jquery-migrate-4.0.2.min.js
ExternalScripts[jqueryUI]=https://code.jquery.com/ui/1.14.2/jquery-ui.min.js
*/ ?>
```

## LocalScripts

> Liste lokaler JavaScript-Bibliotheken für jQuery; ein "/" am Anfang zeigt an, dass sie im  
> Wurzelverzeichnis des Design-Ordners liegen und nicht im Unterordner /javascript  
> ezjsc::jquery: jQuery 4.0.0, gefolgt von jQuery Migrate 4.0.2, das die von jQuery 4 entfernten APIs wiederherstellt, damit älterer  
> Code weiter funktioniert. Dieser Build ist still; um in der Browser-Konsole jeden alten Aufruf zu sehen, den er wiederherstellt, verwenden Sie den  
> meldenden Build, jquery-migrate-4.0.2.js. Lassen Sie jqueryMigrate leer, um jQuery 4 allein zu laden. Nur diese Releases  
> werden ausgeliefert: Die älteren Dateien von jQuery 3.7.1, Migrate 3.4.1 und jQuery UI 1.10.3 sind entfernt.  
> ezjsc::jqueryUI: jQuery UI 1.14.2, das jQuery 4 unterstützt  

Standard: ein Hash mit 3: `jquery=jquery-4.0.0.min.js`, `jqueryMigrate=jquery-migrate-4.0.2.min.js`, `jqueryUI=jquery-ui-1.14.2.min.js`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[eZJSCore]
# ein leeres "LocalScripts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LocalScripts[]
LocalScripts[jquery]=jquery-4.0.0.min.js
LocalScripts[jqueryMigrate]=jquery-migrate-4.0.2.min.js
LocalScripts[jqueryUI]=jquery-ui-1.14.2.min.js
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezjscore.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/settings/ezjscore.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
