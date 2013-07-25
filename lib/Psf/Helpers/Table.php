<?php
/**
 * Table
 *
 * @author Piotr Olaszewski
 */
namespace Psf\Helpers;

use Psf\Interfaces\HelperInterface;
use Psf\Output\Writer;

class Table implements HelperInterface
{
    private $_headers = array();
    private $_rows = array();
    /**
     * @var Writer
     */
    private $_output;

    public function setHeaders(array $headers)
    {
        $this->_headers = $headers;
        return $this;
    }

    public function setRows(array $rows)
    {
        $this->_rows = $rows;
        return $this;
    }

    public function render(Writer $output)
    {
        $this->_output = $output;

        $this->_renderHeaderRowSeparator();
        $this->_generateHeaderRow();
        $this->_renderHeaderRowSeparator();

        $this->_generateBodyRows();
        $this->_renderHeaderRowSeparator();
    }

    private function _renderHeaderRowSeparator()
    {
        $separator = '+';
        foreach ($this->_headers as $column => $header) {
            $columnWidth = $this->_getColumnWidth($column);
            $separator .= str_repeat('-', $columnWidth) . '+';
        }
        $this->_output->writeMessage($separator);
    }

    private function _generateHeaderRow()
    {
        $this->_renderDataRow($this->_headers);
    }

    private function _generateBodyRows()
    {
        foreach ($this->_rows as $row) {
            $this->_renderDataRow($row);
        }
    }

    private function _renderDataRow($row)
    {
        $line = '|';
        foreach ($row as $column => $name) {
            $columnWidth = $this->_getColumnWidth($column);
            $spaces = $columnWidth - strlen($name) - 1;
            $line .= ' ' . $name . str_repeat(' ', $spaces) . '|';
        }
        $this->_output->writeMessage($line);
    }

    private function _getColumnWidth($column)
    {
        $width = 0;
        if (isset($this->_headers[$column])) {
            $width = strlen($this->_headers[$column]);
        }
        array_map(function ($element) use (&$width, $column) {
            $length = strlen($element[$column]);
            if ($length > $width) {
                $width = $length;
            }
        }, $this->_rows);
        return $this->_widthWithSpaces($width);
    }

    private function _widthWithSpaces($width)
    {
        return $width + 2;
    }
}