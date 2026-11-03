# Twig-Templates: Problembehebung und häufige Fragen

Diese Seite ist für Template-Entwickler, deren `.twig` nicht tut, was sie erwarten. Jeder Eintrag nennt, was Sie sehen,
die Ursache und die Abhilfe. Die meisten Fehler findet `./console exp:template:check <file or --design-dir=...>`, bevor
eine Seite sie zeigt; mit eingeschalteter Debug-Ausgabe hinterlässt ein scheiterndes Template dort, wo seine Ausgabe
gestanden hätte, einen Kommentar mit der Meldung, und jedes Scheitern wird in die Debug-Ausgabe und nach
`var/log/error.log` geschrieben.

## Die Seite zeigt nichts, wo das .twig stehen sollte

Ein Template, das nicht kompiliert oder beim Ausführen scheitert, druckt nichts. Suchen Sie die Meldung in der
Debug-Ausgabe (oder im HTML-Kommentar `<!-- twig: rendering ... failed: ... -->`), oder führen Sie die Prüfung auf der
Datei aus.

## Unknown "wahs" filter

```
ERROR  twig  templates/full/article.twig:1
       Unknown "wahs" filter. [TWIG-SYNTAX-001]
       > 1 | {{ node.name|wahs }}
       hint: did you mean "wash"?
```

Der Name ist kein Operator der Installation und kein Filter von Twig. Ein Tippfehler, oder der Operator gehört zu einer
Extension, die in diesem Siteaccess nicht aktiv ist. `exp_<name>` eines unbekannten Operators kompiliert, reicht seine
Eingabe aber durch; `--level=strict` meldet es als `TWIG-STRICT-UNKNOWN-OPERATOR`.

## Tag "do" is not allowed / Calling "x" method ... is not allowed

Die Sandbox hat eines von Twigs eigenen Tags, Filtern oder Funktionen abgelehnt, oder einen Methodenaufruf auf einem
Objekt. Lesen Sie Werte über ihre Attribute (`node.object.name`, nicht `node.object.name()` oder
`node.attribute('name')`); erlauben Sie ein Twig-Konstrukt in `[TwigSettings] SecurityAllowed...List[]` eines
Siteaccess, dem Sie vertrauen. Siehe [Sicherheit](twig-templates-sicherheit.md).

## Ein Operator liefert ein anderes Ergebnis als im .tpl

Prüfen Sie die Form: `x|op(a)` ist das `$x|op( a )` des `.tpl`, `op(x, a)` ist `op( $x, a )`. Bei den meisten Operatoren
sind das unterschiedliche Aufrufe. Und prüfen Sie den Namen: Wo Twig einen Filter gleichen Namens hat (`merge`, `round`,
`replace`, `trim` ...), gehört der einfache Name Twig; der Operator ist `exp_merge`. Siehe
[Operatoren](twig-templates-operatoren.md).

## Eine in einem eingebundenen Template gesetzte Variable ist nicht zu sehen

`{% set %}` in einem `.twig`, das mit `{% expinclude %}` eingebunden ist, schreibt in den gemeinsamen Namensraum
zurück, wenn dieses Template endet, sodass das einbindende Template sie danach sieht. Ein auf dieselbe Weise
eingebundenes `.tpl` behält sein `{def}` für sich, wie es das immer getan hat. Twigs eigenes `{% include %}` übergibt
eine Kopie der Variablen und schreibt nichts zurück: Nehmen Sie `expinclude`, wo das eingebundene Template etwas setzt.

## Eine Änderung an einem .twig ist nicht zu sehen

`[TwigSettings] AutoReload=enabled` (die Voreinstellung) kompiliert eine geänderte Datei bei der nächsten Anfrage neu,
auch in den Workern von Velocity. Wenn eine Änderung nicht zu sehen ist: Die Seite kommt womöglich aus einem Cache
(View-Cache, Cache-Block, HTTP-Cache oder der Antwort-Cache von Velocity: Leeren Sie sie, oder sehen Sie mit
eingeschalteter Debug-Ausgabe nach), oder `AutoReload` ist in diesem Siteaccess abgeschaltet (leeren Sie die kompilierten
Templates: `./console exp:ezcache --clear-id=twig`).

## Twig 3 not found

```
Twig 3 not found: install twig/twig with Composer or set [TwigSettings] LibraryPath in twig.ini
```

Der Composer-Autoload der Installation hat kein Twig 3. Führen Sie `composer update` mit der `composer.json` der
Installation aus: Sie verlangt `se7enxweb/exponential-php-floor` `~1.0.3 || ~1.1.4 || ~1.2.3 || ~1.3.3`, was auf PHP
8.1 und neuer `twig/twig` `^3.27` installiert und auf PHP 7.4 und 8.0 `se7enxweb/twig` (ein Floor älter als x.y.3
installiert kein Twig). Oder installieren Sie Twig in ein eigenes Verzeichnis und setzen Sie `LibraryPath`
([Twig installieren](../funktionen/6.0/twig-templates.md#twig-installieren)). `./console exp:template:engines` zeigt, ob
Twig verfügbar ist.

## Eine Schleife hat nach 10000 Runden angehalten

Ein umgewandeltes `{while}` oder `{do}` hält nach `[TwigSettings] WhileLoopLimit` Runden an und schreibt einen Hinweis
in die Debug-Ausgabe. Heben Sie das Limit für diesen Siteaccess an, oder korrigieren Sie die Bedingung der Schleife.

## Häufige Fragen

**Können ein .tpl und ein .twig in einer Seite benutzt werden?** Ja: Jedes Template einer Seite kann zu jeder Engine
gehören. Ein `.tpl` bindet ein `.twig` ein und umgekehrt, über die Design-Ressource.

**Muss ich alle Templates umwandeln?** Nein. Wandeln Sie um oder schreiben Sie in Twig, was Sie möchten; der Rest bleibt
`.tpl`.

**Macht Twig Seiten langsamer?** Nein: Gecachte Seiten erreichen nie eine Template-Engine, und ungecachte Seiten rendern
etwa so schnell wie mit `.tpl`. Siehe [Leistung](twig-templates-leistung.md).

**Kann ich `{% extends %}` und Blöcke benutzen?** In handgeschriebenen `.twig`, ja. Sie haben keine `.tpl`-Form, deshalb
behält der Konverter zurück nach `.tpl` sie als Anmerkung.

## Siehe auch

- [Template-Syntax prüfen](template-syntaxpruefung.md)
- [Regeln der Template-Syntaxprüfung](../referenz/template-syntaxpruefung-regeln.md)
- [.twig-Templates schreiben und ausführen](twig-templates.md)
