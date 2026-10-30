# admin4l: Die Admin-Seite wird von Exponential Layouts gebaut

Lesen Sie diese Seite, wenn Sie den Admin-Siteaccess mit `admin4l` betreiben, wenn Sie das `pagelayout.tpl` oder die
Seitenteile von admin4 überschreiben oder wenn Sie Admin-Layouts bearbeiten. Die Admin-Seite ist kein einzelnes
Template mehr: Mit `SiteDesign=admin4l` wird die Seite auf ein Admin-Layout aufgelöst und aus dessen Zonen und Blöcken
gezeichnet. Wie Sie es verwenden, steht auf [der Funktionsseite](../../funktionen/6.0/admin4l.md).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neues Design `design/admin4l` (Pagelayout, Zonen-Template, 23 Block-Templates). Neue Admin-Layouttypen `admin_3col`, `admin_2col`, `admin_full` (`Group=admin`), Blockdefinitionen `admin_*` in `[AdminBlockSettings]`, Regelziele `module` und `module_view`, `[AdminLayoutSettings]` in `explayouts.ini`. Neue Fetch-Funktionen `resolve_admin_layout`, `admin_layout_cache_key`, `admin_layouts_enabled` im Modul `explayouts`. |
| Wer betroffen ist | Admin-Siteaccesses mit `SiteDesign=admin4l`. Websites, die `pagelayout.tpl` oder `page_*.tpl` in `admin4` überschreiben, erhalten das Override nur, wenn kein Admin-Layout aufgelöst wird. Wer Layouts bearbeitet, sieht in der API des Editors einen Parameter `group` (Standard `site`). |
| Wie Sie es prüfen | `php vendor/bin/phpunit --testsuite explayouts_admin`; laden Sie die Admin-Seiten und vergleichen Sie mit `SiteDesign=admin4`. |
| Wie Sie es beheben | `[AdminLayoutSettings] Enabled=disabled` in der `explayouts.ini.append.php` des Admin-Siteaccess bringt jede Seite zum einfachen admin4 zurück (leeren Sie die Caches für INI und template-block). |

## Was nicht dasselbe wie bei admin4 ist

Markup, Klassen und Styles sind die von admin4, verglichen per Screenshot (390 und 960 px bei 2x, 1440 px bei 1x,
hell und dunkel, Chromium und Firefox) und über das Elementgerüst der Seite auf den Seiten Dashboard, Inhaltsansicht,
Inhaltsbearbeitung, Setup, Benutzer und Shop. Die vorhandenen Unterschiede:

- Ein Block, der mittendrin einen Fehler hat, behält seine Teilausgabe; nur eine leere Zone fällt auf das Template von
  admin4 zurück.
- Ein nach dem Layout geschlüsselter cache-block: Ein Design, das einen eigenen Cache-Block um eine Zone legt, muss die
  Admin-Generation
  (`fetch( 'explayouts', 'admin_layout_cache_key', hash( 'module', ..., 'view', ... ) )`) in seine Schlüssel
  aufnehmen.
- Die Tab-Reihenfolge der Kopfzeile ist die von admin4; auf einem Telefon hat das geöffnete Menü keine Zeile mit
  Benutzeraktionen (`a4_phone_user`), stattdessen bleibt das Benutzermenü der Kopfzeile.
- Der Block `admin_theme_switch` zeichnet den Link zur Website, die Schaltfläche für den Farbmodus, die Hilfe und das
  Benutzermenü, und `admin_ui_switch` ist das Werkzeug der Werkzeugleiste für die Auswahl der Oberfläche. Die Namen
  bleiben, denn eine Umbenennung würde gespeicherte Layouts zerstören.
- `main_top` und `main_bottom` werden nur mit dem Rahmen `#maincolumn` gezeichnet; `admin_full` deklariert sie nicht
  mehr; `admin_popup_menu`, `admin_debug_area` und `admin_overlay` werden nur aus der Fußzeilen-Zone gezeichnet; nur
  Definitionen `admin_*` werden gezeichnet.
- Die API des Editors führt standardmäßig Website-Layouts auf; fragen Sie mit `?group=admin` oder `all` nach, um die
  Admin-Layouts zu sehen.

## Trennung von der Website

Website-Layouts erreichen nie die Administration, und Admin-Layouts erreichen nie die Website. Der Resolver der Website
überspringt Admin-Layouttypen; die Ziele `module` und `module_view` passen ohne Anfragekontext nie; Admin-Blöcke stehen
nicht in der Blockliste der Website.

## Neu installieren

Die mitgelieferten Admin-Layouts und Regeln werden in dieser Installation durch einen idempotenten Seed angelegt. Sie
sind **noch nicht** in den Seed-Daten des Installationsprogramms: Eine frische Installation hat die Layouttypen, Blöcke
und Einstellungen, und die Seite läuft als einfaches admin4 (nichts wird aufgelöst), bis die Layouts angelegt sind.

## Installierter Seed und `admin_full`

Das Seed-Fragment (`share/fragments/admin_layouts_db_data.dba` des Layouts-Pakets) legt die Zonen `main_top` und
`main_bottom` des Layouts `admin_full` nicht mehr an, ebenso wenig den Block `admin_breadcrumb`, der in `main_top` lag,
weil die Seite sie dort nie zeichnet. Eine Installation, die früher angelegt wurde, behält diese beiden Zonen und den
Block als verwaiste Einträge in ihrer Datenbank. Sie sind harmlos: Nichts zeichnet sie, und sie können bleiben oder im
Layout-Editor gelöscht werden. Eine Migration gibt es nicht.

## Gestaltung

admin4l zeichnet die Stylesheets von admin4, daher gelten die Gestaltungsregeln der Administration auch für sein
Pagelayout und seine Block-Templates: kein `<style>`-Block und kein Attribut `style=""`, die gemeinsamen Komponenten
`.a4-*` und die Tokens `--a4-*`. Siehe [Die Administration gestalten](admin4-gestaltung.md).
