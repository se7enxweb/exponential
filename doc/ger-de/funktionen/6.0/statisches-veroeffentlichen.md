# Statisches Veröffentlichen

Diese Seite ist für Administratoren und Entwickler, die eine öffentliche Kopie einer Exponential-Website möchten, die
jeder statische Host ausliefern kann: GitHub Pages, die Pages eines anderen Git-Hosts oder ein einfacher Webserver
ganz ohne PHP. Statisches Veröffentlichen macht aus einem Front-Siteaccess einen Ordner mit HTML-Seiten und Dateien,
in dem jeder Link umgeschrieben ist, sodass der Ordner für sich allein funktioniert, an der Wurzel einer Domain oder
unterhalb eines Pfads wie `/meine-site/`. Redakteure arbeiten weiter in Exponential; die öffentliche Website ist eine
Kopie.

Es ist neu in Exponential 6.0.15. Diese Seite beschreibt die Build-Engine, das Veröffentlichen nach Git, ihre Befehle
und die Seiten unter Setup > Statisches Veröffentlichen. Die Einrichtung bei GitHub kommt hinzu, sobald sie erscheint.

[Funktionen von 6.0](README.md) · Verwandt: [Generator für den statischen Cache](generator-fuer-den-statischen-cache.md),
[Statische Websites auf GitHub Pages hosten mit Exponential 6.0.15+](../../anleitungen/statische-websites-auf-github-pages-hosten.md),
[Das Build-Format](../../spezifikationen/6.0/statisches-veroeffentlichen-build-format.md)

## Was Sie bekommen

- **Ziele** in `staticcache.ini`: Jeder Block `[Target_<id>]` nennt einen Front-Siteaccess, wie seine Links
  geschrieben werden und wohin die Kopie gehen soll. Eine Installation kann viele Ziele haben.
- **`exp:static:build <ziel>`**: durchläuft den Siteaccess als anonymer Benutzer, nimmt heraus, was eine öffentliche
  Kopie nicht tragen darf, schreibt die Links um, kopiert die Dateien, die die Seiten brauchen, und schreibt die
  Zusatzdateien, die ein statischer Host erwartet.
- **`exp:static:serve <ziel>`**: eine lokale Vorschau des Builds, unterhalb des Basispfads des Ziels ausgeliefert, so
  wie GitHub Pages eine Projekt-Website ausliefert.
- **`exp:static:diff <ziel>`**: was der letzte Build geändert hat, ob der Ordner noch zu ihm passt, und eine
  Linkprüfung jeder Seite und Datei.
- **`exp:static:targets`**: jedes Ziel mit geprüften Einstellungen.
- **`exp:static:publish <ziel>`**: committet den Build im eigenen Git-Repository des Ziels, taggt ihn
  `static/<ziel>/<n>` und pusht ihn, nie erzwungen, angemeldet mit einem Token oder einem SSH-Deploy-Key, die nie auf
  eine Befehlszeile oder in ein Log gelangen. Dazu gehören `exp:static:rollback`, `exp:static:push`, `exp:static:keys`
  und `exp:static:status`.

## Wie ein Build abläuft

1. **Als anonymer Benutzer durchlaufen.** Der Build nutzt den Crawler des
   [Generators für den statischen Cache](generator-fuer-den-statischen-cache.md) in einem Exportmodus. Er sendet kein
   Cookie, also ist jede Seite das, was ein nicht angemeldeter Besucher sieht. Er beginnt auf der Startseite, folgt
   nur den Links, die die Website selbst schreibt, und lässt Modulansichten wie die Suche, die Anmeldung und die
   Bearbeitungsseiten aus. Ein Ziel kann von einer internen Adresse (`CrawlURL`) statt von der öffentlichen `SiteURL`
   abrufen.
2. **Herausnehmen, was nicht öffentlich sein darf.** Jede Seite verliert ihre Debug-Ausgabe, Formular-Token
   (`ezxform_token`-Felder und die `csrf-token`-Tags), Sitzungskennungen in Adressen und Nonces der Content Security
   Policy.
3. **Ablehnen, was sich nicht bereinigen lässt.** Eine Seite, deren Antwort ein Sitzungs-Cookie gesetzt hat oder die
   noch Debug-Ausgabe oder ein Formular-Token trägt, wird abgelehnt. Der Build schlägt dann fehl, und der Ordner des
   Ziels bleibt unverändert. Seiten, die es nur mit einem Query-String gibt (`/blog?page=2`), und Pfade, die nicht auf
   jedem System ein Dateiname sein können, werden übersprungen und gemeldet.
4. **Die Dateien sammeln.** Jede Datei, die die Seiten und ihre Stylesheets nennen, wird kopiert: Stylesheets,
   Skripte, Schriften und Bilder der Designs, die gepackten Dateien in `var/<site>/cache/public`, Bild-Aliase und
   Downloads. Dateien, die der Webserver direkt ausliefert, werden aus der Installation gelesen (`var/` über den
   Cluster-Dateihandler, sodass auch die Dateien eines Clusters gefunden werden). Alles andere wird bei der Website
   abgerufen, wie es ein Besucher täte: ein Download, ein Bild-Alias, der noch nicht erzeugt wurde (die Anfrage erzeugt
   ihn), eine gepackte Datei, die noch nicht geschrieben wurde. Mit `Assets=referenced-and-storage` wird zusätzlich
   jedes Bild in `var/<site>/storage/images` kopiert, das zur veröffentlichten Version von Inhalten gehört, die der
   anonyme Benutzer lesen darf, ob eine Seite darauf verlinkt oder nicht.
5. **Die Links umschreiben.** Siehe [Links](#links) unten.
6. **Die Zusatzdateien und das Manifest schreiben.** `.nojekyll`, `CNAME`, `404.html`, `robots.txt` und `sitemap.xml`,
   wie das Ziel es verlangt, und `.static-publish/manifest.json`, das jede Datei mit SHA-256, Größe und, bei einer
   Seite, ihrem Knoten und dem Zeitpunkt der letzten Änderung ihres Inhalts aufführt.
7. **Vergleichen, prüfen und ersetzen.** Der Build entsteht zuerst in einem Staging-Ordner. Er wird mit dem Ordner des
   Ziels verglichen (der Diff), und seine Links werden geprüft. Ist es kein Probelauf, ersetzt er dann den Inhalt des
   Ordners: neue und geänderte Dateien werden kopiert, Dateien, die das letzte Manifest aufführte und dieses nicht,
   werden entfernt, und sonst wird im Ordner nichts angefasst. Ein `.git`-Ordner oder eine README, die Sie hinzugefügt
   haben, bleibt.

Ein Build der Demo-Website, 560 Seiten und 3.547 Dateien (485 MB, das meiste Speicherbilder), dauerte auf einer Kopie
ihrer Installation rund drei Minuten; den größten Teil braucht das Durchlaufen, das jede Seite einmal rendert. Die
Linkprüfung dieses Builds fand unter 18.534 Links und Dateien keinen defekten, an der Wurzel und unterhalb von
`/<repository>/`.

## Links

| Link in der Seite | In der Kopie |
|---|---|
| Eine Seite der Website (`/about-us`, `/index.php/about-us`, `https://www.example.com/about-us`) | `about-us/` relativ zur Seite (`../about-us/` von einer Seite eine Ebene tiefer) oder `/meine-site/about-us/` mit `Links=base-path` |
| Die Startseite | `./` oder `../../`, oder `/meine-site/` |
| Eine Datei (`/var/site/storage/images/...`, `/design/...`, `/extension/x/design/...`) | ihr Pfad in der Kopie, relativ oder unterhalb des Basispfads; der Query-String einer Datei (`?x=1`) bleibt |
| Ein Fragment (`#top`), `mailto:`, `tel:`, `data:`, ein anderer Host | unverändert |
| Eine Seite oder Datei, die die Kopie nicht enthält (die Suche, die Anmeldung, die Aktion eines Formulars, eine Seite mit Query-String, ein Download, den die Website verweigert hat) | die Live-Website: `LiveURL`, standardmäßig `https://` und der Host der `SiteURL` des Siteaccess |
| Canonical, `hreflang`-Alternativen, `og:url`, `og:image`, `twitter:image`, die Adressen strukturierter Daten (`application/ld+json`) | absolut, unterhalb von `PublicURL`, wenn es gesetzt ist |

Relative Links funktionieren überall, wo der Ordner ausgeliefert wird, unter `/` und unterhalb jedes Pfads.
`Links=base-path` schreibt jeden Link unterhalb von `BasePath`, wie es eine Website braucht, die immer an einer Stelle
ausgeliefert wird. `404.html` verlinkt immer unterhalb von `BasePath`: GitHub Pages liefert sie für jede fehlende
Adresse in jeder Tiefe aus, wo relative Links an die falsche Stelle zeigen würden.

Das Siteaccess-Präfix (`/site/about-us` bei einer per URI zugeordneten Website) und `index.php` werden aus jeder
Seitenadresse entfernt. Adressen in Skripten (außer strukturierten Daten) und Kommentaren bleiben unverändert.

## Einstellungen

Alle Einstellungen stehen in `staticcache.ini`, neben `[CacheSettings]` des statischen Caches. Tragen Sie Ihre in
`settings/override/staticcache.ini.append.php` ein.

`[StaticPublishSettings]`:

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `Enabled` | `disabled` | Statisches Veröffentlichen als Ganzes; solange es `disabled` ist, wird nichts gebaut |
| `TargetList[]` | leer | Die Kennungen der Ziele, die gebaut werden |
| `WorkDir` | `static-publish` | Wo Builds liegen, unterhalb von `var/<site>/`, sofern nicht absolut |
| `MaxSiteBytes` | `1000000000` | Der Build warnt oberhalb von 90 % davon (GitHub Pages erlaubt 1 GB pro Website) |
| `MaxFileBytes` | `100000000` | Der Build warnt bei größeren Dateien (GitHub erlaubt 100 MB pro Datei) |

`[Target_<id>]` (der mitgelieferte Block `[Target_example]` ist eine Vorlage mit jeder Einstellung; er wird nicht
gebaut):

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `Name` | die Kennung | In Listen angezeigt |
| `SiteAccess` | | Der zu veröffentlichende Front-Siteaccess; Administrations- und Bearbeitungs-Siteaccesses werden abgelehnt |
| `Links` | `relative` | `relative` oder `base-path` |
| `BasePath` | leer | Der Pfad, unter dem die Website ausgeliefert wird: `/<repository>/` für eine GitHub-Projekt-Website, leer für eine eigene Domain |
| `PublicURL` | leer | Die öffentliche Adresse, für die Sitemap, `robots.txt` und absolute Canonical- und Social-Card-Links |
| `CustomDomain` | leer | Wird in `CNAME` geschrieben; damit wird die Website an der Wurzel der Domain ausgeliefert |
| `CrawlURL` | leer | Die Seiten von dieser Adresse statt von `SiteURL` abrufen |
| `LiveURL` | leer | Wohin Links auf das gehen, was die Kopie nicht enthält; leer: `https://` und der Host der `SiteURL` |
| `MaxPages` | `10000` | Die meisten Seiten, die ein Build durchläuft (1 bis 20000) |
| `MaxDepth` | leer | Wie viele Links von der Startseite entfernt das Durchlaufen geht; leer: `[CacheSettings] MaxCacheDepth` |
| `Assets` | `referenced-and-storage` | `referenced`, oder zusätzlich jedes Speicherbild von Inhalten, die der anonyme Benutzer sehen darf |
| `Extras[]` | `nojekyll`, `404`, `robots`, `sitemap`, `manifest` | Die Dateien, die neben den Seiten geschrieben werden |
| `PostProcess[]` | `strip` | `strip` läuft immer |
| `ExcludePaths[]` | leer | Seiten, die nach Pfad ausgelassen werden (`/members/*`); Links darauf gehen zur Live-Website |
| `ExcludeSections[]` | leer | Seiten, die nach der Sektion ihres Inhalts ausgelassen werden |
| `Remote`, `Branch`, `Folder` | leer, `gh-pages`, leer | Die Git-Adresse, zu der der Build gepusht wird, ihr Branch, und `docs`, wenn die Website in `/docs` des Branches liegt (der Build wird dann nach `<ordner>/docs` geschrieben) |
| `Credential` | `none` | Wie sich der Push anmeldet: `token:<geheimnis>`, `token:env:<VARIABLE>`, `sshkey:<geheimnis>`, `app:<installation>` (mit der GitHub-Anbindung) oder `none` |
| `Kind`, `Delivery` | `github-pages`, `branch` | Jetzt gelesen und geprüft, genutzt von der Einrichtung bei GitHub |
| `Search`, `Forms`, `Triggers[]`, `Schedule`, `Notify[]`, `Webhook` | `disabled`, `hide`, `manual` und `cli`, leer, `failure`, leer | Jetzt gelesen und geprüft, genutzt von späteren Teilen des statischen Veröffentlichens |

Die erzeugte Referenz führt jede Einstellung mit ihrem Kommentar auf:
[`staticcache.ini [StaticPublishSettings]`](../../beispiele/ini-einstellungen/staticcache/StaticPublishSettings.md) und
[`[Target_example]`](../../beispiele/ini-einstellungen/staticcache/Target_example.md).

`exp:static:targets` prüft jedes Ziel: Der Siteaccess existiert und ist ein Front-Siteaccess, `BasePath` hat die Form
`/name/`, Adressen sind http oder https, `Remote` enthält kein Passwort und kein Token, `Credential` nennt ein
Geheimnis, statt eines zu enthalten, die Listen enthalten bekannte Werte. Fehler sind Fehler; zweifelhafte
Kombinationen (eine `PublicURL`, deren Pfad nicht `BasePath` ist, eine Sitemap ohne `PublicURL`) sind Warnungen.

## In der Administration

**Setup > Statisches Veröffentlichen** (das Modul `staticpublish`) tut, was die Befehle tun, mit Seiten: ein
Assistent, der Ziele anlegt und ändert (über den INI-Editor in `settings/override/staticcache.ini.append.php`
geschrieben), ein im Hintergrund gestarteter Build, dessen Fortschritt live verfolgt wird, eine Vorschau des Builds
(was sich ändert, mit Größen, der Bericht des Builds, die Größe gegenüber den Grenzen des Hosts, die Kopie hinter einem
signierten Link ausgeliefert) mit **Veröffentlichen** und **Verwerfen**, der Verlauf mit der Rücknahme, die Seite jedes
Auftrags und der Deploy-Key. Ein Dashboard-Block listet die Ziele mit ihren veralteten Seiten und ihrer letzten
Veröffentlichung auf, und Setup > Cache verlinkt darauf. Die Richtlinien `staticpublish/read`, `build`, `publish` und
`setup` nehmen eine Einschränkung nach Ziel (Target); Veröffentlichen, Zurücknehmen, das Speichern eines Ziels und der
Deploy-Key fragen „Bestätigen Sie, dass Sie es sind“. Das Hilfehandbuch:
[Statisches Veröffentlichen](../../hilfe/statisches-veroeffentlichen/README.md).

Die Administration startet ihre Aufträge als Konsolenprozesse (`exp:static:build`, `exp:static:publish` und
`exp:static:rollback` mit `--login`, `--run-id` und `--progress`), sodass ein langer Build nie eine Webanfrage
blockiert; die Auftragsdateien liegen in `<WorkDir>/.jobs`, und `[StaticPublishSettings] JobHistory` legt fest, wie
viele aufbewahrt werden.

## Befehle

| Befehl | Was er tut |
|---|---|
| `exp:static:targets` | Listet die Ziele mit Siteaccess, Links, öffentlicher Adresse, Ordner und letztem Build; Exit 1, wenn ein Ziel einen Fehler hat |
| `exp:static:build <ziel>` | Baut; `--dry-run` meldet, was sich ändern würde, ohne den Ordner anzufassen, `--paths=/a,/b` oder `--nodes=12,34` baut nur diese Seiten und behält den Rest, `--report=<datei>` schreibt den Bericht zusätzlich, `--crawl-url` und `--max-pages` übersteuern das Ziel |
| `exp:static:diff <ziel>` | Was der letzte Build geändert hat; `--verify` vergleicht den Ordner mit seinem Manifest; `--check-links --mount=/ --mount=/meine-site/` prüft jeden Link und jede Datei, wie ein Browser es täte |
| `exp:static:serve <ziel>` | Liefert den Ordner unter `http://127.0.0.1:8090/<basispfad>` mit dem eingebauten Webserver von PHP aus; `--base-path=/` liefert ihn an der Wurzel aus |
| `exp:static:publish <ziel>` | Committet den Build, taggt ihn `static/<ziel>/<n>` und pusht ihn; `--build` baut zuerst, `--message` setzt die Commit-Nachricht, `--no-push` committet und taggt nur |
| `exp:static:rollback <ziel> <n>` | Zeigt, was die Rückkehr zu Veröffentlichung `n` (oder zum Tag `static/<ziel>/<n>`) ändern würde; `--apply` macht daraus einen neuen Commit mit dem nächsten Tag und pusht ihn |
| `exp:static:push [<ziel>]` | Pusht die Ziele erneut, deren Push gescheitert ist (auch der Cronjob-Teil `staticpublish`) |
| `exp:static:keys <ziel>` | `--generate` erzeugt den SSH-Deploy-Key des Ziels und zeigt seine öffentliche Hälfte; ohne zeigt es öffentliche Hälfte und Fingerabdruck |
| `exp:static:status <ziel>` | Repository des Ziels, Zugangsdaten (lesbar oder nicht, nie der Wert), die letzten Läufe und die Seiten; `--limit` |

Jeder Befehl nimmt `--format=json` und gibt dann ein JSON-Dokument aus, Schema `exp-static/1`. Die Exit-Codes sind die
von `exp:upgrade`: 0 erledigt, 1 fehlgeschlagen, 2 Aufruffehler, 3 Datenbank nicht erreichbar, 4 interner Fehler. Die
erzeugten Seiten: [exp:static:build](../../beispiele/befehle/expstaticbuild.md),
[exp:static:diff](../../beispiele/befehle/expstaticdiff.md), [exp:static:serve](../../beispiele/befehle/expstaticserve.md),
[exp:static:targets](../../beispiele/befehle/expstatictargets.md),
[exp:static:publish](../../beispiele/befehle/expstaticpublish.md), [exp:static:rollback](../../beispiele/befehle/expstaticrollback.md),
[exp:static:push](../../beispiele/befehle/expstaticpush.md), [exp:static:keys](../../beispiele/befehle/expstatickeys.md),
[exp:static:status](../../beispiele/befehle/expstaticstatus.md).

## Veröffentlichen nach Git

Der Ordner des Ziels `var/<site>/static-publish/<ziel>/` ist ein eigenes Git-Repository (nie im Repository der
Website). `exp:static:publish` committet darin nur die Dateien des Builds, als einen Commit pro Veröffentlichung mit
dem annotierten Tag `static/<ziel>/<n>`, und pusht Branch und Tags zum `Remote` des Ziels. Ein Push wird nie
erzwungen, ein Tag nie verschoben; ein gescheiterter Push lässt den Commit lokal mit dem Push-Zustand `pending`, und
`exp:static:push` oder der Cronjob-Teil `staticpublish` sendet ihn erneut. Zurücknehmen ist ein neuer Commit mit den
Dateien eines früheren Tags, der Verlauf wächst also nur. Je Ziel läuft ein Build, eine Veröffentlichung, ein
Zurücknehmen oder ein Push zur Zeit (eine Sperre pro Ziel).

Zugangsdaten sind Namen: Ein Token unter Setup > Geheimnisse oder in einer Umgebungsvariablen gelangt über
`GIT_ASKPASS` und die Umgebung des einen git-Prozesses zu git; ein SSH-Deploy-Key, je Ziel von `exp:static:keys`
erzeugt, über eine Datei, die nur der Prozess für die Dauer des Pushs lesen kann, und `GIT_SSH_COMMAND`. Keines steht
je auf einer Befehlszeile, in der Remote-Adresse oder in einem Log.

Jede Veröffentlichung und jedes Zurücknehmen ist eine Zeile in `exp_static_run` (Zustand, Zahlen, Bytes, Commit, Tag,
Push-Zustand, wer, wann), und die Seiten der letzten Veröffentlichung stehen in `exp_static_page`; der Upgrade-Schritt
`6.0.15-static-publish-tables` legt beide Tabellen auf jeder Datenbank an. Das Audit zeichnet
`system.staticpublish.publish`, `.rollback` und `.credential` auf.

Anleitungen: [Repositorys und Zugangsdaten](../../anleitungen/statisches-veroeffentlichen-repositorys-und-zugangsdaten.md),
[Verlauf und Zurücknehmen](../../anleitungen/statisches-veroeffentlichen-verlauf-und-zuruecknehmen.md),
[SSH-Deploy-Keys](../../anleitungen/statisches-veroeffentlichen-deploy-keys.md).

## Datenschutz

- Builds laufen als anonymer Benutzer: Das Durchlaufen sendet kein Cookie, und die Prüfungen in der Datenbank (welche
  Speicherbilder kopiert werden dürfen) erfolgen für den anonymen Benutzer.
- Seiten mit Debug-Ausgabe, Formular-Token oder Sitzungskennungen werden bereinigt; eine Seite, die sich nicht
  bereinigen lässt oder deren Antwort ein Sitzungs-Cookie gesetzt hat, hält den Build an, bevor etwas in den Ordner
  des Ziels geschrieben wird.
- Speicherbilder werden nur für die veröffentlichte Version von Inhalten kopiert, die der anonyme Benutzer lesen darf.
  Bilder von Entwürfen (`storage/images-versioned`) und Dateien in `storage/original` werden nie aus dem Speicher
  kopiert; ein Download kommt nur in die Kopie, wenn eine Seite darauf verlinkt und die Website ihn einem anonymen
  Besucher gibt.
- `ExcludePaths[]` und `ExcludeSections[]` lassen Seiten aus.
- Lesen Sie den Diff eines Builds (`exp:static:build --dry-run`, `exp:static:diff`), bevor Sie ihn irgendwohin
  öffentlich übertragen.

## Plattformen

- PHP 7.4 ist nutzbar, 8.0 und neuer werden voll unterstützt.
- Jede Datenbank, auf der Exponential läuft; der Build liest nur Inhalts- und Bildtabellen.
- Apache mit PHP-FPM, Velocity und jeder andere Webserver: Der Build ruft Seiten wie ein Besucher über HTTP ab.
- Cluster-Speicher: Dateien unterhalb von `var/` werden über den Cluster-Dateihandler gelesen.
- Windows: Pfade werden im Manifest auf jedem System mit `/` geschrieben, Seitenpfade, die unter Windows keine
  Dateinamen sein können (`:`, `*`, `?`, `CON` und ähnliche), werden übersprungen und gemeldet, und der
  Vorschauserver wird ohne Shell gestartet.
