# Twig-Templates: Sicherheit und Maskierung

Diese Anleitung ist für Site-Bauer und Template-Entwickler, die entscheiden, was ein `.twig` tun darf, und die
Benutzereingaben drucken. Sie erklärt die Sandbox, die standardmäßig an ist, was sie erlaubt und ablehnt, wie Sie sie
ändern und wie die Maskierung funktioniert (wie in einem `.tpl`: Sie maskieren mit `wash`). Die Ausgaben unten wurden in
einer Entwicklungsinstallation gedruckt.

## Die Sandbox

`settings/twig.ini [TwigSettings] Security=enabled` (die Voreinstellung) führt jedes `.twig` in Twigs Sandbox mit der
Richtlinie von `expTwigSecurity` aus:

- **Jeder überbrückte Operator, jede Template-Funktion und jedes `exp`-Tag ist erlaubt**, mit den eigenen Regeln des
  Template-Systems (ein `fetch()` sieht, was der aktuelle Benutzer sehen darf, wie in einem `.tpl`).
- **Twigs eigene Tags, Filter und Funktionen nur wie gelistet**: `SecurityAllowedTagList[]` (`if`, `for`, `set`,
  `apply`, `block`, `extends`, `include`, `embed`, `macro`, `import`, `from`, `use`, `with`, `verbatim`, `guard`,
  `types`), `SecurityAllowedFilterList[]` (`abs`, `batch`, `capitalize`, `column`, `date`, `default`, `escape`, `first`,
  `format`, `join`, `json_encode`, `keys`, `last`, `length`, `lower`, `merge`, `nl2br`, `number_format`, `raw`,
  `replace`, `reverse`, `round`, `slice`, `sort`, `split`, `striptags`, `title`, `trim`, `upper`, `url_encode`),
  `SecurityAllowedFunctionList[]` (`attribute`, `block`, `cycle`, `max`, `min`, `parent`, `random`, `range`).
- **Kein Methodenaufruf und keine Eigenschaft eines Objekts**: Werte werden über ihre Attribute gelesen (`node.name`,
  `node.data_map.title.content`), wie in einem `.tpl`. `SecurityAllowedMethodList[]` (`Class::method`) und
  `SecurityAllowedPropertyList[]` (`Class::property`) öffnen einzelne.

Was die Sandbox ablehnt, wird beim Kompilieren des Templates gefunden. `exp:template:check --level=strict` meldet es,
bevor eine Seite es tut:

```
ERROR  twig  sec.twig:1
       Tag "do" is not allowed. (the sandbox would refuse the template) [TWIG-SECURITY-001]
       > 1 | {% do 1 %}
```

Ein Methodenaufruf scheitert, wenn er läuft; mit eingeschalteter Debug-Ausgabe bleibt die Meldung als Kommentar in der
Seite:

```
<!-- twig: rendering .../example.twig failed: Calling &quot;attribute&quot; method on a &quot;expTwigObjectProxy&quot; object is not allowed ... -->
```

Um ein Tag, einen Filter oder eine Funktion von Twig in einem Siteaccess zu erlauben, dessen Templates Sie vertrauen,
fügen Sie es der Liste in der `twig.ini.append.php` dieses Siteaccess hinzu:

```ini
[TwigSettings]
SecurityAllowedTagList[]=do
```

`Security=disabled` schaltet die Sandbox ab (kompilierte Templates werden dann in `<cache directory>/twig/compile`
gehalten, getrennt von denen aus der Sandbox). Tun Sie das nur für Templates, die kein Redakteur und keine Extension
ändern kann, die Sie nicht kontrollieren.

## Maskierung

Wie in einem `.tpl` wird nichts maskiert, solange das Template es nicht sagt (`[TwigSettings] Autoescape=disabled`): Ein
umgewandeltes Template druckt genau, was das `.tpl` gedruckt hat. Maskieren Sie jeden Wert, der aus einer Eingabe kommt,
mit `wash`:

```twig
{{ html }} / {{ html|wash }}
```

```
<b>bold</b> / &lt;b&gt;bold&lt;/b&gt;
```

`wash` nimmt dieselben Typen wie in einem `.tpl`: `wash` (xhtml), `wash('email')`, `wash('javascript')`. Twigs eigenes
`escape` (`e`) funktioniert auch; `wash` ist das, was der Rest des Designs benutzt, sodass die beiden Engines gleich
maskieren.

`Autoescape=enabled` (oder ein Strategiename, `html`) lässt Twig jeden gedruckten Wert maskieren. Dann müssen Werte, die
schon HTML sind (die Ausgabe von `attribute_view_gui`, ein gerenderter Rich Text), mit `|raw` gedruckt werden; ein
umgewandeltes Design ist für `Autoescape=disabled` geschrieben.

## Was ein Template erreichen kann

- Die Variablen seines Namensraums und was die Operatoren und Fetch-Funktionen des Template-Systems zurückgeben, mit den
  Rechten des aktuellen Benutzers.
- Dateien unterhalb der Installation über `{% expinclude %}` (Design-Ressource) und Twigs `{% include %}` (unterhalb
  der Template-Verzeichnisse des Designs und des Installationsverzeichnisses). Keines von beiden liest außerhalb der
  Installation.
- Kein PHP: Ein `.twig` kann keinen PHP-Code ausführen, keine PHP-Funktionen aufrufen und keine Objekte erzeugen.

## Siehe auch

- [.twig-Templates schreiben und ausführen](twig-templates.md)
- [Smarty-Templates: Sicherheit](smarty-templates-sicherheit.md)
- [Template-Syntax prüfen](template-syntaxpruefung.md)
