---
title: WCAG 2.2 AA auf Exponential abgebildet
audience: government, project-manager, developer, designer
category: public-sector
order: 70
summary: Eine Zuordnung von WCAG 2.2 Stufe AA Kriterium für Kriterium für öffentliche Stellen - was die Administration admin4 bietet und testet und wer auf der öffentlichen Website und für ihre Inhalte verantwortlich ist.
keywords: WCAG 2.2, Erfolgskriterien, Konformitätsstufe AA, Zuordnung, admin4, öffentliche Website, Redaktion, Design, Entwicklung, Matrix der Barrierefreiheit, success criteria, level AA, mapping, public site, editors, designers, developers, 1.1.1, 1.4.3, 2.1.1, 2.4.7, 2.5.8, 3.3.1, 4.1.2, accessibility matrix
type: reference
---
# WCAG 2.2 AA auf Exponential abgebildet

Diese Seite richtet sich an die Koordination Barrierefreiheit, an Design, Entwicklung und Projektverantwortliche, die
eine Website planen oder prüfen. Sie listet die Erfolgskriterien von WCAG 2.2 auf, die die Erklärung des Projekts
abdeckt, sagt für jedes, was die Administration admin4 tut und welcher Test es absichert, und sagt, wer für dasselbe
Kriterium auf Ihrer öffentlichen Website verantwortlich ist. Jeder Eintrag für die Administration stammt aus
[Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md#wcag-22); WCAG selbst finden Sie unter
<https://www.w3.org/TR/WCAG22/>.

## Inhalt

1. [Wie Sie die Tabelle lesen](#wie-sie-die-tabelle-lesen)
2. [Die Zuordnung](#die-zuordnung)
3. [Kriterien, die die Erklärung nicht nennt](#kriterien-die-die-erklärung-nicht-nennt)
4. [Verwandte Seiten](#verwandte-seiten)

## Wie Sie die Tabelle lesen

- **admin4** ist die Administration, die die Redaktion verwendet. Der Status des Ganzen ist **Partly complies**:
  entworfen und durch Regressionstests abgesichert, nicht Seite für Seite geprüft.
- **Öffentliche Website** ist Ihr Design. Das Projekt hat die öffentlichen Designs nicht geprüft, daher ist jede Zeile
  dort Ihre Arbeit; die Spalte nennt, wer sie üblicherweise erledigt.
- Ein Dateiname eines Tests bezeichnet einen Test in `tests/tests/design/`, der die Quellen der Templates oder
  Stylesheets liest.

## Die Zuordnung

| Kriterium | admin4 (Bearbeitungswerkzeug) | Nachweis | Öffentliche Website: wer es erledigt |
|---|---|---|---|
| 1.1.1 Nicht-Text-Inhalt | jedes Formularelement hat einen zugänglichen Namen | `AdminFormLabelsTest.php` | die Redaktion schreibt Alternativtext; die Administration kann ihn an Bildattributen verpflichtend machen („Alternativtext ist erforderlich“); das Design behandelt dekorative Bilder |
| 1.3.1 Info und Beziehungen | Landmarks, `scope="col"` an Kopfzellen, Pflichtfelder für Screenreader gekennzeichnet | `AdminLandmarksTest.php`, `AdminFormErrorsTest.php` | Entwicklung: semantische Templates; Redaktion: echte Überschriften, Kopfzellen in Tabellen |
| 1.3.2 Bedeutungstragende Reihenfolge | Sprachen von rechts nach links setzen `dir="rtl"`; Namen mit `<bdi>` isoliert | `AdminRightToLeftTest.php` | Entwicklung: Reihenfolge im Quelltext gleich Lesereihenfolge; `lang`/`dir` an Inhalten in einer anderen Sprache |
| 1.4.1 Benutzung von Farbe | Meldungen tragen ein Symbol und eine Farbe | `AdminNoticesTest.php` | Design |
| 1.4.3 Kontrast (Minimum) | Tokens `--a4-*` geprüft: Text 4,5:1 im hellen und dunklen Modus | `AdminTokenContrastTest.php` | Design: die Farb-Tokens Ihres Themes |
| 1.4.4 Textgröße ändern, 1.4.10 Umbruch | in Breiten für Telefon, Tablet und Desktop geprüft; Seiten bei 960 px mit 200 % Zoom geprüft | `AdminResponsiveTest.php` | Design und Entwicklung: responsive Templates |
| 1.4.11 Nicht-Text-Kontrast | Fokusrahmen und Feldränder 3:1, Diagrammfarben 3:1 | `AdminTokenContrastTest.php` | Design |
| 1.4.13 Inhalt bei Hover oder Fokus | Tooltips überfahrbar, dauerhaft, mit Escape schließbar | [Design admin4](../../../../eng-us/features/6.0/admin4-design.md) | Entwicklung |
| 2.1.1 Tastatur | Reiter eines Knotens (Reitermuster der APG), Inhaltsbaum, Kontextmenüs per Tastatur | `AdminKeyboardTest.php`, `AdminContextMenuTest.php` | Entwicklung: Menüs, Slider, eingebettete Widgets |
| 2.4.1 Blöcke umgehen | „Zum Hauptinhalt springen“ ist der erste Tabulatorstopp | `AdminLandmarksTest.php` | Entwicklung: ein Sprunglink in Ihrem Pagelayout |
| 2.4.3 Fokus-Reihenfolge | dieselbe Tabulatorreihenfolge im Kopfbereich in admin4 und admin4l | `AdminHeaderTabOrderTest.php` | Entwicklung |
| 2.4.7 Fokus sichtbar | ein gemeinsamer Fokusrahmen (Umriss von 3 px in `--a4-ring`) | `design/admin4/stylesheets/components.css` | Design: den Umriss nie ohne Ersatz entfernen |
| 2.5.8 Zielgröße (Minimum) | Zielflächen mindestens 24 x 24 px | [Styling von admin4](../../../../eng-us/bc/6.0/admin4-styling.md) | Design |
| 3.1.1 Sprache der Seite | `<html lang>` aus dem Locale des Siteaccess | `design/admin4/templates/pagelayout.tpl` | Entwicklung: `lang` in Ihrem Pagelayout; Siteaccesses je Sprache |
| 3.3.1 Fehlererkennung, 3.3.2 Beschriftungen oder Anweisungen | Fehlerübersicht, die den Fokus erhält und auf Felder verlinkt; `aria-invalid`, `aria-describedby` | `AdminFormErrorsTest.php` | Entwicklung: Ihre Formulare (Informationssammler, Registrierung) |
| 4.1.2 Name, Rolle, Wert | zugängliche Namen; Rollen und Zustände von Reitern, Baum und Menüs | `AdminFormLabelsTest.php`, `AdminKeyboardTest.php` | Entwicklung |

Benutzereinstellungen, die 1.4.3 und 2.3.3 unterstützen: Farbmodi hell, dunkel und System sowie Regeln für
`forced-colors` und `prefers-reduced-motion` in `design/admin4/stylesheets/admin4.css` (`AdminThemeSwitchTest.php`).

## Kriterien, die die Erklärung nicht nennt

Die Erklärung des Projekts listet die obigen Kriterien auf. Für jedes andere AA-Kriterium (zum Beispiel 1.2.x
Untertitel und Audiodeskription, 1.4.5 Bilder von Text, 2.2.1 Zeiteinteilung anpassbar, 2.4.2 Seite mit Titel, 2.4.4
Linkzweck, 2.4.6 Überschriften und Beschriftungen, 2.4.11 Fokus nicht verdeckt, 2.5.7 Ziehbewegungen, 3.2.x
Vorhersehbar, 3.3.7 Redundante Eingabe, 3.3.8 Barrierefreie Authentifizierung) gibt es für die Administration **keine
dokumentierten Nachweise**. Behandeln Sie sie als ungetestet: Nehmen Sie sie für die Administration und die öffentliche
Website in Ihre eigene Prüfung auf.

Einige Fakten, die für diese Kriterien zählen und sich im Repository prüfen lassen:

- **2.5.7 Ziehbewegungen**: Unterelemente lassen sich in admin4 per Ziehen und Ablegen umsortieren. Der Griff ist auch
  eine Schaltfläche, die die Zeile mit Leertaste, Enter und den Pfeiltasten verschiebt, und die Prioritätszellen lassen
  sich weiterhin von Hand bearbeiten
  ([Unterelemente per Ziehen und Ablegen umsortieren](../../../../eng-us/features/6.0/admin4-design.md#reordering-sub-items-by-drag-and-drop)).
  Nehmen Sie andere Ziehinteraktionen Ihrer Erweiterungen (zum Beispiel den Layout-Editor) in Ihre Prüfung auf.
- **2.2.1 Zeiteinteilung anpassbar**: Sitzungen enden nach `site.ini [Session] SessionTimeout` (standardmäßig 259200
  Sekunden); ein aus Sicherheitsgründen gewählter kürzerer Wert kann Redakteurinnen und Redakteure betreffen, die
  langsam arbeiten
  ([13.7 Sitzungen und Cookies](../../../installation/13-sicherheitshaertung.md#137-sessions-und-cookies)).
- **3.3.8 Barrierefreie Authentifizierung**: Die Anmeldung ist ein Formular mit Benutzername und Passwort; die
  zweistufige Anmeldung fügt, wenn eingeschaltet, einen 6-stelligen Code hinzu
  ([Zweistufige Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)). Prüfen Sie, dass Passwortmanager und Einfügen in Ihrer
  Konfiguration funktionieren.

## Verwandte Seiten

- [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)
- [Das Design admin4](../../../../eng-us/features/6.0/admin4-design.md)
- [Styling von admin4](../../../../eng-us/bc/6.0/admin4-styling.md)
- [Die Barrierefreiheit Ihrer Website testen](../../../../eng-us/help/creators/website-creators/testing-accessibility.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
