---
title: URL-, Design-, INI- und HTTP-Operatoren
audience: developer, designer, app-creator
category: development
order: 300
summary: Die zehn Operatoren von eZURLOperator im Kernel (ezurl, ezroot, ezdesign, ezimage, exturl, ezsys, ezhttp, ezhttp_hasvariable, ezini, ezini_hasvariable), wie die Erweiterung ezurlmultisite sie auf alpha übernimmt, und ihre eigenen ezurl_path, ezurl_siteaccess und ezurl_roots, jeweils mit belegten Beispielen.
keywords: URL-Operatoren, Links, Anführungszeichen, Design-Dateien, Einstellungen lesen, Anfragevariablen, ezurl, ezroot, ezdesign, ezimage, exturl, ezsys, ezhttp, ezhttp_hasvariable, ezini, ezini_hasvariable, ezurl_path, ezurl_siteaccess, ezurl_roots, eZURLOperator, ezurlmultisite, links, url, quote
type: help
---
# URL-, Design-, INI- und HTTP-Operatoren

Der Kernel registriert zehn Operatoren mit einer Klasse, `eZURLOperator` (`kernel/common/ezurloperator.php`), in
`kernel/common/eztemplateautoload.php`. Sie bauen Links (`ezurl`, `ezroot`), finden Design-Dateien (`ezdesign`,
`ezimage`), registrieren externe Links (`exturl`) und lesen die Umgebung (`ezsys`), die Anfrage (`ezhttp`,
`ezhttp_hasvariable`) und die Einstellungen (`ezini`, `ezini_hasvariable`).

Auf alpha werden alle zehn Namen stattdessen von der Erweiterung `ezurlmultisite` registriert (Klasse
`eZURLMultisiteURLOperator`, `extension/ezurlmultisite/classes/ezurlmultisiteurloperator.php`), die die Kernel-Klasse
erweitert und nur `ezurl` ändert. Dieselbe Erweiterung fügt drei eigene Operatoren hinzu. Jedes Beispiel auf dieser
Seite wurde auf alpha mit dem Siteaccess `site` auf der Kommandozeile gerendert; Links entstehen also ohne eine
Web-Anfrage (kein `index.php`, Schema `http`).

## Inhalt

1. [Gemeinsame Parameter: quote_val und server_url](#gemeinsame-parameter-quote_val-und-server_url)
2. [Die Operatoren](#die-operatoren): [ezurl](#ezurl), [ezroot](#ezroot), [ezdesign](#ezdesign), [ezimage](#ezimage), [exturl](#exturl), [ezsys](#ezsys), [ezhttp](#ezhttp), [ezhttp_hasvariable](#ezhttp_hasvariable), [ezini](#ezini), [ezini_hasvariable](#ezini_hasvariable)
3. [Wie ezurlmultisite die Operatoren übernimmt](#wie-ezurlmultisite-die-operatoren-übernimmt)
4. [Die Operatoren von ezurlmultisite](#die-operatoren-von-ezurlmultisite): [ezurl_path](#ezurl_path), [ezurl_siteaccess](#ezurl_siteaccess), [ezurl_roots](#ezurl_roots)
5. [Kompilierte Templates](#kompilierte-templates)

## Gemeinsame Parameter: quote_val und server_url

`eZURLOperator::namedParameterList()` gibt eine Liste für alle zehn Operatoren zurück (die Klasse hat kein
`namedParameterPerOperator()`):

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| `quote_val` | string | nein | `double` |
| `server_url` | string | nein | `relative` |

`quote_val` wird von `ezurl`, `ezroot`, `ezdesign`, `ezimage` und `exturl` am Ende von `modify()` verwendet: `double`
setzt das Ergebnis in `"`, `single` in `'`, `no` lässt es ohne Anführungszeichen. Jeder andere Wert ergibt doppelte
Anführungszeichen. `server_url` (`relative` oder `full`) wird von `ezurl` und `ezroot` an `eZURI::transformURI()`
übergeben; `full` stellt Schema und Host voran. Die Erweiterung fügt einen dritten Parameter hinzu, `parameters`
(Array, standardmäßig leer), den nur `ezurl` verwendet.

Ist im HTTP-Werkzeug `UseFullUrl` gesetzt, macht `eZHTTPTool::createRedirectUrl()` bei jedem dieser fünf Operatoren
aus einem Ergebnis, das mit `/` beginnt, eine vollständige Adresse.

## Die Operatoren

### ezurl

Generierte Seite: [ezurl](../../beispiele/template-operatoren/ezurl.md).

- **Eingabe:** ein Pfad, ein URL-Alias oder eine vollständige Adresse (der Wert vor `|`). Mit der Erweiterung auch ein
  Knoten oder ein Inhaltsobjekt.
- **Parameter:** `quote_val`, `server_url` und mit der Erweiterung `parameters` (ein Hash mit Optionen, siehe unten).
- **Rückgabe:** die Adresse, in Anführungszeichen, außer wenn `quote_val` gleich `no` ist.

Der Kernel-Code ruft `eZURI::transformURI( $operatorValue, false, $server_url )` auf, das den Siteaccess-Pfad und
`index.php` dort hinzufügt, wo die Anfrage sie braucht. Ein Wert mit einem Schema (`https:`, `mailto:`) oder `//host`
bleibt unverändert und wird, wenn er in Anführungszeichen steht, von `eZURLOperator::escapeAbsoluteURL()` für ein
HTML-Attribut maskiert (deshalb wurde `&` in Zeile 6 unten zu `&amp;`). Die Maskierung macht einen Link nicht sicher:
Ein Wert `javascript:` bleibt einer.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
1 {'content/view/full/90'|ezurl}
2 {$node.url_alias|ezurl}
3 {$node.url_alias|ezurl( 'single' )}
4 {$node.url_alias|ezurl( 'no' )}
5 {$node.url_alias|ezurl( 'no', 'full' )}
6 {'https://example.com/a?b=1&c=2'|ezurl}
```

Ausgabe auf alpha:

```text
1 "/content/view/full/90"
2 "/privacy-policy"
3 '/privacy-policy'
4 /privacy-policy
5 http://alpha.se7enx.com/privacy-policy
6 "https://example.com/a?b=1&amp;c=2"
```

### ezroot

Generierte Seite: [ezroot](../../beispiele/template-operatoren/ezroot.md).

- **Eingabe:** ein Pfad. **Parameter:** `quote_val`, `server_url`. **Rückgabe:** die Adresse in Anführungszeichen.

Dasselbe wie `ezurl`, aber ohne `index.php` und ohne den Siteaccess-Teil: Der Kernel ergänzt ein fehlendes führendes
`/` und ruft `eZURI::transformURI( $value, true, $server_url )` auf. Verwenden Sie es für Dateien, die aus dem
Web-Root ausgeliefert werden.

### ezdesign

Generierte Seite: [ezdesign](../../beispiele/template-operatoren/ezdesign.md).

- **Eingabe:** ein Pfad innerhalb eines Designs (`stylesheets/core.css`). **Parameter:** `quote_val`.
  **Rückgabe:** der Pfad des ersten Treffers in Anführungszeichen.

`eZURLOperator::eZDesign()` sucht die Datei in jeder Design-Basis (`eZTemplateDesignResource::allDesignBases()`: das
Design des Siteaccess, die zusätzlichen Designs, die Designs der Erweiterungen, `standard`) und gibt das
www-Verzeichnis plus den Pfad der ersten gefundenen Datei zurück, HTML-maskiert. Passt nichts, protokolliert es zwei
Warnungen ("Design element ... does not exist in any design" und die versuchten Dateien) und gibt
`design/<SiteDesign>/<path>` zurück.

### ezimage

Generierte Seite: [ezimage](../../beispiele/template-operatoren/ezimage.md).

- **Eingabe:** ein Bildname. **Parameter:** `quote_val`; ein zweiter Positionswert `true()` verlangt den Pfad ohne das
  führende `/`, wenn die Installation im Web-Root läuft (leeres www-Verzeichnis). **Rückgabe:** der Pfad in
  Anführungszeichen.

Wie `ezdesign`, sucht aber im Ordner `images` jeder Design-Basis. Fehlende Bilder protokollieren dieselben zwei
Warnungen.

```tpl
1 {'/robots.txt'|ezroot}
2 {'favicon.ico'|ezroot( 'no' )}
3 {'stylesheets/core.css'|ezdesign( 'no' )}
4 {'1x1.gif'|ezimage( 'no' )}
5 {'1x1.gif'|ezimage( 'no', true() )}
```

Ausgabe auf alpha:

```text
1 "/robots.txt"
2 /favicon.ico
3 /extension/ezwebin/design/ezwebin/stylesheets/core.css
4 /design/standard/images/1x1.gif
5 design/standard/images/1x1.gif
```

Zeile 3 zeigt den Design-Fallback: Die erste Design-Basis der Liste, die `stylesheets/core.css` hat, antwortet, hier
das ezwebin-Design einer Erweiterung.

### exturl

Generierte Seite: [exturl](../../beispiele/template-operatoren/exturl.md).

- **Eingabe:** eine externe Adresse. **Parameter:** `quote_val`. **Rückgabe:** die Adresse in Anführungszeichen, wie
  sie in der Link-Tabelle gespeichert ist.

`exturl` sucht die Adresse über ihren MD5-Wert in der Tabelle `ezurl` (`eZURL::urlByMD5()`); ist sie dort nicht
vorhanden, **registriert es sie** mit `eZURL::registerURL()`, sodass der Link-Prüfer und die URL-Verwaltung in der
Administrationsoberfläche sie kennen.

Auf alpha nicht ausgeführt: `exturl` schreibt für jede Adresse, die es noch nicht gesehen hat, eine Zeile in die
Tabelle `ezurl`, und diese Beispiele verändern nichts. Verwendet wird es als `{'https://example.com/'|exturl}`, was
`"https://example.com/"` ergibt.

### ezsys

Generierte Seite: [ezsys](../../beispiele/template-operatoren/ezsys.md).

- **Eingabe:** keine (es wird als Funktion aufgerufen). **Parameter:** der Attributname als Positionsparameter.
  **Rückgabe:** der Wert von `eZSys::attribute()`.

Die Attribute sind `wwwdir`, `sitedir`, `indexfile`, `indexdir`, `querystring` und `hostname`
(`eZSys::attributes()` in `lib/ezutils/classes/ezsys.php`). Ohne Namen protokolliert der Operator die Warnung
"Requires attributename"; ein unbekannter Name protokolliert "No such attribute". Die Werte beschreiben die aktuelle
Anfrage, daher sind `wwwdir`, `indexfile` und `querystring` auf der Kommandozeile leer.

### ezhttp

Generierte Seite: [ezhttp](../../beispiele/template-operatoren/ezhttp.md).

| Position | Bedeutung | Standardwert |
|---|---|---|
| 1 | Variablenname | (keiner: `ezhttp()` gibt das Objekt `eZHTTPTool` selbst zurück) |
| 2 | `post`, `get`, `session` oder `cookie` | `post` |
| 3 | `true()` oder `'hasVariable'`: zurückgeben, ob sie existiert, statt ihres Werts | |

- **Eingabe:** keine (als Funktion aufgerufen).
- **Rückgabe:** der Wert der Variablen; mit der Existenzprüfung `true` oder `false`.
- Ein unbekannter Typ protokolliert die Warnung "Unknown http type" und fällt auf `post` zurück.
- Eine fehlende Variable ohne Existenzprüfung ist ein **Fehler** ("Unknown get variable 'q'"); prüfen Sie also zuerst.
- Bei `session` kommt `null` zurück, solange noch keine Sitzung gestartet wurde (verzögerte Sitzungen).

Der Wert ist der unbearbeitete Wert der Anfrage: Bereinigen Sie ihn vor der Ausgabe (`|wash`).

### ezhttp_hasvariable

Generierte Seite: [ezhttp_hasvariable](../../beispiele/template-operatoren/ezhttp_hasvariable.md).

Derselbe Code wie `ezhttp` mit stets eingeschalteter Existenzprüfung: Parameter 1 (Name) und 2 (Typ, Standardwert
`post`), keine Eingabe, Rückgabe `true` oder `false` (oder `null` bei einer noch nicht gestarteten Sitzung). Die Zeilen
1 bis 3 unten verwenden beide Formen; die Zeilen 4 bis 6 zeigen `ezhttp()` ohne Namen und `ezsys`.

```tpl
1 {ezhttp_hasvariable( 'q', 'get' )|choose( 'no', 'yes' )}
2 {ezhttp( 'q', 'get', 'hasVariable' )|choose( 'no', 'yes' )}
3 {ezhttp_hasvariable( 'theme', 'cookie' )|choose( 'no', 'yes' )}
4 {ezhttp()|get_class}
5 wwwdir=[{ezsys( 'wwwdir' )}] indexfile=[{ezsys( 'indexfile' )}]
6 indexdir=[{ezsys( 'indexdir' )}] hostname=[{ezsys( 'hostname' )}] querystring=[{ezsys( 'querystring' )}]
```

Ausgabe auf alpha:

```text
1 no
2 no
3 no
4 ezhttptool
5 wwwdir=[] indexfile=[]
6 indexdir=[] hostname=[alpha.se7enx.com] querystring=[]
```

### ezini

Generierte Seite: [ezini](../../beispiele/template-operatoren/ezini.md).

| Position | Bedeutung | Standardwert |
|---|---|---|
| 1 | Gruppe (Block) | erforderlich: ohne sie der Fehler "Missing group name parameter" |
| 2 | Einstellung | erforderlich: ohne sie der Fehler "Missing variable name parameter" |
| 3 | INI-Datei | `site.ini` |
| 4 | Verzeichnis, aus dem die Datei direkt gelesen wird | (die normale Override-Kette) |
| 5 | kompilierte Templates: `true()` liest den Wert zur Laufzeit statt zur Kompilierzeit | `[eZINISettings] DynamicTemplateMode` |
| 6 (nur `ezini`) | `true()` oder `'hasVariable'`: zurückgeben, ob sie existiert | |

- **Rückgabe:** die Einstellung (eine Zeichenkette oder bei Einstellungen der Form `Name[]` ein Array); mit der
  Existenzprüfung `true`/`false`.
- Eine fehlende Einstellung ist ein Fehler ("No such variable ... for site.ini"), außer wenn Parameter 4 angegeben ist;
  dann ist das Ergebnis eine leere Zeichenkette.
- **Eingabe:** keine (als Funktion aufgerufen).
- Die Werte stammen aus den aufgelösten Einstellungen des aktuellen Siteaccess, mit allen Overrides und Erweiterungen.

### ezini_hasvariable

Generierte Seite: [ezini_hasvariable](../../beispiele/template-operatoren/ezini_hasvariable.md).

`ezini` mit stets eingeschalteter Existenzprüfung: Parameter 1 bis 5 wie oben, Rückgabe `true` oder `false`, nie ein
Fehler. Die Zeilen 6 und 7 unten; Zeile 5 ist dieselbe Prüfung über Parameter 6 von `ezini`.

```tpl
1 {ezini( 'SiteSettings', 'DefaultAccess' )}
2 {ezini( 'DesignSettings', 'SiteDesign' )}
3 {ezini( 'TemplateSettings', 'TemplateCompile' )}
4 {ezini( 'MultisiteSettings', 'ResolveUnprefixedPaths', 'ezurlmultisite.ini' )}
5 {ezini( 'SiteSettings', 'NoSuchSetting', 'site.ini', false(), false(), true() )|choose( 'missing', 'present' )}
6 {ezini_hasvariable( 'SiteSettings', 'DefaultAccess' )|choose( 'no', 'yes' )}
7 {ezini_hasvariable( 'SiteSettings', 'NoSuchSetting' )|choose( 'no', 'yes' )}
8 {ezini( 'SiteAccessSettings', 'AvailableSiteAccessList' )|count} siteaccesses
```

Ausgabe auf alpha:

```text
1 site
2 media
3 enabled
4 disabled
5 missing
6 yes
7 no
8 18 siteaccesses
```

## Wie ezurlmultisite die Operatoren übernimmt

Die Erweiterung schaltet sich in `extension/ezurlmultisite/settings/site.ini.append.php` selbst ein:

```ini
[TemplateSettings]
ExtensionAutoloadPath[]=ezurlmultisite
```

Dadurch liest die Template-Engine `extension/ezurlmultisite/autoloads/eztemplateautoload.php` nach der Autoload-Datei
des Kernels. Sie registriert `eZURLMultisiteURLOperator` für alle zehn Namen. Weil eine spätere Registrierung eines
Namens die frühere in `eZTemplate::$Operators` ersetzt, wird die Kernel-Klasse nie erzeugt. Der Kommentar in dieser
Datei sagt, warum **alle zehn** Namen aufgeführt sein müssen: Die Engine registriert beim ersten Aufruf eines dieser
Namen ein Objekt für jeden Namen aus dessen `operatorList()`; ein einziger fehlender Name würde also das Kernel-Objekt
für den Rest der Anfrage zurückbringen.

Die abgeleitete Klasse ändert nur `ezurl`:

- Mit einem nicht leeren Hash `parameters` oder mit `[OperatorSettings] ForceAbsoluteURL=enabled` in
  `ezurlmultisite.ini` ruft sie `eZURLMultisite::buildURL()` auf, das diese Schlüssel kennt: `absolute`, `siteaccess`,
  `hash`, `query`, `user_parameters`, `no_self_link`, `node_id`, `link_type`, `protocol`, `debug`
  (`eZURLMultisite::extractOptions()`).
- Andernfalls fragt sie `eZURLMultisite::crossSiteURL()`, ob der Pfad, der Knoten oder das Objekt zu einem anderen
  beteiligten Siteaccess gehört. Ist das so, ist der Link die Adresse dieses Siteaccess; wenn nicht, läuft der
  Kernel-Code unverändert.

Die beteiligten Siteaccesses sind `[MultisiteSettings] SiteAccessList[]` oder, wenn diese Liste leer ist, jeder
Siteaccess mit `RequireUserLogin=false` und einem Pfadpräfix. Unten gehört Knoten 74 zur Website Bold Agency; ein
Template im Siteaccess `site` verlinkt ihn deshalb unter `/bold/`, ohne dass man es ihm sagen muss:

```tpl
{def $bold = fetch( 'content', 'node', hash( 'node_id', 74 ) )}
1 {$bold.url_alias|ezurl( 'no' )}
2 {$bold|ezurl( 'no' )}
3 {$bold.url_alias|ezurl_path}
4 {$bold.url_alias|ezurl_siteaccess}
5 {'privacy-policy'|ezurl_siteaccess}
6 {'privacy-policy'|ezurl( 'no', 'relative', hash( 'hash', 'top', 'query', 'a=1' ) )}
7 {'privacy-policy'|ezurl( 'no', 'relative', hash( 'absolute', true() ) )}

{foreach ezurl_roots() as $name => $r}
{$name}: host={$r.host} prefix={$r.prefix} root={$r.root_node_id}

{/foreach}
```

Ausgabe auf alpha:

```text
1 /bold/privacy-policy
2 /bold/privacy-policy
3 privacy-policy
4 bold
5 site
6 /privacy-policy?a=1#top
7 http://alpha.se7enx.com/privacy-policy
site: host=alpha.se7enx.com prefix=fit-healthy root=89
bold: host=alpha.se7enx.com/bold prefix=bold-agency root=73
bold_ger: host=alpha.se7enx.com/bold_ger prefix=bold-agency root=73
starter: host=alpha.se7enx.com/starter prefix=starter root=31927
starter_ger: host=alpha.se7enx.com/starter_ger prefix=starter root=31927
```

## Die Operatoren von ezurlmultisite

Registriert von `eZURLMultisiteOperator` (`extension/ezurlmultisite/classes/ezurlmultisiteoperator.php`), nur solange
die Erweiterung aktiv ist. Eine Parameterliste für alle drei:

| Parameter | Typ | Erforderlich | Standardwert |
|---|---|---|---|
| `parameters` | array | nein | leer |
| `quote_val` | string | nein | `double` |
| `server_url` | string | nein | `relative` |

Sie haben keine generierten Seiten; das Beispiel im vorigen Abschnitt zeigt alle drei.

### ezurl_path

- **Eingabe:** ein Pfad, ein Knoten oder ein Objekt. **Rückgabe:** der Pfad ohne das Pfadpräfix des Siteaccess, zu
  dem er gehört (`eZURLMultisite::buildPath()`), ohne Anführungszeichen; `parameters.siteaccess` wählt den Siteaccess.
  Zeile 3 des Beispiels oben.

### ezurl_siteaccess

- **Eingabe:** dieselbe. **Rückgabe:** der Name des Siteaccess, der es anzeigen soll
  (`eZURLMultisite::buildSiteAccessName()`): `parameters.siteaccess`, wenn angegeben, eine leere Zeichenkette für eine
  absolute Adresse und der aktuelle Siteaccess für Inhalte, die zu keiner anderen Website gehören. Die Zeilen 4 und 5
  oben.

### ezurl_roots

- **Eingabe:** keine (rufen Sie es als `ezurl_roots()` auf). **Rückgabe:** ein Hash je beteiligtem Siteaccess mit
  `siteaccess`, `host`, `prefix` und `root_node_id` (`eZURLMultisite::buildRoots()`). Die Schleife am Ende des
  Beispiels oben.

## Kompilierte Templates

Die Hinweise des Kernels kennzeichnen alle zehn Operatoren für die Elementtransformation: Sind Eingabe und Parameter
Konstanten, kann der Compiler das Ergebnis zur Kompilierzeit erzeugen. `ezini` mit konstanter Gruppe und konstantem
Namen wird **beim Kompilieren des Templates** gelesen und in der kompilierten Datei gespeichert
(`eZURLOperator::iniTrans()`), außer wenn Parameter 5 `true()` ist oder `[eZINISettings] DynamicTemplateMode=enabled`
gilt: Dann liest der kompilierte Code die INI bei jedem Durchlauf. Leeren Sie nach dem Ändern einer Einstellung, die ein
Template mit `ezini` liest, die kompilierten Templates ebenso wie den INI-Cache.

`eZURLMultisiteURLOperator::operatorTemplateHints()` schaltet die Transformation für `ezurl` ab, weil der Siteaccess,
zu dem ein Link gehört, vom Siteaccess abhängt, der das kompilierte Template anzeigt.

## Verwandte Seiten

- [Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md): die allgemeinen Regeln hinter der Übernahme durch ezurlmultisite
- [Kernel-Operatoren überschreiben](kernel-operatoren-ueberschreiben.md)
- [URL-Operatoren für Designer](../designer/vorlagen/url-operatoren.md)
- [Der Operator ezini](../designer/vorlagen/der-ezini-operator.md)
- [Der Operator ezhttp](../designer/vorlagen/der-ezhttp-operator.md)
- [Operatorreferenz](../designer/vorlagen/operatorreferenz.md)
- [Alle Template-Operatoren (generiert)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
