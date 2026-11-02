# Spezifikation: das Build-Format des statischen Veröffentlichens

Diese Seite ist die verbindliche Beschreibung dessen, was ein Build des statischen Veröffentlichens von Exponential
6.0.15 schreibt: der Ordner, wohin jede Seite und Datei kommt, wie Links umgeschrieben werden, was aus einer Seite
herausgenommen wird und was eine Seite ablehnen lässt, das Manifest `exp-static-manifest/1` und die Maschinenausgabe
`exp-static/1` der `exp:static`-Befehle. Spätere Teile des statischen Veröffentlichens (Git, die Administration,
GitHub) lesen diese Dateien; sie dürfen Schlüssel hinzufügen, nie einen umbenennen oder entfernen.

[Spezifikationen](README.md) · Verwandt: [Statisches Veröffentlichen](../../funktionen/6.0/statisches-veroeffentlichen.md),
[Statische Websites auf GitHub Pages hosten](../../anleitungen/statische-websites-auf-github-pages-hosten.md)

## Die Ordner

| Pfad | Was es ist |
|---|---|
| `var/<site>/<WorkDir>/` | Das Arbeitsverzeichnis aller Ziele (`[StaticPublishSettings] WorkDir`, Standard `static-publish`; absolut, wenn es mit `/` oder einem Laufwerksbuchstaben beginnt) |
| `var/<site>/<WorkDir>/<ziel>/` | Der Ordner des Ziels: die Website, wie ein statischer Host sie ausliefert, dazu `.static-publish/` |
| `var/<site>/<WorkDir>/.staging/<ziel>-<lauf-id>/` | Ein laufender Build; entfernt, wenn der Build endet (mit `--keep-staging` behalten) |

Eine Ziel-Kennung entspricht `^[a-z0-9][a-z0-9_-]{0,49}$`. Eine Lauf-Kennung ist `<JJJJMMTT>-<HHMMSS>-<6 Hex-Ziffern>`
(UTC).

Ein Build ersetzt den Inhalt des Ziel-Ordners nur, wenn er gelungen und kein Probelauf ist: Er kopiert die neuen oder
geänderten Dateien, kopiert eine aufgeführte Datei erneut, die fehlt, entfernt die Dateien, die das vorige Manifest
aufführte und dieses nicht (und die Ordner, die dadurch leer werden), und schreibt die Manifeste. Eine Datei, die kein
Manifest aufführt (ein `.git`-Ordner, eine von Hand hinzugefügte README), wird nie angefasst. Ein Teil-Build
(`--paths`, `--nodes`) entfernt nichts.

## Seiten

Eine Seite ist eine Antwort des Durchlaufens mit Status 2xx und Typ `text/html` an einer Adresse unterhalb des
Website-Pfads (das Siteaccess-Präfix und der Ordner der Installation, die nicht Teil des Seitenpfads sind). Ihr
Seitenpfad ist der Pfad der Adresse ohne den Website-Pfad, ohne `/index.php` und ohne abschließenden Schrägstrich; die
Startseite ist `/`.

Ein Ziel mehrerer Siteaccesses (`SiteAccessList[]`) durchläuft jeden unter seinem eigenen Website-Pfad in einen Ordner.
Der Seitenpfad eines anderen Siteaccess als des `DefaultSiteAccess` beginnt mit seinem Ordner, dem URI-Präfix, das ihn
wählt: `/bold_ger/team` ist die Seite `/team` von `bold_ger` (`bold_ger/team/index.html`). Eine Adresse gehört dem
Siteaccess, dessen Website-Pfad der längste ist, der sie enthält; so ist ein Link von einem Siteaccess der Kopie zu einem
anderen eine Seite der Kopie, eine Adresse unter dem Präfix eines Siteaccess, den das Ziel nicht veröffentlicht, eine
Seite, die die Kopie nicht enthält. Ohne `DefaultSiteAccess` liegt jeder Siteaccess in seinem Ordner, und `index.html`
ist ein Extra, das auf jeden verlinkt. Jeder Siteaccess wird unter seinem URI-Präfix durchlaufen, auch der im Stamm
(`/site`); ist site.ini `DefaultAccess` der im Stamm, ist eine Adresse im Stamm des Hosts ohne Präfix (`/about-us`)
ebenfalls seine Seite.

| Seitenpfad | Datei |
|---|---|
| `/` | `index.html` |
| `/about-us`, `/about-us/` | `about-us/index.html` |
| `/old/page.html` (endet auf `.html` oder `.htm`) | `old/page.html` |
| `/%C3%BCber%20uns` | `über uns/index.html` (decodiert) |

Eine Seite wird **übersprungen** (nicht geschrieben, unter `skipped` gemeldet), wenn ihre Adresse einen Query-String
hat oder wenn ein Segment ihres Pfads nicht auf jedem System ein Dateiname sein kann: leer, `.` oder `..`, mit
`< > : " \ | ? *` oder einem Steuerzeichen, endend auf einen Punkt oder ein Leerzeichen, oder ein reservierter
Gerätename (`CON`, `PRN`, `AUX`, `NUL`, `COM1` bis `COM9`, `LPT1` bis `LPT9`). Eine Seite wird **ausgelassen** durch
`ExcludePaths[]` (Shell-Muster auf den Seitenpfad, `*` passt auf jeden Teil, ein Muster, das auf `/*` endet, passt auch
auf die Seite selbst) oder `ExcludeSections[]` (die Sektion des Knotens der Seite).

## Adressen, die weiterleiten

Das Durchlaufen folgt einer Weiterleitung (Status 3xx) nicht selbst. Eine Adresse, die mit einer antwortet, wird nie
als Kopie der Seite geschrieben, zu der sie führt; sie wird zu einem **Weiterleitungs-Stub** an ihrer eigenen Datei:
dieselbe kleine Seite wie die Stubs von `Extras[]=redirects` (ein Meta-Refresh, ein Canonical-Link, `robots`
`noindex`, `<html lang="en">`, ein einfacher Link), im Manifest als `extra` geführt. Der Stub führt zu:

| Wo die Weiterleitung endet | Wohin der Stub zeigt |
|---|---|
| Eine Seite, die die Kopie enthält | Diese Seite in der Kopie (ein relativer oder Basispfad-Link, wie `Links` sagt); Canonical: ihre Adresse unter `PublicURL` |
| Eine Seite der Website, die die Kopie nicht enthält, oder eine Seite der Administration | Die Adresse auf der Live-Website (`LiveURL`, wie andere Links auf das, was die Kopie nicht enthält) |
| Dasselbe mit `OutsideLinks=unlink` (oder `AdminLinks=unlink` für eine Seite der Administration) | Die Startseite der Kopie: ein Stub ohne Ziel wäre eine Sackgasse |
| Ein anderer Host | Diese Adresse, wie sie ist; sie wird nie abgerufen |

Die Adresse, zu der eine Weiterleitung führt, wird einmal durchlaufen, unter ihrer eigenen Adresse und in der Tiefe der
weiterleitenden, wenn sie auf dem Host des Durchlaufens liegt (oder einer anderen Adresse der Website, `SiteURL` oder
`LiveURL`) und eine Seite der Website ist. Einer Kette (`/a` zu `/b` zu `/`) wird bis zu ihrem Ende gefolgt, höchstens
5 Weiterleitungen hintereinander. Kein Stub wird geschrieben, und die Adresse wird mit ihrem Grund unter `skipped`
gemeldet, bei einer Schleife, einer längeren Kette, einer Weiterleitung ohne `Location`, einer Weiterleitung auf ihre
eigene Datei (`/a` zu `/a/`), einer Adresse mit Query-String oder einer, die kein Dateiname sein kann, und in einer
Kopie ohne Startseite, wenn der Link nach außen herausgenommen wird. Ein Link einer Seite auf eine weiterleitende
Adresse führt zu ihrem Stub. `counts.redirects_followed` zählt die geschriebenen Stubs, `redirects_followed` listet sie.

## Was herausgenommen wird

Jede Seite und die 404-Seite der Website verlieren:

| Entfernt | Erkannt an |
|---|---|
| Der Debug-Bericht | von `<div id="debug">` bis `<a id="debug-end"></a>`, mit einem `<script>` direkt danach, das `debug` nennt |
| Template-Debug-Kommentare | `<!-- START: including template: ... -->`, `<!-- STOP: including template: ... -->` |
| Formular-Token | `<input>` namens `ezxform_token`; `<meta name="csrf-param">`, `csrf-token`, `csrf-token-x`; das versteckte `<span id="ezxform_token_js">` |
| Sitzungskennungen in Adressen | Parameter `eZSESSID…=` und `PHPSESSID=` (und das am Ende übrige `?` oder `&`) |
| Nonces der Content Security Policy | Attribute `nonce="…"`; ein CSP-`<meta http-equiv>`, das eine `nonce-` nennt |

## Was eine Seite ablehnen lässt

Nach dem Bereinigen wird eine Seite **abgelehnt**, wenn ihre Antwort ein Cookie setzte, dessen Name mit `eZSESSID`
oder `PHPSESSID` beginnt, oder die Seite noch `<div id="debug">`, `id="debug-end"`, einen Template-Debug-Kommentar,
`id="exp-debug-summary"`, ein `ezxform_token`-Eingabefeld, ein `csrf-token`-`<meta>` oder eine Sitzungskennung in
einer Adresse enthält. Eine abgelehnte Seite steht mit ihren Gründen unter `refused`, der Build endet mit Exit 1, und
der Ordner des Ziels bleibt unverändert.

## Links

Adressen werden umgeschrieben in den Attributen `href`, `src`, `srcset`, `imagesrcset`, `data-srcset`, `poster`,
`action`, `formaction`, `data-src`, `data-href`, `data-background`, `background`, `cite`, `longdesc`, `data`,
`manifest`; in `url()` von `style`-Attributen und `<style>`-Blöcken; in `content` von `<meta>`-Tags, deren `property`
oder `name` `og:url`, `og:image`, `og:image:url`, `og:image:secure_url`, `og:audio`, `og:video`, `og:video:url`,
`twitter:image`, `twitter:image:src`, `twitter:url`, `msapplication-TileImage` oder `thumbnail` ist, oder deren
`itemprop` `image`, `url` oder `thumbnailUrl` ist; in den absoluten Adressen (JSON-Strings, die mit `http://` oder
`https://` beginnen, `\/` maskiert oder nicht) strukturierter Daten, `<script type="application/ld+json">`, die absolut
geschrieben werden; und in Stylesheets in `url()` und `@import`. Der Inhalt jedes anderen `<script>` und von
Kommentaren wird nie geändert. Attributwerte werden vor dem Umschreiben decodiert und danach
codiert; ein Tag ohne etwas zum Umschreiben behält seine Bytes.

Eine Adresse gehört zur Website, wenn sie wurzelrelativ ist, relativ (aufgelöst gegen die Adresse des Dokuments
selbst) oder absolut auf einem der Ursprünge des Builds: die Adresse, von der der Build abrief, die `SiteURL` des
Siteaccess und `LiveURL`. Auf dem Host der `SiteURL` (wenn er nicht die Adresse ist, von der der Build abrief)
liegt die Website auch unterhalb des Pfads der `SiteURL` dort: Eine Website, die unterhalb von `/site` abgerufen,
aber an der Wurzel von `www.example.com` ausgeliefert wird, besitzt `https://www.example.com/about` ebenso wie
`https://www.example.com/site/about`. Punktsegmente werden aufgelöst und `/index.php` entfernt. Dann:

| Art | Regel | Geschrieben als |
|---|---|---|
| Datei des Hosts | der Pfad beginnt mit `var/`, `design/`, `extension/<name>/design/`, `share/`, `lib/ezjscore/`, oder ist `favicon.ico` oder `apple-touch-icon*` | der Pfad in der Kopie; Query und Fragment bleiben |
| Datei der Website | unterhalb des Website-Pfads, mit der Endung eines bekannten Dateityps (Bilder, Stylesheets, Skripte, Schriften, Medien, Dokumente, Archive, `json`, `xml`, `rss`, `ics`, `vcf`) | der Pfad unterhalb des Website-Pfads |
| Seite | unterhalb des Website-Pfads, alles andere | die Adresse der Seite in der Kopie (`about-us/`, `''` für die Startseite, die Datei bei `*.html`), das Fragment bleibt |
| Außerhalb | nicht unterhalb des Website-Pfads | die Live-Website |

Eine Seite, die der Build nicht enthält (nicht durchlaufen, übersprungen, ausgelassen, abgelehnt oder mit
Query-String), und eine Datei, die nicht gesammelt werden konnte, werden als Adresse der Live-Website geschrieben:
`LiveURL` (Standard `https://` und der Host der `SiteURL`), gefolgt von Pfad, Query und Fragment des Originals. Ohne
Live-Adresse bleiben sie unverändert. Adressen auf anderen Hosts, Fragmente derselben Seite (`#top`), ein alleiniger
Query-String (`?x`) und `mailto:`, `tel:`, `javascript:`, `data:` und andere Schemata werden nie geändert.

Mit `OutsideLinks=unlink` wird ein Link (`<a href>`) auf eine solche Seite, und mit `AdminLinks=unlink` ein Link,
dessen Pfad mit dem Pfad eines Siteaccess der Administration beginnt, stattdessen herausgenommen. Er bleibt ein `<a>`,
ein Platzhalter-Link ohne Adresse, so dass jede Regel des Themes für `a`, `nav a`, `.menu a` und ähnliche ihn in den
Menüs, der Fußzeile, den Breadcrumbs, Buttons und Karten weiter gestaltet:

- `href`, `target`, `rel`, `download`, `ping`, `hreflang`, `referrerpolicy` und die Event-Handler (`on...`) fallen weg;
  jedes andere Attribut und der Inhalt bleiben, ebenso das schließende `</a>`.
- `aria-disabled="true"` kommt hinzu, und die Klasse `exp-static-unavailable` (im `class`-Attribut des Links, oder als
  eigenes). Ein `title` kommt nicht hinzu.
- `exp-static.css` an der Wurzel des Builds wird am Ende des `<head>` jeder Seite verlinkt, die einen solchen Link hat
  (wie jeder Link: relativ oder unter dem Basispfad; umbenannt, wenn `PostProcess[]=fingerprint` gesetzt ist). Sie
  wird nur geschrieben, wenn eine Seite sie braucht, und enthält nur
  `.exp-static-unavailable{opacity:.5;cursor:not-allowed;text-decoration:none;pointer-events:none}` und eine Regel, die
  das bei Hover und Fokus so hält: Farbe, Schrift und Abstände kommen vom Theme.

Ein `<link>`, dessen `rel` nur Navigation nennt (`alternate`, `search`, `home`, `next`, `prev` ...), wird entfernt.
Dateien, Bilder, Stylesheets, Skripte, Formularaktionen und andere Hosts werden nie herausgenommen. Ein Pfad der
Administration gilt nie als Seite der Website. Der Bericht zählt beides (`links_removed_outside`,
`links_removed_admin`) und die Platzhalter-Links (`links_unavailable`).

**Relative Links** (`Links=relative`) sind relativ zum Ordner der Datei, die sie enthält: `about-us/` von
`index.html`, `../` von `about-us/index.html` zur Startseite, `./` zur eigenen Seite des Ordners.
**Basispfad-Links** (`Links=base-path`) sind `BasePath`, gefolgt von der Adresse in der Kopie: `/meine-site/about-us/`.
Mit einer `PublicURL` sind `<link rel="canonical|alternate|shortlink">` und die `<meta>`-Tags der Social Cards absolut:
`PublicURL`, gefolgt von der Adresse in der Kopie. `404.html` wird immer mit Basispfad-Links geschrieben.

## Dateien

Eine Datei, die die Seiten oder Stylesheets nennen, wird aus der Installation gelesen, wenn ihr Pfad unterhalb eines
Ordners liegt, den der Webserver als Dateien ausliefert:

- `design/<design>/(stylesheets|images|javascript|javascripts|fonts|lib|icons)/`
- `extension/<name>/design/<design>/(stylesheets|images|javascript|javascripts|fonts|lib|flash|icons)/`
- `share/icons/`
- `var/[<site>/]storage/images/`
- `var/[<site>/]cache/(public|texttoimage)/`
- `favicon.ico`, `apple-touch-icon*`

Pfade unterhalb von `var/` werden über den Cluster-Dateihandler gelesen. Jede andere Datei wird als anonymer Benutzer
bei der Website abgerufen und behalten, wenn die Antwort 2xx und keine HTML-Seite ist (außer die Datei ist eine). Ein
Pfad, der auf `.php`, `.phtml`, `.phar`, `.ini`, `.inc`, `.tpl`, `.sql`, `.sh`, `.env`, `.lock`, `.htaccess`,
`.htpasswd`, `.bak`, `.orig` oder `.swp` endet oder ein Segment `.git`, `.svn` oder `.env` enthält, wird nie kopiert.
Stylesheets werden umgeschrieben, nachdem die Dateien gesammelt sind, die sie nennen, bis zu 5 Stylesheets tief.

Mit `Assets=referenced-and-storage` kopiert der Build zusätzlich jede Datei der Tabelle `ezimagefile` unterhalb von
`var/<site>/storage/images/`, deren Inhaltsobjekt-Attribut zur aktuellen Version eines veröffentlichten Objekts
(`status = 1`) gehört, das der anonyme Benutzer lesen darf. Ihre Art im Manifest ist `storage`; eine Datei, die auch
eine Seite nennt, ist von der Art `file`.

## Zusatzdateien

| Datei | Geschrieben, wenn | Inhalt |
|---|---|---|
| `.nojekyll` | `Extras[]=nojekyll` | leer |
| `CNAME` | `CustomDomain` gesetzt ist | die Domain und ein Zeilenumbruch |
| `404.html` | `Extras[]=404` | die Antwort der Website auf eine fehlende Adresse (`<website-pfad>/static-publish-not-found-<lauf-id>`), wenn sie eine 404-HTML-Seite ist, die die Prüfungen besteht, bereinigt und mit Basispfad-Links umgeschrieben; sonst eine einfache Seite mit einem Link auf den Basispfad |
| `robots.txt` | `Extras[]=robots` | `User-agent: *`, `Allow: /` und `Sitemap: <PublicURL>sitemap.xml`, wenn eine Sitemap geschrieben wird |
| `sitemap.xml` | `Extras[]=sitemap` und eine `PublicURL` | die absolute Adresse jeder Seite, sortiert, mit `<lastmod>` (UTC-Datum), wenn die Änderungszeit ihres Inhalts bekannt ist |

Eine Zusatzdatei ersetzt eine Seite oder Datei gleichen Namens, mit einer Warnung.

## Das Manifest: `exp-static-manifest/1`

`.static-publish/manifest.json` im Ordner des Ziels; das vorige bleibt als `.static-publish/manifest.previous.json`.
JSON, sortierte Schlüssel, `/` als Trenner, `\n` als Zeilenende, ein abschließender Zeilenumbruch: derselbe Build ergibt
auf jedem System dieselben Bytes.

```json
{
    "format": "exp-static-manifest/1",
    "meta": {
        "base_path": "/meine-site/",
        "built_at": "2026-11-01T09:30:00+00:00",
        "links": "relative",
        "public_url": "https://owner.github.io/meine-site/",
        "run_id": "20261101-093000-4f2a1c",
        "scope": "full",
        "siteaccess": "site",
        "target": "github-public"
    },
    "files": {
        "about-us/index.html": {
            "kind": "page",
            "sha256": "…",
            "size": 18234,
            "url": "/about-us",
            "node": 59,
            "modified": 1761984000
        }
    }
}
```

| Schlüssel einer Datei | Bedeutung |
|---|---|
| `kind` | `page`, `file` (von einer Seite oder einem Stylesheet genannt), `storage` (ein Speicherbild, das keine Seite nennt), `extra` |
| `sha256` | der geschriebenen Bytes |
| `size` | in Bytes |
| `url` | der Host-Pfad, von dem sie stammt (leer bei Zusatzdateien) |
| `node` | der Knoten einer Seite, wenn bekannt, sonst `null` |
| `modified` | die Änderungszeit des Inhaltsobjekts einer Seite (Unix-Zeit), wenn bekannt, sonst `null` |
| `siteaccess` | nur im Build mehrerer Siteaccesses: der Siteaccess einer Seite |

Die `meta` eines Builds mehrerer Siteaccesses enthält auch `siteaccesses`: je `name` und `folder` (`/` für den im
Stamm, `/bold_ger/`), der im Stamm zuerst; `siteaccess` ist der im Stamm (sonst der erste). Der Suchindex eines solchen
Builds versieht jeden Eintrag mit `s`, seinem Siteaccess, und die Suchseite bietet an, einen davon
(`search.html?site=<siteaccess>`) oder alle zu durchsuchen.

## Die Maschinenausgabe: `exp-static/1`

Mit `--format=json` gibt jeder `exp:static`-Befehl ein JSON-Objekt mit `schema` (`exp-static/1`), `command`, `mode`,
`errors`, `warnings` und `exit` und den Schlüsseln seines Modus aus. `exp:static:build --report=<datei>` schreibt
dasselbe Objekt in eine Datei.

| Schlüssel eines Builds | Bedeutung |
|---|---|
| `mode` | `build` oder `dry-run` |
| `scope` | `full` oder `partial` |
| `target`, `siteaccess`, `siteaccesses`, `run_id`, `started_at`, `duration_ms`, `directory` | der Build (`siteaccess`: der im Stamm, sonst der erste; `siteaccesses`: alle des Ziels) |
| `links` | `mode`, `base_path`, `public_url` |
| `counts` | `pages`, `pages_refused`, `pages_skipped`, `pages_excluded`, `files`, `storage`, `files_failed`, `extras`, `links_rewritten`, `links_live`, `links_external`, `links_removed_outside`, `links_removed_admin`, `links_unavailable`, `site_broken_links`, `bytes`, `stripped` (nach dem, was herausgenommen wurde), `redirects` (Stubs von `Extras[]=redirects`), `redirects_followed` (Stubs von Adressen, die weiterleiten) |
| `diff` | `added`, `changed`, `removed`, `unchanged` (Anzahlen), `bytes_added`, `bytes_removed`, `files` (die Pfade, höchstens 500 von jedem) |
| `pages` | jede Seite: `path`, `file`, `bytes`, `node`, `stripped` |
| `refused` | jede abgelehnte Seite: `path`, `url`, `reasons` |
| `skipped` | jede übersprungene Seite: `path`, `reason` |
| `redirects_followed` | jeder Stub einer Adresse, die weiterleitet: `path`, `status`, `to` (wo die Weiterleitung endet), `kind` (`page`, `live`, `front`), `file` |
| `files_failed` | jede nicht gesammelte Datei: `file`, `url`, `error` |
| `link_check` | `mount`, `files`, `links`, `broken` (höchstens 200: `file`, `link`, `resolved`), `broken_count`, `skipped` |
| `limits` | `bytes`, `max_site_bytes`, `max_file_bytes`, `too_large` |
| `applied` | ob der Ordner des Ziels geändert wurde |

`exp:static:targets` meldet `enabled` und `targets` (die Einstellungen jedes Ziels mit `id`, `name`, `directory`,
`built_at`, `pages`, `bytes`, `errors`, `warnings`); `exp:static:diff` meldet `diff` (die Pfade von `added`,
`changed`, `removed`, `unchanged`, `first_build`), `verify` (`missing`, `changed`, `unlisted`) und `link_checks`.

| Exit | Bedeutung |
|---|---|
| 0 | erledigt |
| 1 | fehlgeschlagen: ein Fehler in einem Ziel, eine abgelehnte Seite, nichts exportiert, ein Problem, das `--verify` oder `--check-links` findet |
| 2 | Aufruffehler: eine falsche Option oder ein falsches Argument |
| 3 | die Datenbank ist nicht erreichbar |
| 4 | interner Fehler |

## Die Linkprüfung

Jede `*.html`- und `*.css`-Datei des Ordners (außerhalb von `.git` und `.static-publish`) wird gelesen; jede Adresse
in `href`, `src`, `poster`, `data-src`, `srcset`, `data-srcset`, `style` und `<style>` oder in `url()` und `@import`
wird gegen die Adresse aufgelöst, unter der die Datei ausgeliefert wird, wenn der Ordner unter einem Pfad eingehängt
ist, ohne Query und Fragment. Sie muss eine Datei des Ordners nennen; eine Adresse, die auf `/` endet, nennt deren
`index.html`, ein Ordner ohne seinen Schrägstrich wird gefunden, wenn er eine `index.html` hat. Adressen auf anderen
Hosts, protokollrelative Adressen, Fragmente, alleinige Query-Strings und Schemata werden nicht geprüft. `404.html`
wird gegen den Einhängepunkt aufgelöst und nur unterhalb des Basispfads gelesen, für den sie gebaut wurde.
