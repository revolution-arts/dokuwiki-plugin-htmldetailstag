<?php
/**
 * DokuWiki Plugin htmldetailstag (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Sascha Klawohn <sideboard@revolutionarts.de>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

class syntax_plugin_htmldetailstag_details extends DokuWiki_Syntax_Plugin {
    protected $entry_pattern = '<details(?:\s+open)?\s*?>(?=.*?</details>)';
    protected $exit_pattern  = '</details>';

    /**
     * @return string Syntax mode type
     */
    public function getType()
    {
        return 'container';
    }

    /**
     * @param string        $mode The mode to check
     * @return bool Whether $mode is allowed within this mode
     */

    function accepts($mode) {
        if ($mode == substr(get_class($this), 7)) return true;
        return parent::accepts($mode);
    }

    /**
     * @return string|array(string) Allowed syntax mode types
     */
    public function getAllowedTypes()
    {
        return array('container', 'baseonly', 'formatting', 'substition', 'protected', 'disabled', 'paragraphs');
    }

    /**
     * @return string Paragraph type
     */
    public function getPType()
    {
        return 'stack';
    }

    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort()
    {
        return 195; // cf. Doku_Parser_Mode_html 190
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode)
    {
        $this->Lexer->addEntryPattern($this->entry_pattern, $mode, 'plugin_htmldetailstag_details');
    }

   public function postConnect()
   {
       $this->Lexer->addExitPattern($this->exit_pattern, 'plugin_htmldetailstag_details');
   }

    /**
     * Handle matches of the htmldetailstag syntax
     *
     * @param string       $match   The match of the syntax
     * @param int          $state   The state of the handler
     * @param int          $pos     The position in the document
     * @param Doku_Handler $handler The handler
     *
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        $data = array($match, $state);
        return $data;
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string        $mode     Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer $renderer The renderer
     * @param array         $data     The data from the handler() function
     *
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode !== 'xhtml') {
            return false;
        }

        list($match, $state) = $data;
        switch ($state) {
            case DOKU_LEXER_ENTER:
                $renderer->doc .= $match;
                break;
            case DOKU_LEXER_UNMATCHED:
                $renderer->doc .= $renderer->_xmlEntities($match);
                break;
            case DOKU_LEXER_EXIT:
                $renderer->doc .= $match;
                break;
        }
        return true;
    }
}

