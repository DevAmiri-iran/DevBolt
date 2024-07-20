<?php

namespace App;

use eftec\bladeone\BladeOne;

class Blade extends BladeOne
{
    public function getCompiledFile($templateName = ''): string
    {
        $templateName = (empty($templateName)) ? $this->fileName : $templateName;
        $fullPath = $this->getTemplateFile($templateName);
        if ($fullPath == '') {
            throw new \RuntimeException('Template not found: ' .($this->mode == self::MODE_DEBUG ? $this->templatePath[0].'/'.$templateName : $templateName));
        }
        $style = $this->compileTypeFileName;
        if ($style === 'auto') {
            $style = 'sha1';
        }
        return $this->compiledPath . '/' . sha1($fullPath) . $this->compileExtension;
    }
}