# Statisches Veröffentlichen: Grenzen und GitHub-Bedingungen

Diese Anleitung ist für Administratoren, die eine Site auf GitHub Pages planen und vor dem ersten Push wissen wollen,
ob sie passt: die Größengrenzen, die Exponential prüft, die weichen Grenzen, die es nicht prüfen kann, und die
Bedingungen, die entscheiden, welche Sites dorthin gehören.

[Anleitungen](README.md) · Verwandt: [Grenzen und Bedingungen von GitHub Pages](../hilfe/statisches-veroeffentlichen/grenzen-und-bedingungen-von-github-pages.md),
[Passt statisch zu meiner Website?](../hilfe/statisches-veroeffentlichen/passt-statisch-zu-meiner-website.md)

## Was Sie am Ende haben

Einen Build, der zu GitHub Pages passt, die Einstellungen, die ihn so halten, und eine klare Antwort, ob Ihre Site
dort überhaupt liegen darf.

## Die Grenzen

| | Zahl von GitHub | Von Exponential geprüft |
|---|---|---|
| Größe der Site | 1 GB | `MaxSiteBytes` (Standard `1000000000`): Warnung über 90 %, darüber nicht gepusht |
| Eine Datei | 100 MB | `MaxFileBytes` (Standard `100000000`): Warnung über 50 MB, darüber nicht gepusht |
| Datenverkehr | etwa 100 GB im Monat, weich | nein |
| Branch-Builds | etwa 10 pro Stunde, weich | nein; eine Veröffentlichung ist ein Pages-Build |
| Privates Repository | Pages nur in kostenpflichtigen Tarifen | eine Warnung |

Jeder Push eines GitHub-Pages-Ziels wird zuerst geprüft. Ein Push über den Grenzen bleibt **pending**, mit dem Grund im
Bericht und im Verlauf; nichts erreicht GitHub. Die Vorschau eines Builds zeigt seine Größe gegen die Grenzen und die
größten Dateien. Die Zahlen sind die veröffentlichten von GitHub, und GitHub kann sie ändern; setzen Sie
`MaxSiteBytes` und `MaxFileBytes` in `[StaticPublishSettings]`, wenn das geschieht oder Ihr Host andere Grenzen hat.

## Den Build klein halten

- `Assets=referenced` kopiert nur die Dateien, die die Seiten nennen, statt jedes Speicherbilds veröffentlichter Inhalte
  (`referenced-and-storage`, der Standard). Auf der Demo-Site waren die meisten der 485 MB Speicherbilder.
- `ExcludePaths[]` und `ExcludeSections[]` lassen Seiten aus, die nicht öffentlich sein müssen.
- Legen Sie große Videos und Downloads auf eine Videoplattform oder einen Release-Download und verlinken Sie sie.
- `PostProcess[]=minify` und `precompress` verkleinern die Textdateien; `exp:static:diff` zeigt, was eine Änderung
  hinzufügt.

## Seltener veröffentlichen

Trigger beim Veröffentlichen von Inhalten werden verzögert (`PublishDebounce`, standardmäßig 120 Sekunden): Viele
Änderungen in kurzer Zeit ergeben einen Build. Ein `Schedule` veröffentlicht stattdessen zu festen Zeiten. Beides hält
Sie weit unter der weichen Build-Grenze.

## Die Bedingungen

GitHub Pages ist für Projekt-, persönliche und Organisations-Sites. Seine Bedingungen sagen, dass es nicht für ein
Online-Geschäft, einen Onlineshop oder eine Site vor allem für kommerzielle Transaktionen gedacht ist, und nicht als
kostenloses Datei-Hosting. Das entscheidet mehr als die Größe:

- Gut passend: eine Broschüre, ein Blog, eine Dokumentation, ein Portfolio, eine Veranstaltung, ein gemeinnütziger
  Verein, eine Schule oder eine Benutzergruppe.
- Schlecht passend: ein Shop, ein Mitgliederbereich, eine Site mit vielen Formularen. Lassen Sie diese auf Ihrem
  eigenen Hosting; das statische Veröffentlichen lässt Warenkorb und Anmeldung ohnehin aus.
- In einem kostenlosen Tarif ist das Repository öffentlich, und damit seine Dateien und sein Verlauf. Lesen Sie vor dem
  ersten Push [Private Inhalte privat halten](../hilfe/statisches-veroeffentlichen/private-inhalte-privat-halten.md).

Lesen Sie die aktuellen Bedingungen von GitHub für Pages und seine Richtlinien zur akzeptablen Nutzung: Sie gelten für
Ihre Site, nicht für Exponential. Andere Git-Hosts haben ihre eigenen; siehe
[Andere Git-Hosts](../hilfe/statisches-veroeffentlichen/andere-git-hosts-gitlab-gitea-und-eigener-server.md).

## Nächster Schritt

[Statisches Veröffentlichen: Fehlerbehebung](statisches-veroeffentlichen-fehlerbehebung.md).
