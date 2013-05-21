# Click


A simple ExpressionEngine 2.x Fieldtype for creating links using [Markdown](http://daringfireball.net/projects/markdown/ "Markdown") formatting.

* [Full Documentation](https://github.com/johndwells/Click)
* [On @devot-ee](http://devot-ee.com/add-ons/click)
* [Forum Support](http://devot-ee.com/add-ons/support/click/)

# Installation

1. Copy the contents of /system/expressionengine/third_party/click into your own system/expressionengine/third_party folder.
2. Log into the CP and go to Add-Ons > Fieldtypes and install the "Click" fieldtype

# Custom Field Setup

When creating a **Click** custom field, the following option is available:

#### Show hint text as placeholder? (checkbox)

When ticked, this will use the HTML5 `placeholder` attribute to show sample text for an empty field, demonstrating the acceptable format for a Markdown-formatted link:

	[link text](url "optional title")

# Template Tags

### `{custom_field_name}`

This returns a formatted &lt;A&gt; link tag.

## Tag Modifiers

The following tag modifiers are used to retrieve pieces of the link:

### `{custom_field_name:url}`

Returns only the URL.

### `{custom_field_name:text}`

Returns only the readable Text part of the &lt;A&gt; link.

### `{custom_field_name:title}`

Returns only the Alternative title part of the &lt;A&gt; link.

### `{custom_field_name:original}`

Returns the original, unformatted contents of the custom field value.

# Feature Roadmap

 * Low Variables support for field modifiers?
 * option to auto encode mailto: addresses
 * Allow multiple
 * JS validation?
 * Confirm Safecracker support
 * Confirm MSM support

