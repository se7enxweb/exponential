---
title: "Lektion 1.4: Website-Designs und Administrationsdesigns"
audience: trainer, designer, developer
category: training
order: 14
summary: Die Teilnehmenden unterscheiden die öffentlichen Website-Designs von den Administrationsdesigns (admin, admin3, admin4, admin4l, editor), lesen die Kette der Administration und probieren mit dem Werkzeug Oberfläche ein anderes Administrationsdesign für ihre eigene Sitzung aus. 45 Minuten.
keywords: theme-design, website-design, administrationsdesign, admin, admin3, admin4, admin4l, editor, adminui, uiswitch, werkzeug oberfläche, admintest_admin4, theme design, site design, administration design, Interface tool
type: help
---
# Lektion 1.4: Website-Designs und Administrationsdesigns

Exponential zeichnet zwei Arten von Seiten: die öffentliche Website, die Besucher sehen, und die Administration, in der
Redakteure arbeiten. Beide bestehen aus Designs und folgen denselben Regeln der Kette, werden aber für unterschiedliche
Menschen gebaut und geändert. Diese Lektion trennt die beiden, damit die Teilnehmenden wissen, welche davon eine ihrer
Änderungen berührt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 1: Designs und die Fallback-Kette](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Designer und Frontend-Entwickler |
| Voraussetzungen | [Lektion 1.2](die-kette-eines-siteaccess-lesen.md); ein Administratorkonto auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die mitgelieferten Administrationsdesigns (`admin`, `admin3`, `admin4`, `admin4l`) und das Design `editor` nennen;
- die Design-Kette des Siteaccess der Administration lesen;
- das Administrationsdesign für die eigene Sitzung mit dem Werkzeug **Oberfläche** umschalten und wieder zurück;
- erklären, warum ein öffentliches Theme nie die Administration verändert und welche Regeln stattdessen für die
  Administration gelten.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Das Design admin4](../../../../../eng-us/features/6.0/admin4-design.md),
[admin4l](../../../../../eng-us/features/6.0/admin4l.md) und
[Das Administrationsdesign für Ihre Sitzung wählen](../../../../../eng-us/features/6.0/ui-switch-design.md).

1. **Zwei Welten, ein Mechanismus** (4 Minuten). Ein Siteaccess ist entweder eine öffentliche Website oder eine
   Administration. Beide haben ein `SiteDesign` und eine Kette. Ein öffentlicher Siteaccess verwendet ein
   Website-Design wie `plain` oder das Design eines Themes; die Administration verwendet ein Administrationsdesign.
2. **Die Administrationsdesigns** (6 Minuten). Zeigen Sie die Ordner und die Tabelle des Sitzungsschalters:

   | Design | Beschriftung im Schalter | Fällt zurück auf |
   |---|---|---|
   | `admin4l` | Admin 4 (Layouts) | admin4, admin3, admin2, admin |
   | `admin4` | Admin 4 | admin3, admin2, admin |
   | `admin3` | Admin 3 | admin2, admin |
   | `admin` | Klassisch | das Standard-Design |

   admin4 ist für sich vollständig, mit einem hellen und einem dunklen Modus; admin4l ist admin4, aus Layouts
   zusammengesetzt, und enthält nur, was abweicht. Der Siteaccess `editor` (`SiteDesign=editor`) ist die vereinfachte
   Administration für Redakteure und fällt zuerst auf `admin4l` zurück, dann auf `admin4`, `admin3`, `admin2`,
   `admin`. AdminUI (`adminui`) kommt mit der Erweiterung `exp_adminui`.
3. **Für sich selbst umschalten** (4 Minuten). In der rechten Spalte der Administration nennt das Werkzeug
   **Oberfläche** zuerst **Sprache der Oberfläche** und dann die Administrationsdesigns, eine Schaltfläche pro Design
   (nur für eine Rolle mit der Richtlinie `uiswitch/design` und wenn mindestens zwei Designs zur Wahl stehen). Die
   Wahl wirkt nur auf Ihre eigenen Anfragen, bis Sie sich abmelden, und die Wahl des eigenen Designs des Siteaccess
   hebt sie auf. Die Ansicht aller anderen ändert sich nicht, es wird keine Datei geschrieben.
4. **Für alle umschalten** (3 Minuten). Ein Administrator setzt `SiteDesign` für den Siteaccess der Administration,
   zum Beispiel `admin4`, und leert die Caches. Zeigen Sie den Befehl aus dem Leitfaden, führen Sie ihn in der
   Schulung aber nicht aus:

   ```bash
   php bin/php/console exp:ini set site/DesignSettings/SiteDesign admin4 siteaccess:admin
   ```

5. **Andere Regeln** (3 Minuten). Öffentliche Themes sind das eigene Werk der Designer. Für Administrationsdesigns
   gelten strenge gemeinsame Regeln: kein Inline-CSS, gemeinsame Komponenten, Tokens mit einem dunklen Wert. Modul 4
   unterrichtet sie.

### Häufige Fragen

**Verändert mein öffentliches Theme die Administration?** Nicht, wenn `SiteDesign` pro Siteaccess gesetzt ist. Es
geschieht, wenn jemand es in `settings/override/` setzt (Lektion 1.2).

**Warum sehe ich die Design-Schaltflächen im Werkzeug Oberfläche nicht?** Der Rolle fehlt `uiswitch/design`, oder es
steht nur ein Design zur Wahl. Fragen Sie den Trainer, der es mit einem Administratorkonto zeigen kann.

**Was ist `admintest_admin4`?** Auf Installationen, die ihn haben, ein Siteaccess, um admin4 auszuprobieren, ohne die
Hauptadministration zu ändern ([Templates und Design, Teil 8](../../../../anleitungen/templates-und-design.md#8-die-administrationsoberfläche-auf-admin4-umstellen)).

### Stolperfallen

- **Die Teilnehmenden schalten das Design der gemeinsamen Schulungsadministration für alle um.** Auf einer gemeinsamen
  Installation erlauben Sie nur den Sitzungsschalter; der Befehl `exp:ini set` gehört auf die eigene Installation
  jeder Person.
- **Erwarten, dass der Siteaccess editor wie die Administration aussieht.** Er ist absichtlich eine reduzierte
  Oberfläche; sein Aussehen kommt über den Fallback von `admin4l`/`admin4`.
- **Sprache der Oberfläche und Inhaltssprache verwechseln.** Das Werkzeug Oberfläche ändert die Sprache der
  Oberfläche, nicht die Sprache der Inhalte. Lektion 5.4 verwendet es für Rechts-nach-links.

## Praktische Übung

Auf Ihrer Schulungsinstallation, in der Administration (zum Beispiel `/admin`) angemeldet mit Ihrem
Schulungs-Administratorkonto.

1. Lesen Sie das Website-Design der Administration:
   `php bin/php/console exp:ini where site/DesignSettings/SiteDesign admin`. Notieren Sie den wirksamen Wert.
2. Lesen Sie ihre zusätzlichen Designs:
   `php bin/php/console exp:ini get site/DesignSettings/AdditionalSiteDesignList admin`.
3. Schreiben Sie die Kette der Administration auf Ihrem Blatt aus Lektion 1.2 unter die öffentliche Kette.
4. Öffnen Sie im Browser das Dashboard der Administration. Suchen Sie das Werkzeug **Oberfläche** in der rechten Spalte
   (öffnen Sie die rechte Spalte mit **Rechte Spalte** in der Kopfzeile, wenn sie geschlossen ist).
5. Notieren Sie, welche Schaltflächen für Administrationsdesigns angeboten werden und welche als aktuell markiert ist.
6. Klicken Sie auf ein anderes Design, zum Beispiel **Admin 3** oder **Klassisch**. Die Seite wird in diesem Design
   neu geladen.
7. Vergleichen Sie die Kopfzeile und die linke Spalte mit dem Design, von dem Sie kamen. Notieren Sie zwei
   Unterschiede.
8. Öffnen Sie das Werkzeug **Oberfläche** erneut und klicken Sie auf das erste Design der Liste (das eigene des
   Siteaccess), um die Umschaltung aufzuheben.
9. Wenn die Kopfzeile die Schaltfläche für den Farbmodus hat (**Farbmodus: hell** in admin4 und admin4l), klicken Sie
   darauf und beobachten Sie, wie die Seite dunkel wird; klicken Sie so lange, bis sie den Modus zeigt, mit dem Sie
   begonnen haben.

## Erwartetes Ergebnis

- Das Blatt zeigt die öffentliche Kette und die Kette der Administration nebeneinander, mit verschiedenen
  Website-Designs.
- Schritt 6: Die Administration wird auf derselben Seite im anderen Design gezeichnet; Schritt 8 bringt das
  ursprüngliche Design zurück.
- Schritt 9: Der Dunkelmodus ändert Karten und Spalten ebenso wie den Seitenhintergrund.
- Der Trainer prüft, dass alle Teilnehmenden am Ende im eigenen Design des Siteaccess und in ihrem eigenen Farbmodus
  sind.

![Die Liste der Administrationsdesigns in der rechten Spalte, in der alle Benutzer ein Administrationsdesign wählen](../../../../../eng-us/help/images/trainers/td-design-switch.webp)

## Quiz

1. Welches Administrationsdesign ist für sich vollständig, mit einem hellen und einem dunklen Modus?
2. Eine Teilnehmerin schaltet im Werkzeug **Oberfläche** auf **Klassisch** um. Wer sieht das klassische Design?
   A. Alle Benutzer der Administration B. Nur diese Teilnehmerin, bis zur Abmeldung C. Alle Redakteure D. Die
   Besucher der Website
3. Auf welches Design fällt der Siteaccess `editor` zuerst zurück?
4. Welche Richtlinie braucht eine Rolle, damit die Design-Schaltflächen erscheinen?
5. Wo setzt ein Administrator das Administrationsdesign für alle?

<details>
<summary>Antworten</summary>

1. `admin4` ([Das Design admin4](../../../../../eng-us/features/6.0/admin4-design.md)).
2. **B.** Der Schalter ändert das Design nur für die eigenen Anfragen des Benutzers
   ([Das Administrationsdesign für Ihre Sitzung wählen](../../../../../eng-us/features/6.0/ui-switch-design.md)).
3. `admin4l`.
4. `uiswitch/design`.
5. `SiteDesign` in den Einstellungen des Siteaccess der Administration, `settings/siteaccess/admin/site.ini.append.php`,
   zum Beispiel mit `exp:ini set ... siteaccess:admin`, gefolgt vom Leeren des Caches.

</details>

## Weiterführende Literatur

- [Das Design admin4](../../../../../eng-us/features/6.0/admin4-design.md): Umschalten, heller und dunkler Modus, gemerkte
  Auswahl.
- [Das Design admin4l](../../../../../eng-us/features/6.0/admin4l.md): die aus Layouts gebaute Administrationsseite.
- [Das Administrationsdesign für Ihre Sitzung wählen](../../../../../eng-us/features/6.0/ui-switch-design.md): das Werkzeug
  **Oberfläche** im Detail.
- [Das neue Aussehen des Setup-Assistenten und der Siteaccess editor](../../../../../eng-us/features/6.0/setup-wizard-and-editor-siteaccess.md).

## Verwandte Seiten

- [Modul 1: Designs und die Fallback-Kette](README.md)
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
