---
title: Die Template-Sprache
audience: developer, designer, app-creator
category: development
order: 0
summary: Die Template-Sprache von Exponential (eZ TPL, ezpl) im Detail - was sie ist und woher sie kommt, ihre Syntax neben Smarty, Werte, Variablen und Namensräume, Kontrollstrukturen, das Objektmodell von Knoten, Objekten und Attributen, Templates von Modulansichten, der Compiler und die Caches und wie Sie sie erweitern - mit jedem Beispiel auf alpha gerendert.
keywords: Template-Sprache, eZ TPL, ezpl, tpl, Smarty, Syntax, Objektmodell, Modulergebnis, Compiler, Cache, Template-Funktionen, Operatoren, template language, syntax, object model, module result, compiler, cache, template functions, operators
type: help
---
# Die Template-Sprache

Exponential rendert jede Seite, jede Mail und jedes PDF mit seiner eigenen Template-Sprache, bekannt als eZ TPL oder ezpl: Tags in
geschweiften Klammern wie bei Smarty, aber mit Operatoren und Funktionen für Inhaltsobjekte, einer Fetch-API zum Lesen von Daten, einem
Override-System zur Wahl von Templates und einem Compiler, der Templates in PHP umwandelt. Dieser Abschnitt dokumentiert die Sprache selbst,
aus dem Code in `lib/eztemplate/` und dem Kernel, auf 28 Seiten.

Jedes Beispiel auf diesen Seiten wurde auf alpha gerendert, der eigenen Installation des Projekts, gegen ihre echten Inhalte (die Website
Fit & Healthy), mit der Ausgabe darunter, genau wie die Template-Engine sie erzeugt hat; die Beispiele wurden außerdem in einen
Arbeitsordner kompiliert und verglichen. Wo eine Seite Code zeigt, der nicht ausgeführt wurde, sagt sie das und warum.

Für eine sanftere, aufgabenorientierte Einführung beginnen Sie mit dem [Überblick über Templates](../../../en/us/help/designers/templates/templates-overview.md)
für Designer und den [Grundlagen der TPL-Syntax](../../../en/us/help/designers/templates/tpl-syntax-basics.md). Jeder Operator ist in
[Template-Operatoren](../../../en/us/help/template-operators/README.md) dokumentiert.

## Die Sprache

- [Was die Template-Sprache ist](was-die-template-sprache-ist.md): Die Template-Sprache von Exponential (eZ TPL, ezpl) auf einer Seite - eine Smarty-ähnliche Sprache für Dateien .tpl, wo sie genutzt wird (Designs, Modulansichten, Mail, PDF), die Teile der Engine in lib/eztemplate und was sie von Haus aus über Smarty hinaus kann.
- [Geschichte der Template-Sprache](geschichte-der-template-sprache.md): Wie die Template-Sprache gewachsen ist, nur aus dem erzählt, was das Repository festhält - die Git-Geschichte ab 2002, die Changelogs ab 2.9, der Compiler von 2003, die Syntax mit def/if/foreach von 2005 (Version 3.6), die let und section ersetzte, und was vom älteren Stil noch übrig ist.
- [Smarty und die Template-Sprache nebeneinander](smarty-nebeneinander.md): Eine Tabelle, die die Syntax von Smarty und die Template-Sprache von Exponential nebeneinanderstellt - Ausgabe, Modifikatoren und Operatoren, Zuweisung, Standardwerte, Bedingungen, Schleifen, Wechsel, include, literal, Begrenzer, Kommentare, capture, Maskierung, Daten, Caching und Vererbung - mit jedem Beispiel der Template-Sprache auf alpha gerendert.
- [Smarty und die Template-Sprache: die Unterschiede im Detail](smarty-unterschiede-im-detail.md): Die Unterschiede, über die Smarty-Benutzer stolpern, jeder auf alpha gezeigt - keine Arithmetik oder Vergleiche in Infixschreibweise, kein PHP in Templates, keine automatische Maskierung, unbekannte Operatoren, die ihre Eingabe durchreichen, strenges def und set, Klammern in CSS und JavaScript, Operatoren als registrierte PHP-Klassen, fetch und Objekte, Overrides statt Vererbung, Namensräume und Leerraum.
- [Ein Template von Smarty umziehen](ein-template-von-smarty-umziehen.md): Eine Anleitung zum Portieren - ein typisches Listen-Template aus Smarty in drei Schritten in die Template-Sprache übertragen (Variablen, Daten aus fetch, die Schleife mit Bedingungen, Links, Daten und Maskierung), jeder Schritt auf alpha gerendert, plus eine Checkliste der vorzunehmenden Ersetzungen.
- [Lexikalische Struktur](lexikalische-struktur.md): Wie der Parser ein Template liest - die Begrenzer, die Arten von Tags (Ausgabe-Tags, Funktions-Tags, End-Tags, Einzel-Tags), Kommentare, literal-Blöcke, ldelim und rdelim, eine Klammer in einem Tag maskieren, die Regel, die einen Zeilenumbruch um jedes Funktions-Tag entfernt, und wie Parserfehler Zeile und Spalte melden.
- [Werte und Literale](werte-und-literale.md): Die Werte, die ein Template hinschreiben kann - Zeichenketten in einfachen oder doppelten Anführungszeichen (keine Interpolation von Variablen, die Escapes, die der Parser kennt), ganze Zahlen und Gleitkommazahlen, true() und false(), mit array() und hash() gebaute Arrays und Hashes, bloße Bezeichner und was für null steht - jedes auf alpha mit get_type gezeigt.
- [Variablen im Detail](variablen-im-detail.md): Die genauen Regeln von def, undef, set, let und default aus eZTemplateDefFunction und eZTemplateSetFunction - die Warnungen bei doppelter Definition, beim Setzen einer unbekannten Variablen und beim Aufheben einer, die def nicht angelegt hat, undef ohne Argumente, die Lebensdauer einer Variablen (die Datei, der let-Block) und set mit scope=relative, root oder global über ein include hinweg.
- [Namensräume](namensraeume.md): Wie die Template-Engine Variablen auseinanderhält - der Wurzel- und der aktuelle Namensraum, die vier Arten, eine Variable zu benennen ($var, $ns:var, $:var, $#var), was include mit und ohne name= tut, die Namensräume, die section und let anlegen, und der Fehler bei einer Variablen im falschen Namensraum.
- [Zugriff auf Attribute](zugriff-auf-attribute.md): Wie $a.b und $a[b] aufgelöst werden - Arrays nach Schlüssel, Objekte über attributes(), hasAttribute() und attribute(), Felder und Funktionsattribute von eZPersistentObject, dynamische Schlüssel, numerische Indizes, Attribute nach einem Operator, der Fehler mit seiner Liste von Wahlmöglichkeiten und die Operatoren attribute und dump, um in einen Wert hineinzusehen - gezeigt an Knoten 90 von alpha.
- [Operatoren in Ausdrücken](operatoren-in-ausdruecken.md): Wie Operatoren geschrieben und ausgewertet werden - die Pipe-Form und die Aufruf-Form, was der Eingabewert in jeder ist, Verkettung, Operatoren als Parameter verschachteln, Attribute nach einem Operator, Operatoren in Parametern von Funktionen, wie Parameter benannten Parametern mit Standardwerten zugeordnet werden und was bei einem unbekannten Operator oder einem fehlenden Parameter geschieht.
- [Funktionen und Operatoren](funktionen-und-operatoren.md): Die zwei Arten benannter Dinge in der Template-Sprache verglichen - Template-Funktionen sind Tags, die Ausgabe schreiben und das Template steuern, Operatoren berechnen Werte - mit ihren Arrays zur Registrierung, den Methoden, die jede PHP-Klasse umsetzt, Block-Funktionen und Kindern, interpretierter Verarbeitung gegenüber kompilierter Transformation und wann Sie welches schreiben.
- [Referenz der Kontrollstrukturen](referenz-der-kontrollstrukturen.md): Jeder Parameter von if, elseif und else, switch und case (match, in, key und der Standardfall), foreach (key, sequence, offset, max, reverse), for, while und do und die Schleifenhelfer delimiter (mit modulo), break, continue und skip - mit ihrem genauen Verhalten aus dem Code, jeder auf alpha gezeigt.
- [Sections und foreach](sections-und-foreach.md): Die Funktion section vollständig - Schleifen über Arrays, Zahlen und Zeichenketten, name und der Namensraum, den sie anlegt, var und der Iterator mit last-value, sequence, offset, max, reverse, show mit section-else, die Filter section-exclude und section-include und delimiter mit modulo - jede neben ihrer Entsprechung mit foreach, for oder if, alles auf alpha gerendert, plus der eine gefundene Unterschied im kompilierten Modus.

## Das Objektmodell in Templates

- [Das Objektmodell in Templates](das-objektmodell-in-templates.md): Wie ein Template die PHP-Objekte des Inhaltsmodells sieht - Knoten, Inhaltsobjekt, Version, Data Map, Inhaltsobjekt-Attribut und der Inhalt seines Datentyps - und das Attributprotokoll (attributes, hasAttribute, attribute), das die Engine für jeden Punkt in einem Variablenpfad nutzt, an echten Inhalten belegt.
- [Referenz der Knotenattribute](referenz-der-knotenattribute.md): Jedes Attribut, das ein Template von einem Knoten (eZContentObjectTreeNode) lesen kann - die 16 Datenbankfelder und die 35 Funktionsattribute seiner Definition, die PHP-Methode hinter jedem und der Wert, den jedes für einen echten Artikel auf alpha zurückgab.
- [Referenz der Attribute von Inhaltsobjekten](referenz-der-objektattribute.md): Jedes Attribut, das ein Template von einem Inhaltsobjekt (eZContentObject) lesen kann - die 12 Datenbankfelder und die 65 Funktionsattribute seiner Definition, die PHP-Methode hinter jedem und der Wert, den jedes für einen echten Artikel auf alpha zurückgab.
- [Referenz von Inhaltsattribut und Version](referenz-von-inhaltsattribut-und-version.md): Jedes Attribut, das ein Template von einem Inhaltsobjekt-Attribut (eZContentObjectAttribute, die Einträge von data_map) und von einer Version (eZContentObjectVersion) lesen kann, mit der PHP-Methode hinter jedem, Werten von alpha und dem, was .content für jeden Datentyp in den Inhalten von alpha zurückgibt.
- [Data Map und Inhalt](data-map-und-inhalt.md): Die Felder eines Inhaltsobjekts in Templates lesen - data_map, has_content, content, die rohen data_text und data_int und attribute_view_gui - Feld für Feld an einem echten Artikel auf alpha belegt (ezstring, ezxmltext, ezimage, ezobjectrelationlist, eztags, Daten).

## Templates von Modulansichten

- [Templates von Modulansichten](templates-von-modulansichten.md): Wie eine Modulansicht ihre Templates rendert - die PHP-Seite (eZTemplate::factory(), setVariable(), fetch( 'design:...' )), das Array $Result, das eine Ansicht zurückgibt, und wie der Kernel es dem Pagelayout als $module_result zusammen mit den anderen Variablen des Pagelayouts übergibt - belegt durch nur lesendes Ausführen von content/view auf alpha.
- [Referenz des Modulergebnisses](referenz-des-modulergebnisses.md): Jeder im Kernel gefundene Schlüssel von $Result / $module_result - content, path, title_path, content_info und seine 25 Schlüssel, view_parameters, pagelayout, navigation_part, left_menu, errorType und der Rest - mit dem Code, der jeden setzt, und den Werten, die content/view auf alpha zurückgab.
- [Ansichtsparameter](ansichtsparameter.md): Der Teil /(name)/value einer URL - wie eZURI ihn zerlegt, wie eZModule::run() ihn den ungeordneten Parametern und UserParameters einer Ansicht zuordnet, was content/view daraus macht ($view_parameters, eine Variable pro Name, content_info.offset) und wie er den Schlüssel des Content-View-Cache ändert - auf alpha belegt.
- [Die persistente Variable, von innen](die-persistente-variable-von-innen.md): Wie $persistent_variable von einem Template einer Knotenansicht zum Pagelayout gelangt - der Kernel setzt sie auf false, liest sie nach dem Rendern aus dem Wurzel-Namensraum zurück, speichert sie in content_info und im Content-View-Cache und macht sie zu einem Override-Schlüssel - und warum scope='global' zählt, mit Namensräumen auf alpha belegt.

## Compiler, Caches und Ressourcen

- [Der Template-Compiler](der-template-compiler.md): Wie eZTemplateCompiler ein gelesenes Template in PHP umwandelt - die Pipeline (Transformation der Knoten, statische Optimierungen, Zusammenfassen, Erzeugen von PHP), Name und Ordner der kompilierten Datei, die Hinweise von Operatoren und Funktionen, mit denen ein Operator zu Inline-PHP wird, der Rückfall processOperator, DevelopmentMode, die Einstellungen - mit erzeugtem Code von alpha und einem echten Unterschied zwischen kompilierter und interpretierter Ausgabe.
- [Template-Caches](template-caches.md): Jeder Cache, den ein Template berührt - kompilierte Templates, der Template-Block-Cache von cache-block (Schlüssel, expiry, subtree_expiry, ignore_content_expiry und wie der Name der Cache-Datei gebaut wird), der Content-View-Cache und sein Schlüssel, der Cache der Template-Overrides, die Caches für Design-Basen und INI - mit den IDs von ezcache, die jeden leeren, und dem, was exp:velocity deploy leert.
- [Template-Ressourcen und Overrides](template-ressourcen-und-overrides.md): Wie aus einem Template-Namen eine Datei wird - die Ressourcen design: und file:, die geordneten Design-Basen eines Siteaccess (Website-Design, zusätzliche Designs, Standard-Design, jedes zuerst mit seinen Designs von Erweiterungen), Regeln in override.ini mit ihren Match-Schlüsseln und ihrer Reihenfolge und wie include und die Ansichtsfunktionen sie nutzen - mit der echten Auflösung auf dem Siteaccess site von alpha.
- [Fehler und Fehlersuche](fehler-und-fehlersuche.md): Die Meldungen, die die Template-Engine protokolliert - unbekannte Variable, unbekanntes Attribut, unbekannter Operator oder unbekannte Funktion, Parserfehler mit Zeile und Spalte, ein fehlendes Template, Fehlgebrauch von def/set/undef - jede auf alpha erzeugt, was die Engine in jedem Fall ausgibt, die Leerraumregel, die alle überrascht, und die Debug-Einstellungen (Debug, ShowXHTMLCode, ShowUsedTemplates, ShowTemplatePathComments, ShowMethodDebug, DisplayWarnings).

## Die Sprache erweitern

- [Eigene Template-Funktionen](eigene-template-funktionen.md): Eine Template-Funktion (ein Tag in geschweiften Klammern mit benannten Parametern und wahlweise einem Rumpf) in einer Erweiterung schreiben - functionList, hasChildren, attributeList, process, Parameter auswerten, den Rumpf rendern, Registrierung in eztemplateautoload.php, Hinweise zur Kompilierzeit - und die Funktionen des Kernels erweitern oder überschreiben, mit einer Beispielfunktion auf alpha gerendert.

## Verwandte Seiten

- [Template-Operatoren](../../../en/us/help/template-operators/README.md): jeder Operator, Fetch-Aliase, Fetch-Funktionen
- [Templates für Designer](../../../en/us/help/designers/templates/templates-overview.md)
- [Referenz der Funktionen](../../../en/us/help/designers/templates/function-reference.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
