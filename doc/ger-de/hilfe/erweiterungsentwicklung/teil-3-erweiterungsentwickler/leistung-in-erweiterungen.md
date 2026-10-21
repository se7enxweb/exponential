---
title: Leistung in Erweiterungen
audience: developer, app-creator
category: extension-development
order: 360
summary: Eine Erweiterung auch unter persistenten Workern schnell und sicher halten - Abfragen zählen, mit Limits abrufen, cachen, Listener sparsam halten, messen und nie Request-Zustand in statischen Variablen ablegen.
keywords: Leistung, Geschwindigkeit, Abfragen, Limit, View-Cache, Cache, statische Variablen, persistenter Worker, Messen, Debug-Leiste, Zeitmessung, performance, speed, SQL, queries, fetch, limit, view cache, cache-block, statics, Velocity, persistent worker, requestKey, benchmark, exp:benchmark, debug bar, timing
type: help
---
# Leistung in Erweiterungen

Teil 3, Kapitel 36. Eine Erweiterung kann jede Seite einer Website langsamer machen: ein Listener auf
`response/output`, ein Operator im Pagelayout, ein Fetch in einer Schleife. Dieses Kapitel nennt die üblichen
Ursachen und die Werkzeuge, die sie finden.

## Zuerst messen

| Werkzeug | Zeigt |
|---|---|
| Die Zeittabelle der Debug-Leiste | wie lange jeder Teil eines Requests gedauert hat |
| `[DatabaseSettings] SQLOutput=enabled` | jede Abfrage einer Seite; zählen Sie sie |
| `{debug-timing-point id='...'}...{/debug-timing-point}` | die Zeit eines Teils eines Templates |
| `./console exp:benchmark` | Seitenzeiten über HTTP, A/B-Vergleich zwischen zwei Servern, Kernel-Messpunkte, eine Regressionsprüfung gegen eine Basislinie ([Benchmarking](../../../anleitungen/benchmarking.md)) |

Messen Sie die Seite, bevor und nachdem Sie Ihre Erweiterung einschalten; der Unterschied ist das, was sie kostet.

## Datenbank

- **Zählen Sie die Abfragen.** Eine Seite, die dieselbe Abfrage einmal pro Listeneintrag ausführt, enthält eine
  Schleife, die nur einmal abrufen sollte.
- **Rufen Sie mit Limits** und Filtern ab; laden Sie nie einen ganzen Teilbaum, nur um ihn zu zählen
  (`subTreeCountByNodeID()` und die `_count`-Fetch-Funktionen zählen in der Datenbank).
- **Laden Sie keine Attribute, die Sie nicht verwenden**: `load_data_map` `false()` für Namenslisten.
- **Indizieren Sie Ihre eigenen Tabellen** auf den Spalten, nach denen Sie abfragen.

## Caching

| Ebene | Einsatz |
|---|---|
| Der View-Cache für Inhalte | Inhalts-Views werden bereits gecacht; hebeln Sie das nicht aus, indem Sie benutzerspezifische Daten in einem Full-View-Template ausgeben |
| `cache-block` | aufwendige Teile des Pagelayouts: Menüs, Fußzeilen ([Caching und Templates](../teil-2-builder/caching-und-templates.md)) |
| Ihr eigener Cache | Ergebnisse langsamer Arbeit (einer externen API): legen Sie sie in einer Cache-Datei unter dem Cache-Verzeichnis ab oder verwenden Sie den Cluster-Datei-Handler, jeweils mit Ablaufzeit; leeren Sie sie, wenn sich Ihre Daten ändern |
| Die Schlüssel des View-Caches | wenn die Ausgabe einer View von etwas Neuem abhängt, fügen Sie es mit dem Filter `content/view/cachekeys` hinzu, statt das Caching abzuschalten |

## Listener und Operatoren auf jeder Seite

`response/output`, `request/input` und Operatoren im Pagelayout laufen bei jedem Request. Halten Sie sie bei wenigen
Mikrosekunden: keine Datenbank, kein Lesen von Dateien, kein Netzwerk. Erledigen Sie die Arbeit an anderer Stelle und
speichern Sie das Ergebnis.

## Persistente Worker (Velocity)

Velocity, der empfohlene Anwendungsserver, hält PHP-Prozesse zwischen Requests am Leben. Das macht den Kernel schnell
und ändert die Regeln für Ihren Code:

- **Kein Request-Zustand in statischen Variablen.** Ein Worker setzt die statischen Variablen jeder Klasse zwischen den
  Requests zurück, aber Code, der sich darauf verlässt, dass eine statische Variable zu Beginn eines Requests leer ist,
  sollte es nicht geben: Berechnen Sie pro Request oder versehen Sie den Wert mit dem Schlüssel des Requests.
- **Registrieren Sie einmal pro Request mit einem Request-Schlüssel**, nicht mit einem `true`-Flag, wie die
  Velocity-Dokumentation zeigt:

  ```php
  if ( self::$registered !== eZExecution::requestKey() )
  {
      self::$registered = eZExecution::requestKey();
      register_shutdown_function( array( __CLASS__, 'flush' ) );
  }
  ```

- **Neue Klassen erfordern einen Neustart der Worker.** Nach dem Bereitstellen von neuem Code erzeugt
  `./console exp:velocity deploy` die Autoloads neu, leert die Caches und startet die Worker neu.
- **Umschließen Sie einen Download samt `eZExecution::cleanExit()` nie mit `catch ( Exception )`**: Unter Velocity
  wirft der saubere Abschluss eine Ausnahme, um den Request zu beenden.

Siehe [Velocity: Exponential in einem Webserver mit persistenten Workern betreiben](../../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md).

## Frontend

- Laden Sie Skripte und Stylesheets über den Packer (ein Bündel, gecacht, mit dem Zeitstempel im Namen).
- Binden Sie seitenspezifische Skripte mit `ezscript_require` nur auf dieser Seite ein.
- Bilder über Bildaliase, nicht als vollständige Originale.

## Verwandte Seiten

- Zurück: [Erweiterungen debuggen](erweiterungen-debuggen.md)
- Weiter: [Ihre Erweiterung dokumentieren, einschließlich Hilfeseiten](ihre-erweiterung-dokumentieren.md)
- [Benchmarking](../../../anleitungen/benchmarking.md), [Caches](../../../anleitungen/caches.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
