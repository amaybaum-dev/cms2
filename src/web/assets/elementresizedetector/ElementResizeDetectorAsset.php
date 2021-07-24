<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\web\assets\elementresizedetector;

use yii\web\AssetBundle;

/**
 * ElementResizeDetector asset bundle.
 */
class ElementResizeDetectorAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->sourcePath = '@lib/element-resize-detector';

        $this->js = [
            'element-resize-detector.js',
        ];

        parent::init();
    }
}
