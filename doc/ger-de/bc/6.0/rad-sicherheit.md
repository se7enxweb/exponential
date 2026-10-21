# RAD-Werkzeuge: Sicherheit

Lesen Sie diese Seite, wenn Sie Administratoren die RAD-Werkzeuge verwenden lassen (die Assistenten für Erweiterungen
und Module unter `setup/rad`) oder wenn Sie Erweiterungen bereitstellen, die damit erzeugt wurden. Ein RAD-Werkzeug
nimmt, was jemand eingegeben hat, und schreibt es in Dateien, die **bereitgestellt und eingebunden** werden. Jedes Feld
ist daher ein Codepfad, und jede erzeugte Datei gehört zur Angriffsfläche jeder Installation, auf der sie landet, nicht
nur dieser. Diese Seite führt die acht Schutzmaßnahmen auf (RAD-01 bis RAD-08) und was sie für Sie bedeuten.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | In die Assistenten eingegebene Werte können nicht mehr aus Kommentaren oder INI-Zeilen ausbrechen, außerhalb von `extension/<name>` schreiben, eine Erweiterung überschreiben oder ein Datenbankpasswort preisgeben. Erzeugte Templates maskieren ihre Ausgabe, und erzeugte Views validieren ihre Eingabe. |
| Wer betroffen ist | Alle, die vor der Korrektur eine Erweiterung mit den Assistenten erzeugt haben (RAD-01 war eine echte Lücke). |
| Wie Sie es prüfen | Prüfen Sie erzeugte Dateien `ezinfo.php` und `<name>operators.php` auf Code nach einem schließenden `*/`. |
| Wie Sie es beheben | Erzeugen Sie ältere Erweiterungen mit dem aktuellen Assistenten neu, oder prüfen Sie ihre Kommentare von Hand. Lassen Sie die Erweiterung `ezformtoken` aktiv. |

## Zwei Wächter an der Tür

Die Werte werden dort geprüft, wo sie angenommen werden, nicht an jeder der mehreren Dutzend Stellen, an denen sie
geschrieben werden (`kernel/setup/expextensionwizard.php`):

| Wächter | Was er absichert |
|---|---|
| `expExtensionWizard::commentText()` | Was in einem erzeugten PHP-Kommentar stehen darf. |
| `expExtensionWizard::iniValue()` | Was rechts in einer INI-Einstellung stehen darf. |

`expExtensionWizard::text()`, durch das jedes Freitextfeld geht, ruft den ersten auf. Ein Wert, der nie in den
Assistenten gelangt, kann ihn nicht verlassen.

## RAD-01: Ein Wert kann den Kommentar, in dem er steht, nicht schließen

Eine INI-Datei von Exponential ist eine PHP-Datei, deren gesamter Inhalt ein Kommentar ist:

```php
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
ActiveExtensions[]=my_extension
*/ ?>
```

Erzeugtes PHP setzt Titel, Autor und Zusammenfassung in einen Doc-Kommentar. Ein Wert, der `*/` enthält, **schließt
diesen Kommentar**, und alles danach ist PHP, in einer Datei, die eingebunden wird.

Das war eine echte Lücke. Vor der Korrektur ergab ein Autor wie

```
*/ ?><?php echo "OWNED"; /*
```

Dateien `ezinfo.php` und `<name>operators.php`, die beim Einbinden **ausgeführt wurden**; das wurde bestätigt, indem
sie eingebunden und die Ausgabe gelesen wurde. Der Assistent ist nur für Administratoren, es war also eine
Rechteausweitung von „darf die RAD-Werkzeuge verwenden“ zu „beliebiges PHP“. Und weil die Erweiterung ein Lieferobjekt
ist, wanderte die Lücke mit ihr auf jede Installation, auf der sie bereitgestellt wurde.

`commentText()` schreibt `*/` um (und `**/` sowie jede Folge von Sternen vor dem Schrägstrich) und entschärft `<?` und
`?>`. Der Test schickt sechs Nutzlasten durch beide Assistenten, prüft jede erzeugte Datei mit lint, **bindet sie
ein** und stellt sicher, dass nichts von dem geschieht, was die Nutzlast verlangt hat.

## RAD-02: Ein Wert kann nicht zu einer eigenen INI-Einstellung werden

Ein Zeilenumbruch in einem Wert beendet die Einstellung; was folgt, wird als weitere gelesen. Ein Titel wie

```
harmless
ActiveExtensions[]=evil
```

würde sonst eine aktive Erweiterung hinzufügen. `iniValue()` macht aus jedem Zeilenende ein Leerzeichen und begrenzt
die Länge.

## RAD-03: Erzeugtes PHP ist harmlos, was auch immer eingegeben wurde

Eine zweite Verteidigungslinie hinter RAD-01: Im Test wird jede erzeugte `.php`-Datei mit lint geprüft **und
ausgeführt**, für jede Nutzlast, in beiden Assistenten. Eine Datei, die sich parsen lässt, aber etwas tut, ist so
schlecht wie eine, die sich nicht parsen lässt.

## RAD-04: Nichts wird außerhalb von `extension/<name>` geschrieben

- Der Name wird auf `[a-z][a-z0-9_]{2,40}` beschränkt: `../../../tmp/evil` wird zu `tmp_evil`, `/etc/cron.d/evil` zu
  `etc_cron_d_evil`, `..` zu nichts.
- Vom aufgelösten Ziel wird erneut geprüft, dass es unter `extension/` liegt, bevor ein Byte geschrieben wird.
- Der Test durchläuft, was auf der Festplatte gelandet ist, und stellt sicher, dass es **genau** dem entspricht, was
  die Vorschau gezeigt hat: keine Datei mehr, keine weniger.
- Geschriebene Dateien erhalten `chmod 0644`; die umask dessen, was die Anfrage ausgeführt hat, ist keine
  Berechtigungsrichtlinie. Der Test stellt sicher, dass nichts für Gruppe oder Welt beschreibbar ist.
- Eine Datenbank, die eine Datei ist, wird genauso eingegrenzt: `safePath()`
  (`kernel/setup/expmoduleextensionwizard.php`) lehnt `..`, absolute Pfade außerhalb der Installation und jeden Pfad
  mit einem Null-Byte ab.

## RAD-05: Eine vorhandene Erweiterung wird nie überschrieben

Das Schreiben verweigert, und sagt das, wenn `extension/<name>` existiert. Ein Assistent, der ein Design überschreiben
kann, das jemand verwendet, ist ein Unfall, der nur auf einen eingegebenen Namen wartet. Das Archiv wird trotzdem
angeboten, denn jemandem eine Kopie zum Vergleichen zu geben, schadet nicht.

## RAD-06: Erzeugte Templates maskieren, was sie ausgeben

Jedes `{$…}` in einem erzeugten Template geht durch `wash`, `ezurl`, `i18n` oder `l10n`, **einschließlich Werten, die
das Template selbst erzeugt hat**, etwa eines Schleifenzählers oder einer Klasse für abwechselnde Zeilenfarben. Eine
Regel mit Ausnahmen ist eine, über die jemand bei jeder Bearbeitung der Datei nachdenken muss. Der Test parst jedes
erzeugte Template und schlägt bei jedem ausgegebenen Wert fehl, der nicht maskiert ist.

## RAD-07: Erzeugte Views akzeptieren nur, was sie anbieten

Das erzeugte Admin-Modul nimmt aus der Adresse einen Tabellennamen, eine Sortierspalte, eine Seitengröße und einen
Offset. Jeder Wert wird gegen das geprüft, was die Erweiterung selbst deklariert:

- die Tabelle gegen die erzeugte Registry; alles andere ist ein ordentliches 404, keine Abfrage;
- die Sortierspalte gegen die Klassendefinition;
- die Seitengröße gegen `25 / 50 / 250`;
- der Offset wird auf die Liste und auf eine Seitengrenze begrenzt.

Die Bearbeitungsansicht schreibt nur Spalten, die die Tabelle hat, und wandelt jeden Wert in den Typ der Spalte um.
Das Entfernen fragt zuerst nach und handelt nur bei einem bestätigten POST.

Der Schutz von POST-Anfragen kommt von der Erweiterung `ezformtoken`, die bei jedem POST eines angemeldeten Benutzers
ein Token pro Session prüft und das Feld jedem Formular hinzufügt. Ein erzeugtes Modul erbt das, und die erzeugte
README sagt es, denn eine Installation ohne diese Erweiterung hat überall ungeschützte POST-Anfragen, nicht nur hier.

## RAD-08: Geheimnisse werden nicht erneut angezeigt oder in die Erweiterung geschrieben

- Das Passwort der externen Datenbank wird **nie** in das Formular zurückgegeben. Ein Passwort im `value` eines
  Eingabefeldes ist ein Passwort im Quelltext der Seite, im Browser-Cache und in jedem Proxy dazwischen.
- Es wird **nicht** in die erzeugte INI-Datei geschrieben. Die Datei enthält `Password=` und einen Kommentar, der auf
  `settings/override/<name>.ini.append.php` verweist, das außerhalb der Versionsverwaltung gehört.

## Wogegen bewusst nicht geschützt wird

Ein Administrator, der die RAD-Werkzeuge verwenden darf, kann bereits auf andere Weise Code auf dem Server ausführen:
ein Template schreiben, eine Erweiterung installieren, eine INI-Datei bearbeiten. Diese Schutzmaßnahmen gibt es, weil
eine erzeugte Erweiterung **diese Installation verlässt** und ihre Lücken mit ihr wandern.

Sich mit einem beliebigen Host und Port zu verbinden, ist der Sinn der Option für eine externe Datenbank, daher ist
das nicht eingeschränkt. Auf einem Host, wo das wichtig ist, lassen Sie weniger Personen an den Assistenten (die
Richtlinie `setup/setup`), statt ihn unbrauchbar zu machen.

## Die Tests ausführen

Die RAD-Sicherheitsprüfungen laufen auf dem eigenen Code der Installation und schreiben nichts außerhalb von
`var/tmp`. Sie decken die obigen Punkte ab: Validierung von Pfaden, Maskierung in erzeugten Templates und den Umgang
mit dem Passwort der externen Datenbank. Fragen Sie die Maintainer nach dem Testskript des Release, das Sie betreiben.

## Verwandte Seiten

- [RAD-Werkzeuge](../../../eng-us/features/6.0/rad-tools.md)
- [RAD-Erweiterungspunkte](rad-erweiterungspunkte.md) und [die RAD-Erweiterungsoberfläche](rad-erweiterungsoberflaeche.md)
- [Spezifikation: die Sicherheits- und Stabilitätshärtung von 6.0.13](../../spezifikationen/6.0/sicherheitshaertung-6.0.13.md)
- [Spezifikation: die Sicherheitspatches vom August 2026](../../spezifikationen/6.0/sicherheitshaertung-2026-08.md)
- [Sicherheitsvorgaben vom September 2026](../../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md)
- [Härtung von Datentypen und Eingaben (27. September 2026)](../../spezifikationen/6.0/datentyp-eingabehaertung.md)
- [Februar 2026](../../../eng-us/history/2026/2026-02.md)
