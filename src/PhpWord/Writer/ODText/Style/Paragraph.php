<?php
/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 *
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @see         https://github.com/PHPOffice/PHPWord
 * @copyright   2010-2018 PHPWord contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Writer\ODText\Style;

use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;

/**
 * Font style writer
 *
 * @since 0.10.0
 */
class Paragraph extends AbstractStyle
{
    /**
     * Write style.
     */
    public function write()
    {
        $style = $this->getStyle();
        if (!$style instanceof \PhpOffice\PhpWord\Style\Paragraph) {
            return;
        }
        $xmlWriter = $this->getXmlWriter();

        $xmlWriter->startElement('style:style');
        $xmlWriter->writeAttribute('style:name', $style->getStyleName());
        $xmlWriter->writeAttribute('style:family', 'paragraph');
        if ($style->isAuto()) {
            $xmlWriter->writeAttribute('style:parent-style-name', 'Standard');
            $xmlWriter->writeAttribute('style:master-page-name', 'Standard');
        }

        $xmlWriter->startElement('style:paragraph-properties');
        if ($style->isAuto()) {
            $xmlWriter->writeAttribute('style:page-number', 'auto');
        } else {
            // Spacing
            $marginTop = $style->getSpaceBefore();
            $xmlWriter->writeAttributeIf(null != $marginTop, 'fo:margin-top', Converter::twipToCm($marginTop) . 'cm');
            $marginBottom = $style->getSpaceAfter();
            $xmlWriter->writeAttributeIf(null != $marginBottom, 'fo:margin-bottom', Converter::twipToCm($marginBottom) . 'cm');

            // Alignment
            $xmlWriter->writeAttributeIf('' !== $style->getAlignment(), 'fo:text-align', $this->getAlignment($style->getAlignment()));

            // Page break
            $xmlWriter->writeAttributeIf($style->hasPageBreakBefore(), 'fo:break-before', 'page');

            // Indentation
            if ($style->getIndentation() != null) {
                $marginLeft = $style->getIndentation()->getLeft();
                $xmlWriter->writeAttributeIf(null != $marginLeft, 'fo:margin-left', Converter::twipToCm($marginLeft) . 'cm');

                $marginRight = $style->getIndentation()->getRight();
                $xmlWriter->writeAttributeIf(null != $marginRight, 'fo:margin-right', Converter::twipToCm($marginRight) . 'cm');

                $firstLine = $style->getIndentation()->getFirstLine();
                $xmlWriter->writeAttributeIf(null != $firstLine, 'fo:text-indent', Converter::twipToCm($firstLine) . 'cm');
            }
        }

        $xmlWriter->endElement(); //style:paragraph-properties

        $xmlWriter->endElement(); //style:style
    }

    private function getAlignment($alignment)
    {
        $textAlign = '';

        switch ($alignment) {
            case Jc::CENTER:
                $textAlign = 'center';
                break;
            case Jc::END:
            case Jc::MEDIUM_KASHIDA:
            case Jc::HIGH_KASHIDA:
            case Jc::LOW_KASHIDA:
            case Jc::RIGHT:
                $textAlign = 'right';
                break;
            case Jc::BOTH:
            case Jc::DISTRIBUTE:
            case Jc::THAI_DISTRIBUTE:
            case Jc::JUSTIFY:
                $textAlign = 'justify';
                break;
            default: // all others, align left
                $textAlign = 'left';
                break;
        }

        return $textAlign;
    }
}
