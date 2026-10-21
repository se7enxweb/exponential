---
title: "CSP: von Event-Handler-Attributen zu data-Attributen"
audience: developer, designer
category: extension-development
order: 140
summary: Ersetzen Sie onclick, onchange, onmouseover und andere Event-Handler-Attribute durch data-Attribute und einen delegierten Listener in einer Skriptdatei, mit den Korrekturen der Kontextmenüs von ezownerchange und expchangeclass.
keywords: Event-Handler-Attribut, data-Attribut, delegierter Listener, Kontextmenü, Upgrade, CSP, onclick, onmouseover, onchange, onsubmit, event handler attribute, data attribute, delegated listener, addEventListener, context menu, upgrade
type: help
---
# CSP: von Event-Handler-Attributen zu data-Attributen

Teil 4, Kapitel 14. Ein `onclick="..."`-Attribut ist Inline-Code, genau wie ein `<script>`-Block, und eine Content
Security Policy ohne `'unsafe-inline'` verweigert seine Ausführung. Alte Templates haben weit mehr davon als
Skriptblöcke: Eine Messung auf einer Installation mit vielen Erweiterungen im Oktober 2026 zählte 165 bis 167
Event-Handler-Attribute pro Administrationsseite, bevor die eigenen Templates des Kernels umgestellt wurden
([Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md)). Dieses Kapitel macht daraus
data-Attribute, die von einer Skriptdatei verarbeitet werden.

## Sie finden

```bash
grep -rnoE '\son[a-z]+\s*=\s*"[^"]*"' --include='*.tpl' extension/myoldext/design/ | sort | uniq -c | sort -rn | head -30
```

Die häufigsten sind meist eine Handvoll Muster, die oft wiederholt werden: eine Bestätigung vor dem Löschen,
Checkboxen zum „Alle auswählen“, Absenden bei Änderung, ein Popup öffnen. Wandeln Sie das Muster einmal um und wenden
Sie es dann überall an.

## Das Muster

1. Legen Sie fest, **was** passieren soll, in data-Attributen auf dem Element: `data-myoldext-confirm="Delete?"`.
2. Schreiben Sie **einen** Listener pro Ereignistyp in eine Datei, auf `document`, der sich das Element ansieht, von
   dem das Ereignis kam, und dessen data-Attribute liest.
3. Geben Sie die Datei im Template mit `ezscript` aus, einmal oder mehrmals; sie richtet sich nur einmal ein.

Ein Listener auf `document` verarbeitet jedes passende Element, auch solche, die später von anderen Skripten
hinzugefügt werden. Das nennt man Event-Delegation.

### Eine Bestätigung vor einer Löschen-Schaltfläche

Vorher:

```text
<input type="submit" class="button" name="RemoveButton" value="{'Remove'|i18n( 'extension/myoldext' )}"
       onclick="return confirm( '{'Really remove the selected items?'|i18n( 'extension/myoldext' )}' );" />
```

Nachher, im Template:

```text
<input type="submit" class="button" name="RemoveButton" value="{'Remove'|i18n( 'extension/myoldext' )|wash}"
       data-myoldext-confirm="{'Really remove the selected items?'|i18n( 'extension/myoldext' )|wash}" />
{ezscript( array( 'myoldext_actions.js' ) )}
```

und in `design/standard/javascript/myoldext_actions.js`:

```javascript
( function()
{
    if ( window.myOldExtActionsReady )
        return;
    window.myOldExtActionsReady = true;

    document.addEventListener( 'click', function( e )
    {
        var el = e.target && e.target.closest ? e.target.closest( '[data-myoldext-confirm]' ) : null;
        if ( el && !window.confirm( el.getAttribute( 'data-myoldext-confirm' ) ) )
        {
            e.preventDefault();
        }
    } );

    document.addEventListener( 'change', function( e )
    {
        var el = e.target;
        if ( el && el.hasAttribute && el.hasAttribute( 'data-myoldext-submit-on-change' ) && el.form )
        {
            el.form.submit();
        }
    } );
} )();
```

Der zweite Listener deckt ein weiteres häufiges Attribut ab, `onchange="this.form.submit()"`, das auf dem
`<select>` zu `data-myoldext-submit-on-change` wird.

> **Tipp**
> Im Design admin4 hat der Kernel solche Aktionen bereits: `data-a4-confirm`, `data-a4-submit-on-change`,
> `data-a4-toggle-checkboxes` und weitere, verarbeitet von `design/admin4/javascript/a4_actions.js`. Eine
> Erweiterung, die nur in admin4 läuft, kann sie verwenden. Eine Erweiterung, die in jedem Administrationsdesign oder
> auf älteren Releases läuft, liefert ihre eigene kleine Datei mit, wie die Beispiele unten.

## Ein echtes Beispiel: Einträge im Kontextmenü

Kontextmenüs sind die Stelle, an der alte Erweiterungen die meisten ihrer Handler untergebracht haben, weil die
eigenen Menüs des Kernels früher so gebaut wurden. Zwei Erweiterungen zeigen die beiden Arten, wie die alten Einträge
funktionierten.

### Ein Eintrag, der eine URL öffnet: ezownerchange

se7enxweb/ezownerchange, Commit a5ebbb8, „The Change owner entries of the context menus need no inline script“.
Vorher hat das Template den Eintrag mit einem Inline-Skript zum `menuArray` des Kernels hinzugefügt und mit
`onmouseover` darauf verwiesen:

```text
<script type="text/javascript">
menuArray['ContextMenu']['elements']['menu-change-owner'] = [];
menuArray['ContextMenu']['elements']['menu-change-owner']['url'] = {"/owner/change/%objectID%/(node)/%nodeID%"|ezurl};
</script>
<hr/>
<a id="menu-change-owner" href="#" onmouseover="ezpopmenu_mouseOver( 'ContextMenu' );">{"Change owner"|i18n( 'extension/ezownerchange' )}</a>
```

Nachher trägt der Eintrag sein Menü und seine URL in data-Attributen, und eine Datei erledigt beide Aufgaben:

```text
<hr/>
<a id="menu-change-owner" href="#" data-ezownerchange-menu="ContextMenu" data-ezownerchange-url="{'/owner/change/%objectID%/(node)/%nodeID%'|ezurl( 'no' )|wash}">{"Change owner"|i18n( 'extension/ezownerchange' )}</a>
{ezscript( array( 'ezownerchange_menu.js' ) )}
```

Die Datei `ezownerchange_menu.js` (gekürzt):

```javascript
function register()
{
    var items = document.querySelectorAll( 'a[data-ezownerchange-menu]' );
    for ( var i = 0; i < items.length; i++ )
    {
        var item = items[i], menuID = item.getAttribute( 'data-ezownerchange-menu' );
        if ( item.getAttribute( 'data-ezownerchange-added' ) || !item.id || window.menuArray === undefined )
            continue;
        var menu = window.menuArray[menuID];
        if ( !menu || typeof menu !== 'object' )
            continue;
        if ( !menu['elements'] )
            menu['elements'] = {};
        menu['elements'][item.id] = { 'url': item.getAttribute( 'data-ezownerchange-url' ) };
        item.setAttribute( 'data-ezownerchange-added', '1' );
    }
}
```

und ein `mouseover`-Listener auf `document`, der für diese Einträge `ezpopmenu_mouseOver()` aufruft, wie es das
Attribut tat. Beachten Sie `ezurl( 'no' )`: Die URL wird ohne Anführungszeichen geschrieben, weil das Attribut sie
bereits hat, und `wash` escapet sie für HTML.

### Ein Eintrag, der ein Formular absendet: expchangeclass

se7enxweb/expchangeclass, Commit 88b60d9. Vorher:

```text
<a id="menu-class-change" href="#" onmouseover="ezpopmenu_mouseOver( 'ContextMenu' )"
   onclick="ezpopmenu_submitForm( 'menu-form-class-change' ); return false;">{"Change content class"|i18n("design/admin/changeclass")}</a>
```

Nachher:

```text
<a id="menu-class-change" href="#" data-menu-form="menu-form-class-change">{"Change content class"|i18n("design/admin/changeclass")}</a>
...
{ezscript( array( 'expchangeclass_menu.js' ) )}
```

Hier verwendet die Erweiterung die eigene Deklaration des Kernels, `data-menu-form`, die `ezpopupmenu.js` des
Kernels ab Exponential 6.0.15 verarbeitet. Für ein älteres `ezpopupmenu.js` ohne deklarierte Menüs sendet
`expchangeclass_menu.js` das Formular selbst ab und tut nichts, wenn der Kernel es bereits verarbeitet. So
funktioniert ein Release der Erweiterung mit alten und neuen Kerneln.

> **Warnung**
> Weisen Sie `menuArray` in einer Erweiterung niemals neu zu (`var menuArray = new Array();` oder
> `menuArray = {};`). Der Kernel legt es an und fügt die mit `data-menu-*`-Attributen deklarierten Menüs hinzu; wenn
> Sie es ersetzen, öffnen sich die eigenen Kontextmenüs des Kernels nicht mehr. Fügen Sie nur hinzu.

## Die Handler, denen Sie begegnen werden, und ihr Ersatz

| Altes Attribut | Ersatz |
|---|---|
| `onclick="return confirm(...)"` | `data-<ext>-confirm` und ein Click-Listener |
| `onclick="this.form.submit()"`, `onchange="this.form.submit()"` | `data-<ext>-submit-on-change` und ein Change-Listener |
| `onclick="ezjs_toggleCheckboxes( document.myform, 'DeleteIDArray[]' ); return false;"` | `data-<ext>-toggle="DeleteIDArray[]"` und ein Click-Listener (in admin4: `data-a4-toggle-checkboxes`) |
| `onclick="window.open( ... )"` | ein normaler Link mit `target="_blank"` und `rel="noopener"` oder ein data-Attribut mit der URL |
| `onload="..."` auf `<body>` | ein `DOMContentLoaded`-Listener in der Datei der Seite |
| `onsubmit="return check()"` | ein `submit`-Listener auf dem Formular |
| `onmouseover` auf einem Menüeintrag | die `data-menu-*`-Deklaration des Kernels oder Ihr eigener Listener wie oben |

## Ihre Arbeit prüfen

1. Die Suche oben auf dieser Seite findet in Ihren Templates nichts.
2. In der Browser-Konsole jeder Seite keine Zeilen „Refused to execute inline event handler“.
3. Jedes umgewandelte Bedienelement tut weiterhin, was es tat: Klicken Sie jede Schaltfläche, ändern Sie jede
   Auswahlliste, öffnen Sie jeden Menüeintrag.
4. Ein Test in der Erweiterung wie der in [CSP: Inline-Skripte in Dateien verschieben](csp-inline-skripte-auslagern.md)
   schlägt auch bei `\son[a-z]+\s*=` fehl.

## Verwandte Seiten

- Zurück: [CSP: Inline-Skripte in Dateien verschieben](csp-inline-skripte-auslagern.md)
- Weiter: [CSP: javascript:-Links, eval und String-Timer](csp-javascript-links-und-eval.md)
- [Kontextmenüs und Integration in die Administration](kontextmenues-und-admin-integration.md)
- [Content Security Policy für Erweiterungen](../teil-3-erweiterungsentwickler/content-security-policy-fuer-erweiterungen.md)
- [Event-Delegation](https://developer.mozilla.org/en-US/docs/Learn_web_development/Core/Scripting/Event_bubbling) auf MDN
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
