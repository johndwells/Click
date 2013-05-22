# Click


A simple ExpressionEngine 2.x Fieldtype for creating links using [Markdown](http://daringfireball.net/projects/markdown/ "Markdown") formatting.

* [Full Documentation](https://github.com/johndwells/Click)

---

# Installation

1. Copy the contents of /system/expressionengine/third_party/click into your own system/expressionengine/third_party folder.
2. Log into the CP and go to Add-Ons > Fieldtypes and install the "Click" fieldtype


---

# Custom Field Setup

When creating a Click custom field, the following options are available:

#### Show hint text as placeholder? (checkbox)

When ticked, this will use the HTML5 `placeholder` attribute to show sample text for an empty field, demonstrating the acceptable format for a Markdown-formatted link:

	[link text](url "optional title")


#### Allow multiple links? (checkbox)

When ticked, the custom field will allow for multiple links, each separated by a new line.

---

# Field Tags

The following tags are available for your Click field within `{exp:channel:entries}` tag pairs.

## Single Tag

Return the field as a fully formatted &lt;A&gt; link tag:

	{my_click_field}
	
	// renders something like:
	<a href="http:domain.com" title="Alternative Title">Link Text</a>

**Note: In the case where multiple links are allowed for the field, the Single Tag will only return the first link.**

## Tag Pair

Click's tag pair is used to iterate & display multilpe links, in the case where multiple links are enabled.

	<ul class="nav">
		{my_click_field prefix="click:"}
			<li class="{switch='odd|even'}"><a href="{click:url}" title="{click:title}">{click:text}</a></li>
		{/my_click_field}
	</ul>

### Tag Parameters

#### `backspace=`

Strip the last X characters from the tag output.

	{my_click_field backspace="2"}{click}, {/my_click_field}

#### `var_prefix=`

Specify Click to only parse variables which have the specified prefix. Useful in case of naming conflicts between nested fields.

	{my_click_field prefix="click"}{click:url}, {/my_click_field}

#### `limit=`

Specify the maximum number of links to return.

	{my_click_field limit="2"}{click}, {/my_click_field}

### Variable Tags

Within the Tag Pair, the following variable tags are available (note these may change if you have specified a `var_prefix=`).

#### `{click}`

Returns the fully rendered &lt;A&gt; link.

	{my_click_field}{click}, {/my_click_field}

#### `{text}`

Returns the readable Text portion of the &lt;A&gt; link.

	{my_click_field}{text}, {/my_click_field}

#### `{title}`

Returns the Alternative Title portion of the &lt;A&gt; link.

	{my_click_field}{title}, {/my_click_field}

#### `{url}`

Returns the URL portion of the &lt;A&gt; link.

	{my_click_field}{url}, {/my_click_field}

## :total Tag

Returns the total number of links.

	{my_click_field:total}

## :first Tag

In the case where multiple links are allowed for a field, this will return the first link tag:

	{my_click_field:first}

## :last Tag

In the case where multiple links are allowed for a field, this will return the last link tag:

	{my_click_field:last}

## :ul Tag

Return an automatically-generated unordered list of links.

	{my_click_field:ul}

## :ol Tag

Return an automatically-generated ordered list of links.

	{my_click_field:ol}

## :url Tag

Returns only the URL of the &lt;A&gt; link. In the case where multiple links are allowed, will only return the first.

	{my_click_field:url}

## :text Tag

Returns only the readable Text part of the &lt;A&gt; link. In the case where multiple links are allowed, will only return the first.

	{my_click_field:text}

## :title Tag

Returns the Alternative title part of the &lt;A&gt; link. In the case where multiple links are allowed, will only return the first.

	{my_click_field:title}

## :original Tag

Returns the original, unformatted contents of the entire custom field.

	{my_click_field:original}

---

# Feature Roadmap

 * Low Variables support for field modifiers?
 * option to auto encode mailto: addresses
 * JS validation?
 * Confirm Safecracker support
 * Confirm MSM support

