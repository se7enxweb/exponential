# Übersetzte Dokumentation: doc/eng-us/ und ein Verzeichnis pro Sprache (6.0.15)

Die Dokumentation von Exponential ist am 20. Oktober 2026 von `doc/` in ein Verzeichnis pro Sprache umgezogen, damit
sie übersetzt werden kann. Jede Sprache hat unter `doc/` ein eigenes Verzeichnis mit denselben relativen Pfaden,
benannt nach ihrem Locale-Code von Exponential in Kleinbuchstaben: `doc/eng-us/` ist das Original (amerikanisches
Englisch, Locale `eng-US`), und eine Übersetzung liegt in `doc/<locale>/`, zum Beispiel `doc/ger-de/` für Deutsch
(`ger-DE`). Die Changelogs bleiben in `doc/changelogs/`: Sie werden nicht übersetzt.

Die Hilfe der Administration (Hilfe in der oberen Leiste) liest die Sprache der Oberfläche und zeigt jede Seite darin
an, und dort, wo es noch keine Übersetzung gibt, die englische Seite mit einem Hinweis, der das sagt.

## Inhalt

1. [Was umgezogen ist](#was-umgezogen-ist)
2. [Die Verzeichnisnamen: der Locale-Code in Kleinbuchstaben](#die-verzeichnisnamen-der-locale-code-in-kleinbuchstaben)
3. [Was in doc/ bleibt](#was-in-doc-bleibt)
4. [Was zu ändern ist](#was-zu-ändern-ist)
5. [Upgrade einer Website, die auf doc/en/us oder doc/de verweist](#upgrade-einer-website-die-auf-docenus-oder-docde-verweist)
6. [Die Hilfe und die Sprache des Lesers](#die-hilfe-und-die-sprache-des-lesers)
7. [Seiten-IDs und Adressen](#seiten-ids-und-adressen)
8. [Eine Übersetzung hinzufügen](#eine-übersetzung-hinzufügen)
9. [Übersetzte Dateinamen: .paths.json](#übersetzte-dateinamen-pathsjson)
10. [Einstellungen](#einstellungen)
11. [Die Kommandozeile](#die-kommandozeile)
12. [Mehrere Übersetzer: .paths.json und .dirnames.json zusammenführen](#mehrere-übersetzer-pathsjson-und-dirnamesjson-zusammenführen)
13. [Tests](#tests)

## Was umgezogen ist

Jede versionierte Datei unter `doc/` ist mit demselben relativen Pfad nach `doc/eng-us/` umgezogen, mit `git mv`,
sodass die Geschichte jeder Datei ihr folgt (`git log --follow doc/eng-us/guides/caches.md`):

| Vorher | Nachher |
|--------|-------|
| `doc/INSTALL.md` | `doc/eng-us/INSTALL.md` |
| `doc/guides/...`, `doc/features/...`, `doc/bc/...` | `doc/eng-us/guides/...`, `doc/eng-us/features/...`, `doc/eng-us/bc/...` |
| `doc/help/...`, `doc/install/...`, `doc/examples/...`, `doc/standards/...` | dasselbe unter `doc/eng-us/` |
| `doc/history/...`, `doc/specifications/...`, `doc/shop/...`, `doc/doxygen/...` | dasselbe unter `doc/eng-us/` |
| `doc/glossary.md`, `doc/languages.md`, `doc/branch-overview.md`, `doc/release-overview` | dasselbe unter `doc/eng-us/` |
| `doc/README-LICENSE.md`, `doc/LICENSE-GFDL-1.3.txt` | dasselbe unter `doc/eng-us/` |
| `doc/changelogs/...` | unverändert |

Der Umzug geschah am selben Tag in zwei Schritten, beide vor dem Release von 6.0.15: zuerst nach `doc/en/us/` (mit der
deutschen Übersetzung in `doc/de/`), dann, in #650, zu den Namen der Locale-Codes: Aus `doc/en/us/` wurde
`doc/eng-us/` und aus `doc/de/` wurde `doc/ger-de/` (mit ihren `.paths.json` und `.dirnames.json`). Beide Schritte sind
reine Umbenennungen (`git mv`) in eigenen Commits, sodass `git log --follow` die Geschichte vor jedem von ihnen
erreicht.

Jeder Verweis auf die alten Pfade im Repository wurde mit geändert: die Links der Dateien im Wurzelverzeichnis
(`README.md`, `INSTALL.md`, `CONTRIBUTING.md`, `SECURITY.md`), die Links der Templates auf die Anleitungen und ihre
GitHub-Links, die Einstellungen (Pfadregeln, Titel und Lizenzregeln von `exphelp.ini` sowie die Einträge `Doc=` der
anderen INI-Dateien), die Code-Kommentare und die Pfade in PHP, die Kommandozeilenskripte, die Tests, die
CI-Workflows, `composer.json` und die Übersetzungen, deren Quelltext eine Anleitung nennt. Die relativen Links innerhalb
des umgezogenen Baums haben sich nicht geändert; die Links aus ihm heraus auf `doc/changelogs/` und auf Dateien
außerhalb von `doc/` sowie die Links der Changelogs auf die Anleitungen wurden umgeschrieben.

Bewusst nicht geändert:

- die veröffentlichten Changelogs (`doc/changelogs/`, außer dem von 6.0.15, der noch geschrieben wird) und die Seiten
  der Geschichte (`doc/eng-us/history/`): Sie halten die Commits und Pfade ihrer Zeit fest;
- die Verzeichnisse `doc/` von Erweiterungen und anderen Repositorys (`extension/ezfind/doc/INSTALL.md`) und URLs
  anderer Projekte.

## Die Verzeichnisnamen: der Locale-Code in Kleinbuchstaben

Ein Sprachverzeichnis ist nach dem Locale-Code von Exponential seiner Sprache benannt (`share/locale/<code>.ini`,
`site.ini [RegionalSettings] Locale`), in Kleinbuchstaben:

| Locale | Verzeichnis |
|--------|-----------|
| `eng-US` (amerikanisches Englisch, das Original) | `doc/eng-us/` |
| `ger-DE` (Deutsch) | `doc/ger-de/` |
| `fre-FR`, `nor-NO`, `heb-IL` ... (eine künftige Übersetzung) | `doc/fre-fr/`, `doc/nor-no/`, `doc/heb-il/` ... |

Die Regel hat keine Ausnahmen: Das Verzeichnis einer Locale ist die Locale in Kleinbuchstaben, und die Locale eines
Verzeichnisses ist das Verzeichnis mit dem Land in Großbuchstaben. Die Oberfläche, die Inhaltssprachen und die
Dokumentation benennen eine Sprache daher auf dieselbe Weise, eine neue Übersetzung braucht keine eigene Zuordnung, und
der Name kann nicht mit einem zweibuchstabigen Sprachcode eines anderen Schemas kollidieren (`de`, `en/us`). Eine
Variante einer Locale (`nor-NO@intl`) erhält kein eigenes Verzeichnis; sie liest das Verzeichnis ihrer Locale.

## Was in doc/ bleibt

- `doc/changelogs/`: nicht übersetzt, daher in keinem Sprachverzeichnis.
- `doc/README.md`: eine kurze Seite für Leser des Repositorys (GitHub zeigt sie für `doc/` an), die die
  Sprachverzeichnisse aufführt. Die Hilfe lässt sie weg (`[IndexSettings] ExcludePatterns[]`), weil sie die Sprache
  selbst wählt.

## Was zu ändern ist

- **Ein Link oder Lesezeichen auf eine Datei bei GitHub** (`.../blob/main/doc/guides/caches.md`): Fügen Sie `eng-us/`
  hinzu (`.../blob/main/doc/eng-us/guides/caches.md`).
- **Eine Adresse der Hilfe** (`/exphelp/viewer/guides/caches.md`): nichts. Die Seiten-IDs haben sich nicht geändert.
- **Eigene Einstellungen, die eine Datei der Dokumentation nennen** (ein Override von `exphelp.ini` `[AudienceSettings]`,
  `[CategorySettings]` oder `[TypeSettings] PathRules[]`, `[TitleSettings] Titles[]`,
  `[License] OtherLicensePathRules[]`, `[IndexSettings] ExcludePatterns[]`): Schreiben Sie `doc/eng-us/`, wo `doc/`
  stand (`doc/changelogs/` bleibt). Die Seiten-IDs in `[ContextHelp] Map[]`, `[FeaturedSettings] PageList[]`,
  `[TreeSettings] PageOrder[]` und `[Landing_*]` ändern sich nicht; ein Wert, der die Datei nennt (`doc/guides/x.md`),
  funktioniert weiterhin.
- **Skripte, die eine Datei der Dokumentation lesen** (`doc/INSTALL.md` in einem Deploy-Skript): Fügen Sie `eng-us/`
  hinzu.
- **Die Caches der Hilfe** (`var/<site>/cache/exphelp`): nichts; der Index wird von selbst neu gebaut. Führen Sie nach
  dem Deploy `./console exp:help index --rebuild` aus, um den Index und die Suche jeder Sprache auf einmal zu bauen.

## Upgrade einer Website, die auf doc/en/us oder doc/de verweist

Der Entwicklungs-Branch hatte am 20. Oktober 2026 eine Zeit lang die Verzeichnisse `doc/en/us/` und `doc/de/`, daher
kann eine Website, ein Lesezeichen oder ein Override, das gegen diesen Stand geschrieben wurde, sie nennen. Alles, was
über die Hilfe geht, funktioniert weiter; was die Dateien direkt liest, braucht den neuen Namen:

| Geschrieben als | Jetzt | Was geschieht |
|------------|-----|--------------|
| `/exphelp/viewer/en/us/guides/caches.md`, `/exphelp/viewer/doc/en/us/guides/caches.md` | `/exphelp/viewer/guides/caches.md` | Weiterleitung auf die Seiten-ID (`Legacy[en/us]`) |
| `/exphelp/viewer/de/anleitungen/caches.md` (ein übersetzter Pfad) | `/exphelp/viewer/guides/caches.md` | Weiterleitung auf die Seiten-ID (`Legacy[de]`) |
| `?lang=en-us`, `?lang=de` (das Original einer Seite, oder eine Sprache für eine Anfrage) | `/(lang)/eng-us`, `/(lang)/ger-de` (eine Locale wie `ger-DE` funktioniert ebenfalls) | die alten Schlüssel werden weiterhin als `/(lang)/en-us` akzeptiert (`Legacy[]`); ein Query-Parameter `?lang=` wird nicht mehr gelesen |
| `[DocLanguages] Default=en/us`, `Directories[]=en/us`, `Directories[]=de`, `Map[...]=en/us` / `=de` in einem Override | `eng-us`, `ger-de` | ändern Sie das Override: `Legacy[]` bedient nur alte Adressen, es benennt kein konfiguriertes Verzeichnis um, und ein Verzeichnis namens `en/us` oder `de` existiert nicht mehr, hat also keine Seiten |
| `doc/en/us/...`, `doc/de/...` in den Pfadregeln eines Overrides, in einem Skript oder in einem GitHub-Link | `doc/eng-us/...`, `doc/ger-de/...` | ändern Sie es: Die alten Dateien existieren nicht mehr |
| eine eigene Übersetzung in `doc/<two letters>/` (`doc/fr/`) | `doc/<locale lowercased>/` (`doc/fre-fr/`) | verschieben Sie sie mit `git mv`, führen Sie den neuen Namen in `Directories[]` auf und fügen Sie `Legacy[fr]=fre-fr` für ihre alten Adressen hinzu |
| die Caches der Hilfe pro Sprache `var/<site>/cache/exphelp/lang-de/` | `lang-ger-de/` | werden von selbst neu gebaut; das alte Verzeichnis kann entfernt werden |

Auf einer an Ort und Stelle aktualisierten Website (ein Deploy, der Dateien kopiert und nichts löscht) können die alten
Kopien `doc/en/us/` und `doc/de/` neben den neuen auf der Festplatte bleiben. Die Hilfe liest nur die Verzeichnisse aus
`Directories[]` und nur versionierte Dateien, daher werden sie nicht angezeigt; entfernen Sie sie, wenn der Deploy
abgeschlossen ist.

## Die Hilfe und die Sprache des Lesers

Die Sprache des Lesers ist die Sprache, in der die Administration angezeigt wird: `site.ini [RegionalSettings] Locale`
der Anfrage, die die Sprachumschaltung in der oberen Leiste für den angemeldeten Benutzer setzt. Die Hilfe findet ihr
Verzeichnis in dieser Reihenfolge (`expHelpLanguage::forLocale()`), jeder Schritt nur, wenn
`[DocLanguages] Directories[]` das Verzeichnis aufführt, das er liefert:

1. ein Alias der Locale in `exphelp.ini [DocLanguages] Map[]` (`Map[eng-GB]=eng-us`, `Map[ger-AT]=ger-de`);
2. die Locale in Kleinbuchstaben (`ger-DE` liest `ger-de`);
3. dieselben beiden für die Locale ohne ihre Variante (`nor-NO@intl` als `nor-NO`);
4. die Sprache (die drei Buchstaben vor „-“): ein Eintrag in `Map[]` nur für die Sprache (`Map[fre]=fre-fr`), sonst das
   erste Verzeichnis dieser Sprache in `Directories[]`, sodass Deutsch eines anderen Landes (`ger-LU`) ohne Alias
   `ger-de` liest;
5. der Standard (`Default=eng-us`).

| Locale | Verzeichnis |
|--------|-----------|
| `eng-US` | `eng-us` (sein eigener Name) |
| `eng-GB` | `eng-us` (Alias) |
| jedes andere `eng-*` | `eng-us` (das erste Verzeichnis `eng-`) |
| `ger-DE` | `ger-de` (sein eigener Name) |
| `ger-AT`, `ger-CH` | `ger-de` (Alias) |
| jedes andere `ger-*` | `ger-de` (das erste Verzeichnis `ger-`) |
| jede andere | `eng-us` (der Standard) |

Index, Suche, Navigationsbaum, gerenderte Seiten und die Kontexthilfe sind dann die dieser Sprache:

- eine Seite, die die Sprache hat, wird darin angezeigt;
- eine Seite, die sie noch nicht hat, wird auf Englisch angezeigt, mit einem Hinweis darüber: „Diese Seite ist noch
  nicht auf Deutsch übersetzt. Sie wird auf Englisch angezeigt.“ (übersetzt, `kernel/exphelp`);
- die Changelogs sind immer englisch (sie haben denselben Hinweis für einen Leser einer anderen Sprache);
- der Artikel trägt die Sprache der Seite (`lang="de"`, `lang="en-US"`), sodass Screenreader ihn richtig aussprechen.

Die Suche einer Sprache findet die übersetzten Seiten und die englischen Seiten, die nicht übersetzt sind, nie das
englische Original einer übersetzten Seite. Jede Sprache hat ihren eigenen Index und ihren eigenen Suchindex, in
`var/<site>/cache/exphelp/` für den Standard und `var/<site>/cache/exphelp/lang-<directory>/` (`lang-ger-de`) für die
anderen. Die Formate wurden mit der Umbenennung angehoben (der Index auf 4, der Suchindex auf 5, die gerenderten Seiten
des Viewers auf 5), sodass Caches, die für die alten Verzeichnisnamen gebaut wurden, von selbst neu gebaut werden.

## Seiten-IDs und Adressen

Eine Seite wird über ihre ID benannt: ihren Pfad unter `doc/eng-us/` (`guides/caches.md`), in jeder Sprache derselbe.
Die Adresse einer Seite ist daher in jeder Sprache dieselbe (`/exphelp/viewer/guides/caches.md`), und wenn ein Leser die
Sprache der Oberfläche wechselt, bleibt die Seite, auf der er ist, geöffnet und wird in der neuen Sprache angezeigt. Eine
Seite aus `doc/changelogs/` hat wie bisher die ID `changelogs/<path>`. Die Umbenennung zu den Locale-Codes hat keine ID
geändert, daher sind `[TreeSettings] PageOrder[]`, die Kontexthilfe und Lesezeichen auf Adressen der Hilfe nicht
betroffen.

Eine Adresse, die statt einer ID eine Datei nennt, wird auf die ID weitergeleitet:
`/exphelp/viewer/doc/eng-us/guides/caches.md`, ein übersetzter Pfad wie
`/exphelp/viewer/ger-de/anleitungen/caches.md` und die alten Namen von vor der Umbenennung
(`/exphelp/viewer/en/us/guides/caches.md`, `/exphelp/viewer/doc/en/us/guides/caches.md`,
`/exphelp/viewer/de/anleitungen/caches.md`). Jede Adresse der Hilfe von vor dem Umzug funktioniert unverändert weiter,
weil sich die IDs nicht geändert haben.

Links innerhalb einer Seite werden gegen die Datei aufgelöst, aus der die Seite gelesen wurde, so wie GitHub sie
auflöst. Eine deutsche Seite, die auf `../hilfe/suchen.md` verweist, öffnet die Seite, die diese Datei übersetzt, in der
Sprache des Lesers.

Ein Anker in einem Link ist für die Fassung des Ziels in einer Sprache geschrieben (`guides/x.md#cross-engine-notes`
in einer englischen Seite), aber der Leser sieht vielleicht die andere Fassung, deren Überschriften andere Anker haben.
Eine Übersetzung behält die Überschriften ihres Originals in derselben Reihenfolge, daher ordnet die Hilfe den Anker
nach seiner Position zu: Die n-te Überschrift der Fassung, für die der Link geschrieben wurde, ist die n-te Überschrift
der angezeigten Fassung (`expHelpDocumentIndex::anchorOf()`). Sie tut das beim Rendern der Seite und in
`exp:help check`, vom Englischen in eine Übersetzung und zurück; ein Anker, den die angezeigte Seite selbst hat, oder
Fassungen mit einer unterschiedlichen Zahl von Überschriften bleiben, wie sie sind.

## Eine Übersetzung hinzufügen

1. Benennen Sie das Verzeichnis nach der Locale in Kleinbuchstaben (`fre-FR` -> `doc/fre-fr/`) und fügen Sie es zu
   `exphelp.ini [DocLanguages] Directories[]` hinzu (in einem Override, oder in der Datei des Kernels für eine
   Übersetzung, die mit Exponential ausgeliefert wird). Ihre eigene Locale braucht keinen Eintrag in `Map[]`; fügen
   Sie Aliase für die anderen Locales hinzu, die sie lesen sollen (`Map[fre-CA]=fre-fr`, `Map[fre]=fre-fr`), und geben
   Sie ihren englischen Namen in `Names[]` an, mit einer Übersetzung dieses Namens in `kernel/exphelp`.
2. Kopieren Sie die Seiten, die Sie übersetzen, mit denselben relativen Pfaden von `doc/eng-us/` nach `doc/<locale>/`,
   übersetzen Sie sie und committen Sie sie: Die Hilfe zeigt nur versionierte Dateien.
3. Übersetzen Sie so viele Seiten, wie Sie möchten; jede Seite, die Sie noch nicht übersetzt haben, wird auf Englisch
   angezeigt.
4. Bilder: Ein Bild, das die Übersetzung nicht hat, wird aus `doc/eng-us/` genommen. Legen Sie einen übersetzten
   Screenshot unter denselben relativen Pfad, um ihn stattdessen zu verwenden. Eine Seite mit übersetztem Dateinamen
   verweist auf das Bild des Originals über dessen Pfad von der Datei aus, wie GitHub es tut
   (`![...](../../eng-us/help/images/x.webp)` in `doc/ger-de/hilfe/x.md`): Links und Bilder einer Seite werden von der
   Datei aus aufgelöst, aus der sie gelesen wurde, und dürfen daher über die ID der Seite hinaus nach oben gehen.

Ein Verzeichnis unter `doc/`, das `Directories[]` nicht aufführt (`doc/fre-fr/`), wird nicht gelesen, sodass eine
Übersetzung im Repository vorbereitet werden kann, bevor sie eingeschaltet wird.

## Übersetzte Dateinamen: .paths.json

Eine Übersetzung kann die englischen Dateinamen behalten (`doc/ger-de/guides/caches.md`), was nichts weiter braucht.
Übersetzt sie sie, führt sie jeden übersetzten Pfad in `doc/<locale>/.paths.json` auf, einem JSON-Objekt von
„übersetzter Pfad unter dem Sprachverzeichnis“ auf „Pfad unter `doc/eng-us/`“:

```json
{
    "anleitungen/caches.md": "guides/caches.md",
    "hilfe/suchen.md": "help/searching.md"
}
```

Die Seite `doc/ger-de/anleitungen/caches.md` hat dann die ID `guides/caches.md`: dieselbe Adresse, dieselbe
Kontexthilfe, dieselben Landing Pages und hervorgehobenen Seiten wie die englische Seite, und die Pfadregeln der
Einstellungen sehen sie als `doc/eng-us/guides/caches.md` (eine Übersetzung hat Zielgruppe, Kategorie und Lizenz der
Seite, die sie übersetzt). Ein Eintrag, dessen Pfade keine sauberen relativen Pfade sind, wird weggelassen; die Datei
wird gelesen, wenn sie eine reguläre Datei von höchstens 1 MB ist.

## Einstellungen

`exphelp.ini`:

```ini
[DocLanguages]
Default=eng-us
Directories[]
Directories[]=eng-us
Directories[]=ger-de
# Aliases only: a locale whose lowercased code is a directory needs no entry
Map[]
Map[eng-GB]=eng-us
Map[ger-AT]=ger-de
Map[ger-CH]=ger-de
Names[]
Names[eng-us]=English
Names[ger-de]=German
# The directories of 6.0.14 and before => today's, for old addresses only
Legacy[]
Legacy[en/us]=eng-us
Legacy[de]=ger-de

[IndexSettings]
ExcludePatterns[]=doc/README.md
```

Ein Eintrag in `Map[]`, der ein Verzeichnis nennt, das `Directories[]` nicht aufführt, wird ignoriert. `Legacy[]` ordnet
einen alten Verzeichnisnamen dem heutigen zu, sodass eine alte Adresse (`/exphelp/viewer/en/us/<id>`,
`/exphelp/viewer/doc/en/us/<id>`, `/exphelp/viewer/de/<translated path>`, `/(lang)/en-us`) ihre Seite weiterhin findet
(`expHelpLanguage::resolveDirectory()`, `splitLegacy()`); aus einem alten Verzeichnis wird nie eine Datei gelesen. Die
beiden obigen Einträge sind außerdem eingebaut, sodass ein Override, das `Legacy[]` ersetzt, sie behält. Eine eigene
Übersetzung, die ein zweibuchstabiges Verzeichnis hatte (`doc/fr/`), kann nach dem Umbenennen `Legacy[fr]=fre-fr`
hinzufügen, damit ihre alten Adressen weiter funktionieren.

## Die Kommandozeile

- `./console exp:help index` baut den Index und den Suchindex jeder Sprache und meldet pro Sprache, wie viele Seiten
  übersetzt sind und wie viele auf Englisch angezeigt werden. `--language=ger-de` baut nur diese Sprache.
- `./console exp:help search`, `show`, `check` und `pdf` lesen die Standardsprache oder die von `--language=`.
- `./console exp:help pdf doc/eng-us/guides/caches.md` akzeptiert einen Dateinamen ebenso wie eine ID.
- `./console exp:help maps --language=ger-de` prüft die Pfadzuordnung und die Zuordnung der Verzeichnisnamen einer
  Übersetzung (siehe unten); `--fix` schreibt sie zusammengeführt und sortiert zurück.

## Mehrere Übersetzer: .paths.json und .dirnames.json zusammenführen

Eine Übersetzung, die ihre Dateinamen übersetzt, hält in ihrem Verzeichnis zwei Zuordnungen:

- `.paths.json`: übersetzter Pfad => Pfad unter `doc/eng-us/` (von der Hilfe gelesen, siehe oben);
- `.dirnames.json`: englischer Verzeichnisname => übersetzter Verzeichnisname (`{"help": "hilfe", "guides":
  "anleitungen"}`). Die Hilfe liest sie nicht; sie hält parallel arbeitende Übersetzer bei einem Namen pro
  Verzeichnis, sodass die Seiten von `help/` alle in `hilfe/` landen.

Beide werden auf dieselbe Weise geschrieben: ein JSON-Objekt, Schlüssel sortiert, ein Eintrag pro Zeile, ein
abschließender Zeilenumbruch (`expHelpLanguage::encodePathMap()`). Zwei Übersetzer, die an verschiedenen Stellen
Einträge hinzufügen, führen dann ohne Konflikt zusammen, und wenn git doch an einem anhält, ist die Regel die
Vereinigung beider Seiten:

```bash
git merge origin/main                                      # conflict in doc/ger-de/.paths.json
./console exp:help maps --language=ger-de --fix            # both sides of the conflict, merged and sorted
./console exp:help maps --language=ger-de --fix --paths=my-paths.json --dirnames=my-dirnames.json   # and a list of your own
./console exp:help maps --language=ger-de                  # check: PASS, or what is wrong
git add doc/ger-de/.paths.json doc/ger-de/.dirnames.json
```

`--fix` liest beide Seiten der Konfliktmarkierungen, die git in der Datei hinterlassen hat (auch mit einem
diff3-Basisabschnitt und wenn das Komma der letzten Zeile einer Seite verloren gegangen oder verdoppelt ist), fügt die
Zuordnungen aus `--paths=` und `--dirnames=` hinzu und schreibt die Vereinigung (`expHelpLanguage::mergePathMaps()`).
Ein Eintrag geht nie stillschweigend verloren: Ein Pfad, dem die beiden Seiten verschiedene Werte geben, behält den
eigenen Wert der Datei (unsere Seite) und wird als `CONFLICT` gemeldet, ebenso eine Seite, die zwei übersetzte Pfade
nennen (ihre ID würde zwei Dateien nennen). Die Prüfung meldet außerdem einen übersetzten Pfad, der keine Datei ist,
ein Original, das keine Seite ist, und einen übersetzten Pfad, dessen Verzeichnisse nicht so benannt sind, wie
`.dirnames.json` sagt (jedes Verzeichnis hat entweder seinen übersetzten Namen oder behält seinen englischen). Sie
endet mit 1, wenn etwas falsch ist.

## Tests

`tests/tests/kernel/classes/help/expHelpTranslatedDocumentationTest.php` (ohne Datenbank):

- TD-01: Jede versionierte Datei unter `doc/` liegt in einem Sprachverzeichnis (`doc/eng-us/`, `doc/ger-de/`), in
  `doc/changelogs/` oder ist `doc/README.md`;
- TD-02: Keine versionierte Textdatei nennt einen Pfad von vor dem Umzug (`doc/guides/...`) oder von vor der
  Umbenennung (`doc/en/us/`, `doc/de/`), abgesehen von der Geschichte (veröffentlichte Changelogs, Seiten der
  Geschichte, der Hinweis zum Upgrade auf dieser Seite) und den Verzeichnissen `doc/` anderer Repositorys;
- TD-03 und TD-04: Locales werden ihrem Verzeichnis zugeordnet (die Locale in Kleinbuchstaben, die Aliase aus `Map[]`,
  mit den Rückfällen auf Variante und Sprache), und die ausgelieferten Einstellungen entsprechen den eingebauten
  Standardwerten;
- TD-05: die Pfadzuordnung in beide Richtungen, fehlerhafte Einträge weggelassen, Dateinamen in IDs umgewandelt;
- TD-06 bis TD-09: Der Index einer Übersetzung liefert übersetzte Seiten und kennzeichnet die Rückfälle, der Index des
  Originals liest nie eine Übersetzung, dieselbe ID nennt in jeder Sprache dieselbe Seite, Links werden gegen die Datei
  aufgelöst, die Suche ist pro Sprache, und eine Übersetzung wird wie ihr Original eingeordnet;
- TD-10: Die Pfadzuordnungen werden als Vereinigung zusammengeführt (sortiert, dieselbe Datei unabhängig von der
  Reihenfolge, ein Konflikt aus der ersten Zuordnung behalten und gemeldet, ein Git-Konflikt als seine beiden Seiten
  gelesen), und Verzeichnisnamen folgen `.dirnames.json`;
- TD-11: wie viel der Hilfe jede Sprache hat, die Auswahl der Umschaltung „Lesesprache:“ und `/(lang)/` für das
  Original (`eng-us`, `ger-de` und die alten Schlüssel `en-us`, `de`);
- TD-12: Eine übersetzte Seite wird aus ihrer Datei gerendert, sodass ihre Links und Bilder das Verzeichnis des
  Originals erreichen (`../../eng-us/help/images/x.webp` aus `doc/ger-de/hilfe/x.md`);
- TD-13: Ein Anker, der für die Fassung einer Seite in einer anderen Sprache geschrieben ist, wird der Überschrift an
  derselben Position der angezeigten Seite zugeordnet, beim Rendern der Seite und in der Link-Prüfung, in beide
  Richtungen.

Die ganze Kette auf einer laufenden Website (beide Server, hell und dunkel, die alten Adressen, die Sprachumschaltung
und eine deutsche Seite) wird in einem Browser als Wegwerf-Administrator geprüft.
