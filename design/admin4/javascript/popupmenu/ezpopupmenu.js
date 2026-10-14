//
// Created on: <1-Aug-2002 16:45:00 fh>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: Exponential
// SOFTWARE RELEASE: 4.1.x
// COPYRIGHT NOTICE: Copyright (C) 1998 - 2026 7x & Exponential Foundation
// COPYRIGHT NOTICE: Copyright (C) 1999-2014 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

/*! \file ezpopupmenu.js
*/


/*!
  \brief
  This javascript provides a very cool and flexible DHTML popupmenu.
  In order to use this javascript you must also include ezlibdomsupport.js.


  Features:
  - Multilevel popupmenu
  - Supports all major browsers
  - All HTML code in template
  - Supports context sensitivity for Exponential through:
     - String substitution in the href attribute of menuitems.
     - Form submital with string substitution.

  Currenty not supported but possible if requested:
  - Dynamic building of menus based on Exponential content.

 Public interface:
ezpopmenu_showTopLevel - This method opens a new top-level popupmenu.
ezpopmenu_showSubLevel - This menu opens a sublevel popupmenu. It does not hide any parent menus.
ezpopmenu_submitForm - This method is used to activate a form.
ezpopmenu_mouseOver - This method should be called by all menuitems when a mouseover event occurs. It currently hides and submenus that should not be shown.
ez_createAArray - Helper method to create associative arrays. It takes an array with an even number of elements and makes a associate element out of every two array elements.

In order to use the popupmenu javascript you need to provide:
1. The HTML/CSS structure for you menu(s)
2. The menuArray javascript array containing the configuration of your popupmenu(s).

Exponential provides a default template for this purpose. It is located in popupmenu/popup_menu.tpl. You are encouraged to override this file in the siteacess where you want to use the menu.

 1. Setting up the HTML/CSS structure for your popup menu(s).
 Your menu should be set up completely with all elements. The following requirements apply:
- The outer element should be a "div".
- The css of the outer div must have the following CSS attributes set:
  position: absolute;
  z-index: +1;
  display: none;
- The menuitems must be of type "a".
- Both the menu and the menuitems must be given and "id". This id is used in the menuArray to set up that menu/item.
- Each menuitem must call ezpopmenu_mouseOver or one of the methods spawning a metnu on the mouseover event. The name of the enclosing menu must be given as parameter.
- The menus should be defined in the order they will be shown. In other words, the mainmenu first and then any submenus. This is to ensure the correct visible stack order.

An example example with a menu and a submenu.

 example with a main menu and a submenu:
 -------------------------------------------------------------------------------
<!-- The main menu -->
 <div class="menu" id="mainmenu">
  <a id="menu-main" href="http://www.ez.no" onmouseover="ezpopmenu_mouseOver( 'mainmenu' )" >ez.no</a>
  <a id="menu-substitute" href="#" onmouseover="ezpopmenu_mouseOver( 'mainmenu' )" >Dynamic node view</a>
  <a id="menu-spawn" href="#" onmouseover="ezpopmenu_showSubLevel( 'submenu', 'menu-spawn' )" >Show submenu</a>
 </div>
<!-- The submenu -->
 <div class="menu" id="submenu">
  <a id="submenu-item" href="#" onmouseover="ezpopmenu_submitForm( 'myform' )">Form submitter</a>
 </div>
--------------------------------------------------------------------------------


 2. Setting up the menuArray array containing the popupmenu configuration.
 The menuArray is a multilevel array describing the features of your menus. The structure of the
 menuArray array is flat. This means that each menu is described in the toplevel array.
 Each menu can have the following properties set:
  - "depth" [required]: The depth of the menu. Toplevel menus have depth 0. Menus appearing from a toplevel menu
    have depth 1 etc.
  - "elements": The elements property must be yet another array containing all the menuitems that require string substitution.
    Each item can contain the following properties:
      + "url": The URL that this element should point at. Put the part that should be substituted within "%" symbols.

The following example configures the menu created in step 1.
--------------------------------------------------------------------------------
<script language="JavaScript1.2" type="text/javascript">

 var menuArray = new Array();
 <!-- main menu -->
 menuArray['mainmenu'] = {};
 menuArray['mainmenu']['depth'] = 0;
 menuArray['mainmenu']['elements'] = {};
 menuArray['mainmenu']['elements']['menu-substitute'] = new Array();
 menuArray['mainmenu']['elements']['menu-substitute']['url'] = {'/content/view/%nodeID%'|ezurl};

 <!-- submenu -->
 menuArray['submenu'] = {};
 menuArray['submenu']['depth'] = 1; // this is a first level submenu of ContextMenu

</script>

 <!-- The form submitted by the submenu -->
 {* Remove a node. *}
<form id="myform" method="post" action="someactionhere">
  <!-- Notice that there is autoamatic string translation for the contents of value -->
  <input type="hidden" name="example" value="%nodeID%" />
</form>

--------------------------------------------------------------------------------


 Finally you will need to activate the "mainmenu" menu somehow. This is achieved through a call to ezpopmenu_showToplevel. In the eaxample case links in the setup array containing the string %nodeID% will be substituted by 42.

 example:
 <a onmouseclick="ezpopmenu_showTopLevel( event, 'ContextMenu', ez_createAArray( array( %nodeID%, 42) ) ); return false;">show</a><br />


#################### Developer info ##############################
 This script defines the following global constants/variables:
 EZPOPMENU_OFFSET - Added to the x,y position of the mouse when showing the menu.
 CurrentNodeID - Used to remember the node we are currently showing the menu for. Used by submenus.
 VisibleMenus - An array containing the currently visible menus.
 */

// Global vars used in templates
var CurrentSubstituteValues = -1;

// Create scope to avoid filling global scope more then needed
(function()
{
//POPUPMENU CONSTANTS
var EZPOPMENU_OFFSET = 8, EZPOPMENU_SUBOFFSET = 4, EZPOPMENU_SUBTOPOFFSET = 4;

// POPUPMENU VARS
var CurrentDisableIDList = [];
// Which Menu should be disabled
var CurrentDisableMenuID = -1;

var CurrentDisabledMenusItems = {}, DefaultDisabledMenuItemCSSClass = 'menu-item-disabled';;
// VisibleMenus is an array that holds the names of the currently visible menus
var VisibleMenus = [];
// The element that opened the visible top-level menu, and per submenu the item that opened it (keyboard layer)
var MenuOpener = null, SubMenuOpeners = {};

/*!
  Controls the popup offsets of the menu relative to the mouse position.
  Default values are offsetX = 8 and offsetY = 4.
 */
function _initOffsets( offsetX, offsetY )
{
    EZPOPMENU_OFFSET = offsetX;
    EZPOPMENU_SUBTOPOFFSET = offsetY;
}

/*!
 Sets an element of the substitute array.
 This function can be used if you want to change some substitution values dynamically,
 E.g based on the element you chose in the menu.
*/
function _setSubstituteValue( key, value )
{
  if ( CurrentSubstituteValues != -1 )
  {
      CurrentSubstituteValues[key] = value;
  }
}

/*!
   Shows toplevel menu at the current mouseposition + EZPOPMENU_OFFSET.
   'event' This parameter is for the mouse event.
   'menuID' is the identification of the menu to use.
   'substituteValues' is an associative array. The string value of each item in the menu will have they keys, substituted by the value in this array.
   'menuHeader' If the menu has a header it is replaced with this text (plain text, not markup).
   'disableID' If this id is found in the list of known disabled for this menu the item is disabled.
 */
function _showTopLevel( event, menuID, substituteValues, menuHeader, disableIDList, disableMenuID )
{
    if ( !document.getElementById( menuID ) ) return;
    var mousePos = _mouseHandler( event ); // register new mouse position
    var byKeyboard = _isKeyboardEvent( event );

    if ( substituteValues != -1 ) // new topmenu
    {
        _hideAll();
        CurrentSubstituteValues = substituteValues;
        _setOpener( event );
        // Opened with the keyboard (Enter on the trigger, Shift+F10): no mouse position, so open under the trigger.
        if ( byKeyboard && MenuOpener && MenuOpener.getBoundingClientRect )
        {
            var rect = MenuOpener.getBoundingClientRect(), screenData = _getScreenProperties();
            mousePos = { 'x': rect.left + screenData.ScrollX - EZPOPMENU_OFFSET, 'y': rect.bottom + screenData.ScrollY - EZPOPMENU_OFFSET };
        }
    }

    if ( disableIDList && disableIDList != -1 )
    {
        CurrentDisableIDList = disableIDList.push !== undefined ? disableIDList : [disableIDList];
    }

    CurrentDisableMenuID = disableMenuID;

    _doItemSubstitution( menuID, menuHeader );

    // make menu visible
    _makeVisible( menuID );
    _moveTopLevelOnScreen( menuID, mousePos );
    if ( byKeyboard ) _focusItem( menuID, 0 );
}

/*!
  Show sublevel menu. The current substitute values are remembered from the last call to
  ezpopmenu_showTopLevel().
  Params:
  event - Just pass the event causing the script to popup.
  menuName - The name of the menu to popup
  overItem - The id of the item that caused the popup. This is used to reposition the menu correctly.
 */
function _showSubLevel( event, menuID, overItem )
{
    if ( !document.getElementById( menuID ) ) return;
    //var mousePos = _mouseHandler( event ); // register new mouse position
    //    ezpopmenu_showTopLevel( event, menuName, -1 );
    _doItemSubstitution( menuID );

    _hideHigher( menuArray[menuID]['depth'] - 1 ); //hide all other submenus

    // make menu visible (show first since width is used in _moveSubLevelOnScreen)
    _makeVisible( menuID );
    _moveSubLevelOnScreen( menuID, overItem );

    var opener = document.getElementById( overItem );
    if ( opener )
    {
        SubMenuOpeners[menuID] = opener;
        opener.setAttribute( 'aria-expanded', 'true' );
    }
}

/*!
  Makes a window visible for the user.
  This method also sets the necessary variables in order to make the menu
  disappear when appropriate.
 */
function _makeVisible( menuID )
{
    var el = document.getElementById( menuID );
    if ( el ) el.style.display = 'block';
    VisibleMenus[menuArray[menuID]['depth']] = menuID;
    _decorate( menuID );

    _listenForOutsideClicks();
}

/*!
  A press of the mouse outside every shown menu hides them all. The listener is added once, beside any other
  mousedown handler of the page (an assignment to document.onmousedown replaced the page's own), and does nothing
  while no menu is shown.
*/
var OutsideClickListening = false;
function _listenForOutsideClicks()
{
    if ( OutsideClickListening || !document.addEventListener ) return;
    OutsideClickListening = true;
    document.addEventListener( 'mousedown', function( e )
    {
        if ( !VisibleMenus[0] || VisibleMenus[0] == 'none' ) return;
        for ( var i = 0, l = VisibleMenus.length; i < l && VisibleMenus[i] != 'none'; i++ )
        {
            var menu = document.getElementById( VisibleMenus[i] );
            if ( menu && e.target && menu.contains( e.target ) ) return;
        }
        _hideAll();
    }, false );
}

/*!
  Substitute the values of the items in the menu with the items given to the first
  showTopLEvel call.
 */
function _doItemSubstitution( menuID, menuHeader )
{
    // Do URL replace for all items in that menu
    for ( var i in menuArray[menuID]['elements'] )
    {
        var hrefElement = document.getElementById( i );

        if ( !hrefElement )
        {
            continue;
        }

        // href replacement
        var replaceString = menuArray[menuID]['elements'][i]['url'];

        if ( replaceString )
        {
            replaceString = _substituteString( replaceString, CurrentSubstituteValues );
            hrefElement.setAttribute( "href", replaceString );
        }

        // dynamic generation
        var loopingVariable = menuArray[menuID]['elements'][i]['variable'];

        if ( loopingVariable )
        {
            _fillItems( hrefElement, menuArray[menuID]['elements'][i]['content'], CurrentSubstituteValues[loopingVariable] );
        }

        var disabledForElement = false;
        if ( typeof( menuArray[menuID]['elements'][i]['disabled_for'] ) != 'undefined' && CurrentDisableIDList )
        {
            for ( var disI = 0, disL = CurrentDisableIDList.length; disI < disL; disI++ )
            {
                if ( disabledForElement = menuArray[menuID]['elements'][i]['disabled_for'][ CurrentDisableIDList[disI] ] === 'yes'  )
                    break;
            }
        }

        if ( typeof( menuArray[menuID]['elements'][i]['disabled_class'] ) != 'undefined' &&
              ( disabledForElement ||
              ( CurrentDisableMenuID && hrefElement.id == CurrentDisableMenuID ) ) )
        {
            CurrentDisabledMenusItems[hrefElement.id] = new Array();
            CurrentDisabledMenusItems[hrefElement.id]['className'] = hrefElement.className;
            CurrentDisabledMenusItems[hrefElement.id]['href'] = hrefElement.getAttribute('href');
            CurrentDisabledMenusItems[hrefElement.id]['onmouseover'] = hrefElement.onmouseover;
            CurrentDisabledMenusItems[hrefElement.id]['onclick'] = hrefElement.onclick;

            hrefElement.className = menuArray[menuID]['elements'][i]['disabled_class'];
            hrefElement.setAttribute( "href", '#' );
            hrefElement.onmouseover = "";
            hrefElement.onclick = "";

        }
        else if ( typeof( menuArray[menuID]['elements'][i]['disabled_class'] ) != 'undefined' &&
                  hrefElement.className == menuArray[menuID]['elements'][i]['disabled_class'] )
        {
            // Restore(enable) menu item
            if ( typeof( CurrentDisabledMenusItems[hrefElement.id] ) != 'undefined' )
            {
                hrefElement.className = CurrentDisabledMenusItems[hrefElement.id]['className'];
                hrefElement.href = CurrentDisabledMenusItems[hrefElement.id]['href'];
                hrefElement.onmouseover = CurrentDisabledMenusItems[hrefElement.id]['onmouseover'];
                hrefElement.onclick = CurrentDisabledMenusItems[hrefElement.id]['onclick'];
            }
        }
    }

    // set header: the header is text (a node or class name), never markup
    if ( menuHeader && typeof( menuArray[menuID]['headerID'] ) != 'undefined' )
    {
        var header = document.getElementById( menuArray[menuID]['headerID'] );
        if ( header ) header.textContent = String( menuHeader );
    }
}

/*!
  Fills element with one copy of the item markup 'content' (the menu template's own markup) per entry of the
  looping variable (%languages%, %classList%). The values - the entry's own (%name%, %locale%, %classID%) and the
  plain values of the menu (%nodeID%, ...) - are data: they are put into the parsed copy as text and as attribute
  values, escaped for a script in an event handler attribute, so a language or class name never becomes markup.
 */
function _fillItems( element, content, entries )
{
    while ( element.firstChild ) element.removeChild( element.firstChild );
    if ( !content ) return;
    for ( var index in entries )
    {
        var entry = entries[index];
        if ( typeof entry != 'object' || entry === null )
            continue;

        var values = {};
        for ( var localItem in entry )
        {
            values['%' + localItem + '%'] = entry[localItem];
        }
        for ( var substItem in CurrentSubstituteValues )
        {
            if ( typeof CurrentSubstituteValues[substItem] != 'object' && typeof CurrentSubstituteValues[substItem] != 'function' )
            {
                values[substItem] = CurrentSubstituteValues[substItem];
            }
        }

        var holder = document.createElement( 'div' );
        holder.innerHTML = content;
        _fillNodes( holder, values );
        while ( holder.firstChild ) element.appendChild( holder.firstChild );
    }
}

function _fillNodes( parent, values )
{
    for ( var node = parent.firstChild; node; node = node.nextSibling )
    {
        if ( node.nodeType === 3 )
        {
            node.nodeValue = _fillString( node.nodeValue, values, false );
        }
        else if ( node.nodeType === 1 )
        {
            for ( var a = 0; a < node.attributes.length; a++ )
            {
                var name = node.attributes[a].name, value = node.attributes[a].value;
                var filled = _fillString( value, values, /^on/i.test( name ) );
                if ( filled !== value ) node.setAttribute( name, filled );
            }
            _fillNodes( node, values );
        }
    }
}

// Every placeholder of values in text, in one pass (a value that holds a placeholder stays as it is); in a script
// (an event handler attribute) as the content of a string literal.
function _fillString( text, values, inScript )
{
    var keys = [];
    for ( var key in values )
    {
        if ( text.indexOf( key ) !== -1 ) keys.push( key.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' ) );
    }
    if ( !keys.length ) return text;
    keys.sort( function( x, y ) { return y.length - x.length; } );
    return text.replace( new RegExp( keys.join( '|' ), 'g' ), function( key )
    {
        var value = String( values[key] );
        return inScript ? JSON.stringify( value ).slice( 1, -1 ).replace( /'/g, "\\'" ) : value;
    } );
}

function _substituteString( replaceString, substituteValues )
{
    // loop though substitute values and substitute for each of them
    for ( var substItem in substituteValues )
    {
        if ( typeof substituteValues[substItem] != 'object' && typeof substituteValues[substItem] != 'function' )
        {
            replaceString = replaceString.replace( substItem, substituteValues[substItem] );
        }
    }

    return replaceString;
}

/*!
  Reposition a toplevel menu according to the mouse position.
  Makes sure the complete menu is visible in the viewing area.
  The menu is repositioned like most OS's do if it doesn't fit at the normal position: is moved
  to the opposite side of the mouse pointer/menu.
*/
function _moveTopLevelOnScreen( menuID, mousePos )
{
    var menuElement = document.getElementById( menuID ), screenData = _getScreenProperties();
    var newX = 0; var newY = 0;

    // compensate if we are below the screen
    if ( (screenData.ScrollY + screenData.Height) < ( mousePos.y + EZPOPMENU_OFFSET + menuElement.offsetHeight ) )
        newY = mousePos.y - EZPOPMENU_OFFSET - menuElement.offsetHeight;
    // compensate if we are above the top of the screen
    else if ( screenData.ScrollY > EZPOPMENU_OFFSET + mousePos.y )
        newY = screenData.ScrollY;
    else
        newY = mousePos.y + EZPOPMENU_OFFSET;
    // A menu opened upwards stays inside the window; the stylesheet caps its height to the window.
    if ( newY < screenData.ScrollY )
        newY = screenData.ScrollY;

    // compensate if we are to the right of the screen
    if ( (screenData.ScrollX + screenData.Width) < ( mousePos.x + EZPOPMENU_OFFSET + menuElement.offsetWidth ) )
        newX = mousePos.x - EZPOPMENU_OFFSET - menuElement.offsetWidth;
    // compensate if we are to the left
    else if ( screenData.ScrollX > EZPOPMENU_OFFSET + mousePos.x )
        newX = screenData.ScrollX;
    else
        newX = mousePos.x + EZPOPMENU_OFFSET;
    // reposition menu
    menuElement.style.left = newX + "px";
    menuElement.style.top = newY + "px";
}

function _mouseHandler( e )
{
    if ( !e )e = window.event;

    if ( e.pageX || e.pageY )//DOM
        return { 'x': e.pageX, 'y': e.pageY };
    else if ( e.clientX || e.clientY ) // IE needs special treatment
        return { 'x': e.clientX + document.documentElement.scrollLeft, 'y': e.clientY + document.documentElement.scrollTop };
    return { 'x': 0, 'y': 0 };
}


/*!
  Reposition a toplevel menu according to parent window.
  Makes sure the complete menu is visible in the viewing area.
  The menu is repositioned like most OS's do if it doesn't fit at the normal position: is moved
  to the opposite side of the mouse pointer/menu.
  TODO: If you have several submenus we should store any side adjustment in order to
  always adjust to the same side
*/
function _moveSubLevelOnScreen( menuID, alignItem )
{
    var menuElement = document.getElementById( menuID ), screenData = _getScreenProperties();
    var newX = 0; var newY = 0;

    var alignElement = document.getElementById( alignItem );
    var parentElement = document.getElementById( VisibleMenus[menuArray[menuID]['depth'] - 1] );

    if ( alignElement && parentElement )
    {
        // Next to the parent menu: its width, not the submenu's (they differ now that menus size to their items).
        newX = parseInt( parentElement.style.left ) + parentElement.offsetWidth - EZPOPMENU_SUBOFFSET;
        newY = parseInt( parentElement.style.top ) + alignElement.offsetTop + EZPOPMENU_SUBTOPOFFSET;
    }
    // compensate if we are below the screen
    if ( ( screenData.ScrollY + screenData.Height ) < ( newY + menuElement.offsetHeight ) )
        newY = screenData.ScrollY + screenData.Height - menuElement.offsetHeight;
    // compensate if above the screen
    else if ( screenData.ScrollY > newY )
        newY = screenData.ScrollY;

    // compensate if we are to the right of the screen
    // (without its parent menu the submenu stays where it is: there is no side to move it to)
    if ( parentElement && ( screenData.ScrollX + screenData.Width ) < ( newX + menuElement.offsetWidth ) )
    {
        newX = parseInt( parentElement.style.left ) + EZPOPMENU_SUBOFFSET - menuElement.offsetWidth;
    }
    // to the left is impossible

    // reposition menu
    menuElement.style.left = newX + "px";
    menuElement.style.top = newY + "px";
}

/*!
  Submit the form with id formID. All fields of type hidden, and the form's action, will have the texts %nodeID%
  and %objectID% replaced with the corresponding real values.
*/
function _submitForm( formID, customSubstitute )
{
    var formElement = document.getElementById( formID );
    if ( formElement )
    {
        var action = formElement.getAttribute( 'action' );
        if ( action && action.indexOf( '%' ) !== -1 )
        {
            // Numbers in a path (%nodeID%): each value is URL-encoded
            var encoded = {};
            for ( var key in CurrentSubstituteValues )
            {
                if ( typeof CurrentSubstituteValues[key] != 'object' && typeof CurrentSubstituteValues[key] != 'function' )
                    encoded[key] = encodeURIComponent( String( CurrentSubstituteValues[key] ) );
            }
            formElement.setAttribute( 'action', _fillString( action, encoded, false ) );
        }
        // for all children do replacement
        var children = formElement.childNodes,
            origValue = '', resetInputs = [];
        for ( var i = 0, l = children.length; i < l; i++)
        {
            if ( children[i].type == 'hidden' )
            {
                for ( var substItem in CurrentSubstituteValues )
                {
                    origValue = children[i].value;
                    children[i].value = children[i].value.replace( substItem, CurrentSubstituteValues[substItem] );
                    if ( customSubstitute )
                    {
                        for ( var j = 0, jl = customSubstitute.length; j < jl; j += 2 )
                        {
                            children[i].value = children[i].value.replace( '%'+customSubstitute[j]+'%', customSubstitute[j+1] );
                        }
                    }
                    if ( origValue != children[i].value )
                    {
                        resetInputs.push(
                            { 'input': children[i], 'originalValue': origValue }
                        );
                    }
                }
            }
        }
        formElement.submit();

        // restoring the form so that it gets correctly filled
        // if another click from the user occurs
        if ( action ) formElement.setAttribute( 'action', action );
        for( var i = 0; i != resetInputs.length; i++ )
        {
            resetInputs[i].input.value = resetInputs[i].originalValue;
        }
    }
}

/*!
  Hide menu id and all menu's beneath it
 */
function _hide( id )
{
    var level = menuArray[id]['depth'];
    _hideHigher( level - 1 );
}

/*!
  Hide all menus
*/
function _hideAll()
{
    _hideHigher( -1 );
    if ( MenuOpener ) MenuOpener.setAttribute( 'aria-expanded', 'false' );
}

/*
 * Hide all menus above 'level'
 */
function _hideHigher( level, el )
{
    for ( var i = level + 1, l = VisibleMenus.length; i < l && VisibleMenus[i] != 'none' ; i++ )
    {
        el = document.getElementById( VisibleMenus[i] );
        if ( el ) el.style.display = 'none';
        if ( SubMenuOpeners[VisibleMenus[i]] ) SubMenuOpeners[VisibleMenus[i]].setAttribute( 'aria-expanded', 'false' );
        VisibleMenus[i] = 'none';
    }
}

/*
 * This method should be called by mouseover for all items in the implementation.
 * The method makes sure that no menus on a lower level are shown.
 */
function _mouseOver( id )
{
    _hideHigher( menuArray[id]['depth'] );
}

/*
 * Helper function to create an associative object. Every two items will be paired as a key and a value.
 */
function _createAArray( flat )
{
    var resultArray = {};

    if ( flat.length % 2 != 0 ) return resultArray;

    for ( var i = 0, l = flat.length; i < l; i += 2 )
    {
        resultArray[flat[i]] = flat[i+1];
    }

    return resultArray;
}

/*
 * Perform subsitution in 'href' and redirect browser to
 * newly created link.
 */
function _SubstituteAndRedirect( href )
{
    // loop though substitute values and substitute for each of them
    for ( var substItem in CurrentSubstituteValues )
    {
        href = href.replace( substItem, CurrentSubstituteValues[substItem] );
    }
    location.href = href;
}

function _getScreenProperties()
{
  // client width and height
  var result = { 'ScrollX': 0, 'ScrollY': 0, 'Height': 0, 'Width': 0 };
  if ( typeof( window.innerWidth ) == 'number' )// all but IE
  {
    result.Width = window.innerWidth;
    result.Height = window.innerHeight;
  }
  else if ( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) )// IE 6
  {
    result.Width = document.documentElement.clientWidth;
    result.Height = document.documentElement.clientHeight;
  }
  else if ( document.body && ( document.body.clientWidth || document.body.clientHeight ) )// IE 4
  {
    result.Width = document.body.clientWidth;
    result.Height = document.body.clientHeight;
  }

  if ( document.body && ( document.body.scrollLeft || document.body.scrollTop ) )// DOM
  {
    result.ScrollY = document.body.scrollTop;
    result.ScrollX = document.body.scrollLeft;
  }
  else if ( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) )// IE6
  {
    result.ScrollY = document.documentElement.scrollTop;
    result.ScrollX = document.documentElement.scrollLeft;
  }
  else if ( typeof( window.pageYOffset ) == 'number' )// Netscape compliant
  {
    result.ScrollY = window.pageYOffset;
    result.ScrollX = window.pageXOffset;
  }

  return result;
}

/*
 * Keyboard and ARIA layer (WAI-ARIA menu pattern). The menus stay plain markup with inline mouse handlers, as
 * extensions and older templates write them; the roles are added when a menu is shown, so every menu gets them.
 *
 * - The trigger gets aria-haspopup="menu" and aria-expanded; a menu opened from the keyboard (Enter or Space on
 *   the trigger, Shift+F10 or the context menu key on a tree item or table row) opens under the trigger and its
 *   first item takes the focus.
 * - Arrow Down/Up, Home and End move between the items, Arrow Right, Enter or Space open a submenu, Arrow Left
 *   closes it, Enter or Space activate an item, Escape closes the menu and returns the focus to the trigger, Tab
 *   closes the menu.
 */
function _isKeyboardEvent( event )
{
    if ( !event ) return true;
    if ( event.type && event.type.indexOf( 'key' ) === 0 ) return true;
    // A click made with Enter or Space, or by element.click() (Shift+F10), counts no clicks: detail 0. Its position
    // in the window is 0, but pageX/pageY are not: they add the scroll offset, so on a scrolled page they would
    // make it look like a mouse click and open the menu in the window's top left corner.
    if ( event.type === 'click' && typeof event.detail === 'number' ) return event.detail === 0;
    return !event.clientX && !event.clientY;
}

function _setOpener( event )
{
    var el = event && ( event.currentTarget || event.target || event.srcElement );
    if ( !el || el.nodeType !== 1 || el === window || el === document ) el = document.activeElement;
    if ( el && el.closest ) el = el.closest( 'a, button, [tabindex]' ) || el;
    if ( !el || el === document.body || ( el.closest && el.closest( 'div.popupmenu' ) ) )
    {
        MenuOpener = null;
        return;
    }
    MenuOpener = el;
    if ( !el.hasAttribute( 'aria-haspopup' ) ) el.setAttribute( 'aria-haspopup', 'menu' );
    el.setAttribute( 'aria-expanded', 'true' );
}

function _menuItems( menu )
{
    var items = [], links = menu ? menu.getElementsByTagName( 'a' ) : [];
    for ( var i = 0, l = links.length; i < l; i++ )
    {
        if ( links[i].offsetParent !== null || links[i].getClientRects().length ) items.push( links[i] );
    }
    return items;
}

function _decorate( menuID )
{
    var menu = document.getElementById( menuID );
    if ( !menu ) return;
    menu.setAttribute( 'role', 'menu' );

    var headerID = menuArray[menuID] && menuArray[menuID]['headerID'];
    if ( headerID && document.getElementById( headerID ) )
    {
        var header = document.getElementById( headerID );
        menu.setAttribute( 'aria-labelledby', headerID );
        // The title names the menu (aria-labelledby works on hidden elements) but is not a child of it.
        ( header.parentNode && header.parentNode !== menu ? header.parentNode : header ).setAttribute( 'aria-hidden', 'true' );
    }

    var links = menu.getElementsByTagName( 'a' );
    for ( var i = 0, l = links.length; i < l; i++ )
    {
        var a = links[i];
        a.setAttribute( 'role', 'menuitem' );
        a.setAttribute( 'tabindex', '-1' );
        if ( /(^|\s)more(\s|$)/.test( a.className ) )
        {
            a.setAttribute( 'aria-haspopup', 'menu' );
            if ( !a.hasAttribute( 'aria-expanded' ) ) a.setAttribute( 'aria-expanded', 'false' );
        }
        else
        {
            // An item that lost its submenu (a disabled "Create here") must not announce one any more.
            a.removeAttribute( 'aria-haspopup' );
            a.removeAttribute( 'aria-expanded' );
        }
        if ( /disabled/.test( a.className ) ) a.setAttribute( 'aria-disabled', 'true' );
        else a.removeAttribute( 'aria-disabled' );
    }
    var rules = menu.getElementsByTagName( 'hr' );
    for ( var j = 0, k = rules.length; j < k; j++ ) rules[j].setAttribute( 'role', 'separator' );
}

function _focusItem( menuID, index )
{
    var items = _menuItems( document.getElementById( menuID ) );
    if ( !items.length ) return;
    if ( index < 0 ) index = items.length - 1;
    items[index % items.length].focus();
}

function _visibleDepth()
{
    var depth = -1;
    for ( var i = 0, l = VisibleMenus.length; i < l && VisibleMenus[i] != 'none'; i++ ) depth = i;
    return depth;
}

function _menuOf( el )
{
    var menu = el && el.closest ? el.closest( 'div.popupmenu' ) : null;
    return menu && menuArray[menu.id] ? menu : null;
}

function _openSubmenu( item )
{
    var menu = _menuOf( item );
    if ( !menu ) return;
    var level = menuArray[menu.id]['depth'];
    // Calls ezpopmenu_showSubLevel(); the submenu may already be open from the mouse hovering the item.
    if ( typeof item.onmouseover === 'function' ) item.onmouseover.call( item );
    var sub = VisibleMenus[level + 1];
    if ( sub && sub != 'none' && SubMenuOpeners[sub] === item ) _focusItem( sub, 0 );
}

function _activate( item )
{
    if ( item.getAttribute( 'aria-disabled' ) === 'true' ) return;
    if ( item.getAttribute( 'aria-haspopup' ) === 'menu' ) _openSubmenu( item );
    else item.click();
}

function _keyHandler( e )
{
    var key = e.key, active = document.activeElement;

    // Shift+F10 or the context menu key on a tree item or table row opens that row's menu.
    if ( ( key === 'ContextMenu' || ( key === 'F10' && e.shiftKey ) ) && active && active.closest && !_menuOf( active ) )
    {
        // In a form field (the priority input of a row, a checkbox) the browser's own context menu stays.
        if ( /^(INPUT|TEXTAREA|SELECT)$/.test( active.tagName ) || active.isContentEditable ) return;
        var row = active.closest( 'li, tr' ), trigger = null;
        // The row's own menu button: in the content tree a node's li also holds the li of its loaded sub items,
        // so a button inside one of those belongs to another node.
        var candidates = row ? row.querySelectorAll( '[aria-haspopup="menu"]' ) : [];
        for ( var i = 0; i < candidates.length && !trigger; i++ )
        {
            if ( candidates[i].closest( 'li, tr' ) === row )
                trigger = candidates[i];
        }
        if ( trigger )
        {
            e.preventDefault();
            trigger.click();
        }
        return;
    }

    // Space on a link that is a menu button (role="button") opens its menu as Enter does.
    if ( ( key === ' ' || key === 'Spacebar' ) && active && active.tagName === 'A' && !_menuOf( active ) &&
         active.getAttribute( 'aria-haspopup' ) === 'menu' )
    {
        e.preventDefault();
        active.click();
        return;
    }

    var depth = _visibleDepth();
    if ( depth < 0 ) return;

    // A menu opened with the mouse: the keys reach it from its trigger or the page, never from a form field.
    if ( !_menuOf( active ) && active && active !== document.body && active !== MenuOpener && key !== 'Escape' && key !== 'Esc' ) return;

    var menu = _menuOf( active ) || document.getElementById( VisibleMenus[depth] );
    if ( !menu ) return;
    var items = _menuItems( menu ), index = items.indexOf( active ), level = menuArray[menu.id]['depth'];

    // Moving within a level closes a submenu the mouse opened from another item of it.
    if ( /^(ArrowDown|Down|ArrowUp|Up|Home|End)$/.test( key ) && _visibleDepth() > level ) _hideHigher( level );

    switch ( key )
    {
        case 'ArrowDown':
        case 'Down':
            e.preventDefault();
            ( items[index + 1] || items[0] ).focus();
            break;
        case 'ArrowUp':
        case 'Up':
            e.preventDefault();
            ( index > 0 ? items[index - 1] : items[items.length - 1] ).focus();
            break;
        case 'Home':
            e.preventDefault();
            if ( items.length ) items[0].focus();
            break;
        case 'End':
            e.preventDefault();
            if ( items.length ) items[items.length - 1].focus();
            break;
        case 'ArrowRight':
        case 'Right':
            if ( index >= 0 && items[index].getAttribute( 'aria-haspopup' ) === 'menu' )
            {
                e.preventDefault();
                _openSubmenu( items[index] );
            }
            break;
        case 'ArrowLeft':
        case 'Left':
        case 'Escape':
        case 'Esc':
            if ( level > 0 )
            {
                e.preventDefault();
                var opener = SubMenuOpeners[menu.id];
                _hideHigher( level - 1 );
                if ( opener ) opener.focus();
            }
            else if ( key === 'Escape' || key === 'Esc' )
            {
                e.preventDefault();
                var trigger = MenuOpener;
                _hideAll();
                // Back to the button only from the menu or the button itself, never out of a form field.
                if ( trigger && trigger.focus && ( index >= 0 || active === trigger || active === document.body ) ) trigger.focus();
            }
            break;
        case 'Enter':
        case ' ':
        case 'Spacebar':
            if ( index >= 0 )
            {
                // Also for Enter: an item without href (the siteaccess override items) gets no click from the browser.
                e.preventDefault();
                _activate( items[index] );
            }
            break;
        default:
            // Typeahead: a printable character moves to the next item whose label starts with it.
            if ( index >= 0 && key && key.length === 1 && key !== ' ' && !e.ctrlKey && !e.metaKey && !e.altKey )
            {
                var ch = key.toLowerCase();
                for ( var n = 1; n <= items.length; n++ )
                {
                    var cand = items[( index + n ) % items.length];
                    if ( ( cand.textContent || '' ).replace( /^\s+/, '' ).toLowerCase().indexOf( ch ) === 0 )
                    {
                        e.preventDefault();
                        cand.focus();
                        break;
                    }
                }
            }
            break;
        case 'Tab':
            if ( index >= 0 )
            {
                var back = MenuOpener;
                _hideAll();
                if ( back && back.focus ) back.focus();
            }
            break;
    }
}

if ( document.addEventListener ) document.addEventListener( 'keydown', _keyHandler );

// Register some functions on global scope
window.ezpopup_SubstituteAndRedirect = _SubstituteAndRedirect;
window.ez_createAArray = _createAArray;
window.ezpopmenu_mouseOver = _mouseOver;
window.ezpopmenu_showTopLevel = _showTopLevel;
window.ezpopmenu_hideAll = _hideAll;
window.ezpopmenu_hide = _hide;
window.ezpopmenu_submitForm = _submitForm;
window.ezpopmenu_showSubLevel = _showSubLevel;

if ( window.menuArray === undefined )
{
    window.menuArray = {};
}

})();// Scope end
