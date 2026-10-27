# Kikuyu (kik-KE): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome.

## 1. Language and register

Kikuyu (Gĩkũyũ) as written in central Kenya, in the standard orthography of the Kikuyu Bible and of Kikuyu newspapers
and radio (Inooro, Kameme), in a neutral, polite register. No other locale falls back to `kik-KE`.

- Word order is subject - verb - object; the noun class prefixes agree with their noun (kĩndũ kĩerũ, indo njerũ,
  mũhũthiri mwerũ). Function words: na (and, with), kana (or), no (but), angĩkorwo (if), atĩ (that), ũyũ / ĩno / gĩkĩ
  (this), ciothe / othe (all), thĩinĩ wa (in), igũrũ rĩa (on), kuuma (from), nĩ ũndũ wa (because of), ti (is not),
  ndũ- / nd- (negation: ndũngĩhota, ndũrĩ).
- Technical words: a Kikuyu word where one is in use (mũhũthiri, rũtha, kiugo kĩa hitho), a loan in Kikuyu spelling
  where Kikuyu speakers use one, mostly through Swahili (faili, sava, imeili, akaunti, data, kombiuta). Swahili
  (`swa-KE`) is the reference for terminology. Never leave the English word unless it is a product name or code.
  Where Kikuyu has no settled term, a short descriptive phrase is better than an English word.
- Short, plain sentences.

## 2. Form of address

- The user is **wee** (you, singular), polite by word choice ("Ndagũthaitha" for please).
- **Buttons, menu items and links**: the short imperative singular, as Kikuyu and Swahili interfaces do: Iga (save),
  Tiga (cancel), Thiria (delete), Ehereria (remove), Cenjia (edit), Ongerera (add), Umba (create), Caria (search),
  Thuura (select), Tũma (send), Rora (view), Cabithia (publish), Ambatĩria (upload), Ikũrũkia (download).
- **Titles, column headings and tabs**: nouns or noun phrases: Mĩtaratara, Ũtaũri, Handũ harĩa nene, Rĩĩtwa.
- **Instructions**: the imperative, "Ndagũthaitha" where the source has please: "Thuura kĩndũ na ũhũre \"Nĩ wega\".".
- **Confirmations and questions**: "Nĩ ũrenda ... ?" or "Nĩ wĩtĩkĩrĩte ... ?": "Nĩ ũrenda gũthiria gĩcunjĩ gĩkĩ?".
- **What the user can or may do**: "Nĩ ũrahota ...", "Ndũngĩhota ..."; "You do not have permission" is "Ndũrĩ na
  rũtha rwa ...".
- **Messages about a result**: the perfect: "Ndrafti nĩ yaigwo.", "Faili nĩ yambatĩrio wega.".

## 3. Script and typography

- **Latin** alphabet with the Kikuyu letters **ĩ Ĩ** (U+0129/U+0128) and **ũ Ũ** (U+0169/U+0168), as composed
  characters; no tone marks. Never write i or u for them, and never a combining tilde.
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. Nothing is glued to a placeholder (no class prefix or suffix on a
  placeholder). The same for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension.

| English | Kikuyu | Note |
|---|---|---|
| content | ũhoro | content object: kĩndũ kĩa ũhoro |
| object | kĩndũ | plural: indo |
| node | nodi | main node: nodi nene; parent node: nodi mũciari |
| location | handũ | main location: handũ harĩa nene |
| item (list entry) | kĩndũ | |
| tree | mũtĩ | tree menu: menu ya mũtĩ; content structure tree: mũtĩ wa ũhoro |
| class | kĩrathi | the content class: kĩrathi kĩa ũhoro; class group: gĩkundi kĩa irathi |
| attribute | mũhianĩre | plural: mĩhianĩre |
| datatype | mũthemba wa data | |
| version | ũrutwo | version history: rũgano rwa irutwo |
| draft | ndrafti | store draft: Iga ndrafti |
| publish | cabithia | published: nĩ gĩcabithĩtio; publishing: gũcabithia |
| send for publishing | tũma gũcabithio | |
| discard | ikia | discard draft: Ikia ndrafti |
| archived / pending / rejected | kĩigĩtwo / kĩrĩetereire / kĩregetwo | |
| siteaccess | njĩra ya saiti | siteaccess as a setting name stays in code |
| site, website | saiti, wĩbusaiti | |
| section | gĩcunjĩ | the Exponential section; an INI section: gĩcunjĩ kĩa INI |
| role | rolo | the role of the access system |
| policy | watho | plural: mawatho |
| limitation | mũhaka | |
| permission | rũtha | access denied: ndũrĩ na rũtha |
| module | moduli | |
| function | wĩra | a PHP function: PHP function |
| user / user group | mũhũthiri / gĩkundi kĩa ahũthiri | plural: ahũthiri |
| group | gĩkundi | plural: ikundi |
| anonymous | ũtooĩkaine | anonymous user: mũhũthiri ũtooĩkaine |
| administrator | mũrũgamĩrĩri | administration (interface): ũrũgamĩrĩri |
| username | rĩĩtwa rĩa mũhũthiri | |
| password | kiugo kĩa hitho | |
| log in / log out | ingĩra / uma | the act: gũtoonya / kuuma |
| e-mail | imeili | e-mail address: anwani ya imeili |
| cache | kĩigĩro kĩa ihenya | clear cache: Theria kĩigĩro kĩa ihenya |
| template | mũhiano | override template: mũhiano wa kũhĩtũkĩra |
| override | kũhĩtũkĩra | |
| fallback | kĩrĩa kĩa gũcookerera | fallback language: rũthiomi rwa gũcookerera |
| design | mũthondeko | |
| workflow | mũtaratara wa wĩra | |
| trigger | kĩambĩrĩria | |
| event | ũndũ | |
| trash | kĩgira kĩa mahuti | empty trash: Theria kĩgira kĩa mahuti; move to trash: Ikia kĩgira-inĩ kĩa mahuti |
| bookmark | kĩmenyithia | |
| settings | mĩtaratara | configuration: mĩtaratara |
| setup | mwambĩrĩria | the Setup tab |
| layout | mũbangĩre | Exponential Layouts stays |
| block / zone | mbloko / rũhande | |
| extension | kĩongerereo | |
| package | kĩohe | |
| search | ũcaria | to search: caria; search index: indekithi ya ũcaria |
| log | rekodi | a log file: faili ya rekodi; never "log in" |
| queue | mũhari wa kweterera | job queue: mũhari wa mawĩra |
| job / task | wĩra | plural: mawĩra |
| cron | cron | |
| script | script | |
| kernel, core | mũthingi | |
| server | sava | |
| database | kĩigĩro kĩa data | |
| folder / directory | foda | |
| file | faili | |
| image | mbica | thumbnail: mbica nini |
| preview | rora mbere | |
| upload / download | ambatĩria / ikũrũkia | |
| export / import | tũma nja / rehe thĩinĩ | |
| remove | ehereria | take out of a list or place |
| delete | thiria | destroy; deleted: nĩ gĩthirĩtio |
| edit | cenjia | |
| editor | mũcenjia / kĩnyamũ kĩa gũcenjia | person / software |
| save / store | iga | the verb "Store" of the forms |
| Store (shop tab) | nduka | the shop, as "Shop" |
| cancel | tiga | |
| OK | Nĩ wega | |
| apply | hũthĩra | |
| back | cooka | |
| next / previous | ĩrĩa ĩrũmĩrĩire / ĩrĩa ĩhĩtũku | |
| enable / disable | hingũra / hinga | enabled / disabled: nĩ kĩhingũre / nĩ kĩhinge |
| active / inactive | kĩrĩ wĩra-inĩ / gũtirĩ wĩra-inĩ | |
| run | tengeria | |
| start / stop | ambĩrĩria / rũgamia | |
| session | kĩhinda | |
| hidden / visible | kĩhithe / kĩonekaga | hide / reveal: hitha / onania |
| archive | kĩigĩro kĩa tene | |
| order (sort) | mũbango | sort order: mũbango wa gũcuuria |
| order (shop) | oda | |
| shop | nduka | |
| basket | kĩondo | |
| product | kĩndũ kĩa wendia | |
| customer | mũguri | |
| discount | kũnyiihia thogora | |
| VAT | VAT | allowlisted where it stands alone |
| price / currency | thogora / mbeca | |
| tag | tagi | |
| translation | ũtaũri | |
| language | rũthiomi | main language: rũthiomi rũnene |
| locale | rũthiomi na bũrũri | |
| state | hali | the object state |
| status | ũrĩa kĩrĩ | |
| view (button) | rora | a module view (noun): mwonekere |
| format | mũhaano | |
| sort | bange | |
| filter | thuurania | |
| select | thuura | select all: Thuura ciothe; selected: nĩ gĩthuurĩtwo |
| copy / move | kobia / thamia | |
| add / create / new | ongerera / umba / -erũ | new object: kĩndũ kĩerũ |
| restore | cookia | |
| install / uninstall | ingĩria / ruta | |
| related objects | indo iria ihuthanĩtio | relation: ũnyitanĩri |
| dashboard | ũthiũ wa wĩra | |
| identifier / ID | kĩmenyo / ID | |
| priority | mbere | |
| owner | mwene | |
| modified / created | nĩ gĩcenjetio / nĩ kĩumbĩtwo | |
| date / time | tarehe / ihinda | |
| error / warning | mahĩtia / mũtaaro | |
| help | ũteithio | |
| notification | mũmenyithia | |
| account | akaunti | |
| default | kĩrĩa kĩigĩtwo | |
| link | kĩgwatanio | |
| none / all | gũtirĩ / ciothe | |
| yes / no | ĩĩ / aca | |
| name / title / description | rĩĩtwa / kĩongo / ũtaarĩrĩria | |
| value / type / list | mũigana / mũthemba / mũhari | |
| page / path | ũrũa / njĩra | |
| required / optional | nĩ bata / wĩyendeire | |
| options | mĩthuurĩre | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is nduka, the word the shop menu uses for "Shop"; the verb "Store" of the forms is
  iga.
- "Login" in the user, session and audit lists is the user's login name; the Login button of the error page is the act
  (Ingĩra).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay as they are (Exponential, PHP, URL, ID, VAT ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
