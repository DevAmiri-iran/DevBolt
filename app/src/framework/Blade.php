<?php

namespace App;

use eftec\bladeone\BladeOne;
use RuntimeException;

class Blade extends BladeOne
{

    /**
     * Get the full path of the compiled file.
     *
     * @param string $templateName
     * @return string
     */
    public function getCompiledFile($templateName = ''): string
    {
        $templateName = (empty($templateName)) ? $this->fileName : $templateName;
        $fullPath = $this->getTemplateFile($templateName);
        if ($fullPath == '') {
            throw new RuntimeException('Template not found: ' .($this->mode == self::MODE_DEBUG ? $this->templatePath[0].'/'.$templateName : $templateName));
        }
        $style = $this->compileTypeFileName;
        if ($style === 'auto') {
            $style = 'sha1';
        }
        $hash = $style === 'md5' ? md5($fullPath) : sha1($fullPath);
        return $this->compiledPath . '/' . $hash . $this->compileExtension;
    }
}