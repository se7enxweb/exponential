---
title: Die Open-Source-Lizenz (GPL)
audience: government, project-manager, admin
category: public-sector
order: 70
summary: Unter welcher Lizenz Exponential steht (GNU GPL Version 2 oder jede spätere Version), was die Lizenz einer öffentlichen Stelle erlaubt, was sie verlangt, wenn Kopien weitergegeben werden, und wo Sie das im Produkt und im Repository prüfen.
keywords: GPL, GNU General Public License, GPL-2.0-or-later, freie Software, Open Source, Lizenz, Open Source Definition, Urheberrecht, LICENSE, COPYRIGHT, free software, license, copyright
type: help
---
# Die Open-Source-Lizenz (GPL)

Diese Seite richtet sich an Führungskräfte, Mitarbeitende aus Recht und Beschaffung sowie Administratoren, die angeben
müssen, unter welcher Lizenz die Plattform läuft. Sie fasst zusammen; maßgeblich ist der Lizenztext. Die
Zusammenfassung in einfachen Worten für Käufer ist [Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md),
und der Lizenztext ist die [GNU GPL Version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Lizenzen](#die-lizenzen)
2. [Was die Lizenz erlaubt](#was-die-lizenz-erlaubt)
3. [Was sie im Gegenzug verlangt](#was-sie-im-gegenzug-verlangt)
4. [Freie Software und Open Source](#freie-software-und-open-source)
5. [Wo Sie es prüfen](#wo-sie-es-prüfen)

## Die Lizenzen

| Was | Lizenz |
|---|---|
| Exponential 6, der Kernel | GNU GPL v2 oder jede spätere Version (`GPL-2.0-or-later` in `composer.json`) |
| Beiträge zum Kernel | dieselbe |
| Die Dokumentation (6.x) | GNU FDL 1.3 oder jede spätere Version (siehe „Die Dokumentationslizenz (GFDL)“) |
| Exponential Velocity | MIT |
| Jede Erweiterung | in ihrer eigenen `ezinfo.php` und `extension.xml` angegeben, auf der Info-Seite angezeigt |

„Using Exponential under the terms of the GNU GPL is free of charge. The Professional License is no longer
available.“ (aus der README, zitiert in [Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md); auf
Deutsch: Die Nutzung von Exponential unter den Bedingungen der GNU GPL ist kostenlos. Die Professional License wird
nicht mehr angeboten.)

## Was die Lizenz erlaubt

Die Software für jeden Zweck auf beliebig vielen Servern und Websites betreiben; den Quellcode lesen und ändern; sie
selbst oder bei einem beliebigen Anbieter hosten; beliebige Personen mit Arbeiten daran beauftragen; Kopien, geändert
oder nicht, unter den Bedingungen der Lizenz weitergeben.

## Was sie im Gegenzug verlangt

Die Bedingungen gelten, wenn Sie die Software **verbreiten**, also Kopien an andere weitergeben: die Hinweise
erhalten, Änderungen kennzeichnen, das Ganze unter der GPL lizenzieren, den Quellcode bereitstellen, keine weiteren
Einschränkungen hinzufügen. Sie auf dem eigenen Server zu betreiben, um Ihre Website auszuliefern, ist Betreiben, nicht
Verbreiten. Die GPL v2 enthält keine Klausel über Software, die über ein Netzwerk genutzt wird. Einzelheiten und die
zu klärenden Fragen: „Was die GPL für eine öffentliche Stelle bedeutet“.

## Freie Software und Open Source

Die GPL ist eine Lizenz für freie Software, verfasst von der Free Software Foundation, und sie erfüllt die
[Open Source Definition](https://opensource.org/osd) der Open Source Initiative. Beschaffungsregeln verlangen
manchmal den einen oder den anderen Begriff; beide treffen zu.

## Wo Sie es prüfen

- `LICENSE`, `LICENSE.md` und `COPYRIGHT.md` im Wurzelverzeichnis des Repositorys; `"license"` in `composer.json`.
- In der Administration: Die Info-Seite listet jede Erweiterung mit Version und Lizenz auf
  ([Info- und Paketseiten](../../../funktionen/6.0/info-und-paketseiten.md)).
- Die Lizenzliste des Paketsystems ([Paketlizenzen und Versionen](../../../funktionen/6.0/paketlizenzen-und-versionen.md)).

## Verwandte Seiten

- [Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md)
- [Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
