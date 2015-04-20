# Module Keyword Thelia 2

This module is used to associate category, product, content or folders to one or more keywords.
This allows you for example to display items based on a particular keyword or structure your page with a tag system.

## How to install

This module must be into your ```modules/``` directory (thelia/local/modules/).

You can download the .zip file of this module or create a git submodule into your project like this :

```
cd /path-to-thelia
git submodule add https://github.com/thelia-modules/Keyword.git local/modules/Keyword
```

Next, go to your Thelia admin panel for module activation.

## How to update

__Before update, save your database.__

If your module version is 2.3.x and you want to update to the 2.4.x version, you have to use the `keyword:update` command.
__This command is only available from the v2.4.4.__

* After saving your database, deactivate Keyword module
* Replace all files of `local/modules/Keyword` by new files
* Activate new Keyword module : all tables (excepted keyword_group_associated_keyword) of keyword module will be truncated, if this is not the case, empty them manually
* Reinsert the Keyword module table data via phpmyadmin (datas of content_associated_keyword, folder_associated_keyword, category_associated_keyword, product_associated_keyword, keyword_group, keyword_group_i18n, keyword and keyword_i18n)
* Run the Keyword module command : `keyword:update`

Old associations will be updated and obsolete _keyword_group_associated_keyword_ table will be dropped.

## How to use

You can manage your keywords on the configuration view of the module with the "configure" button on the modules list.

To associate one or more keywords to a content or a folder, go to tab "Modules" of content or folder editing view.

This module allow you to use 6 new loops : keyword_category, keyword_product, keyword_content, keyword_folder, keyword_group and keyword.

Here is an example of using each :

__Use the keyword_category loop (list of categories related to the "my_keyword" keyword)__
```html
{loop name="categories" type="keyword_category" keyword="my_keyword" association_order="manual_reverse"}
    ...
{/loop}
```

__Use the keyword_product loop (list of products related to the "my_keyword" keyword)__
```html
{loop name="products" type="keyword_product" keyword="my_keyword" association_order="manual_reverse"}
    ...
{/loop}
```

__Use the keyword_content loop (list of contents related to the "my_keyword" keyword)__
```html
{loop name="contents" type="keyword_content" keyword="my_keyword" folder="1" association_order="manual_reverse"}
    ...
{/loop}
```

__Use the keyword_folder loop (list of folders related to the "my_keyword" keyword)__
```html
{loop name="folders" type="keyword_folder" keyword="my_keyword"}
    ...
{/loop}
```

__Use the keyword_category loop (list of categories related to the "my_keyword" keyword)__
```html
{loop name="categories" type="keyword_category" keyword="my_keyword"}
    ...
{/loop}
```

__Use the keyword_product loop (list of products related to the "my_keyword" keyword)__
```html
{loop name="products" type="keyword_product" keyword="my_keyword"}
    ...
{/loop}
```

__Use the keyword loop (list all keywords that are visible)__
```html
{loop name="keyword_list" type="keyword" visible="*" order="manual" backend_context="1" lang=$lang_id}
    ...
{/loop}
```

__Use the keyword group loop (list all keyword groups that are visible)__
```html
{loop name="keyword_group_list" type="keyword_group" visible="*" order="manual" backend_context="1" lang=$lang_id}
    ...
{/loop}
```
---
*****

You can also check the association between a keyword and a category, a product, a folder or a content by using Smarty extension available in the latest version of this plugin :

__Check if thelia object is associated with keyword "my_keyword"__
```html
{category_has_keyword category_id=$ID keyword_code="my_keyword"} {* return true/false if relation exist or not *}
{product_has_keyword product_id=$ID keyword_code="my_keyword"}
{folder_has_keyword folder_id=$ID keyword_code="my_keyword"}
{content_has_keyword content_id=$ID keyword_code="my_keyword"}
```