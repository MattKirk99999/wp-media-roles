<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       5nines.com
 * @since      1.0.0
 *
 * @package    Wp_Media_Roles
 * @subpackage Wp_Media_Roles/admin/partials
 */

function getMembersApi(): MembersApi
{
    return apply_filters('members_api', null);
}

$membersApi = getMembersApi();
?>

<div class="wrap">

    <h1>
        Media Permissions
    </h1>
    <br/>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">

            <!-- main content -->
            <div id="post-body-content">
                <table>
                    <tr>
                        <td>
                            Members Plugin is Active:
                        </td>
                        <td>
                            <?php echo($membersApi->is_active() > 0 ? "True":"<b>False</b>"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Members Plugin is Synced:
                        </td>
                        <td>
                            <?php echo($membersApi->is_synced() > 0 ? "True":"<b>False</b>"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Members Admin Instance Loaded:
                        </td>
                        <td>
                            <?php echo($membersApi->get_membersuite_admin_instance() === null ? "<b>False</b>":"True"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            .htaccess exists:
                        </td>
                        <td>
                            <?php 
                            $exists = $this->htaccessExists();
                            echo($exists ? "True":"<b>False</b>"); 
                            echo " (<i>" . $this->getHtaccessPath() . "</i>)";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            .htaccess is valid:
                        </td>
                        <td>
                            <?php echo($this->htaccessIsValid() ? "True":"<b>False</b>"); ?>
                        </td>
                    </tr>
                </table>
                
                <br/>
                <br/>
                
                <h4>Current .htaccess: </h4>
                <pre style="border: 1px solid #444444; padding: 5px; background-color: rgba(255,255,255,0.25);"><code><?php 
                    echo htmlentities($htaccessCurrent);  
                ?></code></pre>
                
                <br/>
                <br/>
                
                <form id="recreate-htaccess" action="/wp-admin/admin-ajax.php?action=recreate_htaccess" method="get">
                    <input type="submit" value="Recreate .htaccess as:">
                </form>
                <div id="recreate-results">
                    
                </div>
                <pre style="border: 1px solid #444444; padding: 5px; background-color: rgba(255,255,255,0.25);"><code><?php 
                    echo htmlentities($htaccessExpected);  
                ?></code></pre>
                
                <form id="restore-htaccess" action="/wp-admin/admin-ajax.php?action=restore_htaccess" method="get">
                    <input type="submit" value="Restore .htaccess as:">
                </form>
                <div id="restore-results">
                    
                </div>
                <pre style="border: 1px solid #444444; padding: 5px; background-color: rgba(255,255,255,0.25);"><code><?php 
                    echo htmlentities($htaccessSaved);  
                ?></code></pre>
            </div>
        </div>
    </div>
</div>
