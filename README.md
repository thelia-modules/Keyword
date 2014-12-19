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

## How to use

You can manage your keywords on the configuration view of the module with the "configure" button on the modules list.

To associate one or more keywords to a content or a folder, go to tab "Modules" of content or folder editing view.

This module allow you to use **6 new loops** : 
*keyword_category*, *keyword_product*, *keyword_content*, *keyword_folder*, *keyword_group* and *keyword*.
And **4 Smarty functions** : 
*category_has_keyword*, *folder_has_keyword*, *product_has_keyword*, *content_has_keyword*

## Loops

#### keyword 
**Input**
| Argument | Value | Default value |
|--------- | ----|----|
|id|(int) keyword id||
|visible|(int) 1 or 0|1|
|keyword|(string) The keyword code||
|folder|(int) ID or list of ID's of folder||
|content|(int) ID or list of ID's of content||
|category|(int) ID or list of ID's of category||
|product|(int) ID or list of ID's of product||
|order|(string) alpha, alpha-reverse, manual, manual_reverse, random, given_id|alpha|
|keyword_group|(int) ID or list of ID's of keyword group||

**Output**
| Variable |
|--------- |
|$ID|
|$KEYWORD_GROUP_ID|
|$IS_TRANSLATED|
|$LOCALE|
|$TITLE|
|$CODE|
|$CHAPO|
|$DESCRIPTION|
|$POSTSCRIPTUM|
|$POSITION|
|$VISIBLE|
|$CONTENTS_ASSOCIATION|
|$FOLDERS_ASSOCIATION|
|$CATEGORIES_ASSOCIATION|
|$PRODUCTS_ASSOCIATION|
|$HAS_PREVIOUS|
|$HAS_NEXT|
|$PREVIOUS|
|$NEXT|

#### keyword_group 
**Input**
| Argument | Value | Default value |
|--------- | ----|----|
|id|(int) keyword group id||
|visible|(int) 1 or 0|1|
|keyword|(string) The keyword group code||
|order|(string) alpha, alpha-reverse, manual, manual_reverse, random, given_id|alpha|

**Output**
| Variable |
|--------- |
|$ID|
|$IS_TRANSLATED|
|$LOCALE|
|$TITLE|
|$CODE|
|$CHAPO|
|$DESCRIPTION|
|$POSTSCRIPTUM|
|$POSITION|
|$VISIBLE|
|$HAS_PREVIOUS|
|$HAS_NEXT|
|$PREVIOUS|
|$NEXT|

#### keyword_category, keyword_product, keyword_folder & keyword_content
**Input**
| Argument | Value | Default value |
|--------- | ----|----|
|keyword|(string) The keyword code||
|association\_order|(string) manual, manual_reverse, random|manual|

**Output**
Same as default loops category, product, folder & content with :
| Variable |
|--------- |
|$FOLDER_POSITION|
|$CONTENT_POSITION|
|$CATEGORY_POSITION|
|$PRODUCT_POSITION|

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