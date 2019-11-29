<?php

/**
 * Description of HtaccessService
 *
 * @author mkirk
 */
class HtaccessService 
{
    public $SupportedFileTypes = ['pdf','doc','docx','xls','xlsx','ppt','pptx'];
    
    /**
     * Initialize the service and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( string $plugin_name, string $version) 
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
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

        $contents = $this->getCurrentHtaccessRules();

        $expected_str = $this->getHtaccessRules_Start();
        $expected_dmn = $this->getHtaccessRules_DomainRules();
        $expected_end = $this->getHtaccessRules_End();
        
        $str_index = strpos($contents, $expected_str);
        $dmn_index = strpos($contents, $expected_dmn);
        $end_index = strpos($contents, $expected_end);
        
        if ($str_index === false || $dmn_index === false || $end_index === false) return false;
        
        return $str_index < $dmn_index && $dmn_index < $end_index;
    }

    public function getHtaccessRulesLines(): array
    {
        $contents = $this->getCurrentHtaccessRules();
        
        if ($this->htaccessIsValid()) return explode("\n", $contents );
        
        $expected_str = $this->getHtaccessRules_Start();
        $expected_end = $this->getHtaccessRules_End();
        
        $str_index = strpos($contents, $expected_str);
        $end_index = strpos($contents, $expected_end);
        $end_of_start_index = $str_index + strlen($expected_str);
        
        $other_rules = "";
        
        if ($str_index !== false && $end_index !== false)
            $other_rules = substr($contents, $end_of_start_index, $end_index - $end_of_start_index);
        
        return array_merge( 
                $this->getHtaccessRulesLines_StartWpMediaRoles(), 
                $this->getHtaccessRulesLines_ThisDomain(),
                explode("\n", trim($other_rules) ),
                $this->getHtaccessRulesLines_EndWpMediaRoles()
            );
    }
    
    public function getHtaccessRulesLines_StartWpMediaRoles(): array
    {
        $ret = [];

        $ret[] = "<IfModule mod_rewrite.c>";
        $ret[] =     "RewriteEngine On";
        $ret[] =     "### start-wp-media-roles ###";

        return $ret;
    }
    
    private function getHtaccessRulesLines_ThisDomain(): array
    {
        $urlparts = parse_url(home_url());
        $domain = $urlparts['host'];
        
        $ret = [];
        
        $ret[] =     "RewriteCond %{HTTP_HOST} ^$domain [OR]";
        $ret[] =     "RewriteCond %{HTTP_HOST} ^www.$domain$";
        $ret[] =     "RewriteCond %{REQUEST_FILENAME} -f";
        $ret[] =     "RewriteRule ^(.+\.(" . implode("|", $this->SupportedFileTypes) . "))$ /index.php [L]";
        
        return $ret;
    }
    
    public function getHtaccessRulesLines_EndWpMediaRoles(): array
    {
        $ret = [];

        $ret[] =     "### end-wp-media-roles ###";
        $ret[] = "</IfModule>";

        return $ret;
    }
    
    public function getCurrentHtaccessRules(): string
    {
        return trim( file_get_contents($this->getHtaccessPath()) );
    }
    
    public function getHtaccessRules(): string
    {
        $lines = $this->getHtaccessRulesLines();
        
        return trim( implode("\n", $lines) );
    }
    
    public function getHtaccessRules_Start(): string
    {
        $lines = $this->getHtaccessRulesLines_StartWpMediaRoles();
        
        return trim( implode("\n", $lines) );
    }
    
    public function getHtaccessRules_DomainRules(): string
    {
        $lines = $this->getHtaccessRulesLines_ThisDomain();
        
        return trim( implode("\n", $lines) );
    }
    
    public function getHtaccessRules_End(): string
    {
        $lines = $this->getHtaccessRulesLines_EndWpMediaRoles();
        
        return trim( implode("\n", $lines) );
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
        $rules = $this->getHtaccessRules();
        
        $this->recreateFileWithRules($rules);
    }

    public function save() 
    {
        if (!current_user_can('manage_options')) return;
        
        $option = get_site_option( $this->plugin_name, [] );
        
        $option['prevhtaccess'] = $this->getCurrentHtaccessRules();
        
        update_site_option( $this->plugin_name, $option );
    }

    public function restore() 
    {
        $rules = $this->viewSaved();
        
        if ($rules === null) return;
        
        $this->recreateFileWithRules($rules);
    }
    
    public function viewSaved(): ?string
    {
        $option = get_site_option( $this->plugin_name, [] );
        
        if (!array_key_exists('prevhtaccess', $option)) return null;
        
        return $option['prevhtaccess'];
    }
    
    private function recreateFileWithRules(string $rules)
    {
        $path = $this->getHtaccessPath();

        file_put_contents($path, $rules);
    }
}
