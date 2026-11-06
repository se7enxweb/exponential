# Exponential Components (expcomponents): die Bibliotheken des Kernels, geforkt aus den Zeta Components

Seit 6.0.15 sind die PHP-Bibliotheken, auf denen der Kernel von Exponential aufbaut, die **Exponential Components**,
kurz **expcomponents**: der eigene Fork der Zeta Components durch Exponential, veröffentlicht als die Composer-Pakete
`se7enxweb/expcomponents-<name>`, wie der Code, aus dem sie stammen, unter der Apache License 2.0 lizenziert und
gepflegt von 7x und der Exponential Foundation. Die `composer.json` des Kernels verlangt die Forks anstelle der
Ursprungspakete `zetacomponents/*`. Die Klassen behalten ihre `ezc`-Namen und ihre APIs, daher ändert sich Code, der
sie verwendet, nicht.

Diese Seite ist für alle, die wissen wollen, was diese Bibliotheken sind, woher sie kommen, warum Exponential sie
forkt, was gleich bleibt und wo jede einzelne zu finden ist. Was ein Upgrade für eine Installation, eine Erweiterung
oder eigenen Code bedeutet, steht in den [Upgrade-Hinweisen](../../bc/6.0/expcomponents.md); was jedes Paket im Kernel
tut, Klasse für Klasse, in der [Paketreferenz](../../spezifikationen/6.0/expcomponents-pakete.md); wie man an einem
Fork arbeitet, in der Anleitung [Zu expcomponents beitragen](../../anleitungen/zu-expcomponents-beitragen.md).

## Inhalt

1. [Kurz gesagt](#kurz-gesagt)
2. [Was die Komponenten sind](#was-die-komponenten-sind)
3. [Geschichte: eZ Components, Zeta Components, expcomponents](#geschichte-ez-components-zeta-components-expcomponents)
4. [Warum Exponential sie forkt](#warum-exponential-sie-forkt)
5. [Was gleich bleibt](#was-gleich-bleibt)
6. [Was sich ändert](#was-sich-ändert)
7. [Die Pakete](#die-pakete)
8. [Wie die alten Paketnamen weiter funktionieren: „replace“ in Composer](#wie-die-alten-paketnamen-weiter-funktionieren-replace-in-composer)
9. [Die Lizenz](#die-lizenz)
10. [Wo die Repositories und Pakete sind](#wo-die-repositories-und-pakete-sind)
11. [Versionen und Releases](#versionen-und-releases)
12. [Fehler melden und mitwirken](#fehler-melden-und-mitwirken)
13. [Modernisierung: die Absicht](#modernisierung-die-absicht)
14. [Fragen und Antworten](#fragen-und-antworten)
15. [Verwandte Seiten](#verwandte-seiten)

## Kurz gesagt

| Frage | Antwort |
|---|---|
| Was ist es? | Der Fork der PHP-Bibliotheken Zeta Components durch Exponential (Archive, Mail, WebDAV, Feeds, MVC-Werkzeuge, die Datenbankschicht und mehr) |
| Paketnamen | `se7enxweb/expcomponents-<name>`, zum Beispiel `se7enxweb/expcomponents-mail` für `zetacomponents/mail` |
| Klassennamen | Unverändert: `ezcMail`, `ezcArchive`, `ezcWebdavServer`, `ezcBaseFile` ... |
| Lizenz | Apache License 2.0 (`Apache-2.0`), wie im Original; die Copyright- und Hinweistexte des Originals bleiben erhalten |
| Wer pflegt sie | 7x und die Exponential Foundation, öffentlich, auf GitHub unter `se7enxweb` |
| Was eine Installation tun muss | Nichts außer dem üblichen `composer update` eines Upgrades; siehe die [Upgrade-Hinweise](../../bc/6.0/expcomponents.md) |
| Code Dritter, der `zetacomponents/<name>` verlangt | Funktioniert weiter: Jeder Fork erklärt, dass er den alten Namen ersetzt |
| Seit | 6.0.15. Die ersten beiden Forks, `se7enxweb/expcomponents-archive` und `se7enxweb/expcomponents-webdav`, wurden vor den übrigen verlangt |

## Was die Komponenten sind

Die Komponenten sind eine Sammlung unabhängiger PHP-Bibliotheken, je ein Composer-Paket, von denen jede eine Aufgabe
gut erledigt: tar- und zip-Archive lesen und schreiben, E-Mails erstellen und versenden, WebDAV bereitstellen, RSS-
und Atom-Feeds schreiben, HTTP-Anfragen routen und verteilen, Werte zwischenspeichern, über PDO mit einer Datenbank
sprechen, Kommandozeilenoptionen auswerten und so weiter. Jedes Paket hat seine eigene Version, seine eigenen Tests und
seine eigene Dokumentation; alle teilen sich ein Basispaket (`base`) mit den Ausnahmeklassen, den Optionsklassen,
Dateihilfen und dem ursprünglichen Autoloader.

Jede Klasse jeder Komponente beginnt mit `ezc` („eZ Components“, der ursprüngliche Name), gefolgt von der Komponente:
`ezcArchive`, `ezcMailSmtpTransport`, `ezcWebdavServer`, `ezcFeed`, `ezcMvcController`, `ezcCacheManager`,
`ezcDbFactory`, `ezcConsoleInput`, `ezcBaseFile`. Die Bibliotheken verwenden keine PHP-Namensräume; das liegt an ihrem
Alter und ist kein Mangel, und es ist der Grund, warum die Klassennamen die Schnittstelle sind, die sich nicht ändern
darf.

Der Kernel verwendet sie an vielen Stellen, die man leicht übersieht: Import und Export von Paketen (archive), jede
E-Mail, die die Site verschickt (mail), der WebDAV-Server (`index_webdav.php`), der RSS-Export (feed), die REST-API und
die darauf gebauten Endpunkte für GraphQL, Hilfe und Migrationen (MVC-Werkzeuge, authentication, cache, debug, event
log, persistent object, database), Konsolenskripte (console tools), Setup > Systeminformationen (system information)
und die Veröffentlichungswarteschlange für Inhalte (signal slot). Die
[Paketreferenz](../../spezifikationen/6.0/expcomponents-pakete.md) nennt die Klassen und Dateien für jede.

## Geschichte: eZ Components, Zeta Components, expcomponents

Die Bibliotheken sind älter als der größte Teil des PHP-Ökosystems um sie herum, und sie haben vor diesem Fork zweimal
den Besitzer gewechselt.

| Zeitraum | Name | Wer | Lizenz | Wo |
|---|---|---|---|---|
| Ab 2005 | **eZ Components** | eZ Systems AS, das norwegische Unternehmen hinter eZ Publish, dem Produkt, aus dem Exponential hervorging | New-BSD-Lizenz | ez.no, neben dem Code installiert (zum Beispiel als `ezcomponents/trunk`) |
| Ab 2010 | **Apache Zeta Components** | Der Inkubator der Apache Software Foundation, nachdem eZ Systems den Code eingebracht hatte | Apache License 2.0 | incubator.apache.org |
| Nach dem Inkubator | **Zeta Components** | Die Zeta Components development group, als unabhängiges Projekt | Apache License 2.0 | github.com/zetacomponents, Packagist `zetacomponents/*` |
| Seit 6.0.15 | **Exponential Components (expcomponents)** | 7x und die Exponential Foundation | Apache License 2.0 | github.com/se7enxweb, Packagist `se7enxweb/expcomponents-*` |

Die Lizenzdatei jedes Pakets erzählt diese Geschichte noch heute mit eigenen Worten: „Zeta Components, Copyright
2011-2015 The Zeta Components development group“, „Apache Zeta Components, Copyright 2010-2011 The Apache Software
Foundation“, „Originally developed by eZ Systems as eZ Components, Copyright 2005-2010 eZ Systems AS“. Diese Zeilen
bleiben in den Forks.

Was das für die Produktlinie bedeutete:

- **eZ Publish 4.x**, das eingestellte Produkt, aus dem Exponential hervorging, brauchte die eZ Components neben dem
  Code installiert, in einer Mindestversion je Release (2008.2 für 4.1, 2009.1 für 4.2, 2009.2.1 für 4.3 und 4.4,
  später die Sonderbuilds `ezcomponents-ezp45` bis `ezcomponents-ezp47`). Der Setup-Assistent prüfte das mit dem
  Test `ezcversion`.
- **Exponential 6** installierte deren Fortführung, die Zeta Components, über Composer: `zetacomponents/*` in der
  `composer.json` im Wurzelverzeichnis, geladen über die Klassen-Map von Composer. 6.0.11 (Dezember 2025) hob sie auf
  die Releases an, die PHP 8.5 unterstützen.
- **Exponential 6.0.15** forkte zuerst zwei davon, weil der Kernel Korrekturen brauchte, die das Original nicht
  veröffentlicht hatte: `se7enxweb/expcomponents-webdav` 1.2.2 mit Sicherheitskorrekturen am WebDAV-Server (der Fork
  war vor seiner Umbenennung als `se7enxweb/zetacomponents-webdav` 1.2.1 veröffentlicht worden) und
  `se7enxweb/expcomponents-archive` 1.5.2, das GNU-tar-Einträge mit langen Namen wieder entpackt und unter PHP 8.4
  und 8.5 keine Deprecations mehr ausgibt. Dann machte das Projekt es offiziell: Jede Zeta Component, die der Kernel
  braucht, wird als expcomponents geforkt, und der Kernel verlangt die Forks.

## Warum Exponential sie forkt

- **Die Aktivität im Original ist ungleich.** Manche Pakete werden gepflegt und veröffentlicht (`base` und `database`
  2026), andere hatten seit vielen Jahren kein Release: `configuration`, `debug`, `event-log`, `image-analysis` und
  `signal-slot` haben ihr letztes Tag im Original aus dem Jahr 2015. Ein Kernel, der ein Supportfenster verspricht,
  kann nicht auf Releases warten, die vielleicht nicht kommen.
- **PHP 8.x.** Exponential unterstützt PHP 8.0 bis 8.5 und läuft auf PHP 7.4. Jede neue PHP-Version erklärt etwas für
  veraltet, das diese Bibliotheken tun (implizit nullbare Parameter, fehlende vorläufige Rückgabetypen, `null` an
  String-Funktionen). Eine Deprecation in einer Bibliothek wird zu einer Warnung in jedem Log jeder Site. Die Forks
  beheben sie dort, wo der Code ist, und ihre Tests laufen auf jeder PHP-Version, die der Kernel unterstützt.
- **Sicherheit.** Korrekturen, die der Kernel braucht, müssen veröffentlicht werden können, wenn sie fertig sind. Der
  WebDAV-Fork ist das Beispiel: Sein Release 1.2.2 hält Anfragepfade und das Ziel von COPY und MOVE innerhalb der Wurzel
  des Backends, folgt keinen symbolischen Links aus ihr heraus, weist Anfragekörper mit einer Dokumenttyp-Deklaration
  ab, vergleicht die Digest-Authentifizierung in konstanter Zeit und erzeugt zufällige Sperr-Token.
- **Ein Verantwortlicher für den ganzen Stack.** Kernel, Erweiterungen und Bibliotheken werden zusammen getestet,
  veröffentlicht und dokumentiert, nach denselben Regeln (dauerhafte Tags, ein Changelog-Eintrag je Änderung, Tests
  zu jeder Korrektur).
- **Modernisierung.** Die Bibliotheken werden intern modernisiert; siehe [die Absicht](#modernisierung-die-absicht).

Ein Fork ist kein Bruch mit dem Original. Korrekturen, die allen nützen, werden dem Projekt Zeta Components als
Pull-Requests angeboten, wie 7x es schon getan hat (das se7enxweb-Profil nennt 14 Sicherheits-Pull-Requests an Zeta
Components im April 2026), und Korrekturen des Originals werden in die Forks übernommen
([Abgleich mit dem Original](../../anleitungen/zu-expcomponents-beitragen.md#abgleich-mit-upstream)).

## Was gleich bleibt

- **Die Klassennamen und die APIs.** Jede Klasse behält ihren `ezc`-Namen, jede öffentliche Methode ihre Signatur und
  Bedeutung. Code, der `new ezcMailComposer()` aufruft oder `ezcWebdavSimpleBackend` erweitert, funktioniert
  unverändert. Eine Änderung, die einen Aufrufer brechen würde, wird in der 1.x-Linie eines Forks nicht gemacht.
- **Die Dateien werden auf dieselbe Weise gefunden.** Jedes Paket wird über die Klassen-Map von Composer geladen
  (`"autoload": {"classmap": ["src"]}`), genau wie das Ursprungspaket. In `autoload.php`, `config.php` oder den
  Klassen-Maps des Kernels ändert sich nichts.
- **Die Lizenz.** Apache License 2.0. Die `LICENSE.txt` des Originals mit ihren Hinweiszeilen, `CREDITS`, `ChangeLog`
  und die Copyright-Köpfe in den Quelldateien bleiben, wie sie sind.
- **Die Versionslinie.** Ein Fork führt die Versionsnummern des Originals fort: Das erste Fork-Release eines Pakets
  folgt auf das Release des Originals, von dem es ausgeht (archive ging von 1.5.1 auf 1.5.2, webdav von 1.2 auf
  1.2.2), daher behält eine Bedingung wie `~1.10` ihre Bedeutung.
- **Die alten Paketnamen lösen sich auf.** Über `replace` in Composer (unten) wird eine Anforderung an
  `zetacomponents/mail` von `se7enxweb/expcomponents-mail` erfüllt.
- **Einstellungen.** Keine INI-Einstellung ändert sich. `setup.ini [ezcversion]` prüft weiterhin auf die Klasse
  `ezcBaseFile`, die der Fork von `base` liefert.

## Was sich ändert

- **Die Paketnamen** in `composer.json`, `composer.lock`, `composer show` und `composer licenses`:
  `se7enxweb/expcomponents-<name>` statt `zetacomponents/<name>`.
- **Die Verzeichnisse in `vendor/`**: `vendor/se7enxweb/expcomponents-<name>/` statt
  `vendor/zetacomponents/<name>/`. Code, der eine Bibliotheksdatei über ihren Pfad einbindet (was er nicht sollte),
  muss folgen; Code, der die Klassen verwendet, bemerkt nichts.
- **Wohin Fehlermeldungen gehen**: in das Repository des Forks unter `se7enxweb`, nicht an das Projekt Zeta Components
  ([unten](#fehler-melden-und-mitwirken)).
- **Woher Releases kommen**: Die Forks werden von 7x im Rhythmus von Exponential veröffentlicht, mit eigenen
  Release-Notes auf GitHub.

## Die Pakete

Jede Zeta Component, die der Kernel braucht, hat einen Fork. Die Tabelle nennt sie alle: das Ursprungspaket, seinen
Fork, das Repository, ob das Repository schon bestand, bevor der Fork offiziell wurde (**bestehend**), oder dafür
angelegt wird (**neu**), das Release des Originals, von dem der Fork ausgeht, und das Fork-Release. Mit *vorgeschlagen*
markierte Versionen sind die geplanten Namen des nächsten Fork-Releases; sie werden erst veröffentlicht, wenn das
Release freigegeben ist, und der Kernel verlangt sie in der Tilde-Form (`~1.10.2`, nie als exakte Version).

| Ursprungspaket | Fork-Paket | Repository | Repository | Release des Originals | Fork-Release |
|---|---|---|---|---|---|
| `zetacomponents/base` | `se7enxweb/expcomponents-base` | [expcomponents-base](https://github.com/se7enxweb/expcomponents-base) | bestehend | 1.9.5 | 1.9.6 (vorgeschlagen) |
| `zetacomponents/archive` | `se7enxweb/expcomponents-archive` | [expcomponents-archive](https://github.com/se7enxweb/expcomponents-archive) | bestehend | 1.5.1 | **1.5.2 veröffentlicht** (2026-11-03); nächstes 1.5.3 (vorgeschlagen) |
| `zetacomponents/authentication` | `se7enxweb/expcomponents-authentication` | [expcomponents-authentication](https://github.com/se7enxweb/expcomponents-authentication) | bestehend | 1.4.1 | 1.4.2 (vorgeschlagen) |
| `zetacomponents/cache` | `se7enxweb/expcomponents-cache` | [expcomponents-cache](https://github.com/se7enxweb/expcomponents-cache) | bestehend | 1.6.3 | 1.6.4 (vorgeschlagen) |
| `zetacomponents/configuration` | `se7enxweb/expcomponents-configuration` | [expcomponents-configuration](https://github.com/se7enxweb/expcomponents-configuration) | neu | 1.4 | 1.4.1 (vorgeschlagen) |
| `zetacomponents/console-tools` | `se7enxweb/expcomponents-console-tools` | [expcomponents-console-tools](https://github.com/se7enxweb/expcomponents-console-tools) | neu | 1.7.5 | 1.7.6 (vorgeschlagen) |
| `zetacomponents/database` | `se7enxweb/expcomponents-database` | [expcomponents-database](https://github.com/se7enxweb/expcomponents-database) | bestehend | 1.5.4 | 1.5.5 (vorgeschlagen) |
| `zetacomponents/debug` | `se7enxweb/expcomponents-debug` | [expcomponents-debug](https://github.com/se7enxweb/expcomponents-debug) | neu | 1.3 | 1.3.1 (vorgeschlagen) |
| `zetacomponents/event-log` | `se7enxweb/expcomponents-event-log` | [expcomponents-event-log](https://github.com/se7enxweb/expcomponents-event-log) | neu | 1.5 | 1.5.1 (vorgeschlagen) |
| `zetacomponents/feed` | `se7enxweb/expcomponents-feed` | [expcomponents-feed](https://github.com/se7enxweb/expcomponents-feed) | neu | 1.4.6 | 1.4.7 (vorgeschlagen) |
| `zetacomponents/image-analysis` | `se7enxweb/expcomponents-image-analysis` | [expcomponents-image-analysis](https://github.com/se7enxweb/expcomponents-image-analysis) | neu | 1.2 | 1.2.1 (vorgeschlagen) |
| `zetacomponents/image-conversion` | `se7enxweb/expcomponents-image-conversion` | [expcomponents-image-conversion](https://github.com/se7enxweb/expcomponents-image-conversion) | bestehend | 1.5.1 | 1.5.2 (vorgeschlagen) |
| `zetacomponents/mail` | `se7enxweb/expcomponents-mail` | [expcomponents-mail](https://github.com/se7enxweb/expcomponents-mail) | neu | 1.10.1 | 1.10.2 (vorgeschlagen) |
| `zetacomponents/mvc-tools` | `se7enxweb/expcomponents-mvc-tools` | [expcomponents-mvc-tools](https://github.com/se7enxweb/expcomponents-mvc-tools) | neu | 1.2.4 | 1.2.5 (vorgeschlagen) |
| `zetacomponents/persistent-object` | `se7enxweb/expcomponents-persistent-object` | [expcomponents-persistent-object](https://github.com/se7enxweb/expcomponents-persistent-object) | bestehend | 1.8.2 | 1.8.3 (vorgeschlagen) |
| `zetacomponents/signal-slot` | `se7enxweb/expcomponents-signal-slot` | [expcomponents-signal-slot](https://github.com/se7enxweb/expcomponents-signal-slot) | neu | 1.2 | 1.2.1 (vorgeschlagen) |
| `zetacomponents/system-information` | `se7enxweb/expcomponents-system-information` | [expcomponents-system-information](https://github.com/se7enxweb/expcomponents-system-information) | neu | 1.1.2 | 1.1.3 (vorgeschlagen) |
| `zetacomponents/webdav` | `se7enxweb/expcomponents-webdav` | [expcomponents-webdav](https://github.com/se7enxweb/expcomponents-webdav) | bestehend | 1.2 | **1.2.2 veröffentlicht** (2026-11-01); nächstes 1.2.3 (vorgeschlagen) |

`image-analysis` verwendet der Kernel nicht direkt: `image-conversion` verlangt es, und der Bildeditor ezie verwendet
beide. `configuration` bleibt verlangt, solange 7x SQLI Import 2.0.x eine seiner Ausnahmen wirft.

Forks, die es gibt, die der Kernel aber nicht verlangt:

| Ursprungspaket | Fork-Paket | Warum er nicht verlangt wird |
|---|---|---|
| `zetacomponents/authentication-database-tiein` | `se7enxweb/expcomponents-authentication-database-tiein` (Fork-Release 1.2.1 vorgeschlagen) | Kein Code des Kernels oder der Pakete, die er verlangt, verwendet es; der Kernel verlangt es seit Oktober 2026 nicht mehr ([Verhaltensänderungen vom Oktober 2026](../../bc/6.0/verhaltensaenderungen-2026-10.md)). |
| `zetacomponents/graph` | `se7enxweb/expcomponents-graph` (1.7.3 vorgeschlagen) | Weder der Kernel noch eine mitgelieferte Erweiterung zeichnet damit Diagramme; der Fork ist für Erweiterungen da, die es tun. |
| `zetacomponents/workflow` | `se7enxweb/expcomponents-workflow` (1.5.2 vorgeschlagen) | Der Kernel hat seine eigene Workflow-Engine (`eZWorkflow`); der Fork ist für Erweiterungen da, die die Komponente verwenden. |

Ursprungspakete, die nicht geforkt werden:

- `zetacomponents/mvc-authentication-tiein` und `zetacomponents/php-generator`: Der Kernel verlangt sie seit Oktober
  2026 nicht mehr; nichts verwendet sie.
- `zetacomponents/unit-test`, `zetacomponents/database-schema` und `zetacomponents/coding-standard`:
  Entwicklungsabhängigkeiten der eigenen Testsuiten der Forks; sie bleiben im Original.

Keine `composer.json` einer mitgelieferten Erweiterung verlangt ein Paket `zetacomponents/*`, und auch das Metapaket
`se7enxweb/exponential-php-floor` verlangt keines; der Wechsel geschieht allein in der `composer.json` des Kernels.

## Wie die alten Paketnamen weiter funktionieren: „replace“ in Composer

Die `composer.json` jedes Forks sagt, welches Paket er ersetzt. Der Archive-Fork, wie veröffentlicht:

```json
{
    "name": "se7enxweb/expcomponents-archive",
    "license": "Apache-2.0",
    "require": {
        "php": ">=7.1",
        "zetacomponents/base": "~1.8"
    },
    "replace": {
        "zetacomponents/archive": "self.version"
    }
}
```

Was `replace` für Composer bedeutet:

1. **Eine Anforderung an den alten Namen erfüllt der Fork.** Ein Paket, das `zetacomponents/archive ~1.5` verlangt,
   wird von `se7enxweb/expcomponents-archive` 1.5.2 erfüllt, weil `self.version` den Fork für dieselbe Version des
   alten Namens eintreten lässt.
2. **Das alte Paket wird nicht daneben installiert.** Ein ersetztes Paket und sein Ersatz werden nie zusammen
   installiert; es gibt eine Kopie jeder Klasse.
3. **Die Forks finden sich gegenseitig über die alten Namen.** Der Archive-Fork oben verlangt weiterhin
   `zetacomponents/base ~1.8`. Sobald `se7enxweb/expcomponents-base` installiert ist, ersetzt es `zetacomponents/base`
   und erfüllt diese Anforderung; das ursprüngliche `base` wird nicht installiert.
4. **Der WebDAV-Fork ersetzt zwei Namen**: `zetacomponents/webdav` und `se7enxweb/zetacomponents-webdav`, den Namen,
   unter dem er 1.2.1 veröffentlicht wurde.

`replace` benennt keine Klasse um und legt keinen Alias an; es sagt Composer nur, welche Pakete austauschbar sind. Das
genügt hier, weil die Klassen dieselben sind.

## Die Lizenz

Die Forks stehen unter der **Apache License, Version 2.0** (`Apache-2.0` in `composer.json`), der Lizenz der Zeta
Components, aus denen sie stammen. Apache 2.0 erlaubt jedem, den Code zu verwenden, zu ändern und weiterzugeben,
auch in einem Fork, unter diesen Bedingungen, denen die Forks folgen:

- jedes Paket behält den Lizenztext und die Hinweiszeilen, mit denen es kam (`LICENSE.txt` mit den Hinweisen der Zeta
  Components development group, der Apache Software Foundation und von eZ Systems AS), und die Copyright-Köpfe in seinen
  Quelldateien;
- Apache 2.0 (Abschnitt 4(b)) verlangt, dass geänderte Dateien gut sichtbare Hinweise tragen, dass sie geändert wurden;
  die [Anleitung für Mitwirkende](../../anleitungen/zu-expcomponents-beitragen.md#lizenzköpfe-und-hinweise) sagt, wie
  eine Änderung das festhält, und jede Änderung ist außerdem ein Commit mit eigener Nachricht und ein Eintrag im
  `ChangeLog` des Pakets;
- eine Datei `NOTICE` wird überall weitergegeben, wo das Ursprungspaket eine hat (Apache 2.0, Abschnitt 4(d)); die
  Pakete der Zeta Components stellen ihre Hinweiszeilen an den Anfang von `LICENSE.txt`, und diese Zeilen bleiben.

Der Kernel von Exponential selbst steht unter der **GNU GPL Version 2 oder (nach Ihrer Wahl) jeder späteren Version**
(`GPL-2.0-or-later`). Wie beides zusammenpasst, kurz und ohne Übertreibung: Die Free Software Foundation hält die
Apache License 2.0 für kompatibel mit GPL Version 3 und für inkompatibel mit *ausschließlich* GPL Version 2. Weil die
Lizenz des Kernels „oder jede spätere Version“ enthält, kann der Kernel zusammen mit Bibliotheken unter Apache 2.0
unter GPL Version 3 verbreitet werden. Das ist dieselbe Lage wie mit den ursprünglichen Zeta Components vor dem Fork;
der Fork ändert daran nichts. Eine Erweiterung unter *ausschließlich* GPL Version 2, die Code unter Apache 2.0
mitliefert, ist der Fall, den man genau ansehen sollte
([Lizenzen und Kompatibilität](../../hilfe/erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/lizenzen-und-kompatibilitaet.md)).
Das ist keine Rechtsberatung; fragen Sie zu Ihrer eigenen Verbreitung einen Rechtsbeistand.

## Wo die Repositories und Pakete sind

- **Repositories:** `https://github.com/se7enxweb/expcomponents-<name>`, zum Beispiel
  [github.com/se7enxweb/expcomponents-mail](https://github.com/se7enxweb/expcomponents-mail). Jedes ist ein GitHub-Fork
  des passenden Repositorys unter [github.com/zetacomponents](https://github.com/zetacomponents) (zum Beispiel
  `zetacomponents/Mail`), daher bleibt die Geschichte des Originals erhalten und sichtbar.
- **Pakete:** auf Packagist als `se7enxweb/expcomponents-<name>`, zum Beispiel
  [packagist.org/packages/se7enxweb/expcomponents-archive](https://packagist.org/packages/se7enxweb/expcomponents-archive).
  Ein Paket erscheint dort mit seinem ersten Release.
- **Dokumentation:** der Ordner `docs/` und die `README` jedes Repositorys. Die API ist dieselbe wie im Original, daher
  beschreibt die [Dokumentation der Zeta Components](https://zetacomponents.org/documentation/overview.html) sie
  weiterhin.
- **Installierte Kopien:** `vendor/se7enxweb/expcomponents-<name>/` in einer Installation; `composer show` listet sie.

## Versionen und Releases

- Die Version eines Forks führt die Linie des Originals fort und erhöht für jedes Release die letzte Stelle: 1.5.1 im
  Original, dann 1.5.2, 1.5.3 ... im Fork. Auf 1.5.9 folgt 1.5.10; das Erreichen der Neun verschiebt nie eine höhere
  Stelle.
- Tags haben kein Präfix `v` (`1.5.2`, nicht `v1.5.2`), wie im Original.
- **Ein veröffentlichtes Tag ist dauerhaft.** Es wird nie verschoben, gelöscht oder neu erstellt; ein Fehler in einem
  Release wird mit dem nächsten Release korrigiert, dessen Notes sagen, was falsch war.
- Der Kernel verlangt jeden Fork mit einer Tilde-Bedingung (`~1.5.2`), die jedes spätere Release derselben Linie
  annimmt (1.5.3, 1.5.10) und keine neue Minor-Linie.
- Releases geben die Verantwortlichen frei; auf dieser Seite als *vorgeschlagen* markierte Versionen sind Pläne, keine
  Releases. Die Release-Notes jedes Forks auf GitHub sind der Nachweis dessen, was veröffentlicht wurde.

## Fehler melden und mitwirken

- **Ein Problem in einer Bibliothek** (ein Fehler in `ezcMail...`, ein WebDAV-Client, der scheitert, eine Deprecation
  aus einer Datei in `vendor/se7enxweb/expcomponents-*`): Eröffnen Sie ein Issue im Repository dieses Forks. Nennen Sie
  das Paket und seine Version (`composer show se7enxweb/expcomponents-mail`), die PHP-Version und den kleinsten Code,
  der es zeigt.
- **Ein Sicherheitsproblem:** Eröffnen Sie kein öffentliches Issue. Melden Sie es, wie die `SECURITY.md` des Kernels
  sagt (security@se7enx.com).
- **Unklar, ob es am Kernel oder an der Bibliothek liegt:** Eröffnen Sie es beim Kernel, `se7enxweb/exponential`; es
  wird verschoben.
- **Code:** ein Pull-Request an den Fork, mit einem Test. Wie man klont, die Tests auf mehreren PHP-Versionen ausführt
  und die Commit-Nachricht schreibt: [Zu expcomponents beitragen](../../anleitungen/zu-expcomponents-beitragen.md).

## Modernisierung: die Absicht

In den Worten des Eigentümers forkt das Projekt die Zeta Components offiziell unter der Apache-2.0-Lizenz und
modernisiert ihre Funktionen intern („officially forking zeta components under apache 2.0 license and modernizing its
features internally“). Was das bedeutet und was nicht:

- Die Modernisierung ist **intern**: Der Code in den Bibliotheken wird auf den Stand von aktuellem PHP gebracht,
  während Klassennamen und öffentliche API bleiben, wie sie sind, sodass der Kernel und jede Erweiterung weiter
  funktionieren.
- Sie geschieht **Paket für Paket, Release für Release**, jede Änderung mit Tests, die auf jeder unterstützten
  PHP-Version laufen, und jedes Release mit Notes, die sagen, was sich geändert hat.
- Alles, was ein Verhalten ändern würde, das ein Aufrufer sehen kann, wird in den Release-Notes des Forks und im
  Changelog des Kernels dokumentiert, bevor es verlangt wird.

Hier gibt es keine Liste neuer Funktionen anzukündigen. Wenn ein Fork eine bekommt, sagen es seine Release-Notes und
diese Seite.

## Fragen und Antworten

**Muss ich meinen Code ändern?**
Nein, es sei denn, er bindet eine Bibliotheksdatei über ihren Pfad unter `vendor/zetacomponents/` ein. Klassen,
Methoden und Einstellungen sind unverändert.

**Meine Erweiterung verlangt `zetacomponents/mail`. Wird sie noch installiert?**
Ja. `se7enxweb/expcomponents-mail` ersetzt `zetacomponents/mail`, daher zählt Composer die Anforderung als erfüllt.
Sie können die Anforderung bei Gelegenheit auf den Namen des Forks ändern ([Upgrade-Hinweise](../../bc/6.0/expcomponents.md)).

**Kann ich zu den Ursprungspaketen zurück?**
Ja: Die [Upgrade-Hinweise](../../bc/6.0/expcomponents.md#zurückrollen) zeigen, wie (in einem Git-Klon in der
`composer.json` des Kernels, in einem Composer-Projekt durch Verbleiben beim vorigen Kernel-Release) und
was Sie dabei verlieren (die Korrekturen der Forks, vor allem die Sicherheitskorrekturen an WebDAV).

**Ist das weiterhin Open Source?**
Ja. Apache License 2.0, öffentliche Repositories, öffentliche Issues.

**Werden die Forks auf Namensräume umgestellt?**
Nicht auf eine Weise, die Aufrufer bricht. Die `ezc`-Klassennamen sind das Kompatibilitätsversprechen.

**Warum „expcomponents“ und nicht „zetacomponents“?**
Ein Fork trägt den Namen des Projekts, das ihn pflegt, damit niemand seine Releases mit denen des Originalprojekts
verwechselt. Das Präfix `exp` ist das, das Exponential für eigenen Code verwendet.

## Verwandte Seiten

- [Upgrade-Hinweise: der Wechsel zu expcomponents](../../bc/6.0/expcomponents.md)
- [Spezifikation: die expcomponents-Pakete und wofür der Kernel sie verwendet](../../spezifikationen/6.0/expcomponents-pakete.md)
- [Zu expcomponents beitragen](../../anleitungen/zu-expcomponents-beitragen.md)
- [Upstream-Projekte und Forks](../../hilfe/oekosystem/php-stack/upstream-projekte-und-forks.md)
- [Komponenten Dritter und ihre Lizenzen](../../hilfe/buendler/komponenten-dritter-und-ihre-lizenzen.md)
- [Urheberrecht und Copyleft](../../hilfe/urheberrecht-und-copyleft.md)
- [Das Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md)
