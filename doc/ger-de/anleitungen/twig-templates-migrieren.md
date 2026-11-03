# Twig-Templates: ein Design migrieren

Diese Anleitung ist für Site-Bauer, die ein ganzes Design von `.tpl` (oder `.stpl`) nach `.twig` umziehen. Sie ist ein
Plan in fünf Schritten: ein eigener Siteaccess, die Umwandlung, die Prüfung, der Vergleich Seite für Seite und das
Gleichhalten der Engines bis zur Umstellung. Jeder Schritt benutzt Befehle, die es in jeder Installation gibt; der
Konverter selbst ist in [Der Konverter und die Portierung eines Designs](twig-templates-konverter.md) beschrieben.

## 1. Ein Siteaccess für das Twig-Design

Lassen Sie den `.tpl`-Siteaccess weiterlaufen und fügen Sie einen zweiten hinzu, der das umgewandelte Design benutzt,
sodass beide denselben Inhalt nebeneinander rendern: Kopieren Sie die Einstellungen des Siteaccess in einen neuen
(`settings/siteaccess/<site>_twig/`), setzen Sie das umgewandelte Design an die erste Stelle seiner
`[DesignSettings] SiteDesign` / `AdditionalSiteDesignList[]` und fügen Sie ihn mit einer Pfad- oder Host-Zuordnung zu
`[SiteAccessSettings] AvailableSiteAccessList[]` hinzu.

## 2. Umwandeln

```bash
./console exp:template:convert:tpl-to-twig -s <site>_twig \
    --from=extension/<ext>/design/<design> --to=extension/<ext>/design/<design>_twig \
    --override-ini=settings/siteaccess/<site>_twig/override.ini.append.php
```

Jedes `.tpl` des Designs wird an derselben Stelle unterhalb von `--to` zu einem `.twig`, und die Override-Regeln des
Siteaccess werden für die neuen Dateien geschrieben. Bewahren Sie die NOTE-Zeilen der Ausgabe auf: Jede nennt ein
Konstrukt, das der Konverter nicht genau schreiben konnte. Das Media-Design des Demo-Inhalts (517 Templates) ergab 126
Hinweise in 58 Templates, jeder davon zu einem Fehler in der `.tpl`-Quelle selbst (ein `{if}` ohne Bedingung, ein
Parser-Fehler), den das `.twig` beibehält: Es druckt, was das `.tpl` gedruckt hat. Korrigieren Sie eine solche Quelle
und wandeln Sie erneut um.

## 3. Prüfen

```bash
./console exp:template:check --design-dir=extension/<ext>/design/<design>_twig --level=strict
```

Jedes `.twig` muss kompilieren; die strenge Stufe meldet außerdem Operatoren, die es nicht gibt, Includes, die sich
nicht auflösen lassen, und was die Sandbox ablehnen würde.

## 4. Seiten vergleichen

Fragen Sie dieselben Seiten bei beiden Siteaccesses an und vergleichen Sie das HTML. In einer Entwicklungsinstallation
druckte das umgewandelte Media-Design für 9 Seiten (Startseite, Listenseiten, Artikel, ein Autor, das Kontaktformular)
Byte für Byte, was das `.tpl`-Design druckte, auf Apache mit PHP-FPM und auf Velocity; der einzige erlaubte Unterschied
ist das Siteaccess-Präfix in Links. Vergleichen Sie bei abgeschalteter Debug-Ausgabe, oder schneiden Sie das
Debug-Panel vor dem Vergleich aus beiden Seiten heraus. Angemeldete Seiten, Formulare mit Validierungsfehlern und der
Warenkorb verdienen einen eigenen Blick.

## 5. Die Engines gleich halten, dann umstellen

Ändern Sie bis zur Umstellung das `.tpl` und wandeln Sie erneut um: Ein zweiter Lauf schreibt nur, was sich geändert
hat, und ersetzt nie ein `.twig`, das von Hand geändert wurde (er sagt SKIP). Sobald der Twig-Siteaccess der ist, den
Besucher benutzen, sind die `.twig`-Dateien die Quellen; wandeln Sie mit `twig-to-tpl` um, wenn Sie das `.tpl`-Design
zurück brauchen.

## Geschwindigkeit

Gecachte Seiten werden ausgeliefert, bevor irgendein Template läuft, sie sind mit `.twig` also so schnell wie mit
`.tpl`. Ungecacht rendert eine umgewandelte Seite etwa so schnell wie die `.tpl`-Seite oder schneller; siehe
[Leistung](twig-templates-leistung.md). Hält das Design die ganze Seite in einem Cache-Block, halten Sie das
Benutzermenü (oder jeden besucherbezogenen Teil) außerhalb davon, wie es [Caching](twig-templates-caching.md) zeigt.

## Siehe auch

- [Der Konverter und die Portierung eines Designs](twig-templates-konverter.md)
- [Von .tpl zu .twig umziehen](../hilfe/template-sprache/von-tpl-zu-twig-umziehen.md)
- [Smarty-Templates: ein Design migrieren](smarty-templates-migrieren.md)
