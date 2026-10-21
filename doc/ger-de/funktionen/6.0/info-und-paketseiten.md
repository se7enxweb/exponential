# Die Seiten Über und Copyright

Diese Seite ist für Administratoren, die wissen müssen, welches Exponential sie betreiben, woraus es besteht und unter
welchen Lizenzen, und für Betreuer von Extensions, die möchten, dass ihre Extension richtig erscheint. **Info > Über** und
**Info > Copyright** in der Administrationsoberfläche beantworten diese Fragen. Zwischen dem 27. und 30. September 2026
wurden sie als richtige Seiten des Admin-Designs neu gebaut und vertrauenswürdig gemacht: Jede Extension wird mit einer
Beschreibung, Lizenz und Website aufgeführt, gleich, wie ihre Metadaten aussehen.

## Die Seite Über lesen

Öffnen Sie **Info > Über** (`/ezinfo/about`). Sie sehen:

- eine Zusammenfassung mit der Version, der Anzahl der Extensions und der Komponenten von Drittanbietern und Links zu
  den Abschnitten;
- **What is Exponential**, die Lizenz in einem scrollbaren Block und den Copyright-Hinweis;
- **Software von Drittanbietern** als Tabelle: Software, Version, Lizenz und die Extension, die sie enthält;
- **Extensions** als Tabelle, die Sie nach Ladereihenfolge, Name, Bezeichner, Version und Lizenz sortieren können.
  Unter jedem Namen: Beschreibung, Copyright, Autor und enthaltene Software, mit einem Link zu ihrer Website.

Die Tabellen werden zu Karten, wenn die Inhaltsspalte schmal ist, sodass keine der beiden Seiten auf einem Telefon oder
bei 200 Prozent Zoom seitlich scrollt. Die Stile stehen in `stylesheets/ezinfo.css`, das nur diese beiden Seiten laden.
Jeder Text ist übersetzbar (Englisch, `eng-US`, und Deutsch sind vollständig).

## Die Seite Copyright lesen

Öffnen Sie **Info > Copyright** (`/ezinfo/copyright`). Sie zeigt Copyright, Lizenz, Gewährleistung und ursprüngliche
Autoren als Abschnitte in der Sprache der Oberfläche, darunter den Hinweis, wie er verbreitet wird (Englisch). Sie nennt
das Copyright von Exponential und die GNU General Public License Version 2 (oder jede spätere Version), den
Gewährleistungsausschluss und bewahrt die Nennung der ursprünglichen Entwickler, die die Lizenz verlangt. Sie bietet
keine Lizenz für die geschäftliche Nutzung mehr an, die Exponential nicht gewähren kann.

Designs können den Hinweis selbst darstellen: Die Seite wird über das Template `ezinfo/copyright` ausgegeben, mit dem
einfachen Hinweis als Rückfall. Die Ansichten Über und Copyright geben Designs außerdem Tabellenzeilen und die Teile des
Hinweises.

## Ihre Extension gut erscheinen lassen

Geben Sie `Name`, `Version`, `Copyright`, `License` und `Info_url` in `ezinfo.php` an und dasselbe in `extension.xml`.
Die Release-Regel lautet, dass die Nummer in der Extension der Release-Nummer entspricht. Siehe
[Extension-Metadaten](../../spezifikationen/6.0/extension-metadaten.md).

Der RAD-Extension-Assistent benennt die Klasse einer erzeugten `ezinfo.php` nach der Extension (`<extension>Info`), wie
der Kernel es erwartet, sodass Extensions mit einem Unterstrich im Namen ihre Angaben zeigen.

## Wie die Zeile einer Extension gefüllt wird

`eZExtension::extensionInfo()` sammelt die Angaben in dieser Reihenfolge, und jede Quelle füllt nur die Felder, die die
früheren nicht geliefert haben:

1. `<metadata>` von `extension.xml` und `ezinfo.php`
2. die oberste Ebene von `extension.xml` (Zusammenfassung, Lizenz, Version, Copyright), für eine `extension.xml` ohne
   `<metadata>`
3. `composer.json`: Beschreibung, Lizenz, Version, Support-Quelle oder Homepage, Autoren
4. die https-Adresse des Git-Origins in `.git/config` (gelesen, nie ausgeführt)

Eine Extension, die keinen Namen oder nur ihren Verzeichnisnamen angibt, wird unter dem Namen aufgeführt, mit dem ihre
Beschreibung beginnt (zum Beispiel ergibt „Exponential Layouts - ...“ „Exponential Layouts“), sonst wie bisher unter
ihrem Verzeichnisnamen. Eine fehlerhafte `ezinfo.php` zerstört die Seite Über nicht mehr.

Die mit dem Kernel ausgelieferten Extensions `ezjscore`, `ezoe` und `ezformtoken` geben jetzt die Version von
Exponential an, mit der sie ausgeliefert werden, nennen ihre Lizenz vollständig („GNU General Public License v2.0 (or
any later version)“) und verlinken auf ihre Repositorys. Etwa zwei Dutzend Extensions veröffentlichten im selben Zug
neue Versionen, sodass jede Zeile der Seite Über eine Version, Lizenz und Website hat.

## Verwandte Seiten

- [Spezifikation der Extension-Metadaten](../../spezifikationen/6.0/extension-metadaten.md)
- [Pakete vergleichen und importieren](pakete-vergleichen-und-importieren.md), [Paketlizenzen und Semantic Versioning](paketlizenzen-und-versionen.md)
- [Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md)
- [Das Produkt heißt Exponential](umbenennung-in-exponential.md), [eine saubere Installation, die zu Shared Hosting passt](standardwerte-einer-sauberen-installation.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../bc/6.0/verhaltensaenderungen-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [16. bis 30. September 2026](../../geschichte/2026/2026-09b.md), [August 2025](../../geschichte/2025/2025-08.md), [Juli 2025](../../geschichte/2025/2025-07.md), [Januar 2025](../../geschichte/2025/2025-01.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
