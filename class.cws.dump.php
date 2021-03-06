<?php

/**
 * CwsDump
 * 
 * CwsDump is a PHP class to replace var_dump(), print_r() based on the XDebug style.
 *
 * CwsDump is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option)
 * or (at your option) any later version.
 *
 * CwsDump is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 * for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 * 
 * @package CwsDump
 * @author Cr@zy
 * @copyright 2013-2015, Cr@zy
 * @license GNU LESSER GENERAL PUBLIC LICENSE
 * @version 1.3
 *
 */

class CwsDump
{
    const INI_FILE = 'cws.dump.ini';
    const SEP_OBJECT = '___';
    
    const INICAT_1 = 'cwsdump_general';
    const INI1_MAXDEPTH = 'cwsdump_max_depth';
    const INI1_MAXDATA = 'cwsdump_max_data';
    const INI1_MAXCHILDREN = 'cwsdump_max_children';
    const INI1_FONTFAMILY = 'cwsdump_font_family';
    
    const INICAT_2 = 'cwsdump_colors';
    const INI2_NULL = 'cwsdump_null';
    const INI2_BOOL = 'cwsdump_bool';
    const INI2_STRING = 'cwsdump_string';
    const INI2_INT = 'cwsdump_int';
    const INI2_FLOAT = 'cwsdump_float';
    const INI2_AREMPTY = 'cwsdump_array_empty';
    
    const INICAT_3 = 'cwsdump_names';
    const INI3_NULL = 'cwsdump_null';
    const INI3_BOOL = 'cwsdump_bool';
    const INI3_STRING = 'cwsdump_string';
    const INI3_INT = 'cwsdump_int';
    const INI3_FLOAT = 'cwsdump_float';
    const INI3_ARRAY = 'cwsdump_array';
    const INI3_OBJECT = 'cwsdump_object';
    const INI3_RESOURCE = 'cwsdump_resource';
    
    const INICAT_4 = 'cwsdump_display_values';
    const INI4_NULL = 'cwsdump_null';
    const INI4_TRUE = 'cwsdump_true';
    const INI4_FALSE = 'cwsdump_false';
    const INI4_AREMPTY = 'cwsdump_array_empty';
    const INI4_MAXDEPTH = 'cwsdump_max_depth';
    const INI4_MAXCHILDREN = 'cwsdump_max_children';
    
    /**
     * The properties from the INI file
     * @var array
     */
    private $iniProps;
    
    /**
     * Variable to control the recurse depth.
     * default 0
     * @var int
     */
    private $recurseDepth = 0;
    
    /**
     * Variable to detect the recurse.
     * default array()
     * @var array
     */
    private $recurseDetection = array();
    
    public function __construct()
    {
        $this->retrieveIniProps(realpath(dirname(__FILE__)) . '/' . self::INI_FILE);
    }
    
    /**
     * Retrieve the properties from the INI file
     * @param string $iniFilePath
     */
    private function retrieveIniProps($iniFilePath)
    {
        if (file_exists($iniFilePath)) {
            $this->iniProps = parse_ini_file($iniFilePath, true);
            if ($this->iniProps !== false) {
                $this->checkIniProps();
            } else {
                die(get_class($this) . ': An error occurred while parsing ' . self::INI_FILE . ' file...');
            }
        } else {
            die(get_class($this) . ': Ini file ' . self::INI_FILE . ' not found...');
        }
    }
    
    /**
     * Check the properties from the INI file.
     */
    private function checkIniProps()
    {
        // Cat GENERAL
        $cat = $this->checkIniCat(self::INICAT_1);
        $this->checkIniKey(self::INICAT_1, self::INI1_MAXDEPTH, '3');
        $this->checkIniKey(self::INICAT_1, self::INI1_MAXDATA, '512');
        $this->checkIniKey(self::INICAT_1, self::INI1_MAXCHILDREN, '128');
        $this->checkIniKey(self::INICAT_1, self::INI1_FONTFAMILY, 'Monospace');
        
        // Cat COLORS
        $cat = $this->checkIniCat(self::INICAT_2);
        $this->checkIniKey(self::INICAT_2, self::INI2_NULL, '#3465A4');
        $this->checkIniKey(self::INICAT_2, self::INI2_BOOL, '#75507B');
        $this->checkIniKey(self::INICAT_2, self::INI2_STRING, '#CC0000');
        $this->checkIniKey(self::INICAT_2, self::INI2_INT, '#4E9A06');
        $this->checkIniKey(self::INICAT_2, self::INI2_FLOAT, '#F57900');
        $this->checkIniKey(self::INICAT_2, self::INI2_AREMPTY, '#888A85');
        
        // Cat COLORS
        $cat = $this->checkIniCat(self::INICAT_3);
        $this->checkIniKey(self::INICAT_3, self::INI3_NULL, 'null');
        $this->checkIniKey(self::INICAT_3, self::INI3_BOOL, 'boolean');
        $this->checkIniKey(self::INICAT_3, self::INI3_STRING, 'string');
        $this->checkIniKey(self::INICAT_3, self::INI3_INT, 'int');
        $this->checkIniKey(self::INICAT_3, self::INI3_FLOAT, 'float');
        $this->checkIniKey(self::INICAT_3, self::INI3_ARRAY, 'array');
        $this->checkIniKey(self::INICAT_3, self::INI3_OBJECT, 'object');
        $this->checkIniKey(self::INICAT_3, self::INI3_RESOURCE, 'resource');
        
        // Cat DISPLAY_VALUES
        $cat = $this->checkIniCat(self::INICAT_4);
        $this->checkIniKey(self::INICAT_4, self::INI4_NULL, 'null');
        $this->checkIniKey(self::INICAT_4, self::INI4_TRUE, 'true');
        $this->checkIniKey(self::INICAT_4, self::INI4_FALSE, 'false');
        $this->checkIniKey(self::INICAT_4, self::INI4_AREMPTY, 'empty');
        $this->checkIniKey(self::INICAT_4, self::INI4_MAXDEPTH, '...');
        $this->checkIniKey(self::INICAT_4, self::INI4_MAXCHILDREN, 'more elements...');
        
        // Change some types
        $this->iniProps[self::INICAT_1][self::INI1_MAXDEPTH] = intval($this->iniProps[self::INICAT_1][self::INI1_MAXDEPTH]);
        $this->iniProps[self::INICAT_1][self::INI1_MAXDATA] = intval($this->iniProps[self::INICAT_1][self::INI1_MAXDATA]);
        $this->iniProps[self::INICAT_1][self::INI1_MAXCHILDREN] = intval($this->iniProps[self::INICAT_1][self::INI1_MAXCHILDREN]);
    }
    
    /**
     * Check a category from the INI file
     * @param string $cat
     */
    private function checkIniCat($cat)
    {
        if (!isset($this->iniProps[self::INICAT_1])) {
            $this->iniProps[self::INICAT_1] = array();
        }
    }
    
    /**
     * Check a key from the INI file
     * @param string $cat
     * @param string $key
     * @param string $default
     */
    private function checkIniKey($cat, $key, $default)
    {
        if (!isset($this->iniProps[$cat][$key])) {
            $this->iniProps[$cat][$key] = $default;
        }
    }
    
    /**
     * Dump variable into formatted XHTML string
     * @param mixed $var
     * @param boolean $or
     * @return string - Formatted XHTML
     */
    public function dump($var, $or = true)
    {
        $result = '';
        
        if ($var === null) {
            $result .= $this->dumpNull($or);
        } elseif (is_bool($var)) {
            $result .= $this->dumpBool($var, $or);
        } elseif (is_string($var)) {
            $result .= $this->dumpString($var, $or);
        } elseif (is_int($var)) {
            $result .= $this->dumpInt($var, $or);
        } elseif (is_float($var)) {
            $result .= $this->dumpFloat($var, $or);
        } elseif (is_array($var) || is_object($var)) {
            if ($this->recurseDepth >= $this->iniProps[self::INICAT_1][self::INI1_MAXDEPTH]) {
                $type = is_object($var) ? $this->iniProps[self::INICAT_3][self::INI3_OBJECT]
                    : $this->iniProps[self::INICAT_3][self::INI3_ARRAY];
                $maxDepthReached = $this->writeStart(false);
                $maxDepthReached .= $this->writeRow('<strong>' . $type . '</strong>');
                $maxDepthReached .= $this->writeRow('<span style="padding-left:10pt;font-style:italic;"><i>'
                    . $this->iniProps[self::INICAT_4][self::INI4_MAXDEPTH] . '</span>');
                $maxDepthReached .= $this->writeEnd();
                return $maxDepthReached;
            }
            $this->recurseDepth++;
            if (is_array($var)) {
                $result .= $this->dumpArray($var, $or);
            } else {
                $result .= $this->dumpObject($var, $or);
            }
            $this->recurseDepth--;
        } elseif(is_resource($var)) {
            $result .= $this->dumpRes($var, $or);
        } else {
            $result .= '???';
        }
        
        return $result;
    }
    
    private function dumpNull($or)
    {
        return $this->processVar(
            $this->iniProps[self::INICAT_4][self::INI4_NULL],
            $this->iniProps[self::INICAT_3][self::INI3_NULL],
            $this->iniProps[self::INICAT_2][self::INI2_NULL],
            $or
        );
    }
    
    private function dumpBool($var, $or)
    {
        return $this->processVar(
            $var ? $this->iniProps[self::INICAT_4][self::INI4_TRUE] : $this->iniProps[self::INICAT_4][self::INI4_FALSE],
            $this->iniProps[self::INICAT_3][self::INI3_BOOL],
            $this->iniProps[self::INICAT_2][self::INI2_BOOL],
            $or
        );
    }
    
    private function dumpString($var, $or)
    {
        return $this->processVar(
            var_export($var, true),
            $this->iniProps[self::INICAT_3][self::INI3_STRING],
            $this->iniProps[self::INICAT_2][self::INI2_STRING],
            $or
        );
    }
    
    private function dumpInt($var, $or)
    {
        return $this->processVar(
            var_export($var, true),
            $this->iniProps[self::INICAT_3][self::INI3_INT],
            $this->iniProps[self::INICAT_2][self::INI2_INT],
            $or
        );
    }
    
    private function dumpFloat($var, $or)
    {
        return $this->processVar(
            var_export($var, true),
            $this->iniProps[self::INICAT_3][self::INI3_FLOAT],
            $this->iniProps[self::INICAT_2][self::INI2_FLOAT],
            $or
        );
    }
    
    private function dumpArray(&$var, $or)
    {
        $len = count($var);
        
        $result = $this->writeStart($or);
        $type = $this->iniProps[self::INICAT_3][self::INI3_ARRAY];
        
        if ($len === 0 && $var === array()) {
            $result .= $this->writeRow('<strong>' . $type . '</strong>');
            $result .= $this->writeRow($this->dumpArrayEmpty());
        } else {
            $result .= $this->writeRow('<strong>' . $type . '</strong> <i>(length=' . $len . ')</i>');
            $result .= $this->writeRow($this->processArray($var, false));
        }
        
        $result .= $this->writeEnd();
        
        return $this->writePre($result, $or);
    }
    
    private function dumpArrayEmpty()
    {
        $result = '<span style="font-style:italic;font-family:'
            . $this->iniProps[self::INICAT_1][self::INI1_FONTFAMILY] . ';';
        $result .= 'color:' . $this->iniProps[self::INICAT_2][self::INI2_AREMPTY] . ';">';
        $result .= $this->iniProps[self::INICAT_4][self::INI4_AREMPTY] . '</span>';
        return '<span style="padding-left:10pt">' . $result . '</span>';
    }
    
    private function dumpObject(&$var, $or)
    {
        $props = array();
        $object = get_class($var);
        $array = (array)$var;
        
        foreach ($array as $key => $value) {
            $explKey = explode("\0", $key);
            if (count($explKey) == 1) {
                $props[self::SEP_OBJECT . 'public' . self::SEP_OBJECT . $key] = $value;
            } elseif ($explKey[1] == '*') {
                $props[self::SEP_OBJECT . 'protected' . self::SEP_OBJECT . $explKey[2]] = $value;
            } else {
                $props[self::SEP_OBJECT . 'protected' . self::SEP_OBJECT . $explKey[2]] = $value;
            }
        }
        
        $row = '<span style="font-weight:bold;font-family:';
        $row .= $this->iniProps[self::INICAT_1][self::INI1_FONTFAMILY] . ';">';
        $row .= $this->iniProps[self::INICAT_3][self::INI3_OBJECT] . '</span> ';
        $row .= '<span style="font-style:italic;font-family:';
        $row .= $this->iniProps[self::INICAT_1][self::INI1_FONTFAMILY] . ';">';
        $row .= '(' . $object . ')</i>';
        
        $result = $this->writeStart($or);
        $result .= $this->writeRow($row);
        $result .= $this->writeRow($this->processArray($props, false));
        $result .= $this->writeEnd();
        
        return $this->writePre($result, $or);
    }
    
    private function dumpRes(&$var, $or)
    {
        $result = '<span style="font-weight:bold;font-family:';
        $result .= $this->iniProps[self::INICAT_1][self::INI1_FONTFAMILY] . ';">';
        $result .= $this->iniProps[self::INICAT_3][self::INI3_RESOURCE] . '</span> ';
        $result .= '<span style="font-style:italic;font-family:';
        $result .= $this->iniProps[self::INICAT_1][self::INI1_FONTFAMILY] . ';">';
        $result .= '(' . intval($var) . ', ' . get_resource_type($var) . ')</i>';
        return $this->writePre($result, $or);
    }
    
    private function processVar($content, $type, $color, $or)
    {
        $len = strlen($content);
        if ($len > $this->iniProps[self::INICAT_1][self::INI1_MAXDATA]
                && $type == $this->iniProps[self::INICAT_3][self::INI3_STRING]) {
            $content = substr($content, 0, $this->iniProps[self::INICAT_1][self::INI1_MAXDATA] - 3) . '...';
        }
        
        $result = $type != $this->iniProps[self::INICAT_3][self::INI3_NULL] ? '<small>' . $type . '</small> ' : '';
        $result .= '<span style="font-family:';
        $result .= $this->iniProps[self::INICAT_1][self::INI1_FONTFAMILY] . ';color:' . $color . ';">';
        $result .= str_replace(array("\n", ' '), array('<br/>', '&#160;'), htmlspecialchars($content));
        $result .= '</span>';
        $result .= $type == $this->iniProps[self::INICAT_3][self::INI3_STRING] ? ' <i>(length=' . $len . ')</i>' : '';
        
        return $this->writePre($result, $or);
    }
    
    private function processArray(&$var, $or)
    {
        if (false !== array_search($var, $this->recurseDetection, true)) {
            return 'Recursive dependency detected';
        }
    
        array_push($this->recurseDetection, $var);
    
        $result = $this->writeStart($or);
    
        $children = 0;
        foreach($var as $key => $value) {
            if ($children >= $this->iniProps[self::INICAT_1][self::INI1_MAXCHILDREN]) {
                $result .= $this->writeRow('<i>' . $this->iniProps[self::INICAT_4][self::INI4_MAXCHILDREN] . '</i>');
                break;
            }
            if (substr($key, 0, 3) === self::SEP_OBJECT) {
                $expKey = explode(self::SEP_OBJECT, $key);
                $result .= $this->writeRow('<i>' . $expKey[1] . '</i> \'' . $expKey[2] . '\' => ' . $this->dump($value, false));
            } else {
                $result .= $this->writeRow((is_numeric($key) ? $key : '\'' . $key . '\'') . ' => ' . $this->dump($value, false));
            }
            $children++;
        }
    
        if ($var !== array_pop($this->recurseDetection)) {
            throw new RuntimeException('Inconsistent state');
        }
    
        $result .= $this->writeEnd();
        
        return $this->writePre($result, $or);
    }
    
    private function writePre($elt, $or)
    {
        if ($or) {
            $result = '<table style="margin:7pt 0 7pt 0;padding:0;';
            $result .= 'font-family:' . $this->iniProps[self::INICAT_1][self::INI1_FONTFAMILY] . ';';
            $result .= 'border-collapse:collapse;valign:top;">';
            $result .= $this->writeRow($elt) . $this->writeEnd();
            return $result;
        } else {
            return $elt;
        }
    }
    
    private function writeStart($or = true)
    {
        $result = '<table style="margin:0 0 0 ' . ($or ? '0' : '10pt') . ';';
        $result .= 'padding:0 0 0 ' . ($or ? '0' : '3pt') . ';';
        $result .= 'font-family:' . $this->iniProps[self::INICAT_1][self::INI1_FONTFAMILY] . ';';
        $result .= 'border-collapse:collapse;valign:top;">';
        return $result;
    }
    
    private function writeRow($elt)
    {
        $result = '<tr><td style="font-family:' . $this->iniProps[self::INICAT_1][self::INI1_FONTFAMILY] . ';';
        $result .= 'white-space:nowrap;vertical-align:top">' . $elt . '</td></tr>';
        return $result;
    }
    
    private function writeEnd()
    {
        return "</table>\r\n";
    }
}

function cwsDump($var, $echo = true) {
    $result = call_user_func(array(new CwsDump(), 'dump'), $var);
    if ($echo) {
        echo $result;
    } else {
        return $result;
    }
}
