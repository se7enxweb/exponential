# cjw-exponential-media-site-data: Installationsdaten der Media-Site

Diese Seite ist für Entwickler, die das Starterprojekt CJW Exponential Platform Nexus installieren. Das Paket
`cjw-exponential-media-site-data` enthält den vollständigen Inhalt der Media-Site (Schema und Inhalte), den der
Installationstyp `cjw-exponential-media` verwendet.

## Verwenden

Führen Sie im Starterprojekt aus:

```bash
php bin/console ezplatform:install cjw-exponential-media
```

## Was das Paket enthält

```
cjw-exponential-media/
  data.sql        full content data (schema and content)
  storage/        binary file storage placeholder
schema/
  schema.sql      database schema only
```

Veröffentlicht als 1.0.0 am 3. Juli 2026. Am selben Tag erhielt `composer.json` den Pakettyp, `php >= 8.1` und den
Branch-Alias `dev-main` für Packagist. Lizenziert unter GPL-2.0-only.

Dieses Paket gehört zur Plattformseite des Projekts (Symfony und Ibexa); siehe die
[Übersicht über das Ökosystem](../../../geschichte/oekosystem.md). Es steht hier, weil es ein Datenpaket einer
Extension-Suite ist.

## Verwandte Seiten

- [Plattform-Starter Nexus](../plattform-nexus-starter.md)
- [Chronik](../../../geschichte/erweiterungen/cjw-exponential-media-site-data.md) und [Versionshinweise](../../../../changelogs/extensions/cjw-exponential-media-site-data.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/cjw-exponential-media-site-data.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- [Monat: 2026-07 (alle Extensions)](../../../geschichte/erweiterungen/monate/2026-07.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
