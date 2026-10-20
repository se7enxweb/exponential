---
title: "Lesson 6.4: Forms and actions in a view"
audience: trainer, developer
category: training
order: 64
summary: Learners add a form to their view, map its button to an action with single_post_actions, read the posted value with the module's action parameters, validate it, redirect after the post, and see the form token protect the form. 55 minutes.
keywords: developers course, form, POST, single_post_actions, post_action_parameters, isCurrentAction, actionParameter, redirectTo, eZHTTPTool, ezformtoken, ezxform_token, CSRF, form expired, validation, post redirect get
type: help
---
# Lesson 6.4: Forms and actions in a view

Views that change something receive a form. The kernel turns a pressed button into a named **action**, hands the
view the posted values, and protects every form against cross-site request forgery with a form token. In this
lesson learners add a small form to the `greet` view: a visitor types a name, the view validates it and redirects
to the greeting.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 6: Modules and views](README.md) |
| Duration | 55 minutes (15 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 6.3](policies-for-your-views.md) |

## Learning objectives

After this lesson, learners can:

- map a button to an action with `single_post_actions` and its values with `post_action_parameters`;
- test the action with `$Module->isCurrentAction()` and read values with `$Module->actionParameter()`;
- validate input in the view and show an error in the template;
- redirect after a successful post with `$Module->redirectTo()`;
- explain what the form token (`ezformtoken`) does and what the "form has expired" page means.

## Trainer's notes

### How to present it

Sources: `kernel/content/module.php` (`single_post_actions` of `edit` and `copy`), the methods `isCurrentAction()`,
`actionParameter()` and `redirectTo()` in `lib/ezutils/classes/ezmodule.php`, and
["This form has expired"](../../../../features/6.0/form-expired-page.md).

1. **Actions** (4 minutes). In `module.php`, `'single_post_actions' => array( 'GreetButton' => 'Greet' )` means: a
   POST with a field `GreetButton` is the action `Greet`. `'post_action_parameters' => array( 'Greet' => array(
   'Name' => 'GreetName' ) )` makes the posted field `GreetName` available as the action parameter `Name`.
2. **In the view** (4 minutes). `if ( $Module->isCurrentAction( 'Greet' ) )`, then
   `$Module->actionParameter( 'Name' )`. Validate; on success `return $Module->redirectTo( ... )`; on failure set
   an error for the template.
3. **Post, redirect, get** (2 minutes). Redirecting after a successful post means a reload does not post again.
4. **The form token** (3 minutes). `ezformtoken` adds a hidden field `ezxform_token` to every HTML form the kernel
   outputs and checks it on every POST. A post without it is refused with the "form has expired" page: nothing is
   saved. JavaScript that posts must send the token too.
5. **Never trust the input** (2 minutes). Trim and check length and characters in PHP; wash in the template.

### Common questions

**Do I add the token field myself?** No, the output filter adds it to forms in the HTML. Ajax code must send it; the
`ezformtoken` README explains how.

**Why `redirectTo` and not just showing the result?** A reload of a page that came from a POST asks the browser to
post again; a redirect turns it into a normal page view.

**Can I use `$_POST` directly?** Use `eZHTTPTool::instance()->postVariable()` or the action parameters; they are what
the rest of the kernel uses and what other developers expect.

### Pitfalls

- **A button name that is not in `single_post_actions`.** The view sees no action; nothing happens.
- **Testing with a page opened before a restart.** An old page may carry an old token: reload the form first.
- **A redirect to an address built from input.** Redirect to your own view; never to an address taken from the
  form ([Safe redirects](../../../../features/6.0/safe-redirects.md)).

## Hands-on exercise

On the training installation, in `extension/mysite_hello`. Signed in as an administrator, or with the Anonymous
policy of lesson 6.3.

1. Extend the view in `modules/hello/module.php`:

   ```php
   $ViewList['greet'] = array(
       'functions' => array( 'greet' ),
       'script' => 'greet.php',
       'params' => array( 'Name' ),
       'unordered_params' => array( 'times' => 'Times' ),
       'single_post_actions' => array( 'GreetButton' => 'Greet' ),
       'post_action_parameters' => array( 'Greet' => array( 'Name' => 'GreetName' ) ) );
   ```

2. Add the action handling at the start of `modules/hello/greet.php`, after the opening comment:

   ```php
   $error = false;
   if ( $Module->isCurrentAction( 'Greet' ) )
   {
       $posted = trim( (string)$Module->actionParameter( 'Name' ) );
       if ( $posted === '' || mb_strlen( $posted ) > 40 )
       {
           $error = 'Please type a name of 1 to 40 characters.';
       }
       else
       {
           return $Module->redirectTo( '/hello/greet/' . rawurlencode( $posted ) );
       }
   }
   ```

   and pass it to the template: `$tpl->setVariable( 'error', $error );`.
3. Add the form to `design/standard/templates/hello/greet.tpl`:

   ```
   {if $error}<p class="error">{$error|wash}</p>{/if}
   <form method="post" action={'/hello/greet'|ezurl}>
       <label for="greet-name">Name</label>
       <input id="greet-name" type="text" name="GreetName" value="" />
       <input type="submit" name="GreetButton" value="Greet" />
   </form>
   ```

4. Check the files with `php -l` and `eztemplatecheck.php`; clear the `ini`, `global_ini` and template caches.
5. Open `/hello/greet`, type a name and press **Greet**.
6. Send the form empty; read the error.
7. View the page source of the form and find the hidden field `ezxform_token`.
8. In the browser's developer tools, delete the hidden token field, then send the form: read the "form has expired"
   page.
9. Commit the changes.

## Expected result

- Step 5 redirects to `/hello/greet/<name>` and greets the name.
- Step 6 shows "Please type a name of 1 to 40 characters." above the form.
- Step 7: an `<input type="hidden" name="ezxform_token" ...>` inside the form.
- Step 8: the form expired page; nothing is processed.
- The trainer checks one learner's form in the browser's developer tools.

## Quiz

1. What does `'single_post_actions' => array( 'GreetButton' => 'Greet' )` do?
2. Which method tells the view that the action `Greet` was posted?
   A. `actionParameter()` B. `isCurrentAction()` C. `redirectTo()` D. `currentAction()` only
3. Why redirect after a successful post?
4. What does the form token protect against?
5. Where must the error message be washed?

<details>
<summary>Answers</summary>

1. A POST with the field `GreetButton` becomes the action `Greet`.
2. **B.** `$Module->isCurrentAction( 'Greet' )`.
3. So a reload does not post the form again (post, redirect, get).
4. Cross-site request forgery: another site making the browser post a form to yours.
5. In the template, where it is printed: `{$error|wash}`.

</details>

## Further reading

- ["This form has expired"](../../../../features/6.0/form-expired-page.md): form token refusals.
- [Safe redirects](../../../../features/6.0/safe-redirects.md): redirect targets from user input.
- [Security standard](../../../../standards/security.md): the project's security rules for code.

## Related pages

- [Module 6: Modules and views](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
