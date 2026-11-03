# Twig-Templates: Caching

Diese Anleitung ist für Template-Entwickler und Site-Bauer, die `.twig`-Seiten aus dem Cache ausliefern möchten. Ein
`.twig` nimmt an jeder Cache-Schicht teil, an der ein `.tpl` teilnimmt; diese Seite sagt, welche Schichten es gibt, wie
`{% expcacheblock %}` funktioniert und wie ein Cache-Block um eine ganze Seite das persönliche Menü des Besuchers frisch
hält. Dasselbe Muster in Smarty steht in [Smarty-Templates: Caching](smarty-templates-caching.md).

## Die Schichten

Von außen nach innen:

| Schicht | Hält | Engine-spezifisch? |
|---|---|---|
| Der Antwort-Cache von Velocity | ganze Antworten für anonyme Besucher (nur Velocity) | nein |
| Der rollenbewusste HTTP-Cache (`exphttpcache`) | ganze Seiten je Rollensatz | nein |
| Der Content-View-Cache | das gerenderte `content/view`-Ergebnis eines Knotens | nein |
| Cache-Blöcke (`{cache-block}`, `{% expcacheblock %}`) | Teile der Ausgabe eines Templates | das Tag |
| Kompilierte Templates | `.tpl` als PHP, `.twig` als PHP-Klassen | ja: `<cache directory>/twig/compile-secure` |

Eine gecachte Seite erreicht die Template-Engine nie: Ob ihre Templates `.tpl`, `.stpl` oder `.twig` sind, macht für eine
gecachte Anfrage keinen Unterschied. Messungen in einer Entwicklungsinstallation (gecachte Seiten der drei Engines
innerhalb weniger Prozent voneinander) stehen in [Leistung](twig-templates-leistung.md).

## {% expcacheblock %}

```twig
{% expcacheblock keys=[node.node_id] %}cached: {{ node.name|wash }}{% endexpcacheblock %}
```

```
cached: Why Track Your Workouts?
```

`expcacheblock` ist der `{cache-block}` des Template-Systems: dieselben Parameter, dieselben Cache-Dateien
(`<cache directory>/template-block/`), dasselbe Ablaufen und Leeren.

| Parameter | Bedeutung |
|---|---|
| `keys` | ein Wert oder eine Liste: Der Block wird je Schlüssel gehalten; der Siteaccess und die Stelle im Template sind immer Teil des Schlüssels |
| `expiry` | Sekunden, die eine Kopie gehalten wird (Voreinstellung 7200); `0` hält sie, bis sie geleert wird |
| `ignore_content_expiry` | eine Veröffentlichung von Inhalt lässt den Block nicht ablaufen |
| `subtree_expiry` | eine Veröffentlichung unterhalb dieses Knotens (ein Pfad oder eine Knoten-ID) lässt den Block ablaufen |
| `id` | die Nummer des Blocks innerhalb des Templates (der Konverter nummeriert sie): Blöcke eines Templates ohne `id` und mit denselben Schlüsseln teilen sich einen Cache-Eintrag, geben Sie also jedem Block eines handgeschriebenen Templates eine eigene |

Variablen, die innerhalb eines Blocks gesetzt werden, werden zurückgeschrieben, wenn der Block läuft; kommt der Block aus
dem Cache, werden sie nicht gesetzt (wie in einem `.tpl`). Halten Sie das `{% set %}` eines Werts, den Sie nach dem Block
brauchen, außerhalb des Blocks.

## Ein Block für die ganze Seite, ein frisches Benutzermenü

Das umgewandelte Media-Pagelayout hält die ganze Seite in einem Cache-Block, mit Schlüsseln aus Knoten, URI,
Seiteninhalt und den Rollen des Besuchers, sodass eine gecachte Seite einen Cache-Lesezugriff kostet. Der persönliche
Teil des Besuchers (das Benutzermenü mit dem Warenkorb) wird nie gehalten: Ein Flag sagt dem Menü-Template, an seiner
Stelle eine Markierung zu schreiben, während der Block rendert, und nach dem Block ersetzt das frische Menü die
Markierung bei jeder Anfrage:

```twig
{% expsetblock variable='exp_dynamic_parts' scope='global' %}1{% endexpsetblock %}
{% set el_page_html %}
{% expcacheblock id=4 keys=(['pagelayout-page', module_result.content_info.node_id|default(0), module_result.uri|default(''), module_result.content|md5, el_page_user.role_id_list|implode(','), el_page_user.limited_assignment_value_list|implode(','), el_page_user.is_logged_in]) %}
    ... the page; pagelayout/header writes <!--exp-dynamic:user_menu--> while exp_dynamic_parts is 1 ...
{% endexpcacheblock %}
{% endset %}
{% expsetblock variable='exp_dynamic_parts' scope='global' %}0{% endexpsetblock %}
{% set el_user_menu %}{% expinclude uri='design:pagelayout/header/user_menu.tpl' %}{% endset %}
{{ (el_page_html|explode('<!--exp-dynamic:user_menu-->'))|implode(el_user_menu) }}
```

Der Reiter Rendering der Debug-Leiste zeigt den Block (`pagelayout.twig id=4`), ob er aus dem Cache gelesen oder erzeugt
wurde, seine Schlüssel und seine Datei. Dasselbe Muster für einen anderen besucherbezogenen Teil: Geben Sie dem Teil eine
eigene Markierung, schreiben Sie die Markierung, solange das Flag gesetzt ist, rendern Sie den Teil nach dem Block und
ersetzen Sie die Markierung.

## Kompilierte Templates

Twig kompiliert jedes `.twig` einmal zu einer PHP-Klasse in `<cache directory>/twig/compile-secure` (`compile` bei
abgeschalteter Sandbox). Der Klassenname enthält die Änderungszeit der Datei, den Siteaccess und die Locale, sodass eine
geänderte Datei bei der nächsten Anfrage neu kompiliert wird, auch in den dauerhaften Workern von Velocity, und
Übersetzungen, die beim Kompilieren des Templates gemacht wurden, bei ihrer Sprache bleiben.
`./console exp:ezcache --clear-tag=template` leert die kompilierten Templates aller Engines.

## Siehe auch

- [.twig-Templates schreiben und ausführen](twig-templates.md)
- [Caches](caches.md)
- [Leistung](twig-templates-leistung.md)
