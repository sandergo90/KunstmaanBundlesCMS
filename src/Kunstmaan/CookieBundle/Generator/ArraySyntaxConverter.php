<?php

/**
 * PHP 5.4 Short Array Syntax Converter
 *
 * Command-line script to convert PHP's "array()" syntax to PHP 5.4's
 * short array syntax "[]" using PHP's built-in tokenizer.
 *
 * This script is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License (LGPL) as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @link      https://github.com/thomasbachem/php-short-array-syntax-converter
 *
 * @link      http://php.net/manual/en/language.types.array.php
 *
 * @license   http://www.gnu.org/licenses/lgpl.html
 * @author    Thomas Bachem <mail@thomasbachem.com>
 */

namespace Kunstmaan\CookieBundle\Generator;

/**
 * Class ArraySyntaxConverter
 *
 * @package Kunstmaan\CookieBundle\Generator
 */
class ArraySyntaxConverter
{
    /**
     * @param $filePath
     */
    public function convert($filePath)
    {
        $code = file_get_contents($filePath);
        $tokens = token_get_all($code);
        $tokenCount = \count($tokens);

        // - - - - - PARSE CODE - - - - -

        $replacements = [];
        $offset = 0;
        for ($i = 0; $i < $tokenCount; ++$i) {
            // Keep track of the current byte offset in the source code
            $offset += \strlen(\is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i]);

            // T_ARRAY could either mean the "array(...)" syntax we're looking for
            // or a type hinting statement ("function(array $foo) { ... }")
            if (\is_array($tokens[$i]) && $tokens[$i][0] === T_ARRAY) {
                // Look for a subsequent opening bracket ("(") to be sure we're actually
                // looking at an "array(...)" statement
                $isArraySyntax = false;
                $subOffset = $offset;
                for ($j = $i + 1; $j < $tokenCount; ++$j) {
                    $subOffset += \strlen(\is_array($tokens[$j]) ? $tokens[$j][1] : $tokens[$j]);

                    if (\is_string($tokens[$j]) && $tokens[$j] === '(') {
                        $isArraySyntax = true;
                        break;
                    } elseif (!\is_array($tokens[$j]) || $tokens[$j][0] !== T_WHITESPACE) {
                        $isArraySyntax = false;
                        break;
                    }
                }

                if ($isArraySyntax) {
                    // Replace "array" and the opening bracket (including preceeding whitespace) with "["
                    $replacements[] = [
                        'start' => $offset - \strlen($tokens[$i][1]),
                        'end' => $subOffset,
                        'string' => '[',
                    ];

                    // Look for matching closing bracket (")")
                    $subOffset = $offset;
                    $openBracketsCount = 0;
                    for ($j = $i + 1; $j < $tokenCount; ++$j) {
                        $subOffset += \strlen(\is_array($tokens[$j]) ? $tokens[$j][1] : $tokens[$j]);

                        if (\is_string($tokens[$j]) && $tokens[$j] === '(') {
                            ++$openBracketsCount;
                        } elseif (\is_string($tokens[$j]) && $tokens[$j] === ')') {
                            --$openBracketsCount;

                            if ($openBracketsCount === 0) {
                                // Replace ")" with "]"
                                $replacements[] = [
                                    'start' => $subOffset - 1,
                                    'end' => $subOffset,
                                    'string' => ']',
                                ];
                                break;
                            }
                        }
                    }
                }
            }
        }

        // - - - - - UPDATE CODE - - - - -

        // Apply the replacements to the source code
        $offsetChange = 0;
        foreach ($replacements as $replacement) {
            $code = substr_replace(
                $code,
                $replacement['string'],
                $replacement['start'] + $offsetChange,
                $replacement['end'] - $replacement['start']
            );
            $offsetChange += \strlen($replacement['string']) - ($replacement['end'] - $replacement['start']);
        }


        // - - - - - OUTPUT/WRITE NEW CODE - - - - -

        if ($replacements) {
            file_put_contents($filePath, $code);
            print \count($replacements).' replacements.'."\n";
        } else {
            print 'No replacements.'."\n";
        }
    }

    public function revert($filePath)
    {
        $code = file_get_contents($filePath);
        $tokens = token_get_all($code);
        $tokenCount = \count($tokens);

        // - - - - - PARSE CODE - - - - -
        $replacements = [];
        $offset = 0;
        for ($i = 0; $i < $tokenCount; ++$i) {
            // Keep track of the current byte offset in the source code
            $offset += \strlen(\is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i]);

            // "[" literal could either be an array index pointer
            // or an array definition
            if (\is_string($tokens[$i]) && $tokens[$i] === '[') {
                // Assume we're looking at an array definition by default
                $isArraySyntax = true;
                $subOffset = $offset;
                for ($j = $i - 1; $j > 0; --$j) {
                    $subOffset -= \strlen(\is_array($tokens[$j]) ? $tokens[$j][1] : $tokens[$j]);

                    if (\is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
                        $subOffset += \strlen($tokens[$j][1]);
                        continue;
                        // Look for a previous variable or function return
                        // to make sure we're not looking at an array pointer
                    } elseif (
                        (\is_array($tokens[$j]) && ($tokens[$j][0] === T_VARIABLE || $tokens[$j][0] === T_STRING))
                        || \in_array($tokens[$j], [')', ']', '}'], true)
                    ) {
                        $isArraySyntax = false;
                        break;
                    } else {
                        break;
                    }
                }

                if ($isArraySyntax) {
                    // Replace "[" with "array("
                    $replacements[] = [
                        'start' => $offset - \strlen($tokens[$i]),
                        'end' => $offset,
                        'string' => 'array(',
                    ];

                    // Look for matching closing bracket ("]")
                    $subOffset = $offset;
                    $openBracketsCount = 1;
                    for ($j = $i + 1; $j < $tokenCount; ++$j) {
                        $subOffset += \strlen(\is_array($tokens[$j]) ? $tokens[$j][1] : $tokens[$j]);

                        if (\is_string($tokens[$j]) && $tokens[$j] === '[') {
                            ++$openBracketsCount;
                        } elseif (\is_string($tokens[$j]) && $tokens[$j] === ']') {
                            --$openBracketsCount;
                            if ($openBracketsCount === 0) {
                                // Replace "]" with ")"
                                $replacements[] = [
                                    'start' => $subOffset - 1,
                                    'end' => $subOffset,
                                    'string' => ')',
                                ];
                                break;
                            }
                        }
                    }
                }
            }
        }

        // - - - - - UPDATE CODE - - - - -
        // Apply the replacements to the source code
        $offsetChange = 0;
        foreach ($replacements as $replacement) {
            $code = substr_replace($code, $replacement['string'], $replacement['start'] + $offsetChange, $replacement['end'] - $replacement['start']);
            $offsetChange += \strlen($replacement['string']) - ($replacement['end'] - $replacement['start']);
        }


        // - - - - - OUTPUT/WRITE NEW CODE - - - - -
        if ($replacements) {
            file_put_contents($filePath, $code);
            print \count($replacements).' replacements.'."\n";
        }
    }
}
