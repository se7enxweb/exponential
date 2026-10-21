---
title: Der Packer-Cache und Deploys
audience: designer, admin
category: theme-design
order: 240
summary: Welche Caches nach einer Änderung an Stylesheet, Skript, Template oder Einstellungen eines Designs zu leeren sind, warum gepackte Dateien und Template-Blöcke zusammengehören und der eine Deploy-Befehl, der das auf Installationen mit Velocity erledigt.
keywords: Deploy, Cache leeren, Packer-Cache, ezjscore-packer, template-block, Deploy einer Design-Änderung, exp:velocity deploy, --packer, ezcache, hartes Neuladen, veraltetes CSS, deploy, cache clear, packer cache, design change deploy, hard reload, stale css
type: help
---
# Der Packer-Cache und Deploys

Diese Seite ist für Designer, die ein Design geändert haben und die Änderung auf der Website sehen möchten. Sie beantwortet: was für jede
Art von Änderung zu leeren ist, und warum.

## Inhalt

1. [Was zu leeren ist, nach Art der Änderung](#was-zu-leeren-ist-nach-art-der-änderung)
2. [Warum gepackte Dateien und Template-Blöcke zusammengehören](#warum-gepackte-dateien-und-template-blöcke-zusammengehören)
3. [Der Deploy-Befehl](#der-deploy-befehl)
4. [Nach dem Deploy](#nach-dem-deploy)

## Was zu leeren ist, nach Art der Änderung

| Sie haben geändert | Leeren | Befehl |
|---|---|---|
| eine `.css`- oder `.js`-Datei | nichts: Der Name der gepackten Datei trägt die Zeit der Quelle, eine neue wird erzeugt | den Browser hart neu laden |
| eine Stylesheet-Liste (Aufruf von `ezcss_load`, Liste in `design.ini`) | die Template-Caches, die den Link halten | `--clear-id=template-block` und `--clear-id=content` für zwischengespeicherte Vollansichten |
| ein vorhandenes `.tpl` | kompilierte Templates, Ansichten, Blöcke | `--clear-id=template,content,template-block` |
| ein **neues** `.tpl` oder eine neue Override-Regel | auch den Override-Cache | `--clear-id=template-override` (plus das Obige) |
| eine INI-Datei | die INI-Caches | `--clear-tag=ini` |
| eine Funktion `ezjscServer_*` oder ihre Einstellungen | gepackte Dateien und Template-Blöcke zusammen | `--clear-id=ezjscore-packer,template-block` |
| eine Design-Erweiterung aktiviert | Design-Basen und Autoloads | `--clear-id=design_base`, `php bin/php/ezpgenerateautoloads.php -e` |

Alle Befehle lauten `php bin/php/ezcache.php <option>`; `--list-ids` listet jede id.

## Warum gepackte Dateien und Template-Blöcke zusammengehören

Der Seitenkopf der Administration liegt in einem `{cache-block}`, und der zwischengespeicherte Kopf nennt die gepackten Dateien. Wird nur
`ezjscore-packer` geleert, zeigt der zwischengespeicherte Kopf auf Dateien, die es nicht mehr gibt, und die Administration verliert ihre
Stile und Skripte, bis auch `template-block` geleert wird. Leeren Sie beide zusammen und in dieser Reihenfolge
(`doc/eng-us/bc/6.0/velocity-engines.md`, „Deploying a PHP change“).

Eine bearbeitete `.css`-Datei braucht das nicht: Ihre gepackte Datei bekommt von selbst einen neuen Namen.

## Der Deploy-Befehl

Installationen, die den Anwendungsserver Velocity verwenden, haben einen Befehl, der jeden Schritt in der richtigen Reihenfolge ausführt
(Autoloads, INI-Caches, Template-Caches, PHP neu laden, Velocity neu starten, Content- und Template-Block-Caches):

```bash
./console exp:velocity deploy --allow-root-user            # templates, settings, PHP
./console exp:velocity deploy --packer --allow-root-user   # also packed scripts and styles
./console exp:velocity deploy --dry-run --allow-root-user  # show the steps only
```

`--packer` leert `ezjscore-packer` direkt vor `template-block` und nirgends sonst. Einzelheiten:
[Velocity engines](../../../../eng-us/bc/6.0/velocity-engines.md).

## Nach dem Deploy

- Laden Sie hart neu (Umschalttaste mit dem Neuladen-Knopf oder „Cache leeren und hart neu laden“ der Entwicklerwerkzeuge), wenn ein
  Browser noch die alte Datei zeigt.
- Auf Websites mit Velocity ist dessen Antwort-Cache getrennt: `./console exp:velocity cache clear`.
- Öffnen Sie die Seiten in einem privaten Fenster, abgemeldet, um zu sehen, was Besucher sehen.

## Verwandte Seiten

- [Der Packer](der-packer.md)
- [Caching für Designer](caching-fuer-designer.md)
- [Ein Design debuggen](ein-design-debuggen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
