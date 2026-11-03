# Smarty-Templates: Sicherheit und Escaping

Diese Seite ist für Template-Entwickler und für Prüfer von `.stpl`-Templates. Sie erklärt, was escapt wird und was
nicht, wie sich `|wash` zu Smartys `|escape` und `|raw` verhält, wie Sie Werte drucken, die von Besuchern kommen, was
Smarty erreichen kann, das ein `.tpl` nicht kann, und die Sicherheitsrichtlinie, die das schließt, wie Formulare ihr
Token behalten, und was nie in einen Cache-Block gehört. Die Kurzfassung: Ein `.stpl` escapt von sich aus nichts,
genau wie ein `.tpl`; escapen Sie mit `wash` jeden Wert, der kein Markup ist, das Sie selbst erstellt haben.

## Nichts wird automatisch escapt

Das Template-System druckt Werte so, wie sie sind, und auch die Brücke schaltet Smartys automatisches Escaping nicht ein
(Smartys `escape_html` bleibt aus). Also druckt

```smarty
<h1>{$node.name}</h1>
```

den Namen so, wie er gespeichert ist, einschließlich aller `<`, `&` oder `"`. Namen von Inhalten, Texte von
Attributen, Suchwörter und alles aus der Anfrage müssen dort escapt werden, wo sie gedruckt werden:

```smarty
<h1>{$node.name|wash}</h1>
<a href={$node.url_alias|ezurl} title="{$node.name|wash}">{$node.name|wash}</a>
```

Was kein `|wash` braucht, weil es Markup ist, das zum Drucken gemacht ist:

- die Ausgabe einer Template-Funktion: `{attribute_view_gui attribute=$node.data_map.body}` rendert Rich Text über das
  Template seines Datentyps, das escapt, was es muss. Es mit `wash` zu escapen, würde die Tags als Text zeigen;
- die Ausgabe eines eingebundenen Templates (`{expinclude}`) und `$module_result.content` in einem Pagelayout;
- die Ausgabe von `ezurl`, `ezroot`, `ezdesign` und `ezimage` für Adressen, die Sie kontrollieren (siehe „Adressen“
  unten).

## wash sowie Smartys escape und raw

`|wash` ist der Template-Operator; er ist das, was der Konverter beibehält, und das, was sich in beiden Engines gleich
verhält.

| Form | Bewirkt |
|---|---|
| `{$x\|wash}` (`wash:'xhtml'`) | `htmlspecialchars()` mit `ENT_QUOTES`: `<` `>` `&` `"` `'` werden zu Entitäten. Für Text in Elementen und in Attributen mit Anführungszeichen |
| `{$x\|wash:'email'}` | dasselbe, dann werden `.` und `@` durch `[WashSettings] EmailDotText` und `EmailAtText` aus `template.ini` ersetzt, gegen das Sammeln von Adressen |
| `{$x\|wash:'javascript'}` | ein Wert für ein JavaScript-Zeichenkettenliteral: Anführungszeichen, Backslashes, Zeilenumbrüche und `</script>` werden unschädlich gemacht. Setzen Sie ihn in Anführungszeichen: `var t = '{$x\|wash:'javascript'}';` |
| `{$x\|wash:'pdf'}` | für den PDF-Export: Entitäten dekodiert, Leerzeichen und Zeilenumbrüche als PDF-Anweisungen. Nie für HTML |

Aus den Tests der Template-Engine, dasselbe in `.stpl`: `{'<a>'|wash:'xhtml'}` druckt `&lt;a&gt;`;
`{'a</script>\b'|wash:'javascript'}` druckt `a\x3C/script\x3E\\b`.

Smartys eigene Modifikatoren gibt es auch:

- `|escape` (`escape:'html'`, `'htmlall'`, `'url'`, `'quotes'`, `'javascript'`, `'mail'` ...) ist Smartys.
  `escape:'html'` ergibt fast dasselbe wie `wash`, ist aber ein zweiter, anderer Satz von Regeln: Verwenden Sie `wash`,
  damit `.tpl` und `.stpl` übereinstimmen, und nie beide auf einem Wert (das Ergebnis wäre doppelt escapt: `&amp;lt;`).
- `|raw` markiert einen Wert als nicht von Smartys automatischem Escaping zu escapen. Da das aus ist, ändert `|raw`
  nichts; lesen Sie es nicht als Zusage, dass ein Wert sicher oder unsicher ist.
- `{$x nofilter}` hat aus demselben Grund ebenfalls keine Wirkung.

## Werte von Besuchern

Alles, was ein Besucher sendet, ist nicht vertrauenswürdig: die Adresse und ihre View-Parameter, die Query-Zeichenkette,
gesendete Formularfelder, Cookies, der Suchtext. Escapen Sie es dort, wo es gedruckt wird, in dem Kontext, in dem es
gedruckt wird:

```smarty
{$search_text_html = $search_text|wash}
<p>{'You searched for %text.'|i18n:'design/mysite':expvoid():(['%text' => $search_text_html])}</p>
<input type="text" name="SearchText" value="{$search_text|wash}" />
{if is_set($view_parameters.tag)}<p>Tag: {$view_parameters.tag|wash}</p>{/if}
```

Für `ezhttp` schreiben Sie den Aufruf des `.tpl` und escapen das Ergebnis: `{ezhttp('SearchText', 'get')|wash}` (der
Operator nimmt seinen Namen als Parameter, siehe die [Operator-Referenz](smarty-templates-operator-referenz.md)).

Smarty kann die Anfrage auch direkt lesen: `$smarty.get`, `$smarty.post`, `$smarty.cookies`, `$smarty.server`,
`$smarty.request`, `$smarty.session` und `$smarty.env` sind PHPs `$_GET`, `$_POST` ... ganz ohne Prüfungen. Bevorzugen
Sie die Variablen der View und `ezhttp`; wenn Sie sie verwenden, escapen Sie jeden Wert. Die Sicherheitsrichtlinie
unten lehnt sie ab.

### In Attributen, URLs, Skripten und Styles

| Kontext | Schreiben Sie |
|---|---|
| Text eines Elements | `{$x\|wash}` |
| Ein Attribut mit Anführungszeichen | `title="{$x\|wash}"`; setzen Sie das Attribut immer in Anführungszeichen |
| Ein Link zu einem Inhalt | `href={$node.url_alias\|ezurl}` (Anführungszeichen inbegriffen) |
| Ein Link, der aus dem Wert eines Besuchers gebaut wird | bauen Sie den Pfad aus escapten oder geprüften Teilen; ein Wert in einer Query-Zeichenkette: `{$x\|rawurlencode}` |
| Eine Adresse, die ein Redakteur eingegeben hat (ein Linkfeld, eine Block-Einstellung) | prüfen Sie das Schema: `explayouts_safe_url` (Exponential Layouts) gibt nur sichere Adressen zurück |
| Eine JavaScript-Zeichenkette | `'{$x\|wash:'javascript'}'` |
| JSON in einem `<script>`-Block | kodieren Sie es und halten Sie `<` heraus: Die Media-Portierung macht nach dem Kodieren aus `<` und `>` `<` und `>` (`templates/pagelayout/script_bottom.stpl`) |
| CSS | nie von einem Besucher; Farben und Klassen von Redakteuren über `explayouts_safe_color`, `explayouts_safe_css_class` |

## Was Smarty kann, das ein .tpl nicht kann

Ohne die Sicherheitsrichtlinie (`[SmartySettings] Security=disabled`; die Voreinstellung ist `enabled`) kann ein
`.stpl` jede Smarty-Funktion nutzen, darunter:

- `{fetch file='...'}`: Smartys Tag, das eine lokale Datei oder eine URL liest (nicht das Modul-`fetch()`);
- `$smarty.server`, `$smarty.env`, `$smarty.cookies`, `$smarty.session` (siehe oben), `$smarty.const` und
  PHP-Konstanten;
- `{math}`, `{eval}`, `{debug}`, `{config_load}` und Smartys `{include file=...}` jedes `.stpl` in den
  Template-Verzeichnissen der Designs;
- statische Klassen (`{eZSys::rootDir()}`, `{eZUser::currentUser()}`) und Methoden der Objekte, die es bekommt:
  `{$node->expSmartyObject()->remove()}` erreicht das Inhaltsobjekt selbst.

Ein `.stpl` ist also Code, genauso wie eine PHP-Datei. Nur vertrauenswürdige Entwickler sollten sie schreiben, sie
sollten wie Code geprüft werden, und das Recht, Templates in der Administration zu bearbeiten (der Template-Editor des
Moduls `visual`, der ein `.stpl` bearbeiten kann, das eine Override-Regel bereits verwendet), darf nur Personen gegeben
werden, die Sie auch PHP ändern lassen würden. Dasselbe gilt für `.tpl`-Dateien, die jede Einstellung mit `ezini` lesen
und jeden Inhalt abrufen können, den der Besucher lesen darf; Smarty erweitert das auf das Dateisystem und auf PHP.

## Die Smarty-Sicherheitsrichtlinie (Vorschlag)

`[SmartySettings] Security=enabled` in `settings/smarty.ini` (oder einem Override) schaltet eine
Smarty-Sicherheitsrichtlinie ein (`expSmartySecurity`, eine `Smarty\Security`), die über die `Security*`-Einstellungen
daneben konfiguriert wird. Sie ist **vorerst standardmäßig aus**; dieser Abschnitt ist der Vorschlag, über den zu
entscheiden ist.

### Was sie ablehnt

Ein Template, das eines davon verwendet, wird nicht kompiliert; der Renderer protokolliert Smartys Meldung („tag 'eval'
disabled by security setting“, „special variable '$smarty.server' not allowed by security setting“ ...), und die Seite
zeigt an seiner Stelle nichts (einen HTML-Kommentar bei eingeschalteter Debug-Ausgabe).

| Funktion | Einstellung | Voreinstellung |
|---|---|---|
| `{eval}`, `{fetch file=...}`, `{math}`, `{debug}`, `{config_load}`, `{html_image}` | `SecurityDisabledTagList[]` | diese sechs |
| Smarty-Tags, die nicht in der erlaubten Liste stehen | `SecurityAllowedTagList[]` (leer: alle außer den abgeschalteten) | die Steuer-Tags, `capture`, Begrenzer, `block`/`extends`, `include`, `function`/`call`, `assign`/`append`, `nocache`, `setfilter`, `counter`, `cycle`, `textformat`, `mailto`, die `html_*`-Formularhelfer |
| der Modifikator `debug_print_var` | `SecurityDisabledModifierList[]` | `debug_print_var` |
| `$smarty.get`, `.post`, `.cookies`, `.session`, `.server`, `.env`, `.request`, `.const`, `.template_object` | `SecurityDisabledSpecialVariableList[]` | diese neun |
| PHP-Konstanten (`{if PHP_VERSION}`, `$smarty.const`) | `SecurityConstants` | `disabled` |
| statische Klassen, Methoden und Eigenschaften (`{eZSys::rootDir()}`) | `SecurityStaticClassList[]` (leer: keine) | keine |
| Stream-Wrapper als Templates (`{include file='php://...'}`) | `SecurityStreamList[]` (leer: keine) | keine |
| `->` in einem Tag: Methoden und Eigenschaften von Objekten | `SecurityObjectAccess` | `disabled` |
| Templates außerhalb der Template-Verzeichnisse der Design-Basen | `SecurityTrustedDirList[]` fügt Verzeichnisse hinzu | keine hinzugefügt |

`$smarty.template_object` wird abgelehnt, weil es die Smarty-Instanz erreicht, die die Richtlinie abschalten könnte;
`->`, weil jeder Wert mit Attributen ein Objekt ist (`$node->expSmartyObject()` ist das Inhaltsobjekt, mit `remove()`,
`store()` ...), und `ezhttp()` ohne Parameter gibt das HTTP-Werkzeug zurück. Templates lesen Attribute mit Punkten
(`$node.name`), was erlaubt bleibt: Es ruft nur `attribute()` auf.

### Was sie erlaubt

- Smartys Kontrollstrukturen und die oben aufgeführten Tags; `{$x = ...}` (Zuweisung) und `{call}` immer.
- Jeden Smarty-Modifikator außer den abgeschalteten (`|upper`, `|count`, `|escape`, `isset()`, `|default` ...).
- **Jeden Operator, jede Funktion und jeden Block des Template-Systems**, so wie ein `.tpl` sie verwenden darf:
  `{$x|wash}`, `{concat(...)}`, `{fetch('content', 'node', ...)}`, `{attribute_view_gui ...}`, `{expinclude}`,
  `{expcacheblock}`, `{expset}` ... Eine Abschaltliste kann auch einen davon nennen.
- Die Funktionen, die ein Template mit `{function}` definiert.

Die Richtlinie wird geprüft, wenn ein Template kompiliert wird. Unter ihr kompilierte Templates werden getrennt
gehalten (`<cache dir>/smarty/compile-secure`), sodass ihr Einschalten jedes Template einmal neu kompiliert und ein ohne
sie kompiliertes Template nie mit ihr ausgeführt wird. Zur Laufzeit kostet sie eine Prüfung des Verzeichnisses jeder
Template-Datei, die Smarty lädt.

### Welche Templates brechen würden

- **Keines der Media-Portierung.** Alle 516 Templates von `extension/sevenx_themes_media/design/smarty_media`
  kompilieren unter den Standardlisten (geprüft, indem jedes mit und ohne die Richtlinie kompiliert wurde). Eine
  Durchsicht des Designs: Seine Smarty-Tags sind `if`/`elseif`/`else`, `foreach`, `capture`, `ldelim`/`rdelim`, `break`,
  `continue` und Zuweisungen; seine Smarty-Modifikatoren `default` und `in_array`; alles andere ist ein
  Template-Operator oder eine Template-Funktion; kein `$smarty.*`, kein `::`, kein `->`. `ezhttp(null, ...)`,
  `ezmodule(null, ...)` und `language_switcher(null, ...)` sind Operator-Aufrufe.
- **Nichts aus der Ausgabe des Konverters.** Jeder Template-Engine- und Operator-Fall der Paritätstests
  (`expSmartyOperatorParityTest`, 453 Fälle) ergibt mit eingeschalteter Richtlinie dieselbe Ausgabe.
- **Von Hand geschriebene Templates**, die verwenden, was die Tabelle ablehnt: `$smarty.get`/`.server`/`.cookies`
  (verwenden Sie `ezhttp()` oder die Variablen der View), `{fetch file=...}`, `{math}` (verwenden Sie `sum`, `sub`,
  `mul`, `div`), `{eval}`, statische Klassen oder `->` (schreiben Sie einen Template-Operator), PHP-Konstanten, und
  `{include file=...}` oder `{exp_smarty_render( '<file>' )}` einer Datei außerhalb der Template-Verzeichnisse der
  Design-Basen (fügen Sie das Verzeichnis zu `SecurityTrustedDirList[]` hinzu, oder binden Sie sie über das Design mit
  `{expinclude uri='design:...'}` ein).

### Was sie nicht abdeckt

Die eigenen Operatoren des Template-Systems bleiben so mächtig wie in einem `.tpl`: `fetch()` liest jeden Inhalt, den
der Besucher lesen darf, `ezini()` jede Einstellung, `ezhttp()` die Anfrage. Die Richtlinie schließt, was Smarty
darüber hinaus hinzufügt (das Dateisystem, PHP-Funktionen und -Klassen, die Anfrage außerhalb des Template-Systems),
nicht das, was ein `.tpl` ohnehin kann; Templates sollten weiterhin nur vertrauenswürdige Personen schreiben.

### Empfehlung

Die Richtlinie ist standardmäßig an (`Security=enabled` in `settings/smarty.ini`). Sie wurde eingeschaltet, nachdem die
Media-Portierung unter ihr geprüft worden war: Die 516 Templates kompilieren, die Paritätsfälle bestehen, und die Seiten
der `.tpl`- und `.stpl`-Siteaccesses blieben identisch. Eine Site, die eine abgelehnte Funktion braucht, schaltet genau
diese eine Funktion in ihrem Override wieder ein (`SecurityDisabledTagList[]` ohne `math` neu angegeben, eine Klasse in
`SecurityStaticClassList[]`, `SecurityObjectAccess=enabled`), statt die Richtlinie abzuschalten. Nur wenn eine Site auf
mehrere davon nicht verzichten kann, schalten Sie sie für diesen Siteaccess in
`settings/siteaccess/<siteaccess>/smarty.ini.append.php` ab:

```ini
[SmartySettings]
Security=disabled
```

Nach einer Änderung an diesen Einstellungen deployen Sie (die PHP-Worker behalten die Smarty-Instanz ihres Prozesses).

## Formulare und das Formular-Token

Die Extension `ezformtoken` schützt Formulare gegen Cross-Site Request Forgery. Sie arbeitet mit der Ausgabe der
Seite, nachdem alle Templates gelaufen sind: Für einen angemeldeten Besucher fügt sie jedem `<form ... method="post">`
ein verstecktes Feld `ezxform_token` hinzu, legt das Token in `<meta name="csrf-token">` im `<head>` ab, ersetzt den
Text `@$ezxFormToken@` durch das Token und markiert die Seite mit `Cache-Control: private`. Weil das auf der Ausgabe
geschieht, funktioniert es für `.stpl` und `.tpl` gleichermaßen, und auch für ein Formular, das aus einem
`{expcacheblock}` kommt: Das Token wird bei jeder Antwort hinzugefügt, nie im Cache gehalten. Was Sie tun müssen:

- schreiben Sie `method="post"` an POST-Formulare (Groß- oder Kleinschreibung beliebig), damit das Feld hinzugefügt
  wird;
- für eine Anfrage aus JavaScript lesen Sie das Token aus dem Meta-Tag (`csrf-token`) und senden es als Feld
  `ezxform_token` oder in dem Header, den Ihr Code verwendet;
- drucken Sie nie selbst ein Token in ein Template.

## Nie in einem Cache-Block

Ein `{expcacheblock}` speichert seine Ausgabe für jeden Besucher, dessen Schlüssel passen. Legen Sie nie Folgendes
hinein:

- `csp_nonce()`: Die Nonce ändert sich bei jeder Anfrage, und eine gespeicherte passt nicht mehr zum Header
  `Content-Security-Policy` der Seite, also blockiert der Browser das Skript;
- den Namen des Besuchers, seinen Warenkorb, seine Nachrichten oder irgendetwas pro Benutzer, es sei denn, der Block ist
  den Benutzer im Schlüssel hat (und selbst dann denken Sie zweimal nach: ein Schlüssel pro Benutzer füllt den Cache).
  Verwenden Sie das Platzhalter-Muster aus
  [Caching und Leistung](smarty-templates-caching.md#der-seitenblock-mit-platzhaltern);
- irgendetwas, das von Berechtigungen abhängt, ohne die Rollen und Einschränkungen des Besuchers in den Schlüsseln
  ([Schlüssel für angemeldete Besucher](smarty-templates-caching.md#schlüssel-für-angemeldete-besucher)).

## Prüfliste für ein .stpl

- Jeder gedruckte Wert, der kein Markup ist, ist mit `|wash` escapt (oder mit `wash:'javascript'` in Skripten), einmal.
- Kein `$smarty.get`, `$smarty.post` ... ohne `wash`; bevorzugen Sie View-Variablen und `ezhttp`.
- Kein `{fetch file=...}`, `{eval}`, `{math}` mit Werten von Besuchern.
- Es kompiliert mit `Security=enabled` (kein `$smarty.server`, `::`, `->`, `{eval}`, `{fetch file=...}`).
- Attribute stehen in Anführungszeichen.
- Adressen von Redakteuren gehen durch eine Prüfung auf sichere URLs.
- Cache-Blöcke enthalten nichts pro Besucher und haben die Rollen im Schlüssel, wo der Inhalt von Berechtigungen
  abhängt.
- Kein inline `<style>` oder `style=""` (die Design-Regeln des Projekts); die CSP der Site kann sie ohnehin verbieten.

## Verwandte Seiten

- [Content Security Policy](../funktionen/6.0/content-security-policy.md)
- [Operator-Referenz](smarty-templates-operator-referenz.md), [Caching und Leistung](smarty-templates-caching.md)
- [Sicherheit und Audit](sicherheit-und-audit.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
