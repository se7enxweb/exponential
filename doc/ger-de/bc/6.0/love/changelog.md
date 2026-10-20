# Changelog

Änderungen an den LoveStack-Funktionen in Exponential, die neuesten zuerst. Die vollständige Liste, eine Zeile pro
Funktion mit ihrem LoveStack-Commit und ihrem Upgrade-Hinweis, steht im Changelog von 6.0.15:
[LoveStack-Funktionen (ab 2026-10-12)](../../../../changelogs/6.0/6.0.15.md#lovestack-features-from-2026-10-12).

## 6.0.15 (in Entwicklung)

- Hinzugefügt: 36 Funktionen aus LoveStack (mugoweb/ezpublish-legacy, Commit `e61e9fdf0c` vom 23. Februar 2026), in
  Exponential neu gebaut, mit Dank an Mugo Web: L01–L06 Sicherheit und Sessions, L07–L12 Diagnose, L13–L21 Inhalte
  und Administration, L22–L24 Rich-Text-Editor, L25–L31 APIs und Einstellungen für Entwickler, L32–L36 URLs und Suche
  ([Überblick](../lovestack.md#funktion-für-funktion)).
- Hinzugefügt: Der Name der Einstellung `site.ini [Session] SameSite` von LoveStack wird gelesen, wenn
  `CookieSameSite` nicht gesetzt ist.
- Hinzugefügt: `./console exp:lovestack check` und `./console exp:lovestack migrate` für Websites, die von LoveStack
  umziehen ([migration.md](migration.md)).
- Hinzugefügt: die eigenen Testfälle von LoveStack für Verhalten, das Exponential hat, und ein Test jeder Funktion
  ([zuordnung-der-tests.md](zuordnung-der-tests.md), [testen.md](testen.md)).
- Aktualisiert: die PHP-8-Fälle, die LoveStack behoben hat, unter PHP 8.5 und 8.1 nachgestellt; die noch
  fehlschlagenden sind behoben ([php8-pruefung.md](php8-pruefung.md)).
- Aktualisiert: Die Bestelllisten des Shops finden einen Kunden, dessen E-Mail-Adresse ein „+“ enthält
  (LoveStack-Commit `5a17c08bfc`).
- Hinzugefügt: diese Dokumentation.

Die Upgrade-Hinweise in Kürze: Auf einer vorhandenen Website ändert sich nichts, außer Sie schalten es ein; Ausnahmen
sind die reinen Fehlerkorrekturen, die Änderungen an der Protokollierung von L07 und L08, die Warnungen von L09 und
L12, die Schaltfläche Kopieren von L15 und die Sicherheitsprüfungen von L03, L05 und L06. Neue Installationen schalten
außerdem L01, L02, L04 (den eindeutigen Index der Anmeldenamen) und L23 ein.
