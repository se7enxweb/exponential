---
title: Eine Änderung bereitstellen
audience: software-engineer, admin, developer
category: software-engineering
order: 410
summary: Wie eine Änderung an PHP, Einstellungen, Templates oder Stylesheets jede Laufzeitumgebung erreicht, die eine Exponential-Website bedient - die Schritte von exp:velocity deploy und warum ihre Reihenfolge zählt, Neuladen von PHP-FPM und OPcache, Neustarts von Velocity, Caches zuletzt leeren, Vorwärmen, Upgrades mit Datenbankaktualisierungen und die Regeln für Code, der mit einer Bereitstellung ausgeliefert wird.
keywords: Bereitstellung, bereitstellen, exp:velocity deploy, Autoload, OPcache, PHP-FPM neu laden, Velocity-Neustart, Reihenfolge beim Leeren der Caches, Vorwärmen, Upgrade, Datenbankaktualisierung, class_exists, Softwareingenieur, deploy, deployment, PHP-FPM reload, Velocity restart, cache clear order, warm-up, database update
type: help
---
# Eine Änderung bereitstellen

Diese Seite richtet sich an Ingenieure, die Änderungen an eine laufende Website ausliefern. Exponential hält Code in mehr
als einer Laufzeitumgebung (PHP-FPM mit OPcache, die persistenten Worker von Velocity) und gerenderte Ausgaben in mehreren
Caches; eine Bereitstellung, die die richtigen Schritte in der falschen Reihenfolge ausführt, sieht aus, als hätte sie
nicht gewirkt. Der Standard ist
[Kompatibilität, Releases und Bereitstellung](../../standards/architektur/kompatibilitaet-releases-deployment.md#deployment-standard);
der Befehl ist unter
[Eine PHP-Änderung bereitstellen](../../bc/6.0/velocity-engines.md#eine-php-änderung-bereitstellen-expvelocity-deploy) beschrieben.

## Inhalt

1. [Ein Befehl](#ein-befehl)
2. [Die Schritte und ihre Reihenfolge](#die-schritte-und-ihre-reihenfolge)
3. [Welche Änderung was braucht](#welche-änderung-was-braucht)
4. [Eine Installation aktualisieren](#eine-installation-aktualisieren)
5. [Regeln für Code, der ausgeliefert wird](#regeln-für-code-der-ausgeliefert-wird)
6. [Nach der Bereitstellung](#nach-der-bereitstellung)

## Ein Befehl

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user  # what it would do, and nothing else
php bin/php/console exp:velocity deploy --allow-root-user            # the usual case
php bin/php/console exp:velocity deploy --kernel --allow-root-user   # a kernel class was added or renamed
```

Er gibt für jeden Schritt PASS, FAIL oder SKIP mit der Zeit aus, hält beim ersten Fehler an und endet mit Status 1, wobei
er die nicht ausgeführten Schritte auflistet. `--packer` leert zusätzlich die gepackten Skripte und Styles.

## Die Schritte und ihre Reihenfolge

| # | Schritt | Warum hier |
|---|---|---|
| 1 | Klassenzuordnungen der Erweiterungen (`ezpgenerateautoloads.php -e`) | neue Klassen müssen auffindbar sein |
| 2 | Klassenzuordnungen des Kernels, nur mit `--kernel` | dasselbe für den Kernel |
| 3 | INI-Caches (`--clear-tag=ini`) | Einstellungen werden erneut gelesen |
| 4 | Caches für Templates, Template-Overrides, Übersetzungen und design_base | neue Templates und Design-Verzeichnisse werden gefunden |
| 5 | Das Engine-Archiv, wenn Velocity aus einem läuft | nur neu gebaut, wenn sich `kernel/`, `lib/` oder `autoload/` geändert hat, jede Datei zuerst geparst |
| 6 | PHP-FPM: auf die Revalidierung durch OPcache warten oder es neu laden | der neue Code läuft unter FPM |
| 7 | Velocity neu starten, wenn es läuft | Worker erben die Klassen des Vorwärmens |
| 8 | Caches für Inhalte, HTTP und Template-Blöcke | gerenderte Ausgaben werden erst jetzt geleert |
| 9 | Der Antwort-Cache von Velocity | dasselbe, im Server |
| 10 | Vorwärm-URLs (`[DeploySettings] WarmUpURL[]`) | der erste Besucher baut nicht jeden Cache neu auf |

**Die Reihenfolge ist der Punkt.** Wird vor Schritt 6 und 7 geleert, wird jede in der Zwischenzeit angeforderte Seite vom
alten Code gerendert und erneut zwischengespeichert. Eine Datei, die sich nicht parsen lässt, lässt Schritt 5
fehlschlagen, bevor PHP-FPM oder Velocity berührt werden.

**PHP-FPM wird nur neu geladen, wenn es sein muss** (`[DeploySettings] PhpFpmReload=auto`): Ein Neuladen lässt jeden Pool
dieser PHP-Version auf seine am längsten laufende Anfrage warten. Revalidiert OPcache Dateien, wartet die Bereitstellung
stattdessen `revalidate_freq` plus eine Sekunde. Nennen Sie auf einem Server mit mehreren PHP-Versionen in
`[DeploySettings] PhpFpmService` den Dienst, der die Website tatsächlich bedient.

## Welche Änderung was braucht

| Änderung | Braucht |
|---|---|
| Eine PHP-Klasse, ein Operator oder eine INI-Datei | die ganze Bereitstellung |
| Eine neue oder umbenannte Kernel-Klasse | die Bereitstellung mit `--kernel` |
| Ein vorhandenes Template | die Template-Caches und den Antwort-Cache von Velocity; keinen Neustart |
| Ein Stylesheet oder Skript eines Designs | die Bereitstellung mit `--packer` oder die Caches für Packer und Template-Blöcke |
| Inhalte | nichts; der Kernel invalidiert |
| Eine neu aktivierte Erweiterung | die Bereitstellung: ihre Klassen, Einstellungen und Design-Verzeichnisse (design_base) |

## Eine Installation aktualisieren

1. Sichern Sie Dateien und Datenbank.
2. Bringen Sie den neuen Code an seinen Platz, erzeugen Sie die Klassenzuordnungen neu, leeren Sie alle Caches, laden Sie
   PHP-FPM neu, starten Sie Velocity neu.
3. Wenden Sie die Datenbankaktualisierung einmal, vollständig, der Reihe nach an (`update/database/<driver>/6.0/`), dann
   die Skripte, die die Anleitung nennt.
4. Lesen Sie die Upgrade-Hinweise jedes durchlaufenen Zeitraums in `doc/eng-us/bc/6.0/` und die Changelogs.
5. Prüfen Sie Erweiterungen: `./console exp:checkclasses` meldet Klassen, die PHP nicht laden will.
6. Führen Sie die Upgrade-Prüfung aus: Setup > System Upgrade oder `php bin/php/checkmanifest.php --all --extensions`.

Anleitungen: [Aktualisieren](../../anleitungen/aktualisieren.md), [Mit Git aktualisieren](../mit-git-aktualisieren.md).

## Regeln für Code, der ausgeliefert wird

- Eine neue Klasse, auf die eine mitgelieferte INI-Datei verweist, sollte im Kernel mit `class_exists()` abgesichert
  werden: Ein Velocity-Worker, der vor der Bereitstellung gestartet wurde, kennt sie nicht.
- Templates, die eine neue Klasse oder einen neuen Operator verwenden, dürfen die Server nicht vor der Klasse erreichen;
  stellen Sie zuerst die Kernel-Seite bereit.
- Eine Bereitstellung muss jede PHP-Laufzeitumgebung erreichen, die die Website bedient; nur Caches zu leeren lässt
  PHP-FPM und Velocity bei den alten Klassen.
- Stellen Sie bei mehreren Webknoten jeden Knoten bereit; `exp:velocity deploy` deckt einen Rechner ab.
- Erwägen Sie in der Produktion `ManagedByDeployment=enabled`, damit die Administration keine Einstellungen oder
  Templates schreiben kann, die die nächste Bereitstellung verwerfen würde
  ([Konfiguration, die die Bereitstellung verwaltet](../../funktionen/6.0/durch-deployment-verwaltet.md)).

## Nach der Bereitstellung

- Rufen Sie die Startseite und die Anmeldung der Administration auf; prüfen Sie eine Seite, deren Inhalt Sie kennen.
- Beobachten Sie `error.log` im Log-Viewer einige Minuten lang.
- Prüfen Sie nach einem Neustart von Velocity, dass ein neuer Elternprozess läuft und der Port antwortet
  ([Persistente Worker von Velocity](persistente-worker-von-velocity.md)).

## Verwandte Seiten

- [Bereitstellen](../../anleitungen/bereitstellung.md)
- [Bereitstellungs-Pipeline](../ersteller/anwendungsentwickler/deployment-pipeline.md)
- [Änderungen bereitstellen, für Hoster](../hosting/betrieb/aenderungen-ausrollen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
