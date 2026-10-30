# Twig-Templates: Versionen, Upgrades und Kompatibilität

Diese Seite ist für Betreiber und Site-Builder, die eine Installation mit `.twig`-Templates installieren, aktualisieren
oder umziehen. Sie sagt, welche Twig-3-Version auf welcher PHP-Version läuft, was Composer installiert, was geschieht,
wenn Twig fehlt, worauf beim Upgrade von Twig oder PHP zu achten ist und was unter Exponential Velocity besonders ist.
Alles wurde am 29. Oktober 2026 gegen die Paketdaten von Packagist, Composer und eine Entwicklungsinstallation geprüft;
wo etwas nicht ausgeführt wurde, sagt die Seite es.

## Welches Twig für welches PHP

Die `composer.json` des Kernels verlangt `twig/twig` `^3`: irgendein Release von Twig 3. Welches Composer wählt, hängt
von der PHP-Version ab, denn jede Twig-Linie nennt das PHP, das sie braucht (Packagist-Daten für `twig/twig`, gelesen am
29. Oktober 2026):

| Twig-Linie | Braucht PHP | Neuestes Release der Linie |
|---|---|---|
| 3.0 bis 3.11 | 7.2.5 oder neuer | 3.11.3 (7. November 2024) |
| 3.12 bis 3.19 | 8.0.2 oder neuer | 3.19.0 (29. Januar 2025) |
| 3.20 bis 3.30 | 8.1 oder neuer | 3.30.0 (25. September 2026) |

Gegen die PHP-Versionen gestellt, auf denen Exponential 6.0.15 läuft:

| PHP | Exponential | Neuestes Twig, das dort läuft | Von Composer installiert, mit eingeschalteter Sicherheitssperre (Standard) |
|---|---|---|---|
| 8.1 bis 8.5 | unterstützt | 3.30.0 | ja, das neueste 3.x (3.30.0 am 29. Oktober 2026) |
| 8.0 | unterstützt | Linie 3.19 | **nein**, siehe unten |
| 7.4 | nutzbar ohne Unterstützung | Linie 3.11 | **nein**, siehe unten |

**Warum nicht auf PHP 8.0 und 7.4.** Composer lehnt beim Auflösen der Abhängigkeiten Paketversionen mit bekannten
Sicherheitshinweisen ab, und das Projekt lässt das auf jeder PHP-Version eingeschaltet. Jedes Release von `twig/twig`, das
auf PHP 8.0 oder 7.4 läuft, ist von Sicherheitshinweisen betroffen, Composer findet dort also nichts, was es installieren
darf:

```
Problem 1
  - Root composer.json requires twig/twig ^3 -> satisfiable by twig/twig[v3.27.0, ..., v3.30.0].
  - twig/twig[v3.27.0, ..., v3.30.0] require php >=8.1.0 -> your php version (8.0.30) does not satisfy that requirement.
```

Der Fork des Projekts, `se7enxweb/twig`, hat Releases für diese Linien (3.19.1 vom 25. Oktober 2026 für PHP 8.0, 3.11.4
vom 29. Oktober 2026 für PHP 7.4). Für sich allein installiert er sich auf PHP 8.0 und 7.4. Neben `twig/twig` verlangt,
tritt er an die Stelle von `twig/twig` und erbt dessen Sicherheitshinweise; das Problem oben löst er so also nicht.

**Was das praktisch heißt:**

- **PHP 8.1 oder neuer**: nichts zu tun. `composer update` installiert Twig mit dem Rest; `.twig`-Templates funktionieren.
- **PHP 8.0 oder 7.4**: `.tpl`- und `.stpl`-Templates sind nicht betroffen. Für `.twig` installieren Sie Twig in einem
  eigenen Verzeichnis und lassen `[TwigSettings] LibraryPath` darauf zeigen (nächster Abschnitt). Lesen Sie vorher die
  Sicherheitshinweise, die Composer nennt (auf der Seite der Sicherheitshinweise von Packagist), und entscheiden Sie, ob
  sie für Ihre Templates zählen: Eine Website, deren `.twig`-Dateien nur ihre eigenen Entwickler schreiben, ist anders
  ausgesetzt als eine, bei der andere sie ändern können. Oder wechseln Sie auf PHP 8.1 oder neuer; dann stellt sich die
  Frage nicht.

Was für diese Seite ausgeführt wurde: `.twig` rendern auf PHP 8.5 mit Twig 3.30.0 (jedes Beispiel dieser Anleitungen);
`php -l` für jede Datei der Twig-Brücke (`lib/twig/classes/`, `lib/templatebridge/classes/`) auf PHP 7.4.33 und 8.0.30,
ohne Fehler; die Installation des Forks auf beiden. Rendern auf PHP 8.0 und 7.4 wurde in der Entwicklungsinstallation
nicht ausgeführt, deren Composer-Einrichtung für PHP 8.4 und neuer ist.

Die Brücke kompiliert Templates im Modus `use_yield` von Twig und kennzeichnet ihre Knoten als yield-fähig, was Twig ab
3.9 bietet; sie braucht also Twig 3.9 oder neuer, und das neueste Release jeder Linie in den Tabellen oben ist neuer.

## Twig in einem eigenen Verzeichnis installieren

Wenn die Composer-Einrichtung der Installation kein Twig hat (oder um eine andere Twig-Version zu probieren), installieren
Sie es in einem eigenen Verzeichnis und sagen der Brücke, wo es liegt:

```bash
mkdir -p var/lib/twig-vendor && cd var/lib/twig-vendor
composer require twig/twig:^3
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
