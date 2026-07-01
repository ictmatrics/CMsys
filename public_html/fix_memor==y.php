<?php
$files = [
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Lemars/archive.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Lemars/footer.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Indiro/archive.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Bodyshape/page.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Bodyshape/home.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Bodyshape/header.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Lemars/page.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Bodyshape/footer.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Lemars/home.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Lemars/header.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Bodyshape/archive.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Indiro/footer.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Indiro/page.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Indiro/home.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Indiro/header.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Bodyshape/post.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Lemars/post.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Indiro/post.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Lemars/sidebar.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Bodyshape/sidebar.php',
    'e:/Webserver/cmsys.wis/APP/Views/Themes/Indiro/sidebar.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace('get_defined_vars()', '$data', $content);
        file_put_contents($file, $content);
    }
}
echo "Done replacing.";
