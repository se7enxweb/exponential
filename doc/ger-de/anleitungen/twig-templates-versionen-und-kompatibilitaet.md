# Twig-Templates: Versionen, Upgrades und Kompatibilität

Diese Seite ist für Betreiber und Site-Builder, die eine Installation mit `.twig`-Templates installieren, aktualisieren
oder umziehen. Sie sagt, welche Twig-3-Version auf welcher PHP-Version läuft, was Composer installiert, was geschieht,
wenn Twig fehlt, worauf beim Upgrade von Twig oder PHP zu achten ist und was unter Exponential Velocity besonders ist.
Alles wurde am 29. Oktober 2026 gegen die Paketdaten von Packagist, Composer und eine Entwicklungsinstallation geprüft;
wo etwas nicht ausgeführt wurde, sagt die Seite es.

## Welches Twig für welches PHP

Composer installiert Twig 3 mit dem Rest der Installation, über das Metapaket `se7enxweb/exponential-php-floor`, das
die `composer.json` des Kernels verlangt (`~1.0.3 || ~1.1.3 || ~1.2.3 || ~1.3.3`). Composer wählt das Floor-Release,
das zu der PHP-Version passt, mit der es läuft, und dieses Release verlangt das Twig-3-Paket für diese PHP-Linie:

| PHP | Exponential | Floor-Release | Twig-Paket | Installiertes Twig am 29. Oktober 2026 |
|---|---|---|---|---|
| 8.4 und neuer | unterstützt | 1.3.3 | `twig/twig` `^3.27` | 3.30.0 |
| 8.1 bis 8.3 | unterstützt | 1.2.3 | `twig/twig` `^3.27` | 3.30.0 |
| 8.0 | unterstützt | 1.1.3 | `se7enxweb/twig` `~3.19.1` | 3.19.1 |
| 7.4 | nutzbar ohne Unterstützung | 1.0.3 | `se7enxweb/twig` `~3.11.4` | 3.11.4 |

Eine Installation mit eingeschalteter Sicherheitssperre von Composer (der Standard, den das Projekt auf jeder
PHP-Version eingeschaltet lässt) wurde mit dieser Einrichtung auf PHP 7.4.33, 8.0.30, 8.3.36 und 8.5.12 geprüft: Jede
installierte sich, und `Twig\Environment` und Smarty wurden aus `vendor/` geladen. Ein Floor-Release älter als x.y.3
installiert kein Twig; `composer update` wechselt auf das aktuelle.

**Warum `twig/twig` nicht in der eigenen `composer.json` des Kernels steht.** Jede Twig-Linie von upstream nennt das
PHP, das sie braucht (Packagist-Daten für `twig/twig`, gelesen am 29. Oktober 2026):

| Twig-Linie | Braucht PHP | Neuestes Release der Linie |
|---|---|---|
| 3.0 bis 3.11 | 7.2.5 oder neuer | 3.11.3 (7. November 2024) |
| 3.12 bis 3.19 | 8.0.2 oder neuer | 3.19.0 (29. Januar 2025) |
| 3.20 bis 3.30 | 8.1 oder neuer | 3.30.0 (25. September 2026) |

`twig/twig` von upstream ist bis 3.26 von Sicherheitshinweisen betroffen, und 3.27 und neuer brauchen PHP 8.1. Mit
eingeschalteter Sicherheitssperre findet eine einzelne Anforderung von `twig/twig` auf PHP 8.0 und 7.4 also nichts zu
installieren. Composer sagte das auf PHP 8.0.30 für `twig/twig` `^3`:

```
Problem 1
  - Root composer.json requires twig/twig ^3 -> satisfiable by twig/twig[v3.27.0, ..., v3.30.0].
  - twig/twig[v3.27.0, ..., v3.30.0] require php >=8.1.0 -> your php version (8.0.30) does not satisfy that requirement.
```

Der Fork des Projekts, `se7enxweb/twig`, führt die Linie 3.19 für PHP 8.0 (3.19.1 vom 25. Oktober 2026) und die Linie
3.11 für PHP 7.4 (3.11.4 vom 29. Oktober 2026), mit denselben `Twig\`-Klassen. Er erklärt, dass er `twig/twig` ersetzt;
der Floor verlangt ihn deshalb nur auf diesen PHP-Linien.

**Fügen Sie einer Installation, die auf PHP 7.4 oder 8.0 läuft, kein `twig/twig` hinzu.** Neben dem Fork verlangt,
lässt `twig/twig` Composer die Sicherheitshinweise von `twig/twig` auf `se7enxweb/twig` anwenden, und die Installation
löst sich nicht mehr auf:

```
Problem 1
  - Root composer.json requires se7enxweb/twig ~3.19.1, found se7enxweb/twig[v3.19.1] but these were not loaded, because they are affected by security advisories (...)
```

Auf PHP 8.1 und neuer schadet eine eigene Anforderung von `twig/twig` nicht, solange sie `^3.27` zulässt.

Die Brücke kompiliert Templates im Modus `use_yield` von Twig und kennzeichnet ihre Knoten als yield-fähig, was Twig ab
3.9 bietet; sie braucht also Twig 3.9 oder neuer, und jedes Twig, das der Floor installiert, ist neuer.

Was für diese Seite ausgeführt wurde: `.twig` rendern auf PHP 8.5 mit Twig 3.30.0 (jedes Beispiel dieser Anleitungen);
`php -l` für jede Datei der Twig-Brücke (`lib/twig/classes/`, `lib/templatebridge/classes/`) auf PHP 7.4.33 und 8.0.30,
ohne Fehler; die Installation des Forks auf beiden, allein und neben `twig/twig` (das zweite scheitert wie gezeigt). Die
Installation über den Floor auf den vier PHP-Versionen oben wurde geprüft, als die Floor-Releases veröffentlicht wurden.

## Twig in einem eigenen Verzeichnis installieren

Wenn die Composer-Einrichtung der Installation kein Twig hat (oder um eine andere Twig-Version zu probieren), installieren
Sie es in einem eigenen Verzeichnis und sagen der Brücke, wo es liegt:

```bash
mkdir -p var/lib/twig-vendor && cd var/lib/twig-vendor
composer require twig/twig:^3.27            # PHP 8.1 und neuer
# composer require se7enxweb/twig:~3.19.1   # PHP 8.0 (der Fork allein, ohne twig/twig)
# composer require se7enxweb/twig:~3.11.4   # PHP 7.4
```

```ini
# settings/override/twig.ini.append.php
[TwigSettings]
LibraryPath=var/lib/twig-vendor
```

`LibraryPath` darf ein Composer-Verzeichnis (mit `vendor/autoload.php`) oder das Paket `twig/twig` selbst (mit
`src/Environment.php`) nennen. Es wird nur gelesen, wenn der eigene Composer-Autoload der Installation kein Twig hat. Die
Tests lesen die Umgebungsvariable `TWIG_LIBRARY_PATH` auf dieselbe Weise.

Das Ergebnis prüfen:

```bash
./console exp:template:engines
```

```
AVAILABLE    tpl (Exponential templates) Exponential 6.0.15stable
AVAILABLE    stpl (Smarty templates) Smarty 5.8.5
AVAILABLE    twig (Twig templates)  Twig 3.30.0
             endings .html.twig .twig; settings settings/twig.ini [TwigSettings]; security policy on; compiled var/site/cache/twig/compile-secure
PASS 3 engines available
```

## Wenn Twig nicht installiert ist

Nichts geht kaputt, was Twig nicht benutzt:

- `.tpl`- und `.stpl`-Templates rendern wie bisher; das Template-System lädt Twig erst, wenn ein `.twig` gerendert wird.
- Ein `.twig`, das eine Seite erreicht, druckt an seiner Stelle nichts, und der Fehler `Twig 3 not found: install
  twig/twig with Composer or set [TwigSettings] LibraryPath in twig.ini` geht in die Debug-Ausgabe und nach
  `var/log/error.log`; mit eingeschalteter Debug-Ausgabe trägt die Seite
  `<!-- twig: the Twig library is not available (...) -->` an der Stelle des Templates.
- `./console exp:template:engines` führt `twig` als `UNAVAILABLE ... library not found`, druckt
  `FAIL an engine is not available` und endet mit 3.
- `./console exp:template:check` meldet `UNAVAILABLE twig` und jedes `.twig` als `SKIP`, nie als bestanden, und endet mit
  3, außer mit `--allow-missing-engine`.
- Operatoren und Template-Funktionen, die für das Template-System geschrieben sind, hängen nicht von Twig ab: Der
  Operator einer Extension funktioniert in `.tpl` mit und ohne Twig.

Ein Design kann also `.twig`-Templates mitbringen, und eine Installation kann Twig durch Entfernen abschalten: Die
`.twig`-Teile bleiben leer und sagen im Log, warum. Halten Sie `.tpl`-Fassungen von allem, was nie leer bleiben darf (der
Konverter schreibt sie: `exp:template:convert:twig-to-tpl`).

## Twig aktualisieren

Twig 3 bleibt innerhalb der Hauptversion rückwärtskompatibel, und Veraltetes wird angekündigt, bevor es entfällt. Wenn
Composer auf ein neueres Twig 3 wechselt:

1. Leeren Sie die kompilierten Templates: `./console exp:ezcache --clear-id=twig` (auch Teil von `--clear-tag=template`).
   Die Namen der kompilierten Klassen enthalten die Twig-Version, alte Klassen werden also nie geladen, bleiben aber
   auf der Platte, bis sie geleert werden.
2. Prüfen Sie jedes Design auf Veraltetes, das die neue Version meldet:
   `./console exp:template:check --design-dir=<verzeichnis> --engine=twig --deprecations`
   (Regel `TWIG-DEPRECATION-001`).
3. Unter Velocity starten Sie die Worker neu (`./console exp:velocity restart`), die die alten Twig-Klassen geladen
   haben.

Ein PHP-Upgrade funktioniert genauso: Die Namen der kompilierten Klassen enthalten die Haupt- und Nebenversion von PHP,
ein Template wird also bei seiner ersten Anfrage für das neue PHP neu kompiliert.

## Exponential aktualisieren

Die Twig-Brücke ist Teil der Kernel-Bibliothek (`lib/twig/`, `lib/templatebridge/`, `settings/twig.ini`); sie wird mit dem
Kernel aktualisiert. Nach einem Upgrade:

- leeren Sie die Template-Caches (`./console exp:ezcache --clear-tag=template`): die kompilierten Stubs, die erzeugten
  Dateien der Template-Funktionen in `<Cache-Verzeichnis>/twig/functions/` und die kompilierten `.twig`-Klassen;
- halten Sie Ihre Einstellungen in `twig.ini.append.php`-Dateien; ändern Sie nie `settings/twig.ini`;
- lesen Sie das Änderungsprotokoll von 6.0.x auf geänderte Standardwerte von `[TwigSettings]`, besonders die Listen der
  Sandbox.

## Velocity

Exponential Velocity führt PHP in dauerhaften Workern aus; Twig profitiert davon und muss drei Dinge darüber wissen.

- **Eine Umgebung je Worker.** Die Twig-Umgebung wird einmal je Worker und je Design-Kette aufgebaut, und unter Velocity
  wird dabei jeder Operator und jede Template-Funktion als Twig-Filter oder -Funktion registriert. Unter PHP-FPM, das jede
  Anfrage neu beginnt, werden nur die Namen aufgelöst, die ein Template benutzt.
- **Kompilierte Templates bleiben im Speicher.** Ein kompiliertes `.twig` ist eine PHP-Klasse. Der Klassenname enthält die
  Änderungszeit der Datei, den Siteaccess und die Locale (konstante Übersetzungen werden in die Klasse kompiliert), die
  Twig-Version und die PHP-Version. Eine geänderte Datei bekommt bei der nächsten Anfrage eine neue Klasse, in jedem
  Worker, ohne Neustart. Alte Klassen bleiben geladen, bis der Worker endet; das ist ein wenig Speicher, kein Fehler.
- **Rechte im Cache.** Velocitys Worker laufen meist als root, PHP-FPM als Benutzer der Website. Kompilierte Templates
  werden mit der umask geschrieben, die die Installation für ihre anderen Cache-Dateien benutzt
  (`eZFile::creationUmask()`, aus `[FileSettings] StorageDirPermissions` und `StorageFilePermissions`), sodass ein
  Verzeichnis, das ein Velocity-Worker in `<Cache-Verzeichnis>/twig/` anlegt, für PHP-FPM beschreibbar bleibt. Bevor das
  so war (es ist Teil von 6.0.15), konnte ein Template, das zuerst ein root-Worker kompilierte, ein Verzeichnis
  hinterlassen, in das PHP-FPM nicht schreiben konnte, und das nächste Template, das dorthin kompiliert wurde, scheiterte
  mit `Unable to write in the cache directory` (die Seite druckte an seiner Stelle nichts). Sehen Sie diese Meldung nach
  einem Upgrade, leeren Sie die kompilierten Templates einmal: `./console exp:ezcache --clear-id=twig`.

Nach einer Änderung an `twig.ini` (die Listen der Sandbox, `Autoescape`, `Security`) starten Sie Velocity neu: Die Worker
haben ihre Umgebung mit den alten Einstellungen aufgebaut. Ein Deploy mit `./console exp:velocity deploy` erledigt das.

## Kompatibilität von Templates

- **`.twig` und `.html.twig`** werden beide gerendert (`[TwigSettings] TemplateSuffixList[]`); `.html.twig` gibt es für
  Templates, die mit Projekten im Symfony-Stil geteilt werden. Die längere Endung wird zuerst erkannt, `a.html.twig` ist
  also immer ein Twig-Template namens `a.html.twig`.
- **Templates aus Symfony- oder Platform-Projekten** benutzen Twig-Funktionen, die diese Frameworks hinzufügen (`path()`,
  `asset()`, die Funktionen der Plattformen zum Rendern von Feldern, `form_*`); Exponential hat sie nicht. Ersetzen Sie
  sie durch die Operatoren und Funktionen des Template-Systems (`ezurl`, `ezdesign`, `attribute_view_gui`); siehe
  [Templates nebeneinander](../hilfe/template-sprache/tpl-twig-smarty-nebeneinander.md).
- **Die Sandbox** lehnt standardmäßig einige von Twigs eigenen Tags und Filtern ab
  ([Twig-Templates nativ schreiben](twig-templates-nativ-schreiben.md#was-die-sandbox-erlaubt)); Templates von anderswo
  benutzen oft `filter`, `map` oder `spaceless`.
- **Umgewandelte Templates** sind für `Autoescape=disabled` und eine eingeschaltete Sandbox geschrieben, die Standards.

## Verwandte Seiten

- [Fehlersuche in Twig-Templates](twig-templates-fehlersuche.md)
- [twig.ini [TwigSettings]](../beispiele/ini-einstellungen/twig/TwigSettings.md): jede Einstellung mit ihrem Standardwert, und [der Stub-Operator exp_twig_render](../beispiele/template-operatoren/exp_twig_render.md)
- [Twig templates: versions, upgrades and compatibility](../../eng-us/guides/twig-templates-compatibility.md) (Englisch)
- [Twig templates](../../eng-us/features/6.0/twig-templates.md) (Englisch; Einstellungen und Installation)
- [Velocity engines](../../eng-us/bc/6.0/velocity-engines.md) (Englisch)
