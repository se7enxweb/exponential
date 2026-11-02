# Statisches Veröffentlichen

Lesen Sie diese Seite, wenn Sie auf Exponential 6.0.15 aktualisieren und wissen möchten, was statisches Veröffentlichen
einer bestehenden Installation hinzufügt und ob sich etwas für Sie ändert. Kurz: Nichts ändert sich, bis Sie es
einschalten.

[Funktion](../../funktionen/6.0/statisches-veroeffentlichen.md) · [Hilfehandbuch](../../hilfe/statisches-veroeffentlichen/README.md) ·
[Statische Websites auf GitHub Pages hosten](../../anleitungen/statische-websites-auf-github-pages-hosten.md)

## Was hinzukommt

| Bereich | Neu |
|---|---|
| Einstellungen | `[StaticPublishSettings]` und der Vorlagenblock `[Target_example]` in `settings/staticcache.ini`; Ziele gehören nach `settings/override/staticcache.ini.append.php` |
| Datenbank | die Tabellen `exp_static_run` und `exp_static_page`, angelegt vom Upgrade-Schritt `6.0.15-static-publish-tables` auf jeder unterstützten Datenbank |
| Klassen | `kernel/classes/staticpublish/` (`expStatic*`) |
| Konsole | `exp:static:build`, `diff`, `serve`, `targets`, `publish`, `rollback`, `push`, `keys`, `status`, `github`, `dns` |
| Administration | das Modul `staticpublish` (Setup > Statisches Veröffentlichen), der Dashboard-Block `staticpublish`, ein Link unter Setup > Cache |
| Richtlinien | `staticpublish/read`, `build`, `publish`, `setup`, mit einer Begrenzung nach Ziel |
| Cronjob | der Teil `staticpublish` (erneuter Push, Veröffentlichen beim Veröffentlichen von Inhalten, Zeitplan) |
| Audit | `system.staticpublish.publish`, `.rollback`, `.credential`, `.target`, `.build`, `.form` |
| Frontend | der Formular-Endpunkt `staticpublish/form`, nur von statischen Kopien mit `Forms=endpoint` genutzt |

## Was sich nicht ändert

- **Standardmäßig aus.** `[StaticPublishSettings] Enabled=disabled`: Nichts wird gebaut, kein Cronjob-Teil tut etwas,
  der Dashboard-Block zeigt nur eine einzeilige Einladung zum Assistenten, die jeder Administrator ausblenden kann.
- **Der statische Cache** (`[CacheSettings]`, `makestaticcache.php`) arbeitet wie bisher. Statisches Veröffentlichen
  nutzt seinen Crawler in einem Exportmodus, schreibt aber nirgends hin, wo der statische Cache liest.
- **Keine Rolle erhält die neuen Richtlinien** außer Administrator, der ohnehin jede Funktion hat. Geben Sie der
  Redaktion `staticpublish/read` oder `publish`, wenn sie sehen oder veröffentlichen soll.
- **Keine neue Voraussetzung.** git wird nur auf dem Server gebraucht, der veröffentlicht; curl und openssl in PHP nur
  für die GitHub-Anbindung, die ohne sie ausgeschaltet ist.

## Was beim Aktualisieren zu tun ist

1. Das Upgrade wie gewohnt ausführen (`./console exp:upgrade`); es legt die zwei Tabellen an.
2. Sonst nichts, es sei denn, Sie möchten es nutzen: dann
   [In 15 Minuten eine kostenlose Website](../../hilfe/statisches-veroeffentlichen/in-15-minuten-eine-kostenlose-website.md).
3. Wenn Sie den Cronjob-Teil `staticpublish` einer Cronjob-Gruppe hinzufügen, wirkt er nur bei Zielen mit Auslösern
   oder wartenden Pushes.

## Reservierte Namen in den Einstellungen

`[StaticPublishSettings]` und Blöcke namens `[Target_<id>]` in `staticcache.ini` gehören dem statischen Veröffentlichen.
Ein Override, das diese Namen schon für etwas anderes verwendet, muss sie umbenennen.
