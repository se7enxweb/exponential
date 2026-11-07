---
title: URL-Zuordnungen als Beispiele
audience: admin, developer
category: examples
order: 60
summary: Kleine, realistische URL-Zuordnungen im CSV-, JSON-, Apache- und nginx-Format, mit Weiterleitungen mit Abfrage, vorübergehenden Weiterleitungen und Platzhaltern, jede mit exp:url:import --dry-run geprüft.
keywords: URL-Zuordnung, Beispiel, Zuordnungsdatei, CSV, JSON, htaccess, Apache, nginx, Weiterleitung, Platzhalter, Abfrage, Query-String, vorübergehende Weiterleitung, exp:url:import, URL map, example, mapping file, redirect, wildcard, query string, temporary redirect
type: example
---
# URL-Zuordnungen als Beispiele

Diese Dateien sind kleine URL-Zuordnungen einer gedachten alten Website, die zur Website Fit & Healthy der
Exponential-Demo umzieht. Kopieren Sie eine, ändern Sie die Adressen auf Ihre eigenen und importieren Sie sie. Jedes
Ziel ist eine Seite dieser Website, daher lässt sich jede Datei sauber in sie importieren (nur die Knoten-ID muss
eventuell angepasst werden, siehe unten). Die Dateien sind dieselben wie die englischen, mit deutschen Hinweisen und
Kommentaren.

Wie Sie sie verwenden: [URLs migrieren](../../hilfe/urls-migrieren.md). Die Spalten und Arten:
[URL-Zuordnungen importieren und exportieren](../../funktionen/6.0/url-zuordnungen-importieren-und-exportieren.md).
Jede Einzelheit der Formate: [Die Dateiformate für URL-Zuordnungen](../../spezifikationen/6.0/url-zuordnungsdateiformate.md).

## Die Dateien

| Datei | Format | Was sie zeigt | Zeilen |
|---|---|---|---|
| [`alte-site.csv`](alte-site.csv) | CSV | Dauerhafte Weiterleitungen einzelner Seiten, ein Ziel über die Knoten-ID (`node:163`), ein `alias`, der die Seite unter der alten Adresse zeigt | 8 |
| [`alte-site.json`](alte-site.json) | JSON | Dieselben Weiterleitungen als JSON, mit dem Kopf `exp-url-map/1` | 7 |
| [`alte-site.htaccess`](alte-site.htaccess) | Apache | `RewriteRule`, ein `Redirect` eines ganzen Bereichs (gelesen, wie Apache ihn beantwortet: die Seite und alles darunter), `RedirectMatch` als Platzhalter und eine alte Adresse mit Abfrage (`RewriteCond %{QUERY_STRING}`) | 7 |
| [`alte-site.nginx`](alte-site.nginx) | nginx | `location =` mit `return`, alte Adressen mit Abfrage (`if ($args = ...)`), `rewrite` als Platzhalter und eine `map` einzelner Seiten | 7 |
| [`query-und-voruebergehend.csv`](query-und-voruebergehend.csv) | CSV | Weiterleitungen mit Abfrage (`/index.php?id=12`), eine 308 und vorübergehende Weiterleitungen über die Art (`redirect-temporary`) und über den Status (307) | 5 |
| [`wildcards.csv`](wildcards.csv) | CSV | Platzhalter: ein umgezogener Bereich, ein `*` mitten in einer Adresse, ein vorübergehender Platzhalter und ein direkter (ohne Weiterleitung) | 4 |

„Zeilen“ ist, was der Import aus der Datei macht: Ein `Redirect` von Apache ergibt zwei Zeilen (die Seite und einen
Platzhalter für alles darunter).

## Eine ausprobieren

Die Datei planen, ohne etwas zu schreiben:

```bash
./console exp:url:import doc/ger-de/beispiele/url-zuordnungen/alte-site.csv -s site --dry-run
```

```text
URL map dry run: doc/ger-de/beispiele/url-zuordnungen/alte-site.csv (8 rows)
  Plan:   8 new, 0 changed, 0 unchanged, 0 duplicate, 0 conflict, 0 broken target, 0 unknown host, 0 not importable
  Action: 8 to create, 0 to update, 0 skipped
  Done:   8 created, 0 updated, 0 skipped, 0 failed
```

Bei einem Probelauf zählt „Done“, was der Import tun würde. Lassen Sie `--dry-run` weg, um zu importieren, und machen
Sie es mit `./console exp:url:undo <nummer>` wieder rückgängig (die Nummer gibt der Import aus, und
`./console exp:url:batches` listet sie). In der Administration gehen dieselben Dateien unter **Setup** >
**URL-Übersetzer** > **Importieren**.

Prüfen Sie nach einem echten Import, dass jede alte Adresse dort ankommt, wo die Datei es sagt (die Basis ist die
Website, in die Sie importiert haben):

```bash
./console exp:url:test doc/ger-de/beispiele/url-zuordnungen/alte-site.csv --base=https://www.example.com -s site
```

In der Administration tut **Prüfen** auf der Seite mit dem Ergebnis des Imports dasselbe. Für eine eigene Datei zeigt
das Tutorial `exp:url:from-sitemap`, `exp:url:from-404` und `exp:url:suggest`.

## Wie sie geprüft wurden

Jede Datei wurde mit `exp:url:import --dry-run` gegen eine Installation mit der Website Fit & Healthy geplant: Jede
Zeile jeder Datei kam als **Neu** heraus, ohne fehlendes Ziel, Konflikt, unbekannten Host oder nicht importierbare
Zeile.

## Auf Ihrer eigenen Website

- Ersetzen Sie die alten Adressen durch die Ihrer alten Website und die neuen durch Seiten Ihrer Website. Eine neue
  Adresse, die es nicht gibt, steht als **Ziel fehlt** in der Vorschau und wird nicht importiert.
- `node:163` ist die Seite Running der Installation, auf der die Dateien geprüft wurden. Knoten-IDs unterscheiden sich
  von Installation zu Installation: Nehmen Sie die Knoten-ID Ihrer eigenen Seite (die Seite Running der Demo ist
  `node:171`).
- `/user/login`, `/content/view/full/2` und andere Adressen des Systems können keine alten Adressen sein: Die Vorschau
  listet sie als **Konflikt**.
