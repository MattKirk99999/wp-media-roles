<?php

/**
 * Description of HtaccessService
 *
 * @author mkirk
 */
class HtaccessService 
{
    public function getHtaccessPath(): string
    {
        $wordpress_path = get_home_path();

        $filename = ".htaccess";

        return $wordpress_path . "wp-content/uploads/" . $filename;
    }

    public function htaccessExists(): bool
    {
        $path = $this->getHtaccessPath();

        return file_exists($path);
    }

    public function htaccessIsValid(): bool
    {
        if (!$this->htaccessExists()) return false;

        $contents = trim( file_get_contents($this->getHtaccessPath()) );

        $expected = trim( $this->getHtaccessRules() );

        return $contents == $expected;
    }

    public function getHtaccessRulesLines(): array
    {
        $urlparts = parse_url(home_url());
        $domain = $urlparts['host'];
        
        $ret = [];

        $ret[] = "<IfModule mod_rewrite.c>";
        $ret[] =     "RewriteEngine On";
        $ret[] =     "### start-wp-media-roles ###";
        $ret[] =     "RewriteCond %{HTTP_HOST} ^$domain [OR]";
        $ret[] =     "RewriteCond %{HTTP_HOST} ^www.$domain$";
        $ret[] =     "RewriteCond %{REQUEST_FILENAME} -f";
        $ret[] =     "RewriteRule ^(.+\.(pdf|doc|docx|xls|xlsx|ppt|pptx))$ /index.php [L]";
        $ret[] =     "### end-wp-media-roles ###";
        $ret[] = "</IfModule>";

        return $ret;
    }
    
    public function getHtaccessRules(): string
    {
        $lines = $this->getHtaccessRulesLines();
        
        return implode("\n", $lines);
    }
    
    public function testHtaccess(): bool
    {
//        copy('foo/test.php', 'bar/test.php');
//        
//        $url = plugins_url();
//        
//        var_dump($url);
//        
//        $test_url = get_site_url() . "/wp-content/uploads/wp-media-roles/test/fake/path/to/fake/file.pdf";
        
//        $contents = file_get_contents($test_url);
//        
//        var_dump($contents);
        
        return false;
    }

    public function recreate() 
    {
        $path = $this->getHtaccessPath();
        
        $expected = trim( $this->getHtaccessRules() );
        
        file_put_contents($path, $expected);
    }

}
