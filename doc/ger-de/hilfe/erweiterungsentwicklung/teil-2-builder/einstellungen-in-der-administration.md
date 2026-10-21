---
title: Einstellungen in der Administration
audience: developer, designer, app-creator, admin
category: extension-development
order: 60
summary: Setup > Ini Einstellungen zeigt jede Einstellung mit ihrem wirksamen Wert, der Datei, aus der sie stammt, und der ganzen Override-Kette; vergleicht Siteaccesses; ändert Werte sicher mit maskierten Geheimnissen. Wie ein Builder die Seite nutzt.
keywords: Einstellungsseite, Ini Einstellungen, Override-Kette, Siteaccesses vergleichen, vom Standard geändert, maskierte Geheimnisse, Debug-Leiste, settings page, ini settings, settings/view, settings/edit, override chain, compare siteaccesses, changed from default, masked secrets, debug bar settings tab
type: help
---
# Einstellungen in der Administration

Teil 2, Kapitel 6. Nicht jeder Builder arbeitet im Terminal. Die Administration hat eine Einstellungsseite, die dieselben
Fakten zeigt wie `exp:ini where` und über dieselbe Engine schreibt. Dieses Kapitel zeigt, wie ein Builder sie nutzt;
die vollständige Anleitung ist [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md).

## Öffnen

**Setup > Ini Einstellungen** (`settings/view`). Wählen Sie eine INI-Datei und einen Siteaccess und klicken Sie auf
**Einstellungen anzeigen**. Die Adresse wird zu `settings/view/<siteaccess>/<file>`, zum Beispiel
`/settings/view/admin/site.ini` unter dem Pfad der Administration; Sie können sie als Lesezeichen speichern.

## Was sie zeigt

Für jede Einstellung:

- den für den gewählten Siteaccess **wirksamen** Wert,
- die **Datei, aus der er stammt** (ihre Platzierung: Standard, Erweiterung, Siteaccess, Override),
- die **Override-Kette**: jede Datei, die ihn setzt, in der Reihenfolge, in der sie gelesen wird.

Über der Liste können Sie in einer Datei oder in allen Dateien **suchen**, nur die **vom Standard geänderten**
Einstellungen anzeigen und zwei Siteaccesses nebeneinander **vergleichen**. Der Vergleich ist der schnellste Weg zur
Antwort auf „Warum verhält sich die deutsche Website anders als die englische?“

Geheimnisse werden **nie angezeigt**: Passwörter, Schlüssel, Tokens, Salts, Zugangsdaten und das Passwort in einer
Datenbankadresse werden maskiert; Sie sehen nur, ob sie gesetzt sind.

## Einen Wert ändern

Das **Bearbeiten**-Formular (`settings/edit`) fragt nach der Ebene, in die geschrieben werden soll (globales Override,
ein Siteaccess, eine Erweiterung), zeigt die Änderung und beim Speichern:

- schreibt es die Datei über die Engine von `exp:ini` und behält Kommentare und Reihenfolge bei,
- legt es eine Sicherungskopie an,
- leert es den INI-Cache, sodass PHP-FPM und Velocity bei ihrer nächsten Anfrage den neuen Wert verwenden.

Einige Einstellungen liest Velocity nur beim Start; für diese sagt die Seite, dass Sie
`./console exp:velocity restart` ausführen sollen.

## Der Einstellungs-Tab der Debug-Leiste

Ist die Debug-Ausgabe eingeschaltet, zeigt der Tab **Einstellungen** der Exp Debug-Leiste jede Debug-Einstellung mit
ihrem Wert, ihrer Quelldatei und einem Steuerelement, um sie in einer Ebene Ihrer Wahl zu ändern. Jede Änderung läuft
über dieselbe Engine, wird protokolliert und kann aus der Leiste rückgängig gemacht werden. Dafür ist die Richtlinie
`setup/setup` nötig. Siehe [Die Exp Debug-Leiste](../../../bc/6.0/debug-leiste.md).

## Wer sie nutzen darf

Die Einstellungsseiten brauchen die Richtlinien des Moduls `setup`. Geben Sie sie nur Personen, die das Verhalten der
ganzen Installation ändern dürfen; eine Einstellungsänderung kann Sicherheitsfunktionen genauso leicht abschalten, wie
sie ein Design einschaltet. Rollen werden in [Sektionen, Rollen und Zugriff für Builder](sektionen-rollen-und-zugriff-fuer-builder.md)
erklärt.

## Browser oder Terminal?

| Aufgabe | Besser im |
|---|---|
| Nachsehen, woher ein Wert kommt | beides |
| Zwei Siteaccesses vergleichen | Browser |
| Einen Wert einmal ändern | beides |
| Viele Werte ändern, wiederholbar oder in einem Deployment | Terminal (`exp:ini`, mit `--json` skriptfähig) |
| Einstellungen in eine Erweiterung verschieben | Terminal (`exp:ini move`, `move-all`) |

## Verwandte Seiten

- Zurück: [Einstellungen mit exp:ini ändern](der-befehl-exp-ini.md)
- Weiter: [.ini.append.php-Dateien und sichere Einstellungen](append-php-dateien-und-sicherheit.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
