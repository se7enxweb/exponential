---
title: Ein Tutorial schreiben, das funktioniert
audience: advocate
category: advocacy
order: 51
summary: Wie Sie ein Exponential-Tutorial schreiben, dem Leser wirklich folgen können - ein getesteter Ausgangspunkt, Voraussetzungen, nummerierte Schritte mit je einem Kontrollpunkt, echte Befehle, erwartete Ergebnisse, Fehlersuche und eine Versionszeile - mit einer Vorlage und einem Testablauf.
keywords: Tutorial, Anleitung, Leitfaden, Schritte, Kontrollpunkte, Voraussetzungen, Befehle, Testen, Fehlersuche, Version, Vorlage, Entwickler, Redakteure, tutorial, how-to, guide, steps, checkpoints, prerequisites, commands, testing, troubleshooting, version, template, developers, editors
type: help
---
# Ein Tutorial schreiben, das funktioniert

Diese Seite ist für Fürsprecher, die Schritt-für-Schritt-Tutorials schreiben. Ein Tutorial scheitert in dem Moment, in
dem der Bildschirm eines Lesers nicht zu Ihrem passt. Die Methode unten, übernommen aus der Art, wie die Hilfe selbst
geschrieben ist (jeder Teil „endet mit etwas, das Sie prüfen können“, wie es die
[Anleitung zu Templates und Design](../../anleitungen/templates-und-design.md) sagt), ergibt Tutorials, die funktionieren.

## Inhalt

1. [Die Regeln eines funktionierenden Tutorials](#die-regeln-eines-funktionierenden-tutorials)
2. [Den Ausgangspunkt wählen](#den-ausgangspunkt-wählen)
3. [Die Vorlage](#die-vorlage)
4. [Schritte schreiben](#schritte-schreiben)
5. [Testen Sie es wie ein Fremder](#testen-sie-es-wie-ein-fremder)
6. [Es lebendig halten](#es-lebendig-halten)

## Die Regeln eines funktionierenden Tutorials

1. **Ein Ziel**, im ersten Satz genannt.
2. **Ein nachvollziehbarer Start**: eine frische Installation, auf dokumentierte Weise angelegt.
3. **Jeder Schritt endet mit einem Kontrollpunkt**: etwas, das der Leser sehen kann.
4. **Echte Befehle und echte Namen von Schaltflächen**, vom Bildschirm kopiert, nicht aus dem Gedächtnis.
5. **Die Version**, die Sie verwendet haben, und das Datum, an dem Sie geprüft haben.
6. **Was zu tun ist, wenn es scheitert**, für die zwei oder drei wahrscheinlichen Fehler.
7. **Links zu den Hilfeseiten**, die tiefer gehen.

## Den Ausgangspunkt wählen

Der einfachste nachvollziehbare Start ist eine frische Installation auf SQLite
([Installation mit einem Befehl](../../../eng-us/features/6.0/install-in-one-command.md)):

```bash
./console exp:install --dry-run
./console exp:install
```

Beginnen Sie bei Tutorials über Erweiterungen mit einer Installation aus Git, damit Leser später beitragen können
([Mit Git entwickeln](../mit-git-entwickeln.md)). Sagen Sie bei Tutorials für die Redaktion, welche Rolle der Leser
braucht (zum Beispiel Editor oder Administrator).

## Die Vorlage

```text
# <Do something specific>

After this tutorial you can <goal>. It takes about <n> minutes.

Tested with Exponential <6.0.x>, PHP <8.x>, <engine>, <database>, admin4 design, on <date>.

## Before you start
- <prerequisite 1, with a link>
- <prerequisite 2>

## Step 1: <verb + object>
<What to do, in one or two sentences.>
    <command or click path in bold>
You should now see: <checkpoint>.

## Step 2: ...

## If something goes wrong
| You see | Do |
|---|---|
| <message> | <fix, with a link> |

## Where to go next
- <help page>
- Questions: share.exponential.earth/forums, t.me/exponentialcms
```

Für ein deutschsprachiges Tutorial übersetzen Sie die Überschriften der Vorlage und behalten Befehle und Pfade bei.

## Schritte schreiben

- **Beginnen Sie mit einem Verb**: „Öffnen Sie“, „Drücken Sie“, „Führen Sie aus“, „Tippen Sie“.
- **Schreiben Sie Klickpfade fett**, wie auf dem Bildschirm: **Setup > Klassen**, **Zur Veröffentlichung schicken**.
- **Eine Handlung pro Schritt** oder eine kurze nummerierte Liste innerhalb des Schritts.
- **Zeigen Sie die erwartete Ausgabe**, gekürzt, wenn sie lang ist, mit `...` an der gekürzten Stelle.
- **Erklären Sie das Warum** in einem Satz, wo es hilft; verlinken Sie für mehr die Seite zum Konzept.
- **Keine Geheimnisse** in Beispielen: Platzhalter wie `<your-password>`, erfundene Namen, Adressen mit `example.com`.
- **Codeblöcke** haben eine Sprache (`bash`, `php`), damit die Hilfe und Blogs sie hervorheben.

| Statt | Schreiben Sie |
|---|---|
| „Klicken Sie auf die Schaltfläche.“ | „Drücken Sie **Zur Veröffentlichung schicken**.“ |
| „Konfigurieren Sie die Einstellungen.“ | „Setzen Sie in `settings/site.ini` ...“ (mit genauem Block und Schlüssel) |
| „Es sollte jetzt funktionieren.“ | „Die Seite zeigt jetzt den neuen Titel.“ |

## Testen Sie es wie ein Fremder

1. Legen Sie eine frische Installation genau so an, wie es das Tutorial sagt.
2. Folgen Sie dem Tutorial Wort für Wort und kopieren Sie Befehle aus dem Text, nicht aus Ihrer Terminal-Historie.
3. Notieren Sie jede Stelle, an der Sie raten mussten. Korrigieren Sie dort den Text.
4. Bitten Sie eine andere Person, ihm zu folgen, und schauen Sie still zu. Ihre Fragen sind Ihre fehlenden Sätze.

## Es lebendig halten

- Führen Sie das Tutorial nach jedem Release erneut aus oder prüfen Sie zumindest das Änderungsprotokoll auf geänderte
  Bildschirme ([Änderungsprotokolle](../../../changelogs/README.md)).
- Aktualisieren Sie die Versionszeile, wenn Sie erneut testen.
- Wenn ein Schritt nicht mehr funktioniert, setzen Sie oben einen sichtbaren Hinweis, bis Sie ihn korrigiert haben.
- Wenn Ihr Tutorial allen Benutzern helfen würde, schlagen Sie es für die Hilfe selbst vor ([Die Hilfe verbessern](die-hilfe-verbessern.md)).

## Verwandte Seiten

- [Einen Blogbeitrag schreiben](einen-blogbeitrag-schreiben.md)
- [Einen Screencast aufnehmen](einen-screencast-aufnehmen.md)
- [Auf Share und Projects veröffentlichen](auf-share-und-projects-veroeffentlichen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
