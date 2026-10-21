# admin4l: Die Admin-Seite wird von Exponential Layouts gebaut

Lesen Sie diese Seite, wenn Sie den Admin-Siteaccess mit `admin4l` betreiben, wenn Sie das `pagelayout.tpl` oder die
Seitenteile von admin4 überschreiben oder wenn Sie Admin-Layouts bearbeiten. Die Admin-Seite ist kein einzelnes
Template mehr: Mit `SiteDesign=admin4l` wird die Seite auf ein Admin-Layout aufgelöst und aus dessen Zonen und Blöcken
gezeichnet. Wie Sie es verwenden, steht auf [der Funktionsseite](../../../eng-us/features/6.0/admin4l.md).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neues Design `design/admin4l` (Pagelayout, Zonen-Template, 20 Block-Templates). Neue Admin-Layouttypen `admin_3col`, `admin_2col`, `admin_full` (`Group=admin`), Blockdefinitionen `admin_*` in `[AdminBlockSettings]`, Regelziele `module` und `module_view`, `[AdminLayoutSettings]` in `explayouts.ini`. Neue Fetch-Funktionen `resolve_admin_layout`, `admin_layout_cache_key`, `admin_layouts_enabled` im Modul `explayouts`. |
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

## Gestaltung

admin4l zeichnet die Stylesheets von admin4, daher gelten die Gestaltungsregeln der Administration auch für sein
Pagelayout und seine Block-Templates: kein `<style>`-Block und kein Attribut `style=""`, die gemeinsamen Komponenten
`.a4-*` und die Tokens `--a4-*`. Siehe [Die Administration gestalten](admin4-gestaltung.md).
