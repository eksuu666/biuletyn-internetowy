<?php

$profile = ['PROFILE_EDIT', 'PROFILE_CHANGE_PASSWORD', 'PROFILE_CHANGE_EMAIL', 'PROFILE_SOCIAL_NETWORKS'];
$reporter = array_merge($profile, ['ARTICLE_INDEX', 'ARTICLE_STORE', 'ARTICLE_UPDATE', 'ARTICLE_DESTROY', 'IMAGE_INDEX_OWN', 'IMAGE_UPDATE_OWN', 'IMAGE_DESTROY_OWN', 'VIDEO_STORE', 'VIDEO_INDEX_OWN', 'VIDEO_UPDATE_OWN', 'VIDEO_DESTROY_OWN']);
$secretary = array_merge($reporter, ['ARTICLE_CORRECT', 'COMMENT_INDEX', 'COMMENT_SHOW', 'COMMENT_APPROVE', 'COMMENT_REJECT', 'CATEGORY_INDEX', 'TAG_INDEX']);
$chief = array_merge($secretary, ['ARTICLE_PUBLISH', 'ARTICLE_EDITOR_CHOICE', 'ARTICLE_HOTNESS', 'CATEGORY_STORE', 'CATEGORY_UPDATE', 'TAG_STORE', 'TAG_UPDATE', 'MENU_INDEX', 'MENU_STORE', 'MENU_UPDATE', 'SEO_MANAGEMENT']);
$roles = [
    ['Admin', 'Administrator', ['SUPER_ADMIN']],
    ['Editor', 'Redaktor', $reporter],
    ['Author', 'Redaktor', $reporter],
    ['Secretary', 'Sekretarz redakcji', $secretary],
    ['EditorInChief', 'Redaktor naczelny', array_diff($chief, ['ARTICLE_CORRECT'])],
    ['Subscriber', 'Czytelnik', $profile],
];

return array_map(static fn (array $role) => [
    'name' => $role[0],
    'local_name' => $role[1],
    'permissions' => array_values(array_map(static fn ($key) => config('permissions_list.'.$key), $role[2])),
], $roles);
