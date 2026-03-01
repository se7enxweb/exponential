<ul class="nav">
    <li id="tagcloud"><a href={concat("/content/view/tagcloud/", $pagedata.root_node)|ezurl} title="{'Tag cloud'|i18n('design/ezdemo/pagelayout')}">{'Tag cloud'|i18n('design/ezdemo/pagelayout')}</a></li>
    <li id="sitemap"><a href={concat("/content/view/sitemap/", $pagedata.root_node)|ezurl} title="{'Site map'|i18n('design/ezdemo/pagelayout')}">{'Site map'|i18n('design/ezdemo/pagelayout')}</a></li>
    {if $basket_is_empty|not()}
    <li id="shoppingbasket"><a href={"/shop/basket/"|ezurl} title="{'Shopping basket'|i18n('design/ezdemo/pagelayout')}">{'Shopping basket'|i18n('design/ezdemo/pagelayout')}</a></li>
   {/if}
{if $current_user.is_logged_in}
    <li id="myprofile"><a href={"/user/edit/"|ezurl} title="{'My profile'|i18n('design/ezdemo/pagelayout')}">{'My profile'|i18n('design/ezdemo/pagelayout')}</a></li>
    <li id="logout"><a href={"/user/logout"|ezurl} title="{'Logout'|i18n('design/ezdemo/pagelayout')}">{'Logout'|i18n('design/ezdemo/pagelayout')} ( {$current_user.contentobject.name|wash} )</a></li>
{else}
    {if ezmodule( 'user/register' )}
    <li id="registeruser"><a href={"/user/register"|ezurl} title="{'Register'|i18n('design/ezdemo/pagelayout')}">{'Register'|i18n('design/ezdemo/pagelayout')}</a></li>
    {/if}
    <li id="login" class="transition-showed">
        <a href="#login" title="show login form" class="show-login-form">{'Login'|i18n('design/ezdemo/pagelayout')}</a>
        <a href="#" title="hide login form" class="hide-login-form">{'Login'|i18n('design/ezdemo/pagelayout')}</a>
        <form class="login-form span3" action="{'/user/login'|ezurl( 'no' )}" method="post">
            <fieldset>
                <label>
                    <span class="hidden">{'Username'|i18n('design/ezdemo/pagelayout')}</span>
                    <input type="text" name="Login" id="login-username" placeholder="Username">
                </label>
                <label>
                    <span class="hidden">{'Password'|i18n('design/ezdemo/pagelayout')}</span>
                    <input type="password" name="Password" id="login-password" placeholder="Password">
                </label>
                <div class="clearfix">
                    <a href="{'/user/forgotpassword'|ezurl( 'no' )}" class="forgot-password">{'Forgot your password?'|i18n('design/ezdemo/pagelayout')}</a>
                    <button class="btn btn-warning pull-right" type="submit">
                        {'Login'|i18n('design/ezdemo/pagelayout')}
                    </button>
                </div>
            </fieldset>
            <input type="hidden" name="RedirectURI" value="" />
        </form>
    </li>
{/if}
</ul>
