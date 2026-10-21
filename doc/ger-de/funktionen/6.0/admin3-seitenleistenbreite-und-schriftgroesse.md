# Breite und Schriftgröße der linken Seitenleiste

Diese Seite ist für alle, die täglich in der Administrationsoberfläche arbeiten. Sie können das linke Menü so breit und
seinen Text so groß machen, wie Sie es brauchen. Die Wahl wird mit Ihrem Konto gespeichert, sodass sie Ihnen über
Sitzungen und Computer hinweg folgt. Sie kam im Juni 2026 mit dem [responsiven Admin-Design](admin3-responsives-admin.md).

Sie hilft:

- Menschen, die größere Schrift brauchen: Die Schrift wächst mit der Breite;
- Menschen an einem sehr breiten Monitor, die einen geräumigen Inhaltsbaum möchten;
- Menschen an einem kleinen Laptop, die die Breite dem Inhalt zurückgeben möchten.

## Verwenden

1. Öffnen Sie die Administrationsoberfläche und das Benutzermenü. Es enthält drei Links: **Schmal**, **Mittel** und
   **Breit**. Klicken Sie auf einen; die Seite wird in dieser Größe neu geladen.
2. Für eine genaue Breite ziehen Sie den schmalen Griff am rechten Rand des linken Menüs. Die Breite folgt dem Zeiger
   (Touch und Stift funktionieren auch) und wird gespeichert, wenn Sie loslassen.

| Voreinstellung | Breite | Schriftgröße |
|---|---|---|
| Schmal (Standard) | 16rem | 0.8225rem (etwa 13px) |
| Mittel | 22rem | 1rem (16px) |
| Breit | 30rem | 1.305rem (etwa 21px) |
| Gezogen | 224px bis 72 % der Fensterbreite | die Standardschriftgröße |

Die drei Links stehen in `design/admin3/templates/parts/user/menu.tpl`, in einem Block mit der ID `widthcontrol-links`.

## Den Standard für alle ändern

Überschreiben Sie `design/admin3/templates/pagelayout.tpl` in Ihrer eigenen Design-Extension und ändern Sie die beiden
Standardwerte (`16rem` und `0.8225rem`) in den Zeilen `{def $left_sidebar_width ...}`.

## Wie sie gespeichert wird

Die Breite ist eine Benutzereinstellung namens `admin_left_menu_size`. Ihr Wert ist `small`, `medium`, `large` oder
eine Breite in Pixeln wie `380px`. `pagelayout.tpl` liest die Einstellung und schreibt zwei CSS-Variablen auf `:root`:

```css
:root { --left-sidebar-width: 22rem; --left-sidebar-font-size: 1rem; }
```

`pagelayout.css` verwendet diese Variablen für das Menü und alles darin, sodass die Menüeinträge gemeinsam skalieren.
Der Handler für das Ziehen speichert über die normale URL für Einstellungen, geschützt durch das Formular-Token:

```
user/preferences/set_and_exit/admin_left_menu_size/<value>
```

## Grenzen

- Die Einstellung gehört zu einem Benutzer, nicht zu einem Browser: Ein zweiter Computer zeigt dieselbe Breite.
- Eine gezogene Breite wird unten auf 224px und oben auf 72 % des Fensters begrenzt, sodass der Inhaltsbereich nie
  verschwindet.
- Das Speichern braucht das Formular-Token-Skript von `ezformtoken` (standardmäßig aktiv). Ohne es wird die Breite
  angewendet, aber nicht gespeichert.

## Verwandte Seiten

- [Das responsive Admin-Design (admin3)](admin3-responsives-admin.md), [das Design admin4](admin4-design.md)
- [Blättern, Sortieren und Seitengrößen](seitenweise-listen-im-admin.md)
- [Unterelementliste: Spalten, Voreinstellungen und CSV-Export](optionen-der-unterelementtabelle.md), [ausgewählte Unterelemente kopieren](unterelemente-auswahl-kopieren.md)
- [Ausgewählte ausblenden und einblenden](../../../eng-us/bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md) (die Linie 6.0.15 hat noch kein Release-Tag)
- Chronik: [August 2024](../../../eng-us/history/2024/2024-08.md), [Oktober 2024](../../../eng-us/history/2024/2024-10.md), [November 2024](../../../eng-us/history/2024/2024-11.md), [Januar 2025](../../../eng-us/history/2025/2025-01.md), [Juni 2025](../../../eng-us/history/2025/2025-06.md), [Juni 2026, zweite Hälfte](../../../eng-us/history/2026/2026-06b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
