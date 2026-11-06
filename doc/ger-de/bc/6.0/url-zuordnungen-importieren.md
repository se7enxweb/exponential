# URL-Zuordnungen importieren und exportieren

Lesen Sie diese Seite, wenn Sie auf Exponential 6.0.15 aktualisieren und wissen möchten, was der Import und Export
von URL-Zuordnungen einer bestehenden Installation hinzufügt und ob sich an Vorhandenem etwas ändert. Kurz: Ihre
URL-Aliase und Platzhalter arbeiten wie bisher; neu ist eine Tabelle von Weiterleitungen, die zuerst geprüft wird und
leer ist, bis Sie in sie importieren.

[Die Dateiformate von URL-Zuordnungen](../../spezifikationen/6.0/url-zuordnungsdateiformate.md) ·
[Änderungsprotokoll 6.0.15](../../../changelogs/6.0/6.0.15.md)

## Was hinzukommt

| Bereich | Neu |
|---|---|
| Datenbank | die Tabellen `exp_url_redirect` (Weiterleitungen, die auf der ganzen Anfrage geprüft werden), `exp_urlmap_batch` und `exp_urlmap_batch_item` (die Aufzeichnung der Importe), angelegt vom Aktualisierungsschritt `6.0.15-url-map` auf jeder unterstützten Datenbank |
| Klassen | `kernel/classes/urlmap/`: `expUrlMapPlanner`, `expUrlMapPlan`, `expUrlMapImporter`, `expUrlMapExporter`, `expUrlRedirect`, `expUrlMapBatch`, `expUrlMapBatchItem`, `expUrlMapEnvironment` und `expUrlMapKernelEnvironment` |
| Konsole | `exp:url:import`, `exp:url:export`, `exp:url:batches`, `exp:url:undo` |
| Platzhalter | der Typ `eZURLWildcard::TYPE_FORWARD_TEMPORARY` (3): ein weiterleitender Platzhalter, der mit 302 antwortet |
| Einstellungen | `error.ini` `[HTTPError-302]`, `[HTTPError-303]`, `[HTTPError-307]`, `[HTTPError-308]` |
| Audit | `content.urlalias.import`, `content.urlalias.import_undo` (standardmäßig an) |

## Was sich nicht ändert

- Globale URL-Aliase, die eigenen Aliase von Knoten und Platzhalter werden wie bisher gespeichert und beantwortet.
  Eine 301-Weiterleitung auf eine Seite der Website wird als gewöhnlicher globaler URL-Alias importiert, der
  weiterleitet, genau wie der URL-Übersetzer ihn anlegt; er erscheint dort und kann dort entfernt werden.
- Ohne die Tabellen (bevor der Aktualisierungsschritt gelaufen ist) wird nichts gelesen: keine Anfrage wird anders
  beantwortet, und ein Import wird mit einer Meldung abgelehnt, die den Schritt nennt.
- Ein Platzhalter vom Typ 1 antwortet weiter mit 301, ein weiterleitender Alias ebenso.

## Wohin eine importierte Zeile geht

| Zeile | Gespeichert als |
|---|---|
| `redirect` (301) auf eine Seite dieser Website, alte Adresse ohne Abfrage | ein globaler URL-Alias, der weiterleitet (`ezurlalias_ml`) |
| `alias` | ein globaler URL-Alias, der nicht weiterleitet |
| `wildcard-forward`, `wildcard-forward-temporary`, `wildcard-direct` | ein URL-Platzhalter vom Typ 1, 3 oder 2 (`ezurlwildcard`); Platzhalter gelten in jedem Siteaccess, 303 und 307 werden als 302 beantwortet, 308 als 301 |
| jede andere Weiterleitung: eine alte Adresse mit Abfrage, ein vorübergehender Status (302, 303, 307), 308, ein Ziel mit Abfrage, ein Ziel auf einer anderen Website | eine Zeile von `exp_url_redirect` |

Eine Zeile, deren alte Adresse eine Seite dieser Website ist (die Adresse eines Knotens), eine Moduladresse
(`/content/view/full/12`, `/user/login`) oder die Startseite `/` ohne Abfrage, ist ein Konflikt und wird nie
importiert. `/index.php` ohne Abfrage (der Front-Controller) ist nicht importierbar; `/index.php?id=12` schon.

## Weiterleitungen, die auf der ganzen Anfrage geprüft werden

Die Weiterleitungen von `exp_url_redirect` werden einmal je Anfrage geprüft, auf der Adresse, die der Besucher
aufgerufen hat, vor den URL-Aliasen und Platzhaltern (in `ezpKernelWeb::dispatchLoop()`, also für PHP-FPM, Velocity und
jedes andere Frontend gleich):

- Der Pfad wird normalisiert verglichen wie in den Zuordnungsdateien (ein Schrägstrich zwischen Segmenten, kein
  abschließender Schrägstrich, Punktsegmente aufgelöst, prozentkodiertes UTF-8 dekodiert) und ohne Rücksicht auf
  Groß- und Kleinschreibung, wie bei Aliasen und Platzhaltern. Er wird als ganzer Pfad der Anfrage (`/index.php`,
  `/eng/old`) und als Pfad innerhalb des Siteaccess (`/old`) versucht.
- Die Abfrage wird genau verglichen, die Reihenfolge ihrer Parameter zählt aber nicht: die Parameter werden dekodiert
  (`+` ist ein Leerzeichen, `a` gilt als `a=`) und sortiert. Eine Weiterleitung ohne Abfrage beantwortet nur eine
  Anfrage ohne Abfrage.
- Eine Weiterleitung eines Siteaccess antwortet nur dort; eine für jeden Siteaccess (`*` in der Spalte `siteaccess`
  der Datei, gespeichert ohne) überall, nach denen des Siteaccess.
- Das Ziel ist `node:<id>` (die Adresse des Knotens zur Zeit der Anfrage, folgt also einem verschobenen Knoten), ein
  Pfad dieser Website oder eine vollständige URL. Die Abfrage der Anfrage wird nicht an das Ziel weitergegeben.
- Die Weiterleitung antwortet mit ihrem Status (301, 302, 303, 307, 308), wenn `error.ini` die Gruppe
  `[HTTPError-<Status>]` hat, sonst mit 301.

Die Tabelle wird aus einer Cache-Datei neben dem Platzhalter-Cache gelesen
(`var/<site>/cache/wildcard/urlredirect_*.php`), die der URL-Alias-Cache mit leert und die ein Import oder eine
Rücknahme sofort ersetzt; eine Anfrage ohne Weiterleitungen kostet eine kleine zwischengespeicherte Datei.

## Import, Verlauf und Rücknahme

Ein Import wird zuerst geplant (`expUrlMapPlanner`): jede Zeile ist new (neu), unchanged (unverändert), changed
(geändert), duplicate (eine frühere Zeile der Datei hat dieselbe alte Adresse; die erste zählt), conflict,
broken-target (Ziel fehlt), unknown-host (unbekannter Host) oder not-importable. Neue Zeilen werden importiert,
geänderte nur mit der Konfliktregel overwrite (`--on-conflict=overwrite`). Der Import (`expUrlMapImporter`) läuft in
einer Transaktion, wo die Datenbank sie hat, und zeichnet den Stapel auf und für jede geschriebene Zeile die Zeile
davor (keine bei einer neuen) und danach. Vor jedem Schreiben vergleicht er den Eintrag an der alten Adresse mit dem
des Plans und überspringt eine Zeile, deren Eintrag sich inzwischen geändert hat.

`exp:url:undo <Stapel>` stellt jede Zeile wieder her, wie sie war, die letzte zuerst. Eine nach dem Import geänderte
Zeile bleibt (der Stapel ist dann partly-undone), außer mit `--force`. `exp:url:batches` listet die Importe.

```bash
./console exp:upgrade --apply                                  # legt die Tabellen an (Schritt 6.0.15-url-map)
./console exp:url:import alte-website.csv -s site --dry-run --report=var/tmp/plan.csv
./console exp:url:import alte-website.csv -s site
./console exp:url:import alte-website.htaccess --host=www.alte-website.de=site --on-conflict=overwrite
./console exp:url:batches
./console exp:url:undo 3
./console exp:url:export --output=var/tmp/url-zuordnung.xlsx
./console exp:url:export --kind=wildcard,redirect --format=nginx > var/tmp/regeln.nginx
```

Eine vollständige alte Adresse wählt den Siteaccess über ihren Host, durch die Siteaccess-Zuordnung der Installation;
ein Host, der nicht zur Installation gehört, wird als unknown-host aufgeführt, bis `--host=<alter Host>=<Siteaccess>`
ihn zuordnet (oder der Host auf die Installation zeigt). Bloße Pfade gehören zum Siteaccess von `-s`, sonst zum
Standard-Siteaccess; die Sprache ist die der Zeile, sonst `--language`, sonst die Inhaltssprache des Siteaccess.

`exp:url:import` und `exp:url:export` nehmen das Format der Datei mit `--format` und geben JSON (`exp-url/1`) mit
`--output-format=json` aus; `exp:url:batches` und `exp:url:undo` mit `--format=json`. Die Exit-Codes sind die von
`exp:upgrade`: 0 erledigt, 1 fehlgeschlagen, 2 Aufruf, 3 Datenbank nicht erreichbar, 4 interner Fehler.

## Export

`exp:url:export` schreibt die globalen URL-Aliase des URL-Übersetzers (mit `--include-node-aliases` auch die eigenen
Aliase von Knoten, Ziel `node:<id>`), die Platzhalter und die Weiterleitungen, in jedem Format, das der Import liest,
und als `.ods`. Der Import des Exports in dieselbe Installation findet jede Zeile unverändert.

## Für Entwickler

- Der Planer fragt die Installation über `expUrlMapEnvironment`; `expUrlMapKernelEnvironment` ist die echte, ein Test
  übergibt seine eigene, so wird die Einordnung ohne Datenbank getestet.
- `expUrlMapPlan::setDecision( $index, 'import'|'skip' )` ändert die Entscheidung einer Zeile vor `apply()`;
  `toArray()` und `writeReport()` liefern die Vorschau (Schema `exp-url-map-plan/1`).
- `eZURLWildcard::isForwardType()`, `redirectStatusOfType()`, `typeName()` und `lastRedirectStatus()` sind neu; Code,
  der den Typ eines Platzhalters mit `TYPE_FORWARD` vergleicht, sollte `isForwardType()` verwenden, damit der
  vorübergehende Typ mitzählt.

## Oracle und MongoDB

Der Aktualisierungsschritt legt die Tabellen aus `share/db_schema.dba` auch auf Oracle (über den Schema-Handler von
ezoracle) und MongoDB (Collections) an. Der Import schreibt mit denselben SQL-Anweisungen, die der URL-Alias-Code des
Kernels verwendet; getestet wurde er auf SQLite, MySQL 8.0 und PostgreSQL 13.
