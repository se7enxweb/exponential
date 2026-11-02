# Spezifikation: das Manifest des statischen Veröffentlichens

Diese Seite beschreibt verbindlich das Manifest `exp-static-manifest/1`, das jeder Build des statischen
Veröffentlichens von Exponential 6.0.15 schreibt, und wie Build, Diff, Vorschau, Veröffentlichung und Rücknahme es
nutzen. Das Format des übrigen Builds steht in [Das Build-Format](statisches-veroeffentlichen-build-format.md); diese
Seite geht tiefer in die eine Datei, auf die sich alle anderen Teile stützen.

[Spezifikationen](README.md) · Verwandt: [Statisches Veröffentlichen](../../funktionen/6.0/statisches-veroeffentlichen.md),
[Das Build-Format](statisches-veroeffentlichen-build-format.md), [Der Formular-Endpunkt](statisches-veroeffentlichen-formular-endpunkt.md)

## Dateien

| Datei | Geschrieben von | Bedeutung |
|---|---|---|
| `.static-publish/manifest.json` | jedem Build, der kein Probelauf ist | die Dateien im Ordner des Ziels, wie dieser Build sie hinterließ |
| `.static-publish/manifest.previous.json` | demselben Build, bevor er das neue schreibt | das Manifest des vorigen Builds |

`manifest.json` wird mit jeder Veröffentlichung committet und gepusht, das Repository enthält also das Manifest jeder
Veröffentlichung neben ihren Dateien. `manifest.previous.json` bleibt nur im lokalen Ordner (das Veröffentlichen trägt
es in `.gitignore` ein).

## Kodierung

- JSON in UTF-8, eingerückt, Schrägstriche und Unicode nicht maskiert.
- `\n` als Zeilenende und ein abschließender Zeilenumbruch, auf jedem System.
- Die Schlüssel von `meta` sind sortiert. Derselbe Build ergibt unter Linux, macOS und Windows dieselben Bytes.

## Aufbau

```json
{
    "format": "exp-static-manifest/1",
    "meta": { "base_path": "/my-site/", "built_at": "2026-11-01T09:30:00+00:00", "links": "relative",
              "public_url": "https://owner.github.io/my-site/", "run_id": "20261101-093000-4f2a1c",
              "scope": "full", "siteaccess": "site", "target": "github-public" },
    "files": {
        "about-us/index.html": { "kind": "page", "sha256": "…", "size": 18234, "url": "/about-us",
                                 "node": 59, "modified": 1761984000 }
    }
}
```

| Schlüssel | Typ | Bedeutung |
|---|---|---|
| `format` | Zeichenkette | immer `exp-static-manifest/1`; ein Leser lehnt jeden anderen Wert ab |
| `meta.target`, `meta.siteaccess` | Zeichenkette | die Id des Ziels und der gebaute Siteaccess |
| `meta.run_id` | Zeichenkette | der Lauf des Builds (die Job-Seite und `exp_static_run`) |
| `meta.built_at` | Zeichenkette | ISO 8601 in UTC |
| `meta.links`, `meta.base_path`, `meta.public_url` | Zeichenkette | die Link-Einstellungen des Builds |
| `meta.scope` | Zeichenkette | `full`, oder `partial` für einen Build ausgewählter Seiten (`--paths`, `--nodes`, Veröffentlichen in der Seitenleiste) |
| `files` | Objekt | ein Eintrag je Datei, mit ihrem Pfad als Schlüssel |

## Pfade

Ein Pfad ist relativ zum Ordner des Ziels (zu `<folder>/docs` mit `Folder=docs`), mit `/` als Trenner, ohne führenden
Schrägstrich, ohne Laufwerksbuchstaben, ohne leeres, `.`- oder `..`-Segment. Ein Pfad anderer Form wird beim Lesen
oder Schreiben des Manifests abgelehnt. Das Manifest listet nie sich selbst, `.git` oder Dateien, die der Build nicht
geschrieben hat (eine von Ihnen hinzugefügte README).

## Ein Dateieintrag

| Schlüssel | Typ | Bedeutung |
|---|---|---|
| `kind` | Zeichenkette | `page`, `file` (von einer Seite oder einem Stylesheet genannt), `storage` (ein Speicherbild veröffentlichter Inhalte, das keine Seite nennt), `extra` (`.nojekyll`, `CNAME`, `404.html`, `robots.txt`, `sitemap.xml`, Suche, Weiterleitungen, Workflow, README, LICENSE) |
| `sha256` | Zeichenkette | SHA-256 der geschriebenen Bytes, hexadezimal in Kleinbuchstaben |
| `size` | Ganzzahl | Bytes |
| `url` | Zeichenkette | der Pfad auf der Website, von dem sie abgerufen wurde; leer bei Zusatzdateien |
| `node` | Ganzzahl oder null | der Knoten einer Seite, wenn bekannt |
| `modified` | Ganzzahl oder null | die letzte Änderung des Inhaltsobjekts der Seite, Unix-Zeit, wenn bekannt |

Alle sechs Schlüssel sind immer vorhanden. Ein Leser verlangt `kind`, `sha256` und `size` und behandelt fehlende
`url`, `node` oder `modified` als leer oder null.

## Wie das Manifest genutzt wird

- **Diff.** Zwei Manifeste werden nach Pfad und `sha256` verglichen: Ein Pfad nur im neuen ist hinzugekommen, einer in
  beiden mit anderem Hash geändert, einer nur im alten entfernt. Die Größen ergeben die hinzugekommenen und entfernten
  Bytes. Das zeigen die Vorschau, `exp:static:diff` und der Bericht.
- **Einen Build anwenden.** Neue und geänderte Dateien werden kopiert, eine gelistete Datei, die im Ordner fehlt, wird
  erneut kopiert, Dateien, die das vorige Manifest listete und dieses nicht, werden entfernt, und leer gewordene
  Ordner ebenso. Dateien, die kein Manifest listet, werden nie berührt.
- **Teil-Builds.** Ein `partial`-Build geht von den Einträgen des vorigen Manifests aus und ersetzt nur die gebauten
  Seiten und die Dateien, die sie nennen; das Ergebnis beschreibt also weiter den ganzen Ordner.
- **Prüfen.** `exp:static:diff --verify` berechnet die Hashes im Ordner und vergleicht sie mit dem Manifest.
- **Seiten einer Veröffentlichung.** Nach einem Commit werden die `page`-Einträge (Pfad, `url`, `node`) in die Tabelle
  `exp_static_page` geschrieben; das Veröffentlichen eines Inhalts markiert dort die Zeilen seines Knotens als veraltet,
  was Dashboard und Seitenleiste anzeigen.
- **Rücknahme.** Die Manifeste vorher und nachher ergeben die Zahlen dessen, was eine Rücknahme ändert.

## Kompatibilität

Spätere Versionen können Schlüssel in `meta` und in einem Dateieintrag hinzufügen; sie benennen nie einen um, entfernen
keinen und ändern nie die Bedeutung eines Werts. Eine Änderung, die das täte, bekommt einen neuen Wert für `format`.
