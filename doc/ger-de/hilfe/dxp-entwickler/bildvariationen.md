---
title: Bildvariationen
audience: dxp-developer
category: dxp-development
order: 23
summary: Wie die DXP Bilder in Größen ausliefert - Variationen je Siteaccess-Geltungsbereich festgelegt, bei Bedarf von Liip Imagine erzeugt, aus Twig verwendet, nach einer Änderung geleert - sowie die Konsolenbefehle für Originale und Speicherpfade.
keywords: Bildvariationen, Bildvarianten, Liip Imagine, image_variations, geometry/scaledownonly, ibexa_image_alias, liip:imagine:cache:remove, resize-original, normalize-paths, ImageMagick, image variations, image aliases
type: help
---
# Bildvariationen

Diese Seite richtet sich an Entwickler, die festlegen, wie Bilder zugeschnitten und skaliert werden. Auf der DXP
speichert ein Bildfeld das Original; Variationen (Größen) erzeugt Liip Imagine bei Bedarf, wenn ein Template sie zum
ersten Mal anfordert (Anleitung des Skeletons, Abschnitt 13). Die Idee entspricht den Bildvarianten in Exponential 6.

## Inhalt

1. [Variationen festlegen](#variationen-festlegen)
2. [Sie in Twig verwenden](#sie-in-twig-verwenden)
3. [Erzeugte Variationen leeren](#erzeugte-variationen-leeren)
4. [Mit Originalen arbeiten](#mit-originalen-arbeiten)
5. [Ratschläge](#ratschläge)

## Variationen festlegen

Variationen sind eine siteaccessbewusste Einstellung unter `ibexa.system.<scope>.image_variations`. Das Beispiel der
Anleitung:

```yaml
ibexa:
    system:
        site_group:
            image_variations:
                small:
                    reference: ~
                    filters:
                        - { name: geometry/scaledownonly, params: [160, 120] }
                medium:
                    reference: ~
                    filters:
                        - { name: geometry/scaledownonly, params: [480, 360] }
                large:
                    reference: ~
                    filters:
                        - { name: geometry/scaledownonly, params: [960, 720] }
```

Mit `reference` beginnt eine Variation bei einer anderen Variation statt beim Original. ImageMagick ist optional; sein
Pfad ist `IMAGEMAGICK_PATH` ([Umgebungsvariablen](umgebungsvariablen.md#suche-cache-e-mail-und-der-rest)).

## Sie in Twig verwenden

```twig
{{ ibexa_render_field(content, 'image', { parameters: { alias: 'medium' } }) }}

{% set variation = ibexa_image_alias(content.getField('image'), content.versionInfo, 'large') %}
{% if variation %}
    <img src="{{ variation.uri }}" width="{{ variation.width }}" height="{{ variation.height }}" alt="">
{% endif %}
```

Geben Sie jedem Bild Breite und Höhe, damit die Seite beim Laden nicht springt, und echten Alternativtext aus einem
Feld, wo das Bild eine Bedeutung trägt.

## Erzeugte Variationen leeren

Nachdem Sie die Filter einer Variation geändert haben, entfernen Sie die erzeugten Dateien, damit sie neu gebaut werden:

```bash
php bin/console liip:imagine:cache:remove                  # all variations
php bin/console liip:imagine:cache:remove --filter=small   # one variation
php bin/console cache:clear
```

## Mit Originalen arbeiten

| Befehl | Verwendung |
|---|---|
| `exponential:images:resize-original <field> <content type> --filter=<variation>` | gespeicherte Originale gesammelt verkleinern, zum Beispiel nachdem Redakteure Bilder in Kameragröße hochgeladen haben |
| `exponential:images:normalize-paths` | Speicherpfade von Bildfeldern korrigieren |
| `exponential:io:migrate-files` | Binärdateien zwischen Speicher-Handlern verschieben |

`resize-original` ändert gespeicherte Dateien: Führen Sie es zuerst an einer Kopie aus
([Konsolenbefehle](konsolenbefehle-und-umbenennungen.md)).

## Ratschläge

- Legen Sie einen kleinen Satz von Variationen fest, der zu den Breakpoints des Designs passt; jede kostet für jedes Bild
  Speicherplatz.
- Erzeugte Variationen liegen unter `public/var/`; der Webserver muss dort schreiben können
  ([Dateiberechtigungen](dateiberechtigungen.md)).
- Ein 404 für eine Variation nach einer Datenbankumstellung bedeutet meist, dass Zeilen fehlen
  ([Datenbanken, nach einer Umstellung](datenbanken-mysql-postgresql-sqlite.md#nach-einer-umstellung)).

## Verwandte Seiten

- [Twig-Funktionen für den Alltag](twig-funktionen-fuer-den-alltag.md#bilder)
- [Bildvarianten in Themes von Exponential 6](../designer/theme-design/bildaliase.md)
- [Bildleistung](../designer/theme-design/bildleistung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
