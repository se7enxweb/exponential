---
title: Kunden-Websites aktuell halten
audience: partner
category: partnership
order: 680
summary: Wie Partner Kunden-Websites auf gepflegten Exponential-Releases und unterstützten PHP-Versionen halten - eine Bestandsliste, die Support-Zusagen des Projekts, Wartungsverträge, Upgrade-Rhythmus und wie Sie mit Kunden über Upgrades sprechen.
keywords: Wartung, Upgrade, Releases, PHP-Versionen, Bestandsliste, Wartungsvertrag, Sicherheitskorrekturen, Support-Zusage, Lebenszyklus, maintenance, releases, PHP versions, inventory, maintenance contract, security fixes, support commitment, lifecycle
type: help
---
# Kunden-Websites aktuell halten

Diese Seite ist für Inhaber und technische Leitungen von Partnern, die nach dem Start für Kunden-Websites verantwortlich
sind. Eine Website, die nicht aktualisiert wird, wird zum Sicherheitsrisiko und irgendwann zu einer teuren Rettung.

## Die Zusagen des Projekts

| Zusage | Quelle |
|---|---|
| „Exponential 6.0 ist die gepflegte Linie. Sicherheitskorrekturen werden auf dem Branch `main` gemacht ... und mit dem nächsten Release 6.0.x veröffentlicht.“ | SECURITY.md |
| 5.x, 4.x, 3.x und älter werden nicht unterstützt | SECURITY.md |
| PHP 8.0 bis 8.5 werden für die Linie 6.0.15 unterstützt; PHP 7.4 nur bis 6.0.7 | README, `doc/en/us/install/02-requirements.md` |
| „Ein veröffentlichtes Tag wird nie geändert“ | SECURITY.md |

Die vollständige Liste: [Wer ist die Exponential Foundation?, „Die Support-Zusage“](../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung).

## Eine Bestandsliste führen

```text
Customer site inventory
Site | Exponential version | PHP | Database | Engine | Extensions (versions) | Last upgrade | Next planned
-----|---------------------|-----|----------|--------|-----------------------|--------------|-------------
```

Prüfen Sie sie monatlich. Jede Website unter dem aktuellen Release 6.0.x oder auf einer PHP-Version kurz vor ihrem Ende
bekommt einen Plan.

## Wartungsverträge

Bieten Sie mit jedem Projekt Wartung an. Ein nützlicher Vertrag legt fest:

- welche Releases eingespielt werden und wie schnell (Sicherheitskorrekturen schneller als Funktions-Releases);
- dass jedes Release zuerst auf Staging getestet wird ([Releases testen, bevor Ihre Kunden es tun](releases-testen.md));
- PHP-Upgrades als Teil der Leistung;
- was nicht enthalten ist (neue Funktionen, Redesigns).

## Mit Kunden sprechen

- Erklären Sie Upgrades als Schutz, nicht als Verkauf.
- Zeigen Sie die Punkte des Änderungsprotokolls, die für den Kunden wichtig sind.
- Planen Sie große Schritte (PHP-Versionen, Ersatz von Erweiterungen) ein Jahr im Voraus
  ([Release- und Upgrade-Planung](../fuehrung/release-und-upgrade-planung.md)).

## Websites aus der Zeit von eZ Publish

Viele Organisationen betreiben noch Websites, die auf der alten Codebasis gebaut wurden, bevor sie zu Exponential wurde.
Sie sind die natürlichen Kunden von Partnern. Die Migrationskapitel `doc/en/us/install/14-migrating-from-4x.md` bis
`17-migration-reference.md` beschreiben die Wege.

## Verwandte Seiten

- [Releases testen, bevor Ihre Kunden es tun](releases-testen.md)
- [Sicherheitspflichten von Partnern](sicherheitspflichten.md)
- [Release- und Upgrade-Planung](../fuehrung/release-und-upgrade-planung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
