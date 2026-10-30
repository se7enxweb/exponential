---
title: site.ini [TemplateSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TemplateSettings] in site.ini: AutoloadPathList, ExtensionAutoloadPath, Debug, ShowXHTMLCode, ShowMethodDebug, ShowUsedTemplates, ShowTemplatePathComments, TemplatesWithoutDebugComments ..."
keywords: Einstellung, site.ini, TemplateSettings, INI, setting, AutoloadPathList, ExtensionAutoloadPath, Debug, ShowXHTMLCode, ShowMethodDebug, ShowUsedTemplates, ShowTemplatePathComments, TemplatesWithoutDebugComments, NodeTreeCaching, TemplateCompile, ShareCompiledTemplates, SharedCompiledTemplatesDir, TemplateOptimization, UseFormatting, TemplateCache, DelayedCacheBlockCleanup, TemplateCompression, DevelopmentMode, CompileComments, CompileAccumulators, CompileTimingPoints, CompileResourceFallback, CompileNodePlacements, CompileExecution, CompileAlwaysGenerate, CompileIncludeNodeTree
type: generated
generated: exp:help:examples
---
# site.ini [TemplateSettings]

Der Block `[TemplateSettings]` von `settings/site.ini`, mit 26 Einstellung(en).

> AllowedDeletionDirs\[\]=/var/share/somedir  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AutoloadPathList

> Eine Liste von Verzeichnissen, in denen nach Dateien eztemplateautoload.php gesucht wird  
> Diese Dateien werden verwendet, um Template-Funktionen und -Operatoren automatisch zu laden  
> Sie können neue hinzufügen, wenn Sie eigenen Template-Code erstellen.  

Standard: eine Liste mit 6: `lib/eztemplate/classes/`, `kernel/common/`, `lib/ezpdf/classes/`, `lib/smarty/classes/`, `lib/twig/classes/`, `kernel/private/eztemplate/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
# ein leeres "AutoloadPathList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AutoloadPathList[]
AutoloadPathList[]=lib/eztemplate/classes/
AutoloadPathList[]=kernel/common/
AutoloadPathList[]=lib/ezpdf/classes/
AutoloadPathList[]=lib/smarty/classes/
AutoloadPathList[]=lib/twig/classes/
AutoloadPathList[]=kernel/private/eztemplate/
*/ ?>
```

## ExtensionAutoloadPath

> Eine Liste der Erweiterungen, die Template-Autoloads haben.  
> Geben Sie nur den Namen der Erweiterung an, nicht den Pfad.  
> Die Erweiterung muss ein Unterverzeichnis namens autoloads enthalten.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
# ein leeres "ExtensionAutoloadPath[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionAutoloadPath[]
*/ ?>
```

## Debug

> enabled, um zu sehen, welche Template-Dateien geladen werden, oder disabled, um das Debugging zu unterdrücken  
> Warnung: Fügt Ihrem Quelltext Debug-Kommentare in XHTML hinzu, auch in Mails!  
> Hinweis: Kein Debugging bei Templates, die mit <!DOCTYPE beginnen, um den Quirks-Modus nicht auszulösen!  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
Debug=disabled
*/ ?>
```

## ShowXHTMLCode

> Wenn aktiviert, wird Code hinzugefügt, der den Template-Namen im Browser anzeigt  
> Ist Debug deaktiviert, geschieht nichts  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
ShowXHTMLCode=enabled
*/ ?>
```

## ShowMethodDebug

> Ob beim Verarbeiten von Knoten das Debugging von Funktionen und Operatoren  
> angezeigt wird.  
> Das ist nur für Kernel-Entwickler gedacht, um zu prüfen, welche  
> Operatoren und Funktionen aufgerufen werden.  
> Hinweis: Funktioniert nur, wenn der Template-Compiler aus ist  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
ShowMethodDebug=disabled
*/ ?>
```

## ShowUsedTemplates

> Wenn aktiviert, wird eine Tabelle der zum Rendern einer Seite verwendeten Templates hinzugefügt.  
> DebugOutput sollte ebenfalls aktiviert sein.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
ShowUsedTemplates=disabled
*/ ?>
```

## ShowTemplatePathComments

> Wenn aktiviert, wird jedes gerenderte Template in HTML-Kommentare eingeschlossen, die seinen Pfad nennen,  
> z. B. <!-- START design/media/templates/pagelayout/footer.tpl --> ... <!-- STOP ... -->  
> Das entspricht der Debug-Ausgabe von Symfony/Twig und macht das Debuggen von Layouts und das Verfolgen von Blöcken  
> in der Legacy-Pipeline möglich. Funktioniert mit ein- oder ausgeschaltetem Template-Compiler.  
> Nur für die Entwicklung: Es fügt in die Ausgabe jedes Templates Markup ein, muss also in Produktion  
> und in jedem Siteaccess, der JSON, XML oder RSS ausliefert, deaktiviert bleiben.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
ShowTemplatePathComments=disabled
*/ ?>
```

## TemplatesWithoutDebugComments

> Templates that Debug and ShowTemplatePathComments never wrap in comments, by their path below templates/  
> (a file of that path in any design or extension), like  
> <h1 class="title"{include uri='design:parts/exp\_content\_direction.tpl' ...}>, which prints into a start tag.  
> Most templates need no entry: the comments are taken out of every rendered output where they would break it  
> (inside a tag or an attribute value, inside <script>, <style>, <title> and <textarea>, and everywhere in output  
> that is not HTML: XML, JSON, plain text), and a set-block that captures a value rather than HTML (a name, a URL,  
> attributes, nothing) gets no comments. List a template whose output reaches the page some other way, e.g. through  
> an operator that changes it before it is printed.  

Standard: eine Liste mit 1: `parts/exp_content_direction.tpl`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
# ein leeres "TemplatesWithoutDebugComments[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TemplatesWithoutDebugComments[]
TemplatesWithoutDebugComments[]=parts/exp_content_direction.tpl
*/ ?>
```

## NodeTreeCaching

> Legt fest, ob der interne Knotenbaum gecacht wird; das reduziert das Laden  
> und Parsen von Templates deutlich.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
NodeTreeCaching=disabled
*/ ?>
```

## TemplateCompile

> Legt fest, ob die Templates zu PHP-Code kompiliert werden; dann entfallen das Laden  
> und Parsen der Templates, und die Template-Verarbeitung wird deutlich reduziert.  
> Hinweis: Das erste Kompilieren der Templates dauert lange; verwenden Sie das  
>       Skript bin/php/eztc.php, um alle Ihre Templates vorzubereiten.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
TemplateCompile=enabled
*/ ?>
```

## ShareCompiledTemplates

> Ob Sie kompilierte Templates mit anderen Siteaccesses teilen  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
ShareCompiledTemplates=disabled
*/ ?>
```

## SharedCompiledTemplatesDir

> Wo gemeinsam genutzte kompilierte Templates gespeichert werden  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
SharedCompiledTemplatesDir=
*/ ?>
```

## TemplateOptimization

> Steuert, ob an kompilierten Templates weitere Optimierungen  
> vorgenommen werden  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
TemplateOptimization=enabled
*/ ?>
```

## UseFormatting

> Steuert, ob kompilierte Templates Formatierung enthalten.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
UseFormatting=disabled
*/ ?>
```

## TemplateCache

> Steuert alle auf Templates beruhenden Cache-Mechanismen; wenn deaktiviert, werden sie nie  
> verwendet.  
> Die Elemente, die dies derzeit steuert, sind:  
> - cache-block  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
TemplateCache=enabled
*/ ?>
```

## DelayedCacheBlockCleanup

> Steuert, ob abgelaufene Cache-Blöcke mit dem Parameter 'subtree\_expiry'  
> sofort von der Festplatte entfernt werden.  
> Wenn aktiviert, sollte das die Leistung großer Websites verbessern, Sie sollten abgelaufene  
> Caches dann aber von Hand oder mit dem Cronjob 'cronjobs/subtreeexpirycleanup.php' entfernen.  
> Seit 4.6 ist dieser Cronjob standardmäßig nicht aktiviert.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
DelayedCacheBlockCleanup=disabled
*/ ?>
```

## TemplateCompression

> Steuert, ob kompilierte Templates gzip-komprimiert auf der Festplatte gespeichert werden.  
> Das reduziert den Speicherbedarf drastisch.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
TemplateCompression=disabled
*/ ?>
```

## DevelopmentMode

> Steuert, ob der Entwicklungsmodus aktiviert ist.  
> Wenn aktiviert, führt das System mehr Prüfungen durch, etwa der Änderungszeit von  
> kompilierter gegenüber Quelldatei, und das Leeren kompilierter Template-Dateien wird seltener  
> nötig.  
> Hinweis: Live-Websites sollten dies nicht aktiviert haben, weil es die Dateizugriffe erhöht  
>       und langsamer sein kann.  
> Hinweis: Beim Umschalten dieser Einstellung müssen die kompilierten Template-Dateien geleert werden.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
DevelopmentMode=disabled
*/ ?>
```

## CompileComments

> HINWEIS:  
> Die folgenden Einstellungen dienen nur der Entwicklung der Template-Kompilierung  
> und sind für normale Websites nicht nützlich.  
>
> Ob Kommentare in die Cache-Dateien aufgenommen werden.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileComments=disabled
*/ ?>
```

## CompileAccumulators

> Ob Debug-Akkumulatoren eingebunden werden.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileAccumulators=disabled
*/ ?>
```

## CompileTimingPoints

> Ob Debug-Zeitpunkte eingebunden werden.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileTimingPoints=disabled
*/ ?>
```

## CompileResourceFallback

> Ob Ausweichcode für die Behandlung von Ressourcen verwendet wird  
> Sollte nicht nötig sein, außer man muss den Kompilierungsprozess debuggen  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileResourceFallback=disabled
*/ ?>
```

## CompileNodePlacements

> Wenn deaktiviert, enthält die Template-Kompilierung keine Positionsangaben für Knoten  
> Die Positionsangaben dienen dazu, Zeilen- und Spaltennummern für Warnungen und Fehler anzuzeigen.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileNodePlacements=enabled
*/ ?>
```

## CompileExecution

> Wenn deaktiviert, werden die Dateien der Template-Kompilierung nur erzeugt, aber  
> nicht ausgeführt; für das Ergebnis wird also der normale Template-Prozess verwendet.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileExecution=enabled
*/ ?>
```

## CompileAlwaysGenerate

> Wenn aktiviert, werden die Templates immer kompiliert, auch wenn schon ein  
> ausreichender Cache existiert.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
CompileAlwaysGenerate=disabled
*/ ?>
```

## CompileIncludeNodeTree

> Ein Array mit Knotenbäumen, die in die kompilierte Template-Datei aufgenommen werden.  
> Das vergrößert die Cache-Dateien und  
> ist nur für die Entwicklung der Template-Kompilierung nützlich.  
> Mögliche Einträge sind:  
> transformed - der Baum nach der Transformation der Knoten  
> static      - der Baum nach der statischen Extraktion  
> combined    - der Baum nach dem Zusammenfassen statischer Elemente  
> original    - der ursprünglich geparste Baum  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TemplateSettings]
# ein leeres "CompileIncludeNodeTree[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CompileIncludeNodeTree[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
