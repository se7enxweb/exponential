# Pagelayout-Hooks: eine Stelle am Ende jeder Seite für Erweiterungen

Seit 6.0.15 endet jedes Pagelayout mit einer Zeile, die `design:pagelayout/hooks/body_end.tpl` einbindet, und dieses
Template bindet jedes Template ein, das eine Erweiterung in `site.ini [PageLayoutHooks] BodyEnd[]` nennt. Eine
Erweiterung, die etwas auf jeder Seite jedes Designs braucht (die Website-Seitenleiste expwt ist die erste), ergänzt
eine Einstellung, statt jedes Design um eine Änderung seines Pagelayouts zu bitten.

## Die Zeile im Pagelayout

Eine Zeile je Template-Engine, direkt vor `</body>`:

| Engine | Zeile |
|---|---|
| `.tpl` | `{include uri='design:pagelayout/hooks/body_end.tpl'}` |
| `.stpl` (Smarty) | `{expinclude uri='design:pagelayout/hooks/body_end.tpl'}` |
| `.twig` (Twig) | `{% expinclude uri='design:pagelayout/hooks/body_end.tpl' %}` |

Die Designs des Kernels haben sie: `standard`, `base`, `plain`, `mysite`, `admin`, `admin3`, `admin4` und `admin4l`.
Das Design einer Erweiterung ergänzt die Zeile in seinen eigenen Pagelayouts. Ohne Eintrag in `BodyEnd[]` gibt der Hook
nichts aus; die Zeile kostet also nichts auf einer Website, die ihn nicht nutzt.

## Einen Hook ergänzen

In `settings/site.ini.append.php` der Erweiterung:

```ini
[PageLayoutHooks]
BodyEnd[]=myext/body_end.tpl
```

`myext/body_end.tpl` ist ein Template im Design der Erweiterung (`design/standard/templates/myext/body_end.tpl`) und wird
wie jedes andere über die Design-Kette gefunden.

## Was ein Hook ausgeben darf

Ein Pagelayout, das die ganze Seite in einem Cache-Block hält (die Media-Designs tun das), rendert den Hook darin, und
der Seiten-Cache hält die Seite für alle mit denselben Rollen. Ein Hook gibt also nur aus, was diese Schlüssel abdecken:
den Siteaccess, den Knoten, ob der Besucher angemeldet ist, und die Rollen. Was dem Benutzer selbst gehört (ein Name, ein
Token, eine Einstellung), holt das Skript des Hooks, nachdem die Seite geladen ist. Ein Hook ohne etwas zu zeigen gibt
nichts aus, nicht einmal einen Kommentar.

expwt zum Beispiel gibt einem Benutzer mit der Policy `expwt/use` ein Custom Element und ein verzögertes Skript aus und
einem nicht angemeldeten Besucher nichts, solange `ShowToAnonymous` aus ist ([die Website-Seitenleiste](website-seitenleiste.md)).
