# Click


A simple ExpressionEngine 2.x Fieldtype for creating links using [Markdown](http://daringfireball.net/projects/markdown/ "Markdown") formatting.

* Version 0.9.0 (beta)
* [Full Documentation](https://github.com/johndwells/Click)

## At a glance

* Hyperlinks without HTML
* Single or multiple links per field
* Full control over output
* Low Variables compatible
* Matrix compatible

---

# Introduction

**Click** was built to easily make *one or more* arbitrary hyperlinks, without needing to know HTML, and with full control over markup in your templates.

Click leverages [Markdown](http://daringfireball.net/projects/markdown/ "Markdown") as the formatting syntax to write your links. So it can turn this:

	[Google it!](http://google.com "Google")
	[Bing it!](http://bing.com "Bing")
	[Ask it!](http://ask.com "Ask")

Into this:

	<ul>
		<li><a href="http://google.com" title="Google">Google it!</a></li>
		<li><a href="http://bing.com" title="Bing">Bing it!</a></li>
		<li><a href="http://ask.com" title="Ask">Ask it!</a></li>
	</ul>

Simply by writing this:

	<ul>
		{my_click_field}
			<li>{click}</li>
		{/my_click_field}
	</ul>

Or this:

	<ul>
		{my_click_field}
			<li><a href="{url}" title="{title}">{text}</a></li>
		{/my_click_field}
	</ul>

Or actually, even this:

	{my_click_field:ul}

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

## Tag Pair

Click's tag pair is used to iterate & display multiple links, in the case where multiple links are enabled.

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

## Single Tags

### Primary Tag

When Click is configured to only allow a single link, returning the fully formatted &lt;A&gt; link is as simple as:

	{my_click_field}
	
	// renders something like:
	<a href="http:domain.com" title="Alternative Title">Link Text</a>

**Note: In the case where multiple links are allowed for the field, the Primary Tag will only return the first link.**

### :total Tag

Returns the total number of links.

	{my_click_field:total}

### :first Tag

In the case where multiple links are allowed for a field, this will return the first link tag:

	{my_click_field:first}

### :last Tag

In the case where multiple links are allowed for a field, this will return the last link tag:

	{my_click_field:last}

### :ul Tag

Return an automatically-generated unordered list of links.

	{my_click_field:ul}

### :ol Tag

Return an automatically-generated ordered list of links.

	{my_click_field:ol}

### :url Tag

Returns only the URL of the &lt;A&gt; link. In the case where multiple links are allowed, will only return the first.

	{my_click_field:url}

### :text Tag

Returns only the readable Text part of the &lt;A&gt; link. In the case where multiple links are allowed, will only return the first.

	{my_click_field:text}

### :title Tag

Returns the Alternative title part of the &lt;A&gt; link. In the case where multiple links are allowed, will only return the first.

	{my_click_field:title}

### :original Tag

Returns the original, unformatted contents of the entire custom field.

	{my_click_field:original}

---

# Feature Roadmap (Maybe?)

 * option to auto encode mailto: addresses
 * JS validation
 * Confirm Safecracker support
 * Confirm MSM support

