---
title: "Lektion 4.1: Warum Links auf eine andere Website kaputtgehen"
audience: trainer, admin, developer
category: training
order: 41
summary: Die Teilnehmenden erklären, warum ein Link von einer Website einer Multisite auf Inhalte einer anderen ohne Hilfe mit 404 antwortet und wie die Erweiterung ezurlmultisite den Siteaccess wählt, der einen Knoten ausliefert. 35 Minuten.
keywords: multisite und seitenpakete, websiteübergreifende links, url-alias, multisite-demo, multisite and site packages, cross-site links, ezurlmultisite, PathPrefix, ezurl, URL alias, 404, kernel (20), RootNodeList, SiteAccessList, SiteURL, multisite demo
type: help
---
# Lektion 4.1: Warum Links auf eine andere Website kaputtgehen

Wenn mehrere Websites einen Inhaltsbaum teilen, listen Seiten einer Website regelmäßig Inhalte einer anderen auf: eine
Tag-Seite, ein Suchergebnis, ein verwandtes Element. Ohne Hilfe zeigen diese Links in die falsche Website und
antworten mit "nicht gefunden". Diese Lektion erklärt die Ursache und die Regel, mit der die Erweiterung
`ezurlmultisite` das behebt, bevor Lektion 4.2 sie konfiguriert.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 4: Links zwischen Websites](README.md) |
| Dauer | 35 Minuten (20 Präsentation, 15 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Lektion 2.1](../modul-02-anfragen-zuordnen/zuordnung-nach-url-pfad.md): `PathPrefix` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, wie `PathPrefix` und der Template-Operator `ezurl` zusammen einen kaputten Link auf eine andere Website
  erzeugen;
- beschreiben, wie `ezurlmultisite` die Website findet, die einen Knoten oder einen URL-Alias ausliefert;
- angeben, welche Links die Erweiterung unverändert lässt;
- die Adresse eines websiteübergreifenden Links auf demselben Host und auf einem anderen Host vorhersagen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Links auf Inhalte einer anderen Website einer Multisite-Installation](../../../../../eng-us/features/6.0/multisite-cross-site-links.md).

1. **Das Beispiel** (5 Minuten). Die Multisite-Demo liefert Fit & Healthy unter `/site` (oder `/`) und Bold Agency
   unter `/bold` aus, aus einem Baum. Jede Website blendet den Pfad ihres Teilbaums mit `PathPrefix` aus. Ein Knoten
   der anderen Website behält den Pfad dieser Website in seinem URL-Alias, und `ezurl` setzt den aktuellen Siteaccess
   davor: `/bold/fit-healthy/video/...`, was nicht existiert (`kernel (20)`, Modul nicht gefunden).
2. **Die Regel** (8 Minuten). Mit aktiver Erweiterung wird ein Link, der auf einem teilnehmenden Siteaccess erzeugt
   wird, so aufgelöst:
   1. für einen Knoten oder ein Objekt ist die Website die, deren Wurzelknoten auf dem Pfad des Knotens am tiefsten
      liegt (`RootNodeList`); für einen URL-Alias als Zeichenkette die Website, mit deren `PathPrefix` der Alias
      beginnt, das längste Präfix zuerst;
   2. ist das eine andere Website, wird die Adresse aus der `SiteURL` dieses Siteaccess gebildet, ohne ihren
      `PathPrefix`: hostrelativ auf dem aktuellen Host (`/bold/services`, wobei Schema und Port erhalten bleiben),
      absolut auf einem anderen Host (`https://bold.example.com/services`);
   3. eine Website und ihre Übersetzungen teilen einen Teilbaum: Der aktuelle Siteaccess gewinnt, dann einer in der
      aktuellen Locale, dann der erste in `SiteAccessList[]`.
3. **Was gleich bleibt** (3 Minuten). Links innerhalb der aktuellen Website, Modul-Links (`user/login`,
   `content/search`), Dateien und Design-Pfade sowie jeder Link, der auf einem nicht teilnehmenden Siteaccess erzeugt
   wird, wozu jeder Administrations-Siteaccess gehört.
4. **Kosten** (2 Minuten). Keine Datenbankabfrage pro Link; die Siteaccess-Tabelle wird einmal pro Anfrage gelesen
   (unter Exponential Velocity einmal pro Worker).

### Häufige Fragen

**Brauche ich sie, wenn jede Website eigene Inhalte hat?** Nur wenn Seiten websiteübergreifend verlinken. Eine
Website, die nie Inhalte einer anderen auflistet, braucht sie nicht, aber sie kostet nichts.

**Ist sie Teil des Kernels?** Sie ist die Erweiterung `ezurlmultisite` (`se7enxweb/ezurlmultisite`), die von der
`composer.json` im Wurzelverzeichnis verlangt wird; auf einer Standardinstallation ist sie also vorhanden.

**Was ist mit den eigenen Link-Operatoren des Medien-Themes?** `exp_path`, `exp_url`, `content_link` und ähnliche
geben die Adresse auf dem ausliefernden Siteaccess so an, wie `ezurl` es tut.

**Ändert sie Links in der Administration?** Nein: Administrations-Siteaccesses nehmen nicht teil.

### Stolperfallen

- **In der Administration testen.** Websiteübergreifende Links zeigen sich nur auf öffentlichen Seiten; öffnen Sie
  die öffentliche Website.
- **Den Inhalten die Schuld geben.** Ein 404 bei einem websiteübergreifenden Link ist eine Frage der Konfiguration,
  kein kaputtes Objekt. Prüfen Sie zuerst die Einstellungen der Erweiterung (Lektion 4.2).
- **Zwischengespeicherte Seiten.** Kompilierte Templates enthalten den `ezurl`-Code des Kernels, bis sie erneut
  kompiliert werden; leeren Sie nach dem Aktivieren der Erweiterung den Template- und den Inhalts-Cache.

## Praktische Übung

Eine Lese- und Vorhersageübung zu zweit, mit geöffneter Feature-Seite. Eine Prüfung in der Übungsumgebung folgt in
Lektion 4.2.

Gegeben ist eine Installation mit den Siteaccesses `site` (`PathPrefix=fit-healthy`, Wurzelknoten 97, `SiteURL` auf
`www.example.com/site`), `bold` (`PathPrefix=bold-agency`, Wurzelknoten 81, `SiteURL` `www.example.com/bold`) und
`bold_ger` (die deutsche Übersetzung von `bold`, derselbe Wurzelknoten):

1. Ohne die Erweiterung: Eine Seite unter `/bold` listet ein Video, dessen URL-Alias `fit-healthy/video/morning-run`
   ist. Schreiben Sie den Link auf, den `ezurl` erzeugt.
2. Erklären Sie, warum diese Adresse mit 404 antwortet.
3. Mit der Erweiterung: Schreiben Sie den Link für dasselbe Video auf derselben Seite auf.
4. Eine Seite unter `/site` verlinkt auf den Knoten `bold-agency/services`. Schreiben Sie den Link auf.
5. Dieselbe Seite verlinkt auf `user/login`. Schreiben Sie den Link auf.
6. `bold` zieht auf einen eigenen Host `bold.example.com` um. Schreiben Sie den Link aus Schritt 4 erneut auf.
7. Ein Besucher auf `/bold_ger` sieht einen Link auf einen Knoten unterhalb von Knoten 81. Welcher Siteaccess liefert
   ihn aus, und warum?
8. Vergleichen Sie mit einem anderen Paar und klären Sie Unstimmigkeiten mit der Feature-Seite.

## Erwartetes Ergebnis

- Schritt 1: `/bold/fit-healthy/video/morning-run`; Schritt 2: Der Siteaccess `bold` hat weder einen solchen Alias noch
  ein solches Modul.
- Schritt 3: `/site/video/morning-run` (die Adresse auf dem Siteaccess, der es ausliefert, ohne sein Präfix).
- Schritt 4: `/bold/services`; Schritt 5: `/site/user/login`, unverändert.
- Schritt 6: `https://bold.example.com/services`, absolut, weil der Host abweicht.
- Schritt 7: `bold_ger`, weil der aktuelle Siteaccess gewinnt, wenn die Website und ihre Übersetzungen einen Teilbaum
  teilen.
- Der Trainer prüft das, indem er die Antworten der Schritte 3, 4 und 6 an der Tafel sammelt.

## Quiz

1. Welche zwei Dinge erzeugen zusammen den kaputten Link?
2. Welche Links lässt `ezurlmultisite` unverändert?
   A. Links innerhalb der aktuellen Website B. Modul-Links wie `user/login` C. In der Administration erzeugte Links
   D. Alle drei
3. Wie wird die Website eines Knotens gefunden?
4. Wann ist ein websiteübergreifender Link absolut?
5. Führt die Erweiterung pro Link eine Datenbankabfrage aus?

<details>
<summary>Antworten</summary>

1. Der `PathPrefix` der anderen Website bleibt im URL-Alias des Knotens, und `ezurl` setzt den aktuellen Siteaccess
   davor.
2. **D.** ([Links auf Inhalte einer anderen Website](../../../../../eng-us/features/6.0/multisite-cross-site-links.md#in-short))
3. Die Website, deren Wurzelknoten auf dem Pfad des Knotens am tiefsten liegt, aus `RootNodeList`.
4. Wenn die `SiteURL` der anderen Website auf einem anderen Host liegt; auf dem aktuellen Host ist er hostrelativ.
5. Nein; die Siteaccess-Tabelle wird einmal pro Anfrage gelesen (unter Velocity einmal pro Worker).

</details>

## Weiterführende Informationen

- [Links auf Inhalte einer anderen Website einer Multisite-Installation](../../../../../eng-us/features/6.0/multisite-cross-site-links.md).
- [Mehrere Websites in einer Installation einrichten: Links zwischen den Websites](../../../../../eng-us/help/creators/website-creators/multisite-setup.md#links-between-the-sites).
- [URL-Aliase und sprechende URLs](../../../../../eng-us/help/creators/website-creators/url-aliases-and-nice-urls.md).
- [Beziehungen und Wiederverwendung](../../../../../eng-us/help/creators/website-creators/relations-and-reuse.md): woher
  websiteübergreifende Links kommen.

## Verwandte Seiten

- [Modul 4: Links zwischen Websites](README.md)
- [Multisite und Seitenpakete: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
