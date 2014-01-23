# Module Keyword Thelia 2

This module is used to associate content or folders to one or more keywords.
This allows you for example to display items based on a particular keyword or structure your page with a tag system.

## How to install

This module must be into your ```modules/``` directory (thelia/local/modules/).

You can download the .zip file of this module or create a git submodule into your project like this :

```
cd /path-to-thelia
git submodule add https://github.com/thelia-modules/keyword.git local/modules/Keyword
```

Next, go to your Thelia admin panel for module activation.

## How to use

You can manage your keywords on the configuration view of the module with the "configure" button on the modules list.

To associate one or more keywords to a content or a folder, go to tab "Modules" of content or folder editing view.

This module allow you to use 3 new loops : keyword_content, keyword_folder and keyword.

Here is an example of using each :

__Use the keyword_content loop (list of contents related to the "my_keyword" keyword)__
```html
{loop name="contents" type="keyword_content" keyword="my_keyword" folder="1" order="manual_reverse"}
    ...
{/loop}
```

__Use the keyword_folder loop (list of folders related to the "my_keyword" keyword)__
```html
{loop name="folders" type="keyword_folder" keyword="my_keyword"}
    ...
{/loop}
```

__Use the keyword loop (list all keywords that are visible)__
```html
{loop name="keyword_list" type="keyword" visible="*" order="manual" backend_context="1" lang=$lang_id}
    ...
{/loop}
```
