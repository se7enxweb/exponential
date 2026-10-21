---
title: "Lektion 3.2: Der ezjscore-Packer"
audience: trainer, designer, developer
category: training
order: 32
summary: Die Teilnehmenden erklären, was der Packer auf jeder Stufe tut, schalten ihn für einen Siteaccess aus und wieder ein, um den Seitenquelltext zu vergleichen, finden die gepackten Dateien auf der Festplatte und lesen ihre Namen. 45 Minuten.
keywords: theme-design, ezjscore, packer, Packer, packstufe, DevelopmentMode, AppendLastModifiedTime, CustomHosts, CssOptimizer, JavaScriptOptimizer, gepackte datei, cache/public, ezjscore.ini, theme design, pack level, packed file
type: help
---
# Lektion 3.2: Der ezjscore-Packer

Der Packer macht aus den Listen von Lektion 3.1 so wenige Dateien wie möglich, minimiert sie und schreibt den
Änderungszeitpunkt in jeden Dateinamen. Ist er ausgeschaltet, wird jede Datei einzeln verlinkt, was die Fehlersuche
erleichtert. Designer wechseln viele Male am Tag zwischen beiden; diese Lektion gibt ihnen Sicherheit darüber, in
welchem Modus sie sich befinden und warum.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 3: Stylesheets und Skripte](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Frontend-Entwickler |
| Voraussetzungen | [Lektion 3.1](dateien-in-design-ini-auflisten.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- beschreiben, was der Packer auf den Stufen 0, 1, 2 und 3 tut;
- am Seitenquelltext erkennen, ob der Packer eingeschaltet ist;
- ihn mit `[eZJSCore] Packer` für einen Siteaccess aus- und einschalten;
- eine gepackte Datei auf der Festplatte finden und den Änderungszeitpunkt in ihrem Namen lesen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Stylesheets und Skripte für Ihre Website, der Packer](../../../../../eng-us/help/creators/website-creators/stylesheets-and-scripts.md#the-packer)
und `extension/ezjscore/settings/ezjscore.ini` (zeigen Sie die Datei am Projektor).

1. **Was er tut** (5 Minuten). Die Stufen, wie der Code sie anwendet:

   | Stufe | Ergebnis |
   |---|---|
   | 0 | kein Packen: Jede Datei wird unter ihrer eigenen Adresse verlinkt |
   | 1 | die Dateien eines Aufrufs werden zu einer Datei zusammengefasst |
   | 2 | zusammengefasst und durch die Optimierer geleitet (`JavaScriptOptimizer[]`, `CssOptimizer[]`) |
   | 3 | wie 2, mit den weiteren Schritten des Stylesheet-Optimierers |

   Stylesheets verwenden standardmäßig Stufe 3, Skripte Stufe 2.
2. **Wer entscheidet** (4 Minuten). `ezjscore.ini [eZJSCore] Packer` ist standardmäßig auskommentiert; dann folgt der
   Packer `site.ini [TemplateSettings] DevelopmentMode`: `enabled` bedeutet Stufe 0, `disabled` (der Standard)
   bedeutet Packen. Setzen Sie `Packer=disabled` (oder `0`), um ihn zwangsweise auszuschalten, oder eine Zahl von `0`
   bis `3`, um eine Stufe zu erzwingen.
3. **Der Name** (3 Minuten). `[Packer] AppendLastModifiedTime=enabled`: Der neueste Änderungszeitpunkt der Dateien ist
   Teil des Namens der gepackten Datei, etwa `<hash>_<time>_all.css`. Eine geänderte Datei ergibt einen neuen Namen,
   sodass Browser sie holen, obwohl gepackte Dateien lange zwischengespeichert werden. Die gepackten Dateien liegen im
   Cache-Verzeichnis der Installation, in `public/stylesheets/` und `public/javascript/`.
4. **Andere Hosts** (2 Minuten). `[Packer] CustomHosts[.css]` und `[.js]` liefern gepackte Dateien von einem anderen
   Host aus. Erwähnen Sie es; es ist eine Entscheidung des Hostings.

### Häufige Fragen

**Warum verlinkt die Seite nach einer Änderung die Datei zweimal mit verschiedenen Namen?** Das tut sie nicht; der alte
Name steckt in einer zwischengespeicherten Seite oder einem Cache-Block. Lektion 3.3 leert die richtigen Caches.

**Sollte die Produktion mit `DevelopmentMode=enabled` laufen?** Nein. Es schaltet das Packen aus und liefert jede Datei
einzeln aus. Behalten Sie es Entwicklungs-Siteaccesses vor.

**Wo sehe ich, was gepackt wurde?** Bei eingeschalteter Debug-Ausgabe hat der Debug-Bericht einen Abschnitt
„CSS/JS files loaded with 'ezjscPacker' during request“, der jede gepackte Datei und ihre Stufe auflistet.

### Stolperfallen

- **Den Packer für jeden Siteaccess umschalten.** Verwenden Sie den Siteaccess-Bereich; die Administration sollte
  gepackt bleiben.
- **Die gepackte Datei bearbeiten.** Sie wird erzeugt und bei der nächsten Änderung ersetzt. Bearbeiten Sie die Quelle
  im Design.
- **Die beiden Einstellungsdateien verwechseln.** `Packer` steht in `ezjscore.ini`, `DevelopmentMode` in `site.ini`.

## Praktische Übung

Auf Ihrer Schulungsinstallation, `<site>` wird von `training_theme` gezeichnet.

1. Öffnen Sie eine Seite von `<site>` und ihren Seitenquelltext. Notieren Sie die Elemente `<link rel="stylesheet">`
   und `<script>`: Wie viele gibt es, und wie sehen ihre Dateinamen aus?
2. Schalten Sie den Packer nur für Ihren Siteaccess aus:

   ```bash
   php bin/php/console exp:ini set ezjscore.ini/eZJSCore/Packer disabled siteaccess:<site>
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-tag=template
   php bin/php/ezcache.php --clear-id=template-block
   ```

3. Laden Sie den Seitenquelltext neu. Notieren Sie die Stylesheet- und Skriptelemente erneut und vergleichen Sie mit
   Schritt 1.
4. Öffnen Sie `training.css` über seinen Link: Es ist Ihre Datei, genau so, wie Sie sie geschrieben haben.
5. Schalten Sie den Packer wieder ein, indem Sie die Einstellung entfernen:

   ```bash
   php bin/php/console exp:ini rem ezjscore.ini/eZJSCore/Packer siteaccess:<site>
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-tag=template
   php bin/php/ezcache.php --clear-id=template-block
   ```

6. Laden Sie den Seitenquelltext neu: Sie sind wieder beim Ergebnis von Schritt 1.
7. Suchen Sie das gepackte Stylesheet auf der Festplatte. Entnehmen Sie seinen Dateinamen dem Seitenquelltext und
   suchen Sie danach: `find var -name '<file name>'`.
8. Öffnen Sie die Datei in einem Editor: die Regeln mehrerer Stylesheets, minimiert, eine nach der anderen.
9. Lesen Sie die Zahl zwischen den beiden Unterstrichen des Namens. Führen Sie `stat -c %Y` für Ihre `training.css`
   und für die anderen Quell-Stylesheets aus und vergleichen Sie.

## Erwartetes Ergebnis

- Schritt 1: ein gepackter Stylesheet-Link für die Liste im Head und ein gepacktes Skript für die Liste im Body, mit
  langen Hash-Namen, die auf `_all.css` und `.js` enden.
- Schritt 3: ein Link pro Quelldatei, unter den eigenen Adressen der Dateien in den Design-Ordnern.
- Schritt 9: Die Zahl im Namen ist der neueste Änderungszeitpunkt unter den Quelldateien dieses Bündels.
- Der Trainer prüft, dass der Packer bei allen Teilnehmenden am Ende wieder eingeschaltet ist
  (`exp:ini where ezjscore.ini/eZJSCore/Packer <site>` zeigt keinen Siteaccess-Eintrag).

## Quiz

1. `Packer` ist in `ezjscore.ini` nicht gesetzt. Was entscheidet, ob Dateien gepackt werden?
2. Was bedeutet Stufe 0?
   A. Gepackt und minimiert B. Zusammengefasst, aber nicht minimiert C. Jede Datei einzeln verlinkt D. Dateien nicht
   geladen
3. Warum ist der Änderungszeitpunkt Teil des Namens der gepackten Datei?
4. Welche Einstellung liefert gepackte Skripte von einem anderen Host aus?
5. Eine Teilnehmerin bearbeitet eine gepackte Datei im Cache-Verzeichnis, um eine Farbe zu korrigieren. Warum ist das
   falsch?

<details>
<summary>Antworten</summary>

1. `site.ini [TemplateSettings] DevelopmentMode`: `enabled` schaltet das Packen aus, `disabled` lässt es eingeschaltet.
2. **C.**
3. Damit eine geänderte Datei einen neuen Namen bekommt und Browser sie holen, obwohl gepackte Dateien lange
   zwischengespeichert werden (`[Packer] AppendLastModifiedTime=enabled`).
4. `ezjscore.ini [Packer] CustomHosts[.js]`.
5. Die gepackte Datei wird erzeugt; die nächste Änderung oder das nächste Leeren des Caches ersetzt sie. Die Korrektur
   gehört ins Quell-Stylesheet.

</details>

## Weiterführende Literatur

- [Stylesheets und Skripte für Ihre Website](../../../../../eng-us/help/creators/website-creators/stylesheets-and-scripts.md): die
  Einstellungen des Packers in einer Tabelle.
- [ezjscore](../../../../../eng-us/features/6.0/extensions/ezjscore.md): die Erweiterung hinter dem Packer.
- [Leistung und Caching für Websites](../../../../../eng-us/help/creators/website-creators/performance-and-caching.md): was der Packer
  für die Ladezeit tut.

## Verwandte Seiten

- [Modul 3: Stylesheets und Skripte](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
