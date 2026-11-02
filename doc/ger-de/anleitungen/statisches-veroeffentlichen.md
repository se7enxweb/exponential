# Statisches Veröffentlichen: ein Überblick für Administratoren

Diese Anleitung ist für Administratoren, die eine Installation mit Exponential 6.0.15+ betreuen und wissen wollen, was
das statische Veröffentlichen hinzufügt, was es braucht und was es im Alltag ändert, bevor sie das erste Ziel
einrichten.

[Anleitungen](README.md) · Verwandt: [Statisches Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md),
[Statische Websites auf GitHub Pages hosten mit Exponential 6.0.15+](statische-websites-auf-github-pages-hosten.md),
[Das Hilfe-Handbuch](../hilfe/statisches-veroeffentlichen/README.md)

## Was Sie am Ende haben

- Ein klares Bild der Teile: Ziele, Builds, Veröffentlichungen, der Verlauf und die Trigger.
- Die Einstellungen, Berechtigungen, den Cronjob und die Tabellen, die eine Installation dafür braucht.
- Eine Checkliste zum Einschalten.

## Die Teile

| Teil | Was es ist | Wo |
|---|---|---|
| **Ziel** | Eine öffentliche Kopie: welcher Front-Siteaccess, wie Links geschrieben werden, welches Repository und welcher Branch, wie sich der Push anmeldet | `[Target_<id>]` in `staticcache.ini`, angelegt mit dem Assistenten unter Setup > Statisches Veröffentlichen |
| **Build** | Der Siteaccess, als anonymer Benutzer abgerufen, bereinigt, seine Links umgeschrieben, seine Dateien gesammelt, Zusatzdateien und ein Manifest geschrieben | `exp:static:build`, **Build** in der Administration |
| **Veröffentlichung** | Der Build, im eigenen Git-Repository des Ziels committet, als `static/<ziel>/<n>` getaggt und gepusht, nie erzwungen | `exp:static:publish`, **Veröffentlichen** in der Vorschau |
| **Verlauf** | Jede Veröffentlichung mit Commit und Tag; ein Zurücknehmen ist ein neuer Commit | `exp:static:rollback`, **Verlauf** |
| **Trigger** | Veröffentlichen beim Veröffentlichen von Inhalten (verzögert) oder nach Zeitplan | `Triggers[]`, der Cronjob-Teil `staticpublish` |

Der Ordner des Ziels, `var/<site>/static-publish/<ziel>/`, ist die Kopie und ihr Git-Repository. Er liegt nie im
eigenen Repository der Installation.

## Was es braucht

- Das statische Veröffentlichen eingeschaltet: `[StaticPublishSettings] Enabled=enabled` und die Id des Ziels in
  `TargetList[]`. Der Assistent schreibt beides nach `settings/override/staticcache.ini.append.php`.
- Den Aktualisierungsschritt `6.0.15-static-publish-tables`, der `exp_static_run` und `exp_static_page` auf jeder
  unterstützten Datenbank anlegt. `exp:upgrade` führt ihn aus.
- `git` auf dem Server für das Veröffentlichen; curl und openssl in PHP für die GitHub-Anbindung.
- Ein Geheimnis unter Setup > Geheimnisse für den Push (ein Token oder ein Deploy-Key), benannt im `Credential` des
  Ziels. Ein Geheimnis wird nie in das Ziel selbst geschrieben.
- Den Cronjob-Teil `staticpublish` in Ihrem Cronjob-Plan, wenn Pushes wiederholt werden oder Ziele sich selbst
  veröffentlichen sollen.
- Die Befehle laufen als der Systembenutzer, dem die Dateien der Site gehören.

## Wer was darf

Die Richtlinien `staticpublish/read`, `build`, `publish` und `setup` nehmen eine Ziel-Beschränkung. Veröffentlichen,
Zurücknehmen, ein Ziel speichern und einen Deploy-Key erstellen fragen „Bestätigen Sie, dass Sie es sind“. Geben Sie
Redakteuren `read` und, wenn sie einzelne Seiten aus der Website-Seitenleiste veröffentlichen sollen, `build` und
`publish` nur für ihr Ziel. Siehe [Wer veröffentlichen darf](../hilfe/statisches-veroeffentlichen/wer-veroeffentlichen-darf-rollen-und-richtlinien.md).

## Was sich im Alltag ändert

- Redakteure arbeiten wie bisher. Die öffentliche Kopie ändert sich erst, wenn ein Ziel gebaut und veröffentlicht
  wird, durch eine Person, einen Trigger oder einen Zeitplan.
- Der Dashboard-Block **Statisches Veröffentlichen** zeigt die veralteten Seiten je Ziel: Seiten, deren Inhalt sich
  nach der letzten Veröffentlichung geändert hat.
- Suche, Formulare, Kommentare, Anmeldung und Warenkorb brauchen einen Server. Der Build gibt jedem einen Ersatz und
  listet sie in seinem Bericht; siehe [Dynamische Funktionen](statisches-veroeffentlichen-dynamische-funktionen.md).
- Die Live-Installation bleibt, wo sie ist. Sie kann intern bleiben; die Öffentlichkeit sieht die Kopie, während
  Formulare weiterhin an den Formular-Endpunkt der Live-Site senden können.

## Einschalten, eine Checkliste

1. `./console exp:upgrade` ausführen, damit die Tabellen existieren.
2. Die Zugangsdaten für den Push unter Setup > Geheimnisse speichern.
3. Das Ziel unter Setup > Statisches Veröffentlichen > **Neues Ziel** anlegen ([Der Einrichtungsassistent](../hilfe/statisches-veroeffentlichen/der-einrichtungsassistent.md)).
4. Bauen, Bericht und Änderungen lesen, die Kopie ansehen ([Build, Vorschau und Veröffentlichen](../hilfe/statisches-veroeffentlichen/build-vorschau-und-veroeffentlichen.md)).
5. Vor dem ersten öffentlichen Push [Private Inhalte privat halten](../hilfe/statisches-veroeffentlichen/private-inhalte-privat-halten.md) lesen.
6. Veröffentlichen, dann GitHub Pages (oder Ihren Host) den Branch ausliefern lassen.
7. Den Rollen ihre Richtlinien geben und den Cronjob-Teil hinzufügen, wenn Sie Trigger nutzen.

## Nächster Schritt

[Statische Websites auf GitHub Pages hosten mit Exponential 6.0.15+](statische-websites-auf-github-pages-hosten.md) oder
das 15-Minuten-Tutorial [In 15 Minuten eine kostenlose Website](../hilfe/statisches-veroeffentlichen/in-15-minuten-eine-kostenlose-website.md).
