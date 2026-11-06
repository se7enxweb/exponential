# Spezifikation: die Dateiformate für URL-Zuordnungen

Diese Seite ist die verbindliche Beschreibung der Dateien, die eine URL-Zuordnung in Exponential 6.0.15 tragen: eine
Liste alter Adressen und wohin jede führt, wie sie für die globalen URL-Aliase (den URL-Übersetzer) und die
URL-Platzhalter (Wildcards) verwendet wird. Sie beschreibt das Zeilenmodell, die Spalten und Arten, wie Adressen
normalisiert werden, und jedes Dateiformat mit dem, was es liest, was es schreibt und was es nicht ausdrücken kann.
Sie richtet sich an Entwickler und an Administratoren, die solche Dateien von Hand oder mit anderen Werkzeugen
vorbereiten.

Version dieser Spezifikation: 1 (`exp-url-map/1`).

Die Klassen liegen in `kernel/classes/urlmap/` und `kernel/classes/spreadsheet/`; sie lesen und schreiben nur Dateien
und verwenden keine Datenbank. Das Importieren der Zeilen in eine Installation, die Vorschau und das Rückgängigmachen
gehören nicht zu dieser Seite.

## Die Zeile

Jedes Format trägt dieselben sechs Spalten:

| Spalte | Inhalt |
|---|---|
| `old_url` | Die alte Adresse: ein Pfad (`/about-us`) oder eine vollständige URL (`https://old-site.com/about-us`). Ein `*` im Pfad macht daraus einen Platzhalter. |
| `new_url` | Das Ziel: ein Pfad dieser Website (`/About`), ein Knoten (`node:123` oder `eznode://123`), eine vollständige URL oder bei Platzhaltern ein Muster mit den Platzhaltern `{1}`, `{2}`. |
| `kind` | Leer (automatisch) oder eine der Arten unten. |
| `siteaccess` | Optional: der Siteaccess, zu dem die Zuordnung gehört. Buchstaben, Ziffern, `_` und `-`. |
| `language` | Optional: ein Gebietsschema wie `eng-GB`. |
| `note` | Optional: freier Text. |

### Arten

| Art | Bedeutung |
|---|---|
| (leer) | Automatisch: ein Platzhalter (weiterleitend), wenn `old_url` ein `*` enthält, sonst eine Weiterleitung |
| `redirect` | Ein globaler URL-Alias, der mit 301 auf das Ziel weiterleitet |
| `alias` | Ein globaler URL-Alias ohne Weiterleitung: die Zielseite wird unter der alten Adresse ausgeliefert |
| `wildcard-forward` | Ein URL-Platzhalter, der weiterleitet |
| `wildcard-direct` | Ein URL-Platzhalter, der das Ziel ohne Weiterleitung ausliefert |

Eine Art wird in Kleinbuchstaben verglichen, und `_` oder ein Leerzeichen gilt als `-` (`Wildcard_Forward` ist
`wildcard-forward`). `auto` ist dasselbe wie leer. Jeder andere Wert bleibt, wie er geschrieben ist, und macht die
Zeile nicht importierbar.

### Normalisierung

Die alte und die neue Adresse werden beim Lesen einer Zeile normalisiert, damit dieselbe Adresse in jedem Format
dieselbe Zeile ergibt:

- Schema und Host werden abgetrennt. Schema und Host werden kleingeschrieben; ein Standardport (`:80` für http,
  `:443` für https) und ein abschließender Punkt des Hosts entfallen. `//host/pfad` ist ein Host ohne Schema;
  `www.example.com/pfad` ist ein Host, weil es mit `www.` beginnt. Jeder andere Text ohne Schema ist ein Pfad.
- Nur `http` und `https` werden angenommen; ein Benutzername oder Passwort in der Adresse ist ein Problem.
- Der Pfad beginnt mit `/`; mehrere Schrägstriche werden einer; die Segmente `.` und `..` werden aufgelöst.
- **Abschließender Schrägstrich:** ein abschließender Schrägstrich entfällt (das Wurzelverzeichnis `/` bleibt).
  Exponential liefert `/about` und `/about/` als dieselbe Seite aus, also sind `/about/` und `/about` dieselbe alte
  Adresse.
- **Prozentkodierung:** `%XX`-Folgen, die UTF-8-Text kodieren, werden dekodiert (`/%C3%9Cber` ist `/Über`). `%2F`,
  `%3F`, `%23` und `%25` bleiben kodiert, weil das Dekodieren die Adresse verändern würde; Leerzeichen und
  Steuerzeichen werden kodiert (`/Über uns` ist `/Über%20uns`); die verbleibenden Hexziffern werden großgeschrieben.
  Bytes, die kein UTF-8 sind, bleiben kodiert.
- Das Fragment (`#...`) entfällt; die Abfrage (`?...`) bleibt, wie sie geschrieben ist. Ein Platzhalter kann keine
  Abfrage haben.
- Groß- und Kleinschreibung des Pfads bleiben erhalten.
- Ein Ziel `node:123` oder `eznode://123` wird `node:123` geschrieben. Ein Ziel mit Host, aber ohne Schema, erhält
  `https`.

### Prüfung

Eine Zeile ist importierbar, wenn sie keines dieser Probleme hat:

| Problem | Beispiel |
|---|---|
| `old_url` oder `new_url` ist leer | `/old,` |
| Eine unbekannte Art | `moved` |
| Ein `*` in `old_url` mit einer Art, die kein Platzhalter ist | `/a/*,/b,redirect` |
| Eine Platzhalter-Art ohne `*` | `/a,/b,wildcard-forward` |
| Ein Platzhalter in `new_url` einer Zeile, die kein Platzhalter ist, oder einer ohne passendes `*` | `/a/*,/b/{2}` |
| Ein `*` im Host, eine Abfrage bei einem Platzhalter | `https://*.example.com/a` |
| Derselbe Pfad als alte und neue Adresse | `/a/,/a` |
| Ein Siteaccess oder eine Sprache, die kein Name oder Gebietsschema ist | `my site`, `english` |
| Ein anderes Schema als http(s), ein Benutzername oder Passwort | `ftp://...` |
| Ein Knotenverweis, der nicht `node:<Zahl>` ist | `node:abc` |

Jede Zeile behält ihre Herkunft (`line()`): die Zeile, in der ein CSV-Datensatz beginnt, die Zeilennummer in einer
Tabelle, die Eintragsnummer in JSON (ab 1, ohne den Kopf), die Zeile der Direktive in Apache- und nginx-Dateien.
Eine Datei, die sich gar nicht lesen lässt (ungültiges JSON, keine Arbeitsmappe, ein unbekanntes Format), wird als
Ganzes abgelehnt; eine einzelne fehlerhafte Zeile ist eine Zeile mit ihren Problemen, damit eine Vorschau sie
aufführen kann.

## CSV

Gelesen und geschrieben. Endungen `.csv`, `.tsv` und `.txt`.

- RFC 4180: Felder können mit `"` eingefasst werden; ein eingefasstes Feld kann das Trennzeichen, Zeilenumbrüche und
  verdoppelte Anführungszeichen enthalten.
- Das Trennzeichen ist `,`, `;` oder ein Tabulator und wird aus den ersten 20 Zeilen ermittelt (das Zeichen, das die
  meisten Zeilen in dieselbe Zahl von Feldern teilt). Tabellenprogramme mit deutscher Einstellung schreiben `;`,
  „Unicode-Text“ aus Excel ist durch Tabulatoren getrennt.
- Kodierung: UTF-8, mit oder ohne Byte-Order-Mark; UTF-16 mit Byte-Order-Mark wird umgewandelt; Text, der kein
  gültiges UTF-8 ist, wird als Windows-1252 gelesen. Zeilenenden `\r\n`, `\n` und `\r`.
- Eine Kopfzeile ist optional. Sie wird erkannt, wenn sie `old_url` oder `new_url` nennt (auch `old`, `from`,
  `source`, `new`, `to`, `target`, `destination` und die deutschen `Alte URL`, `Quelle`, `Neue URL`, `Ziel` und
  ähnliche) und ihre erste Zelle keine Adresse ist. Ihre Spalten können in beliebiger Reihenfolge stehen; unbekannte
  Spalten werden übergangen. Ohne Kopfzeile stehen die Spalten in der Reihenfolge oben.
- Leere Zeilen und Zeilen, deren erste Zelle mit `#` beginnt, werden übersprungen. Eine Zeile ohne Kopfzeile mit mehr
  als sechs gefüllten Spalten ist ein Problem.
- Geschrieben mit Kopfzeile, CRLF-Zeilenenden, nur bei Bedarf eingefassten Feldern und einer UTF-8-Byte-Order-Mark
  (die Option `bom`, standardmäßig an, damit Excel UTF-8 liest). Eine `.tsv`-Datei wird mit Tabulatoren geschrieben.

```text
old_url,new_url,kind,siteaccess,language,note
/about-us.html,/About,,,,old about page
https://old-site.com/news/2019/,/News,redirect,site,eng-GB,
/products/*,/Shop/{1},,,,"catalogue, all pages"
/team,node:42,alias,,,
```

## JSON

Gelesen und geschrieben. Endung `.json`.

Ein Array von Objekten mit den kanonischen Schlüsseln. Der erste Eintrag kann der Kopf `{"schema": "exp-url-map/1"}`
sein; ein Objekt `{"schema": "exp-url-map/1", "rows": [...]}` wird ebenfalls gelesen. Ein anderes Schema wird
abgelehnt. Die anderen Spaltennamen der CSV-Kopfzeile werden als Schlüssel angenommen; Zahlen werden als Text gelesen;
ein Eintrag, der kein Objekt ist, oder ein Wert, der ein Array ist, ist eine Zeile mit einem Problem. Geschrieben wird
das Array mit dem Kopf zuerst und allen sechs Schlüsseln in jedem Objekt, eingerückt, Schrägstriche und Unicode nicht
maskiert.

```json
[
    { "schema": "exp-url-map/1" },
    { "old_url": "/about-us.html", "new_url": "/About", "kind": "", "siteaccess": "", "language": "", "note": "" },
    { "old_url": "/products/*", "new_url": "/Shop/{1}", "kind": "", "siteaccess": "", "language": "", "note": "" }
]
```

## Excel (.xlsx)

Gelesen und geschrieben. Endung `.xlsx`. Benötigt die PHP-Erweiterung zip (ZipArchive); ohne sie bricht das Lesen
und Schreiben von Tabellen mit einer Meldung ab, die das sagt, und CSV und JSON funktionieren weiter.

- Gelesen wird das erste Tabellenblatt der Arbeitsmappe (in der Reihenfolge der Mappe) als Tabelle, wie bei CSV:
  dieselbe Erkennung der Kopfzeile, dieselben Spalten. Gemeinsame Zeichenketten (auch mit formatierten Abschnitten),
  eingebettete Zeichenketten, Zahlen (ganze Zahlen ohne `.0` oder Exponent), Wahrheitswerte (`TRUE`, `FALSE`) und das
  letzte Ergebnis von Formeln werden gelesen; Formatvorlagen, andere Blätter, verbundene Zellen und Kommentare werden
  übergangen. Eine Zelle mit einem Fehler ist leer.
- Geschrieben wird ein Blatt „URL map“ mit der Kopfzeile (fett und fixiert) und Spaltenbreiten; jede Zelle ist eine
  eingebettete Zeichenkette, damit nichts zu einer Zahl, einem Datum oder einer Formel wird. Das Paket enthält nur
  die Teile, die eine Arbeitsmappe braucht: `[Content_Types].xml`, `_rels/.rels`, `xl/workbook.xml`,
  `xl/_rels/workbook.xml.rels`, `xl/styles.xml` und `xl/worksheets/sheet1.xml`.
- Ein Teil mit einem `DOCTYPE` oder mit mehr als 100 MB unkomprimiert wird abgelehnt.

## OpenDocument-Tabelle (.ods)

Nur geschrieben (für LibreOffice Calc). Endung `.ods`. Benötigt die PHP-Erweiterung zip. Ein Blatt „URL map“ mit der
Kopfzeile in Fettschrift; der Eintrag `mimetype` steht zuerst und unkomprimiert, wie OpenDocument es verlangt;
Leerzeichen, Tabulatoren und Zeilenumbrüche in einer Zelle werden als OpenDocument-Elemente geschrieben, damit sie
erhalten bleiben. Um eine in Calc bearbeitete Zuordnung zu importieren, speichern Sie sie als .xlsx oder CSV.

## Apache

Gelesen und geschrieben. Endungen `.htaccess`, `.apache`, `.apacheconf`, der Dateiname `.htaccess` und `.conf`, wenn
der Inhalt Apache-Direktiven enthält.

Gelesen:

| Direktive | Zeile |
|---|---|
| `Redirect [status] /old /new`, `RedirectPermanent`, `RedirectTemp` | Eine Weiterleitung genau dieses Pfads. Apaches `Redirect` erfasst auch die Pfade darunter; für einen Teilbaum verwenden Sie `RedirectMatch` oder eine Platzhalter-Zeile. |
| `RedirectMatch [status] ^/old/(.*)$ /new/$1` | Ein Platzhalter (weiterleitend), wenn der Ausdruck einfach ist (unten) |
| `RewriteRule ^/?old$ /new [R=301,L]` | Eine Weiterleitung oder mit Gruppen ein Platzhalter (weiterleitend) |
| `RewriteRule ^/?old/(.*)$ /new/$1 [L]` (ohne `R`) | Eine interne Umschreibung: ein Alias oder ein Platzhalter (direkt) |
| `RewriteCond %{HTTP_HOST} ^old\.com$ [NC]` vor einer Regel | Der Host der alten Adresse (ein vorangestelltes `(www\.)?` entfällt) |
| `RewriteCond %{QUERY_STRING} ^id=5$` vor einer Regel | Die Abfrage der alten Adresse |
| `ServerName` innerhalb von `<VirtualHost>` | Der Host der Zeilen in diesem Block |
| `RewriteBase /base/` | Die Basis relativer Ziele |
| `#exp:<kind> <old> <new>` | Eine Zeile dieser Art (von Exponential geschrieben, unten) |

Eine Weiterleitung mit 302, 303 oder 307 wird als Weiterleitung mit einer Notiz gelesen („the source answered with
302; Exponential redirects with 301“). Nicht importierbar, mit dem Grund in der Zeile und der Direktive in ihrer
Notiz: `gone`/410 und andere Statuscodes, andere Bedingungen (`%{REQUEST_FILENAME}`, `[OR]`), die Flags `F`, `G`,
`P`, `C`, `S`, `E`, `CO`, `T`, `H`, `N`, ein negiertes Muster, das Ziel `-`, `%1` und `%{...}` im Ziel. Die Flags
`L`, `NC`, `QSA`, `QSD`, `NE`, `END` und `PT` ändern die Zeile nicht. Mit `\` fortgesetzte Zeilen werden verbunden.
Andere Direktiven werden übergangen.

Geschrieben, für einen `<VirtualHost>` oder die `.htaccess` des Dokumentverzeichnisses:

```apache
RewriteEngine On

# old about page
RewriteRule ^/?about\-us\.html/?$ /About [R=301,L]

RewriteCond %{HTTP_HOST} ^old\-site\.com$ [NC]
RewriteRule ^/?news/?$ /News [R=301,L]

RewriteCond %{QUERY_STRING} ^id\=5$
RewriteRule ^/?index\.php/?$ /Home? [R=301,L]

RewriteRule ^/?products/(.*)$ /Shop/$1 [R=301,L]

#exp:alias /team /Team
#exp:redirect /staff node:42
```

`^/?` und `/?$` lassen die Regel an beiden Orten wirken und mit und ohne abschließenden Schrägstrich passen. Eine
Abfragebedingung beendet das Ziel mit `?`, damit die alte Abfrage nicht übernommen wird. Aliase und Platzhalter
(direkt) liefert Exponential selbst ohne Weiterleitung aus, und ein Ziel `node:<id>` kennt nur Exponential; sie werden
daher als `#exp:`-Kommentare geschrieben, die Apache übergeht und der Leser wieder einliest. Notiz, Siteaccess und
Sprache stehen in einem Kommentar über der Regel. Nicht importierbare Zeilen werden mit einem Kommentar
`# not written:` ausgelassen.

## nginx

Gelesen und geschrieben. Endung `.nginx` und `.conf`, wenn der Inhalt nginx-Direktiven enthält.

Gelesen:

| Direktive | Zeile |
|---|---|
| `rewrite ^/old$ /new permanent;` | Eine Weiterleitung (`redirect` ist 302 und erhält die Notiz); mit Gruppen ein Platzhalter (weiterleitend) |
| `rewrite ^/old/(.*)$ /new/$1 last;` (`last`, `break`) | Eine interne Umschreibung: ein Alias oder ein Platzhalter (direkt) |
| `location = /old { return 301 /new; }` | Eine Weiterleitung |
| `location ~ ^/old/(.*)$ { return 301 /new/$1; }` | Ein Platzhalter (weiterleitend) |
| `if ($args = "id=5") { return 301 /new; }` innerhalb von `location =` | Eine Weiterleitung der alten Adresse mit dieser Abfrage |
| `map $uri $new { /old /new; ~^/old/(.*)$ /new/$1; }` | Eine Weiterleitung (oder ein Platzhalter) je Eintrag; auch `$request_uri` und `$document_uri`. Das `return 301 $new;`, das die Zuordnung verwendet, ergibt keine eigene Zeile. |
| `server_name` in einem `server`-Block | Der Host der Zeilen in diesem Block (der erste Name ohne `*`, `~` oder `$`) |
| `#exp:<kind> <old> <new>` | Eine Zeile dieser Art |

`$scheme://$host` (oder `https://$host`) vor einem Ziel ist derselbe Host und entfällt. Nicht importierbar: ein
`return` in einer Präfix-Location (`location /old` erfasst jeden Pfad, der mit `/old` beginnt) oder außerhalb einer
Location, ein `return` mit einem anderen Status (410, 404), ein `rewrite` innerhalb eines `if`, ein `return` innerhalb
eines `if` mit einer anderen Bedingung, andere Variablen im Ziel, eine `map` von etwas anderem als einem Pfad.

Geschrieben, zum Einbinden in den Server-Block der Website:

```nginx
location = /about-us.html {
    return 301 /About;
}

location = /index.php {
    if ($args = "id=5") { return 301 /Home; }
    return 301 /;
}

rewrite ^/products/(.*)$ /Shop/$1 permanent;

#exp:alias /team /Team

server {
    server_name old-site.com;

    location = /news {
        return 301 /News;
    }
}
```

Exakte Weiterleitungen eines Pfads bilden einen `location =`-Block (mit einem `if` je Abfrage); eine zweite
Weiterleitung derselben Adresse wird mit einem Kommentar ausgelassen, da nginx zwei gleiche Locations ablehnt. Zeilen
eines anderen Hosts kommen in einen eigenen `server`-Block, dem noch die `listen`-Zeilen der Website hinzuzufügen
sind. `location =` vergleicht den Pfad exakt, einschließlich eines abschließenden Schrägstrichs.

## Reguläre Ausdrücke und Platzhalter

Eine Umschreiberegel wird zum Platzhalter, wenn ihr Ausdruck einer ist, den ein Platzhalter sagen kann:

- am Anfang mit `^` verankert; ein `/` oder `/?` danach ist optional (Regeln pro Verzeichnis haben keinen führenden
  Schrägstrich)
- wörtlicher Text, mit `\` vor Satzzeichen; ein nicht maskierter `.` wird als wörtlicher Punkt gelesen, wie ihn fast
  jede Regel mit einem Punkt meint
- die Gruppen `(.*)`, `(.+)`, `(.*?)`, `(.+?)` und `([^/]+)`, `([^/]*)`, jede ein `*` (die letzten beiden mit einer
  Notiz: ein `*` passt auch auf `/`); ein alleinstehendes `.*` ist ebenfalls ein `*`
- ein optionaler abschließender Schrägstrich (`/?`) vor dem Ende, der entfällt
- `$` am Ende; ohne es passt der Ausdruck auf jeden Pfad, der mit ihm beginnt, also ein `*` am Ende

`$1`, `$2` im Ziel werden zu den Platzhaltern des `*` ihrer Gruppe (ein alleinstehendes `.*` zählt als `*`, aber
nicht als Gruppe, daher werden die Nummern umgerechnet). Alles andere, etwa `\d`, `[a-z]`, `a|b`, `x?`, `x+`, `{2}`
oder `(?:...)`, macht die Regel nicht importierbar, mit dem Konstrukt im Grund genannt.

Wird eine Zuordnung als Regeln geschrieben, wird jedes `*` zu `(.*)` und jedes `{N}` zu `$N`; wörtlicher Text wird
maskiert.

## Die Wahl des Formats

`expUrlMapFormats` wählt Leser und Schreiber: nach einem ausdrücklichen Formatnamen (`csv`, `json`, `xlsx`, `ods`,
`apache`, `nginx`), sonst nach der Endung der Datei, sonst nach ihrem Inhalt (ein ZIP-Archiv ist .xlsx, `[` oder `{`
zuerst ist JSON, Apache- oder nginx-Direktiven, sonst CSV). ODS kann nur geschrieben werden.

```php
$rows = expUrlMapFormats::readFile( 'old-site.htaccess' );
foreach ( $rows as $row )
{
    if ( !$row->isImportable() )
        echo $row->line(), ': ', implode( '; ', $row->problems() ), "\n";
}
expUrlMapFormats::writeFile( 'map.xlsx', $rows );
```

## Verwandte Seiten

- [Das Migrationsdateiformat](migrationsdateiformat.md)
- [Spezifikationen 6.0](README.md)
- [Änderungsprotokoll 6.0.15](../../../changelogs/6.0/6.0.15.md)
