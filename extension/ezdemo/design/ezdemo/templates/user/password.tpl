<section class="user-forgotpassword">
    <form action={concat($module.functions.password.uri,"/",$userID)|ezurl} method="post" name="Password">

    <div class="attribute-header">
        <h1 class="long">{"Change password for user"|i18n("design/ezdemo/user/password")} {$userAccount.login}</h1>
    </div>

    {if $message}
    {if or( $oldPasswordNotValid, $newPasswordNotMatch, $newPasswordTooShort )}
        {if $oldPasswordNotValid}
            <div class="warning">
                <h2>{'Please retype your old password.'|i18n('design/ezdemo/user/password')}</h2>
            </div>
        {/if}
        {if $newPasswordNotMatch}
            <div class="warning">
                <h2>{"Password didn't match, please retype your new password."|i18n('design/ezdemo/user/password')}</h2>
            </div>
        {/if}
        {if $newPasswordTooShort}
            <div class="warning">
                <h2>{"The new password must be at least %1 characters long, please retype your new password."|i18n( 'design/ezdemo/user/password','',array( ezini('UserSettings','MinPasswordLength') ) )}</h2>
            </div>
        {/if}

    {else}
        <div class="feedback">
            <h2>{'Password successfully updated.'|i18n('design/ezdemo/user/password')}</h2>
        </div>
    {/if}

    {/if}

    <div class="block">
        {if $oldPasswordNotValid}*{/if}
        <label>{"Old password"|i18n("design/ezdemo/user/password")}</label><div class="labelbreak"></div>
        <input class="halfbox" type="password" name="oldPassword" size="11" value="{$oldPassword|wash}" />
    </div>

    <div class="block">
        <div class="element">
            {if $newPasswordNotMatch}*{/if}
            <label>{"New password"|i18n("design/ezdemo/user/password")}</label><div class="labelbreak"></div>
            <input class="halfbox" type="password" name="newPassword" size="11" value="{$newPassword|wash}" />
        </div>
        <div class="element">
            {if $newPasswordNotMatch}*{/if}
            <label>{"Retype password"|i18n("design/ezdemo/user/password")}</label><div class="labelbreak"></div>
            <input class="halfbox" type="password" name="confirmPassword" size="11" value="{$confirmPassword|wash}" />
        </div>
        <div class="break"></div>
    </div>

    <div class="buttonblock">
        <input class="defaultbutton" type="submit" name="OKButton" value="{'OK'|i18n('design/ezdemo/user/password')}" />
        <input class="button" type="submit" name="CancelButton" value="{'Cancel'|i18n('design/ezdemo/user/password')}" />
    </div>

    </form>
</section>
