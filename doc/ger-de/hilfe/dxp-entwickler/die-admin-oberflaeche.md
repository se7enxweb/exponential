---
title: Die Admin-Oberfläche
audience: dxp-developer
category: dxp-development
order: 16
summary: Die Administrationsoberfläche der DXP unter /adminui/ für Entwickler - was Redakteure und Administratoren dort tun, was der se7enxweb-Fork ändert, wie ihre Assets gebaut und übersetzt werden und was nach einer Aktualisierung neu gebaut werden muss.
keywords: Admin-Oberfläche, adminui, Administrationsoberfläche, se7enxweb/admin-ui, admin-ui-assets, yarn ibexa:build, bazinga-Übersetzungen, Inhaltsbaum, Inhaltstypen, Rollen, Branding, admin UI, administration interface, bazinga translations, content tree, content types, roles
type: help
---
# Die Admin-Oberfläche

Diese Seite richtet sich an Entwickler, die die Administrationsoberfläche eines DXP-Projekts installieren, erweitern
oder betreuen. Redakteure und Administratoren arbeiten unter `/adminui/`. Es ist die Administrationsoberfläche der
Plattform, nicht die Administrationsdesigns von Exponential 6.

## Inhalt

1. [Was dort geschieht](#was-dort-geschieht)
2. [Was der Fork ändert](#was-der-fork-ändert)
3. [Ihre Assets bauen](#ihre-assets-bauen)
4. [Übersetzungen](#übersetzungen)
5. [Was einen Neubau braucht](#was-einen-neubau-braucht)
6. [Redakteure unterstützen](#redakteure-unterstützen)

## Was dort geschieht

Aus [Die Administrationsoberfläche von Exponential Platform](../../funktionen/6.0/plattform-admin-ui-fork.md): der
Inhaltsbaum, das Bearbeiten von Inhalten, die Suche, Inhaltstypen, Benutzer und Rollen sowie die Medienbibliothek.
Administratoren verwalten dort außerdem Sektionen, Sprachen und Systeminformationen (`se7enxweb/system-info`).

## Was der Fork ändert

`se7enxweb/admin-ui` ersetzt `ibexa/admin-ui` und `ezsystems/ezplatform-admin-ui`. Es ändert drei Dinge:

1. **Branding**: das Logo von Exponential Platform DXP, Favicons, Seitentitel, Begrüßungstexte und die E-Mail zum
   Zurücksetzen des Passworts.
2. **Paketidentität**: Es wird unter dem Namen se7enxweb installiert und deklariert die Upstream-Namen als ersetzt.
3. **Build-Pfade**: Webpack Encore liest aus `vendor/se7enxweb/...`, sodass sich die Oberfläche mit ausschließlich
   installierten se7enxweb-Paketen bauen lässt.

Release-Geschichte: [Releases der Admin-Oberfläche](../../../changelogs/extensions/admin-ui.md).

## Ihre Assets bauen

Die Admin-Assets werden getrennt von den Assets der Website gebaut und nicht bei `composer install`; Produktionsserver
brauchen also kein Node.js, wenn Sie gebaute Assets bereitstellen.

```bash
php bin/console assets:install --symlink --relative public    # populates public/bundles/ and var/encore/
yarn ibexa:build      # production
yarn ibexa:dev        # development, with source maps
yarn ibexa:watch      # rebuild on change
```

Alle Yarn-Skripte `ibexa:*` laufen über die `webpack.config.js` des Projekts mit `--config-name ibexa`, die den Alias
`@ibexa-admin-ui` auf `vendor/se7enxweb/admin-ui` zeigen lässt (Anleitung des Skeletons, Abschnitt 9). Findet der Build
eine Datei unter `vendor/ibexa/...` nicht, ist ein Upstream-Paket neben dem Fork installiert
([Die se7enxweb-Forks und `replace`](die-se7enxweb-forks-und-replace.md#die-regel-nie-beide)).

## Übersetzungen

Die JavaScript-Übersetzungen der Oberfläche werden nach `public/assets` ausgegeben:

```bash
php bin/console bazinga:js-translation:dump public/assets --merge-domains
```

Nur die englischen Texte wurden auf den neuen Produktnamen geändert; andere Sprachen zeigen möglicherweise noch den
Upstream-Namen ([Administrationsoberfläche der Plattform, Grenzen](../../funktionen/6.0/plattform-admin-ui-fork.md#grenzen)).

## Was einen Neubau braucht

| Änderung | Neubau |
|---|---|
| `composer update` brachte ein neues `se7enxweb/admin-ui` | `yarn ibexa:build` |
| JavaScript oder SCSS unter `Resources/public/` eines Bundles | `yarn ibexa:build` |
| `webpack.config.js` oder `ibexa.webpack.config.manager.js` | `yarn ibexa:build` |
| Die Konfiguration des Rich-Text-Editors | `yarn ibexa:build` |
| Übersetzungstexte | der Befehl `bazinga:js-translation:dump` |

## Redakteure unterstützen

- Geben Sie Redakteuren eigene Konten und Rollen; teilen Sie nie das Administratorkonto.
- Das Administratorpasswort des Installers ist öffentlich: Ändern Sie es sofort
  ([Installation mit dem Skeleton](installation-mit-dem-skeleton.md#nach-der-installation)).
- Redaktionelle Aufgaben in der Oberfläche der Plattform unterscheiden sich von Exponential 6; die Hilfe für
  Redakteure von Exponential 6 in diesem Hilfe-Center beschreibt `/adminui/` nicht.

## Verwandte Seiten

- [Die Administrationsoberfläche von Exponential Platform](../../funktionen/6.0/plattform-admin-ui-fork.md)
- [Assets mit Yarn und Encore bauen](assets-mit-yarn-und-encore-bauen.md)
- [Siteaccesses](siteaccesses-site-und-admin.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
