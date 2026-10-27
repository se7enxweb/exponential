# Hawaiian (haw-US): translation guide

The rules common to every language are in [TRANSLATING.md](../TRANSLATING.md). The catalogues were translated in
October 2026 from the English source, machine-assisted; a review by native speakers is welcome, above all of the
terms marked "coined" in the glossary.

## 1. Language and register

ʻŌlelo Hawaiʻi as it is written today in schools, the Hawaiian-medium press and the Hawaiian interfaces of common
software: the modern standard orthography, with the ʻokina and the kahakō. No other locale falls back to `haw-US`.

- **Vocabulary**: the modern Hawaiian computing words of the Hawaiian Lexicon Committee (Kōmike Huaʻōlelo,
  published in *Māmaka Kaiao*) where they exist: kamepiula, pūnaewele, leka uila, ʻikepili, waihona, waihona
  ʻikepili, ʻōlelo huna, loulou, anakuhi, kānana, kāmua, lepili, mana. Where no established word exists, a term was
  coined from Hawaiian roots and is marked "coined" in the glossary. An English word is not used in running text;
  product names, codes and identifiers stay as they are.
- **Word order**: Hawaiian order, verb first: "Ua mālama ʻia ke kāmua." (the draft has been saved), modifier after
  the noun: "ka mea hoʻohana hou" (the new user).
- **Plural**: **nā** before the noun ("nā mea hoʻohana"); "ka / ke" for the singular (ke before words starting
  with k, e, a, o and ʻ, ka otherwise, with the usual exceptions such as ka ʻaoʻao).
- Clear, polite and plain; no archaic or poetic forms.

## 2. Form of address

- The user is **ʻoe** (you, one person); "kou / kāu" for your. "Please" is **E ʻoluʻolu** where the source asks
  something of the user: "E ʻoluʻolu, e koho i kekahi mea." In short instructions the imperative **E ...** alone is
  enough: "E koho i ka waihona."
- **Buttons, menu items and links**: the bare verb, without "E": Mālama, Kāpae, Holoi, Hoʻoponopono, Hoʻohui, Hana,
  ʻImi, Koho, Hoʻouna, Hoʻopuka, Hoʻouka, Hoʻoiho. With an object: "Mālama i ke kāmua", "Holoi i nā mea i koho ʻia".
- **Titles, column headings and tabs**: nouns or noun phrases without the article: "Nā mea hoʻohana", "Nā
  hoʻonohonoho", "Inoa", "Kūlana".
- **Confirmations**: "Makemake maoli anei ʻoe e holoi i kēia papa?" (do you really want to delete this class?).
- **What the user can or may do**: "Hiki iā ʻoe ke ...", "ʻAʻole hiki iā ʻoe ke ...". "You do not have
  permission" is "ʻAʻohe ou ʻae e ...".
- **Messages about a result**: perfective **ua ... ʻia**: "Ua hoʻouka ʻia ka waihona.", "Ua holoi ʻia nā mea
  %count.".
- **Errors**: "Hewa: ..." or a plain sentence: "ʻAʻole i loaʻa ka waihona.".

## 3. Script and typography

- **ʻOkina**: always U+02BB (ʻ), never the apostrophe (') or a curly quote (‘ ’). The capital form is the same
  character: "ʻŌlelo", "ʻImi".
- **Kahakō**: the macron vowels ā ē ī ō ū (Ā Ē Ī Ō Ū) as precomposed characters, never a vowel followed by a
  combining macron.
- **Capitals**: the first word of a button, title or sentence and proper names: "Mālama i ke kāmua". After a leading
  ʻokina the vowel is the capital: "ʻAe", not "ʻae" at the start.
- **Placeholders** (`%name`, `%1`, `%count`, `%login_link_start%`) stay unchanged and stand free: a space before and
  after them, or a punctuation mark right after: "nā mea %count". The same for code, file names, INI settings,
  URLs and product names (Exponential, PHP, MySQL, ezoe ...).
- **HTML tags and entities** (`<b>`, `<br />`, `&quot;`, `&lt;`) are kept exactly, in the same number; the words
  inside a tag are translated.
- **Quotation marks**: straight double quotes "..." or the ones of the source.
- **Punctuation**: as in the source; Hawaiian uses the English marks (. , : ; ? !).
- **Numbers**: digits 0-9, decimal and thousands marks as the locale `share/locale/haw-US.ini` says.
- **Line breaks and leading or trailing spaces** are kept exactly as in the source.

## 4. Glossary

Binding for the kernel and every extension. "coined" marks a term made for this translation where no established
word was found; it is used the same way in every message.

| English | Hawaiian | Note |
|---|---|---|
| computer | kamepiula | |
| network, web | pūnaewele | website: kahua pūnaewele; web page: ʻaoʻao pūnaewele |
| site | kahua | the site of a siteaccess |
| siteaccess | siteaccess | the setting name; in prose also "komo kahua (siteaccess)" |
| page | ʻaoʻao | |
| content | ʻike | content object: mea ʻike |
| object | mea | |
| node | hīpuʻu | main node: hīpuʻu nui (coined in this sense) |
| location | wahi | main location: wahi nui |
| item (list entry) | mea | |
| class | papa | the content class; a PHP class: PHP class |
| attribute | hiʻohiʻona | coined in this sense |
| field (input) | pahu | input field: pahu hoʻokomo |
| type | ʻano | |
| version | mana | version history: moʻolelo o nā mana |
| draft | kāmua | |
| publish | hoʻopuka | published: ua hoʻopuka ʻia |
| discard | hoʻolei | discard draft: Hoʻolei i ke kāmua |
| archived / pending / rejected | mālama ʻia / e kali ana / hōʻole ʻia | |
| section | māhele | the Exponential section |
| role | kuleana | |
| policy | kulekele | |
| limitation | palena | |
| permission | ʻae | you are not allowed: ʻaʻole ʻoe i ʻae ʻia |
| user / user group | mea hoʻohana / pūʻulu mea hoʻohana | |
| anonymous | inoa ʻole | anonymous user: mea hoʻohana inoa ʻole |
| administrator | luna hoʻomalu | |
| username / password | inoa mea hoʻohana / ʻōlelo huna | login (the name): inoa ʻeʻe |
| log in / log out | ʻeʻe / haʻalele | |
| account | moʻokāki | |
| e-mail | leka uila | |
| data / database | ʻikepili / waihona ʻikepili | |
| file / folder | waihona / wahī | |
| image / thumbnail | kiʻi / kiʻi liʻiliʻi | |
| link / URL | loulou / URL | |
| cache | ahu | coined: "a stored heap"; clear cache: Hoʻomaʻemaʻe i ke ahu |
| template | anakuhi | |
| override / fallback | pani / koho hope | coined in these senses |
| workflow / trigger / event | kaʻina hana / mea hoʻomaka / hanana | |
| trash | pahu ʻōpala | empty trash: Hoʻonele i ka pahu ʻōpala |
| bookmark | kaha hoʻomanaʻo | coined |
| settings / configuration | nā hoʻonohonoho | |
| layout / block / zone | hoʻolālā / poloka / kāʻei | block and zone coined |
| extension / package / module | pākuʻi / pūʻolo / mōkule | mōkule coined |
| search | ʻimi | search index: papa kuhi ʻimi |
| index | papa kuhi | |
| log (record) | moʻolelo | a log file: waihona moʻolelo; never "log in" |
| queue / job / task | laina kali / hana / hana | |
| script / server | palapala hana / kikowaena | script coined |
| preview | nānā mua | |
| upload / download | hoʻouka / hoʻoiho | |
| export / import | hoʻokuʻu / lawe mai | |
| remove | wehe | take out of a list or place: wehe mai ... |
| delete | holoi | |
| edit | hoʻoponopono | |
| save / store | mālama | the verb "Store" of the forms |
| Store (shop tab) | Hale kūʻai | the shop, as the shop menu says |
| cancel | kāpae | |
| OK | ʻAe | |
| back | hoʻi | |
| next / previous | aʻe / ma mua | as buttons: Aʻe / Ma mua |
| enable / disable | hoʻā / hoʻopio | enabled / disabled: ua hoʻā ʻia / ua hoʻopio ʻia |
| active / inactive | hana / hana ʻole | |
| run / start / stop | holo / hoʻomaka / hoʻōki | |
| hidden / visible | hūnā ʻia / ʻike ʻia | hide / reveal: hūnā / hōʻike |
| order (sort) | kaʻina | sort: hoʻokaʻina |
| order (shop) | kauoha | |
| shop / basket | hale kūʻai / hīnaʻi kūʻai | |
| product / customer / price | huahana / mea kūʻai / kumukūʻai | |
| discount / VAT / currency | hoʻēmi kumukūʻai / ʻauhau kūʻai / ʻano kālā | |
| tag / label | lepili | |
| keyword | huaʻōlelo kī | |
| translation / language | unuhi / ʻōlelo | main language: ʻōlelo nui |
| state / status | kūlana | |
| view (button) | nānā | a module view (noun): hiʻona |
| select | koho | select all: Koho i nā mea a pau; selected: i koho ʻia |
| copy / move | kope / hoʻoneʻe | |
| add / create / new | hoʻohui / hana / hou | |
| restore | hoʻihoʻi | |
| install / uninstall | hoʻokomo / wehe | |
| relation / related objects | pilina / nā mea pili | |
| dashboard | papa nānā | coined |
| owner | kahu | |
| modified / created | hoʻololi ʻia / hana ʻia | |
| date / time | lā / manawa | |
| error / warning | hewa / ʻōlelo aʻo | |
| help | kōkua | |
| none / all | ʻaʻohe / a pau | |
| yes / no | ʻAe / ʻAʻole | |
| name / title / description | inoa / poʻo inoa / wehewehe | |
| required / optional | koi ʻia / koho wale | |
| comment | manaʻo | |
| newsletter / subscription | nūhou leka / kākau inoa | |
| notification | hoʻolaha | |
| filter | kānana | |
| table / list | pakuhi / papa helu | |
| menu / button | papa kuhikuhi / pihi | |
| system | ʻōnaehana | |
| group | pūʻulu | |

Further terms fixed while translating (all coined or chosen for this translation; review welcome):

| English | Hawaiian | Note |
|---|---|---|
| identifier / token | hōʻailona / lekeona | |
| approve / reject | hoʻāpono / hōʻole | approval workflow; "ʻae" stays the permission |
| lock / unlock | laka / wehe laka | locked account: pāpā ʻia |
| tree / subtree | lāʻau / lāʻau lālā | content tree: lāʻau ʻike |
| parent / child (node) | makua / keiki | sub item: mea lalo |
| media / metadata | pāpaho / ʻikepili wehewehe | |
| feed / conflict | kahe ʻike / paio | |
| session | hālāwai | a few messages say "kau"; to be unified on review |
| device / passkey / security key | hāmeʻa / kī komo / kī palekana | two-step sign-in: ʻeʻe pae ʻelua |
| default / automatic | paʻamau / ʻakomi | |
| query / schema / commit / deploy | nīnau / papahana / hoʻopaʻa / hoʻolako | |
| handler / datatype / driver / engine | mea lawelawe / ʻano ʻikepili / mea hoʻokele / mīkini | |
| template operator / function / parameter | mea hoʻokō / hana / ʻāpana | |
| row / column / key | lālani / kolamu / kī | foreign key: kī ʻē |
| program code / string / array | pāʻālua / kaula / ʻākeʻa | |
| environment / staging / production | kaiapuni / kahua hoʻāʻo / kahua hana | |
| statistics / percentage | helu ʻikepili / pākēneka | |
| checkout / exchange rate / postal code | uku / kumukūʻai hoʻololi / helu leka | |
| anchor (link target) | heleuma | |
| audit | loiloi | also "review" |
| weekdays | Lāpule, Poʻakahi ... Poʻaono | |

## 5. Decisions after the translation

- The header tab "Store" of the shop (design/admin/pagelayout) is "Hale kūʻai", as the shop menu names the shop;
  the verb "Store" of the forms is "Mālama".
- The setup text whose source holds a Qt `<byte/>` element keeps its source whole; its translation is the full text.
- Where the English source quotes a word with straight single quotes, the translation uses straight double quotes,
  so that no apostrophe can be mistaken for an ʻokina.
- The help text with the placeholders `%emphasize_start Add bookmarks %emphasize_stop` is separated from the button
  name, so the button name is translated like any other text.
- Product names, codes, acronyms, file and setting names and placeholder-only messages that stay as they are
  carry their reason in `tests/tests/lib/ezi18n/translation-identical-allowlist.json`.
