# Erste Schritte

Diese Seite ist für alle, gleich was Sie über Exponential oder LoveStack wissen.

## Worum geht es?

Der ursprünglich von eZ Systems veröffentlichte Legacy-Kernel hat heute mehr als eine Fortsetzung. Zwei davon sind
**Exponential** (dieses Projekt) und **LoveStack** (die Distribution von Mugo Web, `mugoweb/ezpublish-legacy`).
LoveStack hat im Lauf der Jahre eine Reihe wertvoller Funktionen und Korrekturen hinzugefügt. Exponential 6.0.15
übernimmt 36 davon in die Exponential-Distribution, in eigenem Code neu gebaut und getestet, sodass jede
Exponential-Website sie zusammen mit den eigenen Funktionen von Exponential verwenden kann. Der
[Überblick](../lovestack.md) beschreibt jede davon.

## Betrifft mich das?

Führen Sie dies im Wurzelverzeichnis Ihrer Installation aus:

```sh
./console exp:lovestack check
```

Auf einer Installation, die die Konsole noch nicht hat, sagt ein kurzer Blick dasselbe:

```sh
grep -l -i "lovestack" lib/version.php composer.json composer.lock 2>/dev/null
```

| Sie sind | Lesen Sie |
|---|---|
| auf Exponential (oder einem anderen Nachfolger des Legacy-Kernels), nie auf LoveStack | mehr ist nicht nötig. Lesen Sie [Was ändert sich auf meiner Exponential-Website?](#was-ändert-sich-auf-meiner-exponential-website) für die Funktionen, die Sie vielleicht einschalten möchten |
| auf LoveStack, oder Sie ziehen eine LoveStack-Datenbank um | [Eine Website von LoveStack umziehen](migration.md) |
| ein Entwickler, der eine Methode verwendet hat, die es nur in LoveStack gibt | [api.md](api.md) und [Schritt 7 der Migration](migration.md#schritt-7--ihr-eigener-php-code) |

## Was ändert sich auf meiner Exponential-Website?

Nach dem Upgrade auf 6.0.15:

- **reine Fehlerkorrekturen und Sicherheitsprüfungen** sind für jede Website eingeschaltet: Das Abmelden beendet die
  Session und ihr Cookie (L03), hochgeladene Bilder und Vorschaubilder von Paketen werden anhand ihres Inhalts geprüft
  (L05, L06), dazu ein Dutzend kleinerer Korrekturen;
- **einige kleine sichtbare Änderungen**: PHP-Deprecations gehen nach `var/log/deprecated.log` (L07), Log-Dateien
  werden bei 2 MB rotiert (L08), eine Template-Warnung für Operator-Parameter ohne Standardwert (L09), eine
  Schaltfläche Kopieren in der vollständigen Admin-Ansicht (L15);
- **alles, was ändern könnte, was Besucher oder Redakteure sehen, ist ausgeschaltet**, bis Sie es einschalten: die
  Richtlinie für Knotendaten-Aufrufe (L01), geschlossene Registrierung (L02), der eindeutige Index der Anmeldenamen
  (L04), Audit-Einträge für Orte (L13), Pflicht-Alternativtext (L16), getrimmte Textfelder (L17), Bedienelemente für
  Custom Tags (L23), die URL-Optionen (L32, L33) und der Rest.

Eine **neue Installation** erhält die sicherere Einstellung, wo sich beide unterscheiden: L01, L02, L04 und L23 sind
eingeschaltet.

[funktionen.md](funktionen.md) führt den Standard jeder Funktion auf, und der
[Überblick](../lovestack.md#funktion-für-funktion) sagt, wie Sie jede einschalten.

## Für wen ist welche Funktion?

| Sie sind | Sehen Sie sich an |
|---|---|
| ein **Redakteur** | die Schaltfläche Kopieren (L15), Pflicht-Alternativtext (L16), getrimmte Textfelder (L17), die Korrektur am Fenster Orte (L20), versteckte Custom Tags und Bedienelemente für Custom Tags (L22, L23) |
| ein **Administrator** | die Richtlinien für Knotendaten und Registrierung (L01, L02), eindeutige Anmeldenamen (L04), Bildprüfungen (L05, L06), Logs und Deprecations (L07, L08, L12), Audit-Einträge für Orte (L13), Site-Daten (L14), Header von Fehlerseiten (L29), die URL-Optionen (L32, L33) |
| ein **Entwickler** oder **Designer** | die Template-Warnung und die Bearbeitungslinks (L09, L10), Fehlernummern der Datenbank (L11), von PHP erzeugte Namen (L18), Standard des Klassenfilters (L19), `eZURI::build()` (L25), Mail-Transport pro Nachricht (L26), `rawurlencode` (L27), Includes in Einstellungen (L28), der Schlüssel `layout` (L31) |

## Glossar

| Begriff | Bedeutung |
|---|---|
| LoveStack | die Distribution von Mugo Web des Legacy-Kernels, `mugoweb/ezpublish-legacy` |
| Exponential | dieses CMS, die von 7x gepflegte Fortsetzung des Legacy-Kernels, `se7enxweb/exponential` |
| L01 … L36 | die Nummern der übernommenen Funktionen, auf jeder Seite dieser Dokumentation verwendet |
| Opt-in | eine Funktion, die ausgeschaltet ist, bis Sie sie in einer Einstellung aktivieren |
| Standard beim Upgrade | was eine vorhandene Installation nach dem Upgrade erhält; neue Installationen erhalten möglicherweise einen anderen, sichereren Wert |
| INI-Override | Ihre Einstellungen in `settings/override/*.ini.append.php` oder einem Siteaccess, die Vorrang vor den ausgelieferten `settings/*.ini` haben |
