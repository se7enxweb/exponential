---
title: "Lektion 9.2: Ein eigener Befehl"
audience: trainer, developer
category: training
order: 92
summary: Die Teilnehmenden schreiben in ihrer Erweiterung ein Kommandozeilenskript mit eZScript, Optionen und Hilfe, beschreiben es mit @description, führen es über die Konsole als ext:mysite_hello:latest aus und geben Exit-Codes zurück, auf die sich andere Skripte verlassen können. 50 Minuten.
keywords: entwickler, kommandozeilenskript, eigener befehl, eZScript, eZCLI, getOptions, startup, initialize, shutdown, exit-code, @description, namensraum ext:, bin/php einer erweiterung, konsole, siteaccess-option, developers course, command line script, exit code, ext: namespace, extension bin/php, console, siteaccess option
type: help
---
# Lektion 9.2: Ein eigener Befehl

Importe, Berichte, Aufräumarbeiten und Prüfungen gehören auf die Kommandozeile, wo sie aus Cron und bei Deployments
laufen können. In dieser Lektion schreiben die Teilnehmenden einen Befehl in ihrer Erweiterung, mit Optionen, Hilfe
und Exit-Codes, und führen ihn ohne jede Registrierung über die Konsole aus: Eine Datei in `extension/<ext>/bin/php/`
wird automatisch gefunden.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 9: Die Konsole und persistente Objekte](README.md) |
| Dauer | 50 Minuten (15 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 9.1](die-konsole.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- ein Skript mit `eZScript::instance()`, `startup()`, `getOptions()`, `initialize()` und `shutdown()` schreiben;
- Optionen mit Werten und einem Hilfetext definieren;
- das Skript mit `@description` beschreiben, damit `console list` es anzeigt;
- es als `ext:<ext>:<name>` und mit `-s <siteaccess>` ausführen;
- bei Erfolg 0 und bei einem Fehler einen Exit-Code ungleich null zurückgeben.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Exponential Console, Befehle von Erweiterungen](../../../../bc/6.0/konsole.md#befehle-von-erweiterungen) und
[Befehle, Cronjob-Teile und Modul-Views als Klassen](../../../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md).

1. **Das Gerüst** (5 Minuten). Gehen Sie das Skript der Übung durch: `require 'autoload.php'` (das Skript läuft aus
   dem Wurzelverzeichnis der Installation), `eZScript::instance()` mit einer Beschreibung, `startup()`,
   `getOptions()` mit dem Optionsstring `[class:][limit:]` (ein Doppelpunkt bedeutet „nimmt einen Wert an“),
   `initialize()`, die eigentliche Arbeit, `shutdown( <code> )`.
2. **Standardoptionen** (2 Minuten). Jedes eZScript hat ohne weiteres Zutun `--help`, `-q`, `-s <siteaccess>`, `-d`
   (Debug-Ausgabe) und die Root-Prüfung.
3. **Beschreibung** (2 Minuten). `@description` im einleitenden Docblock ist das, was `console list` anzeigt.
4. **Exit-Codes** (2 Minuten). `shutdown( 0 )` bei Erfolg, `shutdown( 1 )` (oder ein anderer Code ungleich null) bei
   einem Fehler, damit Cron- und Deployment-Skripte reagieren können.
5. **Die Klassenform** (4 Minuten). Die eigenen Befehle des Kernels sind Klassen, die `Exponential\Runnable\Command`
   erweitern, mit einer einzeiligen Einstiegsdatei; Erweiterungen dürfen es genauso machen
   (`extension/<ext>/classes/runnable/commands/`). Beginnen Sie mit dem einfachen Skript; wechseln Sie zur
   Klassenform, wenn der Befehl wächst.

### Häufige Fragen

**Muss ich den Befehl registrieren?** Nein. Die Konsole durchsucht `extension/*/bin/php/*.php`.

**Kann der Befehl die Einstellungen eines Siteaccess verwenden?** Ja, mit `-s <siteaccess>`; ohne die Option wird der
Standard-Siteaccess verwendet.

**Wie schreibe ich eine lang laufende Aufgabe?** In Stapeln, mit Fortschrittsausgabe und einem `--dry-run`; für
Inhaltsänderungen zeigen die Inhaltsjobs (`exp:expcontentjob`) das Muster.

### Stolperfallen

- **Ausgabe mit `echo`.** Verwenden Sie `$cli->output()`, `$cli->warning()` und `$cli->error()`: Sie beachten `-q`
  und Farben.
- **`initialize()` vergessen.** Ohne diesen Aufruf sind Datenbank und Einstellungen nicht bereit.
- **Personenbezogene Daten im Terminal.** Geben Sie in Berichten, die in Logs landen, keine E-Mail-Adressen oder
  Namen echter Benutzer aus.

## Praktische Übung

Auf der Entwicklungsinstallation, in `extension/mysite_hello`.

1. Legen Sie `bin/php/latest.php` an:

   ```php
   #!/usr/bin/env php
   <?php
   /**
    * Lists the newest nodes of a class: php bin/php/console ext:mysite_hello:latest --class=article --limit=5
    *
    * @description List the newest nodes of a content class
    */
   require 'autoload.php';

   $cli = eZCLI::instance();
   $script = eZScript::instance( array( 'description' => 'List the newest nodes of a content class',
                                        'use-session' => false,
                                        'use-modules' => true,
                                        'use-extensions' => true ) );
   $script->startup();
   $options = $script->getOptions( '[class:][limit:]', '',
                                   array( 'class' => 'Class identifier (default: article)',
                                          'limit' => 'How many nodes (default: 5)' ) );
   $script->initialize();

   $classIdentifier = $options['class'] ? $options['class'] : 'article';
   $limit = $options['limit'] ? max( 1, (int)$options['limit'] ) : 5;

   $nodes = eZContentObjectTreeNode::subTreeByNodeID(
       array( 'ClassFilterType'  => 'include',
              'ClassFilterArray' => array( $classIdentifier ),
              'SortBy'           => array( 'published', false ),
              'Limit'            => $limit ),
       2 );

   if ( !$nodes )
   {
       $cli->warning( 'No nodes of the class ' . $classIdentifier );
       $script->shutdown( 1 );
   }
   foreach ( $nodes as $node )
   {
       $cli->output( $node->attribute( 'node_id' ) . "\t" . $node->attribute( 'name' ) );
   }
   $script->shutdown( 0 );
   ```

2. Machen Sie es ausführbar und prüfen Sie es: `chmod +x bin/php/latest.php` und `php -l bin/php/latest.php`.
3. Suchen Sie es aus dem Wurzelverzeichnis der Installation: `php bin/php/console list ext:mysite_hello`.
4. Lesen Sie seine Hilfe: `php bin/php/console ext:mysite_hello:latest --help`.
5. Führen Sie es aus: `php bin/php/console ext:mysite_hello:latest --class=folder --limit=3`.
6. Führen Sie es für eine Klasse ohne Objekte aus und geben Sie den Exit-Code aus:
   `php bin/php/console ext:mysite_hello:latest --class=nothing; echo "exit $?"`.
7. Führen Sie es still aus: Fügen Sie `-q` hinzu und vergleichen Sie die Ausgabe.
8. Committen Sie das Skript.

## Erwartetes Ergebnis

- Schritt 3 listet `ext:mysite_hello:latest` mit "List the newest nodes of a content class" auf.
- Schritt 4 gibt die Bedienungsanleitung mit den Optionen `--class` und `--limit` und den allgemeinen Optionen aus.
- Schritt 5 gibt bis zu drei Zeilen mit Knoten-ID und Name aus.
- Schritt 6 gibt die Warnung und `exit 1` aus.
- Der Trainer prüft `list ext:mysite_hello` bei einer Person.

## Quiz

1. Wo muss ein Skript liegen, damit die Konsole es als `ext:mysite_hello:latest` findet?
2. Was bedeutet der Doppelpunkt im Optionsstring `[class:]`?
   A. Die Option ist erforderlich B. Die Option nimmt einen Wert an C. Die Option ist verborgen D. Die Option wiederholt sich
3. Welche Methode muss laufen, bevor das Skript die Datenbank verwendet?
4. Welcher Tag gibt dem Skript seine Zeile in `console list`?
5. Warum gibt man bei einem Fehler einen Exit-Code ungleich null zurück?

<details>
<summary>Antworten</summary>

1. `extension/mysite_hello/bin/php/latest.php`.
2. **B.** Die Option nimmt einen Wert an, wie in `--class=article`.
3. `$script->initialize()`.
4. `@description` im einleitenden Docblock.
5. Damit Cronjobs, Deployments und andere Skripte erkennen, dass es fehlgeschlagen ist.

</details>

## Weiterführende Literatur

- [Exponential Console](../../../../bc/6.0/konsole.md).
- [Befehle, Cronjob-Teile und Modul-Views als Klassen](../../../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md).
- [Inhaltsjobs](../../../../funktionen/6.0/inhaltsauftraege.md): lange Operationen in Stapeln.

## Verwandte Seiten

- [Modul 9: Die Konsole und persistente Objekte](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
