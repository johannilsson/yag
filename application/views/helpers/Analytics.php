<?php

class Zend_View_Helper_Analytics
{
    /**
     * Return a tracker snippet
     *
     * @param  Zend_Db_Table_Row $photo
     * @return string
     */
    public function analytics()
    {
        if (ENVIRONMENT == 'production') {
            $config = new Zend_Config_Ini(APPLICATION_PATH . '/configuration/tracker.ini', ENVIRONMENT);
            $id = $config->tracker->webPropertyId;
            return '<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("' . $id . '");
pageTracker._trackPageview();
</script>';        
        }

    }

}
