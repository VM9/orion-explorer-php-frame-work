<html>
    <head>
        <base href="/example/"/>
    </head>
    <body>
        <ul>
            <?php
            $files = new \DirectoryIterator(dirname(__FILE__));

            $exclude = ['autoloader.php','index.php'];
            foreach ($files as $file) {
                if ($file->isFile() && $file->getExtension() == "php" && !in_array($file->getFileName(), $exclude)) {
                    echo  "<li><a href='{$file->getFileName()}'>",$file->getFileName(),"</a></li>";
                }
            }
            ?></ul>
    </body>
</html>