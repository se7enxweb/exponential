# URL-Zuordnungen importieren und exportieren

Lesen Sie diese Seite, wenn Sie auf Exponential 6.0.15 aktualisieren und wissen möchten, was der Import und Export
von URL-Zuordnungen einer bestehenden Installation hinzufügt und ob sich an Vorhandenem etwas ändert. Kurz: Ihre
URL-Aliase und Platzhalter arbeiten wie bisher; neu ist eine Tabelle von Weiterleitungen, die zuerst geprüft wird und
leer ist, bis Sie in sie importieren.

[URL-Zuordnungen importieren und exportieren](../../funktionen/6.0/url-zuordnungen-importieren-und-exportieren.md) · [URLs migrieren](../../hilfe/urls-migrieren.md) ·
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
| (beide) | für einen Siteaccess mit `PathPrefix` wird der Alias unter dem Präfix gespeichert (wo der Siteaccess ihn sucht), außer sein erstes Element steht in `PathPrefixExclude[]` |
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

## Hilfen für den Umzug einer Website

Vier Konsolenbefehle erstellen und prüfen eine URL-Zuordnung; keiner von ihnen ändert die Installation:

| Befehl | Was er tut |
|---|---|
| [`exp:url:from-sitemap <Adresse oder Datei>`](../../beispiele/befehle/expurlfromsitemap.md) | die Adressen der alten Website aus ihrer Sitemap, einem Sitemap-Index (mit den Sitemaps, die er nennt), `.gz` oder einer Crawl-Liste mit einer Adresse je Zeile, als Zeilen mit leerem `new_url`; nur der Host der Sitemap (`--only-host`, `--any-host`), jede Adresse einmal (`--keep-duplicates`), `--limit`, Pfade oder `--full-urls` |
| [`exp:url:suggest <Datei>`](../../beispiele/befehle/expurlsuggest.md) | für jede Zeile ohne Ziel die Seiten des Siteaccess von `-s`, die der alten Adresse am ähnlichsten sind (ihr URL-Alias und Name, bewertet von 0 bis 100); `--fill-above=<Bewertung>` schreibt die beste in `new_url`, die anderen stehen in der Notiz oder in `--report` |
| [`exp:url:from-404`](../../beispiele/befehle/expurlfrom404.md) | die Adressen des Protokolls nicht gefundener Seiten, die häufigsten zuerst (`--min-hits`, `--since`, `--limit`), ohne Assets und Bots; `--log` liest stattdessen Zugriffsprotokolle des Webservers |
| [`exp:url:test <Datei> --base=<Adresse>`](../../beispiele/befehle/expurltest.md) | ruft jede alte Adresse auf einer Website auf, mehrere gleichzeitig (`--concurrency`), folgt den Weiterleitungen und meldet je Zeile landed, wrong-status, wrong-target, not-redirected oder error; Exit 1, wenn eine Zeile nicht angekommen ist; `--insecure` für einen Testhost mit selbst signiertem Zertifikat |

- Die Quelle der 404-Adressen ist das Protokoll nicht gefundener Seiten, das der Kernel schon schreibt
  (`site.ini [RequestLogSettings] NotFoundLog=enabled`, der Standard; `var/log/404.log` und seine rotierten Dateien).
  Es kommt kein neues Protokoll und keine Tabelle hinzu. Das Protokoll schreibt jeden Abfragewert als `***`, deshalb
  fällt die Abfrage einer Adresse dort weg; Zugriffsprotokolle behalten sie.
- Die Vorschläge lesen die URL-Aliase, den Baum und die Objektnamen einmal (drei Abfragen), nur sichtbare Knoten
  unterhalb des Inhaltsstamms des Siteaccess, und schreiben Ziele so, wie Besucher sie verwenden, ohne den `PathPrefix`.
- Anfragen laufen über `expUrlMapHttp`: nur GET, nur http und https, ein Zeitlimit, der User-Agent
  `Exponential URL map`, eine Größengrenze, der Proxy aus `site.ini [ProxySettings]`. Das XML einer Sitemap wird nie
  mit einer DOCTYPE-Angabe oder aus dem Netz gelesen.
- Die Befehle folgen dem Vertrag der anderen `exp:url`-Befehle: `--format=json` gibt die Maschinenausgabe
  `exp-url/1` aus, `--output` und `--output-format` schreiben die Zuordnung in jedem Format, das der Import liest,
  die Exit-Codes sind dieselben.

## In der Administration

Die Seiten des URL-Übersetzers und der Platzhalter haben die Schaltflächen **Importieren**, **Exportieren**,
**Importhistorie** und **Weiterleitungen** ([URL-Zuordnungen: Import, Export und Rückgängig](../../anleitungen/url-zuordnungen.md)):

| Ansicht | Richtlinie | Was sie tut |
|---|---|---|
| `content/urlimport` | `content/urltranslator_import` (neu) | eine Datei hochladen oder einfügen, jede Zeile mit ihrem Zustand in der Vorschau, Zeilen abwählen, unbekannte Hosts zuordnen, Probelauf, Import (fragt nach der Identität), der Plan als CSV oder JSON |
| `content/urlimporthistory[/<id>]` | `content/urltranslator_import` | die Importe, die Einträge eines Imports, Rückgängig (fragt nach der Identität; seither geänderte Einträge bleiben, außer sie sind angehakt, `--force` der Konsole) |
| `content/urlexport` | `content/urltranslator` | der Export als Download |
| `content/urlredirects` | `content/urltranslator` | die Weiterleitungen von `exp_url_redirect`, eine oder die angehakten entfernen |

- Die Funktion `content/urltranslator_import` ist neu und hat keine Einschränkungen. Eine Rolle mit `content/*` oder
  `*/*` (Administrator) hat sie; eine Rolle, die nur `content/urltranslator` hatte, kann den Export und die
  Weiterleitungen öffnen, nicht den Import. Das Upgrade ändert keine Rolle: Geben Sie die Funktion den Rollen, die
  importieren sollen.
- `site.ini [ReauthGroup_system]` erhält `content/urlimport ImportButton` und `content/urlimporthistory UndoButton`.
- Die hochgeladene Datei und ihr Plan bleiben auf dem Server, bis der Import erledigt oder verworfen ist, höchstens
  einen Tag, in `var/<site>/urlimport/<token>/`, nur für den Benutzer, der sie hochgeladen hat; die Formulare senden
  nur die Entscheidungen der angezeigten Zeilen.
- Die Platzhalterseiten zeigen einen vorübergehend weiterleitenden Platzhalter (Typ 3) als **Vorübergehende
  Weiterleitung (302)**, und der Filter **Weiterleitend** listet beide Weiterleitungstypen.

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
