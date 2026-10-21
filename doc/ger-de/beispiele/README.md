---
title: Beispiele
audience: developer, admin
category: examples
order: 0
summary: Funktionierende Beispiele für Exponential 6. Geprüfte Webserver-Konfigurationen, ein Kochbuch mit vollständigen Aufgaben und mehr als zweitausend aus dem Code erzeugte Referenzseiten zu Template-Operatoren, Fetch-Funktionen, Modulansichten, Richtlinien, INI-Einstellungen, Datentypen, Workflow-Ereignissen, Cronjobs, Konsolenbefehlen und Erweiterungspunkten.
keywords: Beispiele, Beispiel, Kochbuch, Referenz, Webserver, Template-Operator, Modul, Ansicht, Richtlinie, Einstellung, Datentyp, Konsolenbefehl, Erweiterungspunkt, examples, example, cookbook, reference, web server, htaccess, nginx, template operator, fetch, module, view, policy, INI, setting, datatype, workflow, cronjob, console, command, extension point
featured: true
type: example
---
# Beispiele

Dieser Bereich enthält Beispiele, die Sie in eine echte Installation übernehmen können. Es gibt drei Arten:

- **[Webserver-Beispiele](webserver/README.md)**: vollständige Konfigurationen für Exponential Velocity, Apache
  (`.htaccess` und virtueller Host), nginx, Caddy, FrankenPHP, LiteSpeed, IIS und PHP-FPM. Jede leitet Anfragen
  genauso weiter wie die ausgelieferte `.htaccess_root`. Die Syntax jeder Konfiguration wurde geprüft, die meisten
  wurden außerdem auf einem Testserver erprobt.
- **[Kochbuch](kochbuch/README.md)**: vollständige Aufgaben von Anfang bis Ende. Eine Inhaltsklasse, ein
  Template-Override, ein eigener Template-Operator, Datentyp, ein eigenes Modul, Workflow-Ereignis und ein eigener
  Cronjob, ein Erweiterungsgerüst, JSON-Aufrufe, mehrere Websites in einer Installation, Übersetzungen, Caching und das
  Ausrollen mit Velocity.
- **Referenzbeispiele, aus dem Code erzeugt**: eine Seite pro Element, jede mit einem Beispiel, das aus der Definition
  abgeleitet ist, die der Code mitbringt. Die Liste steht unten.

Die Regeln für das Schreiben von Code (Benennung, Dateiaufbau, Sicherheit, Templates ohne Inline-Styles oder
-Skripte) stehen in [Standards und Muster](../../eng-us/standards/README.md). Dieser Bereich zeigt sie in der Anwendung
und wiederholt sie nicht.

## Webserver und Hosting

| Seite | Was sie zeigt |
|---|---|
| [Webserver-Beispiele](webserver/README.md) | Die Übersicht: welcher Server, welche Datei und wie sie geprüft wurde |
| [Apache .htaccess](webserver/apache-htaccess.md) | [`htaccess`](../../eng-us/examples/htaccess): die ausgelieferten Regeln, eine nach der anderen erklärt |
| [Apache virtueller Host](webserver/apache-virtueller-host.md) | [`ezpublish.conf`](../../eng-us/examples/ezpublish.conf): HTTPS, HTTP/2, PHP-FPM und die Regeln im Serverkontext |
| [nginx](webserver/nginx.md) | Ein Server-Block mit derselben Weiterleitung, HTTPS und Headern |
| [Caddy](webserver/caddy.md) | Caddy mit PHP-FPM, weitergeleitet wie die erzeugte FrankenPHP-Konfiguration |
| [FrankenPHP](webserver/frankenphp.md) | Von Velocity gestartet oder für sich allein |
| [LiteSpeed und OpenLiteSpeed](webserver/litespeed.md) | `.htaccess` unter LiteSpeed und ein virtueller Host für OpenLiteSpeed |
| [IIS](webserver/iis.md) | `web.config` mit URL Rewrite |
| [PHP-FPM](webserver/php-fpm.md) | Ein Pool für eine Website |
| [Velocity](webserver/velocity.md) | Overrides von `velocity.ini`, der Baum `/etc/vc` und eine systemd-Unit |
| [Demomodus](demomodus/README.md) | `EXP_DEMO_MODE=1` für eine öffentliche Demo: `.htaccess`, virtueller Host, PHP-FPM-Pool, nginx, ein systemd-Drop-in, eine Crontab, eine Container-Umgebung und ein Skript zum Neuaufbau |

`staticcache_offload.conf` ergänzt Regeln, die Seiten direkt aus dem statischen Cache ausliefern. Siehe
[Apache .htaccess](webserver/apache-htaccess.md).

## Aus dem Code erzeugte Referenzbeispiele

| Bereich | Was jede Seite enthält |
|---|---|
| [Template-Operatoren](template-operatoren/README.md) | Die Parameter (Typ, erforderlich, Standard), ein Beispielaufruf und die erste Verwendung in einem ausgelieferten Template |
| [Template-Funktionen](template-funktionen/README.md) | Die Klasse, innere Funktionen, wie sie ihr Template findet, und die erste Verwendung in einem ausgelieferten Template |
| [Fetch-Funktionen](fetch-funktionen/README.md) | `fetch( 'module', 'function', hash( ... ) )` mit jedem Parameter, derselbe Aufruf in PHP und eine echte Verwendung |
| [Modulansichten](modulansichten/README.md) | Die Adresse mit geordneten und ungeordneten Parametern, die Richtlinienfunktionen, die sie braucht, und ein Link auf sie |
| [Richtlinienfunktionen und Einschränkungen](richtlinien/README.md) | Die Richtlinienfunktionen jedes Moduls, ihre Einschränkungen, die Ansichten, die sie prüfen, und Beispiele |
| [INI-Einstellungen](ini-einstellungen/README.md) | Eine Seite pro Block: jede Einstellung mit dem Kommentar der ausgelieferten Datei, ihrem Standardwert und einem Override |
| [Datentypen](datentypen/README.md) | Der Bezeichner, was er speichert, Templates, die ihn verwenden, und die ausgelieferten Templates zum Anzeigen und Bearbeiten |
| [Workflow-Ereignistypen und Auslöser](workflow-ereignisse/README.md) | Jeder Ereignistyp, die Auslöser, die er akzeptiert, und jeder Auslösepunkt |
| [Cronjob-Skripte und -Teile](cronjobs/README.md) | Was jedes Skript tut, seine Teile und wie man es ausführt und zeitlich plant |
| [Konsolenbefehle](befehle/README.md) | Jeder Befehl `./console exp:*` mit Hilfetext, Optionen und Aliasen |
| [Erweiterungspunkte](erweiterungspunkte/README.md) | INI-Listen von Handlern, Klassen und Verzeichnissen, die eine Erweiterung ergänzen kann |

Die Hilfe listet diese Seiten in Ordnern. Ein Ordner klappt in der Navigation auf, sobald Sie auf einer seiner Seiten
sind; seine Übersichtsseite verlinkt jede Seite darin. In der Suche stehen sie unter den geschriebenen Anleitungen,
sodass eine Frage zuerst die Erklärung findet und danach die Referenz.

Die deutschen Seiten werden aus demselben Code erzeugt wie die englischen. Die festen Texte und die Kommentare der
Quellen kommen aus der deutschen Übersetzung in `doc/ger-de/.examples/`; ein Kommentar, der dort noch fehlt oder sich
seit der Übersetzung geändert hat, erscheint auf Englisch.

### Was „aus dem Code abgeleitet“ bedeutet

- **Aus den Definitionen gelesen.** Namen, Parameter, Typen, Standardwerte, Richtlinienfunktionen, Einschränkungen,
  INI-Werte und ihre Kommentare sowie Optionslisten werden aus den Dateien gelesen, die der Kernel lädt, etwa
  `eztemplateautoload.php`, `function_definition.php`, `module.php`, `operation_definition.php`, die ausgelieferten
  INI-Dateien, die Klassen der Datentypen und Workflow-Ereignisse, `cronjob.ini` und die Befehlsklassen. Jede Seite
  nennt ihre Quellen am Ende.
- **Platzhalter.** Werte in einem Beispielaufruf, etwa `1` für eine Ganzzahl oder `'text'` für eine Zeichenkette,
  zeigen nur die Form. Jede Seite sagt das.
- **Heuristiken.** Manche Angaben sind Vermutungen und als solche gekennzeichnet: die Speicherspalten, die ein Datentyp
  verwendet (durch Lesen seiner Klasse ermittelt), und die Erweiterungspunkte (am Namen der Einstellung erkannt).
- **Erste Verwendung in einem ausgelieferten Template.** Das ist der erste Treffer in den Templates des Repositorys, in
  der Reihenfolge der Pfade. Es ist echter Code, aber nicht immer das einfachste Beispiel.
- **Nur der ausgelieferte Code.** Gelesen werden nur Dateien, die im Repository versioniert sind: der Kernel, seine
  Bibliotheken und die mitgelieferten Erweiterungen. Andere installierte Erweiterungen, lokale Overrides und die
  Datenbank bleiben außen vor. Die Einschränkungen nach Objektzuständen sind die einzige Stelle, an der der Kernel die
  Datenbank lesen würde. Ihre Seiten sagen stattdessen, woher diese Einschränkungen kommen.

## So entstehen die generierten Seiten

Sie stammen aus einem Befehl, der im Wurzelverzeichnis der Installation ausgeführt wird:

```bash
./console exp:help:examples                 # write every area; only changed files are written
./console exp:help:examples --list          # the areas and how many pages each has
./console exp:help:examples --check         # change nothing; exit 1 if a page is missing, different or stale
./console exp:help:examples --area=datatypes,cronjobs
./console exp:help index --rebuild          # then rebuild the help index
```

Die deutschen Seiten erzeugt derselbe Befehl mit `--language=ger-de`; er schreibt sie nach `doc/ger-de/beispiele/`
und trägt sie in `doc/ger-de/.paths.json` ein:

```bash
./console exp:help:examples --language=ger-de
./console exp:help:examples --language=ger-de --check
./console exp:help:examples --language=ger-de --worklist=var/tmp/todo.json
```

Die Ausgabe ist deterministisch. Dieselben Quellen ergeben immer dieselben Dateien: Die Eingabe wird sortiert, und die
Seiten enthalten keine Daten und keine Hostnamen. Führen Sie den Befehl nach einer Änderung an einem Operator, einer
Fetch-Funktion, einer Ansicht, einer Richtlinie, einer INI-Datei, einem Datentyp, einem Workflow-Ereignis, einem
Cronjob oder einem Befehl aus und committen Sie die geänderten Seiten zusammen mit dieser Änderung. `--check` in einem
Review oder einem CI-Job findet Seiten, die nicht neu erzeugt wurden.

Die Übersetzung liest ihre Texte aus `doc/ger-de/.examples/`: `strings.json` enthält die festen Texte der Seiten
(Überschriften, Beschriftungen, Sätze) mit ihren `{Platzhaltern}`, `descriptions/*.json` die Kommentare und
Beschreibungen aus den Quellen, je Element (etwa `ini:settings/site.ini [Block] Setting`) mit dem englischen Text, den
sie übersetzen. Eine Übersetzung wird nur verwendet, solange ihr englischer Text dem der Quelle entspricht; die
Arbeitsliste von `--worklist` enthält die Texte, die neu zu übersetzen sind. Code, INI-Schlüssel und -Werte, Befehle
und Bezeichner werden nie übersetzt.

Jede generierte Seite trägt `generated: exp:help:examples` in ihrem Front Matter. Der Befehl ersetzt oder entfernt nur
Seiten mit dieser Markierung, innerhalb der oben genannten Bereiche. Die von Hand geschriebenen Seiten fasst er nie an:
diese hier, die Webserver-Beispiele und das Kochbuch. Um eine generierte Seite zu ändern, ändern Sie ihre Quelle (oder
ihre Übersetzung) und führen den Befehl erneut aus. Änderungen an der Seite selbst gehen beim nächsten Lauf verloren.

Der Code ist `kernel/classes/help/exphelpexamplesgenerator.php` (Klasse `expHelpExamplesGenerator`), die Übersetzung
`kernel/classes/help/exphelpexamplestranslation.php` (Klasse `expHelpExamplesTranslation`), und der Befehl ist
`kernel/private/classes/commands/exphelpexamples.php`. Um einen Bereich hinzuzufügen, geben Sie der Klasse eine
Methode `area<Name>()` und einen Eintrag in `AREAS`.

## Hilfe-Einstellungen für diesen Bereich

`settings/exphelp.ini`:

- Die Kategorie `examples`: `[CategorySettings] Categories[]`, `CategoryNames[examples]`, `CategoryIcons[examples]`.
- Der Seitentyp `generated` für die generierten Seiten: `[TypeSettings] TypeNames[generated]`. Er steht in der Suche
  über `[SearchSettings] TypeBoost[generated]` weiter unten und bleibt über `[DashboardSettings] AudienceExcludeTypes[]`
  von den Zielgruppenkarten des Dashboards fern.
- Die eingeklappten Ordner der Navigation: `[NavigationSettings] FoldedFolders[]`.

## Verwandte Seiten

- [Standards und Muster](../../eng-us/standards/README.md): die Regeln, denen die Beispiele folgen.
- [Die Website ausliefern](../installation/08-die-website-ausliefern.md) und [Sicherheitshärtung](../installation/13-sicherheitshaertung.md).
- [Templates und Design](../anleitungen/templates-und-design.md), [Rollen und Richtlinien](../anleitungen/rollen-und-richtlinien.md),
  [Workflows](../anleitungen/workflows.md), [Cronjobs](../anleitungen/cronjobs.md).
- [Das Hilfesystem](../../eng-us/features/6.0/help-system.md).
- [Freie Dokumentation](../hilfe/freie-dokumentation.md): Diese Seite dürfen Sie frei kopieren, übersetzen und verbessern.
