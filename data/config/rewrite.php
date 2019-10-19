<?php

return [
    /* URL规则 */
    'REWRITE_RULE' => [
        'list/<class_id>.html'      => 'article/Category/index',
        'page/<class_id>.html'      => 'page/Category/index',
        'article/<content_id>.html' => 'article/Content/index',
        'form-<name>/<id>.html'     => 'duxcms/Form/info',
        'form-<name>.html'          => 'duxcms/Form/index',
        'tags-list/<name>.html'     => 'duxcms/Tags/index',
        'tags/<name>.html'          => 'duxcms/TagsContent/index',

        /*
            个性化自定义
            // 列表页
            '<urlname>/index.html'      => 'article/Category/index',
            // 列表详情页
            '<class_urlname>/<urltitle>.html' => 'article/Content/index',
            // 单页面
            '<urlname>'      => 'page/Category/index',
        */
    ],
];
