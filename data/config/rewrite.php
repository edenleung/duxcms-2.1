<?php
return array(
    /* URL规则 */
    'REWRITE_RULE' =>array(
        'list/<class_id>.html' => 'article/Category/index',
        'page/<class_id>.html' => 'page/Category/index',
        'article/<content_id>.html' => 'article/Content/index',
        'form-<name>/<id>.html' => 'duxcms/Form/info',
        'form-<name>.html' => 'duxcms/Form/index',
        'tags-list/<name>.html' => 'duxcms/Tags/index',
        'tags/<name>.html' => 'duxcms/TagsContent/index',

        '<lang>/list/<class_id>.html' => 'article/Category/index',
        '<lang>/page/<class_id>.html' => 'page/Category/index',
        '<lang>/article/<content_id>.html' => 'article/Content/index',
        '<lang>/form-<name>/<id>.html' => 'duxcms/Form/info',
        '<lang>/form-<name>.html' => 'duxcms/Form/index',
        '<lang>/tags-list/<name>.html' => 'duxcms/Tags/index',
        '<lang>/tags/<name>.html' => 'duxcms/TagsContent/index',
        '<lang>' => 'home/index/index',
    ),
);
