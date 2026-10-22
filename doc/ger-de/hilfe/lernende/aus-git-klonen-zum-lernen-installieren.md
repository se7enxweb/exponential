---
title: Aus Git-Klonen zum Lernen installieren
audience: student
category: learning
order: 13
summary: Wie Lernende im Entwicklerpfad Exponential als Git-Klone mit ihrer vollständigen Historie installieren, nach Mit Git entwickeln - die eine Composer-Einstellung, den Kernel klonen, ein eigener Branch, die Website installieren, prüfen, ob jede Erweiterung ein Klon ist, und warum das der beste Weg ist, aus dem Code zu lernen.
keywords: git, klon, klonen, composer, preferred-install, source, historie, branch my-site, mit git entwickeln, lernen, entwickler, installieren, erweiterungen, clone, history, develop with git, study, developer, install, extensions
type: help
---
# Aus Git-Klonen zum Lernen installieren

Diese Seite ist für Lernende im [Entwicklerpfad](lernpfad-entwickler.md) und für alle, die den Code lesen möchten,
den sie ausführen. Sie folgt [Mit Git entwickeln](../mit-git-entwickeln.md), was das Projekt „für den Bau einer Website“
empfiehlt, und erklärt, warum ein Klon auch der beste Weg zum Lernen ist.

## Inhalt

1. [Warum Klone beim Lernen helfen](#warum-klone-beim-lernen-helfen)
2. [Was Sie brauchen](#was-sie-brauchen)
3. [Die Schritte](#die-schritte)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Was Sie zuerst erkunden](#was-sie-zuerst-erkunden)

## Warum Klone beim Lernen helfen

Aus dem Vergleich in [Mit Git entwickeln](../mit-git-entwickeln.md#warum-git-klone-und-keine-archive):

| Mit einem Klon können Sie | Befehl |
|---|---|
| Jede Änderung sehen, die Sie gemacht haben, Zeile für Zeile | `git status`, `git diff` |
| Herausfinden, warum eine Zeile da ist, wer sie geändert hat und wann | `git log`, `git blame` |
| Finden, welche Änderung etwas kaputt gemacht hat | `git bisect` |
| Ihre eigenen Änderungen auf Ihrem eigenen Branch halten | `git switch -c my-site` |
| Eine Korrektur an das Projekt zurückschicken | in Ihren Fork pushen, einen Pull Request eröffnen |

Ein Release-Archiv gibt Ihnen die Dateien und sonst nichts. Ein Klon gibt Ihnen die Historie, ein Protokoll darüber,
wie erfahrene Entwickler echte Probleme gelöst haben. Sie zu lesen ist Lernen.

## Was Sie brauchen

- Git 2.28 oder neuer (`git --version`);
- Composer 2 (`composer --version`) und PHP wie in den Anforderungen angegeben;
- ein GitHub-Konto nur, wenn Sie Änderungen zurückschicken möchten. Für die Installation brauchen Sie keines.

Quelle: [Mit Git entwickeln](../mit-git-entwickeln.md#was-sie-brauchen).

## Die Schritte

```bash
composer config --global 'preferred-install.se7enxweb/*' source
git clone https://github.com/se7enxweb/exponential.git exponential
cd exponential
git switch -c my-site
composer install
php bin/php/console exp:install --random-password --url=http://localhost:8087
php bin/php/console exp:velocity start --engine=php
```

Was jede Zeile bewirkt, wie [Mit Git entwickeln](../mit-git-entwickeln.md#schritt-2-mit-git-installieren) es erklärt:

1. Die Composer-Einstellung installiert jedes Paket `se7enxweb/*` als Git-Klon und die Bibliotheken als Archive.
   Behalten Sie die Anführungszeichen bei, damit Ihre Shell das `*` nicht expandiert.
2. `git clone` holt den Kernel mit seiner gesamten Historie; die Seite hat mehr als 27.000 Commits und etwa 370 MB
   gemessen, zwei bis drei Minuten bei einer normalen Verbindung.
3. `git switch -c my-site` beginnt einen eigenen Branch für Ihre Änderungen, auf `main`, der aktuellen
   Entwicklungslinie.
4. `composer install` klont jede Erweiterung, die der Kernel benötigt, nach `extension/<name>`, jeweils mit ihrer
   Historie.
5. Die letzten beiden Zeilen installieren die Lernwebsite und liefern sie aus, wie in
   [Eine Lernwebsite mit einem Befehl installieren](eine-lernwebsite-mit-einem-befehl-installieren.md).

Um statt der Entwicklungslinie ein veröffentlichtes Release zu studieren, nennen Sie seinen Tag:
`git switch -c my-site v6.0.14`. Lesen Sie zuerst
[Versionen, Tags und Branches](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches), denn ältere Tags
brauchen für ihre Testwerkzeuge ein neueres PHP.

## Prüfen, ob es funktioniert hat

```bash
composer status -v
ls -d extension/*/.git | wc -l
git -C extension/eztags log --oneline -3
git -C extension/eztags status
```

`composer status -v` sollte `No local changes` melden; die zweite Zeile zählt die Erweiterungen, die Klone sind. In
einer Erweiterung ist **„HEAD detached“ normal**: Composer checkt genau den Release-Tag aus, den `composer.json`
verlangt. Beginnen Sie einen Branch, bevor Sie dort etwas ändern
([Mit Git entwickeln](../mit-git-entwickeln.md#prüfen-ob-es-funktioniert-hat)).

`git status` im Wurzelverzeichnis listet die Erweiterungen als nicht versionierte Ordner auf; sie sind eigene
Repositorys, und das ist so erwartet.

## Was Sie zuerst erkunden

| Übung | Befehl oder Seite |
|---|---|
| Die letzten zehn Änderungen an einer Datei | `git log --oneline -10 -- kernel/content/view.php` |
| Wer jede Zeile davon geändert hat | `git blame kernel/content/view.php` |
| Jeder Commit, der ein Wort hinzugefügt oder entfernt hat | `git log -S "CheckValidity" --oneline` |
| Wie Commit-Nachrichten hier geschrieben werden | `git log --oneline -20` (Betreffzeilen beginnen mit `Added:`, `Updated:`, `Fixed:` und so weiter) |
| Was eine Erweiterung enthält | [Was eine Erweiterung ist](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/was-eine-erweiterung-ist.md) |

Notieren Sie in Ihrem [Lerntagebuch](ein-lerntagebuch-fuehren.md) einen Commit, den Sie verstanden haben, und warum er
gemacht wurde.

> **Warnung**
> Klonen Sie niemals von Hand mit `git clone` in einen Ordner, den Composer als Archiv installiert hat. Beim nächsten
> Update ersetzt Composer ihn ohne Rückfrage, und Ihre Commits sind verloren. Lassen Sie Composer den Klon mit
> `composer reinstall <package>` anlegen ([Wenn Composer sich weigert](../mit-git-entwickeln.md#wenn-composer-sich-weigert)).

## Verwandte Seiten

- [Git als Lernende nutzen](git-als-lernende-nutzen.md)
- [Git-Kurzreferenz für Lernende](git-kurzreferenz-fuer-lernende.md)
- [Mit Git aktualisieren](../mit-git-aktualisieren.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
