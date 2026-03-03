<?php

namespace App\Rise\Core\Helpers\Files\Searching;

class SearchFile {
    private string $path;

    public function __construct(string $path) {
        $this->path = $path;
    } 

    public function search() {
        $currentPath = realpath(__DIR__);

        $AppDir;
        
        while ($currentPath !== DIRECTORY_SEPARATOR) {
            $possible = $currentPath . DIRECTORY_SEPARATOR . 'App';
            if (is_dir($possible)) {
                $AppDir = $possible;
                break;
            }

            $parent = dirname($currentPath);
            if ($parent === $currentPath) break;
            $currentPath = $parent;
        }

        if ($AppDir) {
            return $AppDir . $this->path;
        } else {
            return null;
        }
    }
}