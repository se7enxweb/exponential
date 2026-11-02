---
title: "Grenzen und Bedingungen von GitHub Pages"
audience: admin, website-creator, non-profit, local-business, individual, student, hosting
category: administration
order: 18
summary: Was GitHub Pages erlaubt - 1 GB pro Website, 100 MB pro Datei, etwa 100 GB Datenverkehr im Monat, ein öffentliches Repository bei einem kostenlosen Tarif, keine Shops - wie das statische Veröffentlichen einen Build vor jedem Push daran prüft und wie Sie einen Build verkleinern.
keywords: Statisches Veröffentlichen, GitHub Pages, Grenzen, Größe, 1 GB, 100 MB, Bandbreite, Datenverkehr, öffentliches Repository, privates Repository, kostenloser Tarif, Bedingungen, Nutzungsrichtlinien, Shop, kommerziell, zu groß, Push abgelehnt, static publishing, GitHub Pages, limits, size, 1 GB, 100 MB, bandwidth, traffic, public repository, private repository, free plan, terms, acceptable use, shop, commercial, MaxSiteBytes, MaxFileBytes, too large, push refused
type: help
---
# Grenzen und Bedingungen von GitHub Pages

GitHub Pages ist kostenlos und hat Grenzen. Das statische Veröffentlichen kennt sie: Der Assistent nennt sie, die
Vorschau zeigt die Größe des Builds im Vergleich dazu, und **jeder Push** eines GitHub-Pages-Ziels wird zuerst geprüft.

## Was Sie am Ende haben

Einen Build, der zu GitHub Pages passt, und eine Website, die sich an dessen Bedingungen hält.

![Die Vorschau: die Größe des Builds gegenüber den Grenzen von GitHub Pages](../bilder/statisches-veroeffentlichen/limits-light.webp)

## Die Grenzen

| | Grenze | Was das statische Veröffentlichen tut |
|---|---|---|
| Die Website | 1 GB | über 90 %: eine Warnung; darüber: nicht gepusht |
| Eine Datei | 100 MB | über 50 MB: eine Warnung (GitHub warnt auch); über 100 MB: nicht gepusht |
| Datenverkehr | etwa 100 GB im Monat (eine weiche Grenze) | nichts zu prüfen; GitHub schreibt Ihnen, wenn eine Website weit mehr verbraucht |
| Builds | etwa 10 pro Stunde (eine weiche Grenze, Branch-Builds) | eine Veröffentlichung ist ein Build |
| Repository | öffentlich bei GitHub Free; privat bei GitHub Pro, Team oder Enterprise | ein privates Repository ist eine Warnung |

Geprüft werden die Grenzen `MaxSiteBytes` und `MaxFileBytes` in `[StaticPublishSettings]` (1 GB und 100 MB). Ein Push
darüber bleibt **pending**, mit dem Grund im Bericht des Auftrags und im Verlauf; nichts erreicht GitHub. Es sind die
von GitHub veröffentlichten Zahlen; GitHub kann sie ändern, und maßgeblich ist GitHubs eigene Dokumentation.

## Einen Build verkleinern

- **Dateien** in Schritt 7 des Assistenten: **Nur was die Seiten nennen** (`Assets=referenced`) statt jedes Bildes
  veröffentlichter Inhalte.
- Seiten oder Sektionen **auslassen**, die nicht öffentlich sein müssen (`ExcludePaths[]`, `ExcludeSections[]`).
- Große Videos und Downloads: von einem anderen Host verlinken (einer Videoplattform, einem Release-Download) statt
  aus dem Speicher der Website.
- Die Vorschau listet vor dem Veröffentlichen die größten Dateien und die Größe im Vergleich zur Grenze auf.

## Die Bedingungen

GitHub Pages ist für Projekt-, persönliche und Organisations-Websites gedacht. Seine Bedingungen sagen, dass es nicht
dafür gedacht ist, ein Online-Geschäft, eine E-Commerce-Website oder etwas vorwiegend für kommerzielle Transaktionen zu
betreiben, und nicht als kostenloses Datei-Hosting. Ein Shop, ein Mitgliederbereich oder eine Website, die verkauft,
sollte auf Ihrem eigenen Hosting bleiben; das statische Veröffentlichen lässt außerdem aus, was statisch nicht
funktionieren kann (Suche, Formulare, Anmeldungen). Lesen Sie vor dem Veröffentlichen die aktuellen Bedingungen von
GitHub für GitHub Pages und seine Nutzungsrichtlinien: Sie gelten für Ihre Website, nicht für Exponential.

Eine Website, deren Inhalte nicht öffentlich sein dürfen, gehört in ein privates Repository mit einem kostenpflichtigen
Tarif oder auf einen anderen Host: Ein kostenloser Tarif liefert nur öffentliche Repositorys aus, und deren Dateien
sind auch auf GitHub öffentlich. Siehe
[Private Inhalte privat halten](private-inhalte-privat-halten.md).

## Nächster Schritt

[Build, Vorschau und Veröffentlichen](build-vorschau-und-veroeffentlichen.md).
