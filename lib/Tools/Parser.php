<?php

namespace MySheet\Tools;

use MySheet\Tools\IParser;
use MySheet\Structure\NodeBlock;
use MySheet\Structure\Document;
use MySheet\Error\ParseException;
use MySheet\Error\ErrorTable;
use MySheet\Essentials\ParserExtension;
use MySheet\Essentials\ParserContext;
use MySheet\Essentials\SourceLine;
use MySheet\Traits\RootClassTrait;
use MySheet\Traits\ParserLinesTrait;
use MySheet\Traits\ParserCursorStateTrait;

/**
 * Description of Parser
 *
 * @author dobby007 (Alexander Gilevich, alegil91@gmail.com)
 */
class Parser implements IParser {

    use RootClassTrait,
        ParserLinesTrait,
        ParserCursorStateTrait;

    private $code = null;
    private $curBlock = null;
    private $doc = null;
    protected $extensions = array();

    public function __construct($code) {
        $this->setCode($code);
    }

    public function getCode() {
        return $this->code;
    }

    public function setCode($code) {
        $this->code = (string) $code;
        $this->resetCursor();
    }

    public function addParserExtension($extension) {
        if (is_string($extension) && class_exists($extension)) {
            $extension = new $extension();
        }
        
        if ($extension instanceof ParserExtension) {
            $this->extensions[] = $extension;
        }
    }

    public function comeon() {
        $this->doc = new Document();
        $this->curBlock = $this->doc;

        try {
            $this->divideIntoLines();
            $this->linebyline();
        } catch (ParseException $exc) {
            echo 'Error happened: ' . (string)$exc . "\n";
        }
//        var_dump($this->lines);
        return $this->doc;
    }

    protected function divideIntoLines() {
        $this->lines = preg_split("/\n|\r\n/", $this->code);

        $tabsize = false;
        $is_tabbed = false;
        $lines = [];
        $curly_blocks = [];
        
        array_walk($this->lines, function($line, $index) use(&$tabsize, &$is_tabbed, &$lines, &$curly_blocks) {
            $spaces_count = 0;
            if ($tabsize === false) {
                if (substr($line, 0, 1) === "\t")
                    $is_tabbed = true;

                $spaces_count = $this->linespaces($line, $is_tabbed);
                if ($spaces_count > 0) {
                    $tabsize = $spaces_count;
                }
            } else {
                $spaces_count = $this->linespaces($line, $is_tabbed);
            }

            if ($spaces_count === false) {
                return;
            }
            $level = $tabsize > 0 ? intval($spaces_count / $tabsize) : 0;
            if (count($curly_blocks) > 0 && $level <= end($curly_blocks)) {
                if ($level > end($lines)->getLevel()) {
                    $level = end($lines)->getLevel() + 1;
                } else {
                    $level = end($curly_blocks) + 1;
                }
//                var_dump($line, $curly_blocks, $level);
            }
            
            $nlev = $level;
            $offset = 0;
            /*
            $curly_func = function($line, $offset, &$close_pos) use ( &$curly_func ) {
                $open_pos = strpos($line, '{', $offset);
                if ($open_pos === false) {
                    return false;
                }
                
                $sub_close_pos = $open_pos;
                $subresult = $curly_func($line, $open_pos + 1, $sub_close_pos);
                $close_pos = strpos($line, '}', $sub_close_pos);
                
                if ($close_pos === false) {
                    //throw
                }
                
                return \MySheet\Helpers\ArrayHelper::concat([substr($line, $offset, $open_pos - $offset)], $subresult ? $subresult : []);
            };*/
            
            $i = 0;
            $len = strlen($line);
            $offset = $spaces_count;
            $handled = false;
            
            while ($i < $len) {
                if ($line[$i] === '{') {
                    $lines[] = new SourceLine(trim(substr($line, $offset, $i - $offset)), $nlev, '{');
//                    var_dump($nlev);
                    $curly_blocks[] = $nlev;
                    $nlev++;
                    $offset = $i + 1;
                    $handled = true;
                } else if ($line[$i] === '}') {
                    $newline = trim(substr($line, $offset, $i - $offset));
                    if (!empty($newline)) {
                        $lines[] = new SourceLine($newline, end($curly_blocks) + 1, '}');
                    }
                    
                    array_pop($curly_blocks);
                    $nlev--;
                    $offset = $i + 1;
                    $handled = true;
                }
                
                $i++;
            }
            
            if ($handled) {
                $endstr = trim(substr($line, $offset, $i - $offset));
                if (!empty($endstr)) {
                    $lines[] = new SourceLine($endstr, $nlev);
                }
            }
            
            if (!$handled) {
//                var_dump('not handled', $level, $line);
                $lines[] = new SourceLine(rtrim(substr($line, $spaces_count)), $level);
            }
        });
        
        echo "\n\n-==LINES==-\n" . implode($lines, "\n") . "\n===\n\n";
        $this->lines = $lines;
    }

    protected function linebyline() {
        $this->curLine = 0;
        $curLine = $this->curline();

        do {
            $curLineNumber = $this->getLineNumber();
            $result = false;

            foreach ($this->extensions as $extension) {
                $result = $this->tryParse($extension);
                if ($result !== false)
                    break;
            }
//            var_dump($this->curBlock, $result);
//            var_dump('new result:', $curLine, $this->curBlock, $result);
//            var_dump($this->curBlock, $curLine, $result);
            if (
                $this->curBlock instanceof NodeBlock && 
                $result !== null
            ) {
                $this->curBlock->addChild($result);
            } else if ($result === false) {
                //throw unrecognized sequence
                throw new ParseException(ErrorTable::E_UNRECOGNIZED_SEQUENCE);
            } else {
                //throw can not get parent object of $curLineNumber
            }

//            var_dump($curLine,$result ? $result->getSelectors()[0]->getPath() : null);

            $nextLine = $this->getLine($this->getLineNumber() + 1);
//            var_dump('status: ', $curLine, $this->curline(), $nextLine);
            if ($result && $nextLine) {
                if (
                    $result instanceof NodeBlock && 
                    $nextLine->getLevel() >= $this->curline()->getLevel()
                ) {
                    $this->curBlock = $result;
//                    var_dump('indent + 1 : ', $result->getSelectors());
                } else if ($nextLine->getLevel() < $curLine->getLevel()) {
                    $steps_back = $curLine->getLevel() - $nextLine->getLevel();
                    while ($steps_back --) {
                        $this->curBlock = $this->curBlock->getParent();
                        if ($this->curBlock === null) {
                            //throw can not get parent object of $this->getLineNumber() + 1
                        }
                    }
                } else {
                    //throw bad tab indentation
                }
            }
        } while ($curLine = $this->nextline());
    }

    protected function tryParse(ParserExtension $extension) {
        $result = false;
        $context = new ParserContext($this, $this->lines, $this->getLineNumber());

        $result = $context->parse($extension);
        if (!$result) {
            return false;
        }
        $this->applyParserContext($context);

        return $result;
    }

    protected function applyParserContext(ParserContext $context) {
        $this->setLineCursor($context->getLineNumber());
    }

    /**
     * 
     * @param string $line Text line
     * @param boolean $count_tabs True if you want to count \t characters instead of spaces
     * @return int|boolean Returns the number of spaces till the first meaning character
     */
    protected function linespaces($line, $count_tabs = false) {
        $linelen = strlen($line);
        $i = 0;

        while ($i < $linelen) {
            if (!$this->is_space_symbol($line[$i]) && $i > 0)
                return $i;
            else if ($line[$i] === "\r" || $line[$i] === "\n")
                return false;
            else if ($i === 0 && !$this->is_space_symbol($line[$i])) {
                return 0;
            }

            $i++;
        }

        return false;
    }

    protected function is_space_symbol($char) {
        if ($char === ' ' || $char === "\t")
            return true;

        return false;
    }

}
