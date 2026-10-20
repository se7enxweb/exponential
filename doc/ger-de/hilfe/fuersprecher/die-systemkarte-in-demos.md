---
title: Die Systemkarte in Demos
audience: advocate, press
category: advocacy
order: 46
summary: Beginnen Sie eine Demo, einen Vortrag oder ein Video mit ./console exp:about - ein Bildschirm mit dem Exponential-„e“, den Fakten der Installation und dem, was das Projekt ist, seiner Lizenz und seinen Links, alles aus der Dokumentation entnommen. Wie Sie sie gut zeigen und was sie nie preisgibt.
keywords: exp:about, exp:expfetch, Systemkarte, Demo, Vortrag, Bildschirmfoto, Video, Screencast, Terminal, Logo, Schriftzug, Farbschema, helles Farbschema, Animation, Presse, Fürsprecher, system card, demo, talk, screenshot, video, screencast, terminal, logo, wordmark, theme, light theme, animate, press, advocate
type: help
---
# Die Systemkarte in Demos

Diese Seite ist für Fürsprecher und Kreative, die Exponential in einem Vortrag, einem Stream oder einem Video zeigen.
Wenn jemand fragt „Was ist das?“, beantwortet ein einziger Befehl die Frage auf einem Bildschirm:

```bash
./console exp:about
```

Er zeichnet das orangefarbene Exponential-„e“ (in einem breiten Terminal mit dem Regenbogen-Schriftzug EXPONENTIAL),
die Fakten der Installation (Version, PHP, die Engine, die Datenbank) und danach, was Exponential ist, die
Exponential Foundation und 7x, Exponential Velocity, die Lizenz (GPL 2.0 oder neuer für den Code, GFDL 1.3 oder neuer
für die Dokumentation), die Links und wie Sie beitragen oder Partner werden. Jeder Satz des Projektteils stammt aus
der Dokumentation, Sie können ihn also laut aussprechen, ohne ihn noch einmal zu prüfen. Referenz:
[Die Systemkarte](../../../eng-us/features/6.0/system-card.md).

## Gut zeigen

- **Breite.** Machen Sie das Terminal mindestens 82 Spalten breit, damit das große „e“ mit dem Schriftzug erscheint;
  vergrößern Sie die Schrift, nicht das Fenster, damit das Layout erhalten bleibt.
- **Beamer oder heller Hintergrund.** `--theme=light` hat dunklere Farben; `--theme=high-contrast` ist auf einem
  schwachen Beamer am besten lesbar.
- **Ein eröffnender Effekt.** `--animate` blendet das Logo ein und bewegt seinen Farbverlauf in weniger als einer
  Sekunde.
- **Nur das Projekt.** `./console exp:about --section=about,links --no-logo` ergibt einen Bildschirm wie eine Folie.
- **Ein Standbild für eine Folie.** `./console exp:about --plain > about.txt` liefert Text ohne Escape-Codes, den Sie in
  Folien einfügen können.
- **Stattdessen die Maschine.** `./console exp:expfetch` ist dieselbe Karte ohne den Projektteil: gut für „Das ist der
  Server, den wir gerade installiert haben“.

## Was sie nie zeigt

Die Karte gibt nie ein Passwort, einen Schlüssel, ein Token, einen Datenbankbenutzer oder die vollständige Adresse
der Datenbank aus, auch nicht die Werte der Velocity-envvars. Den Hostnamen und das Betriebssystem zeigt sie
allerdings: Verwenden Sie in einem Stream eine Demo-Maschine mit einem neutralen Hostnamen
([Sicherheit bei Live-Demos](sicherheit-bei-live-demos.md)). Hinweise wie „Demomodus ist aktiv“ sind auf einer
Demo-Installation zu erwarten; sagen Sie das, wenn jemand fragt.

## Verwandte Seiten

- [Die Systemkarte](../../../eng-us/features/6.0/system-card.md)
- [Demos zeigen](demos-zeigen.md)
- [Sicherheit bei Live-Demos](sicherheit-bei-live-demos.md)
- [Demo-Skript: Überblick in fünf Minuten](../../../eng-us/help/press/demo-script-five-minute-overview.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
