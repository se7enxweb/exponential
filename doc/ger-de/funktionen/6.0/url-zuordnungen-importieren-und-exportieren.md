# URL-Zuordnungen importieren und exportieren

Wenn eine Website zu Exponential umzieht oder eine neue Struktur bekommt, müssen ihre alten Adressen weiter
funktionieren: Links auf anderen Websites, Lesezeichen und Suchergebnisse zeigen auf sie. Exponential 6.0.15 nimmt
diese Adressen als **URL-Zuordnung** auf, eine Datei, die jede alte Adresse und ihr neues Ziel auflistet. Die ganze
Datei wird in einem Schritt importiert, mit einer Vorschau vorab, und der ganze Import lässt sich wieder
zurücknehmen. Ebenso schreibt Exponential die eigenen Weiterleitungen der Website als Datei heraus, für einen
Webserver oder als Sicherung.

Vor 6.0.15 musste jeder globale URL-Alias (URL-Übersetzer) und jeder URL-Platzhalter einzeln in der Administration
angelegt werden, und einen Export gab es nicht.

[Funktionen 6.0](README.md) · Tutorial: [URLs migrieren](../../hilfe/urls-migrieren.md) · Beispieldateien:
[URL-Zuordnungen als Beispiele](../../beispiele/url-zuordnungen/README.md)

## Was es tut

- **Importiert** eine URL-Zuordnung in den URL-Übersetzer, die URL-Platzhalter und eine neue Liste von
  Weiterleitungen, als ein Stapel.
- **Zeigt eine Vorschau** jeder Zeile, bevor etwas geschrieben wird: was neu ist, was sich ändern würde und was sich
  nicht importieren lässt und warum. Ein **Probelauf** prüft jede Zeile gegen die Website und schreibt nichts.
- **Macht einen Import als Ganzes rückgängig**: Jeder angelegte Eintrag wird entfernt, jeder ersetzte wiederhergestellt.
- **Exportiert** die globalen URL-Aliase, Platzhalter und Weiterleitungen der Website in jedem Format, das er liest.
  Ein Export, der in dieselbe Website importiert wird, ändert nichts.
- **Leitet alte Adressen mit Abfrage weiter**, etwa `/index.php?id=12` eines alten CMS, auf die neue Seite.
- **Lässt vorübergehende Weiterleitungen vorübergehend** (302, 303, 307), damit Suchmaschinen die alte Adresse
  behalten, wo das gemeint ist.
- **Hilft beim Aufbau der Zuordnung**: beginnt sie mit der Sitemap der alten Website oder mit den Adressen, die
  Besucher nicht finden (dem 404-Protokoll), schlägt für jede alte Adresse die neue Seite vor und prüft die alten
  Adressen nach dem Import auf der Website.

Das geht in der Administration (auf den Seiten URL-Übersetzer und URL-Platzhalter) und auf der Kommandozeile.

## Formate

| Format | Import | Export |
|---|---|---|
| CSV (auch mit `;` oder Tabulator getrennt, wie Tabellenprogramme es speichern) | ja | ja |
| JSON | ja | ja |
| Excel `.xlsx` | ja | ja |
| LibreOffice Calc `.ods` | ja | ja |
| Apache-Regeln (`Redirect`, `RedirectMatch`, `RewriteRule` mit `RewriteCond`) | ja | ja |
| nginx-Regeln (`location =`, `return`, `rewrite`, `map`) | ja | ja |

Das Format wird am Namen oder am Inhalt der Datei erkannt. Eine Tabelle oder CSV-Datei hat die Spalten `old_url`,
`new_url`, `kind`, `siteaccess`, `language`, `note` und `status`; nur die ersten beiden sind nötig:

```text
old_url,new_url
/about-us.html,/fitness
/recipes/hummus.html,/recipes/home-made-hummus
/old-recipes/*,/recipes/{1}
```

Jede Einzelheit jedes Formats, auch was aus einer Umschreibregel werden kann und was nicht, steht in der
Spezifikation [Die Dateiformate für URL-Zuordnungen](../../spezifikationen/6.0/url-zuordnungsdateiformate.md).

## Alte und neue Adressen

- **Die alte Adresse** ist ein Pfad (`/about-us.html`) oder eine vollständige Adresse
  (`https://www.alte-seite.de/about-us.html`). Eine vollständige Adresse wählt den Siteaccess über ihren Host. Einen
  alten Host, für den diese Installation nicht antwortet, ordnen Sie in der Importseite einem Siteaccess zu, oder auf
  der Kommandozeile mit `--host=www.alte-seite.de=site`.
- **Die neue Adresse** ist ein Pfad dieser Website (`/recipes`), ein Knoten (`node:163`, der der Seite folgt, wenn sie
  verschoben wird) oder eine vollständige Adresse auf einer anderen Website.
- Ein `*` in der alten Adresse macht einen **Platzhalter**: `{1}` in der neuen Adresse steht für das, worauf der erste
  `*` gepasst hat.

## Arten und Status

Die Spalte `kind` entscheidet, was aus einer Zeile wird. Ist sie leer, wird eine Zeile mit `*` ein weiterleitender
Platzhalter und jede andere Zeile eine dauerhafte Weiterleitung.

| Art | Was Besucher bekommen | Gespeichert als |
|---|---|---|
| `redirect` | Eine dauerhafte Weiterleitung (301, oder 308 in der Spalte `status`) | Ein globaler URL-Alias, der weiterleitet; eine Weiterleitung, wenn die alte Adresse eine Abfrage hat oder das Ziel auf einer anderen Website liegt |
| `redirect-temporary` | Eine vorübergehende Weiterleitung (302, oder 303 oder 307 in `status`) | Eine Weiterleitung |
| `alias` | Die neue Seite, unter der alten Adresse angezeigt, ohne Weiterleitung | Ein globaler URL-Alias |
| `wildcard-forward` | Eine dauerhafte Weiterleitung für jede passende Adresse | Ein URL-Platzhalter (weiterleitend) |
| `wildcard-forward-temporary` | Eine vorübergehende Weiterleitung (302) für jede passende Adresse | Ein URL-Platzhalter (vorübergehende Weiterleitung) |
| `wildcard-direct` | Die neue Seite unter der alten Adresse, für jede passende Adresse | Ein URL-Platzhalter (direkt) |

Globale URL-Aliase und Platzhalter sind dieselben Einträge, die Sie von Hand anlegen würden; sie erscheinen auf den
Seiten URL-Übersetzer und URL-Platzhalter und lassen sich dort ändern. Weiterleitungen, die ein Alias nicht aufnehmen
kann (eine alte Adresse mit Abfrage, ein vorübergehender Status, ein Ziel auf einer anderen Website), kommen in eine
eigene Liste, **Weiterleitungen**.

## Weiterleitungen mit Abfrage

Alte Content-Management-Systeme hatten oft Adressen wie `/index.php?id=12` oder `/shop.php?category=3`. Eine Zeile
mit einer solchen alten Adresse ist eine Weiterleitung der vollständigen Anfrage:

```text
old_url,new_url
/index.php?id=12,/recipes
/shop.php?category=3,/recipes/kale-salad
```

Diese Weiterleitungen werden bei jeder Anfrage zuerst geprüft, vor den URL-Aliasen und Platzhaltern. Die Reihenfolge
der Parameter zählt nicht (`?b=2&a=1` ist `?a=1&b=2`); eine andere Abfrage oder keine passt nicht. Die alte Abfrage
wird nicht an die neue Adresse weitergegeben.

## Vorschau, Konflikte und Probelauf

Jede Zeile einer Datei wird vor dem Import geplant und bekommt einen dieser Zustände:

| Zustand | Bedeutung | Importiert |
|---|---|---|
| Neu | An der alten Adresse ist noch nichts | ja |
| Geändert | An der alten Adresse ist ein anderer Eintrag | nur, wenn Sie bestehende Einträge ersetzen lassen |
| Unverändert | Derselbe Eintrag ist schon da | nein |
| Doppelt | Eine frühere Zeile der Datei hat dieselbe alte Adresse; die erste zählt | nein |
| Konflikt | Die alte Adresse ist eine Seite dieser Website, eine Adresse des Systems (etwa `/user/login`) oder die Startseite | nie |
| Ziel fehlt | Die neue Adresse gibt es auf dieser Website nicht | nein |
| Unbekannter Host | Der alte Host gehört nicht zu dieser Installation | nein, bis der Host einem Siteaccess zugeordnet ist |
| Nicht importierbar | Die Zeile selbst ist fehlerhaft; ihre Hinweise sagen, warum | nein |

Ein **Probelauf** geht jede Zeile so gegen die Website durch, wie der Import es täte, zählt, was er tun würde, und
schreibt nichts.

## Rückgängig machen

Jeder Import wird als ein Stapel festgehalten, mit jedem geschriebenen Eintrag, wie er vorher und nachher war.
**Rückgängig machen** entfernt, was der Import angelegt hat, und stellt wieder her, was er ersetzt hat, das Letzte
zuerst. Ein Eintrag, den jemand nach dem Import geändert hat, bleibt erhalten, außer Sie lassen auch ihn
zurücknehmen. Die Importhistorie listet, wer welche Datei wann importiert hat, mit den Zahlen.

## Export

Ein Export schreibt die globalen URL-Aliase der Website (auf Wunsch auch die eigenen Aliase von Seiten, als
`node:<id>`), die Platzhalter und die Weiterleitungen in eines der Formate oben. Nutzen Sie ihn als Sicherung vor
einem großen Import, um die Zuordnung in einer Tabelle zu bearbeiten und wieder zu importieren, oder um die
Weiterleitungen dem Webserver zu übergeben: Die Apache- und nginx-Exporte sind Regeln, die der Server selbst
beantwortet, ohne PHP.

## Helfer für eine Migration

Vier Helfer bauen und prüfen eine Zuordnung, auf der Kommandozeile und als Reiter der Importseite. Keiner von ihnen
importiert etwas; sie schreiben eine Zuordnung, die Sie bearbeiten und importieren können.

| Helfer | Reiter der Importseite | Was er liefert |
|---|---|---|
| `exp:url:from-sitemap` | **Aus der alten Sitemap** | Jede Adresse der Sitemap der alten Website (auch eines Sitemap-Index und `.gz`) oder einer Crawl-Liste, einmal, als Zeile mit leerer neuer Adresse. Nur der eigene Host der Sitemap, außer mit `--any-host` (**Auch die Adressen anderer Hosts**). |
| `exp:url:from-404` | **Aus dem 404-Protokoll** | Die Adressen, die 404 oder 410 geantwortet haben, die häufigsten zuerst, mit der Zahl der Aufrufe im Hinweis. Dateien wie Bilder und Bots bleiben außen vor. `--min-hits`, `--since`; `--log` liest stattdessen ein Zugriffsprotokoll eines Webservers. |
| `exp:url:suggest` | **Ziele vorschlagen** | Für jede Zeile ohne neue Adresse die Seiten der Website, die der alten Adresse am ähnlichsten sind, bewertet mit 0 bis 100; `--fill-above=85` trägt die beste in die Zeile ein, die übrigen kommen in ihren Hinweis. |
| `exp:url:test` | **Die alten Adressen prüfen**, nach einem Import | Fragt jede alte Adresse auf einer Website an, folgt den Weiterleitungen und meldet jede Zeile als landed, wrong-status, wrong-target, not-redirected oder error; Exit-Code 1, wenn eine Zeile nicht angekommen ist. |

Das eigene 404-Protokoll von Exponential (`var/log/404.log`, `site.ini [RequestLogSettings] NotFoundLog`,
standardmäßig an) verbirgt die Werte einer Abfrage als `***`. `exp:url:from-404` liefert daher nur Pfade; alte
Adressen mit Abfrage kommen aus der Sitemap oder aus einem Zugriffsprotokoll, das `--log` liest. Der Reiter **Aus der
alten Sitemap** ruft nur Adressen öffentlicher Hosts ab, nie eine aus dem eigenen Netz des Servers. In der
Administration prüft **Prüfen** die ersten 500 alten Adressen unter der Website-Adresse des Siteaccess des Imports
(`site.ini [SiteSettings] SiteURL`); `exp:url:test --base=<adresse>` prüft jede Website, auch eine Staging-Website.

## In der Administration

Auf den Seiten **URL-Übersetzer** und **URL-Platzhalter** (Reiter **Setup**) führen die Schaltflächen
**Importieren**, **Exportieren**, **Importhistorie** und **Weiterleitungen** zu den Seiten:

| Seite | Adresse | Was sie tut |
|---|---|---|
| Importieren | `/content/urlimport` | Reiter **Eine Datei hochladen**, **Aus der alten Sitemap**, **Aus dem 404-Protokoll** und **Ziele vorschlagen**. Eine Datei hochladen oder Zeilen einfügen, Siteaccess und Sprache reiner Pfade wählen und was mit bestehenden Einträgen geschieht, alte Hosts zuordnen; dann die Vorschau mit einem Filter je Zustand, einem Haken je Zeile, **Probelauf**, dem Plan als CSV oder JSON, **Importieren** und nach dem Import **Die alten Adressen prüfen** |
| Importhistorie | `/content/urlimporthistory` | Jeder Import mit wer, wann, Datei und Zahlen; die Einträge eines Imports; **Import rückgängig machen** |
| Exportieren | `/content/urlexport` | Format, Umfang, Siteaccess und Sprache; lädt die Datei herunter |
| Weiterleitungen | `/content/urlredirects` | Die Weiterleitungen mit Abfrage oder Status, mit dem Entfernen einer oder der angehakten |

Importieren und Rückgängigmachen fragen vorher nach Ihrer Identität („Identität bestätigen“). Die Seiten erklärt
einzeln die Anleitung [URL-Zuordnungen: Import, Export und Rückgängig](../../anleitungen/url-zuordnungen.md).

## Richtlinien

| Was | Richtlinie |
|---|---|
| Importieren, die Importhistorie, Rückgängig machen | `content/urltranslator_import` |
| Exportieren, die Liste der Weiterleitungen | `content/urltranslator` |

Die Rolle Administrator hat beide. Geben Sie `content/urltranslator_import` nur den Personen, die die Adressen der
Website in großer Zahl ändern dürfen: Ein Import kann Hunderte Adressen auf einmal weiterleiten. Jeder Import und
jedes Rückgängigmachen steht im Audit-Protokoll (`content.urlalias.import`, `content.urlalias.import_undo`).

## Auf der Kommandozeile

| Befehl | Was er tut |
|---|---|
| [`exp:url:import`](../../beispiele/befehle/expurlimport.md) | Eine Datei planen und importieren; `--dry-run` schreibt nichts, `--report=<datei>` schreibt den Plan Zeile für Zeile |
| [`exp:url:export`](../../beispiele/befehle/expurlexport.md) | Die URL-Zuordnung in eine Datei oder auf die Standardausgabe schreiben |
| [`exp:url:batches`](../../beispiele/befehle/expurlbatches.md) | Die Importe auflisten |
| [`exp:url:undo`](../../beispiele/befehle/expurlundo.md) | Einen Import rückgängig machen |
| [`exp:url:from-sitemap`](../../beispiele/befehle/expurlfromsitemap.md) | Eine Zuordnung mit einer alten Sitemap (auch einem Sitemap-Index, `.gz`) oder einer Crawl-Liste beginnen |
| [`exp:url:from-404`](../../beispiele/befehle/expurlfrom404.md) | Eine Zuordnung mit den Adressen beginnen, die 404 geantwortet haben, die häufigsten zuerst |
| [`exp:url:suggest`](../../beispiele/befehle/expurlsuggest.md) | Neue Ziele für die Zeilen ohne eines vorschlagen, bewertet mit 0 bis 100 |
| [`exp:url:test`](../../beispiele/befehle/expurltest.md) | Jede alte Adresse auf einer Website anfragen und prüfen, dass sie auf ihrer neuen Adresse ankommt |

```bash
./console exp:url:import alte-site.csv -s site --dry-run --report=var/tmp/plan.csv
./console exp:url:import alte-site.csv -s site
./console exp:url:import alte-site.htaccess -s site --host=www.alte-seite.de=site
./console exp:url:batches
./console exp:url:undo 3
./console exp:url:export --output=var/tmp/url-map.xlsx
./console exp:url:export --kind=wildcard,redirect --format=nginx
./console exp:url:from-sitemap https://www.alte-seite.de/sitemap.xml --output=var/tmp/old-site.csv
./console exp:url:from-404 -s site --min-hits=3 --since=30d --output=var/tmp/not-found.csv
./console exp:url:suggest var/tmp/old-site.csv -s site --fill-above=85 --output=var/tmp/map.csv
./console exp:url:test var/tmp/map.csv --base=https://www.example.com -s site
```

Reine Pfade gehören zum Siteaccess, der mit `-s` angegeben ist (sonst zum Standard-Siteaccess). Mit
`--on-conflict=overwrite` ersetzt ein Import die Einträge an Adressen, die schon einen haben; standardmäßig
überspringt er sie. `--output-format=json` (Import, Export) und `--format=json` (Batches, Undo) geben
maschinenlesbare Ausgaben.

## Installation

Das Upgrade auf 6.0.15 legt drei Tabellen an, über den Upgrade-Schritt `6.0.15-url-map`
(`./console exp:upgrade --apply`). Solange der Schritt nicht gelaufen ist, ändert sich nichts, und ein Import wird mit
einer Meldung abgelehnt, die den Schritt nennt. Was hinzukommt und was gleich bleibt:
[URL-Zuordnungen importieren und exportieren](../../bc/6.0/url-zuordnungen-importieren.md).

## Verwandte Seiten

- [URLs migrieren](../../hilfe/urls-migrieren.md): das Tutorial Schritt für Schritt
- [URL-Zuordnungen als Beispiele](../../beispiele/url-zuordnungen/README.md): Beispieldateien, die sich sauber importieren lassen
- [Die Dateiformate für URL-Zuordnungen](../../spezifikationen/6.0/url-zuordnungsdateiformate.md)
- [URL-Zuordnungen importieren und exportieren](../../bc/6.0/url-zuordnungen-importieren.md)
- [Links, URL-Aliase, Platzhalter und Suchstatistiken](../../anleitungen/urls-und-aliase.md)
- [URL-Platzhalter](../../hilfe/ersteller/website-ersteller/url-platzhalter.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
