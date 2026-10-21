# Das Hilfesystem: die Dokumentation in der Administrationsoberfläche

Lesen Sie diese Seite, wenn Sie bei der Arbeit in der Administrationsoberfläche Hilfe suchen, wenn Sie entscheiden,
wer die Hilfe lesen darf, oder wenn Sie Dokumentation schreiben (in `doc/` oder in einer Extension), die darin
erscheinen soll.

## Kurz gesagt

- **Hilfe-Dashboard** (`/exphelp/dashboard`): das Exponential-Logo über „Hilfe-Center“ (die Grafik der Kopfzeile,
  `exphelp/parts/dashboard_brand.tpl`, je Design), ein Suchfeld, die Hilfe für jede Zielgruppe (Redakteure, Administratoren,
  Entwickler, Websitebenutzer, Projektleiter, Vertrieb & Marketing, Partner, Fürsprecher, Designer, Hosting-Unternehmen, App-Entwickler, Website-Ersteller, Unterstützer, Sponsoren, Trainer, Presse, Medien und Influencer, Unternehmen, Behörden und Kunden; die Karten für Projektleiter und für Vertrieb & Marketing verlinken ihre Startseite), die Kategorien, empfohlene Seiten, die neuesten Änderungen an der Dokumentation und wie Sie
  einen Fehler melden.
- **Hilfeseiten** (`/exphelp/viewer/<page>`): jede von git verfolgte Markdown- und Textdatei unter `doc/`, gerendert
  mit Überschriften, einem Inhaltsverzeichnis, Tabellen, Hinweiskästen, hervorgehobenem Code und Bildern, mit der
  Navigation ihres Abschnitts, der vorherigen und nächsten Seite und einem Link zur Datei auf GitHub. `<page>` ist der
  Pfad unterhalb von `doc/` (`/exphelp/viewer/features/6.0/help-system.md`).
- **Suche** (`/exphelp/search/(q)/<words>`, siehe „Eine Seite finden“): findet Seiten nach Titel, Überschriften und Text, mit Filtern für Zielgruppe, Kategorie
  und Typ. Das Suchfeld der Hilfeseiten zeigt Ergebnisse schon während der Eingabe; drücken Sie `/`, um es zu fokussieren.
- **Benutzerhandbuch** (`/exphelp/viewer/help/manual/README.md`, die Kategorie **Benutzerhandbuch**): eine Seite pro
  Frage zur Arbeit in der Administrationsoberfläche Admin 4 (die Reiter, die täglichen Aufgaben, die Aufgaben von
  Administratoren und Websiteverantwortlichen), jeweils mit der Angabe, wer es tun darf, den Schritten, was schiefgehen
  kann, und den verwandten Seiten. Exponential Intelligence hat ein eigenes Handbuch in derselben Form unter der
  Dokumentation der Extensions (`/exphelp/viewer/ext/expai/manual/README.md`).
- **Extensions** (`/exphelp/extensions`, siehe „Dokumentation der Extensions“): die Dokumentation, die jede aktive
  Extension mitbringt (ihr Verzeichnis `doc/`, README, CHANGELOG, LICENSE ...), im selben Viewer und in derselben
  Suche, mit einem Index pro Extension.
- **Kontexthilfe**: Die Schaltfläche **Hilfe** in der Kopfzeile der Administrationsoberfläche öffnet die Hilfeseite,
  die zur aktuellen Seite gehört (Modul und Ansicht), oder das Dashboard, wenn es keine gibt.
- **Fehler melden** (`/exphelp/report`): welcher Tracker zu verwenden ist, was Sie schreiben sollten, ein vorausgefülltes
  Issue und die Diagnosedaten zum Kopieren (Versionen, PHP, Engine, Design; niemals Passwörter, Schlüssel,
  Datenbank-Hosts oder personenbezogene Daten) sowie wo Sie eine Sicherheitslücke vertraulich melden.

## Wer sie lesen darf

Die Richtlinienfunktion `exphelp/read` öffnet jede Hilfeansicht. Die Rolle Administrator hat sie (sie enthält jede
Richtlinie); die Rolle Anonymous hat sie nicht, und die Hilfe wird Besuchern, die nicht angemeldet sind, nie angezeigt.
Eine neue Installation gibt der Rolle Editor `exphelp/read`. Auf einer Installation aus der Zeit vor dem Hilfesystem
vergeben Sie sie mit

```sh
php bin/php/addexphelpreadpolicy.php --dry-run      # lists what it would grant
php bin/php/addexphelpreadpolicy.php                # the Editor role
php bin/php/addexphelpreadpolicy.php --role=Editor --role=5
```

(`./console exp:help-grant` führt denselben Befehl aus.) Rollen, die die Hilfe bereits lesen dürfen, bleiben
unverändert, sodass er erneut laufen kann. Von Hand: Rollen und Richtlinien, die Rolle, Neue Richtlinie, Modul
`exphelp`, Funktion `read`. Die Schaltfläche Hilfe erscheint nur für Benutzer, die sie haben.

## Was sie zeigt und was nie

Die Hilfe zeigt nur Dateien, die **von git verfolgt** werden, unterhalb der Verzeichnisse von
`exphelp.ini [IndexSettings] Roots[]` (standardmäßig `doc/`), mit einer Seitenendung (`md`, `markdown`, `txt`, `rst`
oder keine) oder einer Bildendung (`png`, `jpg`, `jpeg`, `gif`, `webp`), sowie die verfolgten Projektdateien außerhalb
von `doc/` (`README.md`, `SECURITY.md` ..., siehe „Projektdateien“). Alles andere ergibt einen 404: andere Dateien
außerhalb von `doc/`, nicht verfolgte Dateien, Pfade mit `..`, symbolische Links, generiertes HTML
(`doc/eng-us/doxygen/`), SVG-Dateien und Dateien über `MaxFileSize`. Eine Installation ohne `.git` verwendet das
Dateimanifest `share/filelist.md5` als Liste der verfolgten Dateien.

Markdown wird sicher gerendert: Rohes HTML wird maskiert, Links mit `javascript:` und ähnlichen Schemata werden
verworfen, Links auf andere Seiten werden zu Hilfelinks, externe Links öffnen mit `rel="noopener"`, und Bilder kommen
nur aus `doc/` oder von `https://`-Adressen. Die Seiten brauchen weder Inline-Skript noch Inline-Stil und funktionieren
daher unter der Content Security Policy der Administrationsoberfläche.

## Einstellungen: exphelp.ini

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `[HelpSettings] Enabled` | `true` | `false` macht jede Hilfeansicht zu einem 404 und blendet die Schaltfläche Hilfe aus |
| `[HelpSettings] EditBaseURL` | GitHub, `main` | der Link „Auf GitHub bearbeiten“ einer Seite; leer für keinen |
| `[IndexSettings] Roots[]` | `doc` | die Verzeichnisse, deren verfolgte Dateien angezeigt werden |
| `[IndexSettings] ActiveExtensionDocs` | `enabled` | die Dokumentation jeder aktiven Extension wird ebenfalls angezeigt („Dokumentation der Extensions“) |
| `[IndexSettings] ExtensionTopFiles[]` | `README*`, `CHANGELOG*`, `LICENSE*`, `INSTALL*`, `USAGE*`, `COPYING*` | die Dateien oben im Verzeichnis einer Extension, die neben ihrem `doc/` angezeigt werden |
| `[IndexSettings] ExtensionDocDirs[]` | leer | Extensions, deren Dokumentation angezeigt wird, ob sie aktiv sind oder nicht |
| `[IndexSettings] ExcludePatterns[]` | doxygen, HTML, SVG | nie angezeigt, auch wenn verfolgt |
| `[ProjectFiles] Enabled`, `Include[]`, `Exclude[]` | `enabled`, die `*.md` im Wurzelverzeichnis und die README.md von `bin/`, `design/`, `kernel/` | die verfolgten Markdown-Dateien außerhalb von `doc/`, die als Projektdateien angezeigt werden (siehe „Projektdateien“) |
| `[AudienceSettings] Audiences[]` | `editor`, `admin`, `developer`, `user`, `project-manager`, `sales-marketing`, `partner`, `advocate`, `designer`, `hosting`, `app-creator`, `website-creator`, `supporter`, `sponsor`, `trainer`, `press`, `businesses`, `government`, `customer` | die Zielgruppen in der Reihenfolge des Dashboards, mit `AudienceNames[]` und `AudienceIcons[]` |
| `[AudienceSettings] PathRules[]` | pro Verzeichnis | `<glob>;<audience,...>`, der erste Treffer gewinnt |
| `[CategorySettings]` | acht Kategorien | Namen, Icons und Pfadregeln |
| `[SearchSettings]` | 20 Ergebnisse | Anzahl der Ergebnisse und die Gewichtung von Titel, Überschriften und Text |
| `[SearchSettings] AudienceBoost[]` | `trainer` 0.6 | ein Faktor für die Seiten einer Zielgruppe (der niedrigste der Zielgruppen einer Seite), nicht angewandt, solange die Suche auf diese Zielgruppe gefiltert ist: Die Lektionen der Trainer stehen unter den Redakteurs- und Administratorseiten, die sie lehren |
| `[FeaturedSettings] PageList[]` | sechs Hilfeseiten | die „Empfohlenen Anleitungen“ des Dashboards |
| `[ContextHelp] Map[]` | Kernel-Module | `Map[<module>/<view>]=<page>#<anchor>` |
| `[IssueTrackers]` | der Tracker von Exponential | wo Fehler gemeldet werden |
| `[NavigationSettings] FolderNames[]` | eingebaute Namen | `FolderNames[<path>]=<name>`: der Name eines Ordners im Seitenbaum |
| `[ContextHelp] MenuTopics`, `MenuLinks[]` | 6, fünf Seiten | die verwandten Themen und die Schnelllinks des Hilfemenüs in der Kopfzeile |
| `[License]` | GFDL 1.3 oder später | die Lizenz der Dokumentation und ihr Hinweis; `Enabled=false` blendet ihn aus |
| `[PdfSettings] Enabled` | `true` | `false` blendet „Als PDF herunterladen“ aus und macht `exphelp/pdf` zu einem 404 |
| `[PdfSettings] PageSize` | `A4` | `A4` oder `Letter` |
| `[PdfSettings] FontFiles[]` und die anderen | DejaVu Sans, Droid Sans Fallback | TrueType-Schriften des PDFs, die erste lesbare Datei jeder Liste (siehe unten) |
| `[TitleSettings] Titles[]`, `Summaries[]`, `TitleRules[]` | etwa 50 übernommene Seiten | ein klarerer Titel und eine klarere Zusammenfassung für eine Seite, deren eigene unklar sind (siehe unten) |
| `[TreeSettings] PageOrder[]` | die Seiten der obersten Ebene | `PageOrder[<page id>]=<integer>`: der Platz einer Seite im Seitenbaum, in den Ordnerlisten und den Kategorielisten; hat Vorrang vor `order` im Front Matter (siehe „Reihenfolge der Seiten“) |

### Front Matter

Eine Seite kann ihre Zielgruppe, Kategorie und Reihenfolge in einem Front-Matter-Block an ihrem Anfang angeben:

```markdown
---
title: Publishing content
audience: editor, admin
category: content
order: 20
---
```

Ohne ihn entscheiden `[AudienceSettings] PathRules[]` und `[CategorySettings] PathRules[]`.


### Titel unklarer Seiten

Reine Textdateien haben kein Front Matter, und viele der übernommenen beginnen mit einer Zeile, die einen schlechten
Titel abgibt: ein Quellcode-Kommentar (`doc/eng-us/standards/header.txt` begann mit `//`), eine bloße Version (`5.90`),
das Wort `CHANGELOG` oder derselbe Titel wie eine andere Seite. Solche Dateien behalten ihre Namen, weil andere Dateien
auf sie verweisen; die Hilfe gibt ihnen stattdessen in `[TitleSettings]` einen klareren Titel:

```ini
[TitleSettings]
Titles[doc/eng-us/standards/header.txt]=Source file header template (inherited, 4.1)
Summaries[doc/eng-us/standards/header.txt]=The copyright and license header that every PHP file of the inherited 4.x code base started with ...
TitleRules[]=doc/changelogs/*/CHANGELOG-desc-*;Release {fileversion} summary: {title}
TitleRules[]=doc/eng-us/features/*/event.*;{title} ({dirversion})
```

`Titles[]` und `Summaries[]` werden über den Pfad relativ zum Wurzelverzeichnis der Installation angesprochen. Ein
Eintrag `TitleRules[]` ist `<pattern>;<template>` (der erste Treffer gewinnt, nur für Seiten ohne Eintrag in
`Titles[]`); die Vorlage kann `{title}` (der eigene Titel der Seite), `{name}` (ihr Dateiname), `{fileversion}` (die
Version am Ende des Dateinamens) und `{dirversion}` (das nächstgelegene Versionsverzeichnis) verwenden. Ein `title:`
oder `summary:` im Front Matter gewinnt immer. Um unklare Titel zu finden, listen Sie jede Seite mit
`./console exp:help index --list` auf und suchen nach Dateinamen, `README`, Versionen und Duplikaten.

### Reihenfolge der Seiten

Der Seitenbaum (`/exphelp/contents` und die Seitenleiste jeder Seite), die Ordnerlisten und die Kategorielisten
sortieren Seiten nach ihrer Reihenfolge, dann nach Titel. Die Reihenfolge einer Seite ist ihr `order:` im Front Matter,
sonst 0 für die `README.md` oder `index.md` eines Ordners und 1000 für jede andere Seite. Die Projektdateien
(`README.md`, `CONTRIBUTING.md` ...) und die reinen Textseiten (`release-overview`, `LICENSE-GFDL-1.3.txt`) können kein
Front Matter tragen; daher platziert `[TreeSettings] PageOrder[]` eine Seite über ihre ID und hat Vorrang vor ihrem
Front Matter:

```ini
[TreeSettings]
PageOrder[]
PageOrder[project/README.md]=0
PageOrder[INSTALL.md]=10
PageOrder[languages.md]=20
PageOrder[branch-overview.md]=30
PageOrder[release-overview]=40
PageOrder[project/SECURITY.md]=50
PageOrder[project/CONTRIBUTING.md]=100
PageOrder[project/CODE_OF_CONDUCT.md]=105
PageOrder[project/COPYRIGHT.md]=110
PageOrder[README-LICENSE.md]=120
PageOrder[LICENSE-GFDL-1.3.txt]=130
PageOrder[project/LICENSE.md]=140
PageOrder[glossary.md]=9999
```

Der Schlüssel ist die Seiten-ID: der Pfad unterhalb des Sprachverzeichnisses (`INSTALL.md`, `release-overview`),
`project/<path>` für eine Projektdatei, `ext/<extension>/<path>` für eine Seite einer Extension. Die ID ist in jeder
Sprache dieselbe, sodass ein Eintrag eine Seite und ihre Übersetzungen platziert. Die obige Liste liefert Exponential
aus, und sie ist auch der eingebaute Standard (`expHelpAudience::PAGE_ORDER`); `PageOrder[]` allein in einer
Überschreibung leert sie, und eine Überschreibung kann Einträge für ihre eigenen Seiten hinzufügen. Die Seiten der
obersten Ebene werden nur hier platziert (`glossary.md` behält ihr `order: 9999` im Front Matter, denselben Wert).
Ordner stehen weiterhin vor den Seiten ihrer Ebene. Prüfen Sie das Ergebnis unter `/exphelp/contents`.

## Hilfe aus Extensions

Eine Extension fügt Kontexthilfe in `extension/<ext>/settings/exphelp.ini.append.php` hinzu:

```ini
[ContextHelp]
Map[mymodule]=ext/myextension/usage.md
Map[mymodule/edit]=ext/myextension/usage.md#editing
```

Ihre eigene Dokumentation wird von selbst angezeigt, solange die Extension aktiv ist (nächster Abschnitt); ihre Seiten
haben die IDs `ext/<extension>/<path>`, die die Zuordnung der Kontexthilfe nennen kann.

## Dokumentation der Extensions

Das Hilfe-Center findet die Dokumentation jeder **aktiven** Extension, sobald es nachsieht
(`[IndexSettings] ActiveExtensionDocs=enabled`, der Standard; `eZExtension::activeExtensions()` der Anfrage): Eine
eingeschaltete Extension erscheint sofort, eine ausgeschaltete ist sofort verschwunden. Von jeder solchen Extension
zeigt es

- die Dateien unterhalb von `extension/<name>/doc/`, die das Repository der Extension verfolgt (ihr eigenes `.git`
  oder das der Installation, wenn die Extension Teil davon ist) oder die ihr Release in seiner `share/filelist.md5`
  aufführt;
- die Dateien oben in `extension/<name>/`, deren Namen zu `[IndexSettings] ExtensionTopFiles[]` passen: `README*`,
  `CHANGELOG*`, `LICENSE*`, `INSTALL*`, `USAGE*`, `COPYING*` (ohne Beachtung der Groß- und Kleinschreibung verglichen),
  auf dieselbe Weise verfolgt oder aufgeführt.

und nichts anderes von der Extension. Es gelten die Regeln aus „Was sie zeigt und was nie“, zusätzlich:

- nur Seitenendungen (`md`, `markdown`, `txt`, `rst`) und Bilder; PHP, Templates, Skripte, Stile, Einstellungen und
  jede andere Quelldatei werden nie angezeigt, egal wo sie liegen (`ExcludePatterns[]` enthält `*.php`, und andere
  Endungen sind keine Seitenendungen);
- eine Textdatei ohne Endung (`LICENSE`, `CHANGELOG`) wird ausgelassen, wenn sie wie ein Programm beginnt (`#!` oder
  `<?`);
- nichts von einer inaktiven Extension (es sei denn, sie ist in `ExtensionDocDirs[]` genannt), keine nicht verfolgte
  Datei, nichts außerhalb des Verzeichnisses `doc/` der Extension außer den Dateien oben, kein symbolischer Link, kein
  Pfad mit `..`;
- eine Datei aus `doc/` gewinnt gegenüber der gleichnamigen Datei oben (`doc/README.md` vor `README.md`).

Eine Extension, die ohne ihr Repository und ohne `share/filelist.md5` installiert ist (eine Kopie), zeigt nichts: Die
Hilfe kann ihre eigenen Dateien nicht von Dateien unterscheiden, die jemand dort abgelegt hat.

| | |
|---|---|
| Seiten-ID | `ext/<name>/<path below doc/>` (`ext/expai/vector.md`); eine Datei oben `ext/<name>/README.md` |
| Adresse | `/exphelp/viewer/ext/expai/vector.md`, wie jede Seite |
| Typ, Kategorie | „Extension-Dokumentation“ (`extension`), Kategorie und Navigationsordner **Extensions** |
| Zielgruppe | Redakteure, Administratoren und Entwickler, sofern das Front Matter der Seite oder die Pfadregeln nichts anderes sagen |
| Übersicht | `doc/README.md` oder `doc/index.md` der Extension, sonst ihre `README` oben |
| Links | relativ zur Datei aufgelöst, wie es GitHub tut: `../README.md` in `doc/index.md` öffnet die README oben |
| Auf GitHub bearbeiten | keiner: `EditBaseURL` nennt das Repository der Installation, nicht das der Extension |
| Brotkrumennavigation | Hilfe > Extensions > die Übersicht der Extension (oder ihr Index) > ... > die Seite |

**Der Extensions-Index** (`/exphelp/extensions`) listet jede Extension auf, die Seiten hat, mit dem Titel ihrer
Übersicht und der Anzahl ihrer Seiten; `/exphelp/extensions/<name>` ist der Index einer Extension: ihre Seiten nach
Ordnern und ein Suchfeld, das auf sie beschränkt ist. Die **Suche** nimmt `(extension)/<name>` als Ansichtsparameter
(`/exphelp/search/(q)/vector/(extension)/expai`, im Formular als verstecktes Feld und in der JSON-Anfrage als
`extension=expai`); ihre Seite sagt, auf welche Extension sie beschränkt ist, und verlinkt auf die gesamte Hilfe. Die
Inhaltsseite verlinkt auf den Index, und das Dashboard zeigt die Kategorie **Extensions** zusammen mit den anderen.

Eine Extension, die eine eigene Dokumentationsansicht hatte, kann ihre alten Adressen dorthin umleiten; expai tut das
(`expai/help` leitet auf `exphelp/extensions/expai` weiter, `expai/help/(page)/<name>` auf die Seite).

Tests: `tests/tests/kernel/classes/help/expHelpExtensionDocsTest.php` (ohne Datenbank: eine Fixture-Installation mit
Extensions in eigenen Repositorys und im Repository der Installation, aktiv und inaktiv; Quelldateien, Einstellungen,
Skripte ohne Endung, nicht verfolgte Dateien und Pfadüberschreitung werden abgelehnt; IDs, Übersicht, Liste,
Suchfilter, Brotkrumennavigation).

## Projektdateien

Die Markdown-Dateien des Repositorys, die das Projekt selbst dokumentieren, außerhalb von `doc/`, sind ebenfalls
Hilfeseiten: `README.md`, `SECURITY.md`, `CONTRIBUTING.md`, `COPYRIGHT.md`, `LICENSE.md` im Wurzelverzeichnis (und
`INSTALL.md`, `CHANGELOG.md` oder jede andere `*.md` im Wurzelverzeichnis, sobald eine committet ist) sowie die
`README.md`-Dateien von `bin/`, `design/<design>/` und `kernel/`, falls vorhanden. Sie stehen in der Kategorie
**Projektdateien** (`project-files`, Typ `project`) in der Suche (Filter nach Kategorie oder Typ) und auf dem
Dashboard. Im Seitenbaum der Inhaltsseite (`/exphelp/contents`) und jeder Hilfeseite stehen sie in keinem Ordner: Jede
ist eine Seite an der Spitze des Baums, neben den Hauptordnern, und die Brotkrumennavigation einer solchen Seite ist
Hilfe > die Seite. Ihre Zielgruppen sind Entwickler und Administratoren, bei einigen mehr (`[ProjectFiles]
AudienceRules[]`: `README.md` ist auch für Redakteure, Benutzer, Partner und Hosting-Unternehmen); der Titel ist die
erste Überschrift. Im Baum erhält ein Titel, der nicht mehr als „README“ sagt, das Verzeichnis vorangestellt
(`kernel/private README`), sodass keine zwei Einträge gleich lauten.

- **IDs**: `project/<path in the repository>`: `/exphelp/viewer/project/README.md`,
  `/exphelp/viewer/project/bin/php/README.md`. Eine Adresse mit dem Pfad allein (`/exphelp/viewer/README.md`) leitet
  auf die ID weiter. Das Präfix `project/` ist für sie reserviert.
- **Links**: Ein Link einer Dokumentationsseite auf eine Projektdatei (`[README](../../../../README.md)` aus
  `doc/eng-us/features/6.0/`) öffnet sie in der Hilfe, nicht auf GitHub; ein Link aus `README.md` in die Dokumentation
  (`doc/eng-us/guides/caches.md`) öffnet die Hilfeseite. Links werden relativ zur Datei aufgelöst, wie GitHub sie
  auflöst, sodass dasselbe Markdown an beiden Orten funktioniert.
- **Sprache**: Die Projektdateien gibt es nur auf Englisch, und sie werden nicht übersetzt, wie `doc/changelogs/`. Ein
  Leser einer anderen Sprache sieht sie auf Englisch mit dem Hinweis, dass die Seite nicht übersetzt ist.
- **Lizenz**: Sie sind Teil der Software und werden unter deren Lizenz verbreitet (`[ProjectFiles] LicenseName`,
  verlinkt auf `LicensePage`), nicht unter der Lizenz der Dokumentation; der Hinweis darunter sagt das.
- **Ausgelassen** (`[ProjectFiles] Exclude[]`): Test-Fixtures (`**/fixtures/**`) und die Kopien anderer Projekte
  (`**/javascript/**`, die README einer mitgelieferten JavaScript-Bibliothek wie TinyMCE).

Was nie angezeigt werden kann, egal was `Include[]` sagt, auch nicht in einer Überschreibung:

- eine Datei, die git nicht verfolgt: Die Liste stammt aus dem git-Index, `git ls-files` oder dem Release-Manifest,
  nie aus dem Verzeichnis, sodass eine private Notiz im Wurzelverzeichnis, die nicht committet ist (eine nicht
  verfolgte `notes.md`), nie angezeigt, durchsucht, verlinkt oder als Weiterleitungsziel verwendet wird;
- eine verfolgte Datei an einem Ort, den `.gitignore` oder `.git/info/exclude` ignoriert (mit `git add -f`
  hinzugefügt);
- alles außer Markdown (`.md`, `.markdown`) und jeder Pfad unterhalb von `doc/` (die Dokumentation selbst), `var/`,
  `vendor/`, `settings/override/`, `settings/siteaccess/` oder `node_modules/`;
- eine versteckte Datei oder eine Datei in einem versteckten Verzeichnis, einschließlich `.github/` (die Vorlage für
  Pull Requests ist keine Seite);
- alles unterhalb von `extension/`: Die Dokumentation der Extensions ist die Kategorie **Extensions**
  (`exphelp/extensions`), keine Projektdateien;
- alles unterhalb eines Verzeichnisses `tests/`, wo auch immer es liegt (`tests/`, `kernel/classes/tests/`,
  `extension/ezoe/tests/tinymce8/`): Tests und ihre Fixtures sind keine Dokumentation;
- symbolische Links, Dateien außerhalb des Wurzelverzeichnisses der Installation und Dateien über `MaxFileSize`, wie
  bei jeder Hilfedatei.

```ini
[ProjectFiles]
Enabled=enabled
Include[]
Include[]=*.md
Include[]=bin/**/README.md
Exclude[]
Exclude[]=**/fixtures/**
```

`*` bleibt innerhalb eines Verzeichnisses, und `**` überschreitet Verzeichnisse. `Enabled=disabled` nimmt sie aus der
Hilfe heraus. Nach einer Änderung baut `./console exp:help index --rebuild` den Index und die Suche neu auf (ein
Commit, der eine Datei hinzufügt, tut das von selbst, weil sich der git-Index ändert).
`tests/tests/kernel/classes/help/expHelpProjectFilesTest.php` deckt das ab (ohne Datenbank), unter anderem, dass eine
nicht verfolgte Notiz im Wurzelverzeichnis nie indiziert wird.

## Externe Bilder

Eine Seite kann ein Bild einer anderen Website zeigen: `README.md` bettet seine Screenshots von
`github.com/user-attachments` ein, als `![alt](https://...)` oder
`<img width="1200" height="693" alt="..." src="https://...">`. Die Content Security Policy der Administration erlaubt
Bilder nur von `'self'` (`img-src 'self' data: blob:`), deshalb zeigt die Hilfe eine **lokale Kopie**, und die Policy
bleibt, wie sie ist:

- **Beim Indizieren** findet `./console exp:help index` jedes `https://`-Bild, auf das die Seiten verweisen (in jeder
  Sprache), und speichert eine Kopie als `var/<site>/cache/exphelp/images/<id>.<suffix>`, wobei `<id>` der SHA-256 der
  Adresse ist (`expHelpRemoteImages`). Ein einmal gespeichertes Bild wird nicht erneut abgerufen. `--no-images`
  überspringt diesen Schritt; der Bericht listet jedes Bild auf, das nicht gespeichert werden konnte, und den Grund.
- **Das Abrufen ist vorsichtig**: nur `https://`, auch für jede Weiterleitung (höchstens 5; GitHub leitet
  user-attachments an eine signierte Speicheradresse weiter); nie ein Host mit einer privaten, Loopback-,
  Link-Local- oder reservierten Adresse (die Adresse wird einmal aufgelöst und die Verbindung an sie gebunden);
  höchstens 5 MB; 15 Sekunden Zeitlimit; nur PNG, JPEG, GIF und WebP, und nur, wenn die Bytes den Typ haben, den der
  Server nennt. SVG wird nie gespeichert. Während eine Seite angezeigt wird, wird nichts abgerufen.
- **Die Dateien** werden atomar mit `site.ini [FileSettings] StorageFilePermissions` geschrieben, wie der Rest des
  Hilfe-Caches, sodass PHP-FPM (der Benutzer der Website) und Velocity (root) sie beide lesen können, gleich wer den
  Index gebaut hat.
- **Ausliefern**: `exphelp/image/(id)/<sha256>` (Richtlinie `exphelp/read`) sendet die Kopie mit ihrem Inhaltstyp,
  `X-Content-Type-Options: nosniff`, `Content-Security-Policy: default-src 'none'` und
  `Cache-Control: private, max-age=31536000, immutable`. Die ID ist ein View-Parameter, nie ein GET-Parameter; alles
  andere (eine andere Form, ein weiterer Pfad, eine ID ohne Kopie) ergibt einen 404.
- **In der Seite** ist das Bild ein `<img>` mit dieser lokalen Adresse. `width`, `height` und `alt` eines `<img>`-Tags
  bleiben als Attribute erhalten (nie ein Inline-Style); ein Markdown-Bild erhält die Größe der Kopie. Ein Bild ohne
  Kopie bleibt der beschriftete Link „Bild auf <host>: <alt>“ (in einem neuen Tab geöffnet), sodass die Seite nie
  eine Anfrage stellt, die die Policy blockiert. Ein Host, den die Policy selbst erlaubt (`Directives[img-src]`,
  `AdditionalSources[]`), wird ebenfalls aus seiner Kopie gezeigt, wenn es eine gibt.
- Die dargestellten Seiten werden mit dem Zustand des Bildverzeichnisses im Schlüssel zwischengespeichert, sodass
  eine Seite ihre Bilder nach dem nächsten `exp:help index` zeigt, ohne dass ein Cache geleert werden muss.

`tests/tests/kernel/classes/help/expHelpRemoteImagesTest.php` deckt das ab (ohne Datenbank, ohne Netzwerk: ein
nachgebildeter Abrufer).

## Einstiegsseiten der Zielgruppen

Jede Zielgruppe hat ihre eigene Einstiegsseite, `exphelp/audience/<audience>` (zum Beispiel
`exphelp/audience/developer`). Der Titel jeder Zielgruppenkarte auf dem Dashboard öffnet sie. Die Seite besteht aus
Blöcken, in der Reihenfolge, die `[LandingSettings] Blocks[]` vorgibt, oder `[Landing_<audience>] Blocks[]` für eine
Zielgruppe:

| Block | Was er zeigt |
|---|---|
| `hero` | Den Namen der Zielgruppe, eine Zusammenfassung, die Anzahl ihrer Seiten, ein auf die Zielgruppe beschränktes Suchfeld und Schaltflächen: **Hier beginnen**, **Alle Seiten für ...** und **Alle Zielgruppen** |
| `start` | Den Lesepfad: `ReadingPath[]`, sonst die Startseite und die Hilfeseiten der Zielgruppe in ihrer Reihenfolge |
| `boxes` | Infoboxen: Titel und Zusammenfassung von `InfoPages[]`, sonst der nächsten Seiten, die eine Zusammenfassung haben |
| `categories` | Eine Suchschaltfläche für jede Kategorie mit Seiten für die Zielgruppe (`Categories[]` schränkt sie ein); jede öffnet `exphelp/search/(category)/<id>/(audience)/<audience>` |
| `key` | Wichtige Seiten: `KeyPages[]`, sonst die nächsten Seiten in der Reihenfolge |
| `latest` | Kürzlich aktualisierte Seiten der Zielgruppe, aus der git-Historie |
| `related` | Die Einstiegsseiten der Zielgruppen in `Related[]` |
| `commits` | Die neuesten Commits des Projekts, seitenweise (Entwickler) |
| `support` | Der Block Exponential unterstützen (Unterstützer und Sponsoren) |

Eine Seite, die eine Liste nennt, die aber nicht in der Hilfe ist (noch nicht geschrieben, versteckt), wird
ausgelassen, sodass eine Einstiegsseite nie auf eine fehlende Seite verlinkt. Jede Liste stammt aus dem gecachten
Hilfeindex; nichts anderes wird gelesen, um die Seite aufzubauen. `[LandingSettings] Enabled=disabled` schaltet die
Seiten ab; die Karten öffnen dann wie zuvor die Suche.

### Das Commit-Protokoll

Die Einstiegsseite der Entwickler listet die neuesten Commits von `se7enxweb/exponential` auf
(`[CommitFeedSettings]`). Der Server liest den Atom-Feed des Repositorys (`FeedURL`), nie der Browser, sodass die
Content Security Policy des Admins erhalten bleibt und keine Besucheradresse GitHub erreicht. Er liest höchstens einmal
alle `CacheTTL` Sekunden (600), mit einem Timeout von `Timeout` Sekunden (4) und jeweils nur in einem Prozess. Der
Feed enthält die neuesten 20 Commits; jeder Lesevorgang wird der gecachten Liste hinzugefügt (höchstens `MaxCommits`,
500), die die Seite mit dem gemeinsamen Pager zu je `PageSize` (10) anzeigt. Merge-Commits sind ausgeblendet, sofern
nicht **Auch Merge-Commits anzeigen** gewählt ist (`/(merges)/1`). Wenn GitHub nicht erreichbar ist, behält die Seite
die gecachten Commits und sagt, wann sie gelesen wurden; ein fehlgeschlagener Lesevorgang wird nach `RetryAfter`
Sekunden (120) wiederholt. Titel, Nachrichten und Autoren werden als maskierter Text angezeigt, die Links werden aus dem
Hash jedes Commits gebildet (`CommitBaseURL`), und es werden weder Avatare noch andere entfernte Bilder angezeigt.

### Exponential unterstützen

Der untere Teil des Dashboards und die Einstiegsseiten der Unterstützer und Sponsoren bitten die Leser, die Arbeit mit
einem monatlichen Sponsoring über GitHub Sponsors zu finanzieren, für 7x (<https://github.com/sponsors/se7enxweb>) und
für die Exponential Foundation (<https://github.com/sponsors/exponentialcms>), und sagen, wofür die Mittel der
jeweiligen Organisation verwendet werden. `[SupportSettings]` enthält den Titel, den Text und die Reihenfolge der
Organisationen; die Gruppe `[SupportOrganization_<id>]` jeder Organisation enthält ihren Namen, ihre Sponsorenadresse
(nur https), den Schaltflächentext, ihre Seite in der Hilfe, ihre Mission und `Funds[]`. `Enabled=disabled` entfernt
den Block.

## Eine Seite finden

- **Alle Seiten** (`/exphelp/contents`): jede Hilfeseite in einem Baum, die Ordner geschlossen (jeder zeigt seinen
  Titel und die Anzahl seiner Seiten), mit einem Filterfeld, das den Baum auf die Seiten eingrenzt, deren Titel oder
  Ordner jedes eingegebene Wort enthält, und deren Ordner öffnet; geleert (mit **Filter leeren** oder Esc) stellt es die
  Ordner wieder her, wie sie waren. **Alle aufklappen** und **Alle zuklappen** öffnen oder schließen jeden Ordner; sie
  sitzen in einem kleinen Bedienfeld am Ende der Seite, das am unteren Fensterrand auf der Endseite der Hilfespalte
  haftet (der linken Seite in einer Sprache, die von rechts nach links geschrieben wird), sodass sie beim Scrollen
  griffbereit bleiben und nie über den Seitenmenüs liegen, und erscheinen nur mit JavaScript. Der Filtertext und die
  Ordner, die der Leser geöffnet oder geschlossen hat (über ihren Pfad, `data-nav-path`), werden im `localStorage` des
  Browsers gespeichert (Schlüssel `exphelp-nav`), sodass die Inhaltsseite und die Navigation an der Seite jeder
  Hilfeseite von Seite zu Seite denselben Filter und dieselben Ordner zeigen; die Ordner der angezeigten Seite sind
  immer offen, und ohne Speicher (blockiert oder privates Surfen) funktioniert alles weiterhin für die angezeigte
  Seite.
  Das Bedienfeld bleibt über der Debug-Leiste und über deren minimiertem Reiter, über die Custom Property
  `--exp-debug-clearance`, die `expdebugbar.css` setzt (der Platz, den die Leiste am unteren Fensterrand freihält; ohne
  die Leiste nicht gesetzt); ein anderes fixiertes Bedienelement am unteren Rand einer Seite kann sie auf dieselbe Weise
  verwenden: `bottom: calc(var(--exp-debug-clearance, 0px) + <gap>)`. Jeder Ordner hat eine ID aus seinem Pfad, sodass
  `/exphelp/contents#exphelp-folder-help-editors` den Ordner `doc/eng-us/help/editors` öffnet. Der Viewer zeigt
  denselben Baum an der Seite jeder Seite, mit geöffneten Ordnern auf dem Weg zur Seite. Ordner sind native
  Aufklappelemente, sodass der Baum ohne JavaScript funktioniert; die `README.md` oder `index.md` eines Ordners ist
  sein Link „Übersicht“. Ein Ordner wird über `exphelp.ini [NavigationSettings] FolderNames[<path>]` benannt, sonst
  über den Titel seiner README, sonst über seinen Verzeichnisnamen. Der Baum wird pro Index, Siteaccess und Sprache
  gecacht, sodass eine Seite mit 1 200 Einträgen einen einzigen Dateizugriff kostet.
  Ordner, die in `[NavigationSettings] FoldedFolders[]` aufgeführt sind (ausgeliefert: die generierten
  Referenzbeispiele unterhalb von `doc/eng-us/examples/`), zeigen nur ihren Link „Übersicht“ und die Seitenzahl; ihre
  Seiten und die der Ordner darunter werden aufgeführt, solange eine Seite darin geöffnet ist. Ohne das Einklappen
  würden die 2 400 generierten Seiten etwa 2 400 zusätzliche Links auf jede Seite der Hilfe setzen.
- **Adressen für Suche und Stöbern** verwenden Ansichtsparameter, sodass sie als Lesezeichen gespeichert und verlinkt
  werden können: `/exphelp/search/(q)/publish`, `/exphelp/search/(category)/layouts/(audience)/editor`, ebenso mit
  `(type)`, `(extension)` (die Dokumentation einer Extension), `(offset)` und `(limit)`. Ohne `(q)` listet die Seite jede Seite auf, die die Filter zulassen (Stöbermodus);
  ganz ohne Parameter listet sie alle Seiten auf. Die Zielgruppenkarten, Themen und Chips des Dashboards, Kategorie und
  Zielgruppe einer Seite und die Suchformulare verlinken alle auf diese Adressen. Jede Option der Hilfe ist ein
  Ansichtsparameter (`/(name)/value`), nie ein GET-Query-Parameter: `?q=`, `?format=`, `?lang=` und dergleichen werden
  nicht gelesen, ohne Rückfallebene und ohne Weiterleitung. Das Suchformular ohne JavaScript wird per POST gesendet
  und (303) auf die kanonische Adresse weitergeleitet; die JSON-Antwort für die Sofortsuche (`/(format)/json` oder
  `Accept: application/json`) wird nie weitergeleitet, und das Hilfeskript fordert sie als
  `/exphelp/search/(q)/<words>/(category)/<id>/(limit)/20/(format)/json` an. Die anderen Optionen funktionieren genauso:
  `/exphelp/viewer/<page id>/(lang)/eng-us`, `/exphelp/manual/<remote_id>/(format)/pdf`,
  `/exphelp/report/(tracker)/<key>`.
- **Hilfemenü**: Das „?“ in der Kopfzeile von admin4 und admin4l (und des klassischen Admins) öffnet ein Menü mit einem
  Suchfeld, „Hilfe zu dieser Seite“, bis zu `[ContextHelp] MenuTopics` verwandten Themen, den Links aus
  `[ContextHelp] MenuLinks[]` und „Hilfe-Center öffnen“. Ohne JavaScript ist das „?“ ein Link auf die Hilfe zur
  aktuellen Seite. Das Hilfe-Center ist außerdem über das Benutzermenü, die Fußzeile, das Dashboard und die Übersicht
  von Setup verlinkt.

## Datensatzseiten

Die Release-Historie (`doc/eng-us/release-overview`) und jede andere reine Textliste von Versionsdatensätzen werden von
`expHelpRecordRenderer` als Tabellen pro Serie angezeigt: Version, Name, Datum, Revisionen, Adressen und Anmerkungen,
mit einem Filterfeld über alle Zeilen und Sortierschaltflächen in den Spaltenköpfen (ohne das Skript sind die Tabellen
vollständig und nach Datum geordnet). Jede reine Textdatei, die in derselben Form „Namenszeile, dann Zeilen
`- Key=value`“ geschrieben ist, wird auf dieselbe Weise angezeigt. Adressen auf den alten SVN-Hosts werden als Text mit
der Kennzeichnung „historisch“ angezeigt, nie als Links. Diese Seiten erscheinen in der Suche wie die Seiten der alten
Versionen, die sie beschreiben.

## Die Lizenz der Dokumentation

Die Dokumentation in `doc/` ist freie Dokumentation unter der GNU Free Documentation License 1.3 oder später
(`doc/eng-us/LICENSE-GFDL-1.3.txt`, `doc/eng-us/README-LICENSE.md` und für Leser `doc/eng-us/help/free-documentation.md`).
Jede Hilfeseite, ihre Druckansicht und ihr PDF enden mit dem Lizenzhinweis, und das Dashboard hat einen Block „Freie
Dokumentation“. Das gilt für die gesamte Dokumentation von 6.x, einschließlich der über Pull Requests beigetragenen
Seiten; die aus früheren Versionen übernommenen Seiten behalten ihren eigenen Hinweis
(`exphelp.ini [License] OtherLicensePathRules[]`). `[License] Enabled=false` blendet die Hinweise aus.

## Die Partnerkarte

Fast jede Hilfeseite endet mit dem Absatz **Werden Sie Exponential-Partner** (englisch **Become an Exponential
Partner**), geschrieben zwischen zwei Markierungen, die GitHub nicht anzeigt:

```markdown
<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, ... Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
```

Der Viewer zeigt ihn als Karte wie die Blöcke am Ende des Hilfe-Dashboards: das Partnersymbol auf einer Kachel, den
fett gesetzten Anfang als Überschrift, den Text und die Links seines letzten Satzes als Schaltflächen (die erste
hervorgehoben), nach denselben Regeln wie jeder andere Link. Das macht `expHelpMarkdownRenderer` mit seiner Option
`partner_card`, die der Viewer setzt (`section.a4-help-partner`, gestaltet in `stylesheets/modules/help.css` von admin
und admin4 mit den gemeinsamen Tokens, hell und dunkel). Erkannt wird nichts an seinen Worten, deshalb funktioniert
jede Sprache gleich: Eine Übersetzung behält die Markierungen um ihren eigenen Text. Ein markierter Block, der nicht
ein Absatz mit fettem Anfang ist oder keine Endmarkierung hat, wird als der Absatz gezeigt, der er ist. Das PDF druckt
die Karte als Kasten mit den Links in einer Zeile; die Suche, die DITA- und Inhaltsexporte und alle anderen Ausgaben
behalten den einfachen Absatz.

## Verwandt

- `doc/eng-us/help/develop-with-git.md`: Arbeiten mit einem Git-Klon samt seiner Historie, und der Weg vom Benutzer zum Entwickler.
- `doc/eng-us/specifications/6.0/help-system.md` und `doc/eng-us/specifications/6.0/help-style-guide.md`: die
  Spezifikation der Hilfe als Inhalt (im Inhaltsbaum bearbeitete Hilfeseiten) und der Styleguide für das Schreiben von Hilfeseiten.
## Drucken und PDF

Jede Hilfeseite hat unter ihren Eckdaten zwei Aktionen:

- **Diese Seite drucken** öffnet den Druckdialog des Browsers (die Schaltfläche wird vom Skript der Hilfe angezeigt;
  ohne JavaScript tut der Druckbefehl des Browsers dasselbe). Auf Papier wird nur das Dokument gedruckt: keine Menüs,
  kein Baum, kein Inhaltsverzeichnis und keine Schaltflächen, helle Farben auch bei dunkler Administrationsoberfläche,
  die Quelldatei der Seite, ihre letzte Änderung und Adresse unter dem Titel, die Adresse jedes externen Links hinter
  dem Link und kein Umbruch innerhalb eines Codeblocks, einer Tabelle oder eines Hinweiskastens.
- **Als PDF herunterladen** (`/exphelp/pdf/<page>`, Richtlinie `exphelp/read`) sendet die Seite als PDF-Datei, die nach
  ihrem Titel benannt ist. Sie wird auf dem Server von der eigenen PDF-Bibliothek des Kernels (`lib/ezpdf`) erzeugt;
  kein externer Dienst und kein externes Programm wird verwendet. Überschriften (mit funktionierenden Links darauf),
  Absätze, Listen, Tabellen, Codeblöcke (Festbreitenschrift), Hinweiskästen, Zitate, Bilder aus `doc/` (PNG, JPEG,
  GIF, WebP) und Fußnoten bleiben erhalten; der Lizenzhinweis der Seite beschließt die Datei; jede Seite trägt den Titel
  und „Seite n von N“ in ihrer Fußzeile. Nur die Seiten, die der Viewer anzeigt, können heruntergeladen werden; alles
  andere ergibt einen 404.

Der Text des PDFs ist so gut wie seine Schriften. Mit einer TrueType-Schrift (`[PdfSettings] FontFiles[]`,
`BoldFontFiles[]`, `ItalicFontFiles[]`, `BoldItalicFontFiles[]`, `MonoFontFiles[]`; standardmäßig DejaVu Sans des
Betriebssystems) wird jedes Zeichen gezeichnet, das die Schrift enthält, und der Text kann markiert und durchsucht
werden; nur die verwendeten Zeichen werden eingebettet, sodass eine Seite einige Dutzend Kilobyte groß ist.
`FallbackFontFiles[]` (Droid Sans Fallback) übernimmt die Zeichen, die der Hauptschrift fehlen, etwa Chinesisch,
Japanisch und Koreanisch. Text, der von rechts nach links läuft (Hebräisch, Arabisch), wird von rechts nach links
gedruckt; arabische Buchstaben werden in ihren Grundformen gezeichnet, ohne Verbindung. Ohne jede TrueType-Schrift
werden die PDF-Kernschriften verwendet, und Zeichen außerhalb des westeuropäischen Bereichs werden durch die
nächstliegenden lateinischen Buchstaben ersetzt.

PDFs werden in `var/<site>/cache/exphelp/pdf/` gecacht, pro Seite, Indexsignatur, Adresse und Schriften: Eine Änderung
an der Seite oder den Einstellungen erzeugt eine neue Datei.

## Auf der Kommandozeile

```bash
./console exp:help index     # rebuild the help index and the search index
./console exp:help search "publish a page"
./console exp:help check     # broken links and images in doc/
./console exp:help pdf help/searching.md --out=searching.pdf --base-url=https://www.example.com
```

Die Beispiele unterhalb von `doc/eng-us/examples/`, die aus dem Code generiert werden (Template-Operatoren,
Fetch-Funktionen, Modulansichten, Richtlinien, INI-Einstellungen, Datentypen, Workflow-Ereignisse, Cronjobs,
Konsolenbefehle, Erweiterungspunkte), schreibt `./console exp:help:examples`; `--check` meldet Seiten, die nicht
aktuell sind. Siehe [Beispiele](../../beispiele/README.md#so-entstehen-die-generierten-seiten). Sie haben den Seitentyp
`generated`, der unter den geschriebenen Seiten eingestuft wird (`[SearchSettings] TypeBoost[generated]`) und nicht auf
den Zielgruppenkarten des Dashboards erscheint.

Der Index wird in `var/<site>/cache/exphelp/` gecacht und von selbst neu aufgebaut, wenn sich die verfolgten Dateien
ändern. Ein Neuaufbau entfernt auch, was der ältere Index hinterlassen hat: die anderen Dateien
`index-<signature>.php` und die gecachten Navigationsbäume (`nav/`) und gerenderten Seiten (`render/`), die vor ihm
geschrieben wurden, sodass der Cache nur das enthält, was der aktuelle Index verwendet.

## Sprachen und Barrierefreiheit

Die Beschriftungen der Hilfeseiten (Dashboard, Suche, Seiten-Viewer, Fehlermeldung, die Schaltfläche Hilfe) stehen im
Kontext `kernel/exphelp` der Übersetzungen der Administrationsoberfläche, und jeder vollständige Katalog enthält sie:
Deutsch, Französisch, Spanisch (Spanien und Mexiko), Ukrainisch, Hebräisch, Arabisch, Latein und Klingonisch, wobei
Englisch und die Vorlage `untranslated` synchron gehalten werden. Die Namen in `exphelp.ini` (`[CategorySettings]
CategoryNames[]`, `Name` und `Description` der Tracker) laufen über denselben Kontext, sodass ein neuer Name dort eine
Meldung in jedem dieser Kataloge braucht; `tests/tests/lib/ezi18n/expTranslationQualityTest.php` prüft, dass eine
fertige Übersetzung die Platzhalter (`%count`, `%title` ...) ihrer Quelle beibehält. Die Diagnosedaten und der
Issue-Text, den die Fehlermeldung vorbereitet, bleiben absichtlich auf Englisch, weil die Maintainer sie lesen.

### Die Seiten in der Sprache des Lesers

Die Hilfeseiten selbst sind die Dokumentation, ein Verzeichnis pro Sprache unterhalb von `doc/`, benannt nach dem
Locale-Code von Exponential in Kleinbuchstaben: `doc/eng-us/` (`eng-US`) ist das Original, eine Übersetzung ist
`doc/<locale>/` (`doc/ger-de/` für `ger-DE`; eine künftige französische `doc/fre-fr/`), und `doc/changelogs/` wird
nicht übersetzt (`expHelpLanguage`; [Übersetzte Dokumentation](../../bc/6.0/uebersetzte-dokumentation.md)). Die Sprache
des Lesers ist die Sprache der Oberfläche (`site.ini [RegionalSettings] Locale`, die die Sprachumschaltung setzt): ein
Alias aus `exphelp.ini [DocLanguages] Map[]` (`eng-GB` auf `eng-us`, `ger-AT` und `ger-CH` auf `ger-de`), sonst das
Locale in Kleinbuchstaben, wenn `Directories[]` es aufführt, beides versucht für das Locale und für das Locale ohne
seine Variante; sonst das erste Verzeichnis seiner dreibuchstabigen Sprache (jedes andere `ger-*` liest `ger-de`),
sonst `Default=eng-us` (`expHelpLanguage::forLocale()`). Eine Seiten-ID ist in jeder Sprache der Pfad unterhalb von
`doc/eng-us/`, sodass eine Adresse in jeder Sprache dieselbe Seite zeigt; eine Übersetzung kann ihre Dateinamen
übersetzen und sie in `doc/<locale>/.paths.json` aufführen. Eine Seite, die die Sprache noch nicht hat, wird auf
Englisch mit einem Hinweis angezeigt („Diese Seite ist noch nicht ins Deutsche übersetzt“); Index, Suche, Navigation
und gerenderte Seiten werden pro Sprache gehalten (`lang-<directory>/` im Hilfe-Cache, `lang-ger-de/`), und
`./console exp:help index` baut sie alle auf. Eine Adresse, die eine Datei nennt
(`/exphelp/viewer/doc/eng-us/guides/caches.md`, `/exphelp/viewer/ger-de/<translated path>`), wird auf die Seiten-ID
weitergeleitet, ebenso die Namen aus der Zeit vor der Umbenennung auf die Locale-Codes
(`/exphelp/viewer/en/us/guides/caches.md`, `/exphelp/viewer/doc/en/us/...`, `/exphelp/viewer/de/<translated path>`),
die `[DocLanguages] Legacy[]` auf die heutigen Verzeichnisse abbildet (`en/us` auf `eng-us`, `de` auf `ger-de`; nur
für Adressen, aus einem alten Verzeichnis wird keine Datei gelesen).

Die Startseite und jede Seite zeigen die Sprache des Lesers neben der Suche: „Lesesprache: English / Deutsch“
(`exphelp/parts/reading_language.tpl`, `expHelp::reading()`). Sie bietet jede Sprache aus `[DocLanguages]
Directories[]` an, die Seiten hat und die die Sprachumschaltung der Oberfläche für den Benutzer wählen kann; eine
Schaltfläche sendet an `uiswitch/set` wie die Sprachumschaltung der oberen Leiste, ändert also die Oberflächensprache
der gesamten Administrationsoberfläche, und kehrt zur selben Seite zurück. Darunter steht, wie viel der Hilfe jede
Übersetzung hat („Deutsch: 120 von 7.265 Seiten“, `expHelpDocumentIndex::languageCoverage()`, aus der Dateiliste
gezählt, ohne eine Seite zu lesen). Eine übersetzte Seite verlinkt auf ihr englisches Original, ebenso der Hinweis
einer noch nicht übersetzten Seite: Das Original ist dieselbe Adresse mit dem Ansichtsparameter `/(lang)/eng-us` (der
Verzeichnisname; die alten Schlüssel `en-us` und `de` werden weiterhin akzeptiert; ein Query-Parameter `?lang=` wird nicht gelesen), der die Seite für diese eine Anfrage in jener Sprache zeigt, mit einem Link zurück zur Sprache des Lesers.

In einer Oberflächensprache, die von rechts nach links geschrieben wird (Hebräisch, Arabisch), werden die Seiten
gespiegelt: Navigationsbaum, Inhaltsverzeichnis und die Links für vorherige/nächste Seite wechseln die Seite und ihre
Pfeile drehen sich, während Pfade, Code und die Diagnosedaten von links nach rechts bleiben. Jede Seite hat genau ein
`h1`, benannte Navigations-Landmarks, Beschriftungen für jedes Suchfeld und jeden Filter, einen sichtbaren Fokusring
auf allem, was die Tastatur bedient, und einen Hinweis bei jedem Link, der ein neues Fenster öffnet; `/` springt zur
Hilfesuche, und die Sofortergebnisse werden Screenreadern angesagt.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
