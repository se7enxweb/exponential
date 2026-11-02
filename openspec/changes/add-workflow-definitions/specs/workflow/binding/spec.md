# Spec Delta

## Purpose

Decides which published workflow definition, if any, a content version gets, from rules on class,
subtree, section and language, with the most specific rule winning.

## ADDED Requirements

### Requirement: Binding rules
A binding rule SHALL name a definition identifier and a class identifier (`*` for any class), and
MAY name a subtree, a section and a language. Rules SHALL carry a priority (default 0) and SHALL
be enabled or disabled; disabled rules SHALL be ignored.

#### Scenario: Disabled rule is ignored
- **WHEN** the only matching rule is disabled
- **THEN** the version gets no workflow

### Requirement: Matching
A rule SHALL match a content version when the class matches (or is `*`), the object has a
location inside the rule's subtree (if set), the object is in the rule's section (if set), and
the version's initial language equals the rule's language (if set).

#### Scenario: Language rule matches a translation
- **WHEN** a rule binds `translation_review` to class `article` with language `fre-FR`, and a new
  version of an article has initial language `fre-FR`
- **THEN** the rule matches that version

#### Scenario: Language rule does not match the source language
- **WHEN** the same article gets a new version with initial language `eng-GB`
- **THEN** the `fre-FR` rule does not match

### Requirement: Most specific rule wins
Among matching rules, the most specific SHALL win. Specificity SHALL be compared in this order:
the base level (class and subtree > class > subtree > default `*`); then, for subtree rules, the
deeper subtree; then the number of set qualifiers among section and language; then the higher
priority.

#### Scenario: Class and subtree beat class
- **WHEN** rule A binds `news_article` to class `article`, and rule B binds `press_release` to
  class `article` under subtree `/1/2/55/`, and an article under `/1/2/55/` is published
- **THEN** the version gets `press_release`

#### Scenario: Class beats subtree
- **WHEN** rule A binds class `article` and rule B binds class `*` under subtree `/1/2/55/`, and an
  article under `/1/2/55/` is published
- **THEN** the version gets rule A's definition

#### Scenario: Deeper subtree wins
- **WHEN** two class `*` rules have subtrees `/1/2/` and `/1/2/55/`, and an object under
  `/1/2/55/` is published
- **THEN** the rule with subtree `/1/2/55/` wins

#### Scenario: Language adds specificity
- **WHEN** two rules bind class `article`, one with language `fre-FR`, and a version with initial
  language `fre-FR` is published
- **THEN** the rule with the language wins

#### Scenario: Priority breaks a tie
- **WHEN** two rules of equal specificity match with priorities 0 and 10
- **THEN** the rule with priority 10 wins

### Requirement: Deterministic ties
When two matching rules are equal in specificity and priority, the rule created first SHALL win,
and the tie SHALL be reported as a warning when the rules are listed or checked.

#### Scenario: Unresolved tie
- **WHEN** two enabled rules have the same class, no other qualifiers and the same priority
- **THEN** resolution always returns the older rule, and checking the bindings warns about the pair

### Requirement: Only published definitions are chosen
Resolution SHALL only return a definition that has a published version. A rule for an identifier
without a published version SHALL be skipped as if it did not match, and checking the bindings
SHALL warn about it.

#### Scenario: Rule for a draft-only definition
- **WHEN** the most specific matching rule names an identifier that has only a draft version and a
  less specific rule names a published definition
- **THEN** the version gets the published definition of the less specific rule

### Requirement: No match means no workflow
When no rule matches, resolution SHALL return no definition, and publishing SHALL be unaffected by
this extension.

#### Scenario: Unbound class
- **WHEN** no rule matches class `folder` and no default rule exists
- **THEN** resolution returns no definition

### Requirement: Rules check timer attributes
Adding or enabling a rule SHALL be refused when the published definition of its identifier has a
timer on an attribute that the rule's class lacks or that is not a date or datetime attribute.

#### Scenario: Binding a class without the timer attribute
- **WHEN** a rule binds `news_article`, whose published version has a timer on `expiry_date`, to
  class `folder`, which has no `expiry_date`
- **THEN** the rule is refused with an error naming the attribute and the class
