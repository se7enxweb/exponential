---
title: Auf GitHub forken, Schritt für Schritt
audience: developer, designer, app-creator
category: extension-development
order: 30
summary: Forken Sie das Repository einer Erweiterung auf GitHub, klonen Sie es, fügen Sie das Original als upstream hinzu, arbeiten Sie auf einem Branch und installieren Sie Ihren Fork mit Composer in eine Exponential-Website - mit den Befehlen und dem, was Sie sehen sollten.
keywords: Fork, forken, GitHub, klonen, upstream, Remote, Branch, Composer, repositories, vcs, Fork installieren, Schritt für Schritt, fork, clone, remote, branch, composer, install fork, step by step
type: help
---
# Auf GitHub forken, Schritt für Schritt

Teil 5, Kapitel 3. Dieses Kapitel führt durch das Forken einer Erweiterung auf GitHub und die Verwendung Ihres Forks
in einer Entwicklungs-Website. Es setzt ein GitHub-Konto und Git auf Ihrem Rechner voraus
([Mit Git entwickeln](../../mit-git-entwickeln.md) behandelt Git selbst). Das Beispiel forkt
`se7enxweb/expchangeclass`; ersetzen Sie es durch das Repository, das Sie brauchen, und `your-account` durch Ihren
GitHub-Benutzer oder Ihre Organisation.

## 1. Auf GitHub forken

1. Öffnen Sie die Seite des Repositorys, zum Beispiel https://github.com/se7enxweb/expchangeclass.
2. Klicken Sie auf **Fork** (oben rechts).
3. Wählen Sie Ihr Konto als Eigentümer. Behalten Sie den Namen des Repositorys.
4. Lassen Sie „Copy the main branch only“ angehakt, sofern Sie keine anderen Branches brauchen.
5. Klicken Sie auf **Create fork**.

Sie haben jetzt `https://github.com/your-account/expchangeclass`, mit der gesamten Historie des Originals und dem
Vermerk „forked from se7enxweb/expchangeclass“ unter seinem Namen. Dieser Vermerk ist Teil der Namensnennung: Lassen
Sie den Fork verbunden, „lösen“ Sie ihn nicht ab.

## 2. Ihren Fork klonen

```bash
git clone git@github.com:your-account/expchangeclass.git
cd expchangeclass
git remote add upstream https://github.com/se7enxweb/expchangeclass.git
git remote -v
```

Sie sollten zwei Remotes sehen: `origin` (Ihr Fork, auf den Sie pushen) und `upstream` (das Original, von dem Sie
holen).

## 3. Auf einem Branch arbeiten

Arbeiten Sie nie auf `main` Ihres Forks. Ein Branch je Änderung hält jeden Pull Request sauber:

```bash
git switch -c fix-menu-entry-csp
# edit files
git add design/admin/templates/popupmenu/classcontextmenu.tpl
git commit -m "Updated: The context menu entry carries no inline handler"
git push -u origin fix-menu-entry-csp
```

Benennen Sie den Branch nach der Änderung, nicht nach sich selbst.

## 4. Ihren Fork in einer Entwicklungs-Website verwenden

Eine mit Composer installierte Website kann die Erweiterung statt von Packagist aus Ihrem Fork beziehen. In der
`composer.json` der Website:

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/your-account/expchangeclass" }
    ],
    "require": {
        "se7enxweb/expchangeclass": "dev-fix-menu-entry-csp"
    }
}
```

Dann:

```bash
composer update se7enxweb/expchangeclass
```

`dev-` plus der Name des Branches installiert diesen Branch. Composer liest den Paketnamen aus der `composer.json` des
Forks, die weiterhin `se7enxweb/expchangeclass` lautet, daher behält die Anforderung diesen Namen. Leeren Sie danach
die Caches und generieren Sie die Autoloads neu, wie bei jeder Änderung an einer Erweiterung.

> **Warnung**
> Führen Sie `composer update` nicht auf einer Live-Website aus, um einen Fork zu testen. Verwenden Sie eine
> Entwicklungskopie. Ein Composer-Lauf kann auch Erweiterungen ersetzen, die Sie von Hand mit Git ausgecheckt haben;
> wissen Sie, wie Ihre Website installiert wurde, bevor Sie ihn ausführen.

Ohne Composer klonen Sie den Fork direkt nach `extension/`:

```bash
cd extension
git clone -b fix-menu-entry-csp git@github.com:your-account/expchangeclass.git expchangeclass
```

Der Ordnername muss der Name der Erweiterung sein (`expchangeclass`), nicht der des Repositorys, falls sie sich
unterscheiden.

## 5. Wie es weitergeht

- Um Ihre Änderung an das Original zu schicken: [Mit Pull Requests zurückgeben](mit-pull-requests-zurueckgeben.md).
- Um Ihren Fork aktuell zu halten, während Sie warten: [Einen Fork mit seiner Quelle synchron halten](einen-fork-synchron-halten.md).
- Wenn der Fork zu einem dauerhaften mit eigenen Releases wird, ändern Sie den Paketnamen (`name`) in seiner
  `composer.json` in Ihren eigenen und registrieren Sie ihn auf Packagist
  ([Composer und Packagist](../teil-7-veroeffentlichen/composer-und-packagist.md)).

## Was schiefgehen kann

| Problem | Ursache | Lösung |
|---|---|---|
| `Permission denied (publickey)` beim Klonen | kein SSH-Schlüssel bei GitHub | die https-URL verwenden oder Ihren Schlüssel bei GitHub hinzufügen |
| Composer: „could not find a matching version“ | der Branch-Name in `dev-...` ist falsch, oder die `composer.json` des Forks hat einen anderen Namen | `git branch -r` und das Feld `name` prüfen |
| Ihre Änderung ist auf der Website nicht sichtbar | die Website verwendet noch das Original | `composer show se7enxweb/expchangeclass` zeigt die Quelle; die Caches leeren |

## Verwandte Seiten

- Zurück: [Wann Sie forken sollten und wann nicht](wann-forken.md)
- Weiter: [Einen Fork mit seiner Quelle synchron halten](einen-fork-synchron-halten.md)
- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Fork a repository](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/working-with-forks/fork-a-repo) in der GitHub-Dokumentation (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
