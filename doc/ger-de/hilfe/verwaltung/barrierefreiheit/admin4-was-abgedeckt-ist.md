---
title: Was admin4 von Exponential für die Barrierefreiheit abdeckt
audience: government, admin, project-manager, trainer
category: public-sector
order: 90
summary: Für die Administration und die Koordination Barrierefreiheit - die Funktionen zur Barrierefreiheit der Administration admin4, die das Projekt baut und testet (Sprunglink, Landmarks, Tastaturmuster, Kontrast, Fehlerübersichten, Farbmodi, rechts nach links), und wie Sie sicherstellen, dass Ihre Redaktion tatsächlich admin4 verwendet.
keywords: admin4, admin4l, Administration, Redaktion, Sprunglink, Landmarks, Tastatur, Inhaltsbaum, Reiter, Kontrast, Dunkelmodus, erzwungene Farben, reduzierte Bewegung, Fehlerübersicht, Fokusrahmen, Designwechsel, Barrierefreiheit, administration, editors, skip link, landmarks, keyboard, content tree, tabs, contrast, dark mode, forced colors, reduced motion, error summary, focus ring, design switch, accessibility
type: help
---
# Was admin4 von Exponential für die Barrierefreiheit abdeckt

Diese Seite richtet sich an Administratorinnen und Administratoren, die die Bearbeitungsumgebung einrichten, an die
Koordination Barrierefreiheit, die sie bewertet, und an Trainerinnen und Trainer, die Beschäftigten mit Behinderungen
ihre Verwendung beibringen. Sie listet auf, was das Administrationsdesign admin4 (und seine Layoutvariante admin4l)
für die Barrierefreiheit tut, wie es in [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md) und
[Das Design admin4](../../../../eng-us/features/6.0/admin4-design.md) dokumentiert ist, und wie Sie admin4 zu dem Design machen,
das Ihre Redaktion erhält. Das Projekt bewertet admin4 gegenüber WCAG 2.2 AA mit **Partly complies**.

## Inhalt

1. [admin4 zum Design der Redaktion machen](#admin4-zum-design-der-redaktion-machen)
2. [Struktur und Namen](#struktur-und-namen)
3. [Tastatur](#tastatur)
4. [Visuelle Darstellung](#visuelle-darstellung)
5. [Formulare und Fehler](#formulare-und-fehler)
6. [Sprachen und Schreibrichtung](#sprachen-und-schreibrichtung)
7. [Verwandte Seiten](#verwandte-seiten)

## admin4 zum Design der Redaktion machen

Die Arbeit an der Barrierefreiheit konzentriert sich auf admin4 und admin4l. Die klassische Administration
(`design/admin`) und admin3 haben Korrekturen erhalten, werden aber von den Tests für Kontrast, Landmarks und
Formularfehler nicht abgedeckt.

1. Setzen Sie in der `site.ini.append.php` des Administrations-Siteaccess `[DesignSettings] SiteDesign=admin4`
   ([Darauf umstellen](../../../../eng-us/features/6.0/admin4-design.md#switch-to-it)).
2. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-all --allow-root-user`.
3. Wenn Sie den Designwechsel je Sitzung anbieten, entscheiden Sie, welche Designs er auflistet
   ([Der Designwechsel der Administration](../../../anleitungen/ui-umschalter-design.md)). Wer das klassische Design wählt,
   verlässt die getestete Umgebung; sagen Sie das in Ihrem Redaktionsleitfaden.

## Struktur und Namen

- **Sprunglink**: Der erste Tabulatorstopp ist „Zum Hauptinhalt springen“.
- **Landmarks**: Banner, benannte Navigations-Landmarks (oberes Menü, Seitenmenü, Brotkrumenpfad), ergänzende rechte
  Spalte, Fußzeile und genau eine Hauptlandmark je Seite.
- **Aktuelle Seite**: Brotkrumenpfad und oberes Menü kennzeichnen sie mit `aria-current="page"`.
- **Tabellen**: Jede Kopfzelle in den Templates von admin4 hat `scope="col"`.
- **Formularfelder**: Jedes `input`, `select` und `textarea` hat einen zugänglichen Namen; ein `title` oder
  `placeholder` zählt nicht als solcher.

Abgesichert durch `AdminLandmarksTest.php` und `AdminFormLabelsTest.php`
([Landmarks und Namen](../../../../eng-us/features/6.0/admin4-design.md#landmarks-and-names)).

## Tastatur

- **Reiter eines Knotens** folgen dem Reitermuster von WAI-ARIA: Pfeiltasten, Pos1 und Ende.
- **Inhaltsbaum** folgt dem Muster der Baumansicht: Pfeiltasten öffnen, schließen und bewegen; Buchstaben springen;
  Umschalt+F10 oder die Kontextmenütaste öffnet das Menü des Elements.
- **Kontextmenüs** und die Menüs im Kopfbereich funktionieren mit der Tastatur; Escape schließt und gibt den Fokus
  zurück.
- **Das Umsortieren von Unterelementen** hat am Ziehgriff einen Tastaturmodus.
- **Datumsfelder** öffnen ihren Kalender über eine benannte Schaltfläche und geben den Fokus beim Schließen zurück.
- Die Taste `/` springt zum Suchfeld.

Alle Tasten sind für die Benutzer unter [Tastenkürzel](../../tastenkuerzel.md) aufgeführt; die Einzelheiten
stehen unter [Tastatur](../../../../eng-us/features/6.0/admin4-design.md#keyboard).

## Visuelle Darstellung

- **Kontrast**: Die Farb-Tokens (`--a4-*` in `design/admin4/stylesheets/components.css`) werden mit der Formel von
  WCAG 2 im hellen und dunklen Modus geprüft: 4,5:1 für Text, 3:1 für Fokusrahmen, Feldränder und Diagrammfarben.
- **Fokus**: ein gemeinsamer, sichtbarer Fokusrahmen (Umriss von 3 px).
- **Zielgröße**: mindestens 24 x 24 px.
- **Farbmodi**: hell, dunkel und „System“; `forced-colors` und `prefers-reduced-motion` werden beachtet.
- **Umbruch**: admin4 wird in Breiten für Telefon, Tablet und Desktop geprüft, und Seiten bei 960 px mit 200 % Zoom.
- **Farbe ist nicht das einzige Signal**: Meldungen tragen ein Symbol und eine Farbe.

## Formulare und Fehler

- Pflichtfelder tragen eine sichtbare Markierung und das Wort für Screenreader.
- Ein fehlgeschlagenes Speichern zeigt eine Fehlerübersicht, die den Fokus erhält und auf die Felder verlinkt; die Felder
  erhalten `aria-invalid` und `aria-describedby`, das auf die Meldung zeigt.
- Modulmeldungen tragen `role="alert"` oder eine Statusrolle; Toasts verwenden ein Paar von Live-Regionen.

## Sprachen und Schreibrichtung

- `<html lang>` folgt dem Locale des Siteaccess.
- Oberflächensprachen von rechts nach links setzen `dir="rtl"`, spiegeln das Layout und isolieren Namen mit `<bdi>`
  ([Sprachen von rechts nach links](../../../../eng-us/features/6.0/right-to-left.md)).
- Inhalte in einer Inhaltssprache von rechts nach links werden im Bearbeitungsformular und in den Vorschauen mit `lang`
  und `dir` gekennzeichnet.

## Verwandte Seiten

- [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)
- [Das Design admin4](../../../../eng-us/features/6.0/admin4-design.md)
- [Tastenkürzel](../../tastenkuerzel.md)
- [Styling von admin4](../../../bc/6.0/admin4-gestaltung.md) (für Entwicklerinnen und Entwickler, die es erweitern)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
