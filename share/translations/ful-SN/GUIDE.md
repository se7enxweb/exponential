# Fula (ful-SN, Pulaar): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome, more than for most
languages: Pulaar has little software vocabulary in common use, and many terms here were chosen for this catalogue.

## 1. Language and register

**Pulaar** as written in Senegal (and Mauritania, the Gambia), the Fula variety of the Fuuta Tooro, in the official
Latin orthography of Senegal (décret 2005-980), in the plain, polite register of Pulaar newspapers, literacy books
and radio. No other locale falls back to `ful-SN`.

- Pulaar grammar throughout: noun classes with their suffixes and agreeing pronouns (deftere / defte, hello /
  kelle, innde / inɗe), the verb forms in -ii (past), -ay (future, habitual), -aani (negative past), -ataa (negative
  future); e (with, and, in), ka / to (at), ngam (for, because), walla (or), kono (but), so (if), ɗoo (here), oo /
  ngoo / koo (this, by class), fof (all), goɗɗo (other).
- **Words**: a Pulaar word where one is in use (innde = name, hello = page, ɗemngal = language, firo = translation,
  juumre = error, ballal = help, ñalnde = date, waktu = time). A technical term with no Pulaar word stays the French
  word that Senegalese speakers use, in French spelling (fichier, dossier, serveur, cache, base de données). Never
  leave the English word unless it is a product name or code.
- Short, plain sentences.

## 2. Form of address

- The user is **an** (you, singular) with the verb forms of the 2nd person singular (a waawii, a yiɗii). The polite
  plural (on) only where English speaks to all users.
- **Buttons, menu items and links**: the imperative singular: Mooftu (save, keep), Haaytu (cancel), Momtu (delete),
  Itt (remove), Waylu (edit, change), Ɓeydu (add), Tagu (create), Yiylo (search), Suɓo (choose), Neldu (send), Yiy
  (view), Yaltin (publish, bring out), Loow (upload), Telchin (download).
- **Titles, column headings and tabs**: nouns or noun phrases: Teelte, Pirooji, Nokku mawngu, Innde.
- **Instructions**: the imperative, with "Useni" where English has "please": "Suɓo huunde ndee, refti ñoƴƴu
  \"OK\".", "Useni, fadu haa adannde ndee mooftee.". A reference ("see ...") is "yiy ...".
- **Confirmations and questions**: "A yiɗii no feewi ...?": "A yiɗii no feewi momtude ndii sifaa?".
- **What the user can or may do**: "A waawii ...", "A waawaa ...". "You do not have permission" is "A alaa
  jamirol ngam ...".
- **Messages about a result**: the passive past in -aama / -ama: "Daartol ngol mooftaama.", "Fichier oo loowaama
  no moƴƴiri.".

## 3. Script and typography

- **Latin script** with the hooked letters **ɓ ɗ ƴ** (capitals Ɓ Ɗ Ƴ), **ŋ** (Ŋ) and **ñ**; long vowels and
  geminate consonants written double (aa, ee, oo, mm, nn); the prenasalised stops mb, nd, ng, nj. The apostrophe ' for
  the glottal stop where the orthography writes it (ka'e). Never ' for ɓ/ɗ/ƴ.
- **Capitals**: only the first word of a button, title or sentence and proper names.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after. The same for code, file names, INI settings and product names.
- **Quotation marks**: straight double quotes "..." or the ones of the source; `&quot;` and other entities are kept
  exactly.
- **Punctuation**: as in the source.
- **Numbers**: digits 0-9.

## 4. Glossary

Binding for the kernel and every extension. Noun class (plural) where it matters for agreement.

| English | Pulaar | Note |
|---|---|---|
| content | loowdi | content object: huunde loowdi |
| object | huunde | plural: geɗe |
| node | nokkuure | plural: nokkuuje; main node: nokkuure mawnde; parent node: nokkuure neene |
| location | nokku | main location: nokku mawngu |
| item (list entry) | huunde | |
| class | sifaa | the content class (ndii sifaa); class group: fedde sifaaji |
| attribute | jikku | plural: jikkuuji |
| datatype | sifaa keɓe | |
| version | yamre | plural: yamreeji; version history: daartol yamreeji |
| draft | daartol | the unpublished version; store draft: Mooftu daartol |
| publish | yaltin | published: yaltinaama; publishing: yaltingol |
| send for publishing | neldu ngam yaltinde | |
| discard | werlu | discard draft: Werlu daartol |
| archived / pending / rejected | mooftaama e arsiif / ina fadi / salaama | |
| siteaccess | siteaccess | the setting name, allowlisted |
| site, website | lowre | plural: lowe |
| section | taƴre | the Exponential section; an INI section: section |
| role | darnde | |
| policy | doosgol | one rule of a role |
| limitation | keerol | |
| permission | jamirol | access denied: naatgol haɗaama |
| module | module | |
| function | golle | |
| user / user group | kuutoro / fedde kuutorɓe | plural: kuutorɓe |
| group | fedde | |
| anonymous | innde anndaaka | anonymous user: kuutoro mo innde mum anndaaka |
| administrator | toppitiiɗo | administration (interface): toppitagol |
| username | innde kuutoro | login (the name): innde naatgol |
| password | finnde | the secret word |
| log in / log out | naat / yaltu | the act: naatgol / yaltugol |
| e-mail | iimeel | e-mail address: ñiiɓirde iimeel |
| cache | cache | clear cache: Laɓɓin cache |
| template | modèle | override template: modèle lomtoowo |
| override | lomtoo | |
| fallback | lomtoowo | fallback language: ɗemngal lomtoowal |
| design | design | |
| workflow | laawol golle | |
| trigger | dawroowo | |
| event | huunde waɗnde | |
| trash | ɓalli | the bin; empty trash: Ɓoor ɓalli; move to trash: Werlu e ɓalli |
| bookmark | maande | |
| settings | teelte | configuration: configuration |
| setup | teeltugol | the Setup tab |
| layout | tolno | Exponential Layouts |
| block / zone | bloc / zone | |
| extension | ɓeydaari | |
| package | paket | |
| search | yiylo | to search: yiylo; search index: index yiylo |
| log | deftere golle | a log file; never "log in" |
| queue | daaɗe | job queue: daaɗe golle |
| job / task | golle | |
| script | script | |
| kernel, core | ɓernde | |
| server | serveur | |
| database | base de données | |
| folder / directory | dossier | |
| file | fichier | |
| image | natal | plural: nate; thumbnail: natal tokooso |
| preview | yiyde adan | |
| upload / download | loow / telchin | |
| export / import | yaltin / naattin | |
| remove | itt | take out of a list or place |
| delete | momtu | destroy; deleted: momtaama |
| edit | waylu | |
| editor | binndoowo | |
| save / store | mooftu | the verb "Store" of the forms |
| Store (shop tab) | bitik | the shop, as "Shop" |
| cancel | haaytu | |
| OK | Ɗum moƴƴii | |
| apply | jaɓ | |
| back | rutto | |
| next / previous | ɗiɗaɓo / adiiɗo | |
| enable / disable | huuɓnu / ñif | enabled / disabled: ina huuɓi / ñifaama |
| active / inactive | ina gollaa / gollataa | |
| run | dogin | |
| start / stop | fuɗɗo / dartin | |
| session | session | |
| hidden / visible | suuɗaama / ina yiyee | hide / reveal: suuɗ / hollu |
| archive | arsiif | |
| order (sort) | yuɓɓo | |
| order (shop) | ñaagnde | |
| shop | bitik | |
| basket | pañe | |
| product | njeeygu | |
| customer | coodoowo | |
| discount | ustugol coggu | |
| VAT | TVA | |
| price / currency | coggu / kaalis | |
| tag | maande | an HTML tag: tag HTML |
| translation | firo | plural: pirooji |
| language | ɗemngal | plural: ɗemɗe; main language: ɗemngal mawngal |
| locale | locale | |
| state | ngonka | the object state |
| status | statut | kept apart from ngonka |
| view (button) | yiy | a module view (noun): yiyannde |
| format | format | |
| sort | yuɓɓin | |
| filter | sooɓ | the filter: sooɓirde |
| select | suɓo | select all: Suɓo fof; selected: suɓaaɗo |
| copy / move | natto / dirtin | |
| add / create / new | ɓeydu / tagu / kesum | |
| restore | artir | |
| install / uninstall | aaf / itt | |
| related objects | geɗe jokkondirɗe | relation: jokkondiral |
| dashboard | tableau de bord | |
| identifier / ID | anndinirde / ID | |
| priority | adannde | |
| owner | jom | |
| modified / created | waylaama / tagaama | |
| date / time | ñalnde / waktu | |
| error / warning | juumre / reentino | |
| help | ballal | |
| notification | tintinol | |
| account | konte | |
| default | ko woowaa | default value: keewal woowangal |
| link | jokkorde | |
| none / all | alaa / fof | |
| yes / no | eey / alaa | |
| name / title / description | innde / tiitoonde / cifol | |
| value / type / list | keewal / fannu / doggol | |
| page / path | hello / laawol | plural of hello: kelle |
| required / optional | ina waɗɗii / ina suɓoo | |
| options | cuɓe | |

## 5. Decisions after the translation

- The header tab "Store" of the shop is bitik, the word the shop menu uses for "Shop"; the verb "Store" of the forms
  is mooftu.
- "Login" in the user, session and audit lists is the user's login name (innde naatgol); the Login button of the
  error page is the act (Naat).
- A text that is used in several contexts has one translation in all of them, unless its context gives it another
  meaning (the cases of the table in TRANSLATING.md, "Words that need their context").
- Product names, codes and acronyms that stay in Latin letters (Exponential, PHP, URL, ID ...) are listed with their
  reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json` when a whole message consists of them.
