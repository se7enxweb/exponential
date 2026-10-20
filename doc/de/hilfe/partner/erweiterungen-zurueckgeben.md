---
title: Erweiterungen an die Upstream-Projekte zurückgeben
audience: partner
category: partnership
order: 640
summary: Wie Partner bestehende Erweiterungen gemeinsam mit ihren Autoren verbessern - das Upstream-Projekt finden, eine Erweiterung auf aktuelles Exponential und PHP bringen, Änderungen zurückschicken, Forks und Umbenennungen mit Respekt und die Pflege einer verwaisten Erweiterung übernehmen.
keywords: Erweiterung, Upstream, Fork, Umbenennung, Betreuer, Pull Request, Upgrade, PHP-Kompatibilität, verwaist, ursprünglicher Autor, Nennung, extension, upstream, fork, rebrand, maintainer, pull request, upgrade, PHP compatibility, abandoned, original author, credit
type: help
---
# Erweiterungen an die Upstream-Projekte zurückgeben

Diese Seite ist für Entwickler bei Partnern, die bestehende Erweiterungen nutzen, korrigieren oder erweitern. Das Ziel
ist gemeinsames Wachstum: Verbessern Sie die Erweiterung dort, wo sie zu Hause ist, damit alle profitieren und der
ursprüngliche Autor genannt wird.

## 1. Das Upstream-Projekt finden

- `ezinfo.php` und `extension.xml` der Erweiterung nennen ihre Info-URL und Lizenz ([Erweiterungen](../../../en/us/guides/extensions.md)).
- Der Composer-Paketname (`vendor/name`) führt zu ihrem Repository auf Packagist.
- Viele Erweiterungen im Ökosystem werden von se7enxweb als Fortführung früherer Arbeit gepflegt; andere liegen bei
  ihren ursprünglichen Autoren oder auf <https://projects.exponential.earth>.

## 2. Eine Erweiterung auf aktuelles Exponential bringen

Die meisten Upgrades sind klein: PHP-8-Kompatibilität, umbenannte Einstellungen, Anpassungen an Templates. Arbeiten Sie
in einem Branch, halten Sie jede Änderung in einem eigenen Commit und beschreiben Sie in der Nachricht, warum
(`Fixed: ...`, `Updated: ...`). Testen Sie auf den PHP-Versionen, die der Kernel unterstützt (8.0 bis 8.5 für 6.0.15,
`doc/en/us/install/02-requirements.md`).

## 3. Zurückschicken

| Zustand des Upstream-Projekts | Tun Sie |
|---|---|
| Aktiv | Eröffnen Sie einen Pull Request; folgen Sie den Regeln des Projekts |
| Langsam, aber lebendig | Eröffnen Sie den Pull Request und ein Issue, das ihn erklärt; warten Sie eine angemessene Zeit |
| Verwaist (lange keine Antwort) | Fragen Sie öffentlich, ob Sie übernehmen dürfen; sonst forken Sie |

## 4. Forks und Umbenennungen, mit Respekt

- Behalten Sie die vollständige Git-Historie, das ursprüngliche Copyright und die Lizenz.
- Nennen Sie den ursprünglichen Autor in der README („basiert auf [Name] von [Autor]“).
- Benennen Sie nur um, wenn nötig (zum Beispiel, um unter Ihrem Vendor auf Packagist zu veröffentlichen), und sagen Sie,
  woher es stammt.
- Bieten Sie die Verbesserungen Ihres Forks zurück an, wenn das Original wieder auflebt.

Das Projekt hält es ebenso: Exponential Layouts „bringt das Konzept von Netgen Layouts in den Kernel von Exponential“
(README), und die Verbesserungen von LoveStack wurden „mit voller Nennung von Mugo Web“ neu gebaut (README).

## 5. Die Pflege übernehmen

Wenn Sie Betreuer werden:

- Veröffentlichen Sie Releases nach den Regeln: Version in `ezinfo.php` und `extension.xml`, Tag nach dem Anheben, nie
  ein Tag verschieben ([Erweiterungen, „Release it“](../../../en/us/guides/extensions.md#7-release-it)).
- Beantworten Sie Issues, oder sagen Sie in der README, dass Sie sie nur für Ihre eigenen Bedürfnisse pflegen.
- Informieren Sie die Community in den Foren.

## Verwandte Seiten

- [Partner für Erweiterungen und Themes](partner-fuer-erweiterungen-und-themes.md)
- [Eine Erweiterung als Partner veröffentlichen](eine-erweiterung-veroeffentlichen.md)
- [Code zurückgeben](code-zurueckgeben.md)
- [Erweiterungen zurückgeben](../../../en/us/help/contribute-extensions-back.md): eine Erweiterung veröffentlichen und was die GPL verlangt, wenn Sie sie verbreiten
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
