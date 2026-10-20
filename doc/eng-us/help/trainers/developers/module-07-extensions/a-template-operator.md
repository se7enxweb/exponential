---
title: "Lesson 7.5: A template operator"
audience: trainer, developer
category: training
order: 75
summary: Learners write the operator mysite_initials with an optional parameter, register it in autoloads/eztemplateautoload.php and ExtensionAutoloadPath, and use it in a template. 55 minutes.
keywords: developers course, template operator, eztemplateautoload.php, eZTemplateOperatorArray, operatorList, namedParameterPerOperator, namedParameterList, modify, operatorValue, ExtensionAutoloadPath, TemplateSettings, RAD template extension wizard
type: help
---
# Lesson 7.5: A template operator

A template operator is the smallest useful extension: a new word for templates, such as `|mysite_initials`. It is
also the best first piece of PHP for template builders, because its input and output are plain values. In this
lesson learners write, register and use an operator with one optional parameter, and compare their hand-written
version with the RAD wizard.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 7: Extensions](README.md) |
| Duration | 55 minutes (15 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 7.3](autoloads-and-activation.md); [Lesson 2.4](../module-02-the-template-language/operators-wash-and-translations.md) |

## Learning objectives

After this lesson, learners can:

- write an operator class with `operatorList()`, `namedParameterPerOperator()`, `namedParameterList()` and
  `modify()`;
- give every optional parameter a default value;
- register the operator in `autoloads/eztemplateautoload.php` and `[TemplateSettings] ExtensionAutoloadPath[]`;
- use the operator in pipe form and with a parameter;
- find the RAD wizard that writes operators.

## Trainer's notes

### How to present it

Sources: a real operator extension, `extension/str_replace/` (if installed), the template operator warning in
[Templates and design, part 7](../../../../guides/templates-and-design.md#an-operator-parameter-without-a-default-value),
and [RAD tools](../../../../features/6.0/rad-tools.md).

1. **The registration** (3 minutes). `autoloads/eztemplateautoload.php` fills `$eZTemplateOperatorArray[]` with the
   `script`, the `class` and the `operator_names`. The extension's `settings/site.ini.append.php` adds
   `[TemplateSettings] ExtensionAutoloadPath[]=<extension>` so the kernel reads that file.
2. **The class** (6 minutes). Walk through the exercise's class: `operatorList()` names the operators;
   `namedParameterPerOperator()` returns `true`; `namedParameterList()` lists each parameter with `type`,
   `required` and `default`; `modify()` receives the value on the left of the pipe in `$operatorValue` (by
   reference) and the parameters in `$namedParameters`, and replaces `$operatorValue` with the result.
3. **Defaults** (2 minutes). A parameter without a default that a template leaves out arrives as `null`, and the
   debug output warns "Operator '...' has no default value for parameter '...'".
4. **Escaping** (2 minutes). An operator returns a value; the template washes it when it prints it. Do not return
   HTML from an operator unless that is its job, and then escape inside.
5. **The wizard** (2 minutes). **Setup** > **RAD**, the template extension wizard (`/setup/templateoperator`),
   writes operators, fetch functions and template functions as a complete extension.

### Common questions

**Can it be called in function form?** This operator reads the value on the left of the pipe, so write
`{$name|mysite_initials}`. An operator that should also work as `{my_operator( $value )}` takes the value as a
parameter too and uses it when given, as `str_replace`'s `subject` parameter does.

**Can one class hold several operators?** Yes: list them in `operatorList()` and `operator_names`, and switch on
`$operatorName` in `modify()`.

**Is an operator compiled into the template cache?** Its call is; the operator runs every time the compiled
template runs, unless it declares hints for compile-time evaluation. Keep operators fast.

### Pitfalls

- **Forgetting `ExtensionAutoloadPath[]`.** The operator is unknown and the debug output says so.
- **Not clearing caches.** After registering, clear the `ini` and `template` caches.
- **Prefix.** Operator names are global to all templates; prefix them (`mysite_`) to avoid clashes.

## Hands-on exercise

On the development installation, in `extension/mysite_hello`.

1. Create `autoloads/mysitehellooperators.php`:

   ```php
   <?php
   /**
    * Template operators of mysite_hello: {$text|mysite_initials} and {$text|mysite_initials( '-' )}
    */
   class mysiteHelloOperators
   {
       public function operatorList()
       {
           return array( 'mysite_initials' );
       }

       public function namedParameterPerOperator()
       {
           return true;
       }

       public function namedParameterList()
       {
           return array( 'mysite_initials' => array( 'separator' => array( 'type' => 'string',
                                                                            'required' => false,
                                                                            'default' => '' ) ) );
       }

       public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
       {
           if ( $operatorName === 'mysite_initials' )
           {
               $initials = array();
               foreach ( preg_split( '/\s+/u', trim( (string)$operatorValue ) ) as $word )
               {
                   if ( $word !== '' )
                       $initials[] = mb_strtoupper( mb_substr( $word, 0, 1 ) );
               }
               $operatorValue = implode( $namedParameters['separator'], $initials );
           }
       }
   }
   ```

2. Create `autoloads/eztemplateautoload.php`:

   ```php
   <?php
   $eZTemplateOperatorArray = array();
   $eZTemplateOperatorArray[] = array( 'script' => 'extension/mysite_hello/autoloads/mysitehellooperators.php',
                                       'class' => 'mysiteHelloOperators',
                                       'operator_names' => array( 'mysite_initials' ) );
   ```

3. Register the autoload path:
   `php bin/php/console exp:ini add site.ini/TemplateSettings/ExtensionAutoloadPath mysite_hello extension:mysite_hello`.
4. Check both PHP files with `php -l`; clear the `ini` and `template` caches.
5. In the playground, use the operator:

   ```
   [{'Ada Lovelace'|mysite_initials}] [{'grace brewster hopper'|mysite_initials( '.' )}] [{$node.name|mysite_initials|wash}]
   ```

6. Remove `'default' => ''` from the parameter definition, clear the template cache, run the playground with
   `--debug=warning`, and read the warning. Put the default back.
7. Use the operator in `greet.tpl` of lesson 6.2: `<p>Initials: {$name|mysite_initials|wash}</p>`, and open the view.
8. Open **Setup** > **RAD** and find the template extension wizard; compare the files it lists with yours.
9. Commit the files.

## Expected result

- Step 5 prints `[AL] [G.B.H]` and the initials of node 2's name.
- Step 6: the warning "Operator 'mysite_initials' has no default value for parameter 'separator'", followed by
  `(compiling <template>)` because the template was compiled anew.
- Step 7: the greeting page shows the initials.
- The trainer checks one learner's playground output.

## Quiz

1. Which file registers an extension's template operators?
2. Which setting makes the kernel read that file?
   A. `ActiveExtensions[]` B. `ExtensionAutoloadPath[]` C. `DesignExtensions[]` D. `ExtensionRepositories[]`
3. In `modify()`, where is the value on the left of the pipe, and how is the result returned?
4. What happens when a parameter has no default and a template leaves it out?
5. Why prefix operator names?

<details>
<summary>Answers</summary>

1. `autoloads/eztemplateautoload.php` of the extension.
2. **B.** `[TemplateSettings] ExtensionAutoloadPath[]`.
3. In `$operatorValue`, passed by reference; the method replaces it with the result.
4. The operator gets `null`, and the debug output warns once per operator and parameter.
5. Operator names are shared by all templates of the installation; a prefix avoids clashes.

</details>

## Further reading

- [Template operators in a theme](../../../designers/extensions/template-operators-in-a-theme.md).
- [Templates and design, part 7](../../../../guides/templates-and-design.md#7-debugging-templates): the warning for
  missing defaults.
- [RAD tools](../../../../features/6.0/rad-tools.md): the template extension wizard.

## Related pages

- [Module 7: Extensions](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
