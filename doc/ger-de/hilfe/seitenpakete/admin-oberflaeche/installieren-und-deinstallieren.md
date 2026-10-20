---
title: Installieren und Deinstallieren
audience: admin, developer
category: install-upgrade
order: 103
summary: Die Elemente eines Pakets aus der Administration installieren - die Installationsseite, was die Installation hier anhand der Remote-ID vorfindet, als Entwürfe installieren, im Hintergrund installieren, Konflikte, Stapelverarbeitung - und es wieder deinstallieren, mit dem, was jeder Schritt ändert, und warum Seitenpakete nicht von hier installiert werden.
keywords: installieren, deinstallieren, package/install, package/uninstall, Paket installieren, Installation auslassen, Als Entwürfe installieren, Im Hintergrund installieren, Was die Installation hier vorfindet, Remote-ID, Konflikt, InstallBatchMaxItems, Inhaltsjob, Seitenpaket, install, uninstall, Install package, Install as drafts, remote ID, conflict, content job, site package
type: help
---
# Installieren und Deinstallieren

Diese Seite ist für Administratoren, die ein Paket installieren, das bereits im lokalen Repository liegt (importiert, abgerufen oder
erstellt), oder eines wieder entfernen. Installieren führt die Installationselemente des Pakets auf dieser Website aus; Deinstallieren führt
seine Deinstallationselemente aus. Beides ändert die Website.

![Die Paketliste, aus der ein Paket installiert wird](../../../../eng-us/help/images/help/sp-install.webp)

## Inhalt

1. [Seitenpakete werden nicht hier installiert](#seitenpakete-werden-nicht-hier-installiert)
2. [Die Installationsseite öffnen](#die-installationsseite-öffnen)
3. [Was die Installation hier vorfindet](#was-die-installation-hier-vorfindet)
4. [Drei Wege zu installieren](#drei-wege-zu-installieren)
5. [Konflikte und Schritte](#konflikte-und-schritte)
6. [Große Pakete](#große-pakete)
7. [Deinstallieren](#deinstallieren)

## Seitenpakete werden nicht hier installiert

Ein Seitenpaket ist `install_type="import"`: Seine Arbeit (Einstellungen, Siteaccesses, Rollen, die Nachinstallation) erledigt sein
Installerskript im Schritt `CreateSites` des Setup-Assistenten, des Kickstarters oder von `exp:install`. Seine Karte sagt **Nur importiert**,
und seine Seite hat keine Schaltfläche **Installieren**. Die Pakete, die es verlangt (Klassen, Demo-Inhalte), lassen sich hier wie jedes
andere Paket installieren, zum Beispiel um einer vorhandenen Website Demo-Inhalte hinzuzufügen.

## Die Installationsseite öffnen

**Installieren** auf der Seite eines Pakets öffnet `/package/install/<name>`. Sie brauchen `package/install` für den Typ des Pakets
(`eZPackage::canInstall()`); ohne die Richtlinie antwortet die Seite mit verweigertem Zugriff, und die Seite des Pakets sagt „Sie dürfen
keine Pakete installieren (package/install), daher wird die Schaltfläche „Installieren“ nicht angezeigt.“

Die Seite führt die **Elemente installieren** auf, eine Zeile pro Element, wie sein Handler es erklärt, und, wenn das Paket aus einem
Hochladen oder **Prüfen** kam, das Ergebnis der Validierung. Ein bereits installiertes Paket sagt das: Eine erneute Installation wiederholt
jedes Element unten, und Inhalte, die es anlegt, werden erneut angelegt.

## Was die Installation hier vorfindet

Bevor Sie irgendetwas drücken, sucht **Was die Installation hier vorfindet** jedes Inhaltsobjekt des Pakets anhand seiner Remote-ID:
„%total Inhaltsobjekte, nach Remote-ID: %create neu, %update vorhanden (aktualisiert oder ein neuer Entwurf), %conflict Konflikte, %error ohne
ihre Klasse.“ Die Zeile jedes Objekts sagt, wohin sein Hauptort geht: **Oben**, **Ein Knoten hier**, **Ein Knoten des Pakets** oder ein
fehlendes Elternelement.

Lesen Sie sie bei jedem Paket mit Inhalten. Ein Konflikt bedeutet, dass die Remote-ID einem Objekt einer anderen Klasse gehört, das in Ruhe
gelassen wird. „die Klasse ist weder installiert noch im Paket“ bedeutet, dass das Objekt nicht angelegt werden kann, bis seine Klasse
existiert.

## Drei Wege zu installieren

| Schaltfläche | Tut | Nutzen Sie sie, wenn |
|---|---|---|
| **Paket installieren** | installiert jedes Element der Reihe nach, in dieser Anfrage | kleine Pakete, Klassen, Einstellungen |
| **Als Entwürfe installieren** („Die Inhaltsobjekte als Entwürfe installieren“) | schreibt nur die Inhaltsobjekte, als Entwürfe; nichts wird veröffentlicht; ein vorhandenes Objekt bekommt einen neuen Entwurf oder bleibt unverändert, wie Sie wählen; Klassen, Einstellungen und Dateien ändern die Website sofort und werden deshalb ausgelassen | Sie Inhalte prüfen wollen, bevor sie live gehen |
| **Im Hintergrund installieren** | dieselbe Installation als Inhaltsjob (`packageinstall`) unter Ihrem Benutzer; die Paketseite und die Jobseite zeigen den Fortschritt, nichts wartet auf diese Seite | viele Objekte; die Seite schlägt es oberhalb von `[ContentJobSettings] SynchronousLimit` vor |

**Installation auslassen** führt ohne Installation zur Seite des Pakets zurück. Die Installation als Entwürfe ist auf
[Das JSON-Paketformat](../../../../eng-us/bc/6.0/package-json-format.md#install-as-drafts) beschrieben.

## Konflikte und Schritte

„Jedes Element wird der Reihe nach installiert; ein Schritt kann fragen, wie ein Konflikt behandelt werden soll.“ Der Handler für
Inhaltsklassen findet zum Beispiel eine vorhandene Klasse anhand ihrer Remote-ID und fragt, was zu tun ist (`ERROR_EXISTS` in
`kernel/classes/packagehandlers/ezcontentclass/ezcontentclasspackagehandler.php`). Ihre Antwort kann für den Rest des Laufs für Elemente
desselben Typs und Fehlers gespeichert werden.

## Große Pakete

Die Installation läuft in Stapeln, damit ein großes Paket nicht an das Zeitlimit des Webservers stößt (`package.ini [InstallerSettings]`):

```ini
[InstallerSettings]
InstallBatchMaxItems=12
InstallBatchTimeBudgetSeconds=6
```

Ein Stapel endet nach so vielen Elementen oder so vielen Sekunden, und die Seite fährt mit dem nächsten fort. Siehe
[Ein Paket-Installer, der große Pakete übersteht](../../../../eng-us/features/6.0/package-installer-batching.md). Für sehr große Pakete ist die
Kommandozeile oft besser: `php ezpm.php install <name>` ([ezpm](../../../../eng-us/features/6.0/ezpm-package-manager-cli.md#install-a-package)).

## Deinstallieren

**Deinstallieren** auf der Seite eines Pakets öffnet `/package/uninstall/<name>` (ebenfalls Richtlinie `package/install`). Sie führt die
**Elemente deinstallieren** auf und warnt: „Die Deinstallation entfernt, was die Elemente unten auf dieser Site angelegt haben, Inhalte
eingeschlossen. Sie kann nicht rückgängig gemacht werden.“ **Deinstalliere Paket** führt sie in umgekehrter Reihenfolge aus und markiert das
Paket als nicht installiert; **Deinstallation auslassen** führt zurück. Ein Paket ohne etwas zu deinstallieren sagt „Dieses Paket hat nichts
zu deinstallieren.“

> **Warnung**
> Sichern Sie die Datenbank und `var/`, bevor Sie ein Paket mit Inhalten installieren oder deinstallieren. Keine der beiden Seiten hat ein
> Rückgängigmachen, und das Deinstallieren entfernt Inhalte, die Redakteure seitdem vielleicht geändert haben.

## Verwandte Seiten

- [Pakete in der Administration](README.md)
- [Pakete, Abschnitt 7](../../../anleitungen/pakete.md#7-installieren-und-deinstallieren)
- [Importieren oder installieren](../grundlagen/importieren-oder-installieren.md)
- [Installationselemente und Handler](../grundlagen/installationselemente-und-handler.md)
- [In ein Paket hineinsehen, es mit Ihrer Website vergleichen](../../../../eng-us/features/6.0/package-compare-and-import.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
